<?php
class ACF_Options_Page extends ACF_Values {

	// Writes to the database options are stored in the array $default_config.
	static function install( $clear = false ) {

		global $wpdb, $acf_version;

		update_option( 'acf_version', $acf_version );

		$sql = "SELECT field_name, field_label "
				. "FROM " . $wpdb->prefix . "cp_ad_fields "
				. "ORDER BY field_name desc";
		$cp_ad_fields = $wpdb->get_results( $sql );
		$ad_props_arr = array_keys( self::$ad_field_props );

		foreach ( $cp_ad_fields as $cp_ad_field ) {
			foreach ( $ad_props_arr as $key ) {
				$optvar = '';
				switch ( $key ) {
					case 'name':
						continue 2;
						break;
					case 'new_ad_display':
					case 'edit_ad_display':
					case 'single_ad_display':
						$optvar = 'yes';
						break;
					case 'default':
						if ( $cp_ad_field->field_name == 'cp_country' )
							$optvar = 'user_country';
						if ( $cp_ad_field->field_name == 'cp_state' )
							$optvar = 'user_state';
						if ( $cp_ad_field->field_name == 'cp_city' )
							$optvar = 'user_city';
						if ( $cp_ad_field->field_name == 'cp_zipcode' )
							$optvar = 'user_zipcode';
						if ( $cp_ad_field->field_name == 'cp_street' )
							$optvar = 'user_street';
						break;
				}
				self::$default_config[ 'acf_ad_fields' ][ $cp_ad_field->field_name ][ $key ] = $optvar;
			}
		}

		foreach ( self::$default_config as $name => $value ) {
			if ( $clear || !get_option( $name ) )
				update_option( $name, $value );
		}
	}

	static function init_plugin_menu() {
		global $acf_plugin_hook;
		$acf_plugin_hook = add_submenu_page( 'admin-options.php', ACF_TITLE, ACF_MENU, 'manage_options', 'acf-options', array( 'ACF_Options_Page', 'get_admin_page_html' ) );
		return $acf_plugin_hook;
	}

	static function register_settings() {
		register_setting( 'acf_options', 'acf_profile_fields' );
		register_setting( 'acf_options', 'acf_error_msgs' );
		register_setting( 'acf_options', 'acf_ad_fields' );
		register_setting( 'acf_options', 'acf_date_picker' );
	}

	static function backend_enqueue_scripts( $hook_suffix ) {

		if ( $hook_suffix != 'classipress_page_acf-options' )
			return;

		wp_enqueue_style( 'acf_admin_style', ACF_URL . '/css/acf-options.css', 99 );

		wp_enqueue_script( 'morevalidate', get_bloginfo( 'template_directory' ) . '/includes/js/validate/additional-methods.js' );
		global $app_abbr;
		// add the language validation file if not english
		if ( get_option( $app_abbr . '_form_val_lang' ) ) {
			$lang_code = strtolower( get_option( $app_abbr . '_form_val_lang' ) );
			wp_enqueue_script( 'validate-lang', get_bloginfo( 'template_directory' ) . "/includes/js/validate/localization/messages_$lang_code.js", array( 'jquery' ), '1.6' );
		}
		//$inc = includes_url();
		//wp_enqueue_script( 'datepicker_script',  $inc . '/js/jquery/ui/jquery.ui.datepicker.min.js',99 );
		wp_enqueue_script( 'jqueryUI_locale_script', ACF_URL . '/js/jquery-ui-i18n.min.js', 99 );
		wp_enqueue_script( 'ValidateFix_script', ACF_URL . '/js/ValidateFix.js', 99 );
		wp_enqueue_script( 'acf_admin_script', ACF_URL . '/js/acf-admin-script.js' );

		// send arrays to js
		$msgs_arr = get_option( 'acf_error_msgs' );
		$js_msgs = json_encode( $msgs_arr );
		$js_field_props = json_encode( array_keys( self::$field_props ) ); // свойства настроек полей профиля
		$js_field_formats = json_encode( self::$field_formats ); // форматы полей профиля
		// print script
		print "<script type='text/javascript'>\n";
		print "/* <![CDATA[ */\n";
		print "var field_properties = $js_field_props;";
		print "var field_formats = $js_field_formats;";
		print "var validate_msgs = $js_msgs;";
		print "/* ]]> */\n";
		print "</script>";
	}

