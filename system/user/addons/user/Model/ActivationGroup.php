<?php

namespace Solspace\Addons\User\Model;

class ActivationGroup extends BaseModel
{
	protected static $_primary_key	= 'member_id';
	protected static $_table_name	= 'exp_user_activation_group';

	protected $member_id;
	protected $group_id;
}
//END ActivationGroup
