<?php

require_once PATH_THIRD.'detour_pro/eeharbor_foundation.php';

class Detour_pro_ext {

	public $settings        = array();
	public $name            = 'Detour Pro';
	public $version         = '2.0.4';
	public $description     = 'Reroute urls to another URL.';
	public $settings_exist  = 'y';
	public $docs_url        = 'https://eeharbor.com/detour-pro/documentation';

	function __construct()
	{
		$this->foundation = Eeharbor_foundation::registerModule('detour_pro', 'Detour Pro');

		$this->settings = $this->foundation->getSettings();
	}


	function sessions_start()
	{
		if(isset($this->settings->url_detect) && $this->settings->url_detect == 'php')
		{
			$site_index_file = (ee()->config->item('site_index')) ? ee()->config->item('site_index') . '/' : null;
			$url = trim(str_replace($site_index_file, '', $_SERVER['REQUEST_URI']), '/');
		}
		else
		{
			$url = trim(ee()->uri->uri_string);
		}

		$url = urldecode($url);

		$sql = "SELECT detour_id, original_url, new_url, detour_method, start_date, end_date
		FROM exp_detours
		WHERE (start_date IS NULL OR start_date <= NOW())
		AND (end_date IS NULL OR end_date >= NOW())
		AND '" . ee()->db->escape_str($url) . "' LIKE REPLACE(original_url, '_', '[_') ESCAPE '['
		AND site_id = " . ee()->config->item('site_id');

		$detour = ee()->db->query($sql)->row_array();

		if(!empty($detour))
		{
			// old call to grab url tail, will remove later
			// $tail = $this->_get_tail($url, $detour['original_url']);
			$newUrl = $this->_segmentReplace($url, $detour['original_url'], $detour['new_url']);

			$site_url = (ee()->config->item('site_url')) ? rtrim(ee()->config->item('site_url'),'/') . '/' : '';
			$site_index = (ee()->config->item('site_index')) ? rtrim(ee()->config->item('site_index'),'/') . '/' : '';

			$site_index = $site_url . $site_index;

			if(isset($this->settings->hit_counter) && $this->settings->hit_counter == 'y')
			{
				// Update detours_hits table
				ee()->db->set('detour_id', $detour['detour_id']);
				ee()->db->set('hit_date', 'NOW()', FALSE);
				ee()->db->insert('detours_hits');
			}

			if($url != $newUrl)
			{
				if(substr($detour['new_url'],0,4) == 'http')
				{
					header('Location: ' . $newUrl, TRUE, $detour['detour_method']);
				}
				else
				{
					header('Location: ' . $site_index . ltrim($newUrl,'/'), TRUE, $detour['detour_method']);
				}
				$this->extensions->end_script;
				exit;
			}
		}
	}

	function settings()
	{
		$url_detect_options = array(
			'ee' => 'Expression Engine Native'
		);

		if(array_key_exists('REQUEST_URI', $_SERVER))
		{
			$url_detect_options['php'] = 'PHP $_SERVER[\'REQUEST_URI\'] ';
		}

		$settings['url_detect']		= array('s', $url_detect_options, 'ee');

		$settings['hit_counter']	= array('r', array('y' => "Yes", 'n' => "No"), 'y');

		return $settings;
	}

	function activate_extension()
	{

		ee()->db->where('class', 'Detour_ext');
		ee()->db->delete('extensions');

		ee()->load->dbforge();

		$data = array(
		  'class'       => __CLASS__,
		  'hook'        => 'sessions_start',
		  'method'      => 'sessions_start',
		  'settings'    => serialize($this->settings),
		  'priority'    => 1,
		  'version'     => $this->version,
		  'enabled'     => 'n'
		);

		ee()->functions->clear_caching('db');
		ee()->db->insert('exp_extensions', $data);
	}

	function disable_extension()
	{
		ee()->load->dbforge();

		ee()->functions->clear_caching('db');
		ee()->db->where('class', __CLASS__);
		ee()->db->delete('exp_extensions');
	}

	function update_extension($current = '')
	{
	    if ($current == '' OR $current == $this->version)
	    {
	        return FALSE;
	    }

	    if ($current < '1.0')
	    {
	        // Update to version 1.0
	    }

	    ee()->db->where('class', __CLASS__);
	    ee()->db->update(
	                'extensions',
	                array('version' => $this->version)
	    );
	}

	// Protected Methods

	protected function _site_url()
	{
		 if(array_key_exists('PATH_INFO', $_SERVER) === true)
		 {
		 	return $_SERVER['PATH_INFO'];
		 }

		 $whatToUse = basename(__FILE__);

		 return substr($_SERVER['PHP_SELF'], strpos($_SERVER['PHP_SELF'], $whatToUse) + strlen($whatToUse));
	}

	protected function _get_tail($url, $detour)
	{
		$tail = '';

		if(substr($detour,-2,2) == '%%')
		{
			$detour = substr($detour,0,-2);
			$tail = str_replace($detour, '', $url);
		}

		return $tail;
	}


	protected function _segmentReplace($url, $originalUrl, $newUrl)
	{
		$replace = $this->_headsOrTails($originalUrl);
		$segments = ee()->uri->segment_array();
		$newSegments = array();

        $originalUrlClean = trim($originalUrl, '%/');
        $newUrlClean = trim($newUrl, '%/');

		switch($replace)
		{
			case 'both':
				$newUrl = str_replace($originalUrlClean, $newUrlClean, $url);
			break;

			case 'head':
				$newUrl = str_replace($originalUrlClean, $newUrlClean, $url);
				$newUrl = substr($newUrl,0,(strpos($newUrl, $newUrlClean) + strlen($newUrlClean)));
			break;

			case 'tail':
				$newUrl = str_replace($originalUrlClean, $newUrlClean, $url);
				$newUrl = substr($newUrl,strpos($newUrl,$newUrlClean),strlen($newUrl));
			break;
		}

		return $newUrl;
	}

	protected function _headsOrTails($original_url)
	{
		if(substr($original_url,-2,2) == '%%' && substr($original_url,0,2) == '%%')
			return 'both';

		if(substr($original_url,-2,2) == '%%' && substr($original_url,0,2) != '%%')
			return 'tail';

		if(substr($original_url,-2,2) != '%%' && substr($original_url,0,2) == '%%')
			return 'head';

		return 'none';
	}
}
//END CLASS
