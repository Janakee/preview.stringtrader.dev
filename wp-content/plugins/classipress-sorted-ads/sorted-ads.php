<?php
/**
Plugin Name: Cp Sorted Ads
Plugin URI: http://reloadweb.co.uk/
Description: Users can sort ads on category and search result pages with many options
Version: 1.0
Author: Themecycle (Reload Web Team)
Author URI:  http://reloadweb.co.uk/
*/
global $wpdb,$blog_id, $app_version;


define('TBL_ADS_SORTS',$wpdb->prefix."ads_sorts");

register_activation_hook(__FILE__,'ads_sorts_initiate_options');

function ads_sorts_initiate_options() {
	global $wpdb,$blog_id;
	$table =  TBL_ADS_SORTS;
	add_option('ads_sorts_enable_'.$blog_id, 1);
	add_option('ads_sorts_dropdown_label_'.$blog_id, 'Sort By');
	add_option('ads_sorts_dropdown_label_select_'.$blog_id, 'Select');
	$tbl_sql = "CREATE TABLE IF NOT EXISTS `$table` (
			`sort_id` int(11) NOT NULL auto_increment,
			`sort_label` varchar(255) NOT NULL,
			`sort_admin_label` varchar(255) NOT NULL,
			`sort_field_value` varchar(255) NOT NULL,
			`sort_display_order` int(3) NOT NULL,
			`sort_default` int(2) NOT NULL,
			`sort_status` int(2) NOT NULL,
			PRIMARY KEY  (`sort_id`)
		   ) COLLATE utf8_general_ci";
		$wpdb->query($tbl_sql);
		$tbl_insertsql = "INSERT INTO `$table` (`sort_id`, `sort_label`, `sort_admin_label`, `sort_field_value`, `sort_display_order`, `sort_default`, `sort_status`) VALUES (1, 'Alphabetical (A to Z)', 'Alphabetical (A to Z)', 'alphabet_az', 1, 0, 1),(2, 'Alphabetical (Z to A)', 'Alphabetical (Z to A)', 'alphabet_za', 2, 0, 1),(3, 'Featured First', 'Featured', 'featured', 3, 1, 1),(4, 'Price Lowest First', 'Price Lowest', 'price_asc', 4, 0, 1),(5, 'Price Highest First', 'Price Highest', 'price_desc', 5, 0, 1),(6, 'Newest First', 'Newest', 'new_first', 6, 0, 1),(7, 'Oldest First', 'Oldest', 'old_first', 7, 0, 1),(8, 'Popular First', 'Popular', 'popular', 8, 0, 1);";
		$wpdb->query($tbl_insertsql);
	
}
add_action('init','ads_sorts_init');
function ads_sorts_init() {
	
	add_action('admin_menu', 'ads_sorts_menu');
}


function ads_sorts_menu() {
	add_submenu_page('admin-options.php', __('Ads Sorting', 'appthemes'), __('Ads Sorting Options', 'appthemes'), 'manage_options', 'tc-ads-sorting', 'ads_sorts_admin_page_callback');
}

function ads_sorts_admin_page_callback() {
	include_once('sorted-ads-settings.php');
}

function get_default_sorts_options() {
	global $wpdb;
	$table =  TBL_ADS_SORTS;
	$sorts_options = $wpdb->get_row("SELECT * FROM $table WHERE  sort_status=1 and sort_default=1"); 
	return $sorts_options->sort_field_value;
}
add_filter('posts_clauses_request', 'ad_sticky_on_top' );
function ad_sticky_on_top( $sql ){
	global $wpdb,$blog_id;
	$default_sorting = get_default_sorts_options();
 	if( (is_tax(APP_TAX_CAT) || is_search()) &&  ($_GET['ads_sort']=='featured' || ($default_sorting=='featured' && $_GET['ads_sort']=='')) ){
		$sticky_posts = get_option('sticky_posts');
		if(!$sticky_posts) return $sql;
		$sql['fields'] = $sql['fields'].", IF( $wpdb->posts.ID IN ( ".implode(',',$sticky_posts)."), 1, 0) AS featured";
		$sql['orderby'] = "featured DESC, ". $sql['orderby'];
	}
	return $sql;
}
add_filter('posts_join', 'ads_sorts_join',50);

