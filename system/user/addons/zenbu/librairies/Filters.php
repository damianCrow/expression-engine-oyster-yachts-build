<?php namespace Zenbu\librairies;

use Craft;
use Zenbu\librairies\platform\ee\Cache;
use Zenbu\librairies\platform\ee\Base;
use Zenbu\librairies\platform\ee\Lang;
use Zenbu\librairies\platform\ee\Request;
use Zenbu\librairies\platform\ee\Session;
use Zenbu\librairies\platform\ee\Convert;
use Zenbu\librairies\platform\ee\Db;
use Zenbu\librairies\platform\ee\Categories;
use Zenbu\librairies;

class Filters
{
    var $fields;

    public function __construct()
    {
        $this->fields = new Fields();
        $this->sections = new Sections();
        $this->categories = new Categories();
    }

    public function getFirstFilterOptions()
    {
        $sections = $this->sections->getSections();
        $categories = $this->categories->getNestedCategories();

        // Using a non-numerical parameter will
        // output standard fields only, even if GET/POST present
        $default_fields = $this->fields->getFields('default');
        unset($default_fields[Convert::string('section')]); // Don't need this, we have it as the top dropdown

        foreach($default_fields as $handle => $field)
        {
            $output[0][0][$handle] = $field['name'];
        }

        foreach($sections as $key => $section)
        {
            $subsections = $this->sections->getSubSections($section->id);

            if($subsections)
            {
                foreach($subsections as $subsection)
                {
                    $filter_fields = $this->fields->getFields($section->id, $subsection->id);
                    unset($filter_fields[Convert::string('section')]); // Don't need this, we have it as the top dropdown

                    foreach($filter_fields as $handle => $field)
                    {
                        $output[$section->id][$subsection->id][$handle] = $field['name'];
                    }
                }
            }
            else
            {
                $filter_fields = $this->fields->getFields($section->id, 0);
                // Don't need this, we have it as the top dropdown
                unset($filter_fields[Convert::string('section')]); // Don't need this, we have it as the top dropdown

                // Also don't need a category option for channels
                // without category groups assigned
                if(! isset($categories[$section->id]))
                {
                    unset($filter_fields['category']);
                }

                foreach($filter_fields as $handle => $field)
                {
                    if(is_numeric($handle))
                    {
                        $output[$section->id][0][Lang::t('custom_fields')][$handle] = $field['name'];
                    }
                    elseif(strpos($handle, 'date') !== FALSE)
                    {
                        $output[$section->id][0][Lang::t('date')][$handle] = $field['name'];
                    }
                    else
                    {
                        $output[$section->id][0][$handle] = $field['name'];
                    }
                }
            }
        }

        return $output;
    }

    public static function getSecondFilterOptions()
    {
        $output[] = array(
            'is' => Lang::t('is'),
            );
        $output[] = array(
            'is'    => Lang::t('is'),
            'isnot' => Lang::t('is not'),
            );
        $output[] = array(
            'after'  => Lang::t('after'),
            'before' => Lang::t('before'),
            'on'     => Lang::t('on'),
            'range'     => Lang::t('between'),
            );
        $output[] = array(
            'contains'         => Lang::t('contains'),
            'beginswith'       => Lang::t('begins with'),
            'endswith'         => Lang::t('ends with'),
            'doesnotcontain'   => Lang::t('does not contain'),
            'doesnotbeginwith' => Lang::t('does not begin with'),
            'doesnotendwith'   => Lang::t('does not end with'),
            );
        $output[] = array(
            'contains'         => Lang::t('contains'),
            'beginswith'       => Lang::t('begins with'),
            'endswith'         => Lang::t('ends with'),
            'doesnotcontain'   => Lang::t('does not contain'),
            'doesnotbeginwith' => Lang::t('does not begin with'),
            'doesnotendwith'   => Lang::t('does not end with'),
            'isempty'          => Lang::t('is empty'),
            'isnotempty'       => Lang::t('is not empty'),
            );
         $output[] = array(
            'contains'         => Lang::t('contains'),
            'doesnotcontain'   => Lang::t('does not contain'),
            );

        return $output;
    }

    /**
     * Retrieve Show X results dropdown
     * @return array
     */
    public static function getLimitSelectOptions()
    {
        $output = array(
            '1' => Lang::t('Show') . ' 1 ' . Lang::t('result'),
            '2' => Lang::t('Show') . ' 2 ' . Lang::t('results'),
            '5'   => Lang::t('Show') . ' 5 ' . Lang::t('results'),
            '10'  => Lang::t('Show') . ' 10 ' . Lang::t('results'),
            '25'  => Lang::t('Show') . ' 25 ' . Lang::t('results'),
            '50'  => Lang::t('Show') . ' 50 ' . Lang::t('results'),
            '100' => Lang::t('Show') . ' 100 ' . Lang::t('results'),
            '200' => Lang::t('Show') . ' 200 ' . Lang::t('results'),
            '500' => Lang::t('Show') . ' 500 ' . Lang::t('results'),
            );

        return $output;

    } // END getLimitSelectOptions

    // --------------------------------------------------------------------


