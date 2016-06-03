<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* 
Docs:

Database: https://docs.expressionengine.com/latest/development/legacy/libraries/database.html
Form Helper: https://docs.expressionengine.com/latest/development/legacy/helpers/form_helper.html

*/

class Owners_area_files_ft extends EE_Fieldtype {

    var $info = array(
        'name'      => 'Owners Area Files',
        'version'   => '1.0'
    );

    // --------------------------------------------------------------------

    function display_field($data) {

        $results = ee()->db->select('m.member_id, email, m_field_id_1 as firstname, m_field_id_2 as lastname')
                  ->from('members m')
                  ->join('member_data d', 'm.member_id = d.member_id')
                  ->join('user_roles_assigned r', 'm.member_id = r.content_id')
                  ->where(array(
                        'r.role_id' => 1, // Owners area role
                        'm.group_id' => 5 // User has been approved
                    ))
                  ->get();

        $members = array(
            0 => 'Select Member'
        );

        $selected = array();
        if ($data) {
            $selected[] = (int) $data;
        }

        foreach($results->result_array() as $row) {
            $members[$row['member_id']] = $row['firstname'].' '.$row['lastname'].' ('.$row['email'].')';
        }

        return form_dropdown($this->field_name, $members, $selected);
    }
}