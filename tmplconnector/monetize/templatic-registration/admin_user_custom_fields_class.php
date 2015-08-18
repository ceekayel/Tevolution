<?php
if(!class_exists('Tmpl_WP_List_Table')){
    include_once( WP_PLUGIN_DIR . '/Tevolution/templatic.php');
}
class wp_list_custom_user_field extends Tmpl_WP_List_Table 
{
	/***** FETCH ALL THE DATA AND STORE THEM IN AN ARRAY *****
	* Call a function that will return all the data in an array and we will assign that result to a variable $custom_user_field_data. FIRST OF ALL WE WILL FETCH DATA FROM POST META TABLE STORE THEM IN AN ARRAY $custom_user_field_data */
	function fetch_custom_user_fields_meta_data( $post_id = '' ,$post_title = '' , $post_status = '',$post_name='')
	{
		$ctype 	  = get_post_meta($post_id,'ctype',true);
		$sort_order = get_post_meta($post_id,'sort_order',true);	
		
		$meta_data = array( 'ID'			 => $post_id,
						'title'		 => '<strong><a href="'.site_url().	'/wp-admin/admin.php?page=user_custom_fields&action=addnew&cf='.$post_id.'">'.$post_title.'</a></strong><input type="hidden" name="user_field_sort[]" value="'.$post_id.'">',
						'type' 		 => $ctype,
						'variable_name' => $post_name,
						'active' 		 => $post_status,
						'display_order' => $sort_order
					);
		return $meta_data;
	}
	/* fetch all the custom user fields */
	function custom_user_fields()
	{
		global $post;
		$package_data = array();
		remove_all_actions('posts_where');
		$paged   = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		if(isset($_POST['s']) && $_POST['s'] != '')
		{
			$search_key = $_POST['s'];
			$args = array( 'post_type' 		=> 'custom_user_field',
						'posts_per_page' 	=> $per_page,
						'post_status' 		=> array('publish','draft'),
						'paged' 			=> $paged,
						's'				=> $search_key,
						'meta_key' => 'sort_order',
						'orderby' => 'meta_value_num',
						'meta_value_num'=>'sort_order',
						'order' => 'ASC'
					);
		}
		else
		{
			$args = array( 'post_type' 		=> 'custom_user_field',
						'posts_per_page' 	=> -1,
						'post_status'		=> array('publish','draft'),
						'paged' 			=> $paged,
						'meta_key' => 'sort_order',
						'orderby' => 'meta_value_num',
						'meta_value_num'=>'sort_order',
						'order' => 'ASC'
					);
		}
		$post_query = null;
		$post_query = new WP_Query($args);
		while ($post_query->have_posts()) : $post_query->the_post();
				$custom_user_field_data[] = $this->fetch_custom_user_fields_meta_data($post->ID,$post->post_title,$post->post_status,$post->post_name);
		endwhile;
		wp_reset_query();
		return $custom_user_field_data;
	}
	/* EOF - FETCH PACKAGE DATA */
	
	/* DEFINE THE COLUMNS FOR THE TABLE */
	function get_columns()
	{
		$columns = array( 'cb'            => '<input type="checkbox" />',
					   'title'         => __('Title',ADMINDOMAIN),
					   'type'          => __('Type',ADMINDOMAIN),
					   'variable_name' => __('Variable Name',ADMINDOMAIN),
					   'active'        => __('Active',ADMINDOMAIN),
					   //'display_order' => __('Display Order',DOMAIN)
					);
		return $columns;
	}
	/**/
	function process_bulk_action()
	{ 
		//Detect when a bulk action is being triggered...
		if( 'delete' === $this->current_action() )
		{
			$cids = $_REQUEST['cf'];
			foreach( $cids as $cid )
			{
				wp_delete_post($cid);
			}
			$url = site_url().'/wp-admin/admin.php';
			echo '
			<input type="hidden" value="user_custom_fields" name="page"><input type="hidden" value="delsuccess" name="usermetamsg">
			<script>document.register_custom_fields.submit();</script>
			';exit;	
		}
	}
    
	function prepare_items()
	{
		$per_page = $this->get_items_per_page('user_custom_fields_per_page', 10);
		$columns = $this->get_columns(); /* CALL FUNCTION TO GET THE COLUMNS */
		$hidden = array();
		$sortable = array();
		$sortable = $this->get_sortable_columns(); /* GET THE SORTABLE COLUMNS */
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action(); /* FUNCTION TO PROCESS THE BULK ACTIONS */
		//$action = $this->current_action();
		$data = $this->custom_user_fields(); /* RETIRIVE THE USER FIELDS DATA */
		
		/* FUNCTION THAT SORTS THE COLUMNS */
		function usort_reorder($a,$b)
		{
			$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
			$result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
			return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
		}
		if(is_array($data) && isset($_REQUEST['orderby'])){
			usort( $data, 'usort_reorder');
		}
		$current_page = $this->get_pagenum(); /* GET THE PAGINATION */
		$total_items = count($data); /* CALCULATE THE TOTAL ITEMS */
		if(is_array($data))
		$this->found_data = array_slice($data,(($current_page-1)*$per_page),$per_page); /* TRIM DATA FOR PAGINATION*/
		$this->items = $this->found_data; /* ASSIGN SORTED DATA TO ITEMS TO BE USED ELSEWHERE IN CLASS */
		
		$this->set_pagination_args( array('total_items' => $total_items,'per_page'    => $per_page));
	}
	
	/* To avoid the need to create a method for each column there is column_default that will process any column for which no special method is defined */
	function column_default( $item, $column_name )
	{
		switch( $column_name )
		{
			case 'ID':
			case 'title':
			case 'type':
			case 'variable_name':
			case 'display_order':			
				return $item[ $column_name ];
			case 'active':
				$active=($item[$column_name]=='publish')? 'Yes':'No';						
				
				return $active;
			default:
				return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}
	
	/* DEFINE THE COLUMNS TO BE SORTED */
	function get_sortable_columns()
	{
		$sortable_columns = array(
			'title' => array('title',true)
			);
		return $sortable_columns;
	}
	
	function column_title($item)
	{
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&action=%s&cf=%s">Edit</a>',$_REQUEST['page'],'addnew',$item['ID']),
			'delete' => sprintf('<a href="?page=%s&action_del=%s&cf[]=%s" onclick="return confirm(\'Are you sure for deleteing custom field?\')">Delete</a>',$_REQUEST['page'],'delete',$item['ID'])
			);
		
		return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions , $always_visible = false) );
	}
	
	function get_bulk_actions()
	{
		$actions = array(
			'delete' => 'Delete'
			);
		return $actions;
	}
	
	function column_cb($item)
	{ 
		return sprintf(
			'<input type="checkbox" name="cf[]" value="%s" />', $item['ID']
			);
	}
} ?>