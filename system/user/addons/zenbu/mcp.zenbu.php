<?php 
require_once __DIR__.'/vendor/autoload.php';

use Zenbu\controllers;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * =======
 *  Zenbu
 * =======
 * See more data in your control panel entry listing
 * @version 	1.9.4
 * @copyright 	Nicolas Bottari - Zenbu Studio 2011-2014
 * @author 		Nicolas Bottari - Zenbu Studio
 * ------------------------------
 *
 * *** IMPORTANT NOTES ***
 * I (Nicolas Bottari and Zenbu Studio) am not responsible for any
 * damage, data loss, etc caused directly or indirectly by the use of this add-on.
 * @license		See the license documentation (text file) included with the add-on.
 *
 * Description
 * -----------
 * Zenbu is a powerful and customizable entry list manager similar to
 * ExpressionEngine's Edit Channel Entries section in the control panel.
 * Accessible from Content » Edit, Zenbu enables you to see, all on the same page:
 * Entry ID, Entry title, Entry date, Author name, Channel name, Live Look,
 * Comment count, Entry status URL Title, Assigned categories, Sticky state,
 * All (or a portion of) custom fields for the entry, etc
 *
 * @link	http://zenbustudio.com/software/zenbu/
 * @link	http://zenbustudio.com/software/docs/zenbu/
 *
 * Special thanks to Koen Veestraeten (StudioKong) for his excellent bug reporting during the initial 1.x beta
 * @link	http://twitter.com/#!/studiokong
 *
 */

class Zenbu_mcp {

	var $default_limit = 25;
	var $addon_short_name = 'zenbu';
	var $settings = array();
	var $installed_addons = array();
	var $permissions = array(
			'can_admin',
			'can_copy_profile',
			'can_access_settings',
			'edit_replace',
			'can_view_group_searches',
			'can_admin_group_searches'
		);
	var $non_ft_extra_options = array(
			"date_option_1" 	=> "date_option_1",
			"date_option_2"		=> "date_option_2",
			"view_count_1" 		=> "view_count_1",
			"view_count_2" 		=> "view_count_2",
			"view_count_3" 		=> "view_count_3",
			"view_count_4" 		=> "view_count_4",
			"livelook_option_1" => "livelook_option_1",
			"livelook_option_2" => "livelook_option_2",
			"livelook_option_3" => "livelook_option_3",
			"category_option_1"	=> "category_option_1",
		);

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function __construct()
	{
	}

	// --------------------------------------------------------------------
	

	/**
	 * Main Page
	 *
	 * @access	public
	 */
	function index($limit = '', $perpage = '')
	{		
		$controller = new Zenbu\controllers\ZenbuController;
		return $controller->actionIndex();
	} // END index()

	// --------------------------------------------------------------------


	/*
	*	function save_rules_by_session
	*	Store filter/search rules temporarily as a session variable for later retrieval,
	*	in particular in redirection back to Zenbu
	*/
	function save_rules_by_session()
	{
		if (session_id() == '')
		{
			session_start();
		}

		$filter_elements = array("rule", "limit", "orderby", "sort", "perpage");

		$filter_data = array();

		foreach($filter_elements as $elem)
		{
			$$elem = $this->EE->input->get_post($elem, TRUE);
			$_SESSION['zenbu'][$elem] = serialize($$elem);
			$filter_data[$elem] = $$elem;
		}

		$this->EE->zenbu_db->save_cached_filters($filter_data);

	} // END save_rules_by_session()

	// --------------------------------------------------------------------


	/*
	*	function multi_edit
	*	Build view for Zenbu's multi-entry editor
	*/
	function multi_edit()
	{
		$controller = new Zenbu\controllers\Zenbu_MultiEntryController;
		return $controller->actionIndex();
	} // END multi_edit()

	// --------------------------------------------------------------------


	/**
	 * Ajax results
	 *
	 * @access	public
	 * @return	string Entry listing table, AJAX response
	 */
	function ajax_results($output = "")
	{
		$output = $this->index();

		$this->EE->output->send_ajax_response($output);

	} // END ajax_results()

	// --------------------------------------------------------------------


	/**
	 * Ajax search saving
	 *
	 * @access	public
	 * @return	listing of saved searches
	 */
	function save_searches()
	{
		$controller = new Zenbu\controllers\Zenbu_SavedSearchesController;
		return $controller->actionSave();
	} // END save_search()

	// --------------------------------------------------------------------


	/**
	 * Ajax temp search saving
	 *
	 * @access	public
	 * @return	listing of saved searches
	 */
	public function cache_temp_filters()
	{
		$controller = new Zenbu\controllers\Zenbu_SavedSearchesController;
		echo $controller->actionCacheTempFilters();
		exit();
	} // END temp_save_search()

