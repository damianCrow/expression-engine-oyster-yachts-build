<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * EEHarbor helper class
 *
 * Bridges the functionality gaps between EE versions and allows us to have one
 * code base for all EE versions. If a new version comes out, we create a new
 * eeharbor_eeX_helper.php file with the mapped functions and EEX specific syntax.
 *
 * @package         eeharbor_foundation
 * @version         1.0
 * @author          Tom Jaeger <Tom@EEHarbor.com>
 * @link            https://eeharbor.com
 * @copyright       Copyright (c) 2016, Tom Jaeger/EEHarbor
 */

// --------------------------------------------------------------------

if(!class_exists('Eeharbor_foundation'))
{
	require_once 'helpers/eeharbor_ee'.substr(APP_VER, 0, 1).'_helper.php';

	class Eeharbor_foundation {

		protected static $modules = array();

		public static function registerModule($module, $module_name)
		{
			if(isset($modules[$module]))
			{
				return $modules[$module];
			}

			$mod = new Eeharbor_helper($module, $module_name);
			self::$modules[$module] = $mod;

			return $mod;
		}

		public static function module($module)
		{
			return self::$modules[$module];
		}


	}
}

