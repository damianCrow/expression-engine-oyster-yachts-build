<?php namespace Zenbu\librairies\platform\ee;

use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Cache;
use Zenbu\librairies\platform\ee\Convert;
use Zenbu\librairies\platform\ee\Session;
use Zenbu\librairies\platform\ee\Db;
use Zenbu\librairies\Fields;
use Zenbu\librairies;

class SettingsBase extends Base
{
    public function __construct()
    {
        parent::__construct(__CLASS__);
        parent::init('fields');
        $this->db = new Db;
    }

    /**
     * Retrieve Permissions
     * @return array Permissions
     */
    public function getPermissions($what = '')
    {
        // Return data if already cached
        if($this->cache->get('permissions') && $what != 'all')
        {
            return $this->cache->get('permissions');
        }

        $sql_where = $what != 'all' ? 'zp.userGroupId = ' . $this->user->group_id : '(zp.userId IS NULL OR zp.userId = 0)';

        $sql = '/* '.__FUNCTION__.' */ SELECT zp.* FROM zenbu_permissions zp
                WHERE ' . $sql_where . '
                ORDER BY FIELD(zp.setting, "'.implode('", "', $this->permission_keys).'")';

        $results = Db::rawQuery($sql);

        $permissions = array();

        if(count($results) > 0)
        {
            foreach($results as $row)
            {
                if($what != 'all')
                {
                    $permissions[$row['setting']] = $row['value'];
                }
                else
                {
                    $permissions[$row['userGroupId']][$row['setting']] = $row['value'];
                }
            }
        }

        //    ----------------------------------------
        //    Add Other permissions to array
        //    if not found in database
        //    ----------------------------------------
        foreach($this->permission_keys as $perm_key)
        {
            if($what != 'all')
            {
                $permissions[$perm_key] = isset($permissions[$perm_key]) ? $permissions[$perm_key] : 'n';
            }
        }

        if($what != 'all')
        {
            $this->cache->set('permissions', $permissions);
        }

        return $permissions;
    } // END getPermissions

    // --------------------------------------------------------------------

    /**
     * Retrieve General settings
     * @return array      The General Settings
     */
	public function getGeneralSettings()
	{
        // Return data if already cached
        if($this->cache->get('general_settings_User_'.$this->user->id))
        {
            return $this->cache->get('general_settings_User_'.$this->user->id);
        }

        $sql = '/* '.__FUNCTION__.' */ SELECT zgs.* FROM zenbu_general_settings zgs
                WHERE zgs.userId = ' . $this->user->id . '
                ORDER BY FIELD(zgs.setting, "'.implode('", "', $this->general_settings_keys).'")';

        $results = Db::rawQuery($sql);

        $general_settings = array();

        if(count($results) > 0)
        {
            foreach($results as $row)
            {
                $general_settings[$row['setting']] = $row['value'];
            }
        }

        //    ----------------------------------------
        //    Add Other permissions to array
        //    if not found in database
        //    ----------------------------------------
        foreach($this->general_settings_keys as $setting)
        {
            $general_settings[$setting] = isset($general_settings[$setting]) ? $general_settings[$setting] : '';
        }

        $this->cache->set('general_settings_User_'.$this->user->id, $general_settings);

        return $general_settings;
	} // END getGeneralSettings

    // --------------------------------------------------------------------

    public function getDisplaySettings($what = '')
    {
        if($this->session->getCache('display_settings_Section_'.Request::param(Convert::string('sectionId'), 0).'_User'.Session::user()->id))
        {
            return $this->session->getCache('display_settings_Section_'.Request::param(Convert::string('sectionId'), 0).'_User'.Session::user()->id);
        }

        //    ----------------------------------------
        //    We're joining exp_channel_fields here to
        //    make sure fieldIds in Zenbu settings refer to
        //    existing fields in exp_channel_fields
        //    ----------------------------------------
        $sql = '/* '.__FUNCTION__.' */ SELECT zds.*, cf.* FROM zenbu_display_settings zds
                LEFT JOIN exp_channel_fields cf ON cf.field_id = zds.fieldId
                WHERE zds.sectionId = '. Request::param(Convert::string('sectionId'), 0) . '
                AND zds.userId = ' . Session::user()->id . '
                AND (zds.fieldId = 0 OR zds.fieldId = cf.field_id)';

        if($what != 'all')
        {
            $sql .= " AND zds.show = 1";
        }

        if(Request::param(Convert::col('subSectionId')))
        {
            $sql .= " AND zds.".Convert::col('subSectionId')." = " . Request::param(Convert::col('subSectionId'), 0);
        }

        $sql .= " ORDER BY `order` ASC";

        $results = Db::rawQuery($sql);

        $display_settings = array();

        if(count($results) > 0)
        {
            foreach($results as $row)
            {
                $display_settings['sectionId']                  = $row['sectionId'];
                $display_settings[Convert::col('subSectionId')] = $row[Convert::col('subSectionId')];
                $display_settings['fields'][$row['order']]      = array(
                    'show'      => $row['show'],
                    'fieldType' => $row[Convert::col('fieldType')],
                    'fieldId'   => $row[Convert::col('fieldId')],
                    'order'     => $row['order'],
                    'settings'  => json_decode($row['settings'])
                    );
                $handle = $row[Convert::col('fieldType')] == 'field' ? $row[Convert::col('fieldId')] : $row[Convert::col('fieldType')];
                $display_settings['settings'][$handle] = json_decode($row['settings']);
            }
        }
        else
        {
            // $this->settings = new \Zenbu\librairies\Settings();
            $display_settings = $this->getDefaultDisplaySettings();
        }

        $this->session->setCache('display_settings_Section_'.Request::param(Convert::string('sectionId'), 0).'_User'.Session::user()->id, $display_settings);

        return $display_settings;

    } // END getDisplaySettings

