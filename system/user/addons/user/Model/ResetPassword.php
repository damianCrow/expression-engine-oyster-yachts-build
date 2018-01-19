<?php

namespace Solspace\Addons\User\Model;

class ResetPassword extends BaseModel
{
	protected static $_primary_key = 'reset_id';
	protected static $_table_name = 'reset_password';

	protected $reset_id;
	protected $member_id;
	protected $resetcode;
	protected $date;
}
//END ResetPassword
