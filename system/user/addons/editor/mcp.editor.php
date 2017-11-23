<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use EllisLab\ExpressionEngine\Library\CP\Table;

/**
 * Editor Control Panel Class
 *
 * @package         DevDemon_Editor
 * @author          DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright       Copyright (c) 2007-2016 Parscale Media <http://www.parscale.com>
 * @license         http://www.devdemon.com/license/
 * @link            http://www.devdemon.com
 * @see             https://ellislab.com/expressionengine/user-guide/development/modules.html
 */
class Editor_mcp
{
    /**
     * Views Data
     * @var array
     * @access private
     */
    private $vdata = array();

    /**
     * Base URI
     * @var string
     * @access protected
     */
    protected $baseUri = 'addons/settings/editor/';

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->baseUrl = ee('CP/URL', $this->baseUri);
        $this->site_id = ee()->config->item('site_id');
        $this->vdata['baseUri'] = $this->baseUri;
        $this->vdata['baseUrl'] = $this->baseUrl->compile();

        //----------------------------------------
        // CSS/JS
        //----------------------------------------
        ee('editor:Helper')->mcpAssets('gjs');
        ee('editor:Helper')->mcpAssets('css', 'addon_mcp.css', null, true);
        ee('editor:Helper')->mcpAssets('js', 'addon_mcp.js', null, true);

        //$this->sidebar = ee('CP/Sidebar')->make();
        //$this->navConfigs = $this->sidebar->addHeader(lang('ed:configs'), ee('CP/URL', 'addons/settings/editor'));
        //$this->navCategory = $this->sidebar->addHeader(lang('ed:category_settings'), ee('CP/URL', 'addons/settings/editor/category'));

