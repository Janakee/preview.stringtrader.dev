<?php
/*
 * It is home to the functions, which I want to get rid of,
 * but they are required to support earlier versions of ClassiPress
 */


/**
 * Remake cp_formbuilder function.
 * loops through the custom fields and builds the custom ad form
 * Adds default values and formats
 */
if (!function_exists('cp_formbuilder') && $acf_tested) {
    function cp_formbuilder($results, $renew = false) {
        global $wpdb,$ACF_class;
	$ACF_class = new ACF_Options_Page;
	$current_user = wp_get_current_user();
	$acf_ad_fields = get_option('acf_ad_fields');
	$acf_profile_fields = array_keys(get_option('acf_profile_fields'));

        foreach ( $results as $result ) {

	    $acf_ad_field = $acf_ad_fields[$result->field_name];

	    if ( ! isset( $acf_ad_field['new_ad_display'] ) || $acf_ad_field['new_ad_display'] != 'yes' )
	    continue;

	    unset($input);

            if ( acf_required($acf_ad_field) )
		 $input['required'] = 'required';
            else $input['required'] = "";

	    if (isset($result->field_req) && $result->field_req == '1'){
		$input['class'] = 'required';
                $input['required'] = 'required';
            }

	    if (isset($acf_ad_field['format']) && $acf_ad_field['format'] != '')
		$input['format'] = $acf_ad_field['format'];
	    if (isset($acf_ad_field['limits']) && $acf_ad_field['limits'] != '')
		$input['limits'] = $acf_ad_field['limits'];
	    if (isset($acf_ad_field['limits_attr']) && $acf_ad_field['limits_attr'] != '')
		$input['limits_attr'] = $acf_ad_field['limits_attr'];

	    $input['type'] = $result->field_type;
	    $input['name'] = $result->field_name;
	    $input['id'] = $result->field_name;



            // EPIC field value quest =============================================================================================
            $post_meta_val = ( $renew ) ? get_post_meta($renew->ID, $result->field_name, true) : false;

            if ( isset($_POST[$result->field_name]) ) {

		$input['value'] = $_POST[$result->field_name];

            } elseif ( $result->field_name == 'post_title' && $renew ) {

                    $input['value'] = $renew->post_title;

            } elseif ( $result->field_name == 'tags_input' && $renew ) {

                    $input['value'] = rtrim(trim(cp_get_the_term_list($renew->ID, APP_TAX_TAG)), ',');

            } elseif ( $result->field_name == 'post_content' && $renew ) {

                    $input['value'] = $renew->post_content;

            } elseif ( $post_meta_val ) {

                    $input['value'] = $post_meta_val;

            } elseif ( isset($acf_ad_field['default']) ) {

 		if (in_array($acf_ad_field['default'], $acf_profile_fields))

		    $input['value'] = ( get_user_meta($current_user->ID, $acf_ad_field['default'], true) ) ? get_user_meta($current_user->ID, $acf_ad_field['default'], true) : '';

		else
		    $input['value'] = $acf_ad_field['default'];

            } else {
                    $input['value'] = '';
            }
            // THE END of EPIC quest field value ===================================================================================



	    if ( $input['type'] == 'drop-down' || $input['type'] == 'radio' || $input['type'] == 'checkbox' ){
		$input['values'] = explode(',', $result->field_values);
		$input['pls_select'] = '<option value="">-- ' . __('Select', 'appthemes') . ' --</option>';
	    }

	    if ( $input['type'] == 'text area' ){
		$input['rows'] = 8;
		$input['cols'] = 40;
	    }

        ?>

            <li>
                <div class="labelwrapper">
                    <label><?php if ( $result->field_tooltip ) { ?><a href="#" tip="<?php esc_attr_e($result->field_tooltip); ?>" tabindex="999"><div class="helpico"></div></a><?php } ?><?php esc_html_e($result->field_label); ?>: <?php if ( $input['required'] == 'required' ) echo '<span class="colour">*</span>' ?></label>
					<?php if ( ($result->field_type) == 'text area' && (get_option('cp_allow_html') == 'yes') ) { // only show this for tinymce since it's hard to position the error otherwise ?>
                    <br/><label class="invalid tinymce" for="<?php esc_attr_e($result->field_name); ?>"><?php _e('This field is required.','appthemes');?></label>
					<?php } ?>
                </div>
            <?php

	    $ACF_class->acf_get_cell_html($input);

	    if ( $input['type'] == 'text area' ){

                 if ( get_option('cp_allow_html') == 'yes' ) { ?>
                    <script type="text/javascript"> <!--
                    tinyMCE.execCommand('mceAddControl', false, '<?php esc_attr_e($result->field_name); ?>');
                    --></script>
                <?php }

	    }
	    //var_dump($input);
	    echo '<div class="clr"></div>';


            ?>

            </li>

        <?php
        }
    echo '<img id="calendar" title="..." alt="..." src="' . ACF_URL .'/img/calendar.gif" style="display:none;"/>';
    }
}

