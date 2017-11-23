<?php

use EllisLab\ExpressionEngine\Library\CP;
use EllisLab\ExpressionEngine\Library\CP\Table;
use Solspace\Addons\User\Library\AddonBuilder;
use Solspace\Addons\User\Library\Authors;
use Solspace\Addons\User\Library\ChannelSync;
use Solspace\Addons\User\Library\Utils;

class User_mcp extends AddonBuilder
{
	/**
	 * Row Limit for pagination
	 * @var integer
	 */
	protected $row_limit = 50;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	null
	 */

	public function __construct( $switch = TRUE )
	{
		parent::__construct('module');

		// --------------------------------------------
		//  Module Menu Items
		// --------------------------------------------

		$this->set_nav(array(
			'preferences'		=> array(
				'link'  => $this->mcp_link(array(
					'method' => 'preferences'
				)),
				'title' => lang('preferences'),
			),
			/*
			'channel_sync'	=> array(
				'link'  => $this->mcp_link(array(
					'method' => 'channel_sync'
				)),
				'title' => lang('channel_sync'),
			),
			*/
			'roles'	=> array(
				'link'  => $this->mcp_link(array(
					'method' => 'roles'
				)),
				'title' => lang('roles'),
				'nav_button'	=> array(
					'title'	=> lang('new'),
					'link'  => $this->mcp_link(array(
						'method' => 'role_form'
					)),
				),
				'sub_list'	=> array(
					'roles_assigned'	=> array(
						'link'  => $this->mcp_link(array(
							'method' => 'roles_assigned'
						)),
						'title' => lang('roles_assigned'),
						'nav_button'	=> array(
							'title'	=> lang('new'),
							'link'  => $this->mcp_link(array(
								'method' => 'role_assigned_form'
							)),
						),
					)
				),
			),
			'demo_templates'		=> array(
				'link'  => $this->mcp_link(array(
					'method' => 'code_pack'
				)),
				'title' => lang('demo_templates'),
			),
            'resources'      => array(
                'title'    => lang('user_resources'),
                'sub_list' => array(
                    'product_info'  => array(
                        'link'     => 'https://solspace.com/expressionengine/user',
                        'title'    => lang('user_product_info'),
                        'external' => true,
                    ),
                    'documentation' => array(
                        'link'     => $this->docs_url,
                        'title'    => lang('user_documentation'),
                        'external' => true,
                    ),
                    'support'       => array(
                        'link'     => 'https://solspace.com/expressionengine/support',
                        'title'    => lang('user_official_support'),
                        'external' => true,
                    ),
                ),
			),
		));

		$this->cached_vars['lang_module_version'] 	= lang('user_module_version');
		$this->cached_vars['module_version'] 		= ee('App')->get('user')->getVersion();
		$this->cached_vars['module_menu_highlight']	= 'preferences';

		$this->ucs	= new ChannelSync();
		$this->u	= new Utils();

		ee()->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' . URL_THIRD_THEMES . 'user/css/solspace-fa.css">');
	}

	// END __construct()

	// --------------------------------------------------------------------

	/**
	 * Homepage
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */

	public function index($message = '')
	{
		$this->lib('ChannelSync')->get_data_fields(1, true, 'id');
		return $this->preferences($message);
	}
	// End home

	// --------------------------------------------------------------------

	/**
	 * Get Channel Fields for Channel
	 *
	 * @access	public
	 * @param	boolean		$fetch	return array data (for local use to override ajax detect)
	 * @return	mixed				if $fetch == true, id->label array of fields
	 */

	public function get_channel_fields($channel = '', $fetch = false)
	{
		$result = array(
			'success' => false
		);

		$channel = ( ! empty($channel)) ?
						$channel :
						ee()->input->get_post('channel', true);

		$fields = array();

		if ( ! empty($channel))
		{
			$fields = $this->ucs->get_data_fields(
				$channel,
				true,
				'id'
			);
		}

		if ( ! empty($fields))
		{
			$result['success'] = true;
			$result['fields'] = $fields;
		}

		if ( ! $fetch && $this->is_ajax_request())
		{
			$this->send_ajax_response($result);
			exit();
		}
		else
		{
			return $result;
		}
	}
	//END public get_channel_fields


	// -----------------------------------------------------------------

	/**
	 * Code pack page
	 *
	 * @access public
	 * @param	string	$message	lang line for update message
	 * @return	string				html output
	 */

	public function code_pack($message = '')
	{
		$this->prep_message($message, TRUE, TRUE);

		// --------------------------------------------
		//	Load vars from code pack lib
		// --------------------------------------------

		$codePack = $this->lib('CodePack');
		$cpl      =& $codePack;

		$cpl->autoSetLang = true;

		$cpt = $cpl->getTemplateDirectoryArray(
			$this->addon_path . 'code_pack/'
		);

		// --------------------------------------------
		//  Start sections
		// --------------------------------------------

		$sections = array();

		$main_section = array();

		// --------------------------------------------
		//  Prefix
		// --------------------------------------------

		$main_section['template_group_prefix'] = array(
			'title'		=> lang('template_group_prefix'),
			'desc'		=> lang('template_group_prefix_desc'),
			'fields'	=> array(
				'prefix' => array(
					'type'		=> 'text',
					'value'		=> $this->lower_name . '_',
				)
			)
		);

		// --------------------------------------------
		//  Templates
		// --------------------------------------------

		$main_section['templates'] = array(
			'title'		=> lang('groups_and_templates'),
			'desc'		=> lang('groups_and_templates_desc'),
			'fields'	=> array(
				'templates' => array(
					'type'		=> 'html',
					'content'	=> $this->view('code_pack_list', compact('cpt')),
				)
			)
		);

		// --------------------------------------------
		//  Compile
		// --------------------------------------------

		$this->cached_vars['sections'][] = $main_section;

		$this->cached_vars['form_url'] = $this->mcp_link(array(
			'method' => 'code_pack_install'
		));

		$this->cached_vars['box_class'] = 'code_pack_box';

		//---------------------------------------------
		//  Load Page and set view vars
		//---------------------------------------------

		// Final view variables we need to render the form
		$this->cached_vars += array(
			'base_url'				=> $this->mcp_link(array(
				'method' => 'code_pack_install'
			)),
			'cp_page_title'			=> lang('demo_templates') .
										'<br /><i>' . lang('demo_description') . '</i>' ,
			'save_btn_text'			=> 'install_demo_templates',
			'save_btn_text_working'	=> 'btn_saving'
		);

		ee('CP/Alert')->makeInline('shared-form')
		->asIssue()
		->addToBody(lang('prefix_error'))
		->cannotClose()
		->now();

		return $this->mcp_view(array(
			'file'		=> 'code_pack_form',
			'highlight'	=> 'demo_templates',
			'pkg_css'	=> array('mcp_defaults'),
			'pkg_js'	=> array('code_pack'),
			'crumbs'	=> array(
				array(lang('demo_templates'))
			)
		));
	}
	//END code_pack


