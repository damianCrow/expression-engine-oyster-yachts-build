<?php namespace Zenbu\fieldtypes;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Display;
use Zenbu\librairies\platform\ee\Request;
use Zenbu\fieldtypes\fieldpack as Fieldpack;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*	ZENBU THIRD-PARTY FIELDTYPE SUPPORT
*	============================================
*	Pixel&Tonic's Field Pack Dropdown field
*	@author	Pixel&tonic http://pixelandtonic.com
*	@link	http://pixelandtonic.com/ee
*	============================================
*	File fieldpack_dropdown.php
*
*/

class Zenbu_fieldpack_dropdown_ft extends Base
{
	var $dropdown_type = "contains_doesnotcontain";

	/**
	*	Constructor
	*
	*	@access	public
	*/
	public function __construct()
	{
		parent::init(array('settings', 'fields'));

		$this->zenbu_class = new Fieldpack\Fieldpack_select_fields();
	}

	/**
	*	======================
	*	function zenbu_display
	*	======================
	*	Set up display in entry result cell
	*
	*	@param	$entry_id			int		The entry ID of this single result entry
	*	@param	$channel_id			int		The channel ID associated to this single result entry
	*	@param	$data				array	Raw data as found in database cell in exp_channel_data
	*	@param	$table_data			array	Data array usually retrieved from other table than exp_channel_data
	*	@param	$field_id			int		The ID of this field
	*	@param	$settings			array	The settings array, containing saved field order, display, extra options etc settings
	*	@param	$rules				array	An array of entry filtering rules
	*	@param	$upload_prefs		array	An array of upload preferences (optional)
	*	@param 	$installed_addons	array	An array of installed addons and their version numbers (optional)
	*	@param	$fieldtypes			array	Fieldtype of available fieldtypes: id, name, etc (optional)
	*	@return	$output		The HTML used to display data
	*/
	public function zenbu_display($entry_id, $channel_id, $data, $table_data = array(), $field_id, $settings, $rules = array(), $upload_prefs = array(), $installed_addons, $fieldtypes)
	{
		return $this->zenbu_class->zenbu_display($data, $field_id);
	}

	/**
	*	===================================
	*	function zenbu_result_query
	*	===================================
	*	Extra queries to be intergrated into main entry result query
	*
	*	@param	$rules				int		An array of entry filtering rules
	*	@param	$field_id			array	The ID of this field
	*	@param	$fieldtypes			array	$fieldtype data
	*	@param	$already_queried	bool	Used to avoid using a FROM statement for the same field twice
	*	@param	$installed_addons	array	An array of installed addons and their version numbers (optional)
	*	@return					A query to be integrated with entry results. Should be in CI Active Record format ($this->EE->db->…)
	*/
	public function zenbu_result_query($field_id = "")
	{
		$this->zenbu_class->zenbu_result_query($field_id);
	}


} // END CLASS

/* End of file fieldpack_dropdown.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/fieldpack_dropdown.php */
?>