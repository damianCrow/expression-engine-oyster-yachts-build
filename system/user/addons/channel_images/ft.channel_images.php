<?php if (!defined('BASEPATH')) die('No direct script access allowed');

use DevDemon\ChannelImages\Model\Image;

/**
 * Channel Images Module FieldType
 *
 * @package         DevDemon_ChannelImages
 * @author          DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright       Copyright (c) 2007-2016 Parscale Media <http://www.parscale.com>
 * @license         http://www.devdemon.com/license/
 * @link            http://www.devdemon.com
 * @see             https://ellislab.com/expressionengine/user-guide/development/fieldtypes.html
 */
class Channel_images_ft extends EE_Fieldtype
{

    /**
     * Field info - Required
     *
     * @access public
     * @var array
     */
    public $info = array(
        'name'      => CHANNEL_IMAGES_NAME,
        'version'   => CHANNEL_IMAGES_VERSION,
    );

    public $has_array_data = true;
    public $dropdown_type = 'contains_doesnotcontain'; // Zenbu

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
        $this->moduleSettings = ee('channel_images:Settings')->settings;
    }

    // ********************************************************************************* //

    /**
     * Display the field in the publish form
     *
     * @access public
     * @param $data String Contains the current field data. Blank for new entries.
     * @return String The custom field HTML
     */
    public function display_field($data)
    {
        $locs = array();
        ee()->lang->loadfile('channel_images');

        //----------------------------------------
        // Global Vars
        //----------------------------------------
        $vData = array();
        $vData['missing_settings'] = false;
        $vData['field_name'] = $this->field_name;
        $vData['field_id'] = $this->field_id;
        $vData['temp_key'] = ee()->localize->now;
        $vData['entry_id'] = $this->content_id();// (ee()->input->get_post('entry_id') != false) ? ee()->input->get_post('entry_id') : false;
        $vData['total_images'] = 0;
        $vData['assigned_images'] = array();

        //----------------------------------------
        // Add Global JS & CSS & JS Scripts
        //----------------------------------------
        ee('channel_images:Helper')->mcpAssets('gjs');
        ee('channel_images:Helper')->mcpAssets('css', 'colorbox.css');
        ee('channel_images:Helper')->mcpAssets('css', 'addon_pbf.css', null, true);
        ee('channel_images:Helper')->mcpAssets('js', 'handlebars.runtime-v4.js');
        ee('channel_images:Helper')->mcpAssets('js', 'hbs-templates.js');
        ee('channel_images:Helper')->mcpAssets('js', 'jquery.colorbox.js');
        ee('channel_images:Helper')->mcpAssets('js', 'jquery.editable.js');
        ee('channel_images:Helper')->mcpAssets('js', 'jquery.base64.js');
        ee('channel_images:Helper')->mcpAssets('js', 'jquery.liveurltitle.js');
        ee('channel_images:Helper')->mcpAssets('js', 'jquery.jcrop.js');
        ee('channel_images:Helper')->mcpAssets('js', 'swfupload.js');
        ee('channel_images:Helper')->mcpAssets('js', 'swfupload.queue.js');
        ee('channel_images:Helper')->mcpAssets('js', 'swfupload.speed.js');
        ee('channel_images:Helper')->mcpAssets('js', 'addon_pbf.js', null, true);

        ee()->cp->add_js_script(array(
            'ui' => array('sortable', 'tabs')
        ));

        //----------------------------------------
        // Settings
        //----------------------------------------
       $settings = ee('channel_images:Settings')->getFieldtypeSettings($this->field_id);

        // Settings SET?
        if ( (isset($settings['action_groups']) == false OR empty($settings['action_groups']) == true) && (isset($settings['no_sizes']) == false OR $settings['no_sizes'] != 'yes') ) {
            $vData['missing_settings'] = true;
            return ee()->load->view('pbf_field', $vData, true);
        }

        // Columns?
        if (isset($settings['columns']) == false) $settings['columns'] = ee()->config->item('ci_columns');

        // Stored Images
        if (isset($settings['show_stored_images']) == false) $settings['show_stored_images'] = $defaults['show_stored_images'];

        // Limit Images?
        if (isset($settings['image_limit']) == false OR trim($settings['image_limit']) == false) $settings['image_limit'] = 999999;
        $tmpperimage = ee()->cache->get('ChannelImages/PerImageActionHolder');
        if (isset($tmpperimage) == false && $settings['allow_per_image_action'] == 'yes')
        {
            $vData['actions'] = &ee('channel_images:Helper')->get_actions();
            ee()->cache->save('ChannelImages/PerImageActionHolder',true,500);
        }

        $vData['settings'] = $settings;


        //----------------------------------------
        // Field JSON
        //----------------------------------------
        $vData['field_json'] = array();
        $vData['field_json']['key'] = $vData['temp_key'];
        $vData['field_json']['field_name'] = $this->field_name;
        $vData['field_json']['field_label'] = $this->settings['field_label'];
        $vData['field_json']['settings'] = $vData['settings'];
        $vData['field_json']['categories'] = array();

        // Add Categories
        if (isset($settings['categories']) == true && empty($settings['categories']) == false)
        {
            $vData['field_json']['categories'][''] = '';
            foreach ($settings['categories'] as $cat) $vData['field_json']['categories'][$cat] = $cat;
        }

        // Remove some unwanted stuff
        unset($vData['field_json']['settings']['categories']);
        unset($vData['field_json']['settings']['locations']);
        unset($vData['field_json']['settings']['import_path']);

        //----------------------------------------
        // JS Templates
        //----------------------------------------
        $vData['js_templates'] = false;
        //$tmpjstemplate = ee()->cache->save('ChannelImages/JSTemplates');
        if (isset( $this->EE->session->cache['ChannelImages']['JSTemplates'] ) === false)
        {
            $vData['js_templates'] = true;
            $this->EE->session->cache['ChannelImages']['JSTemplates'] = true;

            $vData['langjson'] = array();

            foreach (ee()->lang->language as $key => $val)
            {
                if (strpos($key, 'ci:json:') === 0)
                {
                    $vData['langjson'][substr($key, 8)] = $val;
                    unset(ee()->lang->language[$key]);
                }

            }

            $vData['langjson'] = json_encode($vData['langjson']);
        }

        //----------------------------------------
        // Auto-Saved Entry?
        //----------------------------------------
        if (ee()->input->get('use_autosave') == 'y') {
            $vData['entry_id'] = false;
            $old_entry_id = $this->content_id();//ee()->input->get_post('entry_id');
            $query = ee()->db->select('original_entry_id')->from('exp_channel_entries_autosave')->where('entry_id', $old_entry_id)->get();
            if ($query->num_rows() > 0 && $query->row('original_entry_id') > 0) $vData['entry_id'] = $query->row('original_entry_id');
        }

        //----------------------------------------
        // Existing Entry?
        //----------------------------------------
        if ($vData['entry_id'] != false) {
            // -----------------------------------------
            // Grab all Images
            // -----------------------------------------
            $images = ee('Model')->get('channel_images:Image')
            ->filter('entry_id', $vData['entry_id'])
            ->filter('field_id', $this->field_id);

            $is_draft = 0;

            if (isset(ee()->publisher_lib) === true && isset(ee()->publisher_lib->status) ==- true) {
                if (ee()->publisher_lib->status == 'draft')  {
                    $is_draft = 1;
                }
            } else if (isset($this->EE->session->cache['ep_better_workflow']['is_draft']) && $this->EE->session->cache['ep_better_workflow']['is_draft']) {
                $is_draft = 1;
            }


            $images->filter('is_draft', $is_draft);

            if ($settings['cover_first'] == 'yes') $images->order('cover', 'desc');
            $images->order('image_order', 'asc');

            // -----------------------------------------
            // Which Previews?
            // -----------------------------------------
            if (isset($settings['small_preview']) == false OR $settings['small_preview'] == false)
            {
                $temp = reset($settings['action_groups']);
                $settings['small_preview'] = $temp['group_name'];
            }

            if (isset($settings['big_preview']) == false OR $settings['big_preview'] == false)
            {
                $temp = reset($settings['action_groups']);
                $settings['big_preview'] = $temp['group_name'];
            }

            // Preview URL
            $preview_url = ee('channel_images:Helper')->getRouterUrl('url', 'simple_image_url');

            $images = $images->all();

            foreach ($images as $image)
            {
                // We need a good field_id to continue
                $image->field_id = $image->getFieldId();

                // Is it a linked image?
                // Then we need to "fake" the channel_id/field_id
                if ($image->link_image_id >= 1)
                {
                    $image->entry_id = $image->link_entry_id;
                    $image->field_id = $image->link_field_id;
                    $image->channel_id = $image->link_channel_id;
                }

                // Just in case lets try to get the field_id again
                $image->field_id = $image->getFieldId();
                // Get settings for that field..
                $temp_settings = ee('channel_images:Settings')->getFieldtypeSettings($image->field_id);

                $act_url_params = "&amp;fid={$image->field_id}&amp;d={$image->entry_id}";

                // Since the EE model doesn't allow for custom properties, let's make an clean object
                $image = (object) $image->toArray();

                if ( empty($settings['action_groups']) == false && (isset($settings['no_sizes']) == false OR $settings['no_sizes'] != 'yes') )
                {
                    // Display SIzes URL
                    $small_filename = str_replace('.'.$image->extension, "__{$settings['small_preview']}.{$image->extension}", urlencode($image->filename) );
                    $big_filename = str_replace('.'.$image->extension, "__{$settings['big_preview']}.{$image->extension}", urlencode($image->filename) );

                    if (ee()->config->item('ci_encode_filename_url') == 'yes')
                    {
                        $small_filename = base64_encode($small_filename);
                        $big_filename = base64_encode($big_filename);
                    }

                    $image->small_img_url = "{$preview_url}&amp;f={$small_filename}{$act_url_params}";
                    $image->big_img_url = "{$preview_url}&amp;f={$big_filename}{$act_url_params}";
                }
                else
                {
                    $small_filename = $image->filename;
                    $big_filename = $image->filename;

                    if (ee()->config->item('ci_encode_filename_url') == 'yes')
                    {
                        $small_filename = base64_encode($small_filename);
                        $big_filename = base64_encode($big_filename);
                    }

                    // Display SIzes URL
                    $image->small_img_url = "{$preview_url}&amp;f={$small_filename}{$act_url_params}";
                    $image->big_img_url = "{$preview_url}&amp;f={$big_filename}{$act_url_params}";
                }

                // ReAssign Field ID (WE NEED THIS)
                $image->field_id = $this->field_id;

                $image->title = html_entity_decode( str_replace('&quot;', '"', $image->title), ENT_QUOTES, 'UTF-8');
                $image->description = html_entity_decode( str_replace('&quot;', '"', $image->description), ENT_QUOTES, 'UTF-8');
                $image->cifield_1 = html_entity_decode( str_replace('&quot;', '"', $image->cifield_1), ENT_QUOTES, 'UTF-8');
                $image->cifield_2 = html_entity_decode( str_replace('&quot;', '"', $image->cifield_2), ENT_QUOTES, 'UTF-8');
                $image->cifield_3 = html_entity_decode( str_replace('&quot;', '"', $image->cifield_3), ENT_QUOTES, 'UTF-8');
                $image->cifield_4 = html_entity_decode( str_replace('&quot;', '"', $image->cifield_4), ENT_QUOTES, 'UTF-8');
                $image->cifield_5 = html_entity_decode( str_replace('&quot;', '"', $image->cifield_5), ENT_QUOTES, 'UTF-8');

                //$image->description = EncodingCI::toUTF8($image->description);

                //$image->title = utf8_encode($image->title);
                //$image->description = utf8_encode($image->description);

                // On some systems characters are not passed on as UTF-8, json_encode only works with UTF-8 chars.
                // This "hack" forces utf-8 encoding, good for swedisch chars etc
                if ($this->moduleSettings['utf8_encode_fields_for_json'] == 'yes') {
                    $image->title = utf8_encode($image->title);
                    $image->description = utf8_encode($image->description);
                    $image->cifield_1 = utf8_encode($image->cifield_1);
                    $image->cifield_2 = utf8_encode($image->cifield_2);
                    $image->cifield_3 = utf8_encode($image->cifield_3);
                    $image->cifield_4 = utf8_encode($image->cifield_4);
                    $image->cifield_5 = utf8_encode($image->cifield_5);
                }

                //$from = mb_detect_encoding($image->title);
                //$image->title = mb_convert_encoding($image->title, 'UTF-8', $from);
                //$image->description = mb_convert_encoding($image->description, 'UTF-8', $from);

                if ($this->moduleSettings['utf8_multibyte_fields_for_json'] == 'yes') {
                    $from = mb_detect_encoding($image->title);
                    $image->title = mb_convert_encoding($image->title, 'UTF-8', $from);
                    $image->description = mb_convert_encoding($image->description, 'UTF-8', $from);
                    $image->cifield_1 = mb_convert_encoding($image->cifield_1, 'UTF-8', $from);
                    $image->cifield_2 = mb_convert_encoding($image->cifield_2, 'UTF-8', $from);
                    $image->cifield_3 = mb_convert_encoding($image->cifield_3, 'UTF-8', $from);
                    $image->cifield_4 = mb_convert_encoding($image->cifield_4, 'UTF-8', $from);
                    $image->cifield_5 = mb_convert_encoding($image->cifield_5, 'UTF-8', $from);
                }


                // Fix utf chars once and for all
                //$image->title = base64_encode($image->title);
                //$image->description = base64_encode($image->description);
                //$image->cifield_1 = base64_encode($image->cifield_1);
                //$image->cifield_2 = base64_encode($image->cifield_2);
                //$image->cifield_3 = base64_encode($image->cifield_3);
                //$image->cifield_4 = base64_encode($image->cifield_4);
                //$image->cifield_5 = base64_encode($image->cifield_5);
                //$image->category = base64_encode($image->category);
                //$image->cover = base64_encode($image->cover);

                if ($settings['direct_url'] == 'yes') {
                    if (isset($locs[$image->field_id]) === false) {
                        $location_type = $temp_settings['upload_location'];
                        $location_class = 'CI_Location_'.$location_type;

                        // Load Settings
                        if (isset($temp_settings['locations'][$location_type]) == false) {
                            $o['body'] = ee()->lang->line('ci:location_settings_failure');
                            exit( json_encode($o) );
                        }

                        $location_settings = $temp_settings['locations'][$location_type];

                        // Load Main Class
                        if (class_exists('Image_Location') == false) require PATH_THIRD.'channel_images/locations/image_location.php';

                        // Try to load Location Class
                        if (class_exists($location_class) == false)
                        {
                            $location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';

                            if (file_exists($location_file) == false)
                            {
                                $o['body'] = ee()->lang->line('ci:location_load_failure');
                                exit( json_encode($o) );
                            }

                            require $location_file;
                        }

                        // Init
                        $locs[$image->field_id] = new $location_class($location_settings);
                    }

                    $image->small_img_url = $locs[$image->field_id]->parse_image_url($image->entry_id, $small_filename);
                    $image->big_img_url = $locs[$image->field_id]->parse_image_url($image->entry_id, $big_filename);
                }


                $vData['assigned_images'][] = $image;

                unset($image);
            }

            $vData['total_images'] = $images->count();
        }

        //var_dump($vData);

        //----------------------------------------
        // Form Submission Error?
        //----------------------------------------
        if (isset($_POST[$this->field_name]) OR isset($_POST['field_id_' . $this->field_id]))
        {
            // Post DATA?
            if (isset($_POST[$this->field_name])) {
                $data = $_POST[$this->field_name];
            }

            if (isset($_POST['field_id_' . $this->field_id])) {
                $data = $_POST['field_id_' . $this->field_id];
            }

            // First.. The Key!
            $vData['field_json']['key'] = $data['key'];
            $vData['temp_key'] = $data['key'];

            if (isset($data['images']) == true)
            {
                $vData['assigned_images'] = '';

                // Preview URL
                $preview_url = ee('channel_images:Helper')->getRouterUrl('url', 'simple_image_url');

                foreach($data['images'] as $num => $img)
                {
                    $img = json_decode(html_entity_decode($img['data']));

                    // Existing? lets get it!
                    if ($img->image_id > 0)
                    {
                        $image = $img;
                    }
                    else
                    {
                        $image = $img;

                        if ($image->link_image_id > 0)
                        {
                            continue;
                        }

                        $image->image_id = 0;
                        $image->extension = substr( strrchr($image->filename, '.'), 1);
                        $image->field_id = $this->field_id;

                        // Display SIzes URL
                        $image->small_img_url = $preview_url . '&amp;temp_dir=yes&amp;fid='.$this->field_id.'&amp;d=' . $vData['temp_key'] . '&amp;f=' . str_replace('.'.$image->extension, "__{$settings['small_preview']}.{$image->extension}", $image->filename);
                        $image->big_img_url = $preview_url . '&amp;temp_dir=yes&amp;fid='.$this->field_id.'&amp;d=' . $vData['temp_key'] . '&amp;f=' . str_replace('.'.$image->extension, "__{$settings['big_preview']}.{$image->extension}", $image->filename);
                    }

                    // We need a good field_id to continue
                    $image->field_id = Image::getFieldId($image);

                    // Is it a linked image?
                    // Then we need to "fake" the channel_id/field_id
                    if ($image->link_image_id >= 1)
                    {
                        $image->entry_id = $image->link_entry_id;
                        $image->field_id = $image->link_field_id;
                        $image->channel_id = $image->link_channel_id;
                    }

                    // Just in case lets try to get the field_id again
                    $image->field_id = Image::getFieldId($image);

                    // ReAssign Field ID (WE NEED THIS)
                    $image->field_id = $this->field_id;

                    // Fix utf chars once and for all
                    $image->cover = base64_decode($image->cover);
                    $image->title = base64_decode($image->title);
                    $image->description = base64_decode($image->description);
                    $image->cifield_1 = base64_decode($image->cifield_1);
                    $image->cifield_2 = base64_decode($image->cifield_2);
                    $image->cifield_3 = base64_decode($image->cifield_3);
                    $image->cifield_4 = base64_decode($image->cifield_4);
                    $image->cifield_5 = base64_decode($image->cifield_5);
                    $image->category = base64_decode($image->category);

                    $vData['assigned_images'][] = $image;

                    unset($image);
                }
            }
        }

        $vData['field_json']['images'] = $vData['assigned_images'];
        $vData['field_json'] = base64_encode(json_encode($vData['field_json']));
        // Base64encode why? Safecracker loves to mess with quotes/unicode etc!!

        return ee()->load->view('pbf_field', $vData, true);
    }

    // ********************************************************************************* //

    /**
     * Validates the field input
     *
     * @param $data Contains the submitted field data.
     * @return mixed Must return true or an error message
     */
    public function validate($data)
    {
        // Is this a required field?
        if ($this->settings['field_required'] == 'y')
        {
            if (isset($data['images']) == false OR empty($data['images']) == true)
            {
                return ee()->lang->line('ci:required_field');
            }
        }

        return true;
    }

    // ********************************************************************************* //

    /**
     * Preps the data for saving
     *
     * @param $data Contains the submitted field data.
     * @return string Data to be saved
     */
    public function save($data)
    {
        ee()->session->set_cache('channel_images', 'field-'.$this->field_id, $data);
        $settings = ee('channel_images:Settings')->getFieldtypeSettings($this->field_id, $this->settings);

        if (isset($data['images']) == false) {
            return '';
        } else {
            $field_data = '';

            // -----------------------------------------
            // Save Data in Custom Field
            // -----------------------------------------
            if ($settings['save_data_in_field'] == 'yes') {
                foreach ($data['images'] as $order => $file) {
                    $file = json_decode($file['data']);
                    if (isset($file->delete) === true) continue;

                    $file->title = base64_decode($file->title);
                    $file->description = base64_decode($file->description);
                    $file->url_title = base64_decode($file->url_title);
                    $file->category = base64_decode($file->category);
                    $file->cifield_1 = base64_decode($file->cifield_1);
                    $file->cifield_2 = base64_decode($file->cifield_2);
                    $file->cifield_3 = base64_decode($file->cifield_3);
                    $file->cifield_4 = base64_decode($file->cifield_4);
                    $file->cifield_5 = base64_decode($file->cifield_5);

                    $file->cover = base64_decode($file->cover);

                    $field_data .= "{$file->filename} - {$file->title}\n{$file->description} {$file->cifield_1} {$file->cifield_2} {$file->cifield_3} {$file->cifield_4} {$file->cifield_5}\n\n";
                }
            } else {
                $field_data = 'ChannelImages';
            }

            return $field_data;
        }
    }

    // ********************************************************************************* //

    /**
     * Handles any custom logic after an entry is saved.
     * Called after an entry is added or updated.
     * Available data is identical to save, but the settings array includes an entry_id.
     *
     * @param $data Contains the submitted field data. (Returned by save())
     * @access public
     * @return void
     */
    public function post_save($data)
    {
        return $this->_process_post_save($data);
    }

    // ********************************************************************************* //

    /**
     * Handles any custom logic after an entry is deleted.
     * Called after one or more entries are deleted.
     *
     * @param $ids array is an array containing the ids of the deleted entries.
     * @access public
     * @return void
     */
    function delete($ids)
    {
        foreach ($ids as $entry_id)
        {
            // -----------------------------------------
            // ENTRY TO FIELD (we need settigns :()
            // -----------------------------------------
            ee()->db->select('field_id');
            ee()->db->from('exp_channel_images');
            ee()->db->where('entry_id', $entry_id);
            ee()->db->limit(1);
            $query = ee()->db->get();

            if ($query->num_rows() == 0) continue;

            $field_id = $query->row('field_id');

            // Grab the field settings
            $settings = ee('channel_images:Settings')->getFieldtypeSettings($field_id);

            // -----------------------------------------
            // Load Location
            // -----------------------------------------
            $location_type = $settings['upload_location'];
            $location_class = 'CI_Location_'.$location_type;

            // Load Settings
            if (isset($settings['locations'][$location_type]) == false)
            {
                $o['body'] = ee()->lang->line('ci:location_settings_failure');
                exit( json_encode($o) );
            }

            $location_settings = $settings['locations'][$location_type];

            // Load Main Class
            if (class_exists('Image_Location') == false) require PATH_THIRD.'channel_images/locations/image_location.php';

            // Try to load Location Class
            if (class_exists($location_class) == false)
            {
                $location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';

                if (file_exists($location_file) == false)
                {
                    $o['body'] = ee()->lang->line('ci:location_load_failure');
                    exit( json_encode($o) );
                }

                require $location_file;
            }

            // Init
            $LOC = new $location_class($location_settings);

            // -----------------------------------------
            // Delete From DB
            // -----------------------------------------
            ee()->db->where('entry_id', $entry_id);
            ee()->db->or_where('link_entry_id', $entry_id);
            ee()->db->delete('exp_channel_images');

            // -----------------------------------------
            // Delete!
            // -----------------------------------------
            $LOC->delete_dir($entry_id);
        }

    }

    // ********************************************************************************* //

    /**
     * Display the settings page. The default ExpressionEngine rows can be created using built in methods.
     * All of these take the current $data and the fieltype name as parameters:
     *
     * @param $data array
     * @access public
     * @return void
     */
    public function display_settings($data)
    {
        //----------------------------------------
        // CSS/JS
        //----------------------------------------
        ee('channel_images:Helper')->mcpAssets('gjs');
        ee('channel_images:Helper')->mcpAssets('css', 'addon_fts.css', null, true);
        ee('channel_images:Helper')->mcpAssets('js', 'spin.js');
        ee('channel_images:Helper')->mcpAssets('js', 'hogan.js');
        ee('channel_images:Helper')->mcpAssets('js', 'jquery.editable.js');
        ee('channel_images:Helper')->mcpAssets('js', 'jquery.base64.js');
        ee('channel_images:Helper')->mcpAssets('js', 'addon_fts.js', null, true);
        ee()->cp->add_js_script(array('ui' => array('tabs', 'draggable', 'sortable')));


        return DevDemon\ChannelImages\Service\FieldtypeSettingsHelper::showSettings($this->field_id, $data);

        /*
        // -----------------------------------------
        // Add JS & CSS
        // -----------------------------------------
        ee('channel_images:Helper')->mcp_meta_parser('gjs', '', 'ChannelImages');
        ee('channel_images:Helper')->mcp_meta_parser('css', CHANNELIMAGES_THEME_URL . 'jquery.colorbox.css', 'jquery.colorbox');
        ee('channel_images:Helper')->mcp_meta_parser('css', CHANNELIMAGES_THEME_URL . 'channel_images_fts.css', 'ci-fts');

        ee('channel_images:Helper')->mcp_meta_parser('js', CHANNELIMAGES_THEME_URL . 'hogan.min.js', 'hogan', 'hogan');

        ee('channel_images:Helper')->mcp_meta_parser('js', CHANNELIMAGES_THEME_URL . 'channel_images_fts.js', 'ci-fts');
        ee()->cp->add_js_script(array('ui' => array('tabs', 'draggable', 'sortable')));


        ee()->load->library('javascript');
        ee()->javascript->output('ChannelImages.Init();');
        */


        // -----------------------------------------
        // Actions!
        // -----------------------------------------
        $vData['actions'] = &ee('channel_images:Helper')->get_actions();

      /*  if (isset($vData['action_groups']) == false && (isset($vData['no_sizes']) == false OR $vData['no_sizes'] != 'yes') ) {
            $vData['action_groups'] = ee()->config->item('ci_default_action_groups');
            echo "Hola";

        }*/

        foreach($vData['action_groups'] as &$group)
        {
            $actions = $group['actions'];
            $group['actions'] = array();

            foreach($actions AS $action_name => &$settings)
            {

                // Sometimes people don't have Imagick anymore!
                if (isset($vData['actions'][$action_name]) == false)  continue;

                $new = array();
                $new['action'] = $action_name;
                $new['action_name'] = $vData['actions'][$action_name]->info['title'];
                $new['action_settings'] = $vData['actions'][$action_name]->display_settings($settings);
                $group['actions'][] = $new;
            }

            if (isset($group['wysiwyg']) == true && $group['wysiwyg'] == 'no') unset($group['wysiwyg']);
            if (isset($group['editable']) == true && $group['editable'] == 'no') unset($group['editable']);
        }

        // -----------------------------------------
        // Previews
        // -----------------------------------------
        if (isset($vData['small_preview']) == false OR $vData['small_preview'] == false)
        {
            $temp = reset($vData['action_groups']);
            $vData['small_preview'] = $temp['group_name'];
        }

        // Big Preview
        if (isset($vData['big_preview']) == false OR $vData['big_preview'] == false)
        {
            $temp = reset($vData['action_groups']);
            $vData['big_preview'] = $temp['group_name'];
        }


         $vData['action_groups'] = json_encode($vData['action_groups']);


         ee()->cp->add_to_foot('
        <script type="text/javascript">
        var ChannelImages = ChannelImages ? ChannelImages : new Object();
        ChannelImages.FTS = '.$vData['action_groups'].';
        ChannelImages.previews = {small:"'.$vData['small_preview'].'", big:"'.$vData['big_preview'].'"};
        </script>
        ');



        // -----------------------------------------
        // Display Row
        // -----------------------------------------
   //  $row1 = ee()->load->view('fts_settings', $vData, true);
      // ee()->table->add_row(array('data' => $row, 'colspan' => 2));


  $fields_action =array(
            array(
             //   'title'     => 'Some Field',
                'wide' => 'w-20',
                'fields'    => array(
                    'settings'  => array(
                        'type'  => 'html',
                        'content'=> ee('View')->make('channel_images:fts/actions')->render($vData),//ee()->load->view('fts/actions', array($vData), TRUE),
                    ),
                ),
            )
        );


    }

    // ********************************************************************************* //

    /**
     * Save the fieldtype settings.
     *
     * @param $data array Contains the submitted settings for this field.
     * @access public
     * @return array
     */
    public function save_settings($data)
    {
        return DevDemon\ChannelImages\Service\FieldtypeSettingsHelper::saveSettings($this->field_id, $data);

        $settings = array();

       // print_r($_POST);exit();

       if (isset($_POST['channel_images']) == FALSE) return $settings;

        $P = $_POST['channel_images'];

        // We need this for the url_title() method!
        ee()->load->helper('url');

        // Get Actions
        $actions = &ee('channel_images:Helper')->get_actions(); //&ee()->image_helper->get_actions();

        // -----------------------------------------
        // Loop over all action_groups (if any)
        // -----------------------------------------
        if (isset($P['action_groups']) == true)
        {
            foreach($P['action_groups'] as $order => &$group)
            {
                // Format Group Name
                $group['group_name'] = str_replace('@', '123atsign123', $group['group_name']); // Preserve the @ sign
                $group['group_name'] = strtolower(url_title($group['group_name']));
                $group['group_name'] = str_replace('123atsign123', '@', $group['group_name']); // Put it back!

                $group['final_size'] = false;

                // WYSIWYG
                if (isset($group['wysiwyg']) == false OR $group['wysiwyg'] == false)
                {
                    $group['wysiwyg'] = 'no';
                }

                // Editable
                if (isset($group['editable']) == false OR $group['editable'] == false)
                {
                    $group['editable'] = 'no';
                }

                // -----------------------------------------
                // Process Actions
                // -----------------------------------------
                if (isset($group['actions']) == false OR empty($group['actions']) == true)
                {
                     unset($P['action_groups'][$order]);
                    continue;
                }

                foreach($group['actions'] as $action => &$action_settings)
                {
                    ee()->cache->save('channel_images/group_final_size',false,500);

                    if (isset($actions[$action]) == false)
                    {
                        unset($group['actions'][$action]);
                        continue;
                    }

                    $action_settings = $actions[$action]->save_settings($action_settings);

                     $cgfs =ee()->cache->get('channel_images/group_final_size');
                    if ($cgfs != false)
                    {
                        $group['final_size'] = $cgfs;
                    }
                }
            }

            // -----------------------------------------
            // Previews
            // -----------------------------------------
            if (isset($P['small_preview']) == true && $P['small_preview'] != false)
            {
                $P['small_preview'] = $P['action_groups'][$P['small_preview']]['group_name'];
            }
            else
            {

                $P['small_preview'] = $P['action_groups'][1]['group_name'];
            }

            // Big Preview
            if (isset($P['big_preview']) == true && $P['big_preview'] != false)
            {
                $P['big_preview'] = $P['action_groups'][$P['big_preview']]['group_name'];
            }
            else
            {
                $P['big_preview'] = $P['action_groups'][1]['group_name'];
            }
        }
        else
        {
            // Mark it as having no sizes!
            $P['no_sizes'] = 'yes';
            $P['action_groups'] = array();
        }


        if (isset($P['categories']) == true)
        {
            // -----------------------------------------
            // Parse categories
            // -----------------------------------------
            $categories = array();
            foreach (explode(',', $P['categories']) as $cat)
            {
                $cat = trim ($cat);
                if ($cat != false) $categories[] = $cat;
            }

            $P['categories'] = $categories;


            if (substr($P['import_path'], -1) != '/') $P['import_path'] .= '/';
        }

        // -----------------------------------------
        // Put it Back!
        // -----------------------------------------
        $settings['channel_images'] = $P;


        return  array(
                        'field_wide' => true,
            'settings' =>$settings,

        );
    }

    // ********************************************************************************* //

    /**
     * Allows the specification of an array of fields to be added,
     * modified or dropped when custom fields are created, edited or deleted.
     *
     * $data contains the settings for this field as well an indicator of
     * the action being performed ($data['ee_action'] with a value of delete, add or get_info).
     *
     *  By default, when a new custom field is created,
     *  2 fields are added to the exp_channel_data table.
     *  The content field (field_id_x) is a text field and the format field (field_ft_x)
     *  is a tinytext NULL default. You may override or add to those defaults
     *  by including an array of fields and field formatting options in this method.
     *
     * @param $data array Contains the submitted settings for this field.
     * @access public
     * @return array
     */
    function settings_modify_column($data)
    {
        if ($data['ee_action'] == 'delete')
        {
            // Load the API
            if (class_exists('Channel_Images_API') != true) include 'api.channel_images.php';
            $API = new Channel_Images_API();

            $field_id = $data['field_id'];

            // Grab all images
            ee()->db->select('image_id, field_id, entry_id, filename, extension');
            ee()->db->from('exp_channel_images');
            ee()->db->where('field_id', $field_id);
            ee()->db->where('link_image_id', 0);
            $query = ee()->db->get();

            foreach ($query->result() as $row)
            {
                $API->delete_image($row);
            }
        }

        $fields = parent::settings_modify_column($data);

        return $fields;
    }

    // ********************************************************************************* //

    /**
     * Replace Tag - Replace the field tag on the frontend.
     *
     * @param  mixed   $data    contains the field data (or prepped data, if using pre_process)
     * @param  array   $params  contains field parameters (if any)
     * @param  boolean $tagdata contains data between tag (for tag pairs)
     * @return string           template data
     */
    public function replace_tag($data, $params=array(), $tagdata = false)
    {
        ee()->load->model('channel_images_model');

        // We always need tagdata
        if ($tagdata === false) return '';

        if (isset($params['prefetch']) == true && $params['prefetch'] == 'yes')
        {
            // In some cases EE stores the entry_ids of the whole loop
            // We can use this to our advantage by grabbing
            if (isset($this->EE->session->cache['channel']['entry_ids']) === true)
            {
                ee()->channel_images_model->pre_fetch_data(ee()->session->cache['channel']['entry_ids'], $params);
            }
        }

        return ee()->channel_images_model->parse_template($this->row['entry_id'], $this->field_id, $params, $tagdata);
    }

    // ********************************************************************************* //

    public function draft_save($data, $draft_action)
    {
        // -----------------------------------------
        // Are we creating a new draft?
        // -----------------------------------------
        if ($draft_action == 'create')
        {
            $this->create_draft($data);
        }

        $this->_process_post_save($data, $draft_action);

        if (isset($data['images']) == false) return '';
        else return 'ChannelImages';
    }

    // ********************************************************************************* //

    private function create_draft($data)
    {
        $this->draft_discard();

        // We are doing this because if you delete an image in live mode
        // and hit the draft button, we need to reflect that delete action in the draft
        $images = array();
        if (isset($data['images']) == true)
        {
            foreach ($data['images'] as $key => $file)
            {
                $file = json_decode($file['data']);
                if (isset($file->delete) === true)
                {
                    unset($data['images'][$key]);
                    continue;
                }

                if (isset($file->image_id) === true && $file->image_id > 0) $images[] = $file->image_id;
            }
        }

        if (count($images) > 0)
        {
            // Grab all existing images
            $query = ee()->db->select('*')->from('exp_channel_images')->where_in('image_id', $images)->get();

            foreach ($query->result_array() as $row)
            {
                $row['is_draft'] = 1;
                unset($row['image_id']);
                ee()->db->insert('exp_channel_images', $row);
            }
        }
    }

    // ********************************************************************************* //

    public function draft_discard()
    {
        $entry_id = $this->settings['entry_id'];
        $field_id = $this->settings['field_id'];

        // Load the API
        if (class_exists('Channel_Images_API') != true) include PATH_THIRD.'channel_images/api.channel_images.php';
        $API = new Channel_Images_API();

        // Grab all existing images
        $query = ee()->db->select('*')->from('exp_channel_images')->where('entry_id', $this->settings['entry_id'])->where('field_id', $this->settings['field_id'])->where('is_draft', 1)->get();

        foreach ($query->result() as $row)
        {
            $API->delete_image($row);
        }
    }

    // ********************************************************************************* //

    public function draft_publish()
    {
        // Load the API
        if (class_exists('Channel_Images_API') != true) include PATH_THIRD.'channel_images/api.channel_images.php';
        $API = new Channel_Images_API();

        // Grab all existing images
        $query = ee()->db->select('*')->from('exp_channel_images')->where('entry_id', $this->settings['entry_id'])->where('field_id', $this->settings['field_id'])->where('is_draft', 0)->get();

        foreach ($query->result() as $row)
        {
            $API->delete_image($row);
        }

        // Grab all existing images
        $query = ee()->db->select('image_id')->from('exp_channel_images')->where('entry_id', $this->settings['entry_id'])->where('field_id', $this->settings['field_id'])->where('is_draft', 1)->get();

        foreach ($query->result() as $row)
        {
            ee()->db->set('is_draft', 0);
            ee()->db->where('image_id', $row->image_id);
            ee()->db->update('exp_channel_images');
        }
    }

    // ********************************************************************************* //

    private function _process_post_save($data, $draft_action=NULL)
    {
        // -----------------------------------------
        // Increase all types of limits!
        // -----------------------------------------
        @set_time_limit(0);

        if ($this->moduleSettings['infinite_memory'] == 'yes') {
            @ini_set('memory_limit', '64M');
            @ini_set('memory_limit', '96M');
            @ini_set('memory_limit', '128M');
            @ini_set('memory_limit', '160M');
            @ini_set('memory_limit', '192M');
            @ini_set('memory_limit', '256M');
            @ini_set('memory_limit', '320M');
            @ini_set('memory_limit', '512M');
        }

        $entry_id = $this->content_id();
        ee()->load->helper('url');

        if (isset(ee()->publisher_lib) === true && (isset(ee()->publisher_lib->save_status) || isset(ee()->publisher_lib->publisher_save_status) ) ) {
            $imgdata = ee()->session->cache('channel_images', 'field-'.$this->field_id);
            $data = (empty($imgdata) == FALSE) ? ee()->session->cache('channel_images', 'field-'.$this->field_id) : false;

            $save_status = (isset(ee()->publisher_lib->save_status) === true) ? ee()->publisher_lib->save_status : ee()->publisher_lib->publisher_save_status;
            if ($save_status == 'draft') {
                $draft_action = 'create';

                if (ee()->publisher_entry->has_draft($entry_id)) {
                    $draft_action = 'update';
                }

                // Sometimes we are overwriting an existing draft!
                if ($draft_action == 'update' && ee()->publisher_lib->status != 'draft') {
                    $draft_action = 'create';
                }

                if ($draft_action == 'create') {
                    $this->create_draft($data);
                }
            } else {
                if (ee()->publisher_lib->status == 'draft') {
                    $this->draft_publish();
                }
            }
        }

        // Are we using Better Workflow?
        if ($draft_action !== NULL) {
            $is_draft = 1;
            $field_id = $this->settings['field_id'];
            $channel_id = isset($this->settings['channel_id']) ? $this->settings['channel_id'] : ee()->input->post('channel_id');
        } else {
            $is_draft = 0;
            $imgdata = ee()->session->cache('channel_images', 'field-'.$this->field_id);
            $data = (empty($imgdata) == FALSE) ? $imgdata : false;
            $channel_id = ee()->input->post('channel_id');
            $field_id = $this->field_id;
        }

        $settings =  ee('channel_images:Settings')->getFieldtypeSettings($field_id);

        // Moving Channels?
        if (ee()->input->get_post('new_channel') != false) {
            $channel_id = ee()->input->get_post('new_channel');
        }

        // Double check to see if we have a channel id
        if ($channel_id == false) {
            $query = ee()->db->select('channel_id')->from('exp_channel_titles')->where('entry_id', $entry_id)->get();
            $channel_id = $query->row('channel_id');
        }

        //ee()->firephp->log($data);

        // Do we need to skip?
        if (isset($data['images']) == false) return;
        if (is_array($data['images']) === false) return;

        // Our Key
        $key = $data['key'];

        // -----------------------------------------
        // Load Location
        // -----------------------------------------
        $location_type = $settings['upload_location'];
        $location_class = 'CI_Location_'.$location_type;
        $location_settings = $settings['locations'][$location_type];

        // Load Main Class
        if (class_exists('Image_Location') == false) require PATH_THIRD.'channel_images/locations/image_location.php';

        // Try to load Location Class
        if (class_exists($location_class) == false)
        {
            $location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';

            require $location_file;
        }

        // Init!
        $LOC = new $location_class($location_settings);

        // Create the DIR!
        $LOC->create_dir($entry_id);

        // Image Widths,Height,Filesize
        $metadata = array();

        // Try to load Location Class
        if (class_exists($location_class) == false)
        {
            $location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';

            require $location_file;
        }

        // Load the API
        if (class_exists('Channel_Images_API') != true) include 'api.channel_images.php';
        $API = new Channel_Images_API();

        // -----------------------------------------
        // Loop over all images and delete!
        // -----------------------------------------
        foreach ($data['images'] as $order => $file) {
            $file = json_decode($file['data']);
            $file->title = base64_decode($file->title);
            $file->description = base64_decode($file->description);
            $file->url_title = base64_decode($file->url_title);
            //$file->category = base64_decode($file->category);
            $file->cifield_1 = base64_decode($file->cifield_1);
            $file->cifield_2 = base64_decode($file->cifield_2);
            $file->cifield_3 = base64_decode($file->cifield_3);
            $file->cifield_4 = base64_decode($file->cifield_4);
            $file->cifield_5 = base64_decode($file->cifield_5);

            $file->cover = base64_decode($file->cover);

            $data['images'][$order] = $file;

            if (isset($file->delete) === true) {
                ee('channel_images:Images')->deleteImage($file);
                unset($data['images'][$order]);
            }
        }

        // -----------------------------------------
        // Upload all Images!
        // -----------------------------------------
        $temp_dir = $this->moduleSettings['cache_path'].'channel_images/field_'.$field_id.'/'.$key;

        // Loop over all files
        $tempfiles = @scandir($temp_dir);

        if (is_array($tempfiles) == true)
        {
            foreach ($tempfiles as $tempfile)
            {
                if ($tempfile == '.' OR $tempfile == '..') continue;

                $file   = $temp_dir . '/' . $tempfile;

                $res = $LOC->upload_file($file, $tempfile, $entry_id);

                if ($res == false)
                {

                }

                // Parse Image Size
                $imginfo = @getimagesize($file);

                // Metadata!
                $metadata[$tempfile] = array('width' => @$imginfo[0], 'height' => @$imginfo[1], 'size' => @filesize($file));

                @unlink($file);
            }
        }

        @rmdir($temp_dir);

        // -----------------------------------------
        // Grab all the files from the DB
        // -----------------------------------------
        ee()->db->select('*');
        ee()->db->from('exp_channel_images');
        ee()->db->where('entry_id', $entry_id);
        ee()->db->where('field_id', $field_id);

        if ($is_draft === 1 && $draft_action == 'update')
        {
            ee()->db->where('is_draft', 1);
        }
        else
        {
            ee()->db->where('is_draft', 0);
        }

        $query = ee()->db->get();

        // -----------------------------------------
        // Lets create an image hash! So we can check for unique images
        // -----------------------------------------
        $dbimages = array();
        foreach ($query->result() as $row)
        {
            $dbimages[] = $row->image_id.$row->filename;
        }

        if ($is_draft === 1 && $draft_action == 'create')
        {
            $dbimages = array();
        }

        $field_data = '';

        if (count($dbimages) > 0)
        {
            // Not fresh, lets see whats new.
            foreach ($data['images'] as $order => $file)
            {
                //Extension
                $extension = substr( strrchr($file->filename, '.'), 1);

                // Mime type
                $filemime = 'image/jpeg';
                if ($extension == 'png') $filemime = 'image/png';
                elseif ($extension == 'gif') $filemime = 'image/gif';

                // Check for link_image_id
                if (isset($file->link_image_id) == false) $file->link_image_id = 0;
                $file->link_entryid = 0;
                $file->link_channelid = 0;
                $file->link_fieldid = 0;

                // Check URL Title
                if (isset($file->url_title) == false OR $file->url_title == false) {
                    $file->url_title = url_title(trim(strtolower($file->title)));
                }

                if ($this->in_multi_array($file->image_id.$file->filename, $dbimages) === false) {
                    // Parse Image Size
                    $width=''; $height=''; $filesize='';

                    // -----------------------------------------
                    // Parse width/height/field_id/channel_id/entry_id
                    // -----------------------------------------
                    if ($file->link_image_id > 0) {
                        $imgquery = ee()->db->query("SELECT entry_id, field_id, channel_id, filesize, width, height, sizes_metadata FROM exp_channel_images WHERE image_id = {$file->link_image_id} ");
                        $file->link_entryid = $imgquery->row('entry_id');
                        $file->link_channelid = $imgquery->row('channel_id');
                        $file->link_fieldid = $imgquery->row('field_id');
                        $width = $imgquery->row('width');
                        $height = $imgquery->row('height');
                        $filesize = $imgquery->row('filesize');
                        $mt = $imgquery->row('sizes_metadata');
                        if (is_string($mt) == false) $mt = ''; // Some installs get weird mysql errors
                    } else {
                        $width = isset($metadata[$file->filename]['width']) ? $metadata[$file->filename]['width'] : 0;
                        $height = isset($metadata[$file->filename]['height']) ? $metadata[$file->filename]['height'] : 0;
                        $filesize = isset($metadata[$file->filename]['size']) ? $metadata[$file->filename]['size'] : 0;

                        // -----------------------------------------
                        // Parse Size Metadata!
                        // -----------------------------------------
                        $mt = '';
                        foreach($settings['action_groups'] as $group)
                        {
                            $name = strtolower($group['group_name']);
                            $size_filename = str_replace('.'.$extension, "__{$name}.{$extension}", $file->filename);

                            $mt .= $name.'|' . implode('|', $metadata[$size_filename]) . '/';
                        }
                    }

                    // -----------------------------------------
                    // New File
                    // -----------------------------------------
                    $data = array(  'site_id'   =>  $this->site_id,
                                    'entry_id'  =>  $entry_id,
                                    'channel_id'=>  $channel_id,
                                    'member_id' =>  ee()->session->userdata['member_id'],
                                    'is_draft'  =>  $is_draft,
                                    'link_image_id' => $file->link_image_id,
                                    'link_entry_id' => $file->link_entryid,
                                    'link_channel_id' => $file->link_channelid,
                                    'link_field_id' => $file->link_fieldid,
                                    'upload_date' => ee()->localize->now,
                                    'field_id'  =>  $field_id,
                                    'image_order'   =>  $order,
                                    'filename'  =>  $file->filename,
                                    'extension' =>  $extension,
                                    'mime'      =>  $filemime,
                                    'filesize'  =>  $filesize,
                                    'width'     =>  $width,
                                    'height'    =>  $height,
                                    'title'     =>  $API->process_field_string($file->title),
                                    'url_title' =>  $API->process_field_string($file->url_title),
                                    'description' => $API->process_field_string($file->description),
                                    'category'  =>  (isset($file->category) == true) ? $API->process_field_string($file->category) : '',
                                    'cifield_1' =>  $API->process_field_string($file->cifield_1),
                                    'cifield_2' =>  $API->process_field_string($file->cifield_2),
                                    'cifield_3' =>  $API->process_field_string($file->cifield_3),
                                    'cifield_4' =>  $API->process_field_string($file->cifield_4),
                                    'cifield_5' =>  $API->process_field_string($file->cifield_5),
                                    'cover'     =>  $file->cover ? 1 : 0,
                                    'sizes_metadata' => $mt,
                                    'iptc'      =>  $file->iptc,
                                    'exif'      =>  $file->exif,
                                    'xmp'       =>  $file->xmp,
                                );

                    ee()->db->insert('exp_channel_images', $data);
                }
                else
                {
                    // -----------------------------------------
                    // Old File
                    // -----------------------------------------
                    $data = array(  'cover'     =>  $file->cover ? 1 : 0,
                                    'channel_id'=>  $channel_id,
                                    'field_id'  =>  $field_id,
                                    'is_draft'  =>  $is_draft,
                                    'image_order'=> $order,
                                    'title'     =>  $API->process_field_string($file->title),
                                    'url_title' =>  $API->process_field_string($file->url_title),
                                    'description' => $API->process_field_string($file->description),
                                    'category'  =>  (isset($file->category) == true) ? $file->category : '',
                                    'cifield_1' =>  $API->process_field_string($file->cifield_1),
                                    'cifield_2' =>  $API->process_field_string($file->cifield_2),
                                    'cifield_3' =>  $API->process_field_string($file->cifield_3),
                                    'cifield_4' =>  $API->process_field_string($file->cifield_4),
                                    'cifield_5' =>  $API->process_field_string($file->cifield_5),
                                    'mime'      =>  $filemime,
                                );


                    ee()->db->update('exp_channel_images', $data, array('image_id' =>$file->image_id));
                }
            }
        }
        else
        {

            // No previous entries, fresh fresh
            foreach ($data['images'] as $order => $file)
            {
                // If we are creating a new draft, we already copied all data.. So lets kill the ones that came through POST
                if ($is_draft === 1 && $file->image_id > 0)
                {
                    // -----------------------------------------
                    // Old File
                    // -----------------------------------------
                    ee()->db->set('cover', ($file->cover ? 1 : 0) );
                    ee()->db->set('image_order', $order);
                    ee()->db->set('title', $API->process_field_string($file->title) );
                    ee()->db->set('url_title', $API->process_field_string($file->url_title) );
                    ee()->db->set('description', $API->process_field_string($file->description) );
                    ee()->db->set('category', ((isset($file->category) == true) ? $file->category : '') );
                    ee()->db->set('cifield_1', $API->process_field_string($file->cifield_1) );
                    ee()->db->set('cifield_2', $API->process_field_string($file->cifield_2) );
                    ee()->db->set('cifield_3', $API->process_field_string($file->cifield_3) );
                    ee()->db->set('cifield_4', $API->process_field_string($file->cifield_4) );
                    ee()->db->set('cifield_5', $API->process_field_string($file->cifield_5) );
                    ee()->db->where('filename', $file->filename);
                    ee()->db->where('entry_id', $file->entry_id);
                    ee()->db->where('filesize', $file->filesize);
                    ee()->db->where('is_draft', 1);
                    ee()->db->update('exp_channel_images');

                    continue;
                }

                //if ($file->image_id > 0) {
                if ($file->image_id > 0 && $entry_id == $file->entry_id) {
                    continue;
                }


                //Extension
                $extension = substr( strrchr($file->filename, '.'), 1);

                // Mime type
                $filemime = 'image/jpeg';
                if ($extension == 'png') $filemime = 'image/png';
                elseif ($extension == 'gif') $filemime = 'image/gif';

                // Check for link_image_id
                if (isset($file->link_image_id) == false) $file->link_image_id = 0;
                $file->link_entryid = 0;
                $file->link_channelid = 0;
                $file->link_fieldid = 0;

                // Parse Image Size
                $width=''; $height=''; $filesize='';

                // Lets grab original width/height/field_id/channel_id/entry_id
                if ($file->link_image_id > 0)
                {
                    $imgquery = ee()->db->query("SELECT entry_id, field_id, channel_id, filesize, width, height, sizes_metadata FROM exp_channel_images WHERE image_id = {$file->link_image_id} ");
                    $file->link_entryid = $imgquery->row('entry_id');
                    $file->link_channelid = $imgquery->row('channel_id');
                    $file->link_fieldid = $imgquery->row('field_id');
                    $width = $imgquery->row('width');
                    $height = $imgquery->row('height');
                    $filesize = $imgquery->row('filesize');
                    $mt = $imgquery->row('sizes_metadata');
                    if (is_string($mt) == false) $mt = ''; // Some installs get weird mysql errors
                }
                else
                {
                    $width = isset($metadata[$file->filename]['width']) ? $metadata[$file->filename]['width'] : 0;
                    $height = isset($metadata[$file->filename]['height']) ? $metadata[$file->filename]['height'] : 0;
                    $filesize = isset($metadata[$file->filename]['size']) ? $metadata[$file->filename]['size'] : 0;

                    // -----------------------------------------
                    // Parse Size Metadata!
                    // -----------------------------------------
                    $mt = '';
                    foreach($settings['action_groups'] as $group)
                    {
                        $name = strtolower($group['group_name']);
                        $size_filename = str_replace('.'.$extension, "__{$name}.{$extension}", $file->filename);

                        $mt .= $name.'|' . implode('|', $metadata[$size_filename]) . '/';
                    }
                }

                // Check URL Title
                if (isset($file->url_title) OR $file->url_title == false)
                {
                    $file->url_title = url_title(trim(strtolower($file->title)));
                }

                // -----------------------------------------
                // New File
                // -----------------------------------------
                $data = array(  'site_id'   =>  $this->site_id,
                                'entry_id'  =>  $entry_id,
                                'channel_id'=>  $channel_id,
                                'member_id'=>   ee()->session->userdata['member_id'],
                                'is_draft'  =>  $is_draft,
                                'link_image_id' => $file->link_image_id,
                                'link_entry_id' => $file->link_entryid,
                                'link_channel_id' => $file->link_channelid,
                                'link_field_id' => $file->link_fieldid,
                                'upload_date' => ee()->localize->now,
                                'field_id'=>    $field_id,
                                'image_order'   =>  $order,
                                'filename'  =>  $file->filename,
                                'extension' =>  $extension,
                                'mime'      =>  $filemime,
                                'filesize'  =>  $filesize,
                                'width'     =>  $width,
                                'height'    =>  $height,
                                'title'     =>  $API->process_field_string($file->title),
                                'url_title' =>  $API->process_field_string($file->url_title),
                                'description' => $API->process_field_string($file->description),
                                'category'  =>  (isset($file->category) == true) ? $file->category : '',
                                'cifield_1' =>  $API->process_field_string($file->cifield_1),
                                'cifield_2' =>  $API->process_field_string($file->cifield_2),
                                'cifield_3' =>  $API->process_field_string($file->cifield_3),
                                'cifield_4' =>  $API->process_field_string($file->cifield_4),
                                'cifield_5' =>  $API->process_field_string($file->cifield_5),
                                'cover'     =>  $file->cover ? 1 : 0,
                                'sizes_metadata' => $mt,
                                'iptc'      =>  $file->iptc,
                                'exif'      =>  $file->exif,
                                'xmp'       =>  $file->xmp,
                            );

                ee()->db->insert('exp_channel_images', $data);
            }
        }

        // -----------------------------------------
        // WYGWAM
        // -----------------------------------------

        // Which field_group is assigned to this channel?
        $query = ee()->db->select('field_group')->from('exp_channels')->where('channel_id', $channel_id)->get();
        if ($query->num_rows() == 0) return;
        $field_group = $query->row('field_group');

        // Which fields are WYGWAM/wyvern AND Textarea
        ee()->db->select('field_id');
        ee()->db->from('exp_channel_fields');
        ee()->db->where('group_id', $field_group);
        ee()->db->where('field_type', 'wyvern');
        ee()->db->or_where('field_type', 'textarea');
        ee()->db->or_where('field_type', 'rte');
        $query = ee()->db->get();
        if ($query->num_rows() == 0) return;

        // Harvest all of them
        $fields = array();

        foreach ($query->result() as $row)
        {
            $fields[] = 'field_id_' . $row->field_id;
        }

        if (count($fields) > 0)
        {
            // Grab them!
            foreach ($fields as $field)
            {
                ee()->db->set($field, "
                 REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE({$field}, 'temp_dir=yes', ''),
                        'temp_dir=yes', ''),
                    'd={$key}&amp;', 'd={$entry_id}&amp;'),
                'd={$key}&', 'd={$entry_id}&')

                ", false);
                ee()->db->where('entry_id', $entry_id);
                ee()->db->update('exp_channel_data');
            }

        }

        // Delete old dirs
        $API->clean_temp_dirs($field_id);

        //preg_match_all('/\< *[img][^\>]*[src] *= *[\"\']{0,1}([^\"\'\ >]*)/i', $field, $matches);
