<?php

/**
 * This library is responsible for exporting Link Vault reports as XLS files.
 * @author Masuga Design
 */

class link_vault_export
{

	public function __construct()
	{
		// Load the EE logger library
		ee()->load->library('logger');

		// Load the Link Vault library
		ee()->load->add_package_path( PATH_THIRD.'link_vault' );
		ee()->load->library('link_vault_library');
	}

	// ----------------------------------------------------------------

	/**
	 * This method converts an array of data to tab-delimited text suitable
	 * for writing to an XLS file.
	 * @param array $array
	 * @return string 
	 */
	public function array_to_xls($array)
	{
		ob_start();
		$f = fopen('php://output', 'w') or show_error("Can't open php://output");
		$n = 0;		
		foreach ($array as $line)
		{
			$n++;
			if ( ! fputcsv($f, $line, "\t"))
			{
				show_error("Can't write line $n: $line");
			}
		}
		fclose($f) or show_error("Can't close php://output");
		$str = ob_get_contents();
		ob_end_clean();

		return $str;	
	
	}

	// ----------------------------------------------------------------

	/**
	 * This method writes tab-delimited, XLS-friendly content to a file.
	 * @param string $file_path
	 * @param string $xls_content
	 * @return float
	 */
	public function write_xls_to_file($file_path='', $xls_content)
	{
		// initialize bytes_written
		$bytes_written = 0;

		$file_pointer = fopen($file_path, 'w');
		if ($file_pointer)
		{
			$bytes_written = fwrite($file_pointer, $xls_content);
			fclose($file_pointer);
		}
		
		return $bytes_written;
	}

	// ----------------------------------------------------------------

	/**
	 * This method writes and serves an XLS file.
	 *
	 * @param string $file_name
	 * @param string $xls_content
	 */
	public function download($file_name='link-vault-export.xls', $xls_content='')
	{
		$header_data = array(
			'mime_type' 	=> ee()->link_vault_library->get_mime_type('xls'),
			'download_as'	=> (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) ? preg_replace('/\./', '%2e', $file_name, substr_count($file_name, '.') - 1) : $file_name
		);

		ee()->link_vault_library->serve_file($header_data, $xls_content);
	}

	// ----------------------------------------------------------------

	/**
	 * fetch_network_name
	 *
	 * This method fetches the network category name (url_title) by ID.
	 *
	 * @param Int $network_id
	 * @return String
	 */
	public function fetch_network_name($network_id)
	{
		$query = ee()->db->select('cat_url_title')->get_where('categories', array(
			'cat_id' => $network_id
		));

		return $query->num_rows() == 1 ? $query->row('cat_url_title') : '';
	}

}