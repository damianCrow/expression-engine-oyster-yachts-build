<?php if ($newer_version_exists): ?>
<p style="font-weight: bold; margin-bottom: 12px;">* <?=lang('update_available')?></p>
<?php endif; ?>

<div class="tbl-wrap">

<?php
$custom_table_template = $cp_table_template;
$custom_table_template['table_open'] = '<table id="links" class="mainTable" border="0" cellpadding="4" cellspacing="0">';

$this->table->set_template($custom_table_template);
$this->table->set_heading('Link', 'Details');

if ($sitemap_url)
{
	$this->table->add_row('<a href="'.$sitemap_url.'" target="_blank">View Rendered Sitemap</a>', 'See what web crawlers see when they visit your sitemap');
}
else
{
	$this->table->add_row('<strike><a href="#">View Rendered Sitemap</a></strike>', 'You need to create a template for your sitemap first', '');
}

//$this->table->add_row('<a id="show_links" href="#">show more links...</a>', '');
$this->table->add_row('<a href="http://www.google.com/webmasters/sitemaps" target="_blank">Submit Sitemap to Google manually</a>', 'You must do this once manualy for the sitemap pings to be effective');
$this->table->add_row('<a href="http://www.google.com/search?q=site%3A'.$site_index.'" target="_blank">View Google\'s index of your website</a>', 'See what pages on your site have been indexed by Google');
//$this->table->add_row('<a href="http://www.sitemaps.org/" target="_blank">Sitemap.org</a>', 'Visit the official sitemap website');
//$this->table->add_row('<a href="http://www.putyourlightson.net/sitemap-module" target="_blank">Documentation</a>', 'Installation and general information on the Sitemap Module');
//$this->table->add_row('<a id="hide_links" href="#">show less links...</a>', '');
?>

<?=$this->table->generate()?>
<?php $this->table->clear(); ?>

</div>

<br/>

<?php /*
<div style="margin-bottom: 20px; font-weight: bold;">
	<a id="show_excluded" style="display: none;" href="#">&raquo; Show Excluded Locations and Channels</a>
	<a id="hide_excluded" href="#">&laquo; Hide Excluded Locations and Channels</a>
</div>
*/ ?>

<div class="box">

<?=form_open(ee('CP/URL')->make('addons/settings/sitemap', array('method' => 'update_urls')), 'class="tbl-ctrls settings"')?>

<fieldset class="tbl-search right">
	<a class="btn tn action" href="<?=ee('CP/URL')->make('addons/settings/sitemap', array('method' => 'new_url'))?>">Insert New Location</a>
</fieldset>
		
<h1>Locations</h1>

<div class="tbl-wrap">

<?php
$this->table->set_template($cp_table_template);
$this->table->set_heading('Location', 'URL', 'Change Frequency', 'Priority');
$i = 0;
?>

<?php foreach ($locations as $val): ?>
	<?php $this->table->add_row(
		'<b><a href="'.ee('CP/URL')->make('addons/settings/sitemap', array('method' => 'delete_url', 'id' => $val->id)).'" onclick="return confirm(\''.lang('confirm_delete').'\');">'.lang('delete').'</a></b> <input type="hidden" name="id_'.$i.'" value="'.$val->id.'" />',
		'<input type="text" name="url_'.$i.'" value="'.$val->url.'" style="width: 95%;" />',
		'<select name="change_frequency_'.$i.'">
			<option value="always" '.(($val->change_frequency == 'always') ? 'selected' : '').'>Always</option>
			<option value="hourly" '.(($val->change_frequency == 'hourly') ? 'selected' : '').'>Hourly</option>
			<option value="daily" '.(($val->change_frequency == 'daily') ? 'selected' : '').'>Daily</option>
			<option value="weekly" '.(($val->change_frequency == 'weekly' || $val->change_frequency == '') ? 'selected' : '').'>Weekly</option>
			<option value="monthly" '.(($val->change_frequency == 'monthly') ? 'selected' : '').'>Monthly</option>
			<option value="yearly" '.(($val->change_frequency == 'yearly') ? 'selected' : '').'>Yearly</option>
			<option value="never" '.(($val->change_frequency == 'never') ? 'selected' : '').'>Never</option>
		</select>',
		'<select name="priority_'.$i.'">
			<option value="0.1" '.(($val->priority == '0.1') ? 'selected' : '').'>0.1</option>
			<option value="0.2" '.(($val->priority == '0.2') ? 'selected' : '').'>0.2</option>
			<option value="0.3" '.(($val->priority == '0.3') ? 'selected' : '').'>0.3</option>
			<option value="0.4" '.(($val->priority == '0.4') ? 'selected' : '').'>0.4</option>
			<option value="0.5" '.(($val->priority == '0.5' || $val->priority == '') ? 'selected' : '').'>0.5</option>
			<option value="0.6" '.(($val->priority == '0.6') ? 'selected' : '').'>0.6</option>
			<option value="0.7" '.(($val->priority == '0.7') ? 'selected' : '').'>0.7</option>
			<option value="0.8" '.(($val->priority == '0.8') ? 'selected' : '').'>0.8</option>
			<option value="0.9" '.(($val->priority == '0.9') ? 'selected' : '').'>0.9</option>
			<option value="1.0" '.(($val->priority == '1.0') ? 'selected' : '').'>1.0</option>
		</select>'
	); ?>
	<?php $i++; ?>
<?php endforeach; ?>

