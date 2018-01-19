<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ChannelImagesUpdate_50400
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
        if (ee()->db->field_exists('iptc', 'channel_images') == false) {
            $fields = array( 'iptc' => array('type' => 'TEXT') );
            ee()->dbforge->add_column('channel_images', $fields, 'sizes_metadata');
        }

        if (ee()->db->field_exists('exif', 'channel_images') == false) {
            $fields = array( 'exif' => array('type' => 'TEXT') );
            ee()->dbforge->add_column('channel_images', $fields, 'iptc');
        }

        if (ee()->db->field_exists('xmp', 'channel_images') == false) {
            $fields = array( 'xmp'  => array('type' => 'TEXT') );
            ee()->dbforge->add_column('channel_images', $fields, 'exif');
        }
    }

    // ********************************************************************************* //

}

/* End of file 5_04_00.php */
/* Location: ./system/user/addons/channel_images/updates/5_04_00.php */