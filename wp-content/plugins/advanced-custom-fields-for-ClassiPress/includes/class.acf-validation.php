<?php
/**
 * Here is where ACF PHP validation methods is stored
 *
 * @author Artem Frolov (dikiyforester)
 */
class ACF_Validation {
	/* Formats */

	function v_required( $posted ) {
		if ( $posted )
			return true;
	}

	function v_email( $posted ) {
		if ( !$posted || is_email( $posted ) )
			return true;
	}

	function v_url( $posted ) {
		if ( !$posted || preg_match( "/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])|(([a-z]|\d|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])([a-z]|\d|-|\.|_|~|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])*([a-z]|\d|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])))\.)+(([a-z]|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])|(([a-z]|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])([a-z]|\d|-|\.|_|~|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])*([a-z]|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\x{E000}-\x{F8FF}]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/iu", $posted ) )
			return true;
	}

	function v_phone( $posted ) {
		if ( !$posted || preg_match( '/^(([\+]\d{1,3})?[ \.-]?[\(]?\d{3}[\)]?)?[ \.-]?\d{3}[ \.-]?\d{4}$/', $posted ) )
			return true;
	}

	function v_phoneUS( $posted ) {
		$posted = preg_replace( '/\s+/', '', $posted );
		if ( !$posted || strlen( $posted ) > 9 && preg_match( '/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/', $posted ) )
			return true;
	}

	function v_number( $posted ) {
		if ( !$posted || preg_match( '/^-?(?:\d+|\d{1,3}(?:[\s\.,]\d{3})+)(?:[\.,]\d+)?$/', $posted ) )
			return true;
	}

	function v_4d_4d( $posted ) {
		if ( !$posted || preg_match( '/^\d{4}[ \.-]?\d{4}$/', $posted ) )
			return true;
	}

	function v_digits( $posted ) {
		if ( !$posted || preg_match( '/^\d+$/', $posted ) )
			return true;
	}

	function v_integer( $posted ) {
		if ( !$posted || preg_match( '/^-?\d+$/', $posted ) )
			return true;
	}

	function v_numeric_ws( $posted ) {
		if ( !$posted || preg_match( '/^[\s0-9]+$/', $posted ) )
			return true;
	}

	function v_letterswithbasicpunc( $posted ) {
		if ( !$posted || preg_match( "/^[a-z-.,()'\"\s]+$/i", $posted ) )
			return true;
	}

	function v_alphanumeric( $posted ) {
		if ( !$posted || preg_match( '/^\w+$/i', $posted ) )
			return true;
	}

	function v_lettersonly( $posted ) {
		if ( !$posted || preg_match( '/^[a-z]+$/i', $posted ) )
			return true;
	}

	function v_nowhitespace( $posted ) {
		if ( !$posted || preg_match( '/^\S+$/i', $posted ) )
			return true;
	}

	/* Limits */

	function v_max( $posted, $param ) {
		if ( $this->v_integer( $posted ) && intval( $param[ 0 ] ) && intval( $posted ) <= intval( $param[ 0 ] ) )
			return true;
	}

	function v_min( $posted, $param ) {
		if ( $this->v_integer( $posted ) && intval( $param[ 0 ] ) && intval( $posted ) >= intval( $param[ 0 ] ) )
			return true;
	}

	function v_range( $posted, $param ) {
		if ( $this->v_integer( $posted ) && intval( $param[ 0 ] ) && intval( $param[ 1 ] ) && intval( $posted ) >= intval( $param[ 0 ] ) && intval( $posted ) <= intval( $param[ 1 ] ) )
			return true;
	}

	function v_minlength( $posted, $param ) {
		if ( intval( $param[ 0 ] ) && strlen( $posted ) >= intval( $param[ 0 ] ) )
			return true;
	}

	function v_maxlength( $posted, $param ) {
		if ( intval( $param[ 0 ] ) && strlen( $posted ) <= intval( $param[ 0 ] ) )
			return true;
	}

	function v_rangelength( $posted, $param ) {
		if ( intval( $param[ 0 ] ) && intval( $param[ 1 ] ) && strlen( $posted ) >= intval( $param[ 0 ] ) && strlen( $posted ) <= intval( $param[ 1 ] ) )
			return true;
	}

	function v_striptags( $posted ) {
		return preg_replace( '/<.[^<>]*?>/', '', $posted );
	}

	function v_striphtmlspace( $posted ) {
		return preg_replace( '/&nbsp;|&#160;/i', '', $posted );
	}

	function v_stripnumbersandpunc( $posted ) {
		return preg_replace( '/[0-9.(),;:!?%&#$\'"_+=\/-]*/', '', $posted );
	}

	function v_lettersanddelim( $posted ) {
		return preg_replace( '/\s+/', ',', $this->v_stripNumbersAndPunc( $this->v_stripHtmlSpace( $this->v_stripTags( $posted ) ) ) );
	}

	function v_numwords( $posted ) {
		if ( $this->v_lettersAndDelim( $posted ) )
			return count( explode( ',', $this->v_lettersAndDelim( $posted ) ) );
		else
			return 0;
	}

	function v_maxwords( $posted, $param ) {
		if ( $this->v_numWords( $posted ) <= intval( $param[ 0 ] ) )
			return true;
	}

	function v_minwords( $posted, $param ) {
		if ( $this->v_numWords( $posted ) >= intval( $param[ 0 ] ) )
			return true;
	}

	function v_rangewords( $posted, $param ) {
		if ( $this->v_numWords( $posted ) <= intval( $param[ 1 ] ) && $this->v_numWords( $posted ) >= intval( $param[ 0 ] ) )
			return true;
	}

	function v_collocationsanddelim( $posted ) {
		return preg_replace( '/[0-9.();:!?%&#$\'"_+=\/-]*/', '', $this->v_stripHtmlSpace( $this->v_stripTags( $posted ) ) );
	}

	function v_numcollocations( $posted ) {
		if ( $this->v_collocationsanddelim( $posted ) )
			return count( explode( ',', $this->v_collocationsanddelim( $posted ) ) );
		else
			return 0;
	}

	function v_maxcollocations( $posted, $param ) {
		if ( $this->v_numcollocations( $posted ) <= intval( $param[ 0 ] ) )
			return true;
	}

	function v_mincollocations( $posted, $param ) {
		if ( $this->v_numcollocations( $posted ) >= intval( $param[ 0 ] ) )
			return true;
	}

	function v_rangecollocations( $posted, $param ) {
		if ( $this->v_numcollocations( $posted ) <= intval( $param[ 1 ] ) && $this->v_numcollocations( $posted ) >= intval( $param[ 0 ] ) )
			return true;
	}

}

//end class
?>