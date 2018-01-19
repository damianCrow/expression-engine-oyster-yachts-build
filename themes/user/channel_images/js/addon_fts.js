;(function(global, $){
    //es5 strict mode
    "use strict";

    var ChannelImages = global.ChannelImages = global.ChannelImages || {};
    ChannelImages.Templates = {};

    $('.ci-test_location').on('click', testLocation);

    ChannelImages.init = function(){

        // Parse Hogan Templates
        ChannelImages.Templates['ActionGroups'] = Hogan.compile(jQuery('#ChannelImagesActionGroup').html());

        for (var group in ChannelImages.FTS){
            if (ChannelImages.FTS[group].group_name == ChannelImages.previews.small) ChannelImages.FTS[group].small_preview = 'yes';
            if (ChannelImages.FTS[group].group_name == ChannelImages.previews.big) ChannelImages.FTS[group].big_preview = 'yes';
            jQuery('#CIActions .AddActionGroup').before(ChannelImages.Templates['ActionGroups'].render(ChannelImages.FTS[group]));
        }

        // Weird EE3 bug with disabling fields
        $('select[name="field_type"]').on('change', function() {
            $('.CIActions :input').attr('disabled', false);
        });

        ChannelImages.CIField = jQuery('.ChannelImagesField');
        syncOrderNumbers();
        activateJeditable();
        activateSortable();

        ChannelImages.CIField.on('change', '.ActionGroup tfoot select', addNewAction);
        ChannelImages.CIField.on('click', '.ActionGroup .DelAction', function(){
            jQuery(this).closest('tr').fadeOut('slow', function(){
                jQuery(this).remove();
                setTimeout(function(){
                    syncOrderNumbers();
                }, 100);
            });

            return false;
        });

        ChannelImages.CIField.find('.AddActionGroup').click(addActionGroup);
        ChannelImages.CIField.on('click', '.DelActionGroup', function(Event){
            jQuery(Event.target).closest('.ActionGroup').fadeOut('slow', function(){ jQuery(this).remove();  syncOrderNumbers(); });
            return false;
        });

        ChannelImages.CIField.delegate('.SettingsToggler', 'click', function(Event){
            var Target = $(Event.target);
            var Rel = Target.text();
            var HTML = Target.attr('rel');

            if (Target.hasClass('sHidden')){
                Target.removeClass('sHidden');
                Target.parent().find('.actionsettings').show();
            }
            else {
                Target.addClass('sHidden');
                Target.parent().find('.actionsettings').hide();
            }

            Target.attr('rel', Rel);
            Target.text(HTML);

            return false;
        });
    };

    // ----------------------------------------------------------------------

    function testLocation(e) {
        e.preventDefault();
        var uploadlocation = $('input[name="channel_images\[upload_location\]"]:checked').val();

        // Post Parameters
        var params = $(e.target).closest('fieldset').prevUntil('h2').find(':input').serializeArray();
        params.push({name:'ajax_method', value:'testLocation'});
        params.push({name: 'channel_images[upload_location]', value: uploadlocation});

        // Modal
        var modal = $('.modal-ci_test_location');
        modal.trigger('modal:open');

        // Open the spinner
        var spinner = new Spinner({lines:13, length:18, width:12, radius:32}).spin();
        modal.find('.ajax_results').html(spinner.el);

        $.ajax({url: ChannelImages.AJAX_URL, method: 'post', dataType: 'html', data: params,
            success: function(rdata){
                modal.find('.ajax_results').html(rdata);
            }
        });
    }


    // ----------------------------------------------------------------------

    function syncOrderNumbers(){
        ChannelImages.CIField.find('.ActionGroup').each(function(index, ActionGroup){

            jQuery(ActionGroup).find('> tbody > tr').each(function(trindex, TR){
                jQuery(TR).find('td:first').html(trindex+1);
                jQuery(TR).find('.action_step').attr('value', trindex+1);
            });

            jQuery(ActionGroup).find('.small_preview, .big_preview').attr('value', index+1);

            jQuery(ActionGroup).find('input, textarea, select').each(function(elemindex, InputElem){
                if (typeof(jQuery(InputElem).attr('name')) == 'undefined') return;
                var attr = jQuery(InputElem).attr('name').replace(/\[action_groups\]\[.*?\]/, '[action_groups][' + (index+1) + ']');
                jQuery(InputElem).attr('name', attr);
            });

        });
    };

    //********************************************************************************* //

    function activateJeditable(){
        ChannelImages.CIField.find('.ActionGroup .group_name h4').editable(function(value, settings){
            jQuery(this).closest('.group_name').find('.gname').attr('value', value);
            return value;
        },{
            type: 'text',
            onblur: 'submit',
            event: 'click',
            onedit: function(settings, elem){ jQuery(elem).closest('.group_name').find('small').hide(); },
            onsubmit: function(settings, elem){ jQuery(elem).closest('.group_name').find('small').show(); }
        });
    };

    //********************************************************************************* //

    function activateSortable(){
        ChannelImages.CIField.find('.CIActions .ActionGroup').each(function(index, Group){

            jQuery(Group).find('tbody').sortable({
                handle: '.MoveAction',
                axis: 'y',
                containment: Group,
                helper: function(e, ui) {
                    ui.children().each(function() {
                        $(this).width($(this).width());
                    });

                    return ui;
                },
                stop: function(Event, UI){
                    syncOrderNumbers();
                }
            });

        });
    };

    //********************************************************************************* //

    function addNewAction(Event){
        var Type = $(this).val();
        if (Type == false) return;

        jQuery(this).closest('table').find('.NoActions').remove();

        var Content = jQuery.base64Decode( jQuery(this).closest('.CIActions').find('.default_actions .'+Type).text() ); // Decode Text
        jQuery(this).closest('table').find('tbody:first').append(Content);

        setTimeout(function(){
            syncOrderNumbers();
        }, 100);

        return;
    };

    //********************************************************************************* //

    function addActionGroup(Event){

        var JSONOBJ = {};
        JSONOBJ.group_name = 'Untitled';

        var Cloned = ChannelImages.Templates['ActionGroups'].render(JSONOBJ);

        ChannelImages.CIField.find('.AddActionGroup').before(Cloned);
        activateJeditable();

        return false;
    };

    //********************************************************************************* //

}(window, jQuery));

