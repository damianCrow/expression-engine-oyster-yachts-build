<?php

namespace EllisLab\ExpressionEngine\Controller\Homepage;

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
 * ExpressionEngine CP Homepage Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Homepage extends CP_Controller {

	public function index()
	{
		$stats = ee('Model')->get('Stats')
			->filter('site_id', ee()->config->item('site_id'))
			->first();

		$vars['number_of_members'] = $stats->total_members;
		$vars['number_of_entries'] = $stats->total_entries;
		$vars['number_of_comments'] = $stats->total_comments;

		$vars['last_visit'] = ee()->localize->human_time(ee()->session->userdata['last_visit']);

		if (ee()->config->item('enable_comments') == 'y')
		{
			$vars['number_of_new_comments'] = ee('Model')->get('Comment')
				->filter('site_id', ee()->config->item('site_id'))
				->filter('comment_date', '>', ee()->session->userdata['last_visit'])
				->count();

			$vars['number_of_pending_comments'] = ee('Model')->get('Comment')
				->filter('site_id', ee()->config->item('site_id'))
				->filter('status', 'p')
				->count();

			$vars['number_of_spam_comments'] = ee('Model')->get('Comment')
				->filter('site_id', ee()->config->item('site_id'))
				->filter('status', 's')
				->count();
		}

		$vars['number_of_channels'] = ee('Model')->get('Channel')
			->filter('site_id', ee()->config->item('site_id'))
			->count();

		if ($vars['number_of_channels'] == 1)
		{
			$vars['channel_id'] = ee('Model')->get('Channel')
				->filter('site_id', ee()->config->item('site_id'))
				->first()
				->channel_id;
		}

		$vars['number_of_channel_field_groups'] = ee('Model')->get('ChannelFieldGroup')
			->filter('site_id', ee()->config->item('site_id'))
			->count();

		$vars['number_of_banned_members'] = ee('Model')->get('MemberGroup', 2)
			->first()
			->getMembers()
			->count();

		$vars['number_of_closed_entries'] = ee('Model')->get('ChannelEntry')
			->filter('site_id', ee()->config->item('site_id'))
			->filter('status', 'closed')
			->count();

		$vars['number_of_comments_on_closed_entries'] = ee('Model')->get('Comment')
			->with('Entry')
			->filter('Comment.site_id', ee()->config->item('site_id'))
			->filter('Entry.status', 'closed')
			->count();

		$vars['spam_module_installed'] = (ee('Model')->get('Module')->filter('module_name', 'Spam')->count());

		$vars['can_moderate_comments'] = ee()->cp->allowed_group('can_moderate_comments');
		$vars['can_edit_comments'] = ee()->cp->allowed_group('can_edit_all_comments');
		$vars['can_access_members'] = ee()->cp->allowed_group('can_access_members');
		$vars['can_create_members'] = ee()->cp->allowed_group('can_create_members');
		$vars['can_access_channels'] = ee()->cp->allowed_group('can_admin_channels');
		$vars['can_create_channels'] = ee()->cp->allowed_group('can_create_channels');
		$vars['can_access_fields'] = ee()->cp->allowed_group('can_create_channel_fields', 'can_edit_channel_fields', 'can_delete_channel_fields');
		$vars['can_access_member_settings'] = ee()->cp->allowed_group('can_access_sys_prefs', 'can_access_members');

		ee()->view->cp_page_title = ee()->config->item('site_name') . ' ' . lang('overview');
		ee()->cp->render('homepage', $vars);
	}

	public function acceptChecksums()
	{
		if (ee()->session->userdata('group_id') != 1)
		{
			show_error(lang('unauthorized_access'));
		}

		$return = ee('CP/URL')->make('homepage');

		if (ee()->input->post('return'))
		{
			ee()->load->library('file_integrity');
			$changed = ee()->file_integrity->check_bootstrap_files(TRUE);

			if ($changed)
			{
				foreach($changed as $site_id => $paths)
				{
					foreach($paths as $path)
					{
						ee()->file_integrity->create_bootstrap_checksum($path, $site_id);
					}
				}
			}

			$return = ee('CP/URL')->decodeUrl(ee()->input->post('return'));
		}

		ee()->functions->redirect($return);
	}

}
// EOF
