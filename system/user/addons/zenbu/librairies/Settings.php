<?php namespace Zenbu\librairies;

use Zenbu\librairies\platform\ee\Cache;
use Zenbu\librairies\platform\ee\Lang;
use Zenbu\librairies\platform\ee\Request;
use Zenbu\librairies\platform\ee\Session;
use Zenbu\librairies\platform\ee\Convert;
use Zenbu\librairies\platform\ee\Db;
use Zenbu\librairies\platform\ee\SettingsBase;

class Settings extends SettingsBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getGeneralSettings($what = '')
    {
        return parent::getGeneralSettings($what);
    }

	public function getDisplaySettings($what = '')
    {
        return parent::getDisplaySettings($what);
    } // END getDisplaySettings

    // --------------------------------------------------------------------

    public function getDefaultDisplaySettings()
    {
        return parent::getDefaultDisplaySettings();
    }

    public function getGroupsWithAddonAccess()
    {
        return parent::getGroupsWithAddonAccess();
    }
}