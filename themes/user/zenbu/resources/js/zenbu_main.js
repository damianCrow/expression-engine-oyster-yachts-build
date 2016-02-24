$(document).ready(function (){
    // console.log('zenbu_main.js loaded');

    /**
     * Focus on first filter's third field on page load
     */
    $("div.zenbu-filters").find('.thirdFilter').eq(0).find("input, select").focus();

    var zenbuUrl = EE.BASE.replace(/\?(.*)/g, '?/cp/addons/settings/zenbu/');

    initSortableTable();
    processBasedOnSecondFilter();
    resetRowIndexes();
    toggleRemoveFilterRuleDisplay();
    parseSearchFilters(zenbuUrl + 'fetch_cached_temp_filters'); // Does updateChosen() as well

    /**
     * Run a query on page load if
     * filter data is available
     */
    if( $("div.zenbu-filters").hasClass('has-cached-filter-data') )
    {
        //runQuery($("div.zenbu-filters"));
    }

    /**
     * Prevent the filter form from submitting on
     * pressing Enter key
     */
    $("form#zenbuSearchFilter").submit(function(e){
        e.preventDefault();
    });


    /**
     * Adding a collapsible sidebar
     */
    $("div.content").addClass('hiding-sidebar');
    //$("div.sidebar").hide();

    var sidebar = $("div.mainZenbuArea").closest('.box').prev('.col.w-4').detach();
    $("div.mainZenbuArea").closest('.w-16').after(sidebar);

    $("body").delegate("a.show-sidebar", "click", function(e){
        e.preventDefault();
        if($(this).hasClass("showing"))
        {
            $("div.content").addClass('hiding-sidebar');
            $("div.sidebar").hide();
            $(this).removeClass("showing").show();
            $(this).closest('li').removeClass("back").addClass("rte-order-list");
            // $("div.mainZenbuArea").css({ 'width': '100%'});
            $("div.mainZenbuArea").closest('.w-12').next('.col.w-4').hide();
            $("div.mainZenbuArea").closest(".w-12").removeClass("w-12").addClass("w-16");
        }
        else
        {
            $("div.content").removeClass('hiding-sidebar');
            $("div.sidebar").show();
            $(this).addClass("showing").show();
            $(this).closest('li').removeClass("rte-order-list").addClass("back");
            mainZenbuAreaWidth = $("div.mainZenbuArea").width();
            //$("div.mainZenbuArea").css({ 'width': mainZenbuAreaWidth - 310});
            $("div.mainZenbuArea").closest('.w-16').next('.col.w-4').show();
            $("div.mainZenbuArea").closest(".w-16").removeClass("w-16").addClass("w-12");

        }
    });


    $(".entryType_select").hide().find("select").attr('disabled', 'disabled');

    /**
     * Display entryType dropdown if section has
     * more than one entry type
     */
    var sectionId = $("div.zenbu-filters select#section_select").val();
    $(".entryType_select").hide().find("select").attr('disabled', 'disabled');
    //$(".sectionId-"+sectionId).show().find("select").removeAttr('disabled');

    /**
     * Run the query, filters, etc, based on select field change
     */
    $("body").delegate("div.zenbu-filters select", "change", function (e) {

        e.preventDefault();

        if($(this).attr('name') == 'channel_id' || $(this).attr('name') == 'entryTypeId')
        {
            var sectionId = $("div.zenbu-filters select#section_select").val() !== '' ? $("div.zenbu-filters select#section_select").val() : 0;
            var entryTypeId = $("div.zenbu-filters div.entryType_select.sectionId-"+sectionId).find("select").val();
            $(".entryType_select").hide().find("select").attr('disabled', 'disabled');
            $(".sectionId-"+sectionId).show().find("select").removeAttr('disabled');
            updateRightNav(sectionId);
            fetchNewEntryButton(sectionId);
            fetchFirstFilters(sectionId, entryTypeId);
            fetchOrderby(sectionId, entryTypeId);
        }

        if($(this).closest("td").hasClass('firstFilter'))
        {
            fetchSecondFilters();
            fetchThirdFilters();
        }

        processBasedOnSecondFilter();
        resetRowIndexes();
        updateChosen();
        runQuery($(this));
    });

    /**
     * Datepicker
     */
    $("body").delegate(".datepicker input, input.datepicker", "focus", function(e) {
        var datepickerElem = $(this);
        $(this).datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function() {
                runQuery(datepickerElem);
            }
        });
    });

    /**
    *   Adding typeWatch delay when typing.
    *   Less calls == less sluggishness
    */
    var typeWatchOptions = {
        callback: function () {
            runQuery($("div.zenbu-filters"));
        },
        wait: 750,
        highlight: false,
        captureLength: 0
    };

    /**
     * Bind search after typing delay
     */
    $(".thirdFilter input").typeWatch(typeWatchOptions);

    $("body").delegate("div.paginate a", "click", function (e) {
        e.preventDefault();
        runQuery($("div.zenbu-filters"), $(this).attr('href'));
    });


    /**
     * Add filterRule Rows
     */

    $("body").delegate(".addFilterRule", "click", function (e) {
        filterRow = $(this).closest("tr.filter-params").clone();
        filterRow.find(".thirdFilter select, .thirdFilter input").val('');

        // Removing Chosen elements from cloned row
        filterRow.find("select, input").show().next("div.chosen-container").remove();

        $("tr.filter-params").last().after(filterRow);
        fetchSecondFilters();
        fetchThirdFilters();
        processBasedOnSecondFilter();
        resetRowIndexes();
        toggleRemoveFilterRuleDisplay();
        updateChosen();
    });


    /**
     * Remove filterRule Row
     */
    $("body").delegate(".removeFilterRule", "click", function (e) {
        if($("tr.filter-params").length > 1)
        {
            $(this).closest("tr.filter-params").remove();
            fetchSecondFilters();
            fetchThirdFilters();
            processBasedOnSecondFilter();
            resetRowIndexes();
            runQuery($("div.zenbu-filters"));
        }

        toggleRemoveFilterRuleDisplay();
        updateChosen();
    });


    /**
     * Select All
     */
    $("body").delegate("[name=selectAll]", "click", function(e){
        var checked  = $(this).is(':checked');
        if(checked === true)
        {
            $("[name*=elementIds]").prop("checked", true);
        }
        else
        {
            $("[name*=elementIds]").prop("checked", false);
        }
    });


    /**
     * Main - Show action button when entries are selected
     */
    $("body").delegate("[name*=elementIds], [name=selectAll]", "click", function(e){
        var totalSelected = $("[name*=elementIds]:checked").length;
        if(totalSelected >= 1)
        {
            $(".actionDisplay").show();
        }
        else
        {
            $(".actionDisplay").hide();
        }
    });


    /**
     * Saved Searches - Show action button when saved searches are selected
     */
    $("body").delegate("[name*=searchIds]", "click", function(e){
        var totalSelected = $("[name*=searchIds]:checked").length;
        if(totalSelected >= 1)
        {
            $(".savedSearchActions").show();
        }
        else
        {
            $(".savedSearchActions").hide();
        }
    });


    /**
     * Modal window
     */
    $("body").delegate(".showModal", "click", function(e){
        e.preventDefault();
        var $div = $(this).next("div.outerModal").html();
        var myModal = new Garnish.Modal($div, {
            resizable: true,
            onHide: function(){
                myModal.destroy();
                $(".modal-shade").remove();
            }
        });
    });


    /**
     * Image Modal window
     */
    $("body").delegate(".imageModal", "click", function(e){
        e.preventDefault();
        var imageUrl = $(this).attr("href");
        var image = $(this).next("div.outerModal").find("div.modal").html('<iframe src="'+imageUrl+'" class="imageiframe"></iframe>');
        var $div = $(this).next("div.outerModal").html();
        var originalWidth = $(this).attr("data-original-width");
        var originalHeight = $(this).attr("data-original-height");
        var modalWindow = Garnish.Modal.extend({
           desiredWidth: originalWidth,
           desiredHeight: originalHeight,
        });

        var myModal = new modalWindow($div, {
            resizable: true,
            onShow: function(){
                // Garnish's Modal doesn't go lower than ~600px in width.
                // This means even smaller images have wasted whitespace.
                // The following readjusts the modal to be smaller and centered
                $("div.modal").css({
                        'width':        originalWidth,
                        'min-width':    originalWidth + 'px',
                        'height':       originalHeight,
                        'min-height':   originalHeight + 'px',
                        'left':         Math.round((Garnish.$win.width() - originalWidth) / 2)
                    });
            },
            onHide: function(){
                myModal.destroy();
                $(".modal-shade").remove();
            }
        });
    });


    /**
     * Fetch "New Entry" button
     */
    function fetchNewEntryButton(sectionId)
    {
        //
    }


    /**
     * Update Tab URLs
     */
    function updateTabUrls()
    {
        var sectionId = $("div.zenbu-filters select#section_select").val();
        var entryTypeId = $("div.zenbu-filters div.entryType_select.sectionId-"+sectionId).find("select").val();
        $("nav").find("a.tab").each(function(){
            rawUrl           = $(this).attr('href');
            originalUrl      = rawUrl.replace(/\?(.*)/g, '');

            // Modify only Tab Urls that use GET variables
            if(rawUrl.indexOf("searches") == -1)
            {
                sectionIdParam   = typeof sectionId !== 'undefined' && sectionId !== '' ? '?sectionId=' + sectionId : '';
                entryTypeIdParam = typeof entryTypeId !== 'undefined' && entryTypeId !== '' ? '&entryTypeId=' + entryTypeId : '';
                $(this).attr('href', originalUrl  + sectionIdParam + entryTypeIdParam);
            }
        });
    }


    /**
     * Show or don't show "-" remove
     * filter rule button
     */
    function toggleRemoveFilterRuleDisplay()
    {
        if($("tr.filter-params").length > 1)
        {
            $(".removeFilterRule").show();
        }
        else
        {
            $(".removeFilterRule").hide();
        }
    }


    /**
     * Fetch the 1st filters corresponding to
     * the selected Section and entryType
     */
    function fetchFirstFilters(sectionId, entryTypeId){

        // Loop through each filter row
        $("tr.filter-params").each(function(){
            // Get original value of the 1st filter in row
            originalFirstFieldValue = $(this).find("td.firstFilter").find("select").val();

            // Clone 1st filter
            // Fetch and clone field from hidden filterFields
            dropdownClass = typeof entryTypeId !== 'undefined' ? '.sectionId-' + sectionId + '.entryTypeId-' + entryTypeId : '.sectionId-' + sectionId;
            firstFilter = $("div.filterFields").find("td.firstFilter" + dropdownClass).clone();

            // Set the cloned field to the original value
            firstFilter.find("select").val(originalFirstFieldValue);

            // Set the cloned field to the first option if
            // the original value does not match something
            // in the new cloned field
            if( ! firstFilter.find("select option:selected").length)
            {
                firstFilter.find("select").val(firstFilter.find("select option:first").val());
            }

            fetchSecondFilters();

            // Replace original field with cloned field
            $(this).find("td.firstFilter").replaceWith(firstFilter);
        });
    }


    /**
     * Fetch the 2nd filters corresponding to
     * the selected 1st filter
     */
    function fetchSecondFilters()
    {
        $("tr.filter-params").each(function(){

            // Get original value of the 1st filter in row
            originalFirstFieldValue = $(this).find("td.firstFilter").find("select").val();
            originalSecondFieldValue = $(this).find("td.secondFilter").find("select").val();
            secondFilterType = $("td.fieldsSecondFilterTypes").find('input[name=' + originalFirstFieldValue + ']');

            // Clone 2nd filter
            if($.inArray(originalFirstFieldValue, ['status']) !== -1)
            {
                secondFilter = $("div.filterFields").find("td.secondFilter.index_0").clone();
            }
            else if($.inArray(originalFirstFieldValue, ['id', 'category', 'author']) !== -1)
            {
                secondFilter = $("div.filterFields").find("td.secondFilter.index_1").clone();
            }
            else if($.inArray(originalFirstFieldValue, ['entry_date', 'expiration_date', 'edit_date']) !== -1)
            {
                secondFilter = $("div.filterFields").find("td.secondFilter.index_2").clone();
            }
            else if($.inArray(originalFirstFieldValue, ['title', 'url_title']) !== -1)
            {
                secondFilter = $("div.filterFields").find("td.secondFilter.index_3").clone();
            }
            else if(secondFilterType.length > 0 && secondFilterType.val() == 'contains_doesnotcontain')
            {
                secondFilter = $("div.filterFields").find("td.secondFilter.index_5").clone();
            }
            else
            {
                secondFilter = $("div.filterFields").find("td.secondFilter.index_4").clone();
            }

            // Set the cloned field to the original value
            secondFilter.find("select").val(originalSecondFieldValue);

            // Set the cloned field to the first option if
            // the original value does not match something
            // in the new cloned field
            if( ! secondFilter.find("select option:selected").length)
            {
                secondFilter.find("select").val(secondFilter.find("select option:first").val());
            }

            // Replace original field with cloned field
            $(this).find("td.secondFilter").replaceWith(secondFilter);
        });
    }


    /**
     * Some processing to run based on second filter value
     */
    function processBasedOnSecondFilter()
    {
        $("tr.filter-params").each(function(){
            originalFirstFieldValue  = $(this).find(".firstFilter").find("select").val();
            originalSecondFieldValue = $(this).find("td.secondFilter").find("select").val();
            originalThirdField       = $(this).find(".thirdFilter select, .thirdFilter input, input.thirdFilter, select.thirdFilter");

            if($.inArray(originalSecondFieldValue, ['isempty', 'isnotempty']) !== -1)
            {
                originalThirdField.hide();
            }
            else if($.inArray(originalFirstFieldValue, ['entry_date', 'expiration_date', 'edit_date']) !== -1)
            {
                if($.inArray(originalSecondFieldValue, ['range']) !== -1)
                {
                    thirdFilter = $("div.filterFields").find(".thirdFilter.datepicker-range").clone();
                }
                else
                {
                    thirdFilter = $("div.filterFields").find(".thirdFilter.datepicker").clone();
                }
                originalThirdField.closest('td.thirdFilter').replaceWith(thirdFilter);
            }
            else
            {
                // We don't want to show select fields,
                // since Chosen.js usually has the
                // "real" select field hidden
                if(! originalThirdField.select)
                {
                    originalThirdField.show();
                }
            }
        });
    }


    /**
     * Fetch the 3nd filters corresponding to
     * the selected 1st filter
     */
    function fetchThirdFilters()
    {
        var sectionId = $("select.section_select").val();

        $("tr.filter-params").each(function(){
            // Get original value of the 1st filter in row
            originalFirstFieldValue  = $(this).find(".firstFilter").find("select").val();
            originalSecondFieldValue = $(this).find("td.secondFilter").find("select").val();
            originalThirdFieldValue  = $(this).find(".thirdFilter select, .thirdFilter input, input.thirdFilter, select.thirdFilter").val();

            // Clone 3rd filter
            if($.inArray(originalFirstFieldValue, ['status']) !== -1)
            {
                thirdFilter = $("div.filterFields").find(".thirdFilter.statusSelect").clone();
            }
            else if($.inArray(originalFirstFieldValue, ['author']) !== -1)
            {
                thirdFilter = $("div.filterFields").find(".thirdFilter.authorSelect").clone();
            }
            else if($.inArray(originalFirstFieldValue, ['entry_date', 'expiration_date', 'edit_date']) !== -1)
            {
                thirdFilter = $("div.filterFields").find(".thirdFilter.datepicker").clone();
                originalThirdFieldValue = '';
            }
            else if($.inArray(originalFirstFieldValue, ['category']) !== -1)
            {
                thirdFilter = $("div.filterFields").find(".categoryFilter.sectionId-" + sectionId).clone();
                originalThirdFieldValue = '';
            }
            else
            {
                thirdFilter = $("div.filterFields").find(".thirdFilter.generalInput").clone();
            }

            // Set the cloned field to the original value
            thirdFilter.val(originalThirdFieldValue).find("select, input").val(originalThirdFieldValue);

            // Replace original field with cloned field
            $(this).find("td.thirdFilter").replaceWith(thirdFilter);
        });

        $(".thirdFilter input").typeWatch(typeWatchOptions);
    }

    /**
     * Fetch the Order By filter corresponding to
     * the selected Section and entryType
     */
    function fetchOrderby(sectionId, entryTypeId)
    {
        originalOrderbyFilter = $("table.limit-and-orderby").find("td.orderbyFilter select").val();

        // Clone 1st filter
        // Fetch and clone field from hidden filterFields
        dropdownClass = typeof entryTypeId !== 'undefined' ? '.sectionId-' + sectionId + '.entryTypeId-' + entryTypeId : '.sectionId-' + sectionId;
        orderbyFilter = $("div.filterFields").find("td.orderbyFilter" + dropdownClass).clone();

        $("table.limit-and-orderby").find("td.orderbyFilter").replaceWith(orderbyFilter);
        orderbyFilter.find("select, input").val(originalOrderbyFilter);
    }


    /**
     * Reset filter indexes, i.e. order X in "filter[X]"
     */
    function resetRowIndexes()
    {
        $("tr.filter-params").each(function(index){
            rowIndex = index;
            $(this).find('select[name*=filter], input[name*=filter]').each(function(){
                oldName = $(this).attr('name');
                newName = oldName.replace(/filter\[\d\]/g, 'filter['+ rowIndex + ']');
                $(this).attr('name', newName);
            });
        });
    }


    /**
     * Run the main query
     */
    function runQuery(elem, theURL)
    {
        var theAction = typeof theURL !== 'undefined' ? theURL : elem.closest("form").attr("action");
        var theData = elem.closest("form").serialize();
        $(".loading").show();

        $.ajax({
                    type:     "POST",
                    url: theAction, // <= Providing the URL
                    data: theData, // <= Providing the form data, serialized above
                    success: function(results){
                            // What to do when the ajax is successful.
                            // "results" is the response from the url (eg. "theAction" here)
                            $("div.resultArea").html(results);
                            initSortableTable();
                            $(".loading").hide();
                            resetRowIndexes();
                            $(".multi-entry").find("span.entries").empty();
                            $("th").disableSelection();
                            $(".multi-entry").find("span.entries").empty();
                            $(".multi-entry button.submit").addClass('disable').attr('disabled', 'disabled');
                            cacheSearchFilters();
                            //console.log(results);
                        },
                    error: function(results){
                            // What to do when the ajax fails.
                            console.log(results.statusText);
                            console.log(results.responseText);
                            $("div.resultArea").html(results.responseText);
                            $(".loading").hide();
                            $(".multi-entry").find("span.entries").empty();
                            $(".multi-entry button.submit").addClass('disable').attr('disabled', 'disabled');
                            $("th").disableSelection();
                    }
        });
    }

    /**
     * Confirmation box
     */
    $("body").delegate("a.action", "click", function(e){
        e.preventDefault();

        var param          = $(this).attr('data-param');
        var value          = $(this).attr('data-value');
        var confirmMessage = $(this).attr('data-confirm');
        var returnUrl      = $(this).attr('data-returnUrl');

        if( typeof confirmMessage !== 'undefined' )
        {
            var answer = confirm( confirmMessage );

            if(answer)
            {
                var theAction = $("form#resultList").attr('action');
                var theData   = $("form#resultList").serialize() + '&' + param + '=' + value;

                $.ajax({
                    type:     "POST",
                    url: theAction,
                    data: theData,
                    //dataType: 'json',
                    success: function(results){
                             runQuery($("div.zenbu-filters"));
                             if($("div.actionDisplay").hasClass('hideAfterAction'))
                             {
                                $("div.actionDisplay").hide();
                             }

                             if( typeof returnUrl !== 'undefined' )
                             {
                                window.location = returnUrl;
                             }
                        },
                    error: function(results){
                            console.log(results.statusText);
                            console.log(results.responseText);
                    }
                });
            } else {
                return false;
            }
        }
    });


    /**
     * Saving a Search
     */
    $("body").delegate("form#saveSearch", "submit", function(e){
        e.preventDefault();

        if($(this).find("input[name=label]").val() === '')
        {
            $("button.submit").find("span").hide().eq(0).show();
            return;
        }

        var theAction = $(this).attr("action");
        var theData = $(this).serialize();
        var theFilterData = $("form#zenbuSearchFilter").serialize();
        theData = theData + '&' + theFilterData;

        $(".loading").show();

        $.ajax({
                    type:     "POST",
                    url: theAction,
                    data: theData,
                    //dataType: 'json',
                    success: function(results){
                            parseSavedSearchList(results);
                            $(".loading").hide();
                            $("button.submit").find("span").hide().eq(0).show();
                            $("form#saveSearch").find("input[name=label]").val('');
                            $("li.heading.savedSearches").show();
                        },
                    error: function(results){
                            console.log(results.statusText);
                            console.log(results.responseText);
                            $(".loading").hide();
                    }
        });
    });

    /**
     * Retrieve saved search list
     */
    function parseSavedSearchList(data)
    {
        $("ul#savedSearchesList").empty();

        data = jQuery.parseJSON(data);

        var baseUrl = data.base_url.base + '?/cp/' + data.base_url.path;

        $(data.items).each(function(index, item) {
            $("ul#savedSearchesList").append(
                $('<li></li>').append(
                    $('<a></a>').text(item.label).attr('href', baseUrl + 'fetch_search_filters&searchId=' +item.id)
                )
            );
        });
    }


    /**
     * Saved Search Loading
     */
    $("body").delegate("ul#savedSearchesList li a", "click", function(e){
        e.preventDefault();
        parseSearchFilters($(this).attr('href'));
        cacheSearchFilters();
    });


    /**
     *
     * Temporarily cache search filters to
     * get back to them later
     */
    function cacheSearchFilters()
    {
        var theFilterData = $("form#zenbuSearchFilter").serialize();

        $.ajax({
                type:       "POST",
                url:        zenbuUrl + 'cache_temp_filters',
                data:       theFilterData,
                // dataType:   'json',
                success: function(results){
                },
                error: function(results){
                        console.log(results.statusText);
                        console.log(results.responseText);
                }
        });

    }


    /**
     * Fetch and parse search filters in Zenbu's filter form,
     * based on URL which can be eg. cache fetching URL or saved search URL
     * @param  {string} theUrl The URL to call
     * @return void
     */
    function parseSearchFilters(theUrl)
    {
        var sectionName = 'channel_id';
        var theAction = theUrl;
        var csrf_token = EE.CSRF_TOKEN.length > 0 ? 'csrf_token=' + EE.CSRF_TOKEN : 'XID=' + EE.XID;

        $.ajax({
                type:       "POST",
                url:        theAction,
                data:       csrf_token,
                dataType:   'json',
                success: function(results){

                    if(results.length > 0)
                    {
                        var sectionId = 0;
                        var entryTypeId = 0;

                        // Find sectionId and entryTypeId
                        $.each(results, function(item, filter)
                        {
                            if(filter.filterAttribute1 == sectionName)
                            {
                                sectionId = filter.filterAttribute3;
                            }

                            if(filter.filterAttribute1 == 'entryTypeId')
                            {
                                entryTypeId = filter.filterAttribute3;
                            }
                        });

                        // Get the total filters
                        var totalFilters = 0;
                        $.each(results, function(item, filter){
                            if( $.inArray(filter.filterAttribute1, [sectionName, 'entryTypeId', 'orderby', 'limit']) === -1 )
                            {
                                totalFilters++;
                            }
                        });

                        // Keep the first filterRow and remove others
                        $("tr.filter-params").not(':first').remove();

                        // Clone the kept filterRow as many times as there are filters
                        for(i = 1; i < totalFilters; i++)
                        {
                            firstFilterRow = $("tr.filter-params").eq(0).clone();
                            $("tr.filter-params").last().after(firstFilterRow);
                        }

                        // Fetch dropdowns based on section/entrytype
                        if(typeof sectionId !== 'undefined' && typeof entryTypeId !== 'undefined')
                        {
                            var entryTypeIdParam;

                            if(entryTypeId !== 0)
                            {
                                entryTypeIdParam = entryTypeId;
                            }

                            fetchFirstFilters(sectionId, entryTypeIdParam);
                            fetchSecondFilters();
                            fetchThirdFilters();
                            processBasedOnSecondFilter();
                            resetRowIndexes();
                        }

                        // Set values in fields
                        var rowIndex = 0;
                        $.each(results, function(item, filter){

                            if(filter.filterAttribute1 == sectionName)
                            {
                                sectionIdVal = sectionId === '0' ? '' : sectionId;
                                $("div.zenbu-filters select#section_select").val(sectionIdVal);
                                $(".entryType_select").hide().find("select").attr('disabled', 'disabled');
                                $(".entryType_select.sectionId-"+sectionId).show().find("select").removeAttr('disabled').val(entryTypeId);
                            }

                            if( $.inArray(filter.filterAttribute1, [sectionName, 'entryTypeId', 'orderby', 'limit']) === -1 )
                            {
                                $("tr.filter-params").eq(rowIndex).find(".firstFilter select").val(filter.filterAttribute1);
                                fetchSecondFilters();
                                $("tr.filter-params").eq(rowIndex).find(".secondFilter select").val(filter.filterAttribute2);
                                fetchThirdFilters();
                                $("tr.filter-params").eq(rowIndex).find(".thirdFilter select, .thirdFilter input").val(filter.filterAttribute3);
                                $("tr.filter-params").eq(rowIndex).find(".thirdFilter").val(filter.filterAttribute3);
                                rowIndex++;
                            }

                            if(filter.filterAttribute1 == 'limit')
                            {
                                $("div.zenbu-filters").find("select[name=limit]").val(filter.filterAttribute2);
                            }

                            if(filter.filterAttribute1 == 'orderby')
                            {
                                $("div.zenbu-filters select[name=orderby]").val(filter.filterAttribute2);
                                $("div.zenbu-filters select[name=sort]").val(filter.filterAttribute3);
                            }

                        });

                        $(".loading").show();
                    }

                    processBasedOnSecondFilter();
                    resetRowIndexes();
                    runQuery($("div.zenbu-filters"));
                    updateTabUrls();
                    toggleRemoveFilterRuleDisplay();
                    updateChosen();

                },
                error: function(results){
                        console.log(results.statusText);
                        console.log(results.responseText);
                        $(".loading").hide();
                }

        });
    }


    /**
    *  Table sorter
    */
    $("body").delegate("table.resultsTable thead tr th", 'click', function () {

        orderby = $(this).attr('data-fieldType');

        // Category filtering not set yet, skip all this if "Category" or other header is clicked
        if(orderby in ['entry_checkbox', 'live_look', 'view_count', 'last_author'] || typeof orderby === 'undefined')
        {
            return;
        }

        sort = "ASC";

        if($(this).find('i.icon').hasClass("fa-sort-amount-asc") === true)
        {
            sort = "DESC";
        }

        $("select[name='orderby']").val(orderby);
        $("select[name='sort']").val(sort);

        runQuery($('form#zenbuSearchFilter'));

    });

    /**
    * ========================
    *       Fancybox
    * ========================
    * Events are on *hover* so
    * that fancybox first binds
    * and is then available.
    *
    */

    /**
    *  Fancybox for images
    */
    $("body").delegate("a.fancybox", 'hover', function (e) {
        e.preventDefault();

        $(this).fancybox({
            'overlayShow'       : false,
            'centerOnScroll'    : true,
            'titlePosition'     : 'inside',
            'enableEscapeButton'    : true
        });
    });

    /**
    *  Fancybox for matrix fields
    */
    $("body").delegate("a.fancyboxmatrix", 'hover', function (e) {
        e.preventDefault();

        $(this).fancybox({
            'enableEscapeButton'    : true,
            'centerOnScroll'        : true,
            'autoDimensions'        : true,
        });
    });

    $("body").delegate("a.fancybox-inline", 'hover', function (e) {
        e.preventDefault();

        $(this).fancybox({
            'enableEscapeButton'    : true
        });
    });


    /**
    *  Fancybox for Live Look
    */
    $("body").delegate("a.fancyboxtemplate", 'hover', function (e) {

        // If Cmd/Ctrl+click, don't trigger fancybox
        if(e.ctrlKey || e.metaKey)
        {
            return;
        }

        e.preventDefault();

        $(this).fancybox({
            'overlayShow'       : true,
            'centerOnScroll'    : true,
            'type'              : 'iframe',
            'width'             : '90%',
            'height'            : '90%',
            'autoScale'         : true,
            'titlePosition'     : 'inside'
        });

    });

    /**
    *  Fancybox for iframe
    */
    $("body").delegate("a.fancyboxiframe", 'hover', function (e) {

        // If Cmd/Ctrl+click, don't trigger fancybox
        if(e.ctrlKey || e.metaKey)
        {
            return;
        }

        e.preventDefault();

        $(this).fancybox({
            'overlayShow'       : true,
            'centerOnScroll'    : true,
            'type'              : 'iframe',
            'autoScale'         : true,
            'titlePosition'     : 'inside'
        });
    });

    /**
     *  ------------------------
     *  Entry results checkboxes
     *  ------------------------
     */
    $("body").delegate("input[name^=toggle], th input[type=checkbox], td.clickable", "change, click", function (e){
        if($(this).closest('table').hasClass('mainTable'))
        {
            toggleMultiEntryButtons(true);        
        }
        else
        {
            toggleMultiEntryButtons(false);        
        }
    });

    function toggleMultiEntryButtons(controlFormCtrls)
    {
        totalSelected = $("table.mainTable input[name^=toggle]:checked").length;
        if(totalSelected > 0)
        {
            if(controlFormCtrls)
            {
                $(".form-ctrls").show();            
            }
            theCheckboxes = $("table.mainTable input[name^=toggle]:checked").clone().removeAttr('type').attr('type', 'hidden');
            $(".multi-entry").find("button.submit").removeClass('disable').removeAttr('disabled');
            $(".multi-entry").find("span.entries").empty().html(theCheckboxes);
        }
        else
        {
            if(controlFormCtrls)
            {
                $(".form-ctrls").hide();
            }
            //theCheckboxes = $("input[name^=toggle]:checked").clone();
            $(".multi-entry").find("button.submit").addClass('disable').attr('disabled', 'disabled');
            $(".multi-entry").find("span.entries").empty();
        }
    }

    /**
     * ------------------------
     * Multi-Entry Button Click
     * ------------------------
     * Things that happen when the multi-entry button is clicked
     */
    $("body").delegate(".multi-entry button.submit", "click", function(e){
        // Replace icon with spinner
        $(this).find("i.fa").removeClass().addClass('fa fa-spinner fa-spin');
        // Disable buttons
        $(".multi-entry button.submit").addClass('disable').attr('disabled', 'disabled');
        // Make the currently clicked button enabled
        $(this).removeClass('disable').removeAttr('disabled');
    });

    /**
     * Move the Saved Search form outside of the hyperlink
     */
    //saveSearchForm = $('div.box.sidebar form#saveSearch').clone();
    //$('div.box.sidebar form#saveSearch').closest("a").before(saveSearchForm).remove();
    // $('div.box.sidebar form#saveSearch').closest("a");


});
