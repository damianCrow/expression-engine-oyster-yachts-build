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

	$cp_table_template['table_open'] = '<table class="mainTable addDetour" border="0" cellspacing="0" cellpadding="0">';
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(
		array(
			'data' => ee()->lang->line('title_url'),
			'style' => 'width:26%;'
		),
		array(
			'data' => ee()->lang->line('title_redirect'),
			'style' => 'width:26%;'
		),
		array(
			'data' => ee()->lang->line('title_method'),
			'style' => 'width:6%;'
		),
		array(
			'data' => ee()->lang->line('title_start'),
			'style' => 'width:21%;'
		),
		array(
			'data' => ee()->lang->line('title_end'),
			'style' => 'width:21%;'
		)
	);

	$this->table->add_row(
		form_input(
			array(
				'name' => 'original_url',
				'id' => 'original_url'
			)
		),
		form_input(
			array(
				'name' => 'new_url',
				'id' => 'new_url'
			)
		),
		form_dropdown('detour_method', $detour_methods),
		form_input(
			array(
				'name' => 'start_date',
				'id' => 'start_date',
				'class' => 'datepicker'
			)
		),
		form_input(
			array(
				'name' => 'end_date',
				'id' => 'end_date',
				'class' => 'datepicker'
			)
		)
	);

	$this->table->add_row(
		ee()->lang->line('dir_uri'),
		ee()->lang->line('dir_detour'),
		array('data' => '&nbsp', 'colspan' => 3)
	);

	echo $this->table->generate();
	$this->table->clear();

	echo form_submit(array('name' => 'submit', 'value' => ee()->lang->line('btn_save_detour'), 'class' => 'submit'));

	echo '<br /><br />';
	echo form_input(
			array(
				'name' => 'search',
				'id' => 'search',
				'placeholder' => 'Search...',
				'style' => 'float:right;width:200px;'
			)
		), '<br /><br />', "\n";

	$this->table->set_template($cp_pad_table_template);
	$this->table->set_heading(
		array(
			'data' => '<a href="'.$base_url.'&sort=original_url&sort_dir='.$sort_dir['original_url'].'">'.ee()->lang->line('title_url').'</a>',
			'style' => 'width:35%;',
			'class' => ($sort == 'original_url' ? 'sorting_'.$sort_dir['current'] : '')
		),
		array(
			'data' => '<a href="'.$base_url.'&sort=new_url&sort_dir='.$sort_dir['new_url'].'">'.ee()->lang->line('title_redirect').'</a>',
			'style' => 'width:35%;',
			'class' => ($sort == 'new_url' ? 'sorting_'.$sort_dir['current'] : '')
		),
		array(
			'data' => '<a href="'.$base_url.'&sort=detour_method&sort_dir='.$sort_dir['detour_method'].'">'.ee()->lang->line('title_method').'</a>',
			'style' => 'width:5%;',
			'class' => ($sort == 'detour_method' ? 'sorting_'.$sort_dir['current'] : '')
		),
		array(
			'data' => '<a href="'.$base_url.'&sort=start_date&sort_dir='.$sort_dir['start_date'].'">'.ee()->lang->line('title_start').'</a>',
			'style' => 'width:10%;',
			'class' => ($sort == 'start_date' ? 'sorting_'.$sort_dir['current'] : '')
		),
		array(
			'data' => '<a href="'.$base_url.'&sort=end_date&sort_dir='.$sort_dir['end_date'].'">'.ee()->lang->line('title_end').'</a>',
			'style' => 'width:10%;',
			'class' => ($sort == 'end_date' ? 'sorting_'.$sort_dir['current'] : '')
		),
		array(
			'data' => 'Delete'
		)
	);

	foreach($current_detours as $detour)
	{
		$this->table->add_row(
			'<a href="' . $detour['advanced_link'] . '">' . $detour['original_url'] . '</a>',
			$detour['new_url'],
			'<strong>' . $detour['detour_method'] . '</strong>',
			$detour['start_date'],
			$detour['end_date'],
			'<input type="checkbox" name="detour_delete[]" value="' . $detour['detour_id'] . '" />'
		);

	}

	if(isset($pagination)) {
		$this->table->add_row(
			array('data'=>$pagination, 'colspan'=>6)
		);
	}

	/*
	$this->table->add_row(
		form_input('original_url', ''),
		form_input('new_url', ''),
		form_dropdown('detour_method', $detour_methods),
		'&nbsp;',
		'&nbsp;',
		'',
		''
	);

	$this->table->add_row(
		ee()->lang->line('dir_uri'),
		ee()->lang->line('dir_detour'),
		'&nbsp;',
		'&nbsp;',
		'&nbsp;',
		'',
		''
	);
	*/

	echo $this->table->generate();
	$this->table->clear();

	echo form_submit(array('name' => 'submit', 'value' => ee()->lang->line('btn_delete_detours'), 'class' => 'submit'));

	echo form_close();


?>