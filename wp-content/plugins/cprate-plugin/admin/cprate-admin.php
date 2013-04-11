<?php
/*
Classipress Rated Author
Current Version: 1.2
Plugin Author: Julio Gallegos
Author URL: http://myclassipro.com
*/

function register_cprate_settings() {
	//register our settings
	register_setting( 'cprate-settings-group', 'cprate_header1' );
	register_setting( 'cprate-settings-group', 'cprate_header2' );
	register_setting( 'cprate-settings-group', 'cprate_rating' );
	register_setting( 'cprate-settings-group', 'cprate_feedback' );
	register_setting( 'cprate-settings-group', 'cprate_ratedby' );
	register_setting( 'cprate-settings-group', 'cprate_date' );
	register_setting( 'cprate-settings-group', 'cprate_sub_comment' );
	register_setting( 'cprate-settings-group', 'cprate_req_feedback_mod' );
	register_setting( 'cprate-settings-group', 'cprate_req_feedback_mod_msg' );
	register_setting( 'cprate-settings-group', 'cprate_enable_edit_feedback' );
	register_setting( 'cprate-settings-group', 'cprate_poster_edit_time' );
	//register_setting( 'cprate-settings-group', 'cprate_req_replyback_mod' );
	register_setting( 'cprate-settings-group', 'cprate_enable_replyback' );
	register_setting( 'cprate-settings-group', 'cprate_enable_edit_replyback' );
	register_setting( 'cprate-settings-group', 'cprate_auth_edit_time' );
	register_setting( 'cprate-settings-group', 'cprate_rules' );
	register_setting( 'cprate-settings-group', 'cprate_deletedb' );
	register_setting( 'cprate-settings-group2', 'cprate_records_show' );
	register_setting( 'cprate-settings-group2', 'cprate_sort_order' );
	register_setting( 'cprate-settings-group2', 'cprate_need_mod' );
	register_setting( 'cprate-settings-group3', 'cprate_records_show_toprate' );
	register_setting( 'cprate-settings-group3', 'cprate_sort_toprate' );
}

function cprate_setup_options() {
    if (get_option('cprate_header1') == '') update_option('cprate_header1', 'Author Rating');
    if (get_option('cprate_header2') == '') update_option('cprate_header2', 'Author Score');
    if (get_option('cprate_rating') == '') update_option('cprate_rating', 'Rating');
    if (get_option('cprate_feedback') == '') update_option('cprate_feedback', 'Feedback');
    if (get_option('cprate_ratedby') == '') update_option('cprate_ratedby', 'Rated By');
    if (get_option('cprate_date') == '') update_option('cprate_date', 'Date');
    if (get_option('cprate_sub_comment') == '') update_option('cprate_sub_comment', 'Reply back from Author');
    if (get_option('cprate_req_feedback_mod_msg') == '') update_option('cprate_req_feedback_mod_msg', 'This Feedback is awaiting Moderation');
    
	if (get_option('cprate_poster_edit_time') == '') update_option('cprate_poster_edit_time', 5);
    if (get_option('cprate_auth_edit_time') == '') update_option('cprate_auth_edit_time', 5);
    if (get_option('cprate_records_show') == '') update_option('cprate_records_show', 10);
    if (get_option('cprate_sort_order') == '') update_option('cprate_sort_order', "default");
    if (get_option('cprate_records_show_toprate') == '') update_option('cprate_records_show_toprate', 10);
    if (get_option('cprate_sort_toprate') == '') update_option('cprate_sort_toprate', "default");
}


function cprate_plugin_menu() {
	add_menu_page( 'CP Rate Author Settings', 'CP Rate Author', 'manage_options', 'cprate-settings-page', 'cprate_plugin_options', plugins_url('/css/images/full-star-small.png' , dirname(__FILE__) ) );
	add_submenu_page('cprate-settings-page', '', '', "manage_options", 'cprate-settings-page', "cprate_plugin_options");
	add_submenu_page('cprate-settings-page', __('Settings', 'cprate'), __('Settings', 'cprate'), "manage_options", 'cprate-settings-page', "cprate_plugin_options");
	add_submenu_page("cprate-settings-page", __('Moderate Feedback', 'cprate'), __('Moderate', 'cprate'), "manage_options", "moderate-feedback", "cprate_plugin_feedbacktable");
	add_submenu_page("cprate-settings-page", __('Highest Rated', 'cprate'), __('Highest Rated', 'cprate'), "manage_options", "highest-rated", "cprate_plugin_highestrated");
	
	

	wp_register_style( 'cprate-admin-css', plugins_url('/cprate-admin.css', __FILE__) );
	wp_register_script('cprate-admin-js', plugins_url('/cprate-admin.js', __FILE__) , array('jquery'), '2.50', true);
	add_action( 'admin_init', 'register_cprate_settings' );
	add_action( 'admin_init', 'cprate_setup_options' );

}

