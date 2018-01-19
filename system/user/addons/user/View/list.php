<?php $this->extend('_layouts/table_form_wrapper'); ?>
<form><!-- This fake form is here only to allow the confirm modal's JS to send the checked items through the post once confirm is clicked. -->
<?=$this->embed('ee:_shared/table', $table)?>
<fieldset class="tbl-bulk-act hidden">
	<select name="bulk_action">
		<option value="">-- with selected --</option>
		<option value="remove" data-confirm-trigger="selected" rel="modal-confirm-remove">Remove</option>
	</select>
	<button class="btn submit" data-conditional-modal="confirm-trigger">Submit</button>
</fieldset>
</form>