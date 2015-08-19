<?php
/*
 * fetch payment and its related functions
 */
global $wp_query,$wpdb,$wp_rewrite;

/*
  fetch all the active payment method for preview page.
 */
function templatic_payment_option_preview_page()
{
		global $wpdb,$monetization;
		$paymentsql = "select * from $wpdb->options where option_name like 'payment_method_%' order by option_id";
		$paymentinfo = $wpdb->get_results($paymentsql);
		$one_payment='';
		if($paymentinfo)
		{
			$paymentOptionArray = array();
			$paymethodKeyarray = array();
			$i=0;
			foreach($paymentinfo as $paymentinfoObj)
			{
				$paymentInfo = unserialize($paymentinfoObj->option_value);
				if($paymentInfo['isactive'])
				{
					$paymethodKeyarray[] = $paymentInfo['key'];
					$paymentOptionArray[$paymentInfo['display_order']][] = $paymentInfo;
					$i++;
				}
			}
			if($i==1):?>
               	<h5 class="payment_head"> 
					<?php
					 $one_payment=1;
						$pay_with_title = __('Pay With',DOMAIN);
						if(function_exists('icl_register_string')){
							icl_register_string(ADMINDOMAIN,$pay_with_title,$pay_with_title);
						}
						
						if(function_exists('icl_t')){
							$pay_with_title1 = icl_t(ADMINDOMAIN,$pay_with_title,$pay_with_title);
						}else{
							$pay_with_title1 = __($pay_with_title,ADMINDOMAIN); 
						}
						echo apply_filters('tevolution_payment_title',$pay_with_title1);
					?>
                </h5>
               <?php else:?>
				<h5 class="payment_head"> 
					<?php 
						$select_payment_method_title = SELECT_PAY_MEHTOD_TEXT;
						if(function_exists('icl_register_string')){
							icl_register_string(ADMINDOMAIN,$select_payment_method_title,$select_payment_method_title);
						}
						
						if(function_exists('icl_t')){
							$select_payment_method_title1 = icl_t(ADMINDOMAIN,$select_payment_method_title,$select_payment_method_title);
							echo apply_filters('tevolution_payment_title',$select_payment_method_title1);
						}else{							
							echo apply_filters('tevolution_payment_title',__('Select Payment Method',DOMAIN));
						}
						
					?>
                </h5>
               <?php 
			endif;
			echo '<ul class="payment_method">';
			ksort($paymentOptionArray);
			if($paymentOptionArray)
			{
				foreach($paymentOptionArray as $key=>$paymentInfoval)
				{
					$count_payopts = count($paymentOptionArray);
					for($i=0;$i<count($paymentInfoval);$i++)
					{
						
						$paymentInfo = $paymentInfoval[$i];
						$jsfunction = 'onclick="showoptions(this.value);"';
						$chked = '';
						if($key==1)
						{
							$chked = 'checked="checked"';
						}elseif($count_payopts == 1 && $paymentInfo['key'] == 'prebanktransfer' ){
							$chked = 'checked="checked"';
						}
						$disable_input = false;
						$payment_display_name = "";
						if(isset($_SESSION['custom_fields']['package_select']) && isset($_SESSION['custom_fields']['total_price']) )
						$listing_price_info = $monetization->templ_get_price_info($_SESSION['custom_fields']['package_select'],$_SESSION['custom_fields']['total_price']);
						$payment_display_name = $paymentInfo['name'];
					?>
		<li id="<?php echo $paymentInfo['key'];?>">
        <label>
        <?php if(count($paymentOptionArray) > 1)
		{?>
		  <input <?php echo $jsfunction;?>  type="radio" value="<?php echo $paymentInfo['key'];?>" id="<?php echo $paymentInfo['key'];?>_id" name="paymentmethod" <?php echo $chked; if($disable_input){echo "disabled=true";}?> />  
    <?php }else{?>
		  <input <?php echo $jsfunction;?>  type="radio" value="<?php echo $paymentInfo['key'];?>" id="<?php echo $paymentInfo['key'];?>_id" name="paymentmethod" checked style="display:none" />  
	<?php }?>
						<?php 
							if(function_exists('icl_register_string')){
								$context = DOMAIN;
								icl_register_string($context,$payment_display_name,$payment_display_name);
							}
							if(function_exists('icl_t')){
								$payment_display_name = icl_t(DOMAIN,$payment_display_name,$payment_display_name);
							}
							else
							{
								$payment_display_name = sprintf(__('%1$s',DOMAIN), __($payment_display_name,DOMAIN));
							}
							echo $payment_display_name;
						?>
						</label> 
					</li>
		  <?php
					}
				}
				?>
					<div id="payment_errors" class="payment_error"></div>
					<script type="text/javascript" async src="<?php echo CUSTOM_FIELDS_URLPATH; ?>js/payment_gateway_validation.js"></script>   
				<?php
			}else
			{
			?>
			<li><?php echo NO_PAYMENT_METHOD_MSG;?></li>
			<?php
			}
			
		?>
 	  
  </ul>
  <?php
  if($paymentOptionArray)
		{
			echo "<div class='payment_method payment_credit_card_info'>";
			foreach($paymentOptionArray as $key=>$paymentInfoval)
			{
				$count_payopts = count($paymentOptionArray);
				for($i=0;$i<count($paymentInfoval);$i++)
				{
					
					$paymentInfo = $paymentInfoval[$i];
					$jsfunction = 'onclick="showoptions(this.value);"';
					$chked = '';					
					if($key==1 || $one_payment==1)
					{
						$chked = 'checked="checked"';
					}elseif($count_payopts == 1 && $paymentInfo['key'] == 'prebanktransfer' ){
						$chked = 'checked="checked"';
					}
					$disable_input = false;
					$payment_display_name = "";
					if(isset($_SESSION['custom_fields']['package_select']) && isset($_SESSION['custom_fields']['total_price']) )
					$listing_price_info = $monetization->templ_get_price_info($_SESSION['custom_fields']['package_select'],$_SESSION['custom_fields']['total_price']);
					$payment_display_name = $paymentInfo['name'];
				
					if(file_exists(get_tmpl_plugin_directory() . 'Tevolution-'.$paymentInfo['key'].'/includes/'.strtolower($paymentInfo['key']).'.php'))
					{
						include(get_tmpl_plugin_directory() . 'Tevolution-'.$paymentInfo['key'].'/includes/'.strtolower($paymentInfo['key']).'.php');
					}
					
					
					if(file_exists(TEMPL_PAYMENT_FOLDER_PATH.$paymentInfo['key'].'/'.$paymentInfo['key'].'.php'))
					{
					
						include_once(TEMPL_PAYMENT_FOLDER_PATH.$paymentInfo['key'].'/'.$paymentInfo['key'].'.php');
						
					} 				 
				}
			}
			echo '</div>';
		}

	}
	?>
	<script type="text/javascript" async >
	 /* <![CDATA[ */
	function showoptions(paymethod)
	{
		<?php for($i=0;$i<count($paymethodKeyarray);$i++){?>
		showoptvar = '<?php echo $paymethodKeyarray[$i]?>options';
		if(document.getElementById(showoptvar))
		{
			document.getElementById(showoptvar).style.display = 'none';
			if(paymethod=='<?php echo $paymethodKeyarray[$i]?>'){
				document.getElementById(showoptvar).style.display = '';
			}
		}
		<?php }?>
	}
	
	<?php for($i=0;$i<count($paymethodKeyarray);$i++){?>
		if(document.getElementById('<?php echo $paymethodKeyarray[$i];?>_id').checked)
		{
			showoptions(document.getElementById('<?php echo $paymethodKeyarray[$i];?>_id').value);
		}
	<?php }	?>
	/* ]]> */
	 </script>
	 <?php	
}
/*	fetch payment option values. */
function templatic_get_payment_options($method)
{
	global $wpdb;
	$paymentsql = "select * from $wpdb->options where option_name like 'payment_method_$method'";
	$paymentinfo = $wpdb->get_results($paymentsql);
	if($paymentinfo)
	{
		foreach($paymentinfo as $paymentinfoObj)
		{
			$option_value = unserialize($paymentinfoObj->option_value);
			$paymentOpts = $option_value['payOpts'];
			$optReturnarr = array();
			for($i=0;$i<count($paymentOpts);$i++)
			{
				$optReturnarr[$paymentOpts[$i]['fieldname']] = $paymentOpts[$i]['value'];
			}
			return $optReturnarr;
		}
	}
}
/*
	Return Response URL of payment method
*/
function payment_menthod_response_url($paymentmethod,$last_postid,$renew,$pid,$payable_amount,$trans_id='')
{
	global $current_user;	
	if(isset($_REQUEST['lang']) && $_REQUEST['lang']!="")
	{
		$language="&lang=".$_REQUEST['lang'];
	}
	if($pid>0 && $renew=='' && ($payable_amount <=0 || $payable_amount == '' ))
	{
		wp_redirect(get_author_posts_url($current_user->ID));
		exit;
	}else
	{
		$lang = '';
		/* Pass the wpml language slug in url */
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			if($sitepress->get_current_language()){
				
				if($sitepress->get_default_language() != $sitepress->get_current_language()){
					$lang = '/'.$sitepress->get_current_language();
				}else{
					$lang = '';
				}
			}
		}
		if($payable_amount == '' || $payable_amount <= 0)
		{
			$suburl .= "&pid=$last_postid";
			wp_redirect(get_option('siteurl').$lang."/?page=success$suburl");
			exit;
		}else
		{
			if(function_exists('curl_version')){
				
			}else{
				 echo "cURL is NOT <span style=\"color:red\">installed</span> on this server";die;
			}
			$paymentmethod = $paymentmethod;
			$paymentSuccessFlag = 0;
			if($paymentmethod == 'prebanktransfer' || $paymentmethod == 'payondelivery')
			{
				if($renew =='upgrade'){
					$suburl = "&upgrade=1";
				}elseif(($renew)){
					$suburl = "&renew=1";
				}
				if(isset($_REQUEST['action_edit']) && $_REQUEST['action_edit']){
					$suburl = "&action_edit=1";
				}
				$suburl .= "&pid=$last_postid";
				if($trans_id!=''){
					$suburl .= "&trans_id=$trans_id";
				}
				if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
					global $sitepress;
					if(isset($_REQUEST['lang'])){
						$url = apply_filters('tmpl_returnUrl',site_url().$lang.'/?page=success&paydeltype='.$paymentmethod.$suburl.$_REQUEST['lang']);
						
						
					}elseif($sitepress->get_current_language()){
							if($sitepress->get_default_language() != $sitepress->get_current_language()){
								$url = apply_filters('tmpl_returnUrl',site_url().'/'.$sitepress->get_current_language().'/?page=success&paydeltype='.$paymentmethod.$suburl);
							}else{
								$url = apply_filters('tmpl_returnUrl',site_url().$lang.'/?page=success&paydeltype='.$paymentmethod.$suburl);
							}
					}else{
						$url = apply_filters('tmpl_returnUrl',site_url().'/?page=success&paydeltype='.$paymentmethod.$suburl);
					}
				}else{
					$url = apply_filters('tmpl_returnUrl',site_url().'/?page=success&paydeltype='.$paymentmethod.$suburl);
				}
				echo '<script type="text/javascript">location.href="'.$url.'";</script>';
			}
			else
			{
				if(file_exists(TEMPL_PAYMENT_FOLDER_PATH.$paymentmethod.'/'.strtolower($paymentmethod).'_response.php') && $paymentmethod == 'paypal')
				{
					include_once(TEMPL_PAYMENT_FOLDER_PATH.$paymentmethod.'/'.strtolower($paymentmethod).'_response.php');
				}
				elseif(file_exists(get_tmpl_plugin_directory(). 'Tevolution-'.$paymentmethod.'/includes/'.strtolower($paymentmethod).'_response.php'))
				{
					include_once(get_tmpl_plugin_directory(). 'Tevolution-'.$paymentmethod.'/includes/'.strtolower($paymentmethod).'_response.php');
				}
			}	
		}
	}
}
/*set hidden variable if there is a single payment method i.e. either paypal or non-transferable.*/
function fetch_single_payment_url($post_type='',$post_id='')
{
	global $current_user,$wpdb;
	$paymentsql = "select * from $wpdb->options where option_name like 'payment_method_%' order by option_id";
	$paymentinfo = $wpdb->get_results($paymentsql);
	$i = 0;
	foreach($paymentinfo as $paymentinfoObj)
	{
		$paymentInfo = unserialize($paymentinfoObj->option_value);
		if($paymentInfo['isactive'])
		{
			$paymethodKeyarray[] = $paymentInfo['key'];
			$paymentOptionArray[$paymentInfo['display_order']][] = $paymentInfo;
			$i++;
		}
	}
	if($i == 1 )
	{
		echo '<input type="hidden" name="paymentmethod" id="paymentmethod" value="'.$paymethodKeyarray[0].'">';
	}
	
}

