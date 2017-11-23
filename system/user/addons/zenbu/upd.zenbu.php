<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( ! defined('PATH_THIRD')) { define('PATH_THIRD', APPPATH . 'third_party'); };
require_once PATH_THIRD . 'zenbu/addon.setup.php';

class Zenbu_upd {

	var $version = ZENBU_VER;
	var $addon_short_name = 'zenbu';
	var $standard_fields = array(
		'show_id',
		'show_title',
		'show_url_title',
		'show_channel',
		'show_categories',
		'show_status',
		'show_sticky',
		'show_entry_date',
		'show_author',
		'show_comments',
		'show_view',
		);
	var $permissions = array(
		'can_admin', 
		'can_copy_profile', 
		'can_access_settings', 
		'edit_replace', 
		'can_view_group_searches', 
		'can_admin_group_searches'
		);
	

	// --------------------------------------------------------------------

	/**
	 * Module Installer
	 *
	 * @access	public
	 * @return	bool
	 */	
	function install()
	{
		ee()->load->dbforge();

		$data = array(
			'module_name' => ucfirst($this->addon_short_name),
			'module_version' => $this->version,
			'has_cp_backend' => 'y'
		);

		ee()->db->insert('modules', $data);
		
		unset($data['module_name']);
		unset($data['module_version']);
		unset($data['has_cp_backend']);
		

		//	----------------------------------------
		//	Create exp_zenbu_display_settings table
		//	----------------------------------------

		$fields = array(
			'id'           => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'fieldType'    => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
			'userId'       => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
			'userGroupId'  => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
			'fieldId'      => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
			'sectionId'    => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
			'subSectionId' => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
			'show'         => array('type' => 'tinyint', 'constraint' => '1', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'default' => 0),
			'order'        => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
			'settings'     => array('type' => 'text'),
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('id', TRUE);
		ee()->dbforge->add_key('userId');
		ee()->dbforge->add_key('userGroupId');
		ee()->dbforge->create_table('zenbu_display_settings');

		//	----------------------------------------
		//	Create exp_zenbu_general_settings table
		//	----------------------------------------

		$fields = array(
			'id'          => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'userId'      => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
			'userGroupId' => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
			'setting'     => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
			'value'       => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('id', TRUE);
		ee()->dbforge->add_key('userId');
		ee()->dbforge->add_key('userGroupId');
		ee()->dbforge->create_table('zenbu_general_settings');

		//	----------------------------------------
		//	Create exp_zenbu_permissions table
		//	----------------------------------------

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('id', TRUE);
		ee()->dbforge->add_key('userId');
		ee()->dbforge->add_key('userGroupId');
		ee()->dbforge->create_table('zenbu_permissions');
		
		/**
		* ==============================
		* exp_zenbu_saved_searches table
		* ==============================
		*/
		$fields = array(
			'id'          => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'label'       => array('type'	=> 'text'),
			'userId'      => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE),
			'userGroupId' => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'default' => 0),
			'order'       => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'default' => 0),
			'site_id'     => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE),
		);
		
		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('id', TRUE);
		
		ee()->dbforge->create_table($this->addon_short_name.'_saved_searches');
		

		//	----------------------------------------
		//	Create zenbu_saved_search_filters table
		//	----------------------------------------
		ee()->load->dbforge();

		$fields = array(
			'id'               => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'searchId'         => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
			'filterAttribute1' => array('type'	=> 'varchar', 'constraint' => 255, 'null' => true),
			'filterAttribute2' => array('type'	=> 'varchar', 'constraint' => 255, 'null' => true),
			'filterAttribute3' => array('type'	=> 'varchar', 'constraint' => 255, 'null' => true),
			'order'            => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE),
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('id', TRUE);
		ee()->dbforge->create_table('zenbu_saved_search_filters');

		/**
		* ===============================
		* exp_zenbu_filter_cache table
		* ===============================
		*/
	
		$fields = array(
			'cache_id'		=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'member_id'		=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE),
			'site_id'		=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE),
			'save_date'		=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE),
			'filter_rules'	=> array('type'	=> 'mediumtext'),
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('cache_id', TRUE);
		
		ee()->dbforge->create_table($this->addon_short_name . '_filter_cache');
		

		return TRUE;
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Module Uninstaller
	 *
	 * @access	public
	 * @return	bool
	 */
	function uninstall()
	{
		ee()->load->dbforge();

		ee()->db->select('module_id');
		$query = ee()->db->get_where('modules', array('module_name' => $this->addon_short_name));

		ee()->db->where('module_id', $query->row('module_id'));
		ee()->db->delete('module_member_groups');

		ee()->db->where('module_name', $this->addon_short_name);
		ee()->db->delete('modules');

		ee()->db->where('class', $this->addon_short_name);
		ee()->db->delete('actions');

		ee()->dbforge->drop_table($this->addon_short_name.'_permissions');
		ee()->dbforge->drop_table($this->addon_short_name.'_general_settings');
		ee()->dbforge->drop_table($this->addon_short_name.'_display_settings');
		ee()->dbforge->drop_table($this->addon_short_name.'_saved_searches');
		ee()->dbforge->drop_table($this->addon_short_name.'_saved_search_filters');
		ee()->dbforge->drop_table($this->addon_short_name.'_filter_cache');
		
		return TRUE;
	}



	// --------------------------------------------------------------------

	/**
	 * Module Updater
	 *
	 * @access	public
	 * @return	bool
	 */	
	
	function update($current='')
	{
		//echo $current;
		if ($current == $this->version)
		{
			return FALSE;
		}
		
		/**
		* Version 1.0 => 1.1 Update script
		* --------------------------------
		* Ditch primary keys for member_group_id, add site_id column
		* and add incrementing id column (might come in handy).
		* First site_id is used to update what is already available,
		* then data is copied for other site_ids
		**/
		if (version_compare($current, "1.1", '<')) 
		{
			ee()->load->dbforge();
			ee()->db->query("ALTER TABLE exp_".$this->addon_short_name." DROP PRIMARY KEY");
			ee()->db->query("ALTER TABLE exp_".$this->addon_short_name." ADD COLUMN site_id INT(10) NOT NULL AFTER member_group_id");
			ee()->db->query("ALTER TABLE exp_".$this->addon_short_name." ADD COLUMN id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
			
			$sites = ee()->db->query("SELECT site_id FROM exp_sites ORDER BY site_id");
			if($sites->num_rows() > 0)
			{
				foreach($sites->result_array() as $row)
				{
					$site_id[] = $row['site_id'];
				}
			}
			
			ee()->db->where_not_in('member_group_id', array(0));
			$table_query = ee()->db->get('exp_'.$this->addon_short_name);
			
			foreach($site_id as $key => $site_id)
			{
				
				if($key == 0)
				{
					// Set first site_id to newly created columns
					foreach($table_query->result_array() as $data)
					{
						$data_update['site_id'] = $site_id;
						ee()->db->update($this->addon_short_name, $data_update);
					}
				} else {
					
					// Copy settings to other site_ids
					foreach($table_query->result_array() as $data)
					{
						unset($data['id']);
						$data['site_id'] = $site_id;
						ee()->db->query(ee()->db->insert_string($this->addon_short_name, $data, TRUE));
					}
				}
			}
			
		} 
		
		if (version_compare($current, "1.2.1", '<'))
		{
			$zenbu_query = ee()->db->query("SELECT * FROM exp_zenbu");
			if($zenbu_query->num_rows() > 0)
			{
				foreach($zenbu_query->result_array() as $key => $row)
				{
					$show_fields_settings = unserialize($row['show_fields']);
					foreach($show_fields_settings as $channel_id => $settings_array)
					{
						$show_fields_settings[$channel_id]['show_title'] = 'y';	
					}
					
					$show_fields_settings = serialize($show_fields_settings);
					$data['show_fields'] = $show_fields_settings;
					
					ee()->db->where('id', $row['id']);
					ee()->db->update($this->addon_short_name, $data);
					
				}
			}
		}
		
		if (version_compare($current, "1.3", '<'))
		{
			/**
			* ==============================
			* exp_zenbu_saved_searches table
			* ==============================
			*/
			ee()->load->dbforge();
			
			$fields = array(
				'rule_id'				=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
				'member_id'				=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE),
				'rule_label'			=> array('type'	=> 'text'),
				'rules'					=> array('type'	=> 'mediumtext'),
			);
			
			ee()->dbforge->add_field($fields);
			ee()->dbforge->add_key('rule_id', TRUE);
			
			ee()->dbforge->create_table($this->addon_short_name.'_saved_searches');
			
			
			/**
			* ================================
			* Adding multi-channel preferences
			* ================================
			*/
			$zenbu_query = ee()->db->query("SELECT * FROM exp_zenbu");
			if($zenbu_query->num_rows() > 0)
			{
				foreach($zenbu_query->result_array() as $key => $row)
				{
					$data_show_fields = unserialize($row['show_fields']);
					$data_show_custom_fields = unserialize($row['show_custom_fields']);
					$data_field_order = unserialize($row['field_order']);
					$data_extra_options = unserialize($row['extra_options']);
					
					// -----------------------------------------------------------------
					// Data to be inserted for multi-channel listings (i.e. channel "0")
					// -----------------------------------------------------------------
					$data_show_fields['0'] = array(
						'show_id'			=> 'y',
						'show_title'		=> 'y',
						'show_url_title'	=> 'y',
						'show_channel'		=> 'y',
						'show_categories'	=> 'y',
						'show_status'		=> 'y',
						'show_sticky'		=> 'y',
						'show_entry_date'	=> 'y',
						'show_author'		=> 'y',
						'show_comments'		=> 'y',
						'show_view'			=> 'y',
					);
					$data_show_custom_fields['0'] = array(
						'show_custom_fields'	=> "",
					);
					
					// Create a basic order for standard fields
					$field_order = array();
					foreach($this->standard_fields as $key => $value)
					{
						$field_order[] = $value;
					}
					// Serialize to pack in database columns
					$field_order = array_flip($field_order);
					
					$data_field_order['0'] = array(
						'field_order' 		=> $field_order,
					);
					
					$data_extra_options['0'] = array(
					
						'extra_options' 	=> array(
						'text_option_1' 	=> '',
						'matrix_option_1'	=> '',
						),
					);
					
					
					$db_data['member_group_id'] = $row['member_group_id'];					// Default settings (everything turned on)
					$db_data['site_id'] = $row['site_id'];										
					$db_data['show_fields'] = serialize($data_show_fields);					// Default "show everything" settings
					$db_data['show_custom_fields'] = serialize($data_show_custom_fields);	// Default settings for "fields to show"
					$db_data['field_order'] = serialize($data_field_order);					// Default "field order" settings
					$db_data['extra_options'] = serialize($data_extra_options);				// Default settings for extra field options
					$db_data['can_admin'] = $row['can_admin'];								// Can access the member permissions
					$db_data['can_copy_profile'] = $row['can_copy_profile'];				// Can save own profile to other members
					$db_data['can_access_settings'] = $row['can_access_settings'];			// Can see the "Settings" tab in addon
					$db_data['edit_replace'] = $row['edit_replace'];						// Enables extension to replace Edit link in Content => Edit for these members
	
					$sql = ee()->db->insert_string($this->addon_short_name, $db_data);
					ee()->db->query($sql);
					
					
				}
			}
			
		}
		
		if (version_compare($current, "1.4.0", '<'))
		{
			ee()->db->query("ALTER TABLE exp_".$this->addon_short_name." ADD COLUMN general_settings TEXT NOT NULL AFTER site_id");
			$db_data['general_settings'] = serialize(array());
			
			$zenbu_query = ee()->db->query("SELECT id FROM exp_zenbu");
			if($zenbu_query->num_rows() > 0)
			{
				foreach($zenbu_query->result_array() as $key => $row)
				{
					$id[] = $row['id'];
					
				}
			}
			
			$zenbu_query->free_result();
			
			
			foreach($id as $key => $val)
			{
				ee()->db->where('id', $val);
				ee()->db->update($this->addon_short_name, $db_data);
			}
			
		}

		if (version_compare($current, "1.5.1", '<'))
		{
			ee()->load->dbforge();
			/**
			* ===============================
			* exp_zenbu_member_settings table
			* ===============================
			*/
			
			$fields = array(
			'member_id'				=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'site_id'				=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE),
			'general_settings'		=> array('type'	=> 'text'),
			'show_fields'			=> array('type'	=> 'mediumtext'),
			'show_custom_fields'	=> array('type'	=> 'text'),
			'field_order'			=> array('type'	=> 'mediumtext'),
			'extra_options'			=> array('type'	=> 'mediumtext'),
			'can_admin'				=> array('type'	=> 'varchar', 'constraint'	=> '1'),
			'can_copy_profile'		=> array('type'	=> 'varchar', 'constraint'	=> '1'),
			'can_access_settings'	=> array('type'	=> 'varchar', 'constraint'	=> '1'),
			'edit_replace'			=> array('type'	=> 'varchar', 'constraint'	=> '1'),
			);

			ee()->dbforge->add_field($fields);
			ee()->dbforge->add_key('member_id', TRUE);
			
			ee()->dbforge->create_table($this->addon_short_name . '_member_settings');
		}
		

		if (version_compare($current, "1.5.4", '<'))
		{
			/**
			 * Removing the primary key/auto increment from exp_zenbu_member_settings
			 * since this cannot be done in MSM setups
			 */
			$check_primary = ee()->db->query("SHOW index FROM exp_zenbu_member_settings WHERE Key_name = 'PRIMARY'");
			if($check_primary->num_rows() > 0)
			{
				$sql = array(	'ALTER TABLE exp_zenbu_member_settings MODIFY member_id INT(10) NOT NULL',
								'ALTER TABLE exp_zenbu_member_settings DROP PRIMARY KEY',);
				foreach($sql as $key => $query)
				{
					ee()->db->query($query);
				}
			}
		}

		if (version_compare($current, "1.5.5", '<'))
		{
			ee()->db->query("ALTER TABLE exp_".$this->addon_short_name."_saved_searches ADD COLUMN site_id INT(10) DEFAULT " . ee()->session->userdata['site_id'] . " AFTER member_id");
		}

		if (version_compare($current, "1.8.0b1", '<'))
		{
			ee()->db->query("ALTER TABLE exp_".$this->addon_short_name."_saved_searches ADD COLUMN member_group_id INT(10) DEFAULT 0 AFTER member_id");
			ee()->db->query("ALTER TABLE exp_".$this->addon_short_name."_saved_searches ADD COLUMN rule_order INT(10) DEFAULT 0 AFTER member_group_id");
			ee()->db->query("ALTER TABLE exp_".$this->addon_short_name." ADD COLUMN can_view_group_searches VARCHAR(1) DEFAULT 'n'");
			ee()->db->query("ALTER TABLE exp_".$this->addon_short_name." ADD COLUMN can_admin_group_searches VARCHAR(1) DEFAULT 'n'");

			//	----------------------------------------
			//	Set new defaults for access settings
			//	----------------------------------------
			$alt_cols = array(
				'can_access_settings'	=> 'n',
				'can_admin'				=> 'n',
				'can_copy_profile'		=> 'n',
				'edit_replace'			=> 'y'
				);

			foreach($alt_cols as $col => $val)
			{
				ee()->db->query("ALTER TABLE exp_".$this->addon_short_name." ALTER COLUMN " . $col . " SET DEFAULT '" . $val . "'");
			}

		}

		if (version_compare($current, "1.8.6", '<'))
		{
			ee()->load->dbforge();

			$fields = array(
				'cache_id'		=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
				'member_id'		=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE),
				'site_id'		=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE),
				'save_date'		=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE),
				'filter_rules'	=> array('type'	=> 'mediumtext'),
			);

			ee()->dbforge->add_field($fields);
			ee()->dbforge->add_key('cache_id', TRUE);
			
			ee()->dbforge->create_table($this->addon_short_name . '_filter_cache');
		}

		if (version_compare($current, "1.99.0", '<='))
		{
			//	----------------------------------------
			//	Create Legacy tables
			//	----------------------------------------
			ee()->db->query("CREATE TABLE exp_zenbu_saved_searches_legacy LIKE exp_zenbu_saved_searches");
			ee()->db->query("INSERT INTO exp_zenbu_saved_searches_legacy SELECT * FROM exp_zenbu_saved_searches");
			ee()->db->query("CREATE TABLE exp_zenbu_filter_cache_legacy LIKE exp_zenbu_filter_cache");
			ee()->db->query("INSERT INTO exp_zenbu_filter_cache_legacy SELECT * FROM exp_zenbu_filter_cache");
			ee()->db->query("RENAME TABLE exp_zenbu TO exp_zenbu_legacy");
			ee()->db->query("RENAME TABLE exp_zenbu_member_settings TO exp_zenbu_member_settings_legacy");

			
			ee()->db->query("ALTER TABLE exp_zenbu_saved_searches CHANGE rule_id id int(11) NOT NULL AUTO_INCREMENT");
			ee()->db->query("ALTER TABLE exp_zenbu_saved_searches CHANGE member_group_id userGroupId INT(10) DEFAULT NULL");
			ee()->db->query("ALTER TABLE exp_zenbu_saved_searches CHANGE member_id userId INT(10) DEFAULT NULL");
			ee()->db->query("ALTER TABLE exp_zenbu_saved_searches CHANGE rule_label label varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL");
			ee()->db->query("ALTER TABLE exp_zenbu_saved_searches CHANGE rule_order `order` int(10) DEFAULT NULL");
			ee()->db->query("ALTER TABLE exp_zenbu_saved_searches MODIFY COLUMN label varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL AFTER id");

			//	----------------------------------------
			//	Create zenbu_saved_search_filters table
			//	----------------------------------------
			ee()->load->dbforge();

			$fields = array(
				'id'		=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
				'searchId'		=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
				'filterAttribute1'	=> array('type'	=> 'varchar', 'constraint' => 255, 'null' => true),
				'filterAttribute2'	=> array('type'	=> 'varchar', 'constraint' => 255, 'null' => true),
				'filterAttribute3'	=> array('type'	=> 'varchar', 'constraint' => 255, 'null' => true),
				'order'		=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE),
			);

			ee()->dbforge->add_field($fields);
			ee()->dbforge->add_key('id', TRUE);
			ee()->dbforge->create_table('zenbu_saved_search_filters');

			//	----------------------------------------
			//	Transfer filters to new table
			//	----------------------------------------
			$saved_searched_sql = ee()->db->query("SELECT * FROM exp_zenbu_saved_searches");

			if($saved_searched_sql->num_rows() > 0)
			{
				foreach($saved_searched_sql->result() as $row)
				{
					$filters = unserialize($row->rules);
					$c = 0;
					$data = array();

					foreach($filters as $key => $filter)
					{
						$data['searchId']         = $row->id;
						if( is_numeric($key))
						{
							$data['filterAttribute1'] = $filter['field'];
							$data['filterAttribute2'] = $filter['cond'];
							$data['filterAttribute3'] = $filter['val'];
							$data['order']            = $c;
							ee()->db->insert('exp_zenbu_saved_search_filters', $data);
							unset($data);
							$c++;
						}
						else
						{
							if($key == 'limit')
							{
								$data['filterAttribute1'] = $key;
								$data['filterAttribute2'] = $filter;
								$data['order']            = $c;
								ee()->db->insert('exp_zenbu_saved_search_filters', $data);
								unset($data);
								$c++;
							}
							elseif($key == 'orderby')
							{
								$data['filterAttribute1'] = $key;
								$data['filterAttribute2'] = $filter;
								$data['filterAttribute3'] = strtoupper($filters['sort']);
								$data['order']            = $c;
								ee()->db->insert('exp_zenbu_saved_search_filters', $data);
								unset($data);
								$c++;
							}
						}
					}
				}
			}

			//	----------------------------------------
			//	Get rid of the old rules column
			//	in exp_zenbu_saved_searches
			//	----------------------------------------
			ee()->db->query("ALTER TABLE exp_zenbu_saved_searches DROP COLUMN rules");
			unset($data);

			//	----------------------------------------
			//	Create exp_zenbu_display_settings table
			//	----------------------------------------

			$fields = array(
				'id'           => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
				'fieldType'    => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
				'userId'       => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
				'userGroupId'  => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
				'fieldId'      => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
				'sectionId'    => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
				'subSectionId' => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
				'show'         => array('type' => 'tinyint', 'constraint' => '1', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'default' => 0),
				'order'        => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
				'settings'     => array('type' => 'text'),
			);

			ee()->dbforge->add_field($fields);
			ee()->dbforge->add_key('id', TRUE);
			ee()->dbforge->add_key('userId');
			ee()->dbforge->add_key('userGroupId');
			ee()->dbforge->create_table('zenbu_display_settings');

			//	----------------------------------------
			//	Get old Member Group and Member settings
			//	----------------------------------------

			$sql_member_groups = ee()->db->query("SELECT * FROM exp_zenbu_legacy");
			$sql_members = ee()->db->query("SELECT * FROM exp_zenbu_member_settings_legacy");

			foreach(array('sql_member_groups', 'sql_members') as $query_name)
			{
				if(${$query_name}->num_rows() > 0)
				{
					foreach(${$query_name}->result() as $row)
					{
						$old['general_settings']   = unserialize($row->general_settings);
						$old['show_fields']        = unserialize($row->show_fields);
						$old['show_custom_fields'] = unserialize($row->show_custom_fields);
						$old['field_order']        = unserialize($row->field_order);
						$old['extra_options']      = unserialize($row->extra_options);

						//	----------------------------------------
						//	Processing old data, putting in new table
						//	----------------------------------------
						
						foreach($old['field_order'] as $channel_id => $field_order)
						{
							$c = 1;
							foreach($field_order['field_order'] as $field_order_key => $field_order_val)
							{
								if($query_name == 'sql_member_groups')
								{
									$data['userGroupId'] = $row->member_group_id;								
								}
								else
								{
									$data['userId'] = $row->member_id;	
								}
								$data['sectionId'] = $channel_id;
								$data['fieldType'] = strncmp($field_order_key, 'show_', 5) == 0 ? str_replace('show_', '', $field_order_key) : 'field';
								$data['fieldId'] = strncmp($field_order_key, 'show_', 5) == 0 ? 0 : str_replace('field_', '', $field_order_key);
								$data['order'] = $c;
								$c++;

								//	----------------------------------------
								//	Show & Extra Settings
								//	----------------------------------------
								if(strncmp($field_order_key, 'show_', 5) == 0)
								{
									// Show
									foreach($old['show_fields'][$channel_id] as $show_fields_key => $show_fields_val)
									{
										if($field_order_key == $show_fields_key && $show_fields_val == 'y')
										{
											$data['show'] = 1; 
										}
									}

									// Extra Settings
									foreach($old['extra_options'][$channel_id]['extra_options'] as $extra_options_key => $extra_options_val)
									{
										if($extra_options_key == $field_order_key)
										{
											$data['settings'] = json_encode($extra_options_val);
										}
									}
								}
								else
								{
									// Show
									foreach($old['show_custom_fields'][$channel_id] as $show_custom_fields_key => $show_custom_fields_val)
									{
										$show_custom_fields_val = explode('|', $show_custom_fields_val);
										foreach($show_custom_fields_val as $field_id)
										{
											if(str_replace('field_', '', $field_order_key) == $field_id)
											{
												$data['show'] = 1; 									
											}
										}
									}

									// Extra Settings
									foreach($old['extra_options'][$channel_id]['extra_options'] as $extra_options_key => $extra_options_val)
									{
										if($extra_options_key == $field_order_key)
										{
											$data['settings'] = json_encode($extra_options_val);
										}
									}
								}
								
								ee()->db->insert('zenbu_display_settings', $data);
								unset($data);
							}

						}
					}
				}
			}

			//	----------------------------------------
			//	Create exp_zenbu_general_settings table
			//	----------------------------------------

			$fields = array(
				'id'           => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
				'userId'       => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
				'userGroupId'  => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => FALSE, 'null' => true),
				'setting'    => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
				'value'    => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
			);

			ee()->dbforge->add_field($fields);
			ee()->dbforge->add_key('id', TRUE);
			ee()->dbforge->add_key('userId');
			ee()->dbforge->add_key('userGroupId');
			ee()->dbforge->create_table('zenbu_general_settings');

			//	----------------------------------------
			//	Create exp_zenbu_permissions table
			//	----------------------------------------

			ee()->dbforge->add_field($fields);
			ee()->dbforge->add_key('id', TRUE);
			ee()->dbforge->add_key('userId');
			ee()->dbforge->add_key('userGroupId');
			ee()->dbforge->create_table('zenbu_permissions');

			//	----------------------------------------
			//	Porting over General Settings and Permissions
			//	----------------------------------------
			foreach(array('sql_member_groups', 'sql_members') as $query_name)
			{
				if(${$query_name}->num_rows() > 0)
				{
					foreach(${$query_name}->result() as $row)
					{
						$general_settings   = unserialize($row->general_settings);

						//	----------------------------------------
						//	Processing old data, putting in new table
						//	----------------------------------------
						if( ! empty($general_settings) && is_array($general_settings))
						foreach($general_settings as $setting => $value)
						{
							if($query_name == 'sql_member_groups')
							{
								$data['userGroupId'] = $row->member_group_id;	
								unset($data['userId']);							
							}
							else
							{
								$data['userId'] = $row->member_id;
								unset($data['userGroupId']);
							}
							$data['setting'] = $setting;
							$data['value'] = $value;
							
							ee()->db->insert('zenbu_general_settings', $data);
							unset($data);
						}
					}
					
					unset($data);							

					if($query_name == 'sql_member_groups')
					{
						foreach(${$query_name}->result() as $row)
						{
							$data['userGroupId'] = $row->member_group_id;
							foreach($this->permissions as $perm)
							{
								$data['setting'] = $perm;
								$data['value']   = $row->{$perm};
								ee()->db->insert('zenbu_permissions', $data);
							}
						}
					}
				}
			}

		} // END 1.99 UPDATE SCRIPT
		
	return TRUE; 

	}
	
}
/* END Class */

/* End of file upd.zenbu.php */
/* Location: ./system/expressionengine/third_party/modules/zenbu/upd.zenbu.php */