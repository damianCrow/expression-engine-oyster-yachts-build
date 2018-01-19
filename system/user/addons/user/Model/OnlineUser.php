<?php

namespace Solspace\Addons\User\Model;

class OnlineUser extends BaseModel
{
	protected static $_primary_key	= 'online_id';
	protected static $_table_name	= 'exp_online_users';

	protected $online_id;
	protected $site_id;
	protected $member_id;
	protected $in_forum;
	protected $name;
	protected $ip_address;
	protected $date;
	protected $anon;
}
//END OnlineUser
