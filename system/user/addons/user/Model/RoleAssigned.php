<?php

namespace Solspace\Addons\User\Model;

class RoleAssigned extends BaseModel
{
	protected static $_primary_key	= 'assigned_id';
	protected static $_table_name	= 'exp_user_roles_assigned';

	protected $assigned_id;
	protected $content_id;
	protected $role_id;
	protected $content_type;

	protected static $_relationships = array(
		'Role' => array(
			'model'		=> 'Role',
			'type'		=> 'BelongsTo',
			'from_key'	=> 'role_id',
			'to_key'	=> 'role_id'
		)
	);
}
//END RoleAssigned
