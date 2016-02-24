<?php namespace Zenbu\librairies\platform\ee;

class Db
{
	public function __construct()
	{

	}

	public static function rawQuery($sql)
	{
		$sql = preg_replace("/zenbu\_([a-z\_]*)/i", ee()->db->dbprefix.'zenbu_$1', $sql);
		return ee()->db->query($sql, TRUE)->result_array();
	}

	public function delete($table, $where = '', $replacements = array())
	{
		if(empty($table) || empty($where) || empty($replacements) )
		{
			return FALSE;
		}

		foreach($replacements as $replace)
		{
			$where = preg_replace('/\?/', ee()->db->escape_str($replace), $where, 1);
		}

		ee()->db->delete($table, $where);
		
		return TRUE;
	}

	public function update($table, $data, $where = '', $replacements = array())
	{
		if(empty($table) || empty($where) || empty($replacements) || empty($data))
		{
			return FALSE;
		}

		foreach($replacements as $replace)
		{
			$where = preg_replace('/\?/', ee()->db->escape_str($replace), $where, 1);
		}

		ee()->db->update($table, (array) $data, $where);
		
		return TRUE;
	}

	public function insert($table, $data)
	{
		ee()->db->insert($table, (array) $data);
		return ee()->db->insert_id();
	}

	public function find($table, $where = '', $replacements = array(), $order = FALSE)
	{
		if(empty($table) || empty($where) || empty($replacements) )
		{
			return FALSE;
		}

		foreach($replacements as $replace)
		{
			$where = preg_replace('/\?/', ee()->db->escape_str($replace), $where, 1);
		}

		ee()->db->from($table);
		ee()->db->where($where);
		if($order !== FALSE && is_array($order))
		{
			ee()->db->order_by($order[0], $order[1]);
		}
		$sql = ee()->db->get();

		if($sql->num_rows() > 0)
		{
			return $sql->result();
		}
		else
		{
			return FALSE;
		}
		
		return FALSE;
	}

	/**
	 * ======================
	 * function get_action_id
	 * ======================
	 * Checks for the presence of a specific rule in Zenbu
	 * @param  string 	$type  The type of rule element. Usually 'field', 'cond' or 'val'
	 * @param  string 	$value The value of the rule element
	 * @param  array 	$rules The passed Zenbu rules
	 * @return bool
	 */
	function get_action_id($class, $method)
	{
		
		// Return data if already cached
		if(ee()->session->cache('zenbu', 'get_action_id_'.$method))
		{
			return ee()->session->cache('zenbu', 'get_action_id_'.$method);
		}

		$action_id = "";
		ee()->db->from("actions");
		ee()->db->where("actions.class", $class);
		ee()->db->where("actions.method", $method);
		$action_id_query = ee()->db->get();
		
		if($action_id_query->num_rows() > 0)
		{
			foreach($action_id_query->result_array() as $row)
			{
				$action_id = $row['action_id'];
			}
		}

		ee()->session->set_cache('zenbu', 'get_action_id_'.$method, $action_id);

		return $action_id;
	}
}