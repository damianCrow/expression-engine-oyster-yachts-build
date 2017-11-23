<?php

use Solspace\Addons\User\Library\AddonBuilder;

class User_upd extends AddonBuilder
{
	public $module_actions	= array();
    public $hooks			= array();
	private $old_layout_data	= array(
		array(
			'user_authors' 	=> array(
				'solspace_user_browse_authors'		=> array(
					'visible'		=> 'true',
					'collapse'		=> 'false',
					'htmlbuttons'	=> 'false',
					'width'			=> '100%'
				)
			)
		),
		array(
			'user'			=> array(
				'user__solspace_user_browse_authors' => array(
					'visible'		=> 'true',
					'collapse'		=> 'false',
					'htmlbuttons'	=> 'false',
					'width'			=> '100%'
				)
			)
		)
	);

	// --------------------------------------------------------------------

	/**
	 * Contructor
	 *
	 * @access	public
	 * @return	null
	 */

	public function __construct()
	{
		parent::__construct('module');

		// --------------------------------------------
		//  Module Actions
		// --------------------------------------------

		$this->module_actions = array(
			'group_edit',
			'edit_profile',
			'reg',
			'retrieve_password',
			'do_logout',
			'do_search',
			'delete_account',
			'activate_member',
			'retrieve_username',
			'create_key',
			'process_reset_password'
		);

		// --------------------------------------------
		//  Extension Hooks
		// --------------------------------------------

		$default = array(
			'class' 		=> $this->extension_name,
			'settings'		=> '',
			'priority'		=> 5,
			'version'		=> $this->version,
			'enabled'		=> 'y'
		);

		$this->hooks = array(
			array_merge($default,
				array(
					'method'		=> 'cp_members_validate_members',
					'hook'			=> 'cp_members_validate_members',
					'priority'		=> 1
				)
			),
			array_merge($default,
				array(
					'method'		=> 'channel_form_submit_entry_start',
					'hook'			=> 'channel_form_submit_entry_start'
				)
			),
			array_merge($default,
				array(
					'method'		=> 'channel_form_submit_entry_end',
					'hook'			=> 'channel_form_submit_entry_end'
				)
			),
			array_merge($default,
				array(
					'method'		=> 'cp_members_member_delete_end',
					'hook'			=> 'cp_members_member_delete_end'
				)
			),
			array_merge($default,
				array(
					'method'		=> 'cp_members_member_create',
					'hook'			=> 'cp_members_member_create'
				)
			),
			array_merge($default,
				array(
					'method'		=> 'member_create_end',
					'hook'			=> 'member_create_end'
				)
			),
			array_merge($default,
				array(
					'method'		=> 'member_update_end',
					'hook'			=> 'member_update_end'
				)
			),
			array_merge($default,
				array(
					'method'		=> 'user_register_end',
					'hook'			=> 'user_register_end'
				)
			),
		);
	}
	// END __construct


	// --------------------------------------------------------------------

	/**
	 * Module Installer
	 *
	 * @access	public
	 * @return	bool
	 */

	public function install()
	{
		// Already installed, let's not install again.
		if ($this->database_version() !== FALSE)
		{
			return FALSE;
		}

		// --------------------------------------------
		//  Our Default Install
		// --------------------------------------------

		if ($this->default_module_install() == FALSE)
		{
			return FALSE;
		}

		// --------------------------------------------
		//  Add Profile Views Field to exp_members
		// --------------------------------------------

		if (! ee()->db->field_exists('profile_views', 'exp_members'))
		{
			ee()->db->query(
				"ALTER TABLE exp_members
				 ADD (profile_views int(10) unsigned  NOT NULL DEFAULT '0')"
			);
		}

		// --------------------------------------------
		//  Default Preferences
		// --------------------------------------------

		$forgot_username = "Hi {screen_name},\n\n" .
			"Per your request, this email contains your " .
			"username for {site_name} located at {site_url}.\n\n" .
			"Username: {username}";

		$prefs = array(
			'email_is_username' 						=> 'n',
			'screen_name_override'						=> '',
			'category_groups'							=> '',
			'welcome_email_subject'						=> '',
			'welcome_email_content'						=> '',
			'user_forgot_username_message'				=> $forgot_username,
			'member_update_admin_notification_template'	=> '',
			'member_update_admin_notification_emails'	=> '',
			'key_expiration'							=> 7
		);

		foreach($prefs as $pref => $default)
		{
			ee()->db->insert(
				'exp_user_preferences',
				array(
					'preference_name'	=> $pref,
					'preference_value'	=> $default
				)
			);
		}

		// --------------------------------------------
		//  Module Install
		// --------------------------------------------

		ee()->db->insert(
			'exp_modules',
			array(
				'module_name'			=> $this->class_name,
				'module_version'		=> $this->version,
				'has_cp_backend'		=> 'y',
				'has_publish_fields'	=> 'n'
			)
		);

		return TRUE;
	}

