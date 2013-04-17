<?php
/*
  Plugin Name: Advanced Custom Fields For ClassiPress
  Plugin URI: http://marketplace.appthemes.com/plugins/advanced-custom-fields/
  Description: Advanced Custom Fields Plugin For ClassiPress 3.2 - 3.2.1
  Version: 1.1.2
  Release Date: 12/12/2012
  Author: Artem Frolov (dikiyforester)
  Author URI: http://forums.appthemes.com/members/dikiyforester/
 */

//error_reporting(E_ALL);
// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	die( 'Direct script access not allowed' );
}

// current ClassiPress version
$cur_ver = get_option( 'cp_version' );

// tested ClassiPress version with current Plugin version;
$tested_ver = array( '3.2-beta', '3.2-beta2', '3.2-beta3', '3.2', '3.2.1' );

global $acf_tested, $acf_version;
$acf_version = '1.1.2';
$acf_tested = FALSE;
if ( $cur_ver && in_array( $cur_ver, $tested_ver ) )
	$acf_tested = TRUE;

// var_dump($acf_tested);
define('ACF_FOLDER', 'advanced-custom-fields-for-ClassiPress');
define('ACF_DIR', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . ACF_FOLDER);
define('ACF_URL', WP_PLUGIN_URL . '/' . ACF_FOLDER);
define('ACF_TITLE', 'Advanced Custom Fields for ClassiPress');
define('ACF_MENU', 'ACF Options');

// Call classes
require( ACF_DIR . '/includes/class.acf-values.php' );
require( ACF_DIR . '/includes/class.ini.php' );
require( ACF_DIR . '/includes/class.acf-admin.php' );
require( ACF_DIR . '/includes/class.acf-help.php' );
require( ACF_DIR . '/includes/class.acf-validation.php' );

// Call hooks
require( ACF_DIR . '/includes/acf-hooks.php' );
// Call helpers
require( ACF_DIR . '/includes/acf-helpers.php' );
// Call enqueue
require( ACF_DIR . '/includes/acf-enqueue.php' );

register_activation_hook( __FILE__, array( 'ACF_Options_Page', 'install' ) );

if ( is_admin() ) { // admin actions
	add_action( 'appthemes_add_submenu_page', array( 'ACF_Options_Page', 'init_plugin_menu' ), 10 );
	add_action( 'admin_enqueue_scripts', array( 'ACF_Options_Page', 'backend_enqueue_scripts' ), 99 );
	add_action( 'admin_init', array( 'ACF_Options_Page', 'register_settings' ) );
	add_action( 'contextual_help', array( 'ACF_Help', 'contextual_help' ), 10 );
}


// Call overridden deprecated formbuilders
if ( $cur_ver == '3.2' && $acf_tested )
	require( ACF_DIR . '/deprecated/oldcode.php' );

if ( $cur_ver == '3.2.1' ) {
	// Call new filtering formbuilders
	require( ACF_DIR . '/includes/acf-formbuilders.php' );
}

/**
 * Returns an array list of values ​​for dropdowns, checkboxes and radio buttons.
 * If '$field_values' is the name of the ad field, return a list of values ​​for this field.
 */
function acf_dropdown_values( $field_values ) {

	if ( $field_values ) {

		global $wpdb;
		$ad_fields = $wpdb->get_results( "SELECT field_name, field_values FROM " . $wpdb->cp_ad_fields . " WHERE field_type = 'drop-down' OR field_type = 'radio' OR field_type = 'checkbox';" );

		foreach ( $ad_fields as $ad_field ) {

			if ( $field_values == $ad_field->field_name ) {

				$field_values = $ad_field->field_values;
			}
		}

		$field_values = explode( ',', $field_values );

		return $field_values;
	} else {

		return false;
	}
}


/**
 * Returns the extra  profile fields on the registration form.
 * On condition that the hook 'register_form' will be added to the function 'app_register_form()'.
 */
