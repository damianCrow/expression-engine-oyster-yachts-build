<?php $this->extend('_templates/default-nav'); ?>

<h1><?php echo isset($cp_heading) ? $cp_heading : $cp_page_title?> <span class="req-title"><?=lang('required_fields')?></span></h1>
<?=form_open(ee('CP/URL')->make('utilities/translate/' . $language . '/save/' . $file), 'class="settings"')?>
<?=ee('CP/Alert')->getAllInlines()?>
<?php foreach ($keys as $key => $value):?>
	<fieldset class="col-group required <?=form_error_class($key)?>">
		<div class="setting-txt col w-8">
			<h3><?=$value['original']?></h3>
			<em></em>
		</div>
		<div class="setting-field col w-8 last">
			<input type="text" name="<?=$key?>" value="<?=set_value($key, $value['trans'])?>">
			<?=form_error($key)?>
		</div>
	</fieldset>

<?php endforeach;?>

	<fieldset class="form-ctrls">
		<?=cp_form_submit('translate_btn', 'btn_saving')?>
	</fieldset>
<?=form_close()?>