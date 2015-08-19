<?php
/*
 * ajax to delete an image from submit form for muslti upload image field.
 */
require("../../../../../../wp-load.php");	
if(isset($_REQUEST['image_arr']) && isset($_REQUEST['name']))
{	
	$image_arr=explode(',',$_REQUEST['image_arr']);
	if(($key = array_search($_REQUEST['name'], $image_arr)) !== false) {
		
		$uploaddir = TEMPLATEPATH."/images/tmp/";
		if(file_exists($uploaddir.$image_arr[$key]))
			@unlink($uploaddir.$image_arr[$key]);
   		unset($image_arr[$key]);
	}	
	$image_name=implode(',',$image_arr);
	echo $image_name;
}
else
{
	if(is_array($_REQUEST['i'])){
		$image_name=implode(',',$_REQUEST['i']);
	}else{
		$image_name=$_REQUEST['i'];
	}
	echo $image_name;
}
if(isset($_REQUEST['pid']))
{
   wp_delete_attachment($_REQUEST['pid']);
   $uploaddir = get_image_phy_destination_path_plugin();
   $image_name = $_GET["imagename"];
   $path_info = pathinfo($image_name);
   $file_extension = $path_info["extension"];
   $image_name = basename($image_name,".".$file_extension);
   /*$expImg = strlen(end(explode("-",$image_name)));*/
   /*$finalImg = substr($image_name,0,-($expImg + 1));*/
   @unlink($uploaddir.$image_name.".".$file_extension);
   @unlink($uploaddir.$image_name."-150X150.".$file_extension);
   @unlink($uploaddir.$image_name."-300X300.".$file_extension);
}
?>