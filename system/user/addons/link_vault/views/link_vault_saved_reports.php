<h3><?php echo lang('saved_reports_title'); ?></h3>

<?php if ( !empty($saved_reports) ): ?>
	<table class="mainTable" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th style="width:10%"><?php echo lang('sr_col_id'); ?></th>
				<th style="width:50%"><?php echo lang('sr_col_title'); ?></th>
				<th style="width:40%"><?php echo lang('sr_col_actions'); ?></th>
			</tr>
		</thead>
	
		<tbody id="report_table_body">
		<?php foreach ($saved_reports as $row):?>
			<tr>
				<td><?php echo $row['id']; ?></td>
				<td><?php echo $row['title']; ?></td>
				<td><a href="<?php echo $reports_url.AMP.'report_id='.$row['id']; ?>" >Run Report</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo $delete_report_url.AMP.'report_id='.$row['id']; ?>" >Delete</a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php else: ?>
<p><?php echo lang('no_saved_reports'); ?></p>
<?php endif; ?>