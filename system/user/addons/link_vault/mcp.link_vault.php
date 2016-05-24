<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Link Vault Module Control Panel File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Masuga Design
 * @link		http://www.masugadesign.com
 */

class Link_vault_mcp {

	public $return_data;

	private $base_url            = '';
	private $base_short          = '';
	protected $site_index        = '';
	protected $view_data         = '';
	protected $site_id           = '';
	protected $settings          = array();
	protected $override_settings = array();

	// CP Navigation
	protected $custom_fields_url        = '';
	protected $new_custom_field_url     = '';
	protected $delete_custom_fields_url = '';
	protected $create_custom_field_url  = '';
	protected $reports_url              = '';
	protected $save_report_url          = '';
	protected $delete_report_url        = '';
	protected $update_settings_url      = '';

	protected $docs_url = '';

	protected $custom_fields_array = array();
	protected $default_form_values = array();

	protected $link_vault_themes_url = '';

	/**
	 * This boolean value is set to "true" if EE3+ is installed.
	 * @var boolean
	 */
	protected $ee3 = false;

	// ----------------------------------------------------------------

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Load the Link Vault download model
		ee()->load->add_package_path( PATH_THIRD.'link_vault/' );
		//ee()->load->add_package_path( 'Link_vault' );
		ee()->load->library('link_vault_library');
		// Set the boolean used to determine if this installation is EE3/EE2.
		$this->ee3 = ee()->link_vault_library->ee3();
		// Set some basic site data for quick retrieval.
		$this->site_id = ee()->config->item('site_id');
		$this->site_index = ee()->config->site_url();
		$this->docs_url = ee()->cp->masked_url('http://masugadesign.com/software/link-vault');
		// Set the base URL of the control panel page based on the currently installed EE version.
		$this->base_url   = $this->ee3 ? ee('CP/URL', 'addons/settings/link_vault') : BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=link_vault';
		// Set the short URL used for form submissions in EE2.
		$this->base_short = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=link_vault';
		// Specify the path to Link Vault's themes directory based on currently installed EE version.
		$this->link_vault_themes_url = $this->ee3 ? ee()->config->item('theme_folder_url').'user/link_vault/' : ee()->config->item('theme_folder_url').'third_party/link_vault/';
		// Set all the CP page URL paths based on currently installed EE version.
		$this->custom_fields_url		= $this->ee3 ? ee('CP/URL', 'addons/settings/link_vault/custom_fields')  : $this->base_url.AMP.'method=custom_fields';
		$this->new_custom_field_url		= $this->ee3 ? ee('CP/URL', 'addons/settings/link_vault/new_custom_field') : $this->base_url.AMP.'method=new_custom_field';
		$this->reports_url              = $this->ee3 ? ee('CP/URL', 'addons/settings/link_vault/reports') : $this->base_url.AMP.'method=reports';
		$this->saved_reports_url		= $this->ee3 ? ee('CP/URL', 'addons/settings/link_vault/saved_reports') : $this->base_url.AMP.'method=saved_reports';
		$this->delete_report_url        = $this->ee3 ? ee('CP/URL', 'addons/settings/link_vault/delete_saved_report') : $this->base_url.AMP.'method=delete_saved_report';
		$this->delete_custom_fields_url	= $this->ee3 ? ee('CP/URL', 'addons/settings/link_vault/delete_custom_fields') : $this->base_short.AMP.'method=delete_custom_fields';
		$this->create_custom_field_url	= $this->ee3 ? ee('CP/URL', 'addons/settings/link_vault/create_custom_field') : $this->base_short.AMP.'method=create_custom_field';
		$this->save_report_url          = $this->ee3 ? ee('CP/URL', 'addons/settings/link_vault/save_report') : $this->base_short.AMP.'method=save_report';
		$this->update_settings_url      = $this->ee3 ? ee('CP/URL', 'addons/settings/link_vault/update_settings') : $this->base_short.AMP.'method=update_settings';
		// Load the stored custom settings and the override settings from the config file
		$this->load_custom_settings();
		// Fetch the custom fields from the DB
		$this->custom_fields_array = $this->fetch_custom_fields();
		// Populate the default report form values here so they can be overriden at any time
		$this->default_form_values = $this->_populate_default_report_values();
		// Setup the control panel navigation based on the currently installed version of EE.
		if ( $this->ee3 ) {
			$sidebar = ee('CP/Sidebar')->make();
			$sidebar->addHeader(lang('module_home'), $this->base_url);
			$sidebar->addHeader(lang('module_cols'), $this->custom_fields_url);
			$sidebar->addHeader(lang('module_reports'), $this->reports_url );
			$sidebar->addHeader(lang('module_saved_reports'), ee('CP/URL', 'addons/settings/link_vault/saved_reports') );
			$sidebar->addHeader(lang('module_docs'), $this->docs_url );
		} else {
			ee()->cp->set_right_nav(array(
				'module_home'			=> $this->base_url,
				'module_cols'			=> $this->custom_fields_url,
				'module_reports'		=> $this->reports_url,
				'module_saved_reports'	=> $this->saved_reports_url,
				'module_docs'			=> $this->docs_url
			));
		}
	}

	// ----------------------------------------------------------------

	/**
	 * Index Function
	 *
	 * @return String (the view)
	 */
	public function index()
	{
		ee()->view->cp_page_title = lang('link_vault_module_name').' : '.lang('module_home');

		ee()->load->helper('form');
		$this->view_data = array(
			'settings'                       => $this->settings,
			'override_settings'              => $this->override_settings,
			'themes_dir'                     => $this->link_vault_themes_url,
			'base_short'                     => $this->base_short,
			'update_settings_url'            => $this->update_settings_url,
		);
		ee()->cp->add_to_head(ee()->load->view('styles/cp', null, true));
		return $this->view('index', $this->view_data);
	}

	// ----------------------------------------------------------------

	/**
	 * This method updates the Link Vault settings and will generate a new
	 * salt value if the user did not specify one then it redirects the
	 * user back to the Link Vault control panel homepage.
	 */
	public function update_settings()
	{
		$settings = array();

		ee()->load->helper('string');

		$settings['site_id']         = ee()->config->item('site_id');
		$settings['salt']            = ee()->input->post('lv_salt') ? ee()->input->post('lv_salt', true) : random_string('alnum', 12);
		$settings['hidden_folder']   = ee()->input->post('lv_hidden_folder') ? ltrim(ee()->input->post('lv_hidden_folder', true), '/') : '';
		$settings['leech_url']       = ee()->input->post('lv_leech_url') ? ee()->input->post('lv_leech_url', true) : '';
		$settings['missing_url']     = ee()->input->post('lv_missing_url') ? ee()->input->post('lv_missing_url', true) : '';
		$settings['block_leeching']  = (ee()->input->post('lv_block_leeching') != "") ? ee()->input->post('lv_block_leeching', true) : '1';
		$settings['log_leeching']    = (ee()->input->post('lv_log_leeching') != "") ? ee()->input->post('lv_log_leeching', true) : '1';
		$settings['log_link_clicks'] = (ee()->input->post('lv_log_link_clicks') != "") ? ee()->input->post('lv_log_link_clicks', true) : '1';
		$settings['aws_access_key']  = ee()->input->post('lv_aws_access_key') ? ee()->input->post('lv_aws_access_key', true) : '';
		$settings['aws_secret_key']  = ee()->input->post('lv_aws_secret_key') ? ee()->input->post('lv_aws_secret_key', true) : '';

		if (substr($settings['hidden_folder'], -1) != '/')
			$settings['hidden_folder'] .= '/';

		if(ee()->db->update('link_vault_settings', $settings, array('site_id' => $settings['site_id']))) {
			$this->success_alert(lang('preferences_updated'));
		} else {
			$this->error_alert('Update Failed');
		}

		ee()->functions->redirect($this->base_url);
	}

	// ----------------------------------------------------------------

	/**
	 * This method return the custom fields management page that includes
	 * a table of all existing Link Vault custom fields as well as a button
	 * to generate new ones.
	 * @return string (the view)
	 */
	public function custom_fields()
	{
		ee()->view->cp_page_title = lang('link_vault_module_name').' : '.lang('module_cols');

		ee()->load->helper('form');
		$this->view_data = array(
			'base_short'			=> $this->base_short,
			'custom_fields'			=> $this->custom_fields_array,
			'delete_form_action'	=> $this->delete_custom_fields_url,
			'new_custom_field_url'	=> $this->new_custom_field_url
		);
		ee()->cp->add_to_head(ee()->load->view('styles/cp', null, true));
		return $this->view('custom_fields', $this->view_data);
	}

	// ----------------------------------------------------------------

	/**
	 * Reports
	 *
	 * This CP page allows the user to generate a downloads report based
	 * on customizeable criteria.
	 *
	 * @return String (the view)
	 */
	public function reports()
	{
		ee()->view->cp_page_title = lang('link_vault_module_name').' : '.lang('module_reports');

		ee()->load->helper('form');

		// Calculate the width for each custom field column in the report
		$custom_field_width = !empty($this->custom_fields_array) ? (int)(40 / count($this->custom_fields_array)) : '0';

		// Report filter query parameters
		$data['table']         = ee()->input->get_post('table') ? ee()->input->get_post('table', true) : 'downloads';
		$data['directory']     = ee()->input->get_post('directory', true);
		$data['pretty_url_id'] = ee()->input->get_post('pretty_url_id', true);
		$data['s3_bucket']     = ee()->input->get_post('s3_bucket', true);
		$data['file_name']     = ee()->input->get_post('file_name', true);
		$data['member_id']     = ee()->input->get_post('member_id', true);
		$data['start_date']    = ee()->input->get_post('start_date') ? strtotime( ee()->input->get_post('start_date', true).' 00:00:00' ) : '';
		$data['end_date']      = ee()->input->get_post('end_date') ? strtotime( ee()->input->get_post('end_date', true).' 23:59:59' ) : '';
		$data['order_by']      = ee()->input->get_post('order_by') ? ee()->input->get_post('order_by', true) : 'unix_time';
		$data['sort']          = ee()->input->get_post('sort') ? ee()->input->get_post('sort', true) : 'desc';
		$data['limit']         = ee()->input->get_post('limit') ? ee()->input->get_post('limit', true) : 50;
		$data['offset']        = ee()->input->get_post('per_page') ? ee()->input->get_post('per_page', true) : 0;
		$execution             = ee()->input->get_post('submit') != '' ? ee()->input->get_post('submit', true) : 'run_report'; // or "run_export"

		// If a report_id was specified, override the query criteria with those values
		if (ee()->input->get('report_id') != '') {
			$data = array_merge($data, $this->_fetch_report_criteria( ee()->input->get('report_id', true) ) );
		}

		// Loop through the defined custom fields and check for form submission values
		$custom_field_data = array();
		foreach ($this->custom_fields_array as $field) {
			// Old reports with custom field values specified will not have the cf_ prefix, check for that first.
			$cf_fallback = !empty($data[ $field['field_name'] ]) ? $data[ $field['field_name'] ] : ee()->input->get_post('cf_'.$field['field_name'], true);
			// Custom fields should be prefixed with cf_ in the form as of v1.3.9
			$cf_value = !empty($data[ 'cf_'.$field['field_name'] ]) ? $data[ 'cf_'.$field['field_name'] ] : $cf_fallback;
			// A custom field filter field is populated
			if ($cf_value != '') {
				// This array is used for querying the LV downloads table
				$custom_field_data[$field['field_name']] = $cf_value;
			}
		}

		// Downloads and link clicks are actually in the same table, so we take that into account here
		$data['is_link_click'] = 'n';
		if ($data['table'] == 'link_clicks') {
			$data['table'] = 'downloads';
			$data['is_link_click'] = 'y';
		}

		// If we are doing an XLS export, we need to override the limit to be a much larger number.
		if ( $execution == 'run_export' ) {
			$data['limit'] = 999999999;
		}

		// Search the LV data for the rows that match the specified criteria from the reports page
		$prepend_columns = $execution == 'run_export' ? true : false;
		$report_data = ee()->link_vault_library->row_search($data, $custom_field_data, false, '', $prepend_columns);

		// Are we displaying the report results in the browser or downloading an XLS?
		if ( $execution == 'run_report' ) {

			// Fetch the total rows without limitation for pagination purposes
			$complete_data = $data;
			$complete_data['limit'] = 999999999;
			unset($complete_data['offset']);
			$grand_total_results = ee()->link_vault_library->row_search_count($complete_data, $custom_field_data);
			$results_variables = array(
				'report_results'	=> $report_data,
				'custom_fields'		=> $this->custom_fields_array,
			);

			// Set the values that will appear in the form inputs
			$form_values = array_merge($this->default_form_values, $data, $custom_field_data);

			// Override the table selector value to reflect if we are looking for link clicks and not standard downloads. (presentation only)
			if ( $form_values['is_link_click'] == 'y' ) {
				$form_values['table'] = 'link_clicks';
			}

			ee()->load->library('pagination');

			// Manually configure pagination so we can perform backwards compatibility in EE2 with custom styles.
			ee()->pagination->cur_tag_open	 = '<li><a href="" class="act">';
			ee()->pagination->cur_tag_close = '</a></li>';
			ee()->pagination->first_tag_open = '<li>';
			ee()->pagination->first_tag_close = '</li>';
			ee()->pagination->last_tag_open = '<li>';
			ee()->pagination->last_tag_close = '</li>';
			ee()->pagination->next_tag_open = '<li>';
			ee()->pagination->next_tag_close = '</li>';
			ee()->pagination->prev_tag_open = '<li>';
			ee()->pagination->prev_tag_close = '</li>';
			ee()->pagination->num_tag_open	 = '<li>';
			ee()->pagination->num_tag_close = '</li>';

			$query_string_suffix = '';
			foreach ($form_values as $datum => $value) {
				if ( !empty($value) ) {
					$query_string_suffix .= AMP.$datum.'='.$value;
				}
			}
			//mail('{YOUR_EMAIL}', 'Values Arrays', "Form Values:".print_r($form_values, true)."\nDefault Form Vals:".print_r($this->default_form_values,true)."\nData:".print_r($data,true)."\nCustom Fields:".print_r($custom_field_data,true)."\nGET:".print_r($_GET, true)."\nNext Query String Suffix: \n\n".$query_string_suffix);
			// Setup the pagination links
			$page_config = array(
				'base_url'          => $this->base_url.AMP.'method=reports'.$query_string_suffix,
				'total_rows'        => $grand_total_results,
				'per_page'          => $data['limit'],
				'page_query_string' => true
			);
			ee()->pagination->initialize($page_config);

			// We want the relative path to the jQuery library and NOT the full system path
			$alternate_jquery_url = ee()->functions->fetch_site_index().str_replace($_SERVER['DOCUMENT_ROOT'].'/', '', PATH_JQUERY);
			$jquery_path = ee()->config->item('theme_folder_url') != '' ? ee()->config->item('theme_folder_url').'javascript/compressed/jquery/' : $alternate_jquery_url;

			// These variables are used in the view to populate various dropdown options
			$distinct_directories = ee()->link_vault_library->distinct_download_directory_options();
			$distinct_s3_buckets  = ee()->link_vault_library->distinct_s3_bucket_options();
			$distinct_pretty_urls = ee()->link_vault_library->distinct_pretty_urls();

			$this->view_data = array(
				'base_short'					=> $this->base_short,
				'save_report_url'				=> $this->save_report_url,
				'custom_fields'					=> $this->custom_fields_array,
				'custom_field_width'			=> $custom_field_width,
				'distinct_pretty_urls'			=> $distinct_pretty_urls,
				'distinct_directories'			=> $distinct_directories,
				'distinct_s3_buckets'			=> $distinct_s3_buckets,
				'cp_theme_url'					=> ee()->cp->cp_theme_url,
				'order_by_options'				=> $this->_order_by_options(),
				'limit_options'					=> $this->_limit_options(),
				'jquery_path'					=> $jquery_path,
				'default_values'				=> $form_values,
				'themes_dir'					=> $this->link_vault_themes_url,
				'order_by'						=> $data['order_by'],
				'sort'							=> $data['sort'],
				'limit'							=> $data['limit'],
				'total_rows'					=> $grand_total_results,
				'cp_url'						=> ee()->config->item('cp_url'),
				'report_submit_url'				=> $this->reports_url,
				'ee3'							=> $this->ee3,
				'pagination_links'				=> ee()->pagination->create_links(),
				'report_content'				=> $this->view('reports_results', $results_variables)
			);

			// This page needs the jQuery date picker @todo : test to make sure this works, Ben!
			ee()->cp->add_to_head(ee()->load->view('styles/cp', null, true));
			ee()->cp->add_js_script('ui', 'datepicker');
			ee()->cp->load_package_js('reports');

			return $this->view('reports', $this->view_data);

		} else {

			ee()->load->add_package_path( PATH_THIRD.'link_vault' );
			ee()->load->library('link_vault_export');

			// Generate XLS content from the report results, write it to a file and download it.
			$xls_data = ee()->link_vault_export->array_to_xls($report_data);
			$file_name = 'link-vault-report-'.date('YmdHis').'.xls';
			ee()->link_vault_export->download($file_name, $xls_data);
		}
	}

	// ----------------------------------------------------------------

	/**
	 * Save Report
	 *
	 * This method saves report criteria so that the report can be run
	 * again at a later date.
	 *
	 * @return void
	 */
	public function save_report()
	{
		// POST parameters
		$report_title    = ee()->input->post('report_title', true);
		$report_criteria = ee()->input->post('report_criteria', true);

		// Initialize the success status as 0 (error)
		$status['status'] = 0;

		// Only attempt to store the report criteria if the POST data is populated
		if ($report_title && $report_criteria)
		{
			unset($report_criteria['XID']);
			unset($report_criteria['rpt_title']);

			$record_data = array(
				'site_id'	=> ee()->config->item('site_id'),
				'title'		=> $report_title,
				'criteria'	=> serialize($report_criteria)
			);

			ee()->db->insert('link_vault_reports', $record_data);
			$id = ee()->db->insert_id();

			// Record was saved successfully
			if ($id) {
				$status['status'] = 1;
			}
		}

		header('Content-Type: application/json');
		exit( json_encode($status) );
	}

	// ----------------------------------------------------------------

	/**
	 * This method loads the CP page for saved reports.
	 * @return string
	 */
	public function saved_reports()
	{
		ee()->view->cp_page_title = lang('link_vault_module_name').' : '.lang('module_saved_reports');
		$this->view_data = array(
			'reports_url'					=> $this->reports_url,
			'delete_report_url'				=> $this->delete_report_url,
			'saved_reports'					=> $this->_fetch_reports()
		);
		ee()->cp->add_to_head(ee()->load->view('styles/cp', null, true));
		return $this->view('saved_reports', $this->view_data);
	}

	// ----------------------------------------------------------------

	/**
	 * This is the CP method for deleting a saved report.
	 * @return void
	 */
	public function delete_saved_report()
	{
		$report_id = ee()->input->get('report_id', true);

		if ($report_id != '') {
			ee()->db->where('id', $report_id)->delete('link_vault_reports');

			if (ee()->db->affected_rows() > 0) {
				$this->success_alert(lang('success_report_deleted'));
			} else {
				$this->error_alert(lang('error_report_deleted'));
			}
		}

		ee()->functions->redirect($this->saved_reports_url);
	}

	// ----------------------------------------------------------------

	/**
	 * This method is performed when a user attempts to delete a custom
	 * field from the CP.
	 * @return void
	 */
	public function delete_custom_fields()
	{
		$custom_fields = ee()->input->post('cf_delete') ? ee()->input->post('cf_delete', true) : array();

		$deleted = 0;

		foreach($custom_fields as $field_id => $value) {
			// Fetch the custom field by ID.
			$row = ee()->db->get_where('link_vault_custom_fields', array(
				'field_id' => $field_id
			))->row();
			// If the field exists, remove the column from the Link Vault tables and delete the row.
			if ($row) {
				ee()->load->dbforge();
				// drop the column from the downloads table.
				if ( ee()->db->field_exists('cf_'.$row->field_name, 'link_vault_downloads') ) {
					ee()->dbforge->drop_column('link_vault_downloads', 'cf_'.$row->field_name);
				}
				// Drop the column from the leeches table.
				if ( ! ee()->db->field_exists('cf_'.$row->field_name, 'link_vault_leeches') ) {
					ee()->dbforge->drop_column('link_vault_leeches', 'cf_'.$row->field_name);
				}
				// Remove the custom field record from the DB.
				ee()->db->delete('link_vault_custom_fields', array(
					'field_id' => $field_id
				));

				$deleted ++;
			}
		}

		if ($deleted > 0) {
			$this->success_alert(lang('success_fields_deleted'));
		} else {
			$this->error_alert(lang('error_fields_deleted'));
		}

		ee()->functions->redirect($this->custom_fields_url);
	}

	// ----------------------------------------------------------------

	/**
	 * New Custom Field
	 *
	 * This method returns the view containing the new custom field form.
	 *
	 * @return String (the view)
	 */
	public function new_custom_field()
	{
		ee()->view->cp_page_title = lang('link_vault_module_name').' : '.lang('new_custom_field_title');

		$this->view_data = array(
			'create_custom_field_action' => $this->create_custom_field_url
		);
		ee()->cp->add_to_head(ee()->load->view('styles/cp', null, true));
		ee()->cp->load_package_js('new_custom_field');
		return $this->view('new_custom_field', $this->view_data);
	}

	// ----------------------------------------------------------------

	/**
	 * Create Custom Field
	 *
	 * This method is called as a form submission for the new custom field form.
	 * It then redirects back to the custom fields table.
	 *
	 * @return void
	 */
	public function create_custom_field()
	{
		$field_name    = str_replace(' ', '_', strtolower(ee()->input->post('cf_field_name', true)));
		$field_label   = ee()->input->post('cf_field_label', true);
		$field_type    = ee()->input->post('cf_field_type') ? ee()->input->post('cf_field_type', true) : 'VARCHAR';
		$field_length  = ee()->input->post('cf_field_length') ? ee()->input->post('cf_field_length', true) : FALSE;
		$field_default = (is_numeric(ee()->input->post('cf_field_default')) || is_string(ee()->input->post('cf_field_default'))) ? ee()->input->post('cf_field_default', true) : FALSE;

		if ($field_name && $field_label)
		{
			ee()->load->dbforge();

			$field_options = array(
				'cf_'.$field_name => array(
					'type' => $field_type,
				)
			);
			// Set the max length when necessary
			if ($field_length) {
				$field_options['cf_'.$field_name]['constraint'] = $field_length;
			} else if ($field_type == 'VARCHAR') {
				$field_options['cf_'.$field_name]['constraint'] = 200;
			}
			// Set the default field value based on column type
			if (is_numeric($field_default) && !strpos($field_default, '.') && $field_type == 'INT') {
				$field_options['cf_'.$field_name]['default'] = $field_default;
			} else if (is_numeric($field_default) && $field_type == 'FLOAT') {
				$field_options['cf_'.$field_name]['default'] = $field_default;
			} else if (is_string($field_default) && $field_type == 'VARCHAR') {
				$field_options['cf_'.$field_name]['default'] = $field_default;
			}
			// Add the column to the LINK_VAULT_DOWNLOADS table
			if ( ! ee()->db->field_exists('cf_'.$field_name, 'link_vault_downloads') ) {
				ee()->dbforge->add_column('link_vault_downloads', $field_options);
			}
			// Add the column to the LINK_VAULT_LEECHES table
			if ( ! ee()->db->field_exists('cf_'.$field_name, 'link_vault_leeches') ) {
				ee()->dbforge->add_column('link_vault_leeches', $field_options);
			}
			// Insert the column record into the LINK_VAULT_CUSTOM_FIELDS table for reference
			ee()->db->insert('link_vault_custom_fields', array(
				'site_id' => ee()->session->userdata('site_id'),
				'field_name' => $field_name,
				'field_label' => $field_label
			));

			$this->success_alert(lang('success_field_created'));
		} else {
			$this->error_alert(lang('error_field_created'));
		}
		ee()->functions->redirect($this->custom_fields_url);
	}

	// ----------------------------------------------------------------
	//              N O N - C P   P A G E   M E T H O D S
	// ----------------------------------------------------------------

	/**
	 * This function loads the saved custom settings for this module.  If there are none,
	 * a settings record is created using the defaults.
	 */
	protected function load_custom_settings()
	{
		$query = ee()->db->get_where('link_vault_settings', array('site_id' => $this->site_id));
		if ($query->num_rows() == 1)
		{
			$this->settings = $query->row_array();
		}
		else
			$this->_load_default_settings();

		$this->load_override_settings();
	}

	// ----------------------------------------------------------------

	/**
	 * This method loads any declared override settings that are set in the main config.php file.
	 * @return void
	 */
	protected function load_override_settings()
	{
		$this->override_settings['salt']            = ee()->config->item('link_vault_salt') ? ee()->config->item('link_vault_salt') : '';
		$this->override_settings['hidden_folder']   = ee()->config->item('link_vault_hidden_folder') ? ee()->config->item('link_vault_hidden_folder') : '';
		$this->override_settings['leech_url']       = ee()->config->item('link_vault_leech_url') ? ee()->config->item('link_vault_leech_url') : '';
		$this->override_settings['block_leeching']  = ee()->config->item('link_vault_block_leeching') != '' ? ee()->config->item('link_vault_block_leeching') : '';
		$this->override_settings['log_leeching']    = ee()->config->item('link_vault_log_leeching') != '' ? ee()->config->item('link_vault_log_leeching') : '';
		$this->override_settings['log_link_clicks'] = ee()->config->item('link_vault_log_link_clicks') != '' ? ee()->config->item('link_vault_log_link_clicks') : '';
		$this->override_settings['missing_url']     = ee()->config->item('link_vault_missing_url') ? ee()->config->item('link_vault_missing_url') : '';
		$this->override_settings['aws_access_key']  = ee()->config->item('link_vault_aws_access_key') ? ee()->config->item('link_vault_aws_access_key') : '';
		$this->override_settings['aws_secret_key']  = ee()->config->item('link_vault_aws_secret_key') ? ee()->config->item('link_vault_aws_secret_key') : '';
	}

	// ----------------------------------------------------------------

	/**
	 * This method stores the default Link Vault settings.
	 * @return void
	 */
	protected function _load_default_settings()
	{
		ee()->load->helper('string');

		$this->settings['site_id']			= ee()->config->item('site_id');
		$this->settings['salt']				= random_string('alnum', 12);
		$this->settings['hidden_folder']	= '';
		$this->settings['leech_url']		= $this->site_index;
		$this->settings['missing_url']		= $this->site_index;
		$this->settings['block_leeching']	= '0';
		$this->settings['log_leeching']		= '0';
		$this->settings['log_link_clicks']	= '0';
		$this->settings['aws_access_key']   = '';
		$this->settings['aws_secret_key']   = '';

		ee()->db->insert('link_vault_settings', $this->settings);
	}

	// ----------------------------------------------------------------

	/**
	 * This method fetches and array of all the defined Link Vault
	 * custom fields.
	 * @return array
	 */
	public function fetch_custom_fields()
	{
		ee()->db->order_by('field_id', 'asc');
		$query = ee()->db->get_where('link_vault_custom_fields', array(
			'site_id' => ee()->session->userdata('site_id')
		));
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}

	// ----------------------------------------------------------------

	/**
	 * This method returns an array of default values for the report
	 * form search criteria.
	 * @return array
	 */
	protected function _populate_default_report_values()
	{
		$default_values = array(
			'table'			=> 'downloads',
			'directory'		=> '',
			'pretty_url_id'	=> '',
			's3_bucket'		=> '',
			'file_name'		=> '',
			'member_id'		=> '',
			'start_date'	=> '',
			'end_date'		=> '',
			'order_by'		=> 'id',
			'sort'			=> 'asc',
			'limit'			=> '100',
			'is_link_click'	=> 'n'
		);

		foreach ($this->custom_fields_array as $field) {
			$default_values[ $field['field_name'] ] = '';
		}

		return $default_values;
	}

	// ----------------------------------------------------------------

	/**
	 * This method fetches the default values for a saved report from
	 * the DB and stores them in the default values array.
	 * @param integer $report_id
	 * @return array
	 */
	protected function _fetch_report_criteria($report_id='')
	{
		$values = array();

		// Don't do anything if no report_id is passed, but don't error out either
		if ($report_id != '') {
			$query = ee()->db->get_where('link_vault_reports', array('id' => $report_id));
			if ($query->num_rows() == 1) {
				ee()->load->library('javascript');

				// unserialize the criteria
				$values = unserialize( $query->row('criteria') );
			}
		}

		return $values;
	}

	// ----------------------------------------------------------------

	/**
	 * This method fetches all the saved report rows from the link_vault_reports
	 * table.
	 * @return array
	 */
	protected function _fetch_reports()
	{
		$query = ee()->db->where('site_id', ee()->config->item('site_id') )->order_by('title', 'asc')->get('link_vault_reports');
		return $query->result_array();
	}


	// ----------------------------------------------------------------

	/**
	 * Creates an array of dropdown options for the order_by select element.
	 * @return array
	 */
	protected function _order_by_options()
	{
		$options = array(
			'id'		=> lang('report_col_id'),
			'unix_time'	=> lang('report_col_time'),
			'member_id'	=> lang('report_col_member_id'),
		);
		// Add the custom fields to the order_by selector options
		foreach ($this->custom_fields_array as $field) {
			$options[ 'cf_'.$field['field_name'] ] = $field['field_label'];
		}

		return $options;
	}

	// ----------------------------------------------------------------

	/**
	 * Creates an array of dropdown options for the limit select element.
	 * @return array
	 */
	protected function _limit_options()
	{
		$limit_options = array();
		$limit = 50;
		$limit_counter = 1;
		while ($limit_counter <= 10) {
			$limit_options[$limit * $limit_counter] = $limit * $limit_counter;
			$limit_counter ++;
		}

		return $limit_options;
	}

	// ----------------------------------------------------------------

	/**
	 * This method queues a success alert message to be displayed on the next page
	 * load. The method used to do this depends on the version of EE installed.
	 * @param string $message
	 */
	public function success_alert($message='')
	{
		if ( $this->ee3 ) {
			ee('CP/Alert')->makeBanner('Success')->addToBody($message)->asSuccess()->defer();
		} else {
			ee()->session->set_flashdata('message_success', $message);
		}
	}

	// ----------------------------------------------------------------

	/**
	 * This method queues an error alert message to be displayed on the next page
	 * load. The method used to do this depends on the version of EE installed.
	 * @param string $message
	 */
	public function error_alert($message='')
	{
		if ( $this->ee3 ) {
			ee('CP/Alert')->makeBanner('Error')->addToBody($message)->asIssue()->defer();
		} else {
			ee()->session->set_flashdata('message_error', $message);
		}
	}

	// ----------------------------------------------------------------

	/**
	 * This method loads one of the add-ons CP views. The process for handling
	 * this varies based on the version of EE installed.
	 * @param string $view
	  * @param array $data
	 * @return string
	 */
	public function view($view='index', $data=array())
	{
		if ( $this->ee3 ) {
			$content = ee('View')->make('link_vault:link_vault_'.$view)->render($data);
		} else {
			$content = ee()->load->view('link_vault_'.$view, $data, true);
		}
		return $content;
	}

}

/* End of file mcp.link_vault.php */