function acf_registration_form() {

	global $acf_posted;

	$ACF_class = new ACF_Options_Page;

	$profile_fields = get_option( 'acf_profile_fields' );

	echo '<div class="acfform">';

	foreach ( $profile_fields as $field_name => $profile_field ):

		unset( $input );

		if ( !isset( $profile_field[ 'reg_form_display' ] ) )
			continue;
		if ( $profile_field[ 'reg_form_display' ] != 'yes' )
			continue;

		$field_title = (isset( $profile_field[ 'title' ] ) && $profile_field[ 'title' ] != '') ? esc_html( $profile_field[ 'title' ] ) : ucfirst( str_replace( '_', ' ', $field_name ) );

		$required = acf_required( $profile_field );
		$asterix = '';

		if ( $required ) {
			$input[ 'required' ] = 'required';
			$asterix = '<span class="colour">*</span>';
		}

		if ( isset( $profile_field[ 'format' ] ) && $profile_field[ 'format' ] != '' )
			$input[ 'format' ] = $profile_field[ 'format' ];
		if ( isset( $profile_field[ 'limits' ] ) && $profile_field[ 'limits' ] != '' )
			$input[ 'limits' ] = $profile_field[ 'limits' ];
		if ( isset( $profile_field[ 'limits_attr' ] ) && $profile_field[ 'limits_attr' ] != '' )
			$input[ 'limits_attr' ] = $profile_field[ 'limits_attr' ];

		$input[ 'type' ] = $profile_field[ 'type' ];
		$input[ 'name' ] = $field_name;
		$input[ 'id' ] = $field_name;
		$input[ 'value' ] = (isset( $acf_posted[ $field_name ] )) ? $acf_posted[ $field_name ] : (( isset( $profile_field[ 'default' ] ) ) ? $profile_field[ 'default' ] : '');

		echo '<div class="clr"></div>';
		echo '<div class="rowwrap">';
		echo '<label>' . $field_title . ': ' . $asterix;
		echo '</label>';

		if ( $input[ 'type' ] == 'drop-down' || $input[ 'type' ] == 'radio' || $input[ 'type' ] == 'checkbox' ) {
			$input[ 'values' ] = acf_dropdown_values( $profile_field[ 'values' ] );
			$input[ 'pls_select' ] = '<option value="">-- ' . __( 'Select', 'appthemes' ) . ' --</option>';
		}

		if ( $input[ 'type' ] == 'text area' ) {
			$input[ 'rows' ] = 8;
			$input[ 'cols' ] = 40;
		}

		$ACF_class->acf_get_cell_html( $input );

		echo '</div>';
		if ( $input[ 'type' ] == 'drop-down' )
			echo '<br />';

		echo '<span class="description acffields">' . stripslashes( $profile_field[ 'description' ] ) . ' </span>';

	endforeach;
	echo '</div>';
	echo '<img id="calendar" title="..." alt="..." src="' . ACF_URL . '/img/calendar.gif" style="display:none;"/>';
}

/**
 * Adds extra fields on edit profile form.
 * Reworked function 'cp_profile_fields($user)'.
 */
function acf_edit_profile_form( $user ) {

	// call tinymce init code if html is enabled
	/* if ( get_option('cp_allow_html') == 'yes' )
	  appthemes_tinymce( $width=540, $height=200 ); */

	$ACF_class = new ACF_Options_Page;
	//var_dump($user);
	//$i = '';
	$profile_fields = get_option( 'acf_profile_fields' );
	$edit_profile = is_page_template( 'tpl-profile.php' );

	echo '<table class="form-table">';

	foreach ( $profile_fields as $field_name => $profile_field ) :

		if ( !isset( $profile_field[ 'edit_profile_display' ] ) && $edit_profile )
			continue;
		if ( $profile_field[ 'edit_profile_display' ] != 'yes' && $edit_profile )
			continue;

		unset( $input );

		$required = acf_required( $profile_field );
		$asterix = '';

		if ( $required ) {
			$input[ 'required' ] = 'required';
			$asterix = '<span class="colour">*</span>';
		}


		//begin writing the row and heading
		echo '<tr id="' . $field_name . '_row">';

		$field_title = (isset( $profile_field[ 'title' ] ) && $profile_field[ 'title' ] != '') ? esc_html( $profile_field[ 'title' ] ) : ucfirst( str_replace( '_', ' ', $field_name ) );

		echo '<th><label for="' . $field_name . '">' . $field_title . ': ' . $asterix . '</label></th><td>';

		if ( isset( $profile_field[ 'format' ] ) && $profile_field[ 'format' ] != '' )
			$input[ 'format' ] = $profile_field[ 'format' ];
		if ( isset( $profile_field[ 'limits' ] ) && $profile_field[ 'limits' ] != '' )
			$input[ 'limits' ] = $profile_field[ 'limits' ];
		if ( isset( $profile_field[ 'limits_attr' ] ) && $profile_field[ 'limits_attr' ] != '' )
			$input[ 'limits_attr' ] = $profile_field[ 'limits_attr' ];

		$input[ 'type' ] = $profile_field[ 'type' ];
		$input[ 'name' ] = $field_name;
		$input[ 'id' ] = $field_name;

		$field_value = (isset( $_POST[ $field_name ] )) ? $_POST[ $field_name ] : get_user_meta( $user->ID, $field_name, true );
		$input[ 'value' ] = (isset( $field_value )) ? $field_value : (( isset( $profile_field[ 'default' ] ) ) ? $profile_field[ 'default' ] : '');


		if ( $input[ 'type' ] == 'drop-down' || $input[ 'type' ] == 'radio' || $input[ 'type' ] == 'checkbox' ) {
			$input[ 'values' ] = acf_dropdown_values( $profile_field[ 'values' ] );
			$input[ 'pls_select' ] = '<option value="">-- ' . __( 'Select', 'appthemes' ) . ' --</option>';
		}

		if ( $input[ 'type' ] == 'text area' ) {
			$input[ 'rows' ] = 8;
			$input[ 'cols' ] = 40;
		}

		$ACF_class->acf_get_cell_html( $input );
		//acf_display_fields($field_id, $field_values, $i, $user);
		/* if ($input['type'] == 'text area'){
		  if ( get_option('cp_allow_html') == 'yes' ) { ?>
		  <script type="text/javascript"> <!--
		  tinyMCE.execCommand('mceAddControl', false, '<?php echo esc_attr($field_name); ?>');
		  --></script>
		  <?php }} */
		echo '<div class="clr"></div>';
		echo '<span class="description">' . stripslashes( $profile_field[ 'description' ] ) . '</span></td></tr>';

	endforeach;

	echo '</table>';
	echo '<img id="calendar" title="..." alt="..." src="' . ACF_URL . '/img/calendar.gif" style="display:none;"/>';
}