//exit();
        // -----------------------------------------
        // Just to be sure (if save_data_in_field is yes, we could overwrite previous data..)
        // -----------------------------------------
        if ( (isset($settings['save_data_in_field']) == false || $settings['save_data_in_field'] == 'no') && $is_draft == 0 )
        {
            $query = ee()->db->select('image_id')->from('exp_channel_images')->where('field_id', $field_id)->where('entry_id', $entry_id)->where('is_draft', 0)->get();
            if ($query->num_rows() == 0) ee()->db->set('field_id_'.$field_id, '');
            else ee()->db->set('field_id_'.$field_id, 'ChannelImages');
            ee()->db->where('entry_id', $entry_id);
            ee()->db->update('exp_channel_data');
        }

        return;
    }

    // ********************************************************************************* //

    /**
    *   ======================
    *   function zenbu_display
    *   ======================
    *   Set up display in entry result cell
    *
    *   @param  $entry_id           int     The entry ID of this single result entry
    *   @param  $channel_id         int     The channel ID associated to this single result entry
    *   @param  $data               array   Raw data as found in database cell in exp_channel_data
    *   @param  $table_data         array   Data array usually retrieved from other table than exp_channel_data
    *   @param  $field_id           int     The ID of this field
    *   @param  $settings           array   The settings array, containing saved field order, display, extra options etc settings
    *   @param  $rules              array   An array of entry filtering rules
    *   @param  $upload_prefs       array   An array of upload preferences (optional)
    *   @param  $installed_addons   array   An array of installed addons and their version numbers (optional)
    *   @param  $fieldtypes         array   Fieldtype of available fieldtypes: id, name, etc (optional)
    *   @return $output     The HTML used to display data
    */
    public function zenbu_display($entry_id, $channel_id, $field_data, $ch_img_data = array(), $field_id, $zenbuSettings, $rules = array(), $upload_prefs = array(), $installed_addons)
    {
        $output = '&nbsp;';

        $settings = ee('channel_images:Settings')->getFieldtypeSettings($field_id, $ch_img_data['field_settings'][$field_id]);

        if (isset($ch_img_data['entries'][$entry_id][$field_id]) === false) return $output;
        if (isset($settings['small_preview']) === false) return $output;

        $small_preview = $settings['small_preview'];
        $big_preview = $settings['big_preview'];

        $max = 999;
        $extra_options = $zenbuSettings['setting'][$channel_id]['extra_options'];
        if (isset($extra_options['field_'.$field_id]['ci_img_show_cover']) === true && $extra_options['field_'.$field_id]['ci_img_show_cover'] == 'yes')
        {
            $max = 1;
        }

        foreach ($ch_img_data['entries'][$entry_id][$field_id] as $count => $image)
        {
            if ($max == $count) break;

            if ($image->link_entry_id > 0)
            {
                $image->entry_id = $image->link_entry_id;
                $image->field_id = $image->link_field_id;
            }

            $act_url_params = "&amp;fid={$image->field_id}&amp;d={$image->entry_id}";

            // Display SIzes URL
            $small_filename = str_replace('.'.$image->extension, "__{$small_preview}.{$image->extension}", urlencode($image->filename) );
            $big_filename = str_replace('.'.$image->extension, "__{$big_preview}.{$image->extension}", urlencode($image->filename) );

            $image->small_img_url = "{$ch_img_data['preview_url']}&amp;f={$small_filename}{$act_url_params}";
            $image->big_img_url = "{$ch_img_data['preview_url']}&amp;f={$big_filename}{$act_url_params}";

            $output .= anchor($image->big_img_url, "<img src='{$image->small_img_url}' width='".$this->moduleSettings['image_preview_size']."' style='margin-right:5px;margin-bottom:5px;'>", 'class="fancybox" rel="ci_img_'.$entry_id.'" title="'.$image->title.'"');
        }

        return $output;
    }

    // ********************************************************************************* //

    /**
    *   =============================
    *   function zenbu_get_table_data
    *   =============================
    *   Retrieve data stored in other database tables
    *   based on results from Zenbu's entry list
    *   @uses   Instead of many small queries, this function can be used to carry out
    *           a single query of data to be later processed by the zenbu_display() method
    *
    *   @param  $entry_ids              array   An array of entry IDs from Zenbu's entry listing results
    *   @param  $field_ids              array   An array of field IDs tied to/associated with result entries
    *   @param  $channel_id             int     The ID of the channel in which Zenbu searched entries (0 = "All channels")
    *   @param  $output_upload_prefs    array   An array of upload preferences
    *   @param  $settings               array   The settings array, containing saved field order, display, extra options etc settings
    *   @param  $rel_array              array   A simple array useful when using related entry-type fields (optional)
    *   @return $output                 array   An array of data (typically broken down by entry_id then field_id) that can be used and processed by the zenbu_display() method
    */
    function zenbu_get_table_data($entry_ids, $field_ids, $channel_id, $output_upload_prefs, $settings)
    {
        $output = array();
        if(empty($entry_ids) || empty($field_ids) || empty($output_upload_prefs) || empty($channel_id))
        {
            return $output;
        }
        $output['preview_url'] = ee('channel_images:Helper')->getRouterUrl('url', 'simple_image_url');
        $output['entries'] = array();
        $output['field_settings'] = array();

        // Get channel images field settings
        ee()->db->select(array("field_id", "field_settings"));
        ee()->db->from("exp_channel_fields");
        ee()->db->where("field_type", "channel_images");
        ee()->db->where_in("field_id", $field_ids);
        $field_settings_query = ee()->db->get();

        if($field_settings_query->num_rows() > 0)
        {
            foreach($field_settings_query->result_array() as $row)
            {
                $output['field_settings'][$row['field_id']] = unserialize(base64_decode($row['field_settings']));
            }
        }


        // Perform the query
        ee()->db->select('field_id, entry_id, filename, extension, link_image_id, link_entry_id, link_field_id, title');
        ee()->db->from('exp_channel_images');
        ee()->db->where_in("entry_id", $entry_ids);
        ee()->db->where_in("field_id", $field_ids);
        ee()->db->where("channel_id", $channel_id);
        ee()->db->order_by("cover", "desc");
        ee()->db->order_by("image_order", "asc");
        $query = ee()->db->get();

        // Create array for output
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                if (isset($output['entries'][$row->entry_id][$row->field_id]) === false) $output['entries'][$row->entry_id][$row->field_id] = array();
                $output['entries'][$row->entry_id][$row->field_id][] = $row;
            }
        }

        return $output;
    }

    // ********************************************************************************* //

    /**
    *   ===================================
    *   function zenbu_field_extra_settings
    *   ===================================
    *   Set up display for this fieldtype in "display settings"
    *
    *   @param  $table_col          string  A Zenbu table column name to be used for settings and input field labels
    *   @param  $channel_id         int     The channel ID for this field
    *   @param  $extra_options      array   The Zenbu field settings, used to retieve pre-saved data
    *   @return $output     The HTML used to display setting fields
    */
    public function zenbu_field_extra_settings($table_col, $channel_id, $extra_options)
    {

        $option_label_array = array(
            'ci_img_show_cover' => ee()->lang->line('ci:zenbu_show_cover'),
        );

        foreach($option_label_array as $label => $lang_string)
        {
            $checked = (isset($extra_options[$label])) ? true : false;
            $output[$label] = form_label(form_checkbox('settings['.$channel_id.']['.$table_col.']['.$label.']', 'yes', $checked).'&nbsp;'.$lang_string).'<br />';
        }

        return $output;
    }

    // ********************************************************************************* //

    /**
    *   ===================================
    *   function zenbu_result_query
    *   ===================================
    *   Extra queries to be intergrated into main entry result query
    *
    *   @param  $rules              int     An array of entry filtering rules
    *   @param  $field_id           array   The ID of this field
    *   @param  $fieldtypes         array   $fieldtype data
    *   @param  $already_queried    bool    Used to avoid using a FROM statement for the same field twice
    *   @return                 A query to be integrated with entry results. Should be in CI Active Record format (ee()->db->…)
    */
    public function zenbu_result_query($rules = array(), $field_id = "", $fieldtypes, $already_queried = false)
    {
        if(empty($rules))
        {
            return;
        }

        if($already_queried === false)
        {
            ee()->db->from("exp_channel_images");
        }

        ee()->db->where("exp_channel_images.field_id", $field_id);
        $col_query = ee()->db->query("/* Zenbu: Show columns for Channel Images */\nSHOW COLUMNS FROM exp_channel_images");
        $concat = "";
        $where_in = array();
        $db_columns = array("filename", "title", "description", "category");

        if($col_query->num_rows() > 0)
        {
            foreach($col_query->result_array() as $row)
            {

                if(in_array($row['Field'], $db_columns))
                {
                    $concat .= 'exp_channel_images.'.$row['Field'].', ';
                }

            }
            $concat = substr($concat, 0, -2);
        }

        if( ! empty($concat))
        {
            // Find entry_ids that have the keyword
            foreach($rules as $rule)
            {
                $rule_field_id = (strncmp($rule['field'], 'field_', 6) == 0) ? substr($rule['field'], 6) : 0;
                if(isset($fieldtypes['fieldtype'][$rule_field_id]) && $fieldtypes['fieldtype'][$rule_field_id] == "channel_images")
                {
                    $keyword = $rule['val'];

                    $keyword_query = ee()->db->query("/* Zenbu: Search Channel Images */\nSELECT entry_id FROM exp_channel_images WHERE \nCONCAT_WS(',', ".$concat.") \nLIKE '%".ee()->db->escape_like_str($keyword)."%'");
                    $where_in = array();
                    if($keyword_query->num_rows() > 0)
                    {
                        foreach($keyword_query->result_array() as $row)
                        {
                            $where_in[] = $row['entry_id'];
                        }
                    }
                } // if
            } // foreach

            // If $keyword_query has hits, $where_in should not be empty.
            // In that case finish the query
            if( ! empty($where_in))
            {
                if($rule['cond'] == "doesnotcontain")
                {
                    // …then query entries NOT in the group of entries
                    ee()->db->where_not_in("exp_channel_titles.entry_id", $where_in);
                } else {
                    ee()->db->where_in("exp_channel_titles.entry_id", $where_in);
                }
            } else {
            // However, $keyword_query has no hits (like on an unexistent word), $where_in will be empty
            // Send no results for: "search field containing this unexistent word".
            // Else, just show everything, as obviously all entries will not contain the odd word
                if($rule['cond'] == "contains")
                {
                    $where_in[] = 0;
                    ee()->db->where_in("exp_channel_titles.entry_id", $where_in);
                }
            }

        } // if
    }

    /**
     * Function for looking for a value in a multi-dimensional array
     *
     * @param string $value
     * @param array $array
     * @return bool
     */
    protected function in_multi_array($value, $array)
    {
        foreach ($array as $key => $item)
        {
            // Item is not an array
            if (!is_array($item))
            {
                // Is this item our value?
                if ($item == $value) return TRUE;
            }

            // Item is an array
            else
            {
                // See if the array name matches our value
                //if ($key == $value) return true;

                // See if this array matches our value
                if (in_array($value, $item)) return TRUE;

                // Search this array
                else if ($this->in_multi_array($value, $item)) return TRUE;
            }
        }

        // Couldn't find the value in array
        return FALSE;
    }
}

