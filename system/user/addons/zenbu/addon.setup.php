<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__.'/vendor/autoload.php';

use Zenbu\librairies\platform\ee\Lang;

Lang::load(array('zenbu'));

$config['name']           = Lang::t('zenbu_module_name');
$config['version']        = '2.0.0';
$config['description']    = Lang::t('zenbu_module_description');
$config['author']         = 'Nicolas Bottari - Zenbu Studio';
$config['author_url']     = 'https://zenbustudio.com/software/zenbu';
$config['docs_url']       = 'https://zenbustudio.com/software/docs/zenbu';
$config['namespace']      = 'Zenbu';
$config['settings_exist'] = TRUE;

if( ! defined('ZENBU_VER') )
{
	define('ZENBU_VER', $config['version']);
	define('ZENBU_NAME', $config['name']);
	define('ZENBU_DESCRIPTION', $config['description']);
	define('ZENBU_SETTINGS_EXIST', $config['settings_exist']);
}

return $config;