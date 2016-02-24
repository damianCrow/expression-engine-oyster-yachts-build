<?php
namespace EllisLab\ExpressionEngine\Service\EntryListing;

use Serializable;
use BadMethodCallException;
use InvalidArgumentException;
use EllisLab\ExpressionEngine\Service\View\View;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		https://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine CP Entry Listing Service
 *
 * @package		ExpressionEngine
 * @category	Service
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class EntryListing {

	/**
	 * @var Filter $channel_filter Channel Filter object
	 */
	public $channel_filter;

	/**
	 * @var Filter $category_filter Category Filter object
	 */
	public $category_filter;

	/**
	 * @var Filter $status_filter Status Filter object
	 */
	public $status_filter;

	/**
	 * @var int $site_id Current site ID
	 */
	protected $site_id;

	/**
	 * @var boolean $is_admin Whether or not a Super Admin is making this
	 * request, skips $allowed_channels check
	 */
	protected $is_admin;

	/**
	 * @var array $allowed_channels IDs of channels this user is allowed to access
	 */
	protected $allowed_channels;

	/**
	 * @var int $now Timestamp of current time, used to filter entries by date
	 */
	protected $now;

	/**
	 * @var string $search_value Search critera to filter entries by
	 */
	protected $search_value;

	/**
	 * @var Query\Builder $entries Builder object for the channel entries
	 */
	protected $entries;

	/**
	 * @var FilterFactory $filters FilterFactory object
	 */
	protected $filters;

	/**
	 * Constructor
	 * @param int $site_id Current site ID
	 * @param boolean $is_admin Whether or not a Super Admin is making this
	 * request, skips $allowed_channels check
	 * @param array $allowed_channels IDs of channels this user is allowed to access
	 * @param int $now Timestamp of current time, used to filter entries by date
	 * @param string $search_value Search critera to filter entries by
	 */
	public function __construct($site_id, $is_admin, $allowed_channels = array(), $now = NULL, $search_value = NULL)
	{
		$this->site_id = $site_id;
		$this->is_admin = $is_admin;
		$this->allowed_channels = $allowed_channels;
		$this->now = $now;
		$this->search_value = $search_value;

		$this->setupFilters();
		$this->setupEntries();
	}

	/**
	 * Getter for channel entries Query\Builder object
	 *
	 * @return Query\Builder
	 */
	public function getEntries()
	{
		return $this->entries;
	}

	/**
	 * Getter for channel entries Query\Builder object
	 *
	 * @return FilterFactory
	 */
	public function getFilters()
	{
		$count = $this->getEntries()->count();

		// Add this last to get the right $count
		$this->filters->add('Perpage', $count, 'all_entries');

		return $this->filters;
	}

	/**
	 * Sets up our various filters for showing an entry listing and
	 * creates the FilterFactory object
	 */
	private function setupFilters()
	{
		$channel = NULL;

		$this->channel_filter = $this->createChannelFilter();

		if ($this->channel_filter->value())
		{
			$channel = ee('Model')->get('Channel', $this->channel_filter->value())
				->first();
		}

		$this->category_filter = $this->createCategoryFilter($channel);
		$this->status_filter = $this->createStatusFilter($channel);

		$this->filters = ee('CP/Filter')
			->add($this->channel_filter)
			->add($this->category_filter)
			->add($this->status_filter)
			->add('Date');
	}

	/**
	 * Given various filters and permissions, sets up an entries Query\Builder
	 * to be passed on to the caller to optionally add more filtering to
	 */
	protected function setupEntries()
	{
		$entries = ee('Model')->get('ChannelEntry')
			->filter('site_id', $this->site_id);

		// We need to filter by Channel first (if necissary) as that will
		// impact the entry count for the perpage filter
		$channel_id = $this->channel_filter->value();

		// If we have a selected channel filter, and we are not an admin, we
		// first need to ensure it is in the list of assigned channels. If it
		// is we will filter by that id. If not we throw an error.
		$channel = NULL;
		if ($channel_id)
		{
			if ($this->is_admin || in_array($channel_id, $this->allowed_channels))
			{
				$entries->filter('channel_id', $channel_id);
				$channel = ee('Model')->get('Channel', $channel_id)
					->first();
				$channel_name = $channel->channel_title;
			}
			else
			{
				show_error(lang('unauthorized_access'));
			}
		}
		// If we have no selected channel filter, and we are not an admin, we
		// need to filter via WHERE IN
		else
		{
			if ( ! $this->is_admin)
			{
				if (empty($this->allowed_channels))
				{
					show_error(lang('no_channels'));
				}

				$entries->filter('channel_id', 'IN', $this->allowed_channels);
			}
		}

		if ($this->category_filter->value())
		{
			$entries->with('Categories')
				->filter('Categories.cat_id', $this->category_filter->value());
		}

		if ($this->status_filter->value())
		{
			$entries->filter('status', $this->status_filter->value());
		}

		if ( ! empty($this->search_value))
		{
			$entries->filter('title', 'LIKE', '%' . $this->search_value . '%');
		}

		$filter_values = $this->filters->values();

		if ( ! empty($filter_values['filter_by_date']))
		{
			if (is_array($filter_values['filter_by_date']))
			{
				$entries->filter('entry_date', '>=', $filter_values['filter_by_date'][0]);
				$entries->filter('entry_date', '<', $filter_values['filter_by_date'][1]);
			}
			else
			{
				$entries->filter('entry_date', '>=', $this->now - $filter_values['filter_by_date']);
			}
		}

		$entries->with('Autosaves', 'Author', 'Channel');

		$this->entries = $entries;
	}

	/**
	 * Creates a channel fllter
	 */
	public function createChannelFilter()
	{
		$allowed_channel_ids = ($this->is_admin) ? NULL : $this->allowed_channels;
		$channels = ee('Model')->get('Channel', $allowed_channel_ids)
			->fields('channel_id', 'channel_title')
			->filter('site_id', ee()->config->item('site_id'))
			->order('channel_title', 'asc')
			->all();

		$channel_filter_options = array();
		foreach ($channels as $channel)
		{
			$channel_filter_options[$channel->channel_id] = $channel->channel_title;
		}
		$channel_filter = ee('CP/Filter')->make('filter_by_channel', 'filter_by_channel', $channel_filter_options);
		$channel_filter->disableCustomValue(); // This may have to go
		return $channel_filter;
	}

	/**
	 * Creates a category fllter
	 */
	private function createCategoryFilter($channel = NULL)
	{
		$cat_id = ($channel) ? explode('|', $channel->cat_group) : NULL;

		$category_groups = ee('Model')->get('CategoryGroup', $cat_id)
			->with('Categories')
			->filter('site_id', ee()->config->item('site_id'))
			->filter('exclude_group', '!=', 1)
			->all();

		$category_options = array();
		foreach ($category_groups as $group)
		{
			foreach ($group->Categories as $category)
			{
				$category_options[$category->cat_id] = $category->cat_name;
			}
		}

		$categories = ee('CP/Filter')->make('filter_by_category', 'filter_by_category', $category_options);
		$categories->disableCustomValue();
		return $categories;
	}

	/**
	 * Creates a category fllter
	 */
	private function createStatusFilter($channel = NULL)
	{
		$statuses = ee('Model')->get('Status')
			->filter('site_id', ee()->config->item('site_id'));

		if ($channel)
		{
			$statuses->filter('group_id', $channel->status_group);
		}

		$status_options = array();

		foreach ($statuses->all() as $status)
		{
			$status_name = ($status->status == 'closed' OR $status->status == 'open') ?  lang($status->status) : $status->status;
			$status_options[$status->status] = $status_name;
		}

		$status = ee('CP/Filter')->make('filter_by_status', 'filter_by_status', $status_options);
		$status->disableCustomValue();
		return $status;
	}
}
// EOF
