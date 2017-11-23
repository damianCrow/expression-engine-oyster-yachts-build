<?php

namespace Solspace\Addons\User\Model;

class Role extends BaseModel
{
	protected static $_primary_key	= 'role_id';
	protected static $_table_name	= 'exp_user_roles';

	protected $role_id;
	protected $role_label;
	protected $role_name;
	protected $role_description;

	protected static $_relationships = array(
		'RoleAssigned' => array(
			'model'		=> 'RoleAssigned',
			'type'		=> 'HasMany',
			'from_key'	=> 'role_id',
			'to_key'	=> 'role_id'
		),
		'RoleEntryPermission' => array(
			'model'		=> 'RoleEntryPermission',
			'type'		=> 'HasMany',
			'from_key'	=> 'role_id',
			'to_key'	=> 'role_id'
		)
	);

	protected $_default_prefs = array(
		'role_id'	=> array(
			'type'		=> 'hidden',
			'default'	=> ''
		),
		'role_label'	=> array(
			'type'		=> 'text',
			'default'	=> '',
			'validate'	=> 'required'
		),
		'role_name'	=> array(
			'type'		=> 'text',
			'default'	=> '',
			'validate'	=> 'required|unique_name'
		),
		'role_description'	=> array(
			'type'		=> 'textarea',
			'default'	=> '',
		),
	);

	// --------------------------------------------------------------------

	/**
	 * Validate Default Prefs
	 *
	 * Since we often use multiple prefs, lets validate default prefs
	 * and leave alone the ones not meant to be in the prefs page
	 *
	 * @access	public
	 * @param	array	$inputs		incoming inputs to validate
	 * @param	array	$required	array of names of required items
	 * @return	object				instance of validator result
	 */

	public function validateDefaultPrefs($inputs, $required = array())
	{
		//not a typo, see get__default_prefs
		$prefsData = $this->default_prefs;

		//validator
		$validator	= ee('Validation')
			->make();

		//unique name
		$validator->defineRule('unique_name', function($key, $value, $parameters) use ($inputs)
		{
			if ($key != 'role_name') return TRUE;

			$count	= ee('Model')
				->get('user:Role')
				->filter('role_name', $value);

			if (! empty($inputs['role_id']))
			{
				$count->filter('role_id', '!=', $inputs['role_id']);
			}

			$count	= $count->count();

			if (! empty($count))
			{
				return str_replace('%name%', $value, lang('role_name_exists'));
			}

			return TRUE;
		});

		$rules = array();

		foreach ($prefsData as $name => $data)
		{
			if (isset($data['validate']))
			{
				$r = (in_array($name, $required)) ? 'required|' : '';

				$rules[$name] = $r . $data['validate'];
			}
		}

		$validator->setRules($rules);

		return $validator->validate($inputs);
	}

	//END validateDefaultPrefs

	// --------------------------------------------------------------------

	/**
	 * Getter: default_prefs
	 *
	 * loads items with lang lines and choices before sending off.
	 * (Requires ee('Model')->make() to access.)
	 *
	 * @access	public
	 * @return	array		key->value array of pref names and defaults
	 */

	public function get__default_prefs()
	{
		//just in case this gets removed in the future.
		if (isset(ee()->lang) && method_exists(ee()->lang, 'loadfile'))
		{
			ee()->lang->loadfile('user');
		}

		$prefs = $this->_default_prefs;

		return $prefs;
	}

	//END get__default_prefs
}
//END Role
