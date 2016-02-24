<?php namespace Zenbu\models;

class ZenbuSavedSearchFiltersModel extends ZenbuBaseModel
{

    protected $_table_name = 'zenbu_saved_search_filters';

    protected $_update_check_fields = array('id');

    var $id;
    var $searchId;
    var $filterAttribute1;
    var $filterAttribute2;
    var $filterAttribute3;
    var $order;

    // --------------------------------------------------------------------
}