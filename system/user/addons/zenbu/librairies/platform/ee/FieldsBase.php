<?php namespace Zenbu\librairies\platform\ee;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Convert;
use Zenbu\librairies\ArrayHelper;
use Zenbu\librairies\Sections;
use Zenbu\librairies;

class FieldsBase extends Base
{
	var $std_fields;

	public function __construct()
	{
        parent::__construct(__CLASS__);

		$this->std_fields = array(
            'title' => array(
                'name'         => Lang::t('title'),
                'handle'       => 'title',
                'query_handle' => 'title',
                'type'         => '-'
                ),
            'id'    => array(
                'name'         => Lang::t('ID'),
                'handle'       => 'id',
                'query_handle' => 'entry_id',
                'type'         => '-'
                ),
            'url_title' => array(
                'name'         => Lang::t('url_title'),
                'handle'       => 'url_title',
                'query_handle' => 'url_title',
                'type'         => '-'
                ),
            'channel' => array(
                'name'         => Lang::t('channel'),
                'handle'       => 'channel',
                'query_handle' => 'channel_name',
                'type'         => '-'
                ),
            'status' => array(
                'name'         => Lang::t('status'),
                'handle'       => 'status',
                'query_handle' => 'status',
                'type'         => '-'
                ),
            'author' => array(
                'name'         => Lang::t('author'),
                'handle'       => 'author',
                'query_handle' => 'author_id',
                'type'         => '-'
                ),
            'category' => array(
                'name'         => Lang::t('categories'),
                'handle'       => 'category',
                'query_handle' => 'cat_id',
                'type'         => '-'
                ),
            'entry_date' => array(
                'name'         => Lang::t('entry_date'),
                'handle'       => 'entry_date',
                'query_handle' => 'entry_date',
                'type'         => '-'
                ),
            'expiration_date' => array(
                'name'         => Lang::t('expiration_date'),
                'handle'       => 'expiration_date',
                'query_handle' => 'expiration_date',
                'type'         => '-'
                ),
            'edit_date' => array(
                'name'         => Lang::t('edit_date'),
                'handle'       => 'edit_date',
                'query_handle' => 'edit_date',
                'type'         => '-'
                ),
            );
        $this->fieldtypes = ArrayHelper::flatten_to_key_val('field_id', 'field_type', $this->getFieldsBase());
        $this->field_settings = ArrayHelper::flatten_to_key_val('field_id', 'field_settings', $this->getFieldsBase());
        $this->field_ids = ArrayHelper::make_array_of('field_id', $this->getFieldsBase());
	}

	public function getFieldsBase()
	{

        // Return data if already cached
        if($this->cache->get('field_ids'))
        {
            return $this->cache->get('field_ids');
        }

        $output = array();

        //$channel_id = ($channel_id != "") ? "AND exp_channels.channel_id = ".$channel_id : '';
        $results = ee()->db->query("/* Zenbu getFields */ \n SELECT c.channel_id,
             cf.*
             FROM exp_channels c, exp_channel_fields cf
             WHERE cf.group_id = c.field_group
             AND cf.site_id = ".$this->user->site_id . "
             ORDER BY cf.field_order ASC"
             );

        if($results->num_rows() > 0)
        {
            foreach($results->result_array() as $row)
            {
                $row['name'] = $row['field_label'];
                $field_data[$row['channel_id']][$row['field_id']] = $row;
            }

            $this->sections = new Sections();
            $sections = $this->sections->getSections();

            foreach($sections as $key => $section)
            {
                $output[Convert::string('sectionId').'_'.$section->channel_id][Convert::col('subSectionId').'_0'] = isset($field_data[$section->channel_id]) ? $this->std_fields + $field_data[$section->channel_id] : $this->std_fields;            
            }

        }

        $results->free_result();

        $this->cache->set('field_ids', $output);

        return $output;
	}

    public function loadFieldtypeClass($fieldtype)
    {
        $builtin_ft_class_name = 'Zenbu\fieldtypes\Zenbu_'.$fieldtype.'_ft';

        if(class_exists($builtin_ft_class_name))
        {
            if($this->session->getCache('builtin_ft_class_name_'.$fieldtype))
            {
                return $this->session->getCache('builtin_ft_class_name_'.$fieldtype);
            }

            $output = new $builtin_ft_class_name;
            
            $this->session->setCache('builtin_ft_class_name_'.$fieldtype, $output);
            
            return $output;
        }

        if($this->session->getCache('3rd_party_ft_class_name_'.$fieldtype))
        {
            return $this->session->getCache('3rd_party_ft_class_name_'.$fieldtype);
        }
        
        $ft_class = ucfirst($fieldtype).'_ft';    // My_field_ft

        ee()->load->library('api');
        ee()->load->helper('file'); 
        //ee()->api->instantiate('channel_fields');
        ee()->api_channel_fields->include_handler($fieldtype);

        if(! class_exists($ft_class))
        {
            return FALSE;
        }

        $output = new $ft_class;
        
        $this->session->setCache('3rd_party_ft_class_name_'.$fieldtype, $output);

        return $output;
    }

    public function getFieldsSecondFilterType()
    {
        $output = array();
        
        $fields = $this->getFieldsBase();

        foreach($fields as $channel_id => $field_array)
        {
            foreach($field_array['subSectionId_0'] as $field_id => $field)
            {
                if(isset($field['field_type']))
                {
                    $ft_object = $this->loadFieldtypeClass($field['field_type']);
                    
                    if($ft_object)
                    {
                        if(isset($ft_object->dropdown_type))
                        {
                            $output[$field_id] = $ft_object->dropdown_type;                
                        }
                    }
                }
                
            }            
        }

        return $output;
    }
}