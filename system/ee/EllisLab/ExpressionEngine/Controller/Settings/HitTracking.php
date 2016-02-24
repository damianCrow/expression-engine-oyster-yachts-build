<?php

namespace EllisLab\ExpressionEngine\Controller\Settings;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use CP_Controller;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2015, EllisLab, Inc.
 * @license		https://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine CP Hit Tracking Settings Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class HitTracking extends Settings {

	/**
	 * General Settings
	 */
	public function index()
	{
		$vars['sections'] = array(
			array(
				array(
					'title' => 'enable_online_user_tracking',
					'desc' => 'enable_online_user_tracking_desc',
					'fields' => array(
						'enable_online_user_tracking' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'enable_hit_tracking',
					'desc' => 'enable_hit_tracking_desc',
					'fields' => array(
						'enable_hit_tracking' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'enable_entry_view_tracking',
					'desc' => 'enable_entry_view_tracking_desc',
					'fields' => array(
						'enable_entry_view_tracking' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'dynamic_tracking_disabling',
					'desc' => sprintf(
						lang('dynamic_tracking_disabling_desc'),
						'https://ellislab.com/expressionengine/user-guide/cp/admin/tracking_preferences.html#suspend-tracking-label'
					),
					'fields' => array(
						'dynamic_tracking_disabling' => array('type' => 'text')
					)
				),
			)
		);

		$base_url = ee('CP/URL')->make('settings/hit_tracking');

		ee()->form_validation->set_rules(array(
			array(
				'field' => 'dynamic_tracking_disabling',
				'label' => 'lang:dynamic_tracking_disabling',
				'rules' => 'is_numeric'
			)
		));

		ee()->form_validation->validateNonTextInputs($vars['sections']);

		if (AJAX_REQUEST)
		{
			ee()->form_validation->run_ajax();
			exit;
		}
		elseif (ee()->form_validation->run() !== FALSE)
		{
			if ($this->saveSettings($vars['sections']))
			{
				ee()->view->set_message('success', lang('preferences_updated'), lang('preferences_updated_desc'), TRUE);
			}

			ee()->functions->redirect($base_url);
		}
		elseif (ee()->form_validation->errors_exist())
		{
			ee()->view->set_message('issue', lang('settings_save_error'), lang('settings_save_error_desc'));
		}

		ee()->view->base_url = $base_url;
		ee()->view->ajax_validate = TRUE;
		ee()->view->cp_page_title = lang('hit_tracking');
		ee()->view->save_btn_text = 'btn_save_settings';
		ee()->view->save_btn_text_working = 'btn_saving';

		ee()->cp->render('settings/form', $vars);
	}
}
// END CLASS

/* End of file HitTracking.php */
/* Location: ./system/EllisLab/ExpressionEngine/Controller/Settings/HitTracking.php */
