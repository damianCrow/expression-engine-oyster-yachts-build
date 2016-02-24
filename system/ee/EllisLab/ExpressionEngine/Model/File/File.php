<?php

namespace EllisLab\ExpressionEngine\Model\File;

use EllisLab\ExpressionEngine\Service\Model\Model;

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
 * ExpressionEngine File Model
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
class File extends Model {

	protected static $_primary_key = 'file_id';
	protected static $_table_name = 'files';
	protected static $_events = array('beforeDelete');

	protected static $_relationships = array(
		'Site' => array(
			'type' => 'belongsTo'
		),
		'UploadDestination' => array(
			'type' => 'belongsTo',
			'to_key' => 'id',
			'from_key' => 'upload_location_id',
		),
		'UploadAuthor' => array(
			'type'     => 'BelongsTo',
			'model'    => 'Member',
			'from_key' => 'uploaded_by_member_id'
		),
		'ModifyAuthor' => array(
			'type'     => 'BelongsTo',
			'model'    => 'Member',
			'from_key' => 'modified_by_member_id'
		),
	);

	protected $file_id;
	protected $site_id;
	protected $title;
	protected $upload_location_id;
	protected $mime_type;
	protected $file_name;
	protected $file_size;
	protected $description;
	protected $credit;
	protected $location;
	protected $uploaded_by_member_id;
	protected $upload_date;
	protected $modified_by_member_id;
	protected $modified_date;
	protected $file_hw_original;

	/**
	 * Uses the file's mime-type to determine if the file is an image or not.
	 *
	 * @return bool TRUE if the file is an image, FALSE otherwise
	 */
	public function isImage()
	{
		return (strpos($this->mime_type, 'image/') === 0);
	}

	/**
	 * Uses the file's upload destination's server path to compute the absolute
	 * path of the file
	 *
	 * @return string The absolute path to the file
	 */
	public function getAbsolutePath()
	{
		return rtrim($this->UploadDestination->server_path, '/') . '/' . $this->file_name;
	}

	/**
	 * Uses the file's upload destination's server path to compute the absolute
	 * thumbnail path of the file
	 *
	 * @return string The absolute path to the file
	 */
	public function getAbsoluteThumbnailPath()
	{
		return rtrim($this->UploadDestination->server_path, '/') . '/_thumbs/' . $this->file_name;
	}

	/**
	 * Uses the file's upload destination's url to compute the absolute URL of
	 * the file
	 *
	 * @return string The absolute URL to the file
	 */
	public function getAbsoluteURL()
	{
		return rtrim($this->UploadDestination->url, '/') . '/' . rawurlencode($this->file_name);
	}

	/**
	 * Uses the file's upload destination's URL to compute the absolute thumbnail
	 *  URL of the file
	 *
	 * @return string The absolute thumbnail URL to the file
	 */
	public function getAbsoluteThumbnailURL()
	{
		if ( ! file_exists($this->getAbsoluteThumbnailPath()))
		{
			return $this->getAbsoluteURL();
		}

		return rtrim($this->UploadDestination->url, '/') . '/_thumbs/' . rawurlencode($this->file_name);
	}

	public function getThumbnailUrl()
	{
		return $this->getAbsoluteThumbnailURL();
	}

	public function onBeforeDelete()
	{
		if ($this->exists())
		{
			// Remove the file
			unlink($this->getAbsolutePath());

			// Remove the thumbnail if it exists
			if (file_exists($this->getAbsoluteThumbnailPath()))
			{
				unlink($this->getAbsoluteThumbnailPath());
			}

			// Remove any manipulated files as well
			foreach ($this->UploadDestination->FileDimensions as $file_dimension)
			{
				$file = rtrim($this->UploadDestination->server_path, '/') . '/_' . $file_dimension->short_name . '/' . $this->file_name;

				if (file_exists($file))
				{
					unlink($file);
				}
			}
		}
	}

	/**
	* Determines if the member group (by ID) has access permission to this
	* upload destination.
	* @see UploadDestination::memberGroupHasAccess
	*
	* @throws InvalidArgumentException
	* @param int|MemberGroup $group_id The Member Group ID
	* @return bool TRUE if access is granted; FALSE if access denied
	*/
	public function memberGroupHasAccess($group)
	{
		$dir = $this->UploadDestination;
		if ( ! $dir)
		{
			return FALSE;
		}

		return $dir->memberGroupHasAccess($group);
	}

	/**
	 * Determines if the file exists
	 *
	 * @return bool TRUE if it does FALSE otherwise
	 */
	public function exists()
	{
		return file_exists($this->getAbsolutePath());
	}

	/**
	 * Determines if the file is writable
	 *
	 * @return bool TRUE if it is FALSE otherwise
	 */
	public function isWritable()
	{
		return is_writable($this->getAbsolutePath());
	}

}
