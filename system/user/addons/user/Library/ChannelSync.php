<?php

namespace Solspace\Addons\User\Library;
use Solspace\Addons\User\Library\AddonBuilder;

class ChannelSync extends AddonBuilder
{
	public $error					= false;
	public $channel_id				= 0;
	public $channel_info			= array();
	public $channel_sync_pref_list	= array(
		'channel_sync_channel',
		'channel_sync_field_map',
		'sync_member_to_channel',
		//'sync_channel_to_member',
		'delete_entry_on_member_delete'
	);
	public $old_group_id			= 0;

    // --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	null
	 */

	public function __construct()
	{
		parent::__construct();
	}

	// End constructor

	// --------------------------------------------------------------------
	//	Public methods
	// --------------------------------------------------------------------

	// --------------------------------------------------------------------

	/**
	 * Channel Data for Members Enabled?
	 *
	 * @access	public
	 * @return	boolean		enabled, yes or now
	 */

	public function enabled()
	{
		//cached
		$prefs = $this->get_sync_prefs();

		return (
			! empty($prefs['channel_sync_channel']) &&
			$this->is_positive_intlike($prefs['channel_sync_channel'])
		);
	}
	//END enabled


	// --------------------------------------------------------------------

	/**
	 * Parse As Channel Form
	 *
	 * @access	public
	 * @param	string	$tagdata	incoming template data to treat as channel form
	 * @return	string				parsed tagdata
	 */

	public function parse_as_channel_form($options = array())
	{
		// -------------------------------------
		//	Set options to local vars
		// -------------------------------------

		$defaults = array(
			'tagdata'		=> '',
			'edit'			=> false,
			'entry_id'		=> 0,
			'channel'		=> $this->get_data_channel(),
			'member_id'		=> 0
		);

		foreach ($defaults as $key => $value)
		{
			$$key = (isset($options[$key])) ? $options[$key] : $defaults[$key];
		}

		// -------------------------------------
		//	emptiness?
		// -------------------------------------

		if (empty(ee()->TMPL) ||
			! $this->get_data_channel() ||
			! $this->enabled())
		{
			return $tagdata;
		}

		$site_id = $this->get_site_id();

		// -------------------------------------
		//	load Channel libs
		// -------------------------------------

		$this->load_channel_libraries();

		$this->set_data_channel($channel);

		// -------------------------------------
		//	edit?
		//	This will set us up correctly both
		//	ways because if there is no
		//	entry_id for the user we will just
		//	get a new one on submit
		// -------------------------------------

		if ($edit)
		{
			if (! $this->is_positive_intlike($member_id))
			{
				$member_id = ee()->session->userdata('member_id');
			}

			if (! $this->is_positive_intlike($entry_id))
			{
				$result = $this->fetch('MemberChannelEntry')
					->filter('member_id', $member_id)
					->filter('site_id', $site_id)
					->first();

				if ($result)
				{
					$entry_id = $result->entry_id;
				}
			}
			//end if ( ! $this->is_positive_intlike($entry_id))

			if ($this->is_positive_intlike($entry_id))
			{
				ee()->TMPL->tagparams['entry_id'] = $entry_id;
			}
		}
		//END if ($edit)

		// -------------------------------------
		//	Wrap tagdata in key so we can remove
		//	additions from and adjust
		// -------------------------------------

		$key = '7eb95917c27b4ff968aa0a6504ee01a7';

		$td_temp = ee()->TMPL->tagdata;

		ee()->TMPL->tagdata = $key . $tagdata . $key;

		//setup temp assigned channels

		$out = '';

		// -------------------------------------
		//	Handle logged out state
		// -------------------------------------

		if (ee()->session->userdata('member_id') == 0)
		{
			//$this->set_logged_out_member_id();
			ee()->TMPL->tagparams['logged_out_member_id']	= $this->get_first_admin_id();
		}

		// -------------------------------------
		//	run entry form
		// -------------------------------------

		try
		{
			$out = ee()->channel_form_lib->entry_form();
		}
		catch (Channel_form_exception $e)
		{
			$this->errors = array($e->getMessage());
		}

		//return tagdata to normal
		ee()->TMPL->tagdata = $td_temp;

		$this->fix_load_stack();

		if (empty($out))
		{
			return '';
		}

		if ( ! empty($this->errors))
		{
			return $tagdata;
		}

		// -------------------------------------
		//	remove form wrapper and hidden fields
		//	that we don't want overridding our own
		// -------------------------------------

		preg_match(
			"/^(.*)?" . preg_quote($key, '/') .
				"(.*)?" .
			preg_quote($key, '/') . "(.*)?/s",
			$out,
			$matches
		);

		if (empty($matches))
		{
			return str_replace($key, '', $out);
		}

		//most of this is already set inside
		//of the encrypted $_POST['meta'] tags
		//and we set channel and author_id ourselves
		//entry_id is moot as this isn't and edit
		//and site_id again is moot
		$start	= preg_replace(
			array(
				'/<form[^>]+>/is',
				'/<input[\s]+type="hidden"[\s]+name="(:?author_id|channel_id|entry_id|site_id|return|return_url|RET|XID|ACT)"[\s]+value="[^>]+"[\s]+\/>/is',
			),
			'',
			$matches[1]
		);

		//Required for some native fields, but since CSS parallel loads anyway,
		//lets just add it instead of doing a bunch of DB calls to see if we
		//need it. If its a problem we can always let a pram disable it.
		$start .= '<link href="{path=css/_ee_channel_form_css}" type="text/css" rel="stylesheet" media="screen" />';

		$body	= $matches[2];

		//we are adding our own ending items.
		$end	= str_replace('</form>', '', $matches[3]);
		//fix for EE 2.7 JS bug
		$end	= str_replace('[![Link:!:http://]', '[![Link:!:http:\\/\\/]', $end);
		//fixes a bug with dual jQuery's on the same page.
		$end	= str_replace('$(document).ready(function()', 'jQuery(document).ready(function($)', $end);

		return $start . $body . $end;
	}