function ads_sorts_join($join){
    global $wpdb,$blog_id;
	$default_sorting = get_default_sorts_options();
	if( (is_tax(APP_TAX_CAT) || is_search()) && ($_GET['ads_sort']=='price_asc' || $_GET['ads_sort']=='price_desc' || ($default_sorting=='price_asc' && $_GET['ads_sort']=='') || ($default_sorting=='price_desc' && $_GET['ads_sort']==''))){
    $join .= " LEFT JOIN $wpdb->postmeta wpmeta ON ($wpdb->posts.ID = wpmeta.post_id AND wpmeta.meta_key = 'cp_price')";
	}
	if( (is_tax(APP_TAX_CAT) || is_search()) && ($_GET['ads_sort']=='popular' || ($default_sorting=='popular' && $_GET['ads_sort']==''))){
    $join .= " LEFT JOIN ".$wpdb->prefix."cp_ad_pop_total a  ON ($wpdb->posts.ID = a.postnum AND a.postcount > 0)";
	}
    return $join;
}
add_filter('posts_orderby', 'edit_ads_sorts_orderby');
function edit_ads_sorts_orderby($ads_orderby) {
global $wpdb,$blog_id;
$default_sorting = get_default_sorts_options();
if( (is_tax(APP_TAX_CAT) || is_search()) && ($_GET['ads_sort']!='' || $default_sorting!='') ){
	if($_GET['ads_sort']=='price_asc' || ($default_sorting=='price_asc' && $_GET['ads_sort']==''))
	$ads_orderby = "ABS(REPLACE(wpmeta.meta_value,',','')) ASC";
	if($_GET['ads_sort']=='price_desc' || ($default_sorting=='price_desc' && $_GET['ads_sort']==''))
	$ads_orderby = "ABS(REPLACE(wpmeta.meta_value,',','')) DESC";
	if($_GET['ads_sort']=='alphabet_az' || ($default_sorting=='alphabet_az' && $_GET['ads_sort']==''))
	$ads_orderby = "$wpdb->posts.post_title ASC";
	if($_GET['ads_sort']=='alphabet_za' || ($default_sorting=='alphabet_za' && $_GET['ads_sort']==''))
	$ads_orderby = "$wpdb->posts.post_title DESC";
	if($_GET['ads_sort']=='old_first' || ($default_sorting=='old_first' && $_GET['ads_sort']==''))
	$ads_orderby = "$wpdb->posts.post_date ASC";
	if($_GET['ads_sort']=='popular' || ($default_sorting=='popular' && $_GET['ads_sort']==''))
	$ads_orderby = "a.postcount DESC";
	}
	return $ads_orderby;
}
add_action( 'appthemes_before_loop', 'ads_sorts_dropdown' );
function ads_sorts_dropdown()
{
	global $wpdb,$blog_id;
	$table =  TBL_ADS_SORTS;
	if( (is_tax(APP_TAX_CAT) || is_search()) && get_option('ads_sorts_enable_'.$blog_id) )
	{?>
		<script type="text/javascript">
		function submit_sort_form()
		{
			document.ads_sort_form.submit();
		}
		</script>
		<style type="text/css">
		.ads_sorts { width:53%; display:table; padding:8px 10px; float:right; margin-bottom:-1px; margin-right:5px;
		-webkit-border-radius: 5px 5px 0 0; -moz-border-radius: 5px 5px 0 0;
		border-radius: 5px 5px 0 0; border:1px solid #BBBBBB; }
		.ads_sorts form { float:right; } 
		</style>
		
		<div class="ads_sorts post-block">
	 	<form name="ads_sort_form" method="get" >
		<?php
		$allquerystring=$_GET;
		foreach( $allquerystring as $key => $value){
		if($key!='ads_sort'){
		?>
		<?php 
		if(is_array($value)){
		for ($i=0; $i<count($value); $i++){?>
		<input name="<?php echo $key;?>[]" type="hidden"  value="<?php echo $value[$i];?>"  />
		<?php }
		}else{?>
		<input name="<?php echo $key;?>" type="hidden"  value="<?php echo $value;?>"  />
		<?php } ?>
		<?php
		}}?>
		<?php echo get_option('ads_sorts_dropdown_label_'.$blog_id); ?> : <select name="ads_sort" id="ads_sort" onchange="submit_sort_form();">
		<option value=""><?php echo get_option('ads_sorts_dropdown_label_select_'.$blog_id); ?></option>
		<?php
		$ads_sorts_result  = $wpdb->get_results("SELECT * FROM $table WHERE  sort_status=1 order by sort_display_order"); 
		foreach($ads_sorts_result as $ads_sorts_rec): 
		?>
		<option value="<?php echo $ads_sorts_rec->sort_field_value;?>" <?php if(($_GET['ads_sort']==$ads_sorts_rec->sort_field_value) || ($_GET['ads_sort']=='' && $ads_sorts_rec->sort_default==1) ){?> selected="selected" <?php } ?> ><?php  echo stripslashes($ads_sorts_rec->sort_label);?></option>
		<?php endforeach;?>
		</select>
		</form>
	</div>
		
		<div class="clr"></div>
 <?php 
   }
}?>