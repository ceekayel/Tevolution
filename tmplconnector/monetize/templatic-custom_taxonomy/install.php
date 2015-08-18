<?php
/**-- conditions for activation of Custom Taxonomy --**/
if(((isset($_REQUEST['activated']) && $_REQUEST['activated'] == 'custom_taxonomy') && (isset($_REQUEST['true']) && $_REQUEST['true']==1)) || (isset($_REQUEST['activated']) && $_REQUEST['activated']=='true'))
{
		update_option('custom_taxonomy','Active');
		/* ADDING A FIELD TERM_PRICE IN TERMS TABLE */
		global $wpdb;
		$field_check = $wpdb->get_var("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_price'");
		if('term_price' != $field_check){
		$wpdb->query("ALTER TABLE $wpdb->terms ADD term_price varchar(100) NOT NULL DEFAULT '0'");
		}
}
else if((isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'custom_taxonomy') && (isset($_REQUEST['true']) && $_REQUEST['true']==0))
{
		delete_option('custom_taxonomy');
}


/*
 * Check custom taxonomy module activate
 */
if(is_active_addons('custom_taxonomy')){
	include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_taxonomy/custom_post_type_lang.php");
	include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_taxonomy/taxonomy_functions.php");
	
	add_action('templ_add_admin_menu_', 'templ_add_submenu_taxonomy',11);
	add_filter('set-screen-option', 'taxonomy_set_screen_option', 10, 3);
	add_action( 'init', 'create_post_type' );  /*create custom post type function */
	add_action( 'init', 'create_custom_taxonomy' ); /* create custom post type taxonomy*/
	add_action( 'init', 'create_custom_tags' ); /* Create custom post type tags */
	
	add_filter('templatic_general_settings_tab', 'email_setting',13); 
	add_action('templatic_general_data','taxonomy_email_setting_data',12);
	
	
}

/**-- coding to add submenu under main menu--**/
function templ_add_submenu_taxonomy()
{
	$menu_title = __('Custom Post Types',ADMINDOMAIN);
	global $taxonomy_screen_option;
	$taxonomy_screen_option = add_submenu_page('templatic_system_menu', $menu_title,$menu_title, 'administrator', 'custom_taxonomy', 'add_custom_taxonomy');
	add_action("load-$taxonomy_screen_option", "taxonomy_screen_options");
}

/* Function for screen option */
function taxonomy_screen_options() {
 	global $taxonomy_screen_option;
 	$screen = get_current_screen();
 	// get out of here if we are not on our settings page
	if(!is_object($screen) || $screen->id != $taxonomy_screen_option)
		return;
 
	$args = array( 'label'   => __('Taxonomy per page', DOMAIN),
				'default' => 10,
				'option'  => 'taxonomy_per_page'
	);
	add_screen_option( 'per_page', $args );
}

function taxonomy_set_screen_option($status, $option, $value) {
	if ( 'taxonomy_per_page' == $option ) return $value;
}

/* NAME : Add taxonomy sub menu page
DESCRIPTION : this function adds a submenu page for creating or editing the taxonomies */
function add_custom_taxonomy()
{
	if((isset($_REQUEST['action']) &&  $_REQUEST['action']== 'add_taxonomy') || (isset($_REQUEST['action']) && $_REQUEST['action']== 'edit-type'))
	 {
		 include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_taxonomy/add_custom_taxonomy.php");
	 }
	else
	 {
		 include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_taxonomy/manage_custom_taxonomy.php");
	 }
}

