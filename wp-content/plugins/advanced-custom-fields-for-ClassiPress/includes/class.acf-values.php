<?php
/**
 * Here is where all the ACF static data is stored
 *
 * @author Artem Frolov (dikiyforester)
 */
class ACF_Values {

	/**
	 * Profile field properties array
	 *
	 * Array param definitions are as follows:
	 * key         = property name
	 *   |-title   = property title
	 *   |-col     = the identifier of the table columns group to group columns by tabs.
	 *   |-type    = property type
	 *   |-desc    = property description
	 */
	static $field_props = array(
		'name' => array(
			'title' => 'Name',
			'col' => '1',
			'type' => 'text',
			'desc' => 'The name of the field. Required field.<br /> The field can contain only letters, numbers, and underscores.'
		),
		/* Type and values */
		'type' => array(
			'title' => 'Field Type',
			'col' => '2',
			'type' => 'drop-down',
			'desc' => 'Type of the field. While there are only the following types: text_box, drop-down, checkbox, radio, text area.<br /> Default value: text_box.'
		),
		'values' => array(
			'title' => 'List Of Values',
			'col' => '2',
			'type' => 'text area',
			'desc' => 'The list of values available in the following field types: drop-down, checkbox, radio. This field is required for these types.<br />
                        Enter a comma separated list of values or single value you want to appear in this drop-down box.<br />
                        If you want to use an existing list of values​of a specific ad field - just enter the name of the field. As done for the field "user_state". This is very handy.'
		),
		'default' => array(
			'title' => 'Default Value',
			'col' => '2',
			'type' => 'text',
			'desc' => 'This is the default value for the form fields. The user can change this value or remain unchanged.<br />
                        If the type field contains a list of values, the default value should be in this list.'
		),
		/* Formats and Limitations */
		'format' => array(
			'title' => 'Format Of Values',
			'col' => '5',
			'type' => 'drop-down',
			'desc' => 'Formats and limitations will allow you to get a more ordered information from the user.<br />
					You can set the format for email or url, or number, or simply required etc.<br />
					See detailed description of formats and limitations in the Help topic "Formats & Limitations".'
		),
		'limits' => array(
			'title' => 'Limitations Of Values',
			'col' => '5',
			'type' => 'drop-down',
			'desc' => 'Restricts user input. You can set limits on the number of characters, words, collocations, and also limits on numeric values.<br />
					There are 3 types of limitations: minimum, maximum and range.<br /> The value of the limitations must be entered in the field "Limitations Attributes".<br />
					See detailed description of formats and limitations in the Help topic "Formats & Limitations".'
		),
		'limits_attr' => array(
			'title' => 'Limitations Attributes',
			'col' => '5',
			'type' => 'text',
			'desc' => 'Methods of limiting take 1 or 2 parameters, you must specify in this field.<br />
					For example, you chose limitation "rangeWords" - this means that the user must enter a few words in a certain range.<br />
					Just write the numbers separated by comma in the "Limitations Attributes" (for example: 2,10).'
		),
		'transform' => array(
			'title' => 'Text Tranform',
			'col' => '5',
			'type' => 'drop-down',
			'desc' => 'As well as the CSS <strong><em>text-transform</em></strong> property controls the capitalization of text. <br />
					But here capitalization occurs before user\'s data saves in database.<br />
					You can select one of 5 options:
					<ul>
					<li><strong><em>Default</em></strong> - The text renders as it is. This is default</li>
					<li><strong><em>Capitalize</em></strong> - Transforms The First Character Of Each Word To Uppercase, All Others Letters To Lowercase.</li>
					<li><strong><em>Uppercase</em></strong> - TRANSFORMS ALL CHARACTERS TO UPPERCASE.</li>
					<li><strong><em>Lowercase</em></strong> - transforms all characters to lowercase.</li>
					</ul>'
		),
		/* Labels and Descriptions */
		'title' => array(
			'title' => 'Title',
			'col' => '3',
			'type' => 'text',
			'desc' => 'Field title.'
		),
		'description' => array(
			'title' => 'Description',
			'col' => '3',
			'type' => 'text area',
			'desc' => 'Field description.'
		),
		/* Display field options */
		'reg_form_display' => array(
			'title' => 'Registration form field',
			'col' => '4',
			'type' => 'checkbox',
			'desc' => 'Check to display this field in the registration page. By default is off, so that you could control the display of fields after activating the plugin.'
		),
		'edit_profile_display' => array(
			'title' => 'Edit form field',
			'col' => '4',
			'type' => 'checkbox',
			'desc' => 'Check to display this field in the edit profile page. By default is off, so that you could control the display of fields after activating the plugin.'
		),
		'author_page_display' => array(
			'title' => 'Author page display',
			'col' => '4',
			'type' => 'checkbox',
			'desc' => 'Check to display this field in the author\'s page. By default is off, so that you could control the display of fields after activating the plugin.'
		),
		'user_sidebar_display' => array(
			'title' => 'User sidebar display',
			'col' => '4',
			'type' => 'checkbox',
			'desc' => 'Check to display this field in the users\'s sidebar (Widget "Account information"). By default is off, so that you could control the display of fields after activating the plugin.'
		),
		'user_sidebar_ad_display' => array(
			'title' => 'Single ad sidebar display',
			'col' => '4',
			'type' => 'checkbox',
			'desc' => 'Check to display this field in the Single ad sidebar (tab "Poster"). By default is off, so that you could control the display of fields after activating the plugin.'
		),
		'single_ad_display' => array(
			'title' => 'Single ad display',
			'col' => '4',
			'type' => 'checkbox',
			'desc' => 'Check to display this field in the ad single page. By default is off, so that you could control the display of fields after activating the plugin.'
		),
		'loop_ad_top' => array(
			'title' => 'Loop ad top',
			'col' => '4',
			'type' => 'checkbox',
			'desc' => 'Check to display this field in the ads loop before the description. By default is off, so that you could control the display of fields after activating the plugin.'
		),
		'loop_ad_bottom' => array(
			'title' => 'Loop ad bottom',
			'col' => '4',
			'type' => 'checkbox',
			'desc' => 'Check to display this field in the ads loop after the description. By default is off, so that you could control the display of fields after activating the plugin.'
		),
		'private' => array(
			'title' => 'Private field',
			'col' => '4',
			'type' => 'checkbox',
			'desc' => 'If you set this property, then this field will see only the author, and administrators. Ordinary visitors and search engines will not see this field.'
		)
	);

