/*
Classipress Rated Author
Current Version: 1.2
Plugin Author: Julio Gallegos
Author URL: http://myclassipro.com
*/

jQuery(document).ready(function($) { 
	var fields = $('.clicker');
	
	function testNumeric(field) {
		var is = $(field);
		var name = is.attr('name');
			if(is.val() != "") {
				var value = is.val().replace(/^\s\s*/, '').replace(/\s\s*$/, '');
				var intRegex = /^\d+$/;
				if(!intRegex.test(value) && (!$('#' + name).length)) {
					is.parent().append("<div id='" + name + "' class='cprate_error'>Field must be numeric</div>");
					is.click(function() { $('#' + name).remove() });
				}
			} 	
	}

	fields.click(function() {
		var input = $(this);
		var text = input.next();
			if (input.find('input').is(":checked")) {
				text.css('color', '').find('input').prop('disabled', false).css('color', '');
			} else {
				text.css('color', '#DDD').find('input').prop('disabled', true).css('color', '#DDD');
			}
	}).click();
	fields.not(":eq(0)").next().find('input').blur(function() { 
			testNumeric(this);
	});
	
/* This controls the moderate table */	

	$('img.remove_fb').click(function() {
		if (confirm('Are you sure you want to delete this record?')) {
			var row = $(this).parent().parent();
			var data = {
					action: "cprate_delete_feedback",
					row_id: row.attr('id')
				};
				$.post(ajaxurl, data, function(response) {
						row.hide('slow', function(){ row.remove(); });
				});
    		}
    	return false;  
	});
	$('img.approve_fb').click(function() {
		var is = $(this);
		var row = is.parent().parent();
		var data = {
				action: "cprate_approve_feedback",
				row_id: row.attr('id')
			};
		$.post(ajaxurl, data, function(response) {
				row.css('background-color','inherit');
				is.hide('slow');
		});
	});
	$('#reload_me').click(function() {
		$(this).parent().submit();
	});
	
	//bulk action
	//var values = jQuery('input:checkbox:checked').map(function () {
  	//return this.value;
	//}).get();

});
