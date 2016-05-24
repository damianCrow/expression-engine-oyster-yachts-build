<?php
/**
 * Link Vault Library
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Masuga Design
 * @link		http://www.masugadesign.com
 */

class Link_vault_library
{
	public $debug = FALSE;

	// Link Vault module settings
	public $salt           = null;
	public $hidden_folder  = null;
	public $leech_url      = null;
	public $return_url     = null;
	public $block_leeching = null;
	public $log_leeching   = null;
	public $log_links      = null;
	public $missing_url    = null;
	public $aws_access_key = null;
	public $aws_secret_key = null;
	public $aws_endpoint   = null;

	// Site & request information
	protected $site_id        = null;
	protected $site_url       = null;
	protected $basepath       = null;
	protected $host_name      = null;
	protected $referring_host = null;
	protected $remote_url     = null;

	/**
	 * An array of supported mime_types.
	 * @var array
	 */
	public $mime_types = array();

	public $custom_fields = array();

	// -------------------------------------------------------------------------------

	public function __construct()
	{
		// Fetch the current Site ID (Don't move this, Ben.)
		$this->site_id    = ee()->config->item('site_id');

		// Populate the mime_types array
		$this->mime_types = require PATH_THIRD.'link_vault/mime-types.php';

		// Load the encryption library
		ee()->load->library('encrypt');

		// Set the debug data member
		if (ee()->config->item('link_vault_debug'))
			$this->debug = (bool)ee()->config->item('link_vault_debug');

		// If debug is enabled, load the logger class
		if ( $this->debug ) {
			ee()->load->library('logger');
		}

		// Fetch the base site URL
		$this->site_url   = ee()->functions->fetch_site_index();

		// Loads the saved module settings
		$this->load_stored_settings();

		// Loads the override settings from EE->config
		$this->load_override_settings();

		// Load the defined custom fields into an array
		$this->custom_fields = $this->load_custom_fields();

		// Determine domain name of your site (ex: domain.com)
		$clean_host_name  = (substr($_SERVER['HTTP_HOST'], 0, 4) == 'www.') ? substr($_SERVER['HTTP_HOST'], 4, strlen($_SERVER['HTTP_HOST'])) : $_SERVER['HTTP_HOST'];
		$this->host_name  = rtrim($clean_host_name, '/');

		// We want the path to the public folder
		$this->basepath   = $_SERVER['DOCUMENT_ROOT'].'/';

		// Generate a random salt string if there isn't one already, but there should be one.
		if (empty($this->salt))
		{
			ee()->load->helper('string');
			$this->salt = random_string('alnum', 12);
		}
	}

	// -------------------------------------------------------------------------------

	/**
	 * Load Custom Fields
	 *
	 * This method loads an array of the current site's Link Vault custom fields.
	 *
	 * @return Array
	 */
	protected function load_custom_fields()
	{
		$custom_fields = array();
		$query = ee()->db->order_by('field_name')
			->get_where('link_vault_custom_fields', array(
			'site_id' => $this->site_id
		));
		foreach ($query->result() as $row) {
			$custom_fields[$row->field_name] = array(
				'field_label' => $row->field_label,
				'field_id' => $row->field_id
			);
		}
		return $custom_fields;
	}

	// -------------------------------------------------------------------------------

	/**
	 * Load Stored Settings
	 *
	 * This method loads the stored settings from the Link Vault module.
	 *
	 * @return void
	 */
	protected function load_stored_settings()
	{
		$query = ee()->db->get_where('link_vault_settings', array('site_id' => $this->site_id ));
		if ($query->num_rows() == 1)
		{
			$settings = $query->row_array();

			$this->hidden_folder  = $settings['hidden_folder'];   // Path to downloadable files (never revealed to users)
			$this->salt           = $settings['salt'];            // The salt string used for encryption/decryption
			$this->leech_url      = $settings['leech_url'];       // Path to and name of your leech warning page
			$this->block_leeching = $settings['block_leeching'];  // Block leech attempts?
			$this->log_leeching   = $settings['log_leeching'];    // Log leech attempts?
			$this->log_links      = $settings['log_link_clicks']; // Log encrypted link clicks
			$this->missing_url    = $settings['missing_url'];     // Where the user should be redirected when trying to download a missing file
			$this->aws_access_key = $settings['aws_access_key'];  // The Amazon Web Services access key
			$this->aws_secret_key = $settings['aws_secret_key'];  // The Amazon Web Services secret key
		}
	}

	// -------------------------------------------------------------------------------

	/**
	 * Load Override Settings
	 *
	 * This method loads any declared override settings that are set in the main config.php file.
	 *
	 * @return void
	 */
	protected function load_override_settings()
	{
		if (ee()->config->item('link_vault_salt'))
			$this->salt = ee()->config->item('link_vault_salt');

		if (ee()->config->item('link_vault_hidden_folder'))
			$this->hidden_folder = ee()->config->item('link_vault_hidden_folder');

		if (ee()->config->item('link_vault_leech_url'))
			$this->leech_url = ee()->config->item('link_vault_leech_url');

		if (ee()->config->item('link_vault_block_leeching') != '')
			$this->block_leeching = ee()->config->item('link_vault_block_leeching');

		if (ee()->config->item('link_vault_log_leeching') != '')
			$this->log_leeching = ee()->config->item('link_vault_log_leeching');

		if (ee()->config->item('link_vault_log_link_clicks') != '')
			$this->log_links = ee()->config->item('link_vault_log_link_clicks');

		if (ee()->config->item('link_vault_missing_url') != '')
			$this->missing_url = ee()->config->item('link_vault_missing_url');

		if (ee()->config->item('link_vault_aws_access_key'))
			$this->aws_access_key = ee()->config->item('link_vault_aws_access_key');

		if (ee()->config->item('link_vault_aws_secret_key'))
			$this->aws_secret_key = ee()->config->item('link_vault_aws_secret_key');

		// This setting only exists as a config variable
		if (ee()->config->item('link_vault_aws_endpoint'))
			$this->aws_endpoint = ee()->config->item('link_vault_aws_endpoint');
	}

	// -------------------------------------------------------------------------------

