<?php namespace Zenbu\fieldtypes;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Display;
use Zenbu\librairies\platform\ee\Request;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*	ZENBU THIRD-PARTY FIELDTYPE SUPPORT
*	============================================
*	CartThrob field
*	@author	CartThrob Team
*	@link	http://cartthrob.com/
*	============================================
*	File cartthrob_price_modifiers.php
*
*/

class Zenbu_cartthrob_price_modifiers_ft extends Base
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
		$this->display = new Display();
		$this->filters = Request::param('filter');
		ee()->lang->loadfile('cartthrob');
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
	*	@param	$filters				array	An array of entry filtering rules
	*	@param	$upload_prefs		array	An array of upload preferences (optional)
	*	@param 	$installed_addons	array	An array of installed addons and their version numbers (optional)
	*	@param	$fieldtypes			array	Fieldtype of available fieldtypes: id, name, etc (optional)
	*	@return	$output		The HTML used to display data
	*/
	public function zenbu_display($entry_id, $channel_id, $data, $table_data = array(), $field_id, $settings, $filters = array(), $upload_prefs = array(), $installed_addons)
	{

		$output = '<table class="mainTable matrixTable width="" cellspacing="0" cellpadding="0" border="0"">';

		$data = unserialize(base64_decode($data));

		if(empty($data))
		{
			return '&nbsp;';
		}

		foreach ($data as $key => $row)
		{
			if($key == 0)
			{
				$output .= '<tr>';
				foreach($row as $key => $info)
				{
					$output .= '<th>'.ee()->lang->line($key).'</th>';
				}
				$output .= '</tr>';
			}
			$output .= '<tr>';
			foreach($row as $key => $info)
			{
				$output .= '<td>'.$info.'</td>';
			}
			$output .= '</tr>';
		}

		$output .= '</table>';

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
	public function zenbu_result_query($field_id = "")
	{
		// Uncomment the below line if you want to disable CT deep searching
		// return;
		if(empty($field_id))
		{
			return;
		}

		/**
		*	Data is stored as base64-encoded data
		*	Fetch entries that have CT data and create an array
		*	with base64-decoded data. Then search in that serialized string
		*/
		$query = ee()->db->query("/* Zenbu: CartThrob keyword search */ \n SELECT entry_id, field_id_" . $field_id . " FROM exp_channel_data WHERE field_id_" . $field_id . " IS NOT NULL AND field_id_" . $field_id . " != ''");

		if($query->num_rows() > 0)
		{
			foreach($query->result_array() as $row)
			{
				$ct_data[$row['entry_id']] = base64_decode($row['field_id_' . $field_id]);
			}
		}

		$query->free_result();

		/**
		*	Search in serialized strings from found entries above
		*/
		foreach($this->filters as $filter)
		{
			$filter_field_id = is_numeric($filter['1st']) ? $filter['1st'] : 0;
			if($filter_field_id == $field_id)
			{
				$keyword = isset($filter['3rd']) ? $filter['3rd'] : '';
				$cond	= isset($filter['2nd']) ? $filter['2nd'] : 'contains';
				foreach($ct_data as $entry_id => $ct_string)
				{
					if(stripos($ct_string, $keyword) !== FALSE)
					{
						$where_in_entries[] = $entry_id;
					}

				}

				if(isset($where_in_entries))
				{
					if($cond == "contains")
					{
						ee()->db->where_in("exp_channel_titles.entry_id", $where_in_entries);
					} elseif($cond == "doesnotcontain") {
						ee()->db->where_not_in("exp_channel_titles.entry_id", $where_in_entries);
					}
				}

				/**
				*	Handling no matches situations
				*/
				if( ! empty($keyword) && ! isset($where_in_entries) && $cond == 'contains')
				{
					ee()->db->where("exp_channel_titles.entry_id", 0);
				}

			}
		}

	}


} // END CLASS

/* End of file matrix.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/cartthrob_price_modifiers.php */
?>