add_action('admin_enqueue_scripts', 'cprate_plugin_admin_scripts');  
      
function cprate_plugin_admin_scripts() {  
	// Include JS/CSS only if we're on our options page  
	if (cprate_plugin_screen()) { 
		wp_enqueue_style( 'cprate-admin-css' );
		wp_enqueue_script( 'cprate-admin-js' );
	} 
} 
 
// Check if we're on our options page  
function cprate_plugin_screen() {  
	$screen = get_current_screen();  
	if (is_object($screen) && ($screen->id == 'toplevel_page_cprate-settings-page' || $screen->id == 'cp-rate-author_page_moderate-feedback' || $screen->id == 'cp-rate-author_page_highest-rated')) {  
		return true;  
	} else {  
		return false;  
	}  
}

function cprate_top_rated($id) {
	$ratecal = cprate_calculate($id);
	$count = round($ratecal);
	$rateperc = $ratecal/5 * 100;
	$toprated .= print_all_stars($count);
	$toprated .='</span> (<span id="cprate_per">'.$rateperc. '%</span>) </h3>';

	return $toprated;
}


function delete_feedback() {
	global $wpdb;
		$id = $_POST['row_id'];
		$wpdb->query("DELETE FROM $wpdb->prefix" . "rate_users WHERE id =" .$id);
	exit;
}
add_action('wp_ajax_cprate_delete_feedback', 'delete_feedback');

function approve_feedback() {
	global $wpdb;
		$id = $_POST['row_id'];
		$wpdb->query("UPDATE $wpdb->prefix" . "rate_users SET feedback_moderate=0 WHERE id =" .$id);
	exit;

}
add_action('wp_ajax_cprate_approve_feedback', 'approve_feedback');

function cprate_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

