<?php
global $wp_query,$wpdb;
/**-- condition for activate bulk upload --**/
if((isset($_REQUEST['activated']) && $_REQUEST['activated'] == 'bulk_upload') && (isset($_REQUEST['true']) && $_REQUEST['true']==1)){
		update_option('bulk_upload','Active');
}elseif((isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'bulk_upload') && (isset($_REQUEST['true']) && $_REQUEST['true']==0)){
		delete_option('bulk_upload');
}
/**-- Add submenu under Templatic main menu--**/
if(is_active_addons('bulk_upload')){
	add_action('templ_add_admin_menu_', 'templ_add_submenu_bulk_upload',20);
	
}
function templ_add_submenu_bulk_upload()
{
	$menu_title = __('Bulk Import/Export',ADMINDOMAIN);	
	add_submenu_page('templatic_system_menu', "",   '<span class="tevolution-menu-separator" style="display:block; 1px -5px;  padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>',"administrator", "admin.php?page=templatic_system_menu"  );
	add_submenu_page('templatic_system_menu', $menu_title,$menu_title, 'administrator', 'bulk_upload', 'templ_bulk_upload');			
	
}

add_action('tevolution_custom_fields','tevolution_address_custom_fields',10,4);
function tevolution_address_custom_fields($post_id,$data,$k,$v){
	
	if($k=='address' && $data['geo_latitude']=='' && $data['geo_longitude']==''){		
		$http=(is_ssl())?"https://":"http://";
		$v = str_replace(' ','+',convert_chars(addslashes(iconv('', 'utf-8',$v))));
		$geocode = file_get_contents($http.'maps.google.com/maps/api/geocode/json?address='.$v.'&sensor=false');
		$output= json_decode($geocode);
		$lat = $output->results[0]->geometry->location->lat;
		$long = $output->results[0]->geometry->location->lng;
		update_post_meta($post_id, 'geo_latitude', convert_chars(addslashes(iconv('', 'utf-8',$lat))));
		update_post_meta($post_id, 'geo_longitude', convert_chars(addslashes(iconv('', 'utf-8',$long))));		
	}
	
}
add_action('tevolution_custom_fields','tevolution_postal_custom_fields',20,4);
function tevolution_postal_custom_fields($post_id,$data,$k,$v){
	global $wpdb;
	$postcodes_table = $wpdb->prefix . "postcodes";	
	if($wpdb->get_var("SHOW TABLES LIKE \"$postcodes_table\"") == $postcodes_table) { 		
		if($k=='address' && trim($data['geo_latitude'])=='' && trim($data['geo_longitude'])==''){
			$http=(is_ssl())?"https://":"http://";
			$v = str_replace(' ','+',convert_chars(addslashes(iconv('', 'utf-8',$v))));
			$geocode = file_get_contents($http.'maps.google.com/maps/api/geocode/json?address='.$v.'&sensor=false');
			$output= json_decode($geocode);			
			$lat = $output->results[0]->geometry->location->lat;
			$long = $output->results[0]->geometry->location->lng;			
			update_post_meta($post_id, 'geo_latitude', convert_chars(addslashes(iconv('', 'utf-8',$lat))));
			update_post_meta($post_id, 'geo_longitude', convert_chars(addslashes(iconv('', 'utf-8',$long))));	
			echo "<br />".convert_chars(addslashes(iconv('', 'utf-8',$lat)))."==".convert_chars(addslashes(iconv('', 'utf-8',$long)))."==".$k."==".$post_id;			
			$pcid = $wpdb->get_results($wpdb->prepare("select pcid from $postcodes_table where post_id = %d",$post_id));
			if(count($pcid)!=0){
				$wpdb->update($postcodes_table , array('post_type' => $data['templatic_post_type'],'address'=>$data['address'],'latitude'=> $lat,'longitude'=> $long), array('pcid' => $pcid,'post_id'=>$post_id) );	
			}else{
				$wpdb->query( $wpdb->prepare("INSERT INTO $postcodes_table ( post_id,post_type,address,latitude,longitude) VALUES ( %d, %s, %s, %s, %s)", $post_id,$data['templatic_post_type'],$data['address'],$lat,$long ) );
			}
		}
		elseif($k=='address' && $data['geo_latitude']!='' && $data['geo_longitude']!='')
		{ echo "sdfsdfsdf";
			$pcid = $wpdb->get_results($wpdb->prepare("select pcid from $postcodes_table where post_id = %d",$post_id));
			if(count($pcid)!=0){
				$wpdb->update($postcodes_table , array('post_type' => $data['templatic_post_type'],'address'=>$data['address'],'latitude'=> $data['geo_latitude'],'longitude'=> $data['geo_longitude']), array('pcid' => $pcid,'post_id'=>$post_id) );	
			}else{ 
				$wpdb->query( $wpdb->prepare("INSERT INTO $postcodes_table ( post_id,post_type,address,latitude,longitude) VALUES ( %d, %s, %s, %s, %s)", $post_id,$data['templatic_post_type'],$data['address'],$data['geo_latitude'],$data['geo_longitude'] ) );
			}
		}
	}
}
/*	included file containing bulk upload functionality	*/
function templ_bulk_upload()
{
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-bulk_upload/templatic_bulk_upload.php')){
		include_once(TEMPL_MONETIZE_FOLDER_PATH.'templatic-bulk_upload/templatic_bulk_upload.php');
	}
}
?>