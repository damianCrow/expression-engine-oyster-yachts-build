<?php namespace Zenbu\librairies\platform\ee;

class Lang
{
	public static function t($key, $fallback = '')
	{
        return lang($key, $fallback);
	}

	public static function load($file)
	{
		if(version_compare(APP_VER, '3.0.0', '<'))
		{
			if(is_array($file))
			{
				foreach($file as $f)
				{
					ee()->lang->loadfile($f);		
				}
			}
			else
			{
		        ee()->lang->loadfile($file);		
			}
		}
	}
}