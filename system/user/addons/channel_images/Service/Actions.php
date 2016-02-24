<?php

namespace DevDemon\ChannelImages\Service;

class Actions
{
    public $actions = array();

    public function __construct()
    {
        $this->getActions();
    }

    public function getActions()
    {
        if (empty($this->actions) == false) {
            return $this->actions;
        }

        if (class_exists('ImageAction') == false) {
            include(PATH_THIRD.'channel_images/actions/imageaction.php');
        }

        // Get the files & sort
        $files = scandir(PATH_THIRD.'channel_images/actions/');
        sort($files);

        if (is_array($files) === false || count($files) == 0) return;

        // Loop over all fields
        foreach ($files as $file) {
            // The file must start with: action.
            if (strpos($file, 'action.') === 0) {

                // Get the class name
                $name = str_replace(array('action.', '.php'), '', $file);
                $class = 'ImageAction_'.$name;

                // Load the file
                $path = PATH_THIRD.'channel_images/actions/'.$file;
                require_once $path;

                // Does the class exists now?
                if (class_exists($class) === false) continue;

                $obj = new $class();

                // Is it enabled? ready to use?
                if (isset($obj->info['enabled']) == false OR $obj->info['enabled'] == false) continue;

                // Store it!
                $this->actions[$name] = $obj;

                // We need to be sure it's formatted correctly
                if (isset($obj->info['title']) == false) unset($this->actions[$name]);
                if (isset($obj->info['name']) == false) unset($this->actions[$name]);
            }
        }

        return $this->actions;
    }
}

/* End of file Actions.php */
/* Location: ./system/user/addons/channel_images/Service/Actions.php */