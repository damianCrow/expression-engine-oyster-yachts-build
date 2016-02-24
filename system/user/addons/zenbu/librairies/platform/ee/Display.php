<?php namespace Zenbu\librairies\platform\ee;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Localize;
use Zenbu\librairies\platform\ee\Files;
use Zenbu\librairies\ArrayHelper;
use Zenbu\librairies\Filters;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Display extends Base
{
	public function __construct()
	{
		parent::__construct();
		parent::init('settings');
		$this->field_settings = ArrayHelper::flatten_to_key_val('fieldId', 'settings', $this->display_settings['fields']);
		$this->files = new Files();
		$this->keywords = Filters::getKeywords();
	}

	/**
	* function highlight
	* Highlights string based on keyword
	* @return	string	<span class="highlight"></span>-highlighted string 
	*/
	public function highlight($text, $field_id)
	{
		ee()->load->helper('text');
		
		if( ! $this->keywords )
		{
			return $text;
		}
		
		foreach($this->keywords as $filter => $keyword)
		{
			if($filter == $field_id)
			{
				return highlight_phrase($text, $keyword, '<span class="highlight">', '</span>');
			}
		}

		return $text;
	}


	/**
	 * function text
	 * Displays standard text, with text trimming if set
	 * @param string $data The text
	 * @param int $field_id the Field ID
	 * @param array $settings Some text settings set by user (word limit, etc)
	 * @param string $keyword Keyword text, if any
	 * @return	string	the text
	 */
	public function text($data, $field_id)
	{
		$settings = $this->field_settings[$field_id];

		$output = "&nbsp;";

		$word_limit = (isset($settings->text_option_1)) ? $settings->text_option_1 : '';
		$word_limit_entered_length = strlen($word_limit);
		if($word_limit == "0" && $word_limit_entered_length == 1) {
			return $output; // From settings page. Shot showing the field would be another way to do this
		}

		// Return a literal '0'
		if($data === '0' && strlen($data) == 1)
		{
			return '0';
		}
		
		$data = $this->ungarble($data);
		
		if((isset($settings->text_option_2) && $settings->text_option_2 == "html") || ! isset($settings->text_option_2))
		{
			
			$data = (empty($data)) ? '&nbsp;' : trim(htmlspecialchars($data));

		} else if ($settings->text_option_2 == "nohtml") {
			
			$data = (empty($data)) ? '&nbsp;' : trim(strip_tags($data));
		
		}
		
		$data_len = function_exists('mb_strlen') ? mb_strlen($data) : strlen($data);
		$ellipsis = (empty($data) || $data_len <= $word_limit) ? '' : '…';
		
		$data = $this->highlight($data, $field_id);

		if( ! empty($word_limit) && is_numeric($word_limit))
		{
			
			$output = function_exists('mb_substr') ? mb_substr($data, 0, $word_limit) : substr($data, 0, $word_limit);
			
			$output = $output.$ellipsis;
		
		} else {
		
			$output = $data;	
		
		}
		
		$output = str_replace("\n", "<br />", $output);
		return $output;
	}


	/**
	 * function date
	 * Formats and displays date based on memebr settings
	 * @param	int		$date	UNIX timestamp
	 * @param	string	$date_type	Type: unix or mysql
	 * @param	string	$custom_date_format	Custom date format set by user (eg. %Y-%m-%d)
	 * @return	string	formatted date data
	 */
	public function date($data, $field_id, $date_type = 'unix')
	{
		$settings = $this->field_settings[$field_id];

		$custom_date_format = (isset($settings->date_option_1)) ? $settings->date_option_1 : '';
		
		if(empty($data) || $data == "0")
		{
			return '&nbsp;';
		}
		
		if($date_type == "mysql")
		{
			$data = mysql_to_unix($data);
		}

		$date = ! empty($custom_date_format) ? Localize::format($custom_date_format, $data) : Localize::human($data);			
		
		$output = $date;//highlight($date, $rules, 'field_'.$field_id);

		return $output;

	}


	/**
	 * Displays file as link, or thumbnail openable by fancybox
	 * @param 	string 	$field_id 		The ID of the custom field
	 * @param 	string 	$field_data 	The filename with upload directory pointer. eg. {filedir_1}myfile.jpg
	 * @param 	array 	$upload_prefs 	Array of upload urls from database
	 * @param 	array 	$rules 			Array of filter rules (optional)
	 * @param 	array 	$settings 		Array of settings (optional)
	 * @return	string	file link
	 */	
	public function file($field_id, $field_data, $upload_prefs, $rules = array(), $settings = FALSE)
	{
		ee()->load->helper('html');
		ee()->load->helper('url');

		$upload_prefs = $this->files->upload_preferences();

		$output = NBS;
		
		preg_match('/\{filedir_([0-9]*?)\}(.*)/', $field_data, $file_data);

		if(isset($file_data[1]) && $file_data[2])
		{
			$filedir_id = $file_data[1];
			$filedata = pathinfo($file_data[2]);
			$filename = $filedata['basename'];
			$filepath = ($filedata['dirname'] == '.') ? '' : $filedata['dirname'].'/';
			$filethumb = isset($settings->file_option_1) ? '_' . $settings->file_option_1 . '/' : '_thumbs/'; // Thanks for making "thumbs" plural from 2.1.5 to 2.2 without telling anyone
			
			if($filedir_id != 0)
			{
				$output = "";
				if(isset($upload_prefs[$filedir_id]['server_path']))
				{
					if(preg_match('/(jpg|jpeg|gif|png|JPG|JPEG|GIF|PNG)/', $filename))
					{
						if(file_exists($upload_prefs[$filedir_id]['server_path']. $filepath . $filethumb . "thumb_".$filename))
						{
							$filedir = $upload_prefs[$filedir_id]['url'] . $filepath . $filethumb;
							$filename_thumb = "thumb_".$filename;
						} else {
							$filedir = $upload_prefs[$filedir_id]['url'] . $filepath . $filethumb; 
							$filename_thumb = $filename;
						}

						//	----------------------------------------
						//	Protocol-relative URLs
						//	----------------------------------------
						if(substr($filedir, 0, 2) == '//')
						{
							$protocol	= (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "https://" : "http://";
							$anchor_url	= str_replace('//', $protocol, $upload_prefs[$filedir_id]['url']) . $filepath . $filename;
							$img_url	= str_replace('//', $protocol, $filedir) . $filename_thumb;
						} else {
							$anchor_url	= $upload_prefs[$filedir_id]['url'] . $filepath . $filename;
							$img_url	= $filedir.$filename_thumb;
						}

						$output	.= anchor($anchor_url, img($img_url), 'class="fancybox" rel="#fancybox" alt="'.$filename.'" title="'.$filename.'"');

					} else {

						$filedir	= $upload_prefs[$filedir_id]['url'] . $filepath;

						//	----------------------------------------
						//	Protocol-relative URLs
						//	----------------------------------------
						if(substr($filedir, 0, 2) == '//')
						{
							$protocol	= (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "https://" : "http://";
							$filedir	= str_replace('//', $protocol, $filedir);
						}
						
						$file_label = $this->highlight($filename, $field_id);
						
						$output .= anchor($filedir . $filename, $file_label);
					}
				}
			}
		}

		return $output;
	}


	/**
	 * function display_filesize
	 *
	 * Make filesizes (in bytes) human-readable.
	 * @param  string $size The filesize (number in bytes)
	 * @return string The human-readable filesize
	 */
	public function display_filesize($size)
	{
	    $units = array(' B', ' KB', ' MB', ' GB', ' TB');

	    for ($i = 0; $size > 1000; $i++) 
	    { 
	    	$size /= 1000;
	    }

	    return round($size, 2).$units[$i];
	}


	/**
	 * function ungarble
	 *
	 * Attempts to make characters, entities, etc human-readable.
	 * @param  string $data The string before being processed
	 * @return string $data The string after being converted to something readable
	 */
	public function ungarble($data)
	{
		//	----------------------------------------
		//	Multibyte functions
		//	----------------------------------------
		if(function_exists('mb_convert_encoding'))
		{
			// Change everything to entities.
			// What's already an entity stays put,
			// what's not an entity gets turned into one.
			$data = mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8');

			// Now that we're all in entities, revert everyone to readable characters!
			$data = mb_convert_encoding($data, 'UTF-8', 'HTML-ENTITIES');
		}

		return $data;
	}
	
}