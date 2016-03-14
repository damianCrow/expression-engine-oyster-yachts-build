<div class="low-reorder-search-fields" data-params="<?=htmlspecialchars($params, ENT_QUOTES)?>">
	<div>
		<select name="search[fields][]">
			<option value="">--</option>
			<?php foreach ($choices as $group => $fields): ?>
				<optgroup label="<?=htmlspecialchars($group)?>">
					<?php foreach ($fields as $key => $val): ?>
						<option value="<?=$key?>"><?=htmlspecialchars($val)?></option>
					<?php endforeach; ?>
				</optgroup>
			<?php endforeach; ?>
		</select> = <input type="text" name="search[values][]">
		<button type="button" class="remove"><?=lang('remove')?></button>
	</div>
	<button type="button" class="add"><?=lang('add_search_filter')?></button>
</div>