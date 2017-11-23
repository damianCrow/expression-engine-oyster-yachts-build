<?php

namespace Solspace\Addons\User\Model;
use Solspace\Addons\User\Library\AddonBuilder;

class Data extends AddonBuilder
{
	/**
	 * Framework object
	 * @var object
	 * @see	__construct
	 */
	protected $EE;

	/**
	 * Cache array
	 * @var array
	 */
	public $cache = array(
		'channel_ids' => array()
	);

	/**
	 * Site Id
	 * @var integer
	 * @see	__construct
	 */
	public $site_id = 1;

	/**
	 * Member Custom fields
	 * @var array
	 * @see mfields
	 */
	public $mfields	= array();

	/**
	 * Base member fields
	 * @var array
	 */
	public 	$base_fields			= array(
		'username',
		'screen_name',
		'email'
		//password is not here on purpose. NEVER
	);

	/**
	 * Standard member fields
	 * @var array
	 */
	public 	$standard			= array(
		'url',
		'location',
		'occupation',
		'interests',
		'language',
		'last_activity',
		'bday_d',
		'bday_m',
		'bday_y',
		'aol_im',
		'yahoo_im',
		'msn_im',
		'icq',
		'bio',
		'profile_views',
		'time_format',
		'timezone',
		'signature',
	);

	public 	$check_boxes		= array(
		'accept_admin_email',
		'accept_user_email',
		'notify_by_default',
		'notify_of_pm',
		'smart_notifications'
	);

	public 	$photo				= array(
		'photo_filename',
		'photo_width',
		'photo_height'
	);

	public 	$avatar				= array(
		'avatar_filename',
		'avatar_width',
		'avatar_height'
	);

	public 	$signature			= array(
		'signature',
		'sig_img_filename',
		'sig_img_width',
		'sig_img_height'
	);

	public 	$images				= array(
		'avatar' 	=> 'avatar_filename',
		'photo' 	=> 'photo_filename',
		'sig' 		=> 'sig_img_filename'
	);

	/**
	 * Fields that are integers in the table
	 *
	 * We dont want to send this as blank strings
	 * otherwise MySQL strict errors occur.
	 *
	 * @var array
	 */
	public	$int_fields			= array(
		'member_id',
		'group_id',
		'bday_d',
		'bday_m',
		'bday_y',
		'avatar_filename',
		'avatar_width',
		'avatar_height',
		'photo_width',
		'photo_height',
		'sig_img_width',
		'sig_img_height',
		'private_messages',
		'last_view_bulletins',
		'last_bulletin_date',
		'join_date',
		'last_visit',
		'last_activity',
		'total_entries',
		'total_comments',
		'total_forum_topics',
		'total_forum_posts',
		'last_entry_date',
		'last_comment_date',
		'last_forum_post_date',
		'last_email_date',
		'profile_views'
	);
	//END $this->$int_fields

	/**
	 * Timezones (Legacy)
	 *
	 * This array is used to render the localization pull-down menu
	 *
	 * (Do not re-order these. They are this way for a legacy reason)
	 *
	 * @var	array
	 */
	public $timezones = array(
		'UM12'		=> -12,
		'UM11'		=> -11,
		'UM10'		=> -10,
		'UM95'		=> -9.5,
		'UM9'		=> -9,
		'UM8'		=> -8,
		'UM7'		=> -7,
		'UM6'		=> -6,
		'UM5'		=> -5,
		'UM45'		=> -4.5,
		'UM4'		=> -4,
		'UM35'		=> -3.5,
		'UM3'		=> -3,
		'UM2'		=> -2,
		'UM1'		=> -1,
		'UTC'		=> 0,
		'UP1'		=> +1,
		'UP2'		=> +2,
		'UP3'		=> +3,
		'UP35'		=> +3.5,
		'UP4'		=> +4,
		'UP45'		=> +4.5,
		'UP5'		=> +5,
		'UP55'		=> +5.5,
		'UP575'		=> +5.75,
		'UP6'		=> +6,
		'UP65'		=> +6.5,
		'UP7'		=> +7,
		'UP8'		=> +8,
		'UP875'		=> +8.75,
		'UP9'		=> +9,
		'UP95'		=> +9.5,
		'UP10'		=> +10,
		'UP105'		=> +10.5,
		'UP11'		=> +11,
		'UP115'		=> +11.5,
		'UP12'		=> +12,
		'UP1275'	=> +12.75,
		'UP13'		=> +13,
		'UP14'		=> +14
	);
	//END $this->timezones