/* EOF - add submenu page for taxonomies */
if((isset($_REQUEST['page']) && $_REQUEST['page'] == 'delete-type') && is_active_addons('custom_taxonomy'))
{ 
	 $post_type = get_option("templatic_custom_post");
	 $taxonomy = get_option("templatic_custom_taxonomy");
	 $tag = get_option("templatic_custom_tags");
	 $taxonomy_slug = $post_type[$_REQUEST['post-type']]['slugs'][0];
	 $tag_slug = $post_type[$_REQUEST['post-type']]['slugs'][1];
	 
	 unset($post_type[$_REQUEST['post-type']]);
	 unset($taxonomy[$taxonomy_slug]);
	 unset($tag[$tag_slug]);
	 update_option("templatic_custom_post",$post_type);
	 update_option("templatic_custom_taxonomy",$taxonomy);
	 update_option("templatic_custom_tags",$tag);
	 if(file_exists(get_template_directory()."/taxonomy-".$taxonomy_slug.".php"))
		unlink(get_template_directory()."/taxonomy-".$taxonomy_slug.".php");
	 if(file_exists(get_template_directory()."/taxonomy-".$tag_slug.".php"))
		unlink(get_template_directory()."/taxonomy-".$tag_slug.".php");
	 if(file_exists(get_template_directory()."/single-".$_REQUEST['post-type'].".php"))
		unlink(get_template_directory()."/single-".$_REQUEST['post-type'].".php");
	 wp_redirect(admin_url("admin.php?page=custom_taxonomy&custom_msg_type=delete"));
	 exit;
}
/* NAME : function to load scripts
DESCRIPTION : this function will load all the jscripts */
function upload_admin_scripts()
{
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('my-upload', __(plugin_dir_url( __FILE__ ),DOMAIN).'/upload-script.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('my-upload');
}
/* EOF - load scripts */
/* NAME : function to load the css
DESCRIPTION : this function will load all the css scripts */ 
function upload_admin_styles() {
	wp_enqueue_style('thickbox');
}
/* EOF - load css */ 
if((isset($_REQUEST['action']) && $_REQUEST['action']=="add_taxonomy") || (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit-type')){
	if (is_active_addons('custom_taxonomy')) {
		add_action('admin_print_scripts', 'upload_admin_scripts');
		add_action('admin_print_styles', 'upload_admin_styles');
	}
}



/* Register all custom posts, taxonomies, tags from here */

function create_post_type() {
	
	add_image_size( 'tevolution_thumbnail', 60, 60, true );	
	$args = get_option('templatic_custom_post');
	if($args):
		foreach($args as $key=> $_args)
		{
			register_post_type($key,$args[$key]);
		}
		if(isset($_REQUEST['post_type']))
		{
			$post_type = $_REQUEST['post_type'];
			if( $post_type != "page" && $post_type != "post"  && $post_type != "product" && $post_type != "attachment" && $post_type != "revision" && $post_type != "nav_menu_item" ){
				if(in_array($post_type,tevolution_get_post_type())){
					add_filter( 'manage_edit-'.$post_type.'_columns', 'templatic_edit_taxonomy_columns',10,2);
					add_action('manage_posts_custom_column','templatic_manage_taxonomy_columns',10,2);
					add_filter('post_row_actions', 'templatic_extra_actions', 10, 2);
				}
			}
		}
	endif;
}

function templatic_extra_actions($actions, $post){
	global $wpdb;	
	$transaction_table = $wpdb->prefix.'transactions';
	$sql = "select trans_id from $transaction_table where 1=1 and post_id='".$post->ID."' AND package_type is NULL order by trans_id DESC LIMIT 1";
	$trans_id = $wpdb->get_results($sql);	
	if(!empty($trans_id) && $trans_id[0]->trans_id){
		$actions['tran_id']= __('Transaction ID:',DOMAIN).' <a href="'.site_url().'/wp-admin/admin.php?page=transcation&action=edit&trans_id='.$trans_id[0]->trans_id.'">'.$trans_id[0]->trans_id .'</a>';
	}
	return $actions; 
	
}

/*
NAME : create_custom_taxonomy
DESCRIPTION : Create custom taxonomy , Move taxonomies and detail page files in template directory 
*/	
function create_custom_taxonomy() {
	$args = get_option('templatic_custom_taxonomy');
	$args1 = get_option('templatic_custom_post');
	if($args):
		foreach($args as $key=> $_args)
		{
			register_taxonomy($_args['labels']['singular_name'],array(@$_args['post_slug']),$args[$key]);
			$_name = @$args1[$_args["post_slug"]]['labels']['name'];
			
			if(!in_array($_args["post_slug"],array('listing','event')))
				register_sidebars(1,array('id'=>'after_'.$_args["labels"]["singular_name"].'_header','name'=>sprintf(__('%s Category Pages - Below Header',DOMAIN),ucfirst($_name)),'description'=>sprintf(__('Widgets placed here appear on the %s category page below the header part, (design supported after tevolution 2.2)',DOMAIN),$_name),'before_widget'=>'<div id="%1$s" class="widget %2$s">','after_widget'=>'</div>','before_title'=>'<h3 class="widget-title">','after_title'=>'</h3>'));
			/*Listing page Sider bar */
			register_sidebars(1,array('id'=>''.$_args["labels"]["singular_name"].'_listing_sidebar','name'=> apply_filters('listing_page_sidebar_title',sprintf(__('%s Category Page Sidebar',DOMAIN),ucfirst($_name)),$_name),'description'=>sprintf(__('Display widgets in a sidebar on %s category pages.',DOMAIN),$_name),'before_widget'=>'<div id="%1$s" class="widget %2$s">','after_widget'=>'</div>','before_title'=>'<h3 class="widget-title">','after_title'=>'</h3>'));
			
			/*Single post Type sider bar  */
			register_sidebars(1,array('id'=>''.$_args["post_slug"].'_detail_sidebar','name'=>sprintf(__('%s Detail Page Sidebar',DOMAIN),ucfirst($_name)),'description'=>sprintf(__('Display widgets in a sidebar on single %s pages.',DOMAIN),$_name),'before_widget'=>'<div id="%1$s" class="widget %2$s">','after_widget'=>'</div>','before_title'=>'<h3 class="widget-title">','after_title'=>'</h3>'));
			
			/*Add post submit side bar*/
			register_sidebars(1,array('id'=>'add_'.$_args["post_slug"].'_submit_sidebar','name'=>sprintf(__('%s Add - Sidebar',DOMAIN),ucfirst($_name)),'description'=>sprintf(__('Display widgets in a sidebar that appears on %s submission pages.',DOMAIN),$_name),'before_widget'=>'<div id="%1$s" class="widget %2$s">','after_widget'=>'</div>','before_title'=>'<h3 class="widget-title">','after_title'=>'</h3>'));
			
			/*Add sidebar for listing page below header */
			
			
			
			 $taxonomy = $_args['labels']['singular_name']; /* DEFINE TAXONOMY */
			 /* CODE TO CALL THE FUNCTIONS WHICH MANAGE THE PRICE FIELD IN CATEGORIES */
			 if(isset($taxonomy) && $taxonomy == $_args['labels']['singular_name']) 
			 {
				if(is_active_addons('monetization'))
				{
					add_action($taxonomy.'_edit_form_fields','category_custom_fields_Edit');
					add_action($taxonomy.'_add_form_fields','category_custom_fields_AddField');
					add_action('edited_term','category_custom_fields_AlterField');
					add_action('created_'.$taxonomy,'category_custom_fields_AlterField');
					/* FILTERS TO MANAGE PRICE COLUMNS */
					add_filter('manage_edit-'.$taxonomy.'_columns', 'edit_price_cat_column');	
					add_filter('manage_'.$taxonomy.'_custom_column', 'tmpl_manage_price_cat_col', 10, 3);
				}
			 }
		}
	endif;
}	

function create_custom_tags() {
	$args = get_option('templatic_custom_tags');		
	$args1 = get_option('templatic_custom_post');
	if($args):
		foreach($args as $key=> $_args)
		{
			register_taxonomy($_args['labels']['singular_name'],@$_args['post_slug'],$args[$key]);
			$_name = @$args1[$_args["post_type"]]['labels']['name'];
			/*Listing page Sider bar */
			register_sidebars(1,array('id'=>''.$_args["labels"]["singular_name"].'_tag_listing_sidebar','name'=>sprintf(__('%s Tag Page Sidebar',DOMAIN),ucfirst($_name)),'description'=>sprintf(__('Display widgets in a sidebar on %s tag pages.',DOMAIN),$_name),'before_widget'=>'<div class="widget">','after_widget'=>'</div>','before_title'=>'<h3 class="widget-title">','after_title'=>'</h3>'));				
		}
	endif;
}

/*
NAME : templatic_edit_taxonomy_columns
DESCRIPTION : Return the columns name for backend listing
*/
function templatic_edit_taxonomy_columns( $columns )
{
	global $wpdb,$post;		
	$post_type = $_REQUEST['post_type'];
	wp_reset_query();
	$cus_post_type = get_post_meta($post_id,'template_post_type',true);
	remove_all_actions('posts_where');
			
	/* code to fetch the columns from custom fields */
	$args = array( 'post_type'      => 'custom_fields',
				'posts_per_page' => -1	,
				'post_status'    => array('publish'),
				'meta_query'     => array('relation' => 'AND',
								array(
									'key'     => 'post_type_'.$post_type.'',
									'value'   => array('all',$post_type),
									'compare' => 'IN',
									'type'    => 'text'
								),
								array(
									'key'     => 'show_in_column',
									'value'   =>  1,
									'compare' => '='
								),
								array(
									'key'     => 'is_active',
									'value'   =>  1,
									'compare' => '='
								)
				),
				'meta_key' => 'sort_order',
				'orderby' => 'meta_value_num',
				'meta_value_num'=>'sort_order',
				'order' => 'ASC'
			);
	$fld_meta_info = null;
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$fld_meta_info = new WP_Query($args);
	remove_filter('posts_join', 'custom_field_posts_where_filter');			
	
	
	/* Re-arrange the custom post type columns field */
	
	
	$columns1['cb']='<input type="checkbox" />';
	$columns1['title']=__('Title',DOMAIN);
	$columns1['post_image']=__('Image',DOMAIN);
	
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		if(function_exists('icl_get_languages')){
			$languages = icl_get_languages('skip_missing=0');
		}
		if(!empty($languages)){
			foreach($languages as $l){
				if(!$l['active']) echo '<a href="'.$l['url'].'">';
				if(!$l['active']) $country_flag .= '<img src="'.$l['country_flag_url'].'" height="12" alt="'.$l['language_code'].'" width="18" />'.' ';
				if(!$l['active']) echo '</a>';
			}
		}
		$columns1['icl_translations']=$country_flag;
	}
	
	
	
	$columns1['categories_']=__('Categories',DOMAIN);
	$columns1['author']=__('Author',DOMAIN);
	
	$fld_columns = array();		
	if($fld_meta_info->have_posts()){
		while ($fld_meta_info->have_posts()) : $fld_meta_info->the_post();				
			
			if($post->post_title!=''){
				$columns1[$post->post_name]=$post->post_title;
			}
			endwhile;
		wp_reset_query();
	}
	
	/* Add apply filters  tevolution_manage_edit columns before price package date and status */
	$columns1=apply_filters('tevolution_manage_edit-'.$post_type.'_columns',$columns1);
	
	if(is_active_addons('monetization'))
		$columns1['price_package'] = __('Price package',DOMAIN);
	$columns1['posted_on']     = __('Date',DOMAIN);
	if(is_active_addons('monetization'))
		$columns1['tran_status']        =__('Status',DOMAIN);
	$columns = array_merge($columns1,$fld_columns);
	
	return apply_filters('tevolution_change_edit-'.$post_type.'_columns',$columns);
}
/* END OF FUNCTION */
/*
NAME : templatic_manage_event_columns
DESCRIPTION : Return the value for specific column
*/
function templatic_manage_taxonomy_columns( $column, $post_id )
{
	global $post,$monetization,$wpdb;
	if(isset($_REQUEST['post_ID']))
		$post_id=$_REQUEST['post_ID'];
	
	$taxonomy ='';
	$post_type = @$_REQUEST['post_type'];
	$custom_post_types_args = array();  
	$custom_post_types = get_post_types($custom_post_types_args,'objects');
	if  ($custom_post_types) {
		 foreach ($custom_post_types as $content_type) {
		 
			if($content_type->name == $post_type){
			$taxonomy = @$content_type->slugs[0];
			$tags = @$content_type->slugs[1]; break;
			}
		
	  }
	}  				
	switch( $column ) { 
	case 'post_image'  :
			//tevolution_thumbnail
				if($post->post_parent)
					$post_id = $post->post_parent;
				else
					$post_id = $post->ID;
				if ( has_post_thumbnail()):						
					$post_image = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'tevolution_thumbnail');
					
					echo '<a href="'.site_url().'/wp-admin/post.php?post='.$post_id.'&action=edit">';
					echo '<img src="'.$post_image[0].'" width="60" height="60">';
					echo "</a>";
				else:
					if(function_exists('bdw_get_images_plugin'))
					{
						$post_image = bdw_get_images_plugin($post_id,'tevolution_thumbnail');
						$post_image=(@$post_image[0]['file'])? @$post_image[0]['file'] :plugin_dir_url( __FILE__ ).'images/noimage-150x150.jpg';
					}						
					echo '<a href="'.site_url().'/wp-admin/post.php?post='.$post_id.'&action=edit">';
					echo '<img src="'.$post_image.'" width="60" height="60">';
					echo "</a>";
				endif;
		 break;
	
	case 'tran_status' :
		$package_id=get_post_meta($post_id,'package_select',true);
			if($package_id!=""){
				if($post->post_parent)
					$post_id = $post->post_parent;
				else
					$post_id = $post->ID;
				$transaction_table = $wpdb->prefix.'transactions';
				$sql = "select trans_id from $transaction_table t where 1=1 and post_id='".$post_id."' AND package_type is NULL order by t.trans_id DESC";
				$trans_id = $wpdb->get_results($sql);
				if(!empty($trans_id) && $trans_id[0]->trans_id)
					echo tmpl_get_transaction_status($trans_id[0]->trans_id,$post_id);
				else
					echo tmpl_get_transaction_status(0,$post_id);
			}else{
				echo "-";	
			}	
			
		break;
	case 'categories_' :
			/* Get the post_category for the post. */
			
			$templ_events = get_the_terms($post_id,$taxonomy);
			if (is_array($templ_events)) {
				foreach($templ_events as $key => $templ_event) {
					$edit_link = site_url()."/wp-admin/edit.php?".$taxonomy."=".$templ_event->slug."&post_type=".$post_type;
					$templ_events[$key] = '<a href="'.$edit_link.'">' . $templ_event->name . '</a>';
					}
				echo implode(' , ',$templ_events);
			}else {
				_e( 'Uncategorized',DOMAIN );
			}
			break;
			
		case 'tags_' :
			/* Get the post_tags for the post. */
			
			$templ_event_tags = get_the_terms($post_id,$tags);
			if (is_array($templ_event_tags)) {
				foreach($templ_event_tags as $key => $templ_event_tag) {
					$edit_link = site_url()."/wp-admin/edit.php?".$tags."=".$templ_event_tag->slug."&post_type=".$post_type;
					$templ_event_tags[$key] = '<a href="'.$edit_link.'">' . $templ_event_tag->name . '</a>';
				}
				echo implode(' , ',$templ_event_tags);
			}else {
				_e( 'No Tags',DOMAIN );
			}
				
			break;
		
		case 'posted_on' :
			/* Get the post_tags for the post. */
			if ($post->post_date) {
				$date_format = get_option('date_format');
				$time_format = get_option('time_format');
				$date = strtotime( get_the_time('Y-m-d H:i', $post_id));					
				echo date_i18n($date_format." , ".$time_format,$date);
			}else {
				echo date_i18n(get_option("date_format").' '.get_option("time_format"), strtotime(date("F j, Y, g:i a")));
			}
				
			break;
		case 'price_package' :
		
			if($post->post_parent)
				$post_id = $post->post_parent;
			else
				$post_id = $post->ID;
				
			$trans_post = $post;
			$package_id=get_post_meta($post_id,'package_select',true);
			if($package_id!=""){
				$package_name=get_the_title($package_id);
				if(function_exists('fetch_currency_with_position'))
				{
					$paid_amount=fetch_currency_with_position(get_post_meta($post_id,'paid_amount',true));
				}
				$transection_db_table_name = $wpdb->prefix.'transactions'; 
				$trans_date = $wpdb->get_var("select payment_date from $transection_db_table_name t where post_id = '".$post_id."' AND package_type is NULL order by t.trans_id DESC"); // change it to calculate expired day as per transactions
				if(!isset($trans_date))
					$trans_date =  get_the_date('Y-m-d', $post_id);
				$transaction_price_pkg = $monetization->templ_get_price_info($package_id,'');
				
				$publish_date =  date_i18n('Y-m-d',strtotime($trans_date));
				$alive_days = $transaction_price_pkg[0]['alive_days'];
				$expired_date = date_i18n(get_option("date_format"),strtotime($publish_date. "+$alive_days day"));
				
				
				$featured_text = '-';
				//Check for featured posts: start
				$featured_type = get_post_meta($post_id,'featured_type',true);
				if( 'h' == $featured_type ){
					$featured_text = __("Home",ADMINDOMAIN);
				}elseif( 'c' == $featured_type ){
					$featured_text = __("Category",ADMINDOMAIN);
				}elseif( 'both' == $featured_type ){
					$featured_text = __("Home, Category",ADMINDOMAIN);
				}
				//
				echo '<p>'.__('Package Name: ',ADMINDOMAIN). '<a href="'.site_url().'/wp-admin/admin.php?page=monetization&action=edit&package_id='.$package_id.'&tab=packages" >'.$package_name .'</a></p>';
				
				if($paid_amount >= 0){
					echo '<p>'.__('Total Price: ',ADMINDOMAIN).$paid_amount .'</p>';
				}
				if($featured_text !='-'){
					echo '<p>'.__('Featured: ',ADMINDOMAIN).$featured_text .'</p>';
				}
				if($expired_date){
					echo '<p>'.__('Exp Date: ',ADMINDOMAIN).$expired_date .'</p>';
					}
			}else{
				echo '-';	
			}
			$post = $trans_post;
		break;
		case $column :
			if (get_post_meta($post_id,$column ,true)) {
				$value =  apply_filters('tevolution_posts_custom_column',get_post_meta($post_id,$column ,true),$column);
				if(is_array($value)){
					echo implode(',',$value);
				}else{
					echo $value;
				}
			}else{
				echo "";
			}
		
		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}		
	
}
/* EOF - FETCH DATA IN BACK END */




