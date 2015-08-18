<?php
/* upload image while drag and drop in multi image field. */
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
if($file_size)
{
	if(($file_size/1024) >= $limit_size)
	 {
		echo json_encode('LIMIT');
		exit;
	 }
}
if(count($nam) > 10)
{
	echo json_encode(count($nam));
	die;
}
  
global $extension_file;

	 $path_info = pathinfo($nam);
	 $file_extension = $path_info["extension"];
	 $finalName = basename($nam,".$file_extension").time().".".$file_extension;
	 $finalCropName = basename($nam,".$file_extension").time()."-80x80.".$file_extension;
	 $finalName=str_replace(array(' ','-','_'),array('','',''),$finalName);
	 $file = $uploaddir .$finalName ;
	 $file_ext= substr($file, -4, 4);		
	 if(in_array($file_ext,$extension_file))
	 {
		 if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file))
		 {
				
				/* $filename should be the path to a file in the upload directory. */
				$filename = $file;
				$upload[] = $finalName;
				echo json_encode($upload);
		 }
		 else
		 {
			echo json_encode("error");
		 }
	 }else
	 	echo json_encode('error');
exit;
?>