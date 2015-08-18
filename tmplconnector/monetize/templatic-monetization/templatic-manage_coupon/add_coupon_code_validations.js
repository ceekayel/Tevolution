/* function to validate coupon code creation from backend */
function check_frm()
{
	jQuery.noConflict();
	
	var startdate = jQuery('#startdate').val();
	var enddate = jQuery('#enddate').val();
	var couponcode = jQuery('#couponcode').val();
	
	if( startdate == "" || enddate == "" || couponcode == "" )
	{
		if(startdate =="")
			jQuery('#startdate1').addClass('form-invalid');
		jQuery('#startdate').change(on_change_startdate);
		if(enddate =="")
			jQuery('#enddate1').addClass('form-invalid');
		jQuery('#enddate').change(on_change_enddate);
		if(couponcode =="")
			jQuery('#couponcode1').addClass('form-invalid');
		jQuery('#couponcode').change(on_change_couponcode);
		return false;
	}
	
	function on_change_startdate()
	{
		var startdate = jQuery('#startdate').val();
		
		if(startdate=="")
		{
			jQuery('#startdate1').addClass('form-invalid');
			return false;
		}
		else if(startdate!="")
		{
			jQuery('#startdate1').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_enddate()
	{
		var enddate = jQuery('#enddate').val();
		if( enddate == '' )
		{
			jQuery('#enddate1').addClass('form-invalid');
			return false;
		}
		else if( enddate != '' )
		{
			jQuery('#enddate1').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_couponcode()
	{
		var couponcode = jQuery('#couponcode').val();
		if( couponcode == '')
		{
			jQuery('#couponcode1').addClass('form-invalid');
			return false;
		}
		else if( couponcode != '' )
		{
			jQuery('#couponcode1').removeClass('form-invalid');
			return true;
		}
	}
}