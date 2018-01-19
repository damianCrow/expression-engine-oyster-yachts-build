<?php

namespace Solspace\Addons\User\Library;
use Solspace\Addons\User\Library\AddonBuilder;

class Roles extends AddonBuilder
{
	/*
	public function has_permission()
	{
		return $this->lib('roles')->has_permission();
	}
	*/

	// --------------------------------------------------------------------

	/**
	 * Has role
	 *
	 * EE Template function for checking if a user has a given role(s)
	 *
	 * @access	public
	 * @return	string	parsed template booleans
	 */

	public function has_role()
	{
		$tmpl_roles			= ee()->TMPL->fetch_param('roles');
		$inclusive			= $this->check_yes(ee()->TMPL->fetch_param('match_all', 'yes'));

		//defaults
		$vars = array(
			'has_roles'		=> false,
			'not_has_roles'	=> true
		);

		if (! empty($tmpl_roles))
		{
			$member_role_ids = $this->get_roles_for_member();

			$role_ids = $this->get_roles_from_pipe_list(
				$tmpl_roles
			);

			if (! empty($member_role_ids) AND ! empty($role_ids))
			{
				$check = array_intersect($role_ids, $member_role_ids);

				if (($inclusive AND $check == $role_ids) OR (! $inclusive AND count($check) >= 1))
				{
					$vars = array(
						'has_roles'		=> true,
						'not_has_roles'	=> false
					);
				}
			}
		}

		//this is mainly going to be used for {if has_roles}{/if}
		//blocks but just in case lets run them as full vars
		return ee()->TMPL->parse_variables(
			ee()->TMPL->tagdata,
			array($vars)
		);
	}
	//END has_role


	// --------------------------------------------------------------------

	/**
	 * Has Permission
	 *
	 * @note	THIS IS NOT IMPLIMENTED YET
	 * @access	public
	 * @return	boolean	parsed template booleans
	 */

	public function has_permission()
	{
		$tmpl_permissions	= ee()->TMPL->fetch_param('permissions');
		$inclusive			= $this->check_yes(ee()->TMPL->fetch_param('match_all', 'yes'));

		//defaults
		$vars = array(
			'has_permissions'		=> false,
			'not_has_permissions'	=> true
		);

		if ( ! empty($tmpl_permissions))
		{
			$member_role_ids = $this->model('Role')->get_roles_for_member();

			$permission_ids = $this->get_roles_from_pipe_list(
				$tmpl_permissions
			);

			if ( ! empty($member_role_ids) && ! empty($role_ids))
			{
				$roles_permissions = $this->model('roles_permissions_assigned')
										->where_in('role_id', $member_role_ids)
										->key('permission_id')
										->get();

				$check = array_intersect(
					$permission_ids,
					array_keys($roles_permissions)
				);

				if ( ($inclusive && $check == $permission_ids)  ||
					( ! $inclusive && count($check) > 1))
				{
					$vars = array(
						'has_permissions'		=> true,
						'not_has_permissions'	=> false
					);
				}
			}
		}
		//END if ( ! empty($permissions))


		//this is mainly going to be used for {if has_roles}{/if}
		//blocks but just in case lets run them as full vars
		return ee()->TMPL->parse_variables(
			ee()->TMPL->tagdata,
			array($vars)
		);
	}
	//END has_permission


	// --------------------------------------------------------------------

	/**
	 * Restricted Roles
	 *
	 * @access	public
	 * @param	string	$restrict_to_toles	optional pipe delimited list of roles
	 * @return	string						template output
	 */