/*
	Payment options return page 
*/
add_action( 'init', 'return_page' );
function return_page()
{
	if(isset($_REQUEST['ptype']) && $_REQUEST['ptype'] == 'return'){
		if($_REQUEST['pmethod'] == 'paypal' && isset($_SESSION['pament_done'])){
			if( file_exists( TEMPL_PAYMENT_FOLDER_PATH . $_REQUEST['pmethod']."/return.php" ) ){
				include (TEMPL_PAYMENT_FOLDER_PATH . $_REQUEST['pmethod']."/return.php");
				unset($_SESSION['pament_done']);
				exit;
			}
		}
	}
	if(isset($_REQUEST['ptype']) && $_REQUEST['ptype'] == 'cancel'){
		if( file_exists( TEMPL_PAYMENT_FOLDER_PATH . $_REQUEST['pmethod']."/cancel.php" ) ){
			include (TEMPL_PAYMENT_FOLDER_PATH . $_REQUEST['pmethod']."/cancel.php");
			exit;
		}elseif( file_exists( TEMPL_MONETIZATION_PATH."templatic-payment_options/payment_cancel.php" ) ){
			include ( TEMPL_MONETIZATION_PATH."templatic-payment_options/payment_cancel.php" );
			exit;
		}
	}
	if(isset($_REQUEST['ptype']) && $_REQUEST['ptype'] == 'notifyurl'){
		if( file_exists( TEMPL_PAYMENT_FOLDER_PATH . $_REQUEST['pmethod']."/ipn_process.php" ) ){
			include (TEMPL_PAYMENT_FOLDER_PATH . $_REQUEST['pmethod']."/ipn_process.php");
			exit;
		}
	}
}
/*
 * display the paypal successful message display
 */
