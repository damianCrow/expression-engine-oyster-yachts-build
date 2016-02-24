<?php

namespace DevDemon\Editor\Service;

class Helper
{
    protected $package_name = EDITOR_CLASS_NAME;
    protected $package_version = EDITOR_VERSION;
    protected $actionUrlCache = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->site_id = ee()->config->item('site_id');
    }

    public function getRouterUrl($type='url', $method='actionGeneralRouter')
    {
        // -----------------------------------------
        // Grab action_id
        // -----------------------------------------
        if (isset($this->actionUrlCache[$method]['action_id']) === false) {
            $action = ee('Model')->get('Action')
            ->filter('class', ucfirst($this->package_name))
            ->filter('method', $method)
            ->fields('action_id')
            ->first();

            if (!$action) {
                return false;
            }

            $action_id = $action->action_id;
        } else {
            $action_id = $this->actionUrlCache[$method]['action_id'];
        }

        // -----------------------------------------
        // Return FULL action URL
        // -----------------------------------------
        if ($type == 'url') {
            // Grab Site URL
            $url = ee()->functions->fetch_site_index(0, 0);

            if (defined('MASKED_CP') == false OR MASKED_CP == false) {
                // Replace site url domain with current working domain
                $server_host = (isset($_SERVER['HTTP_HOST']) == true && $_SERVER['HTTP_HOST'] != false) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
                $url = preg_replace('#http\://(([\w][\w\-\.]*)\.)?([\w][\w\-]+)(\.([\w][\w\.]*))?\/#', "http://{$server_host}/", $url);
            }

            // Create new URL
            $ajax_url = $url.QUERY_MARKER.'ACT=' . $action_id;

            // Config Override for action URLs?
            $config = ee()->config->item($this->package_name);
            $over = isset($config['action_url']) ? $config['action_url'] : array();

            if (is_array($over) === true && isset($over[$method]) === true) {
                $url = $over[$method];
            }

            // Protocol Relative URL
            $ajax_url = str_replace(array('https://', 'http://'), '//', $ajax_url);

            return $ajax_url;
        }

        return $action_id;
    }

    public function getThemeUrl($root=false)
    {
        if (defined('URL_THIRD_THEMES') === true) {
            $theme_url = URL_THIRD_THEMES;
        } else {
            $theme_url = ee()->config->slash_item('theme_folder_url').'third_party/';
        }

        $theme_url = str_replace(array('http://','https://'), '//', $theme_url);

        if ($root) return $theme_url;

        $theme_url .= $this->package_name . '/';

        return $theme_url;
    }

    public function encryptString($string)
    {
        $key = ee()->config->item('encryption_key') ?: substr(sha1(base64_encode('K1A7qN792bJ13St5lOIq6swFvlbBC4504J8rdO901Yz5t9s2jTmKU7TK1yhDhgwC')), 0, 56);
        ee()->load->library('encrypt');
        $string = ee()->encrypt->encode($string, $key);
        return $string;
    }

    public function decryptString($string)
    {
        $key = ee()->config->item('encryption_key') ?: substr(sha1(base64_encode('K1A7qN792bJ13St5lOIq6swFvlbBC4504J8rdO901Yz5t9s2jTmKU7TK1yhDhgwC')), 0, 56);
        ee()->load->library('encrypt');
        $string = ee()->encrypt->decode($string, $key);
        return $string;
    }

    public function mcpAssets($type, $name=null, $dir=null, $addon=false)
    {
        $ajaxUrl  = $this->getRouterUrl('url');
        $themeUrl = $this->getThemeUrl();
        $addon = $this->package_name ?: 'devdemon';

        // -----------------------------------------
        // CSS
        // -----------------------------------------
        if ($type == 'css' && !ee()->session->cache($addon, $name)) {
            $url = $dir ? "{$themeUrl}{$dir}/{$name}" : "{$themeUrl}css/{$name}";
            ee()->cp->add_to_head('<link rel="stylesheet" href="' . $url . '?v='.$this->package_version.'" type="text/css" media="print, projection, screen" />');
            ee()->session->set_cache($addon, $name, 'yes');
        }

        // -----------------------------------------
        // Javascript
        // -----------------------------------------
        if ($type == 'js' && !ee()->session->cache($addon, $name)) {
            $url = $dir ? "{$themeUrl}{$dir}/{$name}" : "{$themeUrl}js/{$name}";
            ee()->cp->add_to_foot('<script src="' . $url . '?v='.$this->package_version.'" type="text/javascript"></script>');
            ee()->session->set_cache($addon, $name, 'yes');
        }

        // -----------------------------------------
        // Global Inline Javascript
        // -----------------------------------------
        if ($type == 'gjs') {
            if (!ee()->session->cache($addon, 'gjs.' . $this->package_name)) {
                $js = " var Editor = Editor ? Editor : {};
                        Editor.AJAX_URL = '{$ajaxUrl}&site_id={$this->site_id}';
                        Editor.THEME_URL = '{$themeUrl}';
                        Editor.site_id = '{$this->site_id}';
                ";

                ee()->cp->add_to_foot('<script type="text/javascript">' . $js . '</script>');
                ee()->session->set_cache($addon, 'gjs.' . $this->package_name, 'yes');
            }
        }
    }
}

/* End of file Helper.php */
/* Location: ./system/user/addons/channel_images/Service/Helper.php */