	// END install()

	// --------------------------------------------------------------------

	/**
	 * Module Uninstaller
	 *
	 * @access	public
	 * @return	bool
	 */

	public function uninstall()
	{
		// Cannot uninstall what does not exist, right?
		if ($this->database_version() === FALSE)
		{
			return FALSE;
		}

		//--------------------------------------------
		//	remove tabs
		//--------------------------------------------

		$this->remove_user_tabs();

		//--------------------------------------------
		//	Drop Profile Views Field from exp_members
		//--------------------------------------------

		ee()->db->query("ALTER TABLE `exp_members` DROP `profile_views`");

		//--------------------------------------------
		//	Default Module Uninstall
		//--------------------------------------------

		if ($this->default_module_uninstall() == FALSE)
		{
			return FALSE;
		}

		return TRUE;
	}

	// END uninstall


	// --------------------------------------------------------------------

	/**
	 * Module Updater
	 *
	 * @access	public
	 * @return	bool
	 */

	public function update($current = "")
	{
		if ($current == $this->version)
		{
			return FALSE;
		}

		// --------------------------------------------
		//  User 2.0.2 Upgrade
		// --------------------------------------------

		if (version_compare($this->database_version(), '2.0.2', '<'))
		{
			ee()->db->query("ALTER TABLE `exp_user_keys` ADD INDEX (`author_id`)");
			ee()->db->query("ALTER TABLE `exp_user_keys` ADD INDEX (`member_id`)");
			ee()->db->query("ALTER TABLE `exp_user_keys` ADD INDEX (`group_id`)");
			ee()->db->query("ALTER TABLE `exp_user_params` ADD INDEX (`hash`)");
		}

		// --------------------------------------------
		// Hermes Conversion
		//  - Added: 3.0.0
		//  - Perform prior to aob default Update
		// --------------------------------------------

		if (version_compare($this->database_version(), '3.0.0', '<'))
		{
			ee()->db->update(
				'exp_extensions',
				array('class' => 'User_extension'),
				array('class' => 'User_ext')
			);

			// --------------------------------------------
			//  Move Preferences Out of Config.php
			// --------------------------------------------

			$prefs = array(
				'user_email_is_username'		=> 'email_is_username',
				'user_screen_name_override'		=> 'screen_name_override',
				'user_category_group'			=> 'category_groups',
				'user_module_key_expiration'	=> 'key_expiration'
			);

			foreach($prefs as $pref => $new_pref)
			{
				if (ee()->config->item($pref) !== FALSE)
				{
					$query = ee()->db
								->select('preference_value')
								->where('preference_name', $new_pref)
								->where("preference_value != ''")
								->limit(1)
								->get('user_preferences');

					if ($query->num_rows() == 0)
					{
						ee()->db->insert(
							'exp_user_preferences',
							array(
								'preference_name'	=> $new_pref,
								'preference_value'	=> ee()->config->item($pref)
							)
						);
					}

					//in case this ever goes private, as it's like to do
					if (is_callable(array(ee()->config, '_update_config')))
					{
						ee()->config->_update_config(array(), array($pref));
					}
				}
			}
		}

		// --------------------------------------------
		//  Welcome Email Subject - 3.0.2.d2
		// --------------------------------------------

		if (version_compare($this->database_version(), '3.0.2', '<'))
		{
			ee()->db->insert(
				'exp_user_preferences',
				array(
					'preference_name'	=> 'welcome_email_subject',
					'preference_value'	=> lang('welcome_email_content')
				)
			);
		}

		// --------------------------------------------
		//  Key Expiration - 3.1.0.d2
		// --------------------------------------------

		if (version_compare($this->database_version(), '3.1.0', '<'))
		{
			ee()->db->insert(
				'exp_user_preferences',
				array(
					'preference_name'	=> 'key_expiration',
					'preference_value'	=> 7
				)
			);
		}

		//remove old tab style from everything
		if (version_compare($this->database_version(), '3.3.2', '<'))
		{
			ee()->load->library('layout');
			//remove original layout tabs
			$this->remove_user_tabs();

			//check and see if we need to install the newest tabs
			//we want a non-cached set
			$tab_channel_ids = $this->model('data')->get_channel_ids(FALSE);

			//if we already have tabs named, we need to reinstall them
			if ($tab_channel_ids !== FALSE AND
				is_array($tab_channel_ids) AND
				! empty($tab_channel_ids))
			{
				ee()->layout->add_layout_tabs(
					$this->tabs(),
					'',
					array_keys($tab_channel_ids)
				);
			}
		}

		//Roles! ::jazz hands::
		if (version_compare($this->database_version(), '4.0.0', '<'))
		{
			// -------------------------------------
			//	Add order column to user_authors
			// -------------------------------------

			if (! ee()->db->field_exists('order', 'exp_user_authors'))
			{
				ee()->db->query(
					"ALTER TABLE exp_user_authors
					 ADD COLUMN (`order` int(10) unsigned NOT NULL DEFAULT '0')"
				);
			}

			// -------------------------------------
			//	roles installs
			// -------------------------------------

			$roles_tables = array(
				"exp_user_roles",
				"exp_user_roles_permissions",
				"exp_user_roles_assigned",
				"exp_user_roles_inherits",
				"exp_user_roles_entry_permissions",
				"exp_user_member_channel_entries",
				//MUST BE LAST (stupid foreign keys)
				"exp_user_roles_permissions_assigned"
			);

			$module_install_sql = file_get_contents(
				$this->addon_path . 'db.' . $this->lower_name . '.sql'
			);

			foreach ($roles_tables as $role_table)
			{
				if (ee()->db->table_exists($role_table) === FALSE)
				{
					$prefs_table = stristr(
						$module_install_sql,
						"CREATE TABLE IF NOT EXISTS `" . $role_table ."`"
					);

					$prefs_table = substr(
						$prefs_table,
						0,
						stripos($prefs_table, ';;')
					);

					//install it
					ee()->db->query($prefs_table);
				}
			}

			//only doing this on upgrade so its easier
			//on people that want to use this up front
			//plus on the off chance they use user
			//authors
			$this->install_user_channel_field();
		}

		//--------------------------------------------
		//	Remove tabs. Gone forever.
		//	(User 4.0.0)
		//--------------------------------------------

		$this->remove_user_tabs();

		// --------------------------------------------
		//  Default Module Update
		// --------------------------------------------

		$this->default_module_update();

		// --------------------------------------------
		//  Version Number Update - LAST!
		// --------------------------------------------

		$data = array(
			'module_version'		=> $this->version,
			'has_publish_fields'	=> 'n'
		);

		ee()->db->update(
			'exp_modules',
			$data,
			array(
				'module_name'	=> $this->class_name
			)
		);

		return TRUE;
	}
	// END update