/**
 * Executes validation and returns an error during user registration
 * Uses the hook 'registration_errors'
 */
function acf_check_fields( $errors ) {
	global $acf_posted, $pagenow;
	$profile_fields = get_option( 'acf_profile_fields' );
	$error_msgs = get_option( 'acf_error_msgs' );
	$valid = new ACF_Validation();
	$methods = array( 'format', 'limits' );
	// Get (and clean) data
	$regpage = is_page_template( 'tpl-registration.php' );
	$edituser = acf_is_edit_profile_action();

	foreach ( $profile_fields as $field => $profile_field ) {

		if ( $regpage && $profile_field[ 'reg_form_display' ] != 'yes' )
			continue;
		if ( $edituser && $profile_field[ 'edit_profile_display' ] != 'yes' )
			continue;
		//if ( $pagenow == 'profile.php' && $profile_field['edit_profile_display'] != 'yes' )
		//continue;
		if ( $pagenow == 'user-new.php' ) // we are don't want load this function, until there not appeared hook for add extra fields in WP admin
			continue;

		// If field type is checkbox
		if ( $profile_field[ 'type' ] == 'checkbox' || $profile_field[ 'type' ] == 'radio' ) {

			if ( $profile_field[ 'format' ] == 'required' && !isset( $_POST[ $field ] ) )
				$errors->add( 'format_' . $field, '<strong>' . __( 'ERROR', 'appthemes' ) . '</strong>: ' . $profile_field[ 'title' ] . ' - ' . $error_msgs[ 'required' ] );

			$acf_posted[ $field ] = isset( $_POST[ $field ] ) ? $_POST[ $field ] : '';
			continue;
		}

		elseif ( $profile_field[ 'type' ] == 'textarea' ) {
			$acf_posted[ $field ] = stripslashes( nl2br( $_POST[ $field ] ) );
		} else {
			// For all other fields
			$acf_posted[ $field ] = stripslashes( strip_tags( trim( $_POST[ $field ] ) ) );
		}
		$field_title = (isset( $profile_field[ 'title' ] ) && $profile_field[ 'title' ] != '') ? esc_html( $profile_field[ 'title' ] ) : ucfirst( str_replace( '_', ' ', $field ) );
		foreach ( $methods as $method ) {

			if ( $profile_field[ $method ] && method_exists( 'ACF_Validation', 'v_' . $profile_field[ $method ] ) ) {

				$method_name = $profile_field[ $method ];
				$v_method = 'v_' . $method_name;
				$method_attr = $method . '_attr';

				if ( isset( $profile_field[ $method_attr ] ) ) {

					$method_attr = explode( ',', $profile_field[ $method_attr ] );
					$error_msg = str_replace( '{0}', $method_attr[ 0 ], $error_msgs[ $method_name ] );
					if ( isset( $method_attr[ 1 ] ) )
						$error_msg = str_replace( '{1}', $method_attr[ 1 ], $error_msg );
				} else {

					$method_attr = '';
					$error_msg = $error_msgs[ $method_name ];
				}

				if ( !$valid->$v_method( $acf_posted[ $field ], $method_attr ) )
					$errors->add( $method . '_' . $field, '<strong>' . __( 'ERROR', 'appthemes' ) . '</strong>: ' . $field_title . ' - ' . $error_msg );
			}
		}
	}

	return $errors;
}


