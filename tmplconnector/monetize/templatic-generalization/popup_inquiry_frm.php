<div id="inquiry_div" class="templ_popup_forms clearfix" style="display:none;">
<?php global $post,$wp_query; ?>
    <form name="inquiry_frm" id="inquiry_frm" action="#" method="post"> 
        <input type="hidden" id="listing_id" name="listing_id" value="<?php _e($post->ID,DOMAIN); ?>"/>
        <input type="hidden" id="request_uri" name="request_uri" value="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>"/>
        <input type="hidden" id="link_url" name="link_url" value="<?php	the_permalink();?>"/>
        <?php $userdata = get_userdata($post->post_author); ?>  
        <input type="hidden" name="to_name" id="to_name" value="<?php _e($userdata->display_name,DOMAIN);?>" />
        <input type="hidden" id="author_email" name="author_email" value="<?php echo $userdata->user_email; ?>"/>
        <div class="email_to_friend">
        	<h3 class="h3"><?php _e("Inquiry for",DOMAIN); echo ' ';_e(stripslashes($post->post_title),DOMAIN); ?></h3>
        	<a class="modal_close" href="#"></a>
    	</div>
          <?php $tmpdata = get_option('templatic_settings');?>          
            <div class="form_row clearfix" ><label><?php _e('Full name',DOMAIN); ?>: <span>*</span></label> <input name="full_name" id="full_name" type="text"  /><span id="full_nameInfo"></span></div>
        
            <div class="form_row clearfix" ><label> <?php _e('Your email',DOMAIN); ?>: <span>*</span></label> <input name="your_iemail" id="your_iemail" type="text"  /><span id="your_iemailInfo"></span></div>
            
            <div class="form_row clearfix" ><label> <?php _e('Contact number',DOMAIN); ?>: </label> <input name="contact_number" id="contact_number" type="text"  /><span id="contact_numberInfo"></span></div>	
            
            <div class="form_row clearfix" ><label> <?php _e('Subject',DOMAIN); ?>: <span>*</span></label>
            <input name="inq_subject" id="inq_subject" type="text"  value="<?php if(isset($tmpdata['send_inquirey_email_sub'])){ _e(stripslashes($tmpdata['send_inquirey_email_sub']),DOMAIN);}else{ _e('Inquiry email',DOMAIN);}?>" />
            <input name="to_email" id="to_email" type="hidden" value="<?php echo get_post_meta($post->ID,'email',true); ?>"  />
            <span id="inq_subInfo"></span></div>
            <div class="form_row  clearfix" ><label> <?php _e(' Message',DOMAIN); ?>: <span>*</span></label> 
				<textarea rows="5" name="inq_msg" id="inq_msg"><?php 
					$msg =_e('Hello, I would like to inquire more about this listing. Please let me know how can I get in touch with you. Waiting for your prompt reply?',DOMAIN);
					if(function_exists('icl_register_string')){
						icl_register_string(DOMAIN,$msg,$msg);
					}
					
					if(function_exists('icl_t')){
						$message1 = icl_t(DOMAIN,$msg,$msg);
					}else{
						$message1 = __($msg,DOMAIN); 
					}
					echo $message1;
				?></textarea><span id="inq_msgInfo"></span></div>
				<?php
                $tmpdata = get_option('templatic_settings');
				$display = @$tmpdata['user_verification_page'];
				if(@in_array('sendinquiry', $display))
				{
                ?>
					<div id="inquiry_frm_popup"></div>
			<?php } ?>
            <div class="send_info_button clearfix" >
            	<input name="Send" type="submit" value="<?php _e('Send',DOMAIN); ?>" class="button send_button" />
               <span id="process_state" style="display:none;"><img src="<?php echo TEMPL_PLUGIN_URL.'images/process.gif'?>" /></span>
              	<strong id="send_inquiry_msg" class="process_state"></strong>
		  </div>
    </form>
</div>
<?php 
	global $post;
	$current_post_id = $post->ID;
