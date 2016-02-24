<?php namespace Zenbu\librairies\platform\ee;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Session;
use Zenbu\librairies\platform\ee\Lang;
use Zenbu\librairies\ArrayHelper;

class Categories extends Base
{

	public function __construct()
	{
		parent::__construct();
		parent::init('settings');
		$this->level = 0;
		$this->nested_categories = array();
		$this->category_groups = array();
	}

	/**
	 * Create an array of nested categories, per group, per channel
	 * @return [type] [description]
	 */
	public function getNestedCategories()
	{
		if($this->cache->get('nested_categories'))
		{
			return $this->cache->get('nested_categories');
		}

		$nested_categories = array();

		$category_groups_by_channel = $this->getCategoryGroupsByChannel();

		foreach($category_groups_by_channel as $channel_id => $cat_groups)
		{
			$categories_by_group = $this->getCategoriesInGroups($cat_groups);

			if($categories_by_group)
			{
				foreach($categories_by_group as $cat_group_id => $category_array)
				{
					$this->nested_categories = array();
					$nested_categories[$channel_id][$cat_group_id] = $this->nestCategories($category_array);
				}
			}
		}

		$this->cache->set('nested_categories', $nested_categories);
		$this->cache->set('category_groups', $this->category_groups);

		return $nested_categories;
	}

	/**
	 * Create an array where category_group_ids are names instead
	 * @param  array $nested_categories    Nested Category
	 * @param  array $category_group_names Simple category group ID/Name array
	 * @return array                       Converted array
	 */
	public function makeCategoryDropdown($nested_categories, $category_group_names)
	{
		$output = array();

		$output[0][''] = Lang::t('any_category');

		foreach($nested_categories as $channel_id => $cat_group_array)
		{
			$output[$channel_id][''] = Lang::t('any_category');
			foreach($cat_group_array as $cat_group_id => $cat_array)
			{
				if(isset($category_group_names[$cat_group_id]))
				{
					$output[$channel_id][$category_group_names[$cat_group_id]] = $cat_array;
					$output[0][$category_group_names[$cat_group_id]] = $cat_array;
				}
			}
		}

		return $output;
	}


	/**
	 * Get authors that have posted
	 * @return	array
	 */
	public function getCategoryGroupsByChannel()
	{
		$output = array();

		$sql = ee()->db->query("/* Zenbu category query */ SELECT c.* 
			FROM exp_channels c 
			WHERE c.site_id = ".$this->user->site_id);

		if($sql->num_rows() > 0)
		{
			foreach($sql->result_array() as $row)
			{
				$output[$row['channel_id']] = empty($row['cat_group']) ? array() : explode('|', $row['cat_group']);
			}
		}

		return $output;
	}


	public function getCategoriesInGroups($cat_groups = array())
	{
		if(empty($cat_groups) || ! is_array($cat_groups))
		{
			return FALSE;
		}

		$output = array();

		$sql = ee()->db->query("SELECT c.*, cg.* 
			FROM exp_categories c
			LEFT JOIN exp_category_groups cg ON cg.group_id = c.group_id
			WHERE c.group_id IN (".implode(',', $cat_groups).")
			AND c.site_id = ".$this->user->site_id."
			ORDER BY c.cat_order");

		if($sql->num_rows() > 0)
		{
			foreach($sql->result_array() as $row)
			{
				$output[$row['group_id']][] = $row;
				$this->category_groups[$row['group_id']] = $row['group_name'];
			}
		}

		return $output;
	}


	public function nestCategories($category_array)
	{
		foreach($category_array as $key => $cat_array)
		{
			if($cat_array['parent_id'] == 0)
			{
				$this->level = 0;
				$this->nested_categories[$cat_array['cat_id']] = $cat_array['cat_name'];

				$this->getSubCategories($cat_array['cat_id'], $category_array);
			}
		}

		return $this->nested_categories;
	}


	public function getSubCategories($parent_id, $categories)
	{
		// Find items with this parent
		$children = ArrayHelper::array_filter_by('parent_id', $parent_id, $categories);

		if($children)
		{
			$this->level++;
			foreach($children as $child)
			{
				$this->nested_categories[$child['cat_id']] = str_repeat(str_repeat(NBS, 6), $this->level) . '<i class="fa fa-level-up fa-rotate-90"></i> ' . $child['cat_name'];

				$this->getSubCategories($child['cat_id'], $categories);
			}
		}
	}

	public function getCategoryGroups()
	{
		if($this->cache->get('category_groups'))
		{
			return $this->cache->get('category_groups');
		}

		return $this->category_groups;
	}

	public function getCategoryEntries($entries)
	{
		$entry_ids = ArrayHelper::make_array_of('entry_id', $entries);

		if(empty($entry_ids))
		{
			return FALSE;
		}

		$output = array();

		$sql = ee()->db->query("SELECT t.*, cp.cat_id FROM exp_channel_titles t
			LEFT JOIN exp_category_posts cp ON cp.entry_id = t.entry_id
			WHERE cp.cat_id IS NOT NULL
			AND t.entry_id IN (".implode(',', $entry_ids).")");

		if($sql->num_rows() > 0)
		{
			foreach($sql->result_array() as $row)
			{
				$output[$row['cat_id']][$row['entry_id']] = $row;
			}

			return $output;
		}

		return FALSE;
	}

	public function getEntryCategories($entries)
	{
		$entry_ids = ArrayHelper::make_array_of('entry_id', $entries);

		if(empty($entry_ids))
		{
			return FALSE;
		}

		$output = array();

		$sql = ee()->db->query("SELECT t.entry_id, cp.cat_id, c.* FROM exp_channel_titles t
			LEFT JOIN exp_category_posts cp ON cp.entry_id = t.entry_id
			LEFT JOIN exp_categories c ON cp.cat_id = c.cat_id
			WHERE cp.cat_id IS NOT NULL
			AND t.entry_id IN (".implode(',', $entry_ids).")");

		if($sql->num_rows() > 0)
		{
			foreach($sql->result_array() as $row)
			{
				$output[$row['entry_id']][$row['cat_id']] = $row;
			}

			return $output;
		}

		return FALSE;
	}
}