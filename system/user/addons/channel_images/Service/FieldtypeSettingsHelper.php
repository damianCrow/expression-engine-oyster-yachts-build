<?php

namespace DevDemon\ChannelImages\Service;

class FieldtypeSettingsHelper
{
    protected static $settings;
    protected static $module;

    public static function showSettings($field_id, $data)
    {
        ee()->lang->loadfile('channel_images');

        self::$settings = ee('channel_images:Settings')->getFieldtypeSettings($field_id);
        self::$module = ee('App')->get('channel_images');

        $sections = array();
        $sections['field_options_ci_general'] = array(
            'label'    => 'field_options',
            'group'    => 'channel_images',
            'settings' => self::settingsGeneral(),
        );

        $sections['field_options_ci_locations'] = array(
            'label'    => 'ci:loc_settings',
            'group'    => 'channel_images',
            'settings' => self::settingsLocation(),
        );

        $sections['field_options_ci_actions'] = array(
            'label'    => 'ci:actions',
            'group'    => 'channel_images',
            'settings' => self::settingsActions(),
        );

        $sections['field_options_ci_advsettings'] = array(
            'label'    => 'ci:adv_settings',
            'group'    => 'channel_images',
            'settings' => self::settingsAdvanced(),
        );

        $sections['field_options_ci_columns'] = array(
            'label'    => 'ci:field_columns',
            'group'    => 'channel_images',
            'settings' => self::settingsColumns(),
        );

        return $sections;
    }

    public static function saveSettings($field_id, $data)
    {
        $post = ee('Request')->post('channel_images');
        $actions = ee('channel_images:Actions')->actions;

        $actionGroups = array();

        $finalSize = '';

        // -----------------------------------------
        // Loop over all action_groups (if any)
        // -----------------------------------------
        if (isset($post['action_groups'])) {
            foreach($post['action_groups'] as $order => $group) {
                // Format Group Name
                $group['group_name'] = str_replace('@', '123atsign123', $group['group_name']); // Preserve the @ sign
                $group['group_name'] = strtolower(url_title($group['group_name']));
                $group['group_name'] = str_replace('123atsign123', '@', $group['group_name']); // Put it back!

                $group['final_size'] = false;

                // WYSIWYG
                if (isset($group['wysiwyg']) == false OR $group['wysiwyg'] == false) {
                    $group['wysiwyg'] = 'no';
                }

                // Editable
                if (isset($group['editable']) == false OR $group['editable'] == false) {
                    $group['editable'] = 'no';
                }

                // -----------------------------------------
                // Process Actions
                // -----------------------------------------
                if (isset($group['actions']) == false OR empty($group['actions']) == true) {
                    unset($post['action_groups'][$order]);
                    continue;
                }

                foreach($group['actions'] as $action => &$action_settings) {
                    $finalSize = false;

                    if (isset($actions[$action]) == false) {
                        unset($group['actions'][$action]);
                        continue;
                    }

                    $action_settings = $actions[$action]->save_settings($action_settings);

                    if ($finalSize != false) {
                        $group['final_size'] = $finalSize;
                    }
                }

                $actionGroups[$order] = $group;
            }

            $post['action_groups'] = $actionGroups;

            // -----------------------------------------
            // Previews
            // -----------------------------------------
            if (isset($post['small_preview']) == true && $post['small_preview'] != false) {
                $post['small_preview'] = $post['action_groups'][$post['small_preview']]['group_name'];
            } else {
                $post['small_preview'] = $post['action_groups'][1]['group_name'];
            }

            // Big Preview
            if (isset($post['big_preview']) == true && $post['big_preview'] != false) {
                $post['big_preview'] = $post['action_groups'][$post['big_preview']]['group_name'];
            } else {
                $post['big_preview'] = $post['action_groups'][1]['group_name'];
            }
        } else {
            // Mark it as having no sizes!
            $post['no_sizes'] = 'yes';
            $post['action_groups'] = array();
        }

        // -----------------------------------------
        // Parse categories
        // -----------------------------------------
        if (isset($post['categories']) && $post['categories']) {
            $categories = array();

            foreach (explode(',', $post['categories']) as $cat) {
                $cat = trim ($cat);
                if ($cat != false) $categories[] = $cat;
            }

            $post['categories'] = $categories;
        } else {
            $post['categories'] = array();
        }

        // Make sure the import path has a slash at the end
        if (substr($post['import_path'], -1) != '/') $post['import_path'] .= '/';

        $settings = array();
        $settings['field_wide'] = true;
        $settings['channel_images'] = $post;

        return $settings;
    }

