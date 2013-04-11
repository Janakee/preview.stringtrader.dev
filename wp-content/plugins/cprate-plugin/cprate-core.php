<?php  
/*
Plugin Name: Classipress Rate Author
Plugin URI: http://myclassipro.com/
Description: Plugin to rate Authors and submit Feedback with a sub comment feature
Version: 1.2.1
Author: Julio Gallegos
Author URI: http://myclassipro.com/
License: GPL
*/

global $cprate_db_version;
$cprate_db_version = "1.0";

//// Creates our table to store user ratings
function cprate_install() {
   global $wpdb;
   global $rate_db_version;

   $table_name = $wpdb->prefix . "rate_users";
   $sql = "CREATE TABLE $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  cprate_time datetime NOT NULL,
	  given_to_user_id MEDIUMINT,
	  given_by_user_id MEDIUMINT,
	  star_rating TINYINT(5),
	  feed_back TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
	  post_feed_back TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
	  feedback_moderate TINYINT(1),
	  UNIQUE KEY id (id)
    );";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
   add_option("cprate_db_version", $cprate_db_version);
   cprate_install_plugin();
}

function cprate_install_plugin(){
	update_option('cprate_enable_replyback', 'true');
	update_option('cprate_rules', 
		'<h3>The Rules</h3>
The site Administrator(s) reserves the right, but undertake no duty, to review, edit, move, or delete any feedback submitted by users, in our sole discretion, without notice, including but not limited to the following reasons:<ul>
<li>Do not post obscene, hateful, offensive, defamatory, abusive, harassing or profane material. </li>
<li>Do not engage in personal attacks. </li> 
<li>Do not post personal information such as e-mail address, telephone number, street address or any other personal information. </li>
<li>Do not post links to another sites. </li>
<li>Keep your feedback on topic for the particular Author you are leaving it for. </li> 
<li>Do not use multiple accounts to post Feedback. Your IP address maybe recorded. </li>
	</ul>');
}
register_activation_hook(__FILE__,'cprate_install');

function cprate_uninstall_plugin(){
	require_once dirname( __FILE__ ) . '/admin/uninstall-cprate.php';
}
register_uninstall_hook( __FILE__, 'cprate_uninstall_plugin' );

if ( is_admin() ) {
	require_once dirname( __FILE__ ) . '/admin/cprate-admin.php';
}
add_action('appthemes_after_post', 'add_adpage_vars');
add_action( 'wp_enqueue_scripts', 'cprate_scripts' );
add_action('wp_ajax_cprate_post_feedback_action', 'cprate_post_feedback');
add_action('wp_ajax_save_sub_comments', 'save_sub_comments_callback');
add_action('wp_print_footer_scripts', 'add_ajax_vars');

//// Loads our CSS and JS files only on pages were we need them
function cprate_scripts() {	
	wp_register_style( 'cprate-css', plugins_url('/css/cprate.css', __FILE__) );
	wp_register_script('cprate_plugin', plugins_url('/js/cprate-jqplugin.js', __FILE__) , array('jquery'), '2.50', true);
	wp_register_script('cprate_js', plugins_url('/js/cprate.js', __FILE__) , array('jquery'), '2.50', true);
	wp_register_script('cprate_ad_js', plugins_url('/js/cprate-adpage.js', __FILE__) , array('jquery'), '2.50', true);
	
	if(is_author()) {
		wp_enqueue_style( 'cprate-css' );
		wp_enqueue_script( 'cprate_plugin' );
       	wp_enqueue_script( 'cprate_js' );
	} elseif(is_single()) {
		wp_enqueue_style( 'cprate-css' );
       	wp_enqueue_script( 'cprate_ad_js' );
	}
}

function add_adpage_vars() {
	global $post;
		$author_id=$post->post_author;
		echo '<script type="text/javascript">
				/* <![CDATA[ */
					var cprateAdPage = {"cprate_id":"'.$author_id .'"}; 
				/* ]]> */
				</script>';
}

function add_ajax_vars() {
		echo '<script type="text/javascript">
				/* <![CDATA[ */
					var cprateAjax = {"cprate_ajax":"'.admin_url( 'admin-ajax.php' ).'"}; 
				/* ]]> */
				</script>';
}


function cprate_calculate($id) {
	global $wpdb;
		$enable_fbmod = get_option('cprate_req_feedback_mod');
		$fbswitch ='';
		
		if($enable_fbmod) { $fbswitch = " AND feedback_moderate=0";}
		
		$rating = $wpdb->get_var("SELECT avg(star_rating) FROM $wpdb->prefix" . "rate_users where given_to_user_id=".$id. $fbswitch); 		
	return round($rating,2);
}

function cprating_count($id) {
	global $wpdb;
		$enable_fbmod = get_option('cprate_req_feedback_mod');
		$fbswitch ='';
		
		if($enable_fbmod) { $fbswitch = " AND feedback_moderate=0";}
		
		$cprating_count = $wpdb->get_var("SELECT count(star_rating) FROM $wpdb->prefix" . "rate_users where given_to_user_id=".$id. $fbswitch); 		
	return $cprating_count;
}

function the_rating($id) {
	echo cprate_calculate($id);
}

function print_enabled_stars($count) {
	$output = "";
		for($i=1;$i<=$count;$i++) {
			$output .= "<img class='cp-rate-stars' src='". plugins_url('/css/images/full-star-small.png', __FILE__) ."' />";
		}
	return $output;
}

function print_disabled_stars($count) {
	$output = "";
		for($j=$count+1;$j<=5;$j++){
			$output .= "<img class='cp-rate-stars' src='". plugins_url('/css/images/empty-star-small.png', __FILE__) ."' />";
		}
	return $output;
}

function print_all_stars($count) {
	return  print_enabled_stars($count).print_disabled_stars($count);
}

function cprate_my_rating($id, $tagid) {
		$ratecal = cprate_calculate($id);
		$count = round($ratecal);
		$rateperc = $ratecal/5 * 100;
		$myrating = '<h3 id="'.$tagid.'" class="cprate-myrating">' .get_option('cprate_header1'). ': <span id="cprate_avgrating">'; 
		$myrating .= print_all_stars($count);
		$myrating .='</span> (<span id="cprate_per">'.$rateperc. '%</span>) </h3>';
	return $myrating;
}

function cprate_my_adpage_rating() {
		$this_id = $_POST['this_id'];
		$ratecal = cprate_calculate($this_id);
		$count = round($ratecal);
		$rateperc = $ratecal/5 * 100;
		$myrating = '<li><div class="cprate-myrating">' .get_option('cprate_header1'). ': <span id="cprate_avgrating">'; 
		$myrating .= print_all_stars($count);
		$myrating .='</span> (<span id="cprate_per">'.$rateperc. '%</span>) </div></li>';
		$myrating .='<li><div class="cprate-myrating">' . get_option('cprate_header2')  . ' ( <span id="total_count">' .cprating_count($this_id). '</span> )</div></li>';
	echo $myrating;
exit;
}
add_action('wp_ajax_cprate_my_rating_action', 'cprate_my_adpage_rating');
add_action('wp_ajax_nopriv_cprate_my_rating_action', 'cprate_my_adpage_rating');

function cprate_feedback_form() {
		$btn = __("Leave Feedback","cprate");
		$thankyou = __("Thank You","cprate") . '!';
		$loader = plugins_url('/css/images/cprate_progress_bar.gif', __FILE__);
		$msg = __("Submitting Feedback","cprate") . '...';
		
		$content = '<div id="cprate_wrapper">
						<input id="leave_feedback" type="button" value="' .$btn. '" />
						<div id="cprate_feedback_form" style="display:none">
						<pre class="cprate-pre">'. get_option('cprate_rules') . '</pre>
							<div id="stars-wrapper"></div>
							<div id="your_rating">Your Rating: <span id="rating_show">not set</span></div>
							
							Feedback comments<br /><textarea style="width:300px; height:75px; margin-bottom:5px" name="cprate_comments" id="cprate_comments"></textarea>
							<br />
							<input style="margin-left:0px" type="button" value="Submit" onclick="SubmitFeedback()"  /><span id="cprate-error"></span>
							</div>
							<div class="pad5"></div>
							</div>
							<div id="cprate_loading" style="display:none">
								<span id="cprate_thankyou">' . $thankyou . '</span>
								<img src="' .  $loader . '" alt="" />
								<span id="cprate_msg">' . $msg . '</span>
							</div>';
	echo $content;
}


function feedback_already_exists($id) {
	global $wpdb;
		$user = wp_get_current_user();	
		$given_by_user_id = $user->ID;
		$myrows = $wpdb->get_results( "SELECT 1 from $wpdb->prefix" . "rate_users where given_to_user_id=".$id." and given_by_user_id =".$given_by_user_id);
		foreach ($myrows as $row) {
			return true;
		}
return false;
}

function cprate_feedback_history($id) {
	global $wpdb, $current_user;
		get_currentuserinfo();
		$userid=$current_user->ID;
		$baseurl = get_site_url();
		$enable_replyback = get_option('cprate_enable_replyback');
		$enable_fbmod = get_option('cprate_req_feedback_mod');
		$myrows = $wpdb->get_results( "SELECT *, user_login FROM $wpdb->prefix" . "rate_users AS p inner join wp_users on p.given_by_user_id = wp_users.ID where given_to_user_id=".$id." ORDER BY p.id DESC");
		
		$output = '<h3 class="cprate-myrating">' . get_option('cprate_header2')  . ' ( <span id="total_count">' .cprating_count($id). '</span> )</h3><div class="pad5"></div>';
		$output .='<table id="cprate_feedback_history">
					<thead>
						<th style="width:18%">' .get_option('cprate_rating'). '</th>
						<th style="width:65%">' .get_option('cprate_feedback'). '</th>
						<th style="width:16%">' .get_option('cprate_ratedby'). '</th>
						<th>' .get_option('cprate_date'). '</th>
					</thead>
					<tbody id="tbody_feedback_history">';
						foreach ($myrows as $row) { 
							$output .= '<tr>';
								if($enable_fbmod == true) {
									if($row->feedback_moderate == 0) {
										$output .= '<td>'.print_all_stars($row->star_rating).'</td>';
											if($enable_replyback == true && $row->post_feed_back == "" && is_user_logged_in() == true && $id==$userid) {
												$output .= '<td>' .$row->feed_back. '<span id="sub_comments_wrapper_'.$row->id.'">
															<span class="reply-back"><input type="button" value="Reply" onclick=jQuery("#span_sub_comments_'.$row->id.'").toggle(200) /></span>
															<span style="display:none" id="span_sub_comments_'.$row->id.'" class="text-sub-comments">
																<textarea id="text_sub_comments_'.$row->id.'"></textarea>
																<div class="submit_subcomments" />
																<input type="button" value="Save" onclick="save_sub_comments(&#39;'.$row->id.'&#39;)" /><span id="cprate-error-rb"></span>
															</span>
														</td>';
											} elseif($enable_replyback == true && $row->post_feed_back != "" ) {
												$output .= '<td>'.$row->feed_back.'<div class="sub-comments-quote">
																<span class="by_author">'.get_option('cprate_sub_comment').':  </span>'.$row->post_feed_back.'</div>
															</td>';
											} else {
												$output .= '<td>'.$row->feed_back.'</td>';
											}
											
									} elseif($row->feedback_moderate == 1) {
										$output .= '<td colspan="2" class="cprate-fbmod">'.get_option('cprate_req_feedback_mod_msg').'</td>';
									} 
								} 
								elseif($enable_fbmod == false) {
									$output .= '<td>'.print_all_stars($row->star_rating).'</td>';
										if($row->post_feed_back == "" && is_user_logged_in() == true && $id==$userid && $enable_replyback == true) {
											$output .= '<td>' .$row->feed_back. '<span id="sub_comments_wrapper_'.$row->id.'">
														<span class="reply-back"><input type="button" value="Reply" onclick=jQuery("#span_sub_comments_'.$row->id.'").toggle(200) /></span>
														<span style="display:none" id="span_sub_comments_'.$row->id.'" class="text-sub-comments">
															<textarea id="text_sub_comments_'.$row->id.'"></textarea>
															<div class="submit_subcomments" />
															<input type="button" value="Save" onclick="save_sub_comments(&#39;'.$row->id.'&#39;)" /><span id="cprate-error-rb"></span>
														</span>
													</td>';
										} elseif($row->post_feed_back != ""  && $enable_replyback == true) {
											$output .= '<td>'.$row->feed_back.'<div class="sub-comments-quote">
															<span class="by_author">'.get_option('cprate_sub_comment').':  </span>'.$row->post_feed_back.'</div>
														</td>';
										} else { 
											$output .= '<td>'.$row->feed_back.'</td>';
										} 
								}
							$output .= 	'<td><a href="' . $baseurl. '/author/' .$row->user_login.  '" />' .$row->user_login. '</a></td>
										<td style="font-size:11px">' .date('Y-m-d', strtotime($row->cprate_time)). '</td>
									</tr>';
						} 

		$output .= '</tbody></table>';
	
	echo $output;
}

function cprate_post_feedback() {
	global $wpdb;
	
		$post_feedback_nonce = $_POST['post_feedback_nonce'];
			if(! wp_verify_nonce( $post_feedback_nonce, 'myajax_post_feedback_nonce')) {
				die( 'You failed to be authenticated!');
			}
		$given_to_user_id = $_POST['given_to_user_id'];
		$star_rating = $_POST['star_rating'];
		$feed_back = $_POST['feed_back'];
		$cprate_time = current_time('mysql');
		
		$user = wp_get_current_user();
		$given_by_user_id = $user->ID;
		
		//$allowedit = get_option('cprate_enable_edit_feedback');
			
		$mod_req = get_option('cprate_req_feedback_mod');
		$feedback_moderate = 0;
			if($mod_req) { 
				$feedback_moderate = 1;
			}
				
		$feedbackdata = compact('cprate_time', 'given_to_user_id', 'given_by_user_id', 'star_rating', 'feed_back', 'feedback_moderate');
		$response = $wpdb->insert($wpdb->prefix . "rate_users", $feedbackdata);
		$lastid = $wpdb->insert_id;	
		
		$avrg_rating = cprate_calculate($given_to_user_id)/5 * 100;
		$newrow = "<tr class='cprate-newrow'><td>".print_all_stars($star_rating)."</td>";
					//if($allowedit) {
					//	$newrow .= "<td id='".$lastid."'>".$feed_back."</td>";
					//} else {
						$newrow .= "<td id='".$lastid."'>".$feed_back."</td>";
					//}
					$newrow .="<td><input type='hidden' id='new_rating' value='".$avrg_rating."' />" .$user->user_login. "</td>
					<td style='font-size:11px'>".date('Y-m-d', strtotime($cprate_time))."</td>
					<td style='display:none'><div>".cprate_my_rating($given_to_user_id, 'cprate-secH3')."</div></td>
				   </tr>";
	echo $newrow;
}

function save_sub_comments_callback() {	
	global $wpdb;
		$post_feedback_nonce = $_POST['post_feedback_nonce'];
			if(! wp_verify_nonce( $post_feedback_nonce, 'myajax_post_feedback_nonce')) {
				die( 'You failed to be authenticated!');
			}
		$id = $_POST['sub_comments_id'];
		$sub_comments = $_POST['sub_comments_text'];
		
		$sql="UPDATE $wpdb->prefix" . "rate_users SET post_feed_back = '".$sub_comments."' WHERE id = ".$id;
		$wpdb->query($sql);
		
		echo "<div class='sub-comments-quote'><span class='by_author'>Replied back by Seller: </span>".$sub_comments."</div>";	
}

function print_cprate($id) {
		global $current_user;
		get_currentuserinfo();
	
		$userid=$current_user->ID;
	
		echo cprate_my_rating($id, 'cprate-firstH3');
		
		if ((is_user_logged_in()) && ($id!=$userid) ) {
			if(!feedback_already_exists($id)) {
				cprate_feedback_form();
			}
		}
		cprate_feedback_history($id);


	
}

function cp_rate_users($id) {
	$star = plugins_url('cprate/css/images/empty-star-small.png');
	$count = cprating_count($id);
	$mod_req_switch = 'off';
		if(get_option('cprate_req_feedback_mod')) {
			$mod_req_switch = 'on';
		}
		wp_localize_script('cprate_js', 'cpRateVars', 
			array('stars' => $star, 
			'total_count' => $count, 
			'given_to_user_id' => $id,
			'mod_req_set' => $mod_req_switch, 
			'post_feedback_nonce' => wp_create_nonce( 'myajax_post_feedback_nonce' )));
		print_cprate($id);
}

?>