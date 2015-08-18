<?php

require( "../../../../../../wp-load.php");
$my_post_type = explode(",",$_REQUEST['post_type']);
?>
<li>
    <input type="checkbox" name="selectall" id="selectall" class="checkbox" onclick="displaychk_frm();" />
    <label for="selectall">&nbsp;<?php echo __('Select All',DOMAIN); ?></label>
</li>
<?php
$pkg_id = $_REQUEST['package_id'];
$scats = $_REQUEST['scats'];
$pid = explode(',',$scats);
if($_REQUEST['post_type'] == 'all' || $_REQUEST['post_type'] == 'all,')
{
	$custom_post_types_args = array();
	
	$custom_post_types = get_option("templatic_custom_post");
	tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>'category','popular_cats' => true,'selected_cats'=>$pid ) );
	foreach ($custom_post_types as $content_type=>$content_type_label) {
		//@get_wp_category_checklist_plugin($content_type_label['slugs'][0],'');
		$taxonomy = $content_type_label['slugs'][0];
		
		echo "<li><label style='font-weight:bold;'>".$content_type_label['taxonomies'][0]."</label></li>";
		tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>$taxonomy,'popular_cats' => true,'selected_cats'=>$pid ) );
	}
}
else
{
	
	$my_post_type = explode(",",substr($_REQUEST['post_type'],0,-1));	
	//get_wp_category_checklist_plugin('category','');
	foreach($my_post_type as $_my_post_type)
	{
		if($_my_post_type!='all'){
			$taxonomy = get_taxonomy( $_my_post_type );
			echo "<li><label style='font-weight:bold;'>".$taxonomy->labels->name."</label></li>";
			tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>$_my_post_type,'popular_cats' => true,'selected_cats'=>$pid ) );
		}
	}
}
?>