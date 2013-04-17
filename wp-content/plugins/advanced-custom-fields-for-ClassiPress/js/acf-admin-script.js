jQuery(document).ready(function($) {

    /* Profile tabs */
    $('#col2 span').addClass('current');
    $('.col2').show();
    $('.col3, .col4, .col5').hide();

    $('#col2').click(function(){
        $( "#slider" ).slider({
            value: 0
        });
    });
    $('#col3').click(function(){
        $( "#slider" ).slider({
            value: 2
        });
    });
    $('#col4').click(function(){
        $( "#slider" ).slider({
            value: 3
        });
    });
    $('#col5').click(function(){
        $( "#slider" ).slider({
            value: 1
        });
    });

    $( "#tabs" ).tabs();
    $( "#slider" ).slider({
        value: 0,
        step: 1,
        max: 3,
        change: function(event, ui){
            switch (ui.value) {
                case 0:
                    $('.col2').fadeIn();
                    $('#col2 span').addClass('current');
                    $('#col3 span, #col4 span, #col5 span').removeClass('current');
                    $('.col3, .col4, .col5').hide();
                    break;
                case 2:
                    $('.col3').fadeIn();
                    $('#col3 span').addClass('current');
                    $('#col2 span, #col4 span, #col5 span').removeClass('current');
                    $('.col2, .col4, .col5').hide();
                    break;
                case 3:
                    $('.col4').fadeIn();
                    $('#col4 span').addClass('current');
                    $('#col3 span, #col2 span, #col5 span').removeClass('current');
                    $('.col3, .col2, .col5').hide();
                    break;
                case 1:
                    $('.col5').fadeIn();
                    $('#col5 span').addClass('current');
                    $('#col3 span, #col4 span, #col2 span').removeClass('current');
                    $('.col3, .col4, .col2').hide();
                    break;
            }
        }
    });

    /* Rewrites indexes of the fields after removing and sorting rows */
    $.indexes = function(row, i) {
    var n, len, prop, attr;
    row.attr('data-array_index', i);
    for (n = 0, len = field_properties.length; n < len; n++) {
        prop = field_properties[n];
        attr = 'field_' + prop + '_' + i;
        row.find('.field_' + prop).attr('name', attr).attr('id', attr);
    }
    };

    /* Setup the form validation */
    $('#acf_options_form').validate({
        errorClass: 'invalid',
        errorPlacement: function(error, element) {
            if (element.attr('type') === 'checkbox' || element.attr('type') === 'radio') {
                element.closest('ol').after(error);
            } else {
                offset = element.offset();
                error.insertAfter(element);
                error.addClass('message');  // add a class to the wrapper
            }
        }
    });

    /* Add Rules */
    $(".field_name").each(function() {
        if ($(this).attr("type") === "text" && $(this).attr("id") !== "field_name_"){
            $(this).rules("add", {
                required: true,
                alphanumeric:true
            });
        }
    });

    $(".field_limits_attr").each(function() {
        $(this).rules("add", {
            numericdelim: true
        });
    });

   /* Field type change event listener */
    $.field_type_change = function (field) {
        var row = field.closest('tr');
        var values = row.find('.field_values');
        var type = field.val();
            if (type === "drop-down" || type === "checkbox" || type === "radio") {
                values.fadeIn();
                values.rules("add", {
                    required: true
                });
            } else {
                values.rules("remove");
                values.fadeOut();
                values.parent().find('label').remove();
            }
    };
    $('.field_type').change(function(){
        $.field_type_change($(this));
    });
    $('.field_type').change();

    /* Add new item event listener */
    $('#acf_add-field-btn').click(function(){
        var template_row = $('#template_row').clone(withDataAndEvents = true);
        var field_name = template_row.find('.field_name');
        var rowid = $('#acf_profile_field-table').find('tbody').find('tr').length;

        template_row.attr('id', '').addClass('even');

        $.indexes(template_row, rowid);

        template_row.insertBefore('#template_row').hide().fadeIn();

        field_name.rules("add", {
            required: true,
            alphanumeric:true
        });
        $.field_type_change(template_row.find('.field_type'));
    });

    /* Delete row */
    $.delete_item = function(td) {
        if(confirm("Are you sure you want to delete this item?")) {
            td.closest('tr').remove();
            var row = $('#acf_profile_field-table').find('tbody').find('tr');
            var len = row.length-1;
            row.each(function(i){
                if (i != len){
                    $.indexes($(this), i+1);
                    $.field_type_change($(this).find('.field_type'));
                }
            });
        }
    };
    $('.row_actions').click( function() {
        $.delete_item($(this));
    });

    /* Date Picker Settings  */

    // Date Picker Settings constructor
    $.dateOptConstr = function(){
        var dateOptions = $.datepicker.regional[$( "#locale" ).val()];
            dateOptions.showAnim=           $( "#animation" ).val();
            dateOptions.numberOfMonths=     parseInt($( "#multi_month" ).val(),10);
            dateOptions.showButtonPanel=    ($("#button_bar").attr("checked")==="checked") ? true : false;
            dateOptions.changeMonth=        ($("#menus").attr("checked")==="checked") ? true : false;
            dateOptions.changeYear=         ($("#menus").attr("checked")==="checked") ? true : false;
            dateOptions.showOtherMonths=    ($("#other_dates").attr("checked")==="checked") ? true : false;
            dateOptions.selectOtherMonths=  ($("#other_dates").attr("checked")==="checked") ? true : false;
            dateOptions.showOn=             ($("#icon_trigger").attr("checked")==="checked") ? "both" : "focus";
            dateOptions.buttonImage=        ($("#icon_trigger").attr("checked")==="checked") ? $( "#calendar" ).attr("src") : "";
            dateOptions.buttonImageOnly=    ($("#icon_trigger").attr("checked")==="checked") ? true : false;
            dateOptions.dateFormat = ($( "input[name=date_format]:checked" ).val() != 0) ? $( "input[name=date_format]:checked" ).val() : dateOptions.dateFormat;
        return dateOptions;
    };

    $( "#datepicker" ).datepicker($.dateOptConstr());

    $( "input[name=date_format]" ).change(function() {
        if($( "input[name=date_format]:checked" ).val() != 0){
           $( "#datepicker" ).datepicker( "option", "dateFormat", $( "input[name=date_format]:checked" ).val() );
        } else {
           $( "#datepicker" ).datepicker( "destroy" ).datepicker( $.dateOptConstr() );
        }
    });
    $( "#custom_format_text" ).change(function() {
            $("#date_format_9").attr("value", $( this ).val());
            $( "#datepicker" ).datepicker( "option", "dateFormat", $( "#custom_format_text" ).val() );
    });

    $( "#locale, #animation, #multi_month, #button_bar, #menus, #other_dates, #icon_trigger" ).change(function() {
             $( "#datepicker" ).datepicker( "destroy" ).datepicker( $.dateOptConstr() );
    });
if ($("#dateCustom_err").val() == "") $("#dateCustom_err").val("Please enter date in valid format!");

    $.validator.addMethod(
       "datevalidator",
        function(value, element) {
            var ret = true;
            var options = $.dateOptConstr();
            var format = options.dateFormat;
	// parseDate throws exception if the value is invalid
             try{$.datepicker.parseDate( format, value, options);}
             catch(e){ret = false;}
             return this.optional(element) || ret;
            },
            $("#dateCustom_err").val()
    );

    $("#dateCustom_err").change(function(){
        $.validator.messages.datevalidator = $(this).val();
    });

    /* Sortable Profile rows */
    // Return a helper with preserved width of cells
    var fixHelper = function(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    };
    $( "#acf_profile_field-table" ).sortable({
        items:'tbody tr',
        helper: fixHelper,
        opacity: 0.7,
        revert:true,
        axis: 'y',
        cursor:'move',
        placeholder: 'ui-placeholder',
        forcePlaceholderSize: true,
        update:function(event, ui){
            var row = $('#acf_profile_field-table').find('tbody').find('tr');
            var len = row.length-1;
            row.each(function(i){
                if (i != len){
                    $.indexes($(this), i+1);
                    $.field_type_change($(this).find('.field_type'));
                }
            });
        }
    }); //.disableSelection(); removed util solution could be found;

    /* Tool Tips */
    $(".titletip").easyTooltip({
        yOffset: -20
    });
    $(".titletip").mousemove(function(e){
        $("#easyTooltip").css("left",((document.body.offsetWidth - e.pageX < 430) ? document.body.offsetWidth - 430 : e.pageX) + "px");
    });

    $.format_tip = function(elem){
        var value = elem.val();
        if (value != ""){
            var cont = field_formats[value].desc;
         elem.easyTooltip({
            content: cont,
            xOffset: 50
         });
        }
    };
    $('.field_format option, .field_limits option').each(function(){
        $.format_tip($(this));
    });
    $('#acf_error_msgs-table .format_name').each(function(){
        var value = $(this).html();
        var cont = field_formats[value].desc;
        $(this).easyTooltip({
            content: cont,
            yOffset: -20
        });
    });


/* Default validation messegies */
    var n;
    if (field_formats){
        for(n in field_formats){
            if ($('#'+n+'_err').val() == ''){
                var args = field_formats[n].args;
                if (args == '2') {
                    $('#'+n+'_err').val('Enter values in the correct range please');
                } else if (args == '1') {
                    $('#'+n+'_err').val($.validator.messages[n]("{0}"));
                } else {
                    $('#'+n+'_err').val($.validator.messages[n]);
                }
            }
        }
    }

    /* Check all checkboxes in the column */
    $('.col_check').click(function(){
        var col = $(this).attr('id');
        if (this.checked){
            $('.'+col).attr("checked","checked");
        }
        else {
            $('.'+col).removeAttr("checked");
        }
        return true;
    });

    /* Form Submit event listener */
    $('#acf_submit_top-btn, #acf_submit_bot-btn').click(function(){
        if ($('#acf_options_form').valid()) {
            $('#template_row').remove();
            return true;
        } else {
            return false;
        }
    });
});

