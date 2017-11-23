<?php

namespace DevDemon\Editor\Service;

class Configuration
{
    public $buttons;

    /**
     * Constructor
     */
    public function __construct($addon)
    {
        // Current Site ID
        $this->site_id = ee()->config->item('site_id');

        // Current Module Settings
        $this->buttons = $this->getButtons($this->site_id);
    }

    public function getButtons()
    {
        if ($this->buttons) {
            return $this->buttons;
        }

        $buttons = ee('App')->get('editor')->get('redactor_buttons');

        return $buttons;
    }

    public function getConfigJson($settings)
    {
        // TODO: Recode this
        $json = array();

        if (isset($settings['config']) && $settings['config'] > 0) {
            $config = ee('Model')->get('editor:Config', $settings['config'])->first();

            if (!$config) {
                return $json;
            }

            $config->settings = array_merge(ee('App')->get('editor')->get('redactor_default'), $config->settings);

            if (isset($config->settings['config']) && !empty($config->settings['config'])) {
                $json = $config->settings['config'];
            }

            $json['buttons'] = $config->settings['buttons'];
            $json['plugins'] = $config->settings['plugins'];
        } else {
            return $json;
        }

        foreach ($this->getDefaultAdvancedSettings() as $key => $adv) {
            if (isset($json[$key]) === false) {
                unset($json[$key]);
                continue;
            }

            if ($adv['type'] == 'text-array') {
                $json[$key] = array_map('trim', explode(',', $json[$key]));
            }

            if ($adv['type'] == 'bool') {
                $json[$key] = ($json[$key] == 'yes') ? true : false;
            }

            if ($adv['type'] == 'number-bool') {
                $json[$key] = ($json[$key] > 0) ? $json[$key] : false;
            }
        }

        if ($config->settings['upload_service'] == 'local') {
            if ($config->settings['files_upload_location'] > 0) {
                $uploadUrl  = ee('editor:Helper')->getRouterUrl('url', 'actionFileUpload');
                $uploadUrl .= '&action=file&upload_location=' . $config->settings['files_upload_location'];
                $json['fileUpload'] = $uploadUrl;

                if ($config->settings['files_browse'] == 'yes') {
                    $browse  = ee('editor:Helper')->getRouterUrl('url', 'actionGeneralRouter');
                    $browse .= '&method=browseFiles&upload_location=' . $config->settings['files_upload_location'];

                    $json['fileManagerJson'] = $browse;
                    $json['plugins'][] = 'filemanager';
                }
            }

            if ($config->settings['images_upload_location'] > 0) {
                $uploadUrl  = ee('editor:Helper')->getRouterUrl('url', 'actionFileUpload');
                $uploadUrl .= '&action=image&upload_location=' . $config->settings['images_upload_location'];
                $json['imageUpload'] = $uploadUrl;

                if ($config->settings['images_browse'] == 'yes') {
                    $browse  = ee('editor:Helper')->getRouterUrl('url', 'actionGeneralRouter');
                    $browse .= '&method=browseImages&upload_location=' . $config->settings['images_upload_location'];

                    $json['imageManagerJson'] = $browse;
                    $json['plugins'][] = 'imagemanager';
                }
            }
        } elseif ($config->settings['upload_service'] == 's3') {
            $uploadUrl  = ee('editor:Helper')->getRouterUrl('url', 'actionFileUpload');
            $string = base64_encode(ee('editor:Helper')->encryptString(json_encode($config->settings['s3'])));
            $json['s3'] = "{$uploadUrl}&action=s3_info&s3={$string}";
            $json['fileUpload'] = true;
            $json['imageUpload'] = true;
        }

        return $json;
    }