/**
 * Processes the registration form with extra profile fields and returns errors/redirects to a page
 * On condition that the hook 'app_after_create_user' will be added to the function 'app_process_register_form()'.
 */
function acf_update_user_meta( $user_id ) {
	global $acf_posted;
	$profile_fields = get_option( 'acf_profile_fields' );

	if ( is_array( $acf_posted ) ) {

		foreach ( $acf_posted as $field => $value ) {

			if ( is_array( $value ) )
				$value = sanitize_text_field( implode( ",", $value ) );

			elseif ( isset( $profile_fields[ $field ][ 'transform' ] ) )
				$value = acf_transform( $value, $profile_fields[ $field ][ 'transform' ] );


			update_user_meta( $user_id, $field, $value );
		}
	}
}

/**
 * Save the ACF user profile fields
 *
 */
function acf_profile_fields_save( $user_id ) { //TODO: split acf profile field save functions
	$profile_fields = get_option( 'acf_profile_fields' );
	$edit_profile = acf_is_edit_profile_action();

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;


	foreach ( $profile_fields as $field_id => $field_values ) :

		if ( $field_values[ 'edit_profile_display' ] != 'yes' && $edit_profile )
			continue;

		if ( isset( $_POST[ $field_id ] ) ) { // Sanitaize fields before saving
			$value = $_POST[ $field_id ];

			if ( is_array( $value ) )
				$value = stripslashes( strip_tags( implode( ",", $value ) ) );

			elseif ( $field_values[ 'type' ] == 'textarea' )
				$value = stripslashes( nl2br( $value ) );
			else
				$value = stripslashes( strip_tags( $value ) );

			if ( isset( $field_values[ 'transform' ] ) && !is_array( $value ) )
				$value = acf_transform( $value, $field_values[ 'transform' ] );


			update_user_meta( $user_id, $field_id, $value );
		}
	endforeach;
}


// calls by hook 'appthemes_notices' in step2.php
function acf_ad_fields_transform_new() {

	global $postvals, $option_name;

	if ( !is_page_template( 'tpl-add-new.php' ) || !$postvals || !$option_name )
		return false;

	$ad_fields = get_option( 'acf_ad_fields' );

	foreach ( $postvals as $key => $postval ) {

		if ( isset( $ad_fields[ $key ][ 'transform' ] ) && $ad_fields[ $key ][ 'transform' ] != '' && !is_array( $postval ) && $postval )
			$postvals[ $key ] = acf_transform( $postval, $ad_fields[ $key ][ 'transform' ] );
	}

	update_option( $option_name, $postvals );
}

function acf_ad_fields_transform_edit() {
//TODO: split ad transform functions

	if ( !acf_is_edit_ad_action() )
		return;

	$ad_fields = get_option( 'acf_ad_fields' );

	foreach ( $_POST as $key => $postval ) {

		if ( isset( $ad_fields[ $key ][ 'transform' ] ) && $ad_fields[ $key ][ 'transform' ] != '' && !is_array( $postval ) && $postval )
			$_POST[ $key ] = acf_transform( $postval, $ad_fields[ $key ][ 'transform' ] );
	}
}


/**
 * Remake cp_get_ad_details function.
 * display ONLY NECESSARY custom fields on the single ad page, by default they are placed in the list area
 */
