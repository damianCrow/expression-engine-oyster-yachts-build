<?php

class Social_feed_upd {

	public $version = '1.0.0';
	public $module_name = 'Social_feed';
	
	function install() {
		ee()->load->dbforge();

		// youtube table
		$fields = array(
			'id'          => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'title'       => array('type' => 'varchar', 'constraint' => 255),
			'image'       => array('type' => 'varchar', 'constraint' => 255),
			'url'         => array('type' => 'varchar', 'constraint' => 255),
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('id', TRUE);
		ee()->dbforge->create_table('social_youtube');

		// twitter table
		$fields = array(
			'id'          => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'tweet'       => array('type' => 'text'),
			'url'         => array('type' => 'varchar', 'constraint' => 255),
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('id', TRUE);
		ee()->dbforge->create_table('social_twitter');

		// instagram table
		$fields = array(
			'id'          => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'title'       => array('type' => 'text'),
			'image'       => array('type' => 'varchar', 'constraint' => 255),
			'url'         => array('type' => 'varchar', 'constraint' => 255),
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('id', TRUE);
		ee()->dbforge->create_table('social_instagram');

		ee()->db->insert('modules', array(
			'module_name'    => $this->module_name,
			'module_version' => $this->version,
			'has_cp_backend' => 'n'
		));

		return TRUE;
	}

	function update($current = '') {
		return TRUE;
	}

	function uninstall() {
		ee()->load->dbforge();

		ee()->dbforge->drop_table('social_youtube');
		ee()->dbforge->drop_table('social_twitter');
		ee()->dbforge->drop_table('social_instagram');

		ee()->db->where('module_name', $this->module_name);
		ee()->db->delete('modules');

		return TRUE;
	}
}