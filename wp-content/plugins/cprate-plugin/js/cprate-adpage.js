/*
Classipress Rated Author
Current Version: 1.2
Plugin Author: Julio Gallegos
Author URL: http://myclassipro.com
*/

jQuery(document).ready(function($) {   
		var data = {
			action: "cprate_my_rating_action",
			this_id: cprateAdPage.cprate_id
		};
		$.post(cprateAjax.cprate_ajax, data, function(response) {	
			var resp=response;
			response=resp.substring(0,resp.length-1);
			$(".member").first().append(response);		
		});
});