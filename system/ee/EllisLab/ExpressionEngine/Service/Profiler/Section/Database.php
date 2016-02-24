<?php

namespace EllisLab\ExpressionEngine\Service\Profiler\Section;

use EllisLab\ExpressionEngine\Service\Profiler\ProfilerSection;
use EllisLab\ExpressionEngine\Service\View\View;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2015, EllisLab, Inc.
 * @license		https://ellislab.com/expressionengine/user-guide/license.html
 * @link		https://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Database Profiler Section
 *
 * @package		ExpressionEngine
 * @subpackage	Profiler\Section
 * @category	Service
 * @author		EllisLab Dev Team
 * @link		https://ellislab.com
 */
class Database extends ProfilerSection {

	/**
	 * @var int  total queries
	 **/
	protected $total_queries;

	/**
	 * @var float  threshold for warnings, in seconds
	 **/
	protected $time_threshold = 0.25;

	/**
	 * @var float  threshold for warnings, in bytes, default 1MB
	 **/
	protected $memory_threshold = 1048576;

	/**
	 * @var SQL Keywords we want highlighted
	 */
	protected $keywords = array(
		'SELECT',
		'DISTINCT',
		'FROM',
		'WHERE',
		'AND',
		'LEFT&nbsp;JOIN',
		'ORDER&nbsp;BY',
		'GROUP&nbsp;BY',
		'LIMIT',
		'INSERT',
		'INTO',
		'VALUES',
		'UPDATE',
		'OR&nbsp;',
		'HAVING',
		'OFFSET',
		'NOT&nbsp;IN',
		'IN',
		'LIKE',
		'NOT&nbsp;LIKE',
		'COUNT',
		'MAX',
		'MIN',
		'ON',
		'AS',
		'AVG',
		'SUM',
		'(',
		')'
	);

	/**
	 * Get a brief text summary (used for tabs, labels, etc.)
	 *
	 * @return  string  the section summary
	 **/
	public function getSummary()
	{
		return $this->total_queries.' '.lang('profiler_queries');
	}

	/**
	 * Set the section's data
	 *
	 * @param  array  Array of Database $db objects
	 * @return void
	 **/
	public function setData($dbs)
	{
		$count = 0;

		foreach ($dbs as $db)
		{
			$count++;
			$log = $db->getLog();
			$this->total_queries += $log->getQueryCount();

			$label = $db->getConfig()->get('database');
			$this->data['duplicate_queries'][$label] = $this->getDuplicateQueries($log);
			$this->data['database'][$label] = $this->getQueries($log);
		}
	}

	/**
	 * Gets the view name needed to render the section
	 *
	 * @return string  the view/name
	 **/
	public function getViewName()
	{
		return 'profiler/section/database';
	}

	/**
	 * Build the data set for duplicate queries
	 *
	 * @param object	$log	a DB Log object
	 * @return array	duplicates [count, query]
	 **/
	private function getDuplicateQueries($log)
	{
		$duplicate_queries = array_filter($log->getQueryMetrics(),
			function($value)
			{
				return ($value['count'] > 1);
			}
		);

		$duplicates = array();
		foreach ($duplicate_queries as $dupe_query)
		{
			$duplicates[] = array(
				'count' => $dupe_query['count'],
				'query' => $this->highlightSql($dupe_query['query']),
				'location' => implode(' ', $dupe_query['locations'])
			);
		}

		return $duplicates;
	}

	/**
	 * Build the data set for queries
	 *
	 * @param object	$log	a DB Log object
	 * @return array	queries [time, query]
	 **/
	private function getQueries($log)
	{
		if ($log->getQueryCount() == 0)
		{
			return lang('profiler_no_queries');
		}

		foreach ($log->getQueries() as $query)
		{
			list($sql, $location, $time, $memory) = $query;

			$data[] = array(
				'time' => number_format($time, 4),
				'memory' => $memory,
				'formatted_memory'=> $this->formatMemoryString($memory),
				'time_threshold' => $this->time_threshold,
				'memory_threshold' => $this->memory_threshold,
				'query' => $this->highlightSql($sql),
				'location' => $location
			);
		}

		return $data;
	}

	/**
	 * Format the memory to a sane byte format
	 *
	 * @param  string  $memory  the memory in bytes
	 * @return string  the formatted memory string
	 **/
	private function formatMemoryString($memory)
	{
		$precision = 0;

		if ($memory >= 1000000000)
		{
			$precision = 2;
			$memory = round($memory / 1073741824, $precision);
			$unit = lang('profiler_gigabytes');
		}
		elseif ($memory >= 1000000)
		{
			$precision = 1;
			$memory = round($memory / 1048576, $precision);
			$unit = lang('profiler_megabytes');
		}
		elseif ($memory >= 1000)
		{
			$memory = round($memory / 1024);
			$unit = lang('profiler_kilobytes');
		}
		else
		{
			$unit = lang('profiler_bytes');
		}

		return number_format($memory, $precision).' '.$unit;
	}

	/**
	 * Syntax highlight the SQL
	 *
	 * @param string	$sql	the query and location
	 * @return string	syntax highlighted query
	 **/
	private function highlightSql($sql)
	{
		// Load the text helper so we can highlight the SQL
		ee()->load->helper('text');
		$highlighted = highlight_code($sql, ENT_QUOTES, 'UTF-8');

		foreach ($this->keywords as $keyword)
		{
			$highlighted = str_replace($keyword, '<b>'.$keyword.'</b>', $highlighted);
		}

		// get rid of non-breaking spaces
		$highlighted = str_replace('&nbsp;', ' ', $highlighted);

		return $highlighted;
	}
}
