<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'detour_pro/eeharbor_foundation.php';

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Detour Pro Module Control Panel File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Mike Hughes - City Zen
 * @author      Tom Jaeger - EEHarbor
 * @link		http://eeharbor.com/detour_pro
 */

class Detour_pro_mcp {

	public $return_data;
	public $return_array = array();

	private $settings;
	private $_base_url;
	private $_data = array();
	private $_module = 'detour_pro';
	private $_detour_methods = array(
		'301' => '301',
		'302' => '302'
	);

	private $search = '';
	private $sort = '';
	private $sort_dir = 'asc';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->foundation = Eeharbor_foundation::registerModule('detour_pro', 'Detour Pro');
		$this->_base_url = $this->foundation->getBaseURL();
		$this->settings = $this->foundation->getSettings();

		if(version_compare(APP_VER, '2.6', '>=')) {
			ee()->view->cp_page_title = lang('detour_pro_module_name');
		} else {
			ee()->cp->set_variable('cp_page_title', lang('detour_pro_module_name'));
		}

		// For the EE2 version.
		$nav_items = array(
			lang('nav_home')       => '/',
			lang('nav_add_detour')    => 'advanced',
			lang('nav_settings')      => 'settings',
			lang('nav_purge')         => 'purge_hits',
			lang('nav_documentation') => 'https://eeharbor.com/detour-pro/documentation'
		);

		$this->foundation->getNav($nav_items);