	static function handle_options_update() {

		if ( !isset( $_POST[ 'clearSettings' ] ) ) {

			$update = true;

			if ( !empty( $_FILES[ 'importSettings' ][ 'name' ] ) ) {

				// Import from file
				$import = INI::read( $_FILES[ 'importSettings' ][ 'tmp_name' ] );
				if ( !empty( $import[ 'acf_profile_fields' ] ) && !empty( $import[ 'acf_profile_fields' ] ) && !empty( $import[ 'acf_profile_fields' ] ) ) {
					$profile_fields = $import[ 'acf_profile_fields' ];
					$error_msgs = $import[ 'acf_error_msgs' ];
					$ad_fields = $import[ 'acf_ad_fields' ];
					$date_picker = $import[ 'acf_date_picker' ];
					$update = false;
				} else {
					$update = true;
				}
			}

			if ( $update ) {

				global $cp_ad_fields;
				$profile_fields = array( );
				$error_msgs = array( );
				$count = 1;
				$props_arr = array_keys( self::$field_props );
				$formats_arr = array_keys( self::$field_formats );
				$ad_props_arr = array_keys( self::$ad_field_props );
				$datepicker_props_arr = array_keys( self::$default_config[ 'acf_date_picker' ] );

				// Update profile options
				while ( isset( $_POST[ 'field_name_' . $count ] ) ) {
					foreach ( $props_arr as $key ) {

						if ( $key == 'name' )
							continue;

						if ( isset( $_POST[ 'field_' . $key . '_' . $count ] ) )
							$profile_fields[ $_POST[ 'field_name_' . $count ] ][ $key ] = appthemes_clean( $_POST[ 'field_' . $key . '_' . $count ] );

						//elseif ($profile_fields[$_POST['field_name_'.$count]][$key]) unset($profile_fields[$_POST['field_name_'.$count]][$key]);
						else
							$profile_fields[ $_POST[ 'field_name_' . $count ] ][ $key ] = '';
					}
					$count++;
				}

				// Update error messegies
				foreach ( $formats_arr as $key ) {
					if ( isset( $_POST[ $key . '_err' ] ) )
						$error_msgs[ $key ] = appthemes_clean( $_POST[ $key . '_err' ] );
				}

				// Update ad fields options
				foreach ( $cp_ad_fields as $cp_ad_field ) {
					foreach ( $ad_props_arr as $key ) {
						$name = $cp_ad_field->field_name . '_' . $key;
						if ( isset( $_POST[ $name ] ) )
							$ad_fields[ $cp_ad_field->field_name ][ $key ] = appthemes_clean( $_POST[ $name ] );
					}
				}

				// Update datepicker options
				foreach ( $datepicker_props_arr as $key ) {
					if ( isset( $_POST[ $key ] ) )
						$date_picker[ $key ] = appthemes_clean( $_POST[ $key ] );
				}
			}

			// Update database
			update_option( 'acf_profile_fields', $profile_fields );
			update_option( 'acf_error_msgs', $error_msgs );
			update_option( 'acf_ad_fields', $ad_fields );
			update_option( 'acf_date_picker', $date_picker );
		} else {
			self::install( $clear = true );
		}
	}

