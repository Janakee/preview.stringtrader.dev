<?php
/**
 * Here placed any acfcp helpers
 *
 * @since 1.1
 *
 */

/**
 * Check page if it a Edit Profile page
 * @since 1.1
 * @return bool
 */
function acf_is_edit_profile_action() {
	if ( isset( $_POST[ 'action' ] ) && 'app-edit-profile' == $_POST[ 'action' ] )
		return true;
	else
		return false;
}

/**
 * Check page if it a Edit Ad page
 * @since 1.1
 * @return bool
 */
function acf_is_edit_ad_action() {
	if ( !isset( $_POST[ 'action' ] ) || 'cp-edit-item' != $_POST[ 'action' ] || !current_user_can( 'edit_posts' ) || !$_POST[ 'custom_fields_vals' ] )
		return false;
	else
		return true;
}

/**
 * ACF checked function.
 * Gets array or delimitted string
 * DO NOT DELETE this function, used in ASP<->ACF checking
 *
 * @param string $option Current option of form field
 * @param string|array $chkarr input value
 * @since 1.0
 * @return string|null checked="checked" or null
 */
function acf_checked( $option, $chkarr ) {

	if ( is_string( $chkarr ) )
		$chkarr = explode( ',', $chkarr );

	if ( is_array( $chkarr ) ) {

		foreach ( $chkarr as $chkval ) {

			if ( trim( $chkval ) == trim( $option ) )
				return 'checked="checked"';
		}
	}
	else
		return '';
}

/**
 * Check if current user is author or admin
 *
 * @param int $author Ad poster ID
 * @param int $current_user Current User ID
 *
 * @since 1.1
 * @return bool
 */
function acf_u_can_see( $author, $current_user ) {

	if ( current_user_can( 'manage_options' ) || $author == $current_user )
		return true;
	else
		return false;
}

/**
 * Transform case of sending text
 *
 * @param string $text String to transform
 * @param string $case Transform case
 *
 * @since 1.1
 * @return string
 */
function acf_transform( $text, $case = '' ) {

	switch ( $case ) {

		case 'Capitalize':

			$text = mb_convert_case( $text, MB_CASE_TITLE, "UTF-8" );
			break;

		case 'Uppercase':

			$text = mb_convert_case( $text, MB_CASE_UPPER, "UTF-8" );
			$text = preg_replace( '/&NBSP;/', '&nbsp;', $text );
			break;

		case 'Lowercase':

			$text = mb_convert_case( $text, MB_CASE_LOWER, "UTF-8" );
			break;

		default:
			break;
	}


	return $text;
}

/**
 * Return required or not, considering limitations mins and ranges
 *
 * @param array $field_props Array of field properties
 *
 * @since 1.1
 * @return bool Required or not
 */
function acf_required( $field_props ) {

	if ( !$field_props )
		return false;

	$required = false;

	if ( isset( $field_props[ 'format' ] ) && 'required' == $field_props[ 'format' ] )
		$required = true;

	if ( isset( $field_props[ 'limits' ] ) && $field_props[ 'limits' ] != '' && isset( $field_props[ 'limits_attr' ] ) && $field_props[ 'limits_attr' ] != '' ) {
		$limit = $field_props[ 'limits' ];
		$limit_arr = array( 'min', 'minlength', 'minWords', 'mincollocations', 'range', 'rangelength', 'rangeWords', 'rangecollocations' );
		$limit_attr = explode( ',', $field_props[ 'limits_attr' ] );
		$limit_attr = (int) $limit_attr[ 0 ];

		if ( in_array( $limit, $limit_arr ) && $limit_attr > 0 )
			$required = true;
	}

	return $required;
}

?>