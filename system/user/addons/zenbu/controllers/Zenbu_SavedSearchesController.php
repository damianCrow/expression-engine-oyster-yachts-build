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
use Zenbu\models as Model;

class Zenbu_SavedSearchesController extends ZenbuBaseController
{
    var $permissions;
    
    public function __construct()
    {
        parent::init();
    }
    
    /**
     * Manage Saved Searches
     * @return string Rendered section
     */
    public function actionIndex()
    {
        $this->cp->title(Lang::t('saved_searches'));
        $this->cp->initSidebar();
        $this->vars['permissions']        = $this->permissions;
        $this->vars['savedSearches']      = $this->saved_searches->getSavedSearches();
        $this->vars['groupSavedSearches'] = $this->saved_searches->getGroupSavedSearches();
        $this->vars['userGroupOptions']   = $this->users->getUserGroupSelectOptions();
        $this->vars['action_url']         = Url::zenbuUrl('save_searches');

        View::includeCss(array(
            'resources/css/zenbu_main.css', 
            'resources/css/font-awesome.min.css'
            ));
        View::includeJs(array(
            'resources/js/zenbu_common.js',
            'resources/js/zenbu_saved_searches.js'
            ));
        return array(
              'body'       => View::render('saved_searches/index.twig', $this->vars),
              'breadcrumb' => array(Url::zenbuUrl()->compile() => Lang::t('entry_manager')),
              'heading'  => Lang::t('saved_searches'),
            );
    } // END actionManageSavedSearches()

    // --------------------------------------------------------------------

    /**
     * Retrieve Search filters based on ID
     * @return string JSON output
     */
    public function actionFetchFilters()
    {
        $output = $this->saved_searches->getSavedSearchFilters();
        return json_encode($output);
    } // END actionFetchFilters()

    // --------------------------------------------------------------------

    /**
     * Retrieve Search filters based on ID
     * @return string JSON output
     */
    public function actionFetchSavedSearches()
    {
        $output = $this->saved_searches->getSavedSearches();
        if(Request::isAjax())
        {
            echo json_encode($output);
            exit();
        }
        else
        {
            return json_encode($output);
        }
    } // END actionFetchFilters()

    // --------------------------------------------------------------------


    /**
     * Save the Saved Search
     * @return string JSON output
     */
    public function actionSave()
    {
        //    ----------------------------------------
        //    Updating, so clear the cache
        //    ----------------------------------------
        Cache::delete('saved_searches_Group_' . $this->user->group_id);
        Cache::delete('saved_searches_User_' . $this->user->id);
        
        $search_ids_selected = Request::post('search_ids_selected');
        $search_labels       = Request::post('search_labels');
        $search_copy         = Request::post('search_copy');

        //    ----------------------------------------
        //    Update
        //    ----------------------------------------
        $c = 0;
        if($search_labels)
        {
            foreach($search_labels as $id => $label)
            {
                $saved_search         = new Model\ZenbuSavedSearchesModel();
                $saved_search->id     = $id;
                $saved_search->label  = $label;
                $saved_search->order  = $c;
                $saved_search->site_id = $this->user->site_id;
                $saved_search->save();
                $c++;
            }
        }

        $this->cp->message('success', Lang::t("Saved Searches Updated"));
        
        //    ----------------------------------------
        //    Copy
        //    ----------------------------------------
        if($search_copy != FALSE)
        {
            $found = $this->db->find('zenbu_saved_searches', 'id IN(?)', array(
                        implode(',', $search_ids_selected)
                    ), array('order', 'asc')
                );
            if(count($found) > 0)
            {
                foreach($found as $found)
                {
                    // Find the filters
                    $found_filters = $this->db->find('zenbu_saved_search_filters', 'searchId = ?', array($found->id), array('order', 'asc'));

                    foreach($search_copy as $group_id)
                    {
                        $current_group_searches = $this->db->find('zenbu_saved_searches', 'userGroupId = ?', array(
                                $group_id
                            ), array('order', 'asc')
                        );
                        $saved_search              = new Model\ZenbuSavedSearchesModel();
                        $saved_search->label       = $found->label;
                        $saved_search->userGroupId = $group_id;
                        $saved_search->order       = count($current_group_searches);
                        $saved_search->site_id     = $found->site_id;
                        $new_id = $saved_search->save();

                        foreach($found_filters as $found_filter)
                        {
                            $saved_search_filter                   = new \stdClass();
                            $saved_search_filter->searchId         = $new_id;
                            $saved_search_filter->filterAttribute1 = $found_filter->filterAttribute1;
                            $saved_search_filter->filterAttribute2 = $found_filter->filterAttribute2;
                            $saved_search_filter->filterAttribute3 = $found_filter->filterAttribute3;
                            $saved_search_filter->order            = $found_filter->order;

                            $this->db->insert('zenbu_saved_search_filters', $saved_search_filter);
                        }
                    }
                }
            }

            $this->cp->message('success', Lang::t("Saved Searches Updated and Copied"));

        }

        //    ----------------------------------------
        //    Delete
        //    ----------------------------------------
        $delete = Request::post('delete');

        if($delete !== FALSE)
        {
            $this->db->delete('zenbu_saved_search_filters', 'searchId IN(?)', array(
                        implode($search_ids_selected, ',')
                    )
                );
            $this->db->delete('zenbu_saved_searches', 'id IN(?)', array(
                        implode($search_ids_selected, ',')
                    )
                );
    
            $this->cp->message('success', Lang::t("Saved Searches Deleted"));

            Request::redirect(Url::zenbuUrl('saved_searches'));
            
        }

        //    ----------------------------------------
        //    Create
        //    ----------------------------------------

        if (Request::post('label'))
        {
            $this->actionCreate();
            if(Request::isAjax())
            {
                $this->actionFetchSavedSearches();
                exit();
            }
        }
        
        Request::redirect(Url::zenbuUrl('saved_searches'));
            
    } // END actionSave()

