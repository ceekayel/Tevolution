<?php
/*
 * class to show the transaction listing
 */
global $pagenow;

if(!class_exists('Tmpl_WP_List_Table')){
    include_once( WP_PLUGIN_DIR . '/Tevolution/templatic.php');
}
class wp_list_transaction extends Tmpl_WP_List_Table 
{
	/***** FETCH ALL THE DATA AND STORE THEM IN AN ARRAY *****
	* Call a function that will return all the data in an array and we will assign that result to a variable $transaction_data. FIRST OF ALL WE WILL FETCH DATA FROM TRANSACTION TABLE STORE THEM IN AN ARRAY $transaction_data */
	
	/* fetch all the transaction data */
	function fetch_transction()
	{
		global $post,$wpdb,$transection_db_table_name,$monetization;
		$transaction_data= array();
		$post_table = $wpdb->prefix."posts";
		$transactions = $wpdb->prefix."transactions";
		if( $_SESSION['query_string'] == ""){
			$_SESSION['query_string'] = "select * from $transactions as t where 1=1 AND  payable_amt > 0 AND (package_type is NULL OR package_type=0)";
		}
		
		
		$transinfo_count = $wpdb->get_results($_SESSION['query_string']);
		
		$transsql_limit=" group by t.trans_id order by t.trans_id desc";	

		$transinfo = $wpdb->get_results($_SESSION['query_string'].$transsql_limit);
		
		$trans_total_pages = count($transinfo_count);
		$tmpdata = get_option('templatic_settings');
		
		if($transinfo)
		{ 
			 foreach($transinfo as $transinfoObj) :
			 	$post_package='';
			 	if($transinfoObj->package_id!=0 && $transinfoObj->package_id!=''){
					$post_package = get_post($transinfoObj->package_id); 
				}
			 	$post = get_post($transinfoObj->post_id);				
				$post_type = @$post->post_type;
				$post_type_object = get_post_type_object($post_type);
				$post_type_label = @$post_type_object->labels->name;
				
				$featured_text = '';
				/*Check for featured posts: start*/
				$featured_type = @get_post_meta($post->ID,'featured_type',true);
				if( 'h' == $featured_type ){
					$featured_text = '<div>'.__("Featured",'templatic-admin').': '.__("Home",'templatic-admin').'</div>';
				}elseif( 'c' == $featured_type ){
					$featured_text = '<div>'.__("Featured",'templatic-admin').': '.__("Category",'templatic-admin').'</div>';
				}elseif( 'both' == $featured_type ){
					$featured_text = '<div>'.__("Featured",'templatic-admin').': '.__("Home, Category",'templatic-admin').'</div>';
				}else{
					$featured_text = '';
				}
				/*Check for featured posts: end*/
				
				/*Check for post is recurring: Start*/
				/*TODO: Need to make entry in post meta table, */
				/* whether currently inserting post is recurring or not */
				$is_recurring = ( @get_post_meta($post_package->ID,'recurring',true)) ? '<div>'.__("Recurring",'templatic-admin').'</div>' : '';				
				/*Check for post is recurring: End*/
				
				$color_taxonomy = 'trans_post_type_colour_'.$post_type;
				$color_taxonomy_value = '';
				
				$package = ( @$post_package->post_title)?'<a target="_blank" href="'.site_url().'/wp-admin/admin.php?page=monetization&action=edit&package_id='.$post_package->ID.'&tab=packages">'.$post_package->post_title.'</a>' :'-';
				if(isset($tmpdata[$color_taxonomy]) && $tmpdata[$color_taxonomy]!= '') { $color_taxonomy_value = $tmpdata[$color_taxonomy]; } 
				
				
				$transaction_price_pkg = $monetization->templ_get_price_info($transinfoObj->package_id,'');
				$publish_date =  date_i18n('Y-m-d',strtotime($transinfoObj->payment_date));
				$alive_days = $transaction_price_pkg[0]['alive_days'];
				$expired_date = date_i18n(get_option("date_format"),strtotime($publish_date. "+$alive_days day"));
				if($transinfoObj->trans_id !=''){
					$trans_id = '<div>'.__('ID:','templatic-admin').' <a href="'.site_url().'/wp-admin/admin.php?page=transcation&action=edit&trans_id='.$transinfoObj->trans_id.'">'.$transinfoObj->trans_id.'</a></div>';
				}else{
					$trans_id ='';
				}
				$transaction_data[] =  array(
                                                                                                                                  'ID'      => $transinfoObj->trans_id,
                                                                                                                                  'title'    => '<a href="'.site_url().'/wp-admin/user-edit.php?user_id='.$transinfoObj->user_id.'">'.$transinfoObj->billing_name.'</a><div>'.__('Email:','templatic-admin').' '.$transinfoObj->pay_email.'</div>'.$trans_id,
                                                                                                                                  'post_type'	  => '<label style="color:'.$color_taxonomy_value.'">'.$post_type_label.'<label>',
                                                                                                                                  'payment_method' => __($transinfoObj->payment_method,'templatic-admin'),
                                                                                                                                  'package' 	  => apply_filters('tmpl_transaction_package_type',$package.$featured_text.$is_recurring,$transinfoObj->payforpackage),
                                                                                                                                  'amount'    => fetch_currency_with_position($transinfoObj->payable_amt,2),
                                                                                                                                  'post_id'  => $transinfoObj->post_id,
                                                                                                                                  'tran_date'  => date_i18n(get_option("date_format"),strtotime($transinfoObj->payment_date)),
                                                                                                                                  'exp_date'  => $expired_date,
                                                                                                                                  'status'   => tmpl_get_transaction_status($transinfoObj->trans_id,$transinfoObj->post_id),

					);
			endforeach;
		}
		
		return $transaction_data;
	}
	/* EOF - FETCH TRANSACTION DATA */
	