?>
<div class="wrap" id="cprate_wrapper">
<h2><?php echo "CP Rate Author Settings Page"; ?></h2>
<form method="post" action="options.php">
<?php 	
	settings_fields( 'cprate-settings-group' );
    do_settings_sections( 'cprate-settings-group' ); 
	$checked = ' checked="checked" ';	
	if( isset($_GET['settings-updated']) ) { ?>
        <div id="message" class="updated">
            <p><strong><?php _e('Settings saved.') ?></strong></p>
        </div>
<?php } ?>
    	<h3 class="cprate-header"><?php echo __("CP User Rate Fields", "cprate") ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Header 1 - Author Page:<br /><span>(Author Rating:)<span></th>
                <td><input type="text" name="cprate_header1" value="<?php echo get_option('cprate_header1'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Header 2 - Author Page:<br /><span>(Author Score:)<span></th>
                <td><input type="text" name="cprate_header2" value="<?php echo get_option('cprate_header2'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Column 1 - Header:<br /><span>(Rating)<span></th>
                <td><input type="text" name="cprate_rating" value="<?php echo get_option('cprate_rating'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Column 2 - Header:<br /><span>(Feedback)<span></th>
                <td><input type="text" name="cprate_feedback" value="<?php echo get_option('cprate_feedback'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Column 3 - Header:<br /><span>(Rated By)<span></th>
                <td><input type="text" name="cprate_ratedby" value="<?php echo get_option('cprate_ratedby'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Column 4 - Header:<br /><span>(Date)<span></th>
                <td><input type="text" name="cprate_date" value="<?php echo get_option('cprate_date'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Sub-Comment Text:<br /><span>(Reply back from Author:)<span></th>
                <td><input type="text" name="cprate_sub_comment" value="<?php echo get_option('cprate_sub_comment'); ?>" /></td>
            </tr>
        </table>
        <h3 class="cprate-header"><?php echo __("User Posting Feedback Options", "cprate") ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row" class="wide" ><?php echo __("Require Feedback to be Moderated:", "cprate") ?></th>			
                <td class="clicker" style="padding-right:25px"><input type="checkbox" name="cprate_req_feedback_mod" <?php if(get_option('cprate_req_feedback_mod')) echo $checked  ?>  /></td>
                <td><?php echo __("Message to display: ", "cprate") ?><input size="35" type="text" name="cprate_req_feedback_mod_msg" value="<?php echo get_option('cprate_req_feedback_mod_msg'); ?>" /></td>
            </tr>
        </table>
        <!--<table class="form-table">
            <tr valign="top">
                <th scope="row" class="wide"><?php echo __("Allow Poster to edit feedback:", "cprate") ?></th>			
                <td class="clicker"><input type="checkbox" name="cprate_enable_edit_feedback" <?php  if(get_option('cprate_enable_edit_feedback')) echo $checked  ?>  /></td>
                <td><?php //echo __("Time allowed to edit: ", "cprate") ?><input size="4" type="text" name="cprate_poster_edit_time" value="<?php // echo get_option('cprate_poster_edit_time'); ?>" /><?php //echo __(" (in minutes)", "cprate") ?>
				</td>
            </tr>
        </table>-->
        <h3 class="cprate-header"><?php echo __("Author Page Options", "cprate") ?></h3>
        <!--<table class="form-table">
            <tr valign="top">
                <th scope="row" class="wide"><?php // echo __("Require reply-back to be Moderated:", "cprate") ?></th>			
                <td><input type="checkbox" name="cprate_req_replyback_mod" <?php // if(get_option('cprate_req_replyback_mod')) echo $checked  ?>  /></td>
            </tr>
        </table> --->
        <table class="form-table">
            <tr valign="top">
                <th scope="row" class="wide"><?php echo __("Enable Author to Reply Back to Feedback:", "cprate") ?></th>			
                <td><input type="checkbox" name="cprate_enable_replyback" <?php if(get_option('cprate_enable_replyback')) echo $checked  ?>  /></td>
            </tr>
        </table>
        <!--<table class="form-table">
            <tr valign="top">
                <th scope="row" class="wide"><?php // echo __("Allow Author to edit reply-back:", "cprate") ?></th>			
                <td class="clicker"><input type="checkbox" name="cprate_enable_edit_replyback" <?php // if(get_option('cprate_enable_edit_replyback')) echo $checked  ?>  /></td>
                <td><?php // echo __("Time allowed to edit: ", "cprate") ?>
                	<input size="4" type="text" name="cprate_auth_edit_time" value="<?php // echo get_option('cprate_auth_edit_time'); ?>" /><?php // echo __(" (in minutes)", "cprate") ?>
				</td>
            </tr>
        </table>-->
        <h3 class="cprate-header"><?php echo __("Feedback Disclaimer to User ", "cprate") ?> <span>(HTML Markup is allowed)</span></h3>
        <table class="form-table">
            <tr valign="top">
                <td><textarea rows="15" cols="90" name="cprate_rules"><?php echo get_option('cprate_rules'); ?></textarea> </td>
            </tr>
        </table>
        <h3 class="cprate-header"><?php echo __("Unistall Options", "cprate") ?> <span>(This cannot be undone)</span></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row" class="wide"><?php echo __("Delete Database and Options on Uninstall:", "cprate") ?></th>			
                <td><input type="checkbox" name="cprate_deletedb" <?php if(get_option('cprate_deletedb')) echo $checked  ?>  /></td>
            </tr>
        </table>
    	<p class="submit">
    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    	</p>
</form>
</div>
<?php } 

function cprate_plugin_feedbacktable() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}?>
    
	<h2><?php echo __('CP Rate Moderate Feedback', 'cprate') ?></h2>
   
<?php
global $wpdb, $sort_by;