if (!function_exists('cp_edit_ad_formbuilder') && $acf_tested) {
function cp_edit_ad_formbuilder( $results, $getad ) {
    global $wpdb;
	$ACF_class = new ACF_Options_Page;
	$current_user = wp_get_current_user();
	$acf_ad_fields = get_option('acf_ad_fields');
	$acf_profile_fields = array_keys(get_option('acf_profile_fields'));
    // create array before adding custom fields
    $custom_fields_array = array();

    foreach ($results as $result) :

	// get all the custom fields on the post and put into an array
        $custom_field_keys = get_post_custom_keys($getad->ID);

        if(!$custom_field_keys) continue;
            // wp_die('Error: There are no custom fields');

        // we only want key values that match the field_name in the custom field table or core WP fields.
        if (in_array($result->field_name, $custom_field_keys) || ($result->field_name == 'post_content') || ($result->field_name == 'post_title') || ($result->field_name == 'tags_input') || $result->field_type == 'checkbox' ) :

            // add each custom field name to an array so we can save them correctly later
            if ( appthemes_str_starts_with($result->field_name, 'cp_'))
              $custom_fields_array[] = $result->field_name;

            // we found a match so go fetch the custom field value
            $post_meta_val = get_post_meta($getad->ID, $result->field_name, true);



	    //If the field had just been created and is not stored in plugin settings
	    /*if (!isset($acf_ad_fields[$result->field_name]))
		$acf_ad_fields[$result->field_name] = array ('edit_ad_display' => 'yes');*/

	    $acf_ad_field = $acf_ad_fields[$result->field_name];

	    if ( !isset($acf_ad_field['edit_ad_display']) || $acf_ad_field['edit_ad_display'] != 'yes' )
	    continue;

	    unset($input);

            $input['required'] = "";

            if ( acf_required($acf_ad_field) )
		$input['required'] = 'required';

            if (isset($result->field_req) && $result->field_req == '1'){
		$input['class'] = 'required';
		$input['required'] = 'required';
	    }

	    if (isset($acf_ad_field['format']) && $acf_ad_field['format'] != '')
		$input['format'] = $acf_ad_field['format'];
	    if (isset($acf_ad_field['limits']) && $acf_ad_field['limits'] != '')
		$input['limits'] = $acf_ad_field['limits'];
	    if (isset($acf_ad_field['limits_attr']) && $acf_ad_field['limits_attr'] != '')
		$input['limits_attr'] = $acf_ad_field['limits_attr'];

	    $input['type'] = $result->field_type;
	    $input['name'] = $result->field_name;
	    $input['id'] = $result->field_name;


	    $input['value'] = '';

	    if ($result->field_name == 'post_title') {
			$input['value'] = $getad->post_title;
		    } elseif ($result->field_name == 'tags_input') {
			$input['value'] = rtrim(trim(cp_get_the_term_list($getad->ID, APP_TAX_TAG)), ',');
		    } elseif ($result->field_name == 'post_content') {
			$input['value'] = $getad->post_content;
		    } elseif ($result->field_type == 'checkbox') {
			$input['value'] = get_post_meta($getad->ID, $result->field_name, false);
		    } else {
			$input['value'] = get_post_meta($getad->ID, $result->field_name, true);
		    }

	    if ( !isset($input['value']) && isset($acf_ad_field['default']) ) {

		if (in_array($acf_ad_field['default'], $acf_profile_fields))

		    $input['value'] = ( get_user_meta($current_user->ID, $acf_ad_field['default'], true) ) ? get_user_meta($current_user->ID, $acf_ad_field['default'], true) : '';

		else
		    $input['value'] = $acf_ad_field['default'];
	    }

	    if ( $input['type'] == 'drop-down' || $input['type'] == 'radio' || $input['type'] == 'checkbox' ){
		$input['values'] = explode(',', $result->field_values);
		if (!$result->field_req) $input['pls_select'] = '<option value="">-- ' . __('Select', 'appthemes') . ' --</option>';
	    }

	    if ( $input['type'] == 'text area' ){
		$input['rows'] = 4;
		$input['cols'] = 23;
	    }

		?>
	    <li id="list_<?php echo $result->field_name; ?>">
	    		<div class="labelwrapper">
	    		    <label><?php if ($result->field_tooltip) : ?><a href="#" tip="<?php esc_attr_e($result->field_tooltip); ?>" tabindex="999"><div class="helpico"></div></a><?php endif; ?><?php esc_html_e($result->field_label); ?>: <?php if ( $input['required'] == 'required' ) echo '<span class="colour">*</span>' ?></label><br />
	    		</div>
			    <?php

                            if ($input['type'] == 'text area') echo '<div class="clr"></div>';
			    $ACF_class->acf_get_cell_html($input);

			    if ($input['type'] == 'text area') {

				if (get_option('cp_allow_html') == 'yes') {
				    ?>
		    		<script type="text/javascript"> <!--
		    			tinyMCE.execCommand('mceAddControl', false, '<?php esc_attr_e($result->field_name); ?>');
		    			--></script>
				<?php }
			    }
			    ?>

	    		<div class="clr"></div>
	    	    </li>
		<?php

        endif;

    endforeach;

	// put all the custom field names into an hidden field so we can process them on save
	$custom_fields_vals = implode( ',', $custom_fields_array );
	?>

	<input type="hidden" name="custom_fields_vals" value="<?php echo $custom_fields_vals; ?>" />

<?php
    echo '<img id="calendar" title="..." alt="..." src="' . ACF_URL .'/img/calendar.gif" style="display:none;"/>';
}
}

?>