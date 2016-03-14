<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use EllisLab\ExpressionEngine\Library\CP\Table;

// include base class
if ( ! class_exists('Low_reorder_base'))
{
	require_once(PATH_THIRD.'low_reorder/base.low_reorder.php');
}

/**
 * Low Reorder Module Control Panel class
 *
 * @package        low_reorder
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-reorder
 * @copyright      Copyright (c) 2016, Low
 */
class Low_reorder_mcp extends Low_reorder_base {

	// --------------------------------------------------------------------
	// CONSTANTS
	// --------------------------------------------------------------------

	const DEBUG = TRUE;

	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * Data array for views
	 *
	 * @var        array
	 * @access     private
	 */
	private $data = array();

	/**
	 * View heading
	 *
	 * @var        string
	 * @access     private
	 */
	private $heading;

	/**
	 * View breadcrumb
	 *
	 * @var        array
	 * @access     private
	 */
	private $crumb = array();

	/**
	 * Shortcut to current member group
	 *
	 * @var        int
	 * @access     private
	 */
	private $member_group;

	/**
	 * Model shortcuts
	 *
	 * @var        object
	 * @access     private
	 */
	private $set;
	private $order;

	// --------------------------------------------------------------------
	// PUBLIC METHODS
	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access      public
	 * @return      void
	 */
	public function __construct()
	{
		// -------------------------------------
		//  Call parent constructor
		// -------------------------------------

		parent::__construct();

		// -------------------------------------
		//  Get member group shortcut
		// -------------------------------------

		$this->member_group = ee()->session->userdata('group_id');

		// -------------------------------------
		//  Model shortcuts
		// -------------------------------------

		$this->set   =& ee()->low_reorder_set_model;
		$this->order =& ee()->low_reorder_order_model;
	}

	// --------------------------------------------------------------------

	/**
	 * Home screen for module
	 *
	 * @access      public
	 * @return      string
	 */
	public function index()
	{
		// --------------------------------------
		// Init table
		// --------------------------------------

		$table = ee('CP/Table', array(
			'sortable' => FALSE,
		));

		// No results
		$table->setNoResultsText('no_reorder_sets');

		// Table columns
		$table->setColumns(array(
			'ID' => array('type' => Table::COL_ID),
			'set_label',
			'set_name',
			array('type' => Table::COL_TOOLBAR),
			array('type' => Table::COL_CHECKBOX)
		));

		// --------------------------------------
		// Initiate table data
		// --------------------------------------

		$rows = array();

		// --------------------------------------
		// Get all sets
		// --------------------------------------

		if ($sets = $this->set->get_by_site($this->site_id))
		{
			// Order naturally by set label
			usort($sets, function($a, $b){
				return strnatcasecmp($a['set_label'], $b['set_label']);
			});

			// Set table limit to total amount of sets
			$table->config['limit'] = count($sets);

			foreach ($sets as $set)
			{
				// Get this set's permissions
				$perms = $this->set->get_permissions(json_decode($set['permissions'], TRUE));

				// Start row with ID, label and name
				$row = array(
					$set['set_id'],
					$set['set_label'],
					$set['set_name']
				);

				// Toolbar
				$items = array();

				// Edit set?
				if ($perms['can_edit'])
				{
					$items['edit'] = array(
						'href'  => $this->mcp_url('edit/'.$set['set_id']),
						'title' => lang('edit_set')
					);
				}

				// Reorder set?
				if ($perms['can_reorder'])
				{
					$items['reorder'] = array(
						'href'  => $this->mcp_url('reorder/'.$set['set_id']),
						'title' => lang('reorder_entries')
					);
				}

				$row[] = array('toolbar_items' => $items);

				// CHECKBOX
				$row[] = array(
					'name'  => 'set_id[]',
					'value' => $set['set_id'],
					'data'  => array(
						'confirm' => htmlspecialchars(lang('set').' “'.$set['set_label'].'”', ENT_QUOTES)
					)
				);

				// Add to table rows
				$rows[] = $row;
			}
		}

		$table->setData($rows);

		// --------------------------------------
		// For batch deletion
		// --------------------------------------

		ee()->cp->add_js_script('file', 'cp/confirm_remove');
		ee()->javascript->set_global('lang.remove_confirm', '### '.lang('sets'));

		// --------------------------------------
		// Compose view data
		// --------------------------------------

		$this->data = array(
			'table'      => $table->viewData(),
			'remove_url' => $this->mcp_url('delete'),
			'pagination' => FALSE
		);

		// Add link to new set if user can create sets
		if ($this->can_create())
		{
			$this->data['create_new_url'] = $this->mcp_url('edit/new');
		}

		// --------------------------------------
		// Set page title and breadcrumb
		// --------------------------------------

		$this->set_cp_var('cp_page_title', lang('low_reorder_module_name'));

		// --------------------------------------
		// Load view and return it
		// --------------------------------------

		return $this->view('list');
	}

