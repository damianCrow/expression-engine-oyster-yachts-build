<?php

namespace DevDemon\ChannelImages\Service;

class Images
{
    public function deleteImage($image)
    {
        if (isset($image->field_id) == false) return false;

        // Grab the field settings
        $settings = ee('channel_images:Settings')->getFieldtypeSettings($image->field_id);

        // Location
        $location_type = $settings['upload_location'];
        $location_class = 'CI_Location_'.$location_type;
        $location_settings = $settings['locations'][$location_type];
        $location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';

        // Load Main Class
        if (class_exists('Image_Location') == false) require PATH_THIRD.'channel_images/locations/image_location.php';
        if (class_exists($location_class) == false) require $location_file;
        $LOC = new $location_class($location_settings);

        // Delete From DB
        ee('Model')->get('channel_images:Image')
        ->filter('image_id', $image->image_id)
        ->orFilter('link_image_id', $image->image_id)
        ->delete();

        // Is there another instance of the image still there?
        ee()->db->select('image_id');
        ee()->db->from('channel_images');
        ee()->db->where('entry_id', $image->entry_id);
        ee()->db->where('field_id', $image->field_id);
        ee()->db->where('filename', $image->filename);
        $query = ee()->db->get();

        if ($query->num_rows() == 0) {
            // Loop over all action groups
            foreach($settings['action_groups'] as $group) {
                $name = strtolower($group['group_name']);
                $name = str_replace('.'.$image->extension, "__{$name}.{$image->extension}", $image->filename);

                $res = $LOC->delete_file($image->entry_id, $name);
            }

            // Delete original file from system
            $res = $LOC->delete_file($image->entry_id, $image->filename);
        }

        return true;
    }
}

/* End of file Images.php */
/* Location: ./system/user/addons/channel_images/Service/Images.php */