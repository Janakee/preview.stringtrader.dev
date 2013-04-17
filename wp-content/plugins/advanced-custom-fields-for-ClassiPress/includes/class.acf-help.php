<?php
/**
 * Class of context help
 *
 * @author Artem Frolov (dikiyforester)
 */
class ACF_Help extends ACF_Values {

	static function contextual_help() {
		global $acf_plugin_hook;
		$screen = get_current_screen();
		if ( $screen->id != $acf_plugin_hook )
			return;

		$content = array( );
		$content[] = array(
			'id' => 'browse',
			'title' => __( 'Browse' ),
			'callback' => __CLASS__ . '::browse_help'
		);
		$content[] = array(
			'id' => 'install',
			'title' => __( 'Installation/Update' ),
			'callback' => __CLASS__ . '::install_help'
		);
		$content[] = array(
			'id' => 'profile_fields',
			'title' => __( 'Profile Fields' ),
			'callback' => __CLASS__ . '::profile_fields_help'
		);
		$content[] = array(
			'id' => 'err_msgs',
			'title' => __( 'Error Messages' ),
			'callback' => __CLASS__ . '::err_msgs_help'
		);
		$content[] = array(
			'id' => 'ad_fields',
			'title' => __( 'Ad Fields' ),
			'callback' => __CLASS__ . '::ad_fields_help'
		);
		$content[] = array(
			'id' => 'formats',
			'title' => __( 'Formats & Limitations' ),
			'callback' => __CLASS__ . '::formats_help'
		);
		$content[] = array(
			'id' => 'other',
			'title' => __( 'Export/Import/Clear Settings' ),
			'callback' => __CLASS__ . '::settings_help'
		);
		$content[] = array(
			'id' => 'date',
			'title' => __( 'Date Picker Settings' ),
			'callback' => __CLASS__ . '::date_help'
		);
		$content[] = array(
			'id' => 'hooks',
			'title' => __( 'Action hooks' ),
			'callback' => __CLASS__ . '::hooks_help'
		);
		$content[] = array(
			'id' => 'tuts',
			'title' => __( 'Video Tutorials' ),
			'callback' => __CLASS__ . '::tuts_help'
		);
		$content[] = array(
			'id' => 'contacts',
			'title' => __( 'Contacts' ),
			'callback' => __CLASS__ . '::contacts_help'
		);

		foreach ( array_keys( $content ) as $key ) {
			$screen->add_help_tab( $content[ $key ] );
		}
	}

