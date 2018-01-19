<?php

namespace Solspace\Addons\User\Model;

class RolePermission extends BaseModel
{
	protected static $_primary_key	= 'permission_id';
	protected static $_table_name	= 'exp_user_roles_permissions';

	protected $permission_id;
	protected $permission_label;
	protected $permission_name;
	protected $permission_description;
}
//END RolePermission
