<div class="box mb" >
	<h1><?php echo lang('reports_title'); ?></h1>
	<div class="tbl-ctrls" >
		<form id="rpt_form" action="<?php echo $cp_url; ?>" method="get" >	
		<?php if( $ee3 ): ?>
			<input type="hidden" name="/cp/addons/settings/link_vault/reports" value="">
		<?php else: ?>
			<input type="hidden" class="ignore" name="S" value="<?php echo ee()->session->session_id(); ?>" />
			<input type="hidden" class="ignore" name="D" value="cp" />
			<input type="hidden" class="ignore" name="C" value="addons_modules" />
			<input type="hidden" class="ignore" name="M" value="show_module_cp" />
			<input type="hidden" class="ignore" name="module" value="link_vault" />
			<input type="hidden" class="ignore" name="method" value="reports" />
		<?php endif; ?>
		
			<div id="report-criteria" >
		
			<h2><?php echo lang('report_legend'); ?></h2>
			<fieldset class="col-group">
			
				<div class="group" >
				
					<span class="lv_field" >
						<label for="rpt_table" ><?php echo lang('label_table'); ?></label><br />
						<?php echo form_dropdown('table', array(
								'downloads'   => lang('option_downloads'),
								'link_clicks' => lang('option_clicks'),
								'leeches'     => lang('option_leeches')), $default_values['table'], 'id="rpt_table"'); ?>
					</span>
				
					<span class="lv_field" id="rpt_directory_span" >
						<label for="rpt_directory" ><?php echo lang('label_directory'); ?></label><br />
						<?php echo form_dropdown('directory', $distinct_directories, $default_values['directory'], 'id="rpt_directory"'); ?>
					</span>
		
					<span class="lv_field" id="rpt_pretty_url_id_span" >
						<label for="rpt_pretty_url_id" ><?php echo lang('label_pretty_url_id'); ?></label><br />
						<?php echo form_dropdown('pretty_url_id', $distinct_pretty_urls, $default_values['pretty_url_id'], 'id="rpt_pretty_url_id"'); ?>
					</span>
		
					<span class="lv_field" id="rpt_s3_bucket_span" >
						<label for="rpt_s3_bucket" ><?php echo lang('label_s3_bucket'); ?></label><br />
						<?php echo form_dropdown('s3_bucket', $distinct_s3_buckets, $default_values['s3_bucket'], 'id="rpt_s3_bucket"'); ?>
					</span>
			
					<span class="lv_field" >
						<label for="rpt_file_name" ><?php echo lang('label_filename'); ?></label><br />
						<?php echo form_input('file_name', $default_values['file_name'], 'id="rpt_file_name"'); ?>
					</span>
			
					<span class="lv_field" >
						<label for="rpt_member_id" ><?php echo lang('label_member_id'); ?></label><br />
						<?php echo form_input('member_id', $default_values['member_id'], 'id="rpt_member_id"'); ?>
					</span>
			
					<span class="lv_field short" >
						<label for="rpt_start_date" ><?php echo lang('label_start_date'); ?></label><br />
						<?php echo form_input('start_date', $default_values['start_date'], 'id="rpt_start_date" class="rpt_date"'); ?>
					</span>
			
					<span class="lv_field short" >
						<label for="rpt_end_date" ><?php echo lang('label_end_date'); ?></label><br />
						<?php echo form_input('end_date', $default_values['end_date'], 'id="rpt_end_date" class="rpt_date"'); ?>
					</span>
			
					<?php foreach($custom_fields as $field): ?>
			
						<span class="lv_field" >
							<label for="rpt_<?php echo $field['field_name']; ?>" ><?php echo $field['field_label']; ?></label><br />
							<?php echo form_input('cf_'.$field['field_name'], $default_values[ $field['field_name'] ], 'id="rpt_'.$field['field_name'].'"'); ?>
						</span>
			
					<?php endforeach; ?>
				
				</div> <!-- .group -->
			</fieldset>
			
			<h2><?php echo lang('report_options_legend'); ?></h2>

			<fieldset class="col-group">
			
				<span class="lv_field" >
					<label for="rpt_order_by" ><?php echo lang('label_order_by'); ?></label>
					<?php echo form_dropdown('order_by', $order_by_options, $order_by, 'id="rpt_order_by"'); ?>
				</span>
			
				<span class="lv_field" >
					<label for="rpt_sort" ><?php echo lang('label_sort'); ?></label>
					<?php echo form_dropdown('sort', array(
						'asc'	=> lang('option_asc'),
						'desc'	=> lang('option_desc')
					), $sort, 'id="rpt_sort"'); ?>
				</span>
			
				<span class="lv_field" >
					<label for="rpt_limit" ><?php echo lang('label_limit'); ?></label>
					<?php echo form_dropdown('limit', $limit_options, $limit, 'id="rpt_limit"'); ?>
				</span>
				
			</fieldset>
			
			</div> <!-- #report-criteria -->
		
			<span class="lv_field" style="width:330px">
				<br />
				<button id="rpt_submit" name="submit" type="submit" value="run_report" class="submit btn lv" ><?php echo lang('run_report_button'); ?></button>
				<button id="rpt_export" name="submit" type="submit" value="run_export" class="submit btn lv" >Export XLS</button>
				<span id="rpt_loader" >Loading...</span>
			</span>
		
		<?php echo form_close(); ?>
		
		<?php echo form_open($save_report_url, array('id' => 'rpt_save_form') ); ?>
			<span class="lv_field_right">
				<br />
				<span id="rpt_save_success" style="color:#9CBA7F" >Success!</span>
				<span id="rpt_save_error" style="color:#b00b00" >Error</span>
				<label for="rpt_title" ><?php echo lang('label_title'); ?></label>
				<input type="text" id="rpt_title" name="rpt_title" />
				<button id="rpt_save" name="save" value="save" class="submit btn" type="submit" ><?php echo lang('save_report_button'); ?></button>	
			</span>
		<?php echo form_close(); ?>
		
		<br class="clear_both" /><p></p>
		
		<h3><?php echo lang('report_results_title')." ($total_rows)"; ?>&nbsp;<span id="rpt_results_count" ></span></h3>
		
		<div class="<?php echo $ee3 ? 'paginate' : 'paginate-backwardscompat'; ?>" >
			<ul>
			<?php echo $pagination_links; ?>
			</ul>
		</div>
		
		<table class="mainTable reportsTable" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th><?php echo lang('report_col_id'); ?></th>
					<th><?php echo lang('report_col_time'); ?></th>
					<th><?php echo lang('report_col_member_id'); ?></th>
					<th><?php echo lang('report_col_remote_ip'); ?></th>
					<th><?php echo lang('report_col_dir'); ?></th>
					<th><?php echo lang('report_col_file'); ?></th>
					<?php foreach ($custom_fields as $field): ?>
						<th><?php echo $field['field_label']; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
		
			<tbody id="report_table_body">
		
				<?php echo $report_content; ?>
		
			</tbody>
		</table>
		
		<div class="<?php echo $ee3 ? 'paginate' : 'paginate-backwardscompat'; ?>" >
			<ul>
			<?php echo $pagination_links; ?>
			</ul>
		</div>
	</div> <!-- .tbl-ctrls -->
</div> <!-- .box.mb -->
