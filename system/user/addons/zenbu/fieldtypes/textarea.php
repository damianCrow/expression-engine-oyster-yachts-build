<?php namespace Zenbu\fieldtypes;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Display;
use Zenbu\librairies\platform\ee\Request;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*	ZENBU THIRD-PARTY FIELDTYPE SUPPORT
*	============================================
*	Standard Textarea
*	@author	EllisLab
*	============================================
*	File textarea.php
*
*/

class Zenbu_textarea_ft extends Base
{
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
		return $this->display->text($data, $field_id);
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
		$extra_text_option_1 = (isset($extra_options['text_option_1'])) ? $extra_options['text_option_1'] : '';
		$extra_text_option_2 = (isset($extra_options['text_option_2'])) ? $extra_options['text_option_2'] : 'html';

		// Option: Text trimming option
		$output['text_option_1'] = form_label(ee()->lang->line('show').NBS.form_input('settings['.$channel_id.']['.$table_col.'][text_option_1]', $extra_text_option_1, 'size="2" maxlength="4" class="bottom-margin"').'&nbsp;'.ee()->lang->line('characters')) . BR;

		// Option: Text display (HTML/no HTML) option)
		$text_option_2_dropdown = array(
			"html" => ee()->lang->line("show_html"),
			"nohtml" => ee()->lang->line("no_html"),
		);
		$output['text_option_2'] = form_dropdown('settings['.$channel_id.']['.$table_col.'][text_option_2]', $text_option_2_dropdown, $extra_text_option_2, 'class="bottom-margin"' );

		// Output
		return $output;

	}

	/**
	*	===================================
	*	function zenbu_field_validation
	*	===================================
	*	Set up extra validation for user input in extra settings
	*
	*	@param	$setting	array	Submitted display settings data
	*	@return	$output		array	Setting values for this fieldtype, with extra setting short name as key
	*/
	public function zenbu_field_validation($setting)
	{
		/**
		*	-------------------------
		*	Text option 1: word limit
		*	-------------------------
		*	Check that input is numerical
		*/
		if(isset($setting['text_option_1']) && ! is_numeric($setting['text_option_1']) && ! empty($setting['text_option_1']))
		{
			ee()->javascript->output('
				$.ee_notice("'.ee()->lang->line("error_not_numeric").'", {"type" : "error"});
			');
			$output['text_option_1'] = (int)$setting['text_option_1'];
			return $output;
		} elseif(empty($setting['text_option_1'])) {
			$output['text_option_1'] = '';
		} else {
			$output['text_option_1'] = (int)$setting['text_option_1'];
		}

		/**
		*	----------------------------
		*	Text option 2: display style
		*	----------------------------
		*	Check that the setting simply exists
		*/
		if(isset($setting['text_option_2']))
		{
			$output['text_option_2'] = $setting['text_option_2'];
		} else {
			$output['text_option_2'] = 'html';
		}

		return $output;
	}

} // END CLASS

/* End of file textarea.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/textarea.php */
?>