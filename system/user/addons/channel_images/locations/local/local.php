<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Images Local location
 *
 * @package         DevDemon_ChannelImages
 * @author          DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright       Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license         http://www.devdemon.com/license/
 * @link            http://www.devdemon.com/channel_images/
 */
class CI_Location_local extends Image_Location
{

    static protected $cachedUploadDest = array();

    /**
     * Constructor
     *
     * @access public
     *
     * Calls the parent constructor
     */
    public function __construct($settings=array())
    {
        parent::__construct();

        $this->lsettings = $settings;

    }

    // ********************************************************************************* //

    public function create_dir($dir)
    {
        // Did we store a location?
        if (isset($this->lsettings['location']) == false OR $this->lsettings['location'] == false) {
            return false;
        }

        $loc = $this->getLocationPrefs($this->lsettings['location']);
        if (!$loc) return false;

        // Mkdir & Chmod
        @mkdir($loc->server_path . $dir);
        @chmod($loc->server_path . $dir, 0777);

        return true;
    }

    // ********************************************************************************* //

    public function delete_dir($dir)
    {
        ee()->load->helper('file');

        // Did we store a location?
        if (isset($this->lsettings['location']) == false OR $this->lsettings['location'] == false) {
            return false;
        }

        $loc = $this->getLocationPrefs($this->lsettings['location']);
        if (!$loc) return false;

        // Delete them all!
        @delete_files($loc->server_path . $dir, true);
        @rmdir($loc->server_path . $dir);

        return true;
    }

    // ********************************************************************************* //
    public function upload_file($source_file, $dest_filename, $dest_folder)
    {
        $loc = $this->getLocationPrefs($this->lsettings['location']);
        if (!$loc) return false;

        $full_target = $loc->server_path . $dest_folder . '/' . $dest_filename;

        if (file_exists($full_target) === true) {
            @chmod($full_target, 0777);
        }

        // Move file
        if (copy($source_file, $full_target) === false) {
            $o['body'] = ee()->lang->line('ci:file_upload_error');
            exit(ee()->ee('channel_images:Helper')->generate_json($o) );
        } else {
            return true;
        }
    }

    // ********************************************************************************* //

    public function download_file($dir, $filename, $dest_folder)
    {
        $loc = $this->getLocationPrefs($this->lsettings['location']);
        if (!$loc) return false;

        copy($loc->server_path.$dir.'/'.$filename, $dest_folder.$filename);

        return true;
    }

    // ********************************************************************************* //

    public function delete_file($dir, $filename)
    {
        $loc = $this->getLocationPrefs($this->lsettings['location']);
        if (!$loc) return false;

        @unlink($loc->server_path . $dir . '/' . $filename);

        return false;
    }

    // ********************************************************************************* //

    public function parse_image_url($dir, $filename)
    {
        $loc = $this->getLocationPrefs($this->lsettings['location']);
        if (!$loc) return false;

        // Does it starts with / ?
        if (strpos($loc->url, '/') === 0 && strpos($loc->url, '//') !== 0) {
            // This may fail if using MSM
            $loc->url = 'http://' .$_SERVER['HTTP_HOST'] . '/' . $loc->url;
            $loc->url = reduce_double_slashes($loc->url); // Remove double slashes
        }

        // Is SSL?
        if (ee('channel_images:Helper')->isSsl() == true) {
            $loc->url = str_replace('http://', 'https://', $loc->url);
        }

        $final = $loc->url . $dir . '/' . $filename;

        // -----------------------------------------
        // Local Spefic Parameters?
        // -----------------------------------------
        if (isset(ee()->TMPL) == true) {
            // Kill the domain name?
            if (ee()->TMPL->fetch_param('local:remove_domain') == 'yes') {
                $url = parse_url($final);
                $final = $url['path'];
            }
        }

        return $final;
    }

    // ********************************************************************************* //