	// --------------------------------------------------------------------

	/**
	 *	remove all tabs, old and new, from layouts
	 *
	 *	@access		public
	 *	@return		null
	 */
	public function remove_user_tabs()
	{
		ee()->load->library('layout');

		ee()->layout->delete_layout_tabs(
			array_merge_recursive($this->old_layout_data, $this->tabs())
		);

		ee()->layout->delete_layout_fields(
			array_merge_recursive($this->old_layout_data, $this->tabs())
		);
	}
	//END remove_user_tabs()


	// --------------------------------------------------------------------

	/**
	 *	tabs
	 *
	 *	returns tab for user. we replace the name choice with JS later
	 *
	 *
	 *	@access		public
	 *	@return		array
	 */

	public function tabs()
	{
		return array(
			'user' => array(
				'user__solspace_user_browse_authors' => array(
					'visible'		=> 'true',
					'collapse'		=> 'false',
					'htmlbuttons'	=> 'false',
					'width'			=> '100%'
				),
				'user__solspace_user_primary_author' => array(
					'visible'		=> 'true',
					'collapse'		=> 'false',
					'htmlbuttons'	=> 'false',
					'width'			=> '100%'
				)
			)
		);
	}
	// END tabs()

	// --------------------------------------------------------------------

	/**
	 * Uninstall User Channel Fieldtype
	 *
	 * @access	public
	 * @return	null
	 */

	public function uninstall_user_channel_field ()
	{
		ee()->load->library('addons/addons_installer');
		ee()->load->model('addons_model');

		if (ee()->addons_model->fieldtype_installed($this->lower_name))
		{
			ee()->addons_installer->uninstall($this->lower_name, 'fieldtype', FALSE);
		}
	}
	//END uninstall_user_channel_field


	// --------------------------------------------------------------------

	/**
	 * Install User Channel Fieldtype
	 *
	 * @access	public
	 * @return	null
	 */

	public function install_user_channel_field()
	{
		ee()->load->library('addons/addons_installer');
		ee()->load->model('addons_model');

		if ( ! ee()->addons_model->fieldtype_installed($this->lower_name))
		{
			ee()->addons_installer->install($this->lower_name, 'fieldtype', FALSE);
		}
	}
	//END install_user_channel_field
}
// END Class
