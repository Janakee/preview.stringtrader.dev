function adv_popup_setCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}
function adv_popup_getCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}
jQuery(document).ready(function($){
		divWidth = $(".advertisement_popupbox").outerWidth(true);
		divheight =$(".advertisement_popupbox").outerHeight(true);
		var maxwidth = divWidth + 20;
		$(".advertisement_popupbox").animate({right : -maxwidth});

			if(adv_popup_getCookie("close_adv_box")=='yes')
			{
			$(".advertisement_popupbox").animate({right:-2},200);
			$(".adv_popup_heading").hide();
			$(".advertisement_popupbox").css({height : 26, width : 22});
			$(".advertisement_popupbox_close").hide();
			$(".advertisement_popupbox_max").show();
			}
		$(window).scroll(function(){
				var chkd=$(window).scrollTop();
				var scrollBottom =$(document).height()-$(window).height()-40;
				if(chkd>=scrollBottom)
				{
				$(".advertisement_popupbox").animate({right:-2},200);
				}
		});
});
jQuery(document).ready(function(){
	jQuery('.advertisement_popupbox_close').click(function(){
	jQuery(".adv_popup_heading").hide();
	jQuery(".advertisement_popupbox").animate({height : 26, width : 22},400);
	jQuery(this).hide();
	jQuery(".advertisement_popupbox_max").show();
	adv_popup_setCookie("close_adv_box",'yes',1);
	});
});
jQuery(document).ready(function(){
	jQuery('.advertisement_popupbox_max').click(function(){
	jQuery(".advertisement_popupbox").animate({height : divheight, width : divWidth},400);
	jQuery(".adv_popup_heading").show();
	jQuery(this).hide();
	jQuery(".advertisement_popupbox_close").show();
 	adv_popup_setCookie("close_adv_box",'no',1);
	});
});