<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_menu_ext {

	var $settings = array();
	var $version = '1.0.0';
	

	function __construct($settings = '')
	{
		$this->settings = $settings;
	}
	
	
	function cp_js_end()
	{
		$name = 'low_reorder';
		$url = ee('CP/URL')->make('addons/settings/' . $name);

		$markup = '<li><a href="'.$url.'">Reorder</a></li>';

		$js = "$('.author-menu').append('$markup');";
		return $js;
	}


	function activate_extension()
	{		
		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'cp_js_end',
			'hook'		=> 'cp_js_end',
			'settings'	=> serialize($this->settings),
			'version'	=> $this->version,
			'enabled'	=> 'y'
		);

		ee()->db->insert('extensions', $data);			
	}	
	

	function disable_extension()
	{
		ee()->db->where('class', __CLASS__);
		ee()->db->delete('extensions');
	}


	function update_extension($version = '')
	{
		if(version_compare($version, $this->version) === 0)
		{
			return FALSE;
		}
		return TRUE;		
	}	
	

}