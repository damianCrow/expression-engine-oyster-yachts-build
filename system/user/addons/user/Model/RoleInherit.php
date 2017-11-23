<?php

namespace Solspace\Addons\User\Model;

class RoleInherit extends BaseModel
{
	protected static $_primary_key	= 'inherits_id';
	protected static $_table_name	= 'exp_user_roles_inherits';

	protected $inherits_id;
	protected $inheriting_role_id;
	protected $from_role_id;
}
//END RoleInherit
