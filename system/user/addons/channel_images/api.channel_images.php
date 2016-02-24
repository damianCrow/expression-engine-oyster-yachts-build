<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Channel Images API File
 *
 * @package         DevDemon_ChannelFiles
 * @author          DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright       Copyright (c) 2007-2010 Parscale Media <http://www.parscale.com>
 * @license         http://www.devdemon.com/license/
 * @link            http://www.devdemon.com
 */
class Channel_Images_API
{
    private $valid_mime = array('jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif');
    private $LOCS = array();

    public $last_error = array();

    /**
     * Constructor
     *
     * @access public
     *
     * Calls the parent constructor
     */
    public function __construct()
    {
        ee()->load->add_package_path(PATH_THIRD . 'channel_images/');
        ee()->load->model('channel_images_model');
        ee()->lang->loadfile('channel_images');
        $this->site_id = ee()->config->item('site_id');

        if (isset($this->channel_images) === FALSE) $this->channel_images = new stdClass();

        $this->moduleSettings = ee('channel_images:Settings')->settings;
    }

    // ********************************************************************************* //

    public function delete_image($image)
    {
        if (isset($image->field_id) == FALSE) return FALSE;

        // Grab the field settings
        $settings = ee('channel_images:Settings')->getFieldtypeSettings($image->field_id);

        // Location
        $location_type = $settings['upload_location'];
        $location_class = 'CI_Location_'.$location_type;
        $location_settings = $settings['locations'][$location_type];
        $location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';

        // Load Main Class
        if (class_exists('Image_Location') == FALSE) require PATH_THIRD.'channel_images/locations/image_location.php';
        if (class_exists($location_class) == FALSE) require $location_file;
        $LOC = new $location_class($location_settings);

        // Delete From DB
        ee()->db->where('image_id', $image->image_id);
        ee()->db->or_where('link_image_id', $image->image_id);
        ee()->db->delete('exp_channel_images');

        // Is there another instance of the image still there?
        ee()->db->select('image_id');
        ee()->db->from('exp_channel_images');
        ee()->db->where('entry_id', $image->entry_id);
        ee()->db->where('field_id', $image->field_id);
        ee()->db->where('filename', $image->filename);
        $query = ee()->db->get();

        if ($query->num_rows() == 0)
        {
            // Loop over all action groups
            foreach($settings['action_groups'] as $group)
            {
                $name = strtolower($group['group_name']);
                $name = str_replace('.'.$image->extension, "__{$name}.{$image->extension}", $image->filename);

                $res = $LOC->delete_file($image->entry_id, $name);
            }

            // Delete original file from system
            $res = $LOC->delete_file($image->entry_id, $image->filename);
        }

        return TRUE;
    }

    // ********************************************************************************* //

    public function clean_temp_dirs($field_id)
    {
        $temp_path = $this->moduleSettings['cache_path'].'channel_images/field_'.$field_id.'/';

        if (file_exists($temp_path) !== TRUE) return;

        ee()->load->helper('file');

        // Loop over all files
        $tempdirs = @scandir($temp_path);

        foreach ($tempdirs as $tempdir)
        {
            if ($tempdir == '.' OR $tempdir == '..') continue;
            if ( (ee()->localize->now - $tempdir) < 7200) continue;

            @chmod($temp_path.$tempdir, 0777);
            @delete_files($temp_path.$tempdir, TRUE);
            @rmdir($temp_path.$tempdir);
        }
    }

    // ********************************************************************************* //

    public function process_field_string($string)
    {
        $conf = ee()->config->item('channel_images');

        //ee()->firephp->log('BEFORE: '.$string);

        // Disable XSS?
        if (isset($conf['xss_field_strings']) === true && $conf['xss_field_strings'] == 'yes') {
            $string = ee()->security->xss_clean($string);
        }

        //ee()->firephp->log('AFTER XSS: '.$string);
        $string = htmlentities($string, ENT_QUOTES, "UTF-8");
        //ee()->firephp->log('AFTER HTML ENTITIES: '.$string);

        return $string;
    }

    // ********************************************************************************* //

