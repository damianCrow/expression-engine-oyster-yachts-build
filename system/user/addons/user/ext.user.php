<?php

use Solspace\Addons\User\Library\AddonBuilder;

class User_ext extends AddonBuilder
{
	public $name				= "User";
	public $version				= "";
	public $description			= "";
	public $settings_exist		= "n";
	public $docs_url			= "https://solspace.com/expressionengine/user/docs";
	public $settings			= array();
	public $user_base			= '';
	public $required_by			= array('module');

	/**
	 * Shim for removed extension calls
	 * that will get hit before upgrade
	 *
	 * @var	array
	 * @see	__call
	 */
	protected $removed_functions = array('ajax');

	// --------------------------------------------------------------------

	/**
	 *	Constructor
	 *
	 *	@access		public
	 *	@param		array
	 *	@return		null
	 */

	public function __construct( $settings = '' )
	{
		// --------------------------------------------
		//  Load Parent Constructor
		// --------------------------------------------

		parent::__construct('extension');

		// --------------------------------------------
		//  Settings!
		// --------------------------------------------

		$this->settings = $settings;
	}
	//	End __construct


	//required by EE but not needed
	public function activate_extension(){}
	public function disable_extension(){}
	public function update_extension(){}

	// --------------------------------------------------------------------

	/**
	 * Magic Call Method
	 *
	 * Used here to shim out removed hook calls so no errors show
	 * when we are needing to upgrade to a new version that doesn't
	 * have the removed function.
	 *
	 * @access	public
	 * @param	string	$method	desired method
	 * @param	array	$args	method ards
	 * @return	mixed			last call, or FALSE, or null if method not removed
	 */

	public function __call($method = '', $args = array())
	{
		if (in_array($method, $this->removed_functions))
		{
			return $this->get_last_call(
				( ! empty($args)) ? array_shift($args) : FALSE
			);
		}
	}
	//END __call

	// --------------------------------------------------------------------

	/**
	 * Channel Form Submit Entry Start
	 *
	 * @access	public
	 * @param	Object	$obj	incoming channel form object
	 * @return	void
	 */

	public function channel_form_submit_entry_start($obj)
	{
		if (isset($this->cache['insert_dummy_cf_error']) &&
			$this->cache['insert_dummy_cf_error'])
		{
			//if we add an error here we get a halt even
			//if things are all ok and we can force the
			//channel form lib to have a 'validate only'
			//setup.
			$obj->errors['halt'] = 'please';
		}
	}
	//END channel_form_submit_entry_start


	// --------------------------------------------------------------------

	/**
	 * Channel Form Submit Entry End
	 *
	 * Halt the channel form completion if the halt command is in
	 * our cache so the User_channel_sync lib can do its work instead.
	 *
	 * @access	public
	 * @param	Object	$obj	Channel Form Object
	 * @return	void
	 */

	public function channel_form_submit_entry_end($obj)
	{
		if (
			isset($this->cache['halt_channel_form']) &&
			$this->cache['halt_channel_form']
		)
		{
			ee()->extensions->end_script = true;

			$this->cache['channel_halted'] = true;
		}
	}
	//END channel_form_submit_entry_end


	// --------------------------------------------------------------------

	/**
	 * CP Members: Member Delete End
	 *
	 * On delete of members, send IDs to User_channel_sync lib for further
	 * processing of deletion.
	 *
	 * @access	public
	 * @param	Array	$member_ids		array of incoming member IDs to delete
	 * @return	void
	 */

	public function cp_members_member_delete_end($member_ids)
	{
		$this->lib('ChannelSync')->delete_member_channel_data($member_ids);
	}

	//END cp_members_member_delete_end()


	// --------------------------------------------------------------------

	/**
	 * CP Members Member Create
	 *
	 * @access	public
	 * @param	Int		$member_id	new member id
	 * @param	array	$data		array of new member data
	 * @return	void
	 */

	public function cp_members_member_create($member_id, $data)
	{
		if ($member_id == 0)
		{
			return;
		}

		$this->lib('ChannelSync')->sync_member_data_to_channel(
			$member_id,
			$data,
			'cp_member_create'
		);
	}

	//END cp_members_member_create

	// --------------------------------------------------------------------

	/**
	 * member_create_end()
	 *
	 * @access	public
	 * @param	Int		$member_id	new member id
	 * @param	array	$data		array of new member data
	 * @return	void
	 */

	public function member_create_end($member_id, $data, $cdata)
	{
		//if ($member_id == 0) return;

		//$this->lib('ChannelSync')->sync_member_data_to_channel($member_id, $data);
	}

	//END member_create_end()

	// --------------------------------------------------------------------

	/**
	 * member_update_end()
	 *
	 * @access	public
	 * @param	Int		$member_id	new member id
	 * @param	array	$data		array of new member data
	 * @return	void
	 */

	public function member_update_end($member_id, $data)
	{
		//if ($member_id == 0) return;

		//$this->lib('ChannelSync')->sync_member_data_to_channel($member_id, $data);
	}

	//END member_update_end()

	// --------------------------------------------------------------------

	/**
	 * user_register_end()
	 *
	 * @access	public
	 * @param	Int		$member_id	new member id
	 * @param	array	$data		array of new member data
	 * @return	void
	 */

	public function user_register_end($ths, $cust_fields, $member_id)
	{
		//	For now because of a couple of limitations in the EE API's, we can't create a channel entry when someone registers.

		return;

		if ($member_id == 0) return;

		$data	= array_merge($ths->insert_data, $cust_fields);

		$this->lib('ChannelSync')->sync_member_data_to_channel($member_id, $data);
	}

	//END user_register_end()

	// --------------------------------------------------------------------

	/**
	 *	Validate Members
	 *
	 *	@access		public
	 *	@return		null
	 */

	public function cp_members_validate_members()
	{
		if ( ! ee()->input->post('toggle') OR
			$_POST['action'] != 'activate')
		{
			return;
		}

		$member_ids = array();

		foreach ($_POST['toggle'] as $key => $val)
		{
			if ( ! is_array($val))
			{
				$member_ids[] = $val;
			}
		}

		if (count($member_ids) == 0)
		{
			return;
		}

		// ----------------------------------------
		//	Instantiate class
		// ----------------------------------------

		if ( class_exists('User') === FALSE )
		{
			require 'mod.user.php';
		}

		$User = new User();

		$User->cp_validate_members($member_ids);
	}
	// END cp_members_validate_members()
}
//	END User_ext
