<?php
define( 'DOING_AJAX', true );
require("../../../../../../wp-load.php");
if(isset($_REQUEST['ptype']) &&$_REQUEST['ptype'] == 'favorite'){
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		global  $sitepress;
		$sitepress->switch_lang($_REQUEST['language']);
	}
	if(isset($_REQUEST['action']) && $_REQUEST['action']=='add')	{
		if(isset($_REQUEST['st_date']) && $_REQUEST['st_date'] != '' && $_REQUEST['st_date'] != 'undefined' )
		{
			if(isset($_REQUEST['language']) && $_REQUEST['language'] != '')
			{
				add_to_favorite($_REQUEST['pid'],$_REQUEST['language']);
			}
			else
			{
				add_to_favorite($_REQUEST['pid']);
			}
		}
		else
		{
			if(isset($_REQUEST['language']) && $_REQUEST['language'] != '')
			{
				add_to_favorite($_REQUEST['pid'],$_REQUEST['language']);
			}
			else
			{
				add_to_favorite($_REQUEST['pid']);
			}
		}
	}else{
		if(isset($_REQUEST['st_date']) && $_REQUEST['st_date'] != '' && $_REQUEST['st_date'] != 'undefined')
		{
			if(isset($_REQUEST['language']) && $_REQUEST['language'] != '')
			{
				remove_from_favorite($_REQUEST['pid'],$_REQUEST['language']);
			}
			else
			{
				remove_from_favorite($_REQUEST['pid']);
			}
		}
		else
		{
			if(isset($_REQUEST['language']) && $_REQUEST['language'] != '')
			{
				remove_from_favorite($_REQUEST['pid'],$_REQUEST['language']);
			}
			else
			{
				remove_from_favorite($_REQUEST['pid']);
			}
		}
	}
}
?>