	// --------------------------------------------------------------------


	/**
	 * Ajax search updating, for titles
	 *
	 * @access	public
	 * @return	none
	 */
	function update_search()
	{
		$data[0]['rule_id']		= $this->EE->input->get_post('rule_id', TRUE);
		$data[0]['rule_label']	= $this->EE->input->get_post('search_title', TRUE);

		//	----------------------------------------
		//	Saving saved search order
		//	----------------------------------------
		if($this->EE->input->get_post('rule_order'))
		{
			// Unset the search label data
			unset($data[0]);

			foreach($this->EE->input->get_post('rule_order') as $order => $rule_id)
			{
				$data[$order]['rule_id'] = $rule_id;
				$data[$order]['rule_order'] = $order;
			}
		}

		$this->EE->zenbu_db->update_search($data);

		if(AJAX_REQUEST)
		{
			$this->EE->output->send_ajax_response($this->EE->input->get_post('search_title', TRUE));
		} else {
			$return_url		= BASE.AMP."C=addons_modules".AMP."M=show_module_cp".AMP."module=".$this->addon_short_name.AMP."method=manage_searches";
			$this->EE->functions->redirect($return_url);
		}

	} // END update_search()

	// --------------------------------------------------------------------


	/**
	 * Search deleting
	 *
	 * @access	public
	 * @return	delete single search and relist saved searches
	 */
	function delete_search()
	{
		$this->EE->zenbu_db->delete_search();
		$saved_searches_array = $this->EE->zenbu_get->_get_saved_searches();
		$output = $this->EE->zenbu_display->_display_saved_searches($saved_searches_array);

		if(AJAX_REQUEST)
		{
			$this->EE->output->send_ajax_response($output);
		} else {
			$return_url		= BASE.AMP."C=addons_modules".AMP."M=show_module_cp".AMP."module=".$this->addon_short_name.AMP."method=manage_searches";
			$this->EE->functions->redirect($return_url);
		}
	} // END delete_search()

	// --------------------------------------------------------------------


	/**
	 * Search copy/assignment
	 *
	 * @access	public
	 * @return	copy single search to member group(s)
	 */
	function copy_search()
	{
		$this->EE->zenbu_db->copy_search();

		$return_url		= BASE.AMP."C=addons_modules".AMP."M=show_module_cp".AMP."module=".$this->addon_short_name.AMP."method=manage_searches";
		$this->EE->functions->redirect($return_url);

	} // END copy_search()

	// --------------------------------------------------------------------


	/**
	 * Manage saved searches
	 */
	function searches() { return $this->manage_searches(); }
	function saved_searches() { return $this->manage_searches(); }
	function manage_searches()
	{
		$controller = new Zenbu\controllers\Zenbu_SavedSearchesController;
		return $controller->actionIndex();
	} // END manage_searches()

	// --------------------------------------------------------------------

	function fetch_search_filters()
	{
		@header('Content-Type: application/json');
		$controller = new Zenbu\controllers\Zenbu_SavedSearchesController;
		echo $controller->actionFetchFilters();
		exit();
	}


	function fetch_cached_temp_filters()
	{
		@header('Content-Type: application/json');
		$controller = new Zenbu\controllers\Zenbu_SavedSearchesController;
		echo $controller->actionFetchCachedTempFilters();
		exit();
	}

	function fetch_saved_searches()
	{
		@header('Content-Type: application/json');
		$controller = new Zenbu\controllers\Zenbu_SavedSearchesController;
		echo $controller->actionFetchSavedSearches();
		exit();
	}

	/**
	 * Display settings
	 *
	 * @access	public
	 */
	function display_settings() { return $this->settings(); }
	function settings()
	{
		$controller = new Zenbu\controllers\Zenbu_DisplaySettingsController;
		return $controller->actionIndex();
	} // END function settings

	function save_settings()
	{
		$controller = new Zenbu\controllers\Zenbu_DisplaySettingsController;
		return $controller->actionSave();
	}

	/**
	 * Member access settings
	 *
	 * @access	public
	 */
	public function permissions() {	return $this->settings_admin(); } 
	public function settings_admin()
	{
		$controller = new Zenbu\controllers\Zenbu_PermissionsController;
		return $controller->actionIndex();
	} // END function settings_admin

	function save_permissions()
	{
		$controller = new Zenbu\controllers\Zenbu_PermissionsController;
		return $controller->actionSave();
	}

}
// END CLASS

/* End of file mcp.download.php */
/* Location: ./system/expressionengine/third_party/modules/zenbu/mcp.zenbu.php */