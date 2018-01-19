<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Install / Uninstall and updates the modules
 *
 * @package         DevDemon_ChannelImages
 * @author          DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright       Copyright (c) 2007-2016 Parscale Media <http://www.parscale.com>
 * @license         http://www.devdemon.com/license/
 * @link            http://www.devdemon.com
 * @see             https://ellislab.com/expressionengine/user-guide/development/modules.html
 */
class Channel_images_upd
{
    /**
     * Module version
     *
     * @var string
     * @access public
     */
    public $version = CHANNEL_IMAGES_VERSION;

    /**
     * Module Short Name
     *
     * @var string
     * @access private
     */
    private $module_name = CHANNEL_IMAGES_CLASS_NAME;

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
        $action->method = $this->module_name . '_router';
        $action->csrf_exempt = 1;
        $action->save();

        $action = ee('Model')->make('Action');
        $action->class = ucfirst($this->module_name);
        $action->method = 'locked_image_url';
        $action->csrf_exempt = 0;
        $action->save();

        $action = ee('Model')->make('Action');
        $action->class = ucfirst($this->module_name);
        $action->method = 'simple_image_url';
        $action->csrf_exempt = 0;
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
        // EXP_CHANNEL_IMAGES
        //----------------------------------------
        $ci = array(
            'image_id'      => array('type' => 'INT',       'unsigned' => TRUE, 'auto_increment' => TRUE),
            'site_id'       => array('type' => 'TINYINT',   'unsigned' => TRUE, 'default' => 1),
            'entry_id'      => array('type' => 'INT',       'unsigned' => TRUE, 'default' => 0),
            'field_id'      => array('type' => 'MEDIUMINT', 'unsigned' => TRUE, 'default' => 0),
            'channel_id'    => array('type' => 'TINYINT',   'unsigned' => TRUE, 'default' => 0),
            'member_id'     => array('type' => 'INT',       'unsigned' => TRUE, 'default' => 0),
            'is_draft'      => array('type' => 'TINYINT',   'unsigned' => TRUE, 'default' => 0),
            'link_image_id' => array('type' => 'INT',       'unsigned' => TRUE, 'default' => 0),
            'link_entry_id' => array('type' => 'INT',       'unsigned' => TRUE, 'default' => 0),
            'link_channel_id'=> array('type' => 'INT',      'unsigned' => TRUE, 'default' => 0),
            'link_field_id' => array('type' => 'INT',       'unsigned' => TRUE, 'default' => 0),
            'upload_date'   => array('type' => 'INT',       'unsigned' => TRUE, 'default' => 0),
            'cover'         => array('type' => 'TINYINT',   'constraint' => '1', 'unsigned' => TRUE, 'default' => 0),
            'image_order'   => array('type' => 'SMALLINT',  'unsigned' => TRUE, 'default' => 1),
            'filename'      => array('type' => 'VARCHAR',   'constraint' => '250', 'default' => ''),
            'extension'     => array('type' => 'VARCHAR',   'constraint' => '20', 'default' => ''),
            'filesize'      => array('type' => 'INT',       'unsigned' => TRUE, 'default' => 0),
            'mime'          => array('type' => 'VARCHAR',   'constraint' => '20', 'default' => ''),
            'width'         => array('type' => 'SMALLINT',  'default' => 0),
            'height'        => array('type' => 'SMALLINT',  'default' => 0),
            'title'         => array('type' => 'VARCHAR',   'constraint' => '250', 'default' => ''),
            'url_title'     => array('type' => 'VARCHAR',   'constraint' => '250', 'default' => ''),
            'description'   => array('type' => 'VARCHAR',   'constraint' => '250', 'default' => ''),
            'category'      => array('type' => 'VARCHAR',   'constraint' => '250', 'default' => ''),
            'cifield_1'     => array('type' => 'VARCHAR',   'constraint' => '250', 'default' => ''),
            'cifield_2'     => array('type' => 'VARCHAR',   'constraint' => '250', 'default' => ''),
            'cifield_3'     => array('type' => 'VARCHAR',   'constraint' => '250', 'default' => ''),
            'cifield_4'     => array('type' => 'VARCHAR',   'constraint' => '250', 'default' => ''),
            'cifield_5'     => array('type' => 'VARCHAR',   'constraint' => '250', 'default' => ''),
            'sizes_metadata'=> array('type' => 'VARCHAR',   'constraint' => '250', 'default' => ''),
            'iptc'          => array('type' => 'TEXT'),
            'exif'          => array('type' => 'TEXT'),
            'xmp'           => array('type' => 'TEXT'),
        );

        ee()->dbforge->add_field($ci);
        ee()->dbforge->add_key('image_id', TRUE);
        ee()->dbforge->add_key('entry_id');
        ee()->dbforge->create_table('channel_images', TRUE);


        // Do we need to enable the extension
        //if ($this->uses_extension === TRUE) $this->extension_handler('enable');

        return TRUE;
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
        ee()->dbforge->drop_table('channel_images');

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
                    $class = 'ChannelImagesUpdate_' . str_replace('.', '', $ver);
                    $UPD = new $class();
                    $UPD->update();
                }
            }
        }

        // Upgrade The Module
        $module = $module = ee('Model')->get('Module')->filter('module_name', ucfirst($this->module_name))->first();
        $module->module_version = $this->version;
        $module->save();

        return true;
    }

} // END CLASS

/* End of file upd.channel_images.php */
/* Location: ./system/user/addons/channel_images/upd.channel_images.php */