	public function roles_entries($restrict_to_roles = '')
	{
		//can't do crap without this
		//in theory we could let the incoming argument
		//do something but what we piggy back still requires TMPL
		if ( ! $this->lib('Utils')->tmpl_usable())
		{
			//can't use no results here because it doesn't
			//work without ee()->TMPL
			return FALSE;
		}

		// -------------------------------------
		//	Surely one of these....
		// -------------------------------------

		//restrict to certain roles
		$tmpl_restrict_to_roles			= ee()->TMPL->fetch_param('restrict_to_roles');
		//restrict to the roles available to the current logged in member
		$tmpl_restrict_to_user_roles	= $this->check_yes(ee()->TMPL->fetch_param('restrict_to_user_roles'));

		//Should we only show entries where the user has permission
		//to whats in restrict_to_roles AND matches all roles available?
		//More comments on this later in code.
		$tmpl_roles_restriction			= ee()->TMPL->fetch_param('roles_restriction', 'filter');

		//do we want all roles given to match?
		$tmpl_exact_matching			= $this->check_yes(ee()->TMPL->fetch_param('match_all'));

		//legacy
		if ($this->check_yes(ee()->TMPL->fetch_param('exact_matching')))
		{
			$tmpl_exact_matching = true;
		}

		$tmpl_entry_ids	= ee()->TMPL->fetch_param('entry_id');

		//incoming function argument
		if (! empty($restrict_to_roles))
		{
			$role_ids = $this->get_roles_from_pipe_list($restrict_to_roles);
		}
		else if (! empty($tmpl_restrict_to_roles))
		{
			$role_ids = $this->get_roles_from_pipe_list(
				$tmpl_restrict_to_roles
			);
		}
		else
		{
			$role_ids = array();
		}

		if (! empty($role_ids))
		{
			//we need this so we can do array merges/intersects later
			$role_ids = array_values($role_ids);

			//fix for some merging errors.
			foreach ($role_ids as $k => $v)
			{
				$role_ids[$k] = (int) $v;
			}
		}

		$inclusive_user_roles = false;

		//restrict entries to the roles of the current logged in user
		//this is separate so we can combine this with restricting to
		//a list of roles.
		if ($tmpl_restrict_to_user_roles)
		{
			//Restrict roles to inclusivity.
			//E.g. we have
			//	restrict_to_roles="new_york|kentucky"
			//	restrict_to_user_roles="yes"
			//	roles_restriction="filter"
			//
			//	Given that the user has roles of
			//		new_york, district_leader, cat_herder
			//	we would remove 'kentucky' from the incoming
			//	restrict_to_roles because the user doesn't have
			//	it, and only show entries with the rules of
			//		new_york
			//	or combos of
			//		new_york and district_leader
			//	or
			//		new_york and cat_hearder
			//	because we are using restrict_to_roles as the main entry
			//	pulling and futher filtering down to what the user has
			//	permission to.
			if ( ! empty($role_ids) &&
				$tmpl_roles_restriction == 'filter')
			{
				$member_role_ids = $this->get_roles_for_member();

				$member_role_ids = array_unique($member_role_ids);

				//fix for some merging errors.
				foreach ($member_role_ids as $k => $v)
				{
					$member_role_ids[$k] = (int) $v;
				}

				//reduce restrict_to_roles="" to what the member can see
				if ( ! empty($member_role_ids))
				{
					$role_ids = array_intersect($role_ids, $member_role_ids);
				}

				//if either are empty after this we cannot complete
				if (empty($role_ids) || empty($member_role_ids))
				{
					return $this->no_results();
				}

				//this will trigger our later code for matching
				//entries
				$inclusive_user_roles = $member_role_ids;
			}
			//just additive
			else
			{
				//returns the sets of role ids the member can access
				$role_ids = array_merge(
					//so merge works correctly
					//this has a list of role_name => id from get_roles_from_pipe_list
					$role_ids,
					$this->get_roles_for_member()
				);

				$role_ids = array_unique($role_ids);
			}
		}

		// -------------------------------------
		//	Role ids? If no role ids found
		// -------------------------------------

		if (empty($role_ids))
		{
			if (empty($tmpl_restrict_to_roles) AND empty($tmpl_restrict_to_user_roles))
			{
				$roles	= $this->fetch('RoleEntryPermission')
					->all()
					->getDictionary('entry_id', 'entry_id');

				if (empty($roles)) return FALSE;

				return $roles;
			}
			else
			{
				return FALSE;
			}
		}

		// -------------------------------------
		//	Search entry permissions on role id
		//	then we will sub filter after that
		//	in PHP
		// -------------------------------------
		/*
		SELECT *, CONCAT('|', group_concat(role_id ORDER BY role_id SEPARATOR '|'), '|') as role_group
		FROM exp_user_roles_entry_permissions
		group by set_id, entry_id
		HAVING role_group LIKE '%|4|%' OR
		 role_group LIKE '%|5|%'
		order by entry_id
		*/

		$model	= ee()->db->from('user_roles_entry_permissions');

		$model->select('entry_id')
			->select("CONCAT('|', GROUP_CONCAT(`role_id` ORDER BY `role_id` SEPARATOR '|'), '|') as role_group", false)
			->group_by('set_id, entry_id')
			->order_by('entry_id');

		// -------------------------------------
		//	entry_ids incoming?
		// -------------------------------------

		if ($tmpl_entry_ids !== false)
		{
			$id_array = preg_split(
				'/\|/s',
				$tmpl_entry_ids,
				-1,
				PREG_SPLIT_NO_EMPTY
			);

			$id_array = array_filter($id_array, array($this, 'is_positive_intlike'));

			if ( ! empty($id_array))
			{
				$model->where_in('entry_id', $id_array);
			}
		}

		// -------------------------------------
		//	search by role
		//	match any to start
		// -------------------------------------

		$first = true;

		sort($role_ids);

		foreach ($role_ids as $role)
		{
			$method = 'or_having';

			if ($first)
			{
				$first = false;
				$method = 'having';
			}

			$model->$method("role_group LIKE '%|" . $role . "|%'");
		}

		$entry_roles = $model->get();

		// -------------------------------------
		//	limit to group sets
		// -------------------------------------

		if ( ! $this->check_no(ee()->TMPL->fetch_param('match_all_roles')))
		{
			$matches = array();

			$match_set = $role_ids;

			// Are we filtering down to just what the member can see
			// from the restrict_to_roles set?
			// (See above for roles_restriction=filter/additive)
			if ($inclusive_user_roles !== false &&
				! empty($inclusive_user_roles))
			{
				$match_set = $inclusive_user_roles;
			}

			foreach ($entry_roles->result_array() as $row)
			{
				$entry_id	= (isset($row['entry_id'])) ? $row['entry_id']: '';
				if (empty($entry_id)) continue;

				//earlier we grouped by set id on the entry id
				//that way if we have muliple sets of 'and' rules
				//they are lumped together for us to match
				$roles_to_match = preg_split(
					'/\|/',
					$row['role_group'],
					-1,
					PREG_SPLIT_NO_EMPTY
				);

				sort($roles_to_match);

				//match exactly the set of incoming
				if ($tmpl_exact_matching)
				{
					$check = $match_set;
				}
				//allow any fitting matches to the set
				//this way if the entry has [1,2] as an
				//'and' set to match and the match_set has
				//[1,2,3,4,5] it will match.
				else
				{
					//if our interset result doesn't match
					//exactly, then we don't have all of the
					//roles required for this entry
					$check = array_intersect($roles_to_match, $match_set);
				}

				if ($check == $roles_to_match)
				{
					$matches[] = $entry_id;
				}
			}

			if (empty($matches))
			{
				return FALSE;
			}

			//its possible we matched the same
			//entry more than once given multiple roles.
			$matches = array_unique($matches);
		}
		else
		{
			$matches = array_keys($entry_roles);
		}
		//END if ( ! $this->check_no(ee()->TMPL->fetch_param('match_all_roles')))

		// -------------------------------------
		//	return entry ids
		// -------------------------------------

		//User is going to pass these directly to
		//the channel object and let it behave and filter
		//as normal otherwise. This will allow us to do
		//sorting and other setups with custom fields.
		return $matches;
	}
	//End roles_entries()


