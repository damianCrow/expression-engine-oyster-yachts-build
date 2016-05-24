<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Link Vault Module Front End File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Masuga Design
 * @link		http://www.masugadesign.com
 */

class Link_vault {

	public $return_data;

	protected $site_url = null;

	// -------------------------------------------------------------------------------

	public function __construct()
	{
		// Load the Link Vault Library
		ee()->load->add_package_path( PATH_THIRD.'link_vault/' );
		ee()->load->library('link_vault_library');

		// Fetch the base site URL
		$this->site_url   = ee()->functions->fetch_site_index();

		// Establish these universally so they can be accessed anywhere within the class.
		$this->unix_time = time();
	}

	// -------------------------------------------------------------------------------
	//                         M O D U L E   A C T I O N S
	// -------------------------------------------------------------------------------

  	/**
  	 * Download
  	 *
     * This method initiates and logs a download attempt for a file that is
     * stored on the server. It is a modified version of what MD Downloaded
     * had.
     *
     * @return void
     */
	public function download()
	{
		$record_data = $this->parse_action_request_data();

		if ($_SERVER['QUERY_STRING'] != null) {
			$download_status = ee()->link_vault_library->download($record_data);
		}

		if ( $download_status == FALSE ) {
			// If the "Missing File" redirect URL is populated, redirect user there
			$missing_file_redirect = ee()->link_vault_library->missing_url != '' ? ee()->link_vault_library->missing_url : $this->site_url;
			ee()->functions->redirect($missing_file_redirect);
		}
	}

	// -------------------------------------------------------------------------------

	/**
	 * Remote Download
	 *
	 * This method inserts an 'exp_link_vault_downloads' record for a remote download then
	 * redirects the user to the actual file.
	 *
	 * @return void
	 */
	public function remote_download()
	{
		$log_record_data = $this->parse_action_request_data();
		$log_record_data['directory'] = '';

		if ($_SERVER['QUERY_STRING'] != null) {
			ee()->link_vault_library->remote_download($log_record_data);
		}
	}

	// -------------------------------------------------------------------------------

	/**
	 * S3 Download
	 *
	 * This method is a module action used to get a file from S3.
	 *
	 * @return Void
	 */
	public function s3_download()
	{
		$log_record_data = $this->parse_action_request_data();

		if ($_SERVER['QUERY_STRING'] != null) {
			ee()->link_vault_library->s3_download($log_record_data);
		}
	}

	// -------------------------------------------------------------------------------

	/**
	 * Follow Encrypted URL
	 *
	 * This method decrypts a URL and redirects the user to it. If logging is enabled,
	 * a link click entry is created in the link_vault_downloads table.
	 *
	 * @return void
	 */
	public function follow_encrypted_url()
	{
		$record_data = $this->parse_action_request_data();

		// This field gets populated by default, but is not tracked for link clicks.
		$record_data['directory'] = '';

		ee()->link_vault_library->follow_encrypted_url($record_data);
	}

	// -------------------------------------------------------------------------------

	public function follow_pretty_url()
	{
		$record_data = $this->parse_action_request_data();

		// This field gets populated by default, but is not tracked for link clicks.
		$record_data['directory'] = '';

		ee()->link_vault_library->follow_pretty_url($record_data);
	}

	// -------------------------------------------------------------------------------
	//                       T E M P L A T E   T A G S
	// -------------------------------------------------------------------------------

