<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( ! defined('PATH_THIRD')) { define('PATH_THIRD', APPPATH . 'third_party'); };
require_once PATH_THIRD . 'zenbu/addon.setup.php';
require_once __DIR__.'/vendor/autoload.php';

use Zenbu\librairies\Settings as Settings;
use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Lang;
use Zenbu\librairies\platform\ee\Session;
use Zenbu\librairies\platform\ee\Cache;
use Zenbu\librairies\platform\ee\Url;
use Zenbu\librairies\platform\ee\View;

class Zenbu_ext extends Base {

	var $name				= ZENBU_NAME;
	var $version 			= ZENBU_VER;
	var $addon_short_name 	= 'zenbu';
	var $description		= 'Extension companion to module of the same name';
	var $settings_exist		= ZENBU_SETTINGS_EXIST;
	var $docs_url			= 'https://zenbustudio.com/software/docs/zenbu';
	var $settings        	= array();

	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	function __construct($settings='')
	{
		$this->settings			= $settings;

		//	----------------------------------------
		//	Load Session Libraries if not available
		//	(eg. in cp_js_end hook) - EE 2.6
		//	----------------------------------------

		// Get the old last_call first, just to be sure we have it
		$old_last_call = ee()->extensions->last_call;

		if ( ! isset(ee()->session) || ! isset(ee()->session->userdata) )
        {

            if (file_exists(APPPATH . 'libraries/Localize.php'))
            {
                ee()->load->library('localize');
            }

            if (file_exists(APPPATH . 'libraries/Remember.php'))
            {
                ee()->load->library('remember');
            }

            if (file_exists(APPPATH.'libraries/Session.php'))
            {
                ee()->load->library('session');
            }
        }

		parent::__construct();

        // Restore last_call
        ee()->extensions->last_call = $old_last_call;
	}

	/**
	 * send_to_addon_post_delete
	 * Hook: delete_entries_end
	 * @return void Redirection
	 */
	function send_to_addon_post_delete()
	{
		// return_to_zenbu attempts to fetch the latest rules saved in session if present
		// First, check if we're in the CP and that we're accessing the delete_entries method.
		if((ee()->uri->segment(1) == 'cp' && ee()->uri->segment(2) == 'content_edit' && ee()->uri->segment(3) == 'delete_entries') || (isset($_GET['D']) && $_GET['D'] == 'cp' && isset($_GET['C']) && $_GET['C'] == 'content_edit' && isset($_GET['M']) && $_GET['M'] == 'delete_entries'))
		{
			ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=zenbu'.AMP."return_to_zenbu=y");
		}
	}

	/**
	 * send_to_addon_post_edit
	 * Hook: update_multi_entries_start
	 * @return void 	Set redirection POST variable
	 */
	function send_to_addon_post_edit()
	{
		// Taking over redirection
		// return_to_zenbu attempts to fetch the latest rules saved in session if present
		// First, check if we're in the CP and that we're accessing the update_multi_entries routine.
		if((ee()->uri->segment(1) == 'cp' && ee()->uri->segment(2) == 'content_edit' && ee()->uri->segment(3) == 'update_multi_entries') || (isset($_GET['D']) && $_GET['D'] == 'cp' && isset($_GET['C']) && $_GET['C'] == 'content_edit' && isset($_GET['M']) && $_GET['M'] == 'delete_entries'))
		{
			unset($_POST['redirect']);
			$_POST['redirect'] = base64_encode(BASE.AMP."C=addons_modules".AMP."M=show_module_cp".AMP."module=zenbu".AMP."return_to_zenbu=y");
		}
	}

	/**
	 * replace_edit_dropdown
	 * Hook: cp_js_end
	 * @return string $output The added JS.
	 */
	function replace_edit_dropdown()
	{
		ee()->lang->loadfile('zenbu');
		ee()->lang->loadfile('content', 'cp');
		parent::init('settings');

		$output = '';
		
		// Sorry I forgot to add this, devs:
		if (ee()->extensions->last_call !== FALSE)
		{
			$output = ee()->extensions->last_call;
		}

		// Replaces the main CP nav with the addon
		if(isset($this->permissions['edit_replace']) && $this->permissions['edit_replace'] == 'y')
		{
			$output .= View::render('extension/edit_replace.js.twig');
		}

		$output .= View::render('extension/index.js.twig');

		return $output;
	}


	/**
	 * Settings Form
	 *
	 * @param	Array	Settings
	 * @return 	void
	 */
	function settings_form()
	{
		ee()->load->helper('form');
		ee()->load->library('table');

		$query = ee()->db->query("SELECT settings FROM exp_extensions WHERE class = '".__CLASS__."'");
		$license = '';

		if($query->num_rows() > 0)
		{
			foreach($query->result_array() as $result)
			{
				$settings = unserialize($result['settings']);
				if(!empty($settings))
				{
					$license = $settings['license'];
				}
			}
		}

		$vars = array();

		$vars['settings'] = array(
			'license'	=> form_input('license', $license, "size='80'"),
			);


		return View::render('extension/settings.twig', $vars);
	}

	/**
	* Save Settings
	*
	* This function provides a little extra processing and validation
	* than the generic settings form.
	*
	* @return void
	*/
	function save_settings()
	{
		if (empty($_POST))
		{
			show_error(ee()->lang->line('unauthorized_access'));
		}

		unset($_POST['submit']);

		$settings = $_POST;

		ee()->db->where('class', __CLASS__);
		ee()->db->update('extensions', array('settings' => serialize($settings)));

		ee()->session->set_flashdata(
			'message_success',
		 	ee()->lang->line('preferences_updated')
		);
	}



	function activate_extension() {

	      $data[] = array(
		        'class'      => __CLASS__,
		        'method'    => "send_to_addon_post_edit",
		        'hook'      => "update_multi_entries_start",
		        'settings'    => serialize($this->settings),
		        'priority'    => 10,
		        'version'    => $this->version,
		        'enabled'    => "y"
		      );

		  $data[] = array(
		        'class'      => __CLASS__,
		        'method'    => "send_to_addon_post_delete",
		        'hook'      => "delete_entries_end",
		        'settings'    => serialize($this->settings),
		        'priority'    => 10,
		        'version'    => $this->version,
		        'enabled'    => "y"
		      );

		  $data[] = array(
		        'class'      => __CLASS__,
		        'method'    => "replace_edit_dropdown",
		        'hook'      => "cp_js_end",
		        'settings'    => serialize($this->settings),
		        'priority'    => 100,
		        'version'    => $this->version,
		        'enabled'    => "y"
		     );

	      // insert in database
	      foreach($data as $key => $data) {
	      ee()->db->insert('exp_extensions', $data);
	      }
	  }


	  function disable_extension() {

	      ee()->db->where('class', __CLASS__);
	      ee()->db->delete('exp_extensions');
	  }

	  /**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return 	mixed	void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}

		if ($current < $this->version)
		{
			// Update to version 1.0
		}

		ee()->db->where('class', __CLASS__);
		ee()->db->update(
					'extensions',
					array('version' => $this->version)
		);
	}




}
// END CLASS