<div class="box">

<?=form_open(ee('CP/URL')->make('addons/settings/snaptcha', array('method' => 'save')), 'class="tbl-ctrls settings"')?>
<?=form_hidden('unique_secret', $unique_secret);?>

<h1>Settings</h1> 

<div class="tbl-wrap">

<?php
$this->table->set_template($cp_pad_table_template);
$this->table->set_heading(
    array('data' => lang('preference'), 'style' => 'width: 35%;'),
    lang('setting')
);

foreach ($settings as $key => $val)
{
	$label = '<div class="'.($key == 'license_number' ? 'required' : '').'">';
	$label .= '<div class="setting-txt"><h3>'.lang($key).'</h3></div></div>';
	
	$val = ($key == 'license_number' AND !$valid_license) ? $val.' <strong class="notice">'.lang('invalid_license').'</strong>' : $val;
	
	$val = ($key == 'member_registration_validation') ? $val.'<div style="display: none; margin-top: 15px;">'.lang('member_register_notice_extended').':<br/><textarea rows="2" readonly>{exp:snaptcha:field}</textarea><br/><br/>'.lang('member_register_notice').':<br/><div class="member_registration_html"><textarea style="display: none;" rows="4" readonly>'.$member_registration_html_low.'</textarea><textarea style="display: none;" rows="4" readonly>'.$member_registration_html_medium.'</textarea><textarea style="display: none;" rows="4" readonly>'.$member_registration_html_high.'</textarea></div></div>' : $val;
	
	$val = ($key == 'logging' AND $log_file_not_writable) ? $val.' <span class="notice" style="display: none;">'.lang('log_file_not_writable').'</span>' : $val;
	
    $this->table->add_row($label, $val);
}

echo $this->table->generate();
?>

</div>

<?=form_submit('submit', lang('save_settings'), 'class="btn submit"')?>

<br/><br/>

<?php $this->table->clear()?>
<?=form_close()?>

</div>
