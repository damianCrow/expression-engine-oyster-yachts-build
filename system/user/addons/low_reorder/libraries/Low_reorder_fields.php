<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Low Search Fields class, for getting field info
 *
 * @package        low_search
 * @author         Lodewijk Schutte ~ Low <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-search
 * @copyright      Copyright (c) 2016, Low
 */
class Low_reorder_fields {

	/**
	 * Native string fields
	 */
	private $_native_strings = array(
		'title',
		'url_title',
		'status'
	);

	/**
	 * Native date fields
	 */
	private $_native_dates = array(
		'entry_date',
		'expiration_date',
		'comment_expiration_date',
		'recent_comment_date',
		'edit_date' // Which is in YYYYMMDDHHMMSS format. Obviously.
	);

	/**
	 * Native numeric fields
	 */
	private $_native_numeric = array(
		'view_count_one',
		'view_count_two',
		'view_count_thee',
		'view_count_four',
		'comment_total'
	);

	/**
	 * The cache
	 */
	protected $cache;

	// --------------------------------------------------------------------

	/**
	 * Get fields
	 *
	 * @access     public
	 * @param      string
	 * @return     array
	 */
	public function get($what = 'custom_channel_fields')
	{
		if ( ! $this->cache)
		{
			// Is local cache set?
			if ( ! ($fields = low_get_cache('channel', $what)))
			{
				// If not present, get them from the API
				// Takes some effort, but its reusable for others this way
				if (isset(ee()->TMPL))
				{
					ee()->TMPL->log_item('Low Reorder: Getting channel field info from API');
				}

				ee()->load->library('api');
				ee()->legacy_api->instantiate('channel_fields');

				$fields = ee()->api_channel_fields->fetch_custom_channel_fields();

				// Add it to EE's cache
				foreach ($fields as $key => $val)
				{
					low_set_cache('channel', $key, $val);
				}
			}

			// Create shortcut to the cache
			$this->cache =& ee()->session->cache['channel'];
		}

		// Return the cached fields
		return array_key_exists($what, $this->cache)
			? $this->cache[$what]
			: array();
	}

	// --------------------------------------------------------------------

	/**
	 * Get field id for given field short name
	 *
	 * @access      public
	 * @param       string
	 * @param       array
	 * @return      int
	 */
	public function id($str, $fields = NULL)
	{
		// --------------------------------------
		// Get custom channel fields from cache
		// --------------------------------------

		if ( ! is_array($fields))
		{
			$fields = $this->get();
		}

		// --------------------------------------
		// To be somewhat compatible with MSM, get the first ID that matches,
		// not just for current site, but all given.
		// --------------------------------------

		// Initiate ID
		$it = 0;

		// Check active site IDs, return first match encountered
		foreach ($this->site_ids() as $site_id)
		{
			if (isset($fields[$site_id][$str]))
			{
				$it = $fields[$site_id][$str];
				break;
			}
		}

		// Please
		return $it;
	}

	// --------------------------------------------------------------------