	// --------------------------------------------------------------------

	/**
	 * Code Pack Install
	 *
	 * @access public
	 * @param	string	$message	lang line for update message
	 * @return	string				html output
	 */

	public function code_pack_install()
	{
		$prefix = trim((string) ee()->input->get_post('prefix'));

		if ($prefix === '')
		{
			return ee()->functions->redirect($this->mcp_link(array(
				'method' => 'code_pack'
			)));
		}

		// -------------------------------------
		//	load lib
		// -------------------------------------

		$codePack = $this->lib('CodePack');
		$cpl      =& $codePack;

		$cpl->autoSetLang = true;

		// -------------------------------------
		//	¡Las Variables en vivo! ¡Que divertido!
		// -------------------------------------

		$variables = array();

		$variables['code_pack_name']	= $this->lower_name . '_code_pack';
		$variables['code_pack_path']	= $this->addon_path . 'code_pack/';
		$variables['prefix']			= $prefix;

		// -------------------------------------
		//	install
		// -------------------------------------

		$return = $cpl->installCodePack($variables);

		//--------------------------------------------
		//	Table
		//--------------------------------------------

		$table = ee('CP/Table', array(
			'sortable'	=> false,
			'search'	=> false
		));

		$tableData = array();

		//--------------------------------------------
		//	Errors or regular
		//--------------------------------------------

		if (! empty($return['errors']))
		{
			foreach ($return['errors'] as $error)
			{
				$item = array();

				//	Error
				$item[]	= lang('error');

				//	Label
				$item[]	= $error['label'];

				//	Field type
				$item[]	= str_replace(
					array(
						'%conflicting_groups%',
						'%conflicting_data%',
						'%conflicting_global_vars%'
					),
					array(
						implode(", ", $return['conflicting_groups']),
						implode("<br />", $return['conflicting_global_vars'])
					),
					$error['description']
				);

				$tableData[] = $item;
			}
		}
		else
		{
			foreach ($return['success'] as $success)
			{
				$item = array();

				//	Error
				$item[]	= lang('success');

				//	Label
				$item[]	= $success['label'];

				//	Field type
				if (isset($success['link']))
				{
					$item[]	= array(
						'content'	=> $success['description'],
						'href'		=>$success['link']
					);
				}
				else
				{
					$item[]	= str_replace(
						array(
							'%template_count%',
							'%global_vars%',
							'%success_link%'
						),
						array(
							$return['template_count'],
							implode("<br />", $return['global_vars']),
							''
						),
						$success['description']
					);
				}

				$tableData[] = $item;
			}
		}

		$table->setColumns(array(
			'status',
			'description',
			'details',
		));

		$table->setData($tableData);

		$table->setNoResultsText('no_results');

		$this->cached_vars['table'] 	= $table->viewData();

		$this->cached_vars['form_url']	= '';

		//---------------------------------------------
		//  Load Page and set view vars
		//---------------------------------------------

		return $this->mcp_view(array(
			'file'		=> 'code_pack_install',
			'highlight'	=> 'demo_templates',
			'pkg_css'	=> array('mcp_defaults'),
			'crumbs'	=> array(
				array(lang('demo_templates'))
			)
		));
	}
	//END code_pack_install

	// --------------------------------------------------------------------

	/**
	 * Roles
	 *
	 * @access	public
	 * @param	string	$message	incoming message or langline to show user.
	 * @return	string				completed output template
	 */

	public function roles($message = '')
	{
		$this->prep_message($message, TRUE, TRUE);

		$this->cached_vars['form_url'] = $this->mcp_link(array(
			'method' => 'role_update'
		));

		$this->cached_vars['form_right_links']	= array(
			array(
				'link' => $this->mcp_link(array('method' => 'role_form')),
				'title' => lang('create_new_role'),
			)
		);

		//	----------------------------------------
		//	Query
		//	----------------------------------------

		$roles = $this->fetch('Role');

		//	----------------------------------------
		//	Start table
		//	----------------------------------------

		$tableData = array();

		//	----------------------------------------
		//	Anything?
		//	----------------------------------------

		if ($roles->count() > 0)
		{
			//	----------------------------------------
			//	Pagination
			//	----------------------------------------

			$page	= 0;

			if ($roles->count() > $this->row_limit)
			{
				$page	= $this->get_post_or_zero('page') ?: 1;

				$mcp_link_array = array(
					'method' => __FUNCTION__
				);

				$this->cached_vars['pagination'] = ee('CP/Pagination', $roles->count())
									->perPage($this->row_limit)
									->currentPage($page)
									->render($this->mcp_link($mcp_link_array, false));

				$roles->limit($this->row_limit)->offset(($page - 1) * $this->row_limit);
			}

			foreach ($roles->all() as $role)
			{
				$tableData[] = array(
					$role->role_id,
					array(
						'content'	=> $role->role_label,
						'href'		=> $this->mcp_link(array(
							'method'	=> 'role_form',
							'role_id'	=> $role->role_id
						))
					),
					$role->role_name,
					$role->role_description,
					array(
						'name'	=> 'selections[]',
						'value' => $role->role_id,
						'data'	=> array(
							'confirm' => lang('role') . ': <b>' . htmlentities($role->role_label, ENT_QUOTES) . '</b>'
						)
					)
				);
			}
		}

		unset($role);

		// -------------------------------------
		//	build table
		// -------------------------------------

		$table = ee('CP/Table', array(
			'sortable'	=> false,
			'search'	=> false,
		));

		$table->setColumns(
			array(
				'role_id' => array(
					'type'			=> Table::COL_ID
				),
				'role_label'		=> array(),
				'role_name'			=> array(),
				'role_description'	=> array(),
				array(
					'type'			=> Table::COL_CHECKBOX,
					'name'			=> 'selection'
				)
			)
		);

		$table->setData($tableData);

		$table->setNoResultsText('no_roles');

		$this->cached_vars['table'] = $table->viewData(
			$this->mcp_link(array('method' => __FUNCTION__), false)
		);

		$modal_vars = array(
			'name'		=> 'modal-confirm-remove',
			'form_url'	=> $this->mcp_link(array('method' => 'roles_delete')),
			'checklist' => array(
				array(
					'kind' => lang('roles'),
				)
			)
		);

		ee('CP/Modal')->addModal('role', ee('View')->make('_shared/modal_confirm_remove')->render($modal_vars));
		ee()->javascript->set_global('lang.remove_confirm', lang('role') . ': <b>### ' . lang('roles') . '</b>');

		ee()->cp->add_js_script(array(
			'file' => array('cp/confirm_remove'),
		));

		//---------------------------------------------
		//  Load Page and set view vars
		//---------------------------------------------

		// Final view variables we need to render the form
		$this->cached_vars += array(
			'base_url'				=> $this->mcp_link(array(
				'method' => 'role_update'
			)),
			'cp_page_title'			=> lang('roles'),
			'table_head'			=> lang('current_roles'),
			'save_btn_text'			=> 'btn_save_role',
			'save_btn_text_working'	=> 'btn_saving_role'
		);

		return $this->mcp_view(array(
			'file'		=> 'list',
			'highlight'	=> 'roles',
			'pkg_js'	=> array('chosen.jquery', 'user', 'roles'),
			'pkg_css'	=> array('mcp_defaults'),
			'crumbs'	=> array(
				array(lang('roles'))
			)
		));
	}

