<?php

return array(
	'author'			=> 'Solspace',
	'author_url'		=> 'https://solspace.com/expressionengine',
	'docs_url'			=> 'https://solspace.com/expressionengine/user/docs',
	'name'				=> 'User',
	'description'		=> 'Create powerful and flexible member management areas using regular EE templates.',
	'version'			=> '4.0.6',
	'namespace'			=> 'Solspace\Addons\User',
	'settings_exist'	=> true,
	'models' => array(
		'Author'					=> 'Model\Author',
		'ActivationGroup'			=> 'Model\ActivationGroup',
		'Cache'						=> 'Model\Cache',
		'CategoryPosts'				=> 'Model\CategoryPosts',
		'Key'						=> 'Model\Key',
		'Member'					=> 'Model\Member',
		'MemberData'				=> 'Model\MemberData',
		'MemberChannelEntry'		=> 'Model\MemberChannelEntry',
		'OnlineUser'				=> 'Model\OnlineUser',
		'Param'						=> 'Model\Param',
		'Preference'				=> 'Model\Preference',
		'ResetPassword'				=> 'Model\ResetPassword',
		'Role'						=> 'Model\Role',
		'RoleAssigned'				=> 'Model\RoleAssigned',
		'RoleEntryPermission'		=> 'Model\RoleEntryPermission',
		'RoleInherit'				=> 'Model\RoleInherit',
		'RolePermission'			=> 'Model\RolePermission',
		'Search'					=> 'Model\Search',
		'Session'					=> 'Model\Session',
		'WelcomeEmailList'			=> 'Model\WelcomeEmailList'
	),
	'models.dependencies' => array(
		'Author'   => array(
			'ee:Member'
		),
		'Key'   => array(
			'ee:MemberGroup'
		),
		'WelcomeEmailList'   => array(
			'ee:Member'
		),
		'MemberChannelEntry'   => array(
			'ee:Member'
		)
	),
);
