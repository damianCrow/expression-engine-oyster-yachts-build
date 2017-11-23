<?php

class User_ft extends EE_Fieldtype
{
	public	$info	= array(
		'name'		=> 'User',
		'version'	=> ''
	);

	public $field_name	= 'default';
	public $field_id	= 'default';

	public $has_array_data = true;

	protected $stored_vars = array();

	protected $field_obj;

	protected $field_sub_type_dir;

	protected $field_choices;

	protected $base_name = '';

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access	public
	 */

	public function __construct()
	{
		parent::__construct();

		$this->info = require 'addon.setup.php';

		ee()->lang->loadfile('user');

		$this->field_id 	= isset($this->settings['field_id']) ?
								$this->settings['field_id'] :
								$this->field_id;
		$this->field_name 	= isset($this->settings['field_name']) ?
								$this->settings['field_name'] :
								$this->field_name;

		$this->field_name = 'field_id_' . $this->field_id;

		$this->field_sub_type_dir = rtrim(dirname(__FILE__), '/') . '/fieldtypes/';

		ee()->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' . URL_THIRD_THEMES . 'super_search/css/solspace-fa.css">');
	}

	//END __construct

	// --------------------------------------------------------------------

	/**
	 * Display Settings
	 *
	 * @access	public
	 * @param	array  $settings	incoming saved settings
	 * @return	void
	 */

	public function display_settings($settings = array())
	{
		$choices = $this->get_field_choices();

		$dropdown = array();

		foreach ($choices as $choice)
		{
			$dropdown[$choice] = lang($choice);
		}

		$settings = array(
			'user' => array(
				'label'		=> 'user',
				'group'		=> 'user',
				'settings' => array(
					array(
						'title' => 'choose_field_sub_type',
						'desc' => 'user_sub_field_notice',
						'fields' => array(
							'user_sub_field' => array(
								'type' 		=> 'select',
								'choices'	=> $dropdown,
								'value' => (isset($settings['user_sub_field'])) ? $settings['user_sub_field']: ''
							)
							//end 'user_sub_field' => array(
						)
						//END 'fields' => array(
					),
					//end 'settings' => array( [0=>] array(
				)
				//end 'settings' => array(
			),
			//end 'tag' => array(
		);
		//end $settings = array(

		return $settings;
	}

	//END display_settings

	// --------------------------------------------------------------------

	/**
	 * Save Settings
	 *
	 * @access	public
	 * @param	array	$data	incoming field array data (just use POST)
	 * @return	array			key=>value store of settings to be saved
	 */

	public function save_settings($data)
	{
		$choices = $this->get_field_choices();
		$choice = ee()->input->get_post('user_sub_field');

		return array(
			'user_sub_field' => in_array($choice, $choices) ? $choice :	''
		);
	}
	//END save_settings


	// -------------------------------------
	//	forward incoming calls if possible
	// -------------------------------------

	public function __get($name)
	{
		$this->set_field_object();

		return $this->field_obj->$name;
	}

	public function __set($name, $value)
	{
		$this->set_field_object();

		return $this->field_obj->$name = $value;
	}

	public function __isset($name)
	{
		$this->set_field_object();

		return isset($this->field_obj->$name);
	}

	//this should handle any special replace_tag items we have too
	public function __call($method, $args)
	{
		$this->set_field_object();

		return call_user_func_array(array($this->field_obj, $method), $args);
	}


	//because we cannot use '__call' here.
	public function replace_tag($data, $params = array(), $tagdata = false)
	{
		return $this->__call(__FUNCTION__, func_get_args());
	}

	public function pre_process($data)
	{
		return $this->__call(__FUNCTION__, func_get_args());
	}

	public function validate($data)
	{
		return $this->__call(__FUNCTION__, func_get_args());
	}

	public function display_publish_field($data)
	{
		return $this->__call(__FUNCTION__, func_get_args());
	}

	public function save($data)
	{
		return $this->__call(__FUNCTION__, func_get_args());
	}

	public function post_save($data)
	{
		return $this->__call(__FUNCTION__, func_get_args());
	}

	public function delete($ids)
	{
		return $this->__call(__FUNCTION__, func_get_args());
	}

	//required argument by the parent abstract
	public function display_field($data)
	{
		return $this->__call(__FUNCTION__, func_get_args());
	}