	/**
	 * This method returns the total download count based on channel 'entry_id' or 'file_name'.
	 * If no identifiers are supplied, the function returns the grand total of downloads.
	 *
	 * {exp:link_vault:download_count entry_id="1234" member_id="5" }
	 */
	public function download_count()
	{
		$params = array();

		$params['entry_id']   = ee()->TMPL->fetch_param('entry_id', '');
		$params['file_name']  = ee()->TMPL->fetch_param('file_name', '');
		$params['directory']  = ee()->TMPL->fetch_param('directory', '');
		$params['file_path']  = ee()->TMPL->fetch_param('file_path', '');
		$params['table_name'] = ee()->TMPL->fetch_param('table_name', 'downloads');
		$params['start_date'] = ee()->TMPL->fetch_param('start_date', '');
		$params['end_date']   = ee()->TMPL->fetch_param('end_date', date('Y-m-d h:i:s'));
		$params['member_id']  = ee()->TMPL->fetch_param('member_id', '');

		$custom_field_params = $this->_fetch_custom_field_template_params();

		$this->return_data = ee()->link_vault_library->download_count($params, $custom_field_params);
		return $this->return_data;
	}

	// -------------------------------------------------------------------------------

	/**
	 * This method returns the total link click count for a URL. It accepts custom field
	 * parameters (cf:field_name) to get a more specific count.
	 *
	 * {exp:link_vault:click_count url='http://mysite.com/important/page' }
	 */
	public function click_count()
	{
		$params = array();

		$params['url']           = ee()->TMPL->fetch_param('url', '');
		$params['member_id']     = ee()->TMPL->fetch_param('member_id', '');
		$params['start_date']    = ee()->TMPL->fetch_param('start_date', '');
		$params['end_date']      = ee()->TMPL->fetch_param('end_date', date('Y-m-d h:i:s'));
		$params['pretty_url_id'] = ee()->TMPL->fetch_param('pretty_url_id', '');

		$custom_field_params = $this->_fetch_custom_field_template_params();

		$this->return_data = ee()->link_vault_library->click_count($params, $custom_field_params);
		return $this->return_data;
	}

	// -------------------------------------------------------------------------------

	/**
	 * This method returns the file size for a specified local file.
	 *
	 * {exp:link_vault:file_size file="reggy.zip" directory="my/horse/files/"}
	 * {exp:link_vault:file_size file_path="my/horse/files/reggy.zip"}
	 */
	public function file_size()
	{
		$params = array();

		$params['file_name'] = ee()->TMPL->fetch_param('file_name', '');
		$params['directory'] = ee()->TMPL->fetch_param('directory', ee()->link_vault_library->hidden_folder);
		$params['file_path'] = ee()->TMPL->fetch_param('file_path', '');

		$this->return_data = ee()->link_vault_library->file_size($params);
		return $this->return_data;
	}

	// -------------------------------------------------------------------------------

	/**
	 * This method generates a mostly hidden form along with a "download" button for
	 * a local file download.
	 *
	 * {exp:link_vault:download_button file="file.zip" directory_path="/alternative/hidden/folder/"}
	 *
	 * @return string
	 */
	public function download_button()
	{
		$params = array();

		$params['file_name']    = ee()->TMPL->fetch_param('file_name', '');
		$params['download_as']  = ee()->TMPL->fetch_param('download_as', '');
		$params['directory']    = ee()->TMPL->fetch_param('directory', '');
		$params['file_path']    = ee()->TMPL->fetch_param('file_path', '');
		$params['entry_id']     = ee()->TMPL->fetch_param('entry_id', '');
		$params['button_text']  = ee()->TMPL->fetch_param('text', 'Download');
		$params['button_class'] = ee()->TMPL->fetch_param('class', '');
		$params['remote']       = ee()->link_vault_library->fetch_boolean( ee()->TMPL->fetch_param('remote', FALSE) );
		$params['action_only']  = ee()->link_vault_library->fetch_boolean( ee()->TMPL->fetch_param('action_only', FALSE) );
		$params['bucket']       = ee()->TMPL->fetch_param('s3_bucket', '');
		$params['expires']      = ee()->TMPL->fetch_param('expires', '');
		$params['expires_text'] = ee()->TMPL->fetch_param('expires_text', '');

		$custom_field_params = $this->_fetch_custom_field_template_params();

		$this->return_data = ee()->link_vault_library->download_button($params, $custom_field_params);
		return $this->return_data;
	}

	// -------------------------------------------------------------------------------