	//END parse_as_channel_form


	// --------------------------------------------------------------------

	/**
	 * Submit Entry
	 *
	 * @access	public
	 * @return	mixed		boolean false if error
	 */

	public function submit_entry($options = array())
	{
		if ( ! $this->enabled())
		{
			return false;
		}
		// -------------------------------------
		//	Set options to local vars
		// -------------------------------------

		$defaults = array(
			'edit'					=> false,
			'entry_id'				=> 0,
			'channel'				=> 0,
			'validate_only'			=> false,
			'data'					=> array(),
			'member_data'			=> array(),
			'member_custom_fields'	=> array(),
			'member_id'				=> 0
		);

		foreach ($defaults as $key => $value)
		{
			$$key = (isset($options[$key])) ? $options[$key] : $defaults[$key];
		}

		$site_id = $this->get_site_id();

		// -------------------------------------
		//	no member id? falure
		// -------------------------------------

		if ($edit && ! $this->is_positive_intlike($member_id))
		{
			$this->lib('Utils')->full_stop(lang('channel_sync_member_id_required'));
		}

		// -------------------------------------
		//	load libs
		// -------------------------------------

		$this->load_channel_libraries();

		$this->set_data_channel($channel);

		//So the extension method channel_form_submit_entry_end
		//can halt the redirect for us instead of letting it
		//redirect.
		$this->cache['halt_channel_form'] = true;

		// -------------------------------------
		//	default result
		// -------------------------------------

		$result = array(
			'success'		=> false,
			'errors'		=> array(),
			'field_errors'	=> array(),
			//these are already set in the posted
			//meta encrypted data so we setup an edit
			//with parse_as_channel_form
			//'entry_id'		=> 0,
			//'url_title'		=> '',
			'channel_id'	=> 0,
			'validate'		=> $validate_only,
			'entry'			=> array()
		);

		// -------------------------------------
		//	map incoming User fields
		// -------------------------------------

		//we are going to get the old title from existing
		//data so we don't want it forcing on edit.
		//If we are changing the title, then this will
		//work as normal.
		$this->map_member_fields($member_id, true, ! $edit);

		// -------------------------------------
		//	work
		// -------------------------------------

		$this->set_logged_out_member_id();

		if ($edit)
		{
			$_POST['edit_date'] = ee()->localize->now;

			// -------------------------------------
			//	get old data and merge
			// -------------------------------------

			$entry = ee('Model')
				->get('ChannelEntry', $entry_id)
				->first();

			if ($entry)
			{
				$old_data = $entry->toArray();

				//need to let our new data override old
				$_POST = array_merge(
					$old_data,
					$_POST
				);
			}
			//END if ($old_data_q->num_rows() > 0)
		}
		//END if ($edit)

		try
		{
			//just validating? We need channel_form_submit_entry_start
			//to insert a dummy error so this wont submit fully if there
			//are no errors to report.
			if ($validate_only)
			{
				$this->cache['insert_dummy_cf_error'] = true;
			}

			if ( ! isset($_POST['entry_date']))
			{
				$_POST['entry_date'] = date('Y-m-d H:i', ee()->localize->now);
			}
			//EE _requires_ incoming dates to be Y-m-d H:i.
			//When we run validation or if this is inserted some other way
			//its a unix timestamp and throws errors.
			//When we run this the first time as a validator before saving
			//the entry, for some dumb reason, the value gets converted even
			//on the $_POST var so that the second time that this runs, it
			//throws an error but the first time it works fine. FUN.
			else if (preg_match("/^[\d]{10}$/", trim((string) $_POST['entry_date'])))
			{
				$_POST['entry_date'] = date('Y-m-d H:i', $_POST['entry_date']);
			}

			//	Various jive ass fixes
			unset($_POST['recent_comment_date']);
			$_POST['versioning_enabled']	= (empty($_POST['versioning_enabled'])) ? 'n': 'y';

			ee()->channel_form_lib->submit_entry();

			$this->cache['insert_dummy_cf_error'] = false;

			//remove dummy error for halting
			unset(ee()->channel_form_lib->errors['halt']);

			// -------------------------------------
			//	success and errors
			// -------------------------------------

			$result['success']		= (
				empty(ee()->channel_form_lib->errors) &&
				empty(ee()->channel_form_lib->field_errors)
			);

			$result['errors']		= (empty(ee()->channel_form_lib->errors)) ?
										array() :
										ee()->channel_form_lib->errors;

			$result['field_errors']	= (empty(ee()->channel_form_lib->field_errors)) ?
										array() :
										ee()->channel_form_lib->field_errors;

			// -------------------------------------
			//	Entry data
			// -------------------------------------

			if ($result['success'] && ! $validate_only)
			{
				$result['entry_id']		= ee()->channel_form_lib->entry('entry_id');
				$result['url_title']	= ee()->channel_form_lib->entry('url_title');
				$result['channel_id']	= ee()->channel_form_lib->entry('channel_id');
				$result['entry']		= ee()->channel_form_lib->entry;

				//update author because we cannot do it with
				//logged_out_member_id
				//see explanation above
				$this->update_member_entry_connection(
					$member_id,
					$result['entry_id']
				);
			}
			//END if ($result['success'] && ! $validate_only)

			$this->errors = $result['errors'];
		}
		//end try
		catch (Channel_form_exception $e)
		{
			$this->errors = $result['errors'] = array($e->getMessage());
		}

		$this->restore_member_id();

		//in case something else needs to happen with the post lib
		//from some other addon
		$this->cache['halt_channel_form'] = false;

		// just in case we error
		$this->cache['insert_dummy_cf_error'] = false;

		//fix module
		$this->fix_load_stack();

		return $result;
	}
	//END submit_entry


