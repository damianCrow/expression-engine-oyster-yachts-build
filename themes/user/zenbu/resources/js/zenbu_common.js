/**
 * Update top nav with updated sectionId
 * @param  {int} sectionId The sectionID
 * @return {null}           Link URLs should have changed
 */
function updateRightNav(sectionId)
{
    // Add channel_id to Display Settings URL,
    // if present
    $('div.rightNav').find('a').each(function(){

        if($.contains($(this).attr('href'),'method=settings'))
        {
            $(this).attr('href', $(this).attr('href').replace(/channel_id=([0-9]*)/, 'channel_id=' + sectionId));
        }

    });
}

/**
 * Updates fields by applying Chosen.js
 */
function updateChosen()
{
    $("div.zenbu-filters").find("select").chosen({search_contains: true});
}

/**
 * Start sortable table
 */
function initSortableTable()
{
    /**
    *   ==============================
    *   Sortable
    *   ==============================
    */

    /**
    *  Make table rows sortable!
    *  Return a helper with preserved width of cells
    */
    var fixHelper = function(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    };

    $("table.sortable-table tbody").sortable({
        cancel: 'table.sortable-table tr th, .not-sortable',
        start: function(){
            $(this).children('tr:empty').html('<td colspan="6">&nbsp;</td>');
            $(this).children('tr.ui-sortable-helper').addClass('hover');
        },
        stop: function(event, ui) {
            if($.isFunction(window.resetRowIndexes))
            {
                resetRowIndexes();            
            }
        },
        placeholder: 'ui-state-highlight',
        forcePlaceholderSize: true,
        helper: fixHelper,
        revert: 200,
        cursor: 'move',
        distance: 15
    });
}     

$(document).ready(function (){
    //console.log('zenbu_common.js loaded');

    // Set up the disableSelection function
    // so that text doesn't get selected when dragging rows
    jQuery.fn.extend({ 
        disableSelection : function() { 
                return this.each(function() { 
                        this.onselectstart = function() { return false; }; 
                        this.unselectable = "on"; 
                        jQuery(this).css('user-select', 'none'); 
                        jQuery(this).css('-o-user-select', 'none'); 
                        jQuery(this).css('-moz-user-select', 'none'); 
                        jQuery(this).css('-khtml-user-select', 'none'); 
                        jQuery(this).css('-webkit-user-select', 'none'); 
                }); 
        } 
    });

    /**
     * Disable selection style on table headers
     */
    $("th").disableSelection();

    /**
     * Make table cells clickable - click on a cell to toggle a checkbox within
     */
    $("body").undelegate("td.clickable", "click");
    $("body").delegate("td.clickable", "click", function(e) {

        if(e.target.type != 'checkbox')
        {
            if($(this).find('input[type=checkbox]:checked').length === 0)
            {
                $(this).disableSelection().find('input[type=checkbox]:not(:disabled)').attr('checked', true);
            } 
            else 
            {
                $(this).disableSelection().find('input[type=checkbox]:not(:disabled)').attr('checked', false);
            }
        }

    });

    /**
     * Magic checkboxes 
     * - recreated for better binding with ajax-loaded elements
     */
    $('table.mainTable th').unbind('click'); // Unbinding the native EE magic checkbox
    $("body").delegate('table.mainTable th input[type=checkbox]', 'click', function (e) {
        parentTableHeaderRow = $(this).closest('tr');
        parentTableCell      = $(this).closest('th');
        columnIndex          = parentTableHeaderRow.find('th').index(parentTableCell);
        
        checked = $(this).is(':checked');
        $('table.mainTable tr').each(function() {
            $(this).find('td').eq(columnIndex).find('input[type=checkbox]:not(:disabled)').attr('checked', checked);
        });
    });

    /**
     * Save/Saving button label switcher
     */
    $("body").delegate("button.submit", "click", function(){
        $(this).find("span").hide();
        $(this).find("span").eq(1).show();
        $(this).addClass('work');
    });

    /**
    *   ========================
    *   General settings slider
    *   ========================
    *   Inspired from EE's control panel style guide
    *   Using the "Forum preferences accordion"
    *   @link   http://expressionengine.com/user_guide/development/cp_styles/index.html#forum_preferences_accordion
    */
    $(".editAccordion > h3").css("cursor", "pointer");

    $(".editAccordion h3").click(function() {
        if ($(this).hasClass("collapsed")) {
            $(this).siblings().slideDown("fast");
            $(this).removeClass("collapsed").parent().removeClass("collapsed");
        }
        else {
            $(this).siblings().slideUp("fast");
            $(this).addClass("collapsed").parent().addClass("collapsed");
        }
    });
    
});