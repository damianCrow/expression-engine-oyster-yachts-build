<?php

namespace Solspace\Addons\User\Model;

class MemberData extends BaseModel
{
	protected static $_primary_key	= 'member_id';
	protected static $_table_name	= 'exp_member_data';

	protected static $_relationships = array(
		'Member' => array(
			'model'		=> 'Member',
			'type'		=> 'HasOne',
			'from_key'	=> 'member_id',
			'to_key'	=> 'member_id',
			'weak'		=> FALSE
		)
	);

	// Properties
	protected $member_id;
}
//END MemberData
