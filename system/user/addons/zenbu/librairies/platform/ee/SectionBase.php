<?php namespace Zenbu\librairies\platform\ee;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Lang;

class SectionBase extends Base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getSections()
    {
        // Return data if already cached
        if($this->cache->get('channel_data'))
        {
            return $this->cache->get('channel_data');
        }

        $output = array();
        $channels = ee('Model')
                    ->get('Channel')
                    ->filter('site_id', $this->user->site_id)
                    ->all()
                    ->indexBy('channel_id');

        foreach($channels as $key => $row)
        {
            $obj                = new \StdClass();
            $obj->id            = $row->channel_id;
            $obj->channel_id    = $row->channel_id;
            $obj->name          = $row->channel_title;
            $obj->channel_title = $row->channel_title;
            $output[$key]       = $obj;
        }

        /**
        *   ===========================================
        *   Extension Hook zenbu_modify_channel_data
        *   ===========================================
        *
        *   Modifies the channel array, used for the Zenbu
        *   channel dropdown, for example
        *   @return $output     array   An array containing channel-related data
        */
        if (ee()->extensions->active_hook('zenbu_modify_channel_data') === TRUE)
        {
            $output = ee()->extensions->call('zenbu_modify_channel_data', $output);
            if (ee()->extensions->end_script === TRUE) return;
        }

        $this->cache->set('channel_data', $output);

        return $output;
	}

	public function getSubSections($section_id)
	{
		return FALSE;
	}

	public static function buildSelectOptions($sections)
	{
        $dropdown_options['sections'] = array(
            '0' => Lang::t('all_channels'),
            );

        foreach($sections as $key => $section)
        {
           $dropdown_options['sections'][$section->channel_id] = $section->channel_title;
        }

        return $dropdown_options;
	}
}