    /**
     * Add Image
     * @param array $data image data
     * @access public
     * @return mixed - false on error or Image ID on success
     */
    public function add_image($data=array())
    {
        $error =& $this->last_error;

        if (isset($data['field_id']) === FALSE) {
            $error = 'Missing Field ID';
            return false;
        }
        if (isset($data['entry_id']) === FALSE) {
            $error = 'Missing Entry ID';
            return false;
        }

        if (isset($data['temp_key']) === FALSE) $data['temp_key'] = time();
        if (isset($data['site_id']) === FALSE) $data['site_id'] = $this->site_id;
        if (isset($data['member_id']) === FALSE) $data['member_id'] = ee()->session->userdata['member_id'];

        if (isset($data['channel_id']) === FALSE)
        {
            $query = ee()->db->select('channel_id')->from('exp_channel_titles')->where('entry_id', $data['entry_id'])->get();
            if ($query->num_rows() == 0) {
                $error = "Couldn't resolve channnel_id from entry_id";
                return false;
            }
            else $data['channel_id'] = $query->row('channel_id');
        }

        if (isset($data['image_order']) === FALSE) $data['image_order'] = 0;
        if (isset($data['title']) === FALSE) $data['title'] = '';
        if (isset($data['description']) === FALSE) $data['description'] = '';
        if (isset($data['category']) === FALSE) $data['category'] = '';
        if (isset($data['cifield_1']) === FALSE) $data['cifield_1'] = '';
        if (isset($data['cifield_2']) === FALSE) $data['cifield_2'] = '';
        if (isset($data['cifield_3']) === FALSE) $data['cifield_3'] = '';
        if (isset($data['cifield_4']) === FALSE) $data['cifield_4'] = '';
        if (isset($data['cifield_5']) === FALSE) $data['cifield_5'] = '';
        if (isset($data['extension']) === FALSE) $data['extension'] = '';

        // -----------------------------------------
        // Temp Dir to run Actions
        // -----------------------------------------
        $this->temp_dir = $this->moduleSettings['cache_path'].'channel_images/field_'.$data['field_id'].'/'.$data['temp_key'].'/';

        if (@is_dir($this->temp_dir) === FALSE)
        {
            @mkdir($this->temp_dir, 0777, true);
            @chmod($this->temp_dir, 0777);
        }

        // -----------------------------------------
        // Load Settings
        // -----------------------------------------
        $settings = ee('channel_images:Settings')->getFieldtypeSettings($data['field_id']);
        if (isset($settings['upload_location']) == FALSE)
        {
            $error = "Couldn't Find Upload Location?? It's Not Set.";
            return false;
            return FALSE;
        }

        //----------------------------------------
        // Image URL
        //----------------------------------------
        if (isset($data['image_url']) === TRUE)
        {
            if (isset($data['filename']) === FALSE OR $data['filename'] == FALSE)
            {
                $data['filename'] = basename($data['image_url']);
            }

            $data['filename'] = $this->format_filename($data['filename']);
            $data['extension'] = substr( strrchr($data['filename'], '.'), 1);
            if (isset($this->valid_mime[ $data['extension'] ]) === FALSE) {
                $error = "Not Falid MIME (extension)";
                return false;
            }
        }

        // -----------------------------------------
        // Grab all the files from the DB
        // -----------------------------------------
        ee()->db->select('*');
        ee()->db->from('exp_channel_images');
        ee()->db->where('entry_id', $data['entry_id']);
        ee()->db->where('field_id', $data['field_id']);
        ee()->db->where('filename', $data['filename']);
        ee()->db->where('is_draft', 0);
        $query = ee()->db->get();

        if ($query->num_rows() > 0)
        {
            ee()->db->set('title', $this->process_field_string($data['title']));
            ee()->db->set('url_title', $this->process_field_string( url_title(trim(strtolower($data['title']))) ));
            ee()->db->set('description', $this->process_field_string($data['description']));
            ee()->db->set('category', $this->process_field_string($data['category']));
            ee()->db->set('cifield_1', $this->process_field_string($data['cifield_1']));
            ee()->db->set('cifield_2', $this->process_field_string($data['cifield_2']));
            ee()->db->set('cifield_3', $this->process_field_string($data['cifield_3']));
            ee()->db->set('cifield_4', $this->process_field_string($data['cifield_4']));
            ee()->db->set('cifield_5', $this->process_field_string($data['cifield_5']));
            ee()->db->where('image_id', $query->row('image_id'));
            ee()->db->update('exp_channel_images');
            return $query->row('image_id');
        }


        //----------------------------------------
        // Image Data
        //----------------------------------------
        if (isset($data['image_data']) === TRUE || isset($data['image_url']) === TRUE)
        {
            if (isset($data['image_url']) === TRUE && $data['image_url'] != FALSE) {
                $data['image_data'] = ee('channel_images:Helper')->fetch_url_file($data['image_url']);
            }

            if (isset($data['filename']) === FALSE OR $data['filename'] == FALSE) {
                $error = "Not Falid MIME (extension)";
                return false;
            }

            //file_put_contents($this->temp_dir.$data['filename'], $data['image_data']);
            $img = @imagecreatefromstring(trim($data['image_data']));

            if ($img === FALSE) {
                $error = "imagecreatefromstring failed!";
                return false;
            }

            if ($data['extension'] == 'jpg') imagejpeg($img, $this->temp_dir.$data['filename'], 100);
            elseif ($data['extension'] == 'png') imagepng($img, $this->temp_dir.$data['filename']);
            elseif ($data['extension'] == 'gif') imagegif($img, $this->temp_dir.$data['filename']);
            else {
                $data['extension'] = 'jpg';
                imagejpeg($img, $this->temp_dir.$data['filename']);
            }

            @imagedestroy($img);

            @chmod($this->temp_dir.$data['filename'], 0777);
        }

        // Last Check
        if (file_exists($this->temp_dir.$data['filename']) === FALSE) {
            $error = "Tripple checked is file was in temp dir and it's not there!";
            return false;
        }
        if (isset($this->valid_mime[ $data['extension'] ]) === FALSE) {
            $error = "Tripple checked mime-type and it's not allowed";
            return false;
        }

        // File Size
        if (isset($data['filesize']) === FALSE || $data['filesize'] == FALSE)
        {
            $data['filesize'] = @filesize($this->temp_dir.$data['filename']);
        }

        // Run the Actions!
        $this->run_actions($data['filename'], $data['field_id']);
        $this->upload_images($data['entry_id'], $data['field_id']);

        $data['width'] = isset($this->metadata[ $data['filename'] ]['width']) ? $this->metadata[ $data['filename'] ]['width'] : 0;
        $data['height'] = isset($this->metadata[ $data['filename'] ]['height']) ? $this->metadata[ $data['filename'] ]['height'] : 0;

        if ($data['title'] == FALSE)
        {
            $data['title'] = ucfirst(str_replace('_', ' ', str_replace('.'.$data['extension'], '', $data['filename'])));
        }

        // -----------------------------------------
        // Parse Size Metadata!
        // -----------------------------------------
        $mt = '';
        foreach($settings['action_groups'] as $group)
        {
            $name = strtolower($group['group_name']);
            $size_filename = str_replace('.'.$data['extension'], "__{$name}.{$data['extension']}", $data['filename']);
            $mt .= $name.'|' . implode('|', $this->image_metadata[$size_filename]) . '/';
        }

        ee()->db->set('site_id', $data['site_id']);
        ee()->db->set('entry_id', $data['entry_id']);
        ee()->db->set('channel_id', $data['channel_id']);
        ee()->db->set('member_id', $data['member_id']);
        ee()->db->set('is_draft', 0);
        ee()->db->set('link_image_id', 0);
        ee()->db->set('link_entry_id', 0);
        ee()->db->set('link_channel_id', 0);
        ee()->db->set('link_field_id', 0);
        ee()->db->set('upload_date', ee()->localize->now);
        ee()->db->set('field_id', $data['field_id']);
        ee()->db->set('image_order', $data['image_order']);
        ee()->db->set('filename', $data['filename']);
        ee()->db->set('extension', $data['extension']);
        ee()->db->set('mime', $this->valid_mime[ $data['extension'] ]);
        ee()->db->set('filesize', $data['filesize']);
        ee()->db->set('width', $data['width']);
        ee()->db->set('height', $data['height']);
        ee()->db->set('title', $this->process_field_string($data['title']));
        ee()->db->set('url_title', $this->process_field_string( url_title(trim(strtolower($data['title']))) ));
        ee()->db->set('description', $this->process_field_string($data['description']));
        ee()->db->set('category', $this->process_field_string($data['category']));
        ee()->db->set('cifield_1', $this->process_field_string($data['cifield_1']));
        ee()->db->set('cifield_2', $this->process_field_string($data['cifield_2']));
        ee()->db->set('cifield_3', $this->process_field_string($data['cifield_3']));
        ee()->db->set('cifield_4', $this->process_field_string($data['cifield_4']));
        ee()->db->set('cifield_5', $this->process_field_string($data['cifield_5']));
        ee()->db->set('cover', 0);
        ee()->db->set('sizes_metadata', $mt);
        ee()->db->insert('exp_channel_images');
        $image_id = ee()->db->insert_id();

        @rmdir($this->temp_dir);

        return $image_id;
    }

