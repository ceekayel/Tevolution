<?php global $post,$wp_query;
$userdata = get_userdata($post->post_author);
$tmpdata = get_option('templatic_settings');
?>  
<div id="tmpl_send_inquiry" class="reveal-modal tmpl_login_frm_data clearfix" style="display:none;" data-reveal>
    <form name="inquiry_frm" id="inquiry_frm" action="#" method="post"> 
        <input type="hidden" id="listing_id" name="listing_id" value="<?php _e($post->ID,'templatic'); ?>"/>
        <input type="hidden" id="request_uri" name="request_uri" value="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>"/>
        <input type="hidden" id="link_url" name="link_url" value="<?php	the_permalink();?>"/>
        
        <input type="hidden" name="to_name" id="to_name" value="<?php _e($userdata->display_name,'templatic');?>" />
        <div class="email_to_friend">
        	<h3 class="h3"><?php _e("Inquiry for",'templatic'); echo '<br/>';_e(stripslashes($post->post_title),'templatic'); ?></h3>
        	<a class="modal_close" href="javascript:;"></a>
    	</div>         
            <div class="form_row clearfix" ><label><?php _e('Full name','templatic'); ?>: <span class="indicates">*</span></label> <input name="full_name" id="full_name" type="text"  /><span id="full_nameInfo"></span></div>
        
            <div class="form_row clearfix" ><label> <?php _e('Your email','templatic'); ?>: <span class="indicates">*</span></label> <input name="your_iemail" id="your_iemail" type="text"  /><span id="your_iemailInfo"></span></div>
            
            <div class="form_row clearfix" ><label> <?php _e('Contact number','templatic'); ?>: </label> <input name="contact_number" id="contact_number" type="text"  /><span id="contact_numberInfo"></span></div>	
            
            <div class="form_row clearfix" ><label> <?php _e('Subject','templatic'); ?>: <span class="indicates">*</span></label>
            <input name="inq_subject" id="inq_subject" type="text"  value="<?php if(isset($tmpdata['send_inquirey_email_sub'])){ _e(stripslashes($tmpdata['send_inquirey_email_sub']),'templatic');}else{ _e('Inquiry email','templatic');}?>" />
            <span id="inq_subInfo"></span></div>
            <div class="form_row  clearfix" ><label> <?php _e(' Message','templatic'); ?>: <span class="indicates">*</span></label> 
				<textarea rows="5" name="inq_msg" id="inq_msg"><?php 
					$msg =_e('Hello, I would like to inquire more about this listing. Please let me know how can I get in touch with you. Waiting for your prompt reply?','templatic');
					if(function_exists('icl_register_string')){
						icl_register_string('templatic',$msg,$msg);
					}
					
					if(function_exists('icl_t')){
						$message1 = icl_t('templatic',$msg,$msg);
					}else{
						$message1 = __($msg,'templatic'); 
					}
					echo $message1;
				?></textarea><span id="inq_msgInfo"></span></div>
				<?php
                $tmpdata = get_option('templatic_settings');
				$display = @$tmpdata['user_verification_page'];
				if(is_array($display) && !empty($display) && @in_array('sendinquiry', $display))
				{
                ?>
					<div id="inquiry_frm_popup"></div>
			<?php } ?>
            <div class="send_info_button clearfix" >
            	<input name="Send" type="submit" value="<?php _e('Send','templatic'); ?>" class="button send_button" />
               <span id="process_state" style="display:none;"><i class="fa fa-circle-o-notch fa-spin"></i></span>
              	<strong id="send_inquiry_msg" class="process_state"></strong>
		  </div>
    </form>
</div>