$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$limit = get_option('cprate_records_show');
$offset = ( $pagenum - 1 ) * $limit;
$sortby_id = 'by';
$sort_this ='';
$need_mod = '';
$enable_fbmod = get_option('cprate_req_feedback_mod');
$needmod_btn = get_option('cprate_need_mod');
$sort_by = get_option('cprate_sort_order');

	if($sort_by == "default") {
		$sort_this = ' p.id DESC ';
	} elseif($sort_by == "date_newest") {
		$sort_this = ' p.cprate_time DESC ';
	} elseif($sort_by == "date_oldest") {
		$sort_this = ' p.cprate_time ASC ';
	} elseif($sort_by == "author_az") {
		$sortby_id = 'to';
		$sort_this = ' user_login ASC ';
	} elseif($sort_by == "author_za") {
		$sortby_id = 'to';
		$sort_this = ' user_login DESC ';	
	} elseif($sort_by == "rater_az") {
		$sort_this = ' user_login ASC ';
	} elseif($sort_by == "rater_za") {
		$sort_this = ' user_login DESC ';
	}
	
	if($enable_fbmod == true && ($needmod_btn)) { 
		$need_mod = " AND p.feedback_moderate=1";
	}

$entries = $wpdb->get_results( "SELECT *, user_login FROM $wpdb->prefix" . "rate_users AS p INNER JOIN wp_users on given_".$sortby_id."_user_id = wp_users.ID ".$need_mod. " ORDER BY ".$sort_this. " LIMIT $offset, $limit" );

echo '<div id="cprate_moderate" class="wrap">';
 
?>
<div id="cprate_sorter">
<form method="post" action="options.php" id="cprate_sort_form">
    <?php
		settings_fields( 'cprate-settings-group2' );
    	do_settings_sections( 'cprate-settings-group2' ); 
	?>
       <span><?php echo __('Sort By: ', 'cprate') ?></span><select name="cprate_sort_order">
        <option value="">Sort Order</option>
			<option value="default" <?php selected( $sort_by,  "default"); ?>>Default</option>
        	<option value="date_newest" <?php selected( $sort_by, "date_newest"); ?>>Newest</option>
        	<option value="date_oldest" <?php selected( $sort_by, "date_oldest"); ?>>Oldest</option>
        	<option value="author_az" <?php selected( $sort_by, "author_az"); ?>>Author A-Z</option>
        	<option value="author_za" <?php selected( $sort_by, "author_za"); ?>>Author Z-A</option>
        	<option value="rater_az" <?php selected( $sort_by, "rater_az"); ?>>Rated By A-Z</option>
        	<option value="rater_za" <?php selected( $sort_by, "rater_za"); ?>>Rated By Z-A</option>
		</select>
        <?php if(get_option('cprate_req_feedback_mod')) { ?>
			<input name="cprate_need_mod" type="checkbox" <?php if($needmod_btn) { echo 'checked="checked"';} ?> /><span><?php echo __('Need Moderations Only', 'cprate') ?></span>
		<?php } ?>
        <img id='reload_me' title="<?php echo _e('reload'); ?>" src="<?php echo plugins_url('/css/images/refresh_icon.png' , dirname(__FILE__) ) ?>" alt="" />
    </form>
</div>
<div id="cprate_showthis">
<form method="post" action="options.php">
    <?php
		settings_fields( 'cprate-settings-group' );
    	do_settings_sections( 'cprate-settings-group' ); 
		$sel = get_option('cprate_records_show');
	?>
       <span><?php echo __('Records to show: ', 'cprate') ?></span><select name="cprate_records_show">
			<option value="10" <?php selected( $sel, 10 ); ?>>10</option> 
			<option value="20" <?php selected( $sel, 20 ); ?>>20</option>
        	<option value="30" <?php selected( $sel, 30 ); ?>>30</option>
        	<option value="40" <?php selected( $sel, 40 ); ?>>40</option>
        	<option value="50" <?php selected( $sel, 50 ); ?>>50</option>
        	<option value="100" <?php selected( $sel, 100 ); ?>>100</option>
       </select>
    	<input type="submit" class="button-primary" value="<?php _e('Go') ?>" />
    </form>
</div>    	