	/**
	 * This method outputs the protected download link to the page.
	 * {exp:link_vault:download_link file="file.zip" link_url_only="yes" }
	 * @return string
	 */
	public function download_link()
	{
		// Fetch the params
		$params['file_name']      = ee()->TMPL->fetch_param('file_name', '');
		$params['download_as']    = ee()->TMPL->fetch_param('download_as', '');
		$params['directory']      = ee()->TMPL->fetch_param('directory', '');
		$params['file_path']      = ee()->TMPL->fetch_param('file_path', '');
		$params['entry_id']       = ee()->TMPL->fetch_param('entry_id', '');
		$params['link_url_only']  = ee()->link_vault_library->fetch_boolean( ee()->TMPL->fetch_param('url_only', FALSE) );
		$params['action_only']    = ee()->link_vault_library->fetch_boolean( ee()->TMPL->fetch_param('action_only', FALSE) );
		$params['link_class']     = ee()->TMPL->fetch_param('class', '');
		$params['link_text']      = ee()->TMPL->fetch_param('text', 'Download');
		$params['remote']         = ee()->link_vault_library->fetch_boolean( ee()->TMPL->fetch_param('remote', FALSE) );
		$params['bucket']         = ee()->TMPL->fetch_param('s3_bucket', '');
		$params['expires']        = ee()->TMPL->fetch_param('expires', '');
		$params['expires_text']   = ee()->TMPL->fetch_param('expires_text', '');
		$params['show_file_name'] = ee()->link_vault_library->fetch_boolean( ee()->TMPL->fetch_param('show_file_name', FALSE) );

		$custom_field_params = $this->_fetch_custom_field_template_params();

		$this->return_data = ee()->link_vault_library->download_link($params, $custom_field_params);
		return $this->return_data;
	}

	// -------------------------------------------------------------------------------

	/**
	 * This method outputs the protected download URL only.
	 * <a href="{exp:link_vault:download_url file="file.zip" }" >Get it now!</a>
	 * @return string
	 */
	public function download_url()
	{
		ee()->TMPL->tagparams['url_only'] = true;
		return $this->download_link();
	}

	// -------------------------------------------------------------------------------

	/**
	 * This method outputs the protected download module action query string only.
	 * <a href="/{exp:link_vault:download_action file="file.zip" }" >Get it now!</a>
	 * @return string
	 */
	public function download_action()
	{
		ee()->TMPL->tagparams['url_only'] = true;
		ee()->TMPL->tagparams['action_only'] = true;
		return $this->download_link();
	}

	// -------------------------------------------------------------------------------

	/**
	 * This template tag accepts a single 'url' parameter and returns the encrypted
	 * Link Vault URL.
	 * {exp:link_vault:url url='http://example.com'}
	 * @return string
	 */
	public function url()
	{
		$params = array();

		$params['url']       = ee()->TMPL->fetch_param('url', '');
		$params['entry_id']  = ee()->TMPL->fetch_param('entry_id', '');
		$custom_field_params = $this->_fetch_custom_field_template_params();

		$this->return_data = ee()->link_vault_library->url($params, $custom_field_params);
		return $this->return_data;
	}

	// -------------------------------------------------------------------------------

	/**
	 * This tag creates a trackable Link Vault link that is not encrypted.
	 * {exp:link_vault:pretty_url url='http://masugadesign.com' text='Masuga Design Website'}
	 * http://site.com?d=masuga-design-website&ACT=57
	 * @return string
	 */
	public function pretty_url()
	{
		$params = array();

		$params['url']       = ee()->TMPL->fetch_param('url', '');
		$params['text']	     = ee()->TMPL->fetch_param('text', '');
		$custom_field_params = $this->_fetch_custom_field_template_params();

		$this->return_data = ee()->link_vault_library->pretty_url($params, $custom_field_params);
		return $this->return_data;
	}

	// -------------------------------------------------------------------------------

