<?php
/*
Plugin Name: Welcome Popup
Plugin URI: http://www.icprojects.net/welcome-popup.html
Description: This plugin adds highly customizable welcome popup to your website.
Author: Ivan Churakov
Author URI: http://www.icprojects.net/about
Version: 1.4
*/
define('WELCOMEPOPUP_VERSION', 1.4);

class welcomepopup_class {
	var $options;
	var $display = 'off';
	var $modes = array("none", "all", "homepost", "post");
	var $font_schemes = array("light" => "Light", "dark" => "Dark");
	
	function __construct() {
		if (function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain('welcomepopup', false, dirname(plugin_basename(__FILE__)).'/languages/');
		}
		$this->options = array (
			"version" => WELCOMEPOPUP_VERSION,
			"cookie_value" => "ilovelencha",
			"cookie_ttl" => 0,
			"once_per_visit" => "off",
			"start_delay" => 0,
			"delay" => 30,
			"mode" => "none",
			"message" => __('Dear visitor! This is demonstration of <a href="http://www.icprojects.net/welcome-popup.html">Welcome Popup</a> plugin.', 'welcomepopup'),
			"widget_areas" => 0,
			"width" => 400,
			"height" => 80,
			"popup_bg_color" => "#333",
			"popup_bg_url" => plugins_url('/images/default2_bg.jpg', __FILE__),
			"overlay_bg_color" => "#EEE",
			"overlay_opacity" => 0.80,
			"disable_mobile" => "off",
			"hide_close" => "off",
			"font_scheme" => "light",
			"css" => "",
		);

		$this->get_options();
		
		add_action('widgets_init', array(&$this, 'widgets_init'), 99);
		if (is_admin()) {
			if ($this->check_options() !== true) add_action('admin_notices', array(&$this, 'admin_warning'));
			add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
			add_action('admin_menu', array(&$this, 'admin_menu'));
			add_action('init', array(&$this, 'admin_request_handler'));
			add_action('admin_menu', array(&$this, 'add_meta'));
			add_action('save_post', array(&$this, 'save_meta'));
			add_action('wp_ajax_welcomepopup_reset', array(&$this, "welcomepopup_reset"));
			add_action('wp_ajax_nopriv_welcomepopup_reset', array(&$this, "welcomepopup_reset"));
			
		} else {
			if ($this->check_options() === true) {
				add_action('wp', array(&$this, 'front_init'));
			}
		}
	}

	function get_options() {
		$exists = get_option('welcomepopup_version');
		if ($exists) {
			foreach ($this->options as $key => $value) {
				$this->options[$key] = get_option('welcomepopup_'.$key);
			}
		}
	}

	function update_options() {
		if (current_user_can('manage_options')) {
			foreach ($this->options as $key => $value) {
				update_option('welcomepopup_'.$key, $value);
			}
		}
	}

	function populate_options() {
		foreach ($this->options as $key => $value) {
			if (isset($_POST['welcomepopup_'.$key])) {
				$this->options[$key] = stripslashes($_POST['welcomepopup_'.$key]);
			}
		}
	}

