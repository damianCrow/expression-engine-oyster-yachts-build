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

class ZenbuController extends ZenbuBaseController
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
		if(Request::isAjax() === FALSE)
		{
			$this->cp->title(Lang::t('entry_manager'));
			$this->cp->rightNav();
			$this->vars['sections']                 = $this->sections->getSections();
			$selectOptions                          = $this->sections->getSectionSelectOptions();
			$this->vars['section_dropdown_options'] = $selectOptions['sections'];
			$this->vars['limit_options']            = $this->filters->getLimitSelectOptions();
			$this->vars['orderby_options']          = $this->filters->getOrderBySelectOptions();
			$this->vars['sort_options']             = $this->filters->getSortSelectOptions();
			$this->vars['firstFilterOptions']       = $this->filters->getFirstFilterOptions();
			$this->vars['secondFilterOptions']      = $this->filters->getSecondFilterOptions();
			$this->vars['fields']                   = $this->fields->getFields();
			$this->vars['fields_2nd_filter_type']   = $this->fields->getFieldsSecondFilterType();
			$this->vars['sections']                 = $this->sections->getSections();
			$this->vars['savedSearches']            = $this->saved_searches->getSavedSearches();
			$this->vars['savedSearch']              = $this->saved_searches->getSavedSearchFilters();
			$this->vars['statusFilterOptions']      = $this->statuses->getStatusFilterOptions();
			$this->vars['nested_categories']        = $this->categories->getNestedCategories();
			$this->vars['category_group_names']     = $this->categories->getCategoryGroups();
			$this->vars['category_dropdowns']       = $this->categories->makeCategoryDropdown($this->vars['nested_categories'], $this->vars['category_group_names']);
			$this->vars['storedFilterData']         = array();

			/**
			*	======================================
			*	Extension Hook zenbu_after_save_search
			*	======================================
			*
			*	Enables the addition of extra code after the "Save this search" link
			*	@return string 	$this->vars_other['extra_options_right_save']	The output HTML
			*
			*/
			if (ee()->extensions->active_hook('zenbu_after_save_search') === TRUE)
			{
				$this->vars['after_save_search'] = ee()->extensions->call('zenbu_after_save_search');
				if (ee()->extensions->end_script === TRUE) return;
			}

			//	----------------------------------------
			//	If this is not a saved search request, or
			//	there is no saved search to retrieve,
			//	try getting filters from DB cache, if
			//	there's anything available
			//	----------------------------------------
			if( empty($this->vars['savedSearch']) )
			{
				// Comment out the following line if constant cache retrieval is being bothersome
				$this->vars['storedFilterData'] = Cache::get('zenbu_filter_cache_'.$this->user->id);
			}
		}

		//	----------------------------------------
		//	Order "show" fields first
		//	----------------------------------------
		$orderedFields = $this->fields->getOrderedFields();

		$this->vars['columns'] = empty($orderedFields) ? $this->vars['fields'] : $orderedFields;

		$this->vars['status_colors']          = $this->statuses->getStatusColors();
		$this->vars['authors']                = $this->authors->getAuthorSelectOptions();
		$this->vars['result_array']           = $results = $this->entries->getEntries();
		$this->vars['entries']                = $results['results'];
		$this->vars['entries_override']       = $this->entries->getOverrides($this->vars['entries']);
		$this->vars['entry_categories']       = $this->categories->getEntryCategories($this->vars['entries']);
		$this->vars['total_results']          = $results['total_results'];
		$this->vars['action_url']             = Url::zenbuUrl();
		$this->vars['multi_edit_action_url']  = Url::zenbuUrl("multi_edit");
		$this->vars['save_search_action_url'] = Url::zenbuUrl("save_searches");

		//	----------------------------------------
		//	Pagination
		//	----------------------------------------
		$this->vars['pagination']   = $this->pagination->getPagination($this->vars['total_results'], Request::param('limit', 25));
		$this->vars['results_from'] = Request::param('perpage') && Request::param('perpage') != 1 ? (Request::param('perpage') - 1) * Request::param('limit', 25) + 1 : 1;
		$this->vars['results_to']   = $this->vars['results_from'] != 1 ? $this->vars['results_from'] + Request::param('limit', 25) - 1 : Request::param('limit', 25);
		$this->vars['results_to']   = $this->vars['results_to'] > $this->vars['total_results'] ? $this->vars['total_results'] : $this->vars['results_to'];

		//	----------------------------------------
		//	Response
		//	----------------------------------------
		if(Request::isAjax() !== FALSE)
		{
			echo View::render('main/results.twig', $this->vars);
			exit();
		}
		else
		{
			//	----------------------------------------
			//	If were loading Zenbu with a channel filter,
			//	remove temporarily cached search filters since
			//	these will interfere with the channel filter.
			//	----------------------------------------
			if(Request::get('channel_id'))
			{
				Cache::delete('TempFilters_User_' . $this->user->id);
			}

			ee()->cp->add_js_script(array('ui' => 'datepicker'));
			View::includeCss(array(
				'resources/fancybox/jquery.fancybox-1.3.4.css',
				'resources/chosen/chosen.min.css',
				'resources/css/zenbu_main.css',
				'resources/css/font-awesome.min.css',
				));
			View::includeJs(array(
				'resources/fancybox/jquery.easing-1.3.pack.js',
				'resources/fancybox/jquery.mousewheel-3.0.4.pack.js',
				'resources/fancybox/jquery.fancybox-1.3.4.pack.js',
				'resources/chosen/chosen.jquery.min.js',
				'resources/js/typewatch.js',
				'resources/js/zenbu_common.js',
				'resources/js/zenbu_main.js'
				));
			return array(
			  'body'       => View::render('main/index.twig', $this->vars),
			  'breadcrumb' => array(),
			  'heading'  => Lang::t('entry_manager'),
			);
		}

	} // END actionIndex()

	// --------------------------------------------------------------------

}
