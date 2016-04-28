<?php

class Event_to_news_ext {

    var $name           = 'Event to News';
    var $version        = '1.0';
    var $description    = 'Adds news article when event day report is created';
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
            'method'    => 'addNews',
            'hook'      => 'after_channel_entry_insert',
            'settings'  => '',
            'priority'  => 1,
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

    function addNews($entry, $values) {
        /*echo '<pre>';
        var_dump($_POST);
        die();*/

        // grid 55

        if ($values['channel_id'] !== "8") return;


        $eventId = $_POST['field_id_87']['data'][0];

        $query = ee()->db->query("SELECT title FROM exp_channel_titles WHERE entry_id=".$eventId);

        if ($query->num_rows() > 0) {
            $row = $query->row();
        }

        $data = array();

        $data['title'] = $row->title.' - '.$values["field_id_97"];


        $data['field_id_55'] = array(
            'rows' => array(
                'new_row_1' => array(
                    'col_id_27' => $values["field_id_82"],
                    'col_id_40' => ''
                )
            )
        );

        //$data['field_id_55'] = $values["field_id_82"];
        $data['status'] = 'Published';

        /*if (isset($_POST['field_id_84'])) {
            $image = json_decode($_POST['field_id_84']['images'][1]['data']);
        }*/
        

        /*echo '<pre>';

        echo $eventId;

        var_dump($_POST);

        echo '<br>';
        // Day label => Race One
        echo $values["field_id_97"];
        echo '<br>';

       

        var_dump($values);
        echo '<br><br>';
        //var_dump($entry);
        die();*/

        ee()->load->library('api');
        ee()->legacy_api->instantiate('channel_entries');
        ee()->legacy_api->instantiate('channel_fields');

        ee()->api_channel_fields->setup_entry_settings(4, $data);
        $success = ee()->api_channel_entries->save_entry($data, 4);

        if ( ! $success)
        {
                show_error(implode('<br />', ee()->api_channel_entries->errors));
        }

        /*echo 'id'.$entry_id;

        ee()->db->insert(
            'channel_grid_field_55',
            array(
                'entry_id'  => $values["entry_id"],
                'row_order' => 0,
                'col_id_27'   => $values["field_id_82"],
                'col_id_40'   => '',
            )
        );*/

        // field_id_51 => brokerage_status_changed
        //$entry->setProperty('field_id_51', $values['edit_date']);
    }
}
// END CLASS