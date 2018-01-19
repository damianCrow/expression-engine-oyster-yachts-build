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
*	File select_fields.php
*
* 	This file is an attempt to cover P&T Fieldpack 
* 	fieldtypes that act similarly in terms of their
* 	data content and presentation in Zenbu.
*	
*/

class Fieldpack_select_fields extends Base
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
		
		$field_settings = unserialize(base64_decode($this->field_settings[$field_id]));
		$field_setting = $field_settings['options'];
		$field_data = explode("\n", $data);

		// Process options by checking for optgroups, 
		// which are removed. Options are then assembled together
		$f_options = $this->assemble_options($field_setting);

		foreach($field_data as $key => $value)
		{
			$output .= (isset($f_options[$value])) ? $f_options[$value].', ' : '';
		}
		
		$output = substr($output, 0, -2);
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

		if(isset($field_settings['options']))
		{
			$f_options = $this->assemble_options($field_settings['options']);		
		}
		
		if(isset($f_options))
		{
			// Get the keywords stored in db field from keyword based on label
			$keyword_in_db = "";
			foreach($f_options as $key => $val)
			{
				foreach($this->filters as $filter)
				{
					if(is_numeric($filter['1st']))
					{
						$keyword = $filter['3rd'];
						
						if(stripos($f_options[$key], $keyword) !== FALSE)
						{
							$keyword_in_db[] = $key;
						}
					}
				}
			}
			
			foreach($this->filters as $filter)
			{
				if(is_numeric($filter['1st']) && $filter['1st'] == $field_id)
				{
					$keyword = $filter['3rd'];
					if(empty($keyword))
					{
						return;
					}
					
					// Build query to get entries with or without the keyword stored in db field
					switch ($filter['2nd'])
					{
						case "contains" :
							if(empty($keyword_in_db))
							{
								// If the search keyword is not among the options,
								// make it so that no results are returned.
								$like_query = "entry_id = 0";
							} else {
								$like_query = implode($keyword_in_db, '%" OR field_id_'.$field_id.' LIKE "%');
								$like_query = 'field_id_'.$field_id.' LIKE "%'.$like_query.'%"';
							}
						break;
						case "doesnotcontain" :
							if( ! empty($keyword_in_db))
							{
								$like_query = implode($keyword_in_db, '%" AND field_id_'.$field_id.' NOT LIKE "%');
								$like_query = 'field_id_'.$field_id.' NOT LIKE "%'.$like_query.'%" OR field_id_'.$field_id.' IS NULL';
							} else {
								return;
							}
						break;
					}

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


	/**
	*	===================================
	*	function assemble_options
	*	===================================
	*	Remove optgroups from field option array
	*
	*	@param	$field_setting		array	Array of options, including optgroups 
	*	@return	$f_options 			array 	Cleaned up array of options, all on the same level
	*/
	private function assemble_options($field_setting)
	{
		$f_options = array();
		foreach($field_setting as $key => $val)
		{
			if(is_array($field_setting[$key]))
			{
				foreach($val as $k => $v)
				{
					$f_options[$k] = $v;
				}
				
			} else {
				$f_options[$key] = $val;
			}
		}
		return $f_options;
	}
	
	
} // END CLASS

/* End of file select_fields.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/select_fields.php */
?>