    public function getDefaultAdvancedSettings()
    {
        $settings = array();
        $settings['air'] = array('type' => 'bool', 'value' => 'no');
        $settings['airWidth'] = array('type' => 'number', 'value' => '');
        $settings['buttonsHide'] = array('type' => 'text-array', 'value' => '');
        $settings['buttonsHideOnMobile'] = array('type' => 'text-array', 'value' => '');
        $settings['focus'] = array('type' => 'bool', 'value' => 'no');
        $settings['focusEnd'] = array('type' => 'bool', 'value' => 'no');
        $settings['formatting'] = array('type' => 'text-array', 'value' => 'p,blockquote,pre,h1,h2,h3,h4,h5,h6');
        $settings['minHeight'] = array('type' => 'number', 'value' => '300px');
        $settings['maxHeight'] = array('type' => 'number', 'value' => '800px');
        $settings['direction'] = array('type' => 'radio', 'value' => 'ltr', 'options' => array('ltr' => 'left-to-right', 'rtl' => 'right-to-left'));
        $settings['tabKey'] = array('type' => 'bool', 'value' => 'yes');
        $settings['tabAsSpaces'] = array('type' => 'number-bool', 'value' => '0');
        $settings['preSpaces'] = array('type' => 'number-bool', 'value' => '4');
        $settings['linkNofollow'] = array('type' => 'bool', 'value' => 'no');
        $settings['linkSize'] = array('type' => 'number', 'value' => '50');
        $settings['linkTooltip'] = array('type' => 'bool', 'value' => 'yes');
        $settings['linkify'] = array('type' => 'bool', 'value' => 'yes');
        $settings['placeholder'] = array('type' => 'text', 'value' => '');
        $settings['shortcuts'] = array('type' => 'bool', 'value' => 'yes');
        $settings['script'] = array('type' => 'bool', 'value' => 'yes');
        $settings['structure'] = array('type' => 'bool', 'value' => 'no');
        $settings['preClass'] = array('type' => 'text', 'value' => '');
        $settings['animation'] = array('type' => 'bool', 'value' => 'no');
        $settings['toolbarFixed'] = array('type' => 'bool', 'value' => 'no');
        $settings['toolbarFixedTopOffset'] = array('type' => 'number', 'value' => '0');
        $settings['toolbarFixedTarget'] = array('type' => 'text', 'value' => '');
        $settings['toolbarOverflow'] = array('type' => 'bool', 'value' => 'no');
        $settings['lang'] = array('type' => 'select', 'value' => 'en', 'options' => array(
            'ar'    => 'Arabic',
            'de'    => 'German',
            'en'    => 'English',
            'es'    => 'Spanish',
            'fi'    => 'Finnish',
            'fr'    => 'French',
            'ja'    => 'Japanese',
            'ko'    => 'Korean',
            'nl'    => 'Dutch',
            'pl'    => 'Polish',
            'pt_br' => 'Brazilian Portuguese',
            'ru'    => 'Russian',
            'sv'    => 'Swedish',
            'tr'    => 'Turkish',
            'zh_cn' => 'Chinese Simplified',
            'zh_tw' => 'Chinese Traditional'
        ));


        return $settings;
    }

    public function getPluginList()
    {
        $plugins = array();
        $plugins['source']        = array('author' => 'Redactor', 'label' => 'Source Code', 'desc' => "This plugin allows users to look through and edit text's HTML source code.");
        $plugins['table']         = array('author' => 'Redactor', 'label' => 'Table', 'desc' => "Insert and format tables with ease.");
        $plugins['video']         = array('author' => 'Redactor', 'label' => 'Video', 'desc' => "Enrich text with embedded video.");
        $plugins['fullscreen']    = array('author' => 'Redactor', 'label' => 'Fullscreen', 'desc' => "Expand Redactor to fill the whole screen. Also known as 'distraction free' mode.");
        //$plugins['counter']       = array('author' => 'Redactor', 'label' => 'Counter', 'desc' => "Add a character counter.");
        //$plugins['limiter']       = array('author' => 'Redactor', 'label' => 'Limiter', 'desc' => "Limit the number of characters a user can enter.");
        $plugins['properties']    = array('author' => 'Redactor', 'label' => 'Properties', 'desc' => "This plugin allows you to assign any id or class to any block tag (selected or containing cursor).");
        $plugins['textdirection'] = array('author' => 'Redactor', 'label' => 'Text Direction', 'desc' => "Easily change the direction of the text in a block element (paragraph, header, blockquote etc.).");
        $plugins['codemirror']    = array('author' => 'Redactor', 'label' => 'Codemirror', 'desc' => "This plugin enables source code highlighting, powered by CodeMirror.");
        //$plugins['filemanager']   = array('author' => 'Redactor', 'label' => 'File Manager', 'desc' => "Manage, upload, select files and place them anywhere in Redactor.");
        //$plugins['imagemanager']  = array('author' => 'Redactor', 'label' => 'Image Manager', 'desc' => "Upload or choose and insert images to tell a more visual story.");

        // Channel Images?
        if (ee('Addon')->get('channel_images') && ee('Addon')->get('channel_images')->isInstalled()) {
            $plugins['channel_images']    = array(
                'author'   => 'DevDemon',
                'label'    => 'Channel Images',
                'desc'     => 'Channel Images Integration',
                'included' => false,
                'url'      => ee('channel_images:Helper')->getThemeUrl() . 'js/addon_redactor_plugin.js',
            );
        }

        return $plugins;
    }

}

/* End of file Configuration.php */
/* Location: ./system/user/addons/editor/Service/Configuration.php */