    // ********************************************************************************* //

    public function run_actions($filename, $field_id, $temp_dir=FALSE)
    {
        // -----------------------------------------
        // Load Settings
        // -----------------------------------------
        $settings = ee('channel_images:Settings')->getFieldtypeSettings($field_id);
        if (isset($settings['upload_location']) == FALSE)
        {
            return FALSE;
        }

        // -----------------------------------------
        // Load Actions :O
        // -----------------------------------------
        $actions = ee('channel_images:Actions')->actions;

        // Just double check for actions groups
        if (isset($settings['action_groups']) == FALSE) $settings['action_groups'] = array();

        // Extension
        $extension = '.' . substr( strrchr($filename, '.'), 1);

        // Tempdir?
        if ($temp_dir != FALSE) $this->temp_dir = $temp_dir;

        // -----------------------------------------
        // Loop over all action groups!
        // -----------------------------------------
        foreach ($settings['action_groups'] as $group) {
            $size_name = $group['group_name'];
            $size_filename = str_replace($extension, "__{$size_name}{$extension}", $filename);

            // Make a copy of the file
            @copy($this->temp_dir.$filename, $this->temp_dir.$size_filename);
            @chmod($this->temp_dir.$size_filename, 0777);

            // -----------------------------------------
            // Loop over all Actions and RUN! OMG!
            // -----------------------------------------
            foreach($group['actions'] as $action_name => $action_settings)
            {
                // RUN!
                $actions[$action_name]->settings = $action_settings;
                $actions[$action_name]->settings['field_settings'] = $settings;
                $res = $actions[$action_name]->run($this->temp_dir.$size_filename, $this->temp_dir);

                if ($res !== TRUE)
                {
                    @unlink($this->temp_dir.$size_filename);
                    return FALSE;
                }
            }
        }

        // -----------------------------------------
        // Keep Original Image?
        // -----------------------------------------
        if (isset($settings['keep_original']) == TRUE && $settings['keep_original'] == 'no')
        {
            @unlink($this->temp_dir.$filename);
        }

        return TRUE;
    }

