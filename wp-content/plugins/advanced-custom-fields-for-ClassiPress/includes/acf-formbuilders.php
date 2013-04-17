<?php

/**
 * Here placed frontend form builders
 *
 * @since 1.1.1
 *
 */

/**
 *  Add filter to classipress ad field object on the start of form fields loop
 *  At this stage:
 *     1. plugin decides to show or not the field
 *     2. determines  required current field or not.
 *            If field is required, set the value to "1" for property "field_req"
 *     3. add filters to current field (or option)  html arguments
 *
 *  @since 1.1.1
 *  @param <type> $results
 *  @return <type> $results|false
 */
function acf_display_ad_form_fields( $result ) {

	if ( !$result )
		continue;

	$ad_fields = get_option( 'acf_ad_fields' );
	$ad_field = $ad_fields[ $result->field_name ];

	// This global variable will prevent filtering drop-down options, if one option is already selected.
	global $be_frugal;
	$be_frugal = false;


	if ( is_page_template( 'tpl-edit-item.php' ) && !empty( $ad_field[ 'edit_ad_display' ] ) ) {
		if ( acf_required( $ad_field ) )
			$result->field_req = '1';

		add_filter( 'cp_formbuilder_' . $result->field_name, 'acf_ad_form_fields_args', 10, 3 );
		return $result;
	}
	elseif ( is_page_template( 'tpl-add-new.php' ) && !empty( $ad_field[ 'new_ad_display' ] ) ) {
		if ( acf_required( $ad_field ) )
			$result->field_req = '1';

		if ( 'drop-down' == $result->field_type ) {

			add_filter( 'cp_formbuilder_' . $result->field_name . '_option', 'acf_ad_form_fields_args', 10, 3 );
		}
		add_filter( 'cp_formbuilder_' . $result->field_name, 'acf_ad_form_fields_args', 10, 3 );
		return $result;
	}
	else
		return false;
}

add_filter( 'cp_formbuilder_field', 'acf_display_ad_form_fields', 10, 1 );

/**
 *  Add filter to classipress ad field object in the end of form fields loop
 *  At this stage:
 *     1. Add default or inherited values if user add new ad
 *     2. Unsets predefined limitation
 *     3. Add field format to 'class' argument
 *     4. Add advanced field limitation
 *
 *  @since 1.1.1
 *  @param <type> $args
 *  @param <type> $results
 *  @param <type> $post
 *
 *  @return <type> $args Return modified array of field arguments
 */
function acf_ad_form_fields_args( $args, $result, $post ) {

	// if this is drop-down option after already selected option there's nothing to do
	global $be_frugal;
	if ( $be_frugal && isset( $args[ 'value' ] ) )
		return $args;

	$ad_fields = get_option( 'acf_ad_fields' );
	$ad_field = $ad_fields[ $result->field_name ];


	// Default values and inheritance functionality
	if ( is_page_template( 'tpl-add-new.php' ) && $post == false && !isset( $_POST[ $result->field_name ] ) ) {

		$current_user = wp_get_current_user();
		$acf_profile_fields = array_keys( get_option( 'acf_profile_fields' ) );
		$default = false;

		if ( !empty( $ad_field[ 'default' ] ) && in_array( $ad_field[ 'default' ], $acf_profile_fields ) ) {
			// Inherit profile field values
			$default = ( get_user_meta( $current_user->ID, $ad_field[ 'default' ], true ) ) ? get_user_meta( $current_user->ID, $ad_field[ 'default' ], true ) : '';
		} elseif ( !empty( $ad_field[ 'default' ] ) ) {
			// Add simple default values
			$default = $ad_field[ 'default' ];
		}

		if ( $default ) {

			switch ( $result->field_type ) {

				case 'drop-down':
					if ( isset( $args[ 'value' ] ) ) { // check, it's option or select?
						if ( !empty( $args[ 'value' ] ) && $args[ 'value' ] == $default ) {
							$args[ 'selected' ] = 'selected';
							// OK! we are find default option, thanks to all, goodbye!
							$be_frugal = true;
						}
						return $args;
					}
					break;

				case 'radio':
					if ( empty( $args[ 'value' ] ) && isset( $args[ 'checked' ] ) )
						unset( $args[ 'checked' ] );
					if ( !empty( $args[ 'value' ] ) && $args[ 'value' ] == $default )
						$args[ 'checked' ] = 'checked';
					break;

				case 'checkbox':
					$values = explode( ',', $default );
					$values = array_map( 'trim', $values );
					if ( in_array( $args[ 'value' ], $values ) )
						$args[ 'checked' ] = 'checked';
					break;

				case 'text box':
				case 'text area':
					$args[ 'value' ] = $default;
					break;

				default:
					break;
			}
		} else {
			$be_frugal = true;
		}
	}

	// unsets predefined limitation, because ACFCP users
	// can themselves set any limitation, including "minlength"
	unset( $args[ 'minlength' ] );

	// Add field format to 'class' argument
	if ( !empty( $ad_field[ 'format' ] ) )
		$args[ 'class' ] .= ' ' . $ad_field[ 'format' ];

	// Add field limitation
	if ( !empty( $ad_field[ 'limits' ] ) && !empty( $ad_field[ 'limits_attr' ] ) )
		$args[ $ad_field[ 'limits' ] ] = $ad_field[ 'limits_attr' ];

	return $args;
}

// temporary function, must be removed
function acf_add_calendar() {
	echo '<img id="calendar" title="..." alt="..." src="' . ACF_URL . '/img/calendar.gif" style="display:none;"/>';
}

add_action( 'cp_action_formbuilder', 'acf_add_calendar' )
?>