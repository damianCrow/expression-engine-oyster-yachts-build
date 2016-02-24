<?php namespace Zenbu\librairies\platform\ee;

use Zenbu\librairies\platform\ee\Url;
use Zenbu\librairies\platform\ee\Request;

class Pagination
{
	/**
	* function _pagination_config
	* Creates pagination for entry listing
	* @return	string	pagination HTML
	*/
	function getPagination($total_rows, $limit)
	{
		// Leave if you're not in the CP. It can happen.
		if(REQ != 'CP')
		{
			return array();
		}

		if($total_rows == 0)
		{
			return FALSE;
		}

		// Pass the relevant data to the paginate class
		if(version_compare(APP_VER, '3.0.0', '>='))
		{
			$pagination = $limit > 0 ? ee('CP/Pagination', $total_rows)
							->perPage($limit)
							->queryStringVariable('perpage')
							->currentPage(Request::param('perpage', 1))
							->render(Url::zenbuUrl()) : '';

			return $pagination;
		}
		else
		{
			$config = array(
				'base_url'             => Url::zenbuUrl(),
				'total_rows'           => $total_rows,
				'per_page'             => $limit,
				'page_query_string'    => TRUE,
				'query_string_segment' => 'perpage',
				'full_tag_open'        => '<span id="paginationLinks">',
				'full_tag_close'       => '</span>',
				'prev_link'            => '<img src="'.ee()->cp->cp_theme_url.'images/pagination_prev_button.gif" width="13" height="13" alt="<" />',
				'next_link'            => '<img src="'.ee()->cp->cp_theme_url.'images/pagination_next_button.gif" width="13" height="13" alt=">" />',
				'first_link'           => '<img src="'.ee()->cp->cp_theme_url.'images/pagination_first_button.gif" width="13" height="13" alt="< <" />',
				'last_link'            => '<img src="'.ee()->cp->cp_theme_url.'images/pagination_last_button.gif" width="13" height="13" alt="> >" />',
				'anchor_class'         => 'class="pagination"',
				);

			// Set up pagination
			ee()->load->library('pagination');
		
			ee()->pagination->initialize($config);

			return ee()->pagination->create_links();
		}

	} // END function _pagination_config
}