<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ChannelImagesUpdate_40000
{

    /**
     * Constructor
     *
     * @access public
     *
     * Calls the parent constructor
     */
    public function __construct()
    {

    }

    // ********************************************************************************* //

    public function do_update()
    {
        // Drop sizes
        if (ee()->db->field_exists('sizes', 'channel_images') == true) {
            ee()->dbforge->drop_column('channel_images', 'sizes');
        }

        // Add URL TITLE
        if (ee()->db->field_exists('url_title', 'channel_images') == false) {
            $fields = array( 'url_title'=> array('type' => 'VARCHAR',   'constraint' => '250', 'default' => '') );
            ee()->dbforge->add_column('channel_images', $fields, 'title');
        }

        // Add Filesize
        if (ee()->db->field_exists('filesize', 'channel_images') == false) {
            $fields = array( 'filesize'=> array('type' => 'INT',    'unsigned' => true, 'default' => 0));
            ee()->dbforge->add_column('channel_images', $fields, 'mime');
        }

        // Add Width
        if (ee()->db->field_exists('width', 'channel_images') == false) {
            $fields = array( 'width'=> array('type' => 'SMALLINT',  'unsigned' => true, 'default' => 0));
            ee()->dbforge->add_column('channel_images', $fields, 'filesize');
        }

        // Add Height
        if (ee()->db->field_exists('height', 'channel_images') == false) {
            $fields = array( 'height'=> array('type' => 'SMALLINT', 'unsigned' => true, 'default' => 0) );
            ee()->dbforge->add_column('channel_images', $fields, 'width');
        }

        // Add CIFIELD_1
        if (ee()->db->field_exists('cifield_1', 'channel_images') == false) {
            $fields = array( 'cifield_1'=> array('type' => 'VARCHAR',   'constraint' => '250', 'default' => '') );
            ee()->dbforge->add_column('channel_images', $fields);
        }

        // Add CIFIELD_2
        if (ee()->db->field_exists('cifield_2', 'channel_images') == false) {
            $fields = array( 'cifield_2'=> array('type' => 'VARCHAR',   'constraint' => '250', 'default' => '') );
            ee()->dbforge->add_column('channel_images', $fields);
        }

        // Add CIFIELD_3
        if (ee()->db->field_exists('cifield_3', 'channel_images') == false) {
            $fields = array( 'cifield_3'=> array('type' => 'VARCHAR',   'constraint' => '250', 'default' => '') );
            ee()->dbforge->add_column('channel_images', $fields);
        }

        // Add CIFIELD_4
        if (ee()->db->field_exists('cifield_4', 'channel_images') == false) {
            $fields = array( 'cifield_4'=> array('type' => 'VARCHAR',   'constraint' => '250', 'default' => '') );
            ee()->dbforge->add_column('channel_images', $fields);
        }

        // Add CIFIELD_5
        if (ee()->db->field_exists('cifield_5', 'channel_images') == false) {
            $fields = array( 'cifield_5'=> array('type' => 'VARCHAR',   'constraint' => '250', 'default' => '') );
            ee()->dbforge->add_column('channel_images', $fields);
        }

        // We need a new action
        $module = array('class' => 'Channel_images', 'method' => 'locked_image_url' );
        ee()->db->insert('actions', $module);
    }

    // ********************************************************************************* //

}

/* End of file 4_00_00.php */
/* Location: ./system/user/addons/channel_images/updates/4_00_00.php */