?>
<script>
var $q = jQuery.noConflict();
$q(document).ready(function(){
//global vars
	var enquiry1frm = $q("#inquiry_frm");
	var full_name = $q("#full_name");
	var full_nameInfo = $q("#full_nameInfo");
	var your_iemail = $q("#your_iemail");
	var your_iemailInfo = $q("#your_iemailInfo");
	var sub = $q("#inq_subject");
	var subinfo = $q("#inq_subInfo");
	var frnd_comments = $q("#inq_msg");
	var frnd_commentsInfo = $q("#inq_msgInfo");
	//On blur
	full_name.blur(validate_full_name1);
	your_iemail.blur(validate_your_iemail);
	sub.blur(validate_subject);
	frnd_comments.blur(validate_frnd_comments);
	frnd_comments.keyup(validate_frnd_comments);
	//On Submitting
	
	enquiry1frm.submit(function(){
		
		if(validate_full_name1() & validate_your_iemail() & validate_subject() & validate_frnd_comments())
		{ 
			document.getElementById('process_state').style.display="block";
			var inquiry_data = enquiry1frm.serialize();				
			jQuery.ajax({
				url:ajaxUrl,
				type:'POST',
				data:'action=tevolution_send_inquiry_form&' + inquiry_data + '&postid=' + <?php echo $current_post_id; ?>,
				success:function(results) {	
					document.getElementById('process_state').style.display="none";					
					if(results==1){
						jQuery('#send_inquiry_msg').html('<?php _e('Invalid captcha. Please try again.',DOMAIN);?>');	
					}else{
						jQuery('#send_inquiry_msg').html(results);
						setTimeout(function(){
									jQuery("#lean_overlay").fadeOut(200);
									jQuery('#inquiry_div').css({"display":"none"});
									jQuery('#send_inquiry_msg').html('');
									jQuery('#recaptcha_widget_div').html(jQuery('#inquiry_frm_popup').html());
									full_name.val('');
									your_iemail.val('');
									$q("#contact_number").val('');									
										},2000); 
					}
					
				}
			});
			return false;
		}
		else
		{ 
			
			return false;
		}
	});
	
	//validation functions
	function validate_full_name1()
	{	
		if(full_name.val() == '')
		{
			full_name.addClass("error");
			full_nameInfo.text("<?php _e('Please enter your full name',DOMAIN);?>");
			full_nameInfo.addClass("message_error2");
			return false;
		}else{
			full_name.removeClass("error");
			full_nameInfo.text("");
			full_nameInfo.removeClass("message_error2");
			return true;
		}
	}
	function validate_your_iemail()
	{ 
		var isvalidemailflag = 0;
		if(your_iemail.val() == '')
		{
			isvalidemailflag = 1;
		}else {
			if(your_iemail.val() != '')
			{
				var a = your_iemail.val();
				var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				//if it's valid your_iemail
				if(filter.test(a)){
					isvalidemailflag = 0;
				}else{
					isvalidemailflag = 1;	
				}
			}
		}
		if(isvalidemailflag == 1)
		{
			your_iemail.addClass("error");
			your_iemailInfo.text("<?php _e('Please enter your valid email address',DOMAIN);?>");
			your_iemailInfo.addClass("message_error2");
			return false;
		}else
		{
			your_iemail.removeClass("error");
			your_iemailInfo.text("");
			your_iemailInfo.removeClass("message_error");
			return true;
		}
		
	}
	function validate_subject()
	{ 
		if($q("#inq_subject").val() == '')
		{
			sub.addClass("error");
			subinfo.text("<?php _e('Please enter subject line',DOMAIN);?>");
			subinfo.addClass("message_error2");
			return false;
		}
		else{
			sub.removeClass("error");
			subinfo.text("");
			subinfo.removeClass("message_error2");
			return true;
		}
	}
	
	function validate_frnd_comments()
	{
		if($q("#inq_msg").val() == '')
		{
			frnd_comments.addClass("error");
			frnd_commentsInfo.text("<?php _e('Please enter message',DOMAIN);?>");
			frnd_commentsInfo.addClass("message_error2");
			return false;
		}else{
			frnd_comments.removeClass("error");
			frnd_commentsInfo.text("");
			frnd_commentsInfo.removeClass("message_error2");
			return true;
		}
	}
});
</script>
