<?php

namespace DevDemon\ChannelImages\Service;

class Settings
{

    public $site_id;
    public $moduleClassName = CHANNEL_IMAGES_CLASS_NAME;
    public $moduleConfigName = CHANNEL_IMAGES_CLASS_NAME;
    public $setting; // Current Site Settings
    public $config; // Config overrides
    public $moduleSettings; // Cached version of the module settings
    protected $fieldSettingsCache = array();

    /**
     * Constructor
     */
    public function __construct($addon)
    {
        // Current Site ID
        $this->site_id = ee()->config->item('site_id');

        // Current Module Settings
        $this->settings = $this->getModuleSettings($this->site_id);
    }

    public function getModuleSettings($site_id=false)
    {
        if (!$site_id) $site_id = $this->site_id;

        // No Module settings yet? grab it and cache it
        if ($this->moduleSettings === null) {
            $query = ee()->db->select('settings')->where('module_name', ucfirst($this->moduleClassName))->get('modules');

            if ($query->num_rows() > 0) {
                if ($this->isSerialized($query->row('settings'))) {
                    $this->moduleSettings = @unserialize($query->row('settings'));
                } else {
                    $this->moduleSettings = json_decode($query->row('settings'), true);
                }
            }

            if (!$this->moduleSettings) {
                $this->moduleSettings = array();
            }
        }

        // Current Site Settings
        $settings = isset($this->moduleSettings['site:'.$site_id]) ? $this->moduleSettings['site:'.$site_id] : array();

        // Get the default module settings
        $defaultSettings = ee('App')->get($this->moduleClassName)->get('settings_module');

        // Merge the default module settings with the current site settings
        $settings = $this->array_extend($defaultSettings, $settings);

        // Get Config Overrides
        if ($this->config === null) {
            $this->config = ee()->config->item($this->moduleConfigName);
        }

        // Make sure it's an array
        if (is_array($this->config) === false) {
            $this->config = array();
        }

        if (empty($this->config) === false) {
            $settings = $this->array_extend($settings, $this->config);
        }

        return $settings;
    }

    public function saveModuleSettings($settings, $site_id=false)
    {
        if (!$site_id) $site_id = $this->site_id;
        $dbSettings = false;

        $query = ee()->db->select('settings')->where('module_name', ucfirst($this->moduleClassName))->get('modules');

        if ($query->num_rows() > 0) {
            if ($this->isSerialized($query->row('settings'))) {
                $dbSettings = @unserialize($query->row('settings'));
            } else {
                $dbSettings = json_decode($query->row('settings'), true);
            }
        }

        if (!$dbSettings) {
            $dbSettings = array();
        }

        $dbSettings['site:'.$site_id] = $settings;

        ee()->db->set('settings', json_encode($dbSettings));
        ee()->db->where('module_name', ucfirst($this->moduleClassName));
        ee()->db->update('modules');
    }

    public function getFieldtypeSettings($field_id=false)
    {
        $defaultSettings = ee('App')->get($this->moduleClassName)->get('settings_fieldtype');

        if (!$field_id) return $defaultSettings;

        if (!isset($this->fieldSettingsCache[$field_id])) {

            $field = ee('Model')->get('ChannelField')->filter('field_id', $field_id)->first();
            if ($field) {
                $settings = $field->getSettingsValues();
            }
            
            // Empty? Let's make it then
            if (!$settings || !is_array($settings)) {
                $settings = array();
            }

            if (!isset($settings['field_settings'])) {
                $settings['field_settings'] = array();
            }

            $settings = $settings['field_settings'];

            if (isset($settings[$this->moduleConfigName])) {
                $settings = $settings[$this->moduleConfigName];
            }

            $settings = $this->array_extend($defaultSettings, $settings);

            // Any overrides?
            if (isset($this->config['fields'][$field_id]) === true && is_array($this->config['fields'][$field_id]) === true) {
                $settings = $this->array_extend($settings, $this->config['fields'][$field_id]);
                $settings['override'] = $this->config['fields'][$field_id];
            }

            $this->fieldSettingsCache[$field_id] = $settings;
        } else {
            $settings = $this->fieldSettingsCache[$field_id];
        }

        return $settings;
    }

    protected function isSerialized($str)
    {
        return ($str == serialize(false) || @unserialize($str) !== false);
    }

    /**
     * Array Extend
     * "Extend" recursively array $a with array $b values (no deletion in $a, just added and updated values)
     * @param array $a
     * @param array $b
     */
    protected function array_extend($a, $b)
    {
        foreach($b as $k=>$v) {
            if (is_array($v) ) {
                if (!isset($a[$k])) {
                    $a[$k] = $v;
                } else {
                    $a[$k] = $this->array_extend($a[$k], $v);
                }
            } else {
                $a[$k] = $v;
            }
        }

        return $a;
    }
}

/* End of file Settings.php */
/* Location: ./system/user/addons/channel_images/Service/Settings.php */