	// --------------------------------------------------------------------

	/**
	 * Update Member Entry Connection
	 *
	 * @access	public
	 * @param	{Int}		$member_id	incoming member id
	 * @param	{Int}		$entry_id 	incoming entry id
	 * @return	{Boolean}				success
	 */

	public function update_member_entry_connection($member_id, $entry_id)
	{
		if (! $this->enabled() OR empty($member_id) OR empty($entry_id))
		{
			return false;
		}

		$site_id = $this->get_site_id();

		$this->fix_load_stack();

		$new	= ee('Model')
			->get('ChannelEntry', $entry_id)
			->first();
		$new->author_id	= $member_id;
		$new->save();

		// -------------------------------------
		//	update tracking table
		// -------------------------------------

		$connection	= $this->fetch('MemberChannelEntry')
			->filter('site_id', $site_id)
			->filter('member_id', $member_id)
			->first();

		if (! $connection)
		{
			$connection				= $this->make('MemberChannelEntry');
			$connection->site_id	= $site_id;
			$connection->member_id	= $member_id;
			$connection->entry_id	= $entry_id;
		}

		$connection->entry_id	= $entry_id;
		$connection->save();

		return true;
	}

	//END update_member_entry_connection

	// --------------------------------------------------------------------

	/**
	 * Delete Member Channel Data
	 *
	 * @access	public
	 * @param	array	$member_ids		array of incoming member ids
	 * @return	boolean					processed
	 */

