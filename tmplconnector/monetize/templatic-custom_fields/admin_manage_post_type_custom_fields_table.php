<?php

/*************************** LOAD THE BASE CLASS *******************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
 
add_action('admin_init','tmpl_tevolution_custom_sort_order'); 
function tmpl_tevolution_custom_sort_order(){
	global $wpdb,$current_user;

	/*
	 * Get the last custom field sort order number
	 */
	if(!get_option('tevolution_custom_sort_order')){ 
		 $args=array('post_type' => 'custom_fields',
					 'posts_per_page' => -1,				
					);
		$post_query = new WP_Query($args);
		if($post_query->have_posts()){
			while ($post_query->have_posts()) { $post_query->the_post();
				$post_type=explode(',',get_post_meta(get_the_ID(),'post_type',true));
				$sort_order=get_post_meta(get_the_ID(),'sort_order',true);
				$heading_type=get_post_meta(get_the_ID(),'heading_type',true);
				foreach($post_type as $value){
					if(!get_post_meta(get_the_ID(), $value.'_sort_order', true))
						update_post_meta(get_the_ID(), $value.'_sort_order', $sort_order);
					update_post_meta(get_the_ID(), $value.'_heading_type', $heading_type);
				}
				update_post_meta(get_the_ID(),'is_submit_field',1);
			}
		} 
		
		update_option('tevolution_custom_sort_order','1');
	}
	
	
	
	$post_types=array_merge(tevolution_get_post_type(),array('post'));
	$i=1;
	foreach($post_types as $type):
		if($i==1){
			if(isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields'] !=''){
				$_REQUEST['post_type_fields']=(isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields']!="")?$_REQUEST['post_type_fields'] :$type; 
			}else{
				$_REQUEST['post_type_fields']= apply_filters('tmpl_default_posttype','listing');
			}
		}
		$i++;
	endforeach;	

	if(isset($_REQUEST['page']) && $_REQUEST['page']=='custom_fields' && isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields']!=''){
		global $heading_post_type,$wpdb;
		$heading_post_type=fetch_heading_per_post_type($_REQUEST['post_type_fields']);
	}
}


if(!class_exists('Tmpl_WP_List_Table')){
    include_once( WP_PLUGIN_DIR . '/Tevolution/templatic.php');
}

class custom_fields_list_table extends Tmpl_WP_List_Table
{

