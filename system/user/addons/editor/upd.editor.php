<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Install / Uninstall and updates the modules
 *
 * @package         DevDemon_Editor
 * @author          DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright       Copyright (c) 2007-2016 Parscale Media <http://www.parscale.com>
 * @license         http://www.devdemon.com/license/
 * @link            http://www.devdemon.com
 * @see             https://ellislab.com/expressionengine/user-guide/development/modules.html
 */
class Editor_upd
{
    /**
     * Module version
     *
     * @var string
     * @access public
     */
    public $version = EDITOR_VERSION;

    /**
     * Module Short Name
     *
     * @var string
     * @access private
     */
    private $module_name = EDITOR_CLASS_NAME;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Load dbforge
        ee()->load->dbforge();
    }

    // ********************************************************************************* //

    /**
     * Installs the module
     *
     * Installs the module, adding a record to the exp_modules table,
     * creates and populates and necessary database tables,
     * adds any necessary records to the exp_actions table,
     * and if custom tabs are to be used, adds those fields to any saved publish layouts
     *
     * @access public
     * @return boolean
     **/
    public function install()
    {
        //----------------------------------------
        // EXP_MODULES
        //----------------------------------------
        $module = ee('Model')->make('Module');
        $module->module_name = ucfirst($this->module_name);
        $module->module_version = $this->version;
        $module->has_cp_backend = 'y';
        $module->has_publish_fields = 'n';
        $module->save();

        //----------------------------------------
        // Actions
        //----------------------------------------
        $action = ee('Model')->make('Action');
        $action->class = ucfirst($this->module_name);
        $action->method = 'actionGeneralRouter';
        $action->csrf_exempt = 0;
        $action->save();

        $action = ee('Model')->make('Action');
        $action->class = ucfirst($this->module_name);
        $action->method = 'actionFileUpload';
        $action->csrf_exempt = 1;
        $action->save();

        //----------------------------------------
        // EXP_MODULES
        // The settings column, Ellislab should have put this one in long ago.
        // No need for a seperate preferences table for each module.
        //----------------------------------------
        if (ee()->db->field_exists('settings', 'modules') == false) {
            ee()->dbforge->add_column('modules', array('settings' => array('type' => 'TEXT') ) );
        }

        //----------------------------------------
        // EXP_EDITOR_CONFIGS
        //----------------------------------------
        $fields = array(
            'id'         => array('type' => 'INT',       'unsigned' => true, 'auto_increment' => true),
            'site_id'    => array('type' => 'SMALLINT',  'unsigned' => true, 'default' => 1),
            'label'      => array('type' => 'VARCHAR',   'constraint' => 250, 'default' => ''),
            'type'       => array('type' => 'VARCHAR',   'constraint' => 250, 'default' => ''),
            'settings'   => array('type' => 'TEXT'),
        );

        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('id', true);
        ee()->dbforge->create_table('editor_configs', true);

        //----------------------------------------
        // Add Some Defaults
        //----------------------------------------
        $config = ee('Model')->make('editor:Config');
        $config->label = 'Basic';
        $config->type = 'redactor';
        $config->settings = array(
            'buttons'        => array('format', 'bold', 'italic', 'lists', 'link'),
            'plugins'        => array('video'),
            'upload_service' => 'local',
            'files_upload_location' => '0',
            'images_upload_location' => '0',
            's3' => array(
                'files' => array('bucket' => '', 'region' => 'us-east-1'),
                'images' => array('bucket' => '', 'region' => 'us-east-1'),
                'aws_access_key' => '',
                'aws_secret_key' => '',
            ),
        );
        $config->save();

        $config = ee('Model')->make('editor:Config');
        $config->label = 'Advanced';
        $config->type = 'redactor';
        $config->settings = array(
            'buttons'        => array('format', 'bold', 'italic', 'underline', 'deleted', 'lists', 'image', 'file', 'link', 'horizontalrule'),
            'plugins'        => array('source', 'video', 'table'),
            'upload_service' => 'local',
            'files_upload_location' => '0',
            'images_upload_location' => '0',
            's3' => array(
                'files' => array('bucket' => '', 'region' => 'us-east-1'),
                'images' => array('bucket' => '', 'region' => 'us-east-1'),
                'aws_access_key' => '',
                'aws_secret_key' => '',
            ),
        );
        $config->save();

        return true;
    }

    // ********************************************************************************* //

    /**
     * Uninstalls the module
     *
     * @access public
     * @return Boolean FALSE if uninstall failed, TRUE if it was successful
     **/
    public function uninstall()
    {
        // Remove
        ee()->dbforge->drop_table('editor_configs');

        ee('Model')->get('Action')->filter('class', ucfirst($this->module_name))->all()->delete();
        ee('Model')->get('Module')->filter('module_name', ucfirst($this->module_name))->all()->delete();

        return true;
    }

    // ********************************************************************************* //

    /**
     * Updates the module
     *
     * This function is checked on any visit to the module's control panel,
     * and compares the current version number in the file to
     * the recorded version in the database.
     * This allows you to easily make database or
     * other changes as new versions of the module come out.
     *
     * @access public
     * @return Boolean FALSE if no update is necessary, TRUE if it is.
     **/
    public function update($current = '')
    {
        // Are they the same?
        if (version_compare($current, $this->version) >= 0) {
            return false;
        }

        // Two Digits? (needs to be 3)
        if (strlen($current) == 2) $current .= '0';

        $update_dir = PATH_THIRD.strtolower($this->module_name).'/updates/';

        // Does our folder exist?
        if (@is_dir($update_dir) === true) {
            // Loop over all files
            $files = @scandir($update_dir);

            if (is_array($files) == true) {
                foreach ($files as $file) {
                    if (strpos($file, '.php') === false) continue;
                    if (strpos($file, '_') === false) continue; // For legacy: XXX.php
                    if ($file == '.' OR $file == '..' OR strtolower($file) == '.ds_store') continue;

                    // Get the version number
                    $ver = substr($file, 0, -4);
                    $ver = str_replace('_', '.', $ver);

                    // We only want greater ones
                    if (version_compare($current, $ver) >= 0) continue;

                    require $update_dir . $file;
                    $class = 'EditorUpdate_' . str_replace('.', '', $ver);
                    $UPD = new $class();
                    $UPD->update();
                }
            }
        }

        // Upgrade The Module
        $module = ee('Model')->get('Module')->filter('module_name', ucfirst($this->module_name))->first();
        $module->module_version = $this->version;
        $module->save();

        // Upgrade The Fieldtype
        $fieldtype = ee('Model')->get('Fieldtype')->filter('name', $this->module_name)->first();
        $fieldtype->version = $this->version;
        $fieldtype->save();

        return true;
    }

} // END CLASS

/* End of file upd.editor.php */
/* Location: ./system/user/addons/editor/upd.editor.php */