	// --------------------------------------------------------------------

	/**
	 * __construct
	 *
	 * @access	public
	 */

	public function __construct()
	{
		$this->site_id = ee()->config->item('site_id');
	}
	//END __construct()


	// --------------------------------------------------------------------

	/**
	 * Get the Preference for the Module for the Current Site
	 *
	 * @access	public
	 * @param	array	Array of Channel/Weblog IDs
	 * @return	array
	 */

	public function get_channel_data_by_channel_array( $channels = array() )
	{
		// --------------------------------------------
		//  Prep Cache, Return if Set
		// --------------------------------------------
		// --------------------------------------------

		$cache_name = __FUNCTION__;
		$cache_hash = $this->_imploder(func_get_args());

		if (isset($this->cache[$cache_name][$cache_hash][$this->site_id]))
		{
			return $this->cache[$cache_name][$cache_hash][$this->site_id];
		}

		$this->cache[$cache_name][$cache_hash][$this->site_id] = array();

		// --------------------------------------------
		//  Perform the Actual Work
		// --------------------------------------------

		$extra = '';

		if (is_array($channels) && count($channels) > 0)
		{
			$extra = " AND c.channel_id IN ('" .
						implode("','", ee()->db->escape_str($channels))."')";
		}

		$query = ee()->db->query(
			"SELECT c.channel_title, c.channel_id, s.site_id, s.site_label
			 FROM exp_channels AS c, exp_sites AS s
			 WHERE s.site_id = c.site_id
			 {$extra}"
		);

		foreach($query->result_array() as $row)
		{
			$this->cache[$cache_name][$cache_hash][
				$this->site_id
			][$row['channel_id']] = $row;
		}

		// --------------------------------------------
		//  Return Data
		// --------------------------------------------

		return $this->cache[$cache_name][$cache_hash][$this->site_id];
	}
	// END get_channel_data_by_channel_array


	// --------------------------------------------------------------------

	/**
	 * Get Channel Id Preference
	 *
	 * @access	public
	 * @param	int		$id		channel_id to get info for
	 * @return	array			array of pref data for channel_id
	 */

	function get_channel_id_pref($id)
	{
		//cache?
		if (isset($this->cache['channel_ids'][$id]))
		{
			return $this->cache['channel_ids'][$id];
		}

		$channel_data = $this->get_channel_ids();

		if ( isset($channel_data[$id]) )
		{
			$this->cache['channel_ids'][$id] = $channel_data[$id];
			return $this->cache['channel_ids'][$id];
		}
		else
		{
			return array();
		}
	}
	//END get_channel_id_pref

	// --------------------------------------------------------------------

	/**
	 * Gets channel ids' from preferences
	 *
	 * @access	public
	 * @param	boolean	$use_cache	use cached data or get fresh?
	 * @return 	array				array of channel ids
	 */

	public function get_channel_ids($use_cache = TRUE)
	{
		//cache?
		if ($use_cache AND isset($this->cache['channel_ids']['full']))
		{
			return $this->cache['channel_ids']['full'];
		}

		$query = ee()->db
						->select('preference_value')
						->where('preference_name', 'channel_ids')
						->get('user_preferences');

		if ($query->num_rows() == 0)
		{
			return FALSE;
		}

		$this->cache['channel_ids']['full'] = unserialize(
			$query->row('preference_value')
		);

		return $this->cache['channel_ids']['full'];
	}
	//END get_channel_ids


