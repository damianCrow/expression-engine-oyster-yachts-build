<?php namespace Zenbu\librairies\platform\ee;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\ArrayHelper;

class UsersBase extends Base
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get user groups
	 * @return	array
	 */
	public function getUserGroups()
	{
		// Return data if already cached
        if($this->cache->get('userGroups'))
        {
            return $this->cache->get('userGroups');
        }

		$output = array();

		$sql_str = $this->user->group_id != 1 ? '0, 1' : '0';

		$results = ee()->db->query("SELECT *, group_title as name, group_id as id
					FROM exp_member_groups 
					WHERE group_id NOT IN(".$sql_str.") 
					AND site_id = ".$this->user->site_id);

		if($results->num_rows() > 0)
		{
			$output = $results->result_array();
			$this->cache->set('userGroups', $output);
			$results->free_result();
			return $output;
		}
		return $output;

	}

	public function getUserGroupSelectOptions()
	{
		return ArrayHelper::flatten_to_key_val('group_id', 'group_title', $this->getUserGroups());
	}
}