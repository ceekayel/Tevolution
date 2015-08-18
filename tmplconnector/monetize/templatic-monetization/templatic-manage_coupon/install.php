<?php
global $wp_query,$wpdb,$wp_rewrite;
/**-- conditions for activation of login wizard --**/
if(@$_REQUEST['activated'] == 'templatic_manage_coupon' && @$_REQUEST['true']==1){
		update_option('templatic_manage_coupon','Active');
		
}else if(@$_REQUEST['deactivate'] == 'templatic_manage_coupon' && @$_REQUEST['true']==0){
		delete_option('templatic_manage_coupon');		
}
/**-- coding to add submenu under main menu--**/
	/**-- Function to insert file for add/edit/delete options for custom fields BOF --**/
	function manage_coupon_function(){
		if($_REQUEST['page'] == 'monetization' && $_GET['tab']== 'manage_coupon' && @$_GET['action'] != 'addnew' ){
			include (TEMPL_MONETIZATION_PATH . "templatic-manage_coupon/admin_coupon_list.php");
		}else if(isset($_GET['action']) && $_GET['action']=='addnew' ){
			include (TEMPL_MONETIZATION_PATH . "templatic-manage_coupon/admin_coupon_add.php");
		}
	}
define('DISCOUNT_TYPE_TITLE',__('Discount Type',DOMAIN));
define('COUPON_CODE_TITLE',__('Coupon&nbsp;Code',DOMAIN));
define('DISCOUNT_AMOUNT_TITLE',__('Discount Amount',DOMAIN));
define('TEMPL_AJAX_CHK_COUPON_URL', plugin_dir_url( __FILE__ ));
/*
Name :templa_script_validatecoupon
description : validate coupon code field on submit form 
*/
add_action('wp_head','templa_script_validatecoupon',12);
function templa_script_validatecoupon(){
	global $wp_query,$post;
	// If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object	
	if(isset($post))
		$is_tevolution_submit_form = get_post_meta( $post->ID, 'is_tevolution_submit_form', TRUE );
	if(isset($post))
		$is_tevolution_upgrade_form = get_post_meta($post->ID,'is_tevolution_upgrade_form',true);	
	if(is_page() && ($is_tevolution_submit_form==1 || $is_tevolution_upgrade_form == 1)){
?>
	<!-- Validate coupon code -->
	<script type="text/javascript">
	jQuery.noConflict();
	var xmlHTTP;
	function GetXmlHttpObject()
	{
		xmlHTTP=null;
		try
		{
			xmlhttp=new XMLHttpRequest();
		}
		catch (e)
		{
			try
			{
				xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");			
			}
			catch (e)
			{
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
		}
	}
	function checkCoupon()
	{
		if (window.XMLHttpRequest)
		{
			xmlhttp=new XMLHttpRequest();
		}
		else
		{
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		if(xmlhttp == null)
		{
			alert("Your browser not support the AJAX");	
			return;
		}
		if(document.getElementById("add_coupon"))
			add_coupon = document.getElementById("add_coupon").value;
		if(document.getElementById("total_price"))
			total_price = document.getElementById("total_price").value;
			
		<?php
		$language='';
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$language="&language=".$current_lang_code;
		}
		?>
		var url = "<?php echo TEMPL_PLUGIN_URL; ?>tmplconnector/monetize/templatic-monetization/templatic-manage_coupon/ajax_check_coupon_code.php?add_coupon="+add_coupon+'&total_price='+total_price+"<?php echo $language;?>";
		xmlhttp.open("GET",url,true);
		xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xmlhttp.send(null);
		xmlhttp.onreadystatechange=function()
		{	
			if(xmlhttp.readyState==4 && xmlhttp.status==200)
			{
				if(xmlhttp.responseText)
				{
					document.getElementById("msg_coudon_code").innerHTML = xmlhttp.responseText;
					jQuery("#msg_coudon_code").removeClass('message_error2');
					jQuery("#msg_coudon_code").css('color','green');
					jQuery("#msg_coudon_code").css('clear','both');
				}
				else
				{
					document.getElementById("msg_coudon_code").innerHTML = '<?php _e('Sorry! coupon code does not exist.Please try another coupon code.',DOMAIN); ?>';
					jQuery("#msg_coudon_code").css('color','');
					jQuery("#msg_coudon_code").css('clear','');
					jQuery("#msg_coudon_code").addClass('message_error2');
				}
			}
		}
		return true;
	}
	</script>
<?php }
} 
/*
Name :templ_get_coupon_fields
Args :coupon_code
description : fetching coupon code field in submit form 
*/
function templ_get_coupon_fields($coupon_code){ ?>
	 <div id="submit_coupon_code" style="display:none;" class="form_row clearfix">
              	
             	<label><?php  _e('Coupon Code',DOMAIN);?> </label>
				<input type="text" name="add_coupon" id="add_coupon" class="textfield" value="<?php echo esc_attr(stripslashes($coupon_code)); ?>" />
				&nbsp;
				<input class="validate_btn" type="button" name="validate_coupon_code" id="validate_coupon_code" value="<?php _e('Validate',DOMAIN);?>" onclick="return checkCoupon();"  />
				<div id="msg_coudon_code"></div>
				<span class="message_note"><?php _e('Enter coupon code here (optional)',DOMAIN); ?></span>				
      </div>
<?php } ?>