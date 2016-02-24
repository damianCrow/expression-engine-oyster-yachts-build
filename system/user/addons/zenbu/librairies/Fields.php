<?php namespace Zenbu\librairies;

use Zenbu\librairies\platform\ee\Cache;
use Zenbu\librairies\platform\ee\Lang;
use Zenbu\librairies\platform\ee\Request;
use Zenbu\librairies\platform\ee\Session;
use Zenbu\librairies\platform\ee\Convert;
use Zenbu\librairies\platform\ee\Db;
use Zenbu\librairies\platform\ee\FieldsBase as FieldsBase;
use Zenbu\librairies;

class Fields extends FieldsBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getFields($sectionId = FALSE, $subSectionId = FALSE)
    {
        $sectionId    = $sectionId === FALSE ? Request::param(Convert::string('sectionId')) : $sectionId;
        $subSectionId = $subSectionId === FALSE ? Request::param(Convert::col('subSectionId')) : $subSectionId;
        
        if(Cache::get('get_fields_User' . Session::user()->id) !== FALSE)
        {
            $output = Cache::get('get_fields_User' . Session::user()->id);
        }
        else
        {
            $fields = new FieldsBase();
            $output = $fields->getFieldsBase();

            Cache::set('get_fields_User' . Session::user()->id, $output, 10);
        }

        if(isset($output[Convert::string('sectionId').'_'.$sectionId]))
        {
            if($subSectionId)
            {
                foreach($output[Convert::string('sectionId').'_'.$sectionId] as $etId => $array)
                {
                    $subSectionId = str_replace(Convert::col('subSectionId').'_', '', $etId);           
                }
            }
            else
            {
                $subSectionId = 0;
            }

            return $output[Convert::string('sectionId').'_'.$sectionId][Convert::col('subSectionId').'_'.$subSectionId];        
        }
        else
        {
            return $this->std_fields;
        }

    } // END getFields

    // --------------------------------------------------------------------


    public function getOrderedFields($include_nonvisible = FALSE)
    {
        $settings = new Settings();
        $all = $include_nonvisible !== FALSE ? 'all' : '';
        $settings = $settings->getDisplaySettings($all);
        $fields   = $this->getFields();

        $vars = array();

        foreach($settings['fields'] as $key => $setting)
        {
            $fieldkey = $setting[Convert::col('fieldType')] == 'field' ? $setting[Convert::col('fieldId')] : $setting[Convert::col('fieldType')];
            if(isset($fields[$fieldkey]))
            {
                $vars[$fieldkey] = $fields[$fieldkey];
            }
        }

        if($include_nonvisible !== FALSE)
        {
            foreach($fields as $fieldkey => $field_data)
            {
                if( ! isset($vars[$fieldkey]) )
                {
                    $vars[$fieldkey] = $field_data;
                }
            }
        }

        return $vars;
    } // END getOrderedFields

    // --------------------------------------------------------------------
}