	public function delete_member_channel_data($member_ids = array())
	{
		// -------------------------------------
		//	check prefs
		// -------------------------------------

		$prefs = $this->get_sync_prefs();

		if ( ! isset($prefs['delete_entry_on_member_delete']) ||
			! $this->check_yes($prefs['delete_entry_on_member_delete']))
		{
			return false;
		}

		// -------------------------------------
		//	validate
		// -------------------------------------

		if ($this->is_positive_intlike($member_ids))
		{
			$member_ids = array($member_ids);
		}

		if ( ! is_array($member_ids))
		{
			return false;
		}

		$member_ids = array_filter(
			$member_ids,
			array($this, 'is_positive_intlike')
		);

		if (empty($member_ids))
		{
			return false;
		}

		// -------------------------------------
		//	get entry ids from memember ids
		// -------------------------------------

		$entry_ids = $this->fetch('MemberChannelEntry')
			->filter('member_id', 'IN', $member_ids)
			->all()
			->getDictionary('entry_id', 'entry_id');

		if (! $entry_ids)
		{
			return false;
		}

		$this->fetch('MemberChannelEntry')
			->filter('member_id', 'IN', $member_ids)
			->delete();

		// -------------------------------------
		//	delete
		// -------------------------------------

		ee()->load->library('api');
		ee()->legacy_api->instantiate('channel_entries');
		ee()->api_channel_entries->delete_entry($entry_ids);

		return true;
	}
	//END delete_member_channel_data


	// --------------------------------------------------------------------

	/**
	 * Sync Member Data To Channel
	 *
	 * @access public
	 * @param	int		$member_id	member id to sync from
	 * @param	array	$data		member data
	 * @param	string	$from		where the member change came from
	 * @return	void
	 */

	public function sync_member_data_to_channel($member_id, $data, $from = '')
	{
		if (! $this->enabled())
		{
			return false;
		}

		if ($from == 'cp_member_create')
		{
			//no custom member fields will be present
		}

		$prefs = $this->get_sync_prefs();

		if (empty($prefs['channel_sync_channel']) OR
			empty($_POST['username']))
		{
			return false;
		}

		// -------------------------------------
		//	get member id
		// -------------------------------------

		$member_id = 0;

		$member	= ee('Model')
			->get('Member')
			->filter('username', ee()->input->post('username'))
			->first();

		if ($member)
		{
			$member_id = $member->member_id;
		}
		else
		{
			return false;
		}

		// -------------------------------------
		//	entry id?
		// -------------------------------------

		$existing	= $this->fetch('MemberChannelEntry')
			->filter('member_id', $member_id)
			->first();

		$entry_id = 0;

		if ($existing)
		{
			$entry_id = $existing->entry_id;
		}

		// -------------------------------------
		//	Load Publish
		// -------------------------------------

		ee()->load->library('api');
		ee()->legacy_api->instantiate(array(
			'channel_entries',
			'channel_categories',
			'channel_fields'
		));

		ee()->load->helper('url');

		ee()->api_channel_entries->assign_cat_parent = (
			ee()->config->item('auto_assign_cat_parents') == 'n'
		) ? FALSE : TRUE;

		// -------------------------------------
		//	default status
		// -------------------------------------

		$status = 'open';

		$channel_id = $this->get_data_channel();

		$channel_info = ee('Model')
			->get('Channel', $channel_id)
			->first();

		if ($channel_info)
		{
			$status = $channel_info->deft_status;
		}

		// -------------------------------------
		//	defaults for new
		// -------------------------------------

		$post_data = array(
			'channel_id'				=> $this->get_data_channel(),
			'status'					=> $status,
			//subtracting 2 days here
			//because people are seeing things post in the future? :/
			'entry_date'				=> (ee()->localize->now - ((3600 * 24) * 2)),
			'expiration_date'			=> '',
			'comment_expiration_date'	=> '',
			'allow_comments'			=> 'n'
		);

		$old_POST = $_POST;

		if ($entry_id)
		{
			// -------------------------------------
			//	Edit date
			// -------------------------------------

			unset($post_data['entry_date']);
			$post_data['edit_date'] = ee()->localize->now;

			// -------------------------------------
			//	get old data and merge
			// -------------------------------------

			//if you don't give it the channel_id it
			//only selects entry, channel, and author id. lol wtf?
			$old_data_q = ee('Model')
				->get('ChannelEntry', $entry_id)
				->first();

			if ($old_data_q)
			{
				$old_data = $old_data_q->toArray();

				//need to let our new data override old
				$_POST = $post_data = array_merge(
					$old_data,
					$post_data
				);
			}

			unset($post_data['recent_comment_date']);

			//this needs to happen after we apply our old data to post
			//from above, so this cannot be moved.
			//POST data from submit
			$mapped_m_data = $this->map_member_fields(0, true, false);

			$_POST = $post_data = array_merge($post_data, $mapped_m_data);

			//sets up custom fields
			ee()->api_channel_fields->setup_entry_settings(
				$channel_id,
				$post_data
			);

			//fire update and cross fingers
			$result = ee()->api_channel_entries->update_entry($entry_id, $post_data);
		}
		else
		{
			//cannot be moved up because we need the
			//entry ID clause above to work differently.

			//POST data from submit
			$mapped_m_data = $this->map_member_fields(0, true);

			$_POST = $post_data = array_merge($post_data, $mapped_m_data);

			//sets up custom fields
			ee()->api_channel_fields->setup_entry_settings(
				$channel_id,
				$post_data
			);

			//now we can do the new entry
			$result = ee()->api_channel_entries->submit_new_entry($channel_id, $post_data);
		}
		//END if ($entry_id)

		//restore old post
		$_POST = $old_POST;

		if ($result === FALSE)
		{
			$errors = ee()->api_channel_entries->errors;

			//there is nothing an error stop can help with here
			//because this already occurs after the member is
			//created so sending them back will cause errors
			//with trying to submit the member again
			//return $this->lib('utils')->full_stop($errors);
		}
		else
		{
			$this->update_member_entry_connection(
				$member_id,
				ee()->api_channel_entries->entry_id
			);
		}

		return true;
	}

