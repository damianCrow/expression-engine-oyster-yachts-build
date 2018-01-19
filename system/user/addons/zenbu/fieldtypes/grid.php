<?php namespace Zenbu\fieldtypes;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Display;
use Zenbu\librairies\platform\ee\Request;
use Zenbu\librairies\platform\ee\Files;
use Zenbu\librairies\platform\ee\View;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*	ZENBU THIRD-PARTY FIELDTYPE SUPPORT
*	============================================
*	Grid field
*	@author	EllisLab
*	============================================
*	File grid.php
*
*/

class Zenbu_grid_ft extends Base
{
	var $dropdown_type = "contains_doesnotcontain";

	/**
	*	Constructor
	*
	*	@access	public
	*/
	public function __construct()
	{
		$this->display = new Display();
		$this->filters = Request::param('filter');
		parent::__construct();
		parent::init(array('settings', 'fields'));
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
	public function zenbu_display($entry_id, $channel_id, $data, $grid_data = array(), $field_id, $settings, $rules = array(), $upload_prefs = array(), $installed_addons)
	{
		$output = NBS;

		// If grid_data is empty (no results), stop here and return a space for the field data
		if( ! isset($grid_data['table_data']['entry_id_'.$entry_id]['field_id_'.$field_id]))
		{
			return $output;
		}

		//	----------------------------------------
		//	Rewrite Grid data array with Zenbu-formatted data
		//	instead of raw Grid DB data
		//	----------------------------------------
		foreach($grid_data['table_data']['entry_id_'.$entry_id]['field_id_'.$field_id] as $row => $col)
		{
			foreach($col as $col_id => $data)
			{
				$fieldtype = $grid_data['headers']['field_id_'.$field_id][$col_id]->fieldtype;

				//	----------------------------------------
				// 	Load Zenbu Fieldtype Class
				//	----------------------------------------
				$ft_object = $this->fields->loadFieldtypeClass($fieldtype);

				//	----------------------------------------
				//	Run the zenbu_display method, if it exists
				//	This should convert the raw data into Zenbu data
				//	----------------------------------------
				
				// Some stuff we need for zenbu_display
				//$settings            = ee()->zenbu_get->_get_settings();
				$rules               = array();
				$output_upload_prefs = array();//ee()->zenbu_get->_get_file_upload_prefs();
				$installed_addons    = array();//ee()->zenbu_get->_get_installed_addons();
				$fieldtypes          = $this->fieldtypes;//ee()->zenbu_get->_get_field_ids();

				//	----------------------------------------
				//	Get relationship data, if present
				//	----------------------------------------

				// Send grid_id_X array key to signal the
				// relationship field that this in a grid
				$rel_array = array( $entry_id => array( 'grid_id_'.$field_id => $data ) );
				$table_data 		= $ft_object && method_exists($ft_object, 'zenbu_get_table_data') ? $ft_object->zenbu_get_table_data(array($entry_id), array($field_id), $channel_id, $output_upload_prefs, $settings, $rel_array) : array();
				$table_data['grid_row'] = substr($row, 4);
				$table_data['grid_col'] = substr($col_id, 7);

				//	----------------------------------------
				// 	Convert the data
				//	----------------------------------------
				$data	= $ft_object && method_exists($ft_object, 'zenbu_display') ? $ft_object->zenbu_display($entry_id, $channel_id, $data, $table_data, $field_id, $settings, $rules, $output_upload_prefs, $installed_addons, $fieldtypes) : $data;

				$vars['table_data']['entry_id_'.$entry_id]['field_id_'.$field_id][$row][$col_id] = $data;

			}
		}

		$vars['field_id'] = $field_id;
		$vars['entry_id'] = $entry_id;
		$vars['settings']   = $settings;
		$vars['headers']    = $grid_data['headers']['field_id_'.$field_id];
		$vars['table_data'] = $vars['table_data']['entry_id_'.$entry_id]['field_id_'.$field_id];

		return View::render('columns/fieldtypes/grid.twig', $vars);
	}


	/**
	*	=============================
	*	function zenbu_get_table_data
	*	=============================
	*	Retrieve data stored in other database tables
	*	based on results from Zenbu's entry list
	*	@uses	Instead of many small queries, this function can be used to carry out
	*			a single query of data to be later processed by the zenbu_display() method
	*
	*	@param	$entry_ids				array	An array of entry IDs from Zenbu's entry listing results
	*	@param	$field_ids				array	An array of field IDs tied to/associated with result entries
	*	@param	$channel_id				int		The ID of the channel in which Zenbu searched entries (0 = "All channels")
	*	@param	$output_upload_prefs	array	An array of upload preferences
	*	@param	$settings				array	The settings array, containing saved field order, display, extra options etc settings (optional)
	*	@param	$rel_array				array	A simple array useful when using related entry-type fields (optional)
	*	@return	$output					array	An array of data (typically broken down by entry_id then field_id) that can be used and processed by the zenbu_display() method
	*/
	public function zenbu_get_table_data($entry_ids, $field_ids, $channel_id)
	{
		$output = array();
		if( empty($entry_ids) || empty($field_ids))
		{
			return $output;
		}

		//	----------------------------------------
		//	Get grid field data as an array
		//	----------------------------------------

		//	----------------------------------------
		//	First, get field_ids that are grid fields
		//	----------------------------------------

		if(ee()->session->cache('zenbu', 'grid_field_ids'))
		{
			$field_ids = ee()->session->cache('zenbu', 'grid_field_ids');

		} else {

			$sql = "SHOW TABLES LIKE '%grid_field_%'";

			$query = ee()->db->query($sql);

			$grid_field_ids = array();

			if($query->num_rows() > 0)
			{
				foreach($query->result_array() as $row)
				{
					foreach($row as $val => $table_name)
					{
						preg_match('/(\d+)$/', $table_name, $matches);
						$grid_field_ids[] = $matches[1];
					}
				}
			}

			// Make a new field_ids array, which will only be grid_field_ids
			$field_ids = array_intersect($grid_field_ids, $field_ids);

			ee()->session->set_cache('zenbu', 'grid_field_ids', $field_ids);

		}

		// If the new field_ids array winds up empty, get out.
		if( empty($field_ids))
		{
			return $output;
		}

		//	----------------------------------------
		//	Since each Grid field has its own table, to avoid a mess
		//	of clashing column names, query each table individually.
		//	----------------------------------------

		$sql = '';
		$table_data		= array();

		foreach($field_ids as $field_id)
		{
			if(ee()->session->cache('zenbu', 'grid_table_data_field_'.$field_id))
			{
				$table_data	= ee()->session->cache('zenbu', 'grid_table_data_field_'.$field_id);

			} else {

				$sql = "/* Zenbu grid.php " . __FUNCTION__ ." field_id_" . $field_id. " */
						SELECT *, gc.col_id AS id, gc.col_label AS label, gc.col_type AS fieldtype 
						FROM exp_channel_grid_field_" . $field_id . " gf, exp_grid_columns gc
						WHERE gc.field_id = " . $field_id . "
						AND gf.entry_id IN (" . implode(', ', $entry_ids) . ")
						ORDER BY gf.entry_id ASC, gf.row_order ASC, gc.col_order ASC";

				$query = ee()->db->query($sql);

				$headers_array	= array();

				if($query->num_rows() > 0)
				{
					foreach($query->result() as $row)
					{
						$headers_array[$row->id] = $row;
						$table_data['headers']['field_id_'.$row->field_id]['col_id_'.$row->id] = $row;
					}

					foreach($query->result() as $row)
					{
						foreach($headers_array as $col_id => $col_data)
						{
							$table_data['table_data']['entry_id_'.$row->entry_id]['field_id_'.$field_id]['row_'.$row->row_id]['col_id_'.$col_id] = $row->{'col_id_'.$col_id};
						}
					}
				}

				//ee()->session->set_cache('zenbu', 'grid_headers_field_'.$field_id, $headers);
				ee()->session->set_cache('zenbu', 'grid_table_data_field_'.$field_id, $table_data);
			}
		}

		return $table_data;

	}

	/**
	*	===================================
	*	function zenbu_field_extra_settings
	*	===================================
	*	Set up display for this fieldtype in "display settings"
	*
	*	@param	$table_col			string	A Zenbu table column name to be used for settings and input field labels
	*	@param	$channel_id			int		The channel ID for this field
	*	@param	$extra_options		array	The Zenbu field settings, used to retieve pre-saved data
	*	@return	$output		The HTML used to display setting fields
	*/
	public function zenbu_field_extra_settings($table_col, $channel_id, $extra_options)
	{
		$extra_option_1 = (isset($extra_options['grid_option_1'])) ? TRUE : FALSE;
		$output['grid_option_1'] = form_label(form_checkbox('settings['.$channel_id.']['.$table_col.'][grid_option_1]', 'y', $extra_option_1).'&nbsp;'.ee()->lang->line('show_').ee()->lang->line('grid').ee()->lang->line('_in_row'));
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

		if( ! $this->filters || empty($field_id))
		{
			return;
		}

		if( ! $this->session->getCache('already_queried_grid') )
		{
			ee()->db->from("exp_channel_grid_field_".$field_id);
		}

		//ee()->db->where("exp_grid_data.field_id", $field_id);
		$col_query = ee()->db->query("/* Zenbu: Show columns for Grid */\nSHOW COLUMNS FROM exp_channel_grid_field_".$field_id);
		$concat = "";
		$where_in = array();

		if($col_query->num_rows() > 0)
		{
			foreach($col_query->result_array() as $row)
			{
				if(strchr($row['Field'], 'col_id_') !== FALSE)
				{
					$concat .= 'exp_channel_grid_field_'.$field_id.'.'.$row['Field'].', ';
				}
			}
			$concat = substr($concat, 0, -2);
		}

		$col_query->free_result();

		if( ! empty($concat))
		{
			// Find entry_ids that have the keyword
			foreach($this->filters as $filter)
			{
				if(isset($filter['1st']) && is_numeric($filter['1st']) && $filter['1st'] == $field_id && isset($this->fieldtypes[$filter['1st']]) && $this->fieldtypes[$filter['1st']] == "grid")
				{
					$keyword = $filter['3rd'];

					$keyword_query = ee()->db->query("/* Zenbu: Search Grid */\nSELECT entry_id FROM exp_channel_grid_field_".$field_id." WHERE \nCONCAT_WS(',', ".$concat.") \nLIKE '%".ee()->db->escape_like_str($keyword)."%' GROUP BY entry_id");

					$where_in = array();
					if($keyword_query->num_rows() > 0)
					{
						foreach($keyword_query->result_array() as $row)
						{
							$where_in[] = $row['entry_id'];
						}
					}

					if( ! empty($where_in))
					{
						// If $keyword_query has hits, $where_in should not be empty.
						// In that case finish the query

						if($filter['2nd'] == "doesnotcontain")
						{
							// …then query entries NOT in the group of entries
							ee()->db->where_not_in("exp_channel_titles.entry_id", $where_in);
						} else {
							ee()->db->where_in("exp_channel_titles.entry_id", $where_in);
						}
					} else {

						// However, $keyword_query has no hits (like on an unexistent word), $where_in will be empty
						// Send no results for: "search field containing this unexistent word".
						// Else, just show everything, as obviously all entries will not contain the odd word

						if($filter['2nd'] == "contains")
						{
							$where_in[] = 0;
							ee()->db->where_in("exp_channel_titles.entry_id", $where_in);
						}
					}
				} // if


			} // foreach
		} // if

		$this->session->setCache('already_queried_grid', TRUE);
	}

} // END CLASS

/* End of file grid.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/grid.php */
?>