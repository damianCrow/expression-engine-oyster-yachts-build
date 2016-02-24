$(document).ready(function (){
    // console.log('zenbu_saved_searches.js loaded');

    $("input[name*='search_ids_selected']").click(function() {
        totalSelected = $("input[name*='search_ids_selected']:checked").length;
        if(totalSelected > 0)
        {
            $("div.options").slideDown('fast');
        }
        else
        {
            $("div.options").slideUp('fast');   
        }
    });

    initSortableTable();
});