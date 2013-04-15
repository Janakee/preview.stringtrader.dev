<?php global $app_abbr,$app_version; ?>
 <?php   
if($app_version<='3.1.9'){	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta name="viewport" content="width=device-width; initial-scale=1.0">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php wp_title(''); ?></title>
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php if ( get_option('feedburner_url') <> "" ) echo get_option('feedburner_url'); else echo get_bloginfo_rss('rss2_url').'?post_type='.APP_POST_TYPE; ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php if ( is_singular() && get_option('thread_comments') ) wp_enqueue_script('comment-reply'); ?>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <?php appthemes_before(); ?>

        <div class="container">
     

		    <?php if ( get_option('cp_debug_mode') == 'yes' ) { ?><div class="debug"><h3><?php _e('Debug Mode On',APP_TD); ?></h3><?php print_r($wp_query->query_vars); ?></div><?php } ?>

            <?php appthemes_before_header(); ?>

                <?php } ?>
			 
			 
			 
			 
			 
			  <!-- HEADER -->
    <div class="header">

        <div class="header_top">
			
            <div class="header_top_res">
            
<!-- Steve created separate divs for logo, login and mobile button for positioning when  -->           
 	
 			 <div class= "login">
					                <?php echo cp_login_head(); ?>
			</div><!-- /login-->

			            
			    <div class= "logotype">
				<A href="preview.stringtrader.com" style="text-decoration:none;" class="logotype">String Trader</A>
			</div><!-- /logotype-->		            
			 
	             
	<!-- Steve commenting out rss   <?php   
	if($app_version<='3.1.9'){	?>
	  <a href="<?php if (get_option('cp_feedburner_url')) echo get_option('cp_feedburner_url'); else echo get_bloginfo_rss('rss2_url').'?post_type='.APP_POST_TYPE; ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/icon_rss.gif" width="16" height="16" alt="rss" class="srvicon" /></a>
	<?php }else{?>
                <a href="<?php echo appthemes_get_feed_url(); ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/icon_rss.gif" width="16" height="16" alt="rss" class="srvicon" /></a>
	      <?php }?>

								<?php if ( get_option('cp_twitter_username') ) : ?>
										&nbsp;|&nbsp;<a href="http://twitter.com/<?php echo get_option('cp_twitter_username'); ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/icon_twitter.gif" width="16" height="16" alt="tw" class="srvicon" /></a>
								<?php endif; ?>  -->
	
										
 

            </div><!-- /header_top_res -->
<!--Steve creating search form at top-->

	<div id="search-bar">

		<div class="searchblock_out">

			<div class="searchblock">

				<form action="<?php echo site_url('/'); ?>" method="get" id="searchform_sc" class="form_search">
<div class="form_search_sc"></div>
					<div class="searchfield">

						<input name="s" type="text" id="s" tabindex="1" class="editbox_search" style="width:<?php echo get_option('cp_search_field_width'); ?>" <?php if ( get_search_query() ) { echo 'value="'.trim(strip_tags(esc_attr(get_search_query()))).'"'; } else { ?> value="<?php _e('What are you looking for?',APP_TD); ?>" onFocus="if (this.value == '<?php _e('What are you looking for?',APP_TD); ?>') {this.value = '';}" onBlur="if (this.value == '') {this.value = '<?php _e('What are you looking for?',APP_TD); ?>';}" <?php } ?> />

					</div> <!-- /searchfield -->


					
						<button class="btn-topsearch" type="submit" tabindex="3" title="<?php _e('Search Ads', APP_TD); ?>" id="go" value="search" name="sa"><?php _e('Search Ads', APP_TD); ?></button>

					</div>

				</form>

			</div> <!-- /searchblock -->

		</div> <!-- /searchblock_out -->

	</div> <!-- /search-bar -->
        </div><!-- /header_top -->



        <div class="header_menu">
	

            <div class="header_menu_res">
		

                <a href="<?php echo CP_ADD_NEW_URL ?>" class="obtn btn_orange"><?php _e('Post an Ad', APP_TD) ?></a>
			
	     <?php   
if($app_version<='3.1.9'){	?>	


                <ul id="nav"> 
                
                    <li class="<?php if (is_home()) echo 'page_item current_page_item'; ?>"><a href="<?php echo get_option('home')?>"><?php _e('Home',APP_TD); ?></a></li>
                    <li class="mega"><a href="#" class="cat_arrow"><?php _e('Categories',APP_TD); ?></a>
                        <div class="adv_categories" id="adv_categories">
                         <?php echo cp_create_categories_list( 'menu' ); ?>

                        </div><!-- /adv_categories -->
                    </li>
    
                </ul>
    
                <?php  wp_nav_menu( array('theme_location' => 'primary', 'fallback_cb' => 'appthemes_default_menu', 'container' => false) ); ?>
	      <?php }else{?>
	      
	       <?php wp_nav_menu( array('theme_location' => 'primary', 'fallback_cb' => false, 'container' => false) ); ?>
	       <?php }?>
				<?php do_action('su_show_state_menu'); ?>
				<?php do_action('cu_show_city_menu'); ?>
				 
	

                <div class="clr"></div>

    
            </div><!-- /header_menu_res -->

        </div><!-- /header_menu -->
 <div class= "mobile_post_btn">
		
					 <a href="<?php echo CP_ADD_NEW_URL ?>" class="obtn btn_orange mobile_btn"><?php _e('Post an Ad', APP_TD) ?></a>
			</div><!-- /mobile_post_btn-->


    </div><!-- /header -->
			 
<!-- Steve commenting out stock search bar			 
 <?php   
if($app_version<='3.1.9'){	?>
<?php include_once( STYLESHEETPATH . '/includes/theme-searchbar.php' ); ?>
 <?php appthemes_after_header(); ?>
<?php  }?>		
  