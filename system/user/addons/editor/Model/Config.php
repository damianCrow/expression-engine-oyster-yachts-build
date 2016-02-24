<?php

namespace DevDemon\Editor\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

class Config extends Model {

    protected static $_primary_key = 'id';
    protected static $_table_name = 'editor_configs';

    // Some Caches
    protected static $_cachedChannelToField = array();

    protected static $_typed_columns = array(
        'id'              => 'int',
        'site_id'         => 'int',
        'settings'        => 'json',
    );

    protected $id;
    protected $site_id;
    protected $label;
    protected $type;
    protected $settings;

    // ********************************************************************************* //

} // END CLASS

/* End of file Config.php */
/* Location: ./system/user/addons/editor/Model/Config.php */