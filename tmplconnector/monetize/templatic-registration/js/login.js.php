<script type="text/javascript">
var $cwidget = jQuery.noConflict();
$cwidget(document).ready(function(){
	//global vars
	var loginform = $cwidget("#loginwidgetform");
	var your_name = $cwidget("#widget_user_login");
	var your_pass = $cwidget("#widget_user_pass");
	
	var your_name_Info = $cwidget("#user_login_info");
	var your_pass_Info = $cwidget("#your_pass_info");
	
	//On blur
	your_name.blur(validate_widget_your_name);
	your_pass.blur(validate_widget_your_pass);
	
	//On key press
	your_name.keyup(validate_widget_your_name);
	your_pass.keyup(validate_widget_your_pass);
	//On Submitting
	loginform.submit(function(){
		if(validate_widget_your_name() & validate_widget_your_pass() )
		{
			hideform();
			return true
		}
		else
		{
			return false;
		}
	});
	//validation functions
	function validate_widget_your_name()
	{
		if($cwidget("#widget_user_login").val() == '')
		{
			your_name.addClass("error");
			your_name_Info.text("<?php _e('Please Enter Name',DOMAIN); ?>");
			your_name_Info.addClass("message_error");
			return false;
		}
		else
		{
			your_name.removeClass("error");
			your_name_Info.text("");
			your_name_Info.removeClass("message_error");
			return true;
		}
	}
	
	function validate_widget_your_pass()
	{ 
		if($cwidget("#widget_user_pass").val() == '')
		{ 
			your_pass.addClass("error");
			your_pass_Info.text("<?php _e('Please Enter password',DOMAIN); ?>");
			your_pass_Info.addClass("message_error");
			return false;
		}
		else{
			your_pass.removeClass("error");
			your_pass_Info.text("");
			your_pass_Info.removeClass("message_error");
			return true;
		}
	}
	/**registration form validation**/
	var registerform = $cwidget("#registerform");
	//On blur
	$cwidget('#widget_user_rlogin').blur(validate_widget_your_ulogin);
	$cwidget('#widget_user_remail').blur(validate_widget_your_remail);
	
	var your_rname_Info = $cwidget("#your_rname_Info");
	var your_remail_Info = $cwidget("#your_remail_Info");
	function validate_widget_your_ulogin()
	{ 
		if($cwidget("#widget_user_rlogin").val() == '')
		{ 
			$cwidget('#widget_user_rlogin').addClass("error");
			your_rname_Info.text("<?php _e('Please Enter User name',DOMAIN); ?>");
			your_rname_Info.addClass("message_error");
			return false;
		}
		else{
			$cwidget('#widget_user_rlogin').removeClass("error");
			your_rname_Info.text("");
			your_rname_Info.removeClass("message_error");
			return true;
		}
	}
	
	function validate_widget_your_remail()
	{ 
		if($cwidget("#widget_user_remail").val() == '')
		{ 
			$cwidget('#widget_user_remail').addClass("error");
			your_remail_Info.text("<?php _e('Please Enter Email ID',DOMAIN); ?>");
			your_remail_Info.addClass("message_error");
			return false;
		}
		else{
			$cwidget('#widget_user_remail').removeClass("error");
			your_remail_Info.text("");
			your_remail_Info.removeClass("message_error");
			return true;
		}
	}
	//On Submitting
		registerform.submit(function(){
		if(validate_widget_your_ulogin() & validate_widget_your_remail() )
		{
			hideform();
			return true
		}
		else
		{
			return false;
		}
		});
});			
</script>