<?php
/**
 * Theme functions file
 *
 * DO NOT MODIFY THIS FILE. Make a child theme instead: http://codex.wordpress.org/Child_Themes
 *
 * @package ClassiClean
 * 
 */
 
// processes the entire ad thumbnail logic for featured ads
if ( !function_exists('tb_ad_featured_thumbnail') ) :
	function tb_ad_featured_thumbnail() {
		global $post,$app_version;

		// go see if any images are associated with the ad
    $image_id = cp_get_featured_image_id($post->ID);

		// set the class based on if the hover preview option is set to "yes"
		if (get_option('cp_ad_image_preview') == 'yes')	$prevclass = 'preview'; else $prevclass = 'nopreview';

		if ( $image_id > 0 ) {

			// get 50x50 v3.0.5+ image size
			$adthumbarray = wp_get_attachment_image($image_id, 'ad-small');

			// grab the large image for onhover preview
			$adlargearray = wp_get_attachment_image_src($image_id, 'large');
			$img_large_url_raw = $adlargearray[0];

			// must be a v3.0.5+ created ad
			if($adthumbarray) {
			
				if($app_version<='3.1.9'){
				echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="'.$prevclass.'" rel="'.$img_large_url_raw.'">'.$adthumbarray.'</a>';
				}else{
				echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="'.$prevclass.'" data-rel="'.$img_large_url_raw.'">'.$adthumbarray.'</a>';
				}

			// maybe a v3.0 legacy ad
			} else {
				$adthumblegarray = wp_get_attachment_image_src($image_id, 'ad-small');
				$img_thumbleg_url_raw = $adthumblegarray[0];
				if($app_version<='3.1.9'){
				echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="'.$prevclass.'" rel="'.$img_large_url_raw.'">'.$adthumblegarray.'</a>';
				}else{
				echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="'.$prevclass.'" data-rel="'.$img_large_url_raw.'">'.$adthumblegarray.'</a>';
				}
			}

		// no image so return the placeholder thumbnail
		} else {
			echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'"><img class="attachment-sidebar-thumbnail" alt="" title="" src="'. get_bloginfo('template_url') .'/images/no-thumb-100.jpg" /></a>';
		}

	}
endif;

// load the responsive js files correctly
if ( !function_exists('add_classisky_responsive_js') ) :
function add_classisky_responsive_js() {
	
	wp_register_script("classisky_responsive_js",get_bloginfo('stylesheet_directory')."/js/respond.js");	
	wp_enqueue_script("classisky_responsive_js");

}
endif;
if($app_version<='3.1.9'){
define( 'APP_TD', 'appthemes' );
}

// to speed things up, don't load these scripts in the WP back-end (which is the default)
if ( !is_admin() ) {
    add_action('wp_print_styles', 'add_classisky_responsive_js', 20);
}