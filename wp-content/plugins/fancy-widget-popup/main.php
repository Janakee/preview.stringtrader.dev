<?php
/*
Plugin Name: Fancy Widget PopUp
Plugin URI: http://www.webkhazana.com/plugin/fancy-widget-popup/
Description: This plugin will give you option to add any widget in sidebar provide by the plugin. It will PopOut when user reaches at footer of the site. This will help out user to gain attention after watching page completely.
Version: 1.0
Author: spiralinfotech, Jaytesh Barange
Author URI: http://www.webkhazana.com/author/jaytesh/
*/
function fwpopup_register_sidebar() {
register_sidebar(
array(
'name' => __('Fancy Widget Sidebar', 'fwpopup'),
'id' => 'fancy_widget_sidebar',
'description' => __('Drop Any Widget here for showing it in Fancy Widget Popup. For Options Check Settings -> Fancy Widget Popup', 'fwpopup'),
'before_widget' => '<div id="%1$s" class="widget %2$s">',
'after_widget' => '</div>',
'before_title' => '<h3 class="widget-title section-title">',
'after_title' => '</h3>'
)
);
}
add_action( 'wp_loaded', 'fwpopup_register_sidebar' );
add_action('wp_footer','show_fwpopup_popup');
function show_fwpopup_popup()
{
?>
<div class="advertisement_popupbox">
<div class="advertisement_popupbox_close">X</div>
<div class="advertisement_popupbox_max">+</div>
<div class="adv_popup_heading"><?php dynamic_sidebar('fancy_widget_sidebar')?></div>
</div>
<?php
}
add_action('wp_enqueue_scripts', 'fwpopup_scripts_method'); 

function fwpopup_scripts_method()
{
wp_enqueue_script('fwpopup_script',plugins_url('/adv/pop_up_right.js', __FILE__),array('jquery'),'2.0'); // for javascript
wp_register_style( 'fwpopup_style',plugins_url('/adv/pop-up-css.php', __FILE__));
wp_enqueue_style( 'fwpopup_style' );
}
add_action('admin_menu', 'fwpopup_plugin_menu');

function fwpopup_plugin_menu() {
	add_options_page('Fancy Widget Popup Settings','Fancy Widget Popup','manage_options', 'fwpopup_options', 'fwpopup_settings_func');
}
function fwpopup_settings_func()
{
global $wpdb;
?>
<div class="wrap">
<div class="icon32" id="icon-options-general"><br></div><h2><?php _e('Fancy Widget Popup Settings','fwpopup');?></h2>
<?php
if(isset($_POST['fwpopup_submit']))
{
$tosave=array('fwpopup_width','fwpopup_height','fwpopup_bgcolor','fwpopup_bordercolor');

	if($tosave)
	{
	foreach($tosave as $field)
	{
	update_option($field,$_POST[$field]);
	}
	?>
    <div class="updated"><?php _e('Settings Saved','fwpopup');?></div>
    <?php
	}
}
?>
<form method="post">
<table class="form-table">
<tbody>
<tr valign="top">
<th scope="row"><label for="fwpopup_width">Popup Width(eg: 365px)</label></th>
<td><input type="text" class="regular-text" value="<?php echo get_option('fwpopup_width');?>" id="fwpopup_width" name="fwpopup_width"></td>
</tr>
<tr valign="top">
<th scope="row"><label for="fwpopup_height">Popup Height(eg: 200px)</label></th>
<td><input type="text" class="regular-text" value="<?php echo get_option('fwpopup_height');?>" id="fwpopup_height" name="fwpopup_height"></td>
</tr>
<tr valign="top">
<th scope="row"><label for="fwpopup_bgcolor">Popup Background Color(eg: #ffffff)</label></th>
<td><input type="text" class="regular-text" value="<?php echo get_option('fwpopup_bgcolor');?>" id="fwpopup_bgcolor" name="fwpopup_bgcolor"></td>
</tr>
<tr valign="top">
<th scope="row"><label for="fwpopup_bordercolor">Popup Border Color(eg: #000000)</label></th>
<td><input type="text" class="regular-text" value="<?php echo get_option('fwpopup_bordercolor');?>" id="fwpopup_bordercolor" name="fwpopup_bordercolor"></td>
</tr>
<tr valign="top">
<th scope="row"><label for="fwpopup_submit">&nbsp;</label></th>
<td><input type="submit" class="button-primary" value="<?php _e('Save Settings','fwpopup');?>" id="fwpopup_submit" name="fwpopup_submit"></td>
</tr>
</tbody>
</table>
</form>
</div>
<?php
}

