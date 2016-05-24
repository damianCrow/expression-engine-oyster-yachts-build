<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Link Vault Module Install/Update File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Masuga Design
 * @link		http://www.masugadesign.com
 */

class Link_vault_upd {

	public $version = '1.4.2';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Always load the dbforge library for Link Vault installations and updates
		ee()->load->dbforge();
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
			'module_name'			=> 'Link_vault',
			'module_version'		=> $this->version,
			'has_cp_backend'		=> "y",
			'has_publish_fields'	=> 'n'
		);

		ee()->db->insert('modules', $mod_data);

		//--------------------------------------
		// MODULE ACTIONS
		//--------------------------------------

		$actions = array(
			'follow_encrypted_url',
			'download',
			'remote_download',
			's3_download',
			'follow_pretty_url'
		);

		foreach ($actions as $action) {
			ee()->db->insert('actions', array(
				'class'  => 'Link_vault',
				'method' => $action
			));
		}

		//--------------------------------------
		// Create LINK_VAULT_DOWNLOADS
		//--------------------------------------

		$common_columns = array(
			'id'			=> array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'site_id'		=> array('type' => 'TINYINT', 'unsigned' => TRUE, 'default' => 1),
			'entry_id'		=> array('type' => 'INT', 'unsigned' => TRUE, 'default' => 0),
			'unix_time'		=> array('type' => 'BIGINT', 'unsigned' => TRUE, 'default' => 0),
			'member_id'		=> array('type' => 'INT', 'unsigned' => TRUE, 'default' => 0),
			'remote_ip'		=> array('type' => 'VARCHAR', 'constraint' => 39, 'default' => ''),
			's3_bucket'		=> array('type' => 'VARCHAR', 'constraint' => '100', 'default' => ''),
			'directory'		=> array('type' => 'VARCHAR', 'constraint' => 200, 'default' => ''),
			'file_name'		=> array('type' => 'VARCHAR', 'constraint' => 200, 'default' => ''),
			'download_as'	=> array('type' => 'VARCHAR', 'constraint' => 200, 'default' => '')
		);

		ee()->dbforge->add_field($common_columns);

		// This field is added here because it isn't needed in the leeches table
		ee()->dbforge->add_field(array(
			'is_link_click' => array('type' => 'VARCHAR', 'constraint' => 1, 'default' => ''),
			'pretty_url_id'	=> array('type' => 'INT', 'unsigned' => TRUE, 'default' => 0)
		));
		ee()->dbforge->add_key('id', TRUE);
		ee()->dbforge->add_key('entry_id');
		ee()->dbforge->add_key('file_name');
		ee()->dbforge->create_table('link_vault_downloads', TRUE);

		//--------------------------------------
		// Create LINK_VAULT_LEECHES
		//--------------------------------------

		ee()->dbforge->add_field($common_columns);
		ee()->dbforge->add_key('id', TRUE);
		ee()->dbforge->add_key('entry_id');
		ee()->dbforge->add_key('file_name');
		ee()->dbforge->create_table('link_vault_leeches', TRUE);

		//--------------------------------------
		// Create LINK_VAULT_SETTINGS
		//--------------------------------------

		$settings_columns = array(
			'site_id'			=> array('type' => 'TINYINT', 'unsigned' => TRUE, 'default' => 1),
			'salt'				=> array('type' => 'VARCHAR', 'constraint' => 20, 'default' => ''),
			'hidden_folder'		=> array('type' => 'VARCHAR', 'constraint' => 200, 'default' => ''),
			'leech_url'			=> array('type' => 'VARCHAR', 'constraint' => 200, 'default' => ''),
			'missing_url'		=> array('type' => 'VARCHAR', 'constraint' => 200, 'default' => ''),
			'block_leeching'	=> array('type' => 'TINYINT', 'unsigned' => TRUE, 'default' => 0),
			'log_leeching'      => array('type' => 'TINYINT', 'unsigned' => TRUE, 'default' => 0),
			'log_link_clicks'	=> array('type' => 'TINYINT', 'unsigned' => TRUE, 'default' => 0),
			'aws_access_key'	=> array('type' => 'VARCHAR', 'constraint' => 200, 'default' => ''),
			'aws_secret_key'	=> array('type' => 'VARCHAR', 'constraint' => 200, 'default' => '')
		);

		ee()->dbforge->add_field($settings_columns);
		ee()->dbforge->add_key('site_id', TRUE);
		ee()->dbforge->create_table('link_vault_settings', TRUE);

		$custom_fields_columns = array(
			'field_id'		=> array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'site_id'		=> array('type' => 'TINYINT', 'unsigned' => TRUE, 'default' => 0),
			'field_label'	=> array('type' => 'VARCHAR', 'constraint' => 200, 'default' => ''),
			'field_name'	=> array('type' => 'VARCHAR', 'constraint' => 200, 'default' => '')
		);

		//--------------------------------------
		// Create LINK_VAULT_CUSTOM_FIELDS
		//--------------------------------------
		ee()->dbforge->add_field($custom_fields_columns);
		ee()->dbforge->add_key('field_id', TRUE);
		ee()->dbforge->create_table('link_vault_custom_fields', TRUE);

		//--------------------------------------
		// Create LINK_VAULT_REPORTS
		//--------------------------------------
		$this->_create_link_vault_reports_table();

		//--------------------------------------
		// Create LINK_VAULT_PRETTY_URLS
		//--------------------------------------
		$this->_create_pretty_urls_table();

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
									'module_name'	=> 'Link_vault'
								))->row('module_id');

		ee()->db->where('module_id', $mod_id)
					 ->delete('module_member_groups');

		ee()->db->where('module_name', 'Link_vault')
					 ->delete('modules');

		// Delete all Link Vault actions
		ee()->db->where('class', 'Link_vault');
		ee()->db->delete('actions');

		// Drop all Link Vault tables
		ee()->dbforge->drop_table('link_vault_settings');
		ee()->dbforge->drop_table('link_vault_downloads');
		ee()->dbforge->drop_table('link_vault_leeches');
		ee()->dbforge->drop_table('link_vault_custom_fields');
		ee()->dbforge->drop_table('link_vault_reports');
		ee()->dbforge->drop_table('link_vault_pretty_urls');

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
		//----------
		// v1.1.0
		//----------
		if (version_compare('1.1.0', $current) == 1) {

			if (! ee()->db->field_exists('is_link_click', 'link_vault_downloads')) {
				$fields = array(
					'is_link_click' => array('type' => 'VARCHAR', 'constraint' => 1, 'default' => '')
				);
				ee()->dbforge->add_column('link_vault_downloads', $fields);
			}

			ee()->db->where('is_link_click', '');
			ee()->db->update('link_vault_downloads', array(
				'is_link_click' => 'n'
			));

			if (! ee()->db->field_exists('log_link_clicks', 'link_vault_settings')) {
				$settings_field = array(
					'log_link_clicks' => array('type' => 'TINYINT', 'unsigned' => TRUE, 'default' => 0)
				);
				ee()->dbforge->add_column('link_vault_settings', $settings_field);
			}
		}

		//----------
		// v1.2.0
		//----------
		if (version_compare('1.2.0', $current) == 1) {

			if (! ee()->db->field_exists('missing_url', 'link_vault_settings')) {
				$fields = array(
					'missing_url' => array('type' => 'VARCHAR', 'constraint' => 200, 'default' => '')
				);

				// Add 'missing_url' setting to the settings table
				ee()->dbforge->add_column('link_vault_settings', $fields);
			}

			// Create the LINK_VAULT_REPORTS table
			if (! ee()->db->table_exists('link_vault_reports') ) {
				$this->_create_link_vault_reports_table();
			}
		}

		//----------
		// v1.2.4
		//----------
		if (version_compare('1.2.4', $current) == 1) {
			// Insert action for S3 downloads
			$s3_action_data = array(
				'class'		=> 'Link_vault',
				'method'	=> 's3_download'
			);
			ee()->db->insert('actions', $s3_action_data);

			// Add 's3_bucket' column to link_vault_downloads / leeches tables
			if (! ee()->db->field_exists('s3_bucket', 'link_vault_downloads') && ! ee()->db->field_exists('s3_bucket', 'link_vault_leeches')) {
				$log_field = array(
					's3_bucket' => array('type' => 'VARCHAR', 'constraint' => '100', 'default' => '')
				);
				ee()->dbforge->add_column('link_vault_downloads', $log_field);
				ee()->dbforge->add_column('link_vault_leeches', $log_field);
			}

			// Add new S3 settings columns
			if (! ee()->db->field_exists('aws_access_key', 'link_vault_settings') && ! ee()->db->field_exists('aws_secret_key', 'link_vault_settings')) {
				$settings_columns = array(
					'aws_access_key'	=> array('type' => 'VARCHAR', 'constraint' => 200, 'default' => ''),
					'aws_secret_key'	=> array('type' => 'VARCHAR', 'constraint' => 200, 'default' => '')
				);
				ee()->dbforge->add_column('link_vault_settings', $settings_columns);
			}
		}

		//----------
		// v1.3.0
		//----------
		if (version_compare('1.3.0', $current) == 1) {

			// Insert action for handling pretty URLs
			$purl_action_data = array(
				'class'		=> 'Link_vault',
				'method'	=> 'follow_pretty_url'
			);
			ee()->db->insert('actions', $purl_action_data);

			// Create the LINK_VAULT_PRETTY_URLS table
			if (! ee()->db->table_exists('link_vault_pretty_urls') ) {
				$this->_create_pretty_urls_table();
			}

			// Add the pretty_url_id column to link_vault_downloads
			$columns = array(
				'pretty_url_id'	=> array('type' => 'INT', 'unsigned' => TRUE, 'default' => 0)
			);
			ee()->dbforge->add_column('link_vault_downloads', $columns);

		}

		//----------
		// v1.3.4
		//----------
		if (version_compare('1.3.4', $current) == 1) {

			// Add the download_as column to link_vault_downloads and link_vault_leeches
			$columns = array(
				'download_as'	=> array('type' => 'VARCHAR', 'constraint' => 200, 'default' => '')
			);
			if (! ee()->db->field_exists('download_as', 'link_vault_downloads') ) {
				ee()->dbforge->add_column('link_vault_downloads', $columns);
			}
			if (! ee()->db->field_exists('download_as', 'link_vault_leeches') ) {
				ee()->dbforge->add_column('link_vault_leeches', $columns);
			}

		}

		return TRUE;
	}

	// ----------------------------------------------------------------

	/**
	 * Create link_vault_reports Table
	 *
	 * This method is responsible for creating the LINK_VAULT_REPORTS table
	 * that was introduced in Link Vault v1.2.0.
	 *
	 * @return void
	 */
	protected function _create_link_vault_reports_table()
	{
		$reports_columns = array(
			'id'			=> array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'site_id'		=> array('type' => 'TINYINT', 'unsigned' => TRUE, 'default' => 1),
			'title'			=> array('type' => 'VARCHAR', 'constraint' => 200, 'default' => ''),
			'criteria'		=> array('type' => 'TEXT')
		);

		ee()->dbforge->add_field($reports_columns);
		ee()->dbforge->add_key('id', TRUE);
		ee()->dbforge->create_table('link_vault_reports', TRUE);
	}

	// ----------------------------------------------------------------

	/**
	 * Create link_vault_pretty_urls Table
	 *
	 * This method is responsible for creating the LINK_VAULT_PRETTY_URLS
	 * table that was introduced in Link Vault v1.3.0.
	 *
	 * @return void
	 */
	protected function _create_pretty_urls_table()
	{
		$columns = array(
			'id'		=> array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'site_id'	=> array('type' => 'INT', 'unsigned' => TRUE),
			'text'		=> array('type' => 'VARCHAR', 'constraint' => '255', 'default' => ''),
			'url'		=> array('type' => 'TEXT')
		);

		ee()->dbforge->add_field($columns);
		ee()->dbforge->add_key('id', TRUE);
		ee()->dbforge->add_key('text', TRUE);
		ee()->dbforge->create_table('link_vault_pretty_urls', TRUE);
	}


}

/* End of file upd.link_vault.php */
