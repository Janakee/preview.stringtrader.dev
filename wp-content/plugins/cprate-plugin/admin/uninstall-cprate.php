<?php
/*
Classipress Rated Author
Current Version: 1.2
Plugin Author: Julio Gallegos
Author URL: http://myclassipro.com
*/

function uninstall_cprate() {
	global $wpdb;
	
	if (!defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
		exit();
	}
	
    delete_option('cprate_header1'); 
    delete_option('cprate_header2'); 	
    delete_option('cprate_rating');
    delete_option('cprate_feedback');
    delete_option('cprate_ratedby');
    delete_option('cprate_date');
    delete_option('cprate_sub_comment');
    delete_option('cprate_req_feedback_mod');
    delete_option('cprate_req_feedback_mod_msg');
    delete_option('cprate_enable_edit_feedback');
    delete_option('cprate_poster_edit_time');
    delete_option('cprate_req_replyback_mod');
    delete_option('cprate_enable_replyback');
    delete_option('cprate_enable_edit_replyback');
    delete_option('cprate_auth_edit_time');	
	delete_option('cprate_rules');
	delete_option('cprate_records_show');
	delete_option('cprate_sort_order');
	delete_option('cprate_need_mod');
	delete_option('cprate_records_show_toprate');
	delete_option('cprate_sort_toprate');
	
	unregister_setting( 'cprate-settings-group', 'cprate_header1' );
	unregister_setting( 'cprate-settings-group', 'cprate_header2' );
	unregister_setting( 'cprate-settings-group', 'cprate_rating' );
	unregister_setting( 'cprate-settings-group', 'cprate_feedback' );
	unregister_setting( 'cprate-settings-group', 'cprate_ratedby' );
	unregister_setting( 'cprate-settings-group', 'cprate_date' );
	unregister_setting( 'cprate-settings-group', 'cprate_sub_comment' );
	unregister_setting( 'cprate-settings-group', 'cprate_req_feedback_mod' );
	unregister_setting( 'cprate-settings-group', 'cprate_req_feedback_mod_msg' );
	unregister_setting( 'cprate-settings-group', 'cprate_enable_edit_feedback' );
	unregister_setting( 'cprate-settings-group', 'cprate_poster_edit_time' );
	unregister_setting( 'cprate-settings-group', 'cprate_req_replyback_mod' );
	unregister_setting( 'cprate-settings-group', 'cprate_enable_replyback' );
	unregister_setting( 'cprate-settings-group', 'cprate_enable_edit_replyback' );
	unregister_setting( 'cprate-settings-group', 'cprate_auth_edit_time' );
	unregister_setting( 'cprate-settings-group', 'cprate_rules' );
	unregister_setting( 'cprate-settings-group2', 'cprate_records_show' );
	unregister_setting( 'cprate-settings-group2', 'cprate_sort_order' );
	unregister_setting( 'cprate-settings-group2', 'cprate_need_mod' );
	unregister_setting( 'cprate-settings-group3', 'cprate_records_show_toprate' );
	unregister_setting( 'cprate-settings-group3', 'cprate_sort_toprate' );

  	if(get_option('cprate_deletedb')) {
		delete_option('cprate_deletedb');
		unregister_setting( 'cprate-settings-group', 'cprate_deletedb' );
			$table_name = $wpdb->prefix."rate_users";
			$wpdb->query('DROP TABLE IF EXISTS '.$table_name); 
	}
}
uninstall_cprate();		  
?>