    // ********************************************************************************* //

    public function upload_images($entry_id, $field_id, $temp_dir=FALSE)
    {
        // -----------------------------------------
        // Load Settings
        // -----------------------------------------
        $settings = ee('channel_images:Settings')->getFieldtypeSettings($field_id);
        if (isset($settings['upload_location']) == FALSE)
        {
            return FALSE;
        }

        // Tempdir?
        if ($temp_dir != FALSE) $this->temp_dir = $temp_dir;

        // -----------------------------------------
        // Load Location
        // -----------------------------------------
        $location_type = $settings['upload_location'];
        $location_class = 'CI_Location_'.$location_type;
        $location_settings = $settings['locations'][$location_type];

        // Load Main Class
        if (class_exists('Image_Location') == FALSE) require PATH_THIRD.'channel_images/locations/image_location.php';

        // Try to load Location Class
        if (class_exists($location_class) == FALSE)
        {
            $location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';

            require $location_file;
        }

        // Init!

        $LOC = new $location_class($location_settings);

        // Create the DIR!
        $LOC->create_dir($entry_id);

        // Image Widths,Height,Filesize
        $this->image_metadata = array();

        // Try to load Location Class
        if (class_exists($location_class) == FALSE)
        {
            $location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';
            require $location_file;
        }

        // Loop over all files
        $tempfiles = @scandir($this->temp_dir);

        if (is_array($tempfiles) == TRUE)
        {
            foreach ($tempfiles as $tempfile)
            {
                if ($tempfile == '.' OR $tempfile == '..') continue;

                $file   = $this->temp_dir . '/' . $tempfile;

                $res = $LOC->upload_file($file, $tempfile, $entry_id);

                if ($res == FALSE)
                {

                }

                // Parse Image Size
                $imginfo = @getimagesize($file);

                // Metadata!
                $this->image_metadata[$tempfile] = array('width' => @$imginfo[0], 'height' => @$imginfo[1], 'size' => @filesize($file));
                @unlink($file);
            }
        }

        @rmdir($this->temp_dir);
    }

    // ********************************************************************************* //

    public function format_filename($filename)
    {
        $filename = strtolower(ee()->security->sanitize_filename($filename));
        $filename = str_replace(array(' ', '+'), array('_', ''), $filename);

        // Replace the jpeg extension
        $filename = str_replace('.jpeg', '.jpg', $filename);

        $filename = strtr(utf8_decode($filename),
           utf8_decode( 'ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'),
                        'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy');

        return $filename;
    }