	/**
	 * Track Pretty URL Text
	 *
	 * This method checks to see whether a pretty URL has been inserted into the
	 * link_vault_pretty_urls table with the corresponding URL. If it has, good. If
	 * it hasn't, create a new row.
	 *
	 * @param Array $params
	 * @return String
	 */
	protected function track_pretty_url_text($params=array())
	{
		// Initialize $text with the starting text value
		$text = $params['text'];

		// Query to see if the current text is saved to a link for current site.
		$query = ee()->db->get_where('link_vault_pretty_urls', array(
			'site_id'	=> $this->site_id,
			'text'		=> $text
		));

		// If a link exists with specified text, make sure it matches current URL
		if ( $query->num_rows == 1 ) {

			// If the supplied URL doesn't match the existing link's URL, create a new row.
			if ( $query->row('url') != $params['url'] ) {

				$counter = 1;

				do {
					$text = $params['text']."$counter";
					$counter++;
					$check_query = ee()->db->get_where('link_vault_pretty_urls', array('text' => $text, 'site_id' => $this->site_id));

					if ( $check_query->num_rows() == 1 && $check_query->row('url') == $params['url'] ) {
						return $text;
					}
				}
				while ($check_query->num_rows() != 0);

				$params['text']    = $text;
				$params['site_id'] = $this->site_id;
				ee()->db->insert('link_vault_pretty_urls', $params);
			}

		} else {
			$params['site_id'] = $this->site_id;
			ee()->db->insert('link_vault_pretty_urls', $params);
		}

		return $text;
	}

	// -------------------------------------------------------------------------------

