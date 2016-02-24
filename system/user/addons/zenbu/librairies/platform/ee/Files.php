<?php namespace Zenbu\librairies\platform\ee;

use Zenbu\librairies\platform\ee\Base as Base;

class Files extends Base
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	* Function _get_file_upload_prefs
	* @return	array upload preferences
	*/
	public function upload_preferences()
	{
		// Return data if already cached
		if($this->cache->get('file_upload_prefs'))
		{
			return $this->cache->get('file_upload_prefs');
		}

		// EE 2.4+ only
		ee()->load->model('file_upload_preferences_model');
		$result = ee()->file_upload_preferences_model->get_file_upload_preferences($this->user->group_id);
		$this->cache->set('file_upload_prefs', $result);

		return $result;
	} // END function _get_file_upload_prefs

	/**
	 * function display_filesize
	 *
	 * Make filesizes (in bytes) human-readable.
	 * @param  string $size The filesize (number in bytes)
	 * @return string The human-readable filesize
	 */
	public function display_filesize($size)
	{
	    $units = array(' B', ' KB', ' MB', ' GB', ' TB');

	    for ($i = 0; $size > 1000; $i++) 
	    { 
	    	$size /= 1000;
	    }

	    return round($size, 2).$units[$i];
	}

}