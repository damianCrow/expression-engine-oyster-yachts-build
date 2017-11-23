<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* 
Docs:

Database: https://docs.expressionengine.com/latest/development/legacy/libraries/database.html
Form Helper: https://docs.expressionengine.com/latest/development/legacy/helpers/form_helper.html

*/

class Wr_member_select_ft extends EE_Fieldtype {

    var $info = array(
        'name'      => 'World Rally Member Select',
        'version'   => '1.0'
    );

    //var $has_array_data = TRUE;

    // --------------------------------------------------------------------

    function display_field($data) {

        $results = ee()->db->select('m.member_id, email, m_field_id_2 as firstname, m_field_id_3 as lastname')
                  ->from('members m')
                  ->join('member_data d', 'm.member_id = d.member_id')
                  //->join('user_roles_assigned r', 'm.member_id = r.content_id')
                  ->where(array(
                        //'r.role_id' => 1, // Owners area role
                        //'m.group_id' => 12 // Owners area user group
                        'm_field_id_21' => 'Yes'
                    ))
                  ->order_by('m_field_id_2', 'ASC')
                  ->order_by('m_field_id_3', 'ASC')
                  ->get();

        $members = array();

        $selected = array();
        if ($data) {
            //die($data);

            //ee()->load->helper('custom_field');

            //$selected = decode_multi_field($data);

            //$data = str_replace('|', '', $data);
            //$selected = explode(',', $data);

            //var_dump($data);
            //die();
            //$selected[] = (int) $data;
        }

        foreach($results->result_array() as $row) {
            $members[$row['member_id']] = $row['firstname'].' '.$row['lastname'].' ('.$row['email'].')';
        }

        return form_dropdown($this->field_name, $members, $data);
    }

    public function save($data)
    {
        
        /*if (is_array($data))
        {
            foreach ($data as &$item) {
                $item = '|'.$item.'|';
            }

            return implode(',', $data);
        } 

        return '|'.$data.'|';*/

        /*if (is_array($data))
        {
            ee()->load->helper('custom_field');
            $data = encode_multi_field($data);
        }*/

        return $data;
    }
}