<table class="widefat" id="cprate_moderate">
    <thead>
        <tr>
            <th scope="col" class="cprate_col1"><?php echo __('Record Id', 'cprate') ?></th>
            <th scope="col" class="cprate_col2"><?php echo __('Author Name', 'cprate') ?></th>
            <th scope="col" class="cprate_col3"><?php echo get_option('cprate_rating') ?></th>
            <th scope="col" class="cprate_col4"><?php echo get_option('cprate_feedback') ?></th>
            <th scope="col" class="cprate_col5"><?php echo get_option('cprate_ratedby') ?></th>
            <th scope="col" class="cprate_col6"><?php echo get_option('cprate_date') ?></th>
            <th scope="col" class="cprate_col7"><?php echo __('Action', 'cprate') ?></th>
        </tr>
    </thead>
 
 
    <tbody>
        <?php if( $entries ) { ?>
 
            <?php
            $count = 1;
            $class = '';
            foreach( $entries as $entry ) {
                $class = ( $count % 2 == 0 ) ? ' class="alternate"' : '';
				$pid = ' id="' .$entry->id . '"';
				$mod_req = get_option('cprate_req_feedback_mod');
            ?>
 
            <tr<?php echo $pid . $class;  ?>>
                <td><?php echo $entry->id; ?></td>
                <td><?php $name = get_userdata( $entry->given_to_user_id ); echo $name->user_login; ?></td>
                <td><?php echo print_all_stars($entry->star_rating); ?></td>
                <td><?php echo $entry->feed_back; ?></td>
                <td><?php $name = get_userdata( $entry->given_by_user_id ); echo $name->user_login; ?></td>
                <td><?php echo date('Y-m-d', strtotime($entry->cprate_time)) ?></td>
                <td class="action">
					<img alt="" title="<?php echo __('delete this record') ?>" class="remove_fb" src="<?php echo plugins_url('/css/images/delete.png' , dirname(__FILE__) ); ?>" />
					<?php if($mod_req == true && $entry->feedback_moderate == 1) { ?>
						<img alt="" title="<?php echo __('approve this feedback') ?>" class="approve_fb" src="<?php echo plugins_url('/css/images/checkbox.png' , dirname(__FILE__) ); ?>" />
                        <!--<input type="checkbox" value="<?php //echo $pid;  ?>" />-->
					<? } ?>
                </td>
            </tr>
 
            <?php
                $count++;
            }
            ?>
 
        <?php } else { ?>
        <tr>
            <td colspan="2">No posts yet</td>
        </tr>
        <?php } ?>
    </tbody>
</table>
 
<?
 
$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM $wpdb->prefix" . "rate_users" .$need_mod );
$num_of_pages = ceil( $total / $limit );
$page_links = paginate_links( array(
    'base' => add_query_arg( 'pagenum', '%#%' ),
    'format' => '',
    'prev_text' => __( '&laquo;', 'aag' ),
    'next_text' => __( '&raquo;', 'aag' ),
    'total' => $num_of_pages,
    'current' => $pagenum
) );
 
if ( $page_links ) {
    echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
}
 
echo '</div>';	
	
} 
/////////////////       Highest Rated Author Page     ///////////////////////////////////////
function cprate_plugin_highestrated() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}?>
    
	<h2><?php echo __('Highest Rated Author Page', 'cprate') ?></h2>
   
<?php
global $wpdb, $sort_by2;

$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$limit = get_option('cprate_records_show');
$offset = ( $pagenum - 1 ) * $limit;
$sort_toprate = get_option('cprate_sort_toprate');
	if($sort_toprate == "default") {
		$sort_toprate_this = ' p.given_to_user_id DESC ';
	} elseif ($sort_toprate == "highest_rated") {
		$sort_toprate_this = ' avg_rating DESC ';
	} elseif ($sort_toprate == "lowest_rated") {
		$sort_toprate_this = ' avg_rating ASC ';
	} elseif ($sort_toprate == "author_az") {
		$sort_toprate_this = ' user_login ASC ';
	} elseif ($sort_toprate == "author_za") {
		$sort_toprate_this = ' user_login DESC ';
	} 
	 
