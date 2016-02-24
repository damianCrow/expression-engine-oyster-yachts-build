<?php namespace Zenbu\models;

class ZenbuSavedSearchesModel extends ZenbuBaseModel
{

    protected $_table_name = 'zenbu_saved_searches';

    protected $_update_check_fields = array('id');

    var $id;
    var $label;
    var $userId;
    var $userGroupId;
    var $order;
    var $site_id;

    // --------------------------------------------------------------------
}