if ( !function_exists( 'cp_get_ad_details' ) && $acf_tested ) { //

	function cp_get_ad_details( $postid, $catid, $locationOption = 'list' ) {
		global $wpdb, $current_user;

		//ACF display options:
		$acf_display_options = get_option( 'acf_ad_fields' );
		$acf_profile_fields = get_option( 'acf_profile_fields' );
		$acf_u_can_see = acf_u_can_see( get_the_author_meta( 'ID' ), $current_user->ID ); // used for private fields
		//$all_custom_fields = get_post_custom($post->ID);
		// see if there's a custom form first based on catid.
		$fid = cp_get_form_id( $catid );

		// if there's no form id it must mean the default form is being used
		if ( !($fid) ) {

			// get all the custom field labels so we can match the field_name up against the post_meta keys
			$sql = "SELECT field_label, field_name, field_type FROM $wpdb->cp_ad_fields";

		} else {

			// now we should have the formid so show the form layout based on the category selected
			$sql = $wpdb->prepare("SELECT f.field_label, f.field_name, f.field_type, m.field_pos "
				. "FROM $wpdb->cp_ad_fields f "
				. "INNER JOIN $wpdb->cp_ad_meta m "
				. "ON f.field_id = m.field_id "
				. "WHERE m.form_id = %s "
				. "ORDER BY m.field_pos asc",
				$fid);

		}

		$results = $wpdb->get_results( $sql );

////////////////////////////////ACF ad advanced content details
		if ( $results ) {
			if ( $locationOption == 'content' ) {

				$output_fields = '';
				$there_are = false;

				$output_fields .= '<div id="acf-ad-details" class="custom-text-area dotted acf-ad-details"><h3>' . esc_html( __( 'Full Details', 'appthemes' ) ) . '</h3>';
				$output_fields .= '<ul id="acf-details-tbl">';


				foreach ( $results as $result ) :


					// If field is private and user is not Admin or Author then do nothing
					if ( isset( $acf_display_options[ $result->field_name ][ 'private' ] ) && $acf_display_options[ $result->field_name ][ 'private' ] == 'yes' && !$acf_u_can_see )
						continue;

					if ( isset( $acf_display_options[ $result->field_name ][ 'single_ad_cont' ] ) && $acf_display_options[ $result->field_name ][ 'single_ad_cont' ] == 'yes' ) {

						// now grab all ad fields and print out the field label and value
						$post_meta_val = get_post_meta( $postid, $result->field_name, true );

						if ( !empty( $post_meta_val ) ) {

							if ( $result->field_name != 'cp_price' && $result->field_name != 'cp_currency' && $result->field_type != "text area" ) {

								$output_fields .= '<li class="acf-details-item" id="acf-' . $result->field_name . '">';
								$output_fields .= '<span class="acf-details-label">' . esc_html( translate( $result->field_label, 'appthemes' ) ) . '</span>';
								$output_fields .= '<span class="acf-details-sep">: </span>';

								if ( $result->field_type == "checkbox" ) {

									$there_are = true;
									$post_meta_val = get_post_meta( $postid, $result->field_name, false );
									$output_fields .= '<span class="acf-details-val">' . appthemes_make_clickable( implode( ", ", $post_meta_val ) ) . '</span>';
								} else {

									$there_are = true;
									$output_fields .= '<span class="acf-details-val">' . appthemes_make_clickable( $post_meta_val ) . '</span>';
								}

								$output_fields .= '</li>';
							}
						}
					}

				endforeach;

				$output_fields .= '</ul>';
				$output_fields .= '</div>';

				if ( $there_are )
					echo $output_fields;
			}
		}
//////////////////ACF ad advanced content details
//ACF profile fields display condition:
		if ( $acf_profile_fields ) {
			foreach ( $acf_profile_fields as $profile_field_name => $profile_field ) :

				// If field is private and user is not Admin or Author then do nothing
				if ( isset( $profile_field[ 'private' ] ) && $profile_field[ 'private' ] == 'yes' && !$acf_u_can_see )
					continue;

				$post_meta_val = get_the_author_meta( $profile_field_name );
				if ( !empty( $post_meta_val ) && isset( $profile_field[ 'single_ad_display' ] ) && $profile_field[ 'single_ad_display' ] == 'yes' ) {

					if ( $locationOption == 'list' && $profile_field[ 'type' ] != "text area" )
						echo '<li id="' . $profile_field_name . '"><span>' . $profile_field[ 'title' ] . ':</span> ' . appthemes_make_clickable( $post_meta_val ) . '</li>'; // make_clickable is a WP function that auto hyperlinks urls
					elseif ( $locationOption == 'content' && $profile_field[ 'type' ] == "text area" )
						echo '<div id="' . $profile_field_name . '" class="custom-text-area dotted"><h3>' . $profile_field[ 'title' ] . '</h3>' . stripslashes( nl2br( appthemes_make_clickable( $post_meta_val ) ) ) . '</div>'; // make_clickable is a WP function that auto hyperlinks urls
				}
			endforeach;
		}

		if ( $results ) {
			if ( $locationOption == 'list' ) {
				foreach ( $results as $result ) :

//ACF ad fields display condition:
					// If field is private and user is not Admin or Author then do nothing
					if ( isset( $acf_display_options[ $result->field_name ][ 'private' ] ) && $acf_display_options[ $result->field_name ][ 'private' ] == 'yes' && !$acf_u_can_see )
						continue;

					if ( isset( $acf_display_options[ $result->field_name ][ 'single_ad_display' ] ) && $acf_display_options[ $result->field_name ][ 'single_ad_display' ] == 'yes' ) {

						// now grab all ad fields and print out the field label and value
						$post_meta_val = get_post_meta( $postid, $result->field_name, true );

						if ( !empty( $post_meta_val ) )
							if ( $result->field_type == "checkbox" ) {
								$post_meta_val = get_post_meta( $postid, $result->field_name, false );
								echo '<li id="' . $result->field_name . '"><span>' . esc_html( translate( $result->field_label, 'appthemes' ) ) . ':</span> ' . appthemes_make_clickable( implode( ", ", $post_meta_val ) ) . '</li>'; // make_clickable is a WP function that auto hyperlinks urls}
							} elseif ( $result->field_name != 'cp_price' && $result->field_name != 'cp_currency' && $result->field_type != "text area" ) {
								echo '<li id="' . $result->field_name . '"><span>' . esc_html( translate( $result->field_label, 'appthemes' ) ) . ':</span> ' . appthemes_make_clickable( $post_meta_val ) . '</li>'; // make_clickable is a WP function that auto hyperlinks urls
							}
					}
				endforeach;
			} elseif ( $locationOption == 'content' ) {
				foreach ( $results as $result ) :

//ACF display condition:
					// If field is private and user is not Admin or Author then do nothing
					if ( isset( $acf_display_options[ $result->field_name ][ 'private' ] ) && $acf_display_options[ $result->field_name ][ 'private' ] == 'yes' && !$acf_u_can_see )
						continue;

					if ( isset( $acf_display_options[ $result->field_name ][ 'single_ad_display' ] ) && $acf_display_options[ $result->field_name ][ 'single_ad_display' ] == 'yes' ) {

						// now grab all ad fields and print out the field label and value
						$post_meta_val = get_post_meta( $postid, $result->field_name, true );

						if ( !empty( $post_meta_val ) )
							if ( $result->field_name != 'cp_price' && $result->field_name != 'cp_currency' && $result->field_type == 'text area' )
								echo '<div id="' . $result->field_name . '" class="custom-text-area dotted"><h3>' . esc_html( translate( $result->field_label, 'appthemes' ) ) . '</h3>' . stripslashes( nl2br( appthemes_make_clickable( $post_meta_val ) ) ) . '</div>'; // make_clickable is a WP function that auto hyperlinks urls

					}
				endforeach;
			}
			else {
				// uncomment for debugging
				// echo 'Location Option Set: ' . $locationOption;
			}
		} else {

			echo __( 'No ad details found.', 'appthemes' );
		}
	}

}


