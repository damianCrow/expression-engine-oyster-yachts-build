<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Images CE IMAGE SMOOTH action
 *
 * @package			DevDemon_ChannelImages
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/channel_images/
 */
class ImageAction_filter_smooth extends ImageAction
{

	/**
	 * Action info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title' 	=>	'Filter: Smooth',
		'name'		=>	'filter_smooth',
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

		@imagefilter(self::$imageResource, IMG_FILTER_SMOOTH, $this->settings['smooth']);

		$this->save_image($file);

		return TRUE;
	}

	// ********************************************************************************* //

	public function settings($settings)
	{
		$vData = $settings;

		if (isset($vData['smooth']) == FALSE) $vData['smooth'] = '1';

		return ee()->load->view('actions/ce_image_smooth', $vData, TRUE);
	}

	// ********************************************************************************* //

}

/* End of file action.filter_smooth.php */
/* Location: ./system/expressionengine/third_party/channel_images/actions/action.filter_smooth.php */