	/**
	 * Get database field name
	 *
	 * @access      public
	 * @param       string
	 * @param       string|null
	 * @param       string|null
	 * @return      string|bool
	 */
	public function name($str, $native_prefix = NULL, $custom_prefix = NULL)
	{
		if ($this->is_native($str))
		{
			return $native_prefix ? $native_prefix.'.'.$str : $str;
		}
		elseif ($id = $this->id($str))
		{
			$str = 'field_id_'.$id;
			return $custom_prefix ? $custom_prefix.'.'.$str : $str;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * For is_foo() methods
	 *
	 * @access     public
	 * @param      string
	 * @param      array
	 * @return     bool
	 */
	public function __call($fn, $args)
	{
		// Valid calls are is_foo
		if ( ! preg_match('/^is_([a-z_]+)$/', $fn, $match))
		{
			throw new Exception($fn.' is not a valid method in '.__CLASS__, 1);
		}

		// We need at least 1 argument
		if (empty($args))
		{
			throw new Exception('Too few arguments for '.$fn, 1);
		}

		// Get our vars to w0rk with
		$what  = $match[1];
		$field = $args[0];
		$it    = FALSE;

		// Is what, exactly?
		switch ($what)
		{
			// is_native: Native field?
			case 'native':
				$it = in_array($field, array_merge(
					$this->_native_strings,
					$this->_native_dates,
					$this->_native_numeric
				));
			break;

			// is_date: Native date field?
			case 'date':
				$it = (in_array($field, $this->_native_dates) || $this->id($field, $this->get('date_fields')));
			break;

			// is_grid: grid field?
			case 'grid':
				$it = (bool) $this->id($field, $this->get('grid_fields'));
			break;

			// is_rel: native Relationships field?
			case 'rel':
				$it = (bool) $this->id($field, $this->get('relationship_fields'));
			break;

			// is_foo, where foo is a 3rd party custom pair field like matrix of playa
			// Optional second argument for loose matching
			default:
				if ($id = $this->id($field))
				{
					if ($type = $this->id($id, $this->get('pair_custom_fields')))
					{
						$it = (isset($args[1]) && $args[1] === TRUE)
							? (strpos($type, $what) !== FALSE)
							: ($type == $what);
					}
				}
			break;
		}

		// Please
		return $it;
	}

	// --------------------------------------------------------------------

	/**
	 * Get column ID from given table, based on field ID and column name
	 *
	 * @access     private
	 * @param      int
	 * @param      string
	 * @param      string
	 * @return     mixed     [int|bool]
	 */
	private function _col_id($field_id, $col_name, $table = 'grid_columns')
	{
		$cols = low_get_cache(__CLASS__, $table);

		if ( ! isset($cols[$field_id]))
		{
			$query = ee()->db
				->select('col_id, col_name')
				->from($table)
				->where('field_id', $field_id)
				->get();

			foreach ($query->result() as $row)
			{
				$cols[$field_id][$row->col_id] = $row->col_name;
			}

			low_set_cache(__CLASS__, $table, $cols);
		}

		return array_search($col_name, (array) $cols[$field_id]);
	}

	/**
	 * Get grid column ID based on field ID and column name
	 *
	 * @access     public
	 * @param      int
	 * @param      string
	 * @return     mixed     [int|bool]
	 */
	public function grid_col_id($field_id, $col_name)
	{
		return $this->_col_id($field_id, $col_name);
	}

	/**
	 * Get matrix column ID based on field ID and column name
	 *
	 * @access     public
	 * @param      int
	 * @param      string
	 * @return     mixed     [int|bool]
	 */
	public function matrix_col_id($field_id, $col_name)
	{
		return $this->_col_id($field_id, $col_name, 'matrix_cols');
	}

	// --------------------------------------------------------------------

	/**
	 * Get WHERE clause for given field and value, based on search: field rules
	 *
	 * @access     public
	 * @param      string
	 * @param      string
	 * @return     string
	 */
	public function sql($field, $val)
	{
		// Initiate some vars
		$exact = $all = $starts = $ends = $exclude = FALSE;
		$sep = '|';

		// Exact matches
		if (substr($val, 0, 1) == '=')
		{
			$val   = substr($val, 1);
			$exact = TRUE;
		}

		// Starts with matches
		if (substr($val, 0, 1) == '^')
		{
			$val    = substr($val, 1);
			$starts = TRUE;
		}

		// Ends with matches
		if (substr($val, -1) == '$')
		{
			$val  = rtrim($val, '$');
			$ends = TRUE;
		}

		// All items? -> && instead of |
		if (strpos($val, '&&') !== FALSE)
		{
			$all = TRUE;
			$sep = '&&';
		}

		// Excluding?
		if (substr($val, 0, 4) == 'not ')
		{
			$val = substr($val, 4);
			$exclude = TRUE;
		}

		// Explode it
		$items = explode($sep, $val);

		// Init sql for where clause
		$sql = array();

		// SQL template thingie
		$tmpl = '(%s %s %s)';

		// Loop through each sub-item of the filter an create sub-clause
		foreach ($items AS $item)
		{
			// Left hand side of the sql
			$key = $field;

			// whole word? Regexp search
			if (substr($item, -2) == '\W')
			{
				$operand = $exclude ? 'NOT REGEXP' : 'REGEXP';
				$item = preg_quote(substr($item, 0, -2));
				$item = str_replace("'", "\'", $item);
				$item = "'[[:<:]]{$item}[[:>:]]'";
			}
			else
			{
				if (preg_match('/^([<>]=?)([\d\.]+)$/', $item, $match))
				{
					// Numeric operator!
					$operand = $match[1];
					$item    = $match[2];
				}
				elseif ($item == 'IS_EMPTY')
				{
					// IS_EMPTY should also account for NULL values as well as empty strings
					$key  = sprintf($tmpl, $field, ($exclude ? '!=' : '='), "''");
					$item = sprintf($tmpl, $field, ($exclude ? 'IS NOT' : 'IS'), 'NULL');
					$operand = $exclude ? 'AND' : 'OR';
				}
				elseif ($exact || ($starts && $ends))
				{
					// Use exact operand if empty or = was the first char in param
					$operand = $exclude ? '!=' : '=';
					$item = "'".ee()->db->escape_str($item)."'";
				}
				else
				{
					// Use like operand in all other cases
					$operand = $exclude ? 'NOT LIKE' : 'LIKE';
					$item = '%'.ee()->db->escape_like_str($item).'%';

					// Allow for starts/ends with matching
					if ($starts) $item = ltrim($item, '%');
					if ($ends)   $item = rtrim($item, '%');

					$item = "'{$item}'";
				}
			}

			$sql[] = sprintf($tmpl, $key, $operand, $item);
		}

		// Inclusive or exclusive
		$andor = $all ? ' AND ' : ' OR ';

		// Get complete clause, with parenthesis and everything
		$where = (count($sql) == 1) ? $sql[0] : '('.implode($andor, $sql).')';

		return $where;
	}

	// --------------------------------------------------------------------

	/**
	 * Get site IDs
	 *
	 * @access     public
	 * @return     array
	 */
	public function site_ids()
	{
		return (isset(ee()->TMPL))
			? array_values(ee()->TMPL->site_ids)
			: array(ee()->config->item('site_id'));
	}
}
// End of file Low_search_fields.php