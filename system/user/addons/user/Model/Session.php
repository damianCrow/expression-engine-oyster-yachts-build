<?php

namespace Solspace\Addons\User\Model;

class Session extends BaseModel
{
	protected static $_primary_key = 'session_id';
	protected static $_table_name = 'sessions';

	protected $session_id;
	protected $member_id;
	protected $admin_sess;
	protected $ip_address;
	protected $user_agent;
	protected $fingerprint;
	protected $sess_start;
	protected $last_activity;

}
//END Session
