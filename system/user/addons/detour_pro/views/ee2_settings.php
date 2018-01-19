<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    if ( ! function_exists('ee') )
	{
		function ee()
		{
			return get_instance();
		}
	}
?>

<?php foreach ($cp_messages as $cp_message_type => $cp_message) : ?>
	<p class="notice <?=$cp_message_type?>"><?=$cp_message?></p>
<?php endforeach; ?>

<?php	

	echo form_open($action_url);

	$this->table->set_template($cp_pad_table_template);
	$this->table->set_heading(
		array(
			'data' => ee()->lang->line('title_setting'),
			'style' => 'width:50%;'
		),
		array(
			'data' => ee()->lang->line('title_value'),
			'style' => 'width:50%;'
		)
	);
	
	// URI sniffer
	$uri = array(
      'ee'  => 'Expression Engine (ee()->uri->uri_string)',
      'php'    => 'PHP - $_SERVER[\'REQUEST_URI\']',
    );

	$this->table->add_row(
		form_label(ee()->lang->line('label_setting_url_detect'), '') . 
		'<div class="subtext">' . ee()->lang->line('subtext_setting_url_detect') . '</div>', 
		form_dropdown('setting_uri_detect', $uri, '')
	);	
	
	// Default redirect method
	$redirects = array(
      '301'  => '301',
      '302'    => '302'
    );

	$this->table->add_row(
		form_label(ee()->lang->line('label_setting_default_method'), '') . 
		'<div class="subtext">' . ee()->lang->line('subtext_setting_default_method') . '</div>', 
		form_dropdown('setting_default_method', $redirects, '')
	);	
	
	
	echo $this->table->generate();							
	
	echo form_submit(array('name' => 'submit', 'value' => ee()->lang->line('save_settings'), 'class' => 'submit'));
	echo form_close();
	
?>