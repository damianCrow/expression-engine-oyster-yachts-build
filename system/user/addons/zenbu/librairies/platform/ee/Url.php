<?php namespace Zenbu\librairies\platform\ee;

class Url
{
	public static function cpUrl($url = '')
	{
		if(version_compare(APP_VER, '3.0.0', '>='))
		{
			return ee('CP/URL', $url);			
		}
		
		return BASE.$url;
	}

	public static function cpEditEntryUrl($entry = array())
	{
		if(empty($entry))
		{
			return BASE;
		}
		else
		{
			if(version_compare(APP_VER, '3.0.0', '>='))
			{
				return ee('CP/URL', 'publish/edit/entry/'.$entry['entry_id']);			
			}
			return BASE.AMP."C=content_publish".AMP."M=entry_form".AMP."channel_id=".$entry['channel_id'].AMP."entry_id=".$entry['entry_id'];		
		}
	}

	public static function zenbuUrl($remainder = '', $use_amp_string = FALSE)
	{
		if(version_compare(APP_VER, '3.0.0', '>='))
		{
			return ee('CP/URL', 'addons/settings/zenbu/'.$remainder);			
		}

		$amp = $use_amp_string === FALSE ? AMP : '&';
		$base = $use_amp_string === FALSE ? BASE : str_replace(AMP, '&', BASE);
		return $base.$amp."C=addons_modules".$amp."M=show_module_cp".$amp."module=zenbu".$remainder;
	}
}