	static function browse_help() {
		?>
		<h3 class="accordion-header"><?php echo ACF_TITLE; ?></h3>
		<div class="accordion-content">
			<p>The <?php echo ACF_TITLE; ?> plugin is a powerful tool for extending the functional of ClassiPress theme.<br />
				Plugin adds the most requested themes features.</p>

			<h4>Managing the profile and ad fields:</h4>
			<ul>
				<li>Adding extra Profile fields </li>
				<li>Different types of profile fields</li>
				<li>Date Field with Datepicker and validation of input values</li>
				<li>Private Field. If you set this property, then this field will see only the author, and administrators. Ordinary visitors and search engines will not see this field.</li>
				<li>Formats of ads and profile fields</li>
				<li>Limit the number of input symbols, words, collocations</li>
				<li>Validation of formats and the limitations of form fields</li>
				<li>Custom validation messages (edit messages in your language)</li>
				<li>The default values ​​for all fields</li>
				<li>Inherit values ​​of the profile field in the ad field</li>
				<li>Control output fields on the various pages and forms (Registration user form, Edit profile form, Author's page, Single ad page, Archives pages, Add new ad form, &nbsp;Edit ad form)</li>
			</ul>
			Plugin settings can be exported to INI file, or import from another file.
			<h4>The Plugin interface</h4>
			Plugin page can be accessed from the main administration menu ClassiPress-&gt; ACF options.<br />
			<p>Plugin settings is divided into four tabs with tables of settings:</p>
			<ul>
				<li><?php _e( 'Profile Fields' ); ?> - add and manage profile fields</li>
				<li><?php _e( 'Validation Error Messages' ); ?> - edit messages</li>
				<li><?php _e( 'Ad Fields' ); ?> - ad fields management</li>
				<li><?php _e( 'Export/Import/Clear Settings' ); ?></li>
				<li><?php _e( 'Date Picker Settings' ); ?></li>
			</ul>
			To save all the settings on each tab, click button <strong><i>Save Changes</i></strong>.
			<p>Detailed information about each tab read the specific Help Page.<p>
		</div>

		<?php
	}

	static function install_help() {
		?>
		<h3 class="accordion-header"><?php _e( 'Installation/Update' ); ?></h3>
		<div class="accordion-content">
			<p>
				The Plugin developed for a specific version of ClassiPress! After each
				update ClassiPress Plugin source code review. This may take some time.&nbsp;
			</p>
			<p>
				Be prepared that if you update ClassiPress before the release of upgrade
				Plugin - the Plugin turns off (for not to cause potential errors). The
				Plugin resume work on the condition that his version is compatible with
				the version of ClassiPress.
			</p>
			<div class="note">
				<p>
		            ClassiPress 3.2 will come with significant changes. This causes a big changes in the plugin.
		            Therefore one version of the plugin does not work at the same time with CP3.1.9 and CP3.2.
		            To avoid confusion and problems with the installation,  in a plugin zip file added two versions of the plugin.
				<ul>
					<li>advanced-custom-fields-classipress3.1.9-v1.0.2.zip - for ClassiPress 3.1.8 - 3.1.9</li>
					<li>advanced-custom-fields-classipress3.2.1-v1.1.2.zip - for ClassiPress 3.2 - 3.2.1</li>
				</ul>
				</p>
			</div>

			<h4>
				Manual installation:
			</h4>
			<ol>
				<li>
					Download the Plugin zip file <em>advanced-custom-fields-classipress.zip</em>.
				</li>
				<li>
					Extract the contents of the zip file.<br />
					You will see two zip files
					<ul>
						<li><em>advanced-custom-fields-classipress3.1.9-v1.0.2.zip</em> - for ClassiPress 3.1.8 - 3.1.9</li>
						<li><em>advanced-custom-fields-classipress3.2.1-v1.1.1.zip</em> - for ClassiPress 3.2 - 3.2.1</li>
					</ul>
				</li>
				<li>
					Select the appropriate version for version Classipress.<br />
					Extract the contents of the zip file.
				</li>
				<li>
					Upload the contents of the zip file to the <strong><i>wp-content/plugins/</i></strong> folder
					of your WordPress installation.
				</li>
				<li>
					Then activate the Plugin from Plugins page.
				</li>
				<li>
					Find the menu "ClassiPress" on the WordPress administration page. Select
					the lower point "ACF options" and proceed to configure the Plugin.
					<br />
				</li>
			</ol>
			<br />
			<h4>
				Manual update:
			</h4>
			<ol>
				<li>
					Export Plugin settings to the INI file (just in case)
				</li>
				<li>
					Deactivate installed Plugin from Plugins page.
				</li>
				<li>
					Delete existing ACF Plugin folder from the <strong><i>wp-content/plugins/</i></strong> folder
					of your WordPress installation
				</li>
				<li>
					Repeat all steps from manual install (see above)
				</li>
				<li>
					Then activate the Plugin from Plugins page.
				</li>
			</ol></div>
		<?php
	}

	static function profile_fields_help() {
		?>
		<h3 class="accordion-header"><?php _e( 'Profile field properties' ); ?></h3>
		<div class="accordion-content">
			<p>Configure additional profile fields are presented in tabular form.
				Where rows - additional profile fields, and columns - the properties of these fields.
				Since the properties of the fields were many, I had to divide them into several groups.</p>
			<p>So you can see four groups of the properties of the field profile: "Main Properties", "Formats & Limitations", "Labels & Descriptions", "Display Options".</p>
			<ul><li>If you want to add a new row of the field - click "<?php _e( 'Add field' ); ?>" button at the bottom of the table.</li>
				<li>To remove an existing field, click the X in the right end of the line.</li>
				<li>To save the results click "<?php _e( 'Save Changes' ); ?>".</li></ul>
			<p>Below you will find descriptions of all additional properties of the profile field.</p>

			<div>
				<table class="widefat">
					<thead>
						<tr>
							<th style="width:150px;">
								<strong><?php _e( 'Property' ); ?>:</strong>
							</th>
							<th><strong><?php _e( 'Description' ); ?>:</strong></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( self::$field_props as $value ) {
						?>
							<tr>
								<td style="width:150px;"><span class="format_name"><?php echo $value[ 'title' ]; ?>:</span></td>
								<td><?php echo $value[ 'desc' ]; ?>
								</td>
							</tr><?php
						}
						?>
					</tbody>
				</table>

			</div></div>
		<?php
	}

	static function err_msgs_help() {
		?>
		<h3 class="accordion-header"><?php _e( 'Validation Error Messages' ); ?></h3>
		<div class="accordion-content">
			<p>Tab Validation Error Messages allows you to edit the default validation messages.</p>
			<p>Most of the messages was inherited from the  <a href="http://bassistance.de/jquery-plugins/jquery-plugin-validation/" target="_blank">jQuery.Validation plugin</a>.
				Others I have written myself.
				These messages can be translated to your language is not correctly, so I gave you an opportunity to change them.</p>
			<div class="note">
				<ul>
					<li>Pay attention to the messages with the characters "<strong>{0}</strong>".
						I recommend not to change them, as it will be replaced in the message attribute of the method validation.
					</li>
					<li>In jQuery.Validation plugin is a serious bug.
						Methods of validation with 2 parameters is not working properly and improperly give out an error message. <br />
						I fixed method, but the error messages are still broken. <br />
						To display the message without error in its text should not be a characters "<strong>{0}</strong>" or "<strong>{1}</strong>"!
					</li>
				</ul>
			</div>
		</div>

		<?php
	}

	static function ad_fields_help() {
		?>
		<h3 class="accordion-header"><?php _e( 'Ad field properties' ); ?></h3>
		<div class="accordion-content">
			<p>Configuring profile fields represented as a table, as well as setting the profile fields.
				Where rows - ad fields, and columns - the properties of these fields.</p>

			<p>Below you will find descriptions of all additional properties of the ad fields.</p>
			<div>
				<table class="widefat">
					<thead>
						<tr>
							<th style="width:125px;">
								<strong><?php _e( 'Property' ); ?>:</strong>
							</th>
							<th><strong><?php _e( 'Description' ); ?>:</strong></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( self::$ad_field_props as $value ) {
						?>
							<tr>
								<td style="width:125px;"><span class="format_name"><?php echo $value[ 'title' ]; ?>:</span></td>
								<td><?php echo $value[ 'desc' ]; ?>
								</td>
							</tr><?php
						} ?>
					</tbody>
				</table>

			</div>
		</div>
		<?php
	}

	static function formats_help() {
		?>
		<h3 class="accordion-header"><?php _e( 'Formats & Limitations' ); ?></h3>
		<div class="accordion-content">
			<p>Formats and limitations will allow you to get a more ordered information from the user.<br />
				You can assign formats and limitations of the fields values for the following pages: user registration page, user profile editing page, add new ad page and edit ad page.<br />
				Thereby to simplify the verification of ads and saves your time.</p>
			<h4>How does it work?</h4>
			<p>So, what need to do to get from users only necessary information?</p>
			<ol>
				<li>Set the format of the field (such as numeric, text, date, etc.).</li>
				<li>Limit data entry to certain criteria (for example, set the maximum number of characters, words, or range of numbers, etc.).</li>
				<li>Mark the required fields.</li>
				<li>And most importantly: Have the ability to programmatically check on all of these criteria is that user enters! And if necessary, tell user what he did wrong.</li>
			</ol>
			<p>ACF plugin functional can solve all of these tasks.</p>
			<p>So, all in order.</p>
			<ol>
				<li><h5>Formats:</h5>
					<p>You can choose the format of the field using the drop-down list of "Format Of Values".</p>
					<p>This can be:<br />
						The default format (or nothing or anything, except the bad characters and tags);<br />
						Any of the suggested format (the user can enter a value in the selected format, or leave blank);<br />
						Required field (requires the user to enter anything, no matter what);</p>
				</li>
				<li><h5>Limitations:</h5>
					<p>You can limit entered words, symbols, collocations or numbers.</p>
					<p>You can choose the limitation of the field using the drop-down list "Limitations Of Values", setting out the attribute of the limitation in the "Limitations Attributes".<br />
						Methods of limiting are divided into three types:<br />
						Maximum - Sets the upper limit of the input data. The user can enter data is not above the specified limits, including leave blank;<br />
						Minimum - Sets the lower limit of the input data. The user can enter data is not below the specified limits;<br />
						Range - Sets the range, ie upper and lower limits (safest option);</p>
				</li>
				<li><h5>Appointment required for input fields:</h5>
					<p>Available in 2 variants:<br />
						If you choose a format of the field (but not "required") - you can specify input limitations such as "rangelength" with attributes "1,100". Then user must enter at least one character or more but less than 100.<br />
						If you do not have special format for the field, but this field is required, then select the format "required" or, as in the previous case, use restrictions.</p>
				</li>
				<li><h5>Validation:</h5>
					<p>Each format or limitation is the specific method of verification fields.
						If the user enters data in the wrong format, then pressing the Save button - displays a message about incorrect entry.
						In this case sending data to the server will not happen.</p>
					<p>Verification of fields can be performed on the client side or server side.<br />
						On the client side validation works in real time and the user immediately sees what he has done wrong.<br />
						This feature is provided by plug-jQuery.Validation. This is very handy, but it has some drawbacks.<br />
						For example, if a user will prevent execution of JavaScript in their browser, the client-side validation does not happen at all.<br />
						In order to avoid this vulnerability and maximally protect the registration page and edit profile page, I added to the plugin the same verification methods, but running on the server side.<br />
						Therefore, if the user disables javascript, validation will still be done (you can try).</p>
					<p>For the add and edit ad pages validation works only on the client side. <br />
						It does not contradict source code of the ClassiPress.</p>
					<p>After validation, in any case, all data is cleaned on the server side before saving them to the site database.</p>
				</li>
			</ol>
			<p>Below is a list of all methods of testing fields.<br />
				This list is open, you can send a request to the method that you want and I'll add it (if it's real :)).</p>
			<p class="note">I recommend to read carefully the description of the methods before adding them to the fields of forms.</p>