function acf_get_loop_options() {
	if ( !is_archive() && !is_search() )
		return;
	global $acf_loop_ad_top, $acf_loop_ad_bottom, $acf_loop_ad_top_p, $acf_loop_ad_bottom_p, $acf_private, $acf_private_p;

	if ( isset( $acf_loop_ad_top ) )
		$acf_loop_ad_top = "";
	if ( isset( $acf_loop_ad_bottom ) )
		$acf_loop_ad_bottom = "";
	if ( isset( $acf_loop_ad_top_p ) )
		$acf_loop_ad_top_p = "";
	if ( isset( $acf_loop_ad_bottom_p ) )
		$acf_loop_ad_bottom_p = "";

	$acf_private = array( );
	$acf_private_p = array( );

	$acf_ad_options = get_option( 'acf_ad_fields' );

	foreach ( $acf_ad_options as $key => $ad_field_option ) {
		if ( isset( $ad_field_option[ 'loop_ad_top' ] ) && $ad_field_option[ 'loop_ad_top' ] == 'yes' )
			$acf_loop_ad_top[ ] = $key;
		if ( isset( $ad_field_option[ 'loop_ad_bottom' ] ) && $ad_field_option[ 'loop_ad_bottom' ] == 'yes' )
			$acf_loop_ad_bottom[ ] = $key;
		if ( isset( $ad_field_option[ 'private' ] ) && $ad_field_option[ 'private' ] == 'yes' )
			$acf_private[ ] = $key;
	}

	$acf_profile_options = get_option( 'acf_profile_fields' );

	foreach ( $acf_profile_options as $key => $ad_profile_option ) {
		if ( isset( $ad_profile_option[ 'loop_ad_top' ] ) && $ad_profile_option[ 'loop_ad_top' ] == 'yes' )
			$acf_loop_ad_top_p[ ] = $key;
		if ( isset( $ad_profile_option[ 'loop_ad_bottom' ] ) && $ad_profile_option[ 'loop_ad_bottom' ] == 'yes' )
			$acf_loop_ad_bottom_p[ ] = $key;
		if ( isset( $ad_profile_option[ 'private' ] ) && $ad_profile_option[ 'private' ] == 'yes' )
			$acf_private_p[ ] = $key;
	}
}


/**
 * Unhook Appthemes core functions.
 */
function acf_unhook_appthemes_functions() {
	remove_action( 'appthemes_after_post_title', 'cp_ad_loop_meta' );
}



/**
 * Changing the design of ads in the archives.
 *
 */
