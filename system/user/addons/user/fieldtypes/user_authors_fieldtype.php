<?php

use Solspace\Addons\User\Library\Fieldtype;

class User_authors_fieldtype extends EE_Fieldtype
{
	public	$info	= array(
		'name'		=> 'User',
		'version'	=> ''
	);

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access	public
	 */

	public function __construct()
	{
		parent::__construct();

		$this->info			= require PATH_THIRD . 'user/addon.setup.php';

		$this->field_id 	= isset($this->settings['field_id']) ?
								$this->settings['field_id'] :
								$this->field_id;
		$this->field_name 	= isset($this->settings['field_name']) ?
								$this->settings['field_name'] :
								$this->field_name;

		$this->uob	= new Fieldtype();
	}
	//END __construct


	// --------------------------------------------------------------------

	/**
	 * displays field for publish/saef
	 *
	 * Also receives the incoming AJAX traffic from actions on the field type in the CP. Data is sent into the same view file, so we simply detect AJAX calls and add additional model filters since the AJAX is really only coming from in-context searches.
	 *
	 * @access	public
	 * @param	string	$data	any incoming data from the channel entry
	 * @return	string	html output view
	 */

	public function display_field($data)
	{
		//	$_POST['search']
		//	$_POST['search_related']

		//---------------------------------------------
		//  Set basic vars
		//---------------------------------------------

		$field_name	= $this->field_name;
		$entry_id	= ($this->content_id) ?: ee()->input->get('entry_id');

		$order = array();
		$selected = array();

		if (is_array($data) && isset($data['data']) && ! empty($data['data'])) // autosave
		{
			foreach ($data['data'] as $k => $id)
			{
				$selected[$id]	= $id;
				$order[$id]		= isset($data['sort'][$k]) ? $data['sort'][$k] : 0;
			}
		}
		elseif (is_int($data))
		{
			$selected[$data] = $data;
		}

		//---------------------------------------------
		//  First populate $entries with possible authors
		//---------------------------------------------

		$entries	= array();

		$members	= ee('Model')
			->get('Member')
			->with('MemberGroup')
			->filterGroup()
			->filter('group_id', 1)
			->orFilter('in_authorlist', 'y')
			->orFilter('MemberGroup.include_in_authorlist', 'y')
			->endFilterGroup()
			->order('screen_name');

		if (ee()->input->get_post('search') AND AJAX_REQUEST)
		{
			$members->filter('screen_name', 'LIKE', '%' . ee()->input->get_post('search') . '%');
		}

		$members	= $members->all();

		//---------------------------------------------
		//  Get current authors
		//---------------------------------------------

		$related	= array();

		$authors	= $this->uob->fetch('Author')
			->filter('entry_id', $entry_id)
			->all()
			->getDictionary('author_id', 'order');

		//	This fires when the page loads
		if ($authors AND ! AJAX_REQUEST)
		{
			$selected		= array_keys($authors);
			$used_orders	= array();

			$rel	= ee('Model')
				->get('Member')
				->with('MemberGroup')
				->filter('member_id', 'IN', array_keys($authors))
				->order('screen_name')
				->all();

			foreach ($rel as $key => $member)
			{
				$new	= ee('Model')
					->make('ChannelEntry');

				$channel	= ee('Model')
					->make('Channel');

				$channel->channel_id	= $member->group_id;
				$channel->channel_title	= $member->MemberGroup->group_title;

				$new->Channel 	= $channel;

				$new->title		= $member->screen_name;
				$new->entry_id	= $member->member_id;

				$order	= (isset($authors[$member->member_id])) ? $authors[$member->member_id]: 0;

				//	Sometimes, not sure why, but sometimes the order value from the DB comes across with duplicate orders so we correct for it by force
				while (in_array($order, $used_orders))
				{
					$order++;
				}

				$used_orders[]	= $order;

				$related[$order]	= $new;
			}

			ksort($related);
		}

		//	This fires on ajax calls
		if (! empty($data['data']) AND AJAX_REQUEST)
		{
			$selected	= $data['data'];

			$rel	= ee('Model')
				->get('Member')
				->with('MemberGroup')
				->filter('member_id', 'IN', $data['data'])
				->order('screen_name');

			$searchRelated = ee()->input->get_post('search_related');
			if (! empty($searchRelated))
			{
				$rel->filter('screen_name', 'LIKE', '%' . $searchRelated . '%');
			}

			$rel	= $rel->all();

			foreach ($rel as $member)
			{
				$new	= ee('Model')
					->make('ChannelEntry');

				$channel	= ee('Model')
					->make('Channel');

				$channel->channel_id	= $member->group_id;
				$channel->channel_title	= $member->MemberGroup->group_title;

				$new->Channel 		= $channel;

				$new->title		= $member->screen_name;
				$new->entry_id	= $member->member_id;

				$related[]	= $new;
			}
		}

		//---------------------------------------------
		//  Incredibly lazy Mitchell is going to hijack the ChannelEntry model and force data into it that does not belong. 2015 11 01
		//---------------------------------------------

		if ($members)
		{
			foreach ($members as $member)
			{
				$new	= ee('Model')
					->make('ChannelEntry');

				$channel	= ee('Model')
					->make('Channel');

				$channel->channel_id	= $member->group_id;
				$channel->channel_title	= $member->MemberGroup->group_title;

				$new->Channel 		= $channel;

				$new->title		= $member->screen_name;
				$new->entry_id	= $member->member_id;

				$entries[]	= $new;
			}
		}

		//---------------------------------------------
		//  Dependencies
		//---------------------------------------------

		$multiple	= TRUE;
		$channels	= array();

		ee()->cp->add_js_script(array(
			'plugin' => 'ee_interact.event',
			'file' => 'fields/relationship/cp',
			'ui' => 'sortable'
		));

		//---------------------------------------------
		//  We have some very simple JS that runs that converts the fieldset to a full width field. EE does not yet let us control that width setting at the field type level. It may in the future. In which case we can kill this. mitchell@solspace.com 2015 11 03
		//---------------------------------------------

		ee()->cp->add_to_foot(
			$this->uob->view('publish_tab_js', compact('field_name'))
		);

		//---------------------------------------------
		//  Field view
		//---------------------------------------------

		$field_view	= ee('View')->make('relationship:publish')->render(compact('field_name', 'entries', 'selected', 'related', 'multiple', 'channels'));

		//---------------------------------------------
		//  Change references to 'items' to 'authors'
		//---------------------------------------------
		//	We change references to be 'author' oriented and we also hide the reorder handles on the related authors so that they cannot be drag & drop reordered.
		//---------------------------------------------

		$field_view	= str_replace(
			array(
				lang('items'),
				lang('item_to_relate_with'),
				lang('items_to_relate_with'),
				lang('items_related_to'),
				lang('no_entry_related'),
				lang('search_avilable_entries'),
				lang('search_available_entries'),
				lang('search_related_entries'),
				lang('no_entries_found'),
				lang('no_entries_related'),
				lang('items_related_to'),
				'<div class="filters">'
			),
			array(
				lang('authors'),
				lang('author_to_relate_with'),
				lang('authors_to_relate_with'),
				lang('authors_related_to'),
				lang('no_entry_related'),
				lang('search_available_authors'),
				lang('search_available_authors'),
				lang('search_related_authors'),
				lang('no_authors_found'),
				lang('no_authors_related'),
				'Authors <strong>related to</strong> this entry. ' . lang('primary_author_note'),
				'<div class="filters" style="display:none">'
			),
			$field_view
		);

		//---------------------------------------------
		//  Return
		//---------------------------------------------

		return $field_view;
	}
	//END display_field()


