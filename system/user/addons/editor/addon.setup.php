<?php

if (!defined('EDITOR_NAME')){
    define('EDITOR_NAME',         'Editor');
    define('EDITOR_CLASS_NAME',   'editor');
    define('EDITOR_VERSION',      '4.0.3');
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
    'name'           => EDITOR_NAME,
    'description'    => 'Adds redactor.js support to ExpressionEngine',
    'version'        => EDITOR_VERSION,
    'namespace'      => 'DevDemon\Editor',
    'settings_exist' => true,
    'fieldtypes' => array(
        'editor' => array(
            'name' => 'Editor',
            'compatibility' => 'text'
        ),
    ),
    'models' => array(
        'Config'   => 'Model\Config',
    ),
    'services'       => array(),
    'services.singletons' => array(
        'Settings' => function($addon) {
            return new DevDemon\Editor\Service\Settings($addon);
        },
        'Helper' => function($addon) {
            return new DevDemon\Editor\Service\Helper($addon);
        },
        'Configuration' => function($addon) {
            return new DevDemon\Editor\Service\Configuration($addon);
        },
    ),

    //----------------------------------------
    // Default Module Settings
    //----------------------------------------
    'settings_module' => array(),

    //----------------------------------------
    // Default Fieldtype Settings
    //----------------------------------------
    'settings_fieldtype' => array(
        'config' => null,
    ),

    //----------------------------------------
    // Redactor
    //----------------------------------------
    'redactor_default' => array(
        'config'         => array(),
        'buttons'        => array('format','bold', 'italic', 'lists', 'link'),
        'plugins'        => array(),
        'upload_service' => 'local',
        'files_upload_location'  => 0,
        'images_upload_location' => 0,
        'files_browse'  => 'no',
        'images_browse' => 'no',
        's3' => array(
            'files' => array('bucket' => '', 'region' => 'us-east-1'),
            'images' => array('bucket' => '', 'region' => 'us-east-1'),
            'aws_access_key' => '',
            'aws_secret_key' => '',
        ),
    ),
    'redactor_buttons' => array(
        'format'         => array('label' => 'Format', 'plugin' => null),
        'bold'           => array('label' => 'B',      'plugin' => null),
        'italic'         => array('label' => 'I',      'plugin' => null),
        'underline'      => array('label' => 'U',      'plugin' => null),
        'deleted'        => array('label' => 'S',      'plugin' => null),
        'lists'          => array('label' => 'Lists',  'plugin' => null),
        'image'          => array('label' => 'Image',  'plugin' => null),
        'file'           => array('label' => 'File',   'plugin' => null),
        'link'           => array('label' => 'Link',   'plugin' => null),
        'horizontalrule' => array('label' => 'Line',   'plugin' => null),
    ),

    's3_regions' => array(
        'us-east-1'      => 's3.amazonaws.com',
        'us-west-2'      => 's3-us-west-2.amazonaws.com',
        'us-west-1'      => 's3-us-west-1.amazonaws.com',
        'eu-west-1'      => 's3-eu-west-1.amazonaws.com',
        'eu-central-1'   => 's3.eu-central-1.amazonaws.com',
        'ap-southeast-1' => 's3-ap-southeast-1.amazonaws.com',
        'ap-southeast-2' => 's3-ap-southeast-2.amazonaws.com',
        'ap-northeast-1' => 's3-ap-northeast-1.amazonaws.com',
        'sa-east-1'      => 's3-sa-east-1.amazonaws.com',
    ),
);

/* End of file addon.setup.php */
/* Location: ./system/user/addons/editor/addon.setup.php */