/* 
NAME : ADD THE CATEGORY PRICE
ARGUMENTS : TAXONOMY NAME
DESCRIPTION : THIS FUNCTIONS IS USED TO ADD THE PRICE FIELD IN CATEGORY
*/
function category_custom_fields_AddField($tax)
{
	add_category_price_field($tax,'add');
}
/* EOF - ADD CATEGORY PRICE */
/* NAME : FUNCTION TO ADD/EDIT CATEGORY PRICE FIELD
ARGUMENTS : TAXONOMY NAME, OPERATION
DESCRIPTION : THIS FUNCTION ADDS/EDITS THE CATEGORY PRICE FIELD IN BACK END */
function add_category_price_field($tax,$screen)
{
	if((isset($tax->taxonomy) && $tax->taxonomy != '') || (isset($tax->term_price) && $tax->term_price != ''))
	{
		$taxonomy = $tax->taxonomy;
		$term_price = $tax->term_price;
	}
		$currency_symbol = get_option('currency_symbol');			
		?>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="cat_price"><?php echo __("Category Price", DOMAIN); echo ' ('.$currency_symbol.')'?></label></th>
				<td><input type="text"  name="cat_price" id="cat_price" value="<?php if(isset($term_price) && $term_price != '') { echo $term_price; } ?>"  size="20"/>
				<p class="description"><?php echo sprintf(__('To change currency <a href="%sadmin.php?page=monetization&tab=payment_options" target= "_blank" >click here</a>',DOMAIN),admin_url());?>.</p>
				</td>
			</tr>
		<?php
	}
/* EOF - ADD/EDIT CATEGORY PRICE FIELD */
/* NAME : EDIT THE CATEGORY PRICE
ARGUMENTS : TAXONOMY NAME
DESCRIPTION : THIS FUNCTIONS IS USED TO EDIT THE PRICE FIELD IN CATEGORY */
function category_custom_fields_Edit($tax)
{
	add_category_price_field($tax,'edit');	
}
/* EOF - EDIT CATEGORY PRICE */
/* NAME : EDIT THE CATEGORY PRICE
ARGUMENTS : TERM ID
DESCRIPTION : THIS FUNCTIONS IS USED TO EDIT THE PRICE FIELD IN CATEGORY */
function category_custom_fields_AlterField($termId)
{
	global $wpdb;
	$term_table = $wpdb->prefix."terms";	
	$cat_price = @$_POST['cat_price'];
	if(@$cat_price == '')
	{
		$cat_price = 0;
	}
	if(@$cat_price != '' || @$cat_price == 0)
	{
		$sql = "update $term_table set term_price=".$cat_price." where term_id=".$termId;
		$wpdb->query($sql);
	}
}
/* EOF - EDIT CATEGORY PRICE */
/* NAME : ADD PRICE COLUMN IN TERMS TABLE
ARGUMENTS : COLUMN NAME
DESCRIPTION : THIS FUNCTION ADDS A COLUMN IN CATEGORY TABLE */
function edit_price_cat_column($columns)
{
	$args = get_option('templatic_custom_post');
	foreach($args as $key => $val)
	{
		$taxonomy = $val['label'];
		$posts = $val['labels']['name'];
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'name' => __('Name',DOMAIN),
			'price' =>  __('Price',DOMAIN),
			'description' => __('Description',DOMAIN),
			'slug' => __('Slug',DOMAIN),
			'posts' => __('Posts',DOMAIN)
			);
	}
	return $columns;
}
/* Quick edit code start */
add_action('quick_edit_custom_box', 'category_price_show', 10, 2);
function category_price_show( $col, $type) {
    if( $type == 'event' ) return;
    
    switch ( $col ) {
         case 'price':?>
<fieldset>
    <div class="inline-edit-col">
         <label for="category_price">
         <span class="title"><?php _e('Price',DOAMIN); ?></span>
         <span class="input-text-wrap">
	         <input class="category_price" type="text" name="cat_price" value="" size="10" />
         </span>
         </label>
    </div>
</fieldset>
<?php 
	break;
    }
}
/* Quick edit code end */
/* EOF - ADD COLUMN */
	
/* NAME : DISPLAY PRICE COLUMN IN TERMS TABLE
ARGUMENTS : COLUMN NAME, OUTPUT, CATEGORU ID
DESCRIPTION : THIS FUNCTION DISPLAYS PRICE IN CATEGORY TABLE */
function tmpl_manage_price_cat_col($out, $column_name, $cat_id)
{
	global $wpdb;
	$term_table=$wpdb->prefix."terms";		
	$sql="select * from $term_table where term_id=".$cat_id;
	$term=$wpdb->get_results($sql);	
	switch ($column_name)
	{
		case 'price':	
			$currency_symbol = get_option('currency_symbol');			
			$symbol_position = get_option('currency_pos');
			$amount = isset($term[0]->term_price) ? $term[0]->term_price : 0;
			$price = fetch_currency_with_position($amount);
			$out = $price;
		break;
	}
		
	return $out;	
}
/* EOF - DISPLAY PRICE */



/*
 * Add Filter for create the general setting sub tab for email setting
 */

function email_setting($tabs ) {
	
	$tabs['email']='Email Settings';					
	return $tabs;
}	
/*
 * Create email setting data action
 */	