	// --------------------------------------------------------------------

	/**
	 * Edit Settings form
	 *
	 * @access      public
	 * @return      array
	 */
	public function settings()
	{
		// --------------------------------------
		// Initiate form sections
		// --------------------------------------

		$sections = array();

		// --------------------------------------
		// List of member groups
		// --------------------------------------

		$groups = ee('Model')
			->get('MemberGroup')
			->filter('can_access_cp', 'y')
			->order('group_title')
			->all();

		$active = $this->get_settings('can_create_sets');
		$active[] = 1;

		$sections[0][] = array(
			'title' => 'can_create_sets',
			'fields' => array(
				'can_create_sets' => array(
					'type' => 'checkbox',
					'choices' => $groups->getDictionary('group_id', 'group_title'),
					'value' => $active,
					'disabled_choices' => array(1)
				)
			)
		);

		// --------------------------------------
		// Compose view data
		// --------------------------------------

		$this->data = array(
			'base_url' => $this->mcp_url('save_settings'),
			'save_btn_text' => 'save_settings',
			'save_btn_text_working' => 'saving',
			'sections' => $sections
		);

		// --------------------------------------
		// Set title and breadcrumb
		// --------------------------------------

		$this->set_cp_var('cp_page_title', lang('settings'));
		$this->set_cp_crumb($this->mcp_url(), lang('low_reorder_module_name'));

		return $this->view('form');
	}

	/**
	 * Save the settings
	 *
	 * @access      public
	 * @return      void
	 */
	public function save_settings()
	{
		// --------------------------------------
		// Get settings to sve
		// --------------------------------------

		$settings = array(
			'can_create_sets' => ee('Request')->post('can_create_sets', array())
		);

		// --------------------------------------
		// Update DB
		// --------------------------------------

		ee()->db->where('class', $this->class_name.'_ext');
		ee()->db->update('extensions', array('settings' => serialize($settings)));

		// --------------------------------------
		// Set feedback message
		// --------------------------------------

		ee('CP/Alert')->makeInline('shared-form')
			->asSuccess()
			->withTitle(lang('settings_saved'))
			->defer();

		ee()->functions->redirect($this->mcp_url('settings'));
	}

	// --------------------------------------------------------------------