	/**
	 * This method queries the downloads or leeches table based on table column->value
	 * parameters and returns the rows.
	 * @return String (view data)
	 */
	public function records()
	{
		// Report filter POST parameters
		$data['table']          = ee()->TMPL->fetch_param('table');
		$data['directory']      = ee()->TMPL->fetch_param('directory');
		$data['file_name']      = ee()->TMPL->fetch_param('file_name');
		$data['url']            = ee()->TMPL->fetch_param('url');
		$data['member_id']      = ee()->TMPL->fetch_param('member_id');
		$data['remote_ip']      = ee()->TMPL->fetch_param('remote_ip');
		$data['pretty_url_id']  = ee()->TMPL->fetch_param('pretty_url_id');
		$data['start_date']     = ee()->TMPL->fetch_param('start_date') ? strtotime( ee()->TMPL->fetch_param('start_date').' 00:00:00' ) : '';
		$data['end_date']       = ee()->TMPL->fetch_param('end_date') ? strtotime( ee()->TMPL->fetch_param('end_date').' 23:59:59' ) : '';
		$data['group_by']       = ee()->TMPL->fetch_param('group_by');
		$data['order_by']       = ee()->TMPL->fetch_param('order_by', 'unix_time');
		$data['sort']           = ee()->TMPL->fetch_param('sort', 'desc');
		$data['limit']          = ee()->TMPL->fetch_param('limit', 100);
		$data['count_variable'] = ee()->TMPL->fetch_param('count_variable', '');
		$prefix                 = ee()->TMPL->fetch_param('variable_prefix', '');

		// Downloads and link clicks are actually in the same table, so we take that into account here
		if ($data['table'] == 'link_clicks') {
			$data['table'] = 'downloads';
			$data['is_link_click'] = 'y';
		// If "downloads" was manually specified for the table parameter, do not include redirect link clicks.
		} elseif ($data['table'] == 'downloads') {
			$data['table'] = 'downloads';
			$data['is_link_click'] = 'n';
		// Nothing was specified, choose "downloads" but don't distinguish between downloads and link clicks.
		} elseif (! $data['table']) {
			$data['table'] = 'downloads';
		}
		// Lastly, the value may be "leeches". We don't need to do anything special for that.

		// Fetch all the custom field template tag parameter values
		$custom_field_data = $this->_fetch_custom_field_template_params();
		$records_data = ee()->link_vault_library->row_search($data, $custom_field_data, TRUE, $prefix);
		$this->return_data = !empty($records_data) ? ee()->TMPL->parse_variables( ee()->TMPL->tagdata, $records_data) : $this->no_results($prefix);
		return $this->return_data;
	}

	// -------------------------------------------------------------------------------
	//                         O T H E R   M E T H O D S
	// -------------------------------------------------------------------------------

