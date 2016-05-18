<?php

namespace Solspace\Addons\User\Model;

class Search extends BaseModel
{
	protected static $_primary_key	= 'search_id';
	protected static $_table_name	= 'exp_user_search';

	protected $search_id;
	protected $member_id;
	protected $site_id;
	protected $ip_address;
	protected $search_date;
	protected $total_results;
	protected $keywords;
	protected $categories;
	protected $member_ids;
	protected $fields;
	protected $cfields;
	protected $query;
}
//END Search