	/* 
	 * FETCH ALL THE DATA AND STORE THEM IN AN ARRAY *****
	 * Call a function that will return all the data in an array and we will assign that result to a variable $custom_fields_data.
	 * FIRST OF ALL WE WILL FETCH DATA FROM POST META TABLE STORE THEM IN AN ARRAY $custom_fields_data 
	 */
	function fetch_custom_fields_data($post_id = '' ,$post_title = '')
	{ 
		$headingtype='';
		$fields_label  = $post_title;
		$show_in_post_type = get_post_meta($post_id,"post_type",true);
		$is_edit = get_post_meta($post_id,"is_edit",true);
		$type = get_post_meta($post_id,"ctype",true);
		$html_var = get_post_meta($post_id,"htmlvar_name",true);		
		$sort_order = get_post_meta($post_id,'sort_order', true);
		$heading_type=get_post_meta($post_id,'heading_type',true);
		if(isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields']!=''){
			$heading_post_type=fetch_heading_per_post_type($_REQUEST['post_type_fields']);
			if($_REQUEST['post_type_fields'] ==''){
				$post_type='post';
			}
			$heading_type=get_post_meta($post_id,$_REQUEST['post_type_fields'].'_heading_type',true);
			$headingtype.='<select class="custom_field_heading_type custom_field_heading_type_'.$post_id.'" name="heading_type"  data-id="'.$post_id.'">';
			foreach($heading_post_type as $key=>$value){					
				$selected=($heading_type==htmlspecialchars_decode($value))? 'selected' :'';
				$headingtype.='<option value="'.$value.'" '.$selected.'>'.$value.'</option>';	
			}
			$headingtype.='</select>';
			$heading_type=$headingtype;
			
			$sort_order=get_post_meta($post_id, $_REQUEST['post_type_fields'].'_sort_order',true);
			$sort_order='<input type="text" class="custom_field_sort_order custom_field_sort_order_'.$post_id.'" name="sort_order" value="'.$sort_order.'" size="5" data-id="'.$post_id.'"/>';
		}else{			
			$sort_order=get_post_meta($post_id,'sort_order',true);
		}
		if(get_post_meta($post_id,"is_active",true)){
			$active = '<span style="color:green; font-weight:normal;">'.__('Active','templatic-admin')."</span>";
		}else{
			$active = '<span style="color:#e66f00; font-weight:normal;">'.__('Inactive','templatic-admin')."</span>";
		}
		
		$edit_url=($is_edit =='true')? admin_url("admin.php?page=custom_setup&ctab=custom_fields&action=addnew&amp;field_id=$post_id") : '#';		
		
		/* Start WPML Language conde*/
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))
		{
			global $wpdb, $sitepress_settings,$sitepress;			
			global $id, $__management_columns_posts_translations, $pagenow, $iclTranslationManagement;
			/* get posts translations */
		            		  
			$trids = $wpdb->get_col("SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_type='post_custom_fields' AND element_id IN (".$post_id.")");		 
			$ptrs = $wpdb->get_results("SELECT trid, element_id, language_code, source_language_code FROM {$wpdb->prefix}icl_translations WHERE trid IN (". join(',', $trids).")");		  
			foreach($ptrs as $v){
				$by_trid[$v->trid][] = $v;
			}		 
		 
		   foreach($ptrs as $v){			  
				if($v->element_id == $post_id){
					$el_trid = $v->trid;
					foreach($ptrs as $val){
						if($val->trid == $el_trid){
							$__management_columns_posts_translations[$v->element_id][$val->language_code] = $val;					   
						}
					}
				}
			}		  
		$country_url = '';		
		$active_languages = $sitepress->get_active_languages();
			foreach($active_languages as $k=>$v){				
			if($v['code']==$sitepress->get_current_language()) continue;
			 $post_type = isset($_REQUEST['page']) ? $_REQUEST['page'] : 'custom_fields';						
			 if(isset($__management_columns_posts_translations[$post_id][$v['code']]) && $__management_columns_posts_translations[$post_id][$v['code']]->element_id){
				  /* Translation exists */
				 $img = 'edit_translation.png';
				 $alt = sprintf(__('Edit the %s translation','templatic-admin'), $v['display_name']);				 
				 $link = 'admin.php?page='.$post_type.'&ctab=custom_fields&action=addnew&amp;field_id='.$__management_columns_posts_translations[$post_id][$v['code']]->element_id.'&amp;lang='.$v['code'];				 
				  
			  }else{
				   /* Translation does not exist */
				$img = 'add_translation.png';
				$alt = sprintf(__('Add translation to %s','templatic-admin'), $v['display_name']);
					$src_lang = $sitepress->get_current_language() == 'all' ? $sitepress->get_default_language() : $sitepress->get_current_language();				        					
					$link = '?page='.$post_type.'&ctab=custom_fields&action=addnew&trid='.$post_id.'&amp;lang='.$v['code'].'&amp;source_lang=' . $src_lang;
			  }
			  
			  if($link){
				 if($link == '#'){
					icl_pop_info($alt, ICL_PLUGIN_URL . '/res/img/' .$img, array('icon_size' => 16, 'but_style'=>array('icl_pop_info_but_noabs')));                    
				 }else{
					$country_url.= '<a href="'.$link.'" title="'.$alt.'">';
					$country_url.= '<img style="padding:1px;margin:2px;" border="0" src="'.ICL_PLUGIN_URL . '/res/img/' .$img.'" alt="'.$alt.'" width="16" height="16" />';
					$country_url.= '</a>';
				 }
			  }			  
			}/* finish foreach */
		 
		 
		/* Finish WPML language code  */
		$meta_data = apply_filters('tmpl_fileds_column_value',array(
                                                                                'ID'=> $post_id,
                                                                                'title'	=> '<strong><a href="'.$edit_url.'">'.$fields_label.'</a></strong><input type="hidden" name="custom_sort_order[]" value="' . esc_attr( $post_id ) . '" />',
                                                                                'icl_translations' => $country_url,
                                                                                'html_var' => $html_var,
                                                                                'show_in_post_type' => $show_in_post_type,
                                                                                'type' => $type,
                                                                                'heading_type' => ($type!='heading_type')?$heading_type:'',
                                                                                'sort_order' =>$sort_order,
                                                                                'active' 	=> $active,	
			),$post_id);
		}else
		{
                                                            $meta_data = apply_filters('tmpl_fileds_column_value',array(
                                                                      'ID'=> $post_id,
                                                                      'title'	=> '<strong><a href="'.$edit_url.'">'.$fields_label.'</a></strong><input type="hidden" name="custom_sort_order[]" value="' . esc_attr( $post_id ) . '" />',			
                                                                      'show_in_post_type' 	=> $show_in_post_type,
                                                                      'html_var' => $html_var,
                                                                      'type' => $type,
                                                                      'heading_type' => ($type!='heading_type')?$heading_type:'',
                                                                      'sort_order' =>$sort_order,
                                                                      'active'  => $active,
                                                            ),$post_id);
		}
		return $meta_data;
	}
	function custom_fields_data()
	{
		global $post, $paged, $query_args,$sitepress_settings,$sitepress;
		$paged   = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$per_page = get_option('posts_per_page');
		if(isset($_POST['s']) && $_POST['s'] != '')
		{
			$search_key = $_POST['s'];
			if(isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields']!=''){
				$args = array('post_type' 		=> 'custom_fields',
							  'suppress_filters' => false,
							  'posts_per_page' 	=> '-1',
							  'paged' 			=> $paged,
							  's'					=> $search_key,
							  'post_status' 		=> array('publish'),
							  'meta_query' => array(
										'relation' => 'AND',
										array(
											'key' => 'post_type_'.$_REQUEST['post_type_fields'].'',
											'value' => array('all',$_REQUEST['post_type_fields']),
											'compare' => 'IN',
											'type'=> 'text'
										),
									),		 
							  'meta_key' => $_REQUEST['post_type_fields'].'_sort_order',
							  'orderby' => 'meta_value_num',
							  'meta_value_num'=>$_REQUEST['post_type_fields'].'_sort_order',
							  'order' => 'ASC'
					);
				
			}else{
			
				$args = array('post_type' 		=> 'custom_fields',
							'suppress_filters' => false,
							'posts_per_page' 	=> -1,
							'post_status' 		=> array('publish'),
							'paged' 			=> $paged,
							's'					=> $search_key,
							'meta_key' => 'sort_order',
							'orderby' => 'meta_value_num',
							'meta_value_num'=>'sort_order',
							'order' => 'ASC'
					);
			}
		}
		else
		{
			if(isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields']!=''){
				$args = array('post_type' 		=> 'custom_fields',
							  'suppress_filters' => false,
							  'posts_per_page' 	=> '-1',
							  'paged' 			=> $paged,
							  'post_status' 		=> array('publish'),
							  'meta_query' => array(
										'relation' => 'AND',
										array(
											'key' => 'post_type_'.$_REQUEST['post_type_fields'].'',
											'value' => array('all',$_REQUEST['post_type_fields']),
											'compare' => 'IN',
											'type'=> 'text'
										),
									),		 
							  'meta_key' => $_REQUEST['post_type_fields'].'_sort_order',
							  'orderby' => 'meta_value_num',
							  'meta_value_num'=>$_REQUEST['post_type_fields'].'_sort_order',
							  'order' => 'ASC'
					);

			}else{
				$args = array('post_type' 		=> 'custom_fields',
							  'suppress_filters' => false,
							  'posts_per_page' 	=> '-1',
							  'paged' 			=> $paged,
							  'post_status' 		=> array('publish'),							  
							  'meta_key' => 'sort_order',
							  'orderby' => 'meta_value_num',
							  'meta_value_num'=>'sort_order',
							  'order' => 'ASC'
					);
			}
		}
		$post_meta_info = null;		
		add_filter('posts_join', 'custom_field_posts_where_filter');
		
		$post_meta_info = new WP_Query($args);
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
				$custom_fields_data[] = $this->fetch_custom_fields_data($post->ID,$post->post_title);
		endwhile;
		remove_filter('posts_join', 'custom_field_posts_where_filter');
		return $custom_fields_data;
	}
	/* EOF - FETCH CUSTOM FIELDS DATA */
	
	/* DEFINE THE COLUMNS FOR THE TABLE */
	function get_columns()
	{	
		/* WPML language translation plugin is active */
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))
		{
			$country_flag = '';
			$languages = icl_get_languages('skip_missing=0');
			if(!empty($languages)){
				foreach($languages as $l){
					if(!$l['active']) echo '<a href="'.$l['url'].'">';
					if(!$l['active']) $country_flag .= '<img src="'.$l['country_flag_url'].'" height="12" alt="'.$l['language_code'].'" width="18" />'.' ';
					if(!$l['active']) echo '</a>';
				}
			}
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'title' => __('Field name','templatic-admin'),
				'icl_translations' => $country_flag,
				'show_in_post_type' => __('Shown in post-type','templatic-admin'),
				'html_var' => __('Variable name','templatic-admin'),
				'type' => __('Type','templatic-admin'),
				'heading_type' => __('Heading Type','templatic-admin'),
				'sort_order' => __('Sort Order','templatic-admin'),
				'active' => __('Status','templatic-admin'),
				);
		}else
		{
			$columns = apply_filters('tmpl_fileds_column',array(
			'cb' => '<input type="checkbox" />',
			'title' => __('Field name','templatic-admin'),			
			'show_in_post_type' => __('Shown in post-type','templatic-admin'),
			'html_var' => __('Variable name','templatic-admin'),
			'type' => __('Type','templatic-admin'),	
			'heading_type' => __('Heading Type','templatic-admin'),
			'sort_order' => __('Sort Order','templatic-admin'),
			'active' => __('Status','templatic-admin'),
			));
		}
		return $columns;
	}
	
	function process_bulk_action()
	{ 
		/* Detect when a bulk action is being triggered... */
		if('delete' == $this->current_action() )
		{
			 foreach($_REQUEST['checkbox'] as $postid){
				 wp_delete_post($postid);
			  }
			 update_option('tmpl_default_fields_inserted','');
			 $url = site_url().'/wp-admin/admin.php';
			 wp_redirect($url."?page=custom_setup&ctab=custom_fields&custom_field_msg=delsuccess");
			 exit;	
		}
	}
	
	function prepare_items()
	{
		if($this->get_items_per_page('taxonomy_per_page') =='')
			$per_page = $this->get_items_per_page('taxonomy_per_page', 10);
		$columns = $this->get_columns(); /* CALL FUNCTION TO GET THE COLUMNS */
		$hidden = array();
		$sortable = array();
		$sortable = $this->get_sortable_columns(); /* GET THE SORTABLE COLUMNS */
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action(); /* FUNCTION TO PROCESS THE BULK ACTIONS */
		$data = $this->custom_fields_data(); /* RETIRIVE THE PACKAGE DATA */
		
		/* FUNCTION THAT SORTS THE COLUMNS */
		function usort_reorder($a,$b)
		{
			$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; /* If no sort, default to title */
			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; /* If no order, default to asc */
			$result = strcmp(@$a[$orderby], @$b[$orderby]); /* Determine sort order	 */		
			return ($order==='asc') ? $result : -$result; /* Send final sort direction to usort  */
		}
		
		$current_page = $this->get_pagenum(); /* GET THE PAGINATION */
		$total_items = count($data); /* CALCULATE THE TOTAL ITEMS */
		if(is_array($data))
		$this->found_data = array_slice($data,(($current_page-1)*$per_page),$per_page); /* TRIM DATA FOR PAGINATION*/
		$this->items = $this->found_data; /* ASSIGN SORTED DATA TO ITEMS TO BE USED ELSEWHERE IN CLASS */
		/* REGISTER PAGINATION OPTIONS */
		
		$this->set_pagination_args( array(
			'total_items' => $total_items,      /* WE have to calculate the total number of items */
			'per_page'    => $per_page         /* WE have to determine how many items to show on a page */
		) );
	}
	
	/* To avoid the need to create a method for each column there is column_default that will process any column for which no special method is defined */
	function column_default( $item, $column_name )
	{
		switch( $column_name )
		{
			case 'cb':
			case 'title':
			case 'icl_translations':
			case 'show_in_post_type':
			case 'html_var':
			case 'type':
			case 'admin_desc':
			case 'heading_type':
			case 'sort_order':
			case 'active': 
				return $item[ $column_name ];
			default:
				return $item[ $column_name ]; /* Show the whole array for troubleshooting purposes  */
		}
	}
	
	/* DEFINE THE COLUMNS TO BE SORTED */
	function get_sortable_columns()
	{
		$sortable_columns = array(
			'title' => array('title',true),
			'show_in_post_type' => array('show_in_post_type',true)
			);
		return $sortable_columns;
	}
	
	function column_title($item)
	{
		/* array for fields which are not deletable */
		$exclude_del_array = apply_filters('tmpl_not_deletable_fileds',array('category','post_title','map_view','basic_inf','post_content','post_excerpt','post_images'));
		
		$is_editable = get_post_meta($item['ID'],'is_edit',true);
		$is_deletable = get_post_meta($item['ID'],'is_delete',true);		
		$action1 = array( 'edit' => sprintf('<a href="?page=%s&ctab=%s&action=%s&field_id=%s">Edit</a>',$_REQUEST['page'],'custom_fields','addnew',$item['ID']));
		
		if(!in_array($item['html_var'],$exclude_del_array))
			$action2 = array('delete' => sprintf('<a href="?page=%s&ctab=%s&pagetype=%s&field_id=%s" onclick="return confirm(\'Are you sure for deleteing custom field?\')">Delete Permanently</a>','custom_setup','custom_fields','delete',$item['ID']));
		else	
			$action2 = array('delete' => sprintf(__('Not Deletable','templatic-admin'),'custom_setup','custom_fields','delete',$item['ID']));
			
		$actions = array_merge($action1,$action2);
		return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions , $always_visible = false) );
	}
	
	function get_bulk_actions()
	{
		$actions = array('delete' => 'Delete permanently');
		return $actions;
	}
	
	function column_cb($item)
	{ 
		return sprintf('<input type="checkbox" name="checkbox[]" id="checkbox[]" value="%s" />', $item['ID']);
	}
}

