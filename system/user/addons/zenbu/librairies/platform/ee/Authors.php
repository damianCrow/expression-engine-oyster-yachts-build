<?php namespace Zenbu\librairies\platform\ee;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\ArrayHelper;

class Authors extends Base
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get authors that have posted
	 * @return	array
	 */
	function getAuthors()
	{
		// Return data if already cached
        if($this->cache->get('authors'))
        {
            return $this->cache->get('authors');
        }

		$output = array();

		$results = ee()->db->query("/* Zenbu getAuthors */ \n SELECT m.*
			FROM exp_members m
			JOIN exp_channel_titles ct ON ct.author_id = m.member_id
			GROUP BY member_id");

		if($results->num_rows() > 0)
		{
			$output = $results->result_array();
			$this->cache->set('authors', $output);
			$results->free_result();
			return $output;
		}

		$this->cache->set('authors', $output);

		return $output;

	}

	function getAuthorSelectOptions()
	{
		return ArrayHelper::flatten_to_key_val('member_id', 'screen_name', $this->getAuthors());
	}
}