	/**
	 * Ad field properties array
	 *
	 * Array param definitions are as follows:
	 * key         = property name
	 *   |-title   = property title
	 *   |-type    = property type
	 *   |-desc    = property description
	 */
	static $ad_field_props = array(
		'name' => array(
			'title' => 'Name',
			'type' => 'span',
			'desc' => 'The "Meta Name" of the ClassiPress ad field.'
		),
		'default' => array(
			'title' => 'Default Value',
			'type' => 'text',
			'desc' => 'This is the default value for the form fields.<br />
					<strong>If you want to inherit the value of some field profile into ClassiPress field - enter  name of the field profile.</strong>
					<br /> The user can change this value or remain unchanged.<br />
					If the type field contains a list of values, the default value should be in this list.'
		),
		'format' => array(
			'title' => 'Format Of Values',
			'type' => 'drop-down',
			'desc' => 'Formats and limitations will allow you to get a more ordered information from the user.<br />
					You can set the format for email or url, or number, or simply required etc.<br />
					See detailed description of formats and limitations in the Help topic "Formats & Limitations".'
		),
		'limits' => array(
			'title' => 'Limitations Of Values',
			'type' => 'drop-down',
			'desc' => 'Restricts user input. You can set limits on the number of characters, words, collocations, and also limits on numeric values.<br />
					There are 3 types of limitations: minimum, maximum and range.<br /> The value of the limitations must be entered in the field "Limitations Attributes".<br />
					See detailed description of formats and limitations in the Help topic "Formats & Limitations".'
		),
		'limits_attr' => array(
			'title' => 'Limitations Attributes',
			'type' => 'text',
			'desc' => 'Methods of limiting take 1 or 2 parameters, you must specify in this field.<br />
					For example, you chose limitation "rangeWords" - this means that the user must enter a few words in a certain range.<br />
					Just write the numbers separated by comma in the "Limitations Attributes" (for example: 2,10).'
		),
		'transform' => array(
			'title' => 'Text Tranform',
			'type' => 'drop-down',
			'desc' => 'As well as the CSS <strong><em>text-transform</em></strong> property controls the capitalization of text. <br />
					But here capitalization occurs before user\'s data saves in database.<br />
					You can select one of 5 options:
					<ul>
					<li><strong><em>Default</em></strong> - The text renders as it is. This is default</li>
					<li><strong><em>Capitalize</em></strong> - Transforms The First Character Of Each Word To Uppercase, All Others Letters To Lowercase.</li>
					<li><strong><em>Uppercase</em></strong> - TRANSFORMS ALL CHARACTERS TO UPPERCASE.</li>
					<li><strong><em>Lowercase</em></strong> - transforms all characters to lowercase.</li>
					</ul>'
		),
		'new_ad_display' => array(
			'title' => 'New ad',
			'type' => 'checkbox',
			'desc' => 'Check to display this field in the new ad page. By default is on, so that you could control the display of fields after activating the plugin.'
		),
		'edit_ad_display' => array(
			'title' => 'Edit ad',
			'type' => 'checkbox',
			'desc' => 'Check to display this field in the edit ad page. By default is on, so that you could control the display of fields after activating the plugin.'
		),
		'single_ad_display' => array(
			'title' => 'Single ad list',
			'type' => 'checkbox',
			'desc' => 'Check to display this field in the ad single page. By default is on, so that you could control the display of fields after activating the plugin.'
		),
		'single_ad_cont' => array(
			'title' => 'Single ad content',
			'type' => 'checkbox',
			'desc' => 'Check to display this field in the ad single page in separated content area. By default is off.'
		),
		'loop_ad_top' => array(
			'title' => 'Loop ad top',
			'type' => 'checkbox',
			'desc' => 'Check to display this field in the ads loop before the description. By default is off, so that you could control the display of fields after activating the plugin.'
		),
		'loop_ad_bottom' => array(
			'title' => 'Loop ad bottom',
			'type' => 'checkbox',
			'desc' => 'Check to display this field in the ads loop after the description. By default is off, so that you could control the display of fields after activating the plugin.'
		),
		'private' => array(
			'title' => 'Private field',
			'type' => 'checkbox',
			'desc' => 'If you set this property, then this field will see only the author, and administrators. Ordinary visitors and search engines will not see this field.'
		)
	);

