<?php namespace Zenbu\fieldtypes;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Display;
use Zenbu\librairies\platform\ee\Request;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*	ZENBU THIRD-PARTY FIELDTYPE SUPPORT
*	============================================
*	Standard date field
*	@author	EllisLab
*	============================================
*	File date.php
*
*/

class Zenbu_date_ft extends Base
{
	var $dropdown_type = "date";

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
		parent::init('fields');
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
		$output = $this->display->date($data, $field_id, 'unix');
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
		$extra_option_1 = (isset($extra_options['date_option_1'])) ? $extra_options['date_option_1'] : '';
		$output['date_option_1'] = form_label(ee()->lang->line('date_format').'&nbsp;'.form_input('settings['.$channel_id.']['.$table_col.'][date_option_1]', $extra_option_1, 'size="10"'));
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
		foreach($this->filters as $filter)
		{

			if(isset($filter['1st']) && is_numeric($filter['1st']) && $filter['1st'] == $field_id && isset($this->fieldtypes[$filter['1st']]) && $this->fieldtypes[$filter['1st']] == 'date')
			{
				$column = 'field_id_' . $field_id;

				$date = $filter['3rd'];

				if( ! empty($date))
				{
					$now = ee()->localize->now;

					if(strncmp($date, '+', 1) == 0)
					{
						// THE FUTURE!
						$date			= substr($date, 1);
						$date			= $date*24*60*60; // Convert to seconds
						$date			= $now + $date;
						$comparator1	= "<";
						$comparator2	= ">";
					} elseif ($date != "range") {
						// The past
						$date			= $date*24*60*60; // Convert to seconds
						$date			= $now - $date;
						$comparator1	= ">";
						$comparator2	= "<";
					} else {
						// The Range
						$date_from		= strtotime($rule['date_from']);
						$date_to		= strtotime($rule['date_to']) + 86400;
					}

					if($filter['2nd'] == "is")
					{
						if($date == "range")
						{
							ee()->db->where("channel_data." . $column . " >= ", $date_from);
							ee()->db->where("channel_data." . $column . " <= ", $date_to);
						} else {
							ee()->db->where("channel_data." . $column . " ".$comparator1." ", $date);
							ee()->db->where("channel_data." . $column . " ".$comparator2." ", $now);
						}

					} else {

						if($date == "range")
						{
							$where = "(exp_channel_data." . $column . " < " . $date_from . " OR exp_channel_data." . $column . " > " . $date_to . ")";
						} else {
							$where = "(exp_channel_data." . $column . " ".$comparator2." ".$date." OR exp_channel_data." . $column . " ".$comparator1." ".$now.")";
						}

						ee()->db->where($where);
					}

				}
			}
		}
	}

} // END CLASS

/* End of file date.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/date.php */
?>