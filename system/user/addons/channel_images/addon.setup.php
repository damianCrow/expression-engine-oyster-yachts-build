<?php

if (!defined('CHANNEL_IMAGES_NAME')){
    define('CHANNEL_IMAGES_NAME',         'Channel Images');
    define('CHANNEL_IMAGES_CLASS_NAME',   'channel_images');
    define('CHANNEL_IMAGES_VERSION',      '6.0.1');
}

if ( ! function_exists('dd')) {
    function dd()
    {
        array_map(function($x) { var_dump($x); }, func_get_args()); die;
    }
}

return array(
    'author'         => 'DevDemon',
    'author_url'     => 'https://devdemon.com/',
    'docs_url'       => 'http://www.devdemon.com/docs/',
    'name'           => CHANNEL_IMAGES_NAME,
    'description'    => 'Enables images to be associated with an entry',
    'version'        => CHANNEL_IMAGES_VERSION,
    'namespace'      => 'DevDemon\ChannelImages',
    'settings_exist' => true,
    'fieldtypes' => array(
        'channel_images' => array(
            'name' => 'Channel Images',
            'compatibility' => '',
        ),
    ),
    'models' => array(
        'Image' => 'Model\Image',
    ),
    'services'       => array(),
    'services.singletons' => array(
        'Settings' => function($addon) {
            return new DevDemon\ChannelImages\Service\Settings($addon);
        },
        'Helper' => function($addon) {
            return new DevDemon\ChannelImages\Service\Helper($addon);
        },
        'Actions' => function($addon) {
            return new DevDemon\ChannelImages\Service\Actions($addon);
        },
        'Images' => function($addon) {
            return new DevDemon\ChannelImages\Service\Images($addon);
        }
    ),

    //----------------------------------------
    // Default Module Settings
    //----------------------------------------
    'settings_module' => array(
        'infinite_memory'                => 'yes',
        'ascii_filename'                 => 'yes',
        'utf8_encode_fields_for_json'    => null,
        'utf8_multibyte_fields_for_json' => null,
        'xss_field_strings'              => null,
        'cache_path'                     => PATH_CACHE,
        'image_preview_size'             => '50px',
        'encode_filename_url'            => 'no',
        'host_os'                        => null, // windows, linux
    ),


    //----------------------------------------
    // Default Fieldtype Settings
    //----------------------------------------
    'settings_fieldtype' => array(
        'view_mode'                 => 'tiles',
        'direct_url'                => 'yes',
        'small_preview'             => '',
        'big_preview'               => '',
        'no_sizes'                  => 'no',
        'keep_original'             => 'yes',
        'upload_location'           => 'local',
        'categories'                => array(),
        'default_category'          => '',
        'show_stored_images'        => 'no',
        'stored_images_by_author'   => 'no',
        'stored_images_search_type' => 'entry',
        'show_import_files'         => 'no',
        'import_path'               => '',
        'jeditable_event'           => 'click',
        'image_limit'               => '',
        'hybrid_upload'             => 'yes',
        'progressive_jpeg'          => 'no',
        'wysiwyg_original'          => 'yes',
        'save_data_in_field'        => 'no',
        'show_image_edit'           => 'yes',
        'show_image_replace'        => 'yes',
        'allow_per_image_action'    => 'no',
        'locked_url_fieldtype'      => 'no',
        'disable_cover'             => 'no',
        'convert_jpg'               => 'no',
        'parse_exif'                => 'no',
        'parse_xmp'                 => 'no',
        'parse_iptc'                => 'no',
        'cover_first'               => 'yes',
        'wysiwyg_output'            => 'image_url',
        'max_filesize'              => '',

        'locations' => array(
            'local' => array(
                'location' => 0,
            ),
            's3' => array(
                'key'               => '',
                'secret_key'        => '',
                'bucket'            => '',
                'region'            => 'us-east-1',
                'acl'               => 'public-read',
                'storage'           => 'standard',
                'directory'         => '',
                'cloudfront_domain' => '',
            ),
            'cloudfiles' => array(
                'username'  => '',
                'api'       => '',
                'container' => '',
                'region'    => 'us',
                'cdn_uri'   => '',
            ),
        ),

        'columns' => array(
            'row_num'   => lang('ci:row_num'),
            'id'        => lang('ci:id'),
            'image'     => lang('ci:image'),
            'filename'  => '',
            'title'     => lang('ci:title'),
            'url_title' => '',
            'desc'      => lang('ci:desc'),
            'category'  => '',
            'cifield_1' => '',
            'cifield_2' => '',
            'cifield_3' => '',
            'cifield_4' => '',
            'cifield_5' => '',
        ),

        'columns_default' => array(
            'title'     => '',
            'url_title' => '',
            'desc'      => '',
            'category'  => '',
            'cifield_1' => '',
            'cifield_2' => '',
            'cifield_3' => '',
            'cifield_4' => '',
            'cifield_5' => '',
        ),
    ),

    //----------------------------------------
    // Default Action Groups
    //----------------------------------------
    'action_groups' => array(
        array(
            'group_name' => 'small',
            'wysiwyg' => 'yes',
            'actions' => array(
                'resize_adaptive' => array(
                    'width' => 100,
                    'height' => 100,
                    'quality' => 80,
                    'upsizing' => 'no'
                ),
            ),
        ),
        array(
            'group_name' => 'medium',
            'wysiwyg' => 'yes',
            'actions' => array(
                'resize_adaptive' => array(
                    'width' => 450,
                    'height' => 300,
                    'quality' => 75,
                    'upsizing' => 'no'
                ),
            ),
        ),
        array(
            'group_name' => 'large',
            'wysiwyg' => 'yes',
            'actions' => array(
                'resize_adaptive' => array(
                    'width' => 800,
                    'height' => 600,
                    'quality' => 75,
                    'upsizing' => 'no'
                ),
            ),
        ),
    ),

    //----------------------------------------
    // Location Specific Settings
    //----------------------------------------
    's3_regions' => array(
        'us-east-1'      => 'REGION_US_E1',
        'us-west-1'      => 'REGION_US_W1',
        'us-west-2'      => 'REGION_US_W2',
        'eu'             => 'REGION_EU_W1',
        'eu-central-1'   => 'REGION_EU_C1',
        'ap-southeast-1' => 'REGION_APAC_SE1',
        'ap-southeast-2' => 'REGION_APAC_SE2',
        'ap-northeast-1' => 'REGION_APAC_NE1',
        'sa-east-1'      => 'REGION_SA_E1',
    ),
    's3_endpoints' => array(
        'us-east-1'      => 's3-us-east-1.amazonaws.com',
        'us-west-1'      => 's3-us-west-2.amazonaws.com',
        'us-west-2'      => 's3-us-west-1.amazonaws.com',
        'eu'             => 's3-eu-west-1.amazonaws.com',
        'eu-central-1'   => 's3-eu-central-1.amazonaws.com',
        'ap-southeast-1' => 's3-ap-southeast-1.amazonaws.com',
        'ap-southeast-2' => 's3-ap-southeast-2.amazonaws.com',
        'ap-northeast-1' => 's3-ap-northeast-1.amazonaws.com',
        'sa-east-1'      => 's3-sa-east-1.amazonaws.com',
    ),
    's3_acl' => array(
        'private'            => 'ACL_PRIVATE',
        'public-read'        => 'ACL_PUBLIC',
        'authenticated-read' => 'ACL_AUTH_READ',
    ),
    's3_storage' => array(
        'standard' => 'STORAGE_STANDARD',
        'reduced'  => 'STORAGE_REDUCED',
    ),

    'cloudfiles_regions' => array(
        'us'  => 'US_AUTHURL',
        'uk'  => 'UK_AUTHURL',
    ),
);

/* End of file addon.setup.php */
/* Location: ./system/user/addons/channel_images/addon.setup.php */