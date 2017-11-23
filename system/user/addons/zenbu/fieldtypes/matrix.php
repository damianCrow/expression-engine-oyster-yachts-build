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
*	Pixel&Tonic's Matrix field
*	@author	Pixel&tonic http://pixelandtonic.com
*	@link	http://pixelandtonic.com/matrix
*	============================================
*	File matrix.php
*
*/

class Zenbu_matrix_ft extends Base
{
	var $dropdown_type = "contains_doesnotcontain";

	/**
	*	Constructor
	*
	*	@access	public
	*/
	public function __construct()
	{
		ee()->lang->loadfile('matrix');
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
	public function zenbu_display($entry_id, $channel_id, $data, $matrix_data = array(), $field_id, $settings, $rules = array(), $upload_prefs = array(), $installed_addons)
	{
		$output = NBS;

		// If matrix_data is empty (no results), stop here and return a space for the field data
		if(isset($matrix_data['entry_id_'.$entry_id]['field_id_'.$field_id]))
		{
			$table_data = $matrix_data['entry_id_'.$entry_id]['field_id_'.$field_id];
		} else {
			return $output;
		}
				
		// Process field data
		foreach($table_data as $row => $col_array_raw)
		{
			foreach($col_array_raw as $col_order => $col_array)
			{
				foreach($col_array as $col_id => $col_data)
				{
					if(substr($col_id, 0, 7) == "col_id_")
					{
						$num_col_id = substr($col_id, 7);
						$num_row_id = substr($row, 7);

						// Create header array for view
						$vars['headers'][$num_col_id] = $matrix_data['entry_id_'.$entry_id]['field_id_'.$field_id]['headers'][$num_col_id]['data'];
						$vars['column_fieldtype'][$num_col_id] = $matrix_data['entry_id_'.$entry_id]['field_id_'.$field_id]['headers'][$num_col_id]['fieldtype'];

						// Create cell data array for view
						switch ($vars['column_fieldtype'][$num_col_id])
						{
							case "file": case "safecracker_file":
								$cell = $this->display->file($field_id, $col_data, $upload_prefs, $this->filters, $settings);
							break;
							case "assets":
								$ft_object = $this->fields->loadFieldtypeClass('assets');
								$cell = '';

								$assets_data = $ft_object->zenbu_get_table_data(array($entry_id), array($field_id), $channel_id, $num_col_id);

								if(isset($assets_data[$num_row_id]))
								{
									foreach($assets_data[$num_row_id] as $asset_row => $asset_array)
									{
										// That last param (TRUE) prevents creating "Show (X)" links with fancybox
										$cell .= $ft_object->zenbu_display($entry_id, $channel_id, $data, $asset_array, $field_id, $settings, $this->filters, $upload_prefs, $installed_addons, TRUE) . NBS;
									}
								}

							break;
							case "date": case "dropdate":
								//$output_date = ee()->zenbu_get->_get_member_date_settings();
								$cell = $this->display->date($col_data, $field_id);
							break;
							case "playa": case "structure_playa":
								// Digging too deep: don't have Playa-within-matrix field-relationship $playa_data array as of this writing.
								// Query entry ids, titles and channel_ids per matrix within the _display_rel function, using the from_matrix = y array

								$ft_object = $this->fields->loadFieldtypeClass($vars['column_fieldtype'][$num_col_id]);

								$ft_table_data = $vars['column_fieldtype'][$num_col_id].'_data';
								$table_data = (isset($$ft_table_data)) ? $$ft_table_data : array();
								$table_data['from_matrix'] = 'y';

								// Destroy cache for each Playa set query.
								// Possibly not great for performance, but without this
								// all Matix Playa columns will have the same data
								ee()->session->set_cache('zenbu', 'core_entry_data', FALSE);

								$field_data = $ft_object && method_exists($ft_object, 'zenbu_display') ? $ft_object->zenbu_display($entry_id, $channel_id, $col_data, $table_data, $field_id, $settings, $this->filters, $upload_prefs, $installed_addons) : '-';

								$cell = $field_data;
							break;
							default:
								$cell = $this->display->text($col_data, $field_id);
							break;
						}
						$vars['rows'][$row][$col_id] = $cell;
					}
				}
			}
		}

		$vars['field_id'] = $field_id;
		$vars['entry_id'] = $entry_id;
		$vars['table_id'] = $entry_id.'-'.$field_id;
		$vars['settings'] = $settings;

		return View::render('columns/fieldtypes/matrix.twig', $vars);
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

		if($this->session->getCache('Zenbu_matrix_table_data'))
		{
			return $this->session->getCache('Zenbu_matrix_table_data');	
		}

		//
		// Get matrix field data as an array also used for headers
		//
		ee()->db->select("col_id, col_type, col_order, col_label, field_id, col_id AS id, col_label AS label, col_type AS fieldtype");
		ee()->db->from("matrix_cols");
		ee()->db->where("site_id", ee()->session->userdata['site_id']);
		ee()->db->order_by('col_order', 'asc');
		$query = ee()->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$headers_array[$row->id] = $row;
				$col_params['field_id_'.$row->field_id]['col_id_'.$row->id] = $row;
			}
		}


