<?php

namespace Solspace\Addons\User\Model;

class Key extends BaseModel
{
	protected static $_primary_key	= 'key_id';
	protected static $_table_name	= 'exp_user_keys';

	protected $key_id;
	protected $author_id;
	protected $member_id;
	protected $group_id;
	protected $date;
	protected $email;
	protected $hash;

	protected static $_relationships = array(
		'MemberGroup' => array(
			'model'		=> 'ee:MemberGroup',
			'type'		=> 'HasOne',
			'from_key'	=> 'group_id',
			'to_key'	=> 'group_id',
			'weak'		=> FALSE,
			'inverse'	=> array(
				'name'	=> 'Key',
				'type'	=> 'hasMany',
				'from_key'	=> 'group_id',
				'to_key'	=> 'group_id',
			)
		)
	);
}
//END Key
