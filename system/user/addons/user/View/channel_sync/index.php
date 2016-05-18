<div class="box">
	<?php $this->embed('ee:_shared/form')?>
</div>

<script type="text/javascript">
	(function(global){
		var User = global.User = global.User || {};
		/* jshint ignore:start */
		User.incomingPrefs		= <?=$sync_prefs?>;
		User.incomingChannel	= '<?=$current_channel?>';
		User.memberFields		= <?=$member_fields_json?>;
		User.channelAjaxUrl		= '<?=addslashes($channel_ajax_url)?>';
		/* jshint ignore:end */
	}(window));
</script>

<script type="text/html" id="new-row-template">
	<{
		memberField = (typeof memberField !== 'undefined') ? memberField : 0;
		channelField = (typeof channelField !== 'undefined') ? channelField : 0;
	}>
	<tr class="<{= (num % 2 ? 'even' : 'odd') }>">
		<td>
			<select name="member_field[<{= num }>]">
				<optgroup label="<?=lang('default_member_fields')?>">
			<?php foreach ($default_member_fields as $id => $label):?>
					<option value="<?=$id?>"
					<{ if (memberField == '<?=$id?>') { }>selected="selected"<{ } }>
					><?=$label?></option>
			<?php endforeach;?>
				</optgroup>
				<optgroup label="<?=lang('custom_member_fields')?>">
			<?php foreach ($custom_member_fields as $id => $label):?>
					<option value="<?=$id?>"
					<{ if (memberField == '<?=$id?>') { }>selected="selected"<{ } }>
					><?=$label?></option>
			<?php endforeach;?>
				</optgroup>
			</select>
		</td>
		<td>
			<select name="channel_field[<{= num }>]">
		<{ $.each(channelFields, function(id, data){ }>
				<option value="<{= data['field_id'] }>"
			<{ if (channelField == id) { }>selected="selected"<{ } }>
				><{= data['field_label'] }></option>
		<{ }); }>
			</select>
		</td>
		<td>
			<ul class="toolbar">
				<li class="remove mapping-delete"><a href="#" title="remove row"></a></li>
			</ul>
		</td>
	</tr>
</script>