	//	End roles()

	// --------------------------------------------------------------------

	/**
	 * role_form()
	 *
	 * @access	public
	 * @param	string	$message	incoming message or langline to show user.
	 * @return	string				completed output template
	 */

	public function role_form()
	{
		$this->prep_message();

		// --------------------------------------------
		//  Current Values
		// --------------------------------------------

		$roleModel		= $this->make('Role');
		$defaultPrefs	= $roleModel->default_prefs;

		if (ee()->input->get_post('role_id'))
		{
			$role = $this->fetch('Role')
				->filter('role_id', ee()->input->get_post('role_id'))
				->first()
				->toArray();
		}

		// --------------------------------------------
		//  Start sections
		// --------------------------------------------

		$sections		= array();
		$main_section	= array();
		$hidden_fields	= array();

		foreach ($defaultPrefs as $short_name => $data)
		{
			$desc_name	= $short_name . '_subtext';
			$desc		= lang($desc_name);

			//if we don't have a description don't set it
			$desc		= ($desc !== $desc_name) ? $desc : '';

			$required	= FALSE;

			$fields		= array(
				$short_name => array_merge($data, array(
					'value'		=> isset($role[$short_name]) ?
									$role[$short_name] :
									$data['default'],
					//we just require everything
					//its a settings form
					'required'	=> $required
				))
			);

			// --------------------------------------------
			//  Special handling for table
			// --------------------------------------------

			if ($data['type'] == 'table')
			{
				$wide	= TRUE;
				$d	= array(
					'table_id'	=> 'mapping-table'
				);

				$fields		= array(
					$short_name => array(
						'type'		=> 'html',
						'content'	=> $this->view('channel_sync/table', $d)
					)
				);
			}

			// --------------------------------------------
			//  Hidden fields
			// --------------------------------------------

			if ($data['type'] == 'hidden')
			{
				$hidden_fields	= array_merge($hidden_fields, $fields);
				continue;
			}

			if ($data == end($defaultPrefs))
			{
				$fields	= array_merge($fields, $hidden_fields);
			}

			// --------------------------------------------
			//  Set the row now
			// --------------------------------------------

			$main_section[$short_name] = array(
				'wide'		=> isset($wide),
				'title'		=> lang($short_name),
				'desc'		=> $desc,
				'group'		=> (empty($data['group'])) ? 'default': $data['group'],
				'fields'	=> $fields
			);
		}

		$sections[]	= $main_section;

		//---------------------------------------------
		//  Load Page and set view vars
		//---------------------------------------------

		$this->cached_vars['sections'] = $sections;

		$this->cached_vars['form_url'] = $this->mcp_link(array(
			'method' => 'role_update'
		));

		$this->cached_vars['form_right_links']	= array(
			array(
				'link' => $this->mcp_link(array('method' => 'roles')),
				'title' => lang('create_new_role'),
			)
		);

		//---------------------------------------------
		//  Load Page and set view vars
		//---------------------------------------------

		// Final view variables we need to render the form
		$this->cached_vars += array(
			'base_url'				=> $this->mcp_link(array(
				'method' => 'role_update'
			)),
			'cp_page_title'			=> lang('create_new_role'),
			'save_btn_text'			=> 'btn_save_role',
			'save_btn_text_working'	=> 'btn_saving_role'
		);

		return $this->mcp_view(array(
			'file'		=> 'form',
			'highlight'	=> 'roles',
			'pkg_js'	=> array('chosen.jquery', 'user', 'roles'),
			'pkg_css'	=> array('mcp_defaults'),
			'crumbs'	=> array(
				array(
					lang('roles'),
					$this->mcp_link(array('method' => 'roles'), false)
				),
				array(lang('create_new_role'))
			)
		));
	}

	//	End role_form()

	// --------------------------------------------------------------------

	/**
	 * roles_delete
	 *
	 * @access	public
	 * @param	array	$post
	 * @return	string	redirect message
	 */

	public function roles_delete()
	{
		//---------------------------------------------
		//  Do this with models
		//---------------------------------------------

		$roles	= $this->fetch('Role')
			->filter('role_id', 'IN', $_POST['selections'])
			->delete();

		//---------------------------------------------
		//  Redirect
		//---------------------------------------------

		return ee()->functions->redirect($this->mcp_link(array(
			'method'	=> 'roles',
			'msg'		=> 'roles_deleted'
		)));
	}

	//	End roles_delete()

	// --------------------------------------------------------------------

	/**
	 * roles_assigned_delete()
	 *
	 * @access	public
	 * @param	array	$post
	 * @return	string	redirect message
	 */

	public function roles_assigned_delete()
	{
		//---------------------------------------------
		//  Do this with models
		//---------------------------------------------

		$roles	= $this->fetch('RoleAssigned')
			->filter('assigned_id', 'IN', $_POST['selections'])
			->delete();

		//---------------------------------------------
		//  Redirect
		//---------------------------------------------

		return ee()->functions->redirect($this->mcp_link(array(
			'method'	=> 'roles_assigned',
			'msg'		=> 'roles_assigned_deleted'
		)));
	}

	//	End roles_assigned_delete()

	// --------------------------------------------------------------------

	/**
	 * role_update()
	 *
	 * @access	public
	 * @param	array	$post
	 * @return	string	redirect message
	 */

	public function role_update()
	{
		$role = $this->make('Role');
		$default_prefs = $role->default_prefs;

		$input_keys = $required = array_keys($default_prefs);

		$inputs = array();

		// --------------------------------------------
		//	fetch only default prefs
		// --------------------------------------------

		foreach ($input_keys as $input)
		{
			if (isset($_POST[$input]))
			{
				if (is_array($_POST[$input]))
				{
					$inputs[$input] = implode('|', $_POST[$input]);
				}
				else
				{
					$inputs[$input] = ee()->input->post($input);
				}
			}
		}

		// --------------------------------------------
		//	validate (custom method)
		// --------------------------------------------

		$result = $role->validateDefaultPrefs($inputs, $required);

		if ( ! $result->isValid())
		{
			$errors = array();

			foreach ($result->getAllErrors() as $name => $error_list)
			{
				foreach($error_list as $error_name => $error_msg)
				{
					$errors[] = lang($name) . ': ' . $error_msg;
				}
			}

			return $this->show_error($errors);
		}

		// --------------------------------------------
		//	Update or create
		// --------------------------------------------

		if (ee()->input->get_post('role_id'))
		{
			$role = $this->fetch('Role')
				->filter('role_id', ee()->input->get_post('role_id'))
				->first();

			unset($inputs['role_id']);
		}
		else
		{
			$role = $this->make('Role');
		}

		foreach ($inputs as $name => $value)
		{
			$role->$name = $value;
		}

		$role->save();

		// --------------------------------------------
		//	Return view
		// --------------------------------------------

		return ee()->functions->redirect($this->mcp_link(array(
			'method'	=> 'roles',
			'role_id'	=> $role->role_id,
			'msg'		=> 'role_updated'
		)));
	}

