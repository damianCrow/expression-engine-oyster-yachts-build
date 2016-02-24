<?php if (!defined('BASEPATH')) die('No direct script access allowed');

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
class Editor_ft extends EE_Fieldtype
{

    /**
     * Field info - Required
     *
     * @access public
     * @var array
     */
    public $info = array(
        'name'      => EDITOR_NAME,
        'version'   => EDITOR_VERSION,
    );

    public $has_array_data = true;

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
        //$this->moduleSettings = ee('editor:Settings')->settings;
    }

    // ********************************************************************************* //

    /**
     * Check if the fieldtype will accept a certain content type
     *
     * For backward compatiblity, all fieldtypes will initially only
     * support the channel content type. Override this method for more
     * control.
     *
     * @param string  The name of the content type
     * @return bool   Supports content type?
     */
    public function accepts_content_type($name)
    {
        return ($name == 'channel' || $name == 'grid');
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
        if (REQ == 'PAGE') {
            $data = form_prep($data, $this->field_name);
        }

        if (!isset($this->settings['editor'])) {

        }

        $settings = $this->settings['editor'];

        // Parse File & Pages URL variables
        $data = $this->parseVariables($data, true);

        // Add all the required CSS & JS
        $this->addCssJs();

        $config = ee('editor:Configuration')->getConfigJson($settings);

        // Other language?
        if (isset($config['lang']) && $config['lang'] != 'en') {
            ee('editor:Helper')->mcpAssets('js', $config['lang'].'.js', 'redactor/languages');
        }

        // Plugins
        if (isset($config['plugins'])) {
            foreach ($config['plugins'] as $plugin) {
                ee('editor:Helper')->mcpAssets('js', $plugin.'.js', 'redactor/plugins');
            }

            if (in_array('codemirror', $config['plugins'])) {
                if (!ee()->session->cache('devdemon', 'codemirror.js')) {
                    ee()->cp->add_to_head(ee()->view->head_link('css/codemirror.css'));
                    ee()->cp->add_to_head(ee()->view->head_link('css/codemirror-additions.css'));
                    ee()->cp->add_js_script(array(
                            'file'      => array(
                                'codemirror/codemirror',
                                'codemirror/closebrackets',
                                'codemirror/xml',
                                'codemirror/css',
                                'codemirror/javascript',
                                'codemirror/htmlmixed',
                            )
                        )
                    );
                    ee()->session->set_cache('devdemon', 'codemirror.js', 'yes');
                }
            }
        }

        // Generate a textarea ID
        $textarea_id = $this->field_name;
        $textarea_id = str_replace(array('[', ']'), array('_', ''), $textarea_id);
        $random_key = ee()->functions->random('md5');
        $field_name = ($this->content_type() == 'channel') ? 'field_id_' .$this->field_id : $this->field_name;
        $json = json_encode($config);

        if ($this->content_type() == 'grid') {
            ee()->cp->add_to_foot("<script type='text/javascript'>Editor.gridConfig['{$random_key}'] = {$json};</script>");
        } else {
            ee()->cp->add_to_foot("<script>
            $('#{$textarea_id}').redactor({$json});
            </script>");
        }

        return "<div class='editor'>
            <textarea id='{$textarea_id}' name='{$field_name}' class='redactor_editor' data-config_key='{$random_key}'>{$data}</textarea>
        </div>";
    }

    // ********************************************************************************* //

    public function display_var_field($data)
    {
        $this->settings['editor'] = $this->settings;
        return $this->display_field($data);
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
        if ($this->settings['field_required'] == 'y' && ! $data) {
            return lang('required');
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
        $data = trim($data);

        // Remove the first and the last empty <p>
        $data = preg_replace('/^<p><\/p>/s', '', $data);
        $data = preg_replace('/<p><\/p>$/s', '', $data);

        // Clear out if just whitespace
        if (! $data || preg_match('/^\s*(<\w+>\s*(&nbsp;)*\s*<\/\w+>|<br \/>)?\s*$/s', $data)) {
            return '';
        }

        // Entitize curly braces within codeblocks
        $data = preg_replace_callback('/<code>(.*?)<\/code>/s',
            create_function('$matches',
                'return str_replace(array("{","}"), array("&#123;","&#125;"), $matches[0]);'
            ),
            $data
        );

        // Remove empty at the end
        for ($i=0; $i < 20; $i++) {
            $data = preg_replace('/<p><br><\/p><p><br><\/p>$/s', '', $data);
        }

        // Just in case, lets remove the last one
        $data = preg_replace('/<p><br><\/p>$/s', '', $data);

        // Remove Firebug 1.5.2+ div
        $data = preg_replace('/<div firebugversion=(.|\t|\n|\s)*<\\/div>/', '', $data);

        // Cursor Resize!
        $data = preg_replace('/cursor\:.*nw-resize\;/', '', $data);

        $data = $this->parseVariables($data, false);

        // Does it contain emptyness?
        if ($data == '<p><br></p>') $data = '';

        if (ee()->extensions->active_hook('editor_before_save')) {
            $data = ee()->extensions->call('editor_before_save', $this, $data);
        }

        return $data;
    }

    // ********************************************************************************* //

    public function pre_process($data)
    {
        ee()->load->library('typography');

        $tmp_encode_email = ee()->typography->encode_email;
        ee()->typography->encode_email = false;

        $tmp_convert_curly = ee()->typography->convert_curly;
        ee()->typography->convert_curly = false;

        $data = ee()->typography->parse_type($data, array(
            'text_format'   => 'none',
            'html_format'   => 'all',
            'auto_links'    => (isset($this->row['channel_auto_link_urls']) ? $this->row['channel_auto_link_urls'] : 'n'),
            'allow_img_url' => (isset($this->row['channel_allow_img_urls']) ? $this->row['channel_allow_img_urls'] : 'y')
        ));

        ee()->typography->encode_email = $tmp_encode_email;
        ee()->typography->convert_curly = $tmp_convert_curly;

        // use normal quotes
        $data = str_replace('&quot;', '"', $data);

        $data = $this->parseVariables($data, true);

        return $data;
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
        if (ee()->extensions->active_hook('editor_before_replace')) {
            $data = ee()->extensions->call('editor_before_replace', $this, $data);
        }

        return $data;
    }

    // ********************************************************************************* //

    /**
     * Used for template display {field_name:text_only word_limit="50" suffix="..."}
     * Original author: Brian Litzinger (Wyvern)
     */
    public function replace_text($data, $params = '', $tagdata = '')
    {
        $data = $this->replace_tag($data, $params, $tagdata);

        // Strip everything but links. May need to revise the allowed list later.
        $data = trim(strip_tags($data, '<a>'));

        if (isset($params['word_limit']) AND is_numeric($params['word_limit'])) {
            // Get the words
            $words = explode(" ", str_replace("\n", '', $data));

            // limit it to specified number of words
            $data = implode(" ", array_splice($words, 0, $params['word_limit']));

            // See if last character is not punctuation or another special char and remove it
            // (note this is basic and might not work in multi-lingual sites)
            $data = ! preg_match("/^[a-z0-9\.\?\!]$/i", substr($data, -1)) ? substr($data, 0, -1) : $data;

            // Add whatever suffix the user wants...
            // Suffix was the first param, added append as an alias b/c it makes more sense, should have used it first
            if (isset($params['suffix'])) {
                $data .= $params['suffix'];
            } elseif (isset($params['append'])) {
                $data .= $params['append'];
            }
        }

        return $data;
    }

    // ********************************************************************************* //

    public function display_var_tag($data)
    {
        return $this->replace_tag($this->pre_process($data));
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
        //dd($this);
        ee()->lang->loadfile('editor');

        $configs = array();

        foreach (ee('Model')->get('editor:Config')->order('label', 'asc')->all() as $config) {
            $configs[$config->id] = $config->label;
        }

        $settings = (isset($data['editor'])) ? $data['editor'] : null;
        if (!$settings) $settings = ee('App')->get('editor')->get('settings_fieldtype');

        $fields = array(
            array(
                'title'  => lang('ed:config'),
                'fields' => array(
                    'editor[config]' => array(
                        'type'    => 'select',
                        'choices' => $configs,
                        'value'   => $settings['config'],
                    )
                )
            )
        );

        if ($this->content_type() == 'channel') {

            $data['field_wide'] = isset($data['field_wide']) ? $data['field_wide'] : false;

            $fields[] = array(
                'title'  => lang('ed:field_wide'),
                'fields' => array(
                    'field_wide' => array(
                        'type' => 'inline_radio',
                        'choices' => array(
                            'yes' => lang('yes'),
                            'no' => lang('no')
                        ),
                        'value' => $data['field_wide'] ? 'yes' : 'no',
                    )
                )
            );
        }

        if ($this->content_type() == 'grid') {
            return array('field_options' => $fields);
        }

        return array('field_options_editor' => array(
            'label'    => 'field_options',
            'group'    => 'editor',
            'settings' => $fields
        ));
    }

    // ********************************************************************************* //

    public function display_var_settings($settings=array())
    {
        return $this->display_settings(array('editor' => $settings));
    }

    // ********************************************************************************* //

    /**
     * Save the fieldtype settings.
     *
     * @param $data array Contains the submitted settings for this field.
     * @access public
     * @return array
     */
    public function save_settings($settings=array(), $ignorePost=false)
    {
        $post = ee('Request')->post('editor');

        $fieldWide = false;
        if (isset($settings['field_wide'])) {
            $fieldWide = ($settings['field_wide'] == 'yes') ? true : false;
        }

        if ($this->content_type() == 'grid') {
            $post = isset($settings['editor']) ? $settings['editor'] : array();
        }

        $arr = array();
        $arr['editor'] = $post;
        $arr['field_wide'] = $fieldWide;


        return $arr;
    }

    // ********************************************************************************* //

    /**
     * Save settings: Low Variables
     * @param  array  $settings
     * @return array
     */
    public function save_var_settings($settings=array())
    {
        return $this->save_settings(ee('Request')->post('editor'));
    }

    // ********************************************************************************* //

    private function addCssJs()
    {
        //----------------------------------------
        // CSS/JS
        //----------------------------------------
        ee('editor:Helper')->mcpAssets('gjs');
        ee('editor:Helper')->mcpAssets('css', 'redactor.css', 'redactor');
        ee('editor:Helper')->mcpAssets('css', 'addon_pbf.css', null, true);
        ee('editor:Helper')->mcpAssets('js', 'redactor.min.js', 'redactor');
        ee('editor:Helper')->mcpAssets('js', 'addon_pbf.js', null, true);
    }

    // ********************************************************************************* //

    private function parseVariables($data='', $var_to_val=true)
    {
        if ($var_to_val) {
            $data = $this->parseFileVariables($data);
            //$data = $this->parsePageVariables($data);
        } else {
            $data = $this->parseFileUrls($data);
            //$data = $this->parsePageUrls($data);
        }

        return $data;
    }

    // ********************************************************************************* //

    private function parseFileVariables($data='')
    {
        if (strpos($data, LD.'filedir_') !== false) {
            $vars = $this->fetchFileVariables();

            foreach ($vars as $variable => $url)
            {
                $data = str_replace($variable, $url, $data);
            }
        }

        return $data;
    }

    // ********************************************************************************* //

    private function parseFileUrls($data='')
    {
        $vars = $this->fetchFileVariables();

        foreach ($vars as $variable => $url)
        {
            $data = str_replace($url, $variable, $data);
        }

        return $data;
    }

    // ********************************************************************************* //

    private function fetchFileVariables($sort=false)
    {
        if (! isset($this->cache['file_variables']))
        {
            $this->cache['file_variables'] = array();
            $file_paths = ee()->functions->fetch_file_paths();

            foreach ($file_paths as $id => $url) {
                // ignore "/" URLs
                if ($url == '/') continue;

                $this->cache['file_variables'][LD.'filedir_'.$id.RD] = $url;
            }
        }

        return $this->cache['file_variables'];
    }

    // ********************************************************************************* //

    private function parsePageVariables($data='')
    {
        if (strpos($data, LD.'page_') !== false)
        {
            ee()->editor_helper->pages_get();

            foreach (ee()->session->cache['editor']['pages_urls'] as $entry_id => $url)
            {
                $data = str_replace(LD.'page_'.$entry_id.RD, $url, $data);
            }
        }

        return $data;
    }

    // ********************************************************************************* //

    private function parsePageUrls($data='')
    {
        ee()->editor_helper->pages_get();
        arsort(ee()->session->cache['editor']['pages_urls']);

        foreach (ee()->session->cache['editor']['pages_urls'] as $entry_id => $url)
        {
            $data = str_replace($url, LD.'page_'.$entry_id.RD, $data);
        }

        return $data;
    }

    // ********************************************************************************* //
}

/* End of file ft.editor.php */
/* Location: ./system/user/addons/editor/ft.editor.php */