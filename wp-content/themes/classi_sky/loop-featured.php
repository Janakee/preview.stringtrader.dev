<?php
/**
 * Loop for displaying most popular ads
 *
 * @package ClassiPress
 * @author AppThemes
 *
 */

  global $pageposts,$app_version;
  $pageposts = cp_get_popular_ads();
?>

<?php appthemes_before_loop(); ?>

<?php if ( $pageposts ) : ?>

    <?php foreach ( $pageposts as $post ) : ?>

      <?php setup_postdata( $post ); ?>

      <?php appthemes_before_post(); ?>
    
	    <div class="post-block-out" <?php if($app_version>='3.2'){if ( is_sticky() ) echo 'featured'; }?>>
        
            <div class="post-block">
				
					<?php if(in_array($post->ID, get_option('sticky_posts'))) { ?>
					<span class="i_featured"><?php _e('Featured', APP_TD)?></span>
				<?php } ?>	
				
				<?php if (get_post_meta($post->ID, 'cp_ad_sold', true) == 'yes') : ?>
				<span class="i_sold"><?php _e('Sold', APP_TD)?></span>
				<?php endif; ?>
			
        
                <div class="post-left">
        
                    <?php if ( get_option('cp_ad_images') == 'yes' ) cp_ad_loop_thumbnail(); ?>
                
                </div>
        
                <div class="<?php if ( get_option('cp_ad_images') == 'yes' ) echo 'post-right'; else echo 'post-right-no-img'; ?> <?php echo get_option('cp_ad_right_class'); ?>">
                
                    <?php appthemes_before_post_title(); ?>
        
                    <h3><a href="<?php the_permalink(); ?>"><?php if ( mb_strlen( get_the_title() ) >= 75 ) echo mb_substr( get_the_title(), 0, 75 ).'...'; else the_title(); ?></a></h3>
                    
                    <div class="clr"></div>
                    
                    <?php appthemes_after_post_title(); ?>
                    
                    <div class="clr"></div>
                    
                    <?php appthemes_before_post_content(); ?>
        
                    <p class="post-desc"><?php if($app_version<='3.1.9'){ $tcontent = strip_tags( get_the_content() ); if ( mb_strlen( $tcontent ) >= 165 ) echo mb_substr( $tcontent, 0, 165 ).'...'; else echo $tcontent; }else{ echo cp_get_content_preview( 160 ); } ?></p>
                    
                    <?php appthemes_after_post_content(); ?>
                    
                    <div class="clr"></div>
        
                </div>
        
                <div class="clr"></div>
        
            </div><!-- /post-block -->
          
        </div><!-- /post-block-out -->   

      <?php appthemes_after_post(); ?>

    <?php endforeach; ?>

    <?php appthemes_after_endwhile(); ?>

<?php else: ?>

    <?php appthemes_loop_else(); ?>

<?php endif; ?>

<?php appthemes_after_loop(); ?>

<?php wp_reset_query(); ?>
