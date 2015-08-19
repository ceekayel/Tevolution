/*
 * validation while adding user custom field
 */
function chk_userfield_form()
{
	jQuery.noConflict();
	
	var sort_order 		= jQuery('#sort_order').val();
	var htmlvar_name 	= jQuery('#htmlvar_name').val();
	var site_title 		= jQuery('#site_title').val();
	
	if( sort_order == "" || htmlvar_name == "" || site_title == "" )
	{
		if(sort_order =="")
			jQuery('#sort_order1').addClass('form-invalid');
		jQuery('#sort_order').change(on_change_sort_order);
		if(htmlvar_name =="")
			jQuery('#htmlvar_name1').addClass('form-invalid');
		jQuery('#htmlvar_name').change(on_change_htmlvar_name);
		if(site_title =="")
			jQuery('#admin_title_id').addClass('form-invalid');
		jQuery('#site_title').change(on_change_site_title);
		return false;
	}
	function on_change_sort_order()
	{
		var sort_order = jQuery('#sort_order').val();
		
		if(sort_order=="")
		{
			jQuery('#sort_order1').addClass('form-invalid');
			return false;
		}
		else if(sort_order!="")
		{
			jQuery('#sort_order1').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_htmlvar_name()
	{
		var htmlvar_name = jQuery('#htmlvar_name').val();
		if( htmlvar_name == '' )
		{
			jQuery('#htmlvar_name1').addClass('form-invalid');
			return false;
		}
		else if( htmlvar_name != '' )
		{
			jQuery('#htmlvar_name1').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_site_title()
	{
		var site_title = jQuery('#site_title').val();
		if( site_title == '')
		{
			jQuery('#admin_title_id').addClass('form-invalid');
			return false;
		}
		else if( site_title != '' )
		{
			jQuery('#admin_title_id').removeClass('form-invalid');
			return true;
		}
	}
}