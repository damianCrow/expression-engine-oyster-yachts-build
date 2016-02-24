<?php if (!defined('BASEPATH')) die('No direct script access allowed');

$lang = array(
'editor'    =>  'Editor',
'editors'   =>  'Editors',
'editor_module_name'    =>   'Editor',
'editor_module_description'=>'Adds redactor.js support to ExpressionEngine',

'ed:label'      => 'Label',
'ed:desc'       => 'Description',
'ed:author'     => 'Author',
'ed:type'       => 'Type',
'ed:field_wide' => 'Full Width',

'ed:config'         => 'Configuration',
'ed:configs'        => 'Configurations',
'ed:create_config'  => 'Create Configuration',
'ed:edit_config'    => 'Edit Configuration',
'ed:updated_config' => 'Successfully updated/created the Configuration',
'ed:deleted_config' => 'Successfully deleted the Configuration(s)',
'ed:no_configs'     => 'No Editor configuration have been created..',

'ed:category_settings' => 'Category Settings',
'ed:toolbar_buttons'   =>  'Toolbar Buttons',
'ed:adv_settings'      =>  'Advanced Settings',
'ed:add_adv_setting'   =>  'Add a setting',
'ed:plugin'            => 'Plugin',
'ed:plugins'           => 'Plugins',

'ed:upload_settings'  =>  'Upload Settings',
'ed:upload_service'   =>  'Upload Service',
'ed:file_upload_loc'  =>  'File Upload Location',
'ed:image_upload_loc' =>  'Image Upload Location',
'ed:disabled'         =>  'DISABLED',
'ed:upload_url'       =>  'Upload URL',

'ed:s3:bucket_file'    =>  'S3 Bucket (Files)',
'ed:s3:bucket_image'   =>  'S3 Bucket (Images)',
'ed:s3:region_file'    =>  'S3 Region (Files)',
'ed:s3:region_image'   =>  'S3 Region (Images)',
'ed:s3:aws_key'        =>  'Amazon Access Key',
'ed:s3:aws_secret_key' =>  'Amazon Secret Key',
'ed:s3:us-east-1'      => 'US Standard (N. Virginia)',
'ed:s3:us-west-2'      => 'US West (Oregon)',
'ed:s3:us-west-1'      => 'US West (N. California)',
'ed:s3:eu-west-1'      => 'EU (Ireland)',
'ed:s3:eu-central-1'   => 'EU (Frankfurt)',
'ed:s3:ap-southeast-1' => 'Asia Pacific (Singapore)',
'ed:s3:ap-southeast-2' => 'Asia Pacific (Sydney)',
'ed:s3:ap-northeast-1' => 'Asia Pacific (Tokyo)',
'ed:s3:sa-east-1'      => 'South America (Sao Paulo)',

//----------------------------------------
// Redactor Advanced Settings
//----------------------------------------
'ed:redactor:air'                   => 'Use this setting to turn on Air toolbar. This toolbar only shows up when a user selects some text with a mouse in Redactor. This is an ultimate distraction-free inline editing mode.',
'ed:redactor:airWidth'              => 'This setting allows to set maximum width for Air toolbar. In pixels.',
'ed:redactor:buttonsHide'           => 'This setting allows to hide certain buttons on launch.',
'ed:redactor:buttonsHideOnMobile'   => 'This setting allows to hide certain buttons on mobile devices:',
'ed:redactor:focus'                 => "By default, Redactor doesn't receive focus on load, because there may be other input fields on a page. However, to set focus to Redactor, you can use this setting.",
'ed:redactor:focusEnd'              => 'This setting allows to set focus after the last character in Redactor',
'ed:redactor:formatting'            => 'This setting allows to adjust a list of formatting tags in the default formatting dropdown.',
'ed:redactor:minHeight'             => 'This setting allows to set minimum height for Redactor (in pixels).',
'ed:redactor:maxHeight'             => 'This setting allows to set maximum height for Redactor (in pixels).',
'ed:redactor:tabKey'                => 'This setting turns on Tab key handling. If tabKey is set to false, Tab key will set focus to the next input field on a page.',
'ed:redactor:tabAsSpaces'           => 'This setting allows to apply spaces instead of tabulation on Tab key. To turn this setting on, set number of spaces.',
'ed:redactor:preSpaces'             => "This setting allows to set the number of spaces that will be applied when a user presses Tab key inside of preformatted blocks. If set to 'disabled', Tab key will apply tabulation instead of spaces inside of preformatted blocks.",
'ed:redactor:direction'             => 'Redactor supports both right-to-left and left-to-right text directions. By default, Redactor is set to work with left-to-right.',
'ed:redactor:linkNofollow'          => "When set to 'yes', all links inside of Redactor will get a 'nofollow' attribute. This attribute restricts search engines indexing for these links.",
'ed:redactor:linkSize'              => "This setting allows to automatically truncate link text. Set to '50' characters by default.",
'ed:redactor:linkTooltip'           => 'Shows link tooltip with Edit and Unlink buttons on click.',
'ed:redactor:linkify'               => 'This setting turns off the default conversion of video and image URLs into embedded videos and images, and turns off auromatic conversion of text URLs into clickable links.',
'ed:redactor:placeholder'           => 'Placeholder text to show when the field has no text.',
'ed:redactor:shortcuts'             => "Enable or disable the default shortcuts. Keep in mind, that turning 'shortcuts' off will also disable Tab key, however, 'tabAsSpaces' will still function.",
'ed:redactor:script'                => 'You can restric use of script tag in your HTML. Redactor will automatically and always strip this tag form the code.',
'ed:redactor:structure'             => 'This settings introduces visual indicators for HTML tags h1-h6 and div, helping users understand the structure of the document.',
'ed:redactor:preClass'              => 'This setting allows to set a predefined class for the pre tag.',
'ed:redactor:animation'             => 'This setting makes it easy to turn dropdown animation on and off.',
'ed:redactor:toolbarFixed'          => 'This setting affixes external toolbar to a specific position on a page. When the is being scrolled down, fixed toolbar will remain in place and preserve its initial width.',
'ed:redactor:toolbarFixedTopOffset' => 'This setting allows to set how far from the top of the page the fixed toolbar will be placed. In pixels.',
'ed:redactor:toolbarFixedTarget'    => "This setting allows to set a specific element on a page in relation to which fixed toolbar will be displayed. By default, toolbarFixedTarget is set to 'document'.",
'ed:redactor:toolbarOverflow'       => "When set to 'yes', this setting will place all toolbar buttons on mobile devices in a single line regardless of how many buttons are there. If there's more buttons then can fit on a screen, horizontal scroll will appear.",
'ed:redactor:lang'                  => 'By default, Redactor is set to English language.',

'ed:redactor:exp:buttonsHide'         => 'Example: image,link',
'ed:redactor:exp:buttonsHideOnMobile' => 'Example: image,video',
'ed:redactor:exp:formatting'          => 'Example: p, blockquote, h2',
'ed:redactor:exp:tabAsSpaces'         => '0 = disabled',
'ed:redactor:exp:preSpaces'           => '0 = disabled',

// END
''=>''
);

/* End of file editor_lang.php */
/* Location: .system/user/addons/editor/editor/editor_lang.php */