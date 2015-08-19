/*
 * validation while adding price package from backend
 */
function check_frm()
{
	jQuery.noConflict();
	var package_type = jQuery("input[name='package_type']:checked").length;
	var package_name = jQuery('#package_name').val();
	var package_amount = jQuery('#package_amount').val();
	var billing_period = jQuery('#validity').val();
	var pkg_selected = jQuery("input[name='package_type']:checked").val();
	var limit_no_post = jQuery('#limit_no_post').val();
	var days_for_no_post = jQuery('#days_for_no_post').val();
	var is_home_page_featured = jQuery('#is_home_page_featured').val();
	var is_category_page_featured = jQuery('#is_category_page_featured').val();
	var is_home_page_featured = jQuery('#is_home_page_featured').attr("checked");
	var is_category_page_featured = jQuery('#is_category_page_featured').attr("checked");
	var home_page_tmp = 'true';
	var category_page_tmp = 'true';
	var package_type_tmp = 'true';
	var package_title_tmp = 'true';
	var package_amount_tmp = 'true';
	var billing_period_tmp = 'true';
	var pkg_selected_tmp = 'true';
	var flag = 1;
	if( package_type == 0 || package_name == "" || package_amount == "" || billing_period == "" || (pkg_selected == 2 && limit_no_post == '') || (pkg_selected == 2 && days_for_no_post == '') || (pkg_selected == 2 && package_amount == 0) || is_home_page_featured== 'checked' || is_category_page_featured == 'checked')
	{ 
		if(package_type==0){
			jQuery('#package_type').addClass('form-invalid');
			jQuery('#package_type').change(on_change_package_type);
			package_type_tmp = 'false';
			flag = 0;
		}
		else
		{
			package_type_tmp = 'true';
		}
		if(package_name ==""){
			jQuery('#package_title').addClass('form-invalid');
			jQuery('#package_title').change(on_change_title);
			package_title_tmp = 'false';
			flag = 0;
		}
		else
		{
			package_title_tmp = 'true';
		}
		if(package_amount == '' || (pkg_selected == 2 && package_amount == 0)){
			jQuery('#package_price').addClass('form-invalid');
			jQuery('#package_price').change(on_change_amount);
			package_amount_tmp = 'false';
			flag = 0;
		}
		else
		{
			package_amount_tmp = 'true';
		}
		if(!billing_period){
			jQuery('#billing_period').addClass('form-invalid');
			jQuery('#billing_period').change(on_change_period);
			billing_period_tmp = 'false';
			flag = 0;
		}
		else
		{
			billing_period_tmp = 'true';
		}
		if(pkg_selected == 2 && (limit_no_post == '' || limit_no_post == 0 || days_for_no_post == '' || days_for_no_post == 0)){
                                                                           if(pkg_selected == 2 && (limit_no_post == '' || limit_no_post == 0)){
                                                                                jQuery('#number_of_post').show();
                                                                                jQuery('#number_of_post').addClass('form-invalid');
                                                                                jQuery('#limit_no_post').change(package_limit_no_post);
                                                                           }
                                                                           if(pkg_selected == 2 && (days_for_no_post == '' || days_for_no_post == 0)){
                                                                                jQuery('#number_of_post_days').show();
                                                                                jQuery('#number_of_post_days').addClass('form-invalid');
                                                                                jQuery('#days_for_no_post').change(package_days_for_no_post);
                                                                           }
			pkg_selected_tmp = 'false';
			flag = 0;
                                                  }else{
			pkg_selected_tmp = 'true';
		}
		if(is_home_page_featured == 'checked')
		{
			var is_home_featured = jQuery('#is_home_featured').attr("checked");
			var feature_amount = jQuery('#feature_amount').val();
			if(is_home_featured != 'checked' && ( feature_amount <= 0 || feature_amount == ''))
			{
				jQuery('#home_page_featured_price').addClass('form-invalid');
				jQuery('#home_page_featured_price').change(on_change_is_home_page_featured);
				jQuery('#featured_home').addClass('form-invalid');
				jQuery('#featured_home').change(on_change_is_home_page_featured);
				home_page_tmp = 'false';
				flag = 0;
			}else{
				home_page_tmp = 'true';
			}
			
		}
		if(is_category_page_featured == 'checked')
		{
			var is_category_featured = jQuery('#is_category_featured').attr("checked");
			var feature_cat_amount = jQuery('#feature_cat_amount').val();
			if(is_category_featured != 'checked' && ( feature_cat_amount <= 0 || feature_cat_amount == '' ))
			{
				jQuery('#category_page_featured_price').addClass('form-invalid');
				jQuery('#category_page_featured_price').change(on_change_is_category_page_featured);
				jQuery('#featured_cat').addClass('form-invalid');
				jQuery('#featured_cat').change(on_change_is_category_page_featured);
				category_page_tmp = 'false';
				flag = 0;
			}else{
				category_page_tmp = 'true';
			}
		}
		
	}
	if(pkg_selected == 2 && (parseInt(jQuery('#limit_no_post').val()) < parseInt(jQuery('#subscription_days_free_trail').val())))
	{
		jQuery('#tr_subscription_days_free_trail').addClass('form-invalid');
		jQuery('#error_subscription_days_free_trail_message').show();
		flag = 0;
	}
	else if(pkg_selected == 2 && jQuery('#limit_no_post').val() >= jQuery('#subscription_days_free_trail').val())
	{
		jQuery('#tr_subscription_days_free_trail').removeClass('form-invalid');
		jQuery('#error_subscription_days_free_trail_message').hide();
		
	}
	if(flag == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
	var total_days = 0;
	if(jQuery('#validity').val()>0)
	{
		if(jQuery('#validity_per').val() == 'D')
		{
			total_days = jQuery('#validity').val();
		}
		else if(jQuery('#validity_per').val() == 'M')
		{
			total_days = jQuery('#validity').val() * 30;
		}
		else if(jQuery('#validity_per').val() == 'Y')
		{
			total_days = jQuery('#validity').val() * 365;
		}
	}
//	if(jQuery('#days_for_no_post').val() > total_days && jQuer('#pay_per_sub'))
	function on_change_is_home_page_featured()
	{
		var feature_amount = jQuery('#feature_amount').val();
		var is_home_featured = jQuery('#is_home_featured').attr("checked");
		if(is_home_featured != 'checked' && (feature_amount=="" || feature_amount<=0))
		{
			jQuery('#featured_home').addClass('form-invalid');
			jQuery('#home_page_featured_price').addClass('form-invalid');
			return false;
		}
		else if(feature_amount!="")
		{
			jQuery('#featured_home').removeClass('form-invalid');
			jQuery('#home_page_featured_price').removeClass('form-invalid');
			return true;
		}
		else if(is_home_featured == 'checked')
		{
			jQuery('#featured_home').removeClass('form-invalid');
			jQuery('#home_page_featured_price').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_is_category_page_featured()
	{
		var feature_cat_amount = jQuery('#feature_cat_amount').val();
		var is_category_featured = jQuery('#is_category_featured').attr("checked");
		if(is_category_featured != 'checked' &&  (feature_cat_amount=="" || feature_cat_amount<=0))
		{
			jQuery('#featured_cat').addClass('form-invalid');
			jQuery('#category_page_featured_price').addClass('form-invalid');
			return false;
		}
		else if(feature_cat_amount!="")
		{
			jQuery('#featured_cat').removeClass('form-invalid');
			jQuery('#category_page_featured_price').removeClass('form-invalid');
			return true;
		}
		else if(is_category_featured == 'checked')
		{
			jQuery('#featured_cat').removeClass('form-invalid');
			jQuery('#category_page_featured_price').removeClass('form-invalid');
			return true;
		}
	}
	function package_limit_no_post()
	{
		var limit_no_post = jQuery('#limit_no_post').val();
		if(limit_no_post=="" || limit_no_post==0)
		{
			jQuery('#number_of_post').addClass('form-invalid');
			return false;
		}
		else if(limit_no_post!="")
		{
			jQuery('#number_of_post').removeClass('form-invalid');
			return true;
		}
	}
	function package_days_for_no_post()
	{
		var days_for_no_post = jQuery('#days_for_no_post').val();
		if(days_for_no_post=="")
		{
			jQuery('#number_of_post_days').addClass('form-invalid');
			return false;
		}
		else if(days_for_no_post!="")
		{
			jQuery('#number_of_post_days').removeClass('form-invalid');
			return true;
		}
	}
	
	if(pkg_selected == 2 && limit_no_post != '' )
	{
		var limit_no_post = jQuery('#limit_no_post').val();
		if(limit_no_post=="")
		{
			jQuery('#number_of_post').addClass('form-invalid');
			return false;
		}
		else if(limit_no_post!="")
		{ 
			var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
			if(!numericReg.test(limit_no_post)) {
				jQuery('#number_of_post').addClass('form-invalid');
				return false;
			}
			else
			{
				jQuery('#number_of_post').removeClass('form-invalid');
				return true;
			}
		}
	}
	if(pkg_selected == 2 && days_for_no_post != '' )
	{
		var days_for_no_post = jQuery('#days_for_no_post').val();
		if(days_for_no_post=="")
		{
			jQuery('#number_of_post_days').addClass('form-invalid');
			return false;
		}
		else if(days_for_no_post!="")
		{ 
			var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
			if(!numericReg.test(days_for_no_post)) {
				jQuery('#number_of_post_days').addClass('form-invalid');
				return false;
			}
			else
			{
				jQuery('#number_of_post_days').removeClass('form-invalid');
				return true;
			}
		}
	}
	function on_change_title()
	{
		var package_name = jQuery('#package_name').val();
		if(package_name=="")
		{
			jQuery('#package_title').addClass('form-invalid');
			return false;
		}
		else if(package_name!="")
		{
			jQuery('#package_title').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_package_type()
	{
		var package_type = jQuery("input[name='package_type']:checked").length;
		if( package_type == 0 )
		{
			jQuery('#package_type').addClass('form-invalid');
			return false;
		}
		else if( package_type > 0 )
		{
			jQuery('#package_type').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_amount()
	{
		var package_amount = jQuery('#package_amount').val();
		if( package_amount == '' )
		{
			jQuery('#package_price').addClass('form-invalid');
			return false;
		}
		else if( package_amount != '' )
		{
			jQuery('#package_price').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_period()
	{
		var billing_period = jQuery('#validity').val();
		var billing_period_val = jQuery('#validity_per').val();
		if( billing_period == '' && billing_period_val == '')
		{
			jQuery('#billing_period').addClass('form-invalid');
			return false;
		}
		else if( billing_period != '' && billing_period_val != '')
		{
			jQuery('#billing_period').removeClass('form-invalid');
			return true;
		}
	}
}
function displaychk_frm(){
	dml = document.forms['monetization'];
	chk = dml.elements['category[]'];
	len = dml.elements['category[]'].length;
	
	if(document.getElementById('selectall').checked == true) { 
		for (i = 0; i < len; i++)
		chk[i].checked = true ;
	} else { 
		for (i = 0; i < len; i++)
		chk[i].checked = false ;
	}
}
function showlistpost(id)
{
	var val=id.value;	
	if(val==2)
	{
		document.getElementById('number_of_post').style.display='';
		document.getElementById('pay_per_sub_desc').style.display='block';
		document.getElementById('pay_per_post_desc').style.display='none';
		document.getElementById('subscription_as_pay_per_post').style.display='';
		document.getElementById('tr_subscription_days_free_trail').style.display='';
		document.getElementById('number_of_post_days').style.display='';
	}else
	{
		document.getElementById('number_of_post').style.display='none';
		document.getElementById('pay_per_sub_desc').style.display='none';
		document.getElementById('subscription_as_pay_per_post').style.display='none';
		document.getElementById('tr_subscription_days_free_trail').style.display='none';
		document.getElementById('number_of_post_days').style.display='none';
		document.getElementById('pay_per_post_desc').style.display='block';
	}
}
function rec_div_show(str)
{ 
	if(jQuery('#'+str).attr('checked')) {
		jQuery('#rec_tr').fadeIn('slow');
		jQuery('#rec_tr1').fadeIn('slow');
		jQuery('#rec_tr2').fadeIn('slow');
	}else{
		jQuery('#rec_tr').fadeOut('fast');
		jQuery('#rec_tr1').fadeOut('fast');
		jQuery('#rec_tr2').fadeOut('fast');
	}
	var recuring = jQuery('#'+str).attr('checked');
	if(recuring) {
		document.getElementById('billing_period').style.display="none";
		document.getElementById('billing_period').style.height="0px";
	}else{
		document.getElementById('billing_period').style.display="";
	}
}
function show_featured_package(str)
{
	if(str == 'is_home_page_featured' || str == 'is_category_page_featured' )
	{
		var val = jQuery('#is_home_page_featured').attr("checked");
		var is_home_featured = jQuery('#is_home_featured').attr("checked");
		var is_category_featured = jQuery('#is_category_featured').attr("checked");
		if(val == 'checked')
		{
			jQuery('#featured_home').slideDown('slow');
			if(is_home_featured != 'checked')
				jQuery('#home_page_featured_price').slideDown('slow');
			jQuery('#home_page_featured_alive_days').slideDown('slow');
		}
		else
		{
			jQuery('#featured_home').slideUp('fast');
			jQuery('#is_home_featured').prop('checked', false);
			jQuery('#home_page_featured_price').slideUp('fast');
			jQuery('#home_page_featured_alive_days').slideUp('fast');
		}
		var val = jQuery('#is_category_page_featured').attr("checked");
		if(val == 'checked')
		{
			jQuery('#featured_cat').slideDown('slow');
			if(is_category_featured != 'checked')
				jQuery('#category_page_featured_price').slideDown('slow');
			jQuery('#category_page_featured_alive_days').slideDown('slow');
		}
		else
		{
			jQuery('#featured_cat').slideUp('fast');
			jQuery('#is_category_featured').prop('checked', false);
			jQuery('#category_page_featured_price').slideUp('fast');
			jQuery('#category_page_featured_alive_days').slideUp('fast');
		}
	}
	else if(str == 'is_home_featured')
	{
		jQuery('#home_page_featured_price').toggle();
	}
	else if(str == 'is_category_featured')
	{
		jQuery('#category_page_featured_price').toggle();
	}
}
function show_comment_package(str)
{
	if(document.getElementById("can_author_mederate").checked)
	{
		jQuery('#comment_moderation_charge').slideDown('slow');
	}
	else if(!document.getElementById("can_author_mederate").checked)
	{
		jQuery('#comment_moderation_charge').slideUp('fast');
	}
}
