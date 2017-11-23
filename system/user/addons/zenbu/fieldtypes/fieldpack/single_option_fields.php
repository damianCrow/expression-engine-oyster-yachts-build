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
*	File single_option_fields.php
*
* 	This file is an attempt to cover P&T Fieldpack 
* 	fieldtypes that act similarly in terms of their
* 	data content and presentation in Zenbu.
*	
*/

class Fieldpack_single_option_fields extends Base
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
		$field_setting = $field_settings['options'];
		$field_data = explode("\n", $data);

		foreach($field_data as $key => $value)
		{
			$output .= (isset($field_setting[$value])) ? $field_setting[$value].', ' : '';
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
	*	@return					A query to be integrated with entry results. Should be in CI Active Record format (ee()->db->…)
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
			foreach($this->filters as $filter)
			{
				if(is_numeric($filter['1st']) && $filter['1st'] == $field_id)
				{
					
					$keyword_in_db = "";
					$keyword = $filter['3rd'];

					// No need to run any of this if keyword is empty

					if(empty($keyword))
					{
						return;
					}

					// Get the keywords stored in db field, based on label

					foreach($field_settings['options'] as $key => $val)
					{
						if(stripos($field_settings['options'][$key], $keyword) !== FALSE)
						{
							$keyword_in_db[] = $key;
						}
					}
					
					// Build query to get entries with or without the keyword stored in db field
					
					switch ($filter['2nd'])
					{
						case "contains" :
							if(empty($keyword_in_db))
							{
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

					//	Run the final query that will be added to Zenbu
					
					if( isset($like_query) )
					{
						$query = ee()->db->query("/* fieldpack_pill.php, zenbu_result_query() */ SELECT entry_id FROM exp_channel_data WHERE ".$like_query);
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

						unset($entries);
					}

				}

			} // foreach($rules as $rule)
						
		}
		
		
	}
	
	
} // END CLASS

/* End of file single_option_fields.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/single_option_fields.php */
?>