	//	End role_update()


	// --------------------------------------------------------------------

	/**
	 * Roles Permissions
	 *
	 * @access	public
	 * @param	string	$message	incoming message or langline to show user.
	 * @return	string				completed output template
	 */

	public function roles_permissions($message = '')
	{
		// --------------------------------------------
		//  crumbs, messages, and highlights
		// --------------------------------------------

		$this->prep_message($message);
		$this->cached_vars['module_menu_highlight'] = 'module_roles';
		$this->add_crumb(lang('roles_permissions'));

		$this->roles_nav('roles_permissions');

		$this->cached_vars['form_uri'] = $this->mcp_link(array(
			'method' => 'save_roles_permissions'
		));

		// -------------------------------------
		//	Pagination
		// -------------------------------------

		$pag_url			= $this->mcp_link(array('method' => __FUNCTION__));
		$row_limit			= $this->row_limit;
		$paginate			= '';
		$row_count			= 0;
		$total_entries		= $this->model('roles_permissions')->count_all();
		$current_page		= 0;

		// do we need pagination?
		if ($total_entries > $row_limit )
		{
			$row_count			= $this->get_post_or_zero('row');

			//get pagination info
			$pagination_data 	= $this->universal_pagination(array(
				'total_results'			=> $total_entries,
				'limit'					=> $row_limit,
				'current_page'			=> $row_count,
				'pagination_config'		=> array('base_url' => $pag_url),
				'query_string_segment'	=> 'row'
			));

			$paginate 			= $pagination_data['pagination_links'];
			$current_page 		= $pagination_data['pagination_page'];

			$this->model('roles_permissions')->limit($row_limit, $current_page);
		}

		$this->cached_vars['paginate'] = $paginate;

		// -------------------------------------
		//	output data
		// -------------------------------------

		$roles_permissions = $this->model('roles_permissions')->key('permission_id')->get();


		foreach ($roles_permissions as $permission_id => $permissions_data)
		{
			$roles_permissions[$permission_id]['permissions_assigned_link'] = $this->mcp_link(array(
				'method'	=> 'edit_permission',
				'permission_id'	=> $permission_id
			));

			$roles_permissions[$permission_id]['permission_edit_link'] = $this->mcp_link(array(
				'method'	=> 'edit_permission',
				'permission_id'	=> $permission_id
			));

			$roles_permissions[$permission_id]['permission_delete_link'] = $this->mcp_link(array(
				'method'	=> 'delete_permission_confirm',
				'permission_id'	=> $permission_id
			));
		}


		$this->cached_vars['roles_permissions'] = $roles_permissions;

		ee()->cp->load_package_js('roles');

		$this->cached_vars['current_page'] = $this->view(
			'roles_permissions.html',
			NULL,
			TRUE
		);

		return $this->ee_cp_view('index.html');
	}
	//ENd roles_permissions


	// --------------------------------------------------------------------

	/**
	 * Save Roles Permissions
	 *
	 * @access	public
	 * @return	void (halt on error and redirect on success)
	 */

	public function save_roles_permissions()
	{
		$errors			= array();
		$role_id		= 0;
		$update			= false;

		// -------------------------------------
		//	role name
		// -------------------------------------

		$permission_name		= ee()->input->get_post('permission_name', TRUE);

		//if the field label is blank, make one for them
		//we really dont want to do this, but here we are
		if ( ! $permission_name OR ! trim($permission_name))
		{
			$errors['permission_name'] = lang('permission_name_required');
		}
		else
		{
			$permission_name = strtolower(trim($permission_name));

			//if the form_name they submitted isn't like how a URL title may be
			//also, cannot be numeric
			if (preg_match('/[^a-z0-9\_\-]/i', $permission_name) OR
				is_numeric($permission_name))
			{
				$errors['permission_name'] = lang('role_name_can_only_contain');
			}

			//get dupe from field names
			$dupe_data = $this->model('roles_permissions')->get_row(array(
				'permission_name' => $permission_name
			));

			//if we are updating, we don't want to error on the same field id
			if ( ! empty($dupe_data) AND
				! ($update AND $dupe_data['permission_id'] == $permission_id))
			{
				$errors['permission_name'] = str_replace(
					'%name%',
					$role_name,
					lang('duplicate_role_name')
				);
			}
		}

		// -------------------------------------
		//	form label
		// -------------------------------------

		$permission_label = ee()->input->get_post('permission_label', TRUE);

		if ( ! $permission_label OR ! trim($permission_label) )
		{
			$errors['permission_label'] = lang('permission_label_required');
		}

		$permission_description = ee()->input->get_post('permission_description', TRUE);

		if ( ! empty($errors))
		{
			return $this->u->full_stop($errors);
		}

		$this->model('roles_permissions')->insert(array(
			'permission_label'			=> $permission_label,
			'permission_name'			=> $permission_name,
			'permission_description'	=> $permission_description
		));

		ee()->functions->redirect($this->mcp_link(array(
			'method'	=> 'roles_permissions',
			'msg'		=> 'permission_saved'
		)));
	}
	//END save_roles_permissions


	// --------------------------------------------------------------------

	/**
	 * Delete Role
	 *
	 * @access	public
	 * @return	voic	redirect
	 */
	public function delete_permission()
	{
		$permission_id = ee()->input->get_post('permission_id', TRUE);

		if ( ! $this->is_positive_intlike($permission_id) )
		{
			$this->u->full_stop(lang('no_permission_ids_submitted'));
		}

		$this->lib('Roles')->delete_permissions($permission_id);

		return ee()->functions->redirect($this->mcp_link(array(
			'method'	=> 'roles_permissions',
			'msg'		=> 'permissions_deleted'
		)));
	}

	//END Delete_role

	// --------------------------------------------------------------------

	/**
	 * Roles Assigned
	 *
	 * @access	public
	 * @param	string	$message	incoming message or langline to show user.
	 * @return	string				parsed html output for page
	 */

