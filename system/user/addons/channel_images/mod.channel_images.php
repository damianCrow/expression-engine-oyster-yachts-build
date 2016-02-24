<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Images Module Tags
 *
 * @package         DevDemon_ChannelImages
 * @author          DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright       Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license         http://www.devdemon.com/license/
 * @link            http://www.devdemon.com
 * @see             http://expressionengine.com/user_guide/development/module_tutorial.html#core_module_file
 */
class Channel_images
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
        $this->site_id = ee()->config->item('site_id');

        //ee()->load->library('image_helper');
        ee()->load->model('channel_images_model');
    }

    // ********************************************************************************* //

    public function images()
    {
        // Group By Category? Whole other parsing scheme
        if (ee()->TMPL->fetch_param('group_by_category') != FALSE) return $this->grouped_images(TRUE);

        // Entry ID
        $entry_id = ee()->TMPL->fetch_param('entry_id');

        // URL Title
        if (ee()->TMPL->fetch_param('url_title') != FALSE)
        {
            $entry_id = 9999999;
            if (ee()->TMPL->fetch_param('channel_id')) {
                $query = ee()->db->query("SELECT entry_id FROM exp_channel_titles WHERE url_title = '".ee()->TMPL->fetch_param('url_title')."' AND channel_id = '".ee()->TMPL->fetch_param('channel_id')."' LIMIT 1");
            } else {
                $query = ee()->db->query("SELECT entry_id FROM exp_channel_titles WHERE url_title = '".ee()->TMPL->fetch_param('url_title')."' LIMIT 1");
            }

            if ($query->num_rows() > 0) $entry_id = $query->row('entry_id');
        }

        // Which Fields?
        $fields = ee()->channel_images_model->get_fields_from_params(ee()->TMPL->tagparams);

        return ee()->channel_images_model->parse_template($entry_id, $fields, ee()->TMPL->tagparams, ee()->TMPL->tagdata);
    }

    // ********************************************************************************* //

    function images_static()
    {
        // Variable prefix
        $this->prefix = ee()->TMPL->fetch_param('prefix', 'image') . ':';

        // Entry ID
        $this->entry_id = ee('channel_images:Helper')->get_entry_id_from_param();

        // We need an entry_id
        if ($this->entry_id == FALSE)
        {
            ee()->TMPL->log_item('CHANNEL IMAGES: Entry ID could not be resolved');
            return ee()->TMPL->tagdata;
        }

        // Temp vars
        $final = '';

        // IMG Tag Prefix
        $img_prefix = ee()->TMPL->fetch_param('img_prefix', '');

        // IMG Tag Suffix
        $img_suffix = ee()->TMPL->fetch_param('img_suffix', '');

        // Do we have an category?
        if (ee()->TMPL->fetch_param('category') != FALSE) ee()->db->where('category', ee()->TMPL->fetch_param('category'));

        // Do we need to offset?
        if (ee()->TMPL->fetch_param('offset') != FALSE && ctype_digit(ee()->TMPL->fetch_param('offset')) != FALSE) {
            ee()->db->limit(9999, ee()->TMPL->fetch_param('offset'));
        }

        // Do we need to skip the cover image?
        if (ee()->TMPL->fetch_param('skip_cover') != FALSE)
        {
            ee()->db->where('cover', 0);
        }

        // Shoot the query
        ee()->db->select('*');
        ee()->db->from('exp_channel_images');
        ee()->db->where('entry_id', $this->entry_id);

        // Which Fields?
        $fields = ee()->channel_images_model->get_fields_from_params(ee()->TMPL->tagparams);

        if (is_array($fields) === TRUE)
        {
            ee()->db->where_in('field_id', $fields);
        }
        else
        {
            ee()->db->where('field_id', $fields);
        }

        ee()->db->order_by('image_order');
        $query = ee()->db->get();

        if ($query->num_rows() == 0)
        {
            ee()->TMPL->log_item("CHANNEL IMAGES: No images found. (Entry_ID:{$this->entry_id})");
            return ee()->TMPL->tagdata;
        }
        $images = $query->result();

        //----------------------------------------
        // SSL?
        //----------------------------------------
        $this->IS_SSL = ee('channel_images:Helper')->isSsl();

        //----------------------------------------
        // Performance :)
        //----------------------------------------
        if (isset(ee()->session->cache['ChannelImages']['Location']) == FALSE)
        {
            ee()->session->cache['ChannelImages']['Location'] = array();
        }

        $this->LOCS &= ee()->session->cache['ChannelImages']['Location'];

        // Another Check, just to be sure
        if (is_array($this->LOCS) == FALSE) $this->LOCS = array();

        // Count
        $count = 1;

        // Loop over all images
        foreach ($images as $image)
        {
            // Check for linked image!
            if ($image->link_entry_id > 0)
            {
                $image->entry_id = $image->link_entry_id;
                $image->field_id = $image->link_field_id;
            }

            // Get Field Settings!
            $settings = ee('channel_images:Settings')->getFieldtypeSettings($image->field_id);

            //----------------------------------------
            // Load Location
            //----------------------------------------
            if (isset($this->LOCS[$image->field_id]) == FALSE)
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
                $this->LOCS[$image->field_id] = new $location_class($location_settings);
            }

            //----------------------------------------
            // Check for Mime Type
            //----------------------------------------
            if ($image->mime == FALSE)
            {
                // Mime type
                $image->mime = 'image/jpeg';
                if ($image->extension == 'png') $filemime = 'image/png';
                elseif ($image->extension == 'gif') $filemime = 'image/gif';
            }

            //----------------------------------------
            // Image URL
            //----------------------------------------
            $image_url = $this->LOCS[$image->field_id]->parse_image_url($image->entry_id, $image->filename);

            // Did something go wrong?
            if ($image_url == FALSE)
            {
                ee()->TMPL->log_item('CHANNEL IMAGES: Image URL Failed for: ' . $image->entry_id.'/'.$image->filename);
                continue;
            }

            // SSL?
            if ($this->IS_SSL == TRUE)
            {
                $image_url = str_replace('http://', 'https://', $image_url);
            }

            $arr = array();
            $arr['{IMG_DESC}'] = $image->description;
            $arr['{IMG_TITLE}'] = $image->title;
            $arr['{IMG_CATEGORY}'] = $image->category;
            $arr['{IMG_FIELD_1}'] = $image->cifield_1;
            $arr['{IMG_FIELD_2}'] = $image->cifield_2;
            $arr['{IMG_FIELD_3}'] = $image->cifield_3;
            $arr['{IMG_FIELD_4}'] = $image->cifield_4;
            $arr['{IMG_FIELD_5}'] = $image->cifield_5;

            // Lets parse Description and Title once again in suffix/prefix
            $imgprefix = str_replace(array_keys($arr), array_values($arr), $img_prefix);
            $imgsuffix = str_replace(array_keys($arr), array_values($arr), $img_suffix);

            ee()->TMPL->tagdata = str_replace(
                        array(  LD.$this->prefix.$count.':id'.RD,
                                LD.$this->prefix.$count.':title'.RD,
                                LD.$this->prefix.$count.':description'.RD,
                                LD.$this->prefix.$count.':filename'.RD,
                                LD.$this->prefix.$count.':url'.RD,
                                LD.$this->prefix.$count.':secure_url'.RD,

                                LD.$this->prefix.$count.RD,
                            ),
                        array(  $image->image_id,
                                $image->title,
                                $image->description,
                                $image->filename,
                                $imgprefix.$image_url.$imgsuffix,
                                $imgprefix.$image_url.$imgsuffix,

                                $imgprefix.'<img src="'.$image_url.'" alt="'.$image->description.'">'.$imgsuffix,
                            ),
                    ee()->TMPL->tagdata);

            // get the extensions
            $extension = '.' . substr( strrchr($image->filename, '.'), 1);

            // Generate size names
            if (isset($settings['action_groups']) == TRUE AND empty($settings['action_groups']) == FALSE)
            {
                foreach ($settings['action_groups'] as $group)
                {
                    $name = strtolower($group['group_name']);
                    $newname = str_replace($extension, "__{$name}{$extension}", $image->filename);
                    $size_url = $this->LOCS[$image->field_id]->parse_image_url($image->entry_id, $newname);
                    ee()->TMPL->tagdata = str_replace(LD.$this->prefix.$count.':'.$name.RD, $imgprefix.'<img src="'.$size_url.'" alt="'.$image->description.'">'.$imgsuffix, ee()->TMPL->tagdata);
                    ee()->TMPL->tagdata = str_replace(LD.$this->prefix.$count.':url:'.$name.RD, $imgprefix.$size_url.$imgsuffix, ee()->TMPL->tagdata);
                    ee()->TMPL->tagdata = str_replace(LD.$this->prefix.$count.':secure_url:'.$name.RD, $imgprefix.$size_url.$imgsuffix, ee()->TMPL->tagdata);
                }
            }

            $count++;
        }

        return ee()->TMPL->tagdata;
    }

    // ********************************************************************************* //

    public function grouped_images($legacy=FALSE)
    {
        if ($legacy == FALSE)
        {
            // Variable prefix
            $this->prefix = ee()->TMPL->fetch_param('prefix', 'image') . ':';

            // Entry ID
            $this->entry_id = ee('channel_images:Helper')->get_entry_id_from_param();

            // We need an entry_id
            if ($this->entry_id == FALSE)
            {
                ee()->TMPL->log_item('CHANNEL IMAGES: Entry ID could not be resolved');
                return ee('channel_images:Helper')->custom_no_results_conditional($this->prefix.'no_images', ee()->TMPL->tagdata);
            }
        }


        //----------------------------------------
        // Shoot the Query
        //----------------------------------------
        ee()->db->select('*');
        ee()->db->from('exp_channel_images');
        ee()->db->where('entry_id', $this->entry_id);
        ee()->db->order_by('category', 'ASC');
        ee()->db->order_by('image_order', 'ASC');
        $query = ee()->db->get();

        if ($query->num_rows() == 0)
        {
            ee()->TMPL->log_item("CHANNEL IMAGES: No images found. (Entry_ID:{$this->entry_id})");
            return ee('channel_images:Helper')->custom_no_results_conditional($this->prefix.'no_images', ee()->TMPL->tagdata);
        }

        //----------------------------------------
        // Make the Images Var
        //----------------------------------------
        $images = $query->result();
        $query->free_result();

        //----------------------------------------
        // Grab the {images} var pair
        //----------------------------------------
        if (isset(ee()->TMPL->var_pair['images']) == FALSE)
        {
            ee()->TMPL->log_item("CHANNEL IMAGES: No {images} var pair found.");
            return ee('channel_images:Helper')->custom_no_results_conditional($this->prefix.'no_images', ee()->TMPL->tagdata);
        }

        $pair_data = ee('channel_images:Helper')->fetch_data_between_var_pairs('images', ee()->TMPL->tagdata);

        //----------------------------------------
        // Loop over all images and make a new arr
        //----------------------------------------
        $categories = array();
        foreach($images as $image)
        {
            if (trim($image->category) == FALSE) continue;
            $categories[ $image->category ][] = $image;
        }
        unset($images);

        //----------------------------------------
        // No Categories?
        //----------------------------------------
        if (empty($categories) == TRUE)
        {
            ee()->TMPL->log_item("CHANNEL IMAGES: Found images but no categories.");
            return ee('channel_images:Helper')->custom_no_results_conditional($this->prefix.'no_images', ee()->TMPL->tagdata);
        }

        //----------------------------------------
        // Sort by Category?
        //----------------------------------------
        if (strtolower(ee()->TMPL->fetch_param('category_sort')) != 'desc')
            ksort($categories);
        else krsort($categories);

        //----------------------------------------
        // Check for filesize
        // (only for Local) Since it's an expensive operation
        //----------------------------------------
        $this->parse_filesize = FALSE;
        if (strpos($pair_data, LD.$this->prefix.'filesize') !== FALSE)
        {
            $this->parse_filesize = TRUE;
        }

        //----------------------------------------
        // Check for image_dimensions
        // (only for Local) Since it's an expensive operation
        //----------------------------------------
        $this->parse_dimensions = FALSE;
        if (strpos($pair_data, LD.$this->prefix.'width') !== FALSE OR strpos($pair_data, LD.$this->prefix.'height') !== FALSE)
        {
            $this->parse_dimensions = TRUE;
        }

        //----------------------------------------
        // Switch=""
        //----------------------------------------
        $parse_switch = FALSE;
        if ( preg_match( "/".LD."({$this->prefix}switch\s*=.+?)".RD."/is", $pair_data, $switch_matches ) > 0 )
        {
            $parse_switch = TRUE;
            $switch_param = ee()->functions->assign_parameters($switch_matches['1']);
        }

        //----------------------------------------
        // Locked URL?
        //----------------------------------------
        $this->locked_url = FALSE;
        if ( strpos(ee()->TMPL->tagdata, $this->prefix.'locked_url') !== FALSE)
        {
            $this->locked_url = TRUE;

            // IP
            $this->IP = ee()->input->ip_address();

            // Grab Router URL
            $this->locked_act_url = ee('channel_images:Helper')->getRouterUrl('url', 'locked_image_url');
        }

        //----------------------------------------
        // SSL?
        //----------------------------------------
        $this->IS_SSL = ee('channel_images:Helper')->isSsl();

        //----------------------------------------
        // Performance :)
        //----------------------------------------
        if (isset(ee()->session->cache['ChannelImages']['Location']) == FALSE)
        {
            ee()->session->cache['ChannelImages']['Location'] = array();
        }

        $this->LOCS &= ee()->session->cache['ChannelImages']['Location'];

        // Another Check, just to be sure
        if (is_array($this->LOCS) == FALSE) $this->LOCS = array();


        $OUT = '';
        //----------------------------------------
        // Loop over the new array and parse
        //----------------------------------------
        foreach ($categories as $cat => $images)
        {
            $CATOUT = str_replace(LD.$this->prefix.'category'.RD, $cat, ee()->TMPL->tagdata);
            $CATIMG = '';

            $total_images = count($images);

            //----------------------------------------
            // Loop over all Images
            //----------------------------------------
            foreach ($images as $count => $image)
            {
                $temp = '';

                // Check for linked image!
                if ($image->link_entry_id > 0)
                {
                    $image->entry_id = $image->link_entry_id;
                    $image->field_id = $image->link_field_id;
                }

                // Get Field Settings!
                $settings = ee('channel_images:Settings')->getFieldtypeSettings($image->field_id);

                //----------------------------------------
                // Load Location
                //----------------------------------------
                if (isset($this->LOCS[$image->field_id]) == FALSE)
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
                    $this->LOCS[$image->field_id] = new $location_class($location_settings);
                }

                //----------------------------------------
                // Check for Mime Type
                //----------------------------------------
                if ($image->mime == FALSE)
                {
                    // Mime type
                    $image->mime = 'image/jpeg';
                    if ($image->extension == 'png') $filemime = 'image/png';
                    elseif ($image->extension == 'gif') $filemime = 'image/gif';
                }

                //----------------------------------------
                // Image URL
                //----------------------------------------
                $image_url = $this->LOCS[$image->field_id]->parse_image_url($image->entry_id, $image->filename);

                // Did something go wrong?
                if ($image_url == FALSE)
                {
                    ee()->TMPL->log_item('CHANNEL IMAGES: Image URL Failed for: ' . $image->entry_id.'/'.$image->filename);
                    continue;
                }

                // SSL?
                if ($this->IS_SSL == TRUE)
                {
                    $image_url = str_replace('http://', 'https://', $image_url);
                }

                //----------------------------------------
                // Filedir (local only)
                //----------------------------------------
                $filedir = '';
                if ($settings['upload_location'] == 'local')
                {
                    $filedir = str_replace($image->entry_id.'/'.$image->filename, '', $image_url);
                }

                $vars = array();
                $vars[$this->prefix.'count'] = $count + 1;
                $vars[$this->prefix.'total'] = $total_images;
                $vars[$this->prefix.'entry_id'] = $image->entry_id;
                $vars[$this->prefix.'channel_id'] = $image->channel_id;
                $vars[$this->prefix.'title'] = $image->title;
                $vars[$this->prefix.'url_title'] = $image->url_title;
                $vars[$this->prefix.'description'] = $image->description;
                $vars[$this->prefix.'category'] = $image->category;
                $vars[$this->prefix.'filename'] = $image->filename;
                $vars[$this->prefix.'id'] = $image->image_id;
                $vars[$this->prefix.'url'] = $image_url;
                $vars[$this->prefix.'secure_url'] = $image_url;
                $vars[$this->prefix.'file_path'] = $filedir;
                $vars[$this->prefix.'file_path_secure'] = str_replace('http://', 'https://', $filedir);
                $vars[$this->prefix.'mimetype'] = $image->mime;
                $vars[$this->prefix.'cover'] = $image->cover;
                $vars[$this->prefix.'field:1'] = $image->cifield_1;
                $vars[$this->prefix.'field:2'] = $image->cifield_2;
                $vars[$this->prefix.'field:3'] = $image->cifield_3;
                $vars[$this->prefix.'field:4'] = $image->cifield_4;
                $vars[$this->prefix.'field:5'] = $image->cifield_5;

                //----------------------------------------
                // Check for filesize, Since it's an expensive operation
                //----------------------------------------
                if ($this->parse_filesize == TRUE)
                {
                    // If filesize is not defined, lets find it (only for local files)
                    if ($image->filesize == FALSE && $settings['upload_location'] == 'local')
                    {
                        $filepath = $this->LOCS[$image->field_id]->getLocationPrefs($settings['locations']['local']['location']);
                        $filepath = $filepath->server_path  . $image->entry_id . '/' . $image->filename;
                        $image->filesize = @filesize($filepath);
                    }
                    elseif ($image->filesize == FALSE)
                    {
                        $image->filesize = 0;
                    }

                    $vars[$this->prefix.'filesize'] = ee('channel_images:Helper')->formatBytes($image->filesize);
                    $vars[$this->prefix.'filesize_bytes'] = $image->filesize;
                }

                //----------------------------------------
                // Check for image_dimensions, Since it's an expensive operation
                //----------------------------------------
                if ($this->parse_dimensions == TRUE)
                {
                    // If filesize is not defined, lets find it (only for local files)
                    if ($image->width == FALSE && $settings['upload_location'] == 'local')
                    {
                        $filepath = $this->LOCS[$image->field_id]->getLocationPrefs($settings['locations']['local']['location']);
                        $filepath = $filepath->server_path  . $image->entry_id . '/' . $image->filename;
                        $imginfo = @getimagesize($filepath);
                        $image->width = $imginfo[0];
                        $image->height = $imginfo[1];
                    }
                    elseif ($image->width == FALSE)
                    {
                        $image->width = '';
                        $image->height = '';
                    }

                    $vars[$this->prefix.'width'] = $image->width;
                    $vars[$this->prefix.'height'] = $image->height;
                }

                // -----------------------------------------
                // Locked URL
                // -----------------------------------------
                if ($this->locked_url == TRUE)
                {
                    $locked = array('image_id' => $image->image_id, 'size'=>'', 'time' => ee()->localize->now + 600, 'ip' => $this->IP);
                    $vars[$this->prefix.'locked_url'] = $this->locked_act_url . '&key=' . base64_encode(serialize($locked));
                }

                $temp = ee()->TMPL->parse_variables_row($pair_data, $vars);
                $temp = $this->parse_size_vars($temp, $settings, $image);

                // -----------------------------------------
                // Parse Switch {switch="one|twoo"}
                // -----------------------------------------
                if ($parse_switch)
                {
                    $sw = '';

                    if ( isset( $switch_param[$this->prefix.'switch'] ) !== FALSE )
                    {
                        $sopt = explode("|", $switch_param[$this->prefix.'switch']);

                        $sw = $sopt[($count + count($sopt)) % count($sopt)];
                    }

                    $temp = ee()->TMPL->swap_var_single($switch_matches['1'], $sw, $temp);
                }

                $CATIMG .= $temp;
            }

            $CATOUT = ee('channel_images:Helper')->swap_var_pairs('images', $CATIMG, $CATOUT);

            $OUT .= $CATOUT;
        }

        return $OUT;
    }

    // ********************************************************************************* //

    public function category_list()
    {
        // Variable prefix
        $this->prefix = ee()->TMPL->fetch_param('prefix', 'image') . ':';

        ee()->db->select('DISTINCT (category)', FALSE);
        ee()->db->from('exp_channel_images');

        //----------------------------------------
        // Entry ID ?
        //----------------------------------------
        if (ee()->TMPL->fetch_param('entry_id') != FALSE)
        {
            ee()->db->where('entry_id', ee()->TMPL->fetch_param('entry_id'));
        }

        //----------------------------------------
        // URL Title
        //----------------------------------------
        if (ee()->TMPL->fetch_param('url_title') != FALSE)
        {
            $entry_id = 9999999;
            $query = ee()->db->query("SELECT entry_id FROM exp_channel_titles WHERE url_title = '".ee()->TMPL->fetch_param('url_title')."' LIMIT 1");
            if ($query->num_rows() > 0) $entry_id = $query->row('entry_id');
            ee()->db->where('url_title', $entry_id);
        }

        //----------------------------------------
        // Channel ID?
        //----------------------------------------
        $channel_id = ee('channel_images:Helper')->get_channel_id_from_param();

        if (is_array($channel_id) == TRUE)
        {
            ee()->db->where_in('channel_id', $channel_id);
        }
        elseif ($channel_id != FALSE)
        {
            ee()->db->where('channel_id', $channel_id);
        }

        // Order by
        ee()->db->order_by('category', 'ASC');

        $query = ee()->db->get();
        //----------------------------------------
        // Parse
        //----------------------------------------
        $OUT = '';

        foreach ($query->result() as $count => $row)
        {
            if ($row->category == FALSE) continue;

            $temp = '';

            $vars = array();
            $vars[$this->prefix.'category_label'] = ucfirst($row->category);
            $vars[$this->prefix.'category'] = $row->category;

            $temp = ee()->TMPL->parse_variables_row(ee()->TMPL->tagdata, $vars);

            $OUT .= $temp;
        }

        return $OUT;
    }

    // ********************************************************************************* //

    public function prev_image()
    {
        return $this->prev_next_image('prev');
    }

    // ********************************************************************************* //

    public function next_image()
    {
        return $this->prev_next_image('next');
    }

    // ********************************************************************************* //

    public function prev_next_image($which='next')
    {
        // Variable prefix
        $this->prefix = ee()->TMPL->fetch_param('prefix', 'image') . ':';

        // We need Image ID or Url_title
        if ( ee()->TMPL->fetch_param('image_id') == FALSE && ee()->TMPL->fetch_param('url_title') == FALSE)
        {
            ee()->TMPL->log_item("CHANNEL IMAGES: No Image ID or URL Title ({$which})");
            return ee('channel_images:Helper')->custom_no_results_conditional($this->prefix.'no_image', ee()->TMPL->tagdata);
        }

        ee()->db->select('image_id, entry_id, field_id');
        ee()->db->from('exp_channel_images');
        if (ee()->TMPL->fetch_param('image_id') != FALSE) ee()->db->where('image_id', ee()->TMPL->fetch_param('image_id'));
        if (ee()->TMPL->fetch_param('url_title') != FALSE) ee()->db->where('url_title', ee()->TMPL->fetch_param('url_title'));
        ee()->db->limit(1);
        $query = ee()->db->get();

        if ( $query->num_rows() == 0)
        {
            ee()->TMPL->log_item("CHANNEL IMAGES: No Image found ({$which})");
            return ee('channel_images:Helper')->custom_no_results_conditional($this->prefix.'no_image', ee()->TMPL->tagdata);
        }

        $image_id = $query->row('image_id');
        $entry_id = $query->row('entry_id');
        $field_id = $query->row('field_id');
        $images = array();

        // Did we cache it?
        if (isset(ee()->session->cache['ChannelImages']['NextPrev'][$entry_id]) != TRUE)
        {
            // Grab the whole thing
            ee()->db->select('*')->from('exp_channel_images')->where('entry_id', $entry_id)->where('field_id', $field_id);
            ee()->db->order_by('image_order', 'ASC');
            $query = ee()->db->get();

            ee()->session->cache['ChannelImages']['NextPrev'][$entry_id] = $query->result();
        }

        $images = ee()->session->cache['ChannelImages']['NextPrev'][$entry_id];

        //----------------------------------------
        // Loop over all images
        //----------------------------------------
        $prev = array();
        $next = array();

        foreach($images as $key => $img)
        {
            // Is this it?
            if ($img->image_id == $image_id)
            {
                // Is there a Prev?
                if (isset($images[($key-1)]))
                {
                    $prev = $images[($key-1)];
                }

                // Is there a Next?
                if (isset($images[($key+1)]))
                {
                    $next = $images[($key+1)];
                }
            }
        }

        //----------------------------------------
        // Parse Image
        //----------------------------------------

        // Which one?
        $image = ($which == 'next') ? $next : $prev;

        if (empty($image) === TRUE)
        {
            ee()->TMPL->log_item("CHANNEL IMAGES: No {$which} Image found ({$which})");
            return ee('channel_images:Helper')->custom_no_results_conditional($this->prefix.'no_image', ee()->TMPL->tagdata);
        }

        //----------------------------------------
        // Performance :)
        //----------------------------------------
        if (isset($this->session->cache['channel_images']['locations']) == FALSE)
        {
            $this->session->cache['channel_images']['locations'] = array();
        }

        $this->LOCS &= $this->session->cache['channel_images']['locations'];

        // Another Check, just to be sure
        if (is_array($this->LOCS) == FALSE) $this->LOCS = array();

        // Check for linked image!
        if ($image->link_entry_id > 0)
        {
            $image->entry_id = $image->link_entry_id;
            $image->field_id = $image->link_field_id;
        }

        // Get Field Settings!
        $settings = ee('channel_images:Settings')->getFieldtypeSettings($image->field_id);

        //----------------------------------------
        // Load Location
        //----------------------------------------
        if (isset($this->LOCS[$image->field_id]) == FALSE)
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
            $this->LOCS[$image->field_id] = new $location_class($location_settings);
        }

        //----------------------------------------
        // Check for Mime Type
        //----------------------------------------
        if ($image->mime == FALSE)
        {
            // Mime type
            $image->mime = 'image/jpeg';
            if ($image->extension == 'png') $filemime = 'image/png';
            elseif ($image->extension == 'gif') $filemime = 'image/gif';
        }

        //----------------------------------------
        // Image URL
        //----------------------------------------
        $image_url = $this->LOCS[$image->field_id]->parse_image_url($image->entry_id, $image->filename);

        $vars = array();
        $vars[$this->prefix.'entry_id'] = $image->entry_id;
        $vars[$this->prefix.'channel_id'] = $image->channel_id;
        $vars[$this->prefix.'title'] = $image->title;
        $vars[$this->prefix.'url_title'] = $image->url_title;
        $vars[$this->prefix.'description'] = $image->description;
        $vars[$this->prefix.'category'] = $image->category;
        $vars[$this->prefix.'filename'] = $image->filename;
        $vars[$this->prefix.'id'] = $image->image_id;
        $vars[$this->prefix.'upload_date'] = $image->upload_date;
        $vars[$this->prefix.'url'] = $image_url;
        $vars[$this->prefix.'secure_url'] = $image_url;
        $vars[$this->prefix.'mimetype'] = $image->mime;
        $vars[$this->prefix.'cover'] = $image->cover;
        $vars[$this->prefix.'field:1'] = $image->cifield_1;
        $vars[$this->prefix.'field:2'] = $image->cifield_2;
        $vars[$this->prefix.'field:3'] = $image->cifield_3;
        $vars[$this->prefix.'field:4'] = $image->cifield_4;
        $vars[$this->prefix.'field:5'] = $image->cifield_5;

        // Misc
        $this->IS_SSL = ee('channel_images:Helper')->isSsl();
        $this->parse_filesize = FALSE;

        //----------------------------------------
        // Check for image_dimensions, Since it's an expensive operation
        //----------------------------------------
        $this->parse_dimensions = FALSE;
        if (strpos(ee()->TMPL->tagdata, LD.$this->prefix.'width') !== FALSE OR strpos(ee()->TMPL->tagdata, LD.$this->prefix.'height') !== FALSE)
        {
            $this->parse_dimensions = TRUE;

            // If filesize is not defined, lets find it (only for local files)
            if ($image->width == FALSE && $settings['upload_location'] == 'local')
            {
                $filepath = $this->LOCS[$image->field_id]->getLocationPrefs($settings['locations']['local']['location']);
                $filepath = $filepath->server_path  . $image->entry_id . '/' . $image->filename;
                $imginfo = @getimagesize($filepath);
                $image->width = $imginfo[0];
                $image->height = $imginfo[1];
            }
            elseif ($image->width == FALSE)
            {
                $image->width = '';
                $image->height = '';
            }

            $vars[$this->prefix.'width'] = $image->width;
            $vars[$this->prefix.'height'] = $image->height;
        }


        // -----------------------------------------
        // Locked URL
        // -----------------------------------------
        $this->locked_url = FALSE;
        if ( strpos(ee()->TMPL->tagdata, $this->prefix.'locked_url') !== FALSE)
        {
            $this->locked_url = TRUE;
            $this->IP = ee()->input->ip_address();
            $this->locked_act_url = ee('channel_images:Helper')->getRouterUrl('url', 'locked_image_url');

            $locked = array('image_id' => $image->image_id, 'size'=>'', 'time' => ee()->localize->now + 600, 'ip' => $this->IP);
            $vars[$this->prefix.'locked_url'] = $this->locked_act_url . '&key=' . base64_encode(serialize($locked));
        }


        ee()->TMPL->tagdata = ee()->TMPL->parse_variables_row(ee()->TMPL->tagdata, $vars);
        ee()->TMPL->tagdata = $this->parse_size_vars(ee()->TMPL->tagdata, $settings, $image);

        return ee()->TMPL->tagdata;
    }

    // ********************************************************************************* //

    public function zip()
    {
        // -----------------------------------------
        // Increase all types of limits!
        // -----------------------------------------
        @set_time_limit(0);
        $conf = ee()->config->item('channel_images');
        if (is_array($conf) === false) $conf = array();

        if (isset($conf['infinite_memory']) === FALSE || $conf['infinite_memory'] == 'yes')
        {
            @ini_set('memory_limit', '64M');
            @ini_set('memory_limit', '96M');
            @ini_set('memory_limit', '128M');
            @ini_set('memory_limit', '160M');
            @ini_set('memory_limit', '192M');
        }

        error_reporting(E_ALL);
        @ini_set('display_errors', 1);

        // What Entry?
        $entry_id = ee('channel_images:Helper')->get_entry_id_from_param();

        // Filename
        if (ee()->TMPL->fetch_param('filename') != FALSE)
        {
            $filename = strtolower(ee()->security->sanitize_filename(str_replace(' ', '_', ee()->TMPL->fetch_param('filename'))));
        }
        else
        {
            $query = ee()->db->select('url_title')->from('exp_channel_titles')->where('entry_id', $entry_id)->get();
            $filename = substr($query->row('url_title'), 0 , 50);
        }

        // We need an entry_id
        if ($entry_id == FALSE)
        {
            show_error('No entry found! Unable to generate ZIP');
        }

        ee()->db->select('*');
        ee()->db->from('exp_channel_images');
        ee()->db->where('entry_id', $entry_id);

        //----------------------------------------
        // Field ID
        //----------------------------------------
        if (ee()->TMPL->fetch_param('field_id') != FALSE)
        {
            ee()->db->where('field_id', ee()->TMPL->fetch_param('field_id'));
        }

        //----------------------------------------
        // Field
        //----------------------------------------
        if (ee()->TMPL->fetch_param('field') != FALSE)
        {
            $group = ee()->TMPL->fetch_param('field');

            // Multiple Fields
            if (strpos($group, '|') !== FALSE)
            {
                $group = explode('|', $group);
                $groups = array();

                foreach ($group as $name)
                {
                    $groups[] = $name;
                }
            }
            else
            {
                $groups = ee()->TMPL->fetch_param('field');
            }

            ee()->db->join('exp_channel_fields cf', 'cf.field_id = exp_channel_images.field_id', 'left');
            ee()->db->where_in('cf.field_name', $groups);
        }

        $query = ee()->db->get();

        //----------------------------------------
        // Shoot the query
        //----------------------------------------
        if ($query->num_rows() == 0)
        {
            show_error('No Files found! Unable to generate ZIP');
        }

        $files = $query->result();

        //----------------------------------------
        // Harvest Field ID!
        //----------------------------------------
        $tfields = array();
        foreach ($files as $file)
        {
            if ($file->link_image_id > 0) $tfields[] = $file->link_field_id;
            $tfields[] = $file->field_id;
        }

        $tfields = array_unique($tfields);

        //----------------------------------------
        // Load Location
        //----------------------------------------
        if (class_exists('Image_Location') == FALSE) require PATH_THIRD.'channel_images/locations/image_location.php';
        if (class_exists('CI_Location_local') == FALSE) require PATH_THIRD.'channel_images/locations/local/local.php';
        $LOCAL = new CI_Location_local();

        //----------------------------------------
        // Check Each Field
        //----------------------------------------
        $fields = array();
        foreach ($tfields as $field_id)
        {
            // Get Field Settings!
            $settings = ee('channel_images:Settings')->getFieldtypeSettings($field_id);

            if ($settings['upload_location'] != 'local') continue;

            $settings = $LOCAL->getLocationPrefs($settings['locations']['local']['location']);
            $fields[$field_id] = $settings;
        }

        //print_r($fields);

        if (empty($fields) == TRUE)
        {
            show_error('No suitable fields found! Unable to generate ZIP');
        }

        //----------------------------------------
        // Create .ZIP
        //----------------------------------------
        $zip = new ZipArchive();
        $zip_path = APPPATH."cache/channel_images/{$filename}.zip";
        if ($zip->open($zip_path, ZIPARCHIVE::OVERWRITE) !== true)
        {
            show_error('Unable to Create ZIP. ZIP Open ERROR');
        }

        //----------------------------------------
        // Sizes
        //----------------------------------------
        $sizes = array('ORIGINAL');

        if (ee()->TMPL->fetch_param('size') != FALSE)
        {
            $size = ee()->TMPL->fetch_param('size');

            // Multiple Fields
            if (strpos($size, '|') !== FALSE)
            {
                $size = explode('|', $size);
                $sizes = array();

                foreach ($size as $name)
                {
                    $sizes[] = $name;
                }
            }
            else
            {
                $sizes = array(ee()->TMPL->fetch_param('size'));
            }
        }

        //----------------------------------------
        // Add Files!
        //----------------------------------------
        foreach ($files as $file)
        {
            $entry_id = $file->entry_id;
            $field_id = $file->field_id;

            if ($file->link_image_id > 0)
            {
                $field_id = $file->link_field_id;
                $entry_id = $file->link_entry_id;
            }

            // Good Field?
            if (isset($fields[$field_id]) == FALSE) continue;

            $path = $fields[$field_id]->server_path . $entry_id . '/';

            foreach ($sizes as $size)
            {
                $imgfilename = $file->filename;

                if ($size != 'ORIGINAL')
                {
                    $extension = '.' . substr( strrchr($imgfilename, '.'), 1);
                    $imgfilename = str_replace($extension, "__{$size}{$extension}", $imgfilename );
                }

                if (file_exists($path.$imgfilename) === TRUE)
                {
                    $zip->addFile($path.$imgfilename, $imgfilename);
                }
            }
        }

        $zip->close();

        //----------------------------------------
        // Output to browser!
        //----------------------------------------
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: public', FALSE);
        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Accept-Ranges: bytes');
        header('Content-Disposition: attachment; filename="' . $filename . '.zip";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . @filesize($zip_path));

        if (! $fh = fopen($zip_path, 'rb'))
        {
            exit('COULD NOT OPEN FILE.');
        }

        while (!feof($fh))
        {
            @set_time_limit(0);
            print(fread($fh, 8192));
            flush();
        }
        fclose($fh);

        @unlink($zip_path);

    }

    // ********************************************************************************* //

    public function channel_images_router($mcp_task=false)
    {
        @header('Access-Control-Allow-Origin: *');
        //@header('Access-Control-Allow-Credentials: true');
        @header('Access-Control-Max-Age: 86400');
        @header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        @header('Access-Control-Allow-Headers: Keep-Alive, Content-Type, User-Agent, Cache-Control, Origin, X-Requested-With, X-File-Name, X-File-Size, X-EEXID');

        if (ee()->input->server('REQUEST_METHOD') == 'OPTIONS') exit();

        // Task
        $task = ee()->input->get_post('ajax_method');

        if ($mcp_task !== false) {
            $task = $mcp_task;
        }

        if (!$task) {
            // If nothing of the above is true...
            exit('This is the ACT URL for Channel Images tttt');
        }

        // Load Library
        if (class_exists('Channel_Images_AJAX') != TRUE) include 'ajax.channel_images.php';
        $AJAX = new Channel_Images_AJAX();

        // Shoot the requested method
        echo $AJAX->$task();
        exit();

    }

    // ********************************************************************************* //

    public function locked_image_url()
    {
        // -----------------------------------------
        // We need our Key
        // -----------------------------------------
        $key = ee()->input->get('key');

        if ($key == FALSE) exit();

        try { $data = unserialize(base64_decode(ee()->input->get_post('key'))); }
        catch (Exception $e) { exit(); }

        // -----------------------------------------
        // Get Image
        // -----------------------------------------
        $image = ee()->db->select('*')->from('exp_channel_images')->where('image_id', $data['image_id'])->limit(1)->get();

        if ($image->num_rows() == 0) exit();
        $image = $image->row();

        // -----------------------------------------
        // Within Time?
        // -----------------------------------------
        if ($data['time'] < ee()->localize->now)
        {
            exit();
        }

        // -----------------------------------------
        // And Same IP?
        // -----------------------------------------
        if ($data['ip'] != ee()->input->ip_address())
        {
            exit();
        }

        // -----------------------------------------
        // Check for linked image!
        // -----------------------------------------
        if ($image->link_entry_id > 0)
        {
            $image->entry_id = $image->link_entry_id;
            $image->field_id = $image->link_field_id;
        }

        // -----------------------------------------
        // Which Filename
        // -----------------------------------------
        $filename = $image->filename;
        if ($data['size'] != FALSE)
        {
            $extension = '.' . $image->extension;
            $name = strtolower($data['size']);
            $filename = str_replace($extension, "__{$name}{$extension}", $image->filename);
        }

        $filename = ee()->security->sanitize_filename($filename);

        // -----------------------------------------
        // Get Field Settings
        // -----------------------------------------
        $settings = ee('channel_images:Settings')->getFieldtypeSettings($image->field_id);

        //----------------------------------------
        // Load Location
        //----------------------------------------
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


        // -----------------------------------------
        // Not Local?
        // -----------------------------------------
        if ($settings['upload_location'] != 'local')
        {
            $file_url = $LOC->parse_image_url($image->entry_id, $filename);
            ee()->load->helper('url');
            header('Location: '.$file_url);
            exit();
        }
        else
        {
            // -----------------------------------------
            // Local Location!
            // -----------------------------------------
            $location = $LOC->getLocationPrefs($settings['locations']['local']['location']);
            $filepath = $location->server_path  . $image->entry_id . '/' . $filename;

            // -----------------------------------------
            // Mime Type
            // -----------------------------------------
            if ($image->mime == FALSE)
            {
                // Mime type
                $image->mime = 'image/jpeg';
                if ($image->extension == 'png') $filemime = 'image/png';
                elseif ($image->extension == 'gif') $filemime = 'image/gif';
            }

            // -----------------------------------------
            // Send to Browser
            // -----------------------------------------
            header('Pragma: public');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: public', FALSE);
            header('Content-Type: ' . $image->mime);
            header('Expires: Sat, 12 Dec 1990 11:00:00 GMT'); // Date in the past
            header('X-Robots-Tag: noindex'); // Tell google not to index

            if (! $fh = fopen($filepath, 'rb'))
            {
                exit();
            }

            fpassthru($fh);
            flush();

            /*
            while (!feof($fh))
            {
                @set_time_limit(0);
                print(fread($fh, 8192));
                flush();
            }
            */

            fclose($fh);


            exit();
        }


    }

    // ********************************************************************************* //

    public function simple_image_url()
    {
        error_reporting(E_ALL);
        @ini_set('display_errors', 1);

        $field_id = ee()->input->get('fid');
        $dir = ee()->input->get('d');
        $file = ee()->security->sanitize_filename(ee()->input->get('f'));
        $temp_dir = ee()->input->get('temp_dir');
        $moduleSettings = ee('channel_images:Settings')->settings;

        // Must be an INT
        if (!ctype_digit($field_id)) {
            ee()->output->set_status_header(404);
            echo '<html><head><title>404 Page Not Found</title></head><body><h1>Status: 404 Page Not Found</h1></body></html>';
            exit();
        }

        if ($moduleSettings['encode_filename_url'] == 'yes') {
            $file = base64_decode($file);
        }

        // -----------------------------------------
        // Temp DIR?
        // -----------------------------------------
        if ($temp_dir == 'yes') {
            error_reporting(E_ALL);
            @ini_set('display_errors', 1);

            //Extension
            $extension = substr( strrchr($file, '.'), 1);

            // Mime type
            $filemime = 'image/jpeg';
            if ($extension == 'png') $filemime = 'image/png';
            elseif ($extension == 'gif') $filemime = 'image/gif';

            // Windows?
            if ($moduleSettings['host_os'] == 'windows') {
                $file = APPPATH.'\\cache\\channel_images\\field_'.$field_id.'\\'.$dir.'\\'.$file;
            } else {
                $file = ee('channel_images:Settings')->settings['cache_path'].'channel_images/field_'.$field_id.'/'.$dir.'/'.$file;
            }

            if (!file_exists($file)) {
                if (ee()->session->userdata('group_id') == 1) {
                    echo $file . '<br>';
                }

                exit('FILE NOT FOUND!');
            }

            /** ----------------------------------------
            /**  For Local Files we STREAM
            /** ----------------------------------------*/
            header('Pragma: public');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: public');
            header('Content-Type: ' . $filemime);
            header('Expires: Sat, 12 Dec 1990 11:00:00 GMT'); // Date in the past
            //header('X-Robots-Tag: noindex'); // Tell google not to index

            header('Content-Length: ' . @filesize($file));

            @readfile($file);
            @ob_flush();
            @flush();
            @ob_end_flush();
            @ob_start();

            exit;
        }

        // -----------------------------------------
        // Load Settings
        // -----------------------------------------
        $settings = ee('channel_images:Settings')->getFieldtypeSettings($field_id);

        if (empty($settings) == true) {
            ee()->output->set_status_header(404);
            echo '<html><head><title>404 Page Not Found</title></head><body><h1>Status: 404 Page Not Found</h1></body></html>';
            exit();
        }

        // -----------------------------------------
        // Load Location
        // -----------------------------------------
        $location_type = $settings['upload_location'];
        $location_class = 'CI_Location_'.$location_type;

        // Load Settings
        if (isset($settings['locations'][$location_type]) == FALSE) {
            ee()->output->set_status_header(404);
            echo '<html><head><title>404 Page Not Found</title></head><body><h1>Status: 404 Page Not Found</h1></body></html>';
            exit();
        }

        $location_settings = $settings['locations'][$location_type];

        // Load Main Class
        if (class_exists('Image_Location') == FALSE) require PATH_THIRD.'channel_images/locations/image_location.php';

        // Try to load Location Class
        if (class_exists($location_class) == FALSE) {
            $location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';

            if (file_exists($location_file) == FALSE)
            {
                ee()->output->set_status_header(404);
                echo '<html><head><title>404 Page Not Found</title></head><body><h1>Status: 404 Page Not Found</h1></body></html>';
                exit();
            }

            require $location_file;
        }

        // Init!
        $LOC = new $location_class($location_settings);

        $config = ee()->config->item('channel_images');

        // -----------------------------------------
        // Is this a local file? Then lets stream it shall we
        // -----------------------------------------
        if ($location_type == 'local') {
            $loc = $LOC->getLocationPrefs($location_settings['location']);
            $server_path = $loc->server_path;

            error_reporting(E_ALL);
            @ini_set('display_errors', 1);

            //Extension
            $extension = substr( strrchr($file, '.'), 1);

            // Mime type
            $filemime = 'image/jpeg';
            if ($extension == 'png') $filemime = 'image/png';
            elseif ($extension == 'gif') $filemime = 'image/gif';

            $file = $server_path . $dir .'/'. $file;

            /** ----------------------------------------
            /**  For Local Files we STREAM
            /** ----------------------------------------*/
            header('Pragma: public');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: public');
            header('Content-Type: ' . $filemime);
            header('Expires: Sat, 12 Dec 1990 11:00:00 GMT'); // Date in the past

            header('Content-Length: ' . @filesize($file));


            @readfile($file);
            @ob_flush();
            @flush();
            @ob_end_flush();
            @ob_start();
            exit;
        }



        // -----------------------------------------
        // Other locations..
        // -----------------------------------------
        $url = $LOC->parse_image_url($dir, $file);

        header('Location: '.$url);
        exit();
    }

    // ********************************************************************************* //

    private function parse_size_vars($OUT, $settings, $image)
    {
        // Get Extension
        $extension = '.' . $image->extension;

        if (isset($settings['action_groups']) == FALSE OR empty($settings['action_groups']) == TRUE) return $OUT;

        //----------------------------------------
        // Size Metadata!
        //----------------------------------------
        $metadata = array();
        if ($image->sizes_metadata != FALSE)
        {
            $temp = explode('/', $image->sizes_metadata);
            foreach($temp as $row)
            {
                if ($row == FALSE) continue;
                $temp2 = explode('|', $row);

                $metadata[$temp2[0]] = array('width' => $temp2[1], 'height'=>$temp2[2], 'size'=>$temp2[3]);
            }
        }

        // -----------------------------------------
        // Loop over all sizes!
        // -----------------------------------------
        foreach ($settings['action_groups'] as $group)
        {
            $name = strtolower($group['group_name']);
            $newname = str_replace($extension, "__{$name}{$extension}", $image->filename);

            // -----------------------------------------
            // Image URL (Size)
            // -----------------------------------------
            $image_url = $this->LOCS[$image->field_id]->parse_image_url($image->entry_id, $newname);

            // Did something go wrong?
            if ($image_url == FALSE)
            {
                ee()->TMPL->log_item('CHANNEL IMAGES: Image URL Failed for: ' . $image->entry_id.'/'.$image->filename);
                continue;
            }

            // SSL?
            if ($this->IS_SSL == TRUE) $image_url = str_replace('http://', 'https://', $image_url);

            $OUT = str_replace(LD.$this->prefix.'filename:'.$name.RD, $newname, $OUT);
            $OUT = str_replace(LD.$this->prefix.'url:'.$name.RD, $image_url, $OUT);
            $OUT = str_replace(LD.$this->prefix.'secure_url:'.$name.RD, str_replace('http://', 'https://', $image_url), $OUT);

            // -----------------------------------------
            // Locked URLS (Size)
            // -----------------------------------------
            if ($this->locked_url == TRUE)
            {
                $locked = array('image_id' => $image->image_id, 'size'=>$name, 'time' => ee()->localize->now + 3600, 'ip' => $this->IP);
                $OUT = str_replace(LD.$this->prefix.'locked_url:'.$name.RD, ($this->locked_act_url . '&key=' . base64_encode(serialize($locked))), $OUT);
            }

            //----------------------------------------
            // Check for filesize, Since it's an expensive operation
            //----------------------------------------
            if ($this->parse_filesize == TRUE)
            {
                // If filesize is not defined, lets find it (only for local files)
                if (isset($metadata[$name]) == FALSE && $settings['upload_location'] == 'local')
                {
                    $filepath = $this->LOCS[$image->field_id]->getLocationPrefs($settings['locations']['local']['location']);
                    $filepath = $filepath->server_path  . $image->entry_id . '/' . $newname;
                    $metadata[$name]['size'] = @filesize($filepath);
                }

                if (isset($metadata[$name]['size']) === FALSE) $metadata[$name]['size'] = 0;

                $OUT = str_replace(LD.$this->prefix.'filesize:'.$name.RD, ee('channel_images:Helper')->formatBytes($metadata[$name]['size']), $OUT);
                $OUT = str_replace(LD.$this->prefix.'filesize_bytes:'.$name.RD, $metadata[$name]['size'], $OUT);
            }

            //----------------------------------------
            // Check for image_dimensions, Since it's an expensive operation
            //----------------------------------------
            if ($this->parse_dimensions == TRUE)
            {
                // If filesize is not defined, lets find it (only for local files)
                if (isset($metadata[$name]) === FALSE && $settings['upload_location'] == 'local')
                {
                    $filepath = $this->LOCS[$image->field_id]->getLocationPrefs($settings['locations']['local']['location']);
                    $filepath = $filepath->server_path  . $image->entry_id . '/' . $newname;
                    $imginfo = @getimagesize($filepath);
                    $metadata[$name]['width'] = $imginfo[0];
                    $metadata[$name]['height'] = $imginfo[1];
                }

                if (isset($metadata[$name]['width']) === FALSE) $metadata[$name]['width'] = '';
                if (isset($metadata[$name]['height']) === FALSE) $metadata[$name]['height'] = '';

                $OUT = str_replace(LD.$this->prefix.'width:'.$name.RD, $metadata[$name]['width'], $OUT);
                $OUT = str_replace(LD.$this->prefix.'height:'.$name.RD, $metadata[$name]['height'], $OUT);
            }
        }

        return $OUT;
    }

    // ********************************************************************************* //

} // END CLASS

/* End of file mod.channel_images.php */
/* Location: ./system/user/addons/channel_images/mod.channel_images.php */