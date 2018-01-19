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

	/**
	 * Foundation function
	 * Determines EE version to:
	 *  - Set up right nav or sidebar
	 *  - Determine base_url from raw or CP/URL service
	 *  - Set view folder
	 * Includes any JS or CSS from themes.
	 */
	public function __construct($module, $module_name)
	{
		$this->_module = $module;
		$this->_module_name = $module_name;
		$this->_ee_major_version = substr(APP_VER, 0, 1);
	}

	public function getEEVersion($major=true)
	{
		if($major == true) return $this->_ee_major_version;
		else return APP_VER;
	}

	public function getBaseURL($method='', $extra='')
	{
		if($method == '/') $method = '';
		elseif($method) $method = '/'.$method;

		return ee('CP/URL', 'addons/settings/'.$this->_module.$method.$extra);
	}

	public function getNav($nav_items=array())
	{
		$sidebar = ee('CP/Sidebar')->make();
		$last_segment = ee()->uri->segment_array();
		$last_segment = end($last_segment);

		foreach($nav_items as $title => $method) {
			if($method == '/') $method = 'index';

			if(strpos($method, 'http') === false) $url = $this->getBaseURL($method);
			else $url = $method;

			$nav_items[$title] = $sidebar->addHeader($title)->withUrl($url);
			if($last_segment == $method || ($method == 'index' && $last_segment == $this->_module)) $nav_items[$title]->isActive();
		}
	}

	public function cpURL($path, $mode='', $variables=array())
	{
		if($mode) $mode = '/'.$mode;

		if($path == 'publish') {
			if($mode == '/create' && isset($variables['channel_id'])) {
				$mode .= '/'.$variables['channel_id'];
				unset($variables['channel_id']);
			} elseif($mode == '/edit' && isset($variables['entry_id'])) {
				$mode .= '/entry/'.$variables['entry_id'];
				unset($variables['entry_id']);
			}
		}

		$url = ee('CP/URL')->make($path.$mode, $variables);

		return $url;
	}

	public function moduleURL($method='index', $variables=array())
	{
		$url = ee('CP/URL')->make('addons/settings/'.$this->_module.'/'.$method, $variables);

		return $url;
	}

	public function view($view, $vars = array(), $return = FALSE)
	{
		if(!isset($vars['base_url'])) $vars['base_url'] = $this->getBaseURL();
		if(!isset($vars['cp_page_title'])) $vars['cp_page_title'] = ee()->view->cp_page_title;

		return array(
			'heading' => ee()->view->cp_page_title,
			'breadcrumb' => array(
				ee('CP/URL', 'addons/settings/'.$this->_module.'/')->compile() => $this->_module
				),
			'body' => ee('View')->make($this->_module.':ee'.$this->_ee_major_version.'_'.$view)->render($vars)
		);
		//return ee()->load->view('ee'.$this->_ee_major_version.'_'.$view, $vars, $return);
	}

	public function getCurrentPage()
	{
		if(ee()->input->get('per_page', 1)) return ee()->input->get('per_page', 1);
		elseif(ee()->input->get('page', 1)) return ee()->input->get('page', 1);
		else return 1;
	}

	public function getStartNum($options)
	{
		return ($options['current_page'] * $options['per_page']) - $options['per_page'];
	}

	public function pagination($options = array())
	{
		$pagination = ee('CP/Pagination', $options['total_rows'])
						->perPage($options['per_page'])
						->currentPage($options['current_page'])
						->render($options['base_url']);

		return $pagination;
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
		// Returns EE's native cache function for EE3.
		switch($mode) {
			case 'get':
				return ee()->cache->get('/'.$this->_module.'/'.$key);
				break;

			case 'set':
				return ee()->cache->save('/'.$this->_module.'/'.$key, $data);
				break;

			case 'delete':
			case 'clear':
				return ee()->cache->delete('/'.$this->_module.'/'.$key);
				break;

			default:
				return false;
		}
	}
}