    protected static function settingsGeneral()
    {
        $fields = array();

        $routerUrl = ee('channel_images:Helper')->getRouterUrl();

        // View Mode
        $fields[] = array(
            'title' =>lang('ci:view_mode'),
            'fields'=>array(
                'channel_images[view_mode]'=>array(
                    'required' => true,
                    'type' => 'inline_radio',
                    'choices'=>array(
                        'tiles' => lang('ci:tiles_view'),
                        'table' => lang('ci:table_view'),
                    ),
                    'value'=> self::$settings['view_mode'],
                ),
            ),
        );

        // Keep Original Image
        $fields[] = array(
            'title' => lang('ci:keep_original'),
            'desc'  => lang('ci:keep_original_exp'),
            'fields'=> array(
                'channel_images[keep_original]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('yes'),
                        'no'  => lang('no')
                    ),
                    'value' => self::$settings['keep_original'],
                )
            ),
        );

        // Upload Location
        $fields[] = array(
            'title' => lang('ci:upload_location'),
            'fields' => array(
                'channel_images[upload_location]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'local'      => lang('ci:local'),
                        's3'         => lang('ci:s3'),
                        'cloudfiles' => lang('ci:cloudfiles'),
                    ),
                    'group_toggle' => array(
                        'local'      => 'ci_location-local',
                        's3'         => 'ci_location-s3',
                        'cloudfiles' => 'ci_location-cloudfiles',
                    ),
                    'value' => self::$settings['upload_location'],
                ),
            )
        );

        // Router URL
        $fields[] = array(
            'title' => lang('ci:act_url'),
            'desc'  => lang('ci:act_url:exp'),
            'fields' => array(
                'act_url' => array(
                    'type'    => 'html',
                    'content' => '<a href="' . $routerUrl . '" target="_blank">' . $routerUrl . '</a>',
                ),
            )
        );

        return $fields;
    }

    protected static function settingsLocation()
    {
        $fields = array();

        // -----------------------------------------
        // File Upload Destinations
        // -----------------------------------------
        $locations = array();
        $dbLocs = ee('Model')->get('UploadDestination')
        ->filter('site_id', ee()->config->item('site_id'))
        ->order('name', 'asc')->all();


        foreach ($dbLocs as $loc) {
            $locations[$loc->id] = $loc->name;
        }

        // -----------------------------------------
        // S3, Dropdows
        // -----------------------------------------
        $s3Regions = array();
        foreach (self::$module->get('s3_regions') as $key => $val) {
            $s3Regions[$key] = lang('ci:s3:region:'.$key);
        }

        $s3Acl = array();
        foreach (self::$module->get('s3_acl') as $key => $val) {
            $s3Acl[$key] = lang('ci:s3:acl:'.$key);
        }

        $s3Storage = array();
        foreach (self::$module->get('s3_storage') as $key => $val) {
            $s3Storage[$key] = lang('ci:s3:storage:'.$key);
        }

        $cloudFilesRegions = array();
        foreach (self::$module->get('cloudfiles_regions') as $key => $val) {
            $cloudFilesRegions[$key] = lang('ci:cloudfiles:region:'.$key);
        }

        // -----------------------------------------
        // Local
        // -----------------------------------------
        $fields[] = array(
            'title' => lang('ci:local'),
            'group'  => 'ci_location-local',
            'fields' => array(
                'channel_images[locations][local][location]' => array(
                    'type'    => 'select',
                    'choices' => $locations,
                    'value'   => self::$settings['locations']['local']['location'],
                ),
            )
        );

        // -----------------------------------------
        // S3
        // -----------------------------------------
        $fields[] = array(
            'title' => lang('ci:s3:key'),
            'desc' => lang('ci:s3:key_exp'),
            'group'  => 'ci_location-s3',
            'fields' => array(
                'channel_images[locations][s3][key]' => array(
                    'type' => 'text',
                    'value' => self::$settings['locations']['s3']['key'],
                ),
            )
        );

        $fields[] = array(
            'title' => lang('ci:s3:secret_key'),
            'desc' => lang('ci:s3:secret_key_exp'),
            'group'  => 'ci_location-s3',
            'fields' => array(
                'channel_images[locations][s3][secret_key]' => array(
                    'type' => 'text',
                    'value' => self::$settings['locations']['s3']['secret_key'],
                ),
            )
        );

        $fields[] = array(
            'title' => lang('ci:s3:bucket'),
            'desc' => lang('ci:s3:bucket_exp'),
            'group'  => 'ci_location-s3',
            'fields' => array(
                'channel_images[locations][s3][bucket]' => array(
                    'type' => 'text',
                    'value' => self::$settings['locations']['s3']['bucket'],
                ),
            )
        );

        // S3: Region
        $fields[] = array(
            'title' => lang('ci:s3:region'),
            'group'  => 'ci_location-s3',
            'fields' => array(
                'channel_images[locations][s3][region]' => array(
                    'type'    => 'select',
                    'choices' => $s3Regions,
                    'value'   => self::$settings['locations']['s3']['region'],
                ),
            )
        );

        // S3: ACL
        $fields[] = array(
            'title' => lang('ci:s3:acl'),
            'desc' => lang('ci:s3:acl_exp'),
            'group'  => 'ci_location-s3',
            'fields' => array(
                'channel_images[locations][s3][acl]' => array(
                    'type'    => 'select',
                    'choices' => $s3Acl,
                    'value'   => self::$settings['locations']['s3']['acl'],
                ),
            )
        );

        // S3: Storage
        $fields[] = array(
            'title' => lang('ci:s3:storage'),
            'group'  => 'ci_location-s3',
            'fields' => array(
                'channel_images[locations][s3][storage]' => array(
                    'type'    => 'select',
                    'choices' => $s3Storage,
                    'value'   => self::$settings['locations']['s3']['storage'],
                ),
            )
        );

        // S3: Directory
        $fields[] = array(
            'title'  => lang('ci:s3:directory'),
            'group'  => 'ci_location-s3',
            'fields' => array(
                'channel_images[locations][s3][directory]' => array(
                    'type'  => 'text',
                    'value' => self::$settings['locations']['s3']['directory'],
                ),
            )
        );

        // S3: CloudFront Domain
        $fields[] = array(
            'title'  => lang('ci:s3:cloudfrontd'),
            'group'  => 'ci_location-s3',
            'fields' => array(
                'channel_images[locations][s3][cloudfront_domain]' => array(
                    'type'  => 'text',
                    'value' => self::$settings['locations']['s3']['cloudfront_domain'],
                ),
            )
        );

        // -----------------------------------------
        // Rackspace
        // -----------------------------------------
        // CloudFiles: Username
        $fields[] = array(
            'title' => lang('ci:cloudfiles:username'),
            'group'  => 'ci_location-cloudfiles',
            'fields' => array(
                'channel_images[locations][cloudfiles][username]' => array(
                    'type'  => 'text',
                    'value' => self::$settings['locations']['cloudfiles']['username'],
                ),
            ),
        );

        // CloudFiles: API KEY
        $fields[] = array(
            'title' => lang('ci:cloudfiles:api'),
            'group'  => 'ci_location-cloudfiles',
            'fields' => array(
                'channel_images[locations][cloudfiles][api]' => array(
                    'type'  => 'text',
                    'value' => self::$settings['locations']['cloudfiles']['api'],
                ),
            ),
        );

        // CloudFiles: Container
        $fields[] = array(
            'title' => lang('ci:cloudfiles:container'),
            'group'  => 'ci_location-cloudfiles',
            'fields' => array(
                'channel_images[locations][cloudfiles][container]' => array(
                    'type'  => 'text',
                    'value' => self::$settings['locations']['cloudfiles']['container'],
                ),
            ),
        );

        // CloudFiles: Region
        $fields[] = array(
            'title'  => lang('ci:cloudfiles:region'),
            'group'  => 'ci_location-cloudfiles',
            'fields' => array(
                'channel_images[locations][cloudfiles][region]' => array(
                    'type'    => 'select',
                    'choices' => $cloudFilesRegions,
                    'value'   => self::$settings['locations']['cloudfiles']['region'],
                ),
            ),
        );

        // -----------------------------------------
        // Test Location
        // -----------------------------------------
        $fields[] = array(
            'wide'   => true,
            'fields' => array(
                'test' => array(
                    'type'    => 'html',
                    'content' => '
                        <a ihref="#" class="ci-test_location">' . lang('ci:test_location') . '</a>
                        <div class="modal-wrap modal-ci_test_location hidden">
                            <div class="modal" style="padding-top:10px">
                                <div class="col-group">
                                    <div class="col w-16">
                                        <div class="box">
                                            <h1>' . lang('ci:test_location') . ' <a class="m-close" href="#"></a></h1>
                                            <div class="ajax_results" style="min-height:260px; padding:20px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ',
                ),
            ),
        );

        return $fields;
    }

    public static function settingsActions()
    {
        $vdata = array();
        $vdata['themeUrl'] = ee('channel_images:Helper')->getThemeUrl();
        $vdata['actions'] = ee('channel_images:Actions')->actions;

        // Any Action Groups?
        if (empty(self::$settings['action_groups']) && self::$settings['no_sizes'] == 'no') {
            self::$settings['action_groups'] = self::$module->get('action_groups');
        }

        foreach(self::$settings['action_groups'] as &$group) {
            $actions = $group['actions'];
            $group['actions'] = array();

            foreach($actions AS $action_name => &$settings) {
                // Sometimes people don't have Imagick anymore!
                if (isset($vdata['actions'][$action_name]) == false)  continue;

                $new = array();
                $new['action'] = $action_name;
                $new['action_name'] = $vdata['actions'][$action_name]->info['title'];
                $new['action_settings'] = $vdata['actions'][$action_name]->display_settings($settings);
                $group['actions'][] = $new;
            }

            if (isset($group['wysiwyg']) == true && $group['wysiwyg'] == 'no') unset($group['wysiwyg']);
            if (isset($group['editable']) == true && $group['editable'] == 'no') unset($group['editable']);
        }

        // Small Preview
        if (self::$settings['small_preview'] == false) {
            $temp = reset(self::$settings['action_groups']);
            self::$settings['small_preview'] = $temp['group_name'];
        }

        // Big Preview
        if (self::$settings['big_preview'] == false) {
            $temp = reset(self::$settings['action_groups']);
            self::$settings['big_preview'] = $temp['group_name'];
        }

        $vdata['action_groups'] = json_encode(self::$settings['action_groups']);

        ee()->cp->add_to_foot('
        <script type="text/javascript">
            ChannelImages.FTS = '.$vdata['action_groups'].';
            ChannelImages.previews = {small:"'.self::$settings['small_preview'].'", big:"'.self::$settings['big_preview'].'"};

            $(function() {
                ChannelImages.init();
            });
        </script>
        ');

        $fields = array();
        $fields[] = array(
            'wide' => true,
            'fields' => array(
                'channel_images[categories]' => array(
                    'type'  => 'html',
                    'content'=> ee('View')->make('channel_images:fts/actions')->render($vdata),
                ),
            ),
        );

        return $fields;
    }

    public static function settingsAdvanced()
    {
        $fields = array();

        $fields[] = array(
            'title' => lang('ci:categories'),
            'desc' => lang('ci:categories_explain'),
            'fields' => array(
                'channel_images[categories]' => array(
                    'type' => 'text',
                    'value' => implode(',', self::$settings['categories']),
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:default_category'),
            'fields' => array(
                'channel_images[default_category]' => array(
                    'type' => 'text',
                    'value' => self::$settings['default_category'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:show_stored_images'),
            'fields' => array(
                'channel_images[show_stored_images]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['show_stored_images'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:limit_stored_images_author'),
            'desc' => lang('ci:limit_stored_images_author_exp'),
            'fields' => array(
                'channel_images[limit_stored_images_author]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['stored_images_by_author'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:stored_images_search_type'),
            'fields' => array(
                'channel_images[stored_images_search_type]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'entry' =>lang('ci:entry_based'),
                        'image' => lang('ci:image_based')
                    ),
                    'value' => self::$settings['stored_images_search_type'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:show_import_files'),
            'desc' => lang('ci:show_import_files_exp'),
            'fields' => array(
                'channel_images[show_import_files]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['show_import_files'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:import_path'),
            'desc' => lang('ci:import_path_exp'),
            'fields' => array(
                'channel_images[import_path]' => array(
                    'type' => 'text',
                    'value' => self::$settings['import_path'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:show_image_edit'),
            'fields' => array(
                'channel_images[show_image_edit]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['show_image_edit'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:show_image_replace'),
            'fields' => array(
                'channel_images[show_image_replace]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['show_image_replace'],
                )
            ),
        );

        $fields[] = array(
            'title' => lang('ci:allow_per_image_action'),
            'fields' => array(
                'channel_images[allow_per_image_action]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['allow_per_image_action'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:image_limit'),
            'desc' => lang('ci:image_limit_exp'),
            'fields' => array(
                'channel_images[image_limit]' => array(
                    'type' => 'short-text',
                    'label' => 'images',
                    'value' => self::$settings['image_limit'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:hybrid_upload'),
            'desc' => lang('ci:hybrid_upload_exp'),
            'fields' => array(
                'channel_images[hybrid_upload]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => (self::$settings['hybrid_upload'] == 'yes' ? 'yes' : 'no'),
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:progressive_jpeg'),
            'desc' => lang('ci:progressive_jpeg_exp'),
            'fields' => array(
                'channel_images[progressive_jpeg]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['progressive_jpeg'],
                )
            ),
        );

        $fields[] = array(
            'title' => lang('ci:wysiwyg_original'),
            'fields' => array(
                'channel_images[wysiwyg_original]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['wysiwyg_original'],
                )
            ),
        );

        $fields[] = array(
            'title' => lang('ci:save_data_in_field'),
            'desc' => lang('ci:save_data_in_field_exp'),
            'fields' => array(
                'channel_images[save_data_in_field]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['save_data_in_field'],
                )
            ),
        );

        $fields[] = array(
            'title' => lang('ci:disable_cover'),
            'fields' => array(
                'channel_images[disable_cover]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['disable_cover'],
                )
            ),
        );

        $fields[] = array(
            'title' => lang('ci:convert_jpg'),
            'fields' => array(
                'channel_images[convert_jpg]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['convert_jpg'],
                )
            ),
        );

        $fields[] = array(
            'title' => lang('ci:cover_first'),
            'fields' => array(
                'channel_images[cover_first]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['cover_first'],
                )
            ),
        );

        $fields[] = array(
            'title' => lang('ci:wysiwyg_output'),
            'fields' => array(
                'channel_images[wysiwyg_output]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'image_url' => lang('ci:image_url'),
                        'static_image' => lang('ci:static_image:var')
                    ),
                    'value' => self::$settings['wysiwyg_output'],
                )
            ),
        );

        $fields[] = array(
            'title' => 'Direct URL to Image Previews',
            'desc' => 'If your images are above the webroot, this setting should be set to "no"',
            'fields' => array(
                'channel_images[direct_url]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['direct_url'],
                ),
            ),
        );

        $fields[] = array(
            'title' => 'File Size Limit (in KB)',
            'desc' => 'Leave empty to ignore/disable filesize. (1024 = 1 MB)',
            'fields' => array(
                'channel_images[max_filesize]' => array(
                    'type' => 'short-text',
                    'label' => 'KB',
                    'value' => self::$settings['max_filesize'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:parse_iptc'),
            'fields' => array(
                'channel_images[parse_iptc]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['parse_iptc'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:parse_exif'),
            'fields' => array(
                'channel_images[parse_exif]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['parse_exif'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:parse_xmp'),
            'fields' => array(
                'channel_images[parse_xmp]' => array(
                    'type' => 'inline_radio',
                    'choices' => array(
                        'yes' => lang('ci:yes'),
                        'no' => lang('ci:no')
                    ),
                    'value' => self::$settings['parse_xmp'],
                ),
            ),
        );

        return $fields;
    }

    public static function settingsColumns()
    {
        $fields = array();

        // Make sure all fields have their lang values
        foreach (self::$settings['columns'] as $key => &$val) {
            if (substr($val, 0, 3) == 'ci:') {
                $val = lang($val);
            }
        }

        $fields[] = array(
            'wide' => true,
            'fields' => array(
                'exp' => array(
                    'type' => 'html',
                    'content' => '<small style="display:block; margin:0 0 10px">' . lang('ci:field_columns_exp') . '</small>',
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:row_num'),
            'fields' => array(
                'channel_images[columns][row_num]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns']['row_num'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:id'),
            'fields' => array(
                'channel_images[columns][id]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns']['id'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:image'),
            'fields' => array(
                'channel_images[columns][image]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns']['image'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:filename'),
            'fields' => array(
                'channel_images[columns][filename]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns']['filename'],
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:title'),
            'fields' => array(
                'channel_images[columns][title]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns']['title'],
                    'attrs' => ' style="width:48%" ',
                ),
                'channel_images[columns_default][title]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns_default']['title'],
                    'attrs' => ' style="width:48%; float:right" ',
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:url_title'),
            'fields' => array(
                'channel_images[columns][url_title]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns']['url_title'],
                    'attrs' => ' style="width:48%" ',
                ),
                'channel_images[columns_default][url_title]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns_default']['url_title'],
                    'attrs' => ' style="width:48%; float:right" ',
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:desc'),
            'fields' => array(
                'channel_images[columns][desc]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns']['desc'],
                    'attrs' => ' style="width:48%" ',
                ),
                'channel_images[columns_default][desc]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns_default']['desc'],
                    'attrs' => ' style="width:48%; float:right" ',
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:category'),
            'fields' => array(
                'channel_images[columns][category]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns']['category'],
                    'attrs' => ' style="width:48%" ',
                ),
                'channel_images[columns_default][category]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns_default']['category'],
                    'attrs' => ' style="width:48%; float:right" ',
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:cifield_1'),
            'fields' => array(
                'channel_images[columns][cifield_1]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns']['cifield_1'],
                    'attrs' => ' style="width:48%" ',
                ),
                'channel_images[columns_default][cifield_1]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns_default']['cifield_1'],
                    'attrs' => ' style="width:48%; float:right" ',
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:cifield_2'),
            'fields' => array(
                'channel_images[columns][cifield_2]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns']['cifield_2'],
                    'attrs' => ' style="width:48%" ',
                ),
                'channel_images[columns_default][cifield_2]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns_default']['cifield_2'],
                    'attrs' => ' style="width:48%; float:right" ',
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:cifield_3'),
            'fields' => array(
                'channel_images[columns][cifield_3]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns']['cifield_3'],
                    'attrs' => ' style="width:48%" ',
                ),
                'channel_images[columns_default][cifield_3]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns_default']['cifield_3'],
                    'attrs' => ' style="width:48%; float:right" ',
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:cifield_4'),
            'fields' => array(
                'channel_images[columns][cifield_4]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns']['cifield_4'],
                    'attrs' => ' style="width:48%" ',
                ),
                'channel_images[columns_default][cifield_4]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns_default']['cifield_4'],
                    'attrs' => ' style="width:48%; float:right" ',
                ),
            ),
        );

        $fields[] = array(
            'title' => lang('ci:cifield_5'),
            'fields' => array(
                'channel_images[columns][cifield_5]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns']['cifield_5'],
                    'attrs' => ' style="width:48%" ',
                ),
                'channel_images[columns_default][cifield_5]' => array(
                    'type' => 'text',
                    'value' => self::$settings['columns_default']['cifield_5'],
                    'attrs' => ' style="width:48%; float:right" ',
                ),
            ),
        );

        return $fields;
    }
}

/* End of file FieldtypeSettingsHelper.php */
/* Location: ./system/user/addons/channel_images/Service/FieldtypeSettingsHelper.php */