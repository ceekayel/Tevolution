<script type="text/javascript" async >
/*
Name :show_featuredprice
Description : Return the total prices and add the calculation in span.
*/
<?php if(!isset($_REQUEST['action']) && $_REQUEST['action'] !='edit'){ ?>
	window.onload=function(){ if(document.getElementById('price_package_price_list')){document.getElementById('price_package_price_list').style.display =='none'; } };
<?php }  
if(is_admin())
{
?>
	jQuery(document).ready(function(){
		jQuery('.select-plan').live('click',function(event){
			event.preventDefault();
			finishStep = [];
			var $target = jQuery(event.currentTarget),
			$ul = $target.closest('ul'),
			$ula = $target.closest('ul a'),
			amount = $ul.attr('data-price'),
			pkg_type =  $ul.attr('data-type'),
			package_free_submission =  $ul.attr('data-free'),
			pkg_subscribed =  $ul.attr('data-subscribed'),
			$step = $ul.closest('div.step'),
			view = this;
			package_id = $ul.attr("data-id");
			upgrade = $ula.attr("data-upgrade");	
			jQuery('#pkg_id').val(package_id);
			jQuery('#pkg_type').val(pkg_type);
			jQuery('#package_free_submission').val(package_free_submission);
			jQuery('#upgrade').val(upgrade);
			jQuery('#total_price').val(amount);
			if($ul.attr("data-post"))
			{
				pkg_post = $ul.attr("data-post");
			}
			currentStep = 'plan';
			jQuery('#step-post').css('display','block');
			if (parseInt(jQuery('#step-auth').length) === 0)
			{
				jQuery('#select_payment').html('3');
			}
			jQuery("#plan").find("ul.selected").removeClass('selected');
			/*added class to highlight the selected package*/
			$target.parents('ul').addClass('selected');
			/*fetch category as per selected price package*/
			jQuery('#submit_category_box').css('opacity','0.5');
			jQuery('#submit_category_box').addClass('overlay_opacity');
			var submit_from = jQuery('form#post').serialize();
			/*fetch selected price package featured option*/
			jQuery.ajax({
				url:ajaxUrl,
				type:'POST',
				async: true,
				data:'pkg_subscribed='+pkg_subscribed+'&' + submit_from+'&action=tmpl_tevolution_submit_from_package_featured_option',
				success:function(results){
					if(jQuery('#package_free_submission').val() <= 0 || jQuery('#package_free_submission').val() == '' || upgrade == 'upgrade' )
					{
						jQuery('#show_featured_option').css('display','block');
						jQuery('div#show_featured_option').html(results);
						if(parseFloat(jQuery('#total_price').val()) >0)
						{
							jQuery('#submit_coupon_code').css('display','block');
							jQuery('#price_package_price_list').css('display','block');
						}
						else
						{
							jQuery('#submit_coupon_code').css('display','none');
							jQuery('#price_package_price_list').css('display','none');
						}
					}
				},
			});
		});
	});
	<?php
}
?>
</script>
<?php
add_action('wp_footer','tmpl_select_price_pkg',9);
function tmpl_select_price_pkg()
{
	?>
    	<script>
			var pkg_post = '';
			jQuery(document).ready(function(){
				function addLoginFinishStep(step) {
					if (typeof finishStep === 'undefined') {
						finishStep = [];
					}
					finishStep.push(step);
				}
				function showNextLoginStep()
				{
					var next = 'post',
					view = this;
					jQuery('.step-wrapper').removeClass('current');
					jQuery('.content').slideUp(500, function() {
						/* current step is plan*/
						if (currentStep === 'plan') {
							if((jQuery('#pkg_type').val() == 1 || pkg_post == 1 || jQuery('#post_upgrade').val() == 'post_upgrade') && jQuery('#upgrade').val() != 'upgrade' )
							{
								next = 'post';
							}
							else if((jQuery('#pkg_type').val() == 2 && jQuery('#package_free_submission').val() == '' ) || ( jQuery('#pkg_type').val() == 2 && jQuery('#upgrade').val() == 'upgrade') )
							{
								jQuery('#step-post').css('display','none');
								if (parseInt(jQuery('#step-auth').length) === 0)
								{
									jQuery('#select_payment').html('2');
									user_login = true;
								}
								else
								{
									jQuery('#span_user_login').html('2');
									jQuery('#select_payment').html('3');
									user_login = false;
								}
								if (user_login) {
									next = 'payment';
								}
								else
								{
									next = 'auth';
								}
							}
							else if((jQuery('#pkg_type').val() == 2 && jQuery('#package_free_submission').val() != '')  || ( jQuery('#pkg_type').val() == 2 && jQuery('#upgrade').val() != 'upgrade'))
							{
								next = 'post';
								jQuery('#show_featured_option').css('display','none');
								jQuery('#submit_coupon_code').css('display','none');
							}
						}
						
						/*show payment tab if total price is greater tha zero*/
						if(jQuery('#total_price').val() > 0)
						{
							jQuery('#step-payment').css('display','block');
						}
						else
						{
							jQuery('#step-payment').css('display','none');
						}
						/* show next step*/
						jQuery('.step-' + next + '  .content').slideDown(10).end();
						jQuery('.step-' + next).addClass('current');
					});
				}
				jQuery(window).load( function( response, status ){
				<?php
					if($_SESSION['custom_fields']['pkg_id'] || (isset($_REQUEST['backandedit']) && $_REQUEST['backandedit'] == 1))
					{
						?>
							var pkg_id =  <?php echo ($_SESSION['custom_fields']['pkg_id'])?$_SESSION['custom_fields']['pkg_id']:$_REQUEST['pkg_id']; ?>;
						
							$ul = jQuery("ul").find("[data-id='" + pkg_id + "']"),
							amount = $ul.attr('data-price'),
							pkg_type =  $ul.attr('data-type'),
							$step = $ul.closest('div.step'),
							view = this;
							package_id = $ul.attr("data-id");
							
							currentStep = 'plan';
							/*added class to highlight the selected package*/
							$ul.closest('ul').addClass('selected');
					
							pkg_type =  $ul.closest('ul').attr('data-type');
							upgrade =  $ul.closest('ul a').attr('data-upgrade');
							if($ul.closest('ul').attr('data-free'))
							{
								package_free_submission =  $ul.closest('ul').attr('data-free');
							}else{
								package_free_submission = '';
							}
							jQuery('#pkg_id').val(package_id);
							jQuery('#pkg_type').val(pkg_type);
							jQuery('#package_free_submission').val(package_free_submission);
							jQuery('#upgrade').val(upgrade);
							if($ul.closest('ul').attr('data-post'))
							{
								pkg_post = $ul.closest('ul').attr('data-post');
							}
							jQuery('#total_price').val($ul.closest('ul').attr('data-price'));
							if(parseFloat(jQuery('#total_price').val()) >0)
							{
								jQuery('#submit_coupon_code').css('display','block');
								jQuery('#price_package_price_list').css('display','block');
								
							}
							else
							{
								jQuery('#submit_coupon_code').css('display','none');
								jQuery('#price_package_price_list').css('display','block');
							}
							/*fetch category as per selected price package*/
							jQuery('#submit_category_box').css('opacity','0.5');
							jQuery('#submit_category_box').addClass('overlay_opacity');
							
							/*to show category selected*/
							jQuery('#submit_category_box').css('opacity','');
							jQuery('#submit_category_box').removeClass('overlay_opacity');
							
							/*to show price package featured option*/
							if(jQuery('#package_free_submission').val() <= 0 || jQuery('#package_free_submission').val() == '' || upgrade == 'upgrade' )
							{
								jQuery('#show_featured_option').css('display','block');
								jQuery('div#show_featured_option').html(response);
								if(parseFloat(jQuery('#total_price').val()) >0)
								{
									jQuery('#submit_coupon_code').css('display','block');
									jQuery('#price_package_price_list').css('display','block');
								}
								else
								{
									jQuery('#submit_coupon_code').css('display','none');
									jQuery('#price_package_price_list').css('display','none');
								}
							}
							
							/* hide all content step*/
							$ul.closest('div.step-wrapper').addClass('complete');
							/* add step plan to finish array*/
							addLoginFinishStep('step-plan');
							/* show next step*/
							showNextLoginStep();		
						
						
						var submit_from = jQuery('form#submit_form').serialize();
						jQuery.ajax({
							url:ajaxUrl,
							type:'POST',
							async: true,
							data:'action=tmpl_tevolution_select_pay_per_subscription_price_package&' + submit_from,
							success:function(results){
								if(Math.floor(results) == results && jQuery.isNumeric(results)) 
								{
									jQuery('.select-plan').trigger('click');
								}
							},
						});
						<?php
						if(($_SESSION['custom_fields']['featured_h']!='' && $_SESSION['custom_fields']['featured_c']!='') || (get_post_meta($_REQUEST['pid'],'featured_h',true)!= '' && get_post_meta($_REQUEST['pid'],'featured_c',true)!= '' ))
						{
							?>
							jQuery('#featured_h').trigger('change');
							<?php
						}elseif(!isset($_SESSION['custom_fields']['featured_h'])&& $_SESSION['custom_fields']['featured_c']!='' || (get_post_meta($_REQUEST['pid'],'featured_h',true) == 'h' && get_post_meta($_REQUEST['pid'],'featured_c',true)!= '' )){
							?>
							jQuery('#featured_c').trigger('change');
							<?php
						}elseif($_SESSION['custom_fields']['featured_h']!='' && !isset($_SESSION['custom_fields']['featured_c']) || (get_post_meta($_REQUEST['pid'],'featured_h',true)!='' && get_post_meta($_REQUEST['pid'],'featured_c',true) == 'c' ) ){
							?>
							jQuery('#featured_h').trigger('change');
							<?php
						}
						
						$cat_price=0;
						$num_decimals    = absint( get_option( 'tmpl_price_num_decimals' ) );
						$decimal_sep     = wp_specialchars_decode( stripslashes( get_option( 'tmpl_price_decimal_sep' ) ), ENT_QUOTES );
						$thousands_sep   = wp_specialchars_decode( stripslashes( get_option( 'tmpl_price_thousand_sep' ) ), ENT_QUOTES );
						$currency = get_option('currency_symbol');
						$position = get_option('currency_pos');
						if((isset($_SESSION['custom_fields']) && $_SESSION['custom_fields']['category']!='') || (isset($_SESSION['custom_fields']) && $_SESSION['custom_fields']['all_cat_price']!='')){
							
							foreach($_SESSION['custom_fields']['category'] as $category){
								$category_price = explode(',',$category);
								$cat_price+=$category_price[1];	
							}
							if(@$_SESSION['custom_fields']['all_cat_price']!='')
							{
								$cat_price = $_SESSION['custom_fields']['all_cat_price'];
							}
							$pkg_id = ($_SESSION['custom_fields']['pkg_id'])?$_SESSION['custom_fields']['pkg_id']:$_REQUEST['pkg_id'];
							$package_price=get_post_meta($pkg_id,'package_amount',true);
							$featured_c=$featured_h=0;
							if(isset($_SESSION['custom_fields']['featured_h'])){
								$featured_h=$_SESSION['custom_fields']['featured_h'];
							}
							if(isset($_SESSION['custom_fields']['featured_c'])){
								$featured_c=$_SESSION['custom_fields']['featured_c'];
							}
							if(get_post_meta($_REQUEST['pkg_id'],'featured_h',true) == 'h'){
								$featured_h=$_REQUEST['pkg_id']['featured_h'];
							}
							if(get_post_meta($_REQUEST['pkg_id'],'featured_c',true) == 'c'){
								$featured_c=$_REQUEST['pkg_id']['featured_c'];
							}
							$total_price=$cat_price+$package_price+$featured_h+$featured_c;
						}
						?>
						
						jQuery('#result_price').html('<?php echo apply_filters( 'formatted_tmpl_price', number_format( $total_price, $num_decimals, $decimal_sep, $thousands_sep ), $amount, $num_decimals, $decimal_sep, $thousands_sep )?>');
						jQuery('#total_price').val('<?php echo apply_filters( 'formatted_tmpl_price', number_format( $total_price, $num_decimals, $decimal_sep, $thousands_sep ), $amount, $num_decimals, $decimal_sep, $thousands_sep )?>');
						/*jQuery(".category_label input[name^='category'], .category_label input[name='selectall'],.category_label select[name^='category']").trigger('click');*/
						return true;
						<?php
					}
				?>
				});
			});
		</script>
    <?php
}
?>