	public function roles_assigned($message = '')
	{
		$this->prep_message($message, TRUE, TRUE);

		$this->cached_vars['form_url'] = $this->mcp_link(array(
			'method' => 'roles_assigned_update'
		));

		if ($this->fetch('Role')->count() > 0)
		{
			$this->cached_vars['form_right_links']	= array(
				array(
					'link' => $this->mcp_link(array('method' => 'roles_assigned_form')),
					'title' => lang('assign_role'),
				)
			);
		}

		//	----------------------------------------
		//	Query
		//	----------------------------------------

		$roles_assigned = $this->fetch('RoleAssigned')
			->with('Role');

		//	----------------------------------------
		//	Start table
		//	----------------------------------------

		$tableData = array();

		//	----------------------------------------
		//	Anything?
		//	----------------------------------------

		if ($roles_assigned->count() > 0)
		{
			//	----------------------------------------
			//	Pagination
			//	----------------------------------------

			$page	= 0;

			if ($roles_assigned->count() > $this->row_limit)
			{
				$page	= $this->get_post_or_zero('page') ?: 1;

				$mcp_link_array = array(
					'method' => __FUNCTION__
				);

				$this->cached_vars['pagination'] = ee('CP/Pagination', $roles_assigned->count())
									->perPage($this->row_limit)
									->currentPage($page)
									->render($this->mcp_link($mcp_link_array, false));

				$roles_assigned->limit($this->row_limit)->offset(($page - 1) * $this->row_limit);
			}

			foreach ($roles_assigned->all() as $role)
			{
				$name	= '';

				if ($role->content_type == 'group')
				{
					$group	= ee('Model')
						->get('MemberGroup')
						->fields('group_title')
						->filter('group_id', $role->content_id)
						->first();

					if ($group)
					{
						$name	= $group->group_title;
					}
				}
				else
				{
					$user	= ee('Model')
						->get('Member')
						->fields('screen_name')
						->filter('member_id', $role->content_id)
						->first();

					if ($user)
			{
						$name	= $user->screen_name;
					}
				}

				$tableData[] = array(
					$role->assigned_id,
					ucfirst($role->content_type),
					$name,
					$role->Role->role_label,
					array(
						'name'	=> 'selections[]',
						'value' => $role->assigned_id,
						'data'	=> array(
							'confirm' => lang('role_assigned') . ': <b>' . htmlentities($role->Role->role_label, ENT_QUOTES) . '</b>'
						)
					)
				);
			}
		}

		unset($role);

		// -------------------------------------
		//	build table
		// -------------------------------------

		$table = ee('CP/Table', array(
			'sortable'	=> false,
			'search'	=> false,
		));

		$table->setColumns(
			array(
				'role_id' => array(
					'type'	=> Table::COL_ID
				),
				'role_type'			=> array(),
				'name'				=> array(),
				'role'		=> array(),
				array(
					'type'			=> Table::COL_CHECKBOX,
					'name'			=> 'selection'
				)
			)
		);

		$table->setData($tableData);

		$table->setNoResultsText('no_roles_assigned');

		$this->cached_vars['table'] = $table->viewData(
			$this->mcp_link(array('method' => __FUNCTION__), false)
		);

		$modal_vars = array(
			'name'		=> 'modal-confirm-remove',
			'form_url'	=> $this->mcp_link(array('method' => 'roles_assigned_delete')),
			'checklist' => array(
				array(
					'kind' => lang('roles_assigned'),
				)
			)
		);

		ee('CP/Modal')->addModal('role', ee('View')->make('_shared/modal_confirm_remove')->render($modal_vars));
		ee()->javascript->set_global('lang.remove_confirm', lang('role_assigned') . ': <b>### ' . lang('roles_assigned') . '</b>');

		ee()->cp->add_js_script(array(
			'file'	=> array('cp/confirm_remove'),
		));

		//---------------------------------------------
		//  Load Page and set view vars
		//---------------------------------------------

		// Final view variables we need to render the form
		$this->cached_vars += array(
			'base_url'				=> $this->mcp_link(array(
				'method' => 'roles_assigned_update'
			)),
			'cp_page_title'			=> lang('roles_assigned'),
			'table_head'			=> lang('current_roles_assigned'),
			'save_btn_text'			=> 'btn_assign_role',
			'save_btn_text_working'	=> 'btn_assigning_role'
		);

		return $this->mcp_view(array(
			'file'		=> 'list',
			'highlight'	=> 'roles/roles_assigned',
			'pkg_js'	=> array('chosen.jquery', 'user', 'roles'),
			'pkg_css'	=> array('mcp_defaults', 'user'),
			'crumbs'	=> array(
				array(
					lang('roles'),
					$this->mcp_link(array('method' => 'roles'), false)
				),
				array(lang('roles_assigned'))
			)
		));
	}

	//	END roles_assigned()

	// --------------------------------------------------------------------

	/**
	 * roles_assigned_form()
	 *
	 * @access	public
	 * @param	string	$message	incoming message or langline to show user.
	 * @return	string				parsed html output for page
	 */

	public function roles_assigned_form()
	{
		// --------------------------------------------
		//  Get roles for field
		// --------------------------------------------

		$this->cached_vars['roles'] = $this->fetch('Role')
			->all()
			->getDictionary(
				'role_id',
				'role_label'
			);

		$this->cached_vars['member_ajax_search_uri'] = $this->mcp_link(array(
			'method' => 'ajax_member_search'
		));

		$this->cached_vars['groups']	= ee('Model')->get('MemberGroup')
			->all()
			->getDictionary(
				'group_id',
				'group_title'
			);

		// --------------------------------------------
		//  Start sections
		// --------------------------------------------

		$sections		= array();
		$main_section	= array();

		$short_name	= 'assign_new_role';
		$desc		= lang($short_name . '_subtext');
		$required	= FALSE;

		$fields		= array(
			$short_name => array(
				'type'		=> 'html',
				'content'	=> $this->view('roles/assign_field', array())
			)
		);

		// --------------------------------------------
		//  Set the row now
		// --------------------------------------------

		$main_section[$short_name] = array(
			'wide'		=> TRUE,
			'title'		=> lang($short_name),
			'desc'		=> $desc,
			'group'		=> 'default',
			'fields'	=> $fields
		);

		$sections[]	= $main_section;

		//---------------------------------------------
		//  Load Page and set view vars
		//---------------------------------------------

		$this->cached_vars['sections'] = $sections;

		$this->cached_vars['form_url'] = $this->mcp_link(array(
			'method' => 'roles_assigned_update'
		));

		ee()->cp->add_js_script(array(
			'ui'	=> array('menu', 'autocomplete'),
			'file'	=> array('cp/confirm_remove'),
		));

		//---------------------------------------------
		//  Load Page and set view vars
		//---------------------------------------------

		// Final view variables we need to render the form
		$this->cached_vars += array(
			'base_url'				=> $this->mcp_link(array(
				'method' => 'roles_assigned_update'
			)),
			'cp_page_title'			=> lang('assign_role'),
			'save_btn_text'			=> 'btn_assign_role',
			'save_btn_text_working'	=> 'btn_assigning_role'
		);

		return $this->mcp_view(array(
			'file'		=> 'form',
			'highlight'	=> 'roles/roles_assigned',
			'pkg_js'	=> array('chosen.jquery', 'user', 'roles', 'roles_assigned'),
			'pkg_css'	=> array('mcp_defaults', 'user'),
			'crumbs'	=> array(
				array(
					lang('roles'),
					$this->mcp_link(array('method' => 'roles'), false)
				),
				array(
					lang('roles_assigned'),
					$this->mcp_link(array('method' => 'roles_assigned'), false)
				),
				array(lang('assign_role'))
			)
		));
	}

