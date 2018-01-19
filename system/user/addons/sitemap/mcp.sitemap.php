<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine Sitemap Module
 *
 * @package		Sitemap
 * @category	Module
 * @author		Ben Croker
 * @link		http://www.putyourlightson.net/sitemap-module
 */


class Sitemap_mcp {

	// defaults
	var $default_change_frequency = 'weekly';
	var $default_priority = '0.5';


	/**
	  *  Index
	  */
	function index($message = '')
	{
		$site_id = ee()->config->item('site_id');

		ee()->load->library('table');
		ee()->load->library('javascript');
		ee()->load->helper('form');

		if (version_compare(APP_VER, '2.6.0', '>='))
		{
			ee()->view->cp_page_title = ee()->lang->line('sitemap');
		}
		else
		{
			ee()->cp->set_variable('cp_page_title', ee()->lang->line('sitemap'));
		}

		// get sitemap url from template group and name
		$sitemap_url = '';

		$query = ee()->db->query("SELECT template_name, group_name FROM exp_templates JOIN exp_template_groups ON exp_templates.group_id = exp_template_groups.group_id WHERE template_data LIKE '%{exp:sitemap:get%' AND exp_templates.site_id = '".$site_id."'");

		if ($row = $query->row())
		{
			$sitemap_url = ee()->functions->create_url($row->group_name.'/'.$row->template_name);
		}

		if ($row = $query->row())
		{
			$sitemap_url = ee()->functions->create_url($row->group_name.'/'.$row->template_name);
		}

		$vars = array(
			'site_index' => ee()->functions->fetch_site_index(1),
			'sitemap_url' => $sitemap_url,
			'newer_version_exists' => false //$this->newer_version_exists() - deprecated
		);


		/** ----------------------------------------
		/**  Locations
		/** ----------------------------------------*/

		$query = ee()->db->query("SELECT * FROM exp_sitemap WHERE channel_id = '' AND site_id = '".$site_id."'");

		if ($query->num_rows == 0)
		{
			$data = array(
				'url' => ee()->functions->fetch_site_index(1),
				'site_id' => ee()->config->item('site_id'),
				'change_frequency' => '',
				'priority' => ''
			);

			// insert new row
			ee()->db->insert('sitemap', $data);

			$query = ee()->db->query("SELECT * FROM exp_sitemap WHERE channel_id = '' AND site_id = '".$site_id."'");
		}

		$vars['locations'] = $query->result();


		/** ----------------------------------------
		/**  Channels
		/** ----------------------------------------*/

		$query = ee()->db->query("SELECT exp_channels.channel_id, channel_title, status_group, id, url, included, statuses, change_frequency, priority FROM exp_channels LEFT JOIN exp_sitemap ON exp_channels.channel_id = exp_sitemap.channel_id WHERE exp_channels.site_id = '".$site_id."'");
		$vars['channels'] = $query->result();


		// get statuses
		ee()->load->model('Status_model');
		$query = ee()->Status_model->get_statuses();
		$vars['statuses'] = $query->result();


		return ee()->load->view('index', $vars, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	  *  Update URLs
	  */
	function update_urls()
	{
		for ($i = 0; ee()->input->post('id_'.$i); $i++)
		{
			// update url
			ee()->db->query("UPDATE exp_sitemap SET url = '".ee()->input->post('url_'.$i)."', change_frequency = '".ee()->input->post('change_frequency_'.$i)."', priority = '".ee()->input->post('priority_'.$i)."' WHERE id = '".ee()->input->post('id_'.$i)."'");
		}

		ee()->session->set_flashdata('message_success', ee()->lang->line('urls_updated'));

		ee()->functions->redirect(ee('CP/URL', 'addons/settings/sitemap'));
	}

	// --------------------------------------------------------------------

	/**
	  *  Update Channels
	  */
	function update_channels()
	{
		for ($i = 0; ee()->input->post('channel_id_'.$i); $i++)
		{
			$data = array(
				'channel_id' => ee()->input->post('channel_id_'.$i),
				'url' => ee()->input->post('url_'.$i),
				'included' => ee()->input->post('included_'.$i),
				'statuses' => (ee()->input->post('statuses_'.$i) ? implode(',', ee()->input->post('statuses_'.$i)) : ''),
				'change_frequency' => ee()->input->post('change_frequency_'.$i),
				'priority' => ee()->input->post('priority_'.$i)
			);

			// update row
			if (ee()->input->post('id_'.$i) != '')
			{
				ee()->db->where('id', ee()->input->post('id_'.$i));
				ee()->db->update('sitemap', $data);
			}

			// insert new row
			else
			{
				ee()->db->insert('sitemap', $data);
			}
		}

		ee()->session->set_flashdata('message_success', ee()->lang->line('channels_updated'));

		ee()->functions->redirect(ee('CP/URL', 'addons/settings/sitemap'));
	}

	// --------------------------------------------------------------------

	/**
	  *  Create new url
	  */
	function new_url()
	{
		$data = array(
			'url' => ee()->functions->fetch_site_index(1),
			'site_id' => ee()->config->item('site_id'),
			'change_frequency' => '',
			'priority' => ''
		);

		// insert new row
		ee()->db->insert('sitemap', $data);

		ee()->functions->redirect(ee('CP/URL', 'addons/settings/sitemap'));
	}

	// --------------------------------------------------------------------

	/**
	  *  Delete url
	  */
	function delete_url()
	{
		if ($id = ee()->input->get_post('id'))
		{
			ee()->db->query("DELETE FROM exp_sitemap WHERE id = '".$id."'");
		}

		ee()->functions->redirect(ee('CP/URL', 'addons/settings/sitemap'));
	}

	// --------------------------------------------------------------------

	/**
	  *  Check if newer version exists
	  */
	function newer_version_exists()
	{
		$url = 'http://www.putyourlightson.net/index.php/projects/sitemap_version';

		// get module version
		$query = ee()->db->query("SELECT module_version FROM exp_modules WHERE module_name = 'Sitemap'");

		if (!$row = $query->row())
		{
			return FALSE;
		}

		$version = $row->module_version;

		$response = '';

		// cURL method
		if (function_exists('curl_init'))
		{
			$curl_handle = curl_init($url);
			curl_setopt($curl_handle, CURLOPT_HEADER, TRUE);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
			$response = curl_exec($curl_handle);
			curl_close($curl_handle);

			preg_match('/version="(.*?)"/i', $response, $matches);
			$latest_version = (isset($matches[1])) ? $matches[1] : 1;

			if ($latest_version > $version)
			{
				return TRUE;
			}
		}

		// file method
		else
		{
			$response = file_get_contents($url);

			preg_match('/version="(.*?)"/i', $response, $matches);
			$latest_version = (isset($matches[1])) ? $matches[1] : 1;

			if ($latest_version > $version)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

}

// END CLASS

/* End of file mcp.sitemap.php */
/* Location: ./system/expressionengine/third_party/sitemap/mcp.sitemap.php */