		//
		// Get Matrix data
		//
		//$field_ids = array("" => 30);
		ee()->db->from("matrix_data");
		ee()->db->where_in("entry_id", $entry_ids);
		ee()->db->where_in("field_id", $field_ids);
		ee()->db->where("site_id", ee()->session->userdata['site_id']);
		ee()->db->order_by("field_id", "asc");
		ee()->db->order_by("row_order", "asc");
		$results = ee()->db->get();
		if($results->num_rows() > 0)
		{

			// Create an array for col_type query and setup data for view
			$col_ids = array();
			foreach($results->result() as $row)
			{
				$entry_id = $row->entry_id;
				$f_id     = $row->field_id;
				$row_id   = $row->row_id;
				if(in_array($f_id, $field_ids))
				{
					foreach($row as $data_field => $data)
					{

						if(strncmp($data_field, "col_id_", 7) == 0 && ! is_null($data))
						{
							// Array for col_type and col_label
							$col_id_number = substr($data_field, 7);

							if(isset($col_params['field_id_'.$f_id]['col_id_'.$col_id_number]->col_order))
							{
								$col_order = $col_params['field_id_'.$f_id]['col_id_'.$col_id_number]->col_order;
							}

							// Data rows
							if(isset($col_params['field_id_'.$f_id]['col_id_'.$col_id_number]) && $col_id_number == $col_params['field_id_'.$f_id]['col_id_'.$col_id_number]->id)
							{
								$row_array['entry_id_'.$entry_id]['field_id_'.$f_id]['row_id_'.$row_id]['col_order_'.$col_order]['col_id_'.$col_id_number] = str_replace('&amp;', '&', htmlspecialchars($data));
								ksort($row_array['entry_id_'.$entry_id]['field_id_'.$f_id]['row_id_'.$row_id]);
							}
						}
					}
				}
			}


			foreach($results->result() as $row)
			{
				$entry_id = $row->entry_id;
				$f_id     = $row->field_id;
				$row_id   = $row->row_id;
				if(in_array($f_id, $field_ids))
				{
					foreach($row as $data_field => $data)
					{

						if(strncmp($data_field, 'col_id_', 7) == 0 && ! is_null($data))
						{
							// Array for col_type and col_label
							$col_id_number = substr($data_field, 7);

							// Data rows
							$row_array['entry_id_'.$entry_id]['field_id_'.$f_id]['headers'][$col_id_number]['data'] = $headers_array[$col_id_number]->label;
							$row_array['entry_id_'.$entry_id]['field_id_'.$f_id]['headers'][$col_id_number]['fieldtype'] = $headers_array[$col_id_number]->fieldtype;

						}
					}
				}
			}

			$results->free_result();

			$table_id['table_id'] = $entry_id;
			$output = array_merge($row_array, $table_id);

			$this->session->setCache('Zenbu_matrix_table_data', $output);

			return $output;

		} else {
			// If matrix is empty, return a space character for the cell
			return $output;
		}
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
		$extra_option_1 = (isset($extra_options['matrix_option_1'])) ? TRUE : FALSE;
		$output['matrix_option_1'] = form_label(form_checkbox('settings['.$channel_id.']['.$table_col.'][matrix_option_1]', 'y', $extra_option_1).'&nbsp;'.ee()->lang->line('show_').ee()->lang->line('matrix').ee()->lang->line('_in_row'));
		return $output;
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
	*	@return					A query to be integrated with entry results. Should be in CI Active Record format (ee()->db->…)
	*/
	public function zenbu_result_query($field_id = "")
	{
		if( ! $this->filters || empty($field_id))
		{
			return;
		}

		if( ! $this->session->getCache('already_queried_matrix') )
		{
			ee()->db->from("exp_matrix_data");
		}

		ee()->db->where("exp_matrix_data.field_id", $field_id);
		$col_query = ee()->db->query("/* Zenbu: Show columns for matrix */\nSHOW COLUMNS FROM exp_matrix_data");
		$concat = "";
		$where_in = array();

		if($col_query->num_rows() > 0)
		{
			foreach($col_query->result_array() as $row)
			{
				if(strchr($row['Field'], 'col_id_') !== FALSE)
				{
					$concat .= 'exp_matrix_data.'.$row['Field'].', ';
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
				
				if(isset($filter['1st']) && is_numeric($filter['1st']) && $filter['1st'] == $field_id && isset($this->fieldtypes[$filter['1st']]) && $this->fieldtypes[$filter['1st']] == "matrix")
				{
					$keyword = $filter['3rd'];

					$keyword_query = ee()->db->query("/* Zenbu: Search matrix */\nSELECT entry_id FROM exp_matrix_data WHERE CONCAT_WS(',', ".$concat.") LIKE '%".ee()->db->escape_like_str($keyword)."%'");

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
	}



} // END CLASS

/* End of file matrix.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/matrix.php */
?>