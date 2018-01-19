<?php

namespace Solspace\Addons\User\Model;

class Author extends BaseModel
{
	protected static $_primary_key	= 'id';
	protected static $_table_name	= 'exp_user_authors';

	protected static $_relationships = array(
		'Member' => array(
			'model'		=> 'ee:Member',
			'type'		=> 'HasOne',
			'from_key'	=> 'author_id',
			'to_key'	=> 'member_id',
			'weak'		=> FALSE,
			'inverse'	=> array(
				'name'	=> 'Author',
				'type'	=> 'hasOne',
				'from_key'	=> 'member_id',
				'to_key'	=> 'author_id',
			)
		)
	);

	protected $id;
	protected $author_id;
	protected $entry_id;
	protected $principal;
	protected $order;
	protected $entry_date;
	protected $hash;
}
//END Author
