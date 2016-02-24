<?php

namespace EllisLab\ExpressionEngine\Controller\Settings;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use CP_Controller;

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
 * ExpressionEngine CP Security & Privacy Settings Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class SecurityPrivacy extends Settings {

	public function __construct()
	{
		parent::__construct();

		if ( ! ee()->cp->allowed_group('can_access_security_settings'))
		{
			show_error(lang('unauthorized_access'));
		}
	}

	public function index()
	{
		$vars['sections'] = array(
			array(
				array(
					'title' => 'cp_session_type',
					'desc' => '',
					'fields' => array(
						'cp_session_type' => array(
							'type' => 'select',
							'choices' => array(
								'cs' => lang('cs_session'),
								'c' => lang('c_session'),
								's' => lang('s_session')
							)
						)
					)
				),
				array(
					'title' => 'website_session_type',
					'desc' => '',
					'fields' => array(
						'website_session_type' => array(
							'type' => 'select',
							'choices' => array(
								'cs' => lang('cs_session'),
								'c' => lang('c_session'),
								's' => lang('s_session')
							)
						)
					)
				)
			),
			'cookie_settings' => array(
				array(
					'title' => 'cookie_domain',
					'desc' => 'cookie_domain_desc',
					'fields' => array(
						'cookie_domain' => array('type' => 'text')
					)
				),
				array(
					'title' => 'cookie_path',
					'desc' => sprintf(lang('cookie_path_desc'), ee()->cp->masked_url('https://docs.expressionengine.com/v3/cp/settings/security-privacy.html#path')),
					'fields' => array(
						'cookie_path' => array('type' => 'text')
					)
				),
				array(
					'title' => 'cookie_prefix',
					'desc' => lang('cookie_prefix_desc'),
					'fields' => array(
						'cookie_prefix' => array('type' => 'text')
					)
				),
				array(
					'title' => 'cookie_httponly',
					'desc' => 'cookie_httponly_desc',
					'security' => TRUE,
					'fields' => array(
						'cookie_httponly' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'cookie_secure',
					'desc' => 'cookie_secure_desc',
					'security' => TRUE,
					'fields' => array(
						'cookie_secure' => array('type' => 'yes_no')
					)
				)
			),
			'member_security_settings' => array(
				array(
					'title' => 'allow_username_change',
					'desc' => 'allow_username_change_desc',
					'fields' => array(
						'allow_username_change' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'un_min_len',
					'desc' => lang('un_min_len_desc'),
					'fields' => array(
						'un_min_len' => array('type' => 'text')
					)
				),
				array(
					'title' => 'allow_multi_logins',
					'desc' => 'allow_multi_logins_desc',
					'fields' => array(
						'allow_multi_logins' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'require_ip_for_login',
					'desc' => 'require_ip_for_login_desc',
					'fields' => array(
						'require_ip_for_login' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'password_lockout',
					'desc' => 'password_lockout_desc',
					'fields' => array(
						'password_lockout' => array(
							'type' => 'inline_radio',
							'choices' => array(
								'y' => lang('enable'),
								'n' => lang('disable')
							)
						)
					)
				),
				array(
					'title' => 'password_lockout_interval',
					'desc' => lang('password_lockout_interval_desc'),
					'fields' => array(
						'password_lockout_interval' => array('type' => 'text')
					)
				),
				array(
					'title' => 'require_secure_passwords',
					'desc' => 'require_secure_passwords_desc',
					'fields' => array(
						'require_secure_passwords' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'pw_min_len',
					'desc' => 'pw_min_len_desc',
					'fields' => array(
						'pw_min_len' => array('type' => 'text')
					)
				),
				array(
					'title' => 'allow_dictionary_pw',
					'desc' => 'allow_dictionary_pw_desc',
					'fields' => array(
						'allow_dictionary_pw' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'name_of_dictionary_file',
					'desc' => 'name_of_dictionary_file_desc',
					'fields' => array(
						'name_of_dictionary_file' => array('type' => 'text')
					)
				)
			),
			'form_security_settings' => array(
				array(
					'title' => 'deny_duplicate_data',
					'desc' => 'deny_duplicate_data_desc',
					'fields' => array(
						'deny_duplicate_data' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'require_ip_for_posting',
					'desc' => 'require_ip_for_posting_desc',
					'fields' => array(
						'require_ip_for_posting' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'xss_clean_uploads',
					'desc' => 'xss_clean_uploads_desc',
					'fields' => array(
						'xss_clean_uploads' => array('type' => 'yes_no')
					)
				)
			)
		);

		ee()->form_validation->set_rules(array(
			array(
				'field' => 'un_min_len',
				'label' => 'lang:un_min_len',
				'rules' => 'integer'
			),
			array(
				'field' => 'password_lockout_interval',
				'label' => 'lang:password_lockout_interval',
				'rules' => 'integer'
			),
			array(
				'field' => 'pw_min_len',
				'label' => 'lang:pw_min_len',
				'rules' => 'integer'
			)
		));

		ee()->form_validation->validateNonTextInputs($vars['sections']);

		$base_url = ee('CP/URL')->make('settings/security-privacy');

		ee('CP/Alert')->makeInline('security-tip')
			->asWarning()
			->cannotClose()
			->addToBody(lang('security_tip'))
			->addToBody(lang('security_tip_desc'), 'enhance')
			->now();
		ee()->view->extra_alerts = array('security-tip');

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

		ee()->view->ajax_validate = TRUE;
		ee()->view->base_url = $base_url;
		ee()->view->cp_page_title = lang('security_privacy');
		ee()->view->save_btn_text = 'btn_save_settings';
		ee()->view->save_btn_text_working = 'btn_saving';

		ee()->cp->render('settings/form', $vars);
	}
}
// END CLASS

/* End of file SecurityPrivacy.php */
/* Location: ./system/EllisLab/ExpressionEngine/Controller/Settings/SecurityPrivacy.php */
