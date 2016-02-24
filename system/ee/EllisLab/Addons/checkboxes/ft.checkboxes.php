<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2015, EllisLab, Inc.
 * @license		https://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 2.0
 * @filesource
 */

// --------------------------------------------------------------------

/**
 * ExpressionEngine Option Group Fieldtype Class
 *
 * @package		ExpressionEngine
 * @subpackage	Fieldtypes
 * @category	Fieldtypes
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Checkboxes_ft extends EE_Fieldtype {

	var $info = array(
		'name'		=> 'Checkboxes',
		'version'	=> '1.0'
	);

	var $has_array_data = TRUE;

	// used in display_field() below to set
	// some defaults for third party usage
	var $settings_vars = array(
		'field_text_direction'	=> 'rtl',
		'field_pre_populate'	=> 'n',
		'field_list_items'		=> array(),
		'field_pre_field_id'	=> '',
		'field_pre_channel_id'	=> ''
	);

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function __construct()
	{
		parent::__construct();
		ee()->load->helper('custom_field');
	}

	// --------------------------------------------------------------------

	function validate($data)
	{
		$selected = decode_multi_field($data);
		$selected = empty($selected) ? array() : (array) $selected;

		// in case another field type was here
		$field_options = $this->_get_field_options($data);
		$field_options = $this->_flatten($field_options);

		if ($selected)
		{
			if ( ! is_array($selected))
			{
				$selected = array($selected);
			}

			$selected = form_prep($selected);
			$unknown = array_diff($selected, array_keys($field_options));

			if (count($unknown) > 0)
			{
				return 'Invalid Selection';
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	protected function _flatten($options)
	{
		$out = array();

		foreach ($options as $key => $item)
		{
			if (is_array($item))
			{
				$out[$key] = $item['name'];

				foreach ($this->_flatten($item['children']) as $k => $v)
				{
					$out[$k] = $v;
				}
			}
			else
			{
				$out[$key] = $item;
			}
		}

		return $out;
	}

	// --------------------------------------------------------------------

	function display_field($data)
	{
		return $this->_display_field($data);
	}

	// --------------------------------------------------------------------

	function grid_display_field($data)
	{
		return $this->_display_field(form_prep($data), 'grid');
	}

	// --------------------------------------------------------------------

	private function _display_field($data, $container = 'fieldset')
	{
		array_merge($this->settings, $this->settings_vars);

		$values = decode_multi_field($data);

		if (isset($this->settings['string_override']) && $this->settings['string_override'] != '')
		{
			return $this->settings['string_override'];
		}

		$values = decode_multi_field($data);
		$field_options = $this->_get_field_options($data);

		if (REQ == 'CP')
		{
			return ee('View')->make('checkboxes:publish')->render(array(
				'field_name' => $this->field_name,
				'values' => $values,
				'options' => $field_options,
				'editable' => isset($this->settings['editable']) ? $this->settings['editable'] : FALSE,
				'editing' => isset($this->settings['editing']) ? $this->settings['editing'] : FALSE,
				'deletable' => isset($this->settings['deletable']) ? $this->settings['deletable'] : FALSE,
				'group_id' => isset($this->settings['group_id']) ? $this->settings['group_id'] : 0,
				'manage_toggle_label' => isset($this->settings['manage_toggle_label']) ? $this->settings['manage_toggle_label'] : lang('manage'),
				'content_item_label' => isset($this->settings['content_item_label']) ? $this->settings['content_item_label'] : ''
			));
		}

		$r = '<div class="scroll-wrap pr">';

		$r .= $this->_display_nested_form($field_options, $values);

		$r .= '</div>';

		switch ($container)
		{
			case 'grid':
				$r = $this->grid_padding_container($r);
				break;

			default:
				$r = form_fieldset('').$r.form_fieldset_close();
				break;
		}

		return $r;
	}

	protected function _display_nested_form($fields, $values, $child = FALSE)
	{
		$out = '';

		foreach ($fields as $id => $option)
		{
			$checked = (in_array(form_prep($option), $values)) ? TRUE : FALSE;

			if (is_array($option))
			{
				$out .= '<label>'.form_checkbox($this->field_name.'[]', $id, $checked).NBS.$option['name'].'</label>';
				$out .= $this->_display_form($option['children'], $values, TRUE);
			}
			else
			{
				$out .= '<label>'.form_checkbox($this->field_name.'[]', $id, $checked).NBS.$option.'</label>';
			}
		}

		return $out;
	}

	// --------------------------------------------------------------------

	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		ee()->load->helper('custom_field');
		$data = decode_multi_field($data);

		ee()->load->library('typography');

		if ($tagdata)
		{
			return $this->_parse_multi($data, $params, $tagdata);
		}
		else
		{
			return $this->_parse_single($data, $params);
		}
	}

	// --------------------------------------------------------------------

	function _parse_single($data, $params)
	{
		if (isset($params['limit']))
		{
			$limit = intval($params['limit']);

			if (count($data) > $limit)
			{
				$data = array_slice($data, 0, $limit);
			}
		}

		if (isset($params['markup']) && ($params['markup'] == 'ol' OR $params['markup'] == 'ul'))
		{
			$entry = '<'.$params['markup'].'>';

			foreach($data as $dv)
			{
				$entry .= '<li>';
				$entry .= $dv;
				$entry .= '</li>';
			}

			$entry .= '</'.$params['markup'].'>';
		}
		else
		{
			$entry = implode(', ', $data);
		}

		// Experimental parameter, do not use
		if (isset($params['raw_output']) && $params['raw_output'] == 'yes')
		{
			return ee()->functions->encode_ee_tags($entry);
		}

		$text_format = ($this->content_type() == 'grid')
			? $this->settings['field_fmt'] : $this->row('field_ft_'.$this->field_id);

		return ee()->typography->parse_type(
				ee()->functions->encode_ee_tags($entry),
				array(
						'text_format'	=> $text_format,
						'html_format'	=> $this->row['channel_html_formatting'],
						'auto_links'	=> $this->row['channel_auto_link_urls'],
						'allow_img_url' => $this->row['channel_allow_img_urls']
					  )
		);
	}

	// --------------------------------------------------------------------

	function _parse_multi($data, $params, $tagdata)
	{
		$chunk = '';
		$raw_chunk = '';
		$limit = FALSE;

		// Limit Parameter
		if (is_array($params) AND isset($params['limit']))
		{
			$limit = $params['limit'];
		}

		$text_format = (isset($this->row['field_ft_'.$this->field_id]))
			? $this->row['field_ft_'.$this->field_id] : 'none';

		foreach($data as $key => $item)
		{
			if ( ! $limit OR $key < $limit)
			{
				$vars['item'] = $item;
				$vars['count'] = $key + 1;	// {count} parameter

				$tmp = ee()->functions->prep_conditionals($tagdata, $vars);
				$raw_chunk .= ee()->functions->var_swap($tmp, $vars);

				$vars['item'] = ee()->typography->parse_type(
						$item,
						array(
								'text_format'	=> $text_format,
								'html_format'	=> $this->row['channel_html_formatting'],
								'auto_links'	=> $this->row['channel_auto_link_urls'],
								'allow_img_url' => $this->row['channel_allow_img_urls']
							  )
						);

				$chunk .= ee()->functions->var_swap($tmp, $vars);
			}
			else
			{
				break;
			}
		}

		// Everybody loves backspace
		if (isset($params['backspace']))
		{
			$chunk = substr($chunk, 0, - $params['backspace']);
			$raw_chunk = substr($raw_chunk, 0, - $params['backspace']);
		}

		// Experimental parameter, do not use
		if (isset($params['raw_output']) && $params['raw_output'] == 'yes')
		{
			return ee()->functions->encode_ee_tags($raw_chunk);
		}

		return $chunk;
	}

	function display_settings($data)
	{
		$format_options = ee()->addons_model->get_plugin_formatting(TRUE);

		$defaults = array(
			'field_fmt' => '',
			'field_pre_populate' => FALSE,
			'field_list_items' => '',
			'field_pre_channel_id' => 0,
			'field_pre_field_id' => 0
		);

		foreach ($defaults as $setting => $value)
		{
			$data[$setting] = isset($data[$setting]) ? $data[$setting] : $value;
		}

		$settings = array(
			array(
				'title' => 'field_fmt',
				'fields' => array(
					'field_fmt' => array(
						'type' => 'select',
						'choices' => $format_options,
						'value' => $data['field_fmt']
					)
				)
			),
			array(
				'title' => 'checkbox_options',
				'desc' => 'checkbox_options_desc',
				'fields' => array(
					'field_pre_populate_n' => array(
						'type' => 'radio',
						'name' => 'field_pre_populate',
						'choices' => array(
							'n' => lang('field_populate_manually'),
						),
						'value' => $data['field_pre_populate'] ? 'y' : 'n'
					),
					'field_list_items' => array(
						'type' => 'textarea',
						'value' => $data['field_list_items']
					),
					'field_pre_populate_y' => array(
						'type' => 'radio',
						'name' => 'field_pre_populate',
						'choices' => array(
							'y' => lang('field_populate_from_channel'),
						),
						'value' => $data['field_pre_populate'] ? 'y' : 'n'
					),
					'field_pre_populate_id' => array(
						'type' => 'select',
						'choices' => $this->get_channel_field_list(),
						'value' => $data['field_pre_channel_id'] . '_' . $data['field_pre_field_id']
					)
				)
			)
		);

		return array('field_options_checkboxes' => array(
			'label' => 'field_options',
			'group' => 'checkboxes',
			'settings' => $settings
		));
	}

	public function grid_display_settings($data)
	{
		$format_options = ee()->addons_model->get_plugin_formatting(TRUE);

		return array(
			'field_options' => array(
				array(
					'title' => 'field_fmt',
					'fields' => array(
						'field_fmt' => array(
							'type' => 'select',
							'choices' => $format_options,
							'value' => isset($data['field_fmt']) ? $data['field_fmt'] : 'none',
						)
					)
				),
				array(
					'title' => 'checkbox_options',
					'desc' => 'grid_checkbox_options_desc',
					'fields' => array(
						'field_list_items' => array(
							'type' => 'textarea',
							'value' => isset($data['field_list_items']) ? $data['field_list_items'] : ''
						)
					)
				)
			)
		);
	}

	function _get_field_options($data)
	{
		$field_options = array();

		if ( ! isset($this->settings['field_pre_populate'])
			OR $this->settings['field_pre_populate'] == 'n'
				OR $this->settings['field_pre_populate'] == FALSE)
		{
			if ( ! is_array($this->settings['field_list_items']))
			{
				foreach (explode("\n", trim($this->settings['field_list_items'])) as $v)
				{
					$v = trim($v);
					$field_options[$v] = $v;
				}
			}
			else
			{
				$field_options = $this->settings['field_list_items'];
			}
		}
		else
		{
			// We need to pre-populate this menu from an another channel custom field

			ee()->db->select('field_id_'.$this->settings['field_pre_field_id']);
			ee()->db->where('channel_id', $this->settings['field_pre_channel_id']);
			$pop_query = ee()->db->get('channel_data');

			if ($pop_query->num_rows() > 0)
			{
				foreach ($pop_query->result_array() as $prow)
				{
					if (trim($prow['field_id_'.$this->settings['field_pre_field_id']]) == '')
					{
					 	continue;
					}

					$selected = ($prow['field_id_'.$this->settings['field_pre_field_id']] == $data) ? 1 : '';
					$pretitle = substr($prow['field_id_'.$this->settings['field_pre_field_id']], 0, 110);
					$pretitle = str_replace(array("\r\n", "\r", "\n", "\t"), " ", $pretitle);
					$pretitle = form_prep($pretitle);

					$field_options[form_prep(trim($prow['field_id_'.$this->settings['field_pre_field_id']]))] = $pretitle;
				}
			}
		}

		return $field_options;
	}

	// --------------------------------------------------------------------

	/**
	 * Accept all content types.
	 *
	 * @param string  The name of the content type
	 * @return bool   Accepts all content types
	 */
	public function accepts_content_type($name)
	{
		return TRUE;
	}

	public function save($data)
	{
		if (is_array($data))
		{
			ee()->load->helper('custom_field');
			$data = encode_multi_field($data);
		}

		return $data;
	}
}

// END Checkboxes_ft class

/* End of file ft.checkboxes.php */
/* Location: ./system/expressionengine/fieldtypes/ft.checkboxes.php */
