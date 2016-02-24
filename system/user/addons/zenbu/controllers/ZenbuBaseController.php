<?php
namespace Zenbu\controllers;

use Zenbu\librairies\platform\ee\Authors;
use Zenbu\librairies\platform\ee\Cache;
use Zenbu\librairies\platform\ee\Categories;
use Zenbu\librairies\platform\ee\Cp;
use Zenbu\librairies\platform\ee\Db;
use Zenbu\librairies\platform\ee\Entries;
use Zenbu\librairies\platform\ee\Lang;
use Zenbu\librairies\platform\ee\Pagination;
use Zenbu\librairies\platform\ee\Request;
use Zenbu\librairies\platform\ee\Session;
use Zenbu\librairies\platform\ee\Statuses;
use Zenbu\librairies\platform\ee\View;
use Zenbu\librairies\platform\ee\Url;
use Zenbu\librairies\Fields;
use Zenbu\librairies\Filters;
use Zenbu\librairies\SavedSearches;
use Zenbu\librairies\Sections;
use Zenbu\librairies\Settings;
use Zenbu\librairies\Users;
use Zenbu\librairies;

class ZenbuBaseController
{
	public $user;
	public $nav_label;
	public $sections;
	public $fields;
	public $filters;
	public $settings;
	public $general_settings;
	public $display_settings;
	public $saved_searches;
	public $debug_mode;
	public $vars;

	public function init()
	{
		$this->session          = new Session();
		$this->user             = Session::user();
		$this->cache            = new Cache();
		$this->users            = new Users();
		$this->request          = new Request;
		$this->sections         = new Sections();
		$this->filters          = new Filters();
		$this->fields           = new Fields();
		$this->db               = new Db();
		$this->entries          = new Entries();
		$this->authors          = new Authors();
		$this->statuses         = new Statuses();
		$this->categories       = new Categories();
		$this->pagination       = new Pagination();
		$this->settings         = new Settings();
		$this->cp               = new Cp();
		$this->url              = new Url();
		$this->view             = new View();
		$this->saved_searches   = new SavedSearches();
		$this->permissions      = $this->settings->getPermissions();
		$this->display_settings = $this->settings->getDisplaySettings();
		$this->general_settings = $this->settings->getGeneralSettings();
		$this->debug_mode       = $this->settings->isDebugEnabled();
		
		Lang::load(array('content', 'cp', 'zenbu'));

		$this->vars['message']          = ee('CP/Alert')->getAllInlines();
		$this->vars['permissions']      = $this->permissions;
		$this->vars['display_settings'] = $this->display_settings;
		$this->vars['general_settings'] = $this->general_settings;
		$this->vars['debug_mode']             = $this->debug_mode;
		
		View::includeJs(array('resources/js/zenbu_common.js'));
	}
}