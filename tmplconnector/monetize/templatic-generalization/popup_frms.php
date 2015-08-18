<div id="basic-modal-content" class="templ_popup_forms clearfix" style="display:none;">
	
	<?php global $post,$wp_query; ?>
    <form name="send_to_frnd" id="send_to_frnd" action="#" method="post">     
    <input type="hidden" id="send_post_id" name="post_id" value="<?php echo $post->ID;?>"/>
    <input type="hidden" id="link_url" name="link_url" value="<?php	the_permalink();?>"/>
    <input type="hidden" id="send_to_Frnd_pid" name="pid" />
    <input type="hidden" name="sendact" value="email_frnd" />
    <div class="email_to_friend">
        <h3 class="h3"><?php _e('Send To Friend',DOMAIN);?></h3>
        <a class="modal_close" href="#"></a>
     </div>
     <?php $tmpdata = get_option('templatic_settings');?>
            
                <div class="form_row clearfix" ><label><?php _e('Friend&rsquo;s name',DOMAIN);?>: <span>*</span></label> <input name="to_name_friend" id="to_name_friend" type="text"  /><span id="to_name_friendInfo"></span></div>
        
                <div class="form_row clearfix" ><label> <?php _e('Friend&rsquo;s email',DOMAIN);?>: <span>*</span></label> <input name="to_friend_email" id="to_friend_email" type="text"  value=""/><span id="to_friend_emailInfo"></span></div>
            
                <div class="form_row clearfix" ><label><?php _e('Your name',DOMAIN);?>: <span>*</span></label> <input name="yourname" id="yourname" type="text"  /><span id="yournameInfo"></span></div>
            
                <div class="form_row clearfix" ><label> <?php _e('Your email',DOMAIN);?>: <span>*</span></label> <input name="youremail" id="youremail" type="text"  /><span id="youremailInfo"></span></div>
            
                <div class="form_row clearfix" ><label><?php _e('Subject',DOMAIN);?>: </label> <input name="frnd_subject" value="<?php if(isset($tmpdata['mail_friend_sub'])){_e($tmpdata['mail_friend_sub'],DOMAIN);}else{ _e('Check out this post',DOMAIN);} ?>" id="frnd_subject" type="text"  /></div>
            
                <div class="form_row clearfix" ><label><?php _e('Comments',DOMAIN);?>: </label> <textarea name="frnd_comments" id="frnd_comments" cols="10" rows="5" ><?php _e('Hello, I just stumbled upon this listing and thought you might like it. Just check it out.',DOMAIN); ?></textarea></div>
                <?php
                $tmpdata = get_option('templatic_settings');
				$display = @$tmpdata['user_verification_page'];
				if(@in_array('emaitofrd', $display))
				{
                ?>
					<div id="snd_frnd_cap"></div>
    		<?php } ?>
                <div class="send_info_button clearfix">
                    <input name="Send"  type="submit" value="<?php _e('Send',DOMAIN)?> " class="button send_button" />
                    <span id="process_send_friend" style="display:none;"><img src="<?php echo TEMPL_PLUGIN_URL.'images/process.gif'?>" alt="<?php _e('Loading...',DOMAIN);?>" /></span>
              		<strong id="send_friend_msg" class="process_state"></strong>
                </div>
               <div class="clearfix"></div>
            
    </form>
