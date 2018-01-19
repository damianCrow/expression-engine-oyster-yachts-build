<div id="cimcp">
<div class="box">


<h1><?=lang('ci:batch_actions')?></h1>
<form class="settings">
    <div class="image-filters">
        <fieldset class="col-group">
            <div class="setting-txt col w-6">
                <h3>Limit By: Channels</h3>
            </div>
            <div class="setting-field col w-10 last">
                <?=form_multiselect('channels[]', $channels, '', ' class="select2" placeholder="Channels"  style="width:100%;"')?>
            </div>
        </fieldset>
        <fieldset class="col-group">
            <div class="setting-txt col w-6">
                <h3>Limit By: Fields</h3>
            </div>
            <div class="setting-field col w-10 last">
                <?=form_multiselect('fields[]', $fields, '', ' class="select2" placeholder="Fields"  style="width:100%;"')?>
            </div>
        </fieldset>
        <fieldset class="col-group">
            <div class="setting-txt col w-6">
                <h3>Override Start Position</h3>
            </div>
            <div class="setting-field col w-10 last">
                <?=form_input('offset', '')?>
            </div>
        </fieldset>
        <fieldset class="col-group">
            <div class="setting-txt col w-6">
                <h3>Limit By: Entry IDs (comma sep.)</h3>
            </div>
            <div class="setting-field col w-10 last">
                <?=form_input('entry_id', '')?>
            </div>
        </fieldset>
    </div>
    <fieldset class="col-group">
        <div class="setting-txt col w-6">
            <h3>Action</h3>
        </div>
        <div class="setting-field col w-10 last">
            <span class="action-toggler">
                <label class="choice mr chosen">
                    <input type="radio" name="action" value="regen" checked> Regenerate Images &nbsp;&nbsp;
                </label>
                <label class="choice mr">
                    <input type="radio" name="action" value="transfer"> Transfer Images
                </label>
            </span>
        </div>
    </fieldset>
    <fieldset class="col-group">
        <div class="setting-txt col w-6">
            <h3>Total Images</h3>
        </div>
        <div class="setting-field col w-10 last">
            <strong class="total_count">0</strong>
        </div>
    </fieldset>

    <div class="actions action-regen">
        <h2>Regenerate Images</h2>
        <table>
            <thead>
                <tr>
                    <th>Group</th>
                    <th>Field</th>
                    <th>Sizes</th>
                </tr>
            </thead>
            <tbody class="image_sizes">

            </tbody>
        </table>
    </div>

    <div class="actions action-transfer">
        <h2>Transfer Images</h2>
        <table>
            <tbody>
                <tr>
                    <td style="width:200px"><label><?=lang('ci:transfer_to')?></label></td>
                    <td>
                        <span class="transfer-toggle">
                            <label class="choice mr chosen">
                                <input type="radio" name="transfer[to]" value="s3" checked> Amazon S3
                            </label>
                            <label class="choice mr">
                                <input type="radio" name="transfer[to]" value="cloudfiles"> Rackspace CloudFiles
                            </label>
                        </span>
                    </td>
                </tr>
            </tbody>
            <tbody class="options option-s3">
                <tr>
                    <td><label>AWS KEY</label></td>
                    <td><input type="text" value="" name="s3[key]"></td>
                </tr>
                <tr>
                    <td><label>AWS SECRET KEY</label></td>
                    <td><input type="text" value="" name="s3[secret_key]"></td>
                </tr>
                <tr>
                    <td><label>Bucket</label></td>
                    <td><input type="text" value="" name="s3[bucket]"></td>
                </tr>
                <tr>
                    <td><label>ACL</label></td>
                    <td>
                        <select name="s3[acl]">
                            <option value="private">Owner-only read</option>
                            <option selected="selected" value="public-read">Public READ</option>
                            <option value="authenticated-read">Public Authenticated Read</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label>Storage Redundancy</label></td>
                    <td>
                        <select name="s3[storage]">
                            <option selected="selected" value="standard">Standard storage redundancy</option>
                            <option value="reduced">Reduced storage redundancy</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label>Subdirectory (optional)</label></td>
                    <td><input type="text" value="" name="s3[directory]"></td>
                </tr>
            </tbody>

            <tbody class="options option-cloudfiles">
                <tr>
                    <td><label>Username</label></td>
                    <td><input type="text" value="" name="cloudfiles[username]"></td>
                </tr>
                <tr>
                    <td><label>API Key</label></td>
                    <td><input type="text" value="" name="cloudfiles[api]"></td>
                </tr>
                <tr>
                    <td><label>Container</label></td>
                    <td><input type="text" value="" name="cloudfiles[container]"></td>
                </tr>
                <tr>
                    <td><label>Region</label></td>
                    <td>
                        <select name="cloudfiles[region]">
                            <option selected="selected" value="us">United States</option>
                            <option value="uk">United Kingdom (London)</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <h2 style="margin-bottom:0">
        <?=lang('ci:progress')?>
        <em style="float:right"><i><?=lang('ci:start_tip')?></i></em>
    </h2>
    <div class="progress_holder">
        <table>
        <tbody class="current-actions">

        </tbody>
        </table>
        <div class="total-progress" colspan="20">
            <div class="progress">
                <div class="progress-text">
                    <span class="total_done">0</span> of <span class="total_count"></span>
                </div>
            </div>
        </div>
    </div>

    <fieldset class="form-ctrls" style="margin-top:0">
        <input class="btn start_actions" type="button" value="<?=lang('ci:start')?>" />
    </fieldset>
</form>


</div>
</div>

<div class="box hidden" id="ci_ajax_error">
    <h1>AJAX Error</h1>
    <iframe src="about:blank"></iframe>
</div>