	//	END roles_assigned_form()

	// --------------------------------------------------------------------

	/**
	 * roles_assigned_update()
	 *
	 * @access	public
	 * @return	void (halt on error and redirect on success)
	 */

	public function roles_assigned_update()
	{
		$errors			= array();
		$role_id		= ee()->input->get_post('role', true);
		$update			= false;
		$is_group		= ee()->input->get_post('member_or_group') == 'group';
		$member_id		= 0;
		$screen_name	= '';

		// -------------------------------------
		// no role?
		// -------------------------------------

		if (empty($role_id))
		{
			$errors[] = lang('no_role_provided');
		}

		// -------------------------------------
		// already assigned?
		// -------------------------------------

		if ($is_group)
		{
			$group_id = ee()->input->get_post('to_group', TRUE);

			if (! $this->is_positive_intlike($group_id))
			{
				$errors[] = lang('invalid_group_id');
			}
			else
			{
				$result = $this->fetch('RoleAssigned')
					->filter('content_id', $group_id)
					->filter('role_id', $role_id)
					->filter('content_type', 'group')
					->first();

				if ($result)
				{
					$errors[] = lang('role_already_assigned_to_group');
				}
			}
		}
		//member
		else
		{
			$screen_name = ee()->input->get_post('member_search', true);

			$member_result	= ee('Model')
				->get('Member')
				->filter('screen_name', $screen_name)
				->first();

			if (! $member_result)
			{
				$errors[] = lang('member_not_found_for_screenname');
			}
			else
			{
				$member_id = $member_result->member_id;

				$result = $this->fetch('RoleAssigned')
								->filter('content_id', $member_id)
								->filter('role_id', $role_id)
								->filter('content_type', 'member')
								->first();

				if ($result)
				{
					$errors[] = lang('role_already_assigned_to_member');
				}
			}
		}

		if ( ! empty($errors))
		{
			return $this->u->full_stop($errors);
		}

		$result = $this->make('RoleAssigned');
		$result->content_id		= $is_group ? $group_id : $member_id;
		$result->role_id		= $role_id;
		$result->content_type	= $is_group ? 'group' : 'member';
		$result->save();

		return ee()->functions->redirect($this->mcp_link(array(
			'method'	=> 'roles_assigned',
			'msg'		=> 'role_assignment_saved'
		)));
	}

	//	END roles_assigned_update()

	// --------------------------------------------------------------------

	/**
	 * channel_sync()
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */

	public function channel_sync($message = '')
	{
		$this->prep_message($message, TRUE, TRUE);

		$channel_fields			= $this->ucs->get_data_fields('', true, 'id');
		$default_member_fields	= $this->model('Data')->get_default_fields();
		$mfields				= $this->model('Data')->mfields_by_id();
		$custom_member_fields	= array();

		foreach ($mfields as $id => $data)
		{
			$custom_member_fields['m_field_id_' . $id] = $data['label'];
		}

		// -------------------------------------
		//	Get prefs
		// -------------------------------------

		$prefs		= $this->ucs->get_sync_prefs();

		$channel_id = $this->ucs->get_channel_data_pref();

		//get map prefs just as IDs
		//(decodes stored json)
		$sync_prefs = $this->ucs->get_field_map_prefs(true);

		// -------------------------------------
		//	view vars
		// -------------------------------------

		$this->cached_vars['channel_ajax_url']	= $this->mcp_link(array(
			'method'	=> 'get_channel_fields',
			'channel'	=> ''
		));

		$this->cached_vars['channel_fields']		= $channel_fields;
		$this->cached_vars['default_member_fields']	= $default_member_fields;
		$this->cached_vars['custom_member_fields']	= $custom_member_fields;
		$this->cached_vars['member_fields_json']	= json_encode(
			array(
				'customMemberFields' => $custom_member_fields,
				'defaultMemberFields' => $default_member_fields
			),
			true
		);
		$this->cached_vars['sync_prefs']	= json_encode($sync_prefs, true);

		// --------------------------------------------
		//  Current Values
		// --------------------------------------------

		$prefModel		= $this->make('Preference');
		$defaultPrefs	= $prefModel->get_channel_sync_prefs();

		$prefs = $this->fetch('Preference')
			->filter('site_id', 0)
			->all()->getDictionary(
				'preference_name',
				'preference_value'
		);

		$this->cached_vars['current_channel']	= (empty($prefs['channel_sync_channel'])) ? 0: $prefs['channel_sync_channel'];

		// --------------------------------------------
		//  Start sections
		// --------------------------------------------

		$sections = array();
		$main_section = array();

		foreach ($defaultPrefs as $short_name => $data)
		{
			$desc_name	= $short_name . '_subtext';
			$desc		= lang($desc_name);

			//if we don't have a description don't set it
			$desc		= ($desc !== $desc_name) ? $desc : '';

			$required	= FALSE;

			$fields		= array(
				$short_name => array_merge($data, array(
					'value'		=> isset($prefs[$short_name]) ?
									$prefs[$short_name] :
									$data['default'],
					//we just require everything
					//its a settings form
					'required'	=> $required
				))
			);

			// --------------------------------------------
			//  Special handling for channel
			// --------------------------------------------

			if ($data['type'] == 'select|channel')
			{
				// --------------------------------------------
				//  Channels
				// --------------------------------------------

				$channels = ee('Model')
					->get('Channel')
					->filter('site_id', ee()->config->item('site_id'))
					->order('channel_title', 'asc')
					->all();

				$data['type']		= 'select';
				$data['choices']	= array(
					'0' => lang('disable_channel_sync')
				);
				$data['value']	= isset($prefs[$short_name]) ? $prefs[$short_name]: 0;

				foreach ($channels as $channel)
				{
					$data['choices'][$channel->channel_id]	= $channel->channel_title;
				}

				$fields		= array(
					$short_name => $data
				);
			}

			// --------------------------------------------
			//  Special handling for table
			// --------------------------------------------

			if ($data['type'] == 'table')
			{
				$wide	= TRUE;
				$d	= array();

				$fields		= array(
					$short_name => array(
						'type'		=> 'html',
						'content'	=> $this->view('channel_sync/table', $d)
					)
				);
			}

			// --------------------------------------------
			//  Set the row now
			// --------------------------------------------

			$main_section[$short_name] = array(
				'wide'		=> isset($wide),
				'title'		=> lang($short_name),
				'desc'		=> $desc,
				'group'		=> (empty($data['group'])) ? 'default': $data['group'],
				'fields'	=> $fields
			);
		}

		$sections[]	= $main_section;

		$this->cached_vars['sections'] = $sections;

		$this->cached_vars['form_url'] = $this->mcp_link(array(
			'method' => 'channel_sync_update'
		));

		//---------------------------------------------
		//  Load Page and set view vars
		//---------------------------------------------

		// Final view variables we need to render the form
		$this->cached_vars += array(
			'base_url'				=> $this->mcp_link(array(
				'method' => 'channel_sync_update'
			)),
			'cp_page_title'			=> lang('channel_sync_desc'),
			'save_btn_text'			=> 'btn_save_settings',
			'save_btn_text_working'	=> 'btn_saving'
		);

		return $this->mcp_view(array(
			'file'		=> 'channel_sync/index',
			'highlight'	=> 'channel_sync',
			'pkg_js'	=> array('channel_sync'),
			'pkg_css'	=> array('mcp_defaults'),
			'crumbs'	=> array(
				array(lang('channel_sync'))
			)
		));
	}

