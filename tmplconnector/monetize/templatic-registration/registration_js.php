<script type="text/javascript">
jQuery.noConflict();
var xmlHTTP;
var checkclick = false;
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
function chkemail()
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
	if(document.getElementById("user_email"))
		user_email = document.getElementById("user_email").value;
	var url = "<?php echo TEMPL_PLUGIN_URL; ?>tmplconnector/monetize/templatic-registration/ajax_check_user_email.php?user_email="+user_email;
	xmlhttp.open("GET",url,true);
	xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xmlhttp.send(null);
	xmlhttp.onreadystatechange=function()
	{
	   	if(xmlhttp.readyState==4 && xmlhttp.status==200)
	   	{
			var email = xmlhttp.responseText.split(","); 
			if(email[1] == 'email')
			{
				if(email[0] > 0)
				{
					document.getElementById("user_email_error").innerHTML = '<?php _e('Email address already exists, Please enter another email',DOMAIN);?>';
					document.getElementById("user_email_already_exist").value = 0;
					jQuery("#user_email_error").removeClass('available_tick');
					jQuery("#user_email_error").addClass('message_error2');
				}
				else
				{
					document.getElementById("user_email_error").innerHTML = '<?php _e('Your email address is verified.',DOMAIN);?>';
					document.getElementById("user_email_already_exist").value = 1;
					jQuery("#user_email_error").removeClass('message_error2');
					jQuery("#user_email_error").addClass('available_tick');
				}
			}
		}
	}
	return true;
}
function chkname()
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
	if(document.getElementById("user_fname"))
		user_fname = document.getElementById("user_fname").value;
	var url = "<?php echo TEMPL_PLUGIN_URL; ?>tmplconnector/monetize/templatic-registration/ajax_check_user_email.php?user_fname="+user_fname;
	jQuery("#registernow_form").click(function(){
			checkclick = true;
   });
	xmlhttp.open("GET",url,true);
	xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xmlhttp.send(null);
	xmlhttp.onreadystatechange=function()
	{
	   	if(xmlhttp.readyState==4 && xmlhttp.status==200)
	   	{
			var fname = xmlhttp.responseText.split(","); 
			if(fname[1] == 'fname')
			{
				if(fname[0] > 0)
				{
					document.getElementById("user_fname_error").innerHTML = "<?php _e('Username already exists, Please use another username',DOMAIN);?>";
					document.getElementById("user_fname_already_exist").value = 0;
					jQuery("#user_fname_error").addClass('message_error2');
					jQuery("#user_fname_error").removeClass('available_tick');
				}
				else
				{
					document.getElementById("user_fname_error").innerHTML = "<?php _e('Your user name is verified.',DOMAIN);?>";
					document.getElementById("user_fname_already_exist").value = 1;
					jQuery("#user_fname_error").removeClass('message_error2');
					jQuery("#user_fname_error").addClass('available_tick');
					if(jQuery("#userform div").size() == 2 && checkclick)
					 {
						 document.userform.submit();
					 }
				}
			}
		}
	}
	return true;
}


function set_login_registration_frm(val)
{
	if(val=='existing_user')
	{
		document.getElementById('login_user_meta').style.display = 'none';
		document.getElementById('login_user_frm_id').style.display = '';
		document.getElementById('login_type').value = val;
		if(document.getElementById('monetize_preview'))
			document.getElementById('monetize_preview').style.display = 'none';
	}else  //new_user
	{
		document.getElementById('login_user_meta').style.display = 'block';
		document.getElementById('login_user_frm_id').style.display = 'none';
		document.getElementById('login_type').value = val;
		if(document.getElementById('monetize_preview'))
			document.getElementById('monetize_preview').style.display = 'block';
	}
}
</script>
