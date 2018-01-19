<?php

namespace Solspace\Addons\User\Library;
use Solspace\Addons\User\Library\AddonBuilder;

class ResetPassword extends AddonBuilder
{
	// --------------------------------------------------------------------

	/**
	 * Check User $this->_params() for template for resetpassword.
	 *
	 * @access	public
	 * @param	array	$params	saved params from posted form
	 * @return	boolean			password_reset_template is in params
	 */

	public function check_params($params)
	{
		return isset($params['password_reset_template']);
	}
	//END check_params()


	// --------------------------------------------------------------------

	/**
	 * Process Reset Password
	 *
	 * Uses intercepting objects to capture how EE would do it but using
	 * our own output and redirecting.
	 *
	 * @access	public
	 * @return	void	redirects
	 */

	public function process_reset_password($params)
	{
		// if the user is logged in, then send them away
		if (ee()->session->userdata('member_id') !== 0)
		{
			return ee()->functions->redirect(ee()->functions->fetch_site_index());
		}

		// If the user is banned, send them away.
		if (ee()->session->userdata('is_banned') === TRUE)
		{
			return ee()->output->show_user_error('general', array(lang('not_authorized')));
		}

		if (! ($resetcode = ee()->input->get_post('resetcode')))
		{
			return ee()->output->show_user_error('submission', array(lang('mbr_no_reset_id')));
		}

		// -------------------------------------
		//	Get the member
		// -------------------------------------

		$a_day_ago = time() - (60*60*24);
		$reset	= $this->fetch('ResetPassword')
			->filter('date', '>', $a_day_ago)
			->filter('resetcode', $resetcode)
			->first();

		if (! $reset)
		{
			return ee()->output->show_user_error('submission', array(lang('mbr_id_not_found')));
		}

		// Ensure the passwords match.
		if (! ($password = ee()->input->get_post('password')))
		{
			return ee()->output->show_user_error('submission', array(lang('mbr_missing_password')));
		}

		if (! ($password_confirm = ee()->input->get_post('password_confirm')))
		{
			return ee()->output->show_user_error('submission', array(lang('mbr_missing_confirm')));
		}

		$validate	= array(
			'val_type'			=> 'new', // new or update
			'password'			=> ee()->input->get_post('password'),
			'password_confirm'	=> ee()->input->get_post('password_confirm')
		);

		//	----------------------------------------
		//	Validate submitted data
		//	----------------------------------------

		ee()->load->library('validate', $validate, 'validate');
		ee()->validate->validate_password();

		if (! empty(ee()->validate->errors))
		{
			return ee()->output->show_user_error('submission', ee()->validate->errors);
		}

		// Update the database with the new password.  Apply the appropriate salt first.
		ee()->load->library('auth');
		ee()->auth->update_password(
			$reset->member_id,
			$password
		);

		$this->fetch('ResetPassword')
			->filter('date', '<', $a_day_ago)
			->orFilter('member_id', $reset->member_id)
			->delete();

		// -------------------------------------
		//	redirect to return template with success
		// -------------------------------------

		if (! empty($this->param['return']))
		{
			$return	= $this->param['return'];
		}

		if (empty($return) AND ! empty($this->param['RET']))
		{
			$return = $this->param['RET'];
		}

		if (empty($return))
		{
			$return = ee()->input->get_post('return');
		}

		if (empty($return))
		{
			$return = ee()->input->get_post('RET');
		}

		if (empty($return))
		{
			$return = ee()->functions->fetch_site_index(0, 0);
		}

		//return is pre-processed by _form()
		ee()->functions->redirect($return);
	}
	//END process_reset_password()


	// --------------------------------------------------------------------

	/**
	 * Process the reset email, etc for our custom password and URL.
	 *
	 * This is nutty, but EE ties the entire work of the reset into
	 * a single function with no way to undo it but to toy around
	 * with replacing objects. This is usually a POST request so doing
	 * so _shouldn't_ hose things but we are playing it as safe as we can.
	 *
	 * @access	public
	 * @param 	array	$params	array of incoming params from posted form
	 * @return	void
	 */

