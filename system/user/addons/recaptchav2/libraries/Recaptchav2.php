<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* ExpressionEngine reCAPTCHA v2
*
* Replaces the built-in CAPTCHA for ExpressionEngine
* 
* @package		ExpressionEngine reCAPTCHA
* @author		Denik
* @link			http://denik.od.ua/expressionengine
* @version		1.0
* @license		
*/

class Recaptchav2
{
	// Google verify URL
	private $captcha_url = "https://www.google.com/recaptcha/api/siteverify";
	
	// Keys
	// You can define your keys here or load them from the database in a __construct function if you have a admin panel to modify them
	public  $site_key = "";
	private $secret   = "";
	
	// Error store
	public $error      = "";
	public $error_code = "";
	
	// Error responses
	private $error_responses = array(
		// Google error code reference
		'missing-input-secret'   => 'The secret parameter is missing',
		'invalid-input-secret'   => 'The secret parameter is invalid or malformed',
		'missing-input-response' => 'The response parameter is missing',
		'invalid-input-response' => 'The response parameter is invalid or malformed',
		
		// Unknown response or valid to connect to Google service
		"unknown-response"       => "We could not validate your response, please try again"
	);
	
	/**
	 * Class constructor
	 * @param array $settings site settings
	 */
	function __construct($settings = array())
	{
		// Set site settings
		$this->set_settings($settings);
	}
	
	// Validate
	public function validate($response)
	{
		// Clear current error if any
		$this->error = "";
		
		// If empty, return error now
		if(empty($response))
		{
			// Set error
			$this->set_error("missing-input-response");
			return false;
		}
		
		// Post to google server
		$fields = array(
						'secret'   => urlencode($this->secret),
						'response' => urlencode($response),
						'remoteip' => urlencode($_SERVER['REMOTE_ADDR'])
				       );
 
		// url-ify the data for the POST
		$post_string = http_build_query($fields);
 
		// Start curl
		$ch = curl_init();
		
		// Set URL, amoutn of fields and the post string
		curl_setopt($ch, CURLOPT_URL, $this->captcha_url);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		// Execute post
		$result = curl_exec($ch);
		
		// close connection
		curl_close($ch);
		
		// If result failed (NULL or FALSE)
		if( ! $result )
		{
			// Set message
			$this->set_error("unknown-response");
			return false;	
		}
		
		// JSON decode result
		$data = json_decode($result, true);

		// Check repsonse from Google
		
		// Successful reply
		if( isset($data['success']) && $data['success'] == true )
		{
			// Captcha OK
			return true;
		}
		
		// Capcha failed
		if( isset($data['error-codes']) && is_array($data['error-codes']) )
		{
			$this->set_error( current($data['error-codes']) );
		}
		else
		{
			$this->set_error("invalid-input-response");
		}

		return false;
	}
	
	/**
	 * Set site settings
	 * @param array $settings site settings
	 */
	public function set_settings($settings = array())
	{
		if( isset($settings['site_key']) ) $this->site_key = $settings['site_key'];
		if( isset($settings['secret']) )   $this->secret   = $settings['secret'];
	}

	// Set error function, good to handle unkown error types
	private function set_error($code)
	{
		// Check type is in error_responses array
		if( ! isset($this->error_responses[$code]) )
		{
			// Use default
			$code = 'unknown-response';
		}
		
		// Set error
		$this->error_code = $code;
		$this->error = $this->error_responses[$code];			
	}

}
// END CLASS