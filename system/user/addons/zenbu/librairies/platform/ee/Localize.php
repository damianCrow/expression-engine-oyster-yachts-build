<?php namespace Zenbu\librairies\platform\ee;

class Localize
{
	public static function now()
	{
        return ee()->localize->now();
	}

	public static function format($custom_date_format, $date)
	{
		if(version_compare(APP_VER, '2.6', '>'))
		{
			$date = ee()->localize->format_date($custom_date_format, $date);
		} else {
			$date = ee()->localize->decode_date($custom_date_format, $date);
		}

		return $date;
	}

	public static function human($date)
	{
		if(version_compare(APP_VER, '2.6', '>'))
		{
			$date = ee()->localize->human_time($date);
		} else {
			$date = ee()->localize->set_human_time($date);	
		}

		return $date;
	}
}