	// --------------------------------------------------------------------

	/**
	 * get_roles_for_member()
	 *
	 * @access	public
	 * @return	array
	 */

	public function get_roles_for_member()
	{
		// Appears that this prevented non-logged in
		// members from getting their roles filtered

		//if (! $this->lib('Utils')->session_useable())
		//{
		//	return array();
		//}

		// -------------------------------------
		//	CHECK AND SET SOME SORT OF CACHE HERE
		// -------------------------------------

		$member_id = ee()->session->userdata('member_id');
		$group_id = ee()->session->userdata('group_id');

		// -------------------------------------
		//	find assigned roles
		// -------------------------------------

		$assigned_roles = $this->fetch('RoleAssigned')
			->filter('content_id', $member_id)
			->filter('content_type', 'member')
			->orFilter('content_id', $group_id)
			->filter('content_type', 'group')
			->all()
			->indexBy('role_id');

		if (! $assigned_roles)
		{
			return array();
		}

		// -------------------------------------
		//	loop through inheritance if any
		// -------------------------------------

		//Going to just get all here because even if we get
		//up to 500 levels of inheritance, its better to make
		//a single call to the DB here and let PHP do a while
		//loop until we find every level of inheritance and
		//then make a single call for all of the permissions
		//that the user has access to.
		$inheritance = $this->fetch('RoleInherit')
			->all()
			->getDictionary('inheriting_role_id','from_role_id');

		$all_roles_assigned = array_keys($assigned_roles);

		// -------------------------------------
		//	loop through inherited roles
		// -------------------------------------

		if ($inheritance)
		{
			//start with first set
			$new_roles = $all_roles_assigned;

			//find roles inherited that aren't arleady in
			do {
				$inherited_roles = array();

				//Check new roles against inheritence.
				//if they exist, and aren't already there
				//add to new roles assigned and set to be
				//checked against inheritance in another loop.
				//E.g., role id 1 inherits from 2. 2 is added
				//to all roles and loop check. Next loop we find 2
				//inherits from 3. and so on until
				//all levels of inheritance are found.
				//This also prevents inheritance looping if 1
				//inherits from 2 and 2 inherits from 1.
				foreach ($new_roles as $role_id)
				{
					if (
						isset($inheritance[$role_id]) &&
						! in_array($inheritance[$role_id], $all_roles_assigned)
					)
					{
						//store for next loop
						$inherited_roles[] = $inheritance[$role_id];
						$all_roles_assigned[] = $inheritance[$role_id];
					}
				}

				$new_roles = $inherited_roles;
			}
			while( ! empty($new_roles));
		}
		//END if ($inheritance !== false)

		return $all_roles_assigned;



		// -------------------------------------
		//	not sure what we are doing with
		//	this permissions setup here ust yet
		// -------------------------------------

		// -------------------------------------
		//	find permissions for all roles assigned.
		// -------------------------------------

		$perm_as_name	= $this->model('roles_permissions_assigned')->get_table_name();
		$perm_name		= $this->model('roles_permissions')->get_table_name();

		$query = ee()->db
					->select('pa.permission_id, pn.permission_name')
					->from($perm_as_name . ' pa')
					->join(
						$perm_name . ' pn',
						'pa.permission_id = pn.permission_id',
						'left'
					)
					->where_in('pa.role_id', $all_roles_assigned)
					->get();

		$return = array();

		if ($query->num_rows() > 0)
		{
			$return = $this->prepare_keyed_result(
				$query,
				'permission_id',
				'permission_name'
			);
		}

		return $cache->set($return);
	}
	//End get_roles_for_member()


