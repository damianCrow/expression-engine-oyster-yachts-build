<script type="text/javascript">
jQuery(function($)
{
	var field_name	= '<?=$field_name?>';
	var $fieldset	= $('fieldset').find('[data-field="' + field_name + '"]').closest('fieldset');
	$fieldset.find('div:eq(0), div:eq(1)').removeClass('w-8').addClass('w-16');
});
</script>