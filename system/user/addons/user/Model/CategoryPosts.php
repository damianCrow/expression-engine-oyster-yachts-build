<?php

namespace Solspace\Addons\User\Model;

class CategoryPosts extends BaseModel
{
	protected static $_primary_key	= 'member_id';
	protected static $_table_name	= 'exp_user_category_posts';

	protected $member_id;
	protected $cat_id;
}
//END CategoryPosts