</div>
<script type="text/javascript">
var $q = jQuery.noConflict();
$q(document).ready(function(){
//global vars
	var send_to_frnd = $q("#send_to_frnd");
	var to_name_friend = $q("#to_name_friend");
	var to_name_friendInfo = $q("#to_name_friendInfo");
	var to_friend_email = $q("#to_friend_email");
	var to_friend_emailInfo = $q("#to_friend_emailInfo");
	var yourname = $q("#yourname");
	var yournameInfo = $q("#yournameInfo");
	var youremail = $q("#youremail");
	var youremailInfo = $q("#youremailInfo");
	var frnd_comments = $q("#frnd_comments");
	var frnd_commentsInfo = $q("#frnd_commentsInfo");
	
	//On blur
	to_name_friend.blur(validate_to_name_friend);
	to_friend_email.blur(validate_to_email_to);
	yourname.blur(validate_yourname);
	youremail.blur(validate_youremail);
	frnd_comments.blur(validate_frnd_comments);
	
	//On key press
	to_name_friend.keyup(validate_to_name_friend);
	to_friend_email.keyup(validate_to_email_to);
	yourname.keyup(validate_yourname);
	youremail.keyup(validate_youremail);
	frnd_comments.keyup(validate_frnd_comments);
	
	//On Submitting
	send_to_frnd.submit(function(){
		if(validate_to_name_friend() & validate_to_email_to() & validate_yourname() & validate_youremail() & validate_frnd_comments())
		{
			function reset_send_email_agent_form()
			{
				document.getElementById('to_name_friend').value = '';
				document.getElementById('to_friend_email').value = '';
				document.getElementById('yourname').value = '';
				document.getElementById('youremail').value = '';	
				document.getElementById('frnd_subject').value = '';
				document.getElementById('frnd_comments').value = '';	
			}
			var captcha_comment = jQuery('#recaptcha_widget_div').html();
			document.getElementById('process_send_friend').style.display="block";
			var send_to_frnd_data = send_to_frnd.serialize();				
			jQuery.ajax({
				url:ajaxUrl,
				type:'POST',
				data:'action=tevolution_send_friendto_form&' + send_to_frnd_data,
				success:function(results) {	
					document.getElementById('process_send_friend').style.display="none";					
					if(results==1){
						jQuery('#send_friend_msg').html('<?php _e('Invalid captcha. Please try again.',DOMAIN);?>');	
					}else if(results==2){
						jQuery('#send_friend_msg').html('<?php _e('You need to play the game to send the mail successfully.',DOMAIN);?>');	
					}else{
						jQuery('#send_friend_msg').html(results);
						setTimeout(function(){
									jQuery("#lean_overlay").fadeOut(200);
									jQuery('#basic-modal-content').css({"display":"none"});
									jQuery('#send_friend_msg').html('');
									yourname.val('');
									jQuery('#recaptcha_widget_div').html(jQuery('#snd_frnd_cap').html());
									youremail.val('');
									to_name_friend.val('');
									to_friend_email.val('');
									
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
	function validate_to_name_friend()
	{
		if($q("#to_name_friend").val() == '')
		{
			to_name_friend.addClass("error");
			to_name_friendInfo.text("<?php _e('Please enter your friend\'s name',DOMAIN); ?>");
			to_name_friendInfo.addClass("message_error2");
			return false;
		}else{
			to_name_friend.removeClass("error");
			to_name_friendInfo.text("");
			to_name_friendInfo.removeClass("message_error2");
			return true;
		}
	}
	function validate_to_email_to()
	{
		var isvalidemailflag = 0;
		if(to_friend_email.val() == '')
		{
			isvalidemailflag = 1;
		}else
		if($q("#to_friend_email").val() != '')
		{
			var a = $q("#to_friend_email").val();			
			var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if(filter.test(a)){
				isvalidemailflag = 0;
			}else{
				isvalidemailflag = 1;	
			}
		}
		if(isvalidemailflag)
		{
			to_friend_email.addClass("error");
			to_friend_emailInfo.text("<?php _e('Please enter your friend\'s valid email address',DOMAIN); ?>");
			to_friend_emailInfo.addClass("message_error2");
			return false;
		}else
		{
			to_friend_email.removeClass("error");
			to_friend_emailInfo.text("");
			to_friend_emailInfo.removeClass("message_error");
			return true;
		}
	}
	function validate_yourname()
	{
		if($q("#yourname").val() == '')
		{
			yourname.addClass("error");
			yournameInfo.text("<?php _e('Please Enter Your Name',DOMAIN); ?>");
			yournameInfo.addClass("message_error2");
			return false;
		}
		else{
			yourname.removeClass("error");
			yournameInfo.text("");
			yournameInfo.removeClass("message_error2");
			return true;
		}
	}
	function validate_youremail()
	{
		var isvalidemailflag = 0;
		if($q("#youremail").val() == '')
		{
			isvalidemailflag = 1;
		}else
		if($q("#youremail").val() != '')
		{
			var a = $q("#youremail").val();
			var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
			//if it's valid email
			if(filter.test(a)){
				isvalidemailflag = 0;
			}else{
				isvalidemailflag = 1;	
			}
		}
		if(isvalidemailflag)
		{
			youremail.addClass("error");
			youremailInfo.text("<?php _e('Please enter your valid email address',DOMAIN); ?>");
			youremailInfo.addClass("message_error2");
			return false;
		}else
		{
			youremail.removeClass("error");
			youremailInfo.text("");
			youremailInfo.removeClass("message_error");
			return true;
		}
	}
	function validate_frnd_comments()
	{
		if($q("#frnd_comments").val() == '')
		{
			frnd_comments.addClass("error");
			frnd_commentsInfo.text("<?php _e('Please Enter Comments',DOMAIN); ?>");
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
<!-- here -->