	// --------------------------------------------------------------------

	/**
	 * post_save
	 *
	 * @access	public
	 * @param	string	$data	any incoming data from the channel entry
	 * @return	null	html output view
	 */

	public function save($data)
	{
		$sort = isset($data['sort']) ? $data['sort'] : array();
		$data = isset($data['data']) ? $data['data'] : array();

		$sort = array_filter($sort);

		$cache_name = $this->field_name;

		ee()->session->set_cache(__CLASS__, $cache_name, array(
			'data' => $data,
			'sort' => $sort
		));

		unset($_POST['sort_'.$this->field_name]);

		return '';
	}
	//	End save()


	// --------------------------------------------------------------------

	/**
	 * Post Save
	 *
	 * Runs after entry saves
	 *
	 * @access public
	 * @param	array	$data	invoming field data from ssaved field
	 * @return	void
	 */

	public function post_save($data)
	{
		$entry_id	= $this->content_id();
		$cache_name	= $this->field_name;

		$post = ee()->session->cache(__CLASS__, $cache_name);

		if ($post === FALSE)
		{
			return;
		}

		$order	= array_values($post['sort']);
		$data	= $post['data'];

		$this->uob->fetch('Author')
			->filter('entry_id', $entry_id)
			->delete();

		//	We want to call the first author in the list the principal author. This helper loop gets us through the ordering array and tells us which author id is the first in the list.
		$first	= 9999;
		foreach ($data as $key => $val)
		{
			if (isset($order[$key]) AND $order[$key] < $first)
			{
				$first_entry	= $val;
				$first			= $order[$key];
			}
		}

		foreach ($data as $key => $val)
		{
			$this->uob->make('Author')
				->set(array(
					'entry_id'		=> $entry_id,
					'author_id'		=> $val,
					'order'			=> isset($order[$key]) ? $order[$key] : 0,
					'entry_date'	=> ee()->localize->now,
					'principal'		=> ($first_entry == $val) ? 'y': 'n'
				))
				->save();
		}
	}
	//END post_save()


	//dummy function but since they use abstract now it errors to hell if
	//you don't have the required params and access keyword
	public function replace_tag($data, $params = array(), $tagdata = FALSE){return '';}


	// --------------------------------------------------------------------

	/**
	 * 	delete from DB if exists and replace
	 *
	 * @access	public
	 * @param	array	$ids ids of the entries being deleted
	 * @return	null
	 */

	public function delete($ids)
	{
		$this->uob->fetch('Author')
			->filter('entry_id', 'IN', $ids)
			->delete();
	}
	//END delete()
}
//END User_ft
