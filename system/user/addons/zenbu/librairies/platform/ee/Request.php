<?php namespace Zenbu\librairies\platform\ee;

class Request
{
	public static function get($key)
	{
        return ee()->input->get($key);
	}

	public static function post($key, $default = FALSE)
	{
		if(ee()->input->post($key))
		{
	        return ee()->input->post($key);
		}
		else
		{
			return $default;
		}
	}

	public static function param($key, $default = FALSE)
	{
		if(ee()->input->get_post($key))
		{
	        return ee()->input->get_post($key);		
		}
		else
		{
			return $default;
		}
	}

	public static function redirect($url)
	{
		return ee()->functions->redirect($url);
	}

	public static function isAjax()
	{
		return AJAX_REQUEST;
	}
}