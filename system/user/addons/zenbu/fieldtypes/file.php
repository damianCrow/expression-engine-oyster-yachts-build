<?php namespace Zenbu\fieldtypes;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Display;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*	ZENBU THIRD-PARTY FIELDTYPE SUPPORT
*	============================================
*	Standard File field
*	@author	EllisLab
*	============================================
*	File file.php
*
*/

class Zenbu_file_ft extends Base
{

	/**
	*	Constructor
	*
	*	@access	public
	*/
	public function __construct()
	{
		$this->display = new Display();
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
	public function zenbu_display($entry_id, $channel_id, $field_data, $table_data = array(), $field_id, $settings, $rules = array(), $upload_prefs = array())
	{
		$output = $this->display->file($field_id, $field_data, $upload_prefs, $rules, $settings);

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

		if(strncmp($table_col, 'field_id_', 9) == 0)
		{
			$field_id = substr($table_col, 9);
		} else {
			$field_id = 0;
		}

		if( ee()->session->cache('zenbu', 'file_thumb_options_' . $field_id) === FALSE )
		{
			/**
			*	--------------------------------------------------------------------
			*	Get field settings to retrieve upload_dir_id ("all" or specific id)
			*	--------------------------------------------------------------------
			*/
			$query = ee()->db->query('/* File: zenbu_field_extra_settings */ SELECT field_settings FROM exp_channel_fields WHERE field_id = ' . $field_id);

			if($query->num_rows() > 0)
			{
				foreach($query->result_array() as $row)
				{
						$field_settings = unserialize(base64_decode($row['field_settings']));
				}
			} else {
				$field_settings = array();
			}

			if(isset($field_settings['allowed_directories']) && $field_settings['allowed_directories'] != 'all')
			{
				$where_str = ' WHERE upload_location_id = ' . $field_settings['allowed_directories'];
			} else {
				$where_str = '';
			}

			/**
			*	-----------------------
			*	Build dropdown options
			*	-----------------------
			*/
			if(version_compare(APP_VER, "2.1.5", '>='))
			{
				$results = ee()->db->query('SELECT * FROM exp_file_dimensions' . $where_str);
				if($results->num_rows() > 0)
				{
					$file_option_dropdown['thumbs'] = ee()->lang->line('standard_thumbs');
					foreach($results->result_array() as $row)
					{
						$file_option_dropdown[$row['short_name']] = $row['title'] . ' (' . $row['width'] . 'x' . $row['height'] . ')';
					}
				} else {
					$file_option_dropdown = array();
				}
			} else {
				$file_option_dropdown = array();
			}

			ee()->session->set_cache('zenbu', 'file_thumb_options_' . $field_id, $file_option_dropdown);

		}

		/**
		*	---------------------------------------
		*	Build output for extra settings column
		*	---------------------------------------
		*/
		if( ee()->session->cache('zenbu', 'file_thumb_options_' . $field_id) && ee()->session->cache('zenbu', 'file_thumb_options_' . $field_id ) != array() )
		{
			$thumb_options = ee()->session->cache('zenbu', 'file_thumb_options_' . $field_id);
			$extra_option_1 = (isset($extra_options['file_option_1'])) ? $extra_options['file_option_1'] : '';
			$output['file_option_1'] = form_label(ee()->lang->line('use_thumbnail') . ' ' . form_dropdown('settings['.$channel_id.']['.$table_col.'][file_option_1]', $thumb_options, $extra_option_1));
		}

		return $output;
	}


} // END CLASS

/* End of file file.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/file.php */
?>