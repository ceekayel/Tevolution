<?php
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
	 $finalName=str_replace(' ','',$finalName);
	 $file = $uploaddir .$finalName ;
	 $file_ext= substr($file, -4, 4);		
	 if(in_array($file_ext,$extension_file))
	 {
		 if (move_uploaded_file($_FILES['uploadfile']['tmp_name'][$key], $file))
		 {
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