	/* DEFINE THE COLUMNS FOR THE TABLE */
	function get_columns()
	{
		$columns = array(
					'cb'            => '<input type="checkbox" />',
					'title'         => __('User','templatic-admin'),
					'package'       => __('Price Package','templatic-admin'),
					'amount'        => __('Amount','templatic-admin'),
					'payment_method'=> __('Payment Method','templatic-admin'),
					'tran_date'     => __('Paid On','templatic-admin'),
					'exp_date'      => __('Exp. Date','templatic-admin'),					
					'status'        => __('Status','templatic-admin'),
				);
		$columns = apply_filters('transaction_column_fields',$columns);
		return $columns;
	}
	/**/
	function process_bulk_action()
	{ 
		if( 'pending' === $this->current_action() )
		{
			global $post,$wpdb,$transection_db_table_name;
			$cids = $_REQUEST['cf'];
			foreach( $cids as $cid )
			{
				$cid = explode(",",$cid);
				$my_post['ID'] = $cid[1];
				$my_post['post_status'] = 'draft';
				wp_update_post( $my_post );
				$trans_status = $wpdb->query("update $transection_db_table_name SET status = 0 where trans_id = '".$cid[0]."'");
			}
			$url = site_url().'/wp-admin/admin.php';
			?>
			
			
			<input type="hidden" value="transcation" name="page"><input type="hidden" value="delsuccess" name="usermetamsg">
			
			<script type="text/javascript">document.frm_transaction.submit();</script>
	<?php		
		}
		elseif( 'confirm' === $this->current_action() )
		{
			global $post,$wpdb,$transection_db_table_name;
			$cids = $_REQUEST['cf'];
			foreach( $cids as $cid )
			{
				$cid = explode(",",$cid);
				$my_post['ID'] = $cid[1];
				$my_post['post_status'] = 'publish';
				wp_update_post( $my_post );
				$trans_status = $wpdb->query("update $transection_db_table_name SET status = 1 where trans_id = '".$cid[0]."'");
			}
			$url = site_url().'/wp-admin/admin.php';
			?>
			
			
			<input type="hidden" value="transcation" name="page"><input type="hidden" value="delsuccess" name="usermetamsg">
			
			<script type="text/javascript">document.frm_transaction.submit();</script>
	<?php		
		}elseif('delete' === $this->current_action() ){
			global $post,$wpdb,$transection_db_table_name;
			$cids = $_REQUEST['cf'];
			foreach( $cids as $cid )
			{
				
				
				if( wp_verify_nonce($_REQUEST['_wpnonce'],'bulk-tevolution_page_transcation')){
					$cid = explode(",",$cid);
					$trans_status = $wpdb->query("delete from $transection_db_table_name where trans_id = '".$cid[0]."'");
				}else{		
					$redirect_to=site_url().'/wp-admin/admin.php?page=transcation&msgtype=noncenotverify';
					wp_redirect($redirect_to);
					exit;
				}
			}
			$url = site_url().'/wp-admin/admin.php';
			?>
               
               <input type="hidden" value="transcation" name="page"><input type="hidden" value="delsuccess" name="usermetamsg">
			
			<script type="text/javascript">document.frm_transaction.submit();</script>
       <?php
		}if( 'cancel' === $this->current_action() )
		{
			global $post,$wpdb,$transection_db_table_name;
			$cids = $_REQUEST['cf'];
			foreach( $cids as $cid )
			{
				$cid = explode(",",$cid);
				$my_post['ID'] = $cid[1];
				$my_post['post_status'] = 'draft';
				wp_update_post( $my_post );
				$trans_status = $wpdb->query("update $transection_db_table_name SET status = 2 where trans_id = '".$cid[0]."'");
			}
			$url = site_url().'/wp-admin/admin.php';
			?>
			
			
			<input type="hidden" value="transcation" name="page"><input type="hidden" value="delsuccess" name="usermetamsg">
			
			<script type="text/javascript">document.frm_transaction.submit();</script>
	<?php		
		}
	}
        