	// --------------------------------------------------------------------

	/**
	 * Get Roles From Pipe List
	 *
	 * @access	public
	 * @param	string	$pipe_list	pipe delimited list of role ids or names
	 * @return	mixed				result of found roles or false
	 */

	public function get_roles_from_pipe_list($pipe_list = '')
	{
		// -------------------------------------
		//	argument or fetch param?
		// -------------------------------------

		$restrict_to_roles = preg_split(
			'/\|/s',
			$pipe_list,
			-1,
			PREG_SPLIT_NO_EMPTY
		);

		// -------------------------------------
		//	prep types
		// -------------------------------------

		$role_ids	= array();

		$role_names	= array();

		foreach ($restrict_to_roles as $possibility)
		{
			$possibility = trim($possibility);

			if ($possibility == '')
			{
				continue;
			}

			if ($this->is_positive_intlike($possibility))
			{
				$role_ids[] = $possibility;
			}
			else
			{
				$role_names[] = $possibility;
			}
		}

		//how?????
		if (empty($role_ids) && empty($role_names))
		{
			return array();
		}

		// -------------------------------------
		//	find roles to restrict to
		// -------------------------------------

		$roles	= $this->fetch('Role');

		if ( ! empty($role_ids))
		{
			$roles->filter('role_id', 'IN', $role_ids);
		}

		if ( ! empty($role_names))
		{
			$method = ( ! empty($role_ids)) ? 'orFilter' : 'filter';

			$roles->$method('role_name', 'IN', $role_names);
		}

		$roles = $roles
			->all()
			->getDictionary('role_name', 'role_id');

		return $roles;
	}
	//END get_roles_from_pipe_list


