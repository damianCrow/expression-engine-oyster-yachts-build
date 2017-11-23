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

class Recaptchav2_ext
{
	public $name			= 'reCAPTCHA v2';
	public $version			= '1.0.1';
	public $description		= "Replaces the built-in CAPTCHA";
	public $settings_exist	= 'y';
	public $docs_url		= 'http://eecoding.com/docs/recaptchav2';
	public $settings		= array();
	private $hooks			= array(
								'create_captcha_start'         => 'create_captcha_start',
								'insert_comment_start'         => 'validate_captcha',
								'member_member_register_start' => 'validate_captcha',
								'user_register_start'          => 'validate_captcha',
								'freeform_module_validate_end' => 'validate_captcha'
							);

	private $_error_msg;
	private $_languages = array(
		''       => 'Auto detect by browser',

		'ar'     => 'Arabic',
		'af'     => 'Afrikaans',
		'am'     => 'Amharic',
		'hy'     => 'Armenian',
		'az'     => 'Azerbaijani',
		'eu'     => 'Basque',
		'bn'     => 'Bengali',
		'bg'     => 'Bulgarian',
		'ca'     => 'Catalan',
		'zh-HK'  => 'Chinese (Hong Kong)',
		'zh-CN'  => 'Chinese (Simplified)',
		'zh-TW'  => 'Chinese (Traditional)',
		'hr'     => 'Croatian',
		'cs'     => 'Czech',
		'da'     => 'Danish',
		'nl'     => 'Dutch',
		'en-GB'  => 'English (UK)',
		'en'     => 'English (US)',
		'et'     => 'Estonian',
		'fil'    => 'Filipino',
		'fi'     => 'Finnish',
		'fr'     => 'French',
		'fr-CA'  => 'French (Canadian)',
		'gl'     => 'Galician',
		'ka'     => 'Georgian',
		'de'     => 'German',
		'de-AT'  => 'German (Austria)',
		'de-CH'  => 'German (Switzerland)',
		'el'     => 'Greek',
		'gu'     => 'Gujarati',
		'iw'     => 'Hebrew',
		'hi'     => 'Hindi',
		'hu'     => 'Hungarain',
		'is'     => 'Icelandic',
		'id'     => 'Indonesian',
		'it'     => 'Italian',
		'ja'     => 'Japanese',
		'kn'     => 'Kannada',
		'ko'     => 'Korean',
		'lo'     => 'Laothian',
		'lv'     => 'Latvian',
		'lt'     => 'Lithuanian',
		'ms'     => 'Malay',
		'ml'     => 'Malayalam',
		'mr'     => 'Marathi',
		'mn'     => 'Mongolian',
		'no'     => 'Norwegian',
		'fa'     => 'Persian',
		'pl'     => 'Polish',
		'pt'     => 'Portuguese',
		'pt-BR'  => 'Portuguese (Brazil)',
		'pt-PT'  => 'Portuguese (Portugal)',
		'ro'     => 'Romanian',
		'ru'     => 'Russian',
		'sr'     => 'Serbian',
		'si'     => 'Sinhalese',
		'sk'     => 'Slovak',
		'sl'     => 'Slovenian',
		'es'     => 'Spanish',
		'es-419' => 'Spanish (Latin America)',
		'sw'     => 'Swahili',
		'sv'     => 'Swedish',
		'ta'     => 'Tamil',
		'te'     => 'Telugu',
		'th'     => 'Thai',
		'tr'     => 'Turkish',
		'uk'     => 'Ukrainian',
		'ur'     => 'Urdu',
		'vi'     => 'Vietnamese',
		'zu'     => 'Zulu'
	);

	/**
	 * Class constructor
	 * @param string $settings current settings
	 */
	function __construct($settings='')
	{
		$this->settings = $settings;
	}