	function acf_get_cell_html( $input ) {

		$name = (isset( $input[ 'name' ] )) ? ' name="' . esc_attr( $input[ 'name' ] ) . '"' : '';
		$value = ' value="' . esc_attr( $input[ 'value' ] ) . '"';
		$id = (isset( $input[ 'id' ] )) ? ' id="' . esc_attr( $input[ 'id' ] ) . '"' : '';
		$class = (isset( $input[ 'class' ] )) ? esc_attr( $input[ 'class' ] ) . ' ' : '';
		$other_attr = '';

		if ( isset( $input[ 'format' ] ) && $input[ 'format' ] != '' )
			$class .= esc_attr( $input[ 'format' ] ) . ' ';

		if ( isset( $input[ 'limits' ] ) && $input[ 'limits' ] != '' ) {

			switch ( self::$field_formats[ $input[ 'limits' ] ][ 'args' ] ) {
				case '0':
					$class .= esc_attr( $input[ 'limits' ] ) . ' ';
					break;
				case '1':
					if ( self::$field_formats[ $input[ 'limits' ] ][ 'args' ] == '1' && isset( $input[ 'limits_attr' ] ) && $input[ 'limits_attr' ] != '' )
						$other_attr .= ' ' . esc_attr( $input[ 'limits' ] ) . '="' . esc_attr( $input[ 'limits_attr' ] ) . '"';
					break;
				case '2':
					if ( self::$field_formats[ $input[ 'limits' ] ][ 'args' ] == '2' && isset( $input[ 'limits_attr' ] ) && $input[ 'limits_attr' ] != '' )
						$other_attr .= ' ' . esc_attr( $input[ 'limits' ] ) . '="' . esc_attr( $input[ 'limits_attr' ] ) . '"';
					break;
			}
		}

		switch ( $input[ 'type' ] ) {

			case 'drop-down':

				echo '<select class="' . $class . 'dropdownlist"' . $name, $id, $other_attr . '>';

				if ( isset( $input[ 'pls_select' ] ) )
					echo $input[ 'pls_select' ];

				foreach ( $input[ 'values' ] as $key ) {
					echo '<option value="' . trim( esc_attr( $key ) ) . '" ' . selected( trim( $key ), trim( $input[ 'value' ] ), false ) . '>' . esc_html( $key ) . '</option>';
				}
				echo '</select>';
				break;

			case 'checkbox':

				if ( !isset( $input[ 'values' ] ) ) {
					echo '<input type="checkbox"' . checked( $input[ 'value' ], 'yes', false ), $name, $id, $other_attr . ' value="yes" class="' . $class . 'checkboxlist" />';
					break;
				}

				$optionCursor = 1;
				echo '<ol class="checkboxes">';

				foreach ( $input[ 'values' ] as $option ) {

					echo '<li>';
					echo '<input type="checkbox"' . acf_checked( $option, $input[ 'value' ] ), $other_attr . ' name="' . esc_attr( $input[ 'name' ] ) . '[]" value="' . trim( esc_attr( $option ) ) . '" id="' . esc_attr( $input[ 'id' ] ) . '_' . $optionCursor++ . '" class="' . $class . 'checkboxlist" />&nbsp;&nbsp;&nbsp;' . trim( esc_attr( $option ) );
					echo '</li>';
				}
				echo '</ol>';
				break;


			case 'text area':

				echo '<textarea rows="' . esc_attr( $input[ 'rows' ] ) . '" cols="' . esc_attr( $input[ 'cols' ] ) . '" class="' . $class . 'text"' . $name, $id, $other_attr . '>' . esc_html( stripslashes( $input[ 'value' ] ) ) . '</textarea>';
				break;

			case 'radio':
				$optionCursor = 1;
				echo '<ol class="radios">';
				if ( !isset( $input[ 'required' ] ) ) {
					echo '<li>';
					echo '<input type="radio"' . $name, $other_attr . ' id="' . esc_attr( $input[ 'id' ] ) . '_' . $optionCursor++ . '" class="' . $class . 'radiolist" checked="checked" value=""/>';
					_e( 'None', 'appthemes' );
					echo '</li>';
				}
				foreach ( $input[ 'values' ] as $option ) {
					echo '<li>';
					echo '<input ' . checked( trim( $option ), trim( $input[ 'value' ] ), false ) . ' type="radio"' . $name, $other_attr . ' value="' . trim( esc_attr( $option ) ) . '" id="' . esc_attr( $input[ 'id' ] ) . '_' . $optionCursor++ . '" class="' . $class . 'radiolist" />&nbsp;&nbsp; ' . trim( esc_html( $option ) );
					echo '</li>';
				}
				echo '</ol>';
				break;

			case 'span':

				echo '<span class="' . trim( $class ) . '">' . esc_html( $input[ 'value' ] ) . '</span>';
				break;

			default:
				echo '<input type="text" class="' . $class . 'text"' . $name, $value, $id, $other_attr . '/>';
				break;
		}
	}

