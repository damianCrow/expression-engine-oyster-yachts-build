<?php

namespace Solspace\Addons\User\Model;

class Cache extends BaseModel
{
	protected static $_primary_key	= 'cache_id';
	protected static $_table_name	= 'exp_user_cache';

	protected $cache_id;
	protected $type;
	protected $entry_date;
	protected $data;
}
//END Cache
