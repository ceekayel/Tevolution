<?php
/* to delete the payment options */
if(!class_exists('Tmpl_WP_List_Table')){
    include_once( WP_PLUGIN_DIR . '/Tevolution/templatic.php');
}
class wp_list_payment_options extends Tmpl_WP_List_Table 
{
	/***** FETCH ALL THE DATA AND STORE THEM IN AN ARRAY *****
	* Call a function that will return all the data in an array and we will assign that result to a variable $payment_options. FIRST OF ALL WE WILL FETCH DATA FROM POST META TABLE STORE THEM IN AN ARRAY $payment_options */
	function templ_get_pay_option_data($payment_method = '')
	{
		global $wpdb;		
		$paymentsql = "select * from $wpdb->options where option_name like 'payment_method_$payment_method'";
		$paymentInfo = $wpdb->get_row($paymentsql);
		$paymentInfo_value=get_option($paymentInfo->option_name);
		
		if((isset($paymentInfo->isactive) && $paymentInfo->isactive !='') || (isset($paymentInfo->autoload) && $paymentInfo->autoload == 'yes')){ 
			$payment_method_name = unserialize($paymentInfo->option_value);
			$payment_method_name = $payment_method_name['name'];
		}else{
			$payment_method_name =$payment_method;
		}
		
		if(!isset($paymentInfo )){ $paymentInfo  = array();}
		if(isset($paymentInfo->option_id)){
			$option_id = $paymentInfo->option_id; 
		}else{ $option_id ='<span class="error">Not installed</span>';}		
		
		/* Display Order */
		$display_order=(isset($paymentInfo_value['display_order'])) ? $paymentInfo_value['display_order'] :'-';
		
		if((isset($paymentInfo->isactive) && $paymentInfo->isactive !='') || (isset($paymentInfo->autoload) && $paymentInfo->autoload == 'yes')){ $status = "<span style='color:green; font-weight:normal;'>".__("Activated",DOMAIN)."</span>"; }else{ $status = "<span style='color:red; font-weight:normal;'>".__("Deactivated",DOMAIN)."</span>"; } /* display status */
		
		/* show install/uninstall links */
		if(get_option('payment_method_'.$payment_method)){
			$action = '<a href="'.site_url('/wp-admin/admin.php?page=monetization&tab=payment_options&uninstall='.$payment_method).'">'. __("Deactivate",DOMAIN).'</a><input type="hidden" value="'.$payment_method.'" name="payment_order[]"> ';
		}else{
			$action = '<a href="'.site_url('/wp-admin/admin.php?page=monetization&tab=payment_options&install='.$payment_method).'">'. __("Activate",DOMAIN).'</a>';
		}
		if((isset($paymentInfo->isactive) && $paymentInfo->isactive !='') || (isset($paymentInfo->autoload) && $paymentInfo->autoload == 'yes')){
			$trans_id = "<br/><a href='?page=monetization&action=settings&id=".$option_id."&tab=payment_options&payact=setting#option_payment'>".__('Setting',DOMAIN)."</a>";
		}else{
			$trans_id ='';
		}
		$meta_data = array(
			'ID'	=> $option_id,
			'title'	=> $payment_method_name.$trans_id,
			'status' 	=> $status,
			'display_order' => $display_order,
			'action' => $action
			);
		return $meta_data;
	}
	/* fetch all the payment options */
	function payment_options()
	{
		$no_include = array('.svn');
		if ($handle = opendir( WP_CONTENT_DIR. '/plugins')) {
			/* This is the correct way to loop over the directory. */
			while (false !== ($file = readdir($handle))) 
			{
				if($file=='.' || $file=='..')
				{
			
				}elseif(!in_array($file,$no_include))
				{
					$templatic_payment_option = explode('-',$file);
					if($templatic_payment_option[0] == 'Tevolution')
					{
						$templatic_payment_option_name = @$templatic_payment_option[1];
						if(file_exists(get_tmpl_plugin_directory().$file.'/'.$file.'.php') && is_plugin_active($file.'/'.$file.'.php'))
							$payment_options[] = $this->templ_get_pay_option_data($templatic_payment_option_name);
					}
				}
			}
		}
		if ($handle = opendir(plugin_dir_path( __FILE__ ).'payment')) {
				/* This is the correct way to loop over the directory. */
				while (false !== ($file = readdir($handle))) 
				{
					if($file=='.' || $file=='..')
					{
				
					}elseif(!in_array($file,$no_include))
					{
						$payment_options[] = $this->templ_get_pay_option_data($file);
					}
				}
			}
			
		foreach ($payment_options as $key => $row) {
		     $display_order[$key]  = $row['display_order']; 
			$status[$key]  = $row['status'];   			
		    // of course, replace 0 with whatever is the date field's index
		}
		array_multisort($status, SORT_ASC,$display_order, SORT_ASC, $payment_options);
		
		return $payment_options;
	}
	/* EOF - FETCH PACKAGE DATA */
	
