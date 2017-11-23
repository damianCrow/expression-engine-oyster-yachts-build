<?php
	// Category
	$categories = array(
		'cat_1' => 'Category 1',
		'cat_2' => 'Category 2'
	);
?>
<div class="box">
	<h1><?php echo ee()->lang->line('nav_add_detour'); ?></h1>

	<?php echo form_open($action_url, array('class'=>'settings')); ?>

	<?php if($id) echo form_hidden('id', $id); ?>

	<fieldset class="col-group">
		<div class="setting-txt col w-8">
			<h3><?php echo ee()->lang->line('label_original_url'); ?></h3>
			<em><?php echo ee()->lang->line('subtext_original_url'); ?></em>
		</div>
		<div class="setting-field col w-8 last">
			<?php echo form_input(
				array(
					'name' => 'original_url',
					'id' => 'original_url',
					'value' => $original_url,
					'size' => '50'
				)); ?>
		</div>
	</fieldset>

	<fieldset class="col-group">
		<div class="setting-txt col w-8">
			<h3><?php echo ee()->lang->line('label_new_url'); ?></h3>
			<em><?php echo ee()->lang->line('subtext_new_url'); ?></em>
		</div>
		<div class="setting-field col w-8 last">
			<?php echo form_input(
				array(
					'name' => 'new_url',
					'id' => 'new_url',
					'value' => $new_url,
					'size' => '50'
				)); ?>
		</div>
	</fieldset>

	<fieldset class="col-group">
		<div class="setting-txt col w-8">
			<h3><?php echo ee()->lang->line('label_detour_method'); ?></h3>
			<em><?php echo ee()->lang->line('subtext_detour_method'); ?></em>
		</div>
		<div class="setting-field col w-8 last">
			<?php echo form_dropdown('detour_method', $detour_methods, $detour_method); ?>
		</div>
	</fieldset>

	<h2><?php echo ee()->lang->line('title_hits'); ?> <?php echo $detour_hits; ?></h2>

	<fieldset class="col-group">
		<div class="setting-txt col w-8">
			<h3><?php echo ee()->lang->line('label_start_date'); ?></h3>
			<em><?php echo ee()->lang->line('subtext_start_date'); ?></em>
		</div>
		<div class="setting-field col w-8 last">
			<?php echo form_input(
				array(
					'name' => 'start_date',
					'id' => 'start_date',
					'value' => $start_date,
					'size' => '30',
					'class' => 'datepicker'
				));
			?>
		</div>
	</fieldset>

<?php
	if($start_date)
	{
?>
	<fieldset class="col-group">
		<div class="setting-txt col w-8">
			<h3><?php echo ee()->lang->line('label_clear_start_date'); ?></h3>
			<em><?php echo ee()->lang->line('subtext_clear_start_date'); ?></em>
		</div>
		<div class="setting-field col w-8 last">
			<label><?php echo form_checkbox('clear_start_date', '1'); ?> <?php echo ee()->lang->line('label_clear_start_date'); ?></label>
		</div>
	</fieldset>
<?php
	}
?>

	<fieldset class="col-group">
		<div class="setting-txt col w-8">
			<h3><?php echo ee()->lang->line('label_end_date'); ?></h3>
			<em><?php echo ee()->lang->line('subtext_end_date'); ?></em>
		</div>
		<div class="setting-field col w-8 last">
			<?php echo form_input(
				array(
					'name' => 'end_date',
					'id' => 'end_date',
					'value' => $end_date,
					'size' => '30',
					'class' => 'datepicker'
				));
			?>
		</div>
	</fieldset>

<?php
	if($end_date)
	{
?>
	<fieldset class="col-group">
		<div class="setting-txt col w-8">
			<h3><?php echo ee()->lang->line('label_clear_end_date'); ?></h3>
			<em><?php echo ee()->lang->line('subtext_clear_end_date'); ?></em>
		</div>
		<div class="setting-field col w-8 last">
			<label><?php echo form_checkbox('clear_end_date', '1'); ?> <?php echo ee()->lang->line('label_clear_end_date'); ?></label>
		</div>
	</fieldset>
<?php
	}
?>

<?php
/*
	// Owner
	<fieldset class="col-group">
		<div class="setting-txt col w-8">
			<h3><?php echo ee()->lang->line('label_detour_owner'); ?></h3>
			<em><?php echo ee()->lang->line('subtext_detour_owner'); ?></em>
		form_input(
			array(
				'name' => 'owner',
				'id' => 'owner',
				// 'value' => $this->get_value( $values, "filename" ),
				'size' => '50'
			)
		)
	);



	<fieldset class="col-group">
		<div class="setting-txt col w-8">
			<h3><?php echo ee()->lang->line('label_detour_category'); ?></h3>
			<em><?php echo ee()->lang->line('subtext_detour_category'); ?></em>
		form_dropdown('detour_category', $categories)
	);
*/
?>

	<?php echo form_submit(array('name' => 'submit', 'value' => ee()->lang->line('btn_save_detour'), 'class' => 'btn action')); ?>

	<?php echo form_close(); ?>
</div>