	// END channel_sync()

	// --------------------------------------------------------------------

	/**
	 * channel_sync_update()
	 *
	 * @access	public
	 * @return	void (redirect)
	 */

	public function channel_sync_update()
	{
		$prefModel		= $this->make('Preference');
		$defaultPrefs	= $prefModel->get_channel_sync_prefs();

		$prefs = $this->fetch('Preference')
			->filter('site_id', 0)
			->all()->getDictionary(
				'preference_name',
				'preference_value'
		);

		$input_keys = $required = array_keys($defaultPrefs);

		$inputs = array();

		// --------------------------------------------
		//	fetch only default prefs
		// --------------------------------------------

		foreach ($input_keys as $input)
		{
			if (isset($_POST[$input]))
			{
				$inputs[$input] = ee()->input->post($input);
			}
		}

		// --------------------------------------------
		//	Channel set to 0? Kill all.
		// --------------------------------------------

		if ($inputs['channel_sync_channel'] == 0)
		{
			$prefs = $this->fetch('Preference')
				->filter('site_id', 0)
				->filter('preference_name', 'IN', $input_keys)
				->all();

			foreach ($prefs as $pref)
			{
				$pref->delete();
			}

			// --------------------------------------------
			//	Return view
			// --------------------------------------------

			return ee()->functions->redirect($this->mcp_link(array(
				'method'	=> 'channel_sync',
				'msg'		=> 'preferences_updated'
			)));
		}

		// --------------------------------------------
		//	field mapping is not required
		// --------------------------------------------

		if (isset($_POST['member_field']) AND
			is_array($_POST['member_field']))
		{
			$field_mapping = array();

			foreach ($_POST['member_field'] as $key => $value)
			{
				if (isset($_POST['channel_field'][$key]))
				{
					$field_mapping[$value] = $_POST['channel_field'][$key];
				}
			}

			if (! empty($field_mapping))
			{
				$inputs['channel_sync_field_map'] = json_encode($field_mapping);
			}
		}

		// --------------------------------------------
		//	Update Preferences
		// --------------------------------------------

		$currentPrefs = $this->fetch('Preference')
			->filter('site_id', 0)
			->all()
			->indexBy('preference_name');

		foreach ($inputs as $name => $value)
		{
			//update
			if (isset($currentPrefs[$name]))
			{
				$currentPrefs[$name]->preference_value = $value;
				$currentPrefs[$name]->save();
			}
			//insert
			else
			{
				$new = $this->make('Preference');
				$new->preference_value = $value;
				$new->preference_name = $name;
				$new->site_id = 0;
				$new->save();
			}
		}

		// --------------------------------------------
		//	Return view
		// --------------------------------------------

		return ee()->functions->redirect($this->mcp_link(array(
			'method'	=> 'channel_sync',
			'msg'		=> 'preferences_updated'
		)));
	}

	//	END channel_sync_update()

	// --------------------------------------------------------------------

	/**
	 * preferences()
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */

	public function preferences($message = '')
	{
		$this->prep_message($message, TRUE, TRUE);

		// --------------------------------------------
		//  Current Values
		// --------------------------------------------

		$prefModel		= $this->make('Preference');
		$defaultPrefs	= $prefModel->default_prefs;

		$prefs = $this->fetch('Preference')
			->filter('site_id', ee()->config->item('site_id'))
			->all()->getDictionary(
				'preference_name',
				'preference_value'
		);

		// --------------------------------------------
		//  Start sections
		// --------------------------------------------

		$sections 	= array();
		$group_name	= '';

		foreach ($defaultPrefs as $short_name => $data)
		{
			//	While in our loop, once we reach the fuzzy searching section, break it out as a separate section.
			if ($short_name == 'welcome_email_subject')
			{
				$sections[]		= $main_section;
				$main_section	= array();
			}

			$desc_name	= $short_name . '_subtext';
			$desc		= lang($short_name . '_subtext');

			//if we don't have a description don't set it
			$desc		= ($desc !== $desc_name) ? $desc : '';

			$required	= FALSE;

			$fields		= array(
				$short_name => array_merge($data, array(
					'value'		=> isset($prefs[$short_name]) ?
									$prefs[$short_name] :
									$data['default'],
					//we just require everything
					//its a settings form
					'required'	=> $required
				))
			);

			// --------------------------------------------
			//  Special handling for category_group
			// --------------------------------------------

			if ($data['type'] == 'multiselect|category_group')
			{
				// --------------------------------------------
				//  Category groups
				// --------------------------------------------

				$categories = ee('Model')
					->get('CategoryGroup')
					->filter('site_id', ee()->config->item('site_id'))
					->order('group_name', 'asc')
					->all();

				$selected	= isset($prefs[$short_name]) ? explode('|', $prefs[$short_name]): $data['default'];

				$d	= array(
					'name'		=> $short_name,
					'options'	=> array(),
					'selected'	=> $selected
				);

				foreach ($categories as $category)
				{
					$d['options'][$category->group_id]	= $category->group_name;
				}

				$fields[$short_name]	= array(
					'type' 		=> 'html',
					'content'	=> $this->view('form/multiselect', $d)
				);
			}

			// --------------------------------------------
			//  Set the row now
			// --------------------------------------------

			$main_section[$short_name] = array(
				'title'		=> lang($short_name),
				'desc'		=> $desc,
				'fields'	=> $fields
			);
		}

		$sections['email_preferences']	= $main_section;

		$this->cached_vars['sections'] = $sections;

		$this->cached_vars['form_url'] = $this->mcp_link(array(
			'method' => 'preferences_update'
		));

		//---------------------------------------------
		//  Load Page and set view vars
		//---------------------------------------------

		// Final view variables we need to render the form
		$this->cached_vars += array(
			'base_url'				=> $this->mcp_link(array(
				'method' => 'preferences_update'
			)),
			'cp_page_title'			=> lang('Preferences'),
			'save_btn_text'			=> 'btn_save_settings',
			'save_btn_text_working'	=> 'btn_saving'
		);

		return $this->mcp_view(array(
			'file'		=> 'form',
			'highlight'	=> 'preferences',
			'pkg_css'	=> array('mcp_defaults'),
			'crumbs'	=> array(
				array(lang('preferences'))
			)
		));
	}