	/**
	 * download
	 *
	 * @param Array $record_data
	 * @return Boolean
	 */
	public function download( $log_record_data=array() )
	{
		// Store the referring host info in data members
		$this->set_referring_host_data();

		// Check for a file leech attempt
		if (($this->log_leeching == '1' || $this->block_leeching == '1') && $this->check_for_leech_attempt())
			$this->log_redirect_leech_attempt( $log_record_data );

		// Initialize method return status
		$return_status = FALSE;

		if ( ! isset($log_record_data['unix_time']) ) {
			$log_record_data['unix_time'] = date('U');
		}
		if ( ! isset($log_record_data['remote_ip']) ) {
			$log_record_data['remote_ip'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		}
		$log_record_data['is_link_click'] = 'n';

		// ------------------------------------------------
		// 'link_vault_download_start' @hook.
		//  - Executes before streaming the file
		//  - Extension method MUST return the log data
		//
		if (ee()->extensions->active_hook('link_vault_download_start'))
		{
			$log_record_data = ee()->extensions->call('link_vault_download_start', $log_record_data);
			if (ee()->extensions->end_script === TRUE) return;
		}
		// ------------------------------------------------

		// Forgiveness for full paths, otherwise, assume the path is relative to document root
		$file_real = file_exists($log_record_data['directory'].$log_record_data['file_name']) ? $log_record_data['directory'].$log_record_data['file_name'] : $this->basepath.$log_record_data['directory'].$log_record_data['file_name'];

		// The file exists, so let's continue
		if (file_exists($file_real) && $log_record_data['file_name'] != '' )
		{
			// Get extension of requested file
			$file_extension = strtolower(substr(strrchr($log_record_data['file_name'], "."), 1));

			// Prepares some header data, including a fix (kludge) for IE
			$header_data = array(
				'mime_type' => $this->get_mime_type($file_extension),
				'file_path'	=> $file_real,
				'file'		=> (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) ? preg_replace('/\./', '%2e', $log_record_data['file_name'], substr_count($log_record_data['file_name'], '.') - 1) : $log_record_data['file_name']
			);

			// Set the "download as" parameter if one was supplied
			$log_record_data['download_as'] = ! empty($log_record_data['download_as']) ? $log_record_data['download_as'].'.'.$file_extension : '';
			$header_data['download_as'] = $log_record_data['download_as'] != '' ? $log_record_data['download_as'] : $header_data['file'];

			// Download the file
			$this->serve_file($header_data);

			// Only log completed downloads, otherwise set the log_id to null
			$log_id = connection_status() == 0 && !connection_aborted() ? $this->log_download($log_record_data) : null;

			// ------------------------------------------------
			// 'link_vault_download_end' @hook.
			//  - Executes after the file is streamed and logged
			//
			if (ee()->extensions->active_hook('link_vault_download_end'))
			{
				$edata = ee()->extensions->call('link_vault_download_end', $log_record_data, $log_id);
				if (ee()->extensions->end_script === TRUE) return;
			}
			// ------------------------------------------------

			$return_status = TRUE;

		} else {
			if ($this->debug)
				ee()->logger->developer('Link_vault - missing file : '.$file_real);

			// File didn't exist, return FALSE
			$return_status = FALSE;
		}

		return $return_status;
	}

	// -------------------------------------------------------------------------------

	/**
	 * remote_download
	 *
	 * This method redirects to a file hosted on a remote server after
	 * logging it in the link_vault_downloads table.
	 *
	 * @param Array $log_record_data
	 * @return Void
	 */
	public function remote_download( $log_record_data=array() )
	{
		// Store the referring host info in data members
		$this->set_referring_host_data();

		if ( ! isset($log_record_data['unix_time']) ) {
			$log_record_data['unix_time'] = date('U');
		}
		if ( ! isset($log_record_data['remote_ip']) ) {
			$log_record_data['remote_ip'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		}
		$log_record_data['is_link_click'] = 'n';

		// ------------------------------------------------
		// 'link_vault_remote_download_start' @hook.
		//  - Executes before redirecting to file URL
		//  - Extension method MUST return the log data
		//
		if (ee()->extensions->active_hook('link_vault_remote_download_start'))
		{
			$log_record_data = ee()->extensions->call('link_vault_remote_download_start', $log_record_data);
			if (ee()->extensions->end_script === TRUE) return;
		}
		// ------------------------------------------------

		if ( ! empty($log_record_data['file_name']) ) {
			$log_id = $this->log_download($log_record_data);
			ee()->functions->redirect($log_record_data['file_name']);
		} else {
			$missing_redirect = $this->missing_url ? $this->missing_url : $this->site_url;
			ee()->functions->redirect($missing_redirect);
		}
	}

	// -------------------------------------------------------------------------------

	/**
	 * S3 Download
	 *
	 * This method is a module action used to get a file from S3.
	 *
	 * @param Array $log_record_data
	 * @return Void
	 */
	public function s3_download( $log_record_data=array() )
	{
		// Store the referring host info in data members
		$this->set_referring_host_data();

		// Check for a file leech attempt
		if (($this->log_leeching == '1' || $this->block_leeching == '1') && $this->check_for_leech_attempt())
			$this->log_redirect_leech_attempt();

		if ( ! isset($log_record_data['unix_time']) ) {
			$log_record_data['unix_time'] = date('U');
		}
		if ( ! isset($log_record_data['remote_ip']) ) {
			$log_record_data['remote_ip'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		}
		$log_record_data['is_link_click'] = 'n';

		// ------------------------------------------------
		// 'link_vault_s3_download_start' @hook.
		//  - Executes before streaming the file
		//  - Extension method MUST return the log data
		//
		if (ee()->extensions->active_hook('link_vault_s3_download_start'))
		{
			$log_record_data = ee()->extensions->call('link_vault_s3_download_start', $log_record_data);
			if (ee()->extensions->end_script === TRUE) return;
		}
		// ------------------------------------------------

		if ( ! class_exists('S3') ) {
			require PATH_THIRD.'link_vault/libraries/s3.php';
		}
		$s3 = new S3($this->aws_access_key, $this->aws_secret_key);
		// Override the default AWS endpoint.
		if ( ee()->config->item('link_vault_aws_endpoint') != '' ) {
			$s3->setEndpoint( ee()->config->item('link_vault_aws_endpoint') );
		}
		/*
		For incorrect system times generating invalid signatures, adjust the current time.
		As of v1.4.1, the S3 library attempts to adjust for incorrect system times automatically
		and this variable should only be used if there are still problems.
		*/
		if (is_numeric(ee()->config->item('link_vault_s3_offset'))) {
			$s3->setTimeCorrectionOffset(ee()->config->item('link_vault_s3_offset'));
		}

		if ( ! empty($log_record_data['file_name']) ) {
			$log_id = $this->log_download($log_record_data);
			$headers = ( ee()->config->item('link_vault_s3_exclude_response_header') !== TRUE ) ? array('response-content-disposition' => 'attachment') : FALSE;
			$timeout = (integer)ee()->config->item('link_vault_s3_timeout') > 0 ? ee()->config->item('link_vault_s3_timeout') : 5;
			ee()->functions->redirect( $s3->getAuthenticatedURL($log_record_data['s3_bucket'], $log_record_data['directory'].$log_record_data['file_name'], $timeout, false, true, $headers) );
		} else {
			$missing_redirect = $this->missing_url ? $this->missing_url : $this->site_url;
			ee()->functions->redirect($missing_redirect);
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
	public function follow_encrypted_url( $record_data=array() )
	{
		$this->set_referring_host_data();

		// ------------------------------------------------
		// 'link_vault_link_click_start' @hook.
		//  - Executes before streaming the file
		//  - Extension method MUST return the log data
		//
		if (ee()->extensions->active_hook('link_vault_link_click_start'))
		{
			$record_data = ee()->extensions->call('link_vault_link_click_start', $record_data);
			if (ee()->extensions->end_script === TRUE) return;
		}
		// ------------------------------------------------

		// If link click logging is enabled, log the link click
		$log_id = ($this->log_links == 1) ? $this->log_download($record_data) : null;

		if ( ! empty($record_data['file_name']) ) {
			ee()->functions->redirect($record_data['file_name']);
		} else {
			ee()->functions->redirect($this->site_url);
		}
	}

	// -------------------------------------------------------------------------------

	/**
	 * Follow Pretty URL
	 *
	 * This method fetches a URL from the link_vault_pretty_urls table based on the
	 * 'd' parameter supplied.
	 *
	 * @return void
	 */
	public function follow_pretty_url( $record_data=array() )
	{
		$this->set_referring_host_data();

		// ------------------------------------------------
		// 'link_vault_link_click_start' @hook.
		//  - Executes before streaming the file
		//  - Extension method MUST return the log data
		//
		if (ee()->extensions->active_hook('link_vault_link_click_start'))
		{
			$record_data = ee()->extensions->call('link_vault_link_click_start', $record_data);
			if (ee()->extensions->end_script === TRUE) return;
		}
		// ------------------------------------------------

		// If link click logging is enabled, log the link click
		$log_id = ($this->log_links == 1) ? $this->log_download($record_data) : null;

		if ( ! empty($record_data['file_name']) ) {
			ee()->functions->redirect($record_data['file_name']);
		} else {
			ee()->functions->redirect($this->site_url);
		}
	}

	// -------------------------------------------------------------------------------

	/**
	 * Serve File
	 *
	 * This method serves a file, of course.
	 *
	 * @param Array header_data
	 * @param Array record_data
	 * @return void
	 *
	 */
	public function serve_file($header_data, $content='')
	{
		// disable error reporting to prevent "headers already sent" errors
		$orig_error_reporting = error_reporting();
		error_reporting(0);
		set_time_limit(0);

		ob_start();
		// Prepare headers
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public", false);
		header("Content-Description: File Transfer");
		header("Content-Type: " . $header_data['mime_type']);
		header("Accept-Ranges: bytes");
		header("Content-Disposition: attachment; filename=\"" . $header_data['download_as'] . "\";");
		header("Content-Transfer-Encoding: binary");

		// Output the file contents
		if ( isset($header_data['file_path']) ) {
			header("Content-Length: " . filesize($header_data['file_path']));
			flush();
			$fp = fopen($header_data['file_path'], "r");
			while (!feof($fp))
			{
				echo fread($fp, 1024*8);
				ob_flush();
				flush();
			}
			fclose($fp);

		// Output the content passed through the second parameter
		} else {
			header("Content-Length: " . strlen($content));
			echo $content;
			ob_flush();
			flush();
		}
		ob_end_flush();

		// return error reporting preferences to original value
		error_reporting($orig_error_reporting);
	}

	// -------------------------------------------------------------------------------

	/**
	 * Fetch Action ID
	 *
	 * This method exists since we don't have access to the CP object outside the control panel
	 * also, the one in the functions class is only good for creating front-end links.
	 *
	 * @param String class_name
	 * @param String method_name
	 * @return Integer
	 */
	public function fetch_action_id($class_name='', $method_name='')
	{
		$action_id = 0;

		if ( isset(ee()->session->cache['link_vault']['action_ids'][$class_name.$method_name]) ) {
			// Fetch the action ID from the cache
			$action_id = ee()->session->cache['link_vault']['action_ids'][$class_name.$method_name];
		} else {
			// Fetch the action ID from the DB
			ee()->db->select('action_id')->from('actions')->where(array('class' => $class_name, 'method' => $method_name));
			$query = ee()->db->get();
			if ($query->num_rows() == 1) {
				$action_id = $query->row('action_id');
				ee()->session->cache['link_vault']['action_ids'][$class_name.$method_name] = $action_id;
			}

		}

		return $action_id;
	}

	// -------------------------------------------------------------------------------

	/**
	 * Set Site Information
	 *
	 * This method sets the server site information as needed within the class.  It also
	 * stores the user's referring host.
	 *
	 * @return void
	 */
	protected function set_referring_host_data()
	{
		// Determine domain name of referer site
		if(isset($_SERVER['HTTP_REFERER']))
		{
			// remove the referring protocol and the trailing slash
			$clean_referrer = rtrim(str_replace('http://', '', str_replace('https://', '', $_SERVER['HTTP_REFERER'])), '/');
			// remove the "www" subdomain if it is there
			$clean_referrer = (substr($clean_referrer, 0, 4) == 'www.') ? substr($clean_referrer, 4, strlen($clean_referrer)) : $clean_referrer;
			// remove everything after the first remaining slash, including the slash
			$clean_referrer =  (strpos($clean_referrer, '/') !== FALSE) ? rtrim( substr($clean_referrer, 0, strpos($clean_referrer, '/')), '/' ) : $clean_referrer;

			$this->referring_host = $clean_referrer;
		}

		$this->remote_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
	}

	// -------------------------------------------------------------------------------

	/**
	 * Check for Leech Attempt
	 *
	 * This method checks if the download attempt is a leech attempt.  People should not
	 * be able to create hot links to the files. It returns "true" if the attempted
	 * download is a leeched link.
	 *
	 * @param String referrer
	 * @param String host
	 * @return Bool
	 */
	protected function check_for_leech_attempt($referrer=null, $host=null)
	{
		if ($referrer == null) $referrer = $this->referring_host;
		if ($host == null) $host = $this->host_name;

		if ($host != $referrer)
			return true;
		else
			return false;
	}

	// -------------------------------------------------------------------------------

	/**
	 * Log / Redirect Leech Attempt
	 *
	 * This method inserts a log record into the 'exp_link_vault_leeches' table then
	 * redirects the user to the URL specified in the Link Vault settings.
	 *
	 * @return void
	 */
	protected function log_redirect_leech_attempt( $record_data=array() )
	{
		if ($this->log_leeching == '1')
		{
			unset($record_data['is_link_click']);
			unset($record_data['pretty_url_id']);

			// Ensure that the Site ID is populated
			if ( ( ! isset($record_data['site_id']) || empty($record_data['site_id']) ) && isset($this->site_id) ) {
				$record_data['site_id'] = $this->site_id;
			}

			// ------------------------------------------------
			// 'link_vault_log_leech_start' @hook.
			//  - Executes before logging a leech attempt
			//  - Extension method MUST return the log data
			//
			if (ee()->extensions->active_hook('link_vault_log_leech_start'))
			{
				$record_data = ee()->extensions->call('link_vault_log_leech_start', $record_data);
				if (ee()->extensions->end_script === TRUE) return;
			}
			// ------------------------------------------------

			ee()->db->insert('link_vault_leeches', $record_data);
			$log_id = ee()->db->insert_id();

			// ------------------------------------------------
			// 'link_vault_log_leech_end' @hook.
			//  - Executes after logging a leech attempt
			//
			if (ee()->extensions->active_hook('link_vault_log_leech_end'))
			{
				$edata = ee()->extensions->call('link_vault_log_leech_end', $record_data, $log_id);
				if (ee()->extensions->end_script === TRUE) return;
			}
			// ------------------------------------------------
		}

		if ($this->block_leeching == '1')
		{
			if (!empty($this->leech_url))
				ee()->functions->redirect($this->leech_url);
			else
				ee()->functions->redirect($this->site_url);
		}
	}

	// -------------------------------------------------------------------------------

	/**
	 * Distinct Download Directories
	 *
	 * This method returns the distinct download/leech directories found
	 * in the downloads/leeches table as an array to be used as <select> options.
	 *
	 * @param String $table_name (downloads | leeches)
	 * @return Array
	 */
	public function distinct_download_directory_options($table_name='downloads')
	{
		$query = ee()->db->distinct()
								->select('directory')
								->from('link_vault_'.$table_name)
								->where('directory !=', '')
								->where('directory IS NOT NULL', null, false)
								->where('is_link_click', 'n')
								->where('site_id', $this->site_id)
								->order_by('directory', 'asc')
								->get();

		$dirs = array('' => 'Select...');
		foreach ($query->result() as $row) {
			$dirs[$row->directory] = $row->directory;
		}
		return $dirs;
	}

	// -------------------------------------------------------------------------------

	/**
	 * Distinct S3 Bucket Options
	 *
	 * This method queries one of the Link Vault tables (downloads or leeches) and
	 * returns an array of distinct S3 buckets to be used as <select> options.
	 *
	 * @param String $table_name (downloads | leeches)
	 * @return Array
	 */
	public function distinct_s3_bucket_options($table_name='downloads')
	{
		$query = ee()->db->distinct()
								->select('s3_bucket')
								->from('link_vault_'.$table_name)
								->where('s3_bucket !=', '')
								->where('s3_bucket IS NOT NULL', null, false)
								->where('site_id', $this->site_id)
								->order_by('s3_bucket', 'asc')
								->get();

		$s3s = array('' => 'Select...');
		foreach ($query->result() as $row) {
			$s3s[$row->s3_bucket] = $row->s3_bucket;
		}
		return $s3s;
	}

	// -------------------------------------------------------------------------------

	/**
	 * Distinct Pretty URLs
	 *
	 * This method queries the link_vault_pretty_urls table and returns an array
	 * of distinct id => text pairs.
	 *
	 * @return Array
	 */
	public function distinct_pretty_urls()
	{
		$query = ee()->db->select('id, text')
							->from('link_vault_pretty_urls')
							->where('text !=', '')
							->where('text IS NOT NULL', null, false)
							->where('site_id', $this->site_id)
							->order_by('text', 'asc')
							->get();

		$pretty_urls = array('' => 'Select...');
		foreach ($query->result() as $row ) {
			$pretty_urls[$row->id] = $row->text;
		}
		return $pretty_urls;
	}

	// -------------------------------------------------------------------------------

	/**
	 * Log Download
	 *
	 * This method inserts a link vault log entry in to the 'exp_link_vault_downloads' table.
	 *
	 * @param Array record_data
	 * @return Integer
	 */
	public function log_download($record_data)
	{
		// Always store the directory as a relative path
		if ( ! empty($record_data['directory']) ) {
			$record_data['directory'] = $this->normalize_directory($record_data['directory']);
		}
		// Last second population in case it was omitted
		if ( ! isset($record_data['is_link_click']) || $record_data['is_link_click'] == '' ) {
			$record_data['is_link_click'] = 'n';
		}
		// Ensure that the Site ID is populated
		if ( (! isset($record_data['site_id']) || empty($record_data['site_id']) ) && isset($this->site_id) ) {
			$record_data['site_id'] = $this->site_id;
		}
		// Add the record to the downloads table
		ee()->db->insert('link_vault_downloads', $record_data);
		$log_id = ee()->db->insert_id();
		return $log_id;
	}

	// -------------------------------------------------------------------------------

	/**
	 * Encrypt
	 *
	 * This method simply encodes a string for use in Link Vault.
	 */
	public function encrypt($string='')
	{
		return ee()->encrypt->encode($string, $this->salt);
	}

	// -------------------------------------------------------------------------------

	/**
	 * Decrypt
	 *
	 * This method simply decodes an encrypted string that was encrypted in Link Vault.
	 */
	public function decrypt($string='')
	{
		return ee()->encrypt->decode($string, $this->salt);
	}

	// -------------------------------------------------------------------------------

	/**
	 * Fetch Boolean
	 *
	 * This method converts some common EE boolean-ish strings to boolean values.
	 *
	 * @param String $value
	 * @return Boolean
	 */
	public function fetch_boolean($value=FALSE)
	{
		// Common false values
		$falses = array('no', 'off', 'false', '0');

		// Common true values
		$trues = array('yes', 'on', 'true', '1');

		if ( in_array(strtolower($value), $falses) ) {
			return FALSE;
		} else if ( in_array(strtolower($value), $trues) ) {
			return TRUE;
		} else {
			return $value;
		}
	}

	// -------------------------------------------------------------------------------

	/**
	 * Row Search
	 *
	 * This method retrieves data from the downloads or leeches table. Both
	 * tables are basically the same and are only separated to prevent one
	 * enormous table.
	 *
	 * The excessive parameters are gross (especially booleans) but this method
	 * is generally wrapped by a template tag method anyway.
	 *
	 * @param array $query_data
	 * @param array $custom_fields
	 * @param boolean $cf_exact_match
	 * @param string $prefix
	 * @param boolean $prepend_columns
	 * @param boolean $count_only
	 * @return array
	 */
	public function row_search($query_data, $custom_fields, $cf_exact_match=false, $prefix='', $prepend_columns=false, $count_only=false)
	{
		// Determine the table (downloads or leeches)
		$table_short_name = isset($query_data['table']) ? $query_data['table'] : 'downloads';

		if ( $count_only ) {
			ee()->db->select('count(*) AS census');
		} else {
			// Prepend the prefix to column names if one was supplied
			if ( $prefix != '' ) {
				// Fetch all the Link Vault table columns
				$columns = ee()->db->list_fields('link_vault_'.$table_short_name);
				// Select each column with the prefix attached (if there is one)
				foreach ($columns as $column) {
				   ee()->db->select("$column AS {$prefix}$column");
				}
				// Create an alias of "url" for "file_name" columns for link clicks.
				ee()->db->select("file_name AS {$prefix}url");
			} else {
				// Select all the columns
				ee()->db->select();
				ee()->db->select("file_name AS url");
			}
		}

		ee()->db->from('link_vault_'.$table_short_name.' AS '.$table_short_name);
		ee()->db->where('site_id', $this->site_id );

		// If not querying leeches, supply is_link_click value
		if ( ! empty($query_data['is_link_click']) && $query_data['table'] != 'leeches') {
			ee()->db->where('is_link_click', $query_data['is_link_click']);
		}
		// Filter by pretty_url_id
		if ( ! empty($query_data['pretty_url_id']) ) {
			ee()->db->where('pretty_url_id', $query_data['pretty_url_id']);
		}
		// Filter by directory
		if ( ! empty($query_data['directory']) ) {
			ee()->db->where('directory', $query_data['directory']);
		}
		if ( ! empty($query_data['s3_bucket']) ) {
			ee()->db->where('s3_bucket', $query_data['s3_bucket']);
		}
		// Filter by filename
		if ( ! empty($query_data['file_name']) ) {
			ee()->db->like('file_name', $query_data['file_name'], 'both');
		}
		// Filter by URL (actually, file_name)
		if ( ! empty($query_data['url']) ) {
			ee()->db->where('file_name', $query_data['url']);
		}
		// Filter by member_id
		if ( ! empty($query_data['member_id']) ) {
			ee()->db->where('member_id', $query_data['member_id']);
		}
		// Filter by remote_ip
		if ( ! empty($query_data['remote_ip']) ) {
			ee()->db->where('remote_ip', $query_data['remote_ip']);
		}
		// Filter by pretty_url_id
		if ( ! empty($query_data['pretty_url_id']) ) {
			ee()->db->where('pretty_url_id', $query_data['pretty_url_id']);
		}
		// Filter by dates
		if ( ! empty($query_data['start_date']) ) {
			ee()->db->where('unix_time >=', $query_data['start_date']);
		}
		if ( ! empty($query_data['end_date']) ) {
			ee()->db->where('unix_time <=', $query_data['end_date']);
		}
		// Loop through the defined custom fields and add "like" conditions
		foreach ($custom_fields as $name => $value) {
			if ($cf_exact_match) {
				ee()->db->where('cf_'.$name, $value);
			} else {
				ee()->db->like('cf_'.$name, $value, 'both');
			}
		}
		// Select the census (count) value and add the Group By SQL
		if ( ! empty($query_data['group_by']) ) {
			// Determine the name for the count(*) variable.
			$count_variable = ! empty($query_data['count_variable']) ? $query_data['count_variable'] : 'census';
			// Again, the URLs are stored in the file_name column
			if ($query_data['group_by'] == 'url') {
				$query_data['group_by'] = 'file_name';
			}
			ee()->db->select('count(*) AS '.$prefix.$count_variable);
			ee()->db->group_by( $query_data['group_by'] );
		}
		// Use proper created at date field
		if ( $query_data['order_by'] == 'date' ) {
			$query_data['order_by'] = 'unix_time';
		}
		$order_by = isset($query_data['order_by']) ? $prefix.$query_data['order_by'] : 'id';
		$sort     = isset($query_data['sort']) ? $query_data['sort'] : 'asc';
		$limit    = isset($query_data['limit']) ? $query_data['limit'] : 100;
		$offset   = isset($query_data['offset']) ? $query_data['offset'] : 0;
		// Limit and sort the results
		ee()->db->order_by($order_by, $sort);
		if ( $offset ) {
			ee()->db->limit($limit, $offset);
		} else if ( is_numeric($limit) ) {
			ee()->db->limit($limit);
		}
		// Run the query
		$query = ee()->db->get();
		// Determine if we should return the count or a collection of results
		if ( $count_only ) {
			$results = $query->row('census');
		} else {
			$columns = $query->list_fields();
			$results =  $query->result_array();
			// If $prepend_columns is true, prefix the results with column name headers
			if ( $prepend_columns ) {
				array_unshift($results, $columns);
			}
		}

		return $results;
	}

	// -------------------------------------------------------------------------------

	/**
	 * This method is just like row_search but it only fetches the number of matching
	 * rows without loading all of the rows into memory.
	 * @param array $data
	 * @param array $cf_data
	 * @param boolean $cf_exact_match
	 */
	public function row_search_count($data=array(), $cf_data=array(), $cf_exact_match=false)
	{
		return $this->row_search($data, $cf_data, $cf_exact_match, '', false, true);
	}

	// -------------------------------------------------------------------------------

	/**
	 * Get MIME Type
	 *
	 * This method retrieves the proper MIME type HTTP header data based on
	 * the download file's extension.
	 *
	 * @param String file_extension
	 * @return String
	 */
	public function get_mime_type($file_extension='')
	{
		return isset($this->mime_types[$file_extension]) ? $this->mime_types[$file_extension] : "application/force-download";
	}

	// -------------------------------------------------------------------------------

	/**
	 * Normalize Directory
	 *
	 * This method formats a dir string the way Link Vault needs it.
	 *
	 * @param String $dir
	 * @return String
	 */
	public function normalize_directory($dir='')
	{
		// Remove site index from the file path
		$dir = str_replace($this->site_url.'/', '', $dir);

		// Get path relative to document root
		$dir = !file_exists(realpath($dir)) ? $dir : $this->relative_path($dir);

		// Trim any leading slash that might be there
		$dir = ltrim($dir, '/');

		// Append a trailing slash if there isn't one.
		if (substr($dir, -1) != '/')
			$dir .= '/';

		return $dir;
	}

	// -------------------------------------------------------------------------------

	/**
	 * Relative Path
	 *
	 * This method takes a full path and returns a relative path to the document root.
	 *
	 * @param String $path
	 * @return String $relative_path
	 */
	protected function relative_path($path1='', $path2='')
	{
		if ($path2 == '') {
			$path2 = $_SERVER['DOCUMENT_ROOT'];
		}

		// Initialize relative path
		$relative_path = '';

		// If the first path contains the entire second path, remove it.
		if ( stristr($path1, $path2) !== FALSE ) {
			$relative_path = str_replace($path2.'/', '', $path1);
		} else if ( strpos($path1, '/') == 0 ) {

			$f1 = explode('/', rtrim($path1, '/') );
			$f2 = explode('/', $path2);

			$mismatch_index = 0;
			foreach ($f2 as $index => $segment) {
				if ( !isset($f1[$index]) || $f1[$index] != $segment) {
					$mismatch_index = $index+1;
					break;
				}
			}

			$times = count($f2) - $mismatch_index;
			while($times >= 0) {
				$relative_path .= '../';
				$times --;
			}

			if ( $mismatch_index >= count($f2) ) {
				$relative_path .= array_pop($f1);
			}
		} else {
			$relative_path = $path1;
		}

		return $relative_path;
	}

	// -------------------------------------------------------------------------------

	/**
	 * Set Download Protocol
	 *
	 * This method uses the "link_vault_download_protocol" config variable to override
	 * the protocol used when serving the file. ( http || https )
	 *
	 * @param String site_url
	 * @return String
	 */
	protected function set_download_protocol($site_url)
	{
		$protocol = ee()->config->item('link_vault_download_protocol') ? ee()->config->item('link_vault_download_protocol') : '';

		if ($protocol != '') {
			// Strip whatever protocol is there and replace with the config variable's value
			$site_url = $protocol.'://'.str_replace('http://', '', str_replace('https://', '', $site_url));
		}

		return $site_url;
	}

	// -------------------------------------------------------------------------------
	//  @tags :        T E M P L A T E   T A G   B A S E   M E T H O D S
	// -------------------------------------------------------------------------------

	/**
	 * url
	 *
	 * This method generates an encrypted URL.
	 *
	 * @param Array $params
	 * @param Array $custom_field_params
	 * @param Boolean $encrypt
	 * @return String
	 */
	public function url($params=array(), $custom_field_params=array())
	{
		$action_id     = $this->fetch_action_id('Link_vault', 'follow_encrypted_url');

		$lv_params = array();

		// Links might come from channels too, so grab that entry_id!
		if ( ! empty($params['entry_id']) ) {
			$lv_params['entry_id'] = $params['entry_id'];
		}
		// Set the custom fields.
		if ( ! empty($custom_field_params)) {
			$lv_params['custom_fields'] = $custom_field_params;
		}
		// Initialize the return data.
		$return_url = '';
		// Construct the URL
		if ( ! empty($params['url']) ) {
			$lv_params['url'] = $params['url'];
			$return_url = $this->site_url.'?ACT='.$action_id.'&lv='.rawurlencode($this->encrypt(serialize($lv_params)));
		} else {
			$return_url = $this->site_url;
		}

		return $return_url;
	}

	// -------------------------------------------------------------------------------

	/**
	 * pretty_url
	 *
	 * This method inserts a pretty URL entry into the DB if it doesn't already exist,
	 * then constructs and returns the URL.
	 *
	 * @param Array $params
	 * @param Array $custom_field_params
	 * @return String
	 */
	public function pretty_url($params=array(), $custom_field_params=array())
	{
		ee()->load->helper('url');
		// Fetch the action ID for pretty URL processing
		$action_id = $this->fetch_action_id('Link_vault', 'follow_pretty_url');
		// Make the link text URL friendly.
		if ( isset($params['text']) && $params['text'] != '') {
			$params['text'] = url_title($params['text']);
		}
		// Check to see if this pretty URL is saved. If not, save it.
		$url_text = $this->track_pretty_url_text($params);
		// Append encrypted custom fields if there are any. Not so pretty now.
		$cf = !empty($custom_field_params) ? '&cf='.rawurlencode($this->encrypt(serialize($custom_field_params))) : '';

		return $this->site_url.'?go='.$url_text.'&ACT='.$action_id.$cf;
	}

	// -------------------------------------------------------------------------------

	/**
	 * download_count
	 *
	 * This method returns a download count for a given set of parameters.
	 *
	 * $params['entry_id']
	 * $params['file_name']
	 * $params['directory']
	 * $params['file_path']
	 * $params['table_name']
	 * $params['start_date']
	 * $params['end_date']
	 * $params['member_id']
	 *
	 * @param Array
	 */
	public function download_count($params=array(), $custom_field_params=array())
	{
		// The table short names supported by this record count method
		$allowed_tables = array('downloads', 'leeches'); // exp_link_vault_[SHORT NAME]

		// Set the table name to the default if parameter is invalid
		if (!in_array($params['table_name'], $allowed_tables)) {
			$params['table_name'] = 'downloads';
		}

		// Perform some formatting if someone passed a file path parameter.
		if ( ! empty($params['file_path']) ) {

			// Strip the site index off the file_path if it is there.;
			$file_array = explode('/', str_replace(ee()->functions->fetch_site_index(1), '', $params['file_path']) );
			$file_name  = array_pop($file_array);
			$file_path  = implode('/', $file_array);
			$directory  = $this->normalize_directory($file_path);

		} else if ( ! empty($params['file_name']) ) {

			// This is forgiveness for those that pass a full path through 'file_name' parameter.
			$file_pieces = explode('/', $params['file_name']);
			$file_name   = end($file_pieces);

			// Remove leading space from the folder if there is one, also add trailing slash if there isn't one.
			if ( ! empty($params['directory']) ) {
				$directory = $this->normalize_directory($params['directory']);
			} else {
				$directory = $this->hidden_folder;
			}
		}

		// Initialize the where conditions array
		$where = array();

		if ( ! empty($file_name) ) {
			$where['file_name'] = $file_name;
		}

		if ( ! empty($directory) ) {
			$where['directory'] = $directory;
		}

		// The download count should only return data for the current site
		$where['site_id'] = $this->site_id;

		//  Limit results by member ID
		if ( ! empty($params['member_id']) ) {
			$where['member_id'] = $params['member_id'];
		}

		// Limit results by entry ID
		if ( ! empty($params['entry_id']) ) {
			$where['entry_id'] = $params['entry_id'];
		}

		// Limit the results by custom field values
		foreach ($custom_field_params as $name => $value) {
			$where['cf_'.$name] = $value;
		}

		// Limit the results to records that were created after a certain start date
		if ( ! empty($params['start_date']) ) {
			$start_obj = new DateTime( $params['start_date'] );
			$start_date  = date_format($start_obj, 'Ymd');
			ee()->db->where('FROM_UNIXTIME(unix_time, "%Y%m%d") >=', $start_date);
		}

		// Limit the results to records that were created prior to a certain end date
		if ( ! empty($params['end_date']) ) {
			$end_obj = new DateTime( $params['end_date'] );
			$end_date  = date_format($end_obj, 'Ymd');
			ee()->db->where('FROM_UNIXTIME(unix_time, "%Y%m%d") <=', $end_date);
		}

		ee()->db->select('count(*) AS census');
		ee()->db->where($where);
		$query = ee()->db->get('link_vault_'.$params['table_name']);

		return $query->row('census');
	}

	// -------------------------------------------------------------------------------

	/**
	 * This method returns the total link click count for a URL.
	 *
	 * @param Array $params
	 * @param Array $custom_field_params
	 * @return Int
	 */
	public function click_count($params=array(), $custom_field_params=array())
	{
		$where = array();

		// Limit the results to link clicks for the current site.
		$where['site_id'] = $this->site_id;
		$where['is_link_click'] = 'y';

		// Limit the results by the specified URL (stored in file_name)
		if ( ! empty($params['url']) )
			$where['file_name'] = $params['url'];

		// Limit the results by member_id if one was specified.
		if ( ! empty($params['member_id']) )
			$where['member_id'] = $params['member_id'];

		// Limit the results by pretty_url_id if one was specified.
		if ( ! empty($params['pretty_url_id']) ) {
			$where['pretty_url_id'] = $params['pretty_url_id'];
		}

		// Limit the results by one or more Link Vault custom field values.
		foreach ($custom_field_params as $name => $value) {
			$where['cf_'.$name] = $value;
		}

		// Limit the results to link clicks on or after a particular start date.
		if ( ! empty($prams['start_date']) ) {
			$start_obj = new DateTime($params['start_date']);
			$start_date  = date_format($start_obj, 'Ymd');
			ee()->db->where('FROM_UNIXTIME(unix_time, "%Y%m%d") >=', $start_date);
		}

		// Limit the results to link clicks on or prior to a particular end date.
		if ( ! empty($params['end_date']) ) {
			$end_obj = new DateTime($params['end_date']);
			$end_date  = date_format($end_obj, 'Ymd');
			ee()->db->where('FROM_UNIXTIME(unix_time, "%Y%m%d") <=', $end_date);
		}

		ee()->db->where($where);
		$query = ee()->db->get('link_vault_downloads');

		// Return the number of rows fetched by the query
		return $query->num_rows();
	}

	// -------------------------------------------------------------------------------

	/**
	 * This method returns the file size for a specified local file.
	 *
	 * $params['directory']
	 * $params['file_name']
	 * $params['file_path']
	 *
	 * @param Aray $params
	 * @return String
	 */
	public function file_size($params=array())
	{
		// Perform some formatting if someone passed a file path parameter.
		if ( ! empty($params['file_path']) ) {

			// Strip the site index off the file_path if it is there.;
			$file_array = explode('/', $params['file_path']);
			$file_name  = array_pop($file_array);
			$file_path  = str_replace(ee()->functions->fetch_site_index(1), '', implode('/', $file_array));
			$directory  = $this->normalize_directory($file_path);

		} else if ( ! empty($params['file_name']) ) {

			// This is forgiveness for those that pass a full path through 'file_name' parameter.
			$file_pieces = explode('/', $params['file_name']);
			$file_name   = end($file_pieces);

			// Remove leading space from the folder if there is one, also add trailing slash if there isn't one.
			if ( ! empty($params['directory']) ) {
				$directory = file_exists($params['directory'].$params['file_name']) ? $params['directory'] : $this->normalize_directory($params['directory']);
			} else {
				$directory = $this->hidden_folder;
			}
		}

		// If not a full system path, prepend the basepath to the directory and file name.
		$file = file_exists($directory.$file_name) ? $directory.$file_name : $this->basepath.$directory.$file_name;

		// Initialize the return data.
		$return_data = '';

		// Determine what we will be returning based on whether or not there is a file at all.
		if (!$file_name && !$file_path) {
			$return_data = "No file specified.";
		} else if (!file_exists($file)) {
			$return_data = "File does not exist.";
		} else {
			$decimals = 2;
			$size = filesize($file);

			$units = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
			$units_count = count($units);

			for($unit = 0; $unit < $units_count && $size >= 1024; $unit++) {
				$size /= 1024;
			}

			$decimals = $unit < 2 ? 0 : $decimals;

			$return_data = number_format($size, $decimals) . " " . $units[$unit];
		}

		return $return_data;
	}

	// -------------------------------------------------------------------------------

	/**
	 * Download Link
	 *
	 * This method outputs the protected download link to the page.
	 *
	 * file_name
	 * directory
	 * file_path
	 * entry_id
	 * link_url_only
	 * action_only
	 * link_class
	 * link_text
	 * remote
	 * bucket
	 * expires
	 * expires_text
	 * encrypted
	 * download_as
	 *
	 * @param Array $params
	 * @param Array $custom_field_params
	 * @return String (HTML content)
	 */
	public function download_link($params=array(), $custom_field_params=array())
	{
		$site_index = $this->set_download_protocol( $this->site_url );
		$encrypted  = isset($params['encrypted']) ? $this->fetch_boolean($params['encrypted']) : TRUE;

		$expired = FALSE;
		if (isset($params['expires']) && $params['expires'] != '') {
			$current_time = date('U');
			if (isset($params['expires']) && strtotime($params['expires']) <= $current_time) {
				$expired = TRUE;
				$this->return_data = isset($params['expires_text']) ? $params['expires_text'] : '';
			}
		}

		if (! $expired) {

			// Initialize $the_file variable
			$the_file = '';

			// Remove everything but the file_name as long as file_path was not provided.
			if ( (! isset($params['remote']) || $params['remote'] == FALSE) &&
				isset($params['file_name']) &&
				(! isset($params['file_path']) || $params['file_path'] == '') )
			{
				// If someone misused the file_name param, clean it up for them.
				if ( strpos($params['file_name'], '/') !== FALSE  ) {
					$file_pieces = explode('/',$params['file_name']);
					$the_file = end($file_pieces);
					$params['file_name'] = $the_file;
				} else {
					$the_file = $params['file_name'];
				}
			}
			else if ( ! empty($params['file_path']) ) {
				$the_file = $params['file_path'];
				$path_pieces = explode('/', $params['file_path']);
				$params['file_name'] = end($path_pieces);
			} else {
				$the_file = $params['file_name'];
			}

			// Fetch the proper module action ID
			if ( isset($params['remote']) && $params['remote'] == TRUE ) {
				$action_id = $this->fetch_action_id('Link_vault', 'remote_download');
			} else if ( ! empty($params['bucket']) ) {
				$action_id = $this->fetch_action_id('Link_vault', 's3_download');
			} else {
				$action_id = $this->fetch_action_id('Link_vault', 'download');
			}

			$fname = isset($params['show_file_name']) && $params['show_file_name'] == TRUE ? 'file='.$params['file_name'].'&' : '';

			// Action only doesn't give the entire URL, just the ACT and the remainded of the query string.
			if ( isset($params['action_only']) && $params['action_only'] == TRUE ) {
				$url = '?'.$fname.'ACT='.$action_id;
			} else {
				$url = $site_index.'?'.$fname.'ACT='.$action_id;
			}

			// Initialize the Link Vault parameters array
			$lv_params = array();

			// Append the entry ID (e) query string parameter if an entry ID was supplied
			if ( ! empty($params['entry_id']) ) {
				$lv_params['entry_id'] = $params['entry_id'];
			}

			// Append the directory (dir) query string parameter if an entry ID was supplied.
			if ( ! empty($params['directory']) ) {
				$lv_params['directory'] = $params['directory'];
			}

			// Append the Amazon S3 bucket (b) query string parameter if one was supplied.
			if ( ! empty($params['bucket']) ) {
				$lv_params['s3_bucket'] = $params['bucket'];
			}

			// Add the "download as" value to the parameters
			if ( ! empty($params['download_as']) ) {
				$lv_params['download_as'] = $params['download_as'];
			}

			$lv_params['file'] = $the_file;

			// Serialize the Link Vault custom field parameter array and append it to the URL (if any were supplied).
			if ( ! empty($custom_field_params) ) {
				$lv_params['custom_fields'] = $custom_field_params;
			}

			$url .= '&lv='.rawurlencode($this->encrypt(serialize($lv_params)));

			// Construct the full HTML anchor element.
			$link_class = isset($params['link_class']) ? $params['link_class'] : '';
			$link_text  = isset($params['link_text']) ? $params['link_text'] : '';
			$link = '<a href="'.$url.'" class="'.$link_class.'" >'.$link_text.'</a>';

			// Determine whether to return the full link or just the URL
			if ( isset($params['link_url_only']) && $this->fetch_boolean($params['link_url_only']) )
				$this->return_data = $url;
			else
				$this->return_data = $link;
		}

		return $this->return_data;
	}

	// -------------------------------------------------------------------------------

	/**
	 * Download Button
	 *
	 * This method generates a mostly hidden form along with a "download" button for
	 * a local file download.
	 *
	 * file
	 * directory
	 * file_path
	 * entry_id
	 * button_text
	 * button_class
	 * remote
	 * action_only
	 * bucket
	 * expires
	 * expires_text
	 * download_as
	 *
	 * @param Array $params
	 * @param Array $custom_field_params
	 * @return String (HTML content)
	 */
	public function download_button($params=array(), $custom_field_params=array())
	{
		$site_index = $this->set_download_protocol( $this->site_url );

		$expired = FALSE;
		if ( ! empty($params['expires']) ) {
			$current_time = date('U');
			if (isset($params['expires']) && strtotime($params['expires']) <= $current_time) {
				$expired = TRUE;
				$this->return_data = isset($params['expires_text']) ? $params['expires_text'] : '';
			}
		}

		if (! $expired) {

			// remove everything but the file_name as long as file_path was not provided
			if ( ( ! isset($params['remote']) || $params['remote'] == FALSE) &&
				isset($params['file_name']) &&
				strpos($params['file_name'], '/') !== FALSE &&
				( ! isset($params['file_path']) || $params['file_path'] == '') )
			{
				$the_file = explode('/',$the_file);
				$the_file = end($the_file);
			}
			else if ( ! empty($params['file_path']) ) {
				$the_file = $params['file_path'];
			} else {
				$the_file = $params['file_name'];
			}

			// Fetch the proper module action ID
			if ( isset($params['remote']) && $params['remote'] == TRUE ) {
				$action_id = $this->fetch_action_id('Link_vault', 'remote_download');
			} else if ( ! empty($params['bucket']) ) {
				$action_id = $this->fetch_action_id('Link_vault', 's3_download');
			} else {
				$action_id = $this->fetch_action_id('Link_vault', 'download');
			}

			// Action only doesn't give the entire URL, just the ACT and the remainded of the query string.
			if ( isset($params['action_only']) && $this->fetch_boolean($params['action_only']) == TRUE ) {
				$form_action = '?ACT='.$action_id;
			} else {
				$form_action = $site_index.'?ACT='.$action_id;
			}

			$form_options = array(
				'action' => $form_action,
				'secure' => TRUE,
				'method' => 'post'
			);

			$lv_params = array();

			$form = ee()->functions->form_declaration($form_options);

			// Add the Entry ID to the parameters
			if ( ! empty($params['entry_id']) ) {
				$lv_params['entry_id'] = $params['entry_id'];
			}

			// Add the directory to the parameters
			if ( ( ! isset($params['remote']) || $this->fetch_boolean($params['remote']) == FALSE ) &&
				isset($params['directory']) && $params['directory'] != '' ) {
				$lv_params['directory'] = $params['directory'];
			}

			// Add the Amazon S3 bucket to the parameters
			if ( ! empty($params['bucket']) ) {
				$lv_params['s3_bucket'] = $params['bucket'];
			}

			// Add the "download as" value to the parameters
			if ( ! empty($params['download_as']) ) {
				$lv_params['download_as'] = $params['download_as'];
			}

			// Add the file to the parameters
			$lv_params['file'] = $the_file;

			// Add the custom fields array to the parameters
			if ( !empty($custom_field_params) ) {
				$lv_params['custom_fields'] = $custom_field_params;
			}

			$form .= "<input type='hidden' name='lv' value='".rawurlencode($this->encrypt(serialize($lv_params)))."' />";

			$button_text  = isset($params['button_text']) ? $params['button_text'] : '';
			$button_class = isset($params['button_class']) ? $params['button_class'] : '';

			$form .= "   <input type='submit' class='$button_class' value='$button_text' />\n";
			$form .= "</form>\n";

			$this->return_data = $form;
		}

		return $this->return_data;
	}

	// -------------------------------------------------------------------------------

	/**
	 * This method exists to check if the current EE installation is version 3.0.0 or
	 * above.
	 * @return boolean
	 */
	public function ee3()
	{
		return (defined('APP_VER') && version_compare(APP_VER, '3.0.0', '>='));
	}

}