	/**
	 * Edit reorder set
	 *
	 * @access      public
	 * @return      string
	 */
	public function edit($set_id = 'new')
	{
		// --------------------------------------
		// Get set from DB or empty row
		// --------------------------------------

		$set = ($set_id == 'new')
			? ee()->low_reorder_set_model->empty_row()
			: ee()->low_reorder_set_model->get_one($set_id);

		// --------------------------------------
		// Bail out for non-existing set
		// --------------------------------------

		if ($set_id != 'new' && empty($set))
		{
			show_404();
		}

		// --------------------------------------
		// Check set permissions
		// --------------------------------------

		$permissions = json_decode($set['permissions'], TRUE);
		$user = $this->set->get_permissions($permissions);

		if (($set_id == 'new' && ! $this->can_create()) || ($set != 'new' && ! $user['can_edit']))
		{
			show_error('Operation not permitted');
		}

		// --------------------------------------
		// Initiate form sections
		// --------------------------------------

		$sections = array();

		// --------------------------------------
		// Set Label
		// --------------------------------------

		$sections[0][] = array(
			'title' => 'set_label',
			'desc' => 'set_label_help',
			'fields' => array(
				'set_id' => array(
					'type' => 'hidden',
					'value' => $set_id
				),
				'set_label' => array(
					'required' => TRUE,
					'type' => 'text',
					'value' => $set['set_label']
				)
			)
		);

		// --------------------------------------
		// Set Name
		// --------------------------------------

		$sections[0][] = array(
			'title' => 'set_name',
			'desc' => 'set_name_help',
			'fields' => array(
				'set_name' => array(
					'required' => TRUE,
					'type' => 'text',
					'value' => $set['set_name']
				)
			)
		);

		// --------------------------------------
		// Set Notes
		// --------------------------------------

		$sections[0][] = array(
			'title' => 'set_notes',
			'desc' => 'set_notes_help',
			'fields' => array(
				'set_notes' => array(
					'type' => 'textarea',
					'value' => $set['set_notes']
				)
			)
		);

		// --------------------------------------
		// Do what with new entries?
		// --------------------------------------

		$choices = array(
			'append' => lang('append'),
			'prepend' => lang('prepend')
		);

		$sections[0][] = array(
			'title' => 'new_entries',
			'desc' => 'new_entries_help',
			'fields' => array(
				'new_entries' => array(
					'type' => 'select',
					'choices' => $choices,
					'value' => $set['new_entries']
				)
			)
		);

		// --------------------------------------
		// Filter Options section
		// --------------------------------------

		$name   = 'filter_options';
		$params = $this->set->get_params($set['parameters']);

		// --------------------------------------
		// Show Expired Entries
		// --------------------------------------

		$sections[$name][] = array(
			'title' => 'show_expired_entries',
			'fields' => array(
				'parameters[show_expired]' => array(
					'type'  => 'yes_no',
					'value' => $params['show_expired']
				)
			)
		);

		// --------------------------------------
		// Show Future Entries
		// --------------------------------------

		$sections[$name][] = array(
			'title' => 'show_future_entries',
			'fields' => array(
				'parameters[show_future_entries]' => array(
					'type'  => 'yes_no',
					'value' => $params['show_future_entries']
				)
			)
		);

		// --------------------------------------
		// Sticky
		// --------------------------------------

		$sections[$name][] = array(
			'title' => 'sticky_only',
			'fields' => array(
				'parameters[sticky]' => array(
					'type'  => 'yes_no',
					'value' => $params['sticky']
				)
			)
		);

		// --------------------------------------
		// Channels
		// --------------------------------------

		$channels = ee('Model')
			->get('Channel')
			->filter('site_id', $this->site_id)
			->order('channel_title', 'ASC')
			->all();

		$choices = $channels->getDictionary('channel_id', 'channel_title');

		$sections[$name][] = array(
			'title' => 'channels',
			'fields' => array(array(
				'required' => TRUE,
				'type' => 'html',
				'content' => form_multiselect(
					'channels[]',
					$choices,
					low_delinearize($set['channels']),
					'multiple class="low-select-multiple"'
				)
			))
		);

		// --------------------------------------
		// Statuses
		// --------------------------------------

		// Initiate status choices
		$choices = array(
			'open'   => lang('open'),
			'closed' => lang('closed')
		);

		// Get statuses from DB
		$statuses = ee('Model')
			->get('Status')
			->with('StatusGroup')
			->filter('site_id', $this->site_id)
			->filter('status', 'NOT IN', array_keys($choices))
			->order('StatusGroup.group_name')
			->order('Status.status_order')
			->all();

		// Add statuses to choices
		foreach ($statuses as $status)
		{
			$choices[$status->StatusGroup->group_name][$status->status] = $status->status;
		}

		// Add to form
		$sections[$name][] = array(
			'title' => 'statuses',
			'required' => TRUE,
			'fields' => array(array(
				'required' => TRUE,
				'type' => 'html',
				'content' => form_multiselect(
					'parameters[status][]',
					$choices,
					low_delinearize($params['status']),
					'multiple class="low-select-multiple"'
				)
			))
		);

		// --------------------------------------
		// Categories
		// --------------------------------------

		// Data for sub-view
		$data = array(
			'cat_option'    => $set['cat_option'],
			'cat_options'   => array('all', 'some', 'one', 'none'),
			'groups'        => $this->get_categories(),
			'active_groups' => low_delinearize($set['cat_groups']),
			'active_cats'   => low_delinearize(@$params['category'])
		);

		// Add to form
		$sections[$name][] = array(
			'title' => 'categories',
			'fields' => array(array(
				'type' => 'html',
				'content' => ee('View')->make($this->package.':edit/categories')->render($data)
			))
		);

		// --------------------------------------
		// Get channel fields for search: params
		// --------------------------------------

		$choices = array();
		$fields = ee('Model')
			->get('ChannelField')
			->with('ChannelFieldGroup')
			->filter('site_id', $this->site_id)
			->order('ChannelFieldGroup.group_name')
			->order('field_order')
			->all();

		foreach ($fields as $field)
		{
			$choices[$field->ChannelFieldGroup->group_name][$field->field_name] = $field->field_label;
		}

		$data = array(
			'choices' => $choices,
			'params'  => json_encode($this->set->get_search_params($params))
		);

		// Add to form
		$sections[$name][] = array(
			'title' => 'search_fields',
			'fields' => array(array(
				'type' => 'html',
				'content' => ee('View')->make($this->package.':edit/fields')->render($data)
			))
		);

		// --------------------------------------
		// Member groups for permissions
		// --------------------------------------

		$groups = ee('Model')
			->get('MemberGroup')
			->filter('group_id', 'NOT IN', range(1, 4))
			->filter('can_access_cp', 'y')
			->order('group_title')
			->all();

		if ($groups->count())
		{
			// Init permission choices
			$choices = array();

			// Add permissions to choices
			foreach (range(0, 2) as $i)
			{
				$choices[$i] = lang('permissions_'.$i);
			}

			// For each member group, add permissions option
			foreach ($groups as $group)
			{
				$sections['permissions'][] = array(
					'title' => $group->group_title,
					'fields' => array(
						"permissions[{$group->group_id}]" => array(
							'type' => 'inline_radio',
							'choices' => $choices,
							'value' => isset($permissions[$group->group_id])
								? $permissions[$group->group_id] : 0
						)
					)
				);
			}
		}

		// --------------------------------------
		// Add set data to view
		// --------------------------------------

		$this->data['set'] = $set;

		// --------------------------------------
		// Submit buttons
		// --------------------------------------

		$buttons = array(array(
			'type'  => 'submit',
			'name'  => 'submit',
			'value' => 'edit',
			'text'  => 'save_set',
			'working' => 'saving'
		));

		if ($user['can_reorder'])
		{
			$buttons[] = array(
				'type'  => 'submit',
				'name'  => 'submit',
				'value' => 'reorder',
				'text'  => 'save_and_reorder',
				'working' => 'saving'
			);
		}

		// --------------------------------------
		// Compose view data
		// --------------------------------------

		$this->data = array(
			'base_url' => $this->mcp_url('save_set'),
			'buttons'  => $buttons,
			'sections' => $sections
		);

		// --------------------------------------
		// Set title and breadcrumb
		// --------------------------------------

		$title = ($set_id == 'new')
			? lang('create_new_set')
			: lang('edit_set').' #'.$set_id;

		$this->set_cp_var('cp_page_title', $title);
		$this->set_cp_crumb($this->mcp_url(), lang('low_reorder_module_name'));

		// Return settings form
		return $this->view('form');
	}

