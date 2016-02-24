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
*	Solspace's Calendar field
*	@author	Solspace http://www.solspace.com/
*	@link	http://www.solspace.com/software/detail/calendar/
*	============================================
*	File calendar.php
*
*/

class Zenbu_calendar_ft extends Base
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
		Lang::load('calendar');
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
	public function zenbu_display($entry_id, $channel_id, $data, $calendar_data = array(), $field_id, $settings, $rules = array())
	{
		if( ! isset($entry_id) || empty($entry_id) )
		{
			return '&nbsp;';
		}


		/**
		*	=============================
		*	Calendar: Calendars
		*	=============================
		*	The calendar field is used in Calendar: Calendars in the "Timezone" field
		*	and Calendar: Events in the "Date & Options" field.
		*	Here is what to do if calendar is of the first kind.
		*/
		if( ! empty($data) && (substr($data, 0, 1) == '+' || substr($data, 0, 1) == '-' || $data == '0000'))
		{
			return $data;
		}

		/**
		*	=============================
		*	Calendar: Events
		*	=============================
		*	Style a nice table for basic event data
		*/
		$output = "-";

		if(isset($calendar_data['entry_id_'.$entry_id]))
		{
			$calendar_name = isset($calendar_data['entry_id_'.$entry_id]['calendar_name']) ? Lang::t('calendar_module_name') . ': <strong>' . $calendar_data['entry_id_'.$entry_id]['calendar_name'] . '</strong>' : '';
			$output = $calendar_name;

			if( ! $settings)
			{
				$output .= '<table class="mainTable matrixTable" width="" cellspacing="0" cellpadding="0" border="0" >
							<tr>
								<th class="nowrap center" ><strong>'.Lang::t('from') . '</strong></th>
								<th class="nowrap center" ><strong>'.Lang::t('to') . '</strong></th>
							</tr>';
				//display_date($entry_id, $channel_id, $data, $table_data, $field_id, $settings, $rules, 'unix');
				if(isset($calendar_data['entry_id_'.$entry_id]['start_date']))
				{
					$output .= '<tr><td class="nowrap center" >' . $calendar_data['entry_id_'.$entry_id]['start_date'] . '</td>';
				}

				if(isset($calendar_data['entry_id_'.$entry_id]['end_date']))
				{
					$output .= '<td class="nowrap center" >' . $calendar_data['entry_id_'.$entry_id]['end_date'] . '</td></tr>';
				}

				/**
				*	Go through event data and format display
				*	========================================
				*/
				$details = '';
				if(isset($calendar_data['entry_id_'.$entry_id]['all_day']) && $calendar_data['entry_id_'.$entry_id]['all_day'] == 'y')
				{
					$details .= Lang::t('all_day');
				}

				if(isset($calendar_data['entry_id_'.$entry_id]['recurs']) && $calendar_data['entry_id_'.$entry_id]['recurs'] == 'y')
				{
					// Link to Calendar module's "edit occurrences" section
					$link = BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=calendar' . AMP . 'method=edit_occurrences' . AMP . 'event_id=' . $entry_id;
					$details .= empty($details) ? anchor($link, Lang::t('recurs')) : '. ' . anchor($link, Lang::t('recurs'));
				}



				if(isset($calendar_data['entry_id_'.$entry_id]['recurs']) && isset($calendar_data['entry_id_'.$entry_id]['last_date']) && $calendar_data['entry_id_'.$entry_id]['recurs'] == 'y' && $calendar_data['entry_id_'.$entry_id]['last_date'] == '0')
				{

					$details .= '. ' . ucfirst(Lang::t('ends')) . ' ' . Lang::t('never');

				} elseif(isset($calendar_data['entry_id_'.$entry_id]['recurs']) && isset($calendar_data['entry_id_'.$entry_id]['last_date']) && $calendar_data['entry_id_'.$entry_id]['recurs'] == 'y' && $calendar_data['entry_id_'.$entry_id]['last_date'] != '0') {

					$details .= '. ' . ucfirst(Lang::t('ends')) . ' ' . $calendar_data['entry_id_'.$entry_id]['last_date'];

				}

				if( ! empty($details))
				{
					$output .= '<tr><td colspan="2" class="nowrap center" >' . $details . '</td></tr>';
					$output .= '</table>';
				} else {
					/**
					*	Go through event data and format display
					*	========================================
					*/
					$details = '';

					$output .= isset($calendar_data['entry_id_'.$entry_id]['o_start_date']) ? '<tr><td class="nowrap center" >' . $calendar_data['entry_id_'.$entry_id]['o_start_date'] . '</td>' : '';

					$output .= isset($calendar_data['entry_id_'.$entry_id]['o_end_date']) ? '<td class="nowrap center" >' . $calendar_data['entry_id_'.$entry_id]['o_end_date'] . '</td></tr>' . '<tr><td colspan="2" class="center"><strong>' . Lang::t('occurrence') . '</strong></td></tr>' : '';

					$output .= '</table>';

				}
			}

		}

		//$output = $this->display->highlight($output, $rules, 'field_'.$field_id);
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
		$output = array();
		/**
		 * Oops, there are two "calendar" fieldtypes.
		 * The calendar field is used in Calendar: Calendars in the "Timezone" field
		 * and Calendar: Events in the "Date & Options" field.
		 * Would be a good idea to get the channel_id first to check if we're on
		 * Calendar: Calendars or Calendar: Events.
		 * Since the names of the channels can change, let's just query are way to the channel_id
		 */
		$is_cal_cal = FALSE;
		$cal_cal_channel_query = ee()->db->query("SELECT t.channel_id
			FROM exp_channel_titles t
			JOIN exp_calendar_calendars c ON t.entry_id = c.calendar_id
			LIMIT 1");
		if($cal_cal_channel_query->num_rows() > 0)
		{
			foreach($cal_cal_channel_query->result_array() as $row)
			{
				if($row['channel_id'] == $channel_id)
				{
					$is_cal_cal = TRUE;
				}
			}
		}

		if( ! $is_cal_cal)
		{
			$extra_option = (isset($extra_options['cal_show_cal_name_only'])) ? TRUE : FALSE;
			$output['cal_show_cal_name_only'] = form_label(form_checkbox('settings['.$channel_id.']['.$table_col.'][cal_show_cal_name_only]', 'y', $extra_option).'&nbsp;'.Lang::t('show_calendar_only'));
		}
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
	public function zenbu_get_table_data($entry_ids, $field_ids, $channel_id)
	{
		$output = array();
		if( empty($entry_ids) || empty($field_ids))
		{
			return $output;
		}

		ee()->db->select('t.title AS calendar_name,
								e.entry_id,
								e.start_date AS start_date,
								e.start_time AS start_time,
								e.end_date AS end_date,
								e.end_time AS end_time,
								e.all_day AS all_day,
								e.recurs AS recurs,
								e.last_date AS last_date,
								o.entry_id AS o_entry_id,
								o.start_date AS o_start_date,
								o.start_time AS o_start_time,
								o.end_date AS o_end_date,
								o.end_time AS o_end_time,
								o.all_day AS o_all_day');
		ee()->db->from('exp_calendar_events e');
		ee()->db->join('exp_calendar_events_occurrences o', 'e.entry_id = o.event_id', 'left');
		ee()->db->join('exp_channel_titles t', 't.entry_id = e.calendar_id', 'left');
		ee()->db->where_in('e.entry_id', $entry_ids);
		ee()->db->or_where_in('o.entry_id', $entry_ids);
		$query = ee()->db->get('exp_calendar_events');

		if($query->num_rows() > 0)
		{
			foreach($query->result_array() as $row)
			{
				$output['entry_id_'.$row['entry_id']]['calendar_name'] = $row['calendar_name'];

				// Start date
				$time = $row['all_day'] == 'y' ? FALSE : $row['start_time'];
				$output['entry_id_'.$row['entry_id']]['start_date'] = $this->cal_format_date($row['start_date'], $time);

				// End date
				$time = $row['all_day'] == 'y' ? FALSE : $row['end_time'];
				$output['entry_id_'.$row['entry_id']]['end_date'] = $this->cal_format_date($row['end_date'], $time);

				$output['entry_id_'.$row['entry_id']]['all_day'] = $row['all_day'];
				$output['entry_id_'.$row['entry_id']]['recurs'] = $row['recurs'];

				// Last date
				$output['entry_id_'.$row['entry_id']]['last_date'] = $row['last_date'] == 0 ? 0 : $this->cal_format_date($row['last_date'], FALSE);

				// Occurrence start/end dates
				if( ! empty($row['o_entry_id']))
				{
					$output['entry_id_'.$row['o_entry_id']]['o_start_date'] = $this->cal_format_date($row['o_start_date'], $row['o_start_time']);
					$output['entry_id_'.$row['o_entry_id']]['o_end_date'] = $this->cal_format_date($row['o_end_date'], $row['o_end_time']);
				}
			}
		}

		return $output;

	} // END zenbu_get_table_data()

	// --------------------------------------------------------------------


	/**
	 * cal_format_date
	 * Format date/time data picked directly from database.
	 * No fooling around.
	 */
	private function cal_format_date($date, $time)
	{
		 $date = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, -2);

		 if($time === FALSE)
		 {
		 	$time = '';
		 } else {

		 	// Some times are not padded. Eg. 12:30 AM is just "30" in the DB
		 	$time = str_pad($time, 4, '0', STR_PAD_LEFT);
		 	$time = ee()->session->userdata('time_format') == 'us' ? NBS . date('h:i A', strtotime($time)) : NBS . date('H:i', strtotime($time));
		 }

		 return $date . $time;

	} // END cal_format_date()

	// --------------------------------------------------------------------


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
		foreach($this->filters as $filter)
		{
			if(isset($filter['1st']) && is_numeric($filter['1st']) && $filter['1st'] == $field_id && isset($this->fieldtypes[$filter['1st']]) && $this->fieldtypes[$filter['1st']] == "calendar")
			{
				$keyword = $filter['3rd'];

				$where_query = "";
				if($filter['2nd'] == "contains")
				{
					$where_query = "WHERE t.title LIKE '%" . $keyword . "%'";
				} elseif($filter['2nd'] == "doesnotcontain") {
					$where_query = ! empty($keyword) ? "WHERE t.title NOT LIKE '%" . $keyword . "%'" : '';
				}

				$keyword_query = ee()->db->query("/* Zenbu: Search calendars */\nSELECT t.title, t.entry_id
					FROM exp_channel_titles t
					JOIN exp_calendar_calendars c ON t.entry_id = c.calendar_id "
					. $where_query);
				$where_in = array();
				if($keyword_query->num_rows() > 0)
				{
					foreach($keyword_query->result_array() as $row)
					{
						$where_in[] = $row['entry_id'];
					}
				}
			} // if
		}

		// If $keyword_query has hits, $where_in should not be empty.
		// In that case finish the query
		if( isset($where_in) && ! empty($where_in))
		{
			ee()->db->where_in("exp_channel_data.field_id_" . $field_id, $where_in);
		} else {
			// However, $keyword_query has no hits (like on an unexistent word), $where_in will be empty
			// Send no results.
			$where_in[] = 0;
			ee()->db->where_in("exp_channel_titles.entry_id", $where_in);
		}

		return;
	}


} // END CLASS

/* End of file calendar.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/calendar.php */
?>