<div class="redactor-toolbar-config">
    <div class="redactor-box" style="height:65px">
        <ul class="redactor-toolbar" style="position: relative;">
            <?php foreach ($allButtons as $btnHandle => $btn):?>
            <li class="wbtn <?php if (isset($btn['plugin']) && $btn['plugin']):?>wbtn_plugin plugin-<?=$btn['plugin']?><?php endif;?>">
                <a href="#" class="re-button re-<?=$btnHandle?>" title="<?=$btnHandle?>"><?=$btn['label']?></a>
                <?=form_checkbox($formName.'[buttons][]', $btnHandle, in_array($btnHandle, $settings['buttons']))?>
            </li>
            <?php endforeach;?>
        </ul>
    </div>
</div>