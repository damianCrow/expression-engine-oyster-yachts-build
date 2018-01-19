<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Detour Pro Module Install/Update File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Mike Hughes - City Zen
 * @author      Tom Jaeger - EEHarbor
 * @link		http://eeharbor.com/detour_pro
 */

class Detour_pro_upd {

	public $version = '2.0.4';

	private $EE;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		if ( ! function_exists('ee') )
		{
			function ee()
			{
				return get_instance();
			}
		}
	}

	// ----------------------------------------------------------------

	/**
	 * Installation Method
	 *
	 * @return 	boolean 	TRUE
	 */
	public function install()
	{
		$mod_data = array(
			'module_name'			=> 'Detour_pro',
			'module_version'		=> $this->version,
			'has_cp_backend'		=> "y",
			'has_publish_fields'	=> 'n'
		);

		ee()->functions->clear_caching('db');
		ee()->db->insert('modules', $mod_data);

		// create settings table
		ee()->db->query("CREATE TABLE IF NOT EXISTS `".ee()->db->dbprefix('detour_pro_settings')."` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`site_id` int(11) NOT NULL DEFAULT '0',
			`url_detect` varchar(3) NOT NULL DEFAULT 'ee',
			`default_method` varchar(3) NOT NULL DEFAULT '301',
			`hit_counter` char(1) NOT NULL DEFAULT 'n',
			PRIMARY KEY (`id`),
			KEY `site_id` (`site_id`));");

		ee()->load->dbforge();

		/* Check to see if detour table exists */
		$sql = 'SHOW TABLES LIKE \'%detours%\'';
		$result = ee()->db->query($sql);

		$prev_install = ($result->num_rows) ? TRUE : FALSE;

		if($prev_install)
		{
	       	// Detour ext installed, update table to include MSM

	       	$query = ee()->db->get('detours', 1)->row_array();

	       	// Double check to see if site_id already exists
	       	if(!array_key_exists('site_id', $query))
	       	{
		        $fields = array(
	            	'site_id' => array('type' => 'int', 'constraint' => '4', 'unsigned' => TRUE),
	            	'start_date' => array('type' => 'date', 'null' => TRUE),
					'end_date' => array('type' => 'date', 'null' => TRUE),
				);
				ee()->dbforge->add_column('detours', $fields);

				// Apply site id of 1 to all existing detours
				ee()->db->update('detours', array('site_id' => 1), 'detour_id > 0');
	       	}
		}
		else
		{
			// Create detour tables and keys
			$fields = array
			(
				'detour_id'	=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
				'original_url'	=> array('type' => 'varchar', 'constraint' => '250'),
				'new_url'	=> array('type' => 'varchar', 'constraint' => '250', 'null' => TRUE, 'default' => NULL),
				'start_date' => array('type' => 'date', 'null' => TRUE),
				'end_date' => array('type' => 'date', 'null' => TRUE),
				'detour_method' => array('type' => 'int', 'constraint' => '3', 'unsigned' => TRUE, 'default' => '301'),
				'site_id' => array('type' => 'int', 'constraint' => '4', 'unsigned' => TRUE)
			);

			ee()->dbforge->add_field($fields);
			ee()->dbforge->add_key('detour_id', TRUE);
			ee()->dbforge->create_table('detours');
		}

		unset($fields);

		// Create hits table


		$sql = 'SHOW TABLES LIKE \'%detours_hits%\'';
		$result = ee()->db->query($sql);

		$prev_install = ($result->num_rows) ? TRUE : FALSE;

		if(!$prev_install)
		{
			$this->_create_table_hits();
		}

		// Enable the extension to prevent redirect erros while installing.
		ee()->db->where('class', 'Detour_pro_ext');
		ee()->db->update('extensions', array('enabled'=>'y'));

		return TRUE;
	}

	// ----------------------------------------------------------------

	/**
	 * Uninstall
	 *
	 * @return 	boolean 	TRUE
	 */
	public function uninstall()
	{
		$mod_id = ee()->db->select('module_id')
								->get_where('modules', array(
									'module_name'	=> 'Detour_pro'
								))->row('module_id');

		ee()->db->where('module_id', $mod_id)
					 ->delete('module_member_groups');

		ee()->db->where('module_name', 'Detour_pro')
					 ->delete('modules');

		ee()->load->dbforge();
		ee()->dbforge->drop_table('detours');
		ee()->dbforge->drop_table('detours_hits');
		ee()->dbforge->drop_table('detour_pro_settings');

		return TRUE;
	}

	// ----------------------------------------------------------------

	/**
	 * Module Updater
	 *
	 * @return 	boolean 	TRUE
	 */
	public function update($current = '')
	{
		if(version_compare($current, '2.0.1', '<'))
		{
			$this->_update_from_2_0_0();
		}

		// If you have updates, drop 'em in here.
		return TRUE;
	}

	private function _update_from_2_0_0()
	{
		// Create the charge settings table.
		ee()->db->query("CREATE TABLE IF NOT EXISTS `".ee()->db->dbprefix('detour_pro_settings')."` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`site_id` int(11) NOT NULL DEFAULT '0',
			`url_detect` varchar(3) NOT NULL DEFAULT 'ee',
			`default_method` varchar(3) NOT NULL DEFAULT '301',
			`hit_counter` char(1) NOT NULL DEFAULT 'n',
			PRIMARY KEY (`id`),
			KEY `site_id` (`site_id`));");

		// Copy the existing settings from the ee extension preferences.
		if(!isset($site_id) || empty($site_id)) $site_id = ee()->config->item('site_id');
		$sql = "SELECT settings FROM exp_extensions WHERE class='Detour_pro_ext'";
		$query = ee()->db->query( $sql );
		if ( $query->num_rows() == 0 ) return FALSE;

		ee()->load->helper('string');
 		$legacySettings = unserialize( $query->row('settings') );

 		$setting_data = array(
			'site_id' => $site_id,
			'url_detect' => (isset($legacySettings['url_detect']) ? $legacySettings['url_detect'] : 'ee'),
			'default_method' => (isset($legacySettings['default_method']) ? $legacySettings['default_method'] : '301'),
			'hit_counter' => (isset($legacySettings['hit_counter']) ? $legacySettings['hit_counter'] : 'n')
		);

 		// Find out if the settings exist, if not, insert them.
		ee()->db->where('site_id', ee()->config->item('site_id'));
		$exists = ee()->db->count_all_results('detour_pro_settings');

 		// Update or insert the legacy settings into the new table.
 		// The table should always exist when installed for the fist time as we inserted defaults and
 		// never exist for updates to an older install. This check is just for edge cases.
		if($exists) {
			ee()->db->where('site_id', ee()->config->item('site_id'));
			ee()->db->update('detour_pro_settings', $setting_data);
		} else {
			ee()->db->insert('detour_pro_settings', $setting_data);
		}
	}

	/* Private Functions */

	function _create_table_hits()
	{
			$fields = array(
				'hit_id' => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
				'detour_id'	=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
				'hit_date'	=> array('type' => 'datetime')
			);

			ee()->dbforge->add_field($fields);
			ee()->dbforge->add_key('hit_id', TRUE);

			return (ee()->dbforge->create_table('detours_hits')) ? TRUE : FALSE;
	}

}
/* End of file upd.detour_pro.php */
/* Location: /system/expressionengine/third_party/detour_pro/upd.detour_pro.php */
