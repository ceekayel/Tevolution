jQuery.noConflict();
jQuery("#send_friend_id").leanModal({ top : 200, overlay : 0.4, closeButton: ".modal_close" });
jQuery.noConflict();
jQuery("#send_inquiry_id").leanModal({ top : 200, overlay : 0.4, closeButton: ".modal_close" });
/* Send to inquiry */
var captcha ='';
jQuery(function() {
	jQuery('a[rel*=leanModal_send_inquiry]').leanModal({ top : 200, closeButton: ".modal_close" });		
	jQuery('a[rel*=leanModal_send_inquiry]').click(function(){
		if(RECAPTCHA_COMMENT == 1)
		{
			captcha = jQuery('#recaptcha_widget_div').html();
			jQuery('#recaptcha_widget_div').html('');
		}else
		{
			captcha = jQuery('#owner_frm').val();
			jQuery('#owner_frm').html('');
		}
		jQuery('#inquiry_frm_popup').html(captcha);
		jQuery('#myrecap').html('');
		jQuery('#snd_frnd_cap').html('');
		jQuery('#claim_ship_cap').html('');
	});
});
/* Send to friend */
jQuery(function() {
	jQuery('a[rel*=leanModal_email_friend]').leanModal({ top : 200, closeButton: ".modal_close" });
	jQuery('a[rel*=leanModal_email_friend]').click(function(){
		if(RECAPTCHA_COMMENT == 1)
		{
			captcha = jQuery('#recaptcha_widget_div').html();
			jQuery('#recaptcha_widget_div').html('');
		}else
		{
			captcha = jQuery('#owner_frm').val();
			jQuery('#owner_frm').html('');
		}
		jQuery('#snd_frnd_cap').html(captcha);
		jQuery('#myrecap').html('');
		jQuery('#recaptcha_widget_div').html('');
		jQuery('#claim_ship_cap').html('');
	}); 
});
/*Claim Ownership */
jQuery.noConflict();
jQuery("#trigger_id").leanModal({ top : 200, overlay : 0.4, closeButton: ".modal_close" });
jQuery(function() {
	jQuery('a#trigger_id').leanModal({ top : 200, closeButton: ".modal_close" });		
	jQuery('a.i_claim').click(function(){
		if(RECAPTCHA_COMMENT == 1)
		{
			captcha = jQuery('#recaptcha_widget_div').html();
			jQuery('#recaptcha_widget_div').html('');
		}else
		{
			captcha = jQuery('#owner_frm').val();
			jQuery('#owner_frm').html('');
		}
		jQuery('#claim_ship_cap').html(captcha);
		jQuery('#myrecap').html('');
		jQuery('#inquiry_frm_popup').html('');
	}); 
});
jQuery(function() {
jQuery("#lean_overlay").click(function(){if(captcha) {jQuery('#recaptcha_widget_div').html(captcha); }});
jQuery(".modal_close").click(function(){if(captcha) {jQuery('#recaptcha_widget_div').html(captcha);}});
});