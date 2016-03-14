<div id="low-category-options">

	<select name="cat_option">
	<?php foreach ($cat_options as $val): ?>
		<option value="<?=$val?>"<?=($cat_option == $val ? ' selected' : '')?>>
			<?=lang('show_'.$val)?>
		</option>
	<?php endforeach; ?>
	</select>

	<!-- List of categories -->
	<div class="low-category-option" id="low-category-some"<?=($cat_option != 'some' ? ' style="display:none"' : '')?>>
		<!-- <h4><?=lang('select_categories')?></h4> -->
		<select name="parameters[category][]" multiple="multiple" size="10" class="low-select-multiple">
		<?php foreach ($groups as $group): ?>
			<optgroup label="<?=htmlspecialchars($group['name'])?>">
			<?php foreach ($group['categories'] as $cat): ?>
				<option value="<?=$cat['id']?>"<?=(in_array($cat['id'], $active_cats) ? ' selected' : '')?>>
					<?=(str_repeat('&nbsp;&nbsp;', $cat['depth']) . htmlspecialchars($cat['name']))?>
				</option>
			<?php endforeach; ?>
			</optgroup>
		<?php endforeach; ?>
		</select>
	</div>

	<!-- List of category groups -->
	<div class="low-category-option" id="low-category-one"<?=($cat_option != 'one' ? ' style="display:none"' : '')?>>
		<!-- <h4><?=lang('select_category_groups')?></h4> -->
		<select name="cat_groups[]" multiple="multiple" size="10" class="low-select-multiple">
		<?php foreach ($groups as $group): ?>
			<option value="<?=$group['id']?>"<?=(in_array($group['id'], $active_groups) ? ' selected' : '')?>>
				<?=htmlspecialchars($group['name'])?>
			</option>
		<?php endforeach; ?>
		</select>
	</div>

</div>