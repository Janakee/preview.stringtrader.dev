<?php
/*
Plugin Name: Slider Ultimate
Plugin URI: http://themebound.com/
Description: Show large slider on the homepage.
Version: 1.4.1
Author: Themebound
Author URI: http://themebound.com/
*/


global $wpdb, $post;

function tb_slider_enqueue() {
	wp_enqueue_style('tb-slider-css', plugin_dir_url(__FILE__).'style.css');
	wp_enqueue_script('tb-slides-js', plugin_dir_url(__FILE__).'js/slides.min.jquery.js');
}
add_action('wp_head','tb_slider_enqueue');


function tb_slider_ultimate() {
	global $wpdb, $post, $app_version;
	
	if ( get_option('cp_ad_images') == 'yes' ) : ?>
	
	<script type="text/javascript">
		jQuery(function(){
			jQuery('.shadowblockdir_large').slides({
				preload: true,
				preloadImage: '<?php echo plugin_dir_url(__FILE__); ?>images/loading.gif',
				effect: 'slide',
				crossfade: true,
				slideSpeed: 500,
				fadeSpeed: 400,
				generatePagination: false,
				play: 7500,
				pause: 2500,
				hoverPause: true
			});
		});
	</script>

<?php


	query_posts( array('post__in' => get_option('sticky_posts'), 'post_type' => APP_POST_TYPE, 'post_status' => 'publish', 'orderby' => 'rand') ); 
	 
	 if ( have_posts() ) : ?>

        <!-- featured listings -->
        <div class="shadowblock_out">

            <div class="shadowblockdir_large">
				
				<div class="slider_featured"></div>
				
				<div class="slides_container">
					
					<?php $slide_count = 0;
					while ( have_posts() && $slide_count < 7 ) : the_post(); 
						if(tb_has_image()) :
							$slide_count++;
							
					?>
					
						<div class="slide">
							
							<div class="slide_image">
								
								<a href="<?php the_permalink(); ?>"><?php tb_get_image_url_feat($post->ID, 'large', 'attachment-medium img-main'); ?></a>
								
							</div>
							
							<div class="slide_text">
								
								<h3><a href="<?php the_permalink(); ?>"><?php if ( mb_strlen( get_the_title() ) >= 30 ) echo mb_substr( get_the_title(), 0, 30 ).'...'; else the_title(); ?></a></h3>
								<?php appthemes_after_post_title(); ?>
								<p class="post-desc">
									<?php if($app_version < '3.2') {
										$tcontent = strip_tags( get_the_content() ); 
										if ( mb_strlen( $tcontent ) >= 350 ) echo mb_substr( $tcontent, 0, 350 ).'...'; 
										else echo $tcontent;
									} else
										echo cp_get_content_preview( 350 ); ?>
								</p>
								
								<?php appthemes_before_post_title(); ?>
								<button name="sa" value="Go" id="go" type="button" class="obtn btn_orange" onclick="location.href='<?php the_permalink(); ?>'"><?php _e('More info', APP_TD); ?> &raquo;</button>
								
							</div>
						
						</div>
						<?php endif;
					endwhile; 
					rewind_posts(); 
					
					?>
				</div><!-- /sliderblock -->
										
				<ul class="pagination">
						
					<?php $slide_count = 0;
					while ( have_posts() && $slide_count < 7 ) : the_post(); 
					if(tb_has_image()) :
						$slide_count++;
					?>
					
						<li><img class="arrow" src="<?php echo plugin_dir_url(__FILE__); ?>images/active-thumb-bg.png" /><a href="#"><?php cp_get_image($post->ID, 'ad-thumb'); ?></a></li>
					
					<?php endif; endwhile; ?>
					
				</ul>	
						
					
			
				

            </div><!-- /shadowblock -->

        </div><!-- /shadowblock_out -->
		
	<?php endif; ?>

    <?php wp_reset_query(); ?>


<?php endif; // end feature ad slider check 

}

function tb_has_image() {
	global $post, $wpdb;

	// go see if any images are associated with the ad
	$images = get_children( array('post_parent' => $post->ID, 'post_status' => 'inherit', 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'ID') );

	return $images;
}

function tb_get_image_url_feat($post_id = '', $size = 'medium', $class = '', $num = 1) {
	$images = get_posts(array('post_type' => 'attachment', 'numberposts' => $num, 'post_status' => null, 'post_parent' => $post_id, 'order' => 'ASC', 'orderby' => 'ID'));
	if ($images) {
		foreach ($images as $image) {
			$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
			$iarray = wp_get_attachment_image_src($image->ID, $size, $icon = false);
			$projected_height =  $iarray[2]/$iarray[1]*440;
			if($projected_height < 300) {
				$style = ' style="width:auto; height:300px"';
			}
			$img_check = '<img class="'.$class.'" src="'.$iarray[0].'" alt="'.$alt.'" title="'.$alt.'"'.$style.' />';
		}
	} else {
		if(get_option('cp_ad_images') == 'yes') { $img_check = '<img class="preview" alt="" title="" src="'. get_bloginfo('template_url') .'/images/no-thumb-sm.jpg" />'; }
	}
	echo $img_check;
}

?>