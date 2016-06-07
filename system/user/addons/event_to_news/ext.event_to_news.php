<?php

class Event_to_news_ext {

    var $name           = 'Event to News';
    var $version        = '1.0.0';
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
        // Copy images before insert
        $data = array(
            'class'     => __CLASS__,
            'method'    => 'copyImages',
            'hook'      => 'before_channel_entry_insert',
            'settings'  => '',
            'priority'  => 1,
            'version'   => $this->version,
            'enabled'   => 'y'
        );

        ee()->db->insert('extensions', $data);

        // Add Event News after insert
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

        // Copy images before update
        $data = array(
            'class'     => __CLASS__,
            'method'    => 'copyImages',
            'hook'      => 'before_channel_entry_update',
            'settings'  => '',
            'priority'  => 1,
            'version'   => $this->version,
            'enabled'   => 'y'
        );

        ee()->db->insert('extensions', $data);

        // Update Event News after update
        $data = array(
            'class'     => __CLASS__,
            'method'    => 'updateNews',
            'hook'      => 'after_channel_entry_update',
            'settings'  => '',
            'priority'  => 1,
            'version'   => $this->version,
            'enabled'   => 'y'
        );

        ee()->db->insert('extensions', $data);

        // Delete Event News after delete
        $data = array(
            'class'     => __CLASS__,
            'method'    => 'deleteNews',
            'hook'      => 'before_channel_entry_delete',
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

        // if not "Event Day Report" channel then exit
        if ($values['channel_id'] !== "16") return;

        // Get the event ID via the relationship field
        // field_id_120 => relationship to Event channel
        $eventId = $_POST['field_id_120']['data'][0];

        // Get the title field of the Event
        $query = ee()->db->query("SELECT title FROM exp_channel_titles WHERE entry_id=".$eventId);

        if ($query->num_rows() > 0) {
            $row = $query->row();
        }

        $data = array();

        // Set title of Event News item to be "{Event name} - {Event Day Report day label}"
        $data['title'] = $row->title.' - '.$values['field_id_122'];

        
        // We want the first sentence as the excerpt, so break up content into sentences
        $sentences = explode(".", $values['field_id_115']);

        // Add . at end
        $excerpt = $sentences[0];
        if (count($sentences) > 1) {
            $excerpt .= '.';
        }
        
        if ($excerpt) {
            // Close off any open HTML tags which may have been cut
            // DOMDocument adds html so we need to trim it => http://php.net/manual/en/domdocument.savehtml.php
            $doc = new DOMDocument();
            $doc->loadHTML($excerpt);
            $data['field_id_127'] = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $doc->saveHTML()));
        } else {
            $data['field_id_127'] = '';
        }

        
        // Set the Event Report field to be a relationship to this Event Report being created
        $data['field_id_128'] = array(
            'data' => array(
                0 => $values['entry_id']
            )
        );

        // If there's a gallery image, set it as the news item image
        // We need to copy the image and it's thumbs from this Event Day Report so a cache directory, so Channel Images can add them to our new Event News post
        if (isset($_POST['field_id_116']['images'][1]['data'])) {

            // Channel Images image field key
            $key = $_POST['field_id_116']['key'];
            
            // Channel Images image field JSON
            $imageDataJson = $_POST['field_id_116']['images'][1]['data'];

            // Set the channel ID to Event News so Channel Images sets it correctly, else it's set to Event Day Report channel ID
            $_POST['channel_id'] = 17;

            // Decode JSON
            $imageData = json_decode($imageDataJson);

            // If images were uploaded successfully
            if ($imageData->success === "yes") {

                // Set field
                $data['field_id_129'] = array(
                    'images' => array(
                        1 => array(
                            'data' => $imageDataJson
                        )
                    ),
                    'key' => $key
                );
            } 
        }       
        
        // Set status
        $data['status'] = 'Published';

        
        // Load API
        ee()->load->library('api');
        ee()->legacy_api->instantiate('channel_entries');
        ee()->legacy_api->instantiate('channel_fields');

        // Add entry to Event News channel
        ee()->api_channel_fields->setup_entry_settings(17, $data);
        $success = ee()->api_channel_entries->save_entry($data, 17);

        if ( ! $success)
        {
            show_error(implode('<br />', ee()->api_channel_entries->errors));
        }
    }

    function updateNews($entry, $values, $modified) {

        // if not "Event Day Report" channel then exit
        if ($values['channel_id'] !== "16") return;


        // Load API
        ee()->load->library('api');
        ee()->legacy_api->instantiate('channel_entries');
        ee()->legacy_api->instantiate('channel_fields');

        $query = ee()->db->query("SELECT parent_id FROM exp_relationships WHERE child_id=".$values['entry_id']);

        if ($query->num_rows() === 0) {
            return;
        }

        $parent = $query->row();

        // Get the event ID via the relationship field
        // field_id_120 => relationship to Event channel
        $eventId = $_POST['field_id_120']['data'][0];

        // Get the title field of the Event
        $parentQuery = ee()->db->query("SELECT title FROM exp_channel_titles WHERE entry_id=".$eventId);

        if ($parentQuery->num_rows() > 0) {
            $row = $parentQuery->row();
        }
        

        $data = array();

        // Set title of Event News item to be "{Event name} - {Event Day Report day label}"
        $data['title'] = $row->title.' - '.$values['field_id_122'];

        
        // We want the first sentence as the excerpt, so break up content into sentences
        $sentences = explode(".", $values['field_id_115']);

        // Add . at end
        $excerpt = $sentences[0];
        if (count($sentences) > 1) {
            $excerpt .= '.';
        }
        
        if ($excerpt) {
            // Close off any open HTML tags which may have been cut
            // DOMDocument adds html so we need to trim it => http://php.net/manual/en/domdocument.savehtml.php
            $doc = new DOMDocument();
            $doc->loadHTML($excerpt);
            $data['field_id_127'] = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $doc->saveHTML()));
        } else {
            $data['field_id_127'] = '';
        }

        
        // Set the Event Report field to be a relationship to this Event Report being created
        $data['field_id_128'] = array(
            'data' => array(
                0 => $values['entry_id']
            )
        );

         /*echo '<pre>';
         var_dump($_POST);
         die();*/

        // If there's a gallery image, set it as the news item image
        // We need to copy the image and it's thumbs from this Event Day Report so a cache directory, so Channel Images can add them to our new Event News post
        if (isset($_POST['field_id_116']['images'][1]['data'])) {

            // Channel Images image field key
            $key = $_POST['field_id_116']['key'];
            
            // Set the channel ID to Event News so Channel Images sets it correctly, else it's set to Event Day Report channel ID
            $_POST['channel_id'] = 17;

            $found = false;
            $i = 1;

            $data['field_id_129'] = array(
                'images' => array(),
                'key' => $key
            );

            while (!$found && $i <= count($_POST['field_id_116']['images'])) {
                // Channel Images image field JSON
                $imageDataJson = $_POST['field_id_116']['images'][$i]['data'];

                // Decode JSON
                $imageData = json_decode($imageDataJson);

                if (property_exists($imageData, 'success')) {
                    // If images were uploaded successfully
                    if ($imageData->success === "yes") {

                        // Set field
                        
                        $data['field_id_129']['images'][$i] = array(
                            'data' => str_replace('fid=116', 'fid=129', $imageDataJson)
                        );

                        $found = true;
                    }
                } else if (property_exists($imageData, 'delete')) {
                    //echo 'delete: '.$i.'<br />';

                    /*var_dump($imageData);
                    die();*/

                    $imageData->field_id = 129;
                    $imageData->entry_id = $parent->parent_id;

                    $imageQuery = ee()->db->query("SELECT image_id FROM exp_channel_images WHERE filename='".$imageData->filename."'");

                    if ($imageQuery->num_rows() > 0) {
                        $imageRow = $imageQuery->row();

                        $imageData->image_id = $imageRow->image_id;
                    }

                    $data['field_id_129']['images'][$i] = array(
                        'data' => json_encode($imageData)
                    );

                } else {
                    $found = true;
                }

                $i++;
            }

            /*if (!$found) {
                $data['field_id_129'] = array();
            }*/
        }
        
        /*echo '<pre>';
        var_dump($_POST['field_id_116']['images']);
        var_dump($data);
        die();*/

        /*// Set status
        $data['status'] = 'Published';

        // Load API
        ee()->load->library('api');
        ee()->legacy_api->instantiate('channel_entries');
        ee()->legacy_api->instantiate('channel_fields');

        $query = ee()->db->query("SELECT parent_id FROM exp_relationships WHERE child_id=".$values['entry_id']);
*/
        if ($parentQuery->num_rows() > 0) {
            //$row = $query->row();

            // Add entry to Event News channel
            ee()->api_channel_fields->setup_entry_settings(17, $data);
            $success = ee()->api_channel_entries->save_entry($data, 17, $parent->parent_id);
        }

        
    }

    function deleteNews($entry, $values) {
        // if not "Event Day Report" channel then exit
        if ($values['channel_id'] !== "16") return;

        $query = ee()->db->query("SELECT parent_id FROM exp_relationships WHERE child_id=".$values['entry_id']);

        if ($query->num_rows() > 0) {
            $row = $query->row();

            $success = ee()->api_channel_entries->delete_entry($row->parent_id);
        }
    }

    function copyImages($entry, $values) {
        // If there's a gallery image, set it as the news item image
        // We need to copy the image and it's thumbs from this Event Day Report so a cache directory, so Channel Images can add them to our new Event News post
        if (isset($_POST['field_id_116']['images'][1]['data'])) {

            // Channel Images image field key
            $key = $_POST['field_id_116']['key'];
            
            foreach ($_POST['field_id_116']['images'] as $image) {
                // Channel Images image field JSON
                $imageDataJson = $image['data'];

                // Decode JSON
                $imageData = json_decode($imageDataJson);

                if (property_exists($imageData, 'success')) {
                    // If images were uploaded successfully
                    if ($imageData->success === "yes") {

                        // Get filename
                        $imageFilename = $imageData->filename;

                        // Get file info
                        $fileInfo = pathinfo($imageFilename);
                       
                        // Get file basename
                        $imageFilenameBase =  basename($imageFilename,'.'.$fileInfo['extension']);

                        // Array of filenames we want to copy
                        // => Update this if the thumb nmse change
                        $files = array(
                            $imageFilename,
                            $imageFilenameBase.'__thumb.'.$fileInfo['extension'],
                            $imageFilenameBase.'__preview.'.$fileInfo['extension'],
                            $imageFilenameBase.'__gallery_thumb.'.$fileInfo['extension'],
                            $imageFilenameBase.'__small.'.$fileInfo['extension']
                        );

                        // Create cache directories
                        @mkdir(SYSPATH.'user/cache/channel_images/field_129', 0777);
                        @mkdir(SYSPATH.'user/cache/channel_images/field_129/'.$key, 0777);

                        //copy('/var/www/vhosts/interstateteam.com/oysteryachts.interstateteam.com/images/events/153/charter-yacht-bg-activities.jpg', '/var/www/vhosts/interstateteam.com/oysteryachts.interstateteam.com/images/events/153/charter-yacht-bg-activities2.jpg');

                        // Copy files to cache dir
                        foreach ($files as $file) {
                            @copy(SYSPATH.'user/cache/channel_images/field_116/'.$key.'/'.$file, SYSPATH.'user/cache/channel_images/field_129/'.$key.'/'.$file);
                        }
                    } 
                }
            }

            
        }     
    }
}
// END CLASS