<script type="text/javascript">
jQuery.noConflict();
<?php
global $user_validation_info;
global $submit_form_validation_id;
$js_code = 'jQuery(document).ready(function()
{
';
//$js_code .= '//global vars ';
$js_code .= 'var userform = jQuery("#'.$submit_form_validation_id.'"); 
'; //form Id
$jsfunction = array();
for($i=0;$i<count($user_validation_info);$i++)
{
	$name = $user_validation_info[$i]['name'];
	$espan = $user_validation_info[$i]['espan'];
	$type = $user_validation_info[$i]['type'];
	$text = html_entity_decode(__($user_validation_info[$i]['text'],DOMAIN),ENT_COMPAT, 'utf-8');
	
	$js_code .= '
	var '.$name.' = jQuery("#'.$name.'"); 
	';
	$js_code .= '
	var '.$espan.' = jQuery("#'.$espan.'"); 
	';
	if($type=='select' || $type=='checkbox' || $type=='multicheckbox' || $type=='catcheckbox')
	{
		$msg = html_entity_decode(__('Please Select ',DOMAIN).$text,ENT_COMPAT, 'utf-8');
	}else	{
		$msg = html_entity_decode(__('Please Enter ',DOMAIN).$text,ENT_COMPAT, 'utf-8');
	}
	
	if($type=='multicheckbox' || $type=='catcheckbox' || $type=='radio')
	{
		$js_code .= '
		function validate_'.$name.'()
		{
			var chklength = jQuery("#'.$name.'").length;
			if("'.$type.'" =="multicheckbox")
			  {
			var chklength =  document.getElementsByName("'.$name.'[]").length;
			}
			var flag      = false;
			var temp	  = "";
			for(i=1;i<=chklength;i++)
			{
				if((\'document.getElementById("'.$name.'_"+i+"")\'))
				{
				   temp = document.getElementById("'.$name.'_"+i+"").checked; 
				   if(temp == true)
				   {
						flag = true;
						break;
					}
				}
			}
			if("'.$type.'" =="radio")
			  {
				if (!jQuery("input:radio[name='.$name.']:checked").val()) {
					flag = 1;
				}
			  }
			var temp	  = "";
			var i = 0;
			chk_'.$name.' = document.getElementsByName("'.$name.'[]");
			
			if(chklength == 0){
			
				if ((chk_'.$name.'.checked == false)) {
					flag = 1;	
				} 
			} else {
				var flag      = 0;
			
				for(i=0;i<chklength;i++) {
					if ((chk_'.$name.'[i].checked == false)) { ';
						$js_code .= '
						flag = 1;
					} else {
						flag = 0;
						break;
					}
				}
				
			}
			if(flag == 1)
			{
				'.$espan.'.text("'.$msg.'");
				'.$espan.'.addClass("message_error2");
				return false;
			}
			else{			
				'.$espan.'.text("");
				'.$espan.'.removeClass("message_error2");
				return true;
			}
			
			return true;
		}
	';
	}else
	{
		$js_code .= '
		function validate_'.$name.'()
		{';
		if($type == 'texteditor'){							
				$js_code .= '
				if(jQuery("#'.$name.'").val() == "") {';
					$msg = $text;
				$js_code .= $name.'.addClass("error");
					'.$espan.'.text("'.$msg.'");
					'.$espan.'.addClass("message_error2");
				return false;';
					
				$js_code .= ' }  else {
					'.$name.'.removeClass("error");
					'.$espan.'.text("");
					'.$espan.'.removeClass("message_error2");
					return true;
				}';
			}
		if($type=='checkbox')
		{
			$js_code .='if(!document.getElementById("'.$name.'").checked)';
		}else
		{
			$js_code .= '
				if(jQuery("#'.$name.'").val() == "")
			';
		}
		$js_code .= '
			{
				'.$name.'.addClass("error");
				'.$espan.'.text("'.$msg.'");
				'.$espan.'.removeClass("available_tick");
				'.$espan.'.addClass("message_error2");
				return false;
			}
			else';
		if($name=='user_email')
		{
			$js_code .= '
			
			if(jQuery("#'.$name.'").val() != "")
			{
				var a = jQuery("#'.$name.'").val();
				var emailReg = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/;
				if(jQuery("#'.$name.'").val() == "") { ';
				$msg = html_entity_decode(__("Please provide your email address",DOMAIN),ENT_COMPAT, 'utf-8');
				$js_code .= $name.'.addClass("error");
					'.$espan.'.text("'.$msg.'");
					'.$espan.'.addClass("message_error2");
				return false;';
					
				$js_code .= ' } else if(!emailReg.test(jQuery("#'.$name.'").val().replace(/\s+$/,""))) { ';
					$msg = html_entity_decode(__("Please provide valid email address",DOMAIN),ENT_COMPAT, 'utf-8');
					$js_code .= $name.'.addClass("error");
					'.$espan.'.text("'.$msg.'");
					'.$espan.'.addClass("message_error2");
					return false;';
				$js_code .= '
				} else {
				chkemail();
				var chk_email = document.getElementById("user_email_already_exist").value;
					if(chk_email > 0)
					{
						'.$name.'.removeClass("error");
						'.$espan.'.text("");
						'.$espan.'.removeClass("message_error2");
						return true;
					}
					else{
						return false;
					}
				}
			}
			';
		}
		elseif($name=='user_fname')
		{
			$js_code .= '
			
			if(jQuery("#'.$name.'").val() != "")
			{
				var a = jQuery("#'.$name.'").val();
				var userLength = jQuery("#'.$name.'").val().length;
				if(jQuery("#'.$name.'").val() == "") { ';
						$js_code .= $name.'.addClass("error");
						'.$espan.'.text("'.$msg.'");
						'.$espan.'.addClass("message_error2");
						
				}else if(jQuery("#'.$name.'").val().match(/\ /)){ ';
					 $js_code .= $name.'.addClass("error");
					'.$espan.'.text("'.__("Usernames should not contain space.",DOMAIN).'");
					'.$espan.'.addClass("message_error2");
					return false;
				}else if(userLength < 4 ){ ';
					 $js_code .= $name.'.addClass("error");
					'.$espan.'.text("'.__("User name must be minimum 4 character long",DOMAIN).'");
					'.$espan.'.addClass("message_error2");
					return false;
				}else
				{
					chkname();
					var chk_fname = document.getElementById("user_fname_already_exist").value;
					if(chk_fname > 0)
					{
						'.$name.'.removeClass("error");
						'.$espan.'.text("");
						'.$espan.'.removeClass("message_error2");
						return true;
					}
					else{
						return false;
					}
				}
			}';
		}
		if($name=='pwd')
		{
			if(jQuery("#pwd").val() != jQuery("#cpwd").val()){
				$msg = html_entity_decode(__("Password could not be match",DOMAIN),ENT_COMPAT, 'utf-8');
				$js_code .= $name.'.addClass("error");
					'.$espan.'.text("'.$msg.'");
					'.$espan.'.addClass("message_error2");
				return false;';
			}
		}
		$js_code .= '{
				'.$name.'.removeClass("error");
				'.$espan.'.text("");
				'.$espan.'.removeClass("message_error2");
				return true;
			}
		}
		';
	}
	//$js_code .= '//On blur ';
	$js_code .= $name.'.blur(validate_'.$name.'); ';
	
	//$js_code .= '//On key press ';
	$js_code .= $name.'.keyup(validate_'.$name.'); ';
	
	$jsfunction[] = 'validate_'.$name.'()';
	
	
	if($type=='multicheckbox')
	{
		$js_code .= "jQuery('input').change(function(){
										validate_".$name."()
											});"; 
	}
}
$js_code .='var pwd = jQuery("#pwd"); 
	
	var pwd_error = jQuery("#pwdInfo"); 
	
		function validate_pwd()
		{
				if(jQuery("#pwd").val() == "")
			
			{
				pwd.addClass("error");
				pwd_error.text("Please enter password");
				pwd_error.addClass("message_error2");
				return false;
			}
			else{
				pwd.removeClass("error");
				pwd_error.text("");
				pwd_error.removeClass("message_error2");
				return true;
			}
		}
		pwd.blur(validate_pwd);
		pwd.keyup(validate_pwd); 
		var cpwd = jQuery("#cpwd"); 
	
	var cpwd_error = jQuery("#cpwdInfo"); 
	
		function validate_cpwd()
		{
				if(jQuery("#cpwd").val() == "")
			
			{
				cpwd.addClass("error");
				cpwd_error.text("Please enter confirm password");
				cpwd_error.addClass("message_error2");
				return false;
			} else if(jQuery("#cpwd").val() != jQuery("#pwd").val()) {
				cpwd.addClass("error");
				cpwd_error.text("Please confirm your password");
				cpwd_error.addClass("message_error2");
				return false;
			}
			else{
				cpwd.removeClass("error");
				cpwd_error.text("");
				cpwd_error.removeClass("message_error2");
				return true;
			}
		}
		cpwd.blur(validate_cpwd);
		cpwd.keyup(validate_cpwd);
		';
if($jsfunction)
{
	$jsfunction_str = implode(' & ', $jsfunction);	
}
//$js_code .= '//On Submitting ';
$js_code .= '	
userform.submit(function()
{
	if('.$jsfunction_str.' & validate_pwd() & validate_cpwd())
	{
		return true
	}
	else
	{
		return false;
	}
});
';
$js_code .= '
});';
echo $js_code;
?>
</script>
