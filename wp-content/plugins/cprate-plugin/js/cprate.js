/*
Classipress Rated Author
Current Version: 1.2
Plugin Author: Julio Gallegos
Author URL: http://myclassipro.com
*/

jQuery(document).ready(function() { 
  	jQuery('#stars-wrapper').ratings(5).bind('ratingchanged', function(event, data) {
    	jQuery('#rating_show').text(data.rating);
  	});
	jQuery("#leave_feedback").toggle(function() {
			jQuery("#cprate_feedback_form").show(500);
			jQuery(this).val('Cancel');
		}, function () {
			jQuery("#cprate_feedback_form").hide(500);
			jQuery(this).val('Leave Feedback');
	});
	var numShown = 10; // Initial rows shown & index
	var numMore = 5; // Increment
	var cprateTbl = jQuery('#tbody_feedback_history');
	var numRows = cprateTbl.find('tr').length; // Total # rows
		if(numRows > 10) {
			cprateTbl.find('tr:gt(' + (numShown - 1) + ')').hide().end().parent().after(
				'<div id="cprate_more">' +
					'<a class="showmore" href="javascript:void(0)" >Show <span>' + numMore + '</span> More</a>' +
					'<nobr> | </nobr>' +
					'<a class="showall" href="javascript:void(0)" >Show All</a>' +
				'</div>'
			);
			if ( numRows - numShown < numMore ) {
				jQuery('#cprate_more span').html(numRows - numShown);
			}
			jQuery('#cprate_more .showmore').click(function(){
				numShown = numShown + numMore;
					if ( numShown >= numRows ) {
						 jQuery('#cprate_more').remove();
					}
					if ( numRows - numShown < numMore ) {
						 jQuery('#cprate_more span').html(numRows - numShown);
					}
				cprateTbl.find('tr:lt('+numShown+')').show('slow');
			});
			jQuery('#cprate_more .showall').click(function(){
					cprateTbl.find('tr').show('slow');
					jQuery('#cprate_more').remove();
			})
		}
});

function removeErr(tag, id) {
	jQuery(tag).mousedown(function() {
		if(id == 1) {
			jQuery(".error1").hide('slow').removeClass('error1');
		}
		else if(id == 2) {
			jQuery(".error2").hide('slow').removeClass('error2');
		}
		else if(id == 3) {
			jQuery(".error3").hide('slow').removeClass('error3');
		}
	});	
}

function save_sub_comments(id) {

	var sub_comments_text = jQuery("#text_sub_comments_"+id);
	var errmsg = jQuery("#cprate-error-rb");
	
	if(sub_comments_text.val() == "") {
		errmsg.show().text("You must enter a comment").addClass("error3");
		removeErr(sub_comments_text, 3);
	}
	else {
		
		jQuery("#sub_comments_wrapper_"+id).empty().html("<div style='color:green'>Saving your reply...</div>");

		var data = {
			action: "save_sub_comments",
			post_feedback_nonce: cpRateVars.post_feedback_nonce,
			sub_comments_id: id,
			sub_comments_text: sub_comments_text.val()	
		};
		
		jQuery.post(cprateAjax.cprate_ajax, data, function(response) {
			var resp=response;
			response=resp.substring(0,resp.length-1);
			jQuery("#sub_comments_wrapper_"+id).html(response);	
			}
		);
	}
}


function SubmitFeedback() {

	var star_rating=0;
	var feed_back = jQuery("#cprate_comments");
	var errmsg = jQuery("#cprate-error");
	var starwrap = jQuery("#stars-wrapper");
	
	if(jQuery("#rating_show").html()>=1){
		star_rating=jQuery("#rating_show").html();
	}
	
	
	if(!starwrap.find('div').hasClass('jquery-ratings-full')) {
		errmsg.show().text("You must select at least 1 star").addClass("error1");
		removeErr(starwrap, 1);
	}
	else if(feed_back.val() == "") {
		errmsg.show().text("Please enter your feedback").addClass("error2");
		removeErr(feed_back, 2);
	}
	else {
			
		jQuery("#cprate_wrapper").hide();
		jQuery("#cprate_loading").show();
		
		var data = {
			action: "cprate_post_feedback_action",
			post_feedback_nonce: cpRateVars.post_feedback_nonce,
			given_to_user_id: cpRateVars.given_to_user_id,
			star_rating: star_rating,
			feed_back: feed_back.val()};
			
			jQuery.post(cprateAjax.cprate_ajax, data, function(response) {
				jQuery("#cprate_wrapper").remove();
				jQuery("#cprate_loading").remove();
				jQuery("#tbody_feedback_history").prepend(response);
				if(cpRateVars.mod_req_set == "off") {
					cpRateVars.total_count++;
				}
				jQuery("#cprate-firstH3").before(jQuery("#cprate-secH3")).remove();
				jQuery("#total_count").html(cpRateVars.total_count);
			
				var new_rating = jQuery("#new_rating").val() + '%';
					jQuery("#cprate_per").html(new_rating);
			});
	}
}