    public function test_location()
    {
        // What is our location path?
        $loc = $this->getLocationPrefs($this->lsettings['location']);
        $dir = $loc->server_path;

        $o = '<div class="sidebar">';
        $o .= '<strong style="color:orange">PATH:</strong> ' . $dir . '<br />';

        if (function_exists('posix_getpwuid')) {
            $userid = posix_getuid();
            $user = posix_getpwuid($userid);
            $o .= '<strong style="color:orange">PHP User:</strong> ' . @$user['name'] ." ({$userid})<br />";
        } else {
            $user = getenv('USERNAME');
            $o .= '<strong style="color:orange">PHP User:</strong> ' . $user . '<br />';
        }

        $o .= '<ul>';

        // Check for Safe Mode?
        $safemode = strtolower(@ini_get('safe_mode'));
        if ($safemode == 'on' || $safemode == 'yes' || $safemode == 'true' ||  $safemode == 1)  $o .= '<li class="remove">PHP Safe Mode (OFF): <span style="color:red">Failed</span></li>';
        else $o .= '<li class="act">PHP Safe Mode (OFF): <span style="color:green">Passed</span></li>';

        // Is DIR?
        if (is_dir($dir) === true)  $o .= '<li class="act">Is Dir: <span style="color:green">Passed</span></li>';
        else $o .= '<li class="remove">Is Dir: <span style="color:red">Failed</span></li>';

        // Is READABLE?
        if (is_readable($dir) === true) $o .= '<li class="act">Is Readable: <span style="color:green">Passed</span></li>';
        else $o .= '<li class="remove">Is Readable: Failed</li>';

        // Is WRITABLE
        if (is_writable($dir) === true) $o .= '<li class="act">Is Writable: <span style="color:green">Passed</span></li>';
        else $o .= '<li class="remove">Is Writable: <span style="color:red">Failed</span></li>';

        // CREATE TEST FILE
        $file = uniqid(mt_rand()).'.tmp';
        if (@touch($dir.$file) === true) $o .= '<li class="act">Create Test File: <span style="color:green">Passed</span></li>';
        else $o .= '<li class="remove">Create Test File: <span style="color:red">Failed</span></li>';

        // DELETE TEST FILE
        if (@unlink($dir.$file) === true) $o .= '<li class="act">Delete Test File: <span style="color:green">Passed</span></li>';
        else $o .= '<li class="remove">Delete Test File: <span style="color:red">Failed</span></li>';

        // CREATE TEST DIR
        $tempdir = 'temp_' . ee()->localize->now;
        if (@mkdir($dir.$tempdir, 0777, true) === true) {
            @chmod($dir.$tempdir, 0777);
            $o .= '<li class="act">';
            $o .= 'Create Test DIR: <span style="color:green">Passed</span>';

            if (function_exists('posix_getpwuid')) {
                $user_id = fileowner($dir.$tempdir);
                $user = posix_getpwuid($user_id);
                $o .= ' - User: ' . @$user['name'] ." ({$user_id})";
            }

            $o .= '</li>';
        }
        else $o .= '<li class="act">Create Test DIR: <span style="color:red">Failed</span></li>';


        // RENAME TEST DIR
        if (@rename($dir.$tempdir, $dir.$tempdir.'temp') === true) $o .= '<li class="act">Rename Test DIR: <span style="color:green">Passed</span></li>';
        else $o .= '<li class="remove">Rename Test DIR: <span style="color:red">Failed</span></li>';

        // DELETE TEST DIR
        if (@rmdir($dir.$tempdir.'temp') === true) $o .= '<li class="act">Delete Test DIR: <span style="color:green">Passed</span> <br>';
        else $o .= '<li class="remove">Delete Test DIR: <span style="color:red">Failed</span> <br>';

        $o .= '</ul>';

        $o .= "<p>Even if all tests PASS, uploading can still fail due Apache/htaccess misconfiguration</p>";

        $o .= '</div>';

        return $o;
    }

    // ********************************************************************************* //

    /**
     * Get Upload Prefs
     *
     * @param int $location_id
     * @access public
     * @return array - Location settings
     */
    public function getLocationPrefs($location_id)
    {
        $location = false;

        if (isset($this->cachedUploadDest[$location_id])) {
            $location = $this->cachedUploadDest[$location_id];
        } else {
            $location = ee('Model')->get('UploadDestination')->filter('id', $location_id)->first();
            if ($location) {
                $this->cachedUploadDest[$location_id] = $location;
            }
        }

        if (!$location) {
            return false;
        }

        $serverPath = (string) $location->server_path;

        // Relative path, always relative to SITE ROOT!
        // If it's being called from within the CP it will be relative to the system dir
        if (substr($serverPath, 0, 1) != '/') {
            // (try) to turn relative path into absolute path.
            if (realpath(FCPATH . $serverPath) != NULL) {
                $serverPath = realpath(FCPATH . $serverPath) . '/';
            }
        }

        // Need last slash!
        if (substr($serverPath, -1, 1) != '/') {
            $serverPath . '/';
        }

        $location->server_path = $serverPath;

        return $location;
    }

    // ********************************************************************************* //
}

/* End of file local.php */
/* Location: ./system/expressionengine/third_party/channel_images/locations/local/local.php */