<?php
/*
 * validation for submit form
 */
?>
<script type="text/javascript">
var front_submission=0;
jQuery.noConflict();
jQuery(document).ready(function(){
//<![CDATA[
<?php
global $validation_info;
$js_code = '';
/*$js_code .= '//global vars ';*/
$js_code .= 'var submit_form = jQuery("#submit_form");'; /*form Id*/
$jsfunction = array();
for($i=0;$i<count($validation_info);$i++) {
	$title = $validation_info[$i]['title'];
	$name = $validation_info[$i]['name'];
	$validation_name = $validation_info[$i]['name'];
	$espan = $validation_info[$i]['espan'];
	$type = $validation_info[$i]['type'];
	$search_ctype = $validation_info[$i]['search_ctype'];
	$text = __($validation_info[$i]['text'],DOMAIN);
	$validation_type = $validation_info[$i]['validation_type'];
	$is_required = $validation_info[$i]['is_require'];
	$is_required_desc = $validation_info[$i]['field_require_desc'];
	if($is_required ==''){
		$is_required = 0;
	}
	$js_code .= '
	dml = document.forms[\'submit_form\'];
	var '.$name.' = jQuery("#'.$name.'"); ';
	$js_code .= '
	var '.$espan.' = jQuery("#'.$espan.'"); 
	';
	if($type=='selectbox' || $type=='checkbox')
	{
		$msg = sprintf(__("%s",DOMAIN),$text);
	}else
	{
		$msg = sprintf(__("%s",DOMAIN),$text);
	}
	$category_can_select_validation_msg = __("You cannot select more than ",DOMAIN); /* message used for while submitting a form with category selected greater than the number of category selection for particular price package. */ 
	$category_can_select_validation_message = __(" categories with this package.",DOMAIN); /* message used for while submitting a form with category selected greater than the number of category selection for particular price package.*/ 
	if($type == 'multicheckbox' || $type=='checkbox' || $type=='radio' || $type=='post_categories' || $type=='upload')
	{
		$js_code .= '
		function validate_'.$name.'()
		{
			if("'.$type.'" != "upload")
			  {
				var chklength = jQuery("#submit_form #'.$name.'").length;
			  }
			if("'.$type.'" =="multicheckbox")
			  {
				chklength = document.getElementsByName("'.$name.'[]").length;
			  }
			if("'.$name.'" == "category"){
				chklength = document.getElementsByName("'.$name.'[]").length;
			}
			if("'.$type.'" =="radio")
			  {
				if(!jQuery("input[name='.$name.']:checked").length > 0) {
					flag = 1;
				}
				else
				{
					flag = 0;
				}
			  }
			
			if("'.$type.'" =="upload")
			  {
				  var id_value = jQuery('.$name.').val();
				  var valid_extensions = /(.txt|.pdf|.doc|.xls|.xlsx|.csv|.docx|.rar|.zip|.jpg|.jpeg|.gif|.png)$/i;
				  if(valid_extensions.test(id_value))
				  {
					  
				  }
				  else
				  { ';
				    if($text !='' && $type=='upload'){
					   $umsg = $text;
					}else{
					   $umsg = __("You are uploading invalid file type. Allowed file types are",DOMAIN)." : txt, pdf, doc, xls, csv, docx, xlsx, zip, rar";
					}
				   $js_code .= 'jQuery("#'.$name.'_error").html("'.$umsg.'");
				   return false;
				  }
			  }
 			var temp	  = "";
			var i = 0;
			if("'.$type.'" =="multicheckbox" || "'.$type.'"=="checkbox")
			  {
			chk_'.$name.' = document.getElementsByName("'.$name.'[]");
			if("'.$name.'" == "category"){
				chk_'.$name.' = document.getElementsByName("'.$name.'[]");
			}			
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
			  }
			if(flag == 1)
			{
				if("'.$name.'" == "category"){
					document.getElementById("'.$espan.'").innerHTML = "'.$msg.'";
				}else{
					jQuery("#'.$espan.'").text("'.$msg.'");
				}
				jQuery("#'.$espan.'").addClass("message_error2");
				 return false;
			}
			else{
				if("'.$name.'" == "category"){
					chklength = document.getElementsByName("'.$name.'[]").length;
					cat_count = 0;
					for(i=0;i<chklength;i++) {
						if ((chk_'.$name.'[i].checked == true)) { ';
							$js_code .= '
							cat_count =  cat_count + 1;
						} 
					}
					if(document.getElementById("category_can_select") && document.getElementById("category_can_select").value > 0)
					{
						if(cat_count > document.getElementById("category_can_select").value && chklength > 0)
						{
							
							document.getElementById("category_error").innerHTML = "'.$category_can_select_validation_msg.' "+document.getElementById("category_can_select").value+"'.$category_can_select_validation_message.' ";
							jQuery("#'.$espan.'").addClass("message_error2");
							return false;
						}
					}
				}	
				jQuery("#'.$espan.'").text("");
				jQuery("#'.$espan.'").removeClass("message_error2");
				return true;
			}
		}
	';
	}else {
		$js_code .= '
		function validate_'.$name.'()
		{
			
			';
			if($validation_type == 'email') {
				$js_code .= '
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;				
				if(jQuery("#submit_form #'.$name.'").val() == "" && '.$is_required.') {';
				if($text){
					$emsg = $text;
				}else{
					$emsg = __("Please provide your email address",DOMAIN);
				}
			
				$js_code .= $name.'.addClass("error");
					jQuery("#'.$espan.'").text("'.$emsg.'");
					jQuery("#'.$espan.'").addClass("message_error2"); 					
				return false;';
				$js_code .= ' } else if(typeof jQuery("#submit_form #'.$name.'").val()!=="undefined" && '.$is_required.' && !emailReg.test(jQuery("#submit_form #'.$name.'").val().replace(/\s+$/,""))  ) { ';
					if($text){
						$emsg = $text;
					}else{
						$emsg = __("Please provide your email address",DOMAIN);
					}
					$js_code .= $name.'.addClass("error");
					jQuery("#'.$espan.'").text("'.$emsg.'");
					jQuery("#'.$espan.'").addClass("message_error2");					
					return false;';
				$js_code .= '
				} else {
					'.$name.'.removeClass("error");
					jQuery("#'.$espan.'").text("");
					jQuery("#'.$espan.'").removeClass("message_error2");
					return true;
				}';
			} if($validation_type == 'phone_no'){
				$js_code .= '
				var phonereg = /^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/;
				if(jQuery("#submit_form #'.$name.'").val() == "" && '.$is_required.') { ';
					$msg = $text;
					$js_code .= $name.'.addClass("error");
					jQuery("#'.$espan.'").text("'.$msg.'");
					jQuery("#'.$espan.'").addClass("message_error2");					
				return false;';
				$js_code .= ' } else if(!phonereg.test(jQuery("#submit_form #'.$name.'").val()) && jQuery("#submit_form #'.$name.'").val()) { ';
					$msg = __("Enter Valid Phone No.",DOMAIN);
					$js_code .= $name.'.addClass("error");
					jQuery("#'.$espan.'").text("'.$msg.'");
					jQuery("#'.$espan.'").addClass("message_error2");					
					return false;';
				$js_code .= '
				} else {
					'.$name.'.removeClass("error");
					jQuery("#'.$espan.'").text("");
					jQuery("#'.$espan.'").removeClass("message_error2");
					return true;
				}';
			}if($validation_type == 'digit'){
				$js_code .= '
				var digitreg = /^[0-9]+$/;
				if(jQuery("#submit_form #'.$name.'").val() == "" && '.$is_required.') { ';
					$msg = trim($text);
				$js_code .= $name.'.addClass("error");
					jQuery("#'.$espan.'").text("'.$msg.'");
					jQuery("#'.$espan.'").addClass("message_error2");					
				return false;';
				$js_code .= ' } else if(jQuery("#submit_form #'.$name.'").val() && !digitreg.test(jQuery("#submit_form #'.$name.'").val()) && '.$is_required.') { ';
					$msg = __("Values must be all numbers.",DOMAIN);
					$js_code .= $name.'.addClass("error");
					jQuery("#'.$espan.'").text("'.$msg.'");
					jQuery("#'.$espan.'").addClass("message_error2");					
					return false;';
				$js_code .= '
				} else {
					'.$name.'.removeClass("error");
					jQuery("#'.$espan.'").text("");
					jQuery("#'.$espan.'").removeClass("message_error2");
					return true;
				}';
			}
			if($type == 'texteditor'){
				$js_code .= 'if(jQuery("#submit_form #'.$name.'").css("display") == "none")
				{
				if(tinyMCE.get("'.$name.'").getContent().replace(/<[^>]+>/g, "") == "") { ';
					$msg = $text;
				$js_code .= $name.'.addClass("error");
					jQuery("#'.$espan.'").text("'.$msg.'");
					jQuery("#'.$espan.'").addClass("message_error2");					
				return false;';
				$js_code .= ' }  else {
					'.$name.'.removeClass("error");
					jQuery("#'.$espan.'").text("");
					jQuery("#'.$espan.'").removeClass("message_error2");					
					return true;
				}
				}else
				{
					if(jQuery("#submit_form #'.$name.'").val() == "")
					{
						jQuery("#'.$espan.'").text("'.$msg.'");
						jQuery("#'.$espan.'").addClass("message_error2");						
						return false;
					}
					else
					{
						jQuery("#'.$espan.'").text("");
						jQuery("#'.$espan.'").removeClass("message_error2");
						return true;
					}
				}';
			}
			if($type == 'image_uploader'){
				$js_code .= 'if(jQuery("#imgarr").val() == "")
				{
					if("'.$msg.'" == "")
					{
						jQuery("#post_images_error").html("'.__("Please upload at least 1 image to the gallery !",DOMAIN).'");						
						return false;
					}
					else
					{
						jQuery("#post_images_error").html("'.$msg.'");						
						return false;
					}
				}';
			}
			$js_code .= 'if("'.$name.'" == "end_date")';
			{
				$js_code .= '
				{
					 if(jQuery("#submit_form #'.$name.'").val() < jQuery("#submit_form #st_date").val() || jQuery("#submit_form #'.$name.'").val() == "")
					{
						';
						$js_code .= $name.'.addClass("error");
						jQuery("#'.$espan.'").text("'.$msg.'");
						jQuery("#'.$espan.'").addClass("message_error2");						
						return false;
					}
					else
					{
						'.$name.'.removeClass("error");
						jQuery("#'.$espan.'").text("");
						jQuery("#'.$espan.'").removeClass("message_error2");
						return true;
					}
				}';
			}
		$js_code .= 'if((!jQuery("#select_category").val() || jQuery("#select_category").val()=="") && "'.$name.'"=="category")';
		$js_code .= '
			{
				
				jQuery("#'.$espan.'").text("'.$msg.'");
				jQuery("#'.$espan.'").addClass("message_error2");				
				return false;
			}';
		$js_code .= 'if(jQuery("#submit_form #'.$name.'").val() == "" && '.$is_required.')';
		$js_code .= '
			{
				jQuery("#'.$espan.'").text("'.$msg.'");
				jQuery("#'.$espan.'").addClass("message_error2");				
				return false;
			}
			else{
				jQuery("#'.$espan.'").text("");
				jQuery("#'.$espan.'").removeClass("message_error2");				
				return true;
			}
		}
		';
	}
	
	if($type == 'range_type' && $search_ctype=='slider_range' ){
		$js_code .= '
		function validate_'.$name.'_range_type()
		{
			
			var value=jQuery("#submit_form #'.$name.'").val();
			var min_value=jQuery("#submit_form #'.$name.'").attr("min");
			var max_value=jQuery("#submit_form #'.$name.'").attr("max");
			if(parseInt(value) < parseInt(min_value)){	
				jQuery("#'.$espan.'_range_type").remove();
				jQuery("#submit_form #'.$name.'").after("<span id=\"'.$espan.'_range_type\" class=\"message_error2\">'.__('Please select a value that higher than',DOMAIN).' "+min_value+"</span>");				
				return false;
			}else if(parseInt(value) > parseInt(max_value)){				
				jQuery("#'.$espan.'_range_type").remove();
				jQuery("#submit_form #'.$name.'").after("<span id=\"'.$espan.'_range_type\" class=\"message_error2\">'.__('Please select a value that lower than',DOMAIN).' "+max_value+"</span>");				
				return false;
			}else if(isNaN(parseInt(value)) && value!=""){				
				jQuery("#'.$espan.'_range_type").remove();
				jQuery("#submit_form #'.$name.'").after("<span id=\"'.$espan.'_range_type\" class=\"message_error2\">'.__('Please enter a number',DOMAIN).'</span>");				
				return false;
			}else{				
				jQuery("#'.$espan.'_range_type").remove();
				return true;
			}
			
		}';
		/*$js_code .= $name.'.blur(validate_'.$name.'_range_type); ';*/
		/*$js_code .= $name.'.keyup(validate_'.$name.'_range_type); ';*/
		
		$js_code .= $name.'.live("focus blur keyup change", function(event){validate_'.$name.'_range_type()});'."\r\n";
	}
	/*$js_code .= 'On blur ';	*/
	/*$js_code .= $name.'.blur(validate_'.$name.'); ';*/
	/*$js_code .= 'On key press ';*/
	/*$js_code .= $name.'.keyup(validate_'.$name.'); ';*/
	if($name=='category'){
		$js_code .='jQuery("input[name^=category],.category_label input[name^=selectall],select[name^=category]").live("blur keyup change click", function(event){validate_'.$name.'()});'."\r\n";	
	}
	$js_code .='jQuery("#submit_form #'.$name.'").live("blur keyup", function(event){validate_'.$name.'()});'."\r\n";
	 
	
	$jsfunction[] = 'validate_'.$name.'()';
}
if($jsfunction)
{
	$jsfunction_str = implode(' & ', $jsfunction);	
}else{
	$jsfunction_str='';
}
/* $js_code .= 'On Submitting '; */
/* $js_code .= 'submit_form.submit(function() */
$js_code .= 'jQuery("#continue_submit_from").on("click",function (e)
{	
	var package_select = jQuery("input[name=package_select]");	
	var package_type=package_select.attr("type");
	if (document.getElementsByName("package_select").length >0){
		if(package_type =="radio")
		{
			if (!jQuery("input:radio[name=package_select]:checked").val())
			 {
				jQuery("#all_packages_error").html("'.__(PRICE_PACKAGE_ERROR,DOMAIN).'");
				return false; /* add comment return false nothing add and directoly submit then only price package error will be shown*/
			 }
			else
			{
				jQuery("#all_packages_error").html("");
			}
		}
	
	}
	
	/* Check terms and condition validation */
	if(jQuery("#term_and_condition").length){		
		if(!jQuery("#term_and_condition").attr("checked"))		
		{
			jQuery("#terms_error").html("'.__('Please accept Terms and Conditions.',DOMAIN).'");			
			return false; /* add comment return false nothing add and directoly submit then only term condition error will be shown*/
		}else{
			jQuery("#terms_error").html("");	
		}
	}
	';
	$js_code=apply_filters('submit_form_validation',$js_code);
	if($jsfunction_str !=''){
		$js_code.='if('.$jsfunction_str.')
		{
			jQuery("#common_error").html("");			
			if(recaptcha==1){
				/* Recaptcha validation function to check captcha validation */
				var check_validation=recaptcha_validation();				
				return check_validation;
			}
			return true;
		}
		else
		{
			jQuery("#common_error").html("'.__('Oops! Please make sure you have filled all the mandatory fields.',DOMAIN).'");
			return false;
		}';
	}
	$js_code.='
});
';
$js_code .= '
});';
echo $js_code;
?>
function hide_error(){
	if(jQuery("#term_and_condition").attr("checked"))
	{
		jQuery("#terms_error").html("");
	}
}

if(recaptcha==1){
	function recaptcha_validation(){
		var submit_from = jQuery('form#submit_form').serialize();
		var output;
		jQuery.ajax({
			url:ajaxUrl,
			type:'POST',
			async: false,
			data:submit_from+'&action=submit_form_recaptcha_validation',
		})
		.done(function(results){			
			if(results==1){
				jQuery("#common_error").html('');
				output= true;
			}else{
				jQuery("#common_error").html(results);
				output= false;
			}			
		});	
		return output;
	}
}
//]]>
</script>