<div class="box">

	<div class="low-reorder tbl-ctrls">

		<?php if ($perm['can_edit']): ?>
			<fieldset class="tbl-search right">
				<a class="btn tn action" href="<?=$edit_url?>"><?=lang('edit_set')?></a>
			</fieldset>
		<?php endif; ?>

		<h1><?=htmlspecialchars($set['set_label'])?></h1>

		<?=ee('CP/Alert')->getAllInlines()?>

		<?php if ( ! empty($set['set_notes'])): ?>
			<div class="low-reorder-notes"><?=$set['set_notes']?></div>
		<?php endif; ?>

		<?=form_open($action, NULL, $hidden)?>

			<?php if ($select_category): ?>
				<fieldset class="low-reorder-select-cat">
					<input type="hidden" name="url" value="<?=$url?>" />
					<select name="category">
						<?php if ( ! $show_entries): ?><option value=""><?=lang('select_category')?>&hellip;</option><?php endif; ?>
						<?php foreach ($groups as $group): ?>
							<?php if (count($groups) > 1): ?><optgroup label="<?=htmlspecialchars($group['name'])?>"><?php endif; ?>
							<?php foreach ($group['categories'] as $cat): ?>
								<option value="<?=$cat['id']?>"<?php if ($cat['id'] == $selected_category): ?> selected="selected"<?php endif; ?>>
									<?=str_repeat('&nbsp;&nbsp;', $cat['depth']).htmlspecialchars($cat['name'])?>
								</option>
							<?php endforeach; ?>
							<?php if (count($groups) > 1): ?></optgroup><?php endif; ?>
						<?php endforeach; ?>
					</select>
				</fieldset>
			<?php endif; ?>

			<?php if ($show_entries && empty($entries)): ?>
				<p class="no-results"><?=lang('no_entries_found')?></p>
			<?php elseif ($show_entries && ! empty($entries)): ?>
				<ul class="low-reorder-entries">
					<?php for($i = 0, $total = count($entries); $i < $total; $i++): $row = $entries[$i]; ?>
						<li id="entry-<?=$row['entry_id']?>">
							<input type="hidden" name="entries[]" value="<?=$row['entry_id']?>" />
							<div class="order"><?=($i + 1)?></div>
							<div class="title"><?=$row['title']?></div>
							<?php if ( ! empty($row['hidden'])): ?>
								<?php foreach ($row['hidden'] AS $j => $hidden): ?>
									<div class="hidden" id="hidden-<?=$j?>"><?=$hidden?></div>
								<?php endforeach; ?>
							<?php endif; ?>
						</li>
					<?php endfor; ?>
				</ul>
				<fieldset class="form-ctrls">
					<button class="btn" name="clear_cache" type="submit" value="" data-work-text="<?=lang('saving')?>"><?=lang('save_order')?></button>
					<button class="btn" name="clear_cache" type="submit" value="y" data-work-text="<?=lang('saving')?>"><?=lang('save_and_clear_cache')?></button>
				</fieldset>
			<?php endif; ?>

		</form>

	</div>
</div>