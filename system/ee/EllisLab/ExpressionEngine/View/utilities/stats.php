<?php $this->extend('_templates/default-nav'); ?>

<div class="tbl-ctrls">
	<?=form_open(ee('CP/URL')->make('utilities/stats/sync'))?>
		<h1><?php echo isset($cp_heading) ? $cp_heading : $cp_page_title?></h1>
		<?=ee('CP/Alert')->getAllInlines()?>
		<?php $this->embed('ee:_shared/table', $table); ?>

		<fieldset class="tbl-bulk-act hidden">
			<select name="bulk_action">
				<option value="">-- <?=lang('with_selected')?> --</option>
				<option value="sync"><?=lang('sync')?></option>
			</select>
			<input class="btn submit" type="submit" value="<?=lang('submit')?>">
		</fieldset>
	<?=form_close()?>
</div>
