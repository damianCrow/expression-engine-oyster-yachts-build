<?php

namespace Solspace\Addons\User\Library;
use Solspace\Addons\User\Library\AddonBuilder;

class Authors extends AddonBuilder
{
	// --------------------------------------------------------------------

	/**
	 * AJAX Author Search
	 *
	 * @access	public
	 * @return	string
	 */

	public function user_authors_search()
	{
		ee()->lang->loadfile( 'user' );

		//	----------------------------------------
		//	Handle existing
		//	----------------------------------------

		$existing	= array();

		if ( ee()->input->get_post('existing') !== FALSE )
		{
			$existing	= explode( "||", ee()->input->get_post('existing', TRUE) );
		}

		//	----------------------------------------
		//	Query and construct
		//	----------------------------------------

		$select	= '<li class="message">'.lang('no_matching_authors').'</li>';

		$str 	= $this->_clean_str( ee()->input->get_post('author') );

		if ( $str == '' )
		{
			echo $select;
			exit();
		}

		$extra = ($str == '*') ? '' : " AND exp_members.screen_name LIKE '%" .
										ee()->db->escape_str( $str )."%' ";

		$sql = "SELECT	exp_members.member_id AS id,
						exp_members.screen_name AS name
				FROM	exp_members
				LEFT JOIN exp_member_groups
				ON		exp_member_groups.group_id = exp_members.group_id
				WHERE	exp_member_groups.site_id = '" .
							ee()->db->escape_str(ee()->config->item('site_id'))."'
				AND (
					 exp_members.group_id = 1 OR
					 exp_members.in_authorlist = 'y' OR
					 exp_member_groups.include_in_authorlist = 'y'
					 )
				AND exp_members.member_id NOT IN ('" .
					implode( "','", ee()->db->escape_str( $existing ) )."')
				{$extra}
				ORDER BY screen_name ASC, username ASC";

		$query	= ee()->db->query($sql);

		$select	= '';

		if ( $query->num_rows() == 0 )
		{
			$select .= '<li class="message">'.lang('no_matching_authors').'</li>';
		}
		else
		{
			foreach ( $query->result_array() as $row )
			{
				$select	.= '<li><input type="radio" name="user_authors_principal" value="'.$row['id'].'" style="display:none;" />'.$row['name'].' (<a href="'.$row['id'].'" alt="'.$row['id'].'">'.lang('add').'</a>)</li>';
			}
		}

		@header("Cache-Control: no-cache, must-revalidate");

		echo $select;

		exit();
	}
	// END user_authors_search()



	// --------------------------------------------------------------------

	/**
	 * AJAX Author Search json
	 *
	 * @access	public
	 * @return	string
	 */

	public function user_authors_search_json()
	{
		//	----------------------------------------
		//	Handle existing
		//	----------------------------------------

		$existing		= array();

		$return_data	= array('found' => FALSE, 'users' => array());

		if ( ee()->input->get_post('existing') !== FALSE )
		{
			$existing	= explode( "||", ee()->input->get_post('existing', TRUE) );
		}

		//	----------------------------------------
		//	Query and construct
		//	----------------------------------------

		$str 	= $this->_clean_str( ee()->input->get_post('author') );

		if ( $str == '' )
		{
			echo $this->json_encode($return_data);
			exit();
		}

		$extra = ($str == '*') ? '' : " AND exp_members.screen_name LIKE '%" .
										ee()->db->escape_str( $str )."%' ";

		$sql = "SELECT 		exp_members.member_id 	AS id,
							exp_members.screen_name AS name
				FROM 		exp_members
				LEFT JOIN 	exp_member_groups
				ON 			exp_member_groups.group_id = exp_members.group_id
				WHERE 		exp_member_groups.site_id = '" .
								ee()->db->escape_str(ee()->config->item('site_id')) . "'
				AND 		(
								exp_members.group_id = 1 OR
								exp_members.in_authorlist = 'y' OR
								exp_member_groups.include_in_authorlist = 'y'
				)
				AND 		exp_members.member_id
				NOT IN 		('".implode( "','", ee()->db->escape_str( $existing ) )."')
				{$extra}
				ORDER BY 	screen_name ASC, username ASC";

		$query	= ee()->db->query($sql);

		if ( $query->num_rows() > 0 )
		{
			$return_data['found'] = TRUE;

			foreach ( $query->result_array() as $row )
			{
				$return_data['users'][] = $row;
			}
		}

		@header("Cache-Control: no-cache, must-revalidate");
		@header("Content-type: application/json");

		echo $this->json_encode($return_data);
		exit();
	}
	// END user_authors_search


	// --------------------------------------------------------------------

	/**
	 * AJAX Author Add
	 *
	 * @access	public
	 * @return	string
	 */

	public function user_authors_add()
	{
		ee()->lang->loadfile( 'user' );

		$entry_id	= '';
		$hash		= '';

		if ( ee()->input->post('entry_id') !== FALSE AND
			ee()->input->post('entry_id') != '' )
		{
			$entry_id	= ee()->input->post('entry_id');
		}

		if ( ee()->input->post('hash') !== FALSE AND
			ee()->input->post('hash') != '' )
		{
			$hash		= ee()->input->post('hash');
		}

		//	----------------------------------------
		//	Author id?
		//	----------------------------------------

		if ( ee()->input->post('author_id') === FALSE OR
			ee()->input->post('author_id') == '' )
		{
			echo "!".lang('no_author_id');
			exit();
		}
		else
		{
			$author_id	= ee()->input->post('author_id');
		}

		//	----------------------------------------
		//	Has this already been saved?
		//	----------------------------------------

		$sql = "SELECT	id, author_id, entry_id, hash
				FROM	exp_user_authors
				WHERE	author_id = '".ee()->db->escape_str( $author_id )."'";

		if ( $entry_id != '' )
		{
			$sql	.= " AND entry_id = '".ee()->db->escape_str( $entry_id )."'";
		}
		elseif ( $hash != '' )
		{
			$sql	.= " AND hash = '".ee()->db->escape_str( $hash )."'";
		}

		$query	= ee()->db->query( $sql );

		if ( $query->num_rows() > 0 AND
			 $query->row('entry_id') == '0' )
		{
			ee()->db->update(
				'exp_user_authors',
				array( 'entry_id' => $entry_id ),
				array( 'id' => $query->row('id'))
			);
		}

		if ( $query->num_rows() == 0 )
		{
			$data['author_id']	= $author_id;
			$data['hash']		= $hash;
			$data['entry_date']	= ee()->localize->now;

			if ( $entry_id != '' )
			{
				$data['entry_id']	= $entry_id;
			}
		}

		ee()->db->insert('exp_user_authors', $data);

		//	----------------------------------------
		//	Return
		//	----------------------------------------

		echo lang('successful_add');


		exit();
	}
	//END user_authors_add


	// --------------------------------------------------------------------

	/**
	 * AJAX Author Delete
	 *
	 * @access	public
	 * @return	string
	 */

	public function user_authors_delete()
	{
		ee()->lang->loadfile( 'user' );

		$entry_id	= '';
		$hash		= '';

		if ( ee()->input->post('entry_id') !== FALSE AND
			ee()->input->post('entry_id') != '' )
		{
			$entry_id	= ee()->input->post('entry_id');
		}

		if ( ee()->input->post('hash') !== FALSE AND
			ee()->input->post('hash') != '' )
		{
			$hash		= ee()->input->post('hash');
		}

		//	----------------------------------------
		//	Author id?
		//	----------------------------------------

		if ( ee()->input->post('author_id') === FALSE OR
			ee()->input->post('author_id') == '' )
		{
			echo "!".lang('no_author_id');
			exit();
		}
		else
		{
			$author_id	= ee()->input->post('author_id');
		}

		//	----------------------------------------
		//	Has this already been saved?
		//	----------------------------------------

		$sql = "SELECT	id, author_id, entry_id, hash
				FROM	exp_user_authors
				WHERE	author_id = '".ee()->db->escape_str( $author_id )."'";

		if ( $entry_id != '' )
		{
			$sql	.= " AND entry_id = '".ee()->db->escape_str( $entry_id )."'";
		}
		elseif ( $hash != '' )
		{
			$sql	.= " AND hash = '".ee()->db->escape_str( $hash )."'";
		}

		$query	= ee()->db->query( $sql );

		if ( $query->num_rows() == 0 )
		{
			echo "!".lang('author_not_assigned');
			exit();
		}
		else
		{
			$sql = "DELETE FROM exp_user_authors
					WHERE author_id = '".ee()->db->escape_str( $author_id )."'";

			if ( $entry_id != '' )
			{
				$sql	.= " AND entry_id = '".ee()->db->escape_str( $entry_id )."'";
			}
			else
			{
				$sql	.= " AND hash = '".ee()->db->escape_str( $hash )."'";
			}
		}

		ee()->db->query( $sql );

		//	----------------------------------------
		//	Return
		//	----------------------------------------

		echo lang('successful_add');

		exit();
	}
	// END user_authors_delete

	// --------------------------------------------------------------------

	/**
	 *	Publish Tab JS
	 *
	 *	Used, currently, for just the User Authors Tab,
	 *	since EE 2.x does not allow us to give
	 *	Publish Tabs to extensions.
	 *
	 *	@access		public
	 *	@return		string
	 */

	function publish_tab_javascript()
	{
		// --------------------------------------------
		//  Publish Tab Name
		// --------------------------------------------

		// Load the string helper
		ee()->load->helper('string');

		//json url for members
		$this->cached_vars['template_uri']				= $this->base .
														'&method=user_authors_template' .
														((ee()->input->get('entry_id') !== FALSE) ?
															'&entry_id=' . ee()->input->get('entry_id') : '');

		$this->cached_vars['user_search_uri']			= $this->base .
															'&method=user_authors_search_json';

		$this->cached_vars['loading_img_uri'] 			= PATH_CP_GBL_IMG . 'indicator.gif';

		$this->cached_vars['lang_loading_users'] 		= lang('loading_users');

		// --------------------------------------------
		//  Output Our JS File
		// --------------------------------------------

		exit($this->view('publish_tab.js', null, TRUE));
	}
	// END publish_tab_javascript


	// --------------------------------------------------------------------

	/**
	 *	Auto Complete for User Authors Publish Tab
	 *
	 *	@access		public
	 *	@return		string
	 */

	function browse_authors_autocomplete()
	{
		// --------------------------------------------
		//  Existing
		// --------------------------------------------

		$existing = array();

		if ( ee()->input->get('current_authors') !== FALSE )
		{
			$existing = array_unique(
				preg_split(
					"/\s*,\s*/",
					trim(ee()->input->get('current_authors', TRUE)),
					', '
				)
			);
		}

		//	----------------------------------------
		//	Query DB
		//	----------------------------------------

		$sql = "SELECT	screen_name
				FROM	exp_members
				WHERE	group_id
				NOT IN	(2,3,4) ";

		if (count($existing) > 0)
		{
			$sql .= "AND screen_name NOT IN ('" .
					implode( "','", ee()->db->escape_str( $existing ) )."') ";
		}

		if (ee()->input->get('q') != '*')
		{
			$sql .= "AND screen_name LIKE '" .
					ee()->db->escape_like_str(ee()->input->get('q'))."%' ";
		}

		$sql .= "ORDER BY screen_name DESC LIMIT 100";

		$query = ee()->db->query($sql);

		$return_users = array();

		foreach($query->result_array() as $row)
		{
			$return_users[] = $row['screen_name'];
		}

		$output = implode("\n", array_unique($return_users));

		// --------------------------------------------
		//  Headers
		// --------------------------------------------

		ee()->output->set_status_header(200);
		@header("Cache-Control: max-age=5184000, must-revalidate");
		@header('Last-Modified: '.gmdate('D, d M Y H:i:s', gmmktime()).' GMT');
		@header('Expires: '.gmdate('D, d M Y H:i:s', gmmktime() + 1).' GMT');
		@header('Content-Length: '.strlen($output));

		//	----------------------------------------
		//	 Send JavaScript/CSS Header and Output
		//	----------------------------------------

		@header("Content-type: text/plain");

		exit($output);
	}
	// END browse_authors_autocomplete


	// --------------------------------------------------------------------

	/**
	 * user_authors_template
	 *
	 *
	 * @access	public
	 * @return	null
	 */

	public function user_authors_template()
	{
		$entry_id 			= ee()->input->get('entry_id');
		$in_primary_author	= ee()->input->get('primary_author');
		$in_user_authors 	= ee()->input->get('user_authors');

		$current_authors 	= array();

		$member_id_sql 		= '0';

		//is the entry_id useable?
		if ($entry_id !== 'FALSE' AND is_numeric($entry_id))
		{
			//data please
			$query	= ee()->db->query(
				"SELECT 	ua.author_id, ua.principal, m.screen_name
				 FROM 		exp_user_authors ua, exp_members m
				 WHERE 		ua.author_id != '0'
				 AND 		ua.entry_id = '".ee()->db->escape_str($entry_id)."'
				 AND 		ua.author_id = m.member_id
				 ORDER BY 	m.screen_name"
			);

			//if we have users, fill arrays and store primary
			if ($query->num_rows() > 0)
			{
				$current_authors = $query->result_array();

				foreach($current_authors as $row)
				{
					//to weed out current authors
					$member_id_sql .= ', ' . $row['author_id'];
				}
			}
		}

		//because EE 2 saves data on exit, no submit, we have to do some footwork
		//this is not an else statement because sometimes there is an entry_id
		//when there shouldn't be, but there might still be stored data
		//damned stored data
		if ( empty($current_authors) AND
			! in_array($in_user_authors, array(FALSE, ''), TRUE) )
		{
			$primary_author = ( ! in_array($in_primary_author, array(FALSE, ''), TRUE) AND
								is_numeric($in_primary_author) ) ? $in_primary_author : 0;

			$temp_authors = preg_split(
				"/[\s]*,[\s]*/is",
				$in_user_authors,
				-1,
				PREG_SPLIT_NO_EMPTY
			);

			//clean
			$search_authors = array();

			foreach($temp_authors AS $author_id)
			{
				if ( ! is_numeric($author_id)) {continue;}

				$search_authors[]	= trim($author_id);
			}

			$search_authors = implode(',', $search_authors);

			//data from members because this could be unsaved data
			$query	= ee()->db->query(
				"SELECT 	screen_name, member_id AS author_id
				 FROM 		exp_members
				 WHERE 		member_id != '0'
				 AND 		member_id
				 IN			($search_authors)
				 ORDER BY 	screen_name"
			);

			//if we have users, fill arrays and store principal correctly
			//different set of data, but needs to match for template
			//cannot rely on entry_id because there might not always be one.
			if ($query->num_rows() > 0)
			{
				foreach($query->result_array() AS $row)
				{
					$row['principal'] 	= ($row['author_id'] === $primary_author) ? 'y' : 'n';

					$current_authors[]  = $row;
				}
			}
		}

		//$this->cached_vars['users']				= $query->result_array();
		$this->cached_vars['current_authors']	= $current_authors;

		//words n stuff
		$lang_items = array(
			'assigned_authors',
			'choose_author_instructions',
			'browse_authors',
			'assigned_authors_instructions',
			'search',
			'no_matching_authors'
		);

		foreach($lang_items AS $item)
		{
			$this->cached_vars['lang_' . $item]	= lang($item);
		}

		exit($this->view('tab_template.html', null, TRUE));
	}
	// END user_author_template

	// --------------------------------------------------------------------

	/**
	 * Clean Tag String
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */

	private function _clean_str( $str = '' )
	{
		ee()->load->helper('text');

		if (ee()->config->item('auto_convert_high_ascii') == 'y')
		{
			$str = ascii_to_entities( $str );
		}

		return ee()->security->xss_clean( $str );
	}
	// END _clean_str
}
//END User_authors
