function resetRowIndexes()
{
    $("table.settingsTable tbody").find("td.order").each(function(index){
       var rowIndex = index + 1;
       $(this).html(rowIndex);

       $(this).next("td").find("input[name^='field']").each(function(){

        oldName = $(this).attr('name');
        newName = oldName.replace(/field\[\d\]/g, 'field['+ rowIndex + ']');
        $(this).attr('name', newName);

       });
    });
}

$(document).ready(function (){
    // console.log('zenbu_display_settings.js loaded');
    
    /**
     * Reset filter indexes, i.e. order X in "filter[X]"
     */

    initSortableTable();
});