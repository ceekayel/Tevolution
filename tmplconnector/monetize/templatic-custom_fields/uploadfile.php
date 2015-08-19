<?php
/*
 * upload image for multi image uploader
 */ 
require("../../../../../../wp-load.php");
$structure = TEMPLATEPATH."/images/";
if(!is_dir($structure."tmp"))
{
	if (!mkdir($structure."tmp", 0777, true)) 
	 {
		die('Failed to create folders...');
	 }
}
$uploaddir = TEMPLATEPATH."/images/tmp/";
$nam = $_FILES['uploadfile']['name'];
$upload = '';
if($_FILES['uploadfile']['size'])
{
	$file_size= $_FILES['uploadfile']['size'];
}
$tmpdata = get_option('templatic_settings');
$limit_size =  $tmpdata['templatic_image_size'];
if(!$limit_size)
 {
	$limit_size = 50;
	update_option('templatic_image_size',$limit_size);
 }
 
if($file_size[0])
{
	if(($file_size[0]/1024) >= $limit_size)
	 {
		echo 'LIMIT';
		exit;
	 }
}
if(count($nam) > 10)
  {
    echo count($nam);
	die;
  }
  
global $extension_file;
foreach($nam as $key=>$_nam)
{
	 $path_info = pathinfo($_nam);
	 $file_extension = $path_info["extension"];
	 $finalName = basename($_nam,".$file_extension").time().".".$file_extension;
	 $finalCropName = basename($_nam,".$file_extension").time()."-80x80.".$file_extension;
	 $finalName=str_replace(' ','',$finalName);
	 $file = $uploaddir .$finalName ;
	 $file_ext= substr($file, -4, 4);		
	 if(in_array($file_ext,$extension_file))
	 {
		 if (move_uploaded_file($_FILES['uploadfile']['tmp_name'][$key], $file))
		 {
			 /* $filename should be the path to a file in the upload directory.*/
			$filename = $file;

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
			echo $upload = $finalName.",";
		 }
		 else
		 {
			echo "error";
		 }
	 }else
	 	echo 'error';
}
?>