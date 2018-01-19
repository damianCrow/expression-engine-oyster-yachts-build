<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine Sitemap Module
 *
 * @package		Sitemap
 * @subpackage	Sitemap
 * @category	Sitemap
 * @author		Ben Croker
 * @link		http://www.putyourlightson.net/sitemap-module
 */


class Sitemap_tab {

	/**
	  *  Display
	  */
	public function display($channel_id, $entry_id = '')
	{
		// load language file
		ee()->lang->loadfile('sitemap');


		// check if this channel is included in the sitemap
		$query = ee()->db->query("SELECT channel_id FROM exp_sitemap WHERE channel_id = '".$channel_id."' AND included = 1");

		if (!$query->num_rows)
		{
			return array();
		}

		// set checked to true if not editing an existing entry
		$checked = !$entry_id ? TRUE : FALSE;

		$settings = array(
			'ping_sitemap' => array(
				'field_id'		=> 'ping_sitemap',
				'field_label'		=> 'Sitemap',
				'field_type'		=> 'checkboxes',
				'field_list_items'	=> array(lang('ping_search_engines') => lang('ping_search_engines')),
				'field_required' 	=> 'n',
				'field_data'		=> ($checked ? lang('ping_search_engines') : ''),
				'field_pre_populate'	=> 'n',
				'field_instructions'	=> lang('sitemap_ping_instructions'),
				'field_text_direction'	=> 'ltr'
			)
		);


		return $settings;
	}

	// --------------------------------------------------------------------

	/**
	  *  Save
	  */
	public function save($entry, $values)
	{
    		if (isset($values['ping_sitemap']) AND $values['ping_sitemap'])
    		{
        		$this->ping_sitemap();
    		}

	}

	// --------------------------------------------------------------------

	/**
	  *  Ping Sitemap
	  */
	public function ping_sitemap()
	{
		$result = '';


		// check if ping sitemap was checked
		if (!ee()->input->post('sitemap__ping_sitemap'))
		{
			return;
		}

		$results = array();

		$urls = array();

		// google
		$urls['Google'] = "http://www.google.com/webmasters/sitemaps/ping?sitemap=";

		// yahoo - have stopped their sitemap ping service
		//$urls['Yahoo'] = "http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=ee_yahoo_map_update&url=";

		// bing
		$urls['Bing'] = "http://www.bing.com/webmaster/ping.aspx?siteMap=";

		// ask.com
		//$urls['Ask'] = "http://submissions.ask.com/ping?sitemap=";

		// moreover - removed as service seems to be no longer available
		//$urls['Moreover'] = "http://api.moreover.com/ping?u=";


		foreach ($urls as $key => $url)
		{
			$url = $url.ee()->config->slash_item('site_url').'sitemap.php';

			// cURL method
			if (function_exists('curl_init'))
			{
				$results[$key] = $this->_curl_ping($url);
			}

			// fsocket method
			else
			{
				$results[$key] = $this->_socket_ping($url);
			}
		}


		$this->_confirmation_message($results);
	}

	// --------------------------------------------------------------------

	/**
	  *  Return confirmation message
	  */
	private function _confirmation_message($results)
	{
		$success_message = '';
		$failure_message = '';

		foreach ($results as $key => $result)
		{
			if ($result == '1')
			{
				$success_message .= '<b>'.$key.'</b> was successfully notified about this entry<br/>';
			}

			else if ($result == '0')
			{
				$failure_message .= 'An error was encountered while trying to notify <b>'.$key.'</b> about this entry<br/>';
			}
		}

		if ($success_message)
		{
			ee('CP/Alert')->makeInline('sitemap-confirmation-message')
				->withTitle('Sitemap')
      				->addToBody($success_message)
      				->asSuccess()
      				->defer();
		}

		if ($failure_message)
		{
			ee('CP/Alert')->makeInline('sitemap-confirmation-message')
				->withTitle('Sitemap')
      				->addToBody($failure_message )
      				->asWarning()
      				->defer();
		}
	}

	// --------------------------------------------------------------------

	/**
	  *  Use the cURL method to send ping
	  */
	private function _curl_ping($url)
	{
		$curl_handle = curl_init($url);
		curl_setopt($curl_handle, CURLOPT_HEADER, TRUE);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($curl_handle);
		curl_close($curl_handle);

		$response_code = trim(substr($response, 9, 4));

		if ($response_code == 200)
		{
			return '1';
		}

		else
		{
			return '0';
		}
	}

	// --------------------------------------------------------------------

	/**
	  *  Use the socket method to send ping
	  */
	private function _socket_ping($url)
	{
		$url = parse_url($url);

		if (!isset($url["port"]))
		{
			$url["port"] = 80;
		}

		if (!isset($url["path"]))
		{
			$url["path"] = "/";
		}

		$fp = @fsockopen($url["host"], $url["port"], $errno, $errstr, 30);

		if ($fp)
		{
			$http_request = "HEAD ".$url["path"]."?".$url["query"]." HTTP/1.1\r\n"."Host: ".$url["host"]."\r\n"."Connection: close\r\n\r\n";
			fputs($fp, $http_request);
	  		$response = fgets($fp, 1024);
			fclose($fp);

			$response_code = trim(substr($response, 9, 4));

			if ($response_code == 200)
			{
				return '1';
			}
		}

		return '0';
	}

}
// END CLASS

/* End of file tab.sitemap.php */
/* Location: ./system/expressionengine/third_party/sitemap/tab.sitemap.php */
