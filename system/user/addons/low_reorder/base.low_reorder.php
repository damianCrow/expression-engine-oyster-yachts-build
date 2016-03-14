<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Low Reorder Base Class
 *
 * @package        low_reorder
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-reorder
 * @copyright      Copyright (c) 2016, Low
 */
abstract class Low_reorder_base {

	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * Add-on version
	 *
	 * @var        string
	 * @access     public
	 */
	public $version;

	/**
	 * Extension settings
	 *
	 * @var        array
	 * @access     public
	 */
	public $settings;

	// --------------------------------------------------------------------

	/**
	 * Package name
	 *
	 * @var        string
	 * @access     protected
	 */
	protected $package = 'low_reorder';

	/**
	 * This add-on's info based on setup file
	 *
	 * @access      private
	 * @var         object
	 */
	protected $info;

	/**
	 * Main class shortcut
	 *
	 * @var        string
	 * @access     protected
	 */
	protected $class_name;

	/**
	 * Site id shortcut
	 *
	 * @var        int
	 * @access     protected
	 */
	protected $site_id;

	/**
	 * Libraries used
	 *
	 * @var        array
	 * @access     protected
	 */
	protected $libraries = array(
		'Low_reorder_fields'
	);

	/**
	 * Models used
	 *
	 * @var        array
	 * @access     protected
	 */
	protected $models = array(
		'low_reorder_set_model',
		'low_reorder_order_model'
	);

	/**
	 * Default extension settings
	 */
	protected $default_settings = array(
		'can_create_sets' => array()
	);

	// --------------------------------------------------------------------
	// METHODS
	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access     public
	 * @return     void
	 */
	public function __construct()
	{
		// -------------------------------------
		//  Set info and version
		// -------------------------------------

		$this->info = ee('App')->get($this->package);
		$this->version = $this->info->getVersion();

		// -------------------------------------
		//  Load helper, libraries and models
		// -------------------------------------

		ee()->load->helper($this->package);
		ee()->load->library($this->libraries);
		ee()->load->model($this->models);

		// -------------------------------------
		//  Class name shortcut
		// -------------------------------------

		$this->class_name = ucfirst($this->package);

		// -------------------------------------
		//  Get site shortcut
		// -------------------------------------

		$this->site_id = (int) ee()->config->item('site_id');
	}

	// --------------------------------------------------------------------

	/**
	 * Get simple list of entries based on given parameters, set order and limit
	 *
	 * @access     private
	 * @param      array
	 * @param      string
	 * @param      int
	 * @return     array
	 */
	protected function get_entries($params, $set_order = array(), $limit = FALSE)
	{
		// --------------------------------------
		// Check search params
		// --------------------------------------

		$where = array();

		if ($search = ee()->low_reorder_set_model->get_search_params($params))
		{
			foreach ($search as $field => $value)
			{
				if ($field = ee()->low_reorder_fields->name($field))
				{
					$where[] = ee()->low_reorder_fields->sql('d.'.$field, $value);
				}
			}
		}

		// --------------------------------------
		//	Get channel entries
		// --------------------------------------

		ee()->db
			->select('DISTINCT(t.entry_id), t.channel_id, t.title, t.status, t.url_title')
			->from('channel_titles t')
			->where_in('t.site_id', ee()->low_reorder_fields->site_ids());

		// Limit by channel_ids
		if ( ! empty($params['channel_id']))
		{
			// Determine which statuses to filter by
			list($channel_ids, $in) = low_explode_param($params['channel_id']);

			// Adjust query accordingly
			ee()->db->{($in ? 'where_in' : 'where_not_in')}('t.channel_id', $channel_ids);
		}

		// Limit by entry_ids
		if ( ! empty($params['entry_id']))
		{
			// Determine which statuses to filter by
			list($entry_ids, $in) = low_explode_param($params['entry_id']);

			// Adjust query accordingly
			ee()->db->{($in ? 'where_in' : 'where_not_in')}('t.entry_id', $entry_ids);
		}

		// Limit by status
		if ( ! empty($params['status']))
		{
			// Determine which statuses to filter by
			list($status, $in) = low_explode_param($params['status']);

			// Adjust query accordingly
			ee()->db->{($in ? 'where_in' : 'where_not_in')}('t.status', $status);
		}

		// Uncategorized entries
		if (@$params['uncategorized_entries'] == 'yes')
		{
			ee()->db->join('category_posts ucp', 't.entry_id = ucp.entry_id', 'left');
			ee()->db->where('ucp.cat_id IS NULL');
		}

		// Limit by category
		if ( ! empty($params['category']))
		{
			// Determine which categories to filter by
			list($categories, $in) = low_explode_param($params['category']);

			// Join table
			ee()->db->join('category_posts cp', 't.entry_id = cp.entry_id');
			ee()->db->{($in ? 'where_in' : 'where_not_in')}('cp.cat_id', $categories);
		}

		// Hide expired entries
		if (@$params['show_expired'] != 'yes')
		{
			ee()->db->where(sprintf("(t.expiration_date = 0 OR t.expiration_date >= '%s')",
				ee()->localize->now));
		}

		// Hide expired entries
		if (@$params['show_future_entries'] != 'yes')
		{
			ee()->db->where('t.entry_date <=', ee()->localize->now);
		}

		// Sticky only
		if (@$params['sticky'] == 'yes')
		{
			ee()->db->where('t.sticky', 'y');
		}

		// Limit by where search
		if ( ! empty($where))
		{
			ee()->db->join('channel_data d', 't.entry_id = d.entry_id');
			ee()->db->where(implode(' AND ', $where), NULL, FALSE);
		}

		// Order by given set order or entry date as fallback
		if ($set_order !== FALSE)
		{
			if ($set_order)
			{
				// Reverse it
				if (@$params['sort'] == 'desc')
				{
					$set_order = array_reverse($set_order);
				}

				ee()->db->order_by('FIELD(t.entry_id,'.implode(',', $set_order).')', FALSE, FALSE);
			}
			else
			{
				// Order by custom order, fallback to entry date
				ee()->db->order_by('t.entry_date', 'desc');
			}
		}

		// Optional limit
		if ($limit)
		{
			ee()->db->limit($limit);
		}

		$query = ee()->db->get();

		// --------------------------------------
		// Return the retrieved entries
		// --------------------------------------

		return $query->result_array();
	}

	// --------------------------------------------------------------------

} // End class low_reorder_base