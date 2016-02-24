<?php $this->extend('_templates/default-nav'); ?>

<h1><?=lang('sql_query_form_abbr')?> <span class="req-title"><?=lang('required_fields')?></span></h1>
<?=form_open(ee('CP/URL')->make('utilities/query'), 'class="settings ajax-validate"')?>
	<div class="alert inline warn">
		<?=lang('sql_warning')?>
	</div>
	<?php if (isset($invalid_query)): ?>
		<div class="alert inline issue">
			<h3><?=lang('query_form_error')?></h3>
			<p><?=$invalid_query?></p>
		</div>
	<?php endif ?>
	<?=ee('CP/Alert')->getAllInlines()?>
	<fieldset class="col-group">
		<div class="setting-txt col w-16">
			<h3><?=lang('common_queries')?></h3>
			<em><?=lang('common_queries_desc')?><br>
				<a href="<?=ee('CP/URL')->make('utilities/query/run-query', array('thequery' => rawurlencode(base64_encode('SHOW STATUS'))))?>">SHOW STATUS</a>,
				<a href="<?=ee('CP/URL')->make('utilities/query/run-query', array('thequery' => rawurlencode(base64_encode('SHOW VARIABLES'))))?>">SHOW VARIABLES</a>,
				<a href="<?=ee('CP/URL')->make('utilities/query/run-query', array('thequery' => rawurlencode(base64_encode('SHOW PROCESSLIST'))))?>">SHOW PROCESSLIST</a></em>
		</div>
	</fieldset>
	<fieldset class="col-group required <?=form_error_class('thequery')?> last">
		<div class="setting-txt col w-16">
			<h3><?=lang('sql_query_to_run')?></h3>
		</div>
		<div class="setting-field col w-16 last">
			<textarea class="has-format-options" name="thequery" cols="" rows=""><?=set_value('thequery')?></textarea>
			<?=form_error('thequery')?>
		</div>
	</fieldset>

	<fieldset class="form-ctrls">
		<?=cp_form_submit('query_btn', 'query_btn_saving')?>
	</fieldset>
</form>