class EncodingCI {

    protected static $win1252ToUtf8 = array(
        128 => "\xe2\x82\xac",

        130 => "\xe2\x80\x9a",
        131 => "\xc6\x92",
        132 => "\xe2\x80\x9e",
        133 => "\xe2\x80\xa6",
        134 => "\xe2\x80\xa0",
        135 => "\xe2\x80\xa1",
        136 => "\xcb\x86",
        137 => "\xe2\x80\xb0",
        138 => "\xc5\xa0",
        139 => "\xe2\x80\xb9",
        140 => "\xc5\x92",

        142 => "\xc5\xbd",

        145 => "\xe2\x80\x98",
        146 => "\xe2\x80\x99",
        147 => "\xe2\x80\x9c",
        148 => "\xe2\x80\x9d",
        149 => "\xe2\x80\xa2",
        150 => "\xe2\x80\x93",
        151 => "\xe2\x80\x94",
        152 => "\xcb\x9c",
        153 => "\xe2\x84\xa2",
        154 => "\xc5\xa1",
        155 => "\xe2\x80\xba",
        156 => "\xc5\x93",

        158 => "\xc5\xbe",
        159 => "\xc5\xb8"
    );

    protected static $brokenUtf8ToUtf8 = array(
        "\xc2\x80" => "\xe2\x82\xac",

        "\xc2\x82" => "\xe2\x80\x9a",
        "\xc2\x83" => "\xc6\x92",
        "\xc2\x84" => "\xe2\x80\x9e",
        "\xc2\x85" => "\xe2\x80\xa6",
        "\xc2\x86" => "\xe2\x80\xa0",
        "\xc2\x87" => "\xe2\x80\xa1",
        "\xc2\x88" => "\xcb\x86",
        "\xc2\x89" => "\xe2\x80\xb0",
        "\xc2\x8a" => "\xc5\xa0",
        "\xc2\x8b" => "\xe2\x80\xb9",
        "\xc2\x8c" => "\xc5\x92",

        "\xc2\x8e" => "\xc5\xbd",

        "\xc2\x91" => "\xe2\x80\x98",
        "\xc2\x92" => "\xe2\x80\x99",
        "\xc2\x93" => "\xe2\x80\x9c",
        "\xc2\x94" => "\xe2\x80\x9d",
        "\xc2\x95" => "\xe2\x80\xa2",
        "\xc2\x96" => "\xe2\x80\x93",
        "\xc2\x97" => "\xe2\x80\x94",
        "\xc2\x98" => "\xcb\x9c",
        "\xc2\x99" => "\xe2\x84\xa2",
        "\xc2\x9a" => "\xc5\xa1",
        "\xc2\x9b" => "\xe2\x80\xba",
        "\xc2\x9c" => "\xc5\x93",

        "\xc2\x9e" => "\xc5\xbe",
        "\xc2\x9f" => "\xc5\xb8"
    );