$entries = $wpdb->get_results( "SELECT p.given_to_user_id, avg(p.star_rating/5) * 100 as avg_rating  
								FROM $wpdb->prefix" . "rate_users AS p INNER JOIN wp_users on p.given_to_user_id = wp_users.ID GROUP BY given_to_user_id ORDER By " .$sort_toprate_this. " LIMIT $offset, $limit" );

echo '<div id="cprate_moderate" class="wrap">';
 
?>
<div id="cprate_sorter">
<form method="post" action="options.php" id="cprate_sort_form">
    <?php
		settings_fields( 'cprate-settings-group3' );
    	do_settings_sections( 'cprate-settings-group3' ); 
	?>
       <span><?php echo __('Sort By: ', 'cprate') ?></span><select name="cprate_sort_toprate">
			<option value="default" <?php selected( $sort_toprate,  "default"); ?>>Default</option>
            <option value="highest_rated" <?php selected( $sort_toprate,  "highest_rated"); ?>>Highest Rated</option>
			<option value="lowest_rated" <?php selected( $sort_toprate, "lowest_rated"); ?>>Lowest Rated</option>
        	<option value="author_az" <?php selected( $sort_toprate, "author_az"); ?>>Author A-Z</option>
        	<option value="author_za" <?php selected( $sort_toprate, "author_za"); ?>>Author Z-A</option>
		</select>
        <img id='reload_me' title="<?php echo _e('reload'); ?>" src="<?php echo plugins_url('/css/images/refresh_icon.png' , dirname(__FILE__) ); ?>" alt="" />
    </form>
</div>
<div id="cprate_showthis">
<form method="post" action="options.php">
    <?php
		settings_fields( 'cprate-settings-group3' );
    	do_settings_sections( 'cprate-settings-group3' ); 
		$sel = get_option('cprate_records_show_toprate');
		$enable_fbmod = get_option('cprate_req_feedback_mod');
	?>
       <span><?php echo __('Records to show: ', 'cprate') ?></span><select name="cprate_records_show">
			<option value="10" <?php selected( $sel, 10 ); ?>>10</option> 
			<option value="20" <?php selected( $sel, 20 ); ?>>20</option>
        	<option value="30" <?php selected( $sel, 30 ); ?>>30</option>
        	<option value="40" <?php selected( $sel, 40 ); ?>>40</option>
        	<option value="50" <?php selected( $sel, 50 ); ?>>50</option>
        	<option value="100" <?php selected( $sel, 100 ); ?>>100</option>
        	<option value="All" <?php selected( $sel, "All" ); ?>>All</option>
       </select>
    	<input type="submit" class="button-primary" value="<?php _e('Go') ?>" />
    </form>
</div>    	
<?php if($enable_fbmod) { ?>
	<div id="mod-on-error"><?php echo __('* These Scores and Ratings do not include Un-Moderated Feedback', 'cprate') ?></div>
<?php } ?>
<table class="widefat" id="cprate_moderate">
    <thead>
        <tr>
            <th scope="col" class="cprate_col4"><?php echo __('Author Name', 'cprate') ?></th>
            <th scope="col" class="cprate_col4"><?php echo get_option('cprate_rating') ?></th>
            <th scope="col" class="cprate_col4"><?php echo __('Total Score', 'cprate') ?></th>
        </tr>
    </thead>
 
 
    <tbody>
        <?php if( $entries ) { ?>
 
            <?php
            $count = 1;
            $class = '';
            foreach( $entries as $entry ) {
                $class = ( $count % 2 == 0 ) ? ' class="alternate"' : '';
				$author = $entry->given_to_user_id;
            ?>
            <tr>
                <td><?php $name = get_userdata($author); echo $name->user_login; ?></td>
                <td><?php echo cprate_top_rated($author); ?></td>
                <td><?php echo cprating_count($author); ?></td>
            </tr>
 
            <?php
                $count++;
            }
            ?>
 
        <?php } else { ?>
        <tr>
            <td colspan="2">No posts yet</td>
        </tr>
        <?php } ?>
    </tbody>
</table>
 
<?
 
$total = $wpdb->get_var( "SELECT COUNT(DISTINCT given_to_user_id) FROM $wpdb->prefix" . "rate_users" );
$num_of_pages = ceil( $total / $limit );
$page_links = paginate_links( array(
    'base' => add_query_arg( 'pagenum', '%#%' ),
    'format' => '',
    'prev_text' => __( '&laquo;', 'aag' ),
    'next_text' => __( '&raquo;', 'aag' ),
    'total' => $num_of_pages,
    'current' => $pagenum
) );
 
if ( $page_links ) {
    echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
}
 
echo '</div>';	
	
} 
add_action( 'admin_menu', 'cprate_plugin_menu' );

 
?>