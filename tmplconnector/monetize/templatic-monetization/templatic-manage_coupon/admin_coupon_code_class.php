<?php
if(!class_exists('Tmpl_WP_List_Table')){
    include_once( WP_PLUGIN_DIR . '/Tevolution/templatic.php');
}
class wp_list_coupon_code extends Tmpl_WP_List_Table 
{
	/***** FETCH ALL THE DATA AND STORE THEM IN AN ARRAY *****
	* Call a function that will return all the data in an array and we will assign that result to a variable $coupon_code_data. FIRST OF ALL WE WILL FETCH DATA FROM POST META TABLE STORE THEM IN AN ARRAY $coupon_code_data */
	function fetch_coupon_code_meta_data( $post_id = '' ,$post_title = '')
	{
		$enddate = get_post_meta($post_id,'enddate',true);
		$startdate = get_post_meta($post_id,'startdate',true);
		$couponamt = get_post_meta($post_id,'couponamt',true);
		$coupondisc = get_post_meta($post_id,'coupondisc',true);
		if($coupondisc == 'per')
		{
			$coupondisc = __('Percentage',ADMINDOMAIN);
			$couponamt=$couponamt."%";
		}
		else
		{
			$coupondisc = __('Amount',ADMINDOMAIN);
			$couponamt=fetch_currency_with_position($couponamt);
		}
		$meta_data = array(
			'ID'				=> $post_id,
			'title'				=> '<strong><a href="'.site_url().	'/wp-admin/admin.php?page=monetization&action=addnew&tab=manage_coupon&cf='.$post_id.'">'.$post_title.'</a></strong>',
			'st_date' 			=> $startdate,
			'end_date' 			=> $enddate,
			'discount_type' 	=> $coupondisc,
			'value' 			=> $couponamt
			);
		return $meta_data;
	}
	/* 
	NAME :coupon_code
	DESCRIPTION : fetch all the coupon code data 
	*/
	function coupon_code()
	{
		global $post;
		$paged   = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		//$per_page = $this->get_items_per_page('package_per_page', 10);
		if(isset($_POST['s']) && $_POST['s'] != '')
		{
			$search_key = $_POST['s'];
			$args = array(
				'post_type' 		=> 'coupon_code',
				'posts_per_page' 	=> -1,
				'post_status' 		=> array('publish'),
				'paged' 			=> $paged,
				's'					=> $search_key
				);
		}
		else
		{
			$args = array(
				'post_type' 		=> 'coupon_code',
				'posts_per_page' 	=> -1,
				'post_status' 		=> array('publish'),
				'paged' 			=> $paged
				);
		}
		$post_query = null;
		$post_query = new WP_Query($args);
		$coupon_code_data=array();
		while ($post_query->have_posts()) : $post_query->the_post();
				$coupon_code_data[] = $this->fetch_coupon_code_meta_data($post->ID,$post->post_title);
		endwhile;
		
		return $coupon_code_data;
	}
	/* EOF - FETCH PACKAGE DATA */
	
	/* 
	NAME : get_columns
	DESCRIPTION : DEFINE THE COLUMNS FOR THE TABLE */
	function get_columns()
	{
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' =>  __('Title',ADMINDOMAIN),
			'st_date' =>  __('Start Date',ADMINDOMAIN),
			'end_date' =>  __('End Date',ADMINDOMAIN),
			'discount_type' => __('Discount Type',ADMINDOMAIN),
			'value' => __('Value',ADMINDOMAIN)
			);
		return $columns;
	}
	/*
	NAME : process_bulk_action
	DESCRIPTION : Detect when a bulk action is being triggered...
	*/
	function process_bulk_action()
	{ 
		if( 'delete' === $this->current_action() )
		{
			$cids = $_REQUEST['cf'];
			foreach( $cids as $cid )
			{
				wp_delete_post($cid);
			}
			$url = site_url().'/wp-admin/admin.php';
			echo '<form action="'.$url.'#manage_coupon" method="get" id="frm_user_emta" name="frm_coupon_code">
			<input type="hidden" value="monetization" name="page"><input type="hidden" value="manage_coupon" name="tab"><input type="hidden" value="delsuccess" name="usermetamsg">
			</form>
			<script>document.frm_coupon_code.submit();</script>
			';exit;	
		}
	}
    /*
	NAME : prepare_items
	DESCRIPTION : call function to prepare option,column ,data..
	*/
	function prepare_items()
	{
		$per_page = $this->get_items_per_page('package_per_page', 10);
		$columns = $this->get_columns(); /* CALL FUNCTION TO GET THE COLUMNS */
        $hidden = array();
		$sortable = array();
        $sortable = $this->get_sortable_columns(); /* GET THE SORTABLE COLUMNS */
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action(); /* FUNCTION TO PROCESS THE BULK ACTIONS */
		//$action = $this->current_action();
		$data = $this->coupon_code(); /* RETIRIVE THE PACKAGE DATA */
		
		/* FUNCTION THAT SORTS THE COLUMNS */
		function usort_reorder($a,$b)
		{
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to sort
        }
        usort( $data, 'usort_reorder');
		
		$current_page = $this->get_pagenum(); /* GET THE PAGINATION */
		$total_items = count($data); /* CALCULATE THE TOTAL ITEMS */
		$this->found_data = array_slice($data,(($current_page-1)*$per_page),$per_page); /* TRIM DATA FOR PAGINATION*/
		$this->items = $this->found_data; /* ASSIGN SORTED DATA TO ITEMS TO BE USED ELSEWHERE IN CLASS */
		
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
			case 'ID':
			case 'title':
			case 'st_date':
			case 'end_date':
			case 'discount_type':
			case 'value':
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
			'st_date' => array('st_date',true),
			'end_date' => array('end_date',true),
			'discount_type' => array('discount_type',true)
			);
		return $sortable_columns;
	}
	
	function column_title($item)
	{
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&action=%s&cf=%s&tab=%s">Edit</a>',$_REQUEST['page'],'addnew',$item['ID'],'manage_coupon'),
			'delete' => sprintf('<a href="?page=%s&action_del=%s&cf[]=%s&tab=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID'],'manage_coupon')
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