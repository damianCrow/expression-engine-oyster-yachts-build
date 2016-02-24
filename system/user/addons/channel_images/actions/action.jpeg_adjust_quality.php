<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Images GREYSCALE action
 *
 * @package			DevDemon_ChannelImages
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/channel_images/
 */
class ImageAction_jpeg_adjust_quality extends ImageAction
{

	/**
	 * Action info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title' 	=>	'JPEG Adjust Quality',
		'name'		=>	'jpeg_adjust_quality',
		'version'	=>	'1.0',
		'enabled'	=>	TRUE,
	);

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// ********************************************************************************* //

	public function settings($settings)
	{
		$vData = $settings;

		if (isset($vData['quality']) == FALSE) $vData['quality'] = '85';

		return ee()->load->view('actions/jpeg_adjust_quality', $vData, TRUE);
	}

	// ********************************************************************************* //

	public function run($file)
	{
		$res = $this->open_image($file);
		if ($res != TRUE) return FALSE;

		$this->image_progressive = (isset($this->settings['field_settings']['progressive_jpeg']) === TRUE && $this->settings['field_settings']['progressive_jpeg'] == 'yes') ? TRUE : FALSE;

		if (self::$imageResource_ext == 'jpg' || self::$imageResource_ext == 'jpeg')
		{
			$this->image_jpeg_quality = $this->settings['quality'];
			$this->save_image($file);
		}

		return TRUE;
	}

	// ********************************************************************************* //


}

/* End of file action.jpeg_adjust_quality.php */
/* Location: ./system/expressionengine/third_party/channel_images/actions/action.jpeg_adjust_quality.php */
