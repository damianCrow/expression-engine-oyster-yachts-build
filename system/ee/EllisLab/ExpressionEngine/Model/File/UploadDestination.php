<?php

namespace EllisLab\ExpressionEngine\Model\File;

use EllisLab\ExpressionEngine\Service\Model\Model;
use EllisLab\ExpressionEngine\Model\Member\MemberGroup;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		https://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine File Upload Location Model
 *
 * A model representing one of many possible upload destintations to which
 * files may be uploaded through the file manager or from the publish page.
 * Contains settings for this upload destination which describe what type of
 * files may be uploaded to it, as well as essential information, such as the
 * server paths where those files actually end up.
 *
 * @package		ExpressionEngine
 * @subpackage	File
 * @category	Model
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class UploadDestination extends Model {

	protected static $_primary_key = 'id';
	protected static $_table_name = 'upload_prefs';

	protected static $_relationships = array(
		'Site' => array(
			'type' => 'belongsTo'
		),
		'NoAccess' => array(
			'type' => 'hasAndBelongsToMany',
			'model' => 'MemberGroup',
			'pivot' => array(
				'table' => 'upload_no_access',
				'left' => 'upload_id',
				'right' => 'member_group'
			)
		),
		'Module' => array(
			'type' => 'belongsTo',
			'model' => 'Module',
			'to_key' => 'module_id'
		),
		'Files' => array(
			'type' => 'hasMany',
			'model' => 'File',
			'to_key' => 'upload_location_id'
		),
		'FileDimensions' => array(
			'type' => 'hasMany',
			'model' => 'FileDimension',
			'to_key' => 'upload_location_id'
		)
	);

	protected static $_type_classes = array(
		'LocalPath' => 'EllisLab\ExpressionEngine\Model\File\Column\LocalPath',
	);

	protected static $_typed_columns = array(
		'server_path' => 'LocalPath'
	);

	protected static $_validation_rules = array(
		'name'               => 'required|xss|noHtml|unique[site_id]',
		'server_path'        => 'required|fileExists|writable',
		'url'                => 'required|validateUrl',
		'allowed_types'      => 'enum[img,all]',
		'default_modal_view' => 'enum[list,thumb]',
		'max_size'           => 'numeric|greaterThan[0]',
		'max_height'         => 'isNatural',
		'max_width'          => 'isNatural'
	);

	protected $_property_overrides = array();

	protected $id;
	protected $site_id;
	protected $name;
	protected $server_path;
	protected $url;
	protected $allowed_types;
	protected $default_modal_view;
	protected $max_size;
	protected $max_height;
	protected $max_width;
	protected $properties;
	protected $pre_format;
	protected $post_format;
	protected $file_properties;
	protected $file_pre_format;
	protected $file_post_format;
	protected $cat_group;
	protected $batch_location;
	protected $module_id;

	/**
	 * Because of the 'upload_preferences' Config value, the data in the DB
	 * is not always authoritative. So we will need to get any override data
	 * from the Config object
	 *
	 * @see Entity::_construct()
	 * @param array $data An associative array of property data
	 * @return void
	 */
	public function __construct(array $data = array())
	{
		parent::__construct($data);

		// @TODO THOU SHALT INJECT ALL THY DEPENDENCIES
		if (ee()->config->item('upload_preferences') !== FALSE)
		{
			$this->_property_overrides = ee()->config->item('upload_preferences');
		}
	}

	/**
	 * Returns the propety value using the overrides if present
	 *
	 * @param str $name The name of the property to fetch
	 * @return mixed The value of the property
	 */
	public function __get($name)
	{
		$value = parent::__get($name);

		if ($this->hasOverride($name))
		{
			$value = $this->_property_overrides[$this->id][$name];
		}

		return $value;
	}

	/**
	 * Returns the propety value using the overrides if present
	 *
	 * @param str $name The name of the property to fetch
	 * @return mixed The value of the property
	 */
	public function getProperty($name)
	{
		$value = parent::getProperty($name);

		if ($this->hasOverride($name))
		{
			$value = $this->_property_overrides[$this->id][$name];
		}

		return $value;
	}

	/**
	 * Check if have an override for this directory and that it's an
	 * array (as it should be)

	 * @param str $name The name of the property to check
	 * @return bool Property is overridden?
	 */
	private function hasOverride($name)
	{
		return (isset($this->_property_overrides[$this->id])
			&& is_array($this->_property_overrides[$this->id])
			&& array_key_exists($name, $this->_property_overrides[$this->id]));
	}

	/**
	 * Custom setter for server path to ensure it's saved with a trailing slash
	 *
	 * @param str $value Value to set on property
	 * @return void
	 */
	protected function set__server_path($value)
	{
		$this->setRawProperty('server_path', $this->getWithTrailingSlash($value));
	}

	/**
	 * Custom setter for URL to ensure it's saved with a trailing slash
	 *
	 * @param str $value Value to set on property
	 * @return void
	 */
	protected function set__url($value)
	{
		$this->setRawProperty('url', $this->getWithTrailingSlash($value));
	}

	/**
	 * Appends a trailing slash on to a value that doesn't have it
	 *
	 * @param str $path Path string to ensure has a trailing slash
	 * @return void
	 */
	private function getWithTrailingSlash($path)
	{
		if ( ! empty($path) && substr($path, -1) != '/' AND substr($path, -1) != '\\')
		{
			$path .= '/';
		}

		return $path;
	}

	/**
	 * Make sure URL is not submitted with the default value
	 */
	public function validateUrl($key, $value, $params, $rule)
	{
		if ($value == 'http://')
		{
			$rule->stop();
			return lang('valid_url');
		}

		return TRUE;
	}

	/**
	 * Get the backing filesystem for this upload destination
	 */
	public function getFilesystem()
	{
		$fs = ee('File')->getPath($this->getProperty('server_path'));
		$fs->setUrl($this->getRawProperty('url'));

		return $fs;
	}

	/**
	 * Determines if the member group (by ID) has access permission to this
	 * upload destination.
	 *
	 * @throws InvalidArgumentException
	 * @param int|MemberGroup $group_id The Meber Group ID
	 * @return bool TRUE if access is granted; FALSE if access denied
	 */
	public function memberGroupHasAccess($group)
	{
		if ($group instanceOf MemberGroup)
		{
			$group_id = $group->group_id;
		}
		elseif(is_numeric($group))
		{
			$group_id = (int) $group;
		}
		else
		{
			throw new \InvalidArgumentException('memberGroupHasAccess expects an number or an instance of MemberGroup.');
		}

		// 2 = Banned
		// 3 = Guests
		// 4 = Pending
		$hardcoded_disallowed_groups = array('2', '3', '4');

		// If the user is a Super Admin, return true
		if ($group_id == 1)
		{
			return TRUE;
		}

		if (in_array($group_id, $hardcoded_disallowed_groups))
		{
			return FALSE;
		}

		if (in_array($group_id, $this->getNoAccess()->pluck('group_id')))
		{
			return FALSE;
		}

		return TRUE;

	}

	/**
	 * Determines if the directory exists
	 *
	 * @return bool TRUE if it does FALSE otherwise
	 */
	public function exists()
	{
		return file_exists($this->getProperty('server_path'));
	}

	/**
	 * Determines if the directory is writable
	 *
	 * @return bool TRUE if it is FALSE otherwise
	 */
	public function isWritable()
	{
		return is_writable($this->getProperty('server_path'));
	}

}
