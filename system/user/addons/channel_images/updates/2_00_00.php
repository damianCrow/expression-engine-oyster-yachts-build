<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ChannelImagesUpdate_20000
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
        // Add the Fiel_id Column
        if (ee()->db->field_exists('field_id', 'channel_images') == false) {
            $fields = array( 'field_id' => array('type' => 'MEDIUMINT', 'unsigned' => true, 'default' => 0) );
            ee()->dbforge->add_column('channel_images', $fields, 'entry_id');
        }

        // Rename: weblog_id=>channel_id,
        if (ee()->db->field_exists('channel_id', 'channel_images') == false) {
            $fields = array( 'weblog_id' => array('name' => 'channel_id', 'type' => 'TINYINT',  'unsigned' => true, 'default' => 0),
                            );
            ee()->dbforge->modify_column('channel_images', $fields);
        }

        //order=>image_order
        if (ee()->db->field_exists('image_order', 'channel_images') == false) {
            $fields = array('`order`' => array('name' => 'image_order', 'type' => 'SMALLINT',   'unsigned' => true, 'default' => 1)
                            );
            ee()->dbforge->modify_column('channel_images', $fields);
        }
    }

    // ********************************************************************************* //

}

/* End of file 2_00_00.php */
/* Location: ./system/user/addons/channel_images/updates/2_00_00.php */