	//END sync_member_data_to_channel

	// --------------------------------------------------------------------

	//not implimented at the moment
	public function sync_channel_data_to_member($entry_id, $data, $from = '')
	{
		if ( ! $this->enabled())
		{
			return false;
		}
		//do we have a hook here that is going to validate making
		//screen name too long?
		//handle 'title_and_url_title'
	}

	//END sync_channel_data_to_member


	// --------------------------------------------------------------------

	/**
	 * get Data Channel
	 *
	 * Tries a variety of means to fetch the user data channel
	 *
	 * @access	public
	 * @param	mixed		$channel	string channel name or string/int channel_id
	 * @param	boolean		$refresh	refresh
	 * @return	mixed					channel_id or boolean false
	 */

	public function get_data_channel($channel = '', $refresh = false)
	{
		$channel_id = 0;

		if (isset($this->channel_id) && $this->channel_id > 0 && ! $refresh)
		{
			return $this->channel_id;
		}

		// -------------------------------------
		//	preferences
		// -------------------------------------

		if (empty($channel))
		{
			$channel = $this->get_channel_data_pref();
		}

		// -------------------------------------
		//	Check template for user data channel
		// -------------------------------------

		if (empty($channel) && ! empty(ee()->TMPL))
		{
			$channel = ee()->TMPL->get_param('user_data_channel');
		}

		// -------------------------------------
		//	validate and get channel id
		// -------------------------------------

		if ( ! empty($channel))
		{
			$channel = trim($channel);

			$ch	= ee('Model')
				->get('Channel');

			if ($this->is_positive_intlike($channel))
			{
				$ch->filter('channel_id', $channel);
			}
			else
			{
				$ch->filter('channel_name', $channel);
			}

			$q = $ch->first();

			if ($q)
			{
				$this->channel_id = $q->channel_id;
				$this->channel_info = $q->toArray();
			}
			else
			{
				$this->channel_info = array();
			}
		}

		return ($this->channel_id > 0) ? $this->channel_id : false;
	}

	//END get_data_channel()

	// --------------------------------------------------------------------

	/**
	 * Get Data Channel Required Fields
	 *
	 * @access	public
	 * @param	string	$channel	Channel name or ID to get fields from
	 * @param	boolean	$refresh	refresh the cache?
	 * @return	array				required channel fields
	 */

	public function get_data_required_fields($channel = '', $refresh = false)
	{
		$field_data = $this->get_data_fields($channel, $refresh);

		if (empty($field_data))
		{
			return array();
		}

		$requireds = array();

		foreach ($field_data as $field_name => $data)
		{
			if (
				isset($data['field_required']) &&
				$this->check_yes($data['field_required'])
			)
			{
				$requireds[] = $field_name;
			}
		}

		return $requireds;
	}
	//END get_data_required_fields


