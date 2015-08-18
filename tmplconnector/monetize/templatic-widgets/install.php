<?php
global $wp_query,$wpdb,$wp_rewrite,$post;
/**
 * conditions for activation Templatic Widgets
 */
if(@$_REQUEST['activated'] == 'templatic_widgets' && @$_REQUEST['true']==1){
		update_option('templatic_widgets','Active');
}else if(@$_REQUEST['deactivate'] == 'templatic_widgets' && @$_REQUEST['true']==0){
		delete_option('templatic_widgets');
}
function get_templatic_widgets_list()
{
	
	$list_of_widgest=array(			
		'templatic_browse_by_categories'=>'Browse By Categories',
		'templatic_browse_by_tag'=>'Browse By Tag',
		'templatic_aboust_us'=>'About Us',		
		'templatic_advanced_search'=>'Advanced Search',
		'templatic_people_list'=>'People Listing',
		'templatic_metakey_search'=>'Meta Key Search',
		
	);
	
	return $list_of_widgest;	
}
if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/install.php')){
	
	$list_of_widgest=get_templatic_widgets_list();
	$tmpdata = get_option('templatic_settings');
	$templatic_widgets =  @$tmpdata['templatic_widgets'];	
	
	foreach($list_of_widgest as $key=>$value): 		
	
		if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-widgets/'.$key.'_widget.php'))
		{				
			include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-widgets/".$key."_widget.php");			
		}		
	endforeach;
}
function templatic_exclude_taxonomies( $taxonomy ) {
	$filters = array( '', 'nav_menu' );
	$filters = apply_filters( 'templatic_exclude_taxonomies', $filters );
	return ( ! in_array( $taxonomy->name, $filters ) );
}
?>