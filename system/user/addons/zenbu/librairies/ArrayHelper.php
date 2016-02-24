<?php namespace Zenbu\librairies;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ArrayHelper
{
	/**
	 * Make an array based on a key in subarrays
	 * Useful for making an array of keys in rows of data
	 * @param  string $key    The key to look into the array
	 * @param  array $arrays The original array
	 * @return array         An array of found items
	 */
	public static function make_array_of($key, $arrays)
	{
		$out = array();
		foreach($arrays as $node)
		{
			if(is_object($node) && isset($node->{$key}))
			{
				$out[] = $node->{$key};
			}

			if(is_array($node) && isset($node[$key]))
			{
				$out[] = $node[$key];					
			}
		}
		return $out;
	}

	public static function flatten_to_key_val($key, $val, $array)
	{
		$output = array();

		if( ! $array )
		{
			return $output;
		}
		
		foreach($array as $arr_key => $arr)
		{
			if(isset($arr[$key]))
			{
				$output[$arr[$key]] = isset($arr[$val]) ? $arr[$val] : null;		
			}
		}

		return $output;
	}

	public function array_filter_by($filter, $value, $array)
	{
	   $output = FALSE;

	   foreach ($array as $key => $val_array) 
	   {
	       if ($val_array[$filter] === $value) 
	       {
	           $output[] = $val_array;
	       }
	   }

	   return $output;
	}
}