    // ********************************************************************************* //

    public function convertUrlsToTags($data)
    {
        $url = ee('channel_images:Helper')->getRouterUrl('url', 'simple_image_url');
        $urls = array();

        preg_match_all("#<img.+?src=[\"'](.+?)[\"'].+?>#s", $data, $matches);

        if (isset($matches[1]) === false || empty($matches[1]) === true) return $data;

        foreach ($matches[1] as $url) {
            $item = array();
            $item['original'] = $url;

            $query = parse_url($url, PHP_URL_QUERY);
            $query = str_replace('&amp;', '&', $query);

            parse_str($query, $output);
            $item['parts'] = $output;

            $urls[] = $item;
        }

        // General Vars
        foreach ($urls as $item) {
            $var = '{ci_image';

            if (isset($item['parts']['d']) === false && isset($item['parts']['f']) === false) {
                continue;
            }

            // Temp Dir ?
            if (isset($item['parts']['temp_dir']) === true && $item['parts']['temp_dir'] == 'yes') {
                $item['parts']['d'] = 0;
            }

            if (isset($item['parts']['fid']) === true) {
                $var .= " field_id='{$item['parts']['fid']}'";
            }

            if (isset($item['parts']['d']) === true) {
                $var .= " entry_id='{$item['parts']['d']}'";
            }

            if (isset($item['parts']['f']) === true) {
                $var .= " filename='{$item['parts']['f']}'";
            }

            $var .= '}';

            $data = str_replace($item['original'], $var, $data);
        }

        return $data;
    }

    // ********************************************************************************* //

    public function generateUrlsFromTags($data, $entry_id=0, $template=false)
    {
        $vars = array();
        preg_match_all("/{ci_image (.*?)}/s", $data, $varMatches);

        if (isset($varMatches[1]) === false || empty($varMatches[1]) === true) return $data;

        foreach ($varMatches[0] as $key => $val) {
            $matches = array();
            $inner = $varMatches[1][$key];

            $inner = str_replace('&#039;', '\'', $inner);
            $inner = str_replace('&quot;', '"', $inner);

            preg_match_all("/(\S+?)\s*=\s*(\042|\047)([^\\2]*?)\\2/is", $inner, $matches, PREG_SET_ORDER);

            if (isset($matches[0][3]) === false) continue;

            $item = array();
            $item['original'] = $val;
            $item['params'] = array();

            foreach($matches as $match) {
                $item['params'][$match[1]] = (trim($match[3]) == '') ? $match[3] : trim($match[3]);
            }

            $vars[] = $item;
        }

        $url = ee('channel_images:Helper')->getRouterUrl('url', 'simple_image_url');

        foreach ($vars as $var) {
            $imgurl = $url;

            if (isset($var['params']['entry_id']) === true) {
                if ($var['params']['entry_id'] == false) $var['params']['entry_id'] = $entry_id;
                $imgurl .= '&amp;d=' . $var['params']['entry_id'];
            }

            if (isset($var['params']['field_id']) === true) {
                $imgurl .= '&amp;fid=' . $var['params']['field_id'];
            }

            if (isset($var['params']['filename']) === true) {
                $imgurl .= '&amp;f=' . $var['params']['filename'];
            }

            if ($template) {

                $field_id = $var['params']['field_id'];

                // Get the field settings
                $settings = ee('channel_images:Settings')->getFieldtypeSettings($field_id);

                //----------------------------------------
                // Load Location
                //----------------------------------------
                if (isset($this->LOCS[$field_id]) === FALSE)
                {
                    $location_type = $settings['upload_location'];
                    $location_class = 'CI_Location_'.$location_type;
                    $location_settings = $settings['locations'][$location_type];

                    // Load Main Class
                    if (class_exists('Image_Location') == FALSE) require PATH_THIRD.'channel_images/locations/image_location.php';

                    // Try to load Location Class
                    if (class_exists($location_class) == FALSE)
                    {
                        $location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';
                        require $location_file;
                    }

                    // Init!
                    $this->LOCS[$field_id] = new $location_class($location_settings);
                }

                $imgurl = $this->LOCS[$field_id]->parse_image_url($var['params']['entry_id'], $var['params']['filename']);
            }

            $data = str_replace($var['original'], $imgurl, $data);
        }

        return $data;
    }

    // ********************************************************************************* //

} // END CLASS

/* End of file api.channel_images.php  */
/* Location: ./system/user/addons/channel_images/api.channel_images.php */
