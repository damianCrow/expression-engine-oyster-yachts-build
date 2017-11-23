<?php

namespace Solspace\Addons\User\Model;

class Preference extends BaseModel
{
	protected static $_primary_key	= 'preference_id';
	protected static $_table_name	= 'exp_user_preferences';

	protected $preference_id;
	protected $preference_name;
	protected $preference_value;
	protected $site_id;

	protected $_default_prefs = array(
		'email_is_username'	=> array(
			'type'		=> 'yes_no',
			'default'	=> 'n',
			'validate'	=> 'enum[y,n]'
		),
		'screen_name_override'	=> array(
			'type'		=> 'text',
			'default'	=> '',
		),
		'category_groups'	=> array(
			'type'		=> 'multiselect|category_group',
			'default'	=> '',
		),
		'key_expiration'	=> array(
			'type'		=> 'text',
			'attrs'		=> ' style="width:20%"',
			'default'	=> '',
		),
		'welcome_email_subject'	=> array(
			'type'		=> 'text',
			'group'		=> 'email_preferences',
			'default'	=> '',
		),
		'welcome_email_content'	=> array(
			'type'		=> 'textarea',
			'group'		=> 'email_preferences',
			'attrs'		=> ' style="height:200px"',
			'default'	=> '',
		),
		'member_update_admin_notification_emails'	=> array(
			'type'		=> 'textarea',
			'group'		=> 'email_preferences',
			'attrs'		=> ' style="height:50px"',
			'default'	=> '',
		),
		'member_update_admin_notification_template'	=> array(
			'type'		=> 'textarea',
			'group'		=> 'email_preferences',
			'attrs'		=> ' style="height:200px"',
			'default'	=> '',
		),
		'user_forgot_username_message'	=> array(
			'type'		=> 'textarea',
			'group'		=> 'email_preferences',
			'attrs'		=> ' style="height:200px"',
			'default'	=> '',
		),
	);

	protected $_channel_sync_prefs = array(
		'channel_sync_channel'	=> array(
			'type'		=> 'select|channel',
			'default'	=> ''
		),
		'sync_member_to_channel'	=> array(
			'type'		=> 'yes_no',
			'group'		=> 'channel_sync',
			'default'	=> 'n',
			'validate'	=> 'enum[y,n]'
		),
		'delete_entry_on_member_delete'	=> array(
			'type'		=> 'yes_no',
			'group'		=> 'channel_sync',
			'default'	=> 'n',
			'validate'	=> 'enum[y,n]'
		),
		'channel_sync_field_map'	=> array(
			'type'		=> 'table',
			'group'		=> 'channel_sync',
			'default'	=> '',
		),
	);

	// --------------------------------------------------------------------

	/**
	 * Validate Default Prefs
	 *
	 * Since we often use multiple prefs, lets validate default prefs
	 * and leave alone the ones not meant to be in the prefs page
	 *
	 * @access	public
	 * @param	array	$inputs		incoming inputs to validate
	 * @param	array	$required	array of names of required items
	 * @return	object				instance of validator result
	 */

	public function validateDefaultPrefs($inputs = array(), $required = array())
	{
		//not a typo, see get__default_prefs
		$prefsData = $this->default_prefs;

		$rules = array();

		foreach ($prefsData as $name => $data)
		{
			if (isset($data['validate']))
			{
				$r = (in_array($name, $required)) ? 'required|' : '';

				$rules[$name] = $r . $data['validate'];
			}
		}

		return ee('Validation')->make($rules)->validate($inputs);
	}

	//END validateDefaultPrefs

	// --------------------------------------------------------------------

	/**
	 * Getter: default_prefs
	 *
	 * loads items with lang lines and choices before sending off.
	 * (Requires ee('Model')->make() to access.)
	 *
	 * @access	public
	 * @return	array		key->value array of pref names and defaults
	 */

	public function get__default_prefs()
	{
		//just in case this gets removed in the future.
		if (isset(ee()->lang) && method_exists(ee()->lang, 'loadfile'))
		{
			ee()->lang->loadfile('user');
		}

		$prefs = $this->_default_prefs;

		return $prefs;
	}

	//END get__default_prefs

	// --------------------------------------------------------------------

	/**
	 * get_channel_sync_prefs()
	 *
	 * @access	public
	 * @return	array		key->value array of pref names and defaults
	 */

	public function get_channel_sync_prefs()
	{
		return $this->_channel_sync_prefs;
	}

	//END get_channel_sync_prefs()
}
//END Preference