	// --------------------------------------------------------------------

	/**
	 * Get Data Fields
	 *
	 * Get the fields for the User Data Channel
	 *
	 * @access	public
	 * @param	string 	$channel	channel name or ID
	 * @param	boolean	$refresh	refresh data?
	 * @param	string	$key		reutrn keys as names or ids? (default name)
	 * @return	mixed				array of data fields field_name -> data
	 */

	public function get_data_fields($channel = '', $refresh = false, $key = 'name')
	{
		$channel_id = $this->get_data_channel($channel, $refresh);

		if (! $channel_id)
		{
			return false;
		}

		if ( ! empty($this->channel_info['field_data']) &&
			! $refresh)
		{
			return $this->channel_info['field_data'];
		}

		// -------------------------------------
		//	Get all custom fields from channel
		// -------------------------------------

		$r_key = ($key == 'id') ? 'field_id' : 'field_name';

		$fields	= ee('Model')
			->get('ChannelField')
			->filter('group_id', $this->channel_info['field_group'])
			->all()
			->indexBy($r_key);

		$result = array(
			'title_and_url_title'	=> array(
				'field_id'		=> 'title_and_url_title',
				'field_name'	=> 'title_and_url_title',
				'field_label'	=> lang('title_and_url_title')
			),
			'title'					=> array(
				'field_id'		=> 'title',
				'field_name'	=> 'title',
				'field_label'	=> lang('title')
			),
			'url_title'				=> array(
				'field_id'		=> 'url_title',
				'field_name'	=> 'url_title',
				'field_label'	=> lang('url_title')
			),
		);

		if ($fields)
		{
			foreach ($fields as $key => $value)
			{
				$result[$key] = $value->toArray();
			}
		}

		$this->channel_info['field_data'] = $result;

		return $this->channel_info['field_data'];
	}

	//END get_data_fields


	// --------------------------------------------------------------------

	/**
	 * Get Sync Prefs
	 *
	 * @access	public
	 * @param	boolean	$raw_ids	get the raw output from the pref with IDS
	 * @param	boolean	$refresh	refresh the data, or use cache?
	 * @return	array				array of field mapping options
	 */

	public function get_field_map_prefs($raw_ids = false, $refresh = false)
	{
		// -------------------------------------
		//	get data from prefs
		// -------------------------------------

		//pull from user prefs at site 0 as json

		$pref_data	= array();

		$site_id	= $this->get_site_id('preferences');

		$result		= $this->get_sync_prefs();

		if (isset($result['channel_sync_field_map']))
		{
			if (is_string($result['channel_sync_field_map']))
			{
				$pref_data = json_decode($result['channel_sync_field_map'], true);
			}
			else
			{
				$pref_data = $result['channel_sync_field_map'];
			}
		}

		if (empty($pref_data))
		{
			return array();
		}

		if ($raw_ids)
		{
			return $pref_data;
		}

		// -------------------------------------
		//	get both sets of custom fields
		// -------------------------------------

		$channel_fields			= $this->get_data_fields('', $refresh, 'id');

		$member_custom_fields	= $this->model('Data')->mfields_by_id();

		// -------------------------------------
		//	build ID -> name arrays
		// -------------------------------------

		$channel_field_ids = array();

		foreach ($channel_fields as $field_id => $field_data)
		{
			$channel_field_ids[$field_data['field_id']] = $field_data['field_name'];
		}

		$member_field_ids = array();

		foreach ($member_custom_fields as $field_id => $field_data)
		{
			$member_field_ids['field_id_' . $field_id] = $field_data['name'];
		}

		// -------------------------------------
		//	build named prefs for mapping
		// -------------------------------------

		$sync_prefs = array();

		foreach ($pref_data as $member_field => $channel_field)
		{
			$key	= $member_field;
			$value	= $channel_field;

			if ($this->is_positive_intlike(str_replace('field_id_', '', $key)) &&
				isset($member_field_ids[$key])
			)
			{
				$key = $member_field_ids[$key];
				$sync_prefs[$key] = $value;
			}

			if ($this->is_positive_intlike($value) &&
				isset($channel_field_ids[$value])
			)
			{
				$sync_prefs[$key] = 'field_id_' . $value;
				//$value = $channel_field_ids[$value];
				//$sync_prefs[$key] = $value;
			}
		}

		return $sync_prefs;
	}

