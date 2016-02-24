<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Channel Images Action File
 *
 * @package         DevDemon_ChannelImages
 * @author          DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright       Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license         http://www.devdemon.com/license/
 * @link            http://www.devdemon.com/channel_images/
 */
class ImageAction
{
    public $image_jpeg_quality = 100;
    public $image_progressive = false;

    public static $imagePath;
    public static $imageExt;
    public static $imageResource;
    public static $imageDimensions;

    /**
     * Action info - Required
     *
     * @access public
     * @var array
     */
    public $info = array(
        'title'     =>  '',
        'name'      =>  '',
        'version'   =>  '1.0',
        'enabled'   =>  true,
    );

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->field_name = 'channel_images[action_groups][][actions]['.$this->info['name'].']';
    }

    // ********************************************************************************* //

    public function run($file, $temp_dir)
    {
        return true;
    }

    // ********************************************************************************* //

    public function settings()
    {
        return '';
    }

    // ********************************************************************************* //

    public function display_settings($settings=array())
    {
        // Final Output
        $out = '';

        $action_path = PATH_THIRD . 'channel_images/actions/' . $this->info['name'] . '/';

        // Add package path (so view files can render properly)
        ee()->load->add_package_path($action_path);

        // Do we need to load LANG file?
        if (@is_dir($action_path . 'language/') == true) {
             ee()->lang->load($this->info['name'], ee()->lang->user_lang, false, true, $action_path);
        }

        // Add some global vars!
        $vars = array();
        $vars['action_field_name'] = $this->field_name;
        ee()->load->vars($vars);

        // Execute the settings method
        $out = $this->settings($settings);

        // Cleanup by removing
        ee()->load->remove_package_path($action_path);

        return $out;
    }

    // ********************************************************************************* //

    public function save_settings($settings)
    {
        return $settings;
    }

    // ********************************************************************************* //

    public function open_image($file)
    {
        // Is it already open?
        if (self::$imagePath == $file)  return true;

        // Is there another once open? close it!
        if (is_resource(self::$imageResource) === true)
        {
            @imagedestroy(self::$imageResource);
        }

        $data = @getimagesize($file);

        if (!$data) return false;

        self::$imageDimensions['width'] = $data[0];
        self::$imageDimensions['height'] = $data[1];

        // Get the image extension
        self::$imageExt = '';

        switch ($data[2])
        {
            case IMAGETYPE_GIF:
                self::$imageExt = 'gif';
                            $ext = 'gif';
                break;
            case IMAGETYPE_PNG:
                self::$imageExt = 'png';
                             $ext = 'png';
                break;
            case IMAGETYPE_JPEG:
                self::$imageExt = 'jpg';
                             $ext = 'jpg';
                break;
            default:
                return false;
        }

        //open the file and create main image handle
        switch (self::$imageExt)
        {
            case 'gif':
                self::$imageResource = @imagecreatefromgif($file);
                break;
            case 'png':
                self::$imageResource = @imagecreatefrompng($file);
                break;
            case 'jpg':
            case 'jpeg':
                self::$imageResource = @imagecreatefromjpeg($file);
                break;
            default:
                return false;
        }


        if (self::$imageResource == false) return false;

        return true;
    }

    // ********************************************************************************* //

    public function save_image($dest_file='', $resource=false, $extension=false)
    {
        if ($resource == false)
        {

            $resource =& self::$imageResource;
            if (!$extension) $extension = self::$imageExt;
        }

        if ($this->image_jpeg_quality == false)
        {
            $this->image_jpeg_quality = 100;
        }

        switch ($extension)
        {
            case 'gif':
                imagegif($resource, $dest_file);
                break;
            case 'png':
                imagepng($resource, $dest_file);
                break;
            case 'jpg':
            case 'jpeg':

                // Do we need to store progressive jpeg?
                if ($this->image_progressive == true) @imageinterlace($resource, 1);
                imagejpeg($resource, $dest_file, $this->image_jpeg_quality);
                break;
            default:
                return false;
        }

        return true;
    }

    // ********************************************************************************* //

    public function getImageDetails($file)
    {
        $info = array();

        $data = @getimagesize($file);
        if (!$data) return false;

        $info['width'] = $data[0];
        $info['height'] = $data[1];

        // Get the image extension
        ee('channel_images:Helper')->image_ext = '';

        switch ($data[2])
        {
            case IMAGETYPE_GIF:
                $info['ext'] = 'gif';
                break;
            case IMAGETYPE_PNG:
                $info['ext'] = 'png';
                break;
            case IMAGETYPE_JPEG:
                $info['ext'] = 'jpg';
                break;
            default:
                return false;
        }

        return $info;
    }

    // ********************************************************************************* //

} // END CLASS

/* End of file imageaction.php  */
/* Location: ./system/expressionengine/third_party/channel_images/actions/imageaction.php */