function acf_ad_loop_meta_top() {
	if ( is_page() || is_singular( APP_POST_TYPE ) )
		return; // don't do ad-meta on pages
	global $post, $acf_loop_ad_top, $acf_loop_ad_top_p, $acf_private, $acf_private_p, $current_user;

	if ( $post->post_type == 'page' )
		return;

	$acf_u_can_see = false; // use for private fields
	if ( current_user_can( 'manage_options' ) || get_the_author_meta( 'ID' ) == $current_user->ID )
		$acf_u_can_see = true;

	$separator = '&nbsp;&nbsp;|&nbsp;&nbsp;';

	echo '<p class="post-meta">';

	$display_cat = '<span class="folder">';
	if ( get_the_category() )
		$display_cat .= the_category( ', ' );
	else
		$display_cat .= get_the_term_list( $post->ID, APP_TAX_CAT, '', ', ', '' );
	$display_cat .= '</span>';

	echo $display_cat;
?>
	        <span class="owner"><?php if ( get_option( 'cp_ad_gravatar_thumb' ) == 'yes' ) appthemes_get_profile_pic( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_email' ), 16 ) ?><?php if ( !is_author() ) the_author_posts_link(); ?></span> | <span class="clock"><span><?php echo appthemes_date_posted( $post->post_date ); ?></span></span>
	<?php
	if ( isset( $acf_loop_ad_top ) ) {
		foreach ( $acf_loop_ad_top as $value ) {

			// If field is private and user is not Admin or Author then do nothing
			if ( in_array( $value, $acf_private ) && !$acf_u_can_see )
				continue;

			if ( get_post_meta( $post->ID, $value, false ) ) {
				$post_meta_val = get_post_meta( $post->ID, $value, false );
				echo $separator;
				echo '<span class="' . $value . '">';
				echo implode( ", ", $post_meta_val );
				echo '</span>';
			}
		}
	}
	if ( isset( $acf_loop_ad_top_p ) ) {
		foreach ( $acf_loop_ad_top_p as $value ) {

			// If field is private and user is not Admin or Author then do nothing
			if ( in_array( $value, $acf_private_p ) && !$acf_u_can_see )
				continue;

			if ( get_the_author_meta( $value ) ) {
				echo $separator;
				echo '<span class="' . $value . '">';
				echo get_the_author_meta( $value );
				echo '</span>';
			}
		}
	}

	//add action after title in loop ad meta (in line with author and category)
	acf_loop_top( $post );

	echo '</p>';
}


function acf_ad_loop_bottom() {
	if ( is_page() || is_singular( APP_POST_TYPE ) )
		return; // don't do ad-meta on pages
	global $post, $acf_loop_ad_bottom, $acf_loop_ad_bottom_p, $acf_private, $acf_private_p, $current_user;
	if ( $post->post_type == 'page' )
		return;

	$acf_u_can_see = false; // use for private fields
	if ( current_user_can( 'manage_options' ) || get_the_author_meta( 'ID' ) == $current_user->ID )
		$acf_u_can_see = true;

	$separator = '&nbsp;&nbsp;|&nbsp;&nbsp;';

	echo '<p class="post-custom-meta" style="float:right; color:#AFAFAF;font-size:11px;margin:0;padding:4px 0;text-shadow:0 1px 0 #FFFFFF;border-bottom:0;">';

	if ( isset( $acf_loop_ad_bottom ) ) {
		foreach ( $acf_loop_ad_bottom as $value ) {

			// If field is private and user is not Admin or Author then do nothing
			if ( in_array( $value, $acf_private ) && !$acf_u_can_see )
				continue;

			if ( get_post_meta( $post->ID, $value, false ) ) {
				$post_meta_val = get_post_meta( $post->ID, $value, false );
				echo '<span class="' . $value . '">';
				echo implode( ", ", $post_meta_val );
				echo '</span>';
				echo $separator;
			}
		}
	}
	if ( isset( $acf_loop_ad_bottom_p ) ) {
		foreach ( $acf_loop_ad_bottom_p as $value ) {

			// If field is private and user is not Admin or Author then do nothing
			if ( in_array( $value, $acf_private_p ) && !$acf_u_can_see )
				continue;

			if ( get_the_author_meta( $value ) ) {
				echo '<span class="' . $value . '">';
				echo get_the_author_meta( $value );
				echo '</span>';
				echo $separator;
			}
		}
	}

	//add action after description in loop ad meta (in line with posted and total viewed)
	acf_loop_bottom( $post );

	echo '</p>';
}


/**
 * This function add new ad fields to plugin settings immediately after their creation in CP dashboard
 */
function acf_ad_field_added() {
	if ( isset( $_GET[ 'page' ] ) && 'fields' == $_GET[ 'page' ] ) {
		if ( isset( $_GET[ 'action' ] ) && 'addfield' == $_GET[ 'action' ] ) {
			if ( isset( $_POST[ 'submitted' ] ) ) {
				$fldname = cp_make_custom_name( $_POST[ 'field_label' ] );
				$acf_ad_fields = get_option( 'acf_ad_fields' );
				$ACF_values = new ACF_Values;
				$ad_field_props = $ACF_values->values( 'ad_field_props', true );

				foreach ( $ad_field_props as $ad_field_prop ) {
					$optvar = '';
					switch ( $ad_field_prop ) {
						case 'name':
							continue 2;
							break;
						case 'new_ad_display':
						case 'edit_ad_display':
						case 'single_ad_display':
							$optvar = 'yes';
							break;
						default:
							break;
					}
					$acf_ad_fields[ $fldname ][ $ad_field_prop ] = $optvar;
				}
				update_option( 'acf_ad_fields', $acf_ad_fields );
			}
		}
	}
}

/**
 * Add profile fields to the author page and author tab info.
 */
function acf_author_info( $location ) {

	global $current_user, $curauth;

	if ( !$curauth ) {

		if ( 'sidebar-user' == $location )
			$curauth = $current_user;
		if ( 'sidebar-ad' == $location )
			$curauth = get_userdata( get_the_author_id() );
	}

	$textareas = '';

	$acf_u_can_see = acf_u_can_see( $curauth->ID, $current_user->ID );

	if ( 'page' == $location ) {
		$disp_opt = 'author_page_display';
		$class = "author-info";
	} elseif ( 'sidebar-user' == $location ) {
		$disp_opt = 'user_sidebar_display';
		$class = "user-details";
	} elseif ( 'sidebar-ad' == $location ) {
		$disp_opt = 'user_sidebar_ad_display';
		$class = "member";
	}
	else
		continue;

	echo '<ul class="' . esc_attr( $class ) . '">';

	$profile_fields = get_option( 'acf_profile_fields' );
	if ( $profile_fields )
		foreach ( $profile_fields as $field_name => $profile_field ) :

			// If field is private and user is not Admin or Author then do nothing
			if ( isset( $profile_field[ 'private' ] ) && $profile_field[ 'private' ] == 'yes' && !$acf_u_can_see )
				continue;

			if ( !empty( $curauth->$field_name ) && isset( $profile_field[ $disp_opt ] ) && $profile_field[ $disp_opt ] == 'yes' )
				if ( $profile_field[ 'type' ] != "text area" )
					echo '<li class="author-' . esc_attr( $field_name ) . '"><strong class="title-' . esc_attr( $field_name ) . '">' . esc_html( $profile_field[ 'title' ] ) . ':</strong> ' . esc_html( $curauth->$field_name ) . '</li>';
				else
					$textareas .= '<h3 class="dotted">' . esc_html( $profile_field[ 'title' ] ) . '</h3><p>' . stripslashes( nl2br( appthemes_make_clickable( $curauth->$field_name ) ) ) . '</p>'; //no html but multilines

				endforeach;

	echo '</ul>';

	// textareas section only for author page (not sidebar)
	if ( 'page' == $location ) {

		echo '</div>'; // close "author-main" div

		echo $textareas;

		echo '<div class="clear">'; //open new div to not break template
	}
}

// Neutralizes the plugin if versions of the plugin and theme are not compatible.
if ( $acf_tested ) {
	add_action( 'appthemes_notices', 'acf_ad_fields_transform_new', 10 );
	add_action( 'init', 'acf_ad_fields_transform_edit' );
	add_action( 'appthemes_add_submenu_page', 'acf_ad_field_added', 10 );
	add_action( 'wp_print_styles', 'acf_enqueue_css', 99 );
	add_action( 'wp_print_scripts', 'acf_enqueue_js', 99 );
	add_action( 'register_form', 'acf_registration_form', 9 );
	add_filter( 'registration_errors', 'acf_check_fields', 10, 1 );
	add_action( 'user_profile_update_errors', 'acf_check_fields', 10, 1 );
	add_action( 'user_register', 'acf_update_user_meta' );
	add_action( 'show_user_profile', 'acf_edit_profile_form', 1 );
	add_action( 'edit_user_profile', 'acf_edit_profile_form' );
	add_action( 'personal_options_update', 'acf_profile_fields_save' );
	add_action( 'edit_user_profile_update', 'acf_profile_fields_save' );
	add_action( 'appthemes_before_loop', 'acf_get_loop_options' );
	add_action( 'init', 'acf_unhook_appthemes_functions' );
	add_action( 'appthemes_after_post_title', 'acf_ad_loop_meta_top' );
	add_action( 'appthemes_after_post_content', 'acf_ad_loop_bottom' );
	add_action( 'template_redirect', array( 'ACF_Options_Page', 'acf_export_ini' ) );
	add_action( 'cp_author_info', 'acf_author_info' );
}
?>