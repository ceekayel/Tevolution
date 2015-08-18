function check_frm()
{
	jQuery.noConflict();
	var package_type = jQuery("input[name='package_type']:checked").length;
	var package_name = jQuery('#package_name').val();
	var package_amount = jQuery('#package_amount').val();
	var billing_period = jQuery('#validity').val();
	var pkg_selected = jQuery("input[name='package_type']:checked").val();
	var limit_no_post = jQuery('#limit_no_post').val();
	if( package_type == 0 || package_name == "" || package_amount == "" || billing_period == "" || (pkg_selected == 2 && limit_no_post == '' ))
	{ 
		if(package_type==0){
			jQuery('#package_type').addClass('form-invalid');
			jQuery('#package_type').change(on_change_package_type);
		}
		if(package_name ==""){
			jQuery('#package_title').addClass('form-invalid');
			jQuery('#package_title').change(on_change_title);
		}
		if(package_amount == ''){
			jQuery('#package_price').addClass('form-invalid');
			jQuery('#package_price').change(on_change_amount);
		}
		if(!billing_period){
			jQuery('#billing_period').addClass('form-invalid');
			jQuery('#billing_period').change(on_change_period);
			
		}
		if(pkg_selected == 2){
			jQuery('#number_of_post').show();
			jQuery('#number_of_post').addClass('form-invalid');
			jQuery('#limit_no_post').change(limit_no_post);
			
		}
		return false;
	} 
	function limit_no_post()
	{
		var limit_no_post = jQuery('#limit_no_post').val();
		if(limit_no_post=="")
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
	}else
	{
		document.getElementById('number_of_post').style.display='none';
		document.getElementById('pay_per_sub_desc').style.display='none';
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
	if(document.getElementById("is_featured").checked)
	{
		jQuery('#featured_home').slideDown('slow');
		jQuery('#featured_cat').slideDown('slow');
	}
	else if(!document.getElementById("is_featured").checked)
	{
		jQuery('#featured_home').slideUp('fast');
		jQuery('#featured_cat').slideUp('fast');
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
