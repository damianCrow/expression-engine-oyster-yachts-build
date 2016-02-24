<?php
namespace Zenbu\librairies\platform\ee;

use Zenbu\librairies\Settings as Settings;
use Zenbu\librairies\platform\ee\Lang;
use Zenbu\librairies\platform\ee\Session;
use Zenbu\librairies\platform\ee\Cache;
use Zenbu\librairies\platform\ee\Url;
use Zenbu\librairies\Fields;
use Zenbu\librairies\ArrayHelper;

class Base
{
	var $display_settings;
	var $general_settings;
	var $permissions;
	var $session;
	var $settings;
	var $user;
	
	public function __construct($child_class = FALSE)
	{
		$this->session = new Session();
		$this->user    = Session::user();
		$this->cache   = new Cache();
		$this->permission_keys = array(
            'can_admin',
            'can_copy_profile',
            'can_access_settings',
            'edit_replace',
            'can_view_group_searches',
            'can_admin_group_searches'
            );
        $this->general_settings_keys = array(
            'default_1st_filter',
            'default_limit',
            'default_sort',
            'default_order',
            'enable_hidden_field_search',
            'max_results_per_page'
            );
	}

	public function init($init = '')
	{
		if( $init == 'settings' || (is_array($init) && in_array('settings', $init)))
		{
			$this->settings         = new Settings();
			$this->display_settings = $this->settings->getDisplaySettings();
			$this->general_settings = $this->settings->getGeneralSettings();
			$this->permissions      = $this->settings->getPermissions();
		}

		if( $init == 'fields' || (is_array($init) && in_array('fields', $init)))
		{
			$this->fields         = new Fields();
			$this->fieldtypes = ArrayHelper::flatten_to_key_val('field_id', 'field_type', $this->fields->getFields());
			$this->field_settings = ArrayHelper::flatten_to_key_val('field_id', 'field_settings', $this->fields->getFields());
			$this->field_ids = ArrayHelper::make_array_of('field_id', $this->fields->getFields());
		}
	}
}