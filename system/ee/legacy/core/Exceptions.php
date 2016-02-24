<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		https://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Exceptions Class
 *
 * @package		ExpressionEngine
 * @subpackage	Core
 * @category	Core
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class EE_Exceptions {

	private $ob_level;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->ob_level = ob_get_level();
	}

	/**
	 * Exception Logger
	 *
	 * This function logs PHP generated error messages
	 *
	 * @param	string	the error severity
	 * @param	string	the error string
	 * @param	string	the error filepath
	 * @param	string	the error line number
	 * @return	string
	 */
	public function log_exception($severity, $message, $filepath, $line)
	{
		list($error_constant, $error_category) = $this->lookupSeverity($severity);

		log_message('error', 'Severity: '.$error_constant.'  --> '.$message. ' '.$filepath.' '.$line, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * 404 Page Not Found Handler
	 *
	 * @param	string
	 * @return	string
	 */
	public function show_404($page = '', $log_error = TRUE)
	{
		if (defined('REQ') && constant('REQ') == 'CP')
		{
			throw new \EllisLab\ExpressionEngine\Error\FileNotFound();
		}

		$heading = "404 Page Not Found";
		$message = "The page you requested was not found.";

		// By default we log this, but allow a dev to skip it
		if ($log_error)
		{
			log_message('error', '404 Page Not Found --> '.$page);
		}

		echo $this->show_error($heading, $message, 'error_general', 404);
		exit;
	}

	// --------------------------------------------------------------------

	/**
	 * Native PHP error handler
	 *
	 * @param	string	the error severity
	 * @param	string	the error string
	 * @param	string	the error filepath
	 * @param	string	the error line number
	 * @return	string
	 */
	public function show_php_error($severity, $message, $filepath, $line)
	{
		list($error_constant, $error_category) = $this->lookupSeverity($severity);

		$filepath = str_replace("\\", "/", $filepath);
		$filepath = str_replace(SYSPATH, '', $filepath);

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}

		ob_start();

		include(APPPATH.'errors/error_php.php');

		$buffer = ob_get_contents();
		ob_end_clean();
		echo $buffer;
	}

	// --------------------------------------------------------------------

	/**
	 * Show Error
	 *
	 * Take over CI's Error template to use the EE user error template
	 *
	 * @param	string	the heading
	 * @param	string	the message
	 * @param	string	the template
	 * @return	string
	 */
	public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{
		set_status_header($status_code);

		// Ajax Requests get a reasonable response
		if (defined('AJAX_REQUEST') && AJAX_REQUEST)
		{
			ee()->output->send_ajax_response(array(
				'error'	=> $message
			));
		}

		if (is_array($message))
		{
			$message = '<p>'.implode("</p>\n\n<p>", $message).'</p>';
		}

		// If we have the template class we can show their error template
		if (function_exists('ee') && isset(ee()->TMPL))
		{
			ee()->output->fatal_error($message);
		}

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}

		ob_start();

		include(APPPATH.'errors/'.$template.'.php');

		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;

/*
		// "safe" HTML typography in EE will strip paragraph tags, and needs newlines to indicate paragraphs
		$message = '<p>'.implode("</p>\n\n<p>", ( ! is_array($message)) ? array($message) : $message).'</p>';

		if ( ! class_exists('CI_Controller'))
		{
			// too early to do anything pretty
			exit($message);
		}

		// let's be kind if it's a submission error, and offer a back link
		if ( ! empty($_POST) && ! (defined('AJAX_REQUEST') && AJAX_REQUEST))
		{
			$message .= '<p><a href="javascript:history.go(-1);">&#171; '.ee()->lang->line('back').'</a></p>';
		}

		// Ajax Requests get a reasonable response
		if (defined('AJAX_REQUEST') && AJAX_REQUEST)
		{
			ee()->output->send_ajax_response(array(
				'error'	=> $message
			));
		}

		// Error occurred on a frontend request

		// AR DB errors can result in a memory loop on subsequent queries so we output them now
		if ($template == 'error_db')
		{
			exit($message);
		}

		// everything is in place to show the
		// custom error template
		ee()->output->fatal_error($message);
		*/
	}

	public function show_exception(\Exception $exception, $status_code = 500)
	{
		set_status_header($status_code);

		$message = $exception->getMessage();
		$location =  $exception->getFile() . ':' . $exception->getLine();
		$trace = explode("\n", $exception->getTraceAsString());

		foreach ($trace as &$line)
		{
			$path = preg_quote(SYSPATH, '/');
			$line = preg_replace('/^(#\d+\s+)'.$path.'/', '$1', $line);
		}

		$debug = DEBUG;

		// We'll only want to show certain information, like file paths, if we're allowed
		if (isset(ee()->config) && isset(ee()->session))
		{
			$debug = (bool) (DEBUG OR ee()->config->item('debug') > 1 OR ee()->session->userdata('group_id') == 1);
		}

		// Only show the file name if debug isn't on
		if ( ! $debug)
		{
			$location = array_pop(explode(DIRECTORY_SEPARATOR, $location));
		}

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}

		ob_start();

		if (defined('EE_APPPATH'))
		{
			include(EE_APPPATH.'errors/error_exception.php');
		}
		else
		{
			include(APPPATH.'errors/error_exception.php');
		}

		$buffer = ob_get_contents();
		ob_end_clean();
		echo $buffer;
		exit;
	}

	/**
	 * @return Array of [PHP Severity constant, Human severity name]
	 */
	private function lookupSeverity($severity)
	{
		switch ($severity)
		{
			case E_ERROR:
			 	return array('E_ERROR', 'Error');
			case E_WARNING:
			 	return array('E_WARNING', 'Warning');
			case E_PARSE:
			 	return array('E_PARSE', 'Error');
			case E_NOTICE:
			 	return array('E_NOTICE', 'Notice');
			case E_CORE_ERROR:
			 	return array('E_CORE_ERROR', 'Error');
			case E_CORE_WARNING:
			 	return array('E_CORE_WARNING', 'Warning');
			case E_COMPILE_ERROR:
			 	return array('E_COMPILE_ERROR', 'Error');
			case E_COMPILE_WARNING:
			 	return array('E_COMPILE_WARNING', 'Warning');
			case E_USER_ERROR:
			 	return array('E_USER_ERROR', 'Error');
			case E_USER_WARNING:
			 	return array('E_USER_WARNING', 'Warning');
			case E_USER_NOTICE:
			 	return array('E_USER_NOTICE', 'Notice');
			case E_STRICT:
			 	return array('E_STRICT', 'Notice');
			case E_RECOVERABLE_ERROR:
			 	return array('E_RECOVERABLE_ERROR', 'Error');
			case E_DEPRECATED:
			 	return array('E_DEPRECATED', 'Deprecated');
			case E_USER_DEPRECATED:
			 	return array('E_USER_DEPRECATED', 'Deprecated');
			default:
				return array('UNKNOWN', 'Error');
		}
	}

}
// END Exceptions Class

/* End of file EE_Exceptions.php */
/* Location: ./system/expressionengine/libraries/EE_Exceptions.php */