	/**
	 * This method parses the query string or POST data and prepares other needed data
	 * for a Link Vault module action.
	 * @param Boolean $encrypted
	 * @return Array
	 */
	protected function parse_action_request_data($encrypted=TRUE)
	{
		$request_data = array();

		// Detect people accessing the module actions with no parameters
		if ( ee()->input->get_post('lv') == '' && ee()->input->get_post('go') == '') {
			ee()->functions->redirect(ee()->functions->fetch_site_index());
		}
		// Parse the lv parameter
		$lv = $encrypted ? unserialize(ee()->link_vault_library->decrypt(rawurldecode(ee()->input->get_post('lv', TRUE)))) : unserialize(rawurldecode(ee()->input->get_post('lv', TRUE)));
		// Fetch the file
		$file = isset($lv['file']) ? $lv['file'] : '';
		// Store the entry ID
		$request_data['entry_id'] = isset($lv['entry_id']) ? $lv['entry_id'] : 0;
		// Fetch the Amazon S3 bucket
		$request_data['s3_bucket'] = isset($lv['s3_bucket']) ? $lv['s3_bucket'] : '';
		// Fetch the "download as" value for renaming the file during the download
		$request_data['download_as'] = isset($lv['download_as']) ? $lv['download_as'] : '';
		// The hidden folder may be overridden or ignored if we are dealing with an S3 file.
		$fallback_folder = $request_data['s3_bucket'] != '' ? '' : ee()->link_vault_library->hidden_folder;
		$hidden_folder = isset($lv['directory']) ? $lv['directory'] : $fallback_folder;
		// Fetch the URL
		$url = isset($lv['url']) ? $lv['url'] : '';
		// Fetch the Link Vault custom fields
		$custom_fields = isset($lv['custom_fields']) ? $lv['custom_fields'] : array();
		// Prepare the custom field columns
		if (is_array($custom_fields) && !empty($custom_fields)) {
			foreach($custom_fields as $name => $value) {
				$request_data['cf_'.$name] = $value;
			}
		}
		// Fetch the Pretty URL parameter
		if ( ee()->input->get_post('go') != '' ) {
			$link_text = ee()->input->get_post('go', TRUE);
			$query = ee()->db->get_where('link_vault_pretty_urls', array(
				'text'	=> $link_text
			));
			$request_data['pretty_url_id'] = $query->num_rows() == 1 ? $query->row('id') : 0;
			$url                           = $query->num_rows() == 1 ? $query->row('url') : '';
		}
		// In case user entered a path value in either file_path or file_name parameter for a non-remote download, divide the parts
		if (strpos($file, '/') !== FALSE && ee()->input->get_post('ACT') != ee()->link_vault_library->fetch_action_id('Link_vault', 'remote_download')) {
			$file          = str_replace( ee()->functions->fetch_site_index(1), '', $file );
			$file_array    = explode('/', $file);
			$file          = array_pop($file_array);
			$hidden_folder = implode('/', $file_array).'/';
		}
		// Set the data members
		$request_data['site_id']       = ee()->config->item('site_id');
		$request_data['directory']     = $hidden_folder;
		$request_data['file_name']     = ($url != '') ? $url : $file;
		$request_data['is_link_click'] = ($url != '') ? 'y' : 'n';
		$request_data['unix_time']     = date('U');
		$request_data['remote_ip']     = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		// Set the member ID
		$request_data['member_id']  = ( isset(ee()->session->userdata) && ee()->session->userdata('member_id') != '' ) ? ee()->session->userdata('member_id') : '0';

		return $request_data;
	}

	// -------------------------------------------------------------------------------

	/**
	 * This method loops through all the supplied template tag parameters and constructs
	 * an associative array of name => value pairs where the name starts with 'cf:'.
	 * @return array
	 */
	protected function _fetch_custom_field_template_params()
	{
		$custom_field_params = array();
		foreach (ee()->link_vault_library->custom_fields as $name => $attrs) {
			$field_value = ee()->TMPL->fetch_param('cf:'.$name, '');
			if ($field_value) {
				$custom_field_params[$name] = $field_value;
			}
		}
		return $custom_field_params;
	}

	// -------------------------------------------------------------------------------

	/**
	 * A custom no_results parser so it won't conflict with other no_results tags
	 * on the template when nested in an entries loop.
	 * @param String $prefix
	 * @return String (HTML content)
	 */
	protected function no_results($prefix='')
	{
		if ( $prefix && preg_match("/".LD."if {$prefix}no_results".RD."(.*?)".LD.preg_quote('/', '/')."if".RD."/s", ee()->TMPL->tagdata, $match)) {
			return $match[1];
		} else {
			return ee()->TMPL->no_results();
		}
	}

	// -------------------------------------------------------------------------------

	/**
	 * This is an alternative error handler that can be used for debugging.
	 * @param integer $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param integer $errline
	 * @param array $errcontext
	 * @return boolean
	 */
	public function error_handler($errno, $errstr, $errfile='', $errline='', $errcontext=array())
	{
		$EE =& get_instance();
		$EE->load->library('logger');
		$EE->logger->developer('Link Vault S3 Error : ['.$errno.'] '.$errstr);
		return true;
	}

}
/* End of file mod.link_vault.php */
