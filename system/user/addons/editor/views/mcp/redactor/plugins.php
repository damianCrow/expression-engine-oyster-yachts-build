<div class="redactor-plugins">
    <table cellspacing="0">
        <thead>
            <tr>
                <th><?=lang('ed:plugin')?></th>
                <th><?=lang('ed:desc')?></th>
                <th><?=lang('ed:author')?></th>
                <th class="check-ctrl"></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($allPlugins as $handle => $plugin):?>
            <tr  <?php if (in_array($handle, isset($settings['plugins']) ? $settings['plugins'] : array())) echo 'class="selected"'?>>
                <td><?=$plugin['label']?></td>
                <td><small><?=$plugin['desc']?></small></td>
                <td><?=$plugin['author']?></td>
                <td>
                    <?=form_checkbox($formName.'[plugins][]', $handle, in_array($handle, isset($settings['plugins']) ? $settings['plugins'] : array()) )?>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</div>