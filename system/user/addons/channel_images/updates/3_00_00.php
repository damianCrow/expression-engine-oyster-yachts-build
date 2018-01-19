<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ChannelImagesUpdate_30000
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
        // Add the link_image_id Column
        if (ee()->db->field_exists('link_image_id', 'channel_images') == false) {
            $fields = array( 'link_image_id'    => array('type' => 'INT',   'unsigned' => true, 'default' => 0) );
            ee()->dbforge->add_column('channel_images', $fields, 'channel_id');
        }

        // Add the link_entry_id Column
        if (ee()->db->field_exists('link_entry_id', 'channel_images') == false) {
            $fields = array( 'link_entry_id'    => array('type' => 'INT',   'unsigned' => true, 'default' => 0) );
            ee()->dbforge->add_column('channel_images', $fields, 'link_image_id');
        }

        // Add the mime Column
        if (ee()->db->field_exists('mime', 'channel_images') == false) {
            $fields = array( 'mime' => array('type' => 'VARCHAR',   'constraint' => '20', 'default' => '') );
            ee()->dbforge->add_column('channel_images', $fields, 'extension');
        }
    }

    // ********************************************************************************* //

}

/* End of file 3_00_00.php */
/* Location: ./system/user/addons/channel_images/updates/3_00_00.php */