	/**
	 * Datepicker properties array
	 *
	 * Array param definitions are as follows:
	 * key         = property name
	 *   |-title   = property title
	 *   |-type    = property type
	 *   |-desc    = property description
	 */
	static $datepicker_props = array(
		'preview' => array(
			'title' => 'Real time preview',
			'type' => 'text',
			'desc' => 'Click on the text box to see the Datepicker with your settings in real time.<br />
					As such, the Datepicker is displayed on the live page, after saving the settings.<br />
					Try to manually enter the date in the wrong format and you will see how the validation works.'
		),
		'date_format' => array(
			'title' => 'Date format',
			'type' => 'radio',
			'desc' => 'You can select a predefined date format for your locale, or you can use these ready-made formats.<br />
					You can also specify your own date format (Custom option).'
		),
		'custom_format_text' => array(
			'type' => 'text'
		),
		'locale' => array(
			'title' => 'Localizations',
			'type' => 'drop-down',
			'desc' => 'Here are the localization of jQuery UI Datepicker.<br />
					Each localization contains not only language translation, but also a set of ready settings for each represented region.'
		),
		'animation' => array(
			'title' => 'Animations',
			'type' => 'drop-down',
			'desc' => 'Set the name of the animation used to show/hide the datepicker.'
		),
		'multi_month' => array(
			'title' => 'Display multiple month',
			'type' => 'text',
			'desc' => 'Set how many months to show at once. The value can be a straight integer.'
		),
		'button_bar' => array(
			'title' => 'Display button bar',
			'type' => 'checkbox',
			'desc' => 'Whether to show the button panel.'
		),
		'menus' => array(
			'title' => 'Display month and year menus',
			'type' => 'checkbox',
			'desc' => 'Allows you to change the year and month by selecting from a drop-down list.'
		),
		'other_dates' => array(
			'title' => 'Display dates in other month',
			'type' => 'checkbox',
			'desc' => 'When true days in other months shown before or after the current month are selectable.'
		),
		'icon_trigger' => array(
			'title' => 'Display icon trigger',
			'type' => 'checkbox',
			'desc' => 'Set to true to place an image after the field to use as the trigger without it appearing on a button.'
		)
	);

