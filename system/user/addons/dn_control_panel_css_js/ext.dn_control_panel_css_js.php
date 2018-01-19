<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Digi Nut Control Panel CSS & JS extension class
 *
 * @package		dn_control_panel_css_js
 * @version		1.0.0
 * @author		Digi Nut Ltd
 * @link		http://www.diginut.co.uk
 * @copyright	Copyright (c) 2016, Digi Nut Ltd
 * @license     http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
*/

class Dn_control_panel_css_js_ext
{

	public $name           = 'Digi Nut Control Panel CSS & JS';
	public $description    = "Customise the ExpressionEngine control panel by adding your own CSS and JavaScript. Based on Pixel & Tonic's \"CP CSS & JS\" extension for ExpressionEngine 2";
	public $version        = '1.0.0';
	public $settings_exist = 'y';
	public $settings       = array();
	public $docs_url       = '';

	/**
	 * Extension hooks
	 *
	 * @var array
	 */
	private $hooks = array(
		'cp_css_end',
		'cp_js_end',
	);

	/**
	 * Constructor
	 *
	 * @param	array
	 * @return  void
	 */
	function __construct($settings = '')
	{
		$this->settings = $settings;
	}

	/**
	 * Activate extension
	 *
	 * @return  void
	 */
	function activate_extension()
	{
		if (version_compare(APP_VER, '2.6', '<'))
		{
			return; // Need v2.6 or above because we're using the ee() function
		}

		foreach ($this->hooks AS $hook)
		{
			$this->_add_hook($hook);
		}
	}

	/**
	 * Update the extension
	 *
	 * @param	string
	 * @return	bool
	 */
	function update_extension($current = '')
	{
		return FALSE;
	}

	/**
	 * Disable the extension
	 *
	 * @return	void
	 */
	function disable_extension()
	{
		// Remove references from extensions
		ee()->db->where('class', __CLASS__);
		ee()->db->delete('extensions');

		return TRUE;
	}

	/**
	 * Abstract settings form
	 *
	 * @link https://docs.expressionengine.com/latest/development/extensions.html#settings
	 * @return	void
	 */
	function settings()
	{
		$settings = array(
			'control_panel_css' => array('t', array('rows' => '15'), ''),
			'control_panel_js'  => array('t', array('rows' => '15'), ''),
		);

		return $settings;
	}

	/**
	 * Add hook to exp_extensions table
	 *
	 * @param	string
	 * @return	void
	 */
	private function _add_hook($hook)
	{
		ee()->db->insert('extensions', array(
			'class'    => __CLASS__,
			'method'   => $hook,
			'hook'     => $hook,
			'settings' => '',
			'priority' => 10,
			'version'  => $this->version,
			'enabled'  => 'y'
		));
	}

	/**
	 * Append the CSS or JS script to the 'cp_css_end' or 'cp_js_end' hook data
	 *
	 * @param	string $data
	 * @param	string $type
	 * @return	string
	 */
	private function append_script($data, $type)
	{
		if (ee()->extensions->last_call !== FALSE)
		{
			$data = ee()->extensions->last_call;
		}
		
		$this->load_settings();

		$data .= NL . $this->settings["control_panel_$type"];

		return $data;
	}
	
	/**
	 * Get the extension settings from the database if the current settings for this class are empty.
	 * EE saves its extension settings only to the first method called from our list of hooks (cp_css_end), so subsequent methods have empty settings
	 * 
	 * @return	void
	 */
	private function load_settings()
	{
		if (empty($this->settings))
		{
			$query = ee()->db->select('settings')
				->where('class', __CLASS__)
				->where('settings !=', '')
				->limit(1)
				->get('extensions');

			$this->settings = unserialize($query->row('settings'));
		}
	}

	// --------------------------------------------------------------------
	// HOOKS
	// --------------------------------------------------------------------

	/**
	 * Hook: cp_css_end
	 *
	 * @param	string $data
	 * @return	string
	 */
	public function cp_css_end($data)
	{
		return $this->append_script($data, 'css');
	}

	/**
	 * Hook: cp_js_end
	 *
	 * @param	string $data
	 * @return	string
	 */
	public function cp_js_end($data)
	{
		return $this->append_script($data, 'js');
	}

}