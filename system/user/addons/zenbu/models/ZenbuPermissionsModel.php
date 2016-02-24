<?php namespace Zenbu\models;

class ZenbuPermissionsModel extends ZenbuBaseModel
{

    protected $_table_name = 'zenbu_permissions';

    protected $_update_check_fields = array('userGroupId', 'setting');

    var $userId;
    var $userGroupId;
    var $setting;
    var $value;

    // --------------------------------------------------------------------
}