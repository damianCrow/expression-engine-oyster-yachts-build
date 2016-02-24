<?php namespace Zenbu\librairies\platform\ee;

use Zenbu\librairies\platform\ee\Session;

class Cache
{
	public static function set($key, $value, $duration = 1)
	{
		if(isset(ee()->cache))
		{
	        ee()->cache->save('Zenbu_'.$key, $value, $duration);		
		}
		else
		{
			Session::setCache($key, $value);
		}
	}

	public static function get($key)
	{
		if(isset(ee()->cache))
		{
	        return ee()->cache->get('Zenbu_'.$key);
		}
		else
		{
			Session::getCache($key);	
		}
	}

	public static function delete($key)
	{
		if(isset(ee()->cache))
		{
        	ee()->cache->delete('Zenbu_'.$key);
        }
	}
}