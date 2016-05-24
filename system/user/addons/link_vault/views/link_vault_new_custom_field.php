<div class="box mb" >
	<h1><?php echo lang('new_custom_field_title'); ?></h1>
	<div class="tbl-ctrls" >
		<?php echo form_open($create_custom_field_action); ?>
		<table class="mainTable" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th style='width:20%'><?php echo lang('label_setting_description'); ?></th>
					<th><?php echo lang('label_setting_value'); ?></th>
				</tr>
			</thead>
			<tr class='odd' >
				<td><b><?php echo lang('label_field_label'); ?></b></td>
				<td><input type='text' id='cf_field_label' name='cf_field_label' /></td>
			</tr>
			<tr class='even' >
				<td><b><?php echo lang('label_field_name'); ?></b><br /><?php echo lang('field_name_desc'); ?></td>
				<td><input type='text' id='cf_field_name' name='cf_field_name' /></td>
			</tr>
			<tr class='odd' >
				<td><b><?php echo lang('label_field_type'); ?></b></td>
				<td>
					<select id='cf_field_type' name='cf_field_type' style='width:150px' >
						<option value='VARCHAR' selected='selected' >Text</option>
						<option value='INT' >Integer</option>
						<option value='FLOAT' >Float</option>	
					</select>&nbsp;&nbsp;
					<span id='cf_field_type_description' ></span>
				</td>
			</tr>
			<tr class='even' >
				<td><b><?php echo lang('label_field_length'); ?></b><br /><?php echo lang('optional_flag'); ?></td>
				<td><input type='number' id='cf_field_length' name='cf_field_length' /></td>
			</tr>
			<tr class='odd' >
				<td><b><?php echo lang('label_field_default'); ?></b><br /><?php echo lang('optional_flag'); ?></td>
				<td><input type='text' id='cf_field_default' name='cf_field_default' /></td>
			</tr>
			
		</table>
		<button name="cf_submit" type="submit" value="create_field" class="submit btn mb" >Create Field</button>
		<?php echo form_close(); ?>
	</div> <!-- .tbl-ctrls -->
</div> <!-- .box.mb -->

<input type="hidden" id="desc_int" value="<?php echo lang('desc_int'); ?>" >
<input type="hidden" id="desc_float" value="<?php echo lang('desc_float'); ?>" >
<input type="hidden" id="desc_varchar" value="<?php echo lang('desc_varchar'); ?>" >
