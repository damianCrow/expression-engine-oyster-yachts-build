<?php

use Solspace\Addons\User\Library\Fieldtype;

class User_channel_sync_fieldtype extends EE_Fieldtype
{
	public	$info	= array(
		'name'		=> 'User',
		'version'	=> ''
	);

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access	public
	 */

	public function __construct()
	{
		parent::__construct();

		$this->info			= require PATH_THIRD . 'user/addon.setup.php';

		$this->field_id 	= isset($this->settings['field_id']) ?
								$this->settings['field_id'] :
								$this->field_id;
		$this->field_name 	= isset($this->settings['field_name']) ?
								$this->settings['field_name'] :
								$this->field_name;

		$this->uob	= new Fieldtype();
	}
	//END __construct


	// --------------------------------------------------------------------

	/**
	 * displays field for publish/saef
	 *
	 * @access	public
	 * @param	string	$data	any incoming data from the channel entry
	 * @return	string	html output view
	 */

	public function display_field($data)
	{
		$output = '';

		$member_data = '';

		$entry_id	= ($this->content_id) ?: ee()->input->get('entry_id');

		if ($entry_id > 0)
		{
			$member	= $this->uob->fetch('MemberChannelEntry')
				->with('Member')
				->filter('site_id', ee()->config->item('site_id'))
				->filter('entry_id', $entry_id)
				->first();

			if ($member)
			{
				$out['member_id']	= $member->member_id;
				$out['username']	= $member->Member->username;
				$out['screen_name']	= $member->Member->screen_name;
				$out['email']		= $member->Member->email;
				$member_data	= $this->uob->view('channel_sync_member_info', $out);
			}
		}

		if (empty($member_data))
		{
			$output .= '<p><strong>' . lang('no_member_assigned_entry') . '</strong></p>';
		}
		else
		{
			$output .= $member_data;;
		}

		return $output;
	}
	//END display_field
}
//END User_ft
