<?php
/*
 * html form for claim ownership
 */
?>
<div id="tmpl_claim_listing" class="reveal-modal tmpl_login_frm_data clearfix"  style="display:none;" data-reveal>
	<div class="claim_ownership">
	<?php global $wp_query,$current_user,$claimpost;
	if($claimpost == '')
	{
		global $post;
		$post = $post;
	} else {
	 $post = $claimpost; } 
	 if($post->post_type){ $post_type = $post->post_type; }else{ $post_type='post'; }
	 ?>
	<div id="claim-header" class="claim-header">
		<h4 class="h4"><?php _e('Verify ownership of','templatic'); echo "&nbsp;<strong>".$post->post_title."</strong>";?></h4>
		<p id="reply_send_success" class="success_msg" style="display:none;"></p>
		<a class="modal_close" href="javascript:;"></a>
	</div>
	<?php do_action('tmpl_before_claim_form'); ?> 
	<form name="claim_listing_frm" id="claim_listing_frm" action="<?php echo the_permalink($post->ID); ?>" method="post">
		<input type="hidden" id="claim_post_id" name="post_id" value="<?php echo $post->ID; ?>"/>
		<?php do_action('tmpl_inside_claim_form'); ?>	
		<input type="hidden" id="request_uri" name="request_uri" value="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>"/>
		<input type="hidden" id="link_url" name="link_url" value="<?php	echo get_permalink($post->ID); ?>"/>
		<input type="hidden" name="claimer_id" id="claimer_id" value="<?php if(is_user_logged_in()) { echo get_current_user_id(); } else { ?>0<?php } ?>" />
		<input type="hidden" name="claimer_name_already_exist" id="claimer_name_already_exist" value="" />
		<input type="hidden" name="claimer_email_already_exist" id="claimer_email_already_exist" value="" />
		<input type="hidden" id="author_id" name="author_id" value="<?php echo $post->post_author; ?>" />
		<input type="hidden" id="post_title" name="post_title" value="<?php echo $post->post_title; ?>" />
		<input type="hidden" id="claim_status" name="claim_status" value="pending"/>
		<input type="hidden" id="claimer_ip" name="claimer_ip" value="<?php echo $_SERVER["REMOTE_ADDR"]; ?>"/>
		
		<div class="form_row clearfix"><label><?php _e('Username','templatic');?><span class="indicates">*</span></label> <input name="claimer_name" id="claimer_name" type="text" <?php if($current_user->ID != '') {?>	value="<?php echo $current_user->user_login; ?>" readonly="readonly" <?php } ?>  autofocus="autofocus"/><span id="claimer_nameInfo"></span></div>
		<div class="form_row clearfix"><label> <?php _e('Your Email','templatic');?><span class="indicates">*</span></label> <input name="claimer_email" id="claimer_email" <?php if($current_user->ID != '') {?>	value="<?php echo $current_user->user_email; ?>" readonly="readonly" <?php } ?> type="text"  /><span id="claimer_emailInfo"></span></div>
		<div class="form_row clearfix"><label> <?php _e('Contact No','templatic');?></label> <input name="claimer_contact" id="claimer_contact" type="text"  /></div>
		<div class="form_row clearfix"><label><?php _e('Your Claim','templatic');?><span class="indicates">*</span></label> <textarea name="claim_msg" id="claim_msg" cols="10" rows="5" ><?php _e('Hello, I would like to notify you that I am the owner of this listing. I would like to verify its authenticity.','templatic'); ?></textarea><span id="claim_msgInfo"></span></div>
		<div id="claim_ship_cap"></div>
		<?php do_action('tmpl_before_claim_submit'); ?>
		<div class="send_info_button clearfix">
          	<input name="Send" class="send_button" id="paynow" type="submit" value="<?php apply_filters('tmpl_submit_claim_button',_e('Submit','templatic'))?> " />
                <span id="process_claimownership" style="display:none;"><i class="fa fa-circle-o-notch fa-spin"></i></span>
              	 <strong id="claimownership_msg" class="process_state"></strong>
          </div>
	</form>
	<?php do_action('tmpl_after_claim_form'); ?>
	</div>
</div>