function taxonomy_email_setting_data($column)
{	
	$tmpdata = get_option('templatic_settings');		
	switch($column)
	{
		case 'email':						
			?>
							<tr class="post-submission">
								<td><label for="package_type" class="form-textfield-label"><?php echo __('Admin email notification after post submission',ADMINDOMAIN); ?></label></td>
								<td>
									<a href="javascript:void(0);" onclick="open_quick_edit('post-submission','edit-post-submission')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a>
									| 
									<a href="javascript:void(0);" onclick="reset_to_default('post_submited_success_email_subject','post_submited_success_email_content','post-submission');"><?php echo __("Reset",ADMINDOMAIN);?></a>
									<span class="spinner" style="margin:0 18px 0;"></span>
									<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
								</td>
							</tr>
							<tr class="edit-post-submission" style="display:none">
								<td width="100%" colspan="3">
									<h4 class="edit-sub-title">Quick Edit</h4>
									<table width="98%" class="tab-sub-table" align="left">
										<tr>
											<td style="line-height:10px">
												<label class="form-textfield-label sub-title"><?php echo __('Subject',ADMINDOMAIN); ?></label>
											</td>
											<td width="90%" style="line-height:10px">
												<input type="text" name="post_submited_success_email_subject" id="post_submited_success_email_subject" value="<?php if(isset($tmpdata['post_submited_success_email_subject'])){echo stripslashes($tmpdata['post_submited_success_email_subject']);}else{ _e('A new post has been submitted on your site',ADMINDOMAIN); } ?>"/>
											</td>
										</tr>
										<tr>
											<td style="line-height:10px">
												<label class="form-textfield-label sub-title"><?php echo __('Message',DOMAIN); ?></label>
											</td>
											<td width="90%" style="line-height:10px">
												<?php
												$settings =   array(
																'wpautop' => false, // use wpautop?
																'media_buttons' => false, // show insert/upload button(s)
																'textarea_name' => 'post_submited_success_email_content', // set the textarea name to something different, square brackets [] can be used here
																'textarea_rows' => '7', // rows="..."
																'tabindex' => '',
																'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
																'editor_class' => '', // add extra class(es) to the editor textarea
																'teeny' => true, // output the minimal editor config used in Press This
																'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
																'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
																'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
															);	
												if($tmpdata['post_submited_success_email_content'] != ""){
													$content = stripslashes($tmpdata['post_submited_success_email_content']);
												}else{
													$content = __('<p>Howdy [#to_name#],</p><p>A new post has been submitted on your site. Here are some details about it</p><p>[#information_details#]</p><p>Thank You,<br/>[#site_name#]</p>',DOMAIN);
												}
												wp_editor( $content, 'post_submited_success_email_content', $settings);
											?>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<div class="buttons">
													<div class="inline_update">
														<a class="button-primary save  quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",DOMAIN);?></a>
														<a class="button-secondary cancel" href="javascript:void(0);" onclick="open_quick_edit('edit-post-submission','post-submission')" accesskey="c">Cancel</a>
														<span class="save_error" style="display:none"></span><span class="spinner"></span>
													</div>
												</div>	
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr class="user-post-submission alternate">
								<td><label for="package_type" class="form-textfield-label"><?php echo __('User email notification after post submission',ADMINDOMAIN); ?></label></td>
								<td>
									<a href="javascript:void(0);" onclick="open_quick_edit('user-post-submission','user-edit-post-submission')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a>
									| 
									<a href="javascript:void(0);" onclick="reset_to_default('user_post_submited_success_email_subject','user_post_submited_success_email_content','user-post-submission');"><?php echo __("Reset",ADMINDOMAIN);?></a>
									<span class="spinner" style="margin:0 18px 0;"></span>
									<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
								</td>
							</tr>
							<tr class="user-edit-post-submission alternate" style="display:none">
								<td width="100%" colspan="3">
									<h4 class="edit-sub-title">Quick Edit</h4>
									<table width="98%" class="tab-sub-table" align="left">
										<tr>
											<td style="line-height:10px">
												<label class="form-textfield-label sub-title"><?php echo __('Subject',ADMINDOMAIN); ?></label>
											</td>
											<td width="90%" style="line-height:10px">
												<input type="text" name="user_post_submited_success_email_subject" id="user_post_submited_success_email_subject" value="<?php if(isset($tmpdata['user_post_submited_success_email_subject'])){echo stripslashes($tmpdata['user_post_submited_success_email_subject']);}else{ _e('A new post has been submitted',ADMINDOMAIN); } ?>"/>
											</td>
										</tr>
										<tr>
											<td style="line-height:10px">
												<label class="form-textfield-label sub-title"><?php echo __('Message',DOMAIN); ?></label>
											</td>
											<td width="90%" style="line-height:10px">
												<?php
												$settings =   array(
																'wpautop' => false, // use wpautop?
																'media_buttons' => false, // show insert/upload button(s)
																'textarea_name' => 'user_post_submited_success_email_content', // set the textarea name to something different, square brackets [] can be used here
																'textarea_rows' => '7', // rows="..."
																'tabindex' => '',
																'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
																'editor_class' => '', // add extra class(es) to the editor textarea
																'teeny' => true, // output the minimal editor config used in Press This
																'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
																'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
																'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
															);	
												if($tmpdata['user_post_submited_success_email_content'] != ""){
													$content = stripslashes($tmpdata['user_post_submited_success_email_content']);
												}else{
													$content = __('<p>Howdy [#to_name#],</p><p>A new post has been submitted . Here are some details about it</p><p>[#information_details#]</p><p>Thank You,<br/>[#site_name#]</p>',DOMAIN);
												}
												wp_editor( $content, 'user_post_submited_success_email_content', $settings);
											?>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<div class="buttons">
													<div class="inline_update">
														<a class="button-primary save  quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",DOMAIN);?></a>
														<a class="button-secondary cancel" href="javascript:void(0);" onclick="open_quick_edit('user-edit-post-submission','user-post-submission')" accesskey="c">Cancel</a>
														<span class="save_error" style="display:none"></span><span class="spinner"></span>
													</div>
												</div>	
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr class="payment-success-client">
								<td><label class="form-textfield-label"><?php echo __('User email notification after successful payment',DOMAIN); ?></label></td>
								
								<td>
									<a href="javascript:void(0);" onclick="open_quick_edit('payment-success-client','edit-payment-success-client')"><?php echo __("Quick Edit",DOMAIN);?></a> 
									| 
									<a href="javascript:void(0);" onclick="reset_to_default('payment_success_email_subject_to_client','payment_success_email_content_to_client','payment-success-client');"><?php echo __("Reset",DOMAIN);?></a>
									<span class="spinner" style="margin:0 18px 0;"></span>
									<span class="qucik_reset"><?php echo __("Data reset",DOMAIN);?></span>
								</td>
							</tr>
							<tr class="edit-payment-success-client alternate" style="display:none">
								<td width="100%" colspan="2">
									<h4 class="edit-sub-title">Quick Edit</h4>
									<table width="98%" align="left" class="tab-sub-table">
										<tr>
											<td style="line-height:10px">
												<label class="form-textfield-label sub-title"><?php echo __('Subject',DOMAIN); ?></label>
											</td>
											<td width="90%" style="line-height:10px">
												<input type="text" name="payment_success_email_subject_to_client" id="payment_success_email_subject_to_client" value="<?php if(isset($tmpdata['payment_success_email_subject_to_client'])){echo $tmpdata['payment_success_email_subject_to_client'];}else{echo __('Thank you for your submission!',DOMAIN);} ?>"/>
											</td>
										</tr>
										<tr>
											<td style="line-height:10px">
												<label class="form-textfield-label sub-title"><?php echo __('Message',DOMAIN); ?></label>
											</td>
											<td width="90%" style="line-height:10px">
												<?php
												$settings =   array(
																'wpautop' => false, // use wpautop?
																'media_buttons' => false, // show insert/upload button(s)
																'textarea_name' => 'payment_success_email_content_to_client', // set the textarea name to something different, square brackets [] can be used here
																'textarea_rows' => '7', // rows="..."
																'tabindex' => '',
																'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
																'editor_class' => '', // add extra class(es) to the editor textarea
																'teeny' => true, // output the minimal editor config used in Press This
																'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
																'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
																'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
															);	
												if($tmpdata['payment_success_email_content_to_client'] != ""){
													$content = stripslashes($tmpdata['payment_success_email_content_to_client']);
												}else{
													$content = __("<p>Hello [#to_name#]</p><p>Here's some info about your payment...</p><p>[#transaction_details#]</p><p>If you'll have any questions about this payment please send an email to [#admin_email#]</p><p>Thanks!,<br/>[#site_name#]</p>",DOMAIN);
												}
												wp_editor( $content, 'payment_success_email_content_to_client', $settings);
											?>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<div class="buttons">
													<div class="inline_update">
													
													<a class="button-primary save alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",DOMAIN);?></a>
													<a class="button-secondary cancel alignright" href="javascript:void(0);" onclick="open_quick_edit('edit-payment-success-client','payment-success-client')" accesskey="c">Cancel</a>
													<span class="save_error" style="display:none"></span><span class="spinner"></span>
													</div>
												</div>	
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr class="payment-success-admin alternate">
								<td><label class="form-textfield-label"><?php echo __('Admin email notification after successful payment',DOMAIN); ?></label></td>
							
								<td>
									<a href="javascript:void(0);" onclick="open_quick_edit('payment-success-admin','edit-payment-success-admin')"><?php echo __("Quick Edit",DOMAIN);?></a>
									| 
									<a href="javascript:void(0);" onclick="reset_to_default('payment_success_email_subject_to_admin','payment_success_email_content_to_admin','payment-success-admin');"><?php echo __("Reset",DOMAIN);?></a>
									<span class="spinner" style="margin:0 18px 0;"></span>
									<span class="qucik_reset"><?php echo __("Data reset",DOMAIN);?></span>
								</td>
							</tr>
							<tr class="edit-payment-success-admin alternate" style="display:none">
								<td width="100%" colspan="2">
									<h4 class="edit-sub-title">Quick Edit</h4>
									<table width="98%" align="left" class="tab-sub-table">
										<tr>
											<td style="line-height:10px">
												<label class="form-textfield-label sub-title"><?php echo __('Subject',ADMINDOMAIN); ?></label>
											</td>
											<td width="90%" style="line-height:10px">
												<input type="text" name="payment_success_email_subject_to_admin" id="payment_success_email_subject_to_admin" value="<?php if(isset($tmpdata['payment_success_email_subject_to_admin'])){echo $tmpdata['payment_success_email_subject_to_admin'];}else{ echo __('You have received a payment',DOMAIN);} ?>"/>
											</td>
										</tr>
										<tr>
											<td style="line-height:10px">
												<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
											</td>
											<td width="90%" style="line-height:10px">
												<?php
												$settings =   array(
																'wpautop' => false, // use wpautop?
																'media_buttons' => false, // show insert/upload button(s)
																'textarea_name' => 'payment_success_email_content_to_admin', // set the textarea name to something different, square brackets [] can be used here
																'textarea_rows' => '7', // rows="..."
																'tabindex' => '',
																'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
																'editor_class' => '', // add extra class(es) to the editor textarea
																'teeny' => true, // output the minimal editor config used in Press This
																'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
																'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
																'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
															);	
												if($tmpdata['payment_success_email_content_to_admin'] != ""){
													$content = stripslashes($tmpdata['payment_success_email_content_to_admin']);
												}else{
													$content = __("<p>Howdy [#to_name#] ,</p><p>You have received a payment of [#payable_amt#] on [#site_name#]. Details are available below</p><p>[#transaction_details#]</p><p>Thanks,<br/>[#site_name#]</p>",DOMAIN);
												}
												wp_editor( $content, 'payment_success_email_content_to_admin', $settings);
											?>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<div class="buttons">
													<div class="inline_update">
														<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
														<a class="button-secondary cancel alignright" href="javascript:void(0);" onclick="open_quick_edit('edit-payment-success-admin','payment-success-admin')" accesskey="c">Cancel</a>
														<span class="save_error" style="display:none"></span><span class="spinner"></span>
														</div>
												</div>	
											</td>
										</tr>
									</table>
								</td>
							</tr>
							
							
							<tr class="pre-payment-success-admin">
								<td><label class="form-textfield-label"><?php echo __('Admin email notification after PreBank transfer',ADMINDOMAIN); ?></label></td>
							
								<td>
									<a href="javascript:void(0);" onclick="open_quick_edit('pre-payment-success-admin','edit-pre-payment-success-admin')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a>
									| 
									<a href="javascript:void(0);" onclick="reset_to_default('pre_payment_success_email_subject_to_admin','pre_payment_success_email_content_to_admin','pre-payment-success-admin');"><?php echo __("Reset",DOMAIN);?></a>
									<span class="spinner" style="margin:0 18px 0;"></span>
									<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
								</td>
							</tr>
							<tr class="edit-pre-payment-success-admin" style="display:none">
								<td width="100%" colspan="2">
									<h4 class="edit-sub-title">Quick Edit</h4>
									<table width="98%" align="left" class="tab-sub-table">
										<tr>
											<td style="line-height:10px">
												<label class="form-textfield-label sub-title"><?php echo __('Subject',ADMINDOMAIN); ?></label>
											</td>
											<td width="90%" style="line-height:10px">
												<input type="text" name="pre_payment_success_email_subject_to_admin" id="pre_payment_success_email_subject_to_admin" value="<?php if(isset($tmpdata['pre_payment_success_email_subject_to_admin'])){echo $tmpdata['pre_payment_success_email_subject_to_admin'];}else{ _e('Pending payment through Pre bank transfer',DOMAIN);} ?>"/>
											</td>
										</tr>
										<tr>
											<td style="line-height:10px">
												<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
											</td>
											<td width="90%" style="line-height:10px">
												<?php
												$settings =   array(
																'wpautop' => false, // use wpautop?
																'media_buttons' => false, // show insert/upload button(s)
																'textarea_name' => 'pre_payment_success_email_content_to_admin', // set the textarea name to something different, square brackets [] can be used here
																'textarea_rows' => '7', // rows="..."
																'tabindex' => '',
																'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
																'editor_class' => '', // add extra class(es) to the editor textarea
																'teeny' => true, // output the minimal editor config used in Press This
																'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
																'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
																'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
															);	
												if($tmpdata['pre_payment_success_email_content_to_admin'] != ""){
													$content = stripslashes($tmpdata['pre_payment_success_email_content_to_admin']);
												}else{
													$content = __("<p>Howdy [#to_name#] ,</p><p>Payment from [#user_login#] is pending for the new listing they submitted on your site as they selected pre bank transfer as their preferred payment method.</p><p>You can view details below [#transaction_details#]</p> <p>You can contact [#user_login#] for status of the payment.</p><p>Thanks!<br/>[#site_name#]</p>",ADMINDOMAIN);
												}
												wp_editor( $content, 'pre_payment_success_email_content_to_admin', $settings);
											?>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<div class="buttons">
													<div class="inline_update">
														<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
														<a class="button-secondary cancel alignright" href="javascript:void(0);" onclick="open_quick_edit('edit-pre-payment-success-admin','pre-payment-success-admin')" accesskey="c"><?php echo __("Cancel",ADMINDOMAIN);?></a>
														<span class="save_error" style="display:none"></span><span class="spinner"></span>
														</div>
												</div>	
											</td>
										</tr>
									</table>
								</td>
							</tr>	
										
										
										
										
										
										<tr class="contact-us alternate">
											<td><label class="form-textfield-label"><?php echo __('Contact Us form notification email',ADMINDOMAIN); ?></label></td>
										
											<td>
												<a href="javascript:void(0);" onclick="open_quick_edit('contact-us','edit-contact-us')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a>
												| 
												<a href="javascript:void(0);" onclick="reset_to_default('contact_us_email_content','contact_us_email_content','contact-us');"><?php echo __("Reset",DOMAIN);?></a>
												<span class="spinner" style="margin:0 18px 0;"></span>
												<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
											</td>
										</tr>
										<tr class="edit-contact-us" style="display:none">
											<td width="100%" colspan="2">
												<h4 class="edit-sub-title">Quick Edit</h4>
												<table width="98%" align="left" class="tab-sub-table">
													
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<?php
															$settings =   array(
																			'wpautop' => false, // use wpautop?
																			'media_buttons' => false, // show insert/upload button(s)
																			'textarea_name' => 'contact_us_email_content', // set the textarea name to something different, square brackets [] can be used here
																			'textarea_rows' => '7', // rows="..."
																			'tabindex' => '',
																			'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
																			'editor_class' => '', // add extra class(es) to the editor textarea
																			'teeny' => true, // output the minimal editor config used in Press This
																			'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
																			'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
																			'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
																		);	
															if($tmpdata['contact_us_email_content'] != ""){
																$content = stripslashes($tmpdata['contact_us_email_content']);
															}else{
																$content = __("<p>Dear [#to_name#] ,</p><p>You have an inquiry message. Here are the details</p><p> Name : [#user_name#] </p> <p> Email : [#user_email#] </p> <p> Message : [#user_message#] </p>",ADMINDOMAIN);
															}
															wp_editor( $content, 'contact_us_email_content', $settings);
														?>
														</td>
													</tr>
													<tr>
														<td colspan="2">
															<div class="buttons">
																<div class="inline_update">
																	<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
																	<a class="button-secondary cancel alignright" href="javascript:void(0);" onclick="open_quick_edit('edit-contact-us','contact-us')" accesskey="c"><?php echo __("Cancel",ADMINDOMAIN);?></a>
																	<span class="save_error" style="display:none"></span><span class="spinner"></span>
																	</div>
															</div>	
														</td>
													</tr>						
												</table>
											</td>
										</tr>
										
										<tr class="admin-post-upgrade">
											<td><label class="form-textfield-label"><?php echo __('Admin post upgrade notification email',ADMINDOMAIN); ?></label></td>
										
											<td>
												<a href="javascript:void(0);" onclick="open_quick_edit('admin-post-upgrade','edit-admin-post-upgrade')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a>
												| 
												<a href="javascript:void(0);" onclick="reset_to_default('admin_post_upgrade_email_subject','admin_post_upgrade_email_content','admin-post-upgrade');"><?php echo __("Reset",DOMAIN);?></a>
												<span class="spinner" style="margin:0 18px 0;"></span>
												<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
											</td>
										</tr>
										<tr class="edit-admin-post-upgrade" style="display:none">
											<td width="100%" colspan="2">
												<h4 class="edit-sub-title">Quick Edit</h4>
												<table width="98%" align="left" class="tab-sub-table">
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Subject',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<input type="text" name="admin_post_upgrade_email_subject" id="admin_post_upgrade_email_subject" value="<?php if(isset($tmpdata['admin_post_upgrade_email_subject'])){echo $tmpdata['admin_post_upgrade_email_subject'];}else{ _e('A New Upgrade Request of ID:#[#post_id#]',DOMAIN);} ?>"/>
														</td>
													</tr>
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<?php
															$settings =   array(
																			'wpautop' => false, // use wpautop?
																			'media_buttons' => false, // show insert/upload button(s)
																			'textarea_name' => 'admin_post_upgrade_email_content', // set the textarea name to something different, square brackets [] can be used here
																			'textarea_rows' => '7', // rows="..."
																			'tabindex' => '',
																			'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
																			'editor_class' => '', // add extra class(es) to the editor textarea
																			'teeny' => true, // output the minimal editor config used in Press This
																			'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
																			'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
																			'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
																		);	
															if($tmpdata['admin_post_upgrade_email_content'] != ""){
																$content = stripslashes($tmpdata['admin_post_upgrade_email_content']);
															}else{
																$content = __("<p>Howdy [#to_name#],</p><p>A New Upgrade request has been submited to your site.</p><br/>Here are some details about it.<br/><p>[#information_details#]</p><p>Thank You,<br/>[#site_name#]</p>",ADMINDOMAIN);
															}
															wp_editor( $content, 'admin_post_upgrade_email_content', $settings);
														?>
														</td>
													</tr>
													<tr>
														<td colspan="2">
															<div class="buttons">
																<div class="inline_update">
																	<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
																	<a class="button-secondary cancel alignright" href="javascript:void(0);" onclick="open_quick_edit('edit-admin-post-upgrade','admin-post-upgrade')" accesskey="c"><?php echo __("Cancel",ADMINDOMAIN);?></a>
																	<span class="save_error" style="display:none"></span><span class="spinner"></span>
																	</div>
															</div>	
														</td>
													</tr>						
												</table>
											</td>
										</tr>
										
										<tr class="client-post-upgrade alternate">
											<td><label class="form-textfield-label"><?php echo __('Client post upgrade notification email',ADMINDOMAIN); ?></label></td>
										
											<td>
												<a href="javascript:void(0);" onclick="open_quick_edit('client-post-upgrade','edit-client-post-upgrade')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a>
												| 
												<a href="javascript:void(0);" onclick="reset_to_default('client_post_upgrade_email_subject','client_post_upgrade_email_content','client-post-upgrade');"><?php echo __("Reset",DOMAIN);?></a>
												<span class="spinner" style="margin:0 18px 0;"></span>
												<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
											</td>
										</tr>
										<tr class="edit-client-post-upgrade" style="display:none">
											<td width="100%" colspan="2">
												<h4 class="edit-sub-title">Quick Edit</h4>
												<table width="98%" align="left" class="tab-sub-table">
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Subject',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<input type="text" name="client_post_upgrade_email_subject" id="client_post_upgrade_email_subject" value="<?php if(isset($tmpdata['client_post_upgrade_email_subject'])){echo $tmpdata['client_post_upgrade_email_subject'];}else{ _e('Payment Pending For Upgrade Request: #[#post_id#]',DOMAIN);} ?>"/>
														</td>
													</tr>
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<?php
															$settings =   array(
																			'wpautop' => false, // use wpautop?
																			'media_buttons' => false, // show insert/upload button(s)
																			'textarea_name' => 'client_post_upgrade_email_content', // set the textarea name to something different, square brackets [] can be used here
																			'textarea_rows' => '7', // rows="..."
																			'tabindex' => '',
																			'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
																			'editor_class' => '', // add extra class(es) to the editor textarea
																			'teeny' => true, // output the minimal editor config used in Press This
																			'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
																			'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
																			'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
																		);	
															if($tmpdata['client_post_upgrade_email_content'] != ""){
																$content = stripslashes($tmpdata['client_post_upgrade_email_content']);
															}else{
																$content = __("<p>Dear [#to_name#],</p><p>Your [#post_type_name#] has been updated by you . Here is the information about the [#post_type_name#]:</p>[#information_details#]<br><p>[#site_name#]</p>",ADMINDOMAIN);
															}
															wp_editor( $content, 'client_post_upgrade_email_content', $settings);
														?>
														</td>
													</tr>
													<tr>
														<td colspan="2">
															<div class="buttons">
																<div class="inline_update">
																	<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
																	<a class="button-secondary cancel alignright" href="javascript:void(0);" onclick="open_quick_edit('edit-client-post-upgrade','client-post-upgrade')" accesskey="c"><?php echo __("Cancel",ADMINDOMAIN);?></a>
																	<span class="save_error" style="display:none"></span><span class="spinner"></span>
																	</div>
															</div>	
														</td>
													</tr>
												</table>
											</td>
										</tr>
										
										
										
										<tr class="reset-password">
											<td><label class="form-textfield-label"><?php echo __('Reset Password',ADMINDOMAIN); ?></label></td>
										
											<td>
												<a href="javascript:void(0);" onclick="open_quick_edit('reset-password','edit-reset-password')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a>
												| 
												<a href="javascript:void(0);" onclick="reset_to_default('reset_password_subject','reset_password_content','reset-password');"><?php echo __("Reset",DOMAIN);?></a>
												<span class="spinner" style="margin:0 18px 0;"></span>
												<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
											</td>
										</tr>
										<tr class="edit-reset-password" style="display:none">
											<td width="100%" colspan="2">
												<h4 class="edit-sub-title">Quick Edit</h4>
												<table width="98%" align="left" class="tab-sub-table">
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Subject',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<input type="text" name="reset_password_subject" id="reset_password_subject" value="<?php if(isset($tmpdata['reset_password_subject'])){echo $tmpdata['reset_password_subject'];}else{ _e('[#site_title#] Your new password',DOMAIN);} ?>"/>
														</td>
													</tr>
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<?php
															$settings =   array(
																			'wpautop' => false, // use wpautop?
																			'media_buttons' => false, // show insert/upload button(s)
																			'textarea_name' => 'reset_password_content', // set the textarea name to something different, square brackets [] can be used here
																			'textarea_rows' => '7', // rows="..."
																			'tabindex' => '',
																			'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
																			'editor_class' => '', // add extra class(es) to the editor textarea
																			'teeny' => true, // output the minimal editor config used in Press This
																			'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
																			'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
																			'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
																		);	
															if($tmpdata['reset_password_content'] != ""){
																$content = stripslashes($tmpdata['reset_password_content']);
															}else{
																$content = __("<p>Hi [#to_name#],</p><p>You have requested for a new password for your account [#user_email#]. Here is the new password</p><p> Login URL: [#login_url#] </p><p> User name: [#user_login#]</p> <p> Password: [#user_password#]</p><p>You may change this password in your profile once you login with the new password.</p><p>Thanks <br/> [#site_title#] </p>",ADMINDOMAIN);
															}
															wp_editor( $content, 'reset_password_content', $settings);
														?>
														</td>
													</tr>
													<tr>
														<td colspan="2">
															<div class="buttons">
																<div class="inline_update">
																	<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
																	<a class="button-secondary cancel alignright" href="javascript:void(0);" onclick="open_quick_edit('edit-reset-password','reset-password')" accesskey="c"><?php echo __("Cancel",ADMINDOMAIN);?></a>
																	<span class="save_error" style="display:none"></span><span class="spinner"></span>
																	</div>
															</div>	
														</td>
													</tr>
												</table>
											</td>
										</tr>
										
										
										
										<tr class="claim-ownership alternate">
											<td><label class="form-textfield-label"><?php echo __('Claim Ownership',ADMINDOMAIN); ?></label></td>
										
											<td>
												<a href="javascript:void(0);" onclick="open_quick_edit('claim-ownership','edit-claim-ownership')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a>
												| 
												<a href="javascript:void(0);" onclick="reset_to_default('claim_ownership_subject','claim_ownership_content','claim-ownership');"><?php echo __("Reset",DOMAIN);?></a>
												<span class="spinner" style="margin:0 18px 0;"></span>
												<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
											</td>
										</tr>
										<tr class="edit-claim-ownership" style="display:none">
											<td width="100%" colspan="2">
												<h4 class="edit-sub-title">Quick Edit</h4>
												<table width="98%" align="left" class="tab-sub-table">
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Subject',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<input type="text" name="claim_ownership_subject" id="claim_ownership_subject" value="<?php if(isset($tmpdata['claim_ownership_subject'])){echo $tmpdata['claim_ownership_subject'];}else{ _e('Claim to - [#post_title#]',DOMAIN);} ?>"/>
														</td>
													</tr>
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<?php
															$settings =   array(
																			'wpautop' => false, // use wpautop?
																			'media_buttons' => false, // show insert/upload button(s)
																			'textarea_name' => 'claim_ownership_content', // set the textarea name to something different, square brackets [] can be used here
																			'textarea_rows' => '7', // rows="..."
																			'tabindex' => '',
																			'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
																			'editor_class' => '', // add extra class(es) to the editor textarea
																			'teeny' => true, // output the minimal editor config used in Press This
																			'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
																			'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
																			'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
																		);	
															if($tmpdata['claim_ownership_content'] != ""){
																$content = stripslashes($tmpdata['claim_ownership_content']);
															}else{
																$content = __("<p>Dear admin,</p><br/><p> [#claim_name#] has claimed for this post</p><p>[#message#]</p><p>Link :[#post_title#]</p><p>From : [#your_name#]</p><p>Email: [#claim_email#]<p>Phone Number : [#your_number#]</p>",ADMINDOMAIN);
															}
															wp_editor( $content, 'claim_ownership_content', $settings);
														?>
														</td>
													</tr>                                                    
													<tr>
														<td colspan="2">
															<div class="buttons">
																<div class="inline_update">
																	<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
																	<a class="button-secondary cancel alignright" href="javascript:void(0);" onclick="open_quick_edit('edit-claim-ownership','claim-ownership')" accesskey="c"><?php echo __("Cancel",ADMINDOMAIN);?></a>
																	<span class="save_error" style="display:none"></span><span class="spinner"></span>
																	</div>
															</div>	
														</td>
													</tr>
												</table>
											</td>
										</tr>
                                        
                                        <tr class="listing-expiration">
											<td><label class="form-textfield-label"><?php echo __('Listing Expiration',ADMINDOMAIN); ?></label></td>
										
											<td>
												<a href="javascript:void(0);" onclick="open_quick_edit('listing-expiration','edit-listing-expiration')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a>
												| 
												<a href="javascript:void(0);" onclick="reset_to_default('listing_expiration_subject','listing_expiration_content','listing-expiration');"><?php echo __("Reset",DOMAIN);?></a>
												<span class="spinner" style="margin:0 18px 0;"></span>
												<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
											</td>
										</tr>
										<tr class="edit-listing-expiration" style="display:none">
											<td width="100%" colspan="2">
												<h4 class="edit-sub-title">Quick Edit</h4>
												<table width="98%" align="left" class="tab-sub-table">
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Subject',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<input type="text" name="listing_expiration_subject" id="listing_expiration_subject" value="<?php if(isset($tmpdata['listing_expiration_subject'])){echo $tmpdata['listing_expiration_subject'];}else{ _e('Listing expiration Notification',DOMAIN);} ?>"/>
														</td>
													</tr>
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<?php
															$settings =   array(
																			'wpautop' => false, // use wpautop?
																			'media_buttons' => false, // show insert/upload button(s)
																			'textarea_name' => 'listing_expiration_content', // set the textarea name to something different, square brackets [] can be used here
																			'textarea_rows' => '7', // rows="..."
																			'tabindex' => '',
																			'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
																			'editor_class' => '', // add extra class(es) to the editor textarea
																			'teeny' => true, // output the minimal editor config used in Press This
																			'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
																			'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
																			'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
																		);	
															if($tmpdata['listing_expiration_content'] != ""){
																$content = stripslashes($tmpdata['listing_expiration_content']);
															}else{
																
																$content = __("<p>Dear [#user_login#],<p><p>Your listing -<a href=\"[#post_link#]\"><b>[#post_title#]</b></a> posted on  <u>[#post_date#]</u> and paid on <u>[#transection_date#]</u> for [#alivedays#] days.</p><p>Its going to expiry after [#days_left#] day(s). If the listing expire, it will no longer appear on the site.</p><p> If you want to renew, Please login to your member area of our site and renew it as soon as it expire. You may like to login the site from <a href=\"[#site_login_url_link#]\">[#site_login_url_link#]</a>.</p><p>Your login ID is <b>[#user_login#]</b> and Email ID is <b>[#user_email#]</b>.</p><p>Thank you,<br />[#site_name#].</p>",ADMINDOMAIN);																	
															}
															
															wp_editor( $content, 'listing_expiration_content', $settings);
														?>
														</td>
													</tr>                                                    
													<tr>
														<td colspan="2">
															<div class="buttons">
																<div class="inline_update">
																	<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
																	<a class="button-secondary cancel alignright" href="javascript:void(0);" onclick="open_quick_edit('edit-listing-expiration','listing-expiration')" accesskey="c"><?php echo __("Cancel",ADMINDOMAIN);?></a>
																	<span class="save_error" style="display:none"></span><span class="spinner"></span>
																	</div>
															</div>	
														</td>
													</tr>
												</table>
											</td>
										</tr>
										
                                         <tr class="payment-cancelled alternate">
											<td><label class="form-textfield-label"><?php echo __('Payment Cancelled',ADMINDOMAIN); ?></label></td>
										
											<td>
												<a href="javascript:void(0);" onclick="open_quick_edit('payment-cancelled','edit-payment-cancelled')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a>
												| 
												<a href="javascript:void(0);" onclick="reset_to_default('payment_cancelled_subject','payment_cancelled_content','payment-cancelled');"><?php echo __("Reset",DOMAIN);?></a>
												<span class="spinner" style="margin:0 18px 0;"></span>
												<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
											</td>
										</tr>
										<tr class="edit-payment-cancelled " style="display:none">
											<td width="100%" colspan="2">
												<h4 class="edit-sub-title">Quick Edit</h4>
												<table width="98%" align="left" class="tab-sub-table">
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Subject',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<input type="text" name="payment_cancelled_subject" id="payment_cancelled_subject" value="<?php if(isset($tmpdata['payment_cancelled_subject'])){echo $tmpdata['payment_cancelled_subject'];}else{ _e('Payment Cancelled',DOMAIN);} ?>"/>
														</td>
													</tr>
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<?php
															$settings =   array(
																			'wpautop' => false, // use wpautop?
																			'media_buttons' => false, // show insert/upload button(s)
																			'textarea_name' => 'payment_cancelled_content', // set the textarea name to something different, square brackets [] can be used here
																			'textarea_rows' => '7', // rows="..."
																			'tabindex' => '',
																			'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
																			'editor_class' => '', // add extra class(es) to the editor textarea
																			'teeny' => true, // output the minimal editor config used in Press This
																			'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
																			'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
																			'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
																		);	
															if($tmpdata['payment_cancelled_content'] != ""){
																$content = stripslashes($tmpdata['payment_cancelled_content']);
															}else{
																$content = __("[#post_type#] has been cancelled with transaction id [#transection_id#]",ADMINDOMAIN);
															}
															wp_editor( $content, 'payment_cancelled_content', $settings);
														?>
														</td>
													</tr>                                                    
													<tr>
														<td colspan="2">
															<div class="buttons">
																<div class="inline_update">
																	<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
																	<a class="button-secondary cancel alignright" href="javascript:void(0);" onclick="open_quick_edit('edit-payment-cancelled','payment-cancelled')" accesskey="c"><?php echo __("Cancel",ADMINDOMAIN);?></a>
																	<span class="save_error" style="display:none"></span><span class="spinner"></span>
																	</div>
															</div>	
														</td>
													</tr>
												</table>
											</td>
										</tr>
                                        
                                        <tr class="update-listing-notification">
											<td><label class="form-textfield-label"><?php echo __('Update listing notification email',ADMINDOMAIN); ?></label></td>
										
											<td>
												<a href="javascript:void(0);" onclick="open_quick_edit('update-listing-notification','edit-update-listing-notification')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a>
												| 
												<a href="javascript:void(0);" onclick="reset_to_default('update_listing_notification_subject','update_listing_notification_content','update-listing-notification');"><?php echo __("Reset",DOMAIN);?></a>
												<span class="spinner" style="margin:0 18px 0;"></span>
												<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
											</td>
										</tr>
										<tr class="edit-update-listing-notification" style="display:none">
											<td width="100%" colspan="2">
												<h4 class="edit-sub-title">Quick Edit</h4>
												<table width="98%" align="left" class="tab-sub-table">
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Subject',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<input type="text" name="update_listing_notification_subject" id="update_listing_notification_subject" value="<?php if(isset($tmpdata['update_listing_notification_subject'])){echo $tmpdata['update_listing_notification_subject'];}else{ _e('[#post_type#] updated of ID:#[#submition_Id#]',DOMAIN);} ?>"/>
														</td>
													</tr>
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<?php
															$settings =   array(
																			'wpautop' => false, // use wpautop?
																			'media_buttons' => false, // show insert/upload button(s)
																			'textarea_name' => 'update_listing_notification_content', // set the textarea name to something different, square brackets [] can be used here
																			'textarea_rows' => '7', // rows="..."
																			'tabindex' => '',
																			'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
																			'editor_class' => '', // add extra class(es) to the editor textarea
																			'teeny' => true, // output the minimal editor config used in Press This
																			'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
																			'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
																			'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
																		);	
															if($tmpdata['update_listing_notification_content'] != ""){
																$content = stripslashes($tmpdata['update_listing_notification_content']);
															}else{
																$content = __("<p>Dear [#to_name#],</p><p>Your [#post_type#] has been updated by you . Here is the information about the [#post_type#]:</p>[#information_details#]<br><p>[#site_name#]</p>",ADMINDOMAIN);
															}
															wp_editor( $content, 'update_listing_notification_content', $settings);
														?>
														</td>
													</tr>                                                    
													<tr>
														<td colspan="2">
															<div class="buttons">
																<div class="inline_update">
																	<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
																	<a class="button-secondary cancel alignright" href="javascript:void(0);" onclick="open_quick_edit('edit-update-listing-notification','update-listing-notification')" accesskey="c"><?php echo __("Cancel",ADMINDOMAIN);?></a>
																	<span class="save_error" style="display:none"></span><span class="spinner"></span>
																	</div>
															</div>	
														</td>
													</tr>
												</table>
											</td>
										</tr>
                                        
                                        <tr class="renew-listing-notification alternate">
											<td><label class="form-textfield-label"><?php echo __('Renew listing notification email',ADMINDOMAIN); ?></label></td>
										
											<td>
												<a href="javascript:void(0);" onclick="open_quick_edit('renew-listing-notification','edit-renew-listing-notification')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a>
												| 
												<a href="javascript:void(0);" onclick="reset_to_default('renew_listing_notification_subject','renew_listing_notification_content','renew-listing-notification');"><?php echo __("Reset",DOMAIN);?></a>
												<span class="spinner" style="margin:0 18px 0;"></span>
												<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
											</td>
										</tr>
                                        <tr class="edit-renew-listing-notification" style="display:none">
											<td width="100%" colspan="2">
												<h4 class="edit-sub-title">Quick Edit</h4>
												<table width="98%" align="left" class="tab-sub-table">
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Subject',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<input type="text" name="renew_listing_notification_subject" id="renew_listing_notification_subject" value="<?php if(isset($tmpdata['renew_listing_notification_subject'])){echo $tmpdata['renew_listing_notification_subject'];}else{ _e('[#post_type#] renew of ID:#[#submition_Id#]',DOMAIN);} ?>"/>
														</td>
													</tr>
													<tr>
														<td style="line-height:10px">
															<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
														</td>
														<td width="90%" style="line-height:10px">
															<?php
															$settings =   array(
																			'wpautop' => false, // use wpautop?
																			'media_buttons' => false, // show insert/upload button(s)
																			'textarea_name' => 'renew_listing_notification_content', // set the textarea name to something different, square brackets [] can be used here
																			'textarea_rows' => '7', // rows="..."
																			'tabindex' => '',
																			'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
																			'editor_class' => '', // add extra class(es) to the editor textarea
																			'teeny' => true, // output the minimal editor config used in Press This
																			'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
																			'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
																			'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
																		);	
															if($tmpdata['renew_listing_notification_content'] != ""){
																$content = stripslashes($tmpdata['renew_listing_notification_content']);
															}else{
																$content = __("<p>Dear [#to_name#],</p><p>Your [#post_type#] has been renew by you . Here is the information about the [#post_type#]:</p>[#information_details#]<br><p>[#site_name#]</p>",ADMINDOMAIN);
															}
															wp_editor( $content, 'renew_listing_notification_content', $settings);
														?>
														</td>
													</tr>                                                    
													<tr>
														<td colspan="2">
															<div class="buttons">
																<div class="inline_update">
																	<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
																	<a class="button-secondary cancel alignright" href="javascript:void(0);" onclick="open_quick_edit('edit-renew-listing-notification','renew-listing-notification')" accesskey="c"><?php echo __("Cancel",ADMINDOMAIN);?></a>
																	<span class="save_error" style="display:none"></span><span class="spinner"></span>
																	</div>
															</div>	
														</td>
													</tr>
												</table>
											</td>
										</tr>
                                        <?php do_action('tevolution_after_email_type_setting');?>
										
						</tbody>
					</table>
			    </td>
				</tr>
	
		    
					<div class="tevo_sub_title" style="padding-top: 10px;"><?php echo __('Notification Content Settings',ADMINDOMAIN); ?></div>
					<p class="tevolution_desc"><?php echo __('These are the messages that appear on your site after certain actions (like content submissions).',ADMINDOMAIN)?></p>
					<table  class="widefat post email-wide-table">
					<thead>
						<tr>
							<th class="first-th">
								<label for="notification_title" class="form-textfield-label"><?php echo __('Notification Title',ADMINDOMAIN); ?></label>
							</th>
							
							<th class="last-th">
								<label for="msg_desc" class="form-textfield-label"><?php echo __('Actions',ADMINDOMAIN); ?></label>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr class="post-submission-not alternate">
							<td><label class="form-textfield-label"><?php echo __('Successful post submission message',ADMINDOMAIN); ?></label></td>
						
							<td><a href="javascript:void(0);" onclick="open_quick_edit('post-submission-not','edit-post-submission-not')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a> 
							| 
									<a href="javascript:void(0);" onclick="reset_to_default('','post_added_success_msg_content','post-submission-not');"><?php echo __("Reset",ADMINDOMAIN);?></a>
									<span class="spinner" style="margin:0 18px 0;"></span>
									<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
							</td>
						</tr>
						<tr class="edit-post-submission-not" style="display:none">
							<td width="100%" colspan="2">
								<h4 class="edit-sub-title"><?php echo __("Quick Edit",ADMINDOMAIN);?></h4>
								<table width="98%" align="left" class="tab-sub-table">
									<tr>
										<td style="line-height:10px">
											<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
										</td>
										<td width="90%" style="line-height:10px">
											<?php
											$settings =   array(
															'wpautop' => false, // use wpautop?
															'media_buttons' => false, // show insert/upload button(s)
															'textarea_name' => 'post_added_success_msg_content', // set the textarea name to something different, square brackets [] can be used here
															'textarea_rows' => '7', // rows="..."
															'tabindex' => '',
															'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
															'editor_class' => '', // add extra class(es) to the editor textarea
															'teeny' => true, // output the minimal editor config used in Press This
															'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
															'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
															'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
														);	
											if($tmpdata['post_added_success_msg_content'] != ""){
												$content = stripslashes($tmpdata['post_added_success_msg_content']);
											}else{
												$content = '<p>'.__("Thank you! We have successfully received the submitted information.",ADMINDOMAIN).'</p><p>[#submited_information_link#]</p><p>'.__("Thanks!",ADMINDOMAIN).'<br/> [#site_name#].</p>';
											}
											wp_editor( $content, 'post_added_success_msg_content', $settings);
										?>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<div class="buttons">
												<div class="inline_update">
													<a class="button-primary save alignleft  quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
													<a class="button-secondary alignright cancel " href="javascript:void(0);" onclick="open_quick_edit('edit-post-submission-not','post-submission-not')" accesskey="c"><?php echo __('Cancel',ADMINDOMAIN);?></a>
													<span class="save_error" style="display:none"></span><span class="spinner"></span>
												</div>
											</div>	
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr class="payment-successful">
							<td><label class="form-textfield-label"><?php echo __('Payment successfully received message',ADMINDOMAIN); ?></label></td>
							
							<td><a href="javascript:void(0);" onclick="open_quick_edit('payment-successful','edit-payment-successful')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a> 
							| 
									<a href="javascript:void(0);" onclick="reset_to_default('','post_payment_success_msg_content','payment-successful');"><?php echo __("Reset",ADMINDOMAIN);?></a>
									<span class="spinner" style="margin:0 18px 0;"></span>
									<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
							</td>
						</tr>
						<tr class="edit-payment-successful" style="display:none">
							<td width="100%" colspan="2">
								<h4 class="edit-sub-title"><?php echo __("Quick Edit",ADMINDOMAIN);?></h4>
								<table width="98%" align="left" class="tab-sub-table">
									<tr>
										<td style="line-height:10px">
											<label class="form-textfield-label sub-title"><?php echo __("Message",ADMINDOMAIN);?></label>
										</td>
										<td width="90%" style="line-height:10px">
											<?php
											$settings =   array(
															'wpautop' => false, // use wpautop?
															'media_buttons' => false, // show insert/upload button(s)
															'textarea_name' => 'post_payment_success_msg_content', // set the textarea name to something different, square brackets [] can be used here
															'textarea_rows' => '7', // rows="..."
															'tabindex' => '',
															'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
															'editor_class' => '', // add extra class(es) to the editor textarea
															'teeny' => true, // output the minimal editor config used in Press This
															'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
															'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
															'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
														);	
											if($tmpdata['post_payment_success_msg_content'] != ""){
												$content = stripslashes($tmpdata['post_payment_success_msg_content']);
											}else{
												$content = '<h4>'.__("Your payment has been successfully received. The submitted content is now published.",ADMINDOMAIN).'</h4><p><a href="[#submited_information_link#]" >'.__("View your submitted information",ADMINDOMAIN).'</a></p><h5>'.__("Thank you for participating at",ADMINDOMAIN).' [#site_name#].</h5>';
											}
											wp_editor( $content, 'post_payment_success_msg_content', $settings);
										?>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<div class="buttons">
												<div class="inline_update">
												<a class="button-primary save  quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
												<a class="button-secondary cancel " href="javascript:void(0);" onclick="open_quick_edit('edit-payment-successful','payment-successful')" accesskey="c"><?php echo __("Cancel",ADMINDOMAIN);?></a>
												<span class="save_error" style="display:none"></span><span class="spinner"></span>
												</div>
											</div>	
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr class="payment-cancel alternate">
							<td><label class="form-textfield-label"><?php echo __('Payment cancelled message',ADMINDOMAIN); ?></label></td>
							
							<td><a href="javascript:void(0);" onclick="open_quick_edit('payment-cancel','edit-payment-cancel')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a> 
							| 
									<a href="javascript:void(0);" onclick="reset_to_default('','post_payment_cancel_msg_content','payment-cancel');"><?php echo __("Reset",ADMINDOMAIN);?></a>
									<span class="spinner" style="margin:0 18px 0;"></span>
									<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
							</td>
						</tr>
						<tr class="edit-payment-cancel alternate" style="display:none">
							<td width="100%" colspan="2">
								<h4 class="edit-sub-title"><?php echo __("Quick Edit",ADMINDOMAIN);?></h4>
								<table width="98%" align="left" class="tab-sub-table">
									<tr>
										<td style="line-height:10px">
											<label class="form-textfield-label sub-title"><?php echo __('Message',ADMINDOMAIN); ?></label>
										</td>
										<td width="90%" style="line-height:10px">
											<?php
											$settings =   array(
															'wpautop' => false, // use wpautop?
															'media_buttons' => false, // show insert/upload button(s)
															'textarea_name' => 'post_payment_cancel_msg_content', // set the textarea name to something different, square brackets [] can be used here
															'textarea_rows' => '7', // rows="..."
															'tabindex' => '',
															'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
															'editor_class' => '', // add extra class(es) to the editor textarea
															'teeny' => true, // output the minimal editor config used in Press This
															'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
															'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
															'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
														);	
											if($tmpdata['post_payment_cancel_msg_content'] != ""){
												$content = stripslashes($tmpdata['post_payment_cancel_msg_content']);
											}else{
												$content = '<h3>Sorry! Your listing has been canceled due to some reason. To get the details on it, contact us at [#admin_email#].</h3><h5>Thank you for your kind co-operation with [#site_name#]</h5>';
											}
											wp_editor( $content, 'post_payment_cancel_msg_content', $settings);
										?>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<div class="buttons">
												<div class="inline_update">
													<a class="button-primary save  quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
													<a class="button-secondary cancel" href="javascript:void(0);" onclick="open_quick_edit('edit-payment-cancel','payment-cancel')" accesskey="c"><?php echo __("Cancel",ADMINDOMAIN);?></a>
													<span class="save_error" style="display:none"></span><span class="spinner"></span>
												</div>
											</div>	
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr class="prebank-transfer">
							<td><label class="form-textfield-label"><?php echo __('PreBank transfer success message',ADMINDOMAIN); ?></label></td>
							
							<td><a href="javascript:void(0);" onclick="open_quick_edit('prebank-transfer','edit-prebank-transfer')"><?php echo __("Quick Edit",ADMINDOMAIN);?></a> 
							| 
									<a href="javascript:void(0);" onclick="reset_to_default('','post_pre_bank_trasfer_msg_content','prebank-transfer');"><?php echo __("Reset",ADMINDOMAIN);?></a>
									<span class="spinner" style="margin:0 18px 0;"></span>
									<span class="qucik_reset"><?php echo __("Data reset",ADMINDOMAIN);?></span>
							</td>
						</tr>
						<tr class="edit-prebank-transfer" style="display:none">
							<td width="100%" colspan="2">
								<h4 class="edit-sub-title"><?php echo __("Quick Edit",ADMINDOMAIN);?></h4>
								<table width="98%" align="left" class="tab-sub-table">
									<tr>
										<td style="line-height:10px">
											<label class="form-textfield-label sub-title"><?php echo __("Message",ADMINDOMAIN); ?></label>
										</td>
										<td width="90%" style="line-height:10px">
											<?php
											$settings =   array(
															'wpautop' => false, // use wpautop?
															'media_buttons' => false, // show insert/upload button(s)
															'textarea_name' => 'post_pre_bank_trasfer_msg_content', // set the textarea name to something different, square brackets [] can be used here
															'textarea_rows' => '7', // rows="..."
															'tabindex' => '',
															'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
															'editor_class' => '', // add extra class(es) to the editor textarea
															'teeny' => true, // output the minimal editor config used in Press This
															'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
															'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
															'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
														);	
											if($tmpdata['post_pre_bank_trasfer_msg_content'] != ""){
												$content = stripslashes($tmpdata['post_pre_bank_trasfer_msg_content']);
											}else{
												$content = '<p>'.__("Thank you! We have successfully received your PreBank payment request.",ADMINDOMAIN).'</p><p>'.__("To complete the transaction please transfer ",ADMINDOMAIN).' <b>[#payable_amt#] </b> '.__("to our bank account. Our bank details are below.",ADMINDOMAIN).'</p><p>'.__("Bank Name:",ADMINDOMAIN).' <b>[#bank_name#]</b></p><p>'.__("Account Number:",ADMINDOMAIN).' <b>[#account_number#]</b></p><p>'.__("Please include the following number as reference:",ADMINDOMAIN).'#[#submition_Id#]</p><p>[#submited_information_link#] </p><p>'.__("Thank you!",ADMINDOMAIN).'<br/>[#site_name#].</p>';
											}
											wp_editor( $content, 'post_pre_bank_trasfer_msg_content', $settings);
										?>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<div class="buttons">
												<div class="inline_update">
													<a class="button-primary save quick_save" href="javascript:void(0);" accesskey="s"><?php echo __("Save Changes",ADMINDOMAIN);?></a>
													<a class="button-secondary cancel " href="javascript:void(0);" onclick="open_quick_edit('edit-prebank-transfer','prebank-transfer')" accesskey="c"><?php echo __('Cancel',ADMINDOMAIN);?></a>
													<span class="save_error" style="display:none"></span>
													<span class="spinner"></span>
												</div>
											</div>	
										</td>
									</tr>
								</table>
							</td>
						</tr>
                        <?php do_action('tevolution_after_email_notification_setting');?>
					</tbody>
					</table>
			<?php					
			break;
	}
}
/*Finish the email setting data do action */
?>