	// --------------------------------------------------------------------

	/**
	 * Get permissions From Pipe List
	 *
	 * @access	public
	 * @param	string	$pipe_list	pipe delimited list of permissions ids or names
	 * @return	mixed				result of found permissions or false
	 */

	public function get_permissions_from_pipe_list($pipe_list = '')
	{
		// -------------------------------------
		//	argument or fetch param?
		// -------------------------------------

		$permissions = preg_split(
			'/\|/s',
			$pipe_list,
			-1,
			PREG_SPLIT_NO_EMPTY
		);

		// -------------------------------------
		//	prep types
		// -------------------------------------

		$role_ids	= array();

		$role_names	= array();

		foreach ($permissions as $possibility)
		{
			$possibility = trim($possibility);

			if ($possibility == '')
			{
				continue;
			}

			if ($this->is_positive_intlike($possibility))
			{
				$permission_ids[] = $possibility;
			}
			else
			{
				$permission_names[] = $possibility;
			}
		}

		//how?????
		if (empty($permission_ids) && empty($permission_names))
		{
			return array();
		}

		// -------------------------------------
		//	find roles to restrict to
		// -------------------------------------

		$this->model('roles_permissions')->key('permission_name', 'permission_id');

		if ( ! empty($permission_ids))
		{
			$this->model('roles_permissions')->where_in('permission_id', $permission_ids);
		}

		if ( ! empty($permission_names))
		{
			$method = ( ! empty($role_ids)) ? 'or_where_in' : 'where_in';

			$this->model('roles_permissions')->$method('permission_name', $permission_names);
		}

		$permissions = $this->model('roles_permissions')->get();

		return $permissions;
	}
	//END get_permissions_from_pipe_list


	// --------------------------------------------------------------------

	/**
	 * Delete Roles
	 *
	 * @access	public
	 * @param	Mixed	$role_ids	single role or array of roles
	 * @return	void
	 */

	public function delete_roles($role_ids)
	{
		if ( ! is_array($role_ids))
		{
			$role_ids = array($role_ids);
		}

		foreach ($role_ids as $role_id)
		{
			if ( ! $this->is_positive_intlike($role_id))
			{
				continue;
			}

			//	If our model is set up correctly,
			//	deleting a role will delete all of
			//	the other role related table rows
			//	across the roles architecture.
			$this->fetch('Role')
				->filter('role_id', $role_id)
				->delete();

			//@todo deal with inheritance
		}
	}
	//END delete_roles


	// --------------------------------------------------------------------

	/**
	 * Delete Permissions
	 *
	 * @access	public
	 * @param	Mixed	$permission_ids	single Permission or array of Permissions
	 * @return	void
	 */

	public function delete_permissions($permission_ids)
	{
		if ( ! is_array($permission_ids))
		{
			$permission_ids = array($permission_ids);
		}

		foreach ($permission_ids as $permission_id)
		{
			if ( ! $this->is_positive_intlike($permission_id))
			{
				continue;
			}

			//first because its a foreign key table
			$this->model('roles_permissions_assigned')
				->where('permission_id', $permission_id)
				->delete();

			$this->model('roles_permissions')
				->where('permission_id', $permission_id)
				->delete();
		}
	}
	//END delete_permissions


	// --------------------------------------------------------------------

	/**
	 * Delete Roles Assigned
	 *
	 * @access	public
	 * @param	Mixed	$assigned_ids	single Permission or array of Permissions
	 * @return	void
	 */

	public function delete_roles_assigned($assigned_ids)
	{
		if ( ! is_array($assigned_ids))
		{
			$assigned_ids = array($assigned_ids);
		}

		foreach ($assigned_ids as $assigned_id)
		{
			if ( ! $this->is_positive_intlike($assigned_id))
			{
				continue;
			}

			$this->fetch('RoleAssigned')
				->filter('assigned_id', $assigned_id)
				->delete();
		}
	}
	//END delete_roles_assigned
}
//END class User_roles