add_action('paypal_successfull_return_content','successfull_return_paypal_content',10,3);
function successfull_return_paypal_content($post_id,$subject,$content)
{
	global $current_user,$wpdb;
	$post_type = get_post_type($post_id);
	$package_id = get_post_meta($post_id,'package_select',true);
	$paymentmethod = get_post_meta($post_id,'paymentmethod',true);
	/* Get the payment method and paid amount */
	$transaction = $wpdb->prefix."transactions";
	$paymentmethod=($paymentmethod!='')?$paymentmethod:$_REQUEST['pmethod'];
	
	if($post_id==''){
		$paidamount_result = $wpdb->get_row("select payable_amt,package_id from $transaction t  order by t.trans_id DESC");
		$paidamount = $paidamount_result->payable_amt;
		$package_id = $paidamount_result->package_id;
	}
	
	if($paidamount !='')
		$paid_amount = display_amount_with_currency_plugin( number_format($paidamount, 2 ) );
		
	$paymentupdsql = "select option_value from $wpdb->options where option_name='payment_method_".$paymentmethod."'";
	$paymentupdinfo = $wpdb->get_results($paymentupdsql);
	$paymentInfo = unserialize($paymentupdinfo[0]->option_value);
	$payment_method_name = $paymentInfo['name'];
	echo "<h1 class='page-title'>".$subject."</h1>";
	echo '<div class="posted_successful">';
	do_action('after_successfull_return_paypal_process');
	tmpl_show_succes_page_info($current_user->ID,$post_type,$package_id,$payment_method_name);
	if(isset($_REQUEST['pid']) && $_REQUEST['pid']==''){
		$submit_form_package_url = '';
		$tevolution_post_type = tevolution_get_post_type();
		$submit_form_package_url='<ul>';
		$submit_form_package_url .= '<li class="sucess_msg_prop">'.'<a class="button" target="_blank" href="'.get_author_posts_url($current_user->ID).'">'.__('Your Profile',DOMAIN).'</a></li>';
		foreach($tevolution_post_type as $post_type)
		{
			if($post_type != 'admanager')
			{
				global $post,$wp_query;
					$args=
					array( 
					'post_type' => 'page',
					'posts_per_page' => -1	,
					'post_status' => array('publish'),
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key' => 'submit_post_type',
							'value' =>  $post_type,
							'compare' => 'LIKE'
							),
							array(
							'key' => 'is_tevolution_submit_form',
							'value' =>  1,
							'compare' => '='
							)
						)
					);
	
				$post_query = null;
				$post_query = new WP_Query($args);		
				$post_meta_info = $post_query;	
				if($post_meta_info->have_posts()){
					while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
						$submit_form_package_url .= "<li><a class='button' target='_blank' href='".get_the_permalink($post->ID)."'>".__('Submit',DOMAIN).' '.ucfirst($post_type)."</a></li>";
				  endwhile;wp_reset_query();wp_reset_postData();
				}
			}
		}
		$submit_form_package_url.='</ul>';
	}
	echo $filecontent .= $submit_form_package_url;
	echo "<p>".$content."</p>";
	echo '</div>';
}

