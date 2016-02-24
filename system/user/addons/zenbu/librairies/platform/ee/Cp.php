<?php namespace Zenbu\librairies\platform\ee;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Lang;
use Zenbu\librairies\platform\ee\Url;
use Zenbu\librairies\SavedSearches;

class Cp extends Base
{
	public function __construct()
	{
		parent::__construct();
		parent::init('settings');
		$this->saved_searches   = new SavedSearches();
	}

	public static function title($title)
	{
		//	----------------------------------------
		//	CP Page Title
		//	----------------------------------------
		if(version_compare(APP_VER, '3.0.0', '>='))
		{
			ee()->view->header = array('title' => $title);
			return;
		}

		if(version_compare(APP_VER, '2.6', '>'))
		{
			ee()->view->cp_page_title = Lang::t($title);
		} else {
			ee()->cp->set_variable('cp_page_title', Lang::t($title));
		}
	}

	public function initSidebar()
	{
		$sidebar = ee('CP/Sidebar')->make();

		$header = $sidebar->addHeader(Lang::t('entries'), Url::zenbuUrl());
		//$main_list = $header->addBasicList();
		//$main_list->addItem(Lang::t('entries'), Url::zenbuUrl());

		$header = $sidebar->addHeader(Lang::t('manage'));
		$manage_list = $header->addBasicList();
		$manage_list->addItem(Lang::t('saved_searches'), ee('CP/URL', 'addons/settings/zenbu/saved_searches'));
		if((isset($this->permissions['can_access_settings']) && $this->permissions['can_access_settings'] == 'y') || $this->user->group_id == 1)
		{
			$manage_list->addItem(Lang::t('display_settings'), ee('CP/URL', 'addons/settings/zenbu/display_settings'));
		}

		if((isset($this->permissions['can_admin']) && $this->permissions['can_admin'] == 'y') || $this->user->group_id == 1)
		{
			$manage_list->addItem(Lang::t('permissions'), ee('CP/URL', 'addons/settings/zenbu/permissions'));
		}
	}

	public function rightNav()
	{
		if(version_compare(APP_VER, '3.0.0', '>='))
		{
			return;
		}
		else
		{
			//	----------------------------------------
			//	Top Right Navigation
			//	----------------------------------------

			$nav_array['<i class=\'fa fa-list\'></i> '.Lang::t('entries')]	= Url::cpUrl(AMP."C=addons_modules".AMP."M=show_module_cp".AMP."module=zenbu".AMP."channel_id=".$this->display_settings['sectionId']);

			$nav_array['<i class=\'fa fa-search\'></i> '.Lang::t('saved_searches')]	= Url::cpUrl(AMP."C=addons_modules".AMP."M=show_module_cp".AMP."module=zenbu".AMP."method=saved_searches");

			if((isset($this->permissions['can_access_settings']) && $this->permissions['can_access_settings'] == 'y') || $this->user->group_id == 1) {
				$nav_array['<i class=\'fa fa-cog\'></i> '.Lang::t('display_settings')]	= Url::cpUrl(AMP."C=addons_modules".AMP."M=show_module_cp".AMP."module=zenbu".AMP."method=settings".AMP."channel_id=".$this->display_settings['sectionId']);
			}
			if((isset($this->permissions['can_admin']) && $this->permissions['can_admin'] == 'y') || $this->user->group_id == 1) {
				$nav_array['<i class=\'fa fa-group\'></i> '.Lang::t('permissions')]	= Url::cpUrl(AMP."C=addons_modules".AMP."M=show_module_cp".AMP."module=zenbu".AMP."method=permissions");
			}

			ee()->cp->set_right_nav($nav_array);
		}
	}

	/**
	 * Set up flash alert/message
	 * @param  string $type    The type of message/alert (success/warning/error)
	 * @param  mixed  $message The message, either title + body or just title
	 * @return void
	 */
	public function message($type = 'success', $message = '')
	{
		if(is_array($message))
		{
			$title = isset($message['title']) ? $message['title'] : '';
			$body  = isset($message['body']) ? $message['body'] : '';
		}
		else
		{
			$title = $message;
			$body = '';
		}

		$alert_type = 'asSuccess';

		if($type == 'warning')
		{
			$alert_type = 'asWarning';
		}

		if($type == 'error')
		{
			$alert_type = 'asIssue';
		}

		if(empty($body))
		{
			ee('CP/Alert')->make('message', 'inline')
					      ->{$alert_type}()
					      ->withTitle($title)
					      ->canClose()
					      ->defer();
		}
		else
		{
			ee('CP/Alert')->make('message', 'inline')
					      ->{$alert_type}()
					      ->withTitle($title)
					      ->addToBody($body)
					      ->canClose()
					      ->defer();
		}
	} // END message()

	// --------------------------------------------------------------------
}