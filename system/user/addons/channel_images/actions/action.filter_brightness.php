<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Images CE IMAGE BRIGHTNESS action
 *
 * @package			DevDemon_ChannelImages
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/channel_images/
 */
class ImageAction_filter_brightness extends ImageAction
{

	/**
	 * Action info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title' 	=>	'Filter: Brightness',
		'name'		=>	'filter_brightness',
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

	public function run($file, $temp_dir)
	{

		$res = $this->open_image($file);

		if ($res != TRUE) return FALSE;

		@imagefilter(self::$imageResource, IMG_FILTER_BRIGHTNESS, $this->settings['brightness']);

		$this->save_image($file);

		return TRUE;
	}

	// ********************************************************************************* //

	public function settings($settings)
	{
		$vData = $settings;

		if (isset($vData['brightness']) == FALSE) $vData['brightness'] = '10';

		return ee()->load->view('actions/ce_image_brightness', $vData, TRUE);
	}

	// ********************************************************************************* //

}

/* End of file action.filter_brightness.php */
/* Location: ./system/expressionengine/third_party/channel_images/actions/action.filter_brightness.php */