	/**
	* Save set settings
	*
	* @access      public
	* @return      void
	*/
	public function save_set()
	{
		// --------------------------------------
		// Get Set id
		// --------------------------------------

		if ( ! ($set_id = ee('Request')->post('set_id')))
		{
			show_error(lang('invalid_request'));
		}

		// --------------------------------------
		// Init data array
		// --------------------------------------

		$data = array();

		// --------------------------------------
		// Regular fields
		// --------------------------------------

		foreach ($this->set->attributes() as $attr)
		{
			$data[$attr] = ee('Request')->post($attr);
		}

		// --------------------------------------
		// Validate some fields
		// --------------------------------------

		// Label shouldn't be empty
		if (empty($data['set_label']))
		{
			show_error(lang('set_label_empty'));
		}

		// Set name should be valid
		if ( ! preg_match('/^[-_\w:]+$/i', $data['set_name']))
		{
			show_error(lang('set_name_invalid'));
		}

		// Check if set name is unique for this site
		if ( ! $this->set->name_is_unique($set_id, $data['set_name'], $this->site_id))
		{
			show_error(lang('set_name_not_unique'));
		}

		// Channels shouldn't be empty
		if (empty($data['channels']))
		{
			show_error(lang('channels_empty'));
		}

		// Default to status = open if none are given
		if (empty($data['parameters']['status']))
		{
			$data['parameters']['status'][] = 'open';
		}

		// Cat groups are required for 'one'
		if ($data['cat_option'] == 'one')
		{
			if (empty($data['cat_groups'])) show_error(lang('cat_groups_empty'));
		}
		else
		{
			// If not set to 'one', set cat_groups to empty
			$data['cat_groups'] = '';
		}

		// Unset category parameter
		if ($data['cat_option'] != 'some')
		{
			unset($data['parameters']['category']);
		}

		// --------------------------------------
		// Set site id
		// --------------------------------------

		$data['site_id'] = $this->site_id;

		// --------------------------------------
		// Get parameters
		// --------------------------------------

		// Store channel_short_names and IDs in parameters
		if ( ! empty($data['channels']))
		{
			$channels = ee('Model')
				->get('Channel')
				->filter('channel_id', 'IN', $data['channels'])
				->all();

			$data['parameters']['channel'] = $channels->getDictionary('channel_id', 'channel_name');
			$data['parameters']['channel_id'] = $data['channels'];
		}

		// --------------------------------------
		// Add search filters to parameters
		// --------------------------------------

		if ($search = ee('Request')->post('search'))
		{
			foreach ($search['fields'] as $i => $key)
			{
				// Skip invalid
				if (empty($search['values'][$i])) continue;

				$data['parameters']['search:'.$key] = $search['values'][$i];
			}
		}

		// --------------------------------------
		// Get parameters and permissions
		// --------------------------------------

		$data['parameters']  = $this->set->get_params($data['parameters']);
		$data['permissions'] = (array) ee('Request')->post('permissions');

		// Copy data to $sql_data
		$sql_data = $data;

		// --------------------------------------
		// Convert sql_data to strings
		// --------------------------------------

		$sql_data['parameters']  = json_encode($sql_data['parameters']);
		$sql_data['permissions'] = json_encode($sql_data['permissions']);

		foreach ($sql_data as &$val)
		{
			if (is_array($val))
			{
				$val = count($val) ? low_linearize($val) : '';
			}
		}

		// --------------------------------------
		// Insert or Update
		// --------------------------------------

		if ($set_id == 'new')
		{
			$set_id = $this->set->insert($sql_data);
		}
		else
		{
			$this->set->update($set_id, $sql_data);
		}

		// --------------------------------------
		// Insert order
		// --------------------------------------

		// Shorcut to parameters
		$params = $data['parameters'];

		// Account for uncategorized entries
		if ($data['cat_option'] == 'none')
		{
			$params['uncategorized_entries'] = 'yes';
		}

		// Initiate orders for all categories
		if ($data['cat_option'] == 'one')
		{
			$query = ee()->db
				->select("c.cat_id, GROUP_CONCAT(t.entry_id ORDER BY t.entry_date DESC SEPARATOR '|') AS entry_ids", FALSE)
				->from('channel_titles t')
				->join('category_posts cp', 't.entry_id = cp.entry_id')
				->join('categories c', 'c.cat_id = cp.cat_id')
				->where_in('t.channel_id', explode('|', $params['channel_id']))
				->where_in('t.status', explode('|', $params['status']))
				->where_in('c.group_id', $data['cat_groups'])
				->group_by('c.cat_id')
				->get();

			foreach ($query->result() as $row)
			{
				$this->order->insert_ignore(array(
					'set_id'     => $set_id,
					'cat_id'     => $row->cat_id,
					'sort_order' => ($row->entry_ids ? "|{$row->entry_ids}|" : '')
				));
			}
		}
		// Initiate order for regular sets
		else
		{
			$entries = low_flatten_results($this->get_entries($params), 'entry_id');

			$this->order->insert_ignore(array(
				'set_id'     => $set_id,
				'cat_id'     => 0,
				'sort_order' => low_linearize($entries)
			));
		}

		// -------------------------------------
		// 'low_reorder_post_save_set' hook.
		//  - Do something after the (new) set has been saved
		// -------------------------------------

		if (ee()->extensions->active_hook('low_reorder_post_save_set') === TRUE)
		{
			// Use raw, non-encoded data to pass through
			ee()->extensions->call('low_reorder_post_save_set', $set_id, $data);
		}

		// --------------------------------------
		// Set feedback message
		// --------------------------------------

		ee('CP/Alert')->makeInline('shared-form')
			->asSuccess()
			->withTitle(lang('settings_saved'))
			->defer();

		// --------------------------------------
		// Go back to set or reoder page
		// --------------------------------------

		$method = ee('Request')->post('submit') ?: 'edit';

		ee()->functions->redirect($this->mcp_url($method .'/'. $set_id));
	}