	//There are other class methods like grid stuff but we aren't using those
	//just yet. If this is ever copy pasted, anything public from EE_Fieldtype
	//needs to have its fowarding here and in $this->set_field_objects for
	//variables predefined in this class or EE_fieldtype.

	// --------------------------------------------------------------------
	// protected/private methods
	// --------------------------------------------------------------------

	// --------------------------------------------------------------------

	/**
	 * Get Field Choices
	 *
	 * @access	protected
	 * @return	array		array of field choices
	 */

	protected function get_field_choices()
	{
		if ( ! empty($this->field_choices))
		{
			return $this->field_choices;
		}

		$iterator = new FilesystemIterator(
			$this->field_sub_type_dir,
			FilesystemIterator::SKIP_DOTS |
			FilesystemIterator::UNIX_PATHS |
			FilesystemIterator::FOLLOW_SYMLINKS
		);

		$this->field_choices = array();

		$name_match = "/^user_([\w\-\_]+)_fieldtype\.php$/ims";

		foreach (iterator_to_array($iterator) as $fileinfo)
		{
			$file_name = $fileinfo->getFilename();

			if ($fileinfo->isFile() AND preg_match($name_match, $file_name))
			{
				$this->field_choices[] = preg_replace($name_match, '$1', $file_name);
			}
		}

		return $this->field_choices;
	}

	//END get_field_choices

	// --------------------------------------------------------------------

	/**
	 * Set Field Object
	 *
	 * Set the object for the selected field
	 *
	 * @access	protected
	 * @return	void
	 */

	protected function set_field_object()
	{
		if (isset($this->settings))
		{
			if ( ! isset($this->field_obj) ||
				//needed for running for multiple differnet types of fields
				//where the fields are differnet
				(
					isset($this->settings['user_sub_field']) &&
					get_class($this->field_obj) !=
						$this->base_name . $this->settings['user_sub_field'] . '_fieldtype'
				)
			)
			{
				//default
				$field_class		= 'roles';
				$lower_name_tmpl	= 'user_{name}_fieldtype';
				$file_uri_tmpl		= $this->field_sub_type_dir . '{lower_name}.php';

				// -------------------------------------
				//	setting for field?
				// -------------------------------------

				if (isset($this->settings['user_sub_field']))
				{
					$temp_class			= $this->settings['user_sub_field'];
					$temp_lower_name	= str_replace('{name}', $temp_class, $lower_name_tmpl);
					$temp_uri			= str_replace('{lower_name}', $temp_lower_name, $file_uri_tmpl);

					if (is_file($temp_uri))
					{
						$field_class = $temp_class;
					}
				}

				// -------------------------------------
				//	set and try
				// -------------------------------------

				$lower_name	= str_replace('{name}', $field_class, $lower_name_tmpl);
				//need this since we are allowing outside input to dictate here
				$lower_name = ee()->security->sanitize_filename($lower_name);
				$class_name	= ucfirst($lower_name);
				$file_uri	= str_replace('{lower_name}', $lower_name, $file_uri_tmpl);

				require_once $file_uri;

				if (class_exists($class_name))
				{
					$this->field_obj = new $class_name;
				}
				else
				{
					throw new Exception(lang('user_field_class_not_found') . $class_name);
				}
			}
			//END if ( ! isset($this->field_obj))

			//we want these to update whenever because we cnanot
			//use __set and __get to do anything about them
			//since the parent class has these listed already
			//this is one way but the child field should never
			//be setting these explicitly
			$this->field_obj->settings		= $this->settings;
			$this->field_obj->field_id		= $this->field_id;
			$this->field_obj->field_name	= $this->field_name;

			foreach (array('content_type', 'content_id', 'row') as $possible)
			{
				//are we during an edit?
				if (isset($this->$possible))
				{
					$this->field_obj->$possible = $this->$possible;
				}
			}
		}
		//END if (isset($this->settings))
	}
	//END set_field_object


	// --------------------------------------------------------------------

	/**
	 * Update
	 *
	 * This is required for EE to update fieldtype version numbers.
	 * (This might be fixed in an upcoming update?)
	 * https://support.ellislab.com/bugs/detail/21524/fieldtypes-with-accompanying-modules-never-update-version-number-without-th
	 *
	 * @access	public
	 * @param	string $version		current version number coming from EE
	 * @return	boolean				should EE update the version number.
	 */

	public function update($version)
	{
		return ($version != $this->info['version']);
	}
	//END update
}
//END User_ft
