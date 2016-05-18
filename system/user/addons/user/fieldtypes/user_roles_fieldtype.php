<?php

use Solspace\Addons\User\Library\Fieldtype;

class User_roles_fieldtype extends EE_Fieldtype
{
	public	$info	= array(
		'name'		=> 'User',
		'version'	=> ''
	);

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access	public
	 */

	public function __construct()
	{
		parent::__construct();

		$this->info			= require PATH_THIRD . 'user/addon.setup.php';

		$this->field_id 	= isset($this->settings['field_id']) ?
								$this->settings['field_id'] :
								$this->field_id;
		$this->field_name 	= isset($this->settings['field_name']) ?
								$this->settings['field_name'] :
								$this->field_name;

		$this->uob	= new Fieldtype();
	}
	//END __construct

	// --------------------------------------------------------------------

	/**
	 * displays field for publish/saef
	 *
	 * @access	public
	 * @param	string	$data	any incoming data from the channel entry
	 * @return	string	html output view
	 */

	public function display_field($data)
	{
		if (isset($this->uob->cache['roles_fieldtype_shown']) &&
			$this->uob->cache['roles_fieldtype_shown'])
		{
			return '<strong>' . lang('fieldtype_limited_to_one') . '</strong>';
		}
		//checkbox for inclusive

		$output = '';

		$vars = array();

		$field_permissions = array();

		$entry_id = ee()->input->get_post('entry_id');

		if ($entry_id === false)
		{
			$entry_id = ! empty($this->content_id) ? $this->content_id : 0;
		}

		//edit?4
		if ($entry_id !== false &&
			$this->uob->is_positive_intlike($entry_id))
		{
			$field_perms	= $this->uob->fetch('RoleEntryPermission')
				->filter('entry_id', $entry_id)
				->filter('field_id', $this->field_id)
				->all();

			if ($field_perms)
			{
				foreach ($field_perms as $perm)
				{
					$field_permissions[] = $perm->asArray();
				}
			}
		}

		$roles	= $this->uob
			->fetch('Role')
			->order('role_label')
			->all();

		foreach ($roles as $role)
		{
			$vars['roles'][$role->role_id]	= $role->asArray();
		}

		if (empty($vars['roles']))
		{
			return '<p><strong>' . lang('no_roles') . '</strong></p>';
		}

		$vars['current_author_id'] = ee()->session->userdata('member_id');

		$vars['field_permissions'] = $field_permissions;

		$vars['field_id'] 	= $this->field_id;
		$vars['field_name']	= $this->field_name;

		$output .= $this->uob->view('roles_field_view', $vars, true);

		if (REQ == 'CP')
		{
			ee()->cp->add_to_head(
				'<link href="' . $this->uob->theme_url . 'css/chosen.min.css"' .
					' media="all" rel="stylesheet" type="text/css" />'
			);
			ee()->cp->add_to_foot(
				'<script src="' . $this->uob->theme_url . 'js/chosen.jquery.min.js"></script>'
			);
		}
		else
		{
			$output .= '<link href="' . $this->uob->theme_url . 'css/chosen.min.css"' .
					' media="all" rel="stylesheet" type="text/css" />';
			$output .= '<script src="' . $this->uob->theme_url . 'js/chosen.jquery.min.js"></script>';
		}

		$this->uob->cache['roles_fieldtype_shown'] = true;

		return $output;
	}
	//END display_field

	// --------------------------------------------------------------------

	/**
	 * save
	 *
	 * @access	public
	 * @param	string	$data	any incoming data from the channel entry
	 * @return	null	html output view
	 */

	public function save($data)
	{
		$cache_name = $this->field_name;

		ee()->session->set_cache(__CLASS__, $cache_name, array(
			'data' => $data,
		));

		return '';
	}

	//	End save()

	// --------------------------------------------------------------------

	/**
	 * post_save. we arent using the intial save() because it doesn't
	 * have the entry id available yet, so it's somewhat useless to us here
	 *
	 * @access	public
	 * @param	string	$data	any incoming data from the channel entry
	 * @return	null	html output view
	 */

	public function post_save($data)
	{
		//	'roles_40_1_1' => string '3' (length=1)
		//	'roles_40_1_2' => string '4' (length=1)
		//	'roles_40_2_1' => string '5' (length=1)
		//	'roles_40_2_2' => string '4' (length=1)

		$sets = array();
		$authors = array();
		$cache_name = $this->field_name;
		$entry_id = $this->content_id();

		$post = ee()->session->cache(__CLASS__, $cache_name);

		foreach ($_POST as $key => $value)
		{
			if (preg_match('/^roles_' . $this->field_id . '/', $key) &&
				$this->uob->is_positive_intlike($value))
			{
				//builds info from key:
				//	'roles_40_1_1'
				//into:
				//	['roles', '40', '1', '2']
				$info = explode('_', $key);

				//set is the rule group
				$set = $info[2];

				// -------------------------------------
				//	build an author string for storing
				//	author id
				// -------------------------------------

				$auth_str = 'roles_author_' . $this->field_id . '_' . $set;

				//validate incoming author string (on edit)
				if (isset($_POST[$auth_str]) &&
					$this->uob->is_positive_intlike($_POST[$auth_str]))
				{
					$authors[$set] = $_POST[$auth_str];
				}

				if ( ! isset($sets[$set]))
				{
					$sets[$set] = array();
				}

				//we dont care about the 4th
				//number in the info array
				//from 'roles_40_1_1'
				//as it is just there to make
				//sure that it got into post
				//the set id and field id are all
				//thats important
				$sets[$set][] = $value;
			}
		}

		//	Delete to rebuild
		if (isset($entry_id))
		{
			$this->uob->fetch('RoleEntryPermission')
				->filter('entry_id', $entry_id)
				->filter('field_id', $this->field_id)
				->delete();
		}

		//can't do a thing without the entry id
		if (! empty($sets) AND isset($entry_id))
		{
			//rebuild set counter because we can remove items
			//and throw the count off (which itself is meaningless really)
			$set_counter = 0;

			foreach($sets as $set_id => $set_array)
			{
				$set_counter++;

				foreach ($set_array as $role_choice)
				{
					$new			= $this->uob->make('RoleEntryPermission');
					$new->entry_id	= $entry_id;
					$new->field_id	= $this->field_id;
					$new->set_id	= $set_counter;
					$new->role_id	= $role_choice;
					$new->author_id	= (isset($authors[$set_id])) ? $authors[$set_id]: ee()->session->userdata('member_id');
					$new->save();
				}
			}
		}
	}

	//END post_save


	//dummy function but since they use abstract now it errors to hell if
	//you don't have the required params and access keyword
	public function replace_tag($data, $params = array(), $tagdata = FALSE){return '';}

	// --------------------------------------------------------------------

	/**
	 * delete. gets called when entries are deleted
	 *
	 * @access	public
	 * @param	array	$ids ids of the entries being deleted
	 * @return	null
	 */

	public function delete($ids)
	{
		$this->uob->fetch('RoleEntryPermission')
			->filter('entry_id', 'IN', $ids)
			->delete();
	}
	//END delete


	// --------------------------------------------------------------------

	/**
	 * User Object
	 *
	 * @access	protected
	 * @return	object		instance of mod.user.php
	 */

	protected function uob()
	{
		if ( ! isset($this->obj))
		{
			$this->obj = new Addon_builder_user();
		}

		return $this->obj;
	}
	//END uob
}
//END User_ft