	static function get_row_html( $tab, $key = '', $option_field = '', $row_index = '' ) {
		// for the profile field $row_index is a row_index, for the ad field $row_index is a ad field type
		$field_props = self::$$tab;

		foreach ( $field_props as $prop_name => $field_prop ) {

			$input[ 'class' ] = 'field_' . $prop_name;

			if ( $tab == 'field_props' ) {
				$input[ 'name' ] = $input[ 'class' ] . '_' . $row_index;
				$td_class = ($field_prop[ 'col' ] == '1') ? ' class="field_name_col"' : ' class="col' . esc_attr( $field_prop[ 'col' ] ) . '"';
			}

			if ( $tab == 'ad_field_props' ) {

				// if field type is not text box than date picker option is not available
				if ( $row_index != 'text box' && $prop_name == 'date' ) {
					$input[ 'class' ] .= ' hidden';
				}

				$input[ 'name' ] = $key . '_' . $prop_name;
				$td_class = '';
			}

			if ( $prop_name == 'name' )
				$input[ 'value' ] = $key;
			else
				$input[ 'value' ] = ( isset( $option_field[ $prop_name ] ) ) ? $option_field[ $prop_name ] : '';

			$input[ 'type' ] = $field_prop[ 'type' ];

			if ( $input[ 'type' ] == 'drop-down' )
				$input[ 'values' ] = self::get_select_values( $prop_name );

			if ( $prop_name != 'type' )
				$input[ 'pls_select' ] = '<option value="">-- ' . __( 'Default' ) . ' --</option>';

			if ( $input[ 'type' ] == 'text area' ) {
				$input[ 'rows' ] = 1;
				$input[ 'cols' ] = 70;
			}

			$input[ 'id' ] = $input[ 'name' ];

			echo '<td' . $td_class . '>';
			self::acf_get_cell_html( $input );
			echo '</td>';

			unset( $input );
		}
	}

	static function get_select_values( $sel_field ) {

		switch ( $sel_field ) {
			case 'format':
				foreach ( self::$field_formats as $format_name => $field_format ) {
					if ( $field_format[ 'validate' ] == 'format' )
						$select_values[ ] = $format_name;
				}
				break;

			case 'limits':
				foreach ( self::$field_formats as $format_name => $field_format ) {
					if ( $field_format[ 'validate' ] == 'limit' )
						$select_values[ ] = $format_name;
				}
				break;

			case 'type':
				$select_values = self::$field_types;
				break;

			case 'transform':
				$select_values = array(
					'Capitalize',
					'Uppercase',
					'Lowercase'
				);
				break;

			default:
				break;
		}
		return $select_values;
	}

	/**
	 * Export plugin settings to acf_export.ini file.
	 */
	static function acf_export_ini() {
		if ( stripos( $_SERVER[ 'REQUEST_URI' ], 'acf_export.ini' ) !== FALSE && current_user_can( 'manage_options' ) ) {
			$export = array( );
			$export[ 'acf_profile_fields' ] = get_option( 'acf_profile_fields' );
			$export[ 'acf_error_msgs' ] = get_option( 'acf_error_msgs' );
			$export[ 'acf_ad_fields' ] = get_option( 'acf_ad_fields' );
			$export[ 'acf_date_picker' ] = get_option( 'acf_date_picker' );

			$string = '';
			foreach ( array_keys( $export ) as $key ) {
				$string .= '[' . $key . "]\n";
				$string .= INI::write_get_string( $export[ $key ], '' ) . "\n";
			}

			header( "Content-type: application/x-msdownload", true, 200 );
			header( "Content-Disposition: attachment; filename='acf_export.ini'" );
			header( "Pragma: no-cache" );
			header( "Expires: 0" );
			echo $string;
			exit();
		}
	}

	static function get_admin_page_html() {

		global $wpdb, $cp_ad_fields, $acf_tested, $acf_version;
		//var_dump($acf_tested);
		$chet = 'even';
		$sql = "SELECT field_name, field_label, field_type "
				. "FROM " . $wpdb->prefix . "cp_ad_fields "
				. "ORDER BY field_name desc";

		$cp_ad_fields = $wpdb->get_results( $sql );

		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}


		if ( isset( $_POST[ 'acf_submit_hidden' ] ) && $_POST[ 'acf_submit_hidden' ] == 'Y' ) {

			self::handle_options_update();
			?>
			<div class="updated"><p><strong><?php _e( 'Settings saved.' ); ?></strong></p></div>
			<?php
		}

		// We're outputting a lot of HTML, and the easiest way
		// to do it is with output buffering from PHP.
		ob_start();

		if ( !$acf_tested ) {
			?>
			<div class="updated"><p><strong><?php echo 'The current version of the ' . ACF_TITLE, $acf_version . ' has not been tested with ClassiPress' . get_option( 'cp_version' ) . ' !<br />
                    For security reasons, all of frontend features are disabled.'; ?></strong></p></div>
			<?php
		}

		foreach ( self::$default_config as $name => $value ) {
			$$name = get_option( $name, $value );
		}
		?>
		<div class="wrap">
			<div id="acf_profiles_img" class="acf_profiles_img icon32">
				<br />
			</div>
			<h2><?php echo ACF_TITLE . ' plugin v' . $acf_version; ?></h2>
			<br/>
			<div>
				<form id="acf_options_form" name="acf_options_form" method="post" action="" enctype="multipart/form-data">

