<?php
/*
 * ajax to upload file for single upload image.
 */
require("../../../../../../wp-load.php");
session_start();
$structure = TEMPLATEPATH."/images/";
if(!is_dir($structure."tmp"))
{
	if (!mkdir($structure."tmp", 0777, true)) 
	 {
		die('Failed to create folders...');
	 }
}
$uploaddir = TEMPLATEPATH."/images/tmp/";

foreach($_FILES as $key=>$val)
{
	if(isset($_FILES[$key]))
	{
		$ret = array();

		$error =$_FILES[$key]["error"];
		/*You need to handle  both cases
		If Any browser does not support serializing of multiple files using FormData() */
		if(!is_array($_FILES[$key]["name"])) /*single file*/
		{
			$srch_arr = array(' ',"'",'"','?','*','!','@','#','$','%','^','&','(',')','+','=');
			$replace_arr = array('_','','','','','','','','','','','','','','','');
			
			$fileName1 = pathinfo($_FILES[$key]["name"], PATHINFO_FILENAME).'_'.rand(0,9999).'.'.pathinfo($_FILES[$key]["name"], PATHINFO_EXTENSION);
			
			$fileName = $name = str_replace($srch_arr,$replace_arr,$fileName1);
			
			/*save the images in tmp folder of parent theme directory*/
			
			if(!move_uploaded_file($_FILES[$key]["tmp_name"],$uploaddir.$fileName))
			{
				$ret[]= 'error';
				echo json_encode($ret);exit;
			}
			
			/* Check the type of tile. We'll use this as the 'post_mime_type'.*/
			$filetype = wp_check_filetype( basename( $filename ), null );

			/* Get the path to the upload directory.*/
			$wp_upload_dir = wp_upload_dir();

			/* Prepare an array of post data for the attachment.*/
			$attachment = array(
				'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
				'post_mime_type' => $filetype['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);

			/* Insert the attachment.*/

			$img_attachment=substr($wp_upload_dir['subdir'].'/'.basename($filename),1);
			$attach_id = wp_insert_attachment( $attachment, $img_attachment);

			/* Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.*/
			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			/* Generate the metadata for the attachment, and update the database record.*/
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
			
			wp_update_attachment_metadata( $attach_id, $attach_data );
			
			/* copy the image from tmp folder to wordpress folder */
			$wp_upload_dir = wp_upload_dir();
			$path = $wp_upload_dir['path'];
			$url = $wp_upload_dir['url'];
			$destination_path = $wp_upload_dir['path'].'/';
			
			$name = str_replace($srch_arr,$replace_arr,$fileName1);
			$tmp_name = $_FILES[$key]['tmp_name'];
			$target_path = $destination_path . str_replace(',','',$name);
			$extension_file = array('.php','.js');
			$file_ext= substr($target_path, -4, 4);	
			
			if(!in_array($file_ext,$extension_file))
			{
				if(@copy($uploaddir.$fileName, $target_path))
				{
					$imagepath1 = $url."/".$name;
					$_SESSION['upload_file'][$key] = $imagepath1;/* save the image path in session */
				}
			}
			$ret[]= $fileName;
			
			/* regenerate image sizes */
			$file = get_attached_file( $attach_id );
			wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $file ) );
		}
		else  /*Multiple files, file[]*/
		{
		  $fileCount = count($_FILES[$key]["name"]);
		  for($i=0; $i < $fileCount; $i++)
		  {
			$fileName = $_FILES[$key]["name"][$i];
			/*save the images in tmp folder of parent theme directory*/
			if(!move_uploaded_file($_FILES[$key]["tmp_name"][$i],$uploaddir.$fileName))
			{
				$ret[]= 'error';
				echo json_encode($ret);exit;
			}
			$filename = $uploaddir.$fileName;

			/* Check the type of tile. We'll use this as the 'post_mime_type'.*/
			$filetype = wp_check_filetype( basename( $filename ), null );

			/* Get the path to the upload directory.*/
			$wp_upload_dir = wp_upload_dir();

			/* Prepare an array of post data for the attachment.*/
			$attachment = array(
				'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
				'post_mime_type' => $filetype['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);

			/* Insert the attachment.*/
			$attach_id = wp_insert_attachment( $attachment, $filename);

			/* Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.*/
			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			/* Generate the metadata for the attachment, and update the database record.*/
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
			wp_update_attachment_metadata( $attach_id, $attach_data );
			
			/* copy the image from tmp folder to wordpress folder */
			$wp_upload_dir = wp_upload_dir();
			$path = $wp_upload_dir['path'];
			$url = $wp_upload_dir['url'];
			$destination_path = $wp_upload_dir['path'].'/';
			$srch_arr = array(' ',"'",'"','?','*','!','@','#','$','%','^','&','(',')','+','=');
			$replace_arr = array('_','','','','','','','','','','','','','','','');
			$name = str_replace($srch_arr,$replace_arr,$_FILES[$key]['name']);
			$tmp_name = $_FILES[$key]['tmp_name'];
			$target_path = $destination_path . str_replace(',','',$name);
			$extension_file = array('.php','.js');
			$file_ext= substr($target_path, -4, 4);	
			
			if(!in_array($file_ext,$extension_file))
			{
				if(@copy($uploaddir.$fileName, $target_path))
				{
					$imagepath1 = $url."/".$name;
					$_SESSION['upload_file'][$key] = $imagepath1;/* save the image path in session */
				}
			}
			
			$ret[]= $fileName;
		  }
		
		}
		echo json_encode($ret);exit;
	 }
}
?>