		// For the EE3 version, setup the header area and settings cog.
		ee()->view->header = array(
			'title' => lang('detour_pro_module_name'),
			'toolbar_items' => array(
				'settings' => array(
					'href' => ee('CP/URL', 'addons/settings/detour_pro/settings'),
					'title' => lang('title_setting')
				)
			)
		);
	}

	// ----------------------------------------------------------------

	//! Index View and Save

	/**
	 * Index Function
	 *
	 * @return 	void
	 */
	public function index()
	{
/*		for($i=1; $i<1000; $i++){
			$data = array(
				'original_url'=>'start'.$i,
				'new_url'=>'redirect'.$i,
				'detour_method'=>301,
				'site_id'=>1
				);

			ee()->db->insert('detours', $data);
		}*/

		$ext = ee()->db->get_where('extensions', array('class' => 'Detour_pro_ext'))->row_array();

		if(!empty($ext) && $ext['enabled'] == 'n')
		{
			ee()->db->where('class', 'Detour_pro_ext');
			ee()->db->update('extensions', array('enabled'=>'y'));
		}

		ee()->load->library('table');

		if(isset($_GET['search']) && !empty($_GET['search'])) {
			$this->search = $_GET['search'];
		}

		if(isset($_GET['sort']) && !empty($_GET['sort'])) {
			$this->sort = $_GET['sort'];
		}

		if(isset($_GET['sort_dir']) && !empty($_GET['sort_dir'])) {
			if($_GET['sort_dir'] == 'desc') $this->sort_dir = 'desc';
			else $this->sort_dir = 'asc';
		}

		$this->_data['sort'] = $this->sort;
		$this->_data['sort_dir']['current'] = $this->sort_dir;

		$this->_data['sort_dir']['original_url'] = 'asc';
		$this->_data['sort_dir']['new_url'] = 'asc';
		$this->_data['sort_dir']['detour_method'] = 'asc';
		$this->_data['sort_dir']['site_id'] = 'asc';
		$this->_data['sort_dir']['start_date'] = 'asc';
		$this->_data['sort_dir']['end_date'] = 'asc';

		if($this->sort_dir == 'asc') $this->_data['sort_dir'][$this->sort] = 'desc';
		else $this->_data['sort_dir'][$this->sort] = 'asc';

		$this->_data['base_url'] = $this->foundation->getBaseURL('index');
		$this->_data['action_url'] = $this->_form_url('save_detour');

		$this->_data['detour_options'] = array(
			'detour' => ee()->lang->line('option_detour'),
			'ignore' => ee()->lang->line('option_ignore'),
		);

		$this->_data['detour_methods'] = $this->_detour_methods;
		$this->_data['total_detours'] = $this->count_detours();

		// Pagination
		$per_page = 100;
		$pagination_config['per_page'] = $per_page;
		$pagination_config['base_url'] = $this->foundation->getBaseURL('index', AMP.'sort='.$this->sort.AMP.'sort_dir='.$this->sort_dir);
		$pagination_config['page_query_string'] = TRUE;

		$pagination_config['total_rows'] = $this->count_detours();
		$pagination_config['current_page'] = $this->foundation->getCurrentPage($pagination_config);

		$start = $this->foundation->getStartNum($pagination_config);

		if(!$this->search) {
			$this->_data['pagination'] = $this->foundation->pagination($pagination_config);
		}

		$this->_data['current_detours'] = $this->get_detours(null, $start, $per_page);

		// If we're not on page 1 and there are no detours, redirect to page 1.
		if($pagination_config['current_page'] > 1 && count($this->_data['current_detours']) == 0) {
			ee()->functions->redirect($this->_base_url);
		}

		$this->skinSupport();
		return $this->foundation->view('index', $this->_data, TRUE);
	}

	public function save_detour()
	{
		// If they're searching for something, redirect to the index with the search query string.
		if(isset($_POST['search']) && !empty($_POST['search'])){
			ee()->functions->redirect($this->_base_url.AMP.'search='.$_POST['search']);
		}

		if(isset($_POST['original_url']) && isset($_POST['new_url'])) {
			// clean original url
			$original_url = trim($_POST['original_url'], '/');
			$new_url = trim($_POST['new_url'], '/');

			$start_date = (isset($_POST['start_date']) && !empty($_POST['start_date']) && !array_key_exists('clear_start_date', $_POST)) ? $_POST['start_date'] : null;
			$end_date = (isset($_POST['end_date']) && !empty($_POST['end_date']) && !array_key_exists('clear_end_date', $_POST)) ? $_POST['end_date'] : null;

			$data = array(
				'original_url' => $original_url,
				'new_url' => $new_url,
				'detour_method' => (isset($_POST['detour_method']) ? $_POST['detour_method'] : '301'),
				'site_id' => ee()->config->item('site_id'),
				'start_date' => $start_date,
				'end_date' => $end_date
			);

			ee()->db->insert('exp_detours', $data);
		}

		// If anything is set for deletion, delete it
		if(isset($_POST['detour_delete']) && !empty($_POST['detour_delete'])){
			foreach($_POST['detour_delete'] as $detour_id) {
				ee()->db->delete('detours', array('detour_id' => $detour_id));
				ee()->db->where('detour_id', $detour_id);
				ee()->db->delete('detours_hits');
			}
		}

		// Redirect back to Detour Pro landing page
		ee()->functions->redirect($this->_base_url);
	}

	//! Advanced View and Save

	public function advanced()
	{
		ee()->cp->add_js_script(
			array(
				'ui' => array(
					'core', 'datepicker'
				)
			)
		);

		ee()->javascript->output(array(
				'$( ".datepicker" ).datepicker({ dateFormat: \'yy-mm-dd\' });'
			)
		);

		$this->_data['id'] = ee()->input->get_post('id') ? ee()->input->get_post('id') : null;

		if(ee()->input->get_post('id'))
		{
			$detour = $this->get_detours(ee()->input->get_post('id'));
			ee()->db->select('COUNT(*) as total');
			$hits = ee()->db->get_where('detours_hits', array('detour_id' => $detour['detour_id']))->result_array();
		}


		$this->_data['original_url'] = (isset($detour)) ? $detour['original_url'] : '';
		$this->_data['new_url'] = (isset($detour)) ? $detour['new_url'] : '';
		$this->_data['detour_method'] = (isset($detour)) ? $detour['detour_method'] : '';
		$this->_data['detour_hits'] = (isset($detour)) ? $hits[0]['total'] : '';
		$this->_data['start_date'] = (isset($detour)) ? $detour['start_date'] : '';
		$this->_data['end_date'] = (isset($detour)) ? $detour['end_date'] : '';
		$this->_data['detour_methods'] = $this->_detour_methods;

		ee()->load->library('table');
		$this->_data['action_url'] = $this->_form_url('save_detour_advanced');

		$this->skinSupport();
		return $this->foundation->view('advanced', $this->_data, TRUE);
	}

	public function save_detour_advanced()
	{

		// clean original url
		$original_url = trim($_POST['original_url'], '/');
		$new_url = trim($_POST['new_url'], '/');

		$start_date = (isset($_POST['start_date']) && !empty($_POST['start_date']) && !array_key_exists('clear_start_date', $_POST)) ? $_POST['start_date'] : null;
		$end_date = (isset($_POST['end_date']) && !empty($_POST['end_date']) && !array_key_exists('clear_end_date', $_POST)) ? $_POST['end_date'] : null;

		$data = array(
			'original_url' => $original_url,
			'new_url' => $new_url,
			'detour_method' => isset($_POST['detour_method']) ? $_POST['detour_method'] : '301',
			'site_id' => ee()->config->item('site_id'),
			'start_date' => $start_date,
			'end_date' => $end_date
		);

		if(isset($_POST['original_url']) && isset($_POST['new_url']) && !array_key_exists('id', $_POST))
		{
			ee()->db->insert('detours', $data);
		}
		elseif(isset($_POST['original_url']) && isset($_POST['new_url']) && array_key_exists('id', $_POST) && $_POST['id'])
		{
			ee()->db->update('detours', $data, 'detour_id = ' . $_POST['id']);
		}

		// Redirect back to Detour Pro landing page
		ee()->functions->redirect($this->_base_url);
	}

	public function purge_hits()
	{
		$this->_data['total_detour_hits'] = ee()->db->count_all_results('detours_hits');
		$this->_data['action_url'] = $this->_form_url('do_purge_hits');

		$this->skinSupport();
		return $this->foundation->view('purge_hits', $this->_data, TRUE);
	}

	public function do_purge_hits()
	{
		ee()->db->empty_table('detours_hits');
		ee()->functions->redirect($this->_base_url);
	}

	// Settings View and Save

	public function settings()
	{
		ee()->load->library('table');
		$this->_data['action_url'] = $this->_form_url('save_settings');
		$this->_data['settings'] = $this->settings;

		$this->skinSupport();
		return $this->foundation->view('settings', $this->_data, TRUE);
	}

	public function save_settings()
	{
		$data = array();

		$data['site_id'] = ee()->config->item('site_id');
		$data['url_detect'] = ee()->input->post('url_detect', TRUE);
		$data['default_method'] = ee()->input->post('default_method', TRUE);
		$data['hit_counter'] = ee()->input->post('hit_counter', TRUE);

		// Find out if the settings exist, if not, insert them.
		ee()->db->where('site_id', ee()->config->item('site_id'));
		$exists = ee()->db->count_all_results('detour_pro_settings');

		if($exists) {
			ee()->db->where('site_id', ee()->config->item('site_id'));
			ee()->db->update('detour_pro_settings', $data);
		} else {
			ee()->db->insert('detour_pro_settings', $data);
		}

		// ----------------------------------
		//  Redirect to Settings page with Message
		// ----------------------------------
		ee()->functions->redirect($this->foundation->moduleURL('index', array('msg'=>'preferences_updated')));
		exit;
	}

	private function count_detours($id='')
	{
		$vars = array(
			'site_id' => ee()->config->item('site_id'),
		);

		if($id)
		{
			$vars['detour_id'] = $id;
		}

		if(!array_key_exists('detour_id', $vars))
		{

			ee()->db->select('*');
			ee()->db->select('DATE_FORMAT(start_date, \'%m/%d/%Y\') AS start_date', FALSE);
			ee()->db->select('DATE_FORMAT(end_date, \'%m/%d/%Y\') AS end_date', FALSE);

			if($this->search) {
				ee()->db->like('original_url', $this->search);
				ee()->db->or_like('new_url', $this->search);
			}

			$detour_count = ee()->db->count_all_results('detours');
		}
		else
		{
			ee()->db->where('site_id', $vars['site_id']);
			$detour_count = ee()->db->count_all_results('detours');
		}

		return $detour_count;
	}

	private function get_detours($id='', $start=0, $per_page=0)
	{
		$vars = array(
			'site_id' => ee()->config->item('site_id'),
		);

		if($id)
		{
			$vars['detour_id'] = $id;
		}

		if(!array_key_exists('detour_id', $vars))
		{

			ee()->db->select('*');
			ee()->db->select('DATE_FORMAT(start_date, \'%m/%d/%Y\') AS start_date', FALSE);
			ee()->db->select('DATE_FORMAT(end_date, \'%m/%d/%Y\') AS end_date', FALSE);

			if($this->search) {
				ee()->db->like('original_url', $this->search);
				ee()->db->or_like('new_url', $this->search);
			}

			if($this->sort) {
				ee()->db->order_by($this->sort, $this->sort_dir);
			}

			if($start > 0 || $per_page > 0) {
				ee()->db->limit($per_page, $start);
			}
			$current_detours = ee()->db->get_where('detours', $vars)->result_array();

			foreach($current_detours as $value)
			{
				extract($value);

				$this->return_array[] = array(
					'original_url' => $original_url,
					'new_url' => $new_url,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'detour_id' => $detour_id,
					'detour_method' => $detour_method,
					'advanced_link' => $this->foundation->moduleURL('advanced', array('id'=>$detour_id))
				);
			}
		}
		else
		{
			if($start > 0 || $per_page > 0) {
				ee()->db->limit($per_page, $start);
			}

			$this->return_array = ee()->db->get_where('detours', $vars)->row_array();
		}

		return $this->return_array;
	}

	//! Linking Methods

	private function _form_url ($method = 'index', $variables = array()) {
		if(substr(APP_VER, 0, 1) > 2) {
			$url = ee('CP/URL')->make('addons/settings/'.$this->_module.'/'.$method, $variables);
		} else {
			$url = 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=' . $this->_module . AMP . 'method=' . $method;

			foreach ($variables as $variable => $value) {
				$url .= AMP . $variable . '=' . $value;
			}
		}

		return $url;
	}

	private function _member_link ($member_id) {
		// if they are anonymous, they don't have a member link
		if (strpos($member_id,'anon') !== FALSE) {
			return FALSE;
		}

		$url = BASE . AMP . 'D=cp' . AMP . 'C=myaccount' . AMP . 'id='. $member_id;

		return $url;
	}

	private function skinSupport()
	{
		// To the theme skin... get it?

		if(!defined('URL_THIRD_THEMES')) {
			define('URL_THIRD_THEMES',	ee()->config->slash_item('theme_folder_url').'third_party/');
		}

		ee()->cp->add_to_head("<link rel='stylesheet' href='".URL_THIRD_THEMES."detour_pro/css/detour.css'>");

		/*ee()->cp->load_package_js('jquery.dataTables.min');
		ee()->cp->add_js_script(
			array(
				'ui' => array(
					'datepicker'
				)
			)
		);
		ee()->javascript->output(array(
			'$(".padTable").dataTable( {
				"iDisplayLength": 100
			} );',
			'$(".datepicker").datepicker({ dateFormat: \'yy-mm-dd\' });',
			'$("#original_url").focus();'
			)
		);*/
	}


}
/* End of file mcp.detour_pro.php */
/* Location: /system/expressionengine/third_party/detour_pro/mcp.detour_pro.php */