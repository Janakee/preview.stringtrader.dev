<?php
/*
Plugin Name: Ajax Image Uploader for Classipress
Plugin URI: http://classipro.com
Description: ClassiPress Ajax Image Uploader allow your users to upload their Ad pictures in a friendly and smooth way.
Author: alucas & rubencio
Version: 1.0
*/

//activate plugin
function cp_ajax_uploader_activate()
{
	//backup
	copy(TEMPLATEPATH . "/includes/forms/step2.php", WP_PLUGIN_DIR . "/cp_ajax_image_uploader/classipress_files_backup/step2.php");
	copy(TEMPLATEPATH . "/includes/forms/step3.php", WP_PLUGIN_DIR . "/cp_ajax_image_uploader/classipress_files_backup/step3.php");
	copy(TEMPLATEPATH . "/includes/forms/step-functions.php", WP_PLUGIN_DIR . "/cp_ajax_image_uploader/classipress_files_backup/step-functions.php");
	//new files
	copy(WP_PLUGIN_DIR . "/cp_ajax_image_uploader/classipress_files/step2.php", TEMPLATEPATH . "/includes/forms/step2.php");
	copy(WP_PLUGIN_DIR . "/cp_ajax_image_uploader/classipress_files/step3.php", TEMPLATEPATH . "/includes/forms/step3.php");
	copy(WP_PLUGIN_DIR . "/cp_ajax_image_uploader/classipress_files/step-functions.php", TEMPLATEPATH . "/includes/forms/step-functions.php");
}
register_activation_hook( __FILE__, 'cp_ajax_uploader_activate');

//deactivate plugin
function cp_ajax_uploader_deactivate()
{
	//copy files from backup
	copy(WP_PLUGIN_DIR . "/cp_ajax_image_uploader/classipress_files_backup/step2.php", TEMPLATEPATH . "/includes/forms/step2.php");
	copy(WP_PLUGIN_DIR . "/cp_ajax_image_uploader/classipress_files_backup/step3.php", TEMPLATEPATH . "/includes/forms/step3.php");
	copy(WP_PLUGIN_DIR . "/cp_ajax_image_uploader/classipress_files_backup/step-functions.php", TEMPLATEPATH . "/includes/forms/step-functions.php");
}
register_deactivation_hook( __FILE__, 'cp_ajax_uploader_deactivate');

function cp_ajax_uploader_init()
{
	wp_enqueue_script('jquery');
}
add_action('init', 'cp_ajax_uploader_init');

function cp_ajax_uploader_shortcode($atts) 
{
	global $load_ajax_multi_upload;
	global $packed;
	extract(shortcode_atts(array(
		'path' => 'files/',
		'starton' => 'manually',
		'buttoncaption' => 'Upload',
		'multi' => 'false',
		'afterupload' => 'link',
		'onprogress' => 'percentage',
		'thumbnails' => '',
		'thumbnailsfolders' => '',
		'fileext' => '',
		'filedesc' => '',
		'method' => 'post',
		'thumbnailsafterupload' => '',
		'maxsize' => '',
		'hidebutton' => 'false',
		'button' => '',
		'bwidth' => '',
		'bheight' => '',
		'buttontext' => '',
		'queueid' => '',
		'data' => '',
		'removedata' => '',
		'fileslimit' => '',
		'allowremove' => 'false',
		'sessionid' => '',
		'ajax' => 'false',
		'ajaxinfoid' => '',
		'ajaxloaderid' => '',
		'uploadscript' => 'upload.php',
		'wmode' => '',
		'packed' => 'false',
		'scriptsonly' => 'false'
	), $atts));
	$load_ajax_multi_upload = true;
	if($scriptsonly=="false")
	{
		$amu = "<div id='queue'><div id='drag-drop-inside'><p class='drag-drop-info'>Drop up to ".get_option('cp_num_images')." images here</p></div></div>";
		$amu .= '<input id="file_upload" name="Filedata" type="file" multiple="true">';
	}
	?>
	<style>
		
	#queue #drag-drop-inside{margin: 40px auto 0; width: 250px;}
	#queue {
			height:auto;
			width:auto;
			min-height:100px;
			margin-bottom:20px;
			border: 4px dashed #DDDDDD;
		}
		
		#drag-drop-inside p.drag-drop-info {
    			font-size: 20px;
		}
		#drag-drop-inside p {
    		text-align: center;
		}
		.drag-over{
			border-color:#83b4d8 !important;
		}
	</style>
	<script type="text/javascript">
		function delete_queue(obj)
		{
			var attach_id = jQuery(obj).parent().attr('attachment');
			data = "action=remove&attach_id="+attach_id;
			url = amuurl+'/uploadifive.php';

			jQuery.ajax({
				url: url,
				data: data,
				async: false,
				success: function(data){
					
				}
			});
		}
		
		<?php $timestamp = time();
			$maximages = get_option('cp_num_images');
		?>
		var max= "<?= $maximages ?>";
		
		jQuery(document).ready(function() {
			jQuery('#file_upload').uploadifive({
				'auto'             : true,
				'formData'         : {
									   'timestamp' : '<?php echo $timestamp;?>',
									   'token'     : '<?php echo md5('unique_salt' . $timestamp);?>',
									   'action'	   : 'upload'
				                     },
				'queueID'          : 'queue',
				'queueSizeLimit'   : max,
				'uploadScript'     : amuurl+'/uploadifive.php',
				'onDrop'		   : function(file, fileDropCount){},
				'onCancel'		   : function(file, data){
					
				},
				'onUploadComplete' : function(file, data) {
					jQuery("#queue").removeClass("drag-over")
				}
			});
		});
	</script>
	<?php 
	return $amu;
}
add_shortcode('cp_ajax_uploader', 'cp_ajax_uploader_shortcode');
 
function cp_ajax_uploader_footer() 
{
	global $load_ajax_multi_upload;
	global $packed;
	if (!$load_ajax_multi_upload)
		return;
	echo "
	<script type='text/javascript'>
		var amuurl = '" . plugins_url('', __FILE__) . "';
	</script>";
	if($packed=='false')
	{
		wp_register_script('upload.min', plugins_url('js/jquery.uploadifive.min.js', __FILE__), array('jquery'));
		wp_print_scripts(array('upload.min'));
	}
	else
	{
		wp_register_script('upload.min', plugins_url('js/jquery.uploadifive.min.js', __FILE__), array('jquery'));
		wp_print_scripts(array('upload.min'));
	}
	wp_register_style('cp_ajax_uploader_style', plugins_url('uploadifive.css', __FILE__));
	wp_print_styles(array('cp_ajax_uploader_style'));
}
add_action('wp_footer', 'cp_ajax_uploader_footer');
?>