	public function send_reset_token($params)
	{
		// -------------------------------------
		//	Fail if user is logged in already
		// -------------------------------------

		if (ee()->session->userdata('member_id') !== 0)
		{
			return ee()->output->show_user_error('general', array(lang('mbr_you_are_registered')));
		}

		// -------------------------------------
		//	Fail if user is banned
		// -------------------------------------

		if (ee()->session->userdata('is_banned') === TRUE)
		{
			return ee()->output->show_user_error('general', array(lang('not_authorized')));
		}

		// -------------------------------------
		//	Right method?
		// -------------------------------------

		if (REQ == 'PAGE' ||
			! isset($params['password_reset_template'])
		)
		{
			return ee()->functions->redirect(ee()->functions->fetch_site_index());
		}

		// -------------------------------------
		//	We need an email
		// -------------------------------------

		if (! ee()->input->post('email'))
		{
			return ee()->output->show_user_error('submission', array(lang('invalid_email_address')));
		}

		ee()->load->helper('email');
		if (! valid_email(ee()->input->post('email')))
		{
			return ee()->output->show_user_error('submission', array(lang('invalid_email_address')));
		}

		// -------------------------------------
		//	Set some default vars
		// -------------------------------------

		$site_name	= stripslashes(ee()->config->item('site_name'));
		$return 	= ee()->config->item('site_url');
		$email		= strip_tags(ee()->input->post('email'));

		// -------------------------------------
		//	Get the member
		// -------------------------------------

		$member	= ee('Model')
			->get('Member')
			->filter('email', $email)
			->first();

		// -------------------------------------
		//	If we can't find the member, return
		//	the message that, if they were actually
		//	a member we will send their reset
		//	otherwise whatever. We want a vague error for hackers.
		// -------------------------------------

		if (! $member)
		{
			return ee()->output->show_user_error('submission', array(lang('invalid_email_address')));
		}

		$member_id	= $member->member_id;
		$name 		= ($member->screen_name == '') ? $member->username: $member->screen_name;
		$username 	= $member->username;

		// -------------------------------------
		//	Prune
		// -------------------------------------

		$a_day_ago = time() - (60*60*24);
		$this->fetch('ResetPassword')
			->filter('date', '<', $a_day_ago)
			->delete();

		// -------------------------------------
		//	Check flood control
		// -------------------------------------

		$max_requests_in_a_day = 3;
		$requests	= $this->fetch('ResetPassword')
			->filter('member_id', $member_id)
			->count();

		if ($requests >= $max_requests_in_a_day)
		{
			$this->show_error(lang('password_reset_flood_lock'));
		}

		// -------------------------------------
		//	Create new code and save it
		// -------------------------------------

		$token	= ee()->functions->random('alnum', 8);
		$reset	= ee('Model')
			->make('ResetPassword')
			->set(array('member_id' => $member_id, 'resetcode' => $token, 'date' => time()))
			->save();

		// -------------------------------------
		//	Assemble reset url
		// -------------------------------------

		$url = $params['password_reset_template'];

		//create url for template.
		if ( preg_match("/" . LD . "\s*path=(.*?)" . RD . "/", $url, $match))
		{
			$url = $match['1'];
		}

		//replace ID placeholder or append to end.
		if (stristr($url, '%id%'))
		{
			$url = str_replace('%id%', $token, $url);
		}
		else
		{
			$url = rtrim($url, '/') . '/' . $token;
		}

		if ( ! stristr($url, "http://") &&
			 ! stristr($url, "https://")
		)
		{
			$url = ee()->functions->create_url($url);
		}

		// -------------------------------------
		//	secure action?
		// -------------------------------------

		if (isset($params['secure_reset_link']) &&
			$this->check_yes($params['secure_reset_link'])
		)
		{
			$url = str_replace('http://', 'https://', $url);
		}

		// -------------------------------------
		//	Prep vars
		// -------------------------------------

		$swap = array(
			'name'		=> $name,
			'username'	=> $username,
			'reset_url'	=> reduce_double_slashes($url),
			'site_name'	=> stripslashes(ee()->config->item('site_name')),
			'site_url'	=> ee()->config->item('site_url')
		);

		// -------------------------------------
		//	Fetch template
		// -------------------------------------

		$default_template = ee()->functions->fetch_email_template(
			'forgot_password_instructions'
		);

		$email_template	= '';
		$email_subject	= $default_template['title'];

		//custom template?
		if (isset($params['password_reset_email_template']))
		{
			$fetcher	= new TemplateFetcher();
			$email_template = $fetcher->fetch_template(
				$params['password_reset_email_template']
			);
		}

		if ($email_template == '')
		{
			$email_template = $default_template['data'];
		}

		// -------------------------------------
		//	Subject
		// -------------------------------------

		if (isset($params['password_reset_email_subject']))
		{
			$email_subject = $params['password_reset_email_subject'];
		}

		// -------------------------------------
		//	Parse
		// -------------------------------------

		$email_subject = ee()->functions->var_swap($email_subject, $swap);
		$email_template = ee()->functions->var_swap($email_template, $swap);

		// -------------------------------------
		//	Instantiate email class
		// -------------------------------------

		ee()->load->library('email');
		ee()->email->wordwrap = true;
		ee()->email->from(
			ee()->config->item('webmaster_email'),
			ee()->config->item('webmaster_name')
		);
		ee()->email->to($email);
		ee()->email->subject($email_subject);
		ee()->email->message($email_template);
		$out	= ee()->email->send();

		// -------------------------------------
		//	Return redirect
		// -------------------------------------

		$return = ee()->input->get_post('return');

		if (empty($return))
		{
			$return = ee()->input->get_post('RET');
		}

		if (empty($return))
		{
			$return = ee()->functions->fetch_site_index(0, 0);
		}

		ee()->functions->redirect($return);
	}

	//END send_reset_token()

}
//END Class ResetPassword
