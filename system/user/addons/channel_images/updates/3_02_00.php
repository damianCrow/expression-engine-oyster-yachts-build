<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ChannelImagesUpdate_30200
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
        // Add the link_channel_id Column
        if (ee()->db->field_exists('link_channel_id', 'channel_images') == false) {
            $fields = array( 'link_channel_id'  => array('type' => 'INT',   'unsigned' => true, 'default' => 0) );
            ee()->dbforge->add_column('channel_images', $fields, 'link_entry_id');
        }

        // Add the link_field_id Column
        if (ee()->db->field_exists('link_field_id', 'channel_images') == false) {
            $fields = array( 'link_field_id'    => array('type' => 'INT',   'unsigned' => true, 'default' => 0) );
            ee()->dbforge->add_column('channel_images', $fields, 'link_channel_id');
        }
    }

    // ********************************************************************************* //

}

/* End of file 3_02_00.php */
/* Location: ./system/user/addons/channel_images/updates/3_02_00.php */