<?=$this->table->generate()?>
<?php $this->table->clear(); ?>

</div>

<input class="btn submit" type="submit" value="<?=lang('update_urls')?>" />

<br/><br/>

</form>

</div>

<br/><br/>

<div class="box">

<?=form_open(ee('CP/URL')->make('addons/settings/sitemap', array('method' => 'update_channels')), 'class="tbl-ctrls settings"')?>

<h1>Channels</h1>

<div class="tbl-wrap">

<?php
$this->table->set_template($cp_table_template);
$this->table->set_heading('Channel', 'URL', 'Included', 'Statuses', 'Change Frequency', 'Priority');
$i = 0;
?>

<?php
foreach ($channels as $val)
{
	$status_options = '';

	foreach ($statuses as $status)
	{
		if ($status->group_id == $val->status_group)
		{
			$selected = in_array($status->status, explode(',', $val->statuses)) ? 'selected' : '';
			$selected = ($selected OR ($val->statuses == '' AND $status->status == 'open')) ? 'selected' : '';

			$status_options .= '<option value="'.$status->status.'" '.$selected.'>'.$status->status.'</option>';
		}
	}

	$status_select = $status_options ? '<select name="statuses_'.$i.'[]" multiple="multiple">'.$status_options.'</select>' : 'None';


	$this->table->add_row(
		'<b>'.$val->channel_title.'</b> <input type="hidden" name="id_'.$i.'" value="'.$val->id.'" /> <input type="hidden" name="channel_id_'.$i.'" value="'.$val->channel_id.'" />',
		'<input type="text" name="url_'.$i.'" value="'.($val->url ? htmlspecialchars($val->url) : $site_index.'{url_title}').'" style="width: 95%;" />',
		'<select name="included_'.$i.'" class="status select">
			<option value="0" '.(($val->included == '0' || $val->included == '') ? 'selected' : '').'>Excluded</option>
			<option value="1" '.(($val->included == '1') ? 'selected' : '').'>Included</option>
		</select>',
		$status_select,
		'<select name="change_frequency_'.$i.'">
			<option value="always" '.(($val->change_frequency == 'always') ? 'selected' : '').'>Always</option>
			<option value="hourly" '.(($val->change_frequency == 'hourly') ? 'selected' : '').'>Hourly</option>
			<option value="daily" '.(($val->change_frequency == 'daily') ? 'selected' : '').'>Daily</option>
			<option value="weekly" '.(($val->change_frequency == 'weekly' || $val->change_frequency == '') ? 'selected' : '').'>Weekly</option>
			<option value="monthly" '.(($val->change_frequency == 'monthly') ? 'selected' : '').'>Monthly</option>
			<option value="yearly" '.(($val->change_frequency == 'yearly') ? 'selected' : '').'>Yearly</option>
			<option value="never" '.(($val->change_frequency == 'never') ? 'selected' : '').'>Never</option>
		</select>',
		'<select name="priority_'.$i.'">
			<option value="0.1" '.(($val->priority == '0.1') ? 'selected' : '').'>0.1</option>
			<option value="0.2" '.(($val->priority == '0.2') ? 'selected' : '').'>0.2</option>
			<option value="0.3" '.(($val->priority == '0.3') ? 'selected' : '').'>0.3</option>
			<option value="0.4" '.(($val->priority == '0.4') ? 'selected' : '').'>0.4</option>
			<option value="0.5" '.(($val->priority == '0.5' || $val->priority == '') ? 'selected' : '').'>0.5</option>
			<option value="0.6" '.(($val->priority == '0.6') ? 'selected' : '').'>0.6</option>
			<option value="0.7" '.(($val->priority == '0.7') ? 'selected' : '').'>0.7</option>
			<option value="0.8" '.(($val->priority == '0.8') ? 'selected' : '').'>0.8</option>
			<option value="0.9" '.(($val->priority == '0.9') ? 'selected' : '').'>0.9</option>
			<option value="1.0" '.(($val->priority == '1.0') ? 'selected' : '').'>1.0</option>
		</select>'
	);

	$i++;
}
?>

<?=$this->table->generate()?>
<?php $this->table->clear(); ?>

</div>

Allowed Tags: {url_title}, {page_uri}, {page_url}, {entry_id}, {channel_id}, {cat_id}, {cat_name}, {cat_url_title}, {entry_date}
<br/><br/>

<input class="btn submit" type="submit" value="<?=lang('update_channels')?>" />

<br/><br/>

</form>

</div>


<script type="text/javascript">
function showLinks() {
	$('#links tbody').children().each(function(i) {
		if (i == 1) {
			$(this).hide();
		}
		else {
			$(this).show();
		}
	});
	return false;
}

function hideLinks() {
	$('#links tbody').children().each(function(i) {
		if (i > 1) {
			$(this).hide();
		}
		else {
			$(this).show();
		}
	});
	return false;
}

function showExcluded() {
	$('#show_excluded').hide();
	$('#hide_excluded').show();

	$('select.status').each(function() {
		$(this).parents('tr').show();
	});

	return false;
}

function hideExcluded() {
	$('#show_excluded').show();
	$('#hide_excluded').hide();

	$('select.status').each(function() {
		if ($(this).val() == 0) {
			$(this).parents('tr').hide();
		}
	});

	return false;
}

hideLinks();

$('#show_links').click(showLinks);
$('#hide_links').click(hideLinks);
$('#show_excluded').click(showExcluded);
$('#hide_excluded').click(hideExcluded);
</script>