	//END get_field_map_prefs


	// --------------------------------------------------------------------

	/**
	 * Get Channel Data Pref
	 *
	 * @access	public
	 * @return	int		channel id of pref channel
	 */

	public function get_channel_data_pref()
	{
		$result = $this->get_sync_prefs();

		return isset($result['channel_sync_channel']) ?
			$result['channel_sync_channel'] :
			0;
	}

	//END get_channel_data_pref()


	// --------------------------------------------------------------------

	/**
	 * Get Sync Prefs
	 *
	 * @access	public
	 * @param	mixed	$site_id	int of site id desired for prefs, 0 = global
	 * @return	array				preference_name => preference_value list of prefs
	 */

	public function get_sync_prefs($site_id = false)
	{
		//0 or greater int?
		if ( ! $this->is_positive_intlike($site_id, -1))
		{
			$site_id = $this->get_site_id('preferences');
		}

		return $this->fetch('Preference')
			->filter('site_id', $site_id)
			->filter('preference_name', 'IN', $this->channel_sync_pref_list)
			->all()
			->getDictionary(
				'preference_name',
				'preference_value'
			);
	}
	//END get_sync_prefs

	// --------------------------------------------------------------------
	//	Private/Protected methods
	// --------------------------------------------------------------------


	// --------------------------------------------------------------------

	/**
	 * Set Logged Out Member Id
	 *
	 * Sets tagparams if available and updates encrypted meta folder
	 * if possible.
	 *
	 * @access	protected
	 * @param	integer $member_id	member_id to set logged_out_member_id to
	 */

	protected function set_logged_out_member_id($member_id = 0)
	{
		$this->old_group_id = ee()->session->userdata['group_id'];
		ee()->session->userdata['group_id'] = 1;
	}
	//END set_logged_out_member_id


	// --------------------------------------------------------------------

	/**
	 * Restore Member Id
	 *
	 * Restores member ID undone in set_logged_out_member_id
	 *
	 * @access	protected
	 * @return	void
	 */

	protected function restore_member_id()
	{
		ee()->session->userdata['group_id'] = $this->old_group_id ;
	}
	//END restore_member_id


	// --------------------------------------------------------------------

	/**
	 * Map Member Fields
	 *
	 * @access	protected
	 * @param	{Int}		$member_id		map already created member ids
	 * @param	{Boolean}	$map_to_post	set mapped data to $_POST
	 * @param	{Boolean}	$force_title	make sure we have a title?
	 * @return	{Array}
	 */

	protected function map_member_fields($member_id = 0, $map_to_post = true, $force_title = true)
	{
		//get preferences
		$map = $this->get_field_map_prefs();

		$out = array();

		// -------------------------------------
		//	get member data if at all possible
		// -------------------------------------

		$member_data = array();

		if ($member_id > 0)
		{
			$default_fields = array_merge(
				$this->model('data')->standard,
				$this->model('data')->base_fields
			);

			//user data has everything if its the current user
			if (ee()->session->userdata('member_id') == $member_id)
			{
				foreach ($default_fields as $default_field)
				{
					$member_data[$default_field] = ee()->session->userdata($default_field);
				}
			}
			//fetch
			else
			{
				$member	= ee('Model')
					->get('Member', $member_id)
					->first()
					->toArray();

				if ($member)
				{
					foreach ($default_fields as $default_field)
					{
						if (! isset($member[$default_field])) continue;
						$member_data[$default_field] = $member[$default_field];
					}
				}
			}
			//END if (ee()->session

			//if the default data is there, lets get member fields
			if ( ! empty($member_data))
			{
				$mfields = $this->model('data')->mfields();

				$mf_result = ee()->db
					->where('member_id', $member_id)
					->get('member_data');

				if ($mf_result->num_rows() > 0)
				{
					foreach ($mfields as $mfield_name => $mfield_data)
					{
						$member_data[$mfield_name] = $mf_result->row('field_id_' . $mfield_data['id']);
					}
				}
			}
			//END if ( ! empty($member_data))
		}
		//END if ($member_id > 0)

		// -------------------------------------
		//	map data
		// -------------------------------------

		if ( ! empty($map))
		{
			foreach ($map as $member_field => $channel_field)
			{
				$post_data = false;

				//@TODO check to see if we should always override?
				if (isset($_POST[$member_field]))
				{
					$post_data = $_POST[$member_field];
				}
				//if we need to sync and not all data is incoming
				else if (isset($member_data[$member_field]))
				{
					$post_data = $member_data[$member_field];
				}

				//found something we can use to map?
				if ($post_data)
				{
					if ($member_field == 'title_and_url_title')
					{
						//EE will handle URL_Title automatically
						$out['title'] = $post_data;
					}
					else
					{
						$out[$channel_field] = $post_data;
					}
				}
				//END if ($post_data)
			}
			//END foreach ($map as $member_field => $channel_field)
		}
		//END if ( ! empty($map))

		// -------------------------------------
		//	adjust url_title if any.
		//	url_title is automatic from title
		//	if not existing
		// -------------------------------------

		if (isset($out['url_title']))
		{
			ee()->load->helper('url');

			$out['url_title'] = url_title($out['url_title']);
		}

		// -------------------------------------
		//	we must have a title
		// -------------------------------------

		if ( ! isset($out['title']) && $force_title)
		{
			if (isset($_POST['title']))
			{
				$out['title'] = $_POST['title'];
			}
			else if ($member_id > 0)
			{
				$out['title'] = lang('default_member_title_') . $member_id;
			}
			else if (isset($_POST['username']))
			{
				$out['title'] = $_POST['username'];
			}
			else if (isset($member_data['username']))
			{
				$out['title'] = $member_data['username'];
			}
		}
		//END if ( ! isset($_POST['title']))

		if ($map_to_post)
		{
			$_POST = array_merge($_POST, $out);
		}

		return $out;
	}
	//END map_member_fields