	/**
	 * Field types
	 *
	 */
	static $field_types = array(
		'text_box',
		'drop-down',
		'checkbox',
		'radio',
		'text area'
	);

	/**
	 * Array of all validation methods of the ACF plugin
	 *
	 * Array param definitions are as follows:
	 * key           = validation method name
	 *   |-args      = the number of arguments which accepts method.
	 *   |-validate  = type of method: the format of the field or its limits.
	 *   |-desc      = method description
	 */
	static $field_formats = array(
		/*  Formats  */
		'email' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Makes the element require a valid email. Works with text inputs.'
		),
		'url' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Makes the element require a valid url. Works with text inputs.<br />
					<strong>REQUIRE:</strong> "http://" or "https://" or "ftp://" in the beginning of the line.<br />'
		),
		'phone' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Makes the element require a valid phone number in any style. Works with text inputs.<br />
					<strong>VALID:</strong> 5555555 | 555-5555 | 555.5555 | 555 5555 | 5555555555 | 555-555-5555 | 555.555.5555 | 555 555 5555 | (555)5555555 | (555)-555-5555 | (555).555.5555 | (555) 555 5555 | +XX5555555555 | +XX-555-555-5555 | +XX.555.555.5555 | +XX 555 555 5555 | +XX(555)5555555 | +XX-(555)-555-5555 | +XX.(555).555.5555 | +XX (555) 555 5555<br />'
		),
		'phoneUS' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Makes the element require a valid US phone number.  Where the area code may not start with 1 and the prefix may not start with 1
					allows "-" or " " as a separator and allows parens around area code. Some people may want to put a "1" in front of their number.<br />
					<strong>VALID:</strong> 1(212)-999-2345 or 212 999 2344 or 212-999-0983<br />
					<strong>INVALID:</strong> 111-123-5434 or 212 123 4567'
		),
		'4d_4d' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Makes the element require a format with 2 separeted 4-gigits numbers.<br />
					<strong>VALID:</strong> 55555555 | 5555-5555 | 5555.5555 | 5555 5555 <br />'
		),
		'dateCustom' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Makes the element require a valid date format.
					You can customize absolutely any date format on the settings tab "Date Picker Settings."<br />
					It works with text fields. For the selected field will be added date picker.<br />
					And most importantly: <strong>no matter what date format you have set up - plug-in will check the user input value for this format!</strong>'
		),
		'number' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Makes the element require positive or negative decimal number. Separator can be dot or a comma.<br />
					<strong>VALID:</strong> 55,555 | 555.55 | -55,5555 | -5555.55 <br />
					<strong>INVALID:</strong> 55,5a5 | 55.5,55 etc.'
		),
		'digits' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Makes the element require digits only.<br />
					<strong>VALID:</strong> 555555555<br />
					<strong>INVALID:</strong> 55a5555555 | 5555 555 | 555,5555 etc.'
		),
		'integer' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Makes the element require positive or negative non-decimal number<br />
					<strong>VALID:</strong> 555555555 | -555555555<br />
					<strong>INVALID:</strong> 55a5555555 | 5555 555 | 555,5555 etc.'
		),
		'numeric_ws' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Matches numbers and whitespaces. Use for i.g. misc. zip-codes, phone-numbers, and other..  where you may not completely know the format.<br />
					<strong>VALID:</strong> 555555555 | 555 555 55 5<br />
					<strong>INVALID:</strong> 55a5555555 | -555 5555 | 5 55,5555 etc.'
		),
		'letterswithbasicpunc' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Makes the element require only latin letters or punctuation( <span class="args">- . , ( ) &#39;  &quot; </span>).'
		),
		'alphanumeric' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Makes the element require only latin letters, numbers, spaces or underscores.'
		),
		'lettersonly' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Makes the element require only latin letters.'
		),
		'nowhitespace' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Makes the element do not accept white space.<br />
					<strong>VALID:</strong> Simple_characters_set_#156416.<br />
					<strong>INVALID:</strong> Simple characters set #156416.'
		),
		'required' => array(
			'args' => '0',
			'validate' => 'format',
			'desc' => 'Jast required element.'
		),
		/*  Limits  */
		'maxlength' => array(
			'args' => '1',
			'validate' => 'limit',
			'desc' => 'Makes the element require a given maxmimum length.<br />
					Method accepts <span class="args">1</span> positive integer argument.'
		),
		'minlength' => array(
			'args' => '1',
			'validate' => 'limit',
			'desc' => 'Makes the element require a given minimum length.<br />
					Method accepts <span class="args">1</span> positive integer argument.'
		),
		'rangelength' => array(
			'args' => '2',
			'validate' => 'limit',
			'desc' => 'Makes the element require a given range length.<br />
					Method accepts <span class="args">2</span> comma separated positive integer arguments.'
		),
		'range' => array(
			'args' => '2',
			'validate' => 'limit',
			'desc' => 'Makes the element require a given value range.<br />
					Method accepts <span class="args">2</span> comma separated positive integer arguments.'
		),
		'max' => array(
			'args' => '1',
			'validate' => 'limit',
			'desc' => 'Makes the element require a given maximum.<br />
					Method accepts <span class="args">1</span> positive integer argument.'
		),
		'min' => array(
			'args' => '1',
			'validate' => 'limit',
			'desc' => 'Makes the element require a given minimum.<br />
					Method accepts <span class="args">1</span> positive integer argument.'
		),
		'maxWords' => array(
			'args' => '1',
			'validate' => 'limit',
			'desc' => 'Makes the element require a given maximum words.<br />
					Method accepts <span class="args">1</span> positive integer argument.'
		),
		'minWords' => array(
			'args' => '1',
			'validate' => 'limit',
			'desc' => 'Makes the element require a given minimum words.<br />
					Method accepts <span class="args">1</span> positive integer argument.'
		),
		'rangeWords' => array(
			'args' => '2',
			'validate' => 'limit',
			'desc' => 'Makes the element require a given range words.<br />
					Method accepts <span class="args">2</span> comma separated positive integer arguments.'
		),
		'maxcollocations' => array(
			'args' => '1',
			'validate' => 'limit',
			'desc' => 'Makes the element require a given maximum comma-separated collocations.<br />
					Method accepts <span class="args">1</span> positive integer argument.'
		),
		'mincollocations' => array(
			'args' => '1',
			'validate' => 'limit',
			'desc' => 'Makes the element require a given minimum comma-separated collocations.<br />
					Method accepts <span class="args">1</span> positive integer argument.'
		),
		'rangecollocations' => array(
			'args' => '2',
			'validate' => 'limit',
			'desc' => 'Makes the element require a given range comma-separated collocations.<br />
					For example, you can use it for <strong>"tags_input"</strong> field and limid number of the tags in range 1-3<br />
					<strong>VALID:</strong> Tag1 | Tag1, Another tag2 | Tag1, Another tag2, Yet another tag3<br />
					<strong>INVALID:</strong> Tag1, Another tag2, Yet another tag3, | Tag1, Another tag2, Yet another tag3, And another tag4<br />
					Method accepts <span class="args">2</span> comma separated positive integer arguments (maximum & minimum).<br />'
		)
	);

	/**
	 * Array of all jQuery UI datepicker localizations
	 */
	static $date_locales = array(
		'af' => 'Afrikaans',
		'sq' => 'Albanian (Gjuha shqipe)',
		'ar-DZ' => 'Algerian Arabic',
		'ar' => 'Arabic (&#8235;(&#1604;&#1593;&#1585;&#1576;&#1610;',
		'hy' => 'Armenian (&#1344;&#1377;&#1397;&#1381;&#1408;&#1381;&#1398;)',
		'az' => 'Azerbaijani (Az&#601;rbaycan dili)',
		'eu' => 'Basque (Euskara)',
		'bs' => 'Bosnian (Bosanski)',
		'bg' => 'Bulgarian (&#1073;&#1098;&#1083;&#1075;&#1072;&#1088;&#1089;&#1082;&#1080; &#1077;&#1079;&#1080;&#1082;)',
		'ca' => 'Catalan (Catal&agrave;)',
		'zh-HK' => 'Chinese Hong Kong (&#32321;&#39636;&#20013;&#25991;)',
		'zh-CN' => 'Chinese Simplified (&#31616;&#20307;&#20013;&#25991;)',
		'zh-TW' => 'Chinese Traditional (&#32321;&#39636;&#20013;&#25991;)',
		'hr' => 'Croatian (Hrvatski jezik)',
		'cs' => 'Czech (&#269;e&#353;tina)',
		'da' => 'Danish (Dansk)',
		'nl-BE' => 'Dutch (Belgium)',
		'nl' => 'Dutch (Nederlands)',
		'en-AU' => 'English/Australia',
		'en-NZ' => 'English/New Zealand',
		'en-GB' => 'English/UK',
		'eo' => 'Esperanto',
		'et' => 'Estonian (eesti keel)',
		'fo' => 'Faroese (f&oslash;royskt)',
		'fa' => 'Farsi/Persian (&#8235;(&#1601;&#1575;&#1585;&#1587;&#1740;',
		'fi' => 'Finnish (suomi)',
		'fr' => 'French (Fran&ccedil;ais)',
		'fr-CH' => 'French/Swiss (Fran&ccedil;ais de Suisse)',
		'gl' => 'Galician',
		'ge' => 'Georgian',
		'de' => 'German (Deutsch)',
		'el' => 'Greek (&#917;&#955;&#955;&#951;&#957;&#953;&#954;&#940;)',
		'he' => 'Hebrew (&#8235;(&#1506;&#1489;&#1512;&#1497;&#1514;',
		'hi' => 'Hindi (&#2361;&#2367;&#2306;&#2342;&#2368;)',
		'hu' => 'Hungarian (Magyar)',
		'is' => 'Icelandic (&Otilde;slenska)',
		'id' => 'Indonesian (Bahasa Indonesia)',
		'it' => 'Italian (Italiano)',
		'ja' => 'Japanese (&#26085;&#26412;&#35486;)',
		'kk' => 'Kazakhstan (Kazakh)',
		'km' => 'Khmer',
		'ko' => 'Korean (&#54620;&#44397;&#50612;)',
		'lv' => 'Latvian (Latvie&ouml;u Valoda)',
		'lt' => 'Lithuanian (lietuviu kalba)',
		'lb' => 'Luxembourgish',
		'mk' => 'Macedonian',
		'ml' => 'Malayalam',
		'ms' => 'Malaysian (Bahasa Malaysia)',
		'no' => 'Norwegian (Norsk)',
		'pl' => 'Polish (Polski)',
		'pt' => 'Portuguese (Portugu&ecirc;s)',
		'pt-BR' => 'Portuguese/Brazilian (Portugu&ecirc;s)',
		'rm' => 'Rhaeto-Romanic (Romansh)',
		'ro' => 'Romanian (Rom&acirc;n&#259;)',
		'ru' => 'Russian (&#1056;&#1091;&#1089;&#1089;&#1082;&#1080;&#1081;)',
		'sr' => 'Serbian (&#1089;&#1088;&#1087;&#1089;&#1082;&#1080; &#1112;&#1077;&#1079;&#1080;&#1082;)',
		'sr-SR' => 'Serbian (srpski jezik)',
		'sk' => 'Slovak (Slovencina)',
		'sl' => 'Slovenian (Slovenski Jezik)',
		'es' => 'Spanish (Espa&ntilde;ol)',
		'sv' => 'Swedish (Svenska)',
		'ta' => 'Tamil (&#2980;&#2990;&#3007;&#2996;&#3021;)',
		'th' => 'Thai (&#3616;&#3634;&#3625;&#3634;&#3652;&#3607;&#3618;)',
		'tj' => 'Tajikistan',
		'tr' => 'Turkish (T&uuml;rk&ccedil;e)',
		'uk' => 'Ukranian (&#1059;&#1082;&#1088;&#1072;&#1111;&#1085;&#1089;&#1100;&#1082;&#1072;)',
		'vi' => 'Vietnamese (Ti&#7871;ng Vi&#7879;t)',
		'cy-GB' => 'Welsh/UK (Cymraeg)'
	);

	/**
	 * An array of plugin default settings.
	 *
	 * Array param definitions are as follows:
	 * key1                     = option name.
	 *    |-key2                = field name.
	 *         |-key3 => value  = property name => value.
	 */
	static $default_config = array(
		'acf_profile_fields' => array(
			'user_country' => array(
				'type' => 'drop-down',
				'values' => 'cp_country',
				'format' => 'required',
				'limits' => 'maxlength',
				'limits_attr' => '100',
				'title' => 'Country',
				'description' => 'Select your country'
			),
			'user_state' => array(
				'type' => 'drop-down',
				'values' => 'cp_state',
				'format' => 'required',
				'limits' => 'maxlength',
				'limits_attr' => '100',
				'title' => 'State',
				'description' => 'Select your state'
			),
			'user_city' => array(
				'type' => 'text_box',
				'limits' => 'rangelength',
				'limits_attr' => '1,100',
				'title' => 'City',
				'description' => 'Enter your city'
			),
			'user_zipcode' => array(
				'type' => 'text_box',
				'format' => 'digits',
				'limits' => 'rangelength',
				'limits_attr' => '1,6',
				'title' => 'Zip/Postal code',
				'description' => 'Enter your Zip/Postal code'
			),
			'user_street' => array(
				'type' => 'text_box',
				'limits' => 'rangelength',
				'limits_attr' => '1,100',
				'title' => 'Street',
				'description' => 'Enter your street'
			),
			'user_office' => array(
				'type' => 'text_box',
				'limits' => 'rangelength',
				'limits_attr' => '1,100',
				'title' => 'Office / Apartament',
				'description' => 'Enter your office or apartament'
			),
			'user_phone_number' => array(
				'type' => 'text_box',
				'format' => 'phone',
				'title' => 'Phone number',
				'description' => 'Enter your phone number'
			),
			'user_age' => array(
				'type' => 'text_box',
				'format' => 'integer',
				'limits' => 'min',
				'limits_attr' => '18',
				'title' => 'Age',
				'description' => 'Enter your Age'
			),
			'user_tax_id' => array(
				'type' => 'text_box',
				'format' => 'required',
				'limits' => 'maxlength',
				'limits_attr' => '15',
				'title' => 'Tax ID',
				'description' => 'Enter your Tax ID (This field will able to see only you and administrator)',
				'private' => 'yes'
			),
			'event_date' => array(
				'type' => 'text_box',
				'format' => 'dateCustom',
				'limits' => 'maxlength',
				'limits_attr' => '50',
				'title' => 'Event Date',
				'description' => 'Select your Event date'
			),
			'event_description' => array(
				'type' => 'text area',
				'limits' => 'maxlength',
				'limits_attr' => '1000',
				'title' => 'Event description',
				'description' => 'Provide some details on your event'
			),
			'type_of_owner' => array(
				'type' => 'radio',
				'values' => 'Individual,Corporation,Disregarded entity,Partnership,Simple trust,Grantor trust,Complex trust,Estate,Government,International organization,Central bank of issue,Tax-exempt organization,Private foundation',
				'default' => 'Individual',
				'format' => 'required',
				'limits' => 'maxlength',
				'limits_attr' => '100',
				'title' => 'Type of beneficial owner',
				'description' => 'Select type of beneficial owner'
			),
			'user_offer' => array(
				'type' => 'checkbox',
				'values' => 'Services,Products',
				'default' => 'Products',
				'format' => 'required',
				'limits' => 'maxlength',
				'limits_attr' => '100',
				'title' => 'What do you offer',
				'description' => 'Select what do you offer'
			),
			'accept_terms' => array(
				'type' => 'checkbox',
				'values' => 'I accept the terms of service',
				'limits' => 'maxlength',
				'limits_attr' => '100',
				'title' => 'Accept Terms',
				'description' => 'Check if you are accept the terms of service'
			)
		),
		'acf_error_msgs' => array(
		),
		'acf_ad_fields' => array(
		),
		'acf_date_picker' => array(
			'date_format' => '',
			'custom_format_text' => 'dd.mm.y',
			'locale' => 'en-GB',
			'animation' => 'show',
			'multi_month' => '1',
			'button_bar' => '',
			'menus' => '',
			'other_dates' => 'yes',
			'icon_trigger' => 'yes'
		),
	);

	function values( $value, $keys = FALSE ) {
		if ( $keys )
			return array_keys( self::$$value );
		else
			return self::$$value;
	}

}

?>