			<div>
				<table class="widefat">
					<thead>
						<tr>
							<th style="width:125px;">
								<strong><?php _e( 'Formats' ); ?>:</strong>
							</th>
							<th><strong><?php _e( 'Description' ); ?>:</strong></th>
						</tr>
					</thead>
					<tbody>
		<?php
		foreach ( self::$field_formats as $key => $value ) {
			if ( $value[ 'validate' ] != 'format' )
				continue;
			?>
							<tr>
								<td style="width:125px;"><span class="format_name"><?php echo $key; ?>:</span></td>
								<td><?php echo $value[ 'desc' ]; ?>
								</td>
							</tr><?php
		}
		?>
					</tbody>
				</table>

			</div>
			<br />
			<div>
				<table class="widefat">
					<thead>
						<tr>
							<th style="width:125px;">
								<strong><?php _e( 'Limits' ); ?>:</strong>
							</th>
							<th><strong><?php _e( 'Description' ); ?>:</strong></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( self::$field_formats as $key => $value ) {
							if ( $value[ 'validate' ] != 'limit' )
								continue;
							?>
							<tr>
								<td style="width:125px;"><span class="format_name"><?php echo $key; ?>:</span></td>
								<td><?php echo $value[ 'desc' ]; ?></td>
							</tr><?php
		}
		?>
					</tbody>
				</table>