	function check_options() {
		$errors = array();
		if (!is_numeric($this->options['cookie_ttl']) || intval($this->options['cookie_ttl']) < 0) $errors[] = __('Cookie lifetime must be at least 0 days', 'welcomepopup');
		if (!is_numeric($this->options['start_delay']) || intval($this->options['start_delay']) < 0) $errors[] = __('Start delay must be valid value', 'welcomepopup');
		if (!is_numeric($this->options['delay']) || intval($this->options['delay']) < 0) $errors[] = __('Autoclose delay must be valid value', 'welcomepopup');
		if (!is_numeric($this->options['width']) || intval($this->options['width']) < 150) $errors[] = __('Width of popup box must be at least 150px', 'welcomepopup');
		if (!is_numeric($this->options['height']) || intval($this->options['height']) < 20) $errors[] = __('Height of popup box must be at least 20px', 'welcomepopup');
		if ($this->get_rgb($this->options['popup_bg_color']) === false) $errors[] = __('Popup box color must be valid value', 'welcomepopup');
		if (!empty($this->options['popup_bg_url'])) {
			if (!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->options['popup_bg_url'])) $errors[] = __('Popup box background URL must be valid URL', 'welcomepopup');
		}
		if ($this->get_rgb($this->options['overlay_bg_color']) === false) $errors[] = __('Overlay color must be valid value', 'welcomepopup');
		if (!is_numeric($this->options['overlay_opacity']) || floatval($this->options['overlay_opacity']) < 0 || floatval($this->options['overlay_opacity']) > 1) $errors[] = __('Overlay opacity must be between 0 and 1', 'welcomepopup');
		if (empty($errors)) return true;
		return $errors;
	}

	function get_meta($post_id) {
		$meta = array();
		$meta["active"] = htmlspecialchars_decode(get_post_meta($post_id, 'welcomepopup_active', true));
		return $meta;
	}

	function add_meta() {
		add_meta_box("welcomepopup", '<img class="welcomepopup_icon" src="'.plugins_url('/images/popup.png', __FILE__).'" alt="Welcome Popup" title="Welcome Popup"> Welcome Popup', array(&$this, 'show_meta'), "post", "normal", "high");
		add_meta_box("welcomepopup", '<img class="welcomepopup_icon" src="'.plugins_url('/images/popup.png', __FILE__).'" alt="Welcome Popup" title="Welcome Popup"> Welcome Popup', array(&$this, 'show_meta'), "page", "normal", "high");
		$post_types = get_post_types(array('public' => true, '_builtin' => false), 'names', 'and'); 
		foreach ($post_types as $post_type ) {
			add_meta_box("welcomepopup", '<img class="welcomepopup_icon" src="'.plugins_url('/images/popup.png', __FILE__).'" alt="Welcome Popup" title="Welcome Popup"> Welcome Popup', array(&$this, 'show_meta'), $post_type, "normal", "high");
		}		
	}
	
	function show_meta() {
		global $post;
		$meta = $this->get_meta($post->ID);
		//wp_nonce_field(basename(__FILE__), 'welcomepopup-nonce');
		print ('
			<table class="welcomepopup_useroptions">
			<tr>
				<th style="width: 100px;">'.__('Welcome Popup', 'welcomepopup').':</th>
				<td><input type="checkbox" id="welcomepopup_active" name="welcomepopup_active" '.($meta["active"] == 'on' ? ' checked="checked"' : '').'> '.__('Activate Welcome Popup', 'welcomepopup').'<br /><em>'.__('Please tick checkbox if you would like to activate welcome popup for this post. This option is ignored if you selected "All website pages" display mode on settings page.', 'welcomepopup').'</em></td>
			</tr>
			</table>');
	}

	function save_meta($post_id) {
		if (isset($_POST['post_type'])) $post_type = $_POST['post_type'];
		else $_POST['post_type'] = null;
		$post_type_object = get_post_type_object($_POST['post_type']);

		if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		|| (!isset($_POST['post_ID']) || $post_id != $_POST['post_ID'])
		//|| (!check_admin_referer(basename(__FILE__), 'welcomepopup-nonce'))
		|| (!current_user_can($post_type_object->cap->edit_post, $post_id))) {
			return $post_id;
		}
		$value_new = (isset($_POST["welcomepopup_active"]) ? "on" : "");
		if (empty($value_new)) delete_post_meta($post_id, "welcomepopup_active");
		else update_post_meta($post_id, "welcomepopup_active", htmlspecialchars($value_new));
		return $post_id;
	}

	function admin_menu() {
		add_options_page(__('Welcome Popup', 'welcomepopup'), __('Welcome Popup', 'welcomepopup'), 'manage_options', 'welcomepopup', array(&$this, 'admin_settings'));
	}

	function admin_enqueue_scripts() {
		wp_enqueue_script("jquery");
		if (isset($_GET['page']) && $_GET['page'] == 'welcomepopup') {
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'farbtastic' );
		}
		wp_enqueue_style('welcomepopup_admin', plugins_url('/css/admin.css', __FILE__));
	}

	function admin_settings() {
		global $wpdb;
		$message = "";
		$errors = $this->check_options();
		if (is_array($errors)) $message .= '<div class="error"><p>'.__('The following error(s) exists:', 'welcomepopup').'<br />- '.implode('<br />- ', $errors).'</p></div>';
		if (isset($_GET['cleared'])) $message .= '<div class="updated"><p><strong>'.__('All counters successfully cleared.', 'welcomepopup').'</strong></p></div>';
		print ('
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br /></div><h2>'.__('Welcome Popup - Settings', 'welcomepopup').'</h2><br />
			'.$message.'
			<form enctype="multipart/form-data" method="post" style="margin: 0px" action="'.admin_url('options-general.php').'">
			<div class="postbox-container" style="width: 100%;">
				<div class="metabox-holder">
					<div class="meta-box-sortables">
						<div class="postbox welcomepopup_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('General Settings', 'welcomepopup').'</span></h3>
							<div class="inside">
								<table class="welcomepopup_useroptions">
									<tr>
										<th>'.__('Reset cookie:', 'welcomepopup').'</th>
										<td>
											<input type="button" class="welcomepopup_button button-primary" value="'.__('Reset Cookie', 'welcomepopup').'" onclick="welcomepopup_resetcookie();" > <img id="welcomepopup_progress" src="'.plugins_url('/images/loading.gif', __FILE__).'" alt="" width="16" height="16" >
											<br /><em>'.__('Click button to reset cookie. After reset welcome popup will appears for all users. Do this operation if you changed content of popup box and want to display it to returning visitors.', 'welcomepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Cookie lifetime (days):', 'welcomepopup').'</th>
										<td>
											<input type="text" id="welcomepopup_cookie_ttl" name="welcomepopup_cookie_ttl" value="'.htmlspecialchars($this->options['cookie_ttl'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<br /><em>'.__('Please set cookie lifetime. Cookie expires after this amount of days and popup appears again.', 'welcomepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Once per visit', 'welcomepopup').':</th>
										<td>
											<input type="checkbox" id="welcomepopup_once_per_visit" name="welcomepopup_once_per_visit" '.($this->options['once_per_visit'] == "on" ? 'checked="checked"' : '').'"> '.__('Display popup once per visit', 'welcomepopup').'
											<br /><em>'.__('Please tick checkbox if popup must appears once per visit. This parameter is used when "Cookie lifetime" is set as zero.', 'welcomepopup').'</em></td>
									</tr>
									<tr>
										<th>'.__('Display mode', 'welcomepopup').':</th>
										<td>
										<input type="radio" name="welcomepopup_mode" id="welcomepopup_mode_all" value="all"'.($this->options['mode'] == 'all' ? ' checked="checked"' : '').'> '.__('All website pages', 'welcomepopup').'
										<br /><em>'.__('Set this option if you wish to display popup on all pages of website.', 'welcomepopup').'</em><br />
										<input type="radio" name="welcomepopup_mode" id="welcomepopup_mode_homepost" value="homepost"'.($this->options['mode'] == 'homepost' ? ' checked="checked"' : '').'> '.__('Homepage and particular posts/pages', 'welcomepopup').'
										<br /><em>'.__('Set this option if you wish to display popup on homepage and particular posts/pages. You can assign particular pages on post/page editor.', 'welcomepopup').'</em><br />
										<input type="radio" name="welcomepopup_mode" id="welcomepopup_mode_post" value="post"'.($this->options['mode'] == 'post' ? ' checked="checked"' : '').'> '.__('Particular posts/pages', 'welcomepopup').'
										<br /><em>'.__('Set this option if you wish to display popup on particular posts/pages. You can assign particular posts/pages on post/page editor.', 'welcomepopup').'</em><br />
										<input type="radio" name="welcomepopup_mode" id="welcomepopup_mode_none" value="none"'.($this->options['mode'] == 'none' ? ' checked="checked"' : '').'> '.__('Do not display popup', 'welcomepopup').'
										<br /><em>'.__('Set this option to disable popup. OnClick popup is still active.', 'welcomepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Popup box content:', 'welcomepopup').'</th>
										<td>');
		if (function_exists('wp_editor')) {
			wp_editor($this->options['message'], "welcomepopup_message", array('wpautop' => false, 'tabindex' => 1));
		} else {
			print ('
											<textarea class="widefat" id="welcomepopup_message" name="welcomepopup_message" style="height: 120px;">'.htmlspecialchars($this->options['message'], ENT_QUOTES).'</textarea><br />');
		}
		print ('									
											<em>'.__('Please enter content of popup box. HTML allowed. Widget Areas are inserted below this content.', 'welcomepopup').'</em>
										</td>
									</tr> 
									<tr>
										<th>'.__('Number of widget areas', 'welcomepopup').':</th>
										<td>
											<select name="welcomepopup_widget_areas" id="welcomepopup_widget_areas" style="width: 80px;">
												<option value="0"'.($this->options['widget_areas'] == 0 ? ' selected="selected"' : '').'>0</option>
												<option value="1"'.($this->options['widget_areas'] == 1 ? ' selected="selected"' : '').'>1</option>
												<option value="2"'.($this->options['widget_areas'] == 2 ? ' selected="selected"' : '').'>2</option>
												<option value="3"'.($this->options['widget_areas'] == 3 ? ' selected="selected"' : '').'>3</option>
											</select>
											<br /><em>'.__('Set number of widget areas (columns) for popup box.', 'welcomepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Popup box size (px):', 'welcomepopup').'</th>
										<td>
											<input type="text" id="welcomepopup_width" name="welcomepopup_width" value="'.htmlspecialchars($this->options['width'], ENT_QUOTES).'" style="width: 80px; text-align: right;"> x
											<input type="text" id="welcomepopup_height" name="welcomepopup_height" value="'.htmlspecialchars($this->options['height'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<br /><em>'.__('Please set popup size (width x height). Popup height is calculated automatically. Here you set minimum height value.', 'welcomepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Start delay (seconds):', 'welcomepopup').'</th>
										<td>
											<input type="text" id="welcomepopup_start_delay" name="welcomepopup_start_delay" value="'.htmlspecialchars($this->options['start_delay'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<br /><em>'.__('Popup appears with this delay after page loaded. Set "0" for immediate start.', 'welcomepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Autoclose delay (seconds):', 'welcomepopup').'</th>
										<td>
											<input type="text" id="welcomepopup_delay" name="welcomepopup_delay" value="'.htmlspecialchars($this->options['delay'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<br /><em>'.__('Popup is automatically closed after this period of time. Set "0", if you do not need this functionality.', 'welcomepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Popup box color:', 'welcomepopup').'</th>
										<td>
											<input type="text" id="welcomepopup_popup_bg_color" name="welcomepopup_popup_bg_color" value="'.htmlspecialchars($this->options['popup_bg_color'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<div id="color_picker_welcomepopup_popup_bg_color"></div>
											<br /><em>'.__('Please set popup box background color.', 'welcomepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Background image URL:', 'welcomepopup').'</th>
										<td>
											<input type="text" id="welcomepopup_popup_bg_url" name="welcomepopup_popup_bg_url" value="'.htmlspecialchars($this->options['popup_bg_url'], ENT_QUOTES).'" class="widefat">
											<br /><em>'.__('Enter your URL of background image. Leave this field blank if you do not need background image.', 'welcomepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Font color scheme', 'welcomepopup').':</th>
										<td>
											<select id="welcomepopup_font_scheme" name="welcomepopup_font_scheme" style="min-width: 80px;">');
				foreach ($this->font_schemes as $key => $value) {
					echo '
												<option value="'.$key.'"'.($this->options['font_scheme'] == $key ? ' selected="selected"' : '').'>'.$value.'</option>';
				}
				print ('
											</select>
											<br /><em>'.__('Please select font color scheme.', 'welcomepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Overlay color:', 'welcomepopup').'</th>
										<td>
											<input type="text" id="welcomepopup_overlay_bg_color" name="welcomepopup_overlay_bg_color" value="'.htmlspecialchars($this->options['overlay_bg_color'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<div id="color_picker_welcomepopup_overlay_bg_color"></div>
											<br /><em>'.__('Please set overlay color.', 'welcomepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Overlay opacity:', 'welcomepopup').'</th>
										<td>
											<input type="text" id="welcomepopup_overlay_opacity" name="welcomepopup_overlay_opacity" value="'.htmlspecialchars($this->options['overlay_opacity'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<br /><em>'.__('Please set overlay opacity. Value must be between 0 and 1.', 'welcomepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Hide close button', 'donationmanager').':</th>
										<td>
											<input type="checkbox" id="welcomepopup_hide_close" name="welcomepopup_hide_close" '.($this->options['hide_close'] == "on" ? 'checked="checked"' : '').'"> '.__('Do not display close button', 'welcomepopup').'
											<br /><em>'.__('Please tick checkbox if you do not want to display close button.', 'welcomepopup').'</em></td>
									</tr>
									<tr>
										<th>'.__('Small screen devices', 'welcomepopup').':</th>
										<td>
											<input type="checkbox" id="welcomepopup_disable_mobile" name="welcomepopup_disable_mobile" '.($this->options['disable_mobile'] == "on" ? 'checked="checked"' : '').'"> '.__('Disable popup for small screen devices', 'welcomepopup').'
											<br /><em>'.__('Please tick checkbox if you want to disable popup for small screen devices.', 'welcomepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Stylesheet', 'welcomepopup').':</th>
										<td><textarea id="welcomepopup_css" name="welcomepopup_css" class="widefat" style="height: 120px;">'.htmlspecialchars($this->options['css'], ENT_QUOTES).'</textarea><br /><em>'.__('Customize stylesheet.', 'welcomepopup').'</em></td>
									</tr>
								</table>
								<div class="alignright">
								<input type="hidden" name="ak_action" value="welcomepopup_update_options" />
								<input type="hidden" name="welcomepopup_version" value="'.WELCOMEPOPUP_VERSION.'" />
								<input type="submit" class="welcomepopup_button button-primary" name="submit" value="'.__('Update Settings', 'welcomepopup').'">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			</form>
			<script type="text/javascript">
				function welcomepopup_resetcookie() {
					jQuery("#welcomepopup_progress").fadeIn(300);
					var data = {action: "welcomepopup_reset"};
					jQuery.post("'.admin_url('admin-ajax.php').'", data, function(data) {
						jQuery("#welcomepopup_progress").fadeOut(300);
					});
				}
				jQuery(document).ready(function(){
					jQuery("#color_picker_welcomepopup_popup_bg_color").farbtastic("#welcomepopup_popup_bg_color");
					jQuery("#color_picker_welcomepopup_overlay_bg_color").farbtastic("#welcomepopup_overlay_bg_color");
				});
			</script>
		</div>');
	}

	function welcomepopup_reset() {
		if (current_user_can('manage_options')) {
			$this->options["cookie_value"] = time();
			update_option('welcomepopup_cookie_value', $this->options["cookie_value"]);
			echo 'OK';
		}
		exit;
	}

	function admin_request_handler() {
		global $wpdb;
		if (!empty($_POST['ak_action'])) {
			switch($_POST['ak_action']) {
				case 'welcomepopup_update_options':
					$this->populate_options();
					if (isset($_POST["welcomepopup_once_per_visit"])) $this->options['once_per_visit'] = "on";
					else $this->options['once_per_visit'] = "off";
					if (isset($_POST["welcomepopup_hide_close"])) $this->options['hide_close'] = "on";
					else $this->options['hide_close'] = "off";
					if (isset($_POST["welcomepopup_disable_mobile"])) $this->options['disable_mobile'] = "on";
					else $this->options['disable_mobile'] = "off";
					$this->update_options();
					$errors = $this->check_options();
					if (!is_array($errors)) header('Location: '.admin_url('options-general.php').'?page=welcomepopup&updated=true');
					else header('Location: '.admin_url('options-general.php').'/?page=welcomepopup');
					die();
					break;
				default:
					break;
			}
		}
	}

	function widgets_init() {
		for($i=1; $i<=$this->options['widget_areas']; $i++) {
			register_sidebar( array(
				'name' => __('Welcome Popup - Area', 'welcomepopup').' #'.$i,
				'id' => 'welcomepopup-'.$i,
				'description' => __('Widget Area for Welcome Popup.', 'welcomepopup' ),
				'before_widget' => '<div id="%1$s" class="welcomepopup_widget %2$s">',
				'after_widget' => "</div>",
				'before_title' => '<h3 class="welcomepopup_widgettitle">',
				'after_title' => '</h3>',
			));
		}
	}

	function admin_warning() {
		echo '
		<div class="updated"><p><strong>Welcome Popup</strong> plugin almost ready. You must do some <a href="'.admin_url('options-general.php').'?page=welcomepopup">settings</a> for it to work.</p></div>';
	}

	function front_init() {
		global $post;
		$this->display = 'off';
		if ($this->options["mode"] == "all") {
			$this->display = 'on';
		} else if ($this->options["mode"] == "homepost") {
			if (is_home()) $this->display = 'on';
			else if (is_singular()) {
				$meta = $this->get_meta($post->ID);
				if ($meta["active"] == "on") $this->display = 'on';
			}
		} else if ($this->options["mode"] == "post") {
			if (is_singular()) {
				$meta = $this->get_meta($post->ID);
				if ($meta["active"] == "on") $this->display = 'on';
			}
		}
		add_action('wp_enqueue_scripts', array(&$this, 'front_enqueue_scripts'));
		add_action("wp_head", array(&$this, 'front_header'));
		add_action("wp_footer", array(&$this, 'front_footer'));
	}

	function front_enqueue_scripts() {
		wp_enqueue_script("jquery");
		wp_enqueue_style('welcomepopup', plugins_url('/css/welcome-popup.css', __FILE__));
		wp_enqueue_script('welcomepopup', plugins_url('/js/welcome-popup.js', __FILE__));
	}

	function front_header() {
		echo '
		<style type="text/css">
			'.($this->options["widget_areas"] > 0 ? '.welcomepopup_area {width: '.intval(100/$this->options["widget_areas"]).'%;}' : '').'
			'.$this->options["css"].'
		</style>
		<script type="text/javascript">
			var welcomepopup_cookie_value = "'.$this->options['cookie_value'].'";
			var welcomepopup_delay_value = '.$this->options['delay'].';
			var welcomepopup_start_delay_value = 1000*'.$this->options['start_delay'].';
			var welcomepopup_once_per_visit = "'.$this->options['once_per_visit'].'";
			var welcomepopup_cookie_ttl = '.$this->options['cookie_ttl'].';
			var welcomepopup_value_overlay_bg_color = "'.$this->options['overlay_bg_color'].'";
			var welcomepopup_value_overlay_opacity = "'.$this->options['overlay_opacity'].'";
			var welcomepopup_value_popup_bg_color = "'.$this->options['popup_bg_color'].'";
			var welcomepopup_value_popup_bg_url = "'.$this->options['popup_bg_url'].'";
			var welcomepopup_value_width = '.$this->options['width'].';
			var welcomepopup_value_height = '.$this->options['height'].';
			var welcomepopup_value_hide_close = "'.$this->options['hide_close'].'";
			var welcomepopup_value_disable_mobile = "'.$this->options['disable_mobile'].'";
			var welcomepopup_value_display_onload = "'.$this->display.'";
		</script>';
	}
	
	function front_footer() {
		$message = do_shortcode($this->options['message']);
		echo '
		<div id="welcomepopup_container" style="display: none;">
			<div class="welcomepopup_box welcomepopup_font_'.($this->options['font_scheme']).'">
				<div class="welcomepopup_message">
				'.$message.'
				</div>';
		if ($this->options["widget_areas"] > 0) {
			echo '
				<div id="welcomepopup_areas">';
			for ($i=1; $i<=$this->options["widget_areas"]; $i++) {
				echo '
					<div class="welcomepopup_area">';
				dynamic_sidebar('welcomepopup-'.$i);
				echo '
					</div>';
			}
			echo '
				</div>';
		}
		echo '
			</div>
		</div>
		<script type="text/javascript">
			welcomepopup_init();
		</script>';
	}

	function get_rgb($_color) {
		if (strlen($_color) != 7 && strlen($_color) != 4) return false;
		$color = preg_replace('/[^#a-fA-F0-9]/', '', $_color);
		if (strlen($color) != strlen($_color)) return false;
		if (strlen($color) == 7) list($r, $g, $b) = array($color[1].$color[2], $color[3].$color[4], $color[5].$color[6]);
		else list($r, $g, $b) = array($color[1].$color[1], $color[2].$color[2], $color[3].$color[3]);
		return array("r" => hexdec($r), "g" => hexdec($g), "b" => hexdec($b));
	}
}

$welcomepopup = new welcomepopup_class();
?>