	// --------------------------------------------------------------------

	/**
	* List entries for single channel/field combo
	*
	* @access      public
	* @return      string
	*/
	public function reorder($set_id, $cat_id = 0)
	{
		// --------------------------------------
		// Get Set
		// --------------------------------------

		if ( ! ($set = $this->set->get_one($set_id)))
		{
			show_404();
		}

		// --------------------------------------
		// Get settings
		// --------------------------------------

		$params = $this->set->get_params($set['parameters']);
		$perm   = $this->set->get_permissions($set['permissions']);

		// --------------------------------------
		// Change channels to array
		// --------------------------------------

		$set['channels']   = low_delinearize($set['channels']);
		$set['cat_groups'] = low_delinearize($set['cat_groups']);

		// --------------------------------------
		// Pre-define some variables for the view
		// --------------------------------------

		$this->data['show_entries']    = TRUE;
		$this->data['select_category'] = FALSE;

		// --------------------------------------
		// Get selected category, if there is one
		// --------------------------------------

		$set['cat_id'] = $cat_id;

		// --------------------------------------
		// If cat_option == 'one', a category must be selected first
		// And we need to get a list of categories to put in the
		// category selection drop down
		// --------------------------------------

		if ($set['cat_option'] == 'one' && ! empty($set['cat_groups']))
		{
			// We need to show the category select
			$this->data['select_category']   = TRUE;
			$this->data['selected_category'] = $cat_id;

			// Content of the category select
			$this->data['groups'] = $this->get_categories($set['cat_groups']);

			// Showing entries depends on selected category
			$this->data['show_entries'] = ($cat_id > 0);

			// URL to jump to
			$this->data['url'] = $this->mcp_url('reorder/'.$set_id);

			// Limit query to selected category
			$params['category'] = $cat_id;
		}
		elseif ($set['cat_option'] == 'none')
		{
			// Make sure only uncategorized entries will be fetched
			$params['uncategorized_entries'] = 'yes';
		}

		// --------------------------------------
		// If we're showing entries, get them first
		// --------------------------------------

		$rows = array();

		if ($this->data['show_entries'])
		{
			// Get the current order from the DB
			ee()->db->where('cat_id', $cat_id);
			$order = $this->order->get_one($set_id, 'set_id');
			$set_order = empty($order) ? array() : low_delinearize($order['sort_order']);

			// Add channel_id as parameter
			$params['channel_id'] = implode('|', $set['channels']);

			// Get 'em, sonny boy
			$entries = $this->get_entries($params, $set_order);

			// Edit entry url
			$edit_tmpl = '<a href="'.ee('CP/URL', 'publish/edit/entry')->compile().'/%d">%s</a>';

			// Loop through row, add stuff
			foreach ($entries as &$row)
			{
				// Escape title
				$row['title'] = htmlspecialchars($row['title']);

				// Add default hidden divs
				$row['hidden'] = array(
					sprintf($edit_tmpl, $row['entry_id'], lang('edit')),
					ucfirst($row['status']),
					'#'.$row['entry_id'],
				);

				$rows[] = array(
					$row['entry_id'],
					$row['title'],
					$row['hidden']
				);
			}

			// -------------------------------------
			// 'low_reorder_show_entries' hook.
			//  - Change the output of entries displayed in the CP reorder list
			// -------------------------------------

			if (ee()->extensions->active_hook('low_reorder_show_entries') === TRUE)
			{
				$entries = ee()->extensions->call('low_reorder_show_entries', $entries, $set);
			}

			$this->data['entries'] = $entries;
		}

		// Add settings to data as well
		$this->data['set'] = $set;
		$this->data['params'] = $params;
		$this->data['perm'] = $perm;
		$this->data['edit_url'] = $this->mcp_url('edit/'.$set_id);

		// --------------------------------------
		// Initiate table data
		// --------------------------------------

		$this->data['action'] = $this->mcp_url('save_order');
		$this->data['hidden'] = array(
			'set_id' => $set_id,
			'cat_id' => $cat_id
		);

		// --------------------------------------
		// Set title and breadcrumb
		// --------------------------------------

		$this->set_cp_var('cp_page_title', $set['set_label']);
		$this->set_cp_crumb($this->mcp_url(), lang('low_reorder_module_name'));

		// Return settings form
		return $this->view('reorder');
	}

