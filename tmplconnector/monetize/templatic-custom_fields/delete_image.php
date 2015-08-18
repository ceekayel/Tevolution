<?php 
/*
 * delete uploaded image from submit form.
 */
require("../../../../../../wp-load.php");
if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'delete')
{
   wp_delete_attachment($_REQUEST['pid']);
   $uploaddir = get_image_phy_destination_path_plugin();
   $image_name = $_REQUEST["name"];
   $path_info = pathinfo($image_name);
   $file_extension = $path_info["extension"];
   $image_name = basename($image_name,".".$file_extension);
   /*$expImg = strlen(end(explode("-",$image_name)));*/
   /*$finalImg = substr($image_name,0,-($expImg + 1));*/
   @unlink($uploaddir.$image_name.".".$file_extension);
}

if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'delete')
{
	/* remove from folder too*/
	$uploaddir = TEMPLATEPATH."/images/tmp/";
	$image_name = $_REQUEST["name"];
	$path_info = pathinfo($image_name);
	$file_extension = $path_info["extension"];
	$image_name = basename($image_name,".".$file_extension);
	@unlink($uploaddir.$image_name.".".$file_extension);
 	@unlink($uploaddir.$image_name."-60X60.".$file_extension);
	echo 'deleted';
}
exit;
?>