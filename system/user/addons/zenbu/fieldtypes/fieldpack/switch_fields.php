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
*	File switch_fields.php
*
* 	This file is an attempt to cover P&T Fieldpack 
* 	fieldtypes that act similarly in terms of their
* 	data content and presentation in Zenbu.
*	
*/

class Fieldpack_switch_fields extends Base
{
	/**
	*	Constructor
	*
	*	@access	public
	*/
	function __construct()
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
		
		$field_settings = unserialize(base64_decode($this->field_settings[$field_id]));
		
		//$field_settings = $fieldtypes['settings'][$field_id];
		
		if($field_settings['on_val'] == $data)
		{
			$output .= $field_settings['on_label'];
		} else {
			$output .= $field_settings['off_label'];
		}
		
		$output = $this->display->text($output, $field_id);

		return $output;
	}
	
	/**
	*	===================================
	*	function zenbu_result_query
	*	===================================
	*	Extra queries to be intergrated into main entry result query
	*
	*	@param	$field_id			array	The ID of this field
	*	@return					A query to be integrated with entry results. Should be in CI Active Record format ($this->EE->db->…)
	*/
	function zenbu_result_query($field_id = "")
	{
		if(empty($field_id))
		{
			return;
		}
		
		$field_settings = unserialize(base64_decode($this->field_settings[$field_id]));
		
		// Get the keywords stored in db field from keyword based on label
		foreach($this->filters as $filter)
		{
			if(is_numeric($filter['1st']) && $filter['1st'] == $field_id)
			{
				$keyword = $filter['3rd'];
				$keyword_in_db = "";
				if(stripos($field_settings['off_label'], $keyword) !== FALSE)
				{		
					$keyword_in_db = $field_settings['off_val'];	
				} elseif(stripos($field_settings['on_label'], $keyword) !== FALSE) {
					$keyword_in_db = $field_settings['on_val'];
				}
	
				// Build query to get entries with or without the keyword stored in db field	
				switch ($filter['2nd'])
				{
					case "contains" :
						if(empty($keyword_in_db))
						{
							if(empty($keyword))
							{
								return;
							} else {
								$like_query = 'field_id_'.$field_id.' LIKE "%'.$this->EE->db->escape_like_str($keyword).'%"';
							}
						} else {
							$like_query = 'field_id_'.$field_id.' LIKE "%'.$this->EE->db->escape_like_str($keyword_in_db).'%"';
						}
					break;
					case "doesnotcontain" :
						if(empty($keyword_in_db))
						{
							return;
						} else {
							$like_query = 'field_id_'.$field_id.' NOT LIKE "%'.$this->EE->db->escape_like_str($keyword_in_db).'%" OR field_id_'.$field_id.' IS NULL';
						}
					break;
				}
			}
		}
		
		$query = ee()->db->query("SELECT entry_id FROM exp_channel_data WHERE ".$like_query);
		if($query->num_rows() > 0)
		{
			foreach($query->result_array() as $row)
			{
				$entries[] = $row['entry_id'];
			}
		} else {
			$entries[] = 0;
		}
		
		// Filter by entry IDs within the above results
		ee()->db->where_in("exp_channel_titles.entry_id", $entries);
	}
	
	
} // END CLASS

/* End of file switch_fields.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/switch_fields.php */
?>