<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ChannelImagesUpdate_40200
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
        // Add the member_id Column
        if (ee()->db->field_exists('member_id', 'channel_images') == false) {
            $fields = array( 'member_id'    => array('type' => 'INT',   'unsigned' => true, 'default' => 0) );
            ee()->dbforge->add_column('channel_images', $fields, 'channel_id');
        }

        // Grab all images!
        $query = ee()->db->select('ci.image_id, ct.author_id')->from('exp_channel_images ci')->join('exp_channel_titles ct', 'ct.entry_id = ci.entry_id', 'left')->get();

        foreach ($query->result() as $row) {
            ee()->db->where('image_id', $row->image_id);
            ee()->db->update('exp_channel_images', array('member_id' => $row->author_id));
        }

        $query->free_result();
    }

    // ********************************************************************************* //

}

/* End of file 4_02_00.php */
/* Location: ./system/user/addons/channel_images/updates/4_02_00.php */