<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Module File
 *
 * @package         DevDemon_Editor
 * @author          DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright       Copyright (c) 2007-2016 Parscale Media <http://www.parscale.com>
 * @license         http://www.devdemon.com/license/
 * @link            http://www.devdemon.com
 * @see             https://ellislab.com/expressionengine/user-guide/development/modules.html
 */
class Editor
{
    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->site_id = ee()->config->item('site_id');
        $this->moduleSettings = ee('editor:Settings')->settings;
    }

    // ********************************************************************************* //

    public function actionGeneralRouter()
    {
        if (ee('Request')->get('method') == 'browseImages') {
            return $this->browseImages();
        }

        if (ee('Request')->get('method') == 'browseFiles') {
            return $this->browseFiles();
        }
    }

    // ********************************************************************************* //

    public function actionFileUpload()
    {
        ee()->load->library('filemanager');
        ee()->load->helper('directory');

        @header('Access-Control-Allow-Origin: *');
        @header('Access-Control-Allow-Credentials: true');
        @header('Access-Control-Max-Age: 86400');
        @header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        @header('Access-Control-Allow-Headers: Keep-Alive, Content-Type, User-Agent, Cache-Control, X-Requested-With, X-File-Name, X-File-Size');

        // -----------------------------------------
        // Increase all types of limits!
        // -----------------------------------------
        @set_time_limit(0);
        @ini_set('memory_limit', '64M');
        @ini_set('memory_limit', '96M');
        @ini_set('memory_limit', '128M');
        @ini_set('memory_limit', '160M');
        @ini_set('memory_limit', '192M');

        error_reporting(E_ALL);
        @ini_set('display_errors', 1);

        // -----------------------------------------
        // S3
        // -----------------------------------------
        if (ee('Request')->get('bucket') != false) {
            $bucket = ee('Request')->get('bucket');
            $filename = ee('Request')->get('key');
            exit(stripslashes('{ "filelink": "https://'.$bucket.'.s3.amazonaws.com/'.$filename.'", "filename": "'.$filename.'" }'));
        }

        if (ee('Request')->get('action') == false) {
            exit('{"error":"Missing Action or this is just an ACT URL test"}');
        }

        $action = ee('Request')->get('action');
        //if ($action == 'image_browser') $this->get_image_dir_json();
        if ($action == 's3_info') $this->s3Info();


        // -----------------------------------------
        // Local File Upload
        // -----------------------------------------
        if (ee('Request')->get('upload_location') === false) {
            exit('{"error":"No Upload destination defined"}');
        }

        $location_id = ee('Request')->get('upload_location');
        $location = ee('Model')->get('UploadDestination', $location_id)->first();

        if ($action == 'image') $image_only = TRUE;
        else $image_only = false;

        $file = ee()->filemanager->upload_file($location_id, 'file', $image_only);

        if (array_key_exists('error', $file)) {
            return '{"error":"'.strip_tags($file['error']).'"}';
        }

        if ($action == 'file') {
            exit(stripslashes('{ "url": "'.$location->url.$file['file_name'].'", "name": "'.$file['file_name'].'" }'));
        }

        if ($action == 'image') {
            exit(stripslashes('{ "url": "'.$location->url.$file['file_name'].'" }'));
        }

        exit('{"error":"Something went wrong"}');
    }

    // ********************************************************************************* //

    public function s3Info()
    {
        $s3 = ee('Request')->get('s3');
        if ($s3 == false) {
            exit();
        }

        $s3 = base64_decode($s3);
        $s3 = ee('editor:Helper')->decryptString($s3);
        $s3 = json_decode($s3, true);
        if ($s3 == false) {
            exit();
        }

        $s3Regions = ee('App')->get('editor')->get('s3_regions');

        $S3_KEY = $s3['aws_access_key'];
        $S3_SECRET = $s3['aws_secret_key'];
        $S3_BUCKET = '/' . $s3['images']['bucket']; // bucket needs / on the front
        $S3_ENDPOINT = $s3Regions[ $s3['images']['region'] ];

        // expiration date of query
        $EXPIRE_TIME = (60 * 10); // 10 minutes

        $filename = ee('Request')->get('name');
        $filename = strtolower(ee()->security->sanitize_filename($filename));
        $filename = str_replace(array(' ', '+', '%'), array('_', '', ''), $filename);

        // not an image?
        $extension = substr( strrchr($filename, '.'), 1);

        if (in_array($extension, array('jpg', 'jpeg', 'png', 'gif', 'bmp')) === false) {
            $S3_BUCKET = '/' . $s3['files']['bucket']; // bucket needs / on the front
            $S3_ENDPOINT = $s3Regions[ $s3['files']['region'] ];
        }

        $objectName = '/' . $filename;

        $S3_URL = 'https://'. $S3_ENDPOINT;

        $mimeType = ee('Request')->get('type');
        $expires = time() + $EXPIRE_TIME;
        $amzHeaders = "x-amz-acl:public-read";
        $stringToSign = "PUT\n\n{$mimeType}\n{$expires}\n{$amzHeaders}\n{$S3_BUCKET}{$objectName}";

        $sig = urlencode(base64_encode(hash_hmac('sha1', $stringToSign, $S3_SECRET, true)));
        $url = "{$S3_URL}{$S3_BUCKET}{$objectName}?AWSAccessKeyId={$S3_KEY}&Expires={$expires}&Signature={$sig}";

        echo $url;
        exit();
    }

    // ********************************************************************************* //

    private function browseFiles()
    {
        ee()->lang->loadfile('filemanager');
        $uploadLocation = ee('Request')->get('upload_location');

        // Adapted from http://jeffreysambells.com/2012/10/25/human-readable-filesize-php
        $size   = array('b', 'kb', 'mb', 'gb', 'tb', 'pb', 'eb', 'zb', 'yb');

        $items = array();
        $files = ee('Model')->get('File')
            ->with('UploadDestination')
            ->filter('upload_location_id', $uploadLocation)
            ->filter('UploadDestination.module_id', 0);

        foreach ($files->all() as $file) {
            $factor = floor((strlen($file->file_size) - 1) / 3);

            $items[] = array(
                'size'  =>  sprintf("%d", $file->file_size / pow(1024, $factor)) . lang('size_' . @$size[$factor]),
                'url'   => $file->getAbsoluteURL(),
                'title' => $file->title,
                'name' => $file->file_name,
                'id'    => $file->file_id,
            );
        }

        return ee()->output->send_ajax_response($items);
    }

    // ********************************************************************************* //

    private function browseImages()
    {
        $uploadLocation = ee('Request')->get('upload_location');

        $items = array();
        $files = ee('Model')->get('File')
            ->with('UploadDestination')
            ->filter('upload_location_id', $uploadLocation)
            ->filter('UploadDestination.module_id', 0);

        foreach ($files->all() as $file) {
            $items[] = array(
                'thumb' => $file->getAbsoluteThumbnailURL(),
                'url'   => $file->getAbsoluteURL(),
                'title' => $file->title,
                'id'    => $file->file_id,
            );
        }

        return ee()->output->send_ajax_response($items);
    }

    // ********************************************************************************* //

} // END CLASS

/* End of file mod.editor.php */
/* Location: ./system/user/addons/editor/mod.editor.php */