	function prepare_items()
	{
		$per_page = $this->get_items_per_page('transaction_per_page', 10);
		$columns = $this->get_columns(); /* CALL FUNCTION TO GET THE COLUMNS */
		
        $hidden = array();
		$sortable = array();
        $sortable = $this->get_sortable_columns(); /* GET THE SORTABLE COLUMNS */
		
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		$this->process_bulk_action(); /* FUNCTION TO PROCESS THE BULK ACTIONS */
		/*$action = $this->current_action();*/
		$data = $this->fetch_transction(); /* RETIRIVE THE TRANSACTION DATA */
		
		/* FUNCTION THAT SORTS THE COLUMNS */
		function usort_reorder($a,$b)
		{
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : ''; /*If no sort, default to title*/
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : ''; /*If no order, default to asc*/
            $result = strcmp($a[$orderby], $b[$orderby]); /*Determine sort order*/
            return ($order==='desc') ? $result : -$result; /*Send final sort direction to usort*/
        }
		if(is_array($data) && isset($_REQUEST['orderby']) && isset($_REQUEST['order'])){
       		usort( $data, 'usort_reorder');
		}
		
		$current_page = $this->get_pagenum(); /* GET THE PAGINATION */
		$total_items = count($data); /* CALCULATE THE TOTAL ITEMS */
		if(is_array($data))
			$this->found_data = array_slice($data,(($current_page-1)*$per_page),$per_page); /* TRIM DATA FOR PAGINATION*/
		$this->items = $this->found_data; /* ASSIGN SORTED DATA TO ITEMS TO BE USED ELSEWHERE IN CLASS */
		
		$this->set_pagination_args( array(
            'total_items' => $total_items,      /*WE have to calculate the total number of items*/
            'per_page'    => $per_page         /*WE have to determine how many items to show on a page*/
        ) );
	}
	
	/* To avoid the need to create a method for each column there is column_default that will process any column for which no special method is defined */
	function column_default( $item, $column_name )
	{
		switch( $column_name )
		{
			case 'ID':
			case 'post_id':
			case 'title':
			case 'post_title':
			case 'payment_method':
			case 'amount':
			case 'package':
			case 'post_type':
			case 'exp_date':
			case 'tran_date':
			case 'status':
			return $item[ $column_name ];
			default:
			return print_r( $item, true ) ; /*Show the whole array for troubleshooting purposes*/
		}
	}
	
	/* DEFINE THE COLUMNS TO BE SORTED */
	function get_sortable_columns()
	{
		$sortable_columns = array(
			'status' => array('status',true),
			'title' => array('title',true),
			'post_title'=>array('post_title',true),
			'post_type'=>array('post_type',true),
			'payment_method' => array('payment_method',true)
			);
		return $sortable_columns;
	}
	
	function get_bulk_actions()
	{
		$actions = array(
			'pending' => 'Pending',
			'confirm' => APPROVED_TEXT,
			'cancel'=>ORDER_CANCEL_TEXT,
			apply_filters('delete_tevolution_transaction_filter_value','delete') => apply_filters('delete_tevolution_transaction_filter_text','Delete')
			
			);
		return $actions;
	}
	
	function column_cb($item)
	{
		return sprintf(
			'<input type="checkbox" name="cf[]" value="%2s" />', $item['ID'].",".$item['post_id']
			);
	}		
}
?>