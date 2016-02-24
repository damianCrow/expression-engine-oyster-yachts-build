<?php namespace Zenbu\models;

use Zenbu\librairies\platform\ee\Db;

class ZenbuBaseModel
{
    public function __construct()
    {
    }

    public function save()
    {
        $Db   = new Db();
        
        if(isset($this->_update_check_fields) && is_array($this->_update_check_fields))
        {
            $check_data = array();
            
            foreach($this->_update_check_fields as $check_field)
            {
                $check_data[] = $this->$check_field;
            }

            $found = $Db->find($this->_table_name, 
                implode(' = "?" AND ', $this->_update_check_fields) . ' = "?"', 
                $check_data
                    );
        }
        else
        {
            $found = FALSE;
        }



        if($found !== FALSE)
        {
            $data = array();
    
            foreach($found as $row)
            {
                foreach($row as $found_key => $found_row)
                {
                    $data[$found_key] = $found_row;                
                }
                
                foreach($this as $key => $var)
                {
                    if(substr($key, 0, 1) != '_')
                    {
                        $data[$key] = is_null($var) && isset($data[$key]) ? $data[$key] : $var; 
                    }
                }

                $Db->update($this->_table_name, $data, implode(' = "?" AND ', $this->_update_check_fields) . ' = "?"',  $check_data);
            }
            
        }
        else
        {
            $data = array();

            foreach($this as $key => $var)
            {
                if(substr($key, 0, 1) != '_')
                {
                    $data[$key] = $var; 
                }
            }

            return $Db->insert($this->_table_name, $data);
        }
    }
    // --------------------------------------------------------------------
}