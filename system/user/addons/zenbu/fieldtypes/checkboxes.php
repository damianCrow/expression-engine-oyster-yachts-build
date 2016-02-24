<?php namespace Zenbu\fieldtypes;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Display;
use Zenbu\librairies\platform\ee\Files;
use Zenbu\librairies\platform\ee\Lang;
use Zenbu\librairies\platform\ee\Request;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*	ZENBU THIRD-PARTY FIELDTYPE SUPPORT
*	============================================
*	Standard Checkbox field
*	@author	EllisLab
*	============================================
*	File checkboxes.php
*
*/

class Zenbu_checkboxes_ft extends Base
{
	/**
	*	Constructor
	*
	*	@access	public
	*/
	public function __construct()
	{
		$this->display = new Display();
		$this->filters = Request::param('filter');
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
	public function zenbu_display($entry_id, $channel_id, $data, $table_data = array(), $field_id, $settings, $rules = array())
	{
		// Display data using the provided display_text helper function, which includes highlighting function
		$output = (empty($data)) ? NBS : '';
		if(empty($data))
		{
			return $output;
		}

		$selected_items = explode('|', $data);
		foreach($selected_items as $key => $string)
		{
			$output .= $this->display->text($string, $field_id) . ', ';
		}
		$output = substr($output, 0, -2);

		// Output
		return $output;
	}


} // END CLASS

/* End of file checkboxes.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/checkboxes.php */
?>