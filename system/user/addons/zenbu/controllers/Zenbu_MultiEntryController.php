<?php
namespace Zenbu\controllers;

use Zenbu\controllers\ZenbuBaseController as ZenbuBaseController;
use Zenbu\librairies\platform\ee\Cache;
use Zenbu\librairies\platform\ee\Convert;
use Zenbu\librairies\platform\ee\Cp;
use Zenbu\librairies\platform\ee\Lang;
use Zenbu\librairies\platform\ee\Request;
use Zenbu\librairies\platform\ee\Url;
use Zenbu\librairies\platform\ee\View;

class Zenbu_MultiEntryController extends ZenbuBaseController
{
	public function __construct()
	{
		parent::init();
	}

	/**
	 * Main page
	 * @return string Rendered template
	 */
	public function actionIndex()
	{
		$this->cp->title(Lang::t('multi_entry_editor'));
		$this->cp->rightNav();

		$this->vars['debug_mode'] = $this->debug_mode;
		$this->vars['action']     = $action = Request::param('action');
		$this->vars['entries']    = $this->entries->baseDataFromSelected();

		ee()->cp->add_js_script(array('ui' => 'datepicker'));
		View::includeCss(array(
			'resources/css/zenbu_main.css',  
			'resources/css/font-awesome.min.css'
			));
        View::includeJs(array(
        	'resources/js/zenbu_common.js', 
        	'resources/js/zenbu_multi_edit.js'
        	));

		if($action == 'edit')
		{
			$this->vars['all_select_options']        = $this->statuses->getStatusFilterOptions();
			$this->vars['select_options_by_channel'] = $this->statuses->getStatusByChannel();
			$this->vars['action_url']                = Url::cpUrl('publish/update_multi_entries');
			return array(
              'body'       => View::render('multi_entry/edit.twig', $this->vars),
              'breadcrumb' => array(Url::zenbuUrl()->compile() => Lang::t('entry_manager')),
              'heading'  => Lang::t('multi_entry_editor'),
            );
		}

		if($action == 'delete')
		{
			$this->cp->title(Lang::t('confirm_delete'));
			$this->vars['action_url']    = Url::cpUrl('cp/publish/edit');
			return array(
              'body'       => View::render('multi_entry/delete.twig', $this->vars),
              'breadcrumb' => array(Url::zenbuUrl()->compile() => Lang::t('entry_manager')),
              'heading'  => Lang::t('confirm_delete'),
            );
		}

		if($action == 'category_add' || $action == 'category_remove')
		{
			$this->vars['nested_categories']    = $this->categories->getNestedCategories();
			$this->vars['category_group_names'] = $this->categories->getCategoryGroups();
			$this->vars['category_entries']     = $this->categories->getCategoryEntries($this->vars['entries']);
			
			if($action == 'category_add')
			{
				$this->cp->title(Lang::t('add_categories'));
				$this->vars['action_url']    = Url::cpUrl(AMP."C=content_edit".AMP."M=multi_entry_category_update");
			}

			if($action == 'category_remove')
			{
				$this->cp->title(Lang::t('remove_categories'));
				$this->vars['action_url']    = Url::cpUrl(AMP."C=content_edit".AMP."M=multi_entry_category_update");
			}

			return View::render('multi_entry/category_add.twig', $this->vars);
		}

	} // END actionIndex()

	// --------------------------------------------------------------------

}