	// --------------------------------------------------------------------

	/**
	 * Load Channel Libraries
	 *
	 * Loads the Channel Form Module into the EE load stack last
	 * and loads the main channel form lib.
	 *
	 * @access	protected
	 * @return	void
	 */

	protected function load_channel_libraries()
	{
		//even if its in the loader path EE needs it to
		//be the last one added. We fix this before return.
		ee()->load->add_package_path(PATH_MOD . 'channel');
		ee()->load->library('channel_form/channel_form_lib');
	}
	//END load_channel_libraries


	// --------------------------------------------------------------------

	/**
	 * Fix Load Stack
	 *
	 * Puts User Module back at the end of the load stack so
	 * that User can correctly laod its own libs again.
	 *
	 * @access	protected
	 * @return	void
	 */

	protected function fix_load_stack()
	{
		//fix ee loader mess
		//@see load_channel_libraries
		ee()->load->add_package_path($this->addon_path);
	}
	//END Fix Load Stack


	// --------------------------------------------------------------------

	/**
	 * Set Channel Module Channel Template Param
	 *
	 * @access	protected
	 * @param	string	$channel	channel ID or channel short name
	 */

	protected function set_data_channel($channel = '')
	{
		$c	= $channel ? $channel : $this->channel_id;
		$tp	= ($this->is_positive_intlike($c)) ? 'channel_id' : 'channel';

		if ($this->lib('Utils')->tmpl_usable())
		{
			ee()->TMPL->tagparams[$tp] = $c;
		}
		else
		{
			$_POST[$tp] = $c;
		}

	}
	//END set_data_channel


	// --------------------------------------------------------------------

	/**
	 * Get the ID of First Admin from Members Table
	 *
	 * @access	protected
	 * @return	int				0 if none found (shouldn't be possible) or
	 * 							member_id of first admin found
	 */

	protected function get_first_admin_id()
	{
		$member	= ee('Model')
			->get('Member')
			->filter('group_id', 1)
			->first();

		if ($member)
		{
			return $member->member_id;
		}
		else
		{
			return 0;
		}
	}
	//END get_first_admin_id


	// --------------------------------------------------------------------

	/**
	 * Get Site Id
	 *
	 * @access	protected
	 * @param	string	$for	what is this site id for? (ideally the same, but...)
	 * @return	int				site_id
	 */

	protected function get_site_id($for = 'default')
	{
		if ($for == 'preferences')
		{
			//This is intentional as prefs User saves are per site
			//except channel sync which is global. Global prefs
			//for Solspace addons use site_id = 0 following the EE
			//convention.
			//Forcing global until we add in support for MSM.
			return 0;
		}

		//This is intentional as there must be a channel named
		//so default to the main site.
		//We cannot use 0 here.
		return 1;
	}
	//END get_site_id
}
//END class User_channel_sync
