<div class="editor-settings">
    <div class="settings-holder"></div>
    <?=form_dropdown('', $avdSettingsArr, '')?>
    <script type="text/x-editor_config"><?=$optionsJson?></script>
    <input name="<?=$formName?>[dummy]" type="hidden" class="dummy" data-name="<?=$formName?>[config]">
</div>