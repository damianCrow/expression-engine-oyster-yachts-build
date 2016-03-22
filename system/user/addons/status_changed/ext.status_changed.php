<?php

class Status_changed_ext {

    var $name           = 'Status changed';
    var $version        = '1.0';
    var $description    = 'Checks if status has changed';
    var $settings_exist = 'n';
    var $docs_url       = ''; //

    var $settings        = array();

    /**
     * Constructor
     *
     * @param   mixed   Settings array or empty string if none exist.
     */
    function __construct($settings='')
    {
        $this->settings = $settings;
    }
    // END
    
    /**
     * Activate Extension
     *
     * This function enters the extension into the exp_extensions table
     *
     * @see https://ellislab.com/codeigniter/user-guide/database/index.html for
     * more information on the db class.
     *
     * @return void
     */
    function activate_extension()
    {
        $data = array(
            'class'     => __CLASS__,
            'method'    => 'checkStatus',
            'hook'      => 'before_channel_entry_update',
            'settings'  => '',
            'priority'  => 10,
            'version'   => $this->version,
            'enabled'   => 'y'
        );

        ee()->db->insert('extensions', $data);

        $data = array(
            'class'     => __CLASS__,
            'method'    => 'addStatus',
            'hook'      => 'before_channel_entry_insert',
            'settings'  => '',
            'priority'  => 10,
            'version'   => $this->version,
            'enabled'   => 'y'
        );

        ee()->db->insert('extensions', $data);
    }


    /**
     * Update Extension
     *
     * This function performs any necessary db updates when the extension
     * page is visited
     *
     * @return  mixed   void on update / false if none
     */
    function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version)
        {
            return FALSE;
        }

        if ($current < '1.0')
        {
            // Update to version 1.0
        }

        ee()->db->where('class', __CLASS__);
        ee()->db->update(
                    'extensions',
                    array('version' => $this->version)
        );
    }


    /**
     * Disable Extension
     *
     * This method removes information from the exp_extensions table
     *
     * @return void
     */
    function disable_extension()
    {
        ee()->db->where('class', __CLASS__);
        ee()->db->delete('extensions');
    }

    function addStatus($entry, $values) {
        if ($values['channel_id'] !== "1") return;

        // field_id_41 => brokerage_status_changed
        $entry->setProperty('field_id_41', $values['edit_date']);
    }

    function checkStatus($entry, $values, $modified) {
        if ($values['channel_id'] !== "1") return;

        // field_id_9 => brokerage_status
        // field_id_41 => brokerage_status_changed
        if (array_key_exists('field_id_9', $modified)) {
            $entry->setProperty('field_id_41', $values['edit_date']);
        }
        
    }
}
// END CLASS