/*
	Return Response url of payment method
*/
function payment_upgrade_response_url($paymentmethod,$last_postid,$renew,$pid,$payable_amount)
{
	global $current_user;	
	if(isset($_REQUEST['lang']) && $_REQUEST['lang']!="")
	{
		$language="&lang=".$_REQUEST['lang'];
	}
	if($pid>0 && $renew=='')
	{
		wp_redirect(get_author_posts_url($current_user->ID));
		exit;
	}else
	{
		if($payable_amount == '' || $payable_amount <= 0)
		{
			$suburl .= "&pid=$last_postid";
			wp_redirect(get_option('siteurl')."/?page=success$suburl");
			exit;
		}else
		{
			$paymentmethod = $paymentmethod;
			$paymentSuccessFlag = 0;
			if($paymentmethod == 'prebanktransfer' || $paymentmethod == 'payondelivery')
			{
	
				$suburl = "&upgrade=1";
				
				$suburl .= "&pid=$last_postid";
				if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
					global $sitepress;
					if(isset($_REQUEST['lang'])){
						$url = site_url().'/?page=success&paydeltype='.$paymentmethod.$suburl.$_REQUEST['lang'];
						
						
					}elseif($sitepress->get_current_language()){
						
						if($sitepress->get_default_language() != $sitepress->get_current_language()){
							$url = site_url().'/'.$sitepress->get_current_language().'/?page=success&paydeltype='.$paymentmethod.$suburl;
						}else{
							$url = site_url().'/?page=success&paydeltype='.$paymentmethod.$suburl;
						}
					}else{
						$url = site_url().'/?page=success&paydeltype='.$paymentmethod.$suburl;
					}
				}else{
					$url = site_url().'/?page=success&paydeltype='.$paymentmethod.$suburl;
				}
				echo '<script type="text/javascript">location.href="'.$url.'";</script>';
			}
			else
			{
				if(file_exists(TEMPL_PAYMENT_FOLDER_PATH.$paymentmethod.'/'.$paymentmethod.'_response.php') && $paymentmethod == 'paypal')
				{
					include_once(TEMPL_PAYMENT_FOLDER_PATH.$paymentmethod.'/'.strtolower($paymentmethod).'_response.php');
				}
				elseif(file_exists(get_tmpl_plugin_directory(). 'Tevolution-'.$paymentmethod.'/includes/'.strtolower($paymentmethod).'_response.php'))
				{
					include_once(get_tmpl_plugin_directory(). 'Tevolution-'.$paymentmethod.'/includes/'.strtolower($paymentmethod).'_response.php');
				}
			}	
		}
	}
}
?>