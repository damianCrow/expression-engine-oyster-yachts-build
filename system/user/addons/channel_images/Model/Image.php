<?php

namespace DevDemon\ChannelImages\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

class Image extends Model {

    protected static $_primary_key = 'image_id';
    protected static $_table_name = 'channel_images';

    // Some Caches
    protected static $_cachedChannelToField = array();

    protected static $_typed_columns = array(
        'image_id'        => 'int',
        'site_id'         => 'int',
        'entry_id'        => 'int',
        'field_id'        => 'int',
        'member_id'       => 'int',
        'link_image_id'   => 'int',
        'link_entry_id'   => 'int',
        'link_channel_id' => 'int',
        'link_field_id'   => 'int',
        'upload_date'     => 'timestamp',
    );

    protected $image_id;
    protected $site_id;
    protected $entry_id;
    protected $field_id;
    protected $channel_id;
    protected $member_id;
    protected $is_draft;
    protected $link_image_id;
    protected $link_entry_id;
    protected $link_channel_id;
    protected $link_field_id;
    protected $upload_date;
    protected $cover;
    protected $image_order;
    protected $filename;
    protected $extension;
    protected $filesize;
    protected $mime;
    protected $width;
    protected $height;
    protected $title;
    protected $url_title;
    protected $description;
    protected $category;
    protected $cifield_1;
    protected $cifield_2;
    protected $cifield_3;
    protected $cifield_4;
    protected $cifield_5;
    protected $sizes_metadata;
    protected $iptc;
    protected $exif;
    protected $xmp;

    /**
     * Get Field ID
     * Since we moved to Field Based Settings, our legacy versions where not storing field_id's
     * so we need to somehow get it from the channel_id
     *
     * @param object $image
     * @access public
     * @return int - The FieldID
     */
    public function getFieldId($image=null)
    {
        if (!$image) $image = $this;

        // Easy way..
        if ($image->field_id > 0) {
            return $image->field_id;
        }

        // Hard way
        if (isset(self::$_cachedChannelToField[$image->channel_id]) == FALSE) {
            // Then we need to use the Channel ID :(
            $query = ee()->db->query("SELECT cf.field_id FROM exp_channel_fields AS cf
                                            LEFT JOIN exp_channels AS c ON c.field_group = cf.group_id
                                            WHERE c.channel_id = {$image->channel_id} AND cf.field_type = 'channel_images'");
            if ($query->num_rows() == 0)
            {
                $query->free_result();
                return 0;
            }

            self::$_cachedChannelToField[$image->channel_id] = $query->row('field_id');
            $field_id = $query->row('field_id');

            $query->free_result();
        } else {
            $field_id = self::$_cachedChannelToField[$image->channel_id];
        }

        return $field_id;
    }

    // ********************************************************************************* //

} // END CLASS

/* End of file Image.php */
/* Location: ./system/user/addons/channel_images/Model/Image.php */