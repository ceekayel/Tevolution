<?php
$file = dirname(__FILE__);
$content_dir = explode("/",WP_CONTENT_DIR);
$file = substr($file,0,stripos($file, $content_dir[1]));
require($file . "/wp-load.php");
if(isset($_REQUEST['pid']))
   {
	   wp_delete_attachment($_REQUEST['pid']);
	   $uploaddir = get_image_phy_destination_path_plugin();
	   $image_name = $_GET["imagename"];
	   $path_info = pathinfo($image_name);
	   $file_extension = $path_info["extension"];
   	   $image_name = basename($image_name,".".$file_extension);
	   //$expImg = strlen(end(explode("-",$image_name)));
	   //$finalImg = substr($image_name,0,-($expImg + 1));
	   @unlink($uploaddir.$image_name.".".$file_extension);
	   @unlink($uploaddir.$image_name."-150X150.".$file_extension);
	   @unlink($uploaddir.$image_name."-300X300.".$file_extension);
   }
if(isset($_GET["imagename"]) && $_GET["imagename"]!="")
{
	// remove from folder too
	$uploaddir = TEMPLATEPATH."/images/tmp/";
	$image_name = $_GET["imagename"];
	@unlink($uploaddir.$image_name);
	echo 'deleted';
}
?>