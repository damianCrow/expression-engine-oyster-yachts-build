<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Images S3 location
 *
 * @package         DevDemon_ChannelImages
 * @author          DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright       Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license         http://www.devdemon.com/license/
 * @link            http://www.devdemon.com/channel_images/
 */
class CI_Location_s3 extends Image_Location
{
    static protected $module;
    static protected $cachedS3 = array();

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

    public function delete_dir($dir)
    {
        $s3 = $this->init();

        // Subdirectory?
        $subdir = (isset($this->lsettings['directory']) == true && $this->lsettings['directory'] != false) ? $this->lsettings['directory'] . '/' .$dir : $dir;

        // Get all objects
        $objects = $s3->getBucket($this->lsettings['bucket'], $subdir);

        foreach  ($objects as $file)
        {
            //$s3->batch()->delete_object($this->lsettings['bucket'], $file);
            $s3->deleteObject($this->lsettings['bucket'], $file['name']);
        }

        //$responses = $s3->batch()->send();

        return true;
    }

    // ********************************************************************************* //

    public function upload_file($source_file, $dest_filename, $dest_folder)
    {
        $s3 = $this->init();

        // Extension
        $extension = substr( strrchr($source_file, '.'), 1);

        // Subdirectory?
        $subdir = (isset($this->lsettings['directory']) == true && $this->lsettings['directory'] != false) ? $this->lsettings['directory'] . '/' : '';

        // Mime type
        $filemime = 'image/jpeg';
        if ($extension == 'png') $filemime = 'image/png';
        elseif ($extension == 'gif') $filemime = 'image/gif';

        /*
        $upload_arr = array();
        $upload_arr['fileUpload'] = $source_file;
        $upload_arr['contentType'] = $filemime;
        $upload_arr['acl'] = $this->lsettings['acl'];
        $upload_arr['storage'] = $this->lsettings['storage'];
        $upload_arr['headers'] = array();

        $headers = $this->EE->config->item('ci_s3_headers');

        if ($headers != false && is_array($headers) === true)
        {
            $upload_arr['headers'] = $headers;
        }

        $response = $s3->create_object($this->lsettings['bucket'], $subdir.$dest_folder.'/'.$dest_filename, $upload_arr);
        */

        $headers = ee()->config->item('ci_s3_headers');
        if ($headers == false) $headers = array();
        $headers['Content-Type'] = $filemime;

        $response = $s3->putObject(
            $s3->inputFile($source_file),
            $this->lsettings['bucket'],
            $subdir.$dest_folder.'/'.$dest_filename,
            $this->lsettings['acl'],
            array(),
            $headers,
            $this->lsettings['storage']
        );

        // Success?
        if (!$response) {
            return $s3->response->error['message'];
        }

        return true;
    }

    // ********************************************************************************* //