        ee()->view->header = array(
            'title' => lang('editor'),
        );
    }

    // ********************************************************************************* //

    public function index()
    {
        // Mark the sidebar menu as active
        //$this->navConfigs->isActive();

        // Use default options
        $table = ee('CP/Table', array('autosort' => true, 'autosearch' => false));
        $table->setNoResultsText('ed:no_configs', 'ed:create_config', ee('CP/URL', 'addons/settings/editor/create-config'));
        $table->setColumns(
            array(
                'ed:label',
                'ed:type',
                'manage' => array(
                    'type'  => Table::COL_TOOLBAR
                ),
                array(
                    'type'  => Table::COL_CHECKBOX
                )
            )
        );

        // Add the confirm_remove JS script
        ee()->cp->add_js_script(array(
            'file' => array('cp/confirm_remove'),
        ));

        $data = array();
        $configs = ee('Model')->get('editor:Config')->all();

        foreach ($configs as $config) {
            $editUrl = ee('CP/URL', 'addons/settings/editor/edit-config/' . $config->id);

            $data[] = array(
                $config->label,
                $config->type,
                array('toolbar_items' => array(
                        'edit' => array(
                            'href' => $editUrl,
                            'title' => lang('edit')
                        )
                    )
                ),
                array(
                    'name' => 'configs[]',
                    'value' => $config->id,
                    'data'  => array(
                        'confirm' => lang('ed:config') . ': <b>' . htmlentities($config->label, ENT_QUOTES) . '</b>'
                    )
                )
            );
        }

        $table->setData($data);

        $base_url = ee('CP/URL', 'addons/settings/editor');
        $this->vdata['table'] = $table->viewData($base_url);

        // Return the view array
        return array(
            'heading'    => lang('ed:configs'),
            'body'       => ee('View')->make('editor:mcp/configs')->render($this->vdata),
            //'sidebar'    => $this->sidebar,
            'breadcrumb' => array(
                $this->baseUrl->compile() => lang('editor')
            )
        );
    }

    // ********************************************************************************* //

    public function createConfig($id=0)
    {
        // Mark the sidebar menu as active
        //$this->navConfigs->isActive();
        ee('editor:Helper')->mcpAssets('css', 'redactor.css', 'redactor');

        ee()->cp->add_js_script(array(
            'file' => array('cp/form_group'),
        ));

        if ($id) {
            $config = ee('Model')->get('editor:Config')->filter('id', $id)->first();
        } else {
            $config = ee('Model')->make('editor:Config');
        }

        if (!$config->settings) {
            $config->settings = ee('App')->get('editor')->get('redactor_default');
        } else {
            $config->settings = array_merge(ee('App')->get('editor')->get('redactor_default'), $config->settings);
        }

        $this->vdata['formName'] = $formName = 'redactor';
        $this->vdata['settings'] = $settings = $config->settings;

        $vdataToolbar = $this->vdata;
        $vdataToolbar['allButtons'] = ee('editor:Configuration')->buttons;

        $vdataPlugins = $this->vdata;
        $vdataPlugins['allPlugins'] = ee('editor:Configuration')->getPluginList();

        // -----------------------------------------
        // Advanced Settings
        // -----------------------------------------
        $vdataSettings = $this->vdata;
        $vdataSettings['avdSettingsArr'] = array();
        $vdataSettings['avdSettingsArr'][''] = lang('ed:add_adv_setting');
        $advSettings = ee('editor:Configuration')->getDefaultAdvancedSettings();

        $json['options'] = array();
        $json['options_current'] = array();

        foreach ($advSettings as $handle => $setting) {
            $vdataSettings['avdSettingsArr'][$handle] = $handle;

            $json['options'][$handle] = $setting;
            $json['options'][$handle]['desc'] = lang('ed:redactor:' . $handle);
            $json['options'][$handle]['exp'] = (lang('ed:redactor:exp:' . $handle) != 'ed:redactor:exp:' . $handle) ? lang('ed:redactor:exp:' . $handle) : false;
        }

        foreach ($config->settings as $key => $val) {
            if (isset($advSettings[$key])) {
                $json['options_current'][$key] = $val;
            }
        }

        $vdataSettings['optionsJson'] = json_encode($json);

        // -----------------------------------------
        // File Upload Destinations
        // -----------------------------------------
        $locations = array('0' => lang('ed:disabled'));
        $dbLocs = ee('Model')->get('UploadDestination')
        ->filter('site_id', ee()->config->item('site_id'))
        ->order('name', 'asc')->all();

        foreach ($dbLocs as $loc) {
            $locations[$loc->id] = $loc->name;
        }

        // -----------------------------------------
        // S3 Regions
        // -----------------------------------------
        $s3 = array();

        foreach (ee('App')->get('editor')->get('s3_regions') as $region => $endpoint) {
            $s3[$region] = lang('ed:s3:' . $region);
        }

        $fileUploadActurl = ee('editor:Helper')->getRouterUrl('url', 'actionFileUpload');

        // -----------------------------------------
        // Form definition array
        // -----------------------------------------
        $this->vdata['sections'] = array(
            array(
                array(
                    'hide'   => true,
                    'fields' => array(
                        'config_id' => array(
                            'type'  => 'hidden',
                            'value' => $config->id,
                        )
                    )
                ),
                array(
                    'title'  => 'ed:label',
                    'fields' => array(
                        'config_label' => array(
                            'type'  => 'text',
                            'value' => $config->label,
                        )
                    )
                ),
                array(
                    'title'  => 'ed:type',
                    'fields' => array(
                        'config_type' => array(
                            'type'  => 'inline_radio',
                            'value' => $config->type ?: 'redactor',
                            'choices'=>array(
                                'redactor' => 'redactor.js',
                            ),
                        )
                    )
                ),
            ),
            'ed:toolbar_buttons' => array(
                array(
                    'wide' => true,
                    'fields' => array(
                        'config_type' => array(
                            'type'  => 'html',
                            'content' => ee('View')->make('editor:mcp/redactor/toolbar')->render($vdataToolbar),
                        )
                    )
                ),
            ),
            'ed:adv_settings' => array(
                array(
                    'wide' => true,
                    'fields' => array(
                        'config_type' => array(
                            'type'  => 'html',
                            'content' => ee('View')->make('editor:mcp/redactor/adv_settings')->render($vdataSettings),
                        )
                    )
                ),
            ),
            'ed:upload_settings' => array(
                array(
                    'title'  => 'ed:upload_service',
                    'fields' => array(
                        $formName.'[upload_service]' => array(
                            'type' => 'inline_radio',
                            'choices' => array(
                                'local'      => 'Local',
                                's3'         => 'Amazon S3',
                            ),
                            'group_toggle' => array(
                                'local'      => 'upload-local',
                                's3'         => 'upload-s3',
                            ),
                            'value' => isset($settings['upload_service']) ? $settings['upload_service'] : 'local',
                        ),
                    ),
                ),
                array(
                    'title' => 'ed:file_upload_loc',
                    'group'  => 'upload-local',
                    'fields' => array(
                        $formName.'[files_upload_location]' => array(
                            'type'    => 'select',
                            'choices' => $locations,
                            'value'   => $settings['files_upload_location'],
                        ),
                    )
                ),
                array(
                    'title' => 'ed:file_browse',
                    'group'  => 'upload-local',
                    'fields' => array(
                        $formName.'[files_browse]' => array(
                            'type' => 'inline_radio',
                            'choices' => array(
                                'yes' => lang('yes'),
                                'no'  => lang('no'),
                            ),
                            'value'   => $settings['files_browse'],
                        ),
                    )
                ),
                array(
                    'title' => 'ed:image_upload_loc',
                    'group'  => 'upload-local',
                    'fields' => array(
                        $formName.'[images_upload_location]' => array(
                            'type'    => 'select',
                            'choices' => $locations,
                            'value'   => $settings['images_upload_location'],
                        ),
                    )
                ),
                array(
                    'title' => 'ed:image_browse',
                    'group'  => 'upload-local',
                    'fields' => array(
                        $formName.'[images_browse]' => array(
                            'type' => 'inline_radio',
                            'choices' => array(
                                'yes' => lang('yes'),
                                'no'  => lang('no'),
                            ),
                            'value'   => $settings['images_browse'],
                        ),
                    )
                ),
                array(
                    'title' => 'ed:s3:bucket_file',
                    'group'  => 'upload-s3',
                    'fields' => array(
                        $formName.'[s3][files][bucket]' => array(
                            'type' => 'text',
                            'value' => $settings['s3']['files']['bucket'],
                        ),
                    )
                ),
                array(
                    'title' => 'ed:s3:region_file',
                    'group'  => 'upload-s3',
                    'fields' => array(
                        $formName.'[s3][files][region]' => array(
                            'type'    => 'select',
                            'choices' => $s3,
                            'value'   => $settings['s3']['files']['region'],
                        ),
                    )
                ),
                array(
                    'title' => 'ed:s3:bucket_image',
                    'group'  => 'upload-s3',
                    'fields' => array(
                        $formName.'[s3][images][bucket]' => array(
                            'type' => 'text',
                            'value' => $settings['s3']['images']['bucket'],
                        ),
                    )
                ),
                array(
                    'title' => 'ed:s3:region_image',
                    'group'  => 'upload-s3',
                    'fields' => array(
                        $formName.'[s3][images][region]' => array(
                            'type'    => 'select',
                            'choices' => $s3,
                            'value'   => $settings['s3']['images']['region'],
                        ),
                    )
                ),
                array(
                    'title' => 'ed:s3:aws_key',
                    'group'  => 'upload-s3',
                    'fields' => array(
                        $formName.'[s3][aws_access_key]' => array(
                            'type' => 'text',
                            'value' => $settings['s3']['aws_access_key'],
                        ),
                    )
                ),
                array(
                    'title' => 'ed:s3:aws_secret_key',
                    'group'  => 'upload-s3',
                    'fields' => array(
                        $formName.'[s3][aws_secret_key]' => array(
                            'type' => 'text',
                            'value' => $settings['s3']['aws_secret_key'],
                        ),
                    )
                ),
                array(
                    'title' => 'ed:upload_url',
                    'fields' => array(
                        'fewfwfewef' => array(
                            'type' => 'html',
                            'content' => '<a href="'.$fileUploadActurl.'" target="_blank">'.$fileUploadActurl.'</a>',
                        ),
                    )
                ),
            ),
            'ed:plugins' => array(
                array(
                    'wide' => true,
                    'fields' => array(
                        'config_type' => array(
                            'type'  => 'html',
                            'content' => ee('View')->make('editor:mcp/redactor/plugins')->render($vdataPlugins),
                        )
                    )
                ),
            ),
        );

        // Final view variables we need to render the form
        $this->vdata += array(
            'base_url'      => ee('CP/URL', $this->baseUri . 'update-config')->compile(),
            'cp_page_title' => $config->isNew() ? lang('ed:create_config') : lang('ed:edit_config'),
            'save_btn_text' => sprintf(lang('btn_save'), lang('ed:config')),
            'save_btn_text_working' => 'btn_saving',
        );

        return array(
            'heading'   => $config->isNew() ? lang('ed:create_config') : lang('ed:edit_config'),
            'body'      => ee('View')->make('editor:mcp/configs_create')->render($this->vdata),
            'breadcrumb'=> array(
                $this->baseUrl->compile() => lang('editor')
            )
        );
    }

    // ********************************************************************************* //

    public function editConfig($id)
    {
        return $this->createConfig($id);
    }

    // ********************************************************************************* //

    public function updateConfig()
    {
        $id = ee('Request')->post('config_id');

        if ($id) {
            $config = ee('Model')->get('editor:Config')->filter('id', $id)->first();
        } else {
            $config = ee('Model')->make('editor:Config');
        }

        $config->label = ee('Request')->post('config_label');
        $config->type  = ee('Request')->post('config_type');
        $config->settings = ee('Request')->post($config->type);

        $config->save();

        ee('CP/Alert')->makeInline('configs-table')
        ->asSuccess()
        ->withTitle(lang('ed:updated_config'))
        ->defer();

        ee()->functions->redirect($this->baseUrl);
    }

    // ********************************************************************************* //

    public function removeConfig()
    {
        if (!ee()->input->post('configs')) {
            ee()->functions->redirect($this->baseUrl);
        }

        $configs = ee('Model')->get('editor:Config')->filter('id', 'IN', ee('Request')->post('configs'))->all();

        foreach ($configs as $config) {
            $config->delete();
        }

        ee('CP/Alert')->makeInline('groups-table')
        ->asSuccess()
        ->withTitle(lang('ed:deleted_config'))
        ->defer();

        ee()->functions->redirect($this->baseUrl);
    }

    // ********************************************************************************* //

} // END CLASS

/* End of file mcp.editor.php */
/* Location: ./system/user/addons/editor/mcp.editor.php */