/* Custom field sort order hook using drag and move position */
add_action('wp_ajax_custom_field_sortorder','tevolution_custom_field_sortorder');
function tevolution_custom_field_sortorder(){
	
	$user_id = get_current_user_id();	
	if(isset($_REQUEST['paging_input']) && $_REQUEST['paging_input']!=0 && $_REQUEST['paging_input']!=1){
		$taxonomy_per_page=get_user_meta($user_id,'taxonomy_per_page',true);
		$j =$_REQUEST['paging_input']*$taxonomy_per_page+1;
		$test='';
		$i=$taxonomy_per_page;		
		for($j; $j >= count($_REQUEST['custom_sort_order']);$j--){			
			if($_REQUEST['custom_sort_order'][$i]!=''){				
				/*change sort order as per post type wise if post type set */
				if($_REQUEST['post_type']!=''){
					update_post_meta($_REQUEST['custom_sort_order'][$i],$_REQUEST['post_type'].'_sort_order',$j);
				}else{
					update_post_meta($_REQUEST['custom_sort_order'][$i],'sort_order',$j);
				}
				
			}
			$i--;	
		}
	}else{
		$j=1;
		for($i=0;$i<count($_REQUEST['custom_sort_order']);$i++){
			/*change sort order as per post type wise if post type set */
			if($_REQUEST['post_type']!=''){
				update_post_meta($_REQUEST['custom_sort_order'][$i],$_REQUEST['post_type'].'_sort_order',$j);
			}else{				
				update_post_meta($_REQUEST['custom_sort_order'][$i],'sort_order',$j);
			}
			
			$j++;
		}
	}	
	exit;
}


/* update tevolution heading type and sort order using wordpress admin ajax */
add_action('wp_ajax_update_tevolution_custom_fields','tmpl_update_tevolution_custom_fields');
function tmpl_update_tevolution_custom_fields(){
	global $wpdb;
	$post_id=$_REQUEST['post_id'];
	$post_type=$_REQUEST['post_type'];
	
	/*change heading type as per post type wise */
	if(isset($_REQUEST['heading_type']) && $_REQUEST['heading_type']!=''){
		update_post_meta($post_id,$post_type.'_heading_type',$_REQUEST['heading_type']);
	}
	
	/*change sort order as per post type wise */
	if(isset($_REQUEST['sort_order']) && $_REQUEST['sort_order']!=''){
		update_post_meta($post_id,$post_type.'_sort_order',$_REQUEST['sort_order']);
	}
	echo '<span style="color:green;">'.__('Saved','templatic-admin').'</span>';
	exit;
}