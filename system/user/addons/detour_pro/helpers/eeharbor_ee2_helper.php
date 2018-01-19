<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * EEHarbor helper class
 *
 * Bridges the functionality gaps between EE versions.
 *
 * @package         eeharbor_helper
 * @version         1.0
 * @author          Tom Jaeger <Tom@EEHarbor.com>
 * @link            https://eeharbor.com
 * @copyright       Copyright (c) 2016, Tom Jaeger/EEHarbor
 */

// --------------------------------------------------------------------

class Eeharbor_helper {

	private $_module;
	private $_module_name;
	private $_ee_major_version;
	private $app_settings;

	public function __construct($module, $module_name)
	{
		$this->_module = $module;
		$this->_module_name = $module_name;
		$this->_ee_major_version = substr(APP_VER, 0, 1);

		if(!function_exists('ee')) {
			function ee() {
				return get_instance();
			}
		}
	}

	public function getEEVersion($major=true)
	{
		if($major == true) return $this->_ee_major_version;
		else return APP_VER;
	}

	public function getBaseURL($method='', $extra='')
	{
		if($method == '/') $method = '';
		elseif($method) $method = AMP.'method='.$method;

		$url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->_module.$method.$extra;

		if (version_compare(APP_VER, '2.6.0', '>=') && version_compare(APP_VER, '2.7.3', '=<')) {
			// Yay, workaround for EE 2.6.0 session bug
			$config_type = 'admin_session_type';
		} else {
			$config_type = 'cp_session_type';
		}

		$s = 0;
		switch (ee()->config->item($config_type)){
			case 's':
				$s = ee()->session->userdata('session_id', 0);
				break;
			case 'cs':
				$s = ee()->session->userdata('fingerprint', 0);
				break;
		}

		// Test if our URL already has the session and directive.
		parse_str(parse_url(str_replace('&amp;', '&', $url), PHP_URL_QUERY), $url_test);

		if(!empty($s) && (!isset($url_test['S']) || empty($url_test['S']))) $url .= AMP.'S='.$s;
		if(!isset($url_test['D']) || empty($url_test['D'])) $url .= AMP.'D=cp';

		return $url;
	}

	public function getNav($nav_items=array())
	{
		foreach($nav_items as $title => $method) {
			if(strpos($method, 'http') === false) $method = $this->getBaseURL($method);

			$nav_items[$title] = $method;
		}

		ee()->cp->set_right_nav($nav_items);
	}

	public function cpURL($path, $mode='', $variables=array())
	{
		switch($path) {
			case 'publish':
				$path = 'content_publish';
				if($mode == 'create' || $mode == 'edit') $mode = 'entry_form';
				break;

			case 'members':
				if($mode == 'groups') $mode = 'member_group_manager';
				break;

			case 'channels':
				$path = 'admin_content';
				if($mode == 'create') $mode = 'channel_add';
				break;
		}

		$url = BASE.AMP.'D=cp'.AMP.'C='.$path.AMP.'M='.$mode;

		foreach ($variables as $variable => $value) {
			$url .= AMP . $variable . '=' . $value;
		}

		return $url;
	}

	public function moduleURL($method='index', $variables=array())
	{
		$url = $this->getBaseURL() . AMP . 'method=' . $method;

		foreach ($variables as $variable => $value) {
			$url .= AMP . $variable . '=' . $value;
		}

		return $url;
	}

	public function view($view, $vars = array(), $return = FALSE)
	{
		return ee()->load->view('ee'.$this->_ee_major_version.'_'.$view, $vars, $return);
	}

	public function getCurrentPage($options)
	{
		// If we have the per_page query variable, it's an offset.
		if(ee()->input->get('per_page', 1)) {
			$offset = (int) ee()->input->get('per_page', 1);
			return ($offset / $options['per_page']) + 1;
		} elseif(ee()->input->get('page', 1)) {
			return (int) ee()->input->get('page', 1);
		} else {
			return 1;
		}
	}

	public function getStartNum($options)
	{
		return ($options['current_page'] * $options['per_page']) - $options['per_page'];
	}

	public function pagination($options = array())
	{
		// Remap from normal logic to EE2 logic.
		if(isset($options['current_page'])) $options['cur_page'] = $options['current_page'];

		ee()->load->library('pagination');
		ee()->pagination->initialize($options);
		return ee()->pagination->create_links();
	}

	public function getSettings($asArray = false)
	{
		// EE caches the list of DB tables, so unset the table_names var if it's set
		// otherwise table_exists could return a false negative if it was just created.
		if(isset(ee()->db->data_cache['table_names'])) unset(ee()->db->data_cache['table_names']);

		if(ee()->db->table_exists($this->_module.'_settings')) {
			$dbSettings = ee()->db->get_where($this->_module.'_settings', array('site_id'=>ee()->config->item('site_id')))->row_array();
		} else {
			$dbSettings = array();
		}

		$addonSettings = require ee()->config->_config_paths[0].'addon.setup.php';

		$this->app_settings = (object) array_merge($dbSettings, $addonSettings);

		if($asArray) return array_merge($dbSettings, $addonSettings);

		return $this->app_settings;
	}

	public function getConfig($item)
	{
		return $this->app_settings->{$item};
	}

	public function setConfig($item, $value)
	{
		// EE caches the list of DB tables, so unset the table_names var if it's set
		// otherwise table_exists could return a false negative if it was just created.
		if(isset(ee()->db->data_cache['table_names'])) unset(ee()->db->data_cache['table_names']);

		// Make sure the settings table exists.
		if(ee()->db->table_exists($this->_module.'_settings')) {
			// Find out if the settings exist, if not, insert them.
			ee()->db->where('site_id', ee()->config->item('site_id'));
			$exists = ee()->db->count_all_results($this->_module.'_settings');

			$data['site_id'] = ee()->config->item('site_id');
			$data[$item] = $value;

			if($exists) {
				ee()->db->where('site_id', ee()->config->item('site_id'));
				ee()->db->update($this->_module.'_settings', $data);
			} else {
				ee()->db->insert($this->_module.'_settings', $data);
			}
		}
	}

	public function cache($mode, $key = false, $data = false) {
		if (! isset(ee()->session->cache[$this->_module]))
		{
		 	ee()->session->cache[$this->_module] = array();
		}

		// Returns EE's native cache function for EE2.
		switch($mode) {
			case 'get':
				if(isset(ee()->session->cache[$this->_module][$key])) return ee()->session->cache[$this->_module][$key];
				else return false;
				break;

			case 'set':
				return ee()->session->cache[$this->_module][$key] = $data;
				break;

			case 'delete':
			case 'clear':
				if($key) unset(ee()->session->cache[$this->_module][$key]);
				else unset(ee()->session->cache[$this->_module]);
				break;

			default:
				return false;
		}
	}
}