<?php foreach ($report_results as $index => $row): ?>
<?php
$class = ($index % 2) ? 'odd' : 'even';
?>
<tr class="entryRow <?php echo $class; ?>" >
	<td><?php echo $row['id']; ?></td>
	<td><?php echo date('Y-m-d H:i:s', $row['unix_time']); ?></td>
	<td><?php echo $row['member_id']; ?></td>
	<td><?php echo $row['remote_ip']; ?></td>
	<td><?php echo $row['directory']; ?></td>
	<td><?php echo $row['file_name']; ?></td>
	<?php foreach ($custom_fields as $field): ?>
		<td><?php echo $row[ 'cf_'.$field['field_name'] ]; ?></td>
	<?php endforeach; ?>
</tr>
<?php endforeach; ?>	