    protected static $utf8ToWin1252 = array(
        "\xe2\x82\xac" => "\x80",

        "\xe2\x80\x9a" => "\x82",
        "\xc6\x92"     => "\x83",
        "\xe2\x80\x9e" => "\x84",
        "\xe2\x80\xa6" => "\x85",
        "\xe2\x80\xa0" => "\x86",
        "\xe2\x80\xa1" => "\x87",
        "\xcb\x86"     => "\x88",
        "\xe2\x80\xb0" => "\x89",
        "\xc5\xa0"     => "\x8a",
        "\xe2\x80\xb9" => "\x8b",
        "\xc5\x92"     => "\x8c",

        "\xc5\xbd"     => "\x8e",

        "\xe2\x80\x98" => "\x91",
        "\xe2\x80\x99" => "\x92",
        "\xe2\x80\x9c" => "\x93",
        "\xe2\x80\x9d" => "\x94",
        "\xe2\x80\xa2" => "\x95",
        "\xe2\x80\x93" => "\x96",
        "\xe2\x80\x94" => "\x97",
        "\xcb\x9c"     => "\x98",
        "\xe2\x84\xa2" => "\x99",
        "\xc5\xa1"     => "\x9a",
        "\xe2\x80\xba" => "\x9b",
        "\xc5\x93"     => "\x9c",

        "\xc5\xbe"     => "\x9e",
        "\xc5\xb8"     => "\x9f"
    );

