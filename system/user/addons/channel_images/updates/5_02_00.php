<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ChannelImagesUpdate_50200
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
        if (ee()->db->field_exists('is_draft', 'channel_images') == false) {
            $fields = array( 'is_draft' => array('type' => 'TINYINT', 'unsigned' => true, 'default' => 0) );
            ee()->dbforge->add_column('channel_images', $fields, 'member_id');
        }
    }

    // ********************************************************************************* //

}

/* End of file 5_02_00.php */
/* Location: ./system/user/addons/channel_images/updates/5_02_00.php */