	function get_sites()
	{
		//--------------------------------------------
		// SuperAdmins Alredy Have All Sites
		//--------------------------------------------

		if (isset(ee()->session) AND
			is_object(ee()->session) AND
			isset(ee()->session->userdata['group_id']) AND
			ee()->session->userdata['group_id'] == 1 AND
			isset(ee()->session->userdata['assigned_sites']) AND
			is_array(ee()->session->userdata['assigned_sites']))
		{
			return ee()->session->userdata['assigned_sites'];
		}

		//--------------------------------------------
		// Prep Cache, Return if Set
		//--------------------------------------------

		$cache_name = __FUNCTION__;
		$cache_hash = $this->_imploder(func_get_args());

		if (isset($this->cache[$cache_name][$cache_hash]))
		{
			return $this->cache[$cache_name][$cache_hash];
		}

		$this->cache[$cache_name][$cache_hash] = array();

		//--------------------------------------------
		// Perform the Actual Work
		//--------------------------------------------

		if (ee()->config->item('multiple_sites_enabled') == 'y')
		{
			$sites_query = ee()->db
							->select('site_id, site_label')
							->order_by('site_label')
							->get('sites');

		}
		else
		{
			$sites_query = ee()->db
							->select('site_id, site_label')
							->where('site_id', 1)
							->get('sites');

		}

		foreach($sites_query->result_array() as $row)
		{
			$this->cache[$cache_name][$cache_hash][$row['site_id']] = $row['site_label'];
		}

		//--------------------------------------------
		// Return Data
		//--------------------------------------------

		return $this->cache[$cache_name][$cache_hash];
	}
	//END get_sites()


	// --------------------------------------------------------------------

	/**
	 * Implodes an Array and Hashes It
	 *
	 * @access	public
	 * @param	array	$args	arguments to hash
	 * @return	string			hashed args
	 */

	public function _imploder ($args)
	{
		return md5(serialize($args));
	}
	// END _imploder


	// --------------------------------------------------------------------

	/**
	 * Template Parser Class Usable
	 *
	 * @access	public
	 * @return	boolean		present and usable ee()->TMPL
	 */

	public function tmpl_usable()
	{
		return isset(ee()->TMPL) &&
				is_object(ee()->TMPL) &&
				//this in case someone sets a varialble to it on
				//accident and auto-instanciates it as stdClass.
				is_callable(array(ee()->TMPL, 'fetch_param'));
	}
	//END tmpl_usable


	// --------------------------------------------------------------------

	/**
	 * Member Fields
	 *
	 * @access	public
	 * @return	array	array of custom member fields with fieldname as key
	 */

	public function mfields()
	{
		if ($this->tmpl_usable() AND
			ee()->TMPL->fetch_param('disable') !== FALSE AND
			stristr('member_data', ee()->TMPL->fetch_param('disable'))
		)
		{
			return array();
		}

		if (count($this->mfields) > 0)
		{
			return $this->mfields;
		}

		$fields	= ee('Model')
			->get('MemberField')
			->all();

		foreach ($fields as $row)
		{
			$this->mfields[$row->m_field_name] = array(
				'id'			=> $row->m_field_id,
				'name'			=> $row->m_field_name,
				'label'			=> $row->m_field_label,
				'type'			=> $row->m_field_type,
				'list'			=> $row->m_field_list_items,
				'required'		=> $row->m_field_required,
				'public'		=> $row->m_field_public,
				'format'		=> $row->m_field_fmt,
				'description'	=> $row->m_field_description
			);
		}

		return $this->mfields;
	}
	//EMD mfields


	// --------------------------------------------------------------------

	/**
	 * Member Fields By ID
	 *
	 * @access	public
	 * @return	array	member fields keyed by ID instead of name
	 */

	public function mfields_by_id()
	{
		$mfields = $this->mfields();

		$return = array();

		foreach ($mfields as $name => $data)
		{
			$return[$data['id']] = $data;
		}

		return $return;
	}
	//END mfields_by_id


	// --------------------------------------------------------------------

	/**
	 * Returns the default member fields (non custom)
	 *
	 * @access	public
	 * @return	Array		name=>label array of member fields
	 */

	public function get_default_fields()
	{
		//just in case these didn't get loaded
		//load file is cached so this isn't
		//a performance hit
		ee()->lang->loadfile('myaccount');
		ee()->lang->loadfile('member');
		//last in case we override something on accident.
		ee()->lang->loadfile('user');

		$out = array();

		foreach (array_merge($this->base_fields, $this->standard) as $line)
		{
			$out[$line] = lang('mbr_' . $line);

			if ($out[$line] == 'mbr_' . $line)
			{
				$out[$line] = lang($line);
			}
		}

		return $out;
	}
	//END get_default_fields
}
// END CLASS Tag_data
