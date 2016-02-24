<?php namespace Zenbu\fieldtypes;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Display;
use Zenbu\librairies\platform\ee\Request;
use Zenbu\librairies\platform\ee\Url;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*	ZENBU THIRD-PARTY FIELDTYPE SUPPORT
*	============================================
*	Standard relationship field (EE 2.6+)
*	@author	EllisLab
*	============================================
*	File relationship.php
*
*/

class Zenbu_relationship_ft extends Base
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
	public function zenbu_display($entry_id, $channel_id, $field_data, $rel_data = array(), $field_id, $settings, $filters = array(), $upload_prefs = array(), $installed_addons)
	{
		$output = NBS;

		$this_entry_id = $entry_id;
		if( empty($rel_data))
		{
			return $output;
		}

		// --------------------
		// Relationship entry display
		// --------------------

		$output = '';

		if(isset($rel_data['grid']))
		{
			$related_entries = isset($rel_data['grid']['parent_id_'.$entry_id]['grid_id_'.$field_id]['grid_row_'.$rel_data['grid_row']]['grid_col_'.$rel_data['grid_col']]) ? $rel_data['grid']['parent_id_'.$entry_id]['grid_id_'.$field_id]['grid_row_'.$rel_data['grid_row']]['grid_col_'.$rel_data['grid_col']] : array();

			foreach($related_entries as $order => $child_entry_data)
			{
				foreach($child_entry_data as $child_id => $entry_data_array)
				{
					$entry_title     = $this->display->text($entry_data_array['title'], $field_id);
					$entry_id_prefix = isset($settings->rel_option_1) && $settings->rel_option_1 == 'y' ? $entry_data_array['entry_id'] . ' - ' : '';
					$output .= '<li>' . anchor(Url::cpEditEntryUrl($entry_data_array), $entry_id_prefix . $entry_title);
					$output .= count($related_entries) > 1 ? '</li>' : '';

				}
			}

		} else {
			$related_entries = isset($rel_data['parent_id_'.$entry_id]['field_id_'.$field_id]) ? $rel_data['parent_id_'.$entry_id]['field_id_'.$field_id] : array();

			foreach($related_entries as $child_entry_id => $entry_data_array)
			{
				$entry_title     = $this->display->text($entry_data_array['title'], $field_id);
				$entry_id_prefix = isset($settings->rel_option_1) && $settings->rel_option_1 == 'y' ? $entry_data_array['entry_id'] . ' - ' : '';
				$output .= '<li>' . anchor(Url::cpEditEntryUrl($entry_data_array), $entry_id_prefix . $entry_title);
				$output .= count($related_entries) > 1 ? '</li>' : '';
			}
		}

		$output = ! empty($output) ? '<ul>'.$output.'</ul>' : NBS;

		return $output;
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
		// Retrieve previous results if present
		$rel_option_1 = (isset($extra_options['rel_option_1']) && $extra_options['rel_option_1'] == 'y') ? TRUE : FALSE;

		// Option: Show related entry ID with related entry title
		$output['rel_option_1'] = form_label(form_checkbox('settings['.$channel_id.']['.$table_col.'][rel_option_1]', 'y', $rel_option_1) . NBS . ee()->lang->line('show').NBS.ee()->lang->line('entry_id'));

		// Output
		return $output;

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
	*	@param	$settings				array	The settings array, containing saved field order, display, extra options etc settings
	*	@param	$rel_array				array	A simple array useful when using related entry-type fields (optional)
	*	@return	$output					array	An array of data (typically broken down by entry_id then field_id) that can be used and processed by the zenbu_display() method
	*/
	public function zenbu_get_table_data($entry_ids, $field_ids, $channel_id, $output_upload_prefs, $settings, $rel_array)
	{
		$output = array();

		if( empty($entry_ids) || empty($field_ids) || empty($rel_array))
		{
			return $output;
		}

		$rel_in_grid = FALSE;

		/**
		 * Retrieve Relationship child_entries (related entries)
		 * ----------------------------------------------
		 * Retrieve based on provided result entry_ids.
		 * Extra processing to get field_ids (those set to be displayed).
		 * Only first entry row is sufficient to get field_ids.
		 */
		$first_result = array_slice($rel_array, 0, 1);
		$first_result = $first_result[0];

		// Get target field_ids from this first entry row
		$parent_field_ids = array();
		foreach($first_result as $field => $data)
		{
			if(strncmp($field, 'field_id_', 9) == 0)
			{
				$field_id = substr($field, 9);
				$parent_field_ids[$field_id] = $field_id;

			} elseif(strncmp($field, 'grid_id_', 8) == 0) {

				$field_id = substr($field, 8);
				$parent_field_ids[$field_id] = $field_id;
				$rel_in_grid = TRUE;
			}
		}

		//	----------------------------------------
		// 	Build relationship data array
		//	----------------------------------------
		ee()->db->from("exp_relationships");
		ee()->db->where_in("parent_id", $entry_ids);

		$rel_data_q = ee()->db->get();

		if($rel_data_q->num_rows() > 0)
		{
			foreach($rel_data_q->result_array() as $row)
			{
				$rel_data[$row['child_id']] = $row['child_id'];
			}
		}

		$db_fields = array("ct.entry_id", "r.child_id", "r.parent_id", "r.field_id", "ct.title", "ct.channel_id", "r.order");

		//	----------------------------------------
		//	Add these Grid columns to the query (EE 2.7+)
		//	----------------------------------------
		if ($rel_in_grid)
		{
			$db_fields[] = "r.grid_field_id";
			$db_fields[] = "r.grid_col_id";
			$db_fields[] = "r.grid_row_id";
		}

		ee()->db->select($db_fields);
		ee()->db->from("exp_channel_titles ct");
		ee()->db->join("exp_relationships r", "ct.entry_id = r.child_id");
		ee()->db->where_in("r.parent_id", $entry_ids);

		//	----------------------------------------
		//	Weed out Grid-Relationship rows if not
		//	currently in a Grid
		//	----------------------------------------
		if($rel_in_grid === FALSE && version_compare(APP_VER, '2.7', '>='))
		{
			ee()->db->where("r.grid_field_id", 0);
		}

		ee()->db->order_by("r.order", 'ASC');
		//ee()->db->where_in("exp_relationships.relationship_id", $rel_data);
		$query = ee()->db->get();

		foreach($query->result_array() as $row)
		{
			$output['parent_id_'.$row['parent_id']]['field_id_'.$row['field_id']]['child_id_'.$row['entry_id']]['title']      = $row['title'];
			$output['parent_id_'.$row['parent_id']]['field_id_'.$row['field_id']]['child_id_'.$row['entry_id']]['entry_id']   = $row['entry_id'];
			$output['parent_id_'.$row['parent_id']]['field_id_'.$row['field_id']]['child_id_'.$row['entry_id']]['channel_id'] = $row['channel_id'];

			//	----------------------------------------
			//	Grid-based relationship data (EE 2.7+)
			//	----------------------------------------
			if($rel_in_grid)
			{
				$grid_id       = 'grid_id_'.$row['grid_field_id'];
				$grid_row      = 'grid_row_'.$row['grid_row_id']; // grid_row_id is the row_id in Grid DB tables
				$grid_col      = 'grid_col_'.$row['grid_col_id'];
				$grid_order    = $row['order'];
				$grid_child_id = $row['entry_id'];

				$output['grid']['parent_id_'.$row['parent_id']][$grid_id][$grid_row][$grid_col][$grid_order][$grid_child_id]['title']      = $row['title'];
				$output['grid']['parent_id_'.$row['parent_id']][$grid_id][$grid_row][$grid_col][$grid_order][$grid_child_id]['entry_id']   = $row['entry_id'];
				$output['grid']['parent_id_'.$row['parent_id']][$grid_id][$grid_row][$grid_col][$grid_order][$grid_child_id]['channel_id'] = $row['channel_id'];
			}

		}
		$query->free_result();

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
		// Let's not even go through this if there isn't a word or rules to search in the first place
		if(empty($field_id))
		{
			return;
		}

		foreach($this->filters as $filter)
		{
			$in = $filter['2nd'];
			$keyword = $filter['3rd'];
			$filter_field_id = is_numeric($filter['1st']) ? $filter['1st'] : '';

			// Blank values just return all entries, like this never happened
			if( ! in_array($in, array('isempty', 'isnotempty')) && empty($keyword))
			{
				return;
			}

			if(is_numeric($filter['1st']) && isset($this->fieldtypes[$filter_field_id]) && $this->fieldtypes[$filter_field_id] == "relationship" && $filter_field_id == $field_id)
			{
				switch($in)
				{
					case 'contains':
						$like_query = "LIKE '%".ee()->db->escape_like_str($keyword)."%' ";
					break;
					case 'doesnotcontain':
						$like_query = "NOT LIKE '%".ee()->db->escape_like_str($keyword)."%' ";
					break;
					case 'beginswith':
						$like_query = "LIKE '".ee()->db->escape_like_str($keyword)."%' ";
					break;
					case 'doesnotbeginwith':
						$like_query = "NOT LIKE '".ee()->db->escape_like_str($keyword)."%' ";
					break;
					case 'endswith':
						$like_query = "LIKE '%".ee()->db->escape_like_str($keyword)."' ";
					break;
					case 'doesnotendwith':
						$like_query = "NOT LIKE '%".ee()->db->escape_like_str($keyword)."' ";
					break;
					case 'containsexactly':
						$like_query = "LIKE '".ee()->db->escape_like_str($keyword)."' ";
					break;
					case 'isempty':
						$children_by_field = $this->_get_children_by_field($filter_field_id);
						ee()->db->where_not_in('exp_channel_titles.entry_id', $children_by_field);
						return; // That's all we need in this case, so stop here
					break;
					case 'isnotempty':
						$children_by_field = $this->_get_children_by_field($filter_field_id);
						ee()->db->where_in('exp_channel_titles.entry_id', $children_by_field);
						return; // That's all we need in this case, so stop here
					break;
				}

				$parent_entries = $this->_relationship_keyword_query($like_query, $field_id);

				/**
				 * Extra query for negatives
				 * -------------------------
				 * Negative rules, such as "does not contain", need two-step verification before outputting
				 * the entry_id array for the final query. This is because some entries could be flagged as matching the query but
				 * based on another row in exp_relationship_relationships. Eg. Entry has rel entries A,B,C. Searching "not A", the above
				 * would flag the entry based on B,C.
				 * Therefore parent_ids from above are compared to the opposite query (giving opposite results) parent_ids below.
				 * The results below are substracted from the results above.
				 */
				if(in_array($in, array('doesnotcontain', 'doesnotbeginwith', 'doesnotendwith')))
				{
					switch($in)
					{
						case 'doesnotcontain':
							$like_query_negatives = "LIKE '%".ee()->db->escape_like_str($keyword)."%' ";
						break;
						case 'doesnotbeginwith':
							$like_query_negatives = "LIKE '".ee()->db->escape_like_str($keyword)."%' ";
						break;
						case 'doesnotendwith':
							$like_query_negatives = "LIKE '%".ee()->db->escape_like_str($keyword)."' ";
						break;
					}

					$parent_entries_negatives = $this->_relationship_keyword_query($like_query_negatives, $field_id);

					$parent_entries = isset($parent_entries_negatives) ? array_diff($parent_entries, $parent_entries_negatives) : $parent_entries;
				}

				if( ! empty($parent_entries))
				{
					ee()->db->where_in('exp_channel_titles.entry_id', $parent_entries);
				} else {
					ee()->db->where('exp_channel_titles.entry_id', "0");
				}
			} //if field, keyword, relationship field checks
		} // foreach
	} // END zenbu_result_query

	// --------------------------------------------------------------------


	/**
	 * ===============================
	 * function _get_children_by_field
	 * ===============================
	 * Builds an array of entries with a related entry for a specified field_id
	 * @param  string $field_id   	The target custom field_id
	 * @return array  $output		An array of result entries
	 */
	private function _get_children_by_field($field_id)
	{
		if( ee()->session->cache('zenbu', 'children_by_field_id_'.$field_id) )
		{
			return ee()->session->cache('zenbu', 'children_by_field_id_'.$field_id);
		}

		$output = array();

		$sql = ee()->db->query("/* Zenbu Relationship field: _get_children_by_field */ SELECT parent_id FROM exp_relationships WHERE field_id = " . ee()->db->escape_str($field_id));

		if($sql->num_rows() > 0)
		{
			foreach($sql->result_array() as $row)
			{
				$output[] = $row['parent_id'];
			}
		}

		ee()->session->set_cache('zenbu', 'children_by_field_id_'.$field_id, $output);

		return $output;

	} // END _get_children_by_field

	// --------------------------------------------------------------------


	/**
	 * ===============================
	 * function _relationship_keyword_query
	 * ===============================
	 * Builds an array of targeted entries based on a simple db query,
	 * used with zenbu_result_query method
	 * @param  string $like_query The query string, which changes based on filter rule
	 * @param  string $field_id   The target custom field_id
	 * @return array  $output_array	An array of result entries
	 */
	private function _relationship_keyword_query($like_query, $field_id)
	{
		$output_array = array();

		$rel_keyword_query = ee()->db->query("/* Zenbu: Relationship query for entries */\nSELECT r.parent_id, e.entry_id, e.title
			FROM exp_channel_titles e
			JOIN exp_relationships r ON e.entry_id = r.child_id
			WHERE r.field_id = " . $field_id . "
			AND e.title " . $like_query);

		if($rel_keyword_query->num_rows() > 0)
		{
			foreach($rel_keyword_query->result_array() as $row)
			{
				$output_array[] = $row['parent_id'];
			}
		}
		$rel_keyword_query->free_result();

		return $output_array;
	}

} // END CLASS

/* End of file relationship.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/relationship.php */
?>