    // --------------------------------------------------------------------


    public function getExtraDisplaySettingsFields($what = '')
    {
        //    ----------------------------------------
        //    Retrieving all custom field types and
        //    loading their extra settings, if any
        //    ----------------------------------------
        $sql = '/* '.__FUNCTION__.' */ SELECT * FROM exp_channel_fields';

        $results = Db::rawQuery($sql);

        $output = array();

        if(count($results) > 0)
        {
            foreach($results as $row)
            {

                /**
                *   ====================================
                *   Adding third-party fieldtype classes
                *   ====================================
                */

                $ft_object = $this->fields->loadFieldtypeClass($row['field_type']);

                $table_col = 'field_'.$row['field_id'];
                $output[$row['field_id']] = $ft_object && method_exists($ft_object, 'zenbu_field_extra_settings') ?
                    $ft_object->zenbu_field_extra_settings(
                        'field_id_'.$row['field_id'],
                        Request::param(Convert::string('sectionId'), 0),
                        FALSE,
                        $row['field_settings']) : array();

            }
        }

        //    ----------------------------------------
        //    Loading fields with saved data,
        //    overwriting the above
        //	  ----------------------------------------
        //    We're joining exp_channel_fields here to
        //    make sure fieldIds in Zenbu settings refer to
        //    existing fields in exp_channel_fields
        //    ----------------------------------------
        $sql = '/* '.__FUNCTION__.' */ SELECT zds.*, cf.* FROM zenbu_display_settings zds
                JOIN exp_channel_fields cf ON cf.field_id = zds.fieldId
                WHERE zds.sectionId = '. Request::param(Convert::string('sectionId'), 0) . '
                AND zds.userId = ' . Session::user()->id . '
                AND (zds.fieldId = 0 OR zds.fieldId = cf.field_id)';

        if($what != 'all')
        {
            $sql .= " AND zds.show = 1";
        }

        if(Request::param(Convert::col('subSectionId')))
        {
            $sql .= " AND zds.".Convert::col('subSectionId')." = " . Request::param(Convert::col('subSectionId'), 0);
        }

        $sql .= " ORDER BY `order` ASC";

        $results = Db::rawQuery($sql);

        if(count($results) > 0)
        {
            foreach($results as $row)
            {

                /**
                *   ====================================
                *   Adding third-party fieldtype classes
                *   ====================================
                */

                $ft_object = $this->fields->loadFieldtypeClass($row['field_type']);

                $table_col = 'field_'.$row['field_id'];
                $output[$row['field_id']] = $ft_object && method_exists($ft_object, 'zenbu_field_extra_settings') ?
                    $ft_object->zenbu_field_extra_settings(
                        'field_id_'.$row['field_id'],
                        $row['sectionId'],
                        json_decode($row['settings'], TRUE),
                        $row['field_settings']) : array();

            }
        }

        return $output;
    } // END getExtraDisplaySettings

    // --------------------------------------------------------------------

    public function getDefaultDisplaySettings()
    {
        $fields                          = $this->fields->getFields();
        $display_settings['sectionId']   = Request::post(Convert::string('sectionId'));
        $display_settings['entryTypeId'] = Request::post('entryTypeId');

        $c = 1;

        foreach($fields as $handle => $field)
        {
            $display_settings['fields'][] = array(
                'show'     => 1,
                'fieldType'=> is_integer($handle) ? 'field' : $handle,
                'fieldId'  => is_integer($handle) ? $handle : 0,
                'order'    => $c,
                'settings' => ''
                );
            $c++;
        }

        return $display_settings;
    } // END getDefaultDisplaySettings()

    // --------------------------------------------------------------------


    public function getGroupsWithAddonAccess()
    {
        // Get module ID
        $module = $this->db->find('modules', $where = 'module_name = "?"', array('Zenbu'), FALSE);

        $member_groups = $this->db->find('module_member_groups', 'module_id = ?', array($module[0]->module_id));

        $output = array();

        if($member_groups)
        {
            foreach($member_groups as $key => $group)
            {
                $output[$group->group_id] = $group->group_id;
            }
        }

        return $output;
    }

    public function isDebugEnabled()
    {
        if(ee()->config->item('zenbu_debug_mode'))
        {
            return TRUE;
        }

        return FALSE;
    }
}