	// END preferences()

	// --------------------------------------------------------------------

	/**
	 * preferences_update()
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */

	public function preferences_update()
	{
		$prefs = $this->make('Preference');
		$default_prefs = $prefs->default_prefs;

		$input_keys = $required = array_keys($default_prefs);

		$inputs = array();

		// --------------------------------------------
		//	fetch only default prefs
		// --------------------------------------------

		foreach ($input_keys as $input)
		{
			if (isset($_POST[$input]))
			{
				if (is_array($_POST[$input]))
				{
					$inputs[$input] = implode('|', $_POST[$input]);
				}
				else
				{
					$inputs[$input] = ee()->input->post($input);
				}
			}
		}

		// --------------------------------------------
		//	Correct for line breaks in email field
		// --------------------------------------------

		if (! empty($inputs['member_update_admin_notification_emails']))
		{
			$inputs['member_update_admin_notification_emails']	= preg_replace('#\s+#', ', ', trim($inputs['member_update_admin_notification_emails']));
		}

		// --------------------------------------------
		//	validate (custom method)
		// --------------------------------------------

		$result = $prefs->validateDefaultPrefs($inputs, $required);

		if ( ! $result->isValid())
		{
			$errors = array();

			foreach ($result->getAllErrors() as $name => $error_list)
			{
				foreach($error_list as $error_name => $error_msg)
				{
					$errors[] = lang($name) . ': ' . $error_msg;
				}
			}

			return $this->show_error($errors);
		}

		// --------------------------------------------
		//	Update Preferences
		// --------------------------------------------

		$currentPrefs = $this->fetch('Preference')
			->filter('site_id', ee()->config->item('site_id'))
			->all()
			->indexBy('preference_name');

		foreach ($inputs as $name => $value)
		{
			//update
			if (isset($currentPrefs[$name]))
			{
				$currentPrefs[$name]->preference_value = $value;
				$currentPrefs[$name]->save();
			}
			//insert
			else
			{
				$new = $this->make('Preference');
				$new->preference_value = $value;
				$new->preference_name = $name;
				$new->site_id = ee()->config->item('site_id');
				$new->save();
			}
		}

		// --------------------------------------------
		//	Return view
		// --------------------------------------------

		return ee()->functions->redirect($this->mcp_link(array(
			'method'	=> 'preferences',
			'msg'		=> 'preferences_updated'
		)));
	}

	// END preferences_update()

	// --------------------------------------------------------------------

	/**
	 * AJAX Author Search
	 *
	 * @access	public
	 * @return	string
	 */

	public function ajax_member_search()
	{
		$str	= ee()->input->get_post('member_keywords');
		$json	= ee()->input->get_post('output') === 'json';
		$extra	= '';

		if (trim($str) == '')
		{
			$this->cached_vars['members'] = array();

			if ($json)
			{
				return $this->send_ajax_response(array('error' => 'no_members_found'));
			}
		}

		$result_array = $this->search_members($str);

		$this->cached_vars['members'] = array();

		foreach($result_array as $row)
		{
			$this->cached_vars['members'][$row['member_id']] = $row['screen_name'];
		}

		if ($json)
		{
			return $this->send_ajax_response(array(
				'error' => false,
				'members' => $this->cached_vars['members']
			));
		}

		//if not json then
		//$this->cached_vars['members'] is used elsewhere
	}

	// END ajax_member_search()


	// --------------------------------------------------------------------

	/**
	 * Search Members on keywork
	 *
	 * @access	protected
	 * @param	string		$str	string to search on
	 * @return	array				result array of rows or empty array
	 */

	protected function search_members($str = '*')
	{
		$str = $this->_clean_str($str);

		if ($str != '*')
		{
			$extra = "	AND LOWER( exp_members.username ) LIKE '%" .
							ee()->db->escape_str(strtolower($str)) ."%'
						OR LOWER( exp_members.screen_name ) LIKE '%" .
							ee()->db->escape_str(strtolower($str)) . "%'
						OR LOWER( exp_members.email ) LIKE '%" .
							ee()->db->escape_str(strtolower($str))."%' ";
		}

		$sql = "SELECT		exp_members.member_id, exp_members.screen_name
				FROM		exp_members
				LEFT JOIN	exp_member_groups
				ON			exp_member_groups.group_id = exp_members.group_id
				WHERE		exp_member_groups.site_id = '" .
								ee()->db->escape_str(ee()->config->item('site_id'))."'
				AND (
					 exp_members.group_id = 1 OR
					 exp_members.in_authorlist = 'y' OR
					 exp_member_groups.include_in_authorlist = 'y'
					 )
				{$extra}
				ORDER BY screen_name ASC, username ASC";

		$query	= ee()->db->query($sql);

		return ($query->num_rows() > 0) ? $query->result_array() : array();
	}

	//END search_members


	// --------------------------------------------------------------------

	/**
	 * Clean Tag String
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */

	private function _clean_str( $str = '' )
	{
		ee()->load->helper('text');

		if (ee()->config->item('auto_convert_high_ascii') == 'y')
		{
			$str = ascii_to_entities( $str );
		}

		return ee('Security/XSS')->clean($str);
	}
	// END _clean_str()

	// -------------------------------------
	//	Moved these to an external lib to
	//	clean this file up and make it more useful
	// -------------------------------------

	public function user_authors_search()
	{
		return $this->lib('Authors')->user_authors_search();
	}

	public function user_authors_search_json()
	{
		return $this->lib('Authors')->user_authors_search_json();
	}

	public function user_authors_add()
	{
		return $this->lib('Authors')->user_authors_add();
	}

	public function user_authors_delete()
	{
		return $this->lib('Authors')->user_authors_delete();
	}

	public function publish_tab_javascript()
	{
		return $this->lib('Authors')->publish_tab_javascript();
	}

	public function browse_authors_autocomplete()
	{
		return $this->lib('Authors')->browse_authors_autocomplete();
	}

	public function user_authors_template()
	{
		return $this->lib('Authors')->user_authors_template();
	}
}
// END User_mcp