    /**
     * Retrieve Order By dropdown
     * @return array
     */
    public function getOrderBySelectOptions()
    {
        foreach($this->fields->std_fields as $handle => $field)
        {
            $output[0][$field['handle']] = Lang::t(strtolower($field['name']));
        }

        $sections = $this->sections->getSections();

        foreach($sections as $key => $section)
        {
            $subsections = $this->sections->getSubSections($section->id);

            if($subsections)
            {
                foreach($subsections as $subsection)
                {
                    $filter_fields = $this->fields->getFields($section->id, $subsection->id);
                    unset($filter_fields[Convert::string('section')]); // Don't need this, we have it as the top dropdown

                    foreach($filter_fields as $handle => $field)
                    {
                        $output[$section->id][$subsection->id][$handle] = $field['name'];
                    }
                }
            }
            else
            {
                $filter_fields = $this->fields->getFields($section->id, 0);
                unset($filter_fields['section']); // Don't need this, we have it as the top dropdown

                foreach($filter_fields as $handle => $field)
                {
                    if(is_numeric($handle))
                    {
                        $output[$section->id][Lang::t('custom_fields')][$handle] = $field['name'];
                    }
                    elseif(strpos($handle, 'date') !== FALSE)
                    {
                        $output[$section->id][Lang::t('date')][$handle] = $field['name'];
                    }
                    else
                    {
                        $output[$section->id][$handle] = $field['name'];
                    }
                }
            }
        }



        return $output;

    } // END getOrderBySelectOptions

    // --------------------------------------------------------------------

    /**
     * Retrueve Sort dropdown
     * @return  array
     */
    public function getSortSelectOptions()
    {
        return array('ASC' => Lang::t('Order by ascending'), 'DESC' => Lang::t('Order by descending'));
    } // END getSortSelectOptions

    // --------------------------------------------------------------------

    /**
     * Save the Saved Search in Cache for
     * future retrieval, eg. when returning to Zenbu
     * @return string JSON output
     */
    public function cacheFilters()
    {
        //  ----------------------------------------
        //  Set up variables
        //  ----------------------------------------

        $sectionId    = Request::param('sectionId', 0);
        $subSectionId = Request::param(Convert::col('subSectionId'), 0);
        $sectionId    = empty($sectionId) ? 0 : $sectionId;
        $subSectionId = empty($subSectionId) ? 0 : $subSectionId;

        $c = 0;
        $searchfilter                   = array();
        $searchfilter[$c]['filterAttribute1'] = 'sectionId';
        $searchfilter[$c]['filterAttribute2'] = 'is';
        $searchfilter[$c]['filterAttribute3'] = $sectionId;
        $c++;

        if($subSectionId != 0)
        {
            $searchfilter[$c]['filterAttribute1'] = Convert::col('subSectionId');
            $searchfilter[$c]['filterAttribute2'] = 'is';
            $searchfilter[$c]['filterAttribute3'] = $subSectionId;
            $c++;
        }

        //  ----------------------------------------
        //  Filters
        //  ----------------------------------------

        $filters = Request::param('filter');

        if( ! empty($filters) )
        {
            foreach($filters as $key => $val)
            {
                $searchfilter[$c]['filterAttribute1'] = $val['1st'];
                $searchfilter[$c]['filterAttribute2'] = $val['2nd'];
                $searchfilter[$c]['filterAttribute3'] = $val['3rd'];
                $c++;
            }
        }

        if(Request::param('limit'))
        {
            $searchfilter[$c]['filterAttribute1'] = 'limit';
            $searchfilter[$c]['filterAttribute2'] = Request::param('limit', '10');
            $searchfilter[$c]['filterAttribute3'] = '';
            $c++;
        }

        if(Request::param('orderby') && Request::param('sort'))
        {
            $searchfilter[$c]['filterAttribute1'] = 'orderby';
            $searchfilter[$c]['filterAttribute2'] = Request::param('orderby', 'title');
            $searchfilter[$c]['filterAttribute3'] = Request::param('sort', 'ASC');
        }

        //    ----------------------------------------
        //    Cache it for 10 minutes, per User ID
        //    ----------------------------------------

        Cache::set('zenbu_filter_cache_' . Session::user()->id, $searchfilter, 600);

        $output['ok'] = TRUE;

        return $output;

    } // END saveFilters()

    // --------------------------------------------------------------------


    /**
     * Check if item is present in filter rows
     * @param  string   $str                The item name to search for in filter rows
     * @param  array    $filters            The search filter rows
     * @param  int      $filterAttribute    The filter dropdown to look into (1st, 2nd or 3rd)
     * @return boolean          Item found or not
     */
    public static function isFilter($str, $filters, $filterAttribute = 1)
    {
        if(empty($str) || ! is_string($str) || ! in_array($filterAttribute, array(1,2,3)) || ! is_array($filters))
        {
            return FALSE;
        }

        foreach($filters as $key => $$filter)
        {
            if(isset($filter['filterAttribute' . $filterAttribute][$str]))
            {
                return TRUE;
            }
        }

        return FALSE;
    } // END isFilter()

    // --------------------------------------------------------------------

    /**
     * Get all keywords in filters (the 3rd filterAttribute)
     * @param  array    $filters            The search filter rows
     * @return array    $keywords          An array of search keywords
     */
    public static function getKeywords($filters = array())
    {
        if(! is_array($filters))
        {
            return FALSE;
        }

        if(empty($filters))
        {
            $filters = Request::param('filter');
            $filter1 = '1st';
            $filter3 = '3rd';

        }
        else
        {
            $filter1 = 'filterAttribute1';
            $filter3 = 'filterAttribute3';
        }

        if( ! $filters )
        {
            return FALSE;
        }

        $output = FALSE;

        $not_row_filters = array(
            Convert::string('sectionId'),
            'limit',
            'orderby'
            );

        foreach($filters as $key => $filter)
        {
            if(isset($filter[$filter1], $filter[$filter3]) && ! in_array($filter[$filter1], $not_row_filters))
            {
                $output[$filter[$filter1]] = $filter[$filter3];
            }
        }

        return $output;
    }

}
