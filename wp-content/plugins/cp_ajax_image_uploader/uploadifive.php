<?php
/*
UploadiFive
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
*/

// Set the uplaod directory
error_reporting(0);
if($_POST["PHPSESSID"]!="")
{
	session_id($_POST["PHPSESSID"]);
	//session_name("your_session_name"); uncomment this if your session has a name
	session_start();
}
if($_POST["action"]=="upload")
{
	if(!empty($_FILES))
	{
		$result = array();
		$tempFile = $_FILES["Filedata"]["tmp_name"];
		$targetPath = $_POST["path"];
		
		require_once("../../../wp-config.php");
		$wp->init();
		$wp->parse_request();
		$wp->query_posts();
		$wp->register_globals();
		$wp->send_headers();
		$uploadsInfo = wp_upload_dir();
		$targetPath = "../../uploads" . $uploadsInfo["subdir"] . "/";
		
		$pathinfo = pathinfo($_FILES['Filedata']['name']);
		$fileExt = $pathinfo["extension"];
		//if($fileExt!="php" && $fileExt!="html" && $fileExt!="htm" && $fileExt!="js")//if you want to prevent from upload the files with given extensions uncomment this line
		//{//if you want to prevent from upload the files with given extensions uncomment this line
			$fileName = stripslashes($pathinfo["filename"]) . time();
			$targetFile =  $targetPath . $fileName . "." . $fileExt;
			$realPath = realpath($targetPath);
			$documentRoot = realpath($_SERVER["DOCUMENT_ROOT"]);
			$realTargetPath = str_replace($documentRoot, "", $realPath) . "/";
			
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			
			$overrides = array( 'test_form' => false );
			//move image to the WP defined upload directory and set correct permissions
			if($file = wp_handle_upload( $_FILES["Filedata"], $overrides ))
			{
				$wp_filetype = wp_check_filetype(basename($realTargetPath . $fileName . "." . $fileExt), null );
				$attachment = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => preg_replace('/\.[^.]+$/', '', basename($realTargetPath . $fileName . "." . $fileExt)),
					'post_content' => '',
					'post_status' => 'inherit'
				);
				$attach_id = wp_insert_attachment( $attachment, $file["file"], null );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $file["file"] );
				wp_update_attachment_metadata( $attach_id,  $attach_data );
				//generate thumbnails after the main file is uploaded
				if($_POST["thumbnails"]!="")
				{
					include("functions.php");
					$thumbnailsExplode = explode(",", $_POST["thumbnails"]);
					if($_POST["thumbnailsFolders"]!="")
						$thumbnailsFoldersExplode = explode(",", $_POST["thumbnailsFolders"]);
					$result["thumbnails"] = array();
					for($i=0; $i<count($thumbnailsExplode); $i++)
					{
						$dimensions = explode("x", trim($thumbnailsExplode[$i]));
						$sourceFile = $targetFile;
						$destinationFile = (trim($thumbnailsFoldersExplode[$i])!="" ? trim($thumbnailsFoldersExplode[$i]):$targetPath) . "thumb" . $i . "_" . $fileName . "." . $fileExt; 
						resizeImage($dimensions[0], $dimensions[1], $sourceFile, $destinationFile);
						$result["thumbnails"][] = "thumb" . $i . "_" . $fileName . "." . $fileExt;
					}
				}
			}
			else
				$result["error"] .= " Upload failed!";
		/*}
		else
			$result["error"] .= " Cannot upload " . $fileExt . " files!";
		//if you want to prevent from upload the files with given extensions uncomment this block
		*/
		$pathinfo = pathinfo($file["url"]);
		$result["path"] = $pathinfo["dirname"] . "/";
		$result["filename"] = $pathinfo["filename"] . "-75x75." . (strtolower($pathinfo["extension"])=="jpeg" ? "jpg" : $pathinfo["extension"]);
		$result["extension"] = $pathinfo["extension"];
		$result["attach_id"] = $attach_id;
		echo json_encode($result);
		exit();
	}
}
else if($_REQUEST["action"]=="remove")
{
	require_once("../../../wp-config.php");
	$wp->init();
	$wp->parse_request();
	$wp->query_posts();
	$wp->register_globals();
	$wp->send_headers();
	$uploadsInfo = wp_upload_dir();
	$targetPath = "../../uploads" . $uploadsInfo["subdir"] . "/";
	$message = "";
	
	wp_delete_attachment($_REQUEST["attach_id"]);
	echo $message;
}
?>