					<?php settings_fields( 'acf_options' ); ?>

					<input type="hidden" name="acf_submit_hidden" value="Y">
					<p><input id="acf_submit_top-btn" class="button-primary" type="submit" name="Save_top" value="<?php _e( 'Save Changes' ); ?>" /></p>
					<div id="tabs-wrap">
						<ul class="tabs">
							<li><a href="#tabs-1"><?php _e( 'Profile Fields' ); ?></a></li>
							<li><a href="#tabs-2"><?php _e( 'Validation Error Messages' ); ?></a></li>
							<li><a href="#tabs-3"><?php _e( 'Ad Fields' ); ?></a></li>
							<li><a href="#tabs-4"><?php _e( 'Export/Import/Clear Settings' ); ?></a></li>
							<li><a href="#tabs-5"><?php _e( 'Date Picker Settings' ); ?></a></li>
						</ul>
						<!-- #tab1 -->
						<div id="tabs-1">
							<div class="slidwrap widefat">
								<div class="slidlb">
									<ul>
										<li id="col2"><span><?php _e( 'Main Properties' ); ?></span></li>
										<li id="col5"><span><?php _e( 'Formats & Limitations' ); ?></span></li>
										<li id="col3"><span><?php _e( 'Labels & Descriptions' ); ?></span></li>
										<li id="col4"><span><?php _e( 'Display Options' ); ?></span></li>
									</ul>
								</div>
								<div id="slider"></div>
							</div>
							<table id="acf_profile_field-table" class="widefat">
								<thead>
									<tr>
										<?php
										foreach ( self::$field_props as $field_prop ) {
											echo '<th class="col' . $field_prop[ 'col' ] . '"><span class="titletip" tip="' . esc_attr( $field_prop[ 'desc' ] ) . '">' . $field_prop[ 'title' ] . '</span></th>';
										}
										?>
										<th>&nbsp;</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 1;
									foreach ( $acf_profile_fields as $key => $profile_field ):
										$chet = ($chet == 'alt') ? 'even' : 'alt';
										?>
										<tr data-array_index="<?php echo $count; ?>" class="<?php echo $chet; ?>">
											<?php self::get_row_html( 'field_props', $key, $profile_field, $count ); ?>
											<td class="row_actions"><a href="javascript:void(0)" class ="row_remove"><img alt="<?php _e( 'Delete' ); ?>" title="<?php _e( 'Delete' ); ?>" tip="<?php _e( 'Delete' ); ?>" width="16" height="16" src="<?php echo ACF_URL; ?>/img/icon-delete.png" /></a></td>
										</tr>
										<?php
										$count++;
									endforeach;
									?>
									<tr class="alternate" id="template_row" >
										<?php self::get_row_html( 'field_props' ); ?>
										<td class="row_actions"><a href="javascript:void(0)" class ="row_remove"><img alt="<?php _e( 'Delete' ); ?>" title="<?php _e( 'Delete' ); ?>" tip="<?php _e( 'Delete' ); ?>" width="16" height="16" src="<?php echo ACF_URL; ?>/img/icon-delete.png" /></a></td>
									</tr>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="17" style="text-align:center"><input id="acf_add-field-btn" class="button-secondary" style="padding:4px" type="button" value="<?php _e( 'Add field' ); ?>" /></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<!-- #tab2 -->
						<div id="tabs-2">
							<table id="acf_error_msgs-table" class="widefat">
								<thead>
									<tr>
										<th><span class="titletip"><?php _e( 'Field Format' ); ?></span></th>
										<th><span class="titletip"><?php _e( 'Error message' ); ?></span></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$formats_arr = array_keys( self::$field_formats );
									foreach ( $formats_arr as $field_format ) :
										$chet = ($chet == 'alt') ? 'even' : 'alt';
										?>
										<tr class="<?php echo $chet; ?>">
											<td><span class="format_name"><?php echo $field_format; ?></span></td>
											<td><textarea name="<?php echo $field_format; ?>_err" rows="1" cols="100" id="<?php echo $field_format; ?>_err" class="field_format_err textarea" ><?php if ( isset( $acf_error_msgs[ $field_format ] ) ) echo $acf_error_msgs[ $field_format ]; ?></textarea></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="2"></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<!-- #tab3 -->
						<div id="tabs-3">
							<table id="acf_ad_field-table" class="widefat">
								<thead>
									<tr>
										<?php
										foreach ( self::$ad_field_props as $ad_field_prop ) {
											echo '<th><span class="titletip" tip="' . esc_attr( $ad_field_prop[ 'desc' ] ) . '">' . $ad_field_prop[ 'title' ] . '</span></th>';
										}
										?>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ( $cp_ad_fields as $cp_ad_field ) {
										$chet = ($chet == 'alt') ? 'even' : 'alt';
										echo '<tr class="' . $chet . '">';

										if ( !isset( $acf_ad_fields[ $cp_ad_field->field_name ] ) )
											$acf_ad_fields[ $cp_ad_field->field_name ] = '';

										self::get_row_html( 'ad_field_props', $cp_ad_field->field_name, $acf_ad_fields[ $cp_ad_field->field_name ], $cp_ad_field->field_type );
										echo '</tr>';
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<?php
										foreach ( self::$ad_field_props as $key => $ad_field_prop ) {
											if ( $ad_field_prop[ 'type' ] == 'checkbox' && $key != 'date' ) {

												echo '<th><input type="checkbox" value="" class="col_check" id="field_' . $key . '"><br />' . $ad_field_prop[ 'title' ] . '</th>';
											} else {

												echo '<th>' . $ad_field_prop[ 'title' ] . '</th>';
											}
										}
										?>
									</tr>
								</tfoot>
							</table>
						</div>
						<!-- #tab4 -->
						<div id="tabs-4">
							<table id="acf_other_settings-table" class="widefat">
								<thead>
									<tr>
										<th colspan="2"><span class="titletip">Export/Import/Clear Settings</span></th>
									</tr>
								</thead>
								<tbody>
									<tr class="alt">
										<td style="width: 200px;"><label>Restore all settings to default:</label></td>
										<td><input id="clearSettings" type="checkbox" name="clearSettings" value="Yes"/></td>
									</tr>
									<tr class="even">
										<td style="width: 200px;"><label>Export settings to file:</label><br />
											<span class="description">(<?php _e( 'Please, save settings before export' ) ?>)</span>

										</td>
										<td><a href="<?php echo ACF_URL . '/downloads/acf_export.ini'; ?>" class="button-secondary"><?php _e( 'Download' ) ?></a></td>

									</tr>
									<tr class="alt">
										<td style="width: 200px;"><label>Import settings from file:</label></td>
										<td><input type="file" name="importSettings" id="importSettings" ACCEPT="ini" /></td>
									</tr>
								</tbody>
								<tfoot>
									<tr>
										<th  colspan="2"></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<!-- #tab5 -->
		<?php
		if ( !isset( $acf_date_picker ) )
			$acf_date_picker = self::$default_config[ 'acf_date_picker' ];
		$datepicker_props = self::$datepicker_props;
		?>
						<div id="tabs-5">
							<table id="acf_date_picker" class="widefat">
								<thead>
									<tr>
										<th colspan="2"><span class="titletip">jQuery UI Datepicker Settings</span></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="width: 250px;">
											<span class="pickerowtitle titletip" tip="<?php esc_attr_e( $datepicker_props[ 'preview' ][ 'desc' ] ); ?>"><?php echo $datepicker_props[ 'preview' ][ 'title' ]; ?>:</span>
										</td>
										<td>Date: <input type="text" id="datepicker" class="datevalidator" value=""/><img id="calendar" alt="..." title="..." width="16" height="15" src="<?php echo ACF_URL . '/img/calendar.gif' ?>" style="display:none;"></td>

									</tr>
									<tr>
										<td>
											<span class="pickerowtitle titletip" tip="<?php esc_attr_e( $datepicker_props[ 'date_format' ][ 'desc' ] ); ?>"><?php echo $datepicker_props[ 'date_format' ][ 'title' ]; ?>:</span>
										</td>
										<td>
											<fieldset class="date_formats">
												<label><input type="radio" name="date_format" class="date_format" id="date_format_0" value="" <?php checked( $acf_date_picker[ 'date_format' ], "", true ); ?>/>&nbsp;&nbsp;&nbsp;Use default format which used in localization</label><br />
												<label><input type="radio" name="date_format" class="date_format" id="date_format_1" value="mm/dd/yy" <?php checked( $acf_date_picker[ 'date_format' ], "mm/dd/yy", true ); ?>/>&nbsp;&nbsp;&nbsp;<?php echo date( "m/d/Y" ); ?>&nbsp;&nbsp;&nbsp;<span class="description">(mm/dd/yy)</span></label><br />
												<label><input type="radio" name="date_format" class="date_format" id="date_format_2" value="dd/mm/yy" <?php checked( $acf_date_picker[ 'date_format' ], "dd/mm/yy", true ); ?>/>&nbsp;&nbsp;&nbsp;<?php echo date( "d/m/Y" ); ?>&nbsp;&nbsp;&nbsp;<span class="description">(dd/mm/yy)</span></label><br />
												<label><input type="radio" name="date_format" class="date_format" id="date_format_3" value="dd.mm.yy" <?php checked( $acf_date_picker[ 'date_format' ], "dd.mm.yy", true ); ?>/>&nbsp;&nbsp;&nbsp;<?php echo date( "d.m.Y" ); ?>&nbsp;&nbsp;&nbsp;<span class="description">(dd.mm.yy)</span></label><br />
												<label><input type="radio" name="date_format" class="date_format" id="date_format_4" value="yy-mm-dd" <?php checked( $acf_date_picker[ 'date_format' ], "yy-mm-dd", true ); ?>/>&nbsp;&nbsp;&nbsp;<?php echo date( "Y-m-d" ); ?>&nbsp;&nbsp;&nbsp;<span class="description">(yy-mm-dd)</span></label><br />
												<label><input type="radio" name="date_format" class="date_format" id="date_format_5" value="d M, y" <?php checked( $acf_date_picker[ 'date_format' ], "d M, y", true ); ?>/>&nbsp;&nbsp;&nbsp;<?php echo date( "j M, y" ); ?>&nbsp;&nbsp;&nbsp;<span class="description">(d M, y)</span></label><br />
												<label><input type="radio" name="date_format" class="date_format" id="date_format_6" value="d MM, y" <?php checked( $acf_date_picker[ 'date_format' ], "d MM, y", true ); ?>/>&nbsp;&nbsp;&nbsp;<?php echo date( "j F, y" ); ?>&nbsp;&nbsp;&nbsp;<span class="description">(d MM, y)</span></label><br />
												<label><input type="radio" name="date_format" class="date_format" id="date_format_7" value="DD, d MM, yy" <?php checked( $acf_date_picker[ 'date_format' ], "DD, d MM, yy", true ); ?>/>&nbsp;&nbsp;&nbsp;<?php echo date( "l, j F, Y" ); ?>&nbsp;&nbsp;&nbsp;<span class="description">(DD, d MM, yy)</span></label><br />
												<label><input type="radio" name="date_format" class="date_format" id="date_format_8" value="'day' d 'of' MM 'in the year' yy" <?php checked( $acf_date_picker[ 'date_format' ], "'day' d 'of' MM 'in the year' yy", true ); ?>/>&nbsp;&nbsp;&nbsp;<?php echo 'day ' . date( "j" ) . ' of ' . date( "F" ) . ' in the year ' . date( "Y" ); ?>&nbsp;&nbsp;&nbsp;<span class="description">('day' d 'of' MM 'in the year' yy)</span></label><br />
												<label><input type="radio" name="date_format" class="date_format" id="date_format_9" value="<?php if ( isset( $acf_date_picker[ 'custom_format_text' ] ) ) esc_attr_e( $acf_date_picker[ 'custom_format_text' ] ); ?>" <?php if ( isset( $acf_date_picker[ 'custom_format_text' ] ) ) checked( $acf_date_picker[ 'date_format' ], $acf_date_picker[ 'custom_format_text' ], true ); ?>/>&nbsp;&nbsp;&nbsp;<span>Custom</span>&nbsp;&nbsp;&nbsp;</label>
												<input name="custom_format_text" id="custom_format_text" type="text" value="<?php if ( isset( $acf_date_picker[ 'custom_format_text' ] ) ) esc_attr_e( $acf_date_picker[ 'custom_format_text' ] ); ?>"/>&nbsp;&nbsp;&nbsp;<span class="description"></span>
												<br />
												<a href="http://docs.jquery.com/UI/Datepicker/formatDate" style="font-size: 11px;">(Documentation on date formatting in jQuery UI.)</a>

											</fieldset>
										</td>
									</tr>
									<tr>
										<td>
											<span class="pickerowtitle titletip" tip="<?php esc_attr_e( $datepicker_props[ 'locale' ][ 'desc' ] ); ?>"><?php echo $datepicker_props[ 'locale' ][ 'title' ]; ?>:</span>
										</td>
										<td>
											<select name="locale" id="locale">
												<?php
												foreach ( self::$date_locales as $key => $date_locale ) {
													echo '<option value="' . $key . '" ' . selected( $acf_date_picker[ 'locale' ], $key, false ) . '>' . $date_locale . '</option>';
												}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td>
											<span class="pickerowtitle titletip" tip="<?php esc_attr_e( $datepicker_props[ 'animation' ][ 'desc' ] ); ?>"><?php echo $datepicker_props[ 'animation' ][ 'title' ]; ?>:</span>
										</td>
										<td>
											<select name="animation" id="animation">
												<option value="show" <?php selected( $acf_date_picker[ 'animation' ], "show", true ); ?>>Show (default)</option>
												<option value="slideDown" <?php selected( $acf_date_picker[ 'animation' ], "slideDown", true ); ?>>Slide down</option>
												<option value="fadeIn" <?php selected( $acf_date_picker[ 'animation' ], "fadeIn", true ); ?>>Fade in</option>
												<option value="" <?php selected( $acf_date_picker[ 'animation' ], "", true ); ?>>None</option>
											</select>
										</td>
									</tr>
									<tr>
										<td>
											<span class="pickerowtitle titletip" tip="<?php esc_attr_e( $datepicker_props[ 'multi_month' ][ 'desc' ] ); ?>"><?php echo $datepicker_props[ 'multi_month' ][ 'title' ]; ?>:</span>
										</td>
										<td>
											<input type="text" name="multi_month" id="multi_month" value="<?php esc_attr_e( $acf_date_picker[ 'multi_month' ] ); ?>"/>
										</td>
									</tr>
									<tr>
										<td>
											<span class="pickerowtitle titletip" tip="<?php esc_attr_e( $datepicker_props[ 'button_bar' ][ 'desc' ] ); ?>"><?php echo $datepicker_props[ 'button_bar' ][ 'title' ]; ?>:</span>
										</td>
										<td>
											<input type="checkbox" <?php checked( $acf_date_picker[ 'button_bar' ], 'yes', true ); ?> name="button_bar" id="button_bar" value="yes"/>
										</td>
									</tr>
									<tr>
										<td>
											<span class="pickerowtitle titletip" tip="<?php esc_attr_e( $datepicker_props[ 'menus' ][ 'desc' ] ); ?>"><?php echo $datepicker_props[ 'menus' ][ 'title' ]; ?>:</span>
										</td>
										<td>
											<input type="checkbox" <?php checked( $acf_date_picker[ 'menus' ], 'yes', true ); ?> name="menus" id="menus" value="yes"/>
										</td>
									</tr>
									<tr>
										<td>
											<span class="pickerowtitle titletip" tip="<?php esc_attr_e( $datepicker_props[ 'other_dates' ][ 'desc' ] ); ?>"><?php echo $datepicker_props[ 'other_dates' ][ 'title' ]; ?>:</span>
										</td>
										<td>
											<input type="checkbox" <?php checked( $acf_date_picker[ 'other_dates' ], 'yes', true ); ?> name="other_dates" id="other_dates" value="yes"/>
										</td>
									</tr>
									<tr>
										<td>
											<span class="pickerowtitle titletip" tip="<?php esc_attr_e( $datepicker_props[ 'icon_trigger' ][ 'desc' ] ); ?>"><?php echo $datepicker_props[ 'icon_trigger' ][ 'title' ]; ?>:</span>
										</td>
										<td>
											<input type="checkbox" <?php checked( $acf_date_picker[ 'icon_trigger' ], 'yes', true ); ?> name="icon_trigger" id="icon_trigger" value="yes"/>
										</td>
									</tr>
								</tbody>
								<tfoot>
									<tr>
										<th  colspan="2"  style="text-align: right;"><a href="http://docs.jquery.com/UI/Datepicker">jQuery UI Datepicker API Documentation</a></th>
									</tr>
								</tfoot>
							</table>

						</div>
					</div>
					<br/>
					<p><input id="acf_submit_bot-btn" class="button-primary" type="submit" name="Save_bot" value="<?php _e( 'Save Changes' ); ?>" /></p>
				</form>
			</div></div>
		<?php
		// Output the content.
		$output = ob_get_contents();
		ob_end_clean();

		echo $output;
	}

}

//end class
?>