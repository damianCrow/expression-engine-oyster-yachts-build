<?php namespace Zenbu\models;

class ZenbuGeneralSettingsModel extends ZenbuBaseModel
{

    protected $_table_name = 'zenbu_general_settings';

    protected $_update_check_fields = array('userId', 'setting');

    var $userId;
    var $userGroupId;
    var $setting;
    var $value;

    // --------------------------------------------------------------------
}