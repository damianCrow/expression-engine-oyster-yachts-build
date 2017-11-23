<?php

namespace EllisLab\ExpressionEngine\Controller\Publish;

use CP_Controller;
use EllisLab\ExpressionEngine\Library\CP\Table;

use EllisLab\ExpressionEngine\Model\Channel\ChannelEntry;
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2016, EllisLab, Inc.
 * @license		https://expressionengine.com/license
 * @link		https://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine CP Abstract Publish Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		https://ellislab.com
 */
abstract class AbstractPublish extends CP_Controller {

	protected $is_admin = FALSE;
	protected $assigned_channel_ids = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		ee()->lang->loadfile('content');

		ee()->cp->get_installed_modules();

		$this->is_admin = (ee()->session->userdata['group_id'] == 1);
		$this->assigned_channel_ids = array_keys(ee()->session->userdata['assigned_channels']);

		$this->pruneAutosaves();
	}

	protected function createChannelFilter()
	{
		$allowed_channel_ids = ($this->is_admin) ? NULL : $this->assigned_channel_ids;
		$channels = ee('Model')->get('Channel', $allowed_channel_ids)
			->fields('channel_id', 'channel_title')
			->filter('site_id', ee()->config->item('site_id'))
			->order('channel_title', 'asc')
			->all();

		$channel_filter_options = array();
		foreach ($channels as $channel)
		{
			$channel_filter_options[$channel->channel_id] = $channel->channel_title;
		}
		$channel_filter = ee('CP/Filter')->make('filter_by_channel', 'filter_by_channel', $channel_filter_options);
		$channel_filter->disableCustomValue(); // This may have to go
		return $channel_filter;
	}

	protected function setGlobalJs($entry, $valid)
	{
		$entry_id = $entry->entry_id;
		$channel_id = $entry->channel_id;

		$autosave_interval_seconds = (ee()->config->item('autosave_interval_seconds') === FALSE) ?
										60 : ee()->config->item('autosave_interval_seconds');

		//	Create Foreign Character Conversion JS
		include(APPPATH.'config/foreign_chars.php');

		/* -------------------------------------
		/*  'foreign_character_conversion_array' hook.
		/*  - Allows you to use your own foreign character conversion array
		/*  - Added 1.6.0
		* 	- Note: in 2.0, you can edit the foreign_chars.php config file as well
		*/
			if (isset(ee()->extensions->extensions['foreign_character_conversion_array']))
			{
				$foreign_characters = ee()->extensions->call('foreign_character_conversion_array');
			}
		/*
		/* -------------------------------------*/

		$smileys_enabled = (isset(ee()->cp->installed_modules['emoticon']) ? TRUE : FALSE);

		if ($smileys_enabled)
		{
			ee()->load->helper('smiley');
			ee()->cp->add_to_foot(smiley_js());
		}

		ee()->cp->add_js_script('plugin', 'nestable');

		ee()->javascript->set_global(array(
			'lang.add_new_html_button'			=> lang('add_new_html_button'),
			'lang.close' 						=> lang('close'),
			'lang.confirm_exit'					=> lang('confirm_exit'),
			'lang.loading'						=> lang('loading'),
			'publish.autosave.interval'			=> (int) $autosave_interval_seconds,
			'publish.autosave.URL'				=> ee('CP/URL')->make('publish/autosave/' . $channel_id . '/' . $entry_id)->compile(),
			'publish.add_category.URL'			=> ee('CP/URL')->make('channels/cat/createCat/###')->compile(),
			'publish.edit_category.URL'			=> ee('CP/URL')->make('channels/cat/editCat/###')->compile(),
			'publish.reorder_categories.URL'	=> ee('CP/URL')->make('channels/cat/cat-reorder/###')->compile(),
			// 'publish.channel_id'				=> $this->_channel_data['channel_id'],
			// 'publish.default_entry_title'		=> $this->_channel_data['default_entry_title'],
			// 'publish.field_group'				=> $this->_channel_data['field_group'],
			'publish.foreignChars'				=> $foreign_characters,
			'publish.lang.no_member_groups'		=> lang('no_member_groups'),
			'publish.lang.refresh_layout'		=> lang('refresh_layout'),
			'publish.lang.tab_count_zero'		=> lang('tab_count_zero'),
			'publish.lang.tab_has_req_field'	=> lang('tab_has_req_field'),
			'publish.markitup.foo'				=> FALSE,
			'publish.smileys'					=> $smileys_enabled,
			'publish.field.URL'                 => ee('CP/URL', 'publish/field/' . $channel_id . '/' . $entry_id)->compile(),
			'publish.auto_assign_cat_parents'	=> ee()->config->item('auto_assign_cat_parents'),
			// 'publish.url_title_prefix'			=> $this->_channel_data['url_title_prefix'],
			'publish.which'						=> ($entry_id) ? 'edit' : 'new',
			'publish.word_separator'			=> ee()->config->item('word_separator') != "dash" ? '_' : '-',
			'user.can_edit_html_buttons'		=> ee()->cp->allowed_group('can_edit_html_buttons'),
			'user.foo'							=> FALSE,
			'user_id'							=> ee()->session->userdata('member_id'),
			// 'upload_directories'				=> $this->_file_manager['file_list'],
		));

		// -------------------------------------------
		//	Publish Page Title Focus - makes the title field gain focus when the page is loaded
		//
		//	Hidden Configuration Variable - publish_page_title_focus => Set focus to the tile? (y/n)

		ee()->javascript->set_global('publish.title_focus', FALSE);

		if ( ! $entry_id && $valid && bool_config_item('publish_page_title_focus'))
		{
			ee()->javascript->set_global('publish.title_focus', TRUE);
		}
	}

	protected function getRevisionsTable($entry, $version_id = FALSE)
	{
		$table = ee('CP/Table');

		$table->setColumns(
			array(
				'rev_id',
				'rev_date',
				'rev_author',
				'manage' => array(
					'encode' => FALSE
				)
			)
		);
		$table->setNoResultsText(lang('no_revisions'));

		$data = array();
		$authors = array();
		$i = 1;

		foreach ($entry->Versions as $version)
		{
			if ( ! isset($authors[$version->author_id]))
			{
				$authors[$version->author_id] = $version->getAuthorName();
			}

			$toolbar = ee('View')->make('_shared/toolbar')->render(array(
				'toolbar_items' => array(
						'txt-only' => array(
							'href' => ee('CP/URL')->make('publish/edit/entry/' . $entry->entry_id, array('version' => $version->version_id)),
							'title' => lang('view'),
							'content' => lang('view')
						),
					)
				)
			);

			$attrs = ($version->version_id == $version_id) ? array('class' => 'selected') : array();

			$data[] = array(
				'attrs'   => $attrs,
				'columns' => array(
					$i,
					ee()->localize->human_time($version->version_date->format('U')),
					$authors[$version->author_id],
					$toolbar
				)
			);
			$i++;
		}

		if ( ! $entry->isNew())
		{
			if ( ! $version_id)
			{
				$attrs = array('class' => 'selected');
			}

			if ( ! isset($authors[$entry->author_id]))
			{
				$authors[$entry->author_id] = $entry->getAuthorName();
			}

			// Current
			$edit_date = ($entry->edit_date)
				? ee()->localize->human_time($entry->edit_date->format('U'))
				: NULL;

			$data[] = array(
				'attrs'   => $attrs,
				'columns' => array(
					$i,
					$edit_date,
					$authors[$entry->author_id],
					'<span class="st-open">' . lang('current') . '</span>'
				)
			);
		}

		$table->setData($data);

		return ee('View')->make('_shared/table')->render($table->viewData(''));
	}

	/**
	 * Adds modals for the category add/edit form and category removal confirmation
	 */
	protected function addCategoryModals()
	{
		// Don't bother adding modals to DOM if they don't have permission
		if ( ! ee()->cp->allowed_group_any(
			'can_create_categories',
			'can_edit_categories',
			'can_delete_categories'
		))
		{
			return;
		}

		$cat_form_modal = ee('View')->make('ee:_shared/modal')->render(array(
			'name'		=> 'modal-checkboxes-edit',
			'contents'	=> '')
		);
		ee('CP/Modal')->addModal('modal-checkboxes-edit', $cat_form_modal);

		$cat_remove_modal = ee('View')->make('ee:_shared/modal_confirm_remove')->render(array(
			'name'		=> 'modal-checkboxes-confirm-remove',
			'form_url'	=> ee('CP/URL')->make('channels/cat/removeCat'),
			'hidden'	=> array(
				'bulk_action'	=> 'remove',
				'categories[]'	=> ''
			)
		));
		ee('CP/Modal')->addModal('modal-checkboxes-confirm-remove', $cat_remove_modal);
	}

	protected function validateEntry(ChannelEntry $entry, $layout)
	{
		if (empty($_POST))
		{
			return FALSE;
		}

		$action = ($entry->isNew()) ? 'create' : 'edit';

		// Get all the fields that should be in the DOM. Any that were not
		// POSTed will be set to NULL. This addresses a bug where browsers
		// do not POST unchecked checkboxes.
		foreach ($layout->getTabs() as $tab)
		{
			// Invisible tabs were not rendered
			if ($tab->isVisible())
			{
				foreach ($tab->getFields() as $field)
				{
					// Fields that were not required and not visible were not rendered
					if ( ! $field->isRequired() && ! $field->isVisible())
					{
						continue;
					}

					$field_name = strstr($field->getName(), '[', TRUE) ?: $field->getName();

					if ( ! array_key_exists($field_name, $_POST))
					{
						$_POST[$field_name] = NULL;
					}
				}
			}
		}

		if ( ! ee()->cp->allowed_group('can_assign_post_authors'))
		{
			unset($_POST['author_id']);
		}

		$entry->set($_POST);

		// if categories are not in POST, then they've unchecked everything
		// and we need to clear them out
		if ( ! isset($_POST['categories']))
		{
			$entry->categories = array();
		}

		$result = $entry->validate();

		if ($response = $this->ajaxValidation($result))
		{
			ee()->output->send_ajax_response($response);
		}

		if ($result->failed())
		{
			ee('CP/Alert')->makeInline('shared-form')
				->asIssue()
				->withTitle(lang($action . '_entry_error'))
				->addToBody(lang($action . '_entry_error_desc'))
				->now();
		}

		return $result;
	}

	protected function saveEntryAndRedirect($entry)
	{
		$action = ($entry->isNew()) ? 'create' : 'edit';

		if ($entry->versioning_enabled && ee()->input->post('save_revision'))
		{
			$entry->saveVersion();

			ee('CP/Alert')->makeInline('entry-form')
				->asSuccess()
				->withTitle(lang('revision_saved'))
				->addToBody(sprintf(lang('revision_saved_desc'), $entry->Versions->count() + 1, $entry->title))
				->defer();

			ee()->functions->redirect(ee('CP/URL')->make('publish/edit/entry/' . $entry->entry_id, ee()->cp->get_url_state()));
		}
		else
		{
			$entry->edit_date = ee()->localize->now;
			$entry->save();

			if ($action == 'create')
			{
				ee()->session->set_flashdata('entry_id', $entry->entry_id);
			}

			ee('CP/Alert')->makeInline('entry-form')
				->asSuccess()
				->withTitle(lang($action . '_entry_success'))
				->addToBody(sprintf(lang($action . '_entry_success_desc'), $entry->title))
				->defer();

			ee()->functions->redirect(ee('CP/URL')->make('publish/edit/', array('filter_by_channel' => $entry->channel_id)));
		}
	}

	/**
	 * Delete stale autosaved data based on the `autosave_prune_hours` config
	 * value
	 *
	 * @return void
	 */
	protected function pruneAutosaves()
	{
		$prune = ee()->config->item('autosave_prune_hours') ?: 6;
		$prune = $prune * 120; // From hours to seconds

		$cutoff = ee()->localize->now - $prune;

		$autosave = ee('Model')->get('ChannelEntryAutosave')
			->filter('edit_date', '<', $cutoff)
			->delete();
	}
}

// EOF
