<?php namespace Zenbu\librairies\platform\ee;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\ArrayHelper;

class Statuses extends Base
{
	public function __construct()
	{
		parent::__construct();
		$this->default_statuses = array(
				array(
					'status_id'    => 1,
					'status'       => 'open',
					'status_order' => 1,
					'highlight'    => '009933'
				),
				array(
					'status_id'    => 2,
					'status'       => 'closed',
					'status_order' => 2,
					'highlight'    => '990000'
				)
			);

	}

	/**
	 * Get authors that have posted
	 * @return	array
	 */
	function getStatuses()
	{
		// Return data if already cached
        if($this->cache->get('statuses'))
        {
            return $this->cache->get('statuses');
        }

		/* Do the query */
		$results = ee()->db->query("/* Zenbu _get_statuses */ \n SELECT s.* FROM exp_statuses s WHERE s.site_id = ".$this->user->site_id." GROUP BY s.status");

		if($results->num_rows() > 0)
		{
			$output = $results->result_array();
			$this->cache->set('statuses', $output);
			$results->free_result();
			return $output;
		} else {
			// If a channel doesn't have a status assigned yet,
			// or there are no statuses at all, show default
			// open/closed statuses
			$output[] = array(
				'id'        => 1,
				'status'    => 'open',
				'highlight' => '009933'
				);
			$output[] = array(
				'id'        => 2,
				'status'    => 'closed',
				'highlight' => '990000'
				);
			return $output;
		}

	}

	function getStatusByChannel()
	{
		// Return data if already cached
        if($this->cache->get('statuses_by_channel'))
        {
            return $this->cache->get('statuses_by_channel');
        }

		/* Do the query */
		$results = ee()->db->query("/* Zenbu _get_statuses */ \n SELECT c.channel_id, s.*
			FROM exp_channels c
			LEFT JOIN exp_statuses s ON c.status_group = s.group_id
			WHERE c.site_id = ".$this->user->site_id);

		if($results->num_rows() > 0)
		{
			foreach($results->result_array() as $row)
			{
				if(isset($output[$row['channel_id']]))
				{
					$output[$row['channel_id']][] = $row;
				}
				else
				{
					$output[$row['channel_id']] = is_null($row['status_id']) ? $this->default_statuses : array($row);
				}
			}
			// $output = $results->result_array();
			$this->cache->set('statuses_by_channel', $output);
			$results->free_result();
			return $output;
		}
	}

	function getStatusColors()
	{
		return ArrayHelper::flatten_to_key_val('status', 'highlight', $this->getStatuses());
	}

	function getStatusFilterOptions()
	{
		return ArrayHelper::flatten_to_key_val('status', 'status', $this->getStatuses());
	}
}
