    <!-- HEADER -->
    <div class="header">

        <div class="header_top">

            <div class="header_top_res">

                <p>
                <?php echo cp_login_head(); ?>

                <a href="<?php if (get_option('cp_feedburner_url')) echo get_option('cp_feedburner_url'); else echo get_bloginfo_rss('rss2_url').'?post_type='.APP_POST_TYPE; ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/icon_rss.gif" width="16" height="16" alt="rss" class="srvicon" /></a>

                <?php if ( get_option('cp_twitter_username') ) : ?>
                    &nbsp;|&nbsp;
                    <a href="http://twitter.com/<?php echo get_option('cp_twitter_username'); ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/icon_twitter.gif" width="16" height="16" alt="tw" class="srvicon" /></a>
                <?php endif; ?>
                </p>

            </div><!-- /header_top_res -->

        </div><!-- /header_top -->


        <div class="header_main">

            <div class="header_main_bg">

                <div class="header_main_res">

                    <div id="logo">

                        <?php if ( get_option('cp_use_logo') != 'no' ) { ?>

                            <?php if ( get_option('cp_logo') ) { ?>
                                <a href="<?php echo home_url(); ?>"><img src="<?php echo get_option('cp_logo'); ?>" alt="<?php bloginfo('name'); ?>" class="header-logo" /></a>
                            <?php } else { ?>
                                <a href="<?php echo home_url(); ?>"><div class="cp_logo"></div></a>
                            <?php } ?>

                        <?php } else { ?>

                            <h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
                            <div class="description"><?php bloginfo('description'); ?></div>

                        <?php } ?>

                    </div><!-- /logo -->

                    <?php if ( get_option('cp_adcode_468x60_enable') == 'yes' ) { ?>

                        <div class="adblock">

                            <?php appthemes_header_ad_468x60();?>

                        </div><!-- /adblock -->

                    <?php } ?>

                    <div class="clr"></div>

                </div><!-- /header_main_res -->

            </div><!-- /header_main_bg -->

        </div><!-- /header_main -->


        <div class="header_menu">

            <div class="header_menu_res">

                <a href="<?php echo CP_ADD_NEW_URL ?>" class="obtn btn_orange"><?php _e('Post an Ad', 'appthemes') ?></a>

                <ul id="nav"> 
                
                    <li class="<?php if (is_home()) echo 'page_item current_page_item'; ?>"><a href="<?php echo get_option('home')?>"><?php _e('Home','appthemes'); ?></a></li>
                    <li class="mega"><a href="#"><?php _e('Categories','appthemes'); ?></a>
                        <div class="adv_categories" id="adv_categories">

                        <?php echo cp_create_categories_list( 'menu' ); ?>

                        </div><!-- /adv_categories -->
                    </li>
    
                </ul>
    
                <?php wp_nav_menu( array('theme_location' => 'primary', 'fallback_cb' => 'appthemes_default_menu', 'container' => false) ); ?>

                <div class="clr"></div>

    
            </div><!-- /header_menu_res -->

        </div><!-- /header_menu -->

    </div><!-- /header -->