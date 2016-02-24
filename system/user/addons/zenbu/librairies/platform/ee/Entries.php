<?php namespace Zenbu\librairies\platform\ee;

use Zenbu\librairies\platform\ee\Convert;
use Zenbu\librairies\platform\ee\Base as Base;
use Zenbu\librairies\platform\ee\Session;
use Zenbu\librairies\ArrayHelper;
use Zenbu\librairies\Sections;
use Zenbu\librairies\Settings;
use Zenbu\librairies\Fields;

class Entries extends Base
{
	public function __construct()
	{
		parent::__construct();
		parent::init(array('settings', 'fields'));

		$this->default_limit	= 25;
		$this->sections	= new Sections();
	}

	public function getOverrides($entries)
	{
		$fields = ArrayHelper::flatten_to_key_val('field_id', 'field_type', $this->fields->getFields());
		$field_ids = ArrayHelper::make_array_of('field_id', $this->fields->getFields());
		$entry_ids = ArrayHelper::make_array_of('entry_id', $entries);
		$field_settings = ArrayHelper::flatten_to_key_val('fieldId', 'settings', $this->display_settings['fields']);
		$output = array();

		foreach($entries as $entry_key => $entry)
		{
			foreach($entry as $key => $val)
			{
				if(strncmp($key, 'field_id_', 9) == 0)
				{
					$field_id = substr($key, 9);

					// Get a basic list of rel data
					foreach($fields as $fid => $field_type)
					{
						if(isset($entry['field_id_'.$fid]))
						{
							$rel_array[$entry['entry_id']]['field_id_'.$fid] = $entry['field_id_'.$fid];
						}
					}


					// Get basic data from fields that store *some* data in
					// actual custom field (i.e. not in separate table)

					// $matrix_installed			= FALSE;
					// $tagger_installed			= FALSE;
					// $playa_installed			= FALSE;
					// $channel_images_installed	= FALSE;

					/**
					*	====================================
					*	Adding third-party fieldtype classes
					*	====================================
					*/

					$ft_object = $this->fields->loadFieldtypeClass($this->fieldtypes[$field_id]);

					$ft_data 	= $this->fieldtypes[$field_id].'_data';
					// Optional variables for simple fields:
					// $keyword: 				..will this be obsolete?
					// $output_upload_prefs: 	add if you need upload settings
					// $settings: 				add if you need saved display settings
					// $rel_array: 				add if you need relationship data
					$$ft_data 	= $ft_object && method_exists($ft_object, 'zenbu_get_table_data') ? $ft_object->zenbu_get_table_data(
							$entry_ids,
							$field_ids,
							$entry['channel_id'],
							array(),//$output_upload_prefs,
							array(),//$settings,
							$rel_array) : array();


					$ft_table_data = $this->fieldtypes[$field_id].'_data';
					$table_data = (isset($$ft_table_data)) ? $$ft_table_data : array();

					$output[$entry_key][$key]	= $ft_object && method_exists($ft_object, 'zenbu_display') ?
						$ft_object->zenbu_display(
							$entry['entry_id'],
							$entry['channel_id'],
							$entry['field_id_'.$field_id],
							$table_data,
							$field_id,
							$field_settings[$field_id],//array(),//$settings,
							array(),//$rules,
							array(),//$output_upload_prefs,
							array(),//$this->installed_addons,
							$fields) : $entry['field_id_'.$field_id];
				}
				// elseif($key == 'entry_date')
				// {
				// 	$output[$entry_key][$key] = '';//$this->localize->human('%Y', $entry[$key]);
				// }
			}
		}

		/**
		*	======================================
		*	Extension Hook zenbu_modify_data_array
		*	======================================
		*
		*	Modifies the data array containing most of
		*	Zenbu’s settings and output data.
		*	This data is used for the result view
		*	@return void
		*
		*/
		if (ee()->extensions->active_hook('zenbu_modify_data_array') === TRUE)
		{
		    $output = ee()->extensions->call('zenbu_modify_data_array', $output);
		    if (ee()->extensions->end_script === TRUE) return;
		}

		return $output;
	}

