<?php global $post,$wp_query; 
$tmpdata = get_option('templatic_settings');
$display = @$tmpdata['user_verification_page'];
?>
<div id="tmpl_send_to_frd" class="reveal-modal tmpl_login_frm_data clearfix" data-reveal>	
    <form name="send_to_frnd" id="send_to_frnd" action="#" method="post">     
        <input type="hidden" id="send_post_id" name="post_id" value="<?php echo $post->ID;?>"/>
        <input type="hidden" id="link_url" name="link_url" value="<?php	the_permalink();?>"/>
        <input type="hidden" id="send_to_Frnd_pid" name="pid" />
        <input type="hidden" name="sendact" value="email_frnd" />
        <div class="email_to_friend">
            <h3 class="h3"><?php _e('Send To Friend','templatic');?></h3>
            <a class="modal_close" href="javascript:;"></a>
         </div>
                
        <div class="form_row clearfix" >
        	<label><?php _e('Friend&rsquo;s name','templatic');?>: <span class="indicates">*</span></label> 
            <input name="to_name_friend" id="to_name_friend" type="text"  />
            <span id="to_name_friendInfo"></span>
		</div>
            
		<div class="form_row clearfix" >
        	<label> <?php _e('Friend&rsquo;s email','templatic');?>: <span class="indicates">*</span></label> 
            <input name="to_friend_email" id="to_friend_email" type="text"  value=""/>
            <span id="to_friend_emailInfo"></span>
		</div>
        <div class="form_row clearfix" >
        	<label><?php _e('Your name','templatic');?>: <span class="indicates">*</span></label> 
            <input name="yourname" id="yourname" type="text"  />
            <span id="yournameInfo"></span>
		</div>
        <div class="form_row clearfix" >
        	<label> <?php _e('Your email','templatic');?>: <span class="indicates">*</span></label> 
            <input name="youremail" id="youremail" type="text"  />
            <span id="youremailInfo"></span>
		</div>                
		<div class="form_row clearfix" >
        	<label><?php _e('Subject','templatic');?>: </label> 
            <input name="frnd_subject" value="<?php if(isset($tmpdata['mail_friend_sub'])){_e($tmpdata['mail_friend_sub'],'templatic');}else{ _e('Check out this post','templatic');} ?>" id="frnd_subject" type="text"  />
		</div>
        <div class="form_row clearfix" >
        	<label><?php _e('Comments','templatic');?>: </label> 
            <textarea name="frnd_comments" id="frnd_comments" cols="10" rows="5" ><?php _e('Hello, I just stumbled upon this listing and thought you might like it. Just check it out.','templatic'); ?></textarea>
		</div>
		<?php		
		if(is_array($display) && !empty($display) && @in_array('emaitofrd', $display))
		{		
			echo '<div id="snd_frnd_cap"></div>';
		} 
		?>
        <div class="send_info_button clearfix">
            <input name="Send"  type="submit" value="<?php _e('Send','templatic')?> " class="button send_button" />
            <span id="process_send_friend" style="display:none;"><i class="fa fa-circle-o-notch fa-spin"></i></span>
            <strong id="send_friend_msg" class="process_state"></strong>
        </div>
      
                
    </form>
</div>