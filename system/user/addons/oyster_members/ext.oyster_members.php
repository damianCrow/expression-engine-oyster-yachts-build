<?php

class Oyster_members_ext {

    var $name           = 'Oyster Members';
    var $version        = '1.0.0';
    var $description    = 'Oyster restricted areas';
    var $settings_exist = 'n';
    var $docs_url       = '';

    var $settings        = array();

    function __construct($settings='')
    {
        $this->settings = $settings;
    }
    
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
            'method'    => 'emailValidated',
            'hook'      => 'cp_members_validate_members',
            'settings'  => '',
            'priority'  => 1,
            'version'   => $this->version,
            'enabled'   => 'y'
        );

        ee()->db->insert('extensions', $data);

        $data = array(
            'class'     => __CLASS__,
            'method'    => 'emailRegistered',
            'hook'      => 'after_member_insert',
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

    /**
     * Send user email when account has been activated by an admin
     */

    function emailValidated($ids) {
        ee()->load->library('email');

        // Get all validated members
        $members = ee('Model')->get('Member', $ids)
                ->fields('member_id', 'username', 'screen_name', 'email', 'group_id')
                ->all();

        // Loop through members
        foreach ($members as $member) {

            // Get member role
            $roles = ee('Model')->get('user:RoleAssigned')
            ->filter('content_id', $member->member_id)
            ->first();

            
            // if role === Owners Area, add member to Owners Area member group
            if ((int) $roles->role_id === 1) {
                $member->group_id = 12;
                $member->save();
            // if role === Media Area, add member to Media Area member group
            } else if ((int) $roles->role_id === 2) {
                $member->group_id = 13;
                $member->save();
            }


            // Build message
            $message = 'Your account has been activated';

            // Send email
            ee()->email->clear();
            ee()->email->from(
                ee()->config->item('webmaster_email'),
                ee()->config->item('webmaster_name')
            );
            ee()->email->to($member->email);
            ee()->email->subject('Your account has been activated. Please login to www.oysteryachts.com.');
            ee()->email->message($message);
            ee()->email->send();


        }
    }

    /**
     * Send emails when user registers to request access
     */

    function emailRegistered($member, $values) {

        // if not pending member then exit
        if ((int) $values['group_id'] !== 4) {
            return;
        }

        //var_dump($values);
        //var_dump($param);
        //die();

        // Get the form parameters to get the role id
        $param = ee('Model')->make('user:Param')
            ->get_params($_POST['params_id']);



        // Get role object from role id
        $role = ee('Model')->get('user:Role')
            ->filter('role_id', $param['assign_roles'][0])
            ->first();

        // Build email message
        // 
        // Common between Owners / Media
        $message = 'User has requested access to: '.$role->role_label.PHP_EOL.PHP_EOL.
                    'Name: '.$values['m_field_id_1'].' '.$values['m_field_id_2'].' '.$values['m_field_id_3'].PHP_EOL.
                    'Email: '.$values['email'].PHP_EOL.
                    'Phone: '.$values['m_field_id_11'].PHP_EOL;

        // Owners Area
        if ($role->role_id === "1") {
            // Address fields
            $message .= 'Address: '.$values['m_field_id_4'];

            for ($i=5;$i<=8;$i++) {
                if ($values['m_field_id_'.$i] !== "") {
                    $message .= ', '.$values['m_field_id_'.$i];
                }
            }

            $message .= PHP_EOL.
                        'Country: '.$values['m_field_id_9'].PHP_EOL.
                        'Postcode: '.$values['m_field_id_10'].PHP_EOL.
                        'Account Type: '.$values['m_field_id_12'].PHP_EOL.
                        'Yacht Name: '.$values['m_field_id_13'].PHP_EOL.
                        'Yacht Model: '.$values['m_field_id_14'];

        
        // Media Area           
        } else if ($role->role_id === "2") {
            $message .= 'Job Position: '.$values['m_field_id_17'].PHP_EOL.
                        'Name of Media Organisation: '.$values['m_field_id_18'].PHP_EOL.
                        'Type of Medium: '.$values['m_field_id_19'].PHP_EOL.
                        'Proposed Usage: '.$values['m_field_id_20'];
        }

        // Common between Owners / Media
        $message .= PHP_EOL.PHP_EOL.
                    'Receive updates: '.$values['m_field_id_16'].PHP_EOL.
                    'Additional info: '.$values['m_field_id_15'];

        // Send email to admin
        ee()->load->library('email');
        
        $subject = 'New User';
        $adminEmail = 'macdochris@gmail.com, giulio.r@interstateteam.com';


        ee()->email->clear();
        ee()->email->from(
            ee()->config->item('webmaster_email'),
            ee()->config->item('webmaster_name')
        );
        ee()->email->to($adminEmail);
        ee()->email->subject($subject);
        ee()->email->message($message);
        ee()->email->send();


        // Send email to user
        $subject = 'Registration';
        $message = 'Your account is currently being verified by our team, you will shortly receive an email from us with your login credentials. Please note there may be a short delay in this process.';

        
        ee()->email->clear();
        ee()->email->from(
            ee()->config->item('webmaster_email'),
            ee()->config->item('webmaster_name')
        );
        ee()->email->to($member->email);
        ee()->email->subject($subject);
        ee()->email->message($message);
        ee()->email->send();

    }
}
// END CLASS