	/**
	 * Save the New Order (dundundun)
	 *
	 * @access      public
	 *  @return      void
	 */
	public function save_order()
	{
		// --------------------------------------
		// Get Set id
		// --------------------------------------

		if ( ! ($set_id = ee('Request')->post('set_id')))
		{
			return $this->_show_error('invalid_request');
		}

		// --------------------------------------
		// Get Cat id
		// --------------------------------------

		$cat_id = ee('Request')->post('cat_id');

		// --------------------------------------
		// Get entries
		// --------------------------------------

		$entries = (array) ee('Request')->post('entries');

		// --------------------------------------
		// REPLACE INTO table statement
		// --------------------------------------

		$this->order->replace(array(
			'set_id' => $set_id,
			'cat_id' => $cat_id,
			'sort_order' => low_linearize($entries)
		));

		// --------------------------------------
		// That's the entries updated
		// Now, do we need to clear the cache?
		// --------------------------------------

		$clear_cache = (ee('Request')->post('clear_cache') == 'y');

		if ($clear_cache)
		{
			ee()->functions->clear_caching('all', '', TRUE);
		}

		// -------------------------------------
		// 'low_reorder_post_sort' hook.
		//  - Do something after new order is saved
		// -------------------------------------

		if (ee()->extensions->active_hook('low_reorder_post_sort') === TRUE)
		{
			ee()->extensions->call('low_reorder_post_sort', $entries, $clear_cache);
		}

		// --------------------------------------
		// Set feedback message
		// --------------------------------------

		ee('CP/Alert')->makeInline('shared-form')
			->asSuccess()
			->withTitle(lang('new_order_saved'))
			->defer();

		// --------------------------------------
		// Get ready to redirect back
		// --------------------------------------

		$url = array('reorder', $set_id);

		if ($cat_id) $url[] = $cat_id;

		// And go back
		ee()->functions->redirect($this->mcp_url($url));
	}

