<?php namespace Zenbu\librairies;

use Craft;
use Zenbu\librairies\platform\ee\Cache;
use Zenbu\librairies\platform\ee\Lang;
use Zenbu\librairies\platform\ee\Request;
use Zenbu\librairies\platform\ee\Session;
use Zenbu\librairies\platform\ee\Convert;
use Zenbu\librairies\platform\ee\Db;
use Zenbu\librairies\platform\ee\Url;
use Zenbu\librairies\Fields;

class SavedSearches
{
    var $fields;

    public function __construct()
    {
        $this->fields = new Fields();
    }

    /**
     * Retrieve saved searches
     * @return array
     */
    public function getSavedSearches()
    {
        if(Cache::get('saved_searches_User_' . Session::user()->id) !== FALSE)
        {
            return Cache::get('saved_searches_User_' . Session::user()->id);
        }

        $sql = 'SELECT * FROM zenbu_saved_searches
                WHERE userId = ' . Session::user()->id . '
                ORDER BY `order` ASC';

        $results = Db::rawQuery($sql);

        $output['base_url'] = Url::zenbuUrl('', TRUE);
        $output['items'] = array();

        if(count($results > 0))
        {
            foreach($results as $row)
            {
                $output['items'][] = $row;
            }
        }

        Cache::set('saved_searches_User_' . Session::user()->id, $output, 10);

        return $output;

    } // END getSavedSearches()

    // --------------------------------------------------------------------


    /**
     * Retrieve user's group saved searches
     * @return array
     */
    public function getGroupSavedSearches()
    {
        if(Cache::get('saved_searches_Group_' . Session::user()->group_id) !== FALSE)
        {
            return Cache::get('saved_searches_Group_' . Session::user()->group_id);
        }

        $sql = 'SELECT * FROM zenbu_saved_searches
                WHERE userGroupId = ' . Session::user()->group_id . '
                ORDER BY `order` ASC';

        $results = Db::rawQuery($sql);

        $output['base_url'] = Url::zenbuUrl();
        $output['items'] = array();

        if(count($results > 0))
        {
            foreach($results as $row)
            {
                $output['items'][] = $row;
            }
        }

        Cache::set('saved_searches_Group_' . Session::user()->group_id, $output, 10);

        return $output;

    } // END getGroupSavedSearches()

    // --------------------------------------------------------------------


    public function getSavedSearchFilters()
    {
        $searchId = Request::param('searchId');

        if(Cache::get('saved_search_filters_SearchId_' . $searchId) !== FALSE)
        {
            return Cache::get('saved_search_filters_SearchId_' . $searchId);
        }

        if( ! empty($searchId) && ctype_digit($searchId) )
        {
            $sql = 'SELECT ssf.* 
                    FROM zenbu_saved_search_filters ssf
                    JOIN zenbu_saved_searches ss ON ss.id = ssf.searchId
                    WHERE ssf.searchId = ' . $searchId . '
                    AND ss.userId = ' . Session::user()->id . '
                    ORDER BY `order` ASC';

            $results = Db::rawQuery($sql);

            $output = array();

            if(count($results > 0))
            {
                foreach($results as $row)
                {
                    $output[] = $row;
                }
            }

            Cache::set('saved_search_filters_SearchId_' . $searchId, $output);

            return $output;
        }
        else
        {
            return array();
        }
    } // END getSavedSearchFilters()

    // --------------------------------------------------------------------
}