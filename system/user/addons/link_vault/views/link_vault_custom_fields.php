<div class="box mb" >
	<h1><?php echo lang('custom_fields_title'); ?></h1>
	<div class="tbl-ctrls" >
		<p><a href='<?php echo $new_custom_field_url; ?>' class='submit btn' title='' ><?php echo lang('button_create_custom_field'); ?></a></p>
		<?php echo form_open($delete_form_action); ?>
		<table class="mainTable" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th><?php echo lang('label_field_id'); ?></th>
					<th><?php echo lang('label_field_label'); ?></th>
					<th><?php echo lang('label_field_name'); ?></th>
				</tr>
			</thead>
			
			<?php 
				if (empty($custom_fields))
					echo "<tr class='odd' ><td colspan='4' >No Custom Fields</td></tr>";
				foreach($custom_fields AS $index => $attrs):
			?>
				<?php $class = ($index % 2) ? 'odd' : 'even'; ?>
				<tr class="entryRow <?php echo $class; ?>" >
					<td style="width: 5%"><input type="checkbox" id="delete_cf_<?php echo $attrs['field_id']; ?>" name="cf_delete[<?php echo $attrs['field_id']; ?>]" value="true" /></td>
					<td style="width: 10%"><?php echo $attrs['field_id']; ?></td>
					<td style="width: 40%"><?php echo $attrs['field_label']; ?></td>
					<td style="width: 45%"><?php echo $attrs['field_name']; ?></td>
				</tr>		
			<?php endforeach; /* $custom_fields */ ?>
		</table>
		<button name="cf_submit" type="submit" value="delete" class="submit btn mb" >Delete</button>
		<?php echo form_close(); ?>
	</div> <!-- .tbl-ctrls -->
</div> <!-- .box.mb -->

	
