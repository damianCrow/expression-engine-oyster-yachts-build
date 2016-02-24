<?php namespace Zenbu\librairies;

use Zenbu\librairies\platform\ee\Cache;
use Zenbu\librairies\platform\ee\Lang;
use Zenbu\librairies\platform\ee\Request;
use Zenbu\librairies\platform\ee\Session;
use Zenbu\librairies\platform\ee\Convert;
use Zenbu\librairies\platform\ee\Db;
use Zenbu\librairies\platform\ee\UsersBase as UsersBase;

class Users extends UsersBase
{
    public function __construct()
    {
        parent::__construct();
        $this->usersBase = new UsersBase();
    }

    public function getUserGroups()
    {
        return $this->usersBase->getUserGroups();
    }

    public function getUserGroupSelectOptions()
    {
        return $this->usersBase->getUserGroupSelectOptions();
    }

    // --------------------------------------------------------------------
}