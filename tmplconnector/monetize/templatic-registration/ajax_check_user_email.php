<?php
require("../../../../../../wp-load.php");	
global $wpdb,$current_user;
if($current_user->ID)
$subsql = " and ID != '".$current_user->ID."'";
if(isset($_REQUEST['user_email']) && $_REQUEST['user_email']!= '' )
{
	$user_email = $_REQUEST['user_email'];
	$cur_user_email = $current_user->user_email;	
	if($cur_user_email != $user_email){
		$count_email =  email_exists($user_email); /* check email id registered/valid */
	}
	echo $count_email.",email";exit;
}
elseif(isset($_REQUEST['user_fname']) && $_REQUEST['user_fname']!= '')
{
	$user_fname = $_REQUEST['user_fname'];
	$cur_user_login = $current_user->user_login;	
	if($cur_user_login != $user_fname){
		$user = get_user_by('login',$user_fname);
	}
	$count_fname = count($user->ID);
	echo $count_fname.",fname";exit;
}
?>