<?php namespace Zenbu\librairies\platform\ee;

use Twig_Loader_Filesystem;

class View
{
	public static function includeJs($file)
	{
		if(REQ != 'CP')
		{
			return;
		}

		if(is_array($file))
		{
			foreach($file as $f)
			{
				ee()->cp->add_to_foot('<script type="text/javascript" src="'.self::themesPath().'/'.$f.'"></script>');
			}
		}
		else
		{
			ee()->cp->add_to_foot('<script type="text/javascript" src="'.self::themesPath().'/'.$file.'"></script>');
		}
	}

	public static function includeCss($file)
	{
		if(REQ != 'CP')
		{
			return;
		}

		if(is_array($file))
		{
			foreach($file as $f)
			{
				ee()->cp->add_to_head('<link type="text/css" rel="stylesheet" href="'.self::themesPath().'/'.$f.'" />');
			}
		}
		else
		{
			ee()->cp->add_to_head('<link type="text/css" rel="stylesheet" href="'.self::themesPath().'/'.$file.'" />');
		}
	}

	public static function themesPath()
	{
		if(defined('URL_THIRD_THEMES'))
		{
			return URL_THIRD_THEMES.'zenbu';

		} else {

			return ee()->config->item('theme_folder_url').'third_party/zenbu';
		}
	}

	public static function render($path, $vars = array())
	{
		$vars['request'] = new \Zenbu\librairies\platform\ee\Request();
		$vars['session'] = new \Zenbu\librairies\platform\ee\Session();
		$vars['localize'] = new \Zenbu\librairies\platform\ee\Localize();
		$vars['convert'] = new \Zenbu\librairies\platform\ee\Convert();
		$vars['array_helper'] = new \Zenbu\librairies\ArrayHelper();
		$vars['user'] = $vars['session']->user();

		require_once PATH_THIRD.'zenbu/vendor/twig/twig/lib/Twig/Autoloader.php';
		\Twig_Autoloader::register();

		$loader = new Twig_Loader_Filesystem(PATH_THIRD.'zenbu/views');
		$twig = new \Twig_Environment($loader, array(
		    'cache' => FALSE,//PATH_THIRD.'/../cache',
		    'debug' => TRUE,
		    'autoescape' => FALSE,
		    'auto_reload' => TRUE,
		));
		$twig->addExtension(new \Twig_Extension_Debug());
		$twig->addFunction('form_dropdown', new \Twig_Function_Function('form_dropdown'));
		$twig->addFunction('form_checkbox', new \Twig_Function_Function('form_checkbox'));
		$twig->addFunction('form_radio', new \Twig_Function_Function('form_radio'));
		$twig->addFunction('form_hidden', new \Twig_Function_Function('form_hidden'));
		$twig->addFunction('themes_path', new \Twig_Function_Function('Zenbu\librairies\platform\ee\View::themesPath'));
		$twig->addFunction('getCsrfInput', new \Twig_Function_Function('Zenbu\librairies\platform\ee\Session::getCsrfInput'));
		$twig->addFunction('cpUrl', new \Twig_Function_Function('Zenbu\librairies\platform\ee\Url::cpUrl'));
		$twig->addFunction('zenbuUrl', new \Twig_Function_Function('Zenbu\librairies\platform\ee\Url::zenbuUrl'));
		$twig->addFunction('cpEditEntryUrl', new \Twig_Function_Function('Zenbu\librairies\platform\ee\Url::cpEditEntryUrl'));
		$twig->addFilter(new \Twig_SimpleFilter('t', array('Zenbu\librairies\platform\ee\Lang', 't')));

		return $twig->render($path, $vars, TRUE);
	} 
}