    /**
   * Function Encoding::toUTF8
   *
   * This function leaves UTF8 characters alone, while converting almost all non-UTF8 to UTF8.
   *
   * It assumes that the encoding of the original string is either Windows-1252 or ISO 8859-1.
   *
   * It may fail to convert characters to UTF-8 if they fall into one of these scenarios:
   *
   * 1) when any of these characters:   ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß
   *    are followed by any of these:  ("group B")
   *                                    ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶•¸¹º»¼½¾¿
   * For example:   %ABREPRESENT%C9%BB. «REPRESENTÉ»
   * The "«" (%AB) character will be converted, but the "É" followed by "»" (%C9%BB)
   * is also a valid unicode character, and will be left unchanged.
   *
   * 2) when any of these: àáâãäåæçèéêëìíîï  are followed by TWO chars from group B,
   * 3) when any of these: ðñòó  are followed by THREE chars from group B.
   *
   * @name toUTF8
   * @param string $text  Any string.
   * @return string  The same string, UTF8 encoded
   *
   */
    static function toUTF8($text){
        if(is_array($text))
        {
          foreach($text as $k => $v)
          {
            $text[$k] = self::toUTF8($v);
          }
          return $text;
        } elseif(is_string($text)) {

          if ( function_exists('mb_strlen') && ((int) ini_get('mbstring.func_overload')) & 2) {
             $max = mb_strlen($text,'8bit');
          } else {
             $max = strlen($text);
          }

          $buf = "";
          for($i = 0; $i < $max; $i++){
              $c1 = $text{$i};
              if($c1>="\xc0"){ //Should be converted to UTF8, if it's not UTF8 already
                $c2 = $i+1 >= $max? "\x00" : $text{$i+1};
                $c3 = $i+2 >= $max? "\x00" : $text{$i+2};
                $c4 = $i+3 >= $max? "\x00" : $text{$i+3};
                  if($c1 >= "\xc0" & $c1 <= "\xdf"){ //looks like 2 bytes UTF8
                      if($c2 >= "\x80" && $c2 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                          $buf .= $c1 . $c2;
                          $i++;
                      } else { //not valid UTF8.  Convert it.
                          $cc1 = (chr(ord($c1) / 64) | "\xc0");
                          $cc2 = ($c1 & "\x3f") | "\x80";
                          $buf .= $cc1 . $cc2;
                      }
                  } elseif($c1 >= "\xe0" & $c1 <= "\xef"){ //looks like 3 bytes UTF8
                      if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                          $buf .= $c1 . $c2 . $c3;
                          $i = $i + 2;
                      } else { //not valid UTF8.  Convert it.
                          $cc1 = (chr(ord($c1) / 64) | "\xc0");
                          $cc2 = ($c1 & "\x3f") | "\x80";
                          $buf .= $cc1 . $cc2;
                      }
                  } elseif($c1 >= "\xf0" & $c1 <= "\xf7"){ //looks like 4 bytes UTF8
                      if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf" && $c4 >= "\x80" && $c4 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                          $buf .= $c1 . $c2 . $c3;
                          $i = $i + 2;
                      } else { //not valid UTF8.  Convert it.
                          $cc1 = (chr(ord($c1) / 64) | "\xc0");
                          $cc2 = ($c1 & "\x3f") | "\x80";
                          $buf .= $cc1 . $cc2;
                      }
                  } else { //doesn't look like UTF8, but should be converted
                          $cc1 = (chr(ord($c1) / 64) | "\xc0");
                          $cc2 = (($c1 & "\x3f") | "\x80");
                          $buf .= $cc1 . $cc2;
                  }
              } elseif(($c1 & "\xc0") == "\x80"){ // needs conversion
                    if(isset(self::$win1252ToUtf8[ord($c1)])) { //found in Windows-1252 special cases
                        $buf .= self::$win1252ToUtf8[ord($c1)];
                    } else {
                      $cc1 = (chr(ord($c1) / 64) | "\xc0");
                      $cc2 = (($c1 & "\x3f") | "\x80");
                      $buf .= $cc1 . $cc2;
                    }
              } else { // it doesn't need conversion
                  $buf .= $c1;
              }
          }
          return $buf;
        } else {
          return $text;
        }
    }

    static function toWin1252($text) {
        if(is_array($text)) {
          foreach($text as $k => $v) {
            $text[$k] = self::toWin1252($v);
          }
          return $text;
        } elseif(is_string($text)) {
          return utf8_decode(str_replace(array_keys(self::$utf8ToWin1252), array_values(self::$utf8ToWin1252), self::toUTF8($text)));
        } else {
          return $text;
        }
    }

    static function toISO8859($text) {
        return self::toWin1252($text);
    }

    static function toLatin1($text) {
        return self::toWin1252($text);
    }

    static function fixUTF8($text){
        if(is_array($text)) {
          foreach($text as $k => $v) {
            $text[$k] = self::fixUTF8($v);
          }
          return $text;
        }

        $last = "";
        while($last <> $text){
          $last = $text;
          $text = self::toUTF8(utf8_decode(str_replace(array_keys(self::$utf8ToWin1252), array_values(self::$utf8ToWin1252), $text)));
        }
        $text = self::toUTF8(utf8_decode(str_replace(array_keys(self::$utf8ToWin1252), array_values(self::$utf8ToWin1252), $text)));
        return $text;
    }

    static function UTF8FixWin1252Chars($text){
        // If you received an UTF-8 string that was converted from Windows-1252 as it was ISO8859-1
        // (ignoring Windows-1252 chars from 80 to 9F) use this function to fix it.
        // See: http://en.wikipedia.org/wiki/Windows-1252

        return str_replace(array_keys(self::$brokenUtf8ToUtf8), array_values(self::$brokenUtf8ToUtf8), $text);
    }

    static function removeBOM($str=""){
        if(substr($str, 0,3) == pack("CCC",0xef,0xbb,0xbf)) {
          $str=substr($str, 3);
        }
        return $str;
    }

    public static function normalizeEncoding($encodingLabel)
    {
        $encoding = strtoupper($encodingLabel);
        $enc = preg_replace('/[^a-zA-Z0-9\s]/', '', $encoding);
        $equivalences = array(
            'ISO88591' => 'ISO-8859-1',
            'ISO8859'  => 'ISO-8859-1',
            'ISO'      => 'ISO-8859-1',
            'LATIN1'   => 'ISO-8859-1',
            'LATIN'    => 'ISO-8859-1',
            'UTF8'     => 'UTF-8',
            'UTF'      => 'UTF-8',
            'WIN1252'  => 'ISO-8859-1',
            'WINDOWS1252' => 'ISO-8859-1'
        );

        if(empty($equivalences[$encoding])){
          return 'UTF-8';
        }

        return $equivalences[$encoding];
    }

    public static function encode($encodingLabel, $text)
    {
        $encodingLabel = self::normalizeEncoding($encodingLabel);
        if($encodingLabel == 'UTF-8') return Encoding::toUTF8($text);
        if($encodingLabel == 'ISO-8859-1') return Encoding::toLatin1($text);
    }
}

/* End of file ft.channel_images.php */
/* Location: ./system/user/addons/channel_images/ft.channel_images.php */