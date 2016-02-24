<?php namespace Zenbu\fieldtypes\fieldpack;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Display;
use Zenbu\librairies\platform\ee\Request;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*	ZENBU THIRD-PARTY FIELDTYPE SUPPORT
*	============================================
*	Pixel&Tonic's Field Pack fields
*	@author	Pixel&tonic http://pixelandtonic.com
*	@link	http://pixelandtonic.com/ee
*	============================================
*	File list_fields.php
*
* 	This file is an attempt to cover P&T Fieldpack 
* 	fieldtypes that act similarly in terms of their
* 	data content and presentation in Zenbu.
*	
*/

class Fieldpack_list_fields extends Base
{
	/**
	*	Constructor
	*
	*	@access	public
	*/
	public function __construct()
	{
		parent::init(array('settings', 'fields'));
		$this->display = new Display();
		$this->filters = Request::param('filter');
	}
	
	/**
	*	======================
	*	function zenbu_display
	*	======================
	*	Set up display in entry result cell
	*
	*	@param	$data				array	Raw data as found in database cell in exp_channel_data
	*	@param	$field_id			int		The ID of this field
	*	@return	$output		The HTML used to display data
	*/
	function zenbu_display($data, $field_id)
	{
		$output = (empty($data)) ? NBS : '';
		
		if(empty($data))
		{
			return $output;
		}
				
		$field_data = explode("\n", $data);
		$output = '<ul>';
		foreach($field_data as $key => $value)
		{
			$output .= '<li>'.$this->display->text($value, $field_id).'</li>';
		}
		$output .= '</ul>';
		
		return $output;
	}

	
} // END CLASS

/* End of file list_fields.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/list_fields.php */
?>