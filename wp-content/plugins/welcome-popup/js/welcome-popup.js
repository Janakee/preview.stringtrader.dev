var welcomepopup_use = false;
var welcomepopup_countdown;
var welcomepopup_timeout;
var welcomepopup_onload = false;

function welcomepopup_open() {
	try {
		if (!welcomepopup_use) {
			welcomepopup_use = true;
			var welcomepopup_overlay_style = "style='background: "+welcomepopup_value_overlay_bg_color+"; opacity: "+welcomepopup_value_overlay_opacity+"; -ms-filter:\"progid:DXImageTransform.Microsoft.Alpha(Opacity="+parseInt(100*welcomepopup_value_overlay_opacity)+")\"; filter:alpha(opacity=\""+parseInt(100*welcomepopup_value_overlay_opacity)+"\";'";
			jQuery("body").append("<div id='welcomepopup_overlay' "+welcomepopup_overlay_style+"></div><div id='welcomepopup_window' style='position: fixed; background:"+welcomepopup_value_popup_bg_color+" url("+welcomepopup_value_popup_bg_url+") 0 0 repeat;'></div>");
			
			var welcomepopup_width = welcomepopup_value_width + 30;
			var welcomepopup_height = welcomepopup_value_height + 40;

			var welcomepopup_close_button = "";
			if (welcomepopup_onload == false || welcomepopup_value_hide_close != "on") {
				jQuery("#welcomepopup_overlay").click(welcomepopup_close);
				welcomepopup_close_button = '<span id="welcomepopup_close" onclick="welcomepopup_close();"></span>';
			}
			
			var window_width = jQuery(window).width();
			if (window_width > 0 && window_width < welcomepopup_width+30) {
				welcomepopup_width = window_width - 30;
			}
			
			jQuery("#welcomepopup_window").append("<div id='welcomepopup_content' style='width:"+parseInt(welcomepopup_width-30, 10)+"px; min-height:"+parseInt(welcomepopup_height-45, 10)+"px;'></div>"+welcomepopup_close_button+"<span id='welcomepopup_delay'></span>");

			jQuery("#welcomepopup_content").append(jQuery("#welcomepopup_container").children());
			jQuery("#welcomepopup_window").bind('welcomepopup_unload', function () {
				jQuery("#welcomepopup_container").append(jQuery("#welcomepopup_content").children() );
			});
			var content_height = jQuery("#welcomepopup_content").height();
			if (content_height > welcomepopup_height-45) {
				welcomepopup_height = content_height + 30;
			}

			var window_height = jQuery(window).height();
			if (window_height > 0 && window_height < welcomepopup_height+30) {
				welcomepopup_height = window_height - 30;
			}
			
			jQuery("#welcomepopup_window").css({
				marginLeft: '-'+parseInt((welcomepopup_width / 2),10)+'px', 
				width: welcomepopup_width+'px',
				marginTop: '-'+parseInt((welcomepopup_height / 2),10)+'px',
				height: welcomepopup_height+'px'
			});
			jQuery("#welcomepopup_window").css({
				"visibility" : "visible"
			});
		}
	} catch(e) {

	}
	return false;
}

function welcomepopup_close() {
	welcomepopup_use = false;
	welcomepopup_onload = false;
	clearTimeout(welcomepopup_timeout);
	jQuery("#welcomepopup_delay").html("");
	jQuery("#welcomepopup_window").fadeOut("fast", function() {
		jQuery("#welcomepopup_window, #welcomepopup_overlay").trigger("welcomepopup_unload").unbind().remove();
	});
	return false;
}

function welcomepopup_read_cookie(key) {
	var pairs = document.cookie.split("; ");
	for (var i = 0, pair; pair = pairs[i] && pairs[i].split("="); i++) {
		if (pair[0] === key) return pair[1] || "";
	}
	return null;
}

function welcomepopup_write_cookie(key, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	} else var expires = "";
	document.cookie = key+"="+value+expires+"; path=/";
}

function welcomepopup_onload_open() {
	if (!welcomepopup_use) {
		if (welcomepopup_cookie_ttl != 0) welcomepopup_write_cookie("welcomepopup", welcomepopup_cookie_value, welcomepopup_cookie_ttl);
		else if (welcomepopup_once_per_visit == "on") welcomepopup_write_cookie("welcomepopup", welcomepopup_cookie_value, 0);
		welcomepopup_onload = true;
		if (welcomepopup_delay_value != 0) {
			welcomepopup_countdown = welcomepopup_delay_value;
			welcomepopup_timeout = setTimeout("welcomepopup_counter();", 1000);
		}
		welcomepopup_open();
	}
}

function welcomepopup_counter() {
	if (welcomepopup_countdown == 0) {
		welcomepopup_close();
	} else {
		welcomepopup_countdown = welcomepopup_countdown - 1;
		jQuery("#welcomepopup_delay").html(welcomepopup_countdown_string(welcomepopup_countdown));
		welcomepopup_timeout = setTimeout("welcomepopup_counter();", 1000);
	}
}

function welcomepopup_init() {
	if (welcomepopup_value_display_onload == "on") {
		var window_width = jQuery(window).width();
		if (welcomepopup_value_disable_mobile != "on" || window_width <= 0 || window_width >= welcomepopup_value_width+30+30) {
			welcomepopup_cookie = welcomepopup_read_cookie("welcomepopup");
			if (welcomepopup_cookie != welcomepopup_cookie_value) {
				if (welcomepopup_start_delay_value == 0) welcomepopup_onload_open();
				else setTimeout("welcomepopup_onload_open();", welcomepopup_start_delay_value);
			}
		}
	}
	jQuery(".welcomepopup").click(function() {
		welcomepopup_open();
		return false;
	});
	
}

function welcomepopup_countdown_string(value) {
	var result = '';
	var hours = Math.floor(value/3600);
	var minutes = Math.floor((value - 3600*hours)/60);
	var seconds = value - 3600*hours - 60*minutes;
	if (hours > 0) {
		if (hours > 9) result = hours.toString() + ":";
		else result = "0" + hours.toString() + ":";
	}
	if (minutes > 9) result = result + minutes.toString() + ":";
	else result = result + "0" + minutes.toString() + ":";
	if (seconds > 9) result = result + seconds.toString();
	else result = result + "0" + seconds.toString();
	return result;
}