	/* DEFINE THE COLUMNS FOR THE TABLE */
	function get_columns()
	{
		$columns = array('title'         => __('Payment Method',DOMAIN),			
					 // 'display_order' => __('Display Order',DOMAIN),
					  'status'        => __('Status',DOMAIN),
					  'action'        => __('Action',DOMAIN)
					);
		return $columns;
	}
	
    
	function prepare_items()
	{
		//$per_page = 3; /* NUMBER OF POSTS PER PAGE */
		$per_page = $this->get_items_per_page('package_per_page', 10);
		$columns = $this->get_columns(); /* CALL FUNCTION TO GET THE COLUMNS */
		$hidden = array();
		$sortable = array();
		$sortable = $this->get_sortable_columns(); /* GET THE SORTABLE COLUMNS */
		$this->_column_headers = array($columns, $hidden, $sortable);
		$action = $this->current_action();
		$data = $this->payment_options(); /* RETIRIVE THE PACKAGE DATA */
		
		$current_page = $this->get_pagenum(); /* GET THE PAGINATION */
		$total_items = count($data); /* CALCULATE THE TOTAL ITEMS */
		$this->found_data = array_slice($data,(($current_page-1)*$per_page),$per_page); /* TRIM DATA FOR PAGINATION*/
		$this->items = $this->found_data; /* ASSIGN SORTED DATA TO ITEMS TO BE USED ELSEWHERE IN CLASS */
		/* REGISTER PAGINATION OPTIONS */
		
		$this->set_pagination_args( array(
									'total_items' => $total_items,      //WE have to calculate the total number of items
									'per_page'    => $per_page         //WE have to determine how many items to show on a page
								) 
							);
	}
	
	/* To avoid the need to create a method for each column there is column_default that will process any column for which no special method is defined */
	function column_default( $item, $column_name )
	{
		switch( $column_name )
		{
			case 'ID':
			case 'title':
			case 'status':
			case 'display_order':
			case 'action':
			return $item[ $column_name ];
			default:
			return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}
	
	/* DEFINE THE COLUMNS TO BE SORTED */
	function get_sortable_columns()
	{
		$sortable_columns = array(
			'status' => array('status',true)
			);
		return $sortable_columns;
	}
	
	function column_title($item)
	{
		if(strtolower($item['status']) == strtolower('yes'))
		{
			$actions = array(
				'settings' => sprintf('<a href="?page=%s&action=%s&id=%s&tab=%s&payact=%s#option_payment">Settings</a>',$_REQUEST['page'],'settings',$item['ID'],'payment_options','setting')
				);
		}else{
			$actions = array();
		}
		
		return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions , $always_visible = false) );
	}
	
	
	function column_cb($item)
	{ 
		return sprintf('<input type="checkbox" name="op_id[]" value="%s" />', $item['ID']);
	}
} ?>