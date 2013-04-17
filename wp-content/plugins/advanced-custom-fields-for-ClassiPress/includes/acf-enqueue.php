<?php
/**
 * Here placed functions for enqueue styles and js
 *
 * @since 1.1
 */


function acf_enqueue_css() {

	if ( is_page_template( 'tpl-add-new.php' ) || is_page_template( 'tpl-profile.php' ) || is_page_template( 'tpl-edit-item.php' ) || is_page_template( 'tpl-registration.php' ) ) {

		wp_enqueue_style( 'jqueryUI_datepicker_style', ACF_URL . '/css/jquery-ui-1.8.21.datepicker.css', 99 );

		if ( is_page_template( 'tpl-registration.php' ) ) {

			// supported child themes
			$childs = array(
				'Headline Blue - Classipress Child Theme' => 'headline',
				'Headline Green - Classipress Child Theme' => 'headline',
				'Headline Orange - Classipress Child Theme' => 'headline',
				'Headline Purple - Classipress Child Theme' => 'headline',
				'Headline Red - Classipress Child Theme' => 'headline'
			);

			$theme = wp_get_theme();
			$themename = $theme->name;
			$filename = ( array_key_exists( $themename, $childs ) ) ? '/css/acf-' . $childs[ $themename ] . '-reg.css' : false;

			if ( $filename && file_exists( ACF_DIR . $filename ) )
				wp_enqueue_style( 'acf_forms_style', ACF_URL . $filename, 99 );
			else
				wp_enqueue_style( 'acf_forms_style', ACF_URL . '/css/acf-default-reg.css', 99 );
		}

		if ( is_page_template( 'tpl-edit-item.php' ) ) {

			wp_register_style( 'acf-edit-item', ACF_URL . '/css/acf-edit-item.css', array( 'at-color' ) );
			wp_enqueue_style( 'acf-edit-item' );
		}

	} elseif ( is_singular( 'ad_listing' ) ) {
		$theme = wp_get_theme();
		$themename = $theme->name;
		$filename = ACF_URL . '/css/acf-' . $themename . '-single-ad.css';
		if ( file_exists( $filename ) )
			wp_enqueue_style( 'acf_forms_style', $filename, 99 );
		else
			wp_enqueue_style( 'acf_forms_style', ACF_URL . '/css/acf-default-single-ad.css', 99 );
	}
}

function acf_enqueue_js() {

	if ( is_page_template( 'tpl-add-new.php' ) || is_page_template( 'tpl-profile.php' ) || is_page_template( 'tpl-edit-item.php' ) || is_page_template( 'tpl-registration.php' ) ) {

		// send arrays to js
		$msgs_arr = get_option( 'acf_error_msgs' );
		$date_obj = get_option( 'acf_date_picker' );
		$js_msgs = json_encode( $msgs_arr );
		$js_date = json_encode( $date_obj );
		// print script

		print "<script type='text/javascript'>\n";
		print "/* <![CDATA[ */\n";
		print "var validate_msgs = $js_msgs;\n";
		print "var dateOptions = $js_date;\n";
		print "/* ]]> */\n";
		print "</script>\n";

		/* if (is_page_template('tpl-profile.php')){
		  if (get_option('cp_allow_html') == 'yes') {
		  wp_enqueue_script('tiny_mce', get_bloginfo('url').'/wp-includes/js/tinymce/tiny_mce.js', array('jquery'), '3.0');
		  wp_enqueue_script('wp-langs-en', get_bloginfo('url').'/wp-includes/js/tinymce/langs/wp-langs-en.js', array('jquery'), '3241-1141');
		  }
		  } */

		if ( is_page_template( 'tpl-registration.php' ) ) {
			wp_enqueue_script( 'validate', get_bloginfo( 'template_directory' ) . '/includes/js/validate/jquery.validate.min.js', array( 'jquery' ), '1.8.1' );
			wp_enqueue_script( 'acf_script', ACF_URL . '/js/acf-script.js', 99 );
		}
		$inc = includes_url();
		wp_enqueue_script( 'wp_enqueue_script', $inc . '/js/jquery/ui/jquery.ui.datepicker.min.js', 99 );
		//wp_enqueue_script('datepicker_script', get_bloginfo('template_directory').'/includes/js/ui.datepicker.js');
		wp_enqueue_script( 'jqueryUI_locale_script', ACF_URL . '/js/jquery-ui-i18n.min.js', 99 );
		wp_enqueue_script( 'acf_datepicker_script', ACF_URL . '/js/acf-date-picker.js', 99 );
		wp_enqueue_script( 'morevalidate', get_bloginfo( 'template_directory' ) . '/includes/js/validate/additional-methods.js' );
		wp_enqueue_script( 'ValidateFix_script', ACF_URL . '/js/ValidateFix.js', 99 );
	}
}
?>