<div class="box mb" >
	<h1><?php echo lang('settings_title'); ?></h1>
	<div class="tbl-ctrls" >
		<?php echo form_open($update_settings_url); ?>
		<table class="mainTable" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th><?php echo lang('label_setting_description'); ?></th>
					<th><?php echo lang('label_setting_value'); ?></th>
				</tr>
			</thead>
			
			<tr class="odd" >
				<td><?php echo lang('setting_salt'); ?></td>
				<td>
				<?php
				$salt_input = array('id' => 'lv_salt', 'name' => 'lv_salt', 'value' => $settings['salt']);
				if ($override_settings['salt'])
					$salt_override = '<br /><strong>'.lang('override_label').'</strong>'.$override_settings['salt'];
				else
					$salt_override = '';
				echo form_input($salt_input).$salt_override;
				?>
				</td>
			</tr>
			<tr class="even" >
				<td><?php echo lang('setting_hidden_folder'); ?></td>
				<td>
				<?php 
				$hidden_folder_input = array('id' => 'lv_hidden_folder', 'name' => 'lv_hidden_folder', 'value' => $settings['hidden_folder']);
				if ($override_settings['hidden_folder'])
				{
					$hidden_folder_override = '<br /><strong>'.lang('override_label').'</strong>'.$override_settings['hidden_folder'];
					$hidden_folder_input['readonly'] = 'readonly';
				}
				else
					$hidden_folder_override = '';
				echo form_input($hidden_folder_input).$hidden_folder_override;
				?>
				</td>
			</tr>
			<tr class="odd" >
				<td><?php echo lang('setting_leech_url'); ?></td>
				<td>
				<?php 
				$leech_url_input = array('id' => 'lv_leech_url', 'name' => 'lv_leech_url', 'value' => $settings['leech_url']);
				if ($override_settings['leech_url'])
				{
					$leech_url_override = '<br /><strong>'.lang('override_label').'</strong>'.$override_settings['leech_url'];
					$leech_url_input['readonly'] = 'readonly';
				}
				else
					$leech_url_override = '';
				echo form_input($leech_url_input).$leech_url_override; 
				?>	
				</td>
			</tr>
			<tr class="even" >
				<td><?php echo lang('setting_missing_url'); ?></td>
				<td>
				<?php 
				$missing_url_input = array('id' => 'lv_missing_url', 'name' => 'lv_missing_url', 'value' => $settings['missing_url']);
				if ($override_settings['missing_url'])
				{
					$missing_url_override = '<br /><strong>'.lang('override_label').'</strong>'.$override_settings['missing_url'];
					$missing_url_input['readonly'] = 'readonly';
				}
				else
					$missing_url_override = '';
				echo form_input($missing_url_input).$missing_url_override; 
				?>	
				</td>
			</tr>
			<tr class="odd" >
				<td><?php echo lang('setting_block_leeching'); ?></td>
				<td>
					<?php
					$options = array('1' => lang('option_yes'), '0' => lang('option_no') );
					if ($override_settings['block_leeching'] != '')
					{
						$block_leeching_override = '<br /><strong>'.lang('override_label').'</strong>'.$options[$override_settings['block_leeching']];
						echo "<strong>",lang('stored_label'),"</strong>",$options[$settings['block_leeching']],form_hidden('lv_block_leeching', $settings['block_leeching']),$block_leeching_override;
					}
					else
						echo form_dropdown('lv_block_leeching', $options, $settings['block_leeching']);			
					?>
				</td>
			</tr>	
			<tr class="even" >
				<td><?php echo lang('setting_log_leeching'); ?></td>
				<td>
					<?php
					$options = array('1' => lang('option_yes'), '0' => lang('option_no') );
					if ($override_settings['log_leeching'] != '')
					{
						$log_leeching_override = '<br /><strong>'.lang('override_label').'</strong>'.$options[$override_settings['log_leeching']];
						echo "<strong>",lang('stored_label'),"</strong>",$options[$settings['log_leeching']],form_hidden('lv_log_leeching', $settings['log_leeching']),$log_leeching_override;
					}
					else
						echo form_dropdown('lv_log_leeching', $options, $settings['log_leeching']);			
					?>
				</td>
			</tr>
			<tr class="odd" >
				<td><?php echo lang('setting_log_link_clicks'); ?></td>
				<td>
					<?php
					$options = array('1' => lang('option_yes'), '0' => lang('option_no') );
					if ($override_settings['log_link_clicks'] != '')
					{
						$log_link_clicks_override = '<br /><strong>'.lang('override_label').'</strong>'.$options[$override_settings['log_link_clicks']];
						echo "<strong>",lang('stored_label'),"</strong>",$options[$settings['log_link_clicks']],form_hidden('lv_log_link_clicks', $settings['log_link_clicks']),$log_link_clicks_override;
					}
					else
						echo form_dropdown('lv_log_link_clicks', $options, $settings['log_link_clicks']);			
					?>
				</td>
			</tr>
		
			<tr class="even" >
				<td><?php echo lang('setting_aws_access_key'); ?></td>
				<td>
				<?php 
				$aws_access_key_input = array('id' => 'lv_aws_access_key', 'name' => 'lv_aws_access_key', 'value' => $settings['aws_access_key']);
				if ($override_settings['aws_access_key'])
				{
					$aws_access_key_override = '<br /><strong>'.lang('override_label').'</strong>'.$override_settings['aws_access_key'];
					$aws_access_key_input['readonly'] = 'readonly';
				}
				else
					$aws_access_key_override = '';
				echo form_input($aws_access_key_input).$aws_access_key_override; 
				?>	
				</td>
			</tr>
			<tr class="even" >
				<td><?php echo lang('setting_aws_secret_key'); ?></td>
				<td>
				<?php 
				$aws_secret_key_input = array('id' => 'lv_aws_secret_key', 'name' => 'lv_aws_secret_key', 'value' => $settings['aws_secret_key']);
				if ($override_settings['aws_secret_key'])
				{
					$aws_secret_key_override = '<br /><strong>'.lang('override_label').'</strong>'.$override_settings['aws_secret_key'];
					$aws_secret_key_input['readonly'] = 'readonly';
				}
				else
					$aws_secret_key_override = '';
				echo form_input($aws_secret_key_input).$aws_secret_key_override; 
				?>	
				</td>
			</tr>
		</table>
		<?php 
		echo form_submit(array('class' => 'submit btn mb', 'value' => lang('setting_submit') )); 
		echo form_close(); 
		?>
	</div>
</div>
