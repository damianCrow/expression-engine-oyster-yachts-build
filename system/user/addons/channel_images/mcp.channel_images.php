<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Channel Images Control Panel Class
 *
 * @package         DevDemon_ChannelImages
 * @author          DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright       Copyright (c) 2007-2016 Parscale Media <http://www.parscale.com>
 * @license         http://www.devdemon.com/license/
 * @link            http://www.devdemon.com
 * @see             http://expressionengine.com/user_guide/development/module_tutorial.html#control_panel_file
 */
class Channel_images_mcp
{
    /**
     * Views Data
     * @var array
     * @access private
     */
    private $vdata = array();

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->baseUrl = ee('CP/URL', 'addons/settings/channel_images');
        $this->site_id = ee()->config->item('site_id');
        $this->vdata['baseUrl'] = $this->baseUrl->compile();

        //----------------------------------------
        // CSS/JS
        //----------------------------------------
        ee('channel_images:Helper')->mcpAssets('gjs');
        ee('channel_images:Helper')->mcpAssets('css', 'select2.css');
        ee('channel_images:Helper')->mcpAssets('css', 'addon_mcp.css', null, true);
        ee('channel_images:Helper')->mcpAssets('js', 'select2.js');
        ee('channel_images:Helper')->mcpAssets('js', 'hogan.js');
        ee('channel_images:Helper')->mcpAssets('js', 'handlebars.runtime-v4.js');
        ee('channel_images:Helper')->mcpAssets('js', 'hbs-templates.js');
        ee('channel_images:Helper')->mcpAssets('js', 'addon_mcp.js', null, true);

        $this->sidebar = ee('CP/Sidebar')->make();
        $this->batchactions = $this->sidebar->addHeader(lang('ci:batch_actions'), ee('CP/URL', 'addons/settings/channel_images'));
        $this->navimport = $this->sidebar->addHeader(lang('ci:import').' (Matrix / File)', ee('CP/URL', 'addons/settings/channel_images/import'));

        ee()->view->header = array(
            'title' => lang('channel_images'),
        );
    }

    // ********************************************************************************* //

    public function index()
    {
        return $this->batch_actions();
    }

    // ********************************************************************************* //

    public function batch_actions()
    {
        // -----------------------------------------
        // Grab all channels
        // -----------------------------------------
        $this->vdata['channels'] = array();

        foreach (ee('Model')->get('Channel')->filter('site_id', $this->site_id)->all() as $row) {
            $this->vdata['channels'][$row->channel_id] = $row->channel_title;
        }

        // -----------------------------------------
        // Grab all fields
        // -----------------------------------------
        $this->vdata['fields'] = array();

        ee()->db->select('cf.field_id, cf.field_label, fg.group_name');
        ee()->db->from('exp_channel_fields cf');
        ee()->db->where('cf.site_id', $this->site_id);
        ee()->db->where('cf.field_type', 'channel_images');
        ee()->db->join('exp_field_groups fg', 'fg.group_id = cf.group_id', 'left');
        $query = ee()->db->get();

        foreach ($query->result() as $row) {
            $this->vdata['fields'][$row->group_name][$row->field_id] = $row->field_label;
        }

        return array(
            'heading'    => lang('ci:actions'),
            'body'       => ee('View')->make('channel_images:mcp/actions')->render($this->vdata),
            'sidebar'    => $this->sidebar,
            'breadcrumb' => array(
                $this->baseUrl->compile() => lang('channel_images')
            )
        );
    }

    // ********************************************************************************* //

    public function import()
    {
        // TODO: the import script should have inserted our place holder in the custom_field so that conditional would work
        // Page Title & BreadCumbs
                $this->vdata['fields'][] = false;
        $this->vdata['section'] = 'import';
        /*if (version_compare(APP_VER, '2.6.0', '>=')) {
            ee()->view->cp_page_title = ee()->lang->line('ci:import');
        } else {
            ee()->cp->set_variable('cp_page_title', ee()->lang->line('ci:import'));
        }*/


        $this->vdata['matrix'] = array();

        // -----------------------------------------
        // Grab all matrix fields
        // -----------------------------------------
        ee()->db->select('cf.field_label, cf.field_type, cf.field_id, cf.group_id, fg.group_name');
        ee()->db->from('exp_channel_fields cf');
        ee()->db->join('exp_field_groups fg', 'cf.group_id = fg.group_id', 'left');
        ee()->db->where_in('cf.field_type', array('matrix', 'file'));
        ee()->db->order_by('cf.field_label', 'ASC');
        $query = ee()->db->get();

        foreach($query->result() as $row)
        {
            // Grab all channel image fields whithin that field group
            $q2 = ee()->db->select('field_id, field_label')->from('exp_channel_fields')->where('group_id', $row->group_id)->where('field_type', 'channel_images')->get();

            if ($row->field_type == 'matrix') {
                // Grab ll matrix columns
                $q3 = ee()->db->select('col_id, col_label')->from('exp_matrix_cols')->where('field_id', $row->field_id)->order_by('col_order', 'ASC')->get();

                // Grab all entry ids
                $q5 = ee()->db->select('entry_id')->from('exp_matrix_data')->where('field_id', $row->field_id)->group_by('entry_id')->get();
            }

            if ($row->field_type == 'file') {
                // Grab all entry ids
                $q5 = ee()->db->select('entry_id')->from('exp_channel_data')->where('field_id_'.$row->field_id. ' !=', '')->get();
            }

            // Grab channel id's
            $q4 = ee()->db->select('channel_id')->from('exp_channels')->where('field_group', $row->group_id)->get();


            $field = array();
            $field['type'] = $row->field_type;
            $field['field_label'] = $row->field_label;
            $field['field_id'] = $row->field_id;
            $field['group_label'] = $row->group_name;
            $field['channel_id'] = $q4->row('channel_id');
            $field['ci_fields'] = $q2->result();
            $field['entries'] = $q5->result();

            if ($row->field_type == 'matrix') {
                $field['cols'] = $q3->result();
            }


            $this->vdata['fields'][] = $field;
        }



        //print_r($this->vdata['matrix']);



        //return ee()->load->view('mcp_import', $this->vdata, TRUE);
                  return array(
            'heading' => lang('ci:import'),
            'body' => ee('View')->make('channel_images:mcp/import')->render($this->vdata),
            'sidebar' => $this->sidebar,
            'breadcrumb'    => array(
                $this->baseUrl->compile() => lang('channel_images')
            )
        );
    }

    // ********************************************************************************* //

    public function ajaxRouter()
    {
        // -----------------------------------------
        // EE 2.7 requires XID, restore the XID
        // -----------------------------------------
        if (version_compare(APP_VER, '2.7.0') >= 0) {
            //ee()->security->restore_xid(ee()->input->post('XID'));
        }

        include PATH_THIRD . 'channel_images/mod.channel_images.php';
        $MOD = new Channel_images();
        $MOD->channel_images_router(ee()->input->get_post('ajax_method'));
    }

    // ********************************************************************************* //

} // END CLASS

/* End of file mcp.channel_images.php */
/* Location: ./system/user/addons/channel_images/mcp.channel_images.php */