	/**
	 * Create captcha hook
	 * @return string
	 */
	public function create_captcha_start()
	{
		// Check settings
		if( ! $this->_validate_settings() )
		{
			ee()->extensions->end_script = TRUE;

			return $this->_error_msg;
		}

		// Create our 'fake' entry in the captcha table
		$data = array(
			'date' 			=> time(),
			'ip_address'	=> ee()->input->ip_address(),
			'word'			=> 'reCAPTCHA v2'
		);

		ee()->db->insert('captcha', $data);

		// Default recaptcha loader
		$output = "";
		
		/**
		 * Include reCAPTCHA scripts
		 */
		if( ! AJAX_REQUEST && ! ee()->session->cache(__CLASS__, 'google_api_inc') )
		{
			// Language
			$hl = isset($this->settings['lang'])&&$this->settings['lang'] ? "&hl={$this->settings['lang']}" : "";

			$output .= <<<HTML
			<script>
				var reCAPTCHAv2 = function(object){
					if( object == undefined ) object = "g-recaptcha";
					if( typeof object == 'string' ) object = window.jQuery ? jQuery("."+object) : document.getElementsByClassName(object);
					if( object.length == undefined ) object = [object];
					for( var i = 0; i<object.length; i++ )
					{
						grecaptcha.render(object[i], {
							'sitekey' : '{$this->settings['site_key']}'
						});
					}
				};
				document.reCAPTCHAv2 = reCAPTCHAv2;
			</script>
			<script src="https://www.google.com/recaptcha/api.js?onload=reCAPTCHAv2&render=explicit{$hl}" async defer></script>
HTML;
			ee()->session->set_cache(__CLASS__, 'google_api_inc', TRUE);
		}

		$output .= "<div class=\"g-recaptcha\" data-callback=\"recaptchaCallback\"></div>";

		ee()->extensions->end_script = TRUE;

		return $output;
	}

	/**
	 * Validate CAPTCHA
	 * @return type
	 */
	public function validate_captcha()
	{
		// Bail out if settings are empty or wrong
		if ( ! $this->_validate_settings())
		{
			ee()->extensions->end_script = TRUE;

			return $this->_error_msg;
		}

		// Load library
		ee()->load->library('recaptchav2', array(
			'site_key' => $this->settings['site_key'],
			'secret'   => $this->settings['secret']
		));

		// Check answer
		$response = ee()->recaptchav2->validate( ee()->input->post('g-recaptcha-response') );
		
		// Clear captcha word
		if( isset($_POST['g-recaptcha-response']) ) unset( $_POST['g-recaptcha-response'] );

		if ($response === TRUE)
		{
			// Give EE what it's looking for
			$_POST['captcha'] = 'reCAPTCHA v2';

			return;
		}

		// Ensure EE knows the captcha was invalid
		$_POST['captcha'] = '';

		// Whether the user's response was empty or just wrong, all we can do is make EE
		// think the captcha is missing, so we'll use more generic language for an error
		ee()->lang->loadfile('recaptchav2');
		ee()->lang->language['captcha_required'] = lang('recaptcha_error');

		if ($this->settings['debug'] == 'y')
		{
			ee()->lang->language['captcha_required'] .= " (".lang(ee()->recaptchav2->error_code).")";
		}

		return;
	}

	/**
	 * Extension settings
	 * @return array
	 */
	public function settings()
	{
		$settings = array(
			'site_key' 	=>  '',
			'secret' 	=>  '',
			'lang'		=> array('s',
				$this->_languages,
				'en'
			),
			'debug'	=> array('r',
				array(
					'y' => lang('yes'),
					'n' => lang('no')
				),
				'n'
			)
		);

		return $settings;
	}

	/**
	 * Check settings
	 * @return bool
	 */
	private function _validate_settings()
	{
		// Have we been configured at all?
		if( ! isset($this->settings['site_key']) || ! isset($this->settings['secret']) )
		{
			$this->_error_msg = 'reCAPTCHA v2: Not yet configured';

			return FALSE;
		}

		// Be nice
		$this->settings['site_key'] = trim($this->settings['site_key']);
		$this->settings['secret'] = trim($this->settings['secret']);
			
		// Is either key obviously invalid?
		if (strlen($this->settings['site_key'])  != 40 OR
			strlen($this->settings['secret']) != 40)
		{
			$this->_error_msg = 'reCAPTCHA: Invalid public or private key';

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Activate extension
	 * @return void
	 */
	public function activate_extension()
	{
		foreach( $this->hooks as $hook => $method )
		{
			ee()->db->insert('extensions',
				array(
					'class'        => __CLASS__,
					'method'       => $method,
					'hook'         => $hook,
					'settings'     => '',
					'priority'     => 5,
					'version'      => $this->version,
					'enabled'      => 'y'
				)
			);
		}
	}

	/**
	 * Update extension
	 * @param  string $current
	 * @return type
	 */
	public function update_extension($current = '')
	{
		return TRUE;
	}

	/**
	 * Disable extension
	 * @return void
	 */
	public function disable_extension()
	{
		ee()->db->where('class', __CLASS__);
		ee()->db->delete('extensions');
	}

}
// END CLASS