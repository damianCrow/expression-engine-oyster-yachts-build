<?php

namespace Solspace\Addons\User\Model;

class WelcomeEmailList extends BaseModel
{
	protected static $_primary_key	= 'member_id';
	protected static $_table_name	= 'exp_user_welcome_email_list';

	protected $member_id;
	protected $group_id;
	protected $email_sent;

	protected static $_relationships = array(
		'Member' => array(
			'model'		=> 'ee:Member',
			'type'		=> 'HasOne',
			'from_key'	=> 'member_id',
			'to_key'	=> 'member_id',
			'weak'		=> FALSE,
			'inverse'	=> array(
				'name'	=> 'Key',
				'type'	=> 'HasOne',
				'from_key'	=> 'member_id',
				'to_key'	=> 'member_id',
			)
		)
	);
}
//END WelcomeEmailList