    public function download_file($dir, $filename, $dest_folder)
    {
        $s3 = $this->init();

        // Subdirectory?
        $subdir = (isset($this->lsettings['directory']) == true && $this->lsettings['directory'] != false) ? $this->lsettings['directory'] . '/' : '';

        try {
            $s3->getObject($this->lsettings['bucket'], $subdir.$dir.'/'.$filename, $dest_folder.$filename);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    // ********************************************************************************* //

    public function delete_file($dir, $filename)
    {
        $s3 = $this->init();

        // Subdirectory?
        $subdir = (isset($this->lsettings['directory']) == true && $this->lsettings['directory'] != false) ? $this->lsettings['directory'] . '/' : '';

        try {
            $s3->deleteObject($this->lsettings['bucket'], $subdir.$dir.'/'.$filename);
        } catch (Exception $e) {

        }

        return false;
    }

    // ********************************************************************************* //

    public function parse_image_url($dir, $filename)
    {
        $s3 = $this->init();

        if ($this->lsettings['region'] == 'us-east-1') {
            $s3->setEndpoint('s3.amazonaws.com');
            $endpoint = 's3.amazonaws.com';
        } else {
            $s3->setEndpoint($this->lsettings['endpoint']);
            $endpoint = $this->lsettings['endpoint'];
        }

        $url = '';

        // Subdirectory?
        $subdir = (isset($this->lsettings['directory']) == true && $this->lsettings['directory'] != false) ? $this->lsettings['directory'] . '/' : '';

        if (isset($this->lsettings['cloudfront_domain']) == true && $this->lsettings['cloudfront_domain'] != false) {
            return 'http://'.$this->lsettings['cloudfront_domain']. '/'.$subdir.$dir . '/' . $filename;
        }

        if ($this->lsettings['acl'] == 'public-read')
        {
            //return 'https://'.$endpoint.'/'.$this->lsettings['bucket'].'/'.$subdir.$dir . '/' . $filename;
            return 'https://'.$this->lsettings['bucket'].'.s3.amazonaws.com/'.$subdir.$dir . '/' . $filename;
        }
        else
        {
            return $s3->getAuthenticatedURL($this->lsettings['bucket'], $subdir.$dir . '/' . $filename, 3600, false, true);
        }

        /*
        $s3->set_region($this->lsettings['region']);

        if ($this->lsettings['acl'] == 'public-read')
        {
            if (isset($this->lsettings['cloudfront_domain']) == true && $this->lsettings['cloudfront_domain'] != false)
            {
                $url = 'http://'.$this->lsettings['cloudfront_domain']. '/'.$subdir.$dir . '/' . $filename;
            }
            else
            {
                $url = $s3->get_object_url($this->lsettings['bucket'], $subdir.$dir . '/' . $filename);
            }
        }
        else
        {

            $url = $s3->get_object_url($this->lsettings['bucket'], $subdir.$dir . '/' . $filename, '60 minutes');
        }
        */

        return $url;
    }

    // ********************************************************************************* //

    public function test_location()
    {
        error_reporting(-1);
        $s3 = $this->init();

        if (!$s3) {
            exit('<div class="alert inline issue">
                AMAZON INIT FAILED. <br /> Check Key, Secret Key and Bucket
            </div>');
        }

        $s3->setExceptions(true);

        $o = '<div class="sidebar">';
        $o .= '<ul>';
        $o .= '<style type="text/css">.good {font-weight:bold; color:green} .bad {font-weight:bold; color:red}</style>';

        $bucket = trim($this->lsettings['bucket']);
        $region = $this->lsettings['region'];
        $acl = $this->lsettings['acl'];
        $storage = $this->lsettings['storage'];
        $file = uniqid(mt_rand()).'.tmp';
        $subdir = (isset($this->lsettings['directory']) == true && $this->lsettings['directory'] != false) ? $this->lsettings['directory'] . '/' : '';

        // Check for Safe Mode?
        $safemode = strtolower(@ini_get('safe_mode'));
        if ($safemode == 'on' || $safemode == 'yes' || $safemode == 'true' ||  $safemode == 1)  $o .= '<li class="remove">PHP Safe Mode (OFF): <span class="bad">Failed</span></li>';
        else $o .= '<li class="act">PHP Safe Mode (OFF): <span class="good">Passed</span></li>';

        // Does the Bucket Exist?
        try {
            $s3->getBucketLocation($bucket);
            $o .= '<li class="act">Bucket Exists?: <span class="good">Yes</span></li>';
        } catch (Exception $e) {
            $o .= '<li class="remove">Bucket Exists: <span class="bad">No</span></li>';

            try {
                $res = $s3->putBucket($bucket, $acl, $region);
                $o .= '<li class="act">Bucket Creation: <span class="good">Passed</span></li>';
            } catch (Exception $e) {
                $o .= '<li class="remove">Bucket Creation: <span class="bad">Failed</span> <br />';
                $o .= '<em>' . (string) $e->getMessage() . '</em>  </li>';
            }
        }

        // Create The File
        try {
            $res = $s3->putObject('TEST', $bucket, $subdir.$file, S3::ACL_PUBLIC_READ, array(), array('Content-Type' => 'text/plain'));
            $o .= '<li class="act">Create Test File: <span class="good">Passed</span></li>';
        }  catch (Exception $e) {
            $o .= '<li class="remove">Create Test File: <span class="bad">Failed</span> <br />';
            $o .= '<em>' . (string) $e->getMessage() . '</em> </li>';
        }


        // Delete The File
        try {
            $res = $s3->deleteObject($bucket, $subdir.$file);
            $o .= '<li class="act">Delete Test File: <span class="good">Passed</span></li>';
        }  catch (Exception $e) {
            $o .= '<li class="remove">Delete Test File: <span class="bad">Failed</span> <br />';
            $o .= '<em>' . (string) $e->getMessage() . '</em> </li>';
        }

        $o .= '</ul>';
        $o .= "<p>Even if all tests PASS, uploading can still fail due Apache/htaccess misconfiguration</p>";
        $o .= '</div>';

        return $o;
    }

    // ********************************************************************************* //

    private function init()
    {
        if (isset(self::$cachedS3[$this->lsettings['bucket']]) == true) {
            return self::$cachedS3[$this->lsettings['bucket']];
        }

        if (!self::$module) {
            self::$module = ee('App')->get('channel_images');
        }

        if ($this->lsettings['key'] == false OR $this->lsettings['secret_key'] == false OR $this->lsettings['bucket'] == false) {
            return false;
        }

        // Just to be sure
        if (class_exists('S3') === false) {
            include PATH_THIRD.'channel_images/locations/s3/s3.class.php';
        }

        // Instantiate the AmazonS3 class
        $s3 = new S3(trim($this->lsettings['key']), trim($this->lsettings['secret_key']), false);
        $s3->setExceptions(false);

        // Cache it
        self::$cachedS3[$this->lsettings['bucket']] = $s3;

        //$s3->set_region($this->lsettings['region']);

        // Init Configs
        if ($this->lsettings['storage'] == 'standard') {
            $this->lsettings['storage'] = S3::STORAGE_CLASS_STANDARD;
        } else {
            $this->lsettings['storage'] = S3::STORAGE_CLASS_RRS;
        }

        $s3Acl = self::$module->get('s3_acl');
        //$this->lsettings['acl'] = constant('AmazonS3::' . $temp[$this->lsettings['acl']]);

        $s3Regions = self::$module->get('s3_regions');
        //$this->lsettings['region'] = constant('AmazonS3::' . $temp[$this->lsettings['region']]);

        $s3Endpoints = self::$module->get('s3_endpoints');
        $this->lsettings['endpoint'] = $s3Endpoints[$this->lsettings['region']];


        return $s3;
    }

    // ********************************************************************************* //
}

/* End of file local.php */
/* Location: ./system/expressionengine/third_party/channel_images/locations/s3/s3.php */
