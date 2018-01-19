<?php

namespace Solspace\Addons\User\Model;

class MemberChannelEntry extends BaseModel
{
	protected static $_primary_key	= 'id';
	protected static $_table_name	= 'exp_user_member_channel_entries';

	protected $id;
	protected $member_id;
	protected $entry_id;
	protected $site_id;

	protected static $_relationships = array(
		'Member' => array(
			'model'		=> 'ee:Member',
			'type'		=> 'HasOne',
			'from_key'	=> 'member_id',
			'to_key'	=> 'member_id',
			'weak'		=> TRUE,
			'inverse'	=> array(
				'name'	=> 'MemberChannelEntry',
				'type'	=> 'hasOne',
				'from_key'	=> 'member_id',
				'to_key'	=> 'member_id',
			)
		)
	);
}
//END RoleAssigned
