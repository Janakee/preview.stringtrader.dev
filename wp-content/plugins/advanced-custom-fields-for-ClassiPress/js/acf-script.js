jQuery(document).ready(function($) {

    $('#user_login').addClass('required');
    $('#user_email').addClass('required email');

    $('<span class="colour"> *</span>').appendTo( $('#user_login').parent().find('label') );
    $('<span class="colour"> *</span>').appendTo( $('#user_email').parent().find('label') );
    $('<span class="colour"> *</span>').appendTo( $('#pass1').parent().find('label') );
    $('<span class="colour"> *</span>').appendTo( $('#pass2').parent().find('label') );

    var tabindex = 1; // Set indexes to registration form elements
    $('#registerform input, #registerform select, #registerform textarea, #registerform a').each(function() {
        if (this.type != "hidden") {
            var $input = $(this);
            $input.attr("tabindex", tabindex);
            tabindex++;
        }
    });


    // Setup js validation
    $('#registerform').validate({
        errorClass: 'invalid',
        errorPlacement: function(error, element) {
            if (element.attr('type') === 'checkbox' || element.attr('type') === 'radio') {
                element.closest('ol').after(error);
            } else {
                offset = element.offset();
                error.appendTo(element.parent());
            }
        }
    });
});