    // --------------------------------------------------------------------

    /**
     * Save the Saved Search
     * @return string JSON output
     */
    public function actionCreate()
    {
        Cache::delete('saved_searches_User_' . $this->user->id);

        $originalSearchList = $this->saved_searches->getSavedSearches();
        
        //    ----------------------------------------
        //    Updating, so clear the cache
        //    ----------------------------------------
        Cache::delete('saved_searches_Group_' . $this->user->group_id);
        Cache::delete('saved_searches_User_' . $this->user->id);

        $search          = new Model\ZenbuSavedSearchesModel();
        $search->label   = Request::post('label', 'Saved Search');
        $search->userId  = $this->user->id;
        $search->order   = count($originalSearchList['items']);
        $search->site_id = $this->user->site_id;
        $searchId        = $search->save();

        $sectionId      = Request::param(Convert::string('sectionId'), 0);

        $c = 0;
        $searchfilter                   = new Model\ZenbuSavedSearchFiltersModel();
        $searchfilter->searchId         = $searchId;
        $searchfilter->filterAttribute1 = Convert::string('sectionId');
        $searchfilter->filterAttribute2 = 'is';
        $searchfilter->filterAttribute3 = $sectionId;
        $searchfilter->order            = $c;
        $searchfilter->save();
        //$this->db->insert('zenbu_saved_search_filters', $searchfilter);
        $c++;

        //  ----------------------------------------
        //  Filters
        //  ----------------------------------------

        $filters = Request::param('filter');

        if (! empty($filters)) {
            foreach ($filters as $key => $val) {
                $searchfilter = new Model\ZenbuSavedSearchFiltersModel();
                $searchfilter->searchId         = $searchId;
                $searchfilter->filterAttribute1 = $val['1st'];
                $searchfilter->filterAttribute2 = $val['2nd'];
                $searchfilter->filterAttribute3 = $val['3rd'];
                $searchfilter->order            = $c;
                $searchfilter->save();
                //$this->db->insert('zenbu_saved_search_filters', $searchfilter);
                $c++;
            }
        }

        $searchfilter = new Model\ZenbuSavedSearchFiltersModel();
        $searchfilter->searchId         = $searchId;
        $searchfilter->filterAttribute1 = 'limit';
        $searchfilter->filterAttribute2 = Request::param('limit', '25');
        $searchfilter->filterAttribute3 = '';
        $searchfilter->order            = $c;
        $searchfilter->save();
        //$this->db->insert('zenbu_saved_search_filters', $searchfilter);
        $c++;

        $searchfilter = new Model\ZenbuSavedSearchFiltersModel();
        $searchfilter->searchId         = $searchId;
        $searchfilter->filterAttribute1 = 'orderby';
        $searchfilter->filterAttribute2 = Request::param('orderby', 'title');
        $searchfilter->filterAttribute3 = Request::param('sort', 'ASC');
        $searchfilter->order            = $c;
        $searchfilter->save();
        //$this->db->insert('zenbu_saved_search_filters', $searchfilter);

    } // END actionCreate()

    // --------------------------------------------------------------------


    /**
     * Save search filters temporarily to be able to come back to them later
     * @return string JSON output
     */
    public function actionCacheTempFilters()
    {
        $sectionId    = Request::param(Convert::string('sectionId'), 0);
        $searchfilter = array();

        $c = 0;
        $searchfilter[$c]['filterAttribute1'] = Convert::string('sectionId');
        $searchfilter[$c]['filterAttribute2'] = 'is';
        $searchfilter[$c]['filterAttribute3'] = $sectionId;
        $searchfilter[$c]['order']            = $c;
        $c++;

        //  ----------------------------------------
        //  Filters
        //  ----------------------------------------

        $filters = Request::param('filter');

        if (! empty($filters)) {
            foreach ($filters as $key => $val) {
                $searchfilter[$c]['filterAttribute1'] = $val['1st'];
                $searchfilter[$c]['filterAttribute2'] = $val['2nd'];
                $searchfilter[$c]['filterAttribute3'] = $val['3rd'];
                $searchfilter[$c]['order']            = $c;
                $c++;
            }
        }

        $searchfilter[$c]['filterAttribute1'] = 'limit';
        $searchfilter[$c]['filterAttribute2'] = Request::param('limit', '25');
        $searchfilter[$c]['filterAttribute3'] = '';
        $searchfilter[$c]['order']            = $c;
        $c++;

        $searchfilter[$c]['filterAttribute1'] = 'orderby';
        $searchfilter[$c]['filterAttribute2'] = Request::param('orderby', 'title');
        $searchfilter[$c]['filterAttribute3'] = Request::param('sort', 'ASC');
        $searchfilter[$c]['order']            = $c;

        Cache::set('TempFilters_User_'.$this->user->id, $searchfilter, 60);

    } // END actionCreate()

    // --------------------------------------------------------------------

    /**
     * Retrieve temporarily stored search filters.
     * Used when returning to Zenbu's main section and 
     * previous search should be retrieved.
     * @return array The temporarily cached search filters for the user
     */
    public function actionFetchCachedTempFilters()
    {
        if(Cache::get('TempFilters_User_' . $this->user->id) !== FALSE)
        {
            return json_encode(Cache::get('TempFilters_User_' . $this->user->id));
        }
        else
        {
            return json_encode(array());
        }
    } // END actionFetchCachedTempFilters()

    // --------------------------------------------------------------------
}