	// --------------------------------------------------------------------
	/**
	 * Delete a set
	 *
	 * @access      public
	 * @return      void
	 */
	public function delete()
	{
		// --------------------------------------
		// Check set id
		// --------------------------------------

		if ($set_id = ee('Request')->post('set_id'))
		{
			// --------------------------------------
			// Delete in 2 tables
			// --------------------------------------

			ee()->low_reorder_set_model->delete($set_id);
			ee()->low_reorder_order_model->delete($set_id, 'set_id');

			// --------------------------------------
			// Set feedback message
			// --------------------------------------

			ee('CP/Alert')
				->makeInline('shared-form')
				->asSuccess()
				->withTitle(count($set_id) == 1 ? lang('set_deleted') : lang('sets_deleted'))
				->defer();
		}

		// --------------------------------------
		// Go home
		// --------------------------------------

		ee()->functions->redirect($this->mcp_url());
	}

	// --------------------------------------------------------------------
	// PRIVATE METHODS
	// --------------------------------------------------------------------

	/**
	 * Get settings
	 *
	 * @access     private
	 * @param      string
	 * @return     mixed
	 */
	private function get_settings($which = FALSE)
	{
		if (empty($this->settings))
		{
			// Check cache
			if (($this->settings = low_get_cache($this->package, 'settings')) === FALSE)
			{
				// Not in cache? Get from DB and add to cache
				$query = ee()->db->select('settings')
				       ->from('extensions')
				       ->where('class', $this->class_name.'_ext')
				       ->limit(1)
				       ->get();

				$this->settings = (array) @unserialize($query->row('settings'));

				// Add to cache
				low_set_cache($this->package, 'settings', $this->settings);
			}
		}

		// Always fallback to default settings
		$this->settings = array_merge($this->default_settings, $this->settings);

		if ($which !== FALSE)
		{
			return isset($this->settings[$which]) ? $this->settings[$which] : FALSE;
		}
		else
		{
			return $this->settings;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get categories from a given optional group including depth key
	 *
	 * @access     private
	 * @param      mixed
	 * @return     array
	 */
	private function get_categories($group_id = NULL)
	{
		// --------------------------------------
		// Initiate return value
		// --------------------------------------

		$groups = array();

		// --------------------------------------
		// Compose Categories model, joined with groups
		// --------------------------------------

		$categories = ee('Model')
			->get('Category')
			->with('CategoryGroup')
			->filter('site_id', $this->site_id)
			->order('CategoryGroup.group_name')
			->order('cat_order');

		// --------------------------------------
		// Optionally filter by group
		// --------------------------------------

		if ( ! empty($group_id))
		{
			// Force to array
			if ( ! is_array($group_id)) $group_id = array($group_id);

			// Filter by group
			$categories->filter('group_id', 'IN', $group_id);
		}

		// --------------------------------------
		// Get 'em boys
		// --------------------------------------

		$categories = $categories->all();

		// --------------------------------------
		// Populates the $groups array into nested array
		// --------------------------------------

		foreach ($categories as $cat)
		{
			if ( ! array_key_exists($cat->group_id, $groups))
			{
				$groups[$cat->group_id] = array(
					'id'   => $cat->group_id,
					'name' => $cat->CategoryGroup->group_name,
					'categories' => array()
				);
			}

			$groups[$cat->group_id]['categories'][] = array(
				'id' => $cat->cat_id,
				'parent_id' => $cat->parent_id,
				'name' => $cat->cat_name
			);
		}

		// --------------------------------------
		// To add depth, we need the tree factory
		// --------------------------------------

		$tree = new EllisLab\ExpressionEngine\Library\DataStructure\Tree\TreeFactory;

		// --------------------------------------
		// Create trees for each category group,
		// and turn back into flat array with added 'depth' key
		// --------------------------------------

		foreach ($groups as &$group)
		{
			if (empty($group['categories'])) continue;

			// Generate nested tree
			$root = $tree->fromList($group['categories']);

			// Overwrite the categories with added depth
			$group['categories'] = $this->add_tree_depth($root->getChildren());
		}

		// --------------------------------------
		// Return the groups
		// --------------------------------------

		return $groups;
	}

	/**
	 * Add depth to a Tree
	 *
	 * @access      private
	 * @param       array
	 * @param       int
	 * @param       array
	 * @param       int
	 * @return      array
	 */
	private function add_tree_depth($array, $result = array(), $depth = 0)
	{
		foreach ($array as $node)
		{
			// This node's data
			$result[] = array_merge($node->data, array('depth' => $depth));

			// Does this node have children?
			if ($children = $node->getChildren())
			{
				// Add those to the result
				$result = $this->add_tree_depth($children, $result, $depth + 1);
			}
		}

		// And return it
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Check if current user can create new sets
	 *
	 * @access     private
	 * @return     bool
	 */
	private function can_create()
	{
		// Get the creator member groups from settings
		$creators = (array) $this->get_settings('can_create_sets');

		// Add superadmins to them
		$creators[] = 1;

		return in_array($this->member_group, $creators);
	}

	// --------------------------------------------------------------------

	/**
	 * Return an MCP URL
	 *
	 * @access     private
	 * @param      string
	 * @param      mixed     [array|string]
	 * @param      bool
	 * @return     mixed
	 */
	private function mcp_url($path = NULL, $extra = NULL, $obj = FALSE)
	{
		// Base settings
		$segments = array('addons', 'settings', $this->package);

		// Add method to segments, of given
		if (is_string($path)) $segments[] = $path;
		if (is_array($path)) $segments = array_merge($segments, $path);

		// Create the URL
		$url = ee('CP/URL', implode('/', $segments));

		// Add the extras to it
		if ( ! empty($extra))
		{
			// convert to array
			if ( ! is_array($extra)) parse_str($extra, $extra);

			// And add to the url
			$url->addQueryStringVariables($extra);
		}

		// Return it
		return ($obj) ? $url : $url->compile();
	}

	/**
	 * Set cp var
	 *
	 * @access     private
	 * @param      string
	 * @param      string
	 * @return     void
	 */
	private function set_cp_var($key, $val)
	{
		ee()->view->$key = $val;

		if ($key == 'cp_page_title')
		{
			$this->heading = $val;
			$this->data[$key] = $val;
		}
	}

	/**
	 * Set cp breadcrumb
	 *
	 * @access     private
	 * @param      string
	 * @param      string
	 * @return     void
	 */
	private function set_cp_crumb($url, $text)
	{
		$this->crumb[$url] = $text;
	}

	/**
	 * View add-on page
	 *
	 * @access     protected
	 * @param      string
	 * @return     string
	 */
	private function view($file)
	{
		// -------------------------------------
		//  Load CSS and JS
		// -------------------------------------

		$version = '&amp;v=' . (static::DEBUG ? time() : $this->version);

		ee()->cp->load_package_css($this->package.$version);
		ee()->cp->load_package_js($this->package.$version);

		// -------------------------------------
		//  Main page header
		// -------------------------------------

		// Define header
		$header = array('title' => $this->info->getName());

		// SuperAdmins can access settings
		if ($this->member_group == 1)
		{
			$header['toolbar_items'] = array(
				'settings' => array(
					'href'  => $this->mcp_url('settings'),
					'title' => lang('settings')
				)
			);
		}

		// And actually set the header
		ee()->view->header = $header;

		// Don't need a sidebar, methinks

		// -------------------------------------
		//  Return the view
		// -------------------------------------

		$view = array(
			'heading' => $this->heading,
			'breadcrumb' => $this->crumb,
			'body' => ee('View')->make($this->package.':'.$file)->render($this->data)
		);

		return $view;
	}
}
// End mcp.low_reorder.php