			</div></div>
		<?php
	}

	static function settings_help() {
		?>
		<h3 class="accordion-header"><?php echo _e( 'Export/Import/Clear Settings' ); ?></h3>
		<div class="accordion-content">
			<p>This section will help you manage your configuration settings of this Plugin.</p>
			<p>Here you can:
			<ul>
				<li>set the default settings;</li>
				<li>export settings in the configuration file;</li>
				<li>import settings from a configuration file.</li>
			</ul>
		</p>
		</div>
		<?php
	}

	static function date_help() {
		?>
		<h3 class="accordion-header"><?php _e( 'Date Picker Settings' ); ?></h3>
		<div class="accordion-content">
			<p>The <a href="http://docs.jquery.com/UI/Datepicker">jQuery UI Datepicker</a> is a highly configurable plugin that adds datepicker functionality to your ad and profile fields.<br />
				You can customize the date format and language, restrict the selectable date ranges and add in buttons and other navigation options easily.</p>
			<p>All you need to do - is to set up Datepicker according to your needs and check a box "Date picker" in front of the desired field profile, or ads.</p>
			<p>Below is a list of customizable properties of Datepicker.<br />

			<div>
				<table class="widefat">
					<thead>
						<tr>
							<th style="width:125px;">
								<strong><?php _e( 'Property' ); ?>:</strong>
							</th>
							<th><strong><?php _e( 'Description' ); ?>:</strong></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( self::$datepicker_props as $value ) {
							if ( !isset( $value[ 'title' ] ) )
								continue;
							?>
							<tr>
								<td style="width:200px;"><span class="format_name"><?php echo $value[ 'title' ]; ?>:</span></td>
								<td><?php echo $value[ 'desc' ]; ?>
								</td>
							</tr><?php
						}
						?>
					</tbody>
				</table>

			</div>
		</div>
		<?php
	}

	static function contacts_help() {
		?>
		<h3 class="accordion-header"><?php _e( 'Contacts' ); ?></h3>
		<div class="accordion-content">
			<p>Hello!</p>
			<p>My name is Artem Frolov and I am developer of this plugin. <br />
				You can find me on the <a href="http://forums.appthemes.com/">AppThemes forum</a> under the nickname <a href="http://forums.appthemes.com/members/dikiyforester/">dikiyforester</a>.</p>
			<p>If you have any questions or suggestions on the use of plugin, or you find an error, write me to the forum.</p>
		</div>
		<?php
	}

	static function tuts_help() {
		?>
		<h3 class="accordion-header"><?php _e( 'Video Tutorials' ); ?></h3>
		<div class="accordion-content">
			<p>As you already know, ACF Plugin is not easy to set up, in spite of the fact that I was trying to make it more simple.</p>
			<p>I'm starting to record video tutorials on working with plug-in.</p>
			<p>Find all tutorials you can on this page <a href="http://forums.appthemes.com/video-tuts-installing-using-acfcp-plugin-36003/">Video Tuts for installing and using ACFCP Plugin</a>.</p>
			<p>Here I will show examples of setting plug and major features.</p>
		</div>
		<?php
	}

	static function hooks_help() {
		?>
		<h3 class="accordion-header"><?php _e( 'Action Hooks' ); ?></h3>
		<div class="accordion-content">
			<p>From ACFCP v1.0.2 I am start to add new action hooks in the code of plugin. <br />
				Now you can use the additional functionality that would enhance opportunities of ClassiPress.</p><br /><br />

			<h3>Loop ad listing</h3>

			<p><i><strong>acf_loop_top($post)</strong></i> - add action after title in loop ad meta (in line with author and category)
			</p>
			<h4>Usage</h4>
			<p>Add this code to your child theme function.php file</p>
			<pre>
		    // This is main function
		    function acf_loop_hooks_top($post){
		        // your action code here
		    }

		    // This is helper function for adding action when wordpress init
		    function acf_add_actions(){
		        add_action('acf_loop_top','acf_loop_hooks_top',1,1);
		    }
		    add_action('init', 'acf_add_actions');
			</pre>
			<h4>Parameters</h4>
			<p><i>$post</i> - (object) (optional) current WP Post object</p>
			<br /><br />
			<p><i><strong>acf_loop_bottom($post)</strong></i> - add action after description in loop ad meta (in line with posted and total viewed)</p>
			<h4>Usage</h4>
			<p>Add this code to your child theme function.php file</p>
			<pre>
		    // This is main function
		    function acf_loop_hooks_bottom($post){
		        // your action code here
		    }

		    // This is helper function for adding action when wordpress init
		    function acf_add_actions(){
		        add_action('acf_loop_bottom','acf_loop_hooks_bottom',1,1);
		    }
		    add_action('init', 'acf_add_actions');
			</pre>
			<h4>Parameters</h4>
			<p><i>$post</i> - (object) (optional) current WP Post object</p>
		</div>
		<?php
	}

}

//end class
?>