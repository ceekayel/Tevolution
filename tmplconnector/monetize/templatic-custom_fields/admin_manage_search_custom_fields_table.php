<?php
/*************************** LOAD THE BASE CLASS *******************************
 * The Tmpl_WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
 
if(isset($_REQUEST['is_search']) && $_REQUEST['is_search']=='delete'){
	 $postid = $_REQUEST['field_id'];
	 update_post_meta($postid,'is_search',0);
	 $url = site_url().'/wp-admin/admin.php';
	echo '<form action="'.$url.'" method="get" id="frm_custom_field" name="frm_custom_field">
	<input type="hidden" value="custom_fields" name="page"><input type="hidden" value="search_custom_fileds" name="search_subtab"><input type="hidden" value="removesuccess" name="search_custom_field_msg">
	</form>
	<script>document.frm_custom_field.submit();</script>
	';exit;	
}
 
 
if(!class_exists('Tmpl_WP_List_Table')){
    include_once( WP_PLUGIN_DIR . '/Tevolution/templatic.php');
}
class search_custom_fields_list_table extends Tmpl_WP_List_Table
{

	/***** FETCH ALL THE DATA AND STORE THEM IN AN ARRAY *****
	 * Call a function that will return all the data in an array and we will assign that result to a variable $custom_fields_data. FIRST OF ALL WE WILL FETCH DATA FROM POST META TABLE STORE THEM IN AN ARRAY $custom_fields_data 	 */
	function fetch_custom_fields_data($post_id = '' ,$post_title = '')
	{ 
		$fields_label  = $post_title;
		$show_in_post_type = get_post_meta($post_id,"post_type",true);
		$is_edit = get_post_meta($post_id,"is_edit",true);
		$type = get_post_meta($post_id,"ctype",true);
		$html_var = get_post_meta($post_id,"htmlvar_name",true);
		$admin_desc = get_post_field('post_content', $post_id);
		$sort_order = get_post_meta($post_id,'sort_order', true);
		if(get_post_meta($post_id,"is_active",true))
		  {
			$active = 'Yes';
		  }	
		else
		  {
			$active = 'No';
		  }	
		if($is_edit =='true'){
			$edit_url = admin_url("admin.php?page=custom_fields&action=addnew&amp;field_id=$post_id");
		}else{ $edit_url ='#'; }
		
		/* Start WPML Language conde*/
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))
		{
			global $wpdb, $sitepress_settings,$sitepress;			
			global $id, $__management_columns_posts_translations, $pagenow, $iclTranslationManagement;
			// get posts translations
            // get trids		
			// get trids		            		  
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
			 if(isset($__management_columns_posts_translations[$id][$v['code']]) && $__management_columns_posts_translations[$id][$v['code']]->element_id){
				  // Translation exists
				 $img = 'edit_translation.png';
				 $alt = sprintf(__('Edit the %s translation',ADMINDOMAIN), $v['display_name']);				 
				 $link = 'admin.php?page='.$post_type.'&action=addnew&amp;field_id='.$__management_columns_posts_translations[$id][$v['code']]->element_id.'&amp;lang='.$v['code'];				 
				  
			  }else{
				   // Translation does not exist
				$img = 'add_translation.png';
				$alt = sprintf(__('Add translation to %s',ADMINDOMAIN), $v['display_name']);
                	$src_lang = $sitepress->get_current_language() == 'all' ? $sitepress->get_default_language() : $sitepress->get_current_language();				        					
                    $link = '?page='.$post_type.'&action=addnew&trid='.$post_id.'&amp;lang='.$v['code'].'&amp;source_lang=' . $src_lang;
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
			}//finish foreach
		 
		 
		/*Finish WPML language code  */
		$meta_data = array(
			'ID'=> $post_id,
			'title'	=> '<strong><a href="'.$edit_url.'">'.$fields_label.'</a></strong><input type="hidden" name="custom_sort_order[]" value="' . esc_attr( $post_id ) . '" />',
			'icl_translations' => $country_url,
			'html_var' => $html_var,
			'show_in_post_type' 	=> $show_in_post_type,
			'type' => $type,
			'active' 	=> $active,
			'admin_desc' => $admin_desc
			);
		}else
		{
			$meta_data = array(
			'ID'=> $post_id,
			'title'	=> '<strong><a href="'.$edit_url.'">'.$fields_label.'</a></strong><input type="hidden" name="custom_sort_order[]" value="' . esc_attr( $post_id ) . '" />',			
			'show_in_post_type' 	=> $show_in_post_type,
			'html_var' => $html_var,
			'type' => $type,
			'active' 	=> $active,
			'admin_desc' => $admin_desc
			);
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
			$args = array(
				'post_type' 		=> 'custom_fields',
				'suppress_filters' => false,
				'posts_per_page' 	=> $per_page,
				'post_status' 		=> array('publish'),
				'paged' 			=> $paged,
				's'					=> $search_key,
				'meta_query' => array(
						'relation' => 'OR',
						array(
							'key' => 'is_search',
							'value' => 1,
							'compare' => '=',
							'type'=> 'text'
						),
					),
				);
		}
		else
		{
			$args = array(
				'post_type' 		=> 'custom_fields',
				'suppress_filters' => false,
				'posts_per_page' 	=> '-1',
				'paged' 			=> $paged,
				'post_status' 		=> array('publish'),
				'meta_query' => array(
						'relation' => 'AND',
						array(
							'key' => 'is_search',
							'value' => 1,
							'compare' => '=',
							'type'=> 'text'
						),
					),
				);
		}
		$post_meta_info = null;		
		add_filter('posts_join', 'custom_field_posts_where_filter');
		add_filter('posts_orderby', 'search_custom_field_posts_orderby_filter');
		$post_meta_info = new WP_Query($args);
		
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
				$custom_fields_data[] = $this->fetch_custom_fields_data($post->ID,$post->post_title);
		endwhile;
		remove_filter('posts_join', 'custom_field_posts_where_filter');
		remove_filter('posts_orderby', 'search_custom_field_posts_orderby_filter');
		return $custom_fields_data;
	}
	/* EOF - FETCH CUSTOM FIELDS DATA */
	
	/* DEFINE THE COLUMNS FOR THE TABLE */
	function get_columns()
	{	
		/*WPML lamguage translation plugin is active */
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
				'title' => __('Field name',ADMINDOMAIN),
				'icl_translations' => $country_flag,
				'show_in_post_type' => __('Shown in post-type',ADMINDOMAIN),
				'html_var' => __('Variable name',ADMINDOMAIN),
				'type' => __('Type',ADMINDOMAIN),
				'active' => __('Status',ADMINDOMAIN),
				'admin_desc' => __('Description',ADMINDOMAIN)
				);
		}else
		{
			$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __('Field name',ADMINDOMAIN),			
			'show_in_post_type' => __('Shown in post-type',ADMINDOMAIN),
			'html_var' => __('Variable name',ADMINDOMAIN),
			'type' => __('Type',ADMINDOMAIN),
			'active' => __('Active',ADMINDOMAIN),
			'admin_desc' => __('Description',ADMINDOMAIN)
			);
		}
		return $columns;
	}
	
	function process_bulk_action()
	{ 
		//Detect when a bulk action is being triggered...
		if('delete' == $this->current_action() )
		{
			 foreach($_REQUEST['checkbox'] as $postid)
			 {
				 update_post_meta($postid,'is_search',0);
			  }	 
			 $url = site_url().'/wp-admin/admin.php';
			 wp_redirect($url."?page=custom_fields&search_subtab=search_custom_fileds&search_custom_field_msg=removesuccess");
			 exit;	
		}
	}
    
	function prepare_items()
	{
		$per_page = $this->get_items_per_page('custom_fields_per_page', 10);
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
			$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
			$result = strcmp(@$a[$orderby], @$b[$orderby]); //Determine sort order			
			return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
		}
		//if(is_array($data))
		//usort( $data, 'usort_reorder');
		
		$current_page = $this->get_pagenum(); /* GET THE PAGINATION */
		$total_items = count($data); /* CALCULATE THE TOTAL ITEMS */
		if(is_array($data))
		$this->found_data = array_slice($data,(($current_page-1)*$per_page),$per_page); /* TRIM DATA FOR PAGINATION*/
		$this->items = $this->found_data; /* ASSIGN SORTED DATA TO ITEMS TO BE USED ELSEWHERE IN CLASS */
		/* REGISTER PAGINATION OPTIONS */
		
		$this->set_pagination_args( array(
			'total_items' => $total_items,      //WE have to calculate the total number of items
			'per_page'    => $per_page         //WE have to determine how many items to show on a page
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
			case 'active':
			case 'admin_desc':
			return $item[ $column_name ];
			default:
			return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
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
		$is_editable = get_post_meta($item['ID'],'is_edit',true);
		$is_deletable = get_post_meta($item['ID'],'is_delete',true);
		
			$action1 = array(
			'edit' => sprintf('<a href="?page=%s&action=%s&field_id=%s">Edit</a>',$_REQUEST['page'],'addnew',$item['ID'])
			);
		
		$action2 = array('delete' => sprintf('<a href="?page=%s&is_search=%s&field_id=%s" onclick="return confirm(\'Are you sure for remove custom field from search form?\')">Remove from advance Search</a>','custom_fields&search_subtab=search_custom_fileds','delete',$item['ID']));		
		$actions = array_merge($action1,$action2);
		return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions , $always_visible = false) );
	}
	
	function get_bulk_actions()
	{
		$actions = array(
			'delete' => 'Remove from advance Search'
			);
		return $actions;
	}
	
	function column_cb($item)
	{ 
		return sprintf(
			'<input type="checkbox" name="checkbox[]" id="checkbox[]" value="%s" />', $item['ID']
			);
	}
	
}


/*
 * Function name: search_custom_field_posts_orderby_filter
 * return : sort search custom field as on display ordering wise on search form
 */
function search_custom_field_posts_orderby_filter($orderby){
	global $wpdb;	
	
	$orderby= " (select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'search_sort_order' ) ASC";
	return $orderby;
}