	public function getEntries()
	{
		$channel_id = Request::param(Convert::string('sectionId'), 0);

		// SQL_CALC_FOUND_ROWS will help get total_results later
		ee()->db->select('SQL_NO_CACHE SQL_CALC_FOUND_ROWS exp_channel_titles.entry_id, exp_channel_titles.url_title, exp_channel_titles.author_id', false);
		ee()->db->where('channel_titles.site_id', $this->user->site_id);
		// First check if user can see other member's entries. If they can't, search in user's entries only.
		if(array_key_exists('can_view_other_entries', $this->permissions) && $this->permissions['can_view_other_entries'] == 'y')
		{
			ee()->db->where('channel_titles.author_id', $this->user->is);
		}

		//	----------------------------------------
		// 	Add query based on settings
		// 	Based on channel_id (if not, the query is made, but not set to display... for now)
		//	----------------------------------------

		ee()->db->select("channel_titles.channel_id"); // This has to be done, since channel id is important
		$queries = array(
			"id"				=> "",
			"title"				=> "channel_titles.title",
			"channel"			=> "channel_titles.channel_id",
			"url_title" 		=> "channel_titles.url_title",
			"author"			=> "channel_titles.author_id",
			"status"			=> "channel_titles.status",
			"sticky"			=> "channel_titles.sticky",
			"entry_date"		=> "channel_titles.entry_date",
			"expiration_date"	=> "channel_titles.expiration_date",
			"edit_date"			=> "channel_titles.edit_date",
			"comments"			=> "channel_titles.comment_total",
			"view_count"		=> array(
										"channel_titles.view_count_one",
										"channel_titles.view_count_two",
										"channel_titles.view_count_three",
										"channel_titles.view_count_four",
										),
			"view"				=> array(
										"channels.live_look_template",
										"channel_titles.url_title",
										),
			"categories"		=> "",
			"last_author"		=> "",
			//"autosave"			=> "channel_entries_autosave.entry_id AS autosave_entry_id",
			"pages"				=> "",
			);

		foreach($this->display_settings['fields'] as $key => $field)
		{
			if($field['show'] == 1 && isset($queries[$field['fieldType']]))
			{
				if(empty($queries[$field['fieldType']]))
				{
					// We'll maybe put special query additions here later
				}
				else
				{
					if(is_array($queries[$field['fieldType']]))
					{
						foreach($queries[$field['fieldType']] as $query)
						{
							ee()->db->select($query);
						}
					}
					else
					{
						ee()->db->select($queries[$field['fieldType']]);
					}

				}
			}
		}

		// foreach($queries as $option => $query)
		// {
		// 	switch ($option)
		// 	{
		// 		case "show_categories": case "show_id": case "show_channel": case "show_pages":
		// 			$output[$option] = (isset($settings['setting'][$channel_id][$option]) && $settings['setting'][$channel_id][$option] == 'y') ? 'y' : 'n';
		// 		break;
		// 		case "show_comments":
		// 			if($comment_module === TRUE)
		// 			{
		// 				(isset($settings['setting'][$channel_id][$option]) && !empty($settings['setting'][$channel_id][$option])) ? ee()->db->select($query) : '';

		// 				$output[$option] = (isset($settings['setting'][$channel_id][$option]) && $settings['setting'][$channel_id][$option] == 'y') ? 'y' : 'n';
		// 			}
		// 		break;
		// 		default:
		// 			if (isset($settings['setting'][$channel_id][$option]) && ! empty($settings['setting'][$channel_id][$option]) && ! empty($query))
		// 			{
		// 				if(is_array($query))
		// 				{
		// 					foreach($query as $key => $query_multi)
		// 					{
		// 						ee()->db->select($query_multi);
		// 					}
		// 				} else {
		// 					ee()->db->select($query);
		// 				}
		// 			}

		// 			$output[$option] = (isset($settings['setting'][$channel_id][$option]) && $settings['setting'][$channel_id][$option] == 'y') ? 'y' : 'n';
		// 		break;
		// 	}
		// }

		/**
		*	===========================================
		*	Extension Hook zenbu_add_column
		*	===========================================
		*
		*	Adds another standard setting row in the Display Settings section
		*	* This hook is used again here to add the field to dropdowns
		*	@return $fields_and_labels 	array	An array containing row data
		*/
		// if (ee()->extensions->active_hook('zenbu_add_column') === TRUE)
		// {
		// 	$hook_fields_and_labels = ee()->extensions->call('zenbu_add_column');
		//  	if (ee()->extensions->end_script === TRUE) return;

		//  	if(is_array($hook_fields_and_labels))
		// 	{
		// 		foreach($hook_fields_and_labels as $key => $fal)
		// 		{
		// 			$option = isset($fal['column']) ? $fal['column'] : '';
		// 			$output[$option] = isset($settings['setting'][$channel_id][$option]) && $settings['setting'][$channel_id][$option] == 'y' ? 'y' : 'n';
		// 		}
		// 		unset($hook_fields_and_labels);
		// 	}
		// }

		$count = 0;

		if( ! empty($channel_id) )
		{
			ee()->db->where("channel_titles.channel_id", $channel_id);
		} else {
			$section_array = ArrayHelper::make_array_of('channel_id', $this->sections->getSections());
			$section_array = empty($section_array) ? array(0) : $section_array;
			ee()->db->where_in("channel_titles.channel_id", $section_array);
		}

		$already_queried_matrix = FALSE;
		$already_queried_ch_img = FALSE;

		$filters = Request::post('filter');

		if($filters)
		{
			foreach($filters as $filter)
			{
				if(isset($filter['1st']))
				{
					switch ($filter['1st'])
					{
						case 'any_cf_title':
							$keyword = trim(ee()->db->escape_like_str($filter['3rd']));
							if ( ! empty($keyword) && isset($field_ids['id']))
							{
								$where = "";
								switch($filter['2nd'])
								{
									case "contains":
										$where = "(exp_channel_titles.title LIKE '%" . $keyword . "%' OR ";
										foreach($field_ids['id'] as $key => $f_id)
										{
											$where .= "exp_channel_data.field_id_".$f_id . " LIKE '%" . $keyword . "%' OR ";
										}
									break;
									case "doesnotcontain":
										$where = "(exp_channel_titles.title NOT LIKE '%" . $keyword . "%' OR ";
										foreach($field_ids['id'] as $key => $f_id)
										{
											$where .= "exp_channel_data.field_id_".$f_id . " NOT LIKE '%" . $keyword . "%' OR ";
										}
									break;
									case "beginswith":
										$where = "(exp_channel_titles.title LIKE '" . $keyword . "%' OR ";
										foreach($field_ids['id'] as $key => $f_id)
										{
											$where .= "exp_channel_data.field_id_".$f_id . " LIKE '" . $keyword . "%' OR ";
										}
									break;
									case "doesnotbeginwith":
										$where = "(exp_channel_titles.title NOT LIKE '%" . $keyword . "%' OR ";
										foreach($field_ids['id'] as $key => $f_id)
										{
											$where .= "exp_channel_data.field_id_".$f_id . " NOT LIKE '%" . $keyword . "%' OR ";
										}
									break;
									case "endswith":
										$where = "(exp_channel_titles.title LIKE '%" . $keyword . "' OR ";
										foreach($field_ids['id'] as $key => $f_id)
										{
											$where .= "exp_channel_data.field_id_".$f_id . " LIKE '%" . $keyword . "' OR ";
										}
									break;
									case "doesnotendwith":
										$where = "(exp_channel_titles.title NOT LIKE '%" . $keyword . "' OR ";
										foreach($field_ids['id'] as $key => $f_id)
										{
											$where .= "exp_channel_data.field_id_".$f_id . " NOT LIKE '%" . $keyword . "' OR ";
										}
									break;
									case "containsexactly":
										$where = "(exp_channel_titles.title LIKE '" . $keyword . "' OR ";
										foreach($field_ids['id'] as $key => $f_id)
										{
											$where .= "exp_channel_data.field_id_".$f_id . " LIKE '" . $keyword . "' OR ";
										}
									break;
								}
								$where = substr($where, 0, -4) . ')';
								ee()->db->where($where);
							}
						break;
						case 'id':
							if($filter['2nd'] == 'is' && ! empty($filter['3rd']))
							{
								ee()->db->where('exp_channel_titles.entry_id', $filter['3rd']);
							} elseif($filter['2nd'] = 'isnot' && ! empty($filter['3rd'])) {
								ee()->db->where('exp_channel_titles.entry_id !=', $filter['3rd']);
							}
						break;
						case 'channel_id': // Keeping this if ever is/is not is used
							if($filter['2nd'] == 'is' && $filter['3rd'] != "0")
							{
								//ee()->db->where('exp_channel_titles.channel_id', $filter['3rd']);
							} elseif($filter['2nd'] = 'isnot' && $filter['3rd'] != "0") {
								ee()->db->where('exp_channel_titles.channel_id !=', $filter['3rd']);
							}
						break;
						case 'category':
							/**
							 * Add category filetering if cat_id is present
							 */
							$cat_id = $filter['3rd'];
							$cat_cond = $filter['2nd'];


							/**
							*	Specific Category ID provided
							*/
							if( is_numeric($cat_id) )
							{
								//
								// Under the effect of Category Permissions
								//
								if(isset($installed_addons) && in_array('Category_permissions_ext', $this->installed_addons['extensions']) && $this->member_group_id != 1)
								{
									if( ee()->session->cache('zenbu', 'permitted_cats') )
									{

										$permitted_cats = ee()->session->cache('zenbu', 'permitted_cats');

									} else {

										ee()->load->add_package_path(PATH_THIRD . '/category_permissions');
										ee()->load->model('category_permissions_model');
										$permitted_cats = ee()->category_permissions_model->get_member_permitted_categories($this->member_id);
										ee()->load->remove_package_path(PATH_THIRD . '/category_permissions');

									}

									if( ! empty($permitted_cats))
									{

										$cat_where_in = $permitted_cats;

										if( ! in_array($cat_id, $cat_where_in) )
										{
											$entry_id_array[] = 0;
											ee()->db->where_in('channel_titles.entry_id', $entry_id_array);
										} else {
											$entry_id_array = array();
											// Check if cat_id is part of allowed cat_ids in channel
											// The following output is a category id/name array. If channel has no associated category group, set variable to empty array to skip category filtering

											// Have $cat_cond conditional here, and intersect $cat_id & $cat_where_in for "isnot" condition
											if($cat_cond == "is")
											{
												$results = ee()->db->query("SELECT entry_id FROM exp_category_posts WHERE cat_id IN (".$cat_id.")");
											} else {
												$cat_id_single[] = $cat_id;
												$cat_id_leftover = array_diff($cat_where_in, $cat_id_single);
												$cat_id_leftover = implode(",", $cat_id_leftover);
												$results = ee()->db->query("SELECT entry_id FROM exp_category_posts WHERE cat_id IN (".$cat_id_leftover.")");
											}

											if($results->num_rows() > 0)
											{
												foreach($results->result_array() as $row)
												{
													$entry_id_array[] = $row['entry_id'];
												}
											} else {
													$entry_id_array[] = 0; // Yields no results, as no entry has an id of 0
											}

											ee()->db->where_in('channel_titles.entry_id', $entry_id_array);

										}
									} elseif(empty($permitted_cats)) {
										// This user is neither a Super Admin nor a user with permitted categories. Show nothing!
										$entry_id_array[] = 0;
										ee()->db->where_in('channel_titles.entry_id', $entry_id_array);
									}

								//
								// Not under Category Permissions
								//
								} else {
									$entry_id_array = array();
									// Check if cat_id is part of allowed cat_ids in channel
									// The following output is a category id/name array. If channel has no associated category group, set variable to empty array to skip category filtering
									$results = ee()->db->query("SELECT entry_id FROM exp_category_posts WHERE cat_id IN (".$cat_id.")");
									if($results->num_rows() > 0)
									{
										foreach($results->result_array() as $row)
										{
											$entry_id_array[] = $row['entry_id'];
										}
									} else {
											$entry_id_array[] = 0; // Yields no results, as no entry has an id of 0
									}

									if($cat_cond == "is")
									{
										ee()->db->where_in('channel_titles.entry_id', $entry_id_array);
									} else {
										ee()->db->where_not_in('channel_titles.entry_id', $entry_id_array);
									}
								}


							/**
							*	"No categories" - "none"
							*/
							} elseif ( $cat_id == "none") {

								//
								// Under the effect of Category Permissions
								//
								if(isset($installed_addons) && in_array('Category_permissions_ext', $this->installed_addons['extensions']) && $this->member_group_id != 1)
								{
									if( ee()->session->cache('zenbu', 'permitted_cats') )
									{

										$permitted_cats = ee()->session->cache('zenbu', 'permitted_cats');

									} else {

										ee()->load->add_package_path(PATH_THIRD . '/category_permissions');
										ee()->load->model('category_permissions_model');
										$permitted_cats = ee()->category_permissions_model->get_member_permitted_categories($this->member_id);
										ee()->load->remove_package_path(PATH_THIRD . '/category_permissions');

									}


									if( ! empty($permitted_cats))
									{
										// Build WHERE … IN statement
										$cat_where_in = implode($permitted_cats, ',');
										$cat_where_in = (empty($cat_where_in)) ? 0 : $cat_where_in;

										// For category filter to "none" under Category Permissions, show nothing, as entries without categories should not be shown
										$entry_id_array[] = 0; // Yields no results later down, as no entry has an id of 0

										// Query for the opposite of "is… none" (i.e. "is not … none"), which means show all entries with permitted categories
										$results = ee()->db->query("SELECT entry_id FROM exp_category_posts WHERE cat_id IN (" . $cat_where_in . ")");
										if($results->num_rows() > 0)
										{
											foreach($results->result_array() as $row)
											{
												$entry_id_array_isnot[] = $row['entry_id'];
											}
										} else {
											$entry_id_array_isnot[] = 0;
										}

										if($cat_cond == "is")
										{
											ee()->db->where_in('channel_titles.entry_id', $entry_id_array);
										} else {
											ee()->db->where_in('channel_titles.entry_id', $entry_id_array_isnot);
										}

									} elseif( empty($permitted_cats) ) {
										// This user is neither a Super Admin nor a user with permitted categories. Show nothing!
										$entry_id_array[] = 0;
										ee()->db->where_in('channel_titles.entry_id', $entry_id_array);
									}

								//
								// Not under Category Permissions
								//
								} else {

									// Drill down to cat_id / cat name array
									foreach($categories['categories'] as $channel_id_string => $dropdown_labels)
									{
										// go through the following only when $channel_id_string has "ch_id_". For "cat_url_title", etc, skip this
										if(strncmp($channel_id_string, 'ch_id_', 6) == 0)
										{
											foreach($dropdown_labels as $dropdown_label => $cat_dropdown_array)
											{
												if($dropdown_label == "dropdown_labels")
												{
													foreach($cat_dropdown_array as $cat_group_name => $cat_array)
													{
														if($cat_group_name != "" && $cat_group_name != "none")
														{
															foreach($cat_array as $single_cat_id => $cat_name)
															{
																$cat_array_raw[$cat_name] = $single_cat_id;
															}
														}
													}
												}
											}
										}
									}

									if( ! empty($cat_array_raw))
									{
										// Build WHERE … IN statement
										$cat_where_in = '';
										foreach($cat_array_raw as $name => $id)
										{
											if(is_numeric($id))
											{
												$cat_where_in .= $id.', ';
											}
										}
										$cat_where_in = substr($cat_where_in, 0, -2);

										// Create similar entry array as above, but with all categories from channel
										$results = ee()->db->query("SELECT entry_id FROM exp_category_posts WHERE cat_id IN  (".$cat_where_in.")");
										if($results->num_rows() > 0)
										{
											foreach($results->result_array() as $row)
											{
												$entry_id_array[] = $row['entry_id'];
											}
										} else {
												$entry_id_array[] = 0; // Yields no results, as no entry has an id of 0
										}

										if($cat_cond == "is")
										{
											ee()->db->where_not_in('channel_titles.entry_id', $entry_id_array);
										} else {
											ee()->db->where_in('channel_titles.entry_id', $entry_id_array);
										}
									}
								}

							/**
							*	"All categories"
							*/
							} elseif ( empty($cat_id) ) {

								//
								// Under the effect of Category Permissions
								//
								if(isset($installed_addons) && in_array('Category_permissions_ext', $this->installed_addons['extensions']) && $this->member_group_id != 1)
								{
									if( ee()->session->cache('zenbu', 'permitted_cats') )
									{
										$permitted_cats = ee()->session->cache('zenbu', 'permitted_cats');
									} else {
										ee()->load->add_package_path(PATH_THIRD . '/category_permissions');
										ee()->load->model('category_permissions_model');
										$permitted_cats = ee()->category_permissions_model->get_member_permitted_categories($this->member_id);
										ee()->load->remove_package_path(PATH_THIRD . '/category_permissions');
									}

									if( $permitted_cats && ! empty($permitted_cats))
									{
										$cat_where_in = implode($permitted_cats, ", ");
										$cat_where_in = (empty($cat_where_in)) ? 0 : $cat_where_in;

										// Create similar entry array as above, but with all categories from channel
										$results = ee()->db->query("SELECT entry_id FROM exp_category_posts WHERE cat_id IN  (".$cat_where_in.")");
										if($results->num_rows() > 0)
										{
											foreach($results->result_array() as $row)
											{
												$entry_id_array[] = $row['entry_id'];
											}
										} else {
												$entry_id_array[] = 0; // Yields no results, as no entry has an id of 0
										}

										if($cat_cond == "is")
										{
											ee()->db->where_in('channel_titles.entry_id', $entry_id_array);
										} else {
											// "Category - is not - All categories": Not entries with and without categories, so basically nothing.
											// Odd filter, but it's present, so set up for it.
											$entry_id_array[] = 0;
											ee()->db->where_in('channel_titles.entry_id', $entry_id_array);
										}

									} elseif($this->member_group_id != 1) {
										// This user is neither a Super Admin nor a user with permitted categories. Show nothing!
										$entry_id_array[] = 0;
										ee()->db->where_in('channel_titles.entry_id', $entry_id_array);
									}

								//
								// Not under Category Permissions
								//
								} else {
									// "Category - is not - All categories": Not entries with and without categories, so basically nothing.
									// Odd filter, but it's present, so set up for it.
									if($cat_cond == "isnot")
									{
										$entry_id_array[] = 0;
										ee()->db->where_in('channel_titles.entry_id', $entry_id_array);
									}
								}

							}
						break;
						case 'status':
							/*
							 * Add status filetering if status is present
							 */
							$status = $filter['3rd'];

							if( ! empty($status) && $status != 'all')
							{
								$where = '';
								if($filter['2nd'] == 'is')
								{
									$where = "exp_channel_titles.status = '" . $status. "'";
								} elseif($filter['2nd'] == 'isnot') {
									$where = "exp_channel_titles.status != '" . $status. "'";
								}


								/**
								*	=====================================
								*	Extension Hook zenbu_filter_by_status
								*	=====================================
								*
								*	Enables the addition of extra queries when filtering entries by status
								*	@param	int		$channel_id	The currently selected channel_id
								*	@param	string	$status		The currently selected status
								*	@param	array	$filter		The current entry filter rule array
								*	@param	string	$where		The partial query string
								*	@return $where 	string 		The modified query string
								*
								*/
								if (ee()->extensions->active_hook('zenbu_filter_by_status') === TRUE)
								{
									$where = ee()->extensions->call('zenbu_filter_by_status', $channel_id, $status, $filter, $where);
									if (ee()->extensions->end_script === TRUE) return;
								}

								if( ! empty($where))
								{
									ee()->db->where($where);
								}
							 }

						break;
						case 'author':
							/*
							 * Add filtering by author
							 */
							$author_id = $filter['3rd'];
							if( ! empty($author_id) && is_numeric($author_id))
							{
								if($filter['2nd'] == 'is')
								{
									ee()->db->where("channel_titles.author_id", $author_id);
								} elseif($filter['2nd'] == 'isnot') {
									ee()->db->where("channel_titles.author_id !=", $author_id);
								}
							}
						break;
						case 'sticky':
							/*
							 * Add filetering based on sticky
							 */
							$sticky = $filter['3rd'];
							if( ! empty($sticky))
							{
								if($filter['2nd'] == 'is')
								{
									ee()->db->where("channel_titles.sticky", $sticky);
								} elseif($filter['2nd'] == 'isnot') {
									ee()->db->where("channel_titles.sticky !=", $sticky);
								}
							}
						break;
						case 'entry_date': case 'expiration_date': case 'edit_date':
							$column = $filter['1st'];
							$range = $filter['2nd'];
							$date = $filter['3rd'];

							if( ! empty($date))
							{
								$now = ee()->localize->now;

								if(! is_array($date))
								{
									$date = strtotime($date);
								}
								elseif(is_array($date))
								{
									$date_from		= strtotime($date[0]);
									$date_to		= strtotime($date[1]);
								}
								// elseif(strncmp($date, '+', 1) == 0)
								// {
								// 	// THE FUTURE!
								// 	$date			= substr($date, 1);
								// 	$date			= $date*24*60*60; // Convert to seconds
								// 	$date			= $now + $date;
								// 	$comparator1	= "<";
								// 	$comparator2	= ">";
								// } elseif ($date != "range") {
								// 	// The past
								// 	$date			= $date*24*60*60; // Convert to seconds
								// 	$date			= $now - $date;
								// 	$comparator1	= ">";
								// 	$comparator2	= "<";
								// } else {
								// 	// The Range
								// 	$date_from		= strtotime($filter['date_from']);
								// 	$date_to		= strtotime($filter['date_to']) + 86400;
								// }

								// Edit date is stored as MySQL time. Need to convert to MySQL time in that case.
								if($column == 'edit_date')
								{
									if($range == "range")
									{
										$date_from = mdate('%Y%m%d%H%i%s', $date_from);
										$date_to = mdate('%Y%m%d%H%i%s', $date_to);
									} else {
										$date = mdate('%Y%m%d%H%i%s', $date);
										$now = mdate('%Y%m%d%H%i%s', $now);
									}
								}

								if($range == 'after')
								{
									ee()->db->where("channel_titles." . $column . " >= ", $date + 86400);
								}
								elseif($range == 'before')
								{
									ee()->db->where("channel_titles." . $column . " <= ", $date);
								}
								elseif($range == 'on' || $range == 'is')
								{
									ee()->db->where("channel_titles." . $column . " >= ", $date);
									ee()->db->where("channel_titles." . $column . " <= ", $date + 86400);
								}
								elseif($range == 'range')
								{
									ee()->db->where("channel_titles." . $column . " >= ", $date_from);
									ee()->db->where("channel_titles." . $column . " <= ", $date_to + 86400);
								}

							}
						break;
						case 'title': case 'url_title':
							$keyword = trim(ee()->db->escape_like_str($filter['3rd']));
							if ( ! empty($keyword))
							{
								$where = "";
								switch($filter['2nd'])
								{
									case "contains":
										$where = "exp_channel_titles.".$filter['1st']." LIKE '%" . $keyword . "%'";
									break;
									case "doesnotcontain":
										$where = "exp_channel_titles.".$filter['1st']." NOT LIKE '%" . $keyword . "%'";
									break;
									case "beginswith":
										$where = "exp_channel_titles.".$filter['1st']." LIKE '" . $keyword . "%'";
									break;
									case "doesnotbeginwith":
										$where = "exp_channel_titles.".$filter['1st']." NOT LIKE '%" . $keyword . "%'";
									break;
									case "endswith":
										$where = "exp_channel_titles.".$filter['1st']." LIKE '%" . $keyword . "'";
									break;
									case "doesnotendwith":
										$where = "exp_channel_titles.".$filter['1st']." NOT LIKE '%" . $keyword . "'";
									break;
									case "containsexactly":
										$where = "exp_channel_titles.".$filter['1st']." LIKE '" . $keyword . "'";
									break;
								}

								ee()->db->where($where);
							}
						break;
						case 'pages':
							$keyword = trim(ee()->db->escape_like_str($filter['3rd']));
							$keyword_len = strlen($keyword);
							if ( ! empty($keyword))
							{
								$entry_id_array = array();
								switch($filter['2nd'])
								{
									case "contains":
										foreach($output_pages as $entry_id => $page_uri)
										{
											if(strstr($page_uri, $keyword) !== FALSE)
											{
												$entry_id_array[] = $entry_id;
											}
										}
									break;
									case "doesnotcontain":
										foreach($output_pages as $entry_id => $page_uri)
										{
											if(strstr($page_uri, $keyword) === FALSE)
											{
												$entry_id_array[] = $entry_id;
											}
										}
									break;
									case "beginswith":
										foreach($output_pages as $entry_id => $page_uri)
										{
											if(substr($page_uri, 0, $keyword_len) == $keyword)
											{
												$entry_id_array[] = $entry_id;
											}
										}
									break;
									case "doesnotbeginwith":
										foreach($output_pages as $entry_id => $page_uri)
										{
											if(substr($page_uri, 0, $keyword_len) != $keyword)
											{
												$entry_id_array[] = $entry_id;
											}
										}
									break;
									case "endswith":
										foreach($output_pages as $entry_id => $page_uri)
										{
											if(substr($page_uri, -$keyword_len) == $keyword)
											{
												$entry_id_array[] = $entry_id;
											}
										}
									break;
									case "doesnotendwith":
										foreach($output_pages as $entry_id => $page_uri)
										{
											if(substr($page_uri, -$keyword_len) != $keyword)
											{
												$entry_id_array[] = $entry_id;
											}
										}
									break;
									case "containsexactly":
										foreach($output_pages as $entry_id => $page_uri)
										{
											if(strcmp($page_uri, $keyword) == 0)
											{
												$entry_id_array[] = $entry_id;
											}
										}
									break;
								}

								$entry_id_array = ! empty($entry_id_array) ? $entry_id_array : array(0);
								ee()->db->where_in('channel_titles.entry_id', $entry_id_array);

							} else {
								$entry_id_array = ! empty($output_pages) ? array_flip($output_pages) : array(0);

								switch($filter['2nd'])
								{
									case "isempty":
										ee()->db->where_not_in('channel_titles.entry_id', $entry_id_array);
									break;
									case "isnotempty":
										ee()->db->where_in('channel_titles.entry_id', $entry_id_array);
									break;
								}
							}
						break;
						case 'all':
							$keyword = trim(ee()->db->escape_like_str($filter['3rd']));
							if ( ! empty($keyword))
							{
								if( ! empty($field_ids['field']))
								{
									$count = 1;
									// The following is to add parenthesis in this part of the query
									$where = "";
									foreach($field_ids['field'] as $field_id => $value)
									{

										if($filter['2nd'] == "notcontains")
										{
											$where .= ($count == 1) ? '(exp_channel_data.field_id_'.$field_id.' NOT LIKE "%' . $keyword . '%"' : ' AND exp_channel_data.field_id_'.$field_id.' NOT LIKE "%'. $keyword . '%"';
										} elseif($filter['2nd'] == "contains") {
											$where .= ($count == 1) ? '(exp_channel_data.field_id_'.$field_id.' LIKE "%' . $keyword . '%"' : ' OR exp_channel_data.field_id_'.$field_id.' LIKE "%' . $keyword . '%"';
										}
										$count++;
									}
									// Close up query section containing parenthesis
									if($filter['2nd'] == "notcontains")
									{
										$where .= ' AND exp_channel_titles.title NOT LIKE "%' . $keyword . '%")';
									} elseif($filter['2nd'] == "contains") {
										$where .= ' OR exp_channel_titles.title LIKE "%' . $keyword . '%")';
									}
									( ! is_null($where) && ! empty($where)) ? ee()->db->where($where) : '';
								}
							}
						break;
						case is_numeric($filter['1st']) :
							$keyword = trim(ee()->db->escape_like_str($filter['3rd']));
							$field_id = $filter['1st'];
							$where = "";
							if(in_array($field_id, $this->field_ids))
							{
								if(isset($this->fieldtypes[$field_id]))
								{

									/**
									*	====================================
									*	Adding third-party fieldtype classes
									*	====================================
									*/

									$ft_object = $this->fields->loadFieldtypeClass($this->fieldtypes[$field_id]);

									if($ft_object && method_exists($ft_object, 'zenbu_result_query'))
									{

										$already_queried = 'already_queried_'.$this->fieldtypes[$field_id];

										// The TRUE/FALSE value for $already_queries_FIELDNAME
										// is to avoid declaring a table twice in the FROM MySQL statement
										$$already_queried = (isset($$already_queried) && $$already_queried === TRUE) ? TRUE : FALSE;

										// $this->installed_addons (optional arg in fieldtype class)
										$ft_object->zenbu_result_query($field_id);

										// Set $already_queries_FIELDNAME to TRUE so that FALSE is nenver passed again in the zenbu_result_query
										$$already_queried = TRUE;
									} else {
										if( ! empty($keyword))
										{
											$where = '';
											switch($filter['2nd'])
											{
												case "contains":
													$where = "exp_channel_data.field_id_".$field_id." LIKE '%" . $keyword . "%'";
												break;
												case "doesnotcontain":
													$where = "(exp_channel_data.field_id_".$field_id." NOT LIKE '%" . $keyword . "%' OR exp_channel_data.field_id_".$field_id." IS NULL)";
												break;
												case "beginswith":
													$where = "exp_channel_data.field_id_".$field_id." LIKE '" . $keyword . "%'";
												break;
												case "doesnotbeginwith":
													$where = "(exp_channel_data.field_id_".$field_id." NOT LIKE '" . $keyword . "%' OR exp_channel_data.field_id_".$field_id." IS NULL)";
												break;
												case "endswith":
													$where = "exp_channel_data.field_id_".$field_id." LIKE '%" . $keyword . "'";
												break;
												case "doesnotendwith":
													$where = "(exp_channel_data.field_id_".$field_id." NOT LIKE '%" . $keyword . "' OR exp_channel_data.field_id_".$field_id." IS NULL)";
												break;
												case "containsexactly":
													$where = "exp_channel_data.field_id_".$field_id." LIKE '" . $keyword . "'";
												break;
												case "isempty":
													$where = "(exp_channel_data.field_id_".$field_id." = ''
																OR exp_channel_data.field_id_".$field_id." IS NULL)";
												break;
												case "isnotempty":
													$where = "(exp_channel_data.field_id_".$field_id." != ''
																AND exp_channel_data.field_id_".$field_id." IS NOT NULL)";
												break;
											}

											if( ! empty($where))
											{
												ee()->db->where($where);
											}

										} else {
											$where = '';
											switch($filter['2nd'])
											{
												case "isempty":
													$where = "(exp_channel_data.field_id_".$field_id." = ''
																OR exp_channel_data.field_id_".$field_id." IS NULL)";
												break;
												case "isnotempty":
													$where = "(exp_channel_data.field_id_".$field_id." != ''
																AND exp_channel_data.field_id_".$field_id." IS NOT NULL)";
												break;
											}

											if( ! empty($where))
											{
												ee()->db->where($where);
											}

										}
									}


								} // if
							} else {
								($filter['2nd'] == "notin") ? ee()->db->not_like("channel_titles.title", $keyword) : ee()->db->like("channel_titles.title", $keyword);
							}

					}



				}
			}
		}



		/*
		 * Parse field query conditions
		 */

		foreach($this->display_settings['fields'] as $key => $field)
		{
			if($field['fieldId'] != 0)
			{
				$has_custom_fields = TRUE;
				ee()->db->select("channel_data.field_id_".$field['fieldId']);
			}

			if($field['fieldType'] == 'channel')
			{
				ee()->db->select("channels.channel_title");
			}

		}

		if(isset($has_custom_fields))
		{
			ee()->db->join('channel_data', 'exp_channel_titles.entry_id = exp_channel_data.entry_id');
		}



		/**
		 *
		 * Last few touches…
		 *
		 */
		ee()->db->from('channel_titles');
		ee()->db->join('channels', "exp_channels.channel_id = exp_channel_titles.channel_id"/*, 'left'*/);

		// Join autosave query if display autosave data is set
		// if(isset($settings['setting'][$channel_id]['show_autosave']) && ! empty($settings['setting'][$channel_id]['show_autosave']))
		// {
		// 	ee()->db->join('channel_entries_autosave', 'exp_channel_titles.entry_id = exp_channel_entries_autosave.original_entry_id', 'left');
		// }

		// If channel is 0 ("All channels") with a "any title/basic custom field" rule, or if channel is not 0, add the exp_channel_data table
		// if($channel_id != 0 || ($channel_id == 0 && find_rule('field', 'any_cf_title', $filters) === TRUE))
		// {
		// 	ee()->db->join('channel_data', "exp_channel_titles.entry_id = exp_channel_data.entry_id"/*, 'left'*/);
		// }


		/**
		 * Add filtering based on entry limit
		 */
		$sort = Request::param('sort', 'DESC');
		$orderby = Request::param('orderby', 'entry_date');
		switch ($orderby)
		{
			case "id":
				ee()->db->order_by('channel_titles.entry_id', $sort);
			break;
			case "title":
				ee()->db->order_by('channel_titles.title', $sort);
			break;
			case "url_title":
				ee()->db->order_by('channel_titles.url_title', $sort);
			break;
			case "entry_date":
				ee()->db->order_by('channel_titles.entry_date', $sort);
			break;
			case "expiration_date":
				ee()->db->order_by('channel_titles.expiration_date', $sort);
			break;
			case "edit_date":
				ee()->db->order_by('channel_titles.edit_date', $sort);
			break;
			case "url_title":
				ee()->db->order_by('channel_titles.url_title', $sort);
			break;
			case "status":
				ee()->db->order_by('channel_titles.status', $sort);
			break;
			case "channel":
				ee()->db->order_by('exp_channels.channel_title', $sort);
			break;
			case "author":
				ee()->db->join('exp_members', 'exp_channel_titles.author_id = exp_members.member_id');
				ee()->db->order_by('exp_members.screen_name', $sort);
			break;
			case "category":
				/* Can potentially slow down performance since two tables are being pulled in */
				ee()->db->join('category_posts AS cp', 'cp.entry_id = exp_channel_titles.entry_id', 'left');
				ee()->db->join('categories AS c', 'c.cat_id = cp.cat_id', 'left');
				ee()->db->order_by('group_concat(c.cat_name ORDER BY c.cat_name)', $sort);
			break;
			case "is_sticky":
				ee()->db->order_by('channel_titles.sticky', $sort);
			break;
			case "comments":
				ee()->db->order_by('channel_titles.comment_total', $sort);
			break;
			case "autosave":
				ee()->db->order_by('channel_entries_autosave.entry_id', $sort);
			break;
			case is_numeric($orderby):
				ee()->db->order_by('channel_data.field_id_'.$orderby, $sort);
			break;
			case "pages":
				if($sort == 'desc')
				{
					arsort($output_pages);
				}
				// Remove DB protection, just for a bit.
				ee()->db->_protect_identifiers = FALSE;
				$entry_fixed_order = implode(',', array_keys($output_pages));
				ee()->db->order_by('FIELD(' . ee()->db->dbprefix . 'channel_titles.entry_id, '.$entry_fixed_order.')', '');
				ee()->db->_protect_identifiers = TRUE;
			break;
			case ( ! empty($orderby)):

				/**
				*	===========================================
				*	Extension Hook zenbu_custom_order_sort
				*	===========================================
				*
				*	Adds custom sorting to Zenbu results
				*	@param $sort 	string	The sort order (asc or desc)
				*	@return void 			Build your order_by() Active Record statements in the extension
				*/
				if (ee()->extensions->active_hook('zenbu_custom_order_sort') === TRUE)
				{
					ee()->extensions->call('zenbu_custom_order_sort', $sort);
				 	if (ee()->extensions->end_script === TRUE) return;
				}


			break;
			default:
				ee()->db->order_by('channel_titles.entry_date', 'desc');
			break;
		}

		ee()->db->group_by("channel_titles.entry_id");

		//	----------------------------------------
		//	Determining result limit
		//	----------------------------------------
		// if(empty($limit))
		// {
		// 	$limit = isset($settings['setting']['general']['default_limit']) ? $settings['setting']['general']['default_limit'] : $this->default_limit;
		// }

		$limit = Request::param('limit', $this->default_limit);

		if( ! Request::param('perpage') || Request::param('perpage') == 0)
		{
			ee()->db->limit($limit);
		}
		else
		{
			ee()->db->limit($limit, Request::param('perpage') * $limit - $limit);
		}

		/**
		*	======================================
		*	Extension Hook zenbu_entry_query_end
		*	======================================
		*
		*	Any last words? Enables adding additional
		*	Active Record patterns/commands before
		*	committing the completed Active Record query
		*	@return void
		*
		*/
		if (ee()->extensions->active_hook('zenbu_entry_query_end') === TRUE)
		{
			ee()->extensions->call('zenbu_entry_query_end');
			if (ee()->extensions->end_script === TRUE) return;
		}

		$final_results	= ee()->db->get();
		$output['results'] = $final_results->result_array();

		$output['main_query'] = ee()->db->last_query();

		$total_query	= ee()->db->query("/* Zenbu getting total results */ \n SELECT FOUND_ROWS() as total_rows"); // Must be run right after the previous query to get all results
		$output['total_results'] = $total_query->row(1)->total_rows;

		return $output;
	}

	public function baseDataFromSelected($selector = 'toggle')
	{
		// Return data if already cached
        if(Session::getCache('entries_base_data'))
        {
            return Session::getCache('entries_base_data');
        }

		$output = array();

		$entries = Request::param($selector);

		if(empty($entries) || $entries === FALSE)
		{
			return $output;
		}

		$results = ee()->db->query("/* Zenbu baseDataFromSelected */ \n SELECT *
			FROM exp_channel_titles
			WHERE entry_id IN (".ee()->db->escape_str(implode(',', $entries)).")");

		if($results->num_rows() > 0)
		{
			$output = $results->result_array();
			Session::setCache('entries_base_data', $output);
			$results->free_result();
			return $output;
		}

		return $output;
	}
}
