/*
 * custom fields, and other backend related js
 */
jQuery(document).ready(function(){  
    jQuery('#the-list').on('click', '.editinline', function(){  		
		var tag_id = jQuery(this).closest('tr').attr('id');   		
		var cat_price = jQuery('.price', '#'+tag_id).text().substr(1);
		jQuery('input[name="cat_price"]', '.inline-edit-row').val(cat_price);
	}); 
														   
	jQuery('.advance_is_search,#search_ctype').on('change',function(){
			
			if (jQuery(".advance_is_search ").is(':checked')){
				jQuery("#option_search_ctype").css('display','block');
			}else{
				jQuery("#min_max_range_option").css('display','none');
				jQuery("#search_select_title").css('display','none');
				jQuery("#search_select_value").css('display','none');
				jQuery("#option_search_ctype").css('display','none');
			}
			if(jQuery('#search_ctype').val()=='slider_range' && jQuery(".advance_is_search ").is(':checked')){
				jQuery("#min_max_range_option").css('display','block');
				jQuery("#min_max_description").css('display','none');
				jQuery("#slider_range_description").css('display','block');
				
				jQuery("#search_min_select_title").css('display','none');
				jQuery("#search_min_select_value").css('display','none');
				jQuery("#search_max_select_title").css('display','none');
				jQuery("#search_max_select_value").css('display','none');
			}else if(jQuery('#search_ctype').val()=='min_max_range' && jQuery(".advance_is_search ").is(':checked')){
				jQuery("#min_max_description").css('display','block');
				jQuery("#min_max_range_option").css('display','none');
				jQuery("#slider_range_description").css('display','none');
				
				jQuery("#search_min_select_title").css('display','none');
				jQuery("#search_min_select_value").css('display','none');
				jQuery("#search_max_select_title").css('display','none');
				jQuery("#search_max_select_value").css('display','none');
			}else if(jQuery('#search_ctype').val()=='min_max_range_select' && jQuery(".advance_is_search ").is(':checked')){
				jQuery("#min_max_description").css('display','none');
				jQuery("#min_max_range_option").css('display','none');
				jQuery("#slider_range_description").css('display','none');
				
				jQuery("#search_min_select_title").css('display','block');
				jQuery("#search_min_select_value").css('display','block');
				jQuery("#search_max_select_title").css('display','block');
				jQuery("#search_max_select_value").css('display','block');
			}else{
				jQuery("#min_max_description").css('display','none');
				jQuery("#slider_range_description").css('display','none');
				jQuery("#min_max_range_option").css('display','none');
				
				jQuery("#search_min_select_title").css('display','none');
				jQuery("#search_min_select_value").css('display','none');
				jQuery("#search_max_select_title").css('display','none');
				jQuery("#search_max_select_value").css('display','none');
			}
			
			if(jQuery('#search_ctype').val()=='select' && jQuery('#ctype').val()=='text'){
				jQuery("#search_select_value").css('display','block');
				jQuery("#search_select_title").css('display','block');
			}else{
				jQuery("#search_select_value").css('display','none');
				jQuery("#search_select_title").css('display','none');
			}

			//jQuery("#min_max_range_option").css('display','none');
			//jQuery("#option_search_ctype").css('display','none');
	});

	if(jQuery('#form_table #ctype').val() =='upload' || jQuery('#form_table #ctype').val() == 'image_uploader' || jQuery('#form_table #ctype').val() == 'oembed_video'){
		jQuery("#form_table #show_on_listing").removeAttr('checked');
		jQuery("#form_table #show_on_listing").attr('disabled', 'disabled');
	}else{
		jQuery("#form_table #show_on_listing").removeAttr('disabled');
	}	
	
	jQuery('#ctype,.advance_is_search').on('change',function(){
		var value=jQuery('#ctype').val();
		if(jQuery(".advance_is_search").is(':checked')){
			ShowHideSearch_ctypeOption(value);
		}else{
			jQuery("#option_search_ctype").css('display','none');
			jQuery("#search_select_title").css('display','none');
			jQuery("#search_select_value").css('display','none');
		}
		if(value=='upload' || value == 'image_uploader' || value == 'oembed_video' || value == 'geo_map'){			
			jQuery('#default_value').attr('placeholder','http://www.xyz.com/image/image.jpg');
			jQuery("#form_table #show_on_listing").removeAttr('checked');
			jQuery("#form_table #show_on_listing").attr('disabled', 'disabled');
		}
		

		jQuery('select#validation_type option').each(function(){			
			if(value=='texteditor' && (jQuery(this).val()=='phone_no' || jQuery(this).val()=='digit' || jQuery(this).val()=='email')){
				jQuery(this).prop('disabled', true);
			}else{
				jQuery(this).prop('disabled', false);
			}
		});
		
	});
});  

/* disable search ctype option value according custom field type according*/
function ShowHideSearch_ctypeOption(value){		
	
		if(value=='text' || value=='textarea' || value=='texteditor' || value=='geo_map'){
			jQuery("#option_search_ctype").css('display','block');
			jQuery("#min_max_range_option").css('display','none');
			jQuery('select#search_ctype option').each(function(){				
				if(jQuery(this).val()=='text' || jQuery(this).val()=='min_max_range' || jQuery(this).val()=='min_max_range_select' || (value=='text' && jQuery(this).val()=='select') ){					
					jQuery(this).prop('disabled', false);
				}else{
					jQuery(this).prop('disabled', true);
				}
				if(jQuery(this).val()==''){
					jQuery(this).prop('disabled', false);
				}
				
				if(value=='text' && jQuery('select#search_ctype :selected').val()=='select'){
					jQuery("#search_select_value").css('display','block');
					jQuery("#search_select_title").css('display','block');
				}else{
					jQuery("#search_select_value").css('display','none');
					jQuery("#search_select_title").css('display','none');
				}
				
				if(value=='text' && jQuery('select#search_ctype :selected').val()=='min_max_range_select'){
					jQuery("#search_min_select_title").css('display','block');
					jQuery("#search_min_select_value").css('display','block');
					jQuery("#search_max_select_title").css('display','block');
					jQuery("#search_max_select_value").css('display','block');
				}else{
					jQuery("#search_min_select_title").css('display','none');
					jQuery("#search_min_select_value").css('display','none');
					jQuery("#search_max_select_title").css('display','none');
					jQuery("#search_max_select_value").css('display','none');
				}
			});			
		}else if(value=='multicheckbox' || value=='radio' || value=='select'){
			jQuery("#option_search_ctype").css('display','block');
			jQuery("#min_max_range_option").css('display','none');
			
			if(!jQuery('#cf_msg').length >0){
				jQuery("#search_ctype").after("<p id='cf_msg' class='description'>Option titles and values for this field will be same as you mentioned above in the Option Title and Option Values fields.</p>");
			}
			jQuery('select#search_ctype option').each(function(){				
				if(jQuery(this).val()=='multicheckbox' || jQuery(this).val()=='radio' || jQuery(this).val()=='select' || jQuery(this).val()=='text' ){
					jQuery(this).prop('disabled', false);
				}else{
					jQuery(this).prop('disabled', true);
				}
				if(jQuery(this).val()==''){
					jQuery(this).prop('disabled', false);
				}				
			});		
		}else if(value=='range_type'){			
			jQuery("#option_search_ctype").css('display','block');
			jQuery("#min_max_range_option").css('display','none');			
			jQuery('select#search_ctype option').each(function(){				
				if(jQuery('select#search_ctype :selected').val()=='slider_range'){					
					jQuery("#min_max_range_option").css('display','block');
				}
				
				if(jQuery(this).val()=='min_max_range' || jQuery(this).val()=='slider_range' || jQuery(this).val()=='min_max_range_select' ){
					jQuery(this).prop('disabled', false);
				}else{
					jQuery(this).prop('disabled', true);
				}
				if(jQuery(this).val()==''){
					jQuery(this).prop('disabled', false);
				}
				if(value=='range_type' && jQuery('select#search_ctype :selected').val()=='min_max_range_select'){
					jQuery("#search_min_select_title").css('display','block');
					jQuery("#search_min_select_value").css('display','block');
					jQuery("#search_max_select_title").css('display','block');
					jQuery("#search_max_select_value").css('display','block');
				}else{
					jQuery("#search_min_select_title").css('display','none');
					jQuery("#search_min_select_value").css('display','none');
					jQuery("#search_max_select_title").css('display','none');
					jQuery("#search_max_select_value").css('display','none');
				}
			});		
			
		}else if(value=='date' ){
			jQuery("#option_search_ctype").css('display','block');
			jQuery("#min_max_range_option").css('display','none');
			jQuery('select#search_ctype option').each(function(){				
				if(jQuery(this).val()=='date'){					
					jQuery(this).prop('disabled', false);
				}else{
					jQuery(this).prop('disabled', true);
				}
				if(jQuery(this).val()==''){
					jQuery(this).prop('disabled', false);
				}				
			});		
			
		}else{
			jQuery("#option_search_ctype").css('display','none');
			jQuery("#min_max_range_option").css('display','none');
		}
	
}

jQuery(document).ready(function() {
	jQuery('.subsubsub a.tab').click(function(e){	
		e.preventDefault();	
		jQuery( this ).parents( '.subsubsub' ).find( '.current' ).removeClass( 'current' );
 		jQuery( this ).addClass( 'current' );
		// If "All" is clicked, show all.
 			if ( jQuery( this ).hasClass( 'all' ) ) {
 				jQuery( '#wpbody-content .widgets-holder-wrap' ).show();
 				jQuery( '#wpbody-content .widgets-holder-wrap .widget' ).show();
 				
 				return false;
 			}
 			// If "Updates Available" is clicked, show only those with updates.
 			if ( jQuery( this ).hasClass( 'has-upgrade' ) ) {
 				jQuery( '#wpbody-content .widget_div' ).hide();
 				jQuery( '#wpbody-content .widget_div.has-upgrade' ).show();
 				jQuery( '.widgets-holder-wrap' ).each( function ( i ) {
 					if ( ! jQuery( this ).find( '.has-upgrade' ).length ) {
 						jQuery( this ).hide();
 					} else {
 						jQuery( this ).show();
 					}
 				});
 				
 				return false;
 			} else {
 				jQuery( '#wpbody-content .widget_div' ).show(); // Restore all widgets.
 			}
 			
 			// If the link is a tab, show only the specified tab.
 			var toShow = jQuery( this ).attr( 'href' );	
			
 			jQuery( '.widgets-holder-wrap:not(' + toShow + ')' ).hide();
 			jQuery( '.widgets-holder-wrap' + toShow ).show();
 			
 			return false;
	});
	jQuery( '#wpbody-content .open-close-all a' ).click( function ( e ) {
 			var status = 'closed';
 			
 			if ( jQuery( this ).attr( 'href' ) == '#open-all' ) {
 				status = 'open';
 			}
 			
 			var components = [];
	 		jQuery( '#wpbody-content .widget_div' ).each( function ( i ) {
	 			var obj = jQuery( this );
	 			var componentToken = obj.attr( 'id' ).replace( '#', '' );
	 			components.push( componentToken );
	 			
	 			if ( status == 'open' ) {
		 			obj.addClass( 'open' ).removeClass( 'closed' );
		 		} else {
		 			obj.addClass( 'closed' ).removeClass( 'open' );
		 		}
	 		});	
	 		
 			return false;
 		});
	/**
	* Jquery for quick editing Email settings: Start
	**/
	jQuery('.buttons .quick_save').click(function(){
		jQuery('.buttons .spinner').css({'display':'block'});
		/**
		* If editor is in visual mode then set value to text area first then serialize form: Start
		**/
		if(jQuery("#mail_friend_description").css("display") == "none"){
			jQuery("#mail_friend_description").val(tinyMCE.get('mail_friend_description').getContent());
		}
		if(jQuery("#send_inquirey_email_description").css("display") == "none"){
			jQuery("#send_inquirey_email_description").val(tinyMCE.get('send_inquirey_email_description').getContent());
		}
		if(jQuery("#registration_success_email_content").css("display") == "none"){
			jQuery("#registration_success_email_content").val(tinyMCE.get('registration_success_email_content').getContent());
		}
		if(jQuery("#admin_registration_success_email_content").css("display") == "none"){
			jQuery("#admin_registration_success_email_content").val(tinyMCE.get('admin_registration_success_email_content').getContent());
		}
		if(jQuery("#post_submited_success_email_content").css("display") == "none"){
			jQuery("#post_submited_success_email_content").val(tinyMCE.get('post_submited_success_email_content').getContent());
		}
		if(jQuery("#payment_success_email_content_to_client").css("display") == "none"){
			jQuery("#payment_success_email_content_to_client").val(tinyMCE.get('payment_success_email_content_to_client').getContent());
		}
		if(jQuery("#user_post_submited_success_email_content").css("display") == "none"){
			jQuery("#user_post_submited_success_email_content").val(tinyMCE.get('user_post_submited_success_email_content').getContent());
		}
		if(jQuery("#payment_success_email_content_to_admin").css("display") == "none"){
			jQuery("#payment_success_email_content_to_admin").val(tinyMCE.get('payment_success_email_content_to_admin').getContent());
		}
		if(jQuery("#post_added_success_msg_content").css("display") == "none"){
			jQuery("#post_added_success_msg_content").val(tinyMCE.get('post_added_success_msg_content').getContent());
		}
		if(jQuery("#post_payment_success_msg_content").css("display") == "none"){
			jQuery("#post_payment_success_msg_content").val(tinyMCE.get('post_payment_success_msg_content').getContent());
		}
		if(jQuery("#post_payment_cancel_msg_content").css("display") == "none"){
			jQuery("#post_payment_cancel_msg_content").val(tinyMCE.get('post_payment_cancel_msg_content').getContent());
		}
		if(jQuery("#post_pre_bank_trasfer_msg_content").css("display") == "none"){
			jQuery("#post_pre_bank_trasfer_msg_content").val(tinyMCE.get('post_pre_bank_trasfer_msg_content').getContent());
		}
		if(jQuery("#pre_payment_success_email_content_to_admin").css("display") == "none"){
			jQuery("#pre_payment_success_email_content_to_admin").val(tinyMCE.get('pre_payment_success_email_content_to_admin').getContent());
		}
		if(jQuery("#contact_us_email_content").css("display") == "none"){
			jQuery("#contact_us_email_content").val(tinyMCE.get('contact_us_email_content').getContent());
		}
		if(jQuery("#admin_post_upgrade_email_content").css("display") == "none"){
			jQuery("#admin_post_upgrade_email_content").val(tinyMCE.get('admin_post_upgrade_email_content').getContent());
		}
		if(jQuery("#client_post_upgrade_email_content").css("display") == "none"){
			jQuery("#client_post_upgrade_email_content").val(tinyMCE.get('client_post_upgrade_email_content').getContent());
		}
		if(jQuery("#reset_password_content").css("display") == "none"){
			jQuery("#reset_password_content").val(tinyMCE.get('reset_password_content').getContent());
		}
		if(jQuery("#claim_ownership_content").css("display") == "none"){
			jQuery("#claim_ownership_content").val(tinyMCE.get('claim_ownership_content').getContent());
		}
		if(jQuery("#listing_expiration_content").css("display") == "none"){
			jQuery("#listing_expiration_content").val(tinyMCE.get('listing_expiration_content').getContent());
		}
		if(jQuery("#payment_cancelled_content").css("display") == "none"){
			jQuery("#payment_cancelled_content").val(tinyMCE.get('payment_cancelled_content').getContent());
		}
		if(jQuery("#update_listing_notification_content").css("display") == "none"){
			jQuery("#update_listing_notification_content").val(tinyMCE.get('update_listing_notification_content').getContent());
		}
		if(jQuery("#renew_listing_notification_content").css("display") == "none"){
			jQuery("#renew_listing_notification_content").val(tinyMCE.get('renew_listing_notification_content').getContent());
		}
		if(jQuery("#pending_listing_notification_content").css("display") == "none"){
			jQuery("#pending_listing_notification_content").val(tinyMCE.get('pending_listing_notification_content').getContent());
		}
		/**
		* If editor is in visual mode then set value to text area first then serialize form: End
		**/
		
		//Serialize form data
		var form_data = jQuery('.form_style').serialize();
		jQuery.ajax({
			url:ajaxurl,
			type:'POST',
			dataType: 'json',
			data:'action=save_email_data&' + form_data,
			success:function(results) {
				jQuery('.buttons .spinner').css({'display':'none'});
				jQuery('.email-table .save_error').css({'display':'block', 'color': 'green', 'float': 'left', 'margin-top': '24px'});
				
				/* Show hide email to friend tr*/
				jQuery('.edit-email-to-friend').css({'display':'none'});
				jQuery('.email-to-friend').css({'display':'table-row'});
				/* Show hide email to friend tr*/
				
				/* Show hide inquiry tr*/
				jQuery('.edit-inquiry-email').css({'display':'none'});
				jQuery('.inquiry-email').css({'display':'table-row'});
				/* Show hide inquiry tr*/
				
				/* Show hide registration email tr*/
				jQuery('.edit-registration-email').css({'display':'none'});
				jQuery('.registration-email').css({'display':'table-row'});
				/* Show hide registration email tr*/
				
				/* Show hide admin registration email tr*/
				jQuery('.edit-admin-registration-email').css({'display':'none'});
				jQuery('.admin-registration-email').css({'display':'table-row'});
				/* Show hide admin registration email tr*/
				
				/* Show hide registration email tr*/
				jQuery('.edit-post-submission').css({'display':'none'});
				jQuery('.post-submission').css({'display':'table-row'});
				/* Show hide registration email tr*/
				
				/* Show hide registration email tr*/
				jQuery('.user-edit-post-submission').css({'display':'none'});
				jQuery('.user-post-submission').css({'display':'table-row'});
				/* Show hide registration email tr*/
				
				/* Show hide registration email tr*/
				jQuery('.edit-payment-success-client').css({'display':'none'});
				jQuery('.payment-success-client').css({'display':'table-row'});
				/* Show hide registration email tr*/
				
				/* Show hide registration email tr*/
				jQuery('.edit-payment-success-admin').css({'display':'none'});
				jQuery('.payment-success-admin').css({'display':'table-row'});
				/* Show hide registration email tr*/
				
				/* Show hide registration email tr*/
				jQuery('.edit-post-submission-not').css({'display':'none'});
				jQuery('.post-submission-not').css({'display':'table-row'});
				/* Show hide registration email tr*/
				
				/* Show hide registration email tr*/
				jQuery('.edit-payment-successful').css({'display':'none'});
				jQuery('.payment-successful').css({'display':'table-row'});
				/* Show hide registration email tr*/
				
				/* Show hide registration email tr*/
				jQuery('.edit-payment-cancel').css({'display':'none'});
				jQuery('.payment-cancel').css({'display':'table-row'});
				/* Show hide registration email tr*/
				
				/* Show hide registration email tr*/
				jQuery('.edit-prebank-transfer').css({'display':'none'});
				jQuery('.prebank-transfer').css({'display':'table-row'});
				/* Show hide registration email tr*/
				
				/* Show hide registration email tr*/
				jQuery('.edit-pre-payment-success-admin').css({'display':'none'});
				jQuery('.pre-payment-success-admin').css({'display':'table-row'});
				/* Show hide registration email tr*/
				
				/* Show hide registration email tr*/
				jQuery('.edit-contact-us').css({'display':'none'});
				jQuery('.contact-us').css({'display':'table-row'});
				/* Show hide registration email tr*/
				
				/* Show hide registration email tr*/
				jQuery('.edit-admin-post-upgrade').css({'display':'none'});
				jQuery('.admin-post-upgrade').css({'display':'table-row'});
				/* Show hide registration email tr*/
				
				/* Show hide registration email tr*/
				jQuery('.edit-client-post-upgrade').css({'display':'none'});
				jQuery('.client-post-upgrade').css({'display':'table-row'});
				/* Show hide registration email tr*/
				
				/* Show hide registration email tr*/
				jQuery('.edit-reset-password').css({'display':'none'});
				jQuery('.reset-password').css({'display':'table-row'});
				/* Show hide registration email tr*/
				
				/* Show hide registration email tr*/
				jQuery('.edit-claim-ownership').css({'display':'none'});
				jQuery('.claim-ownership').css({'display':'table-row'});
				
				
				/* Show hide listing expiration email tr*/				
				jQuery('.edit-listing-expiration').css({'display':'none'});
				jQuery('.listing-expiration').css({'display':'table-row'});
				
				/* Show hide Payment cancle email tr*/				
				jQuery('.edit-payment-cancelled').css({'display':'none'});
				jQuery('.payment-cancelled').css({'display':'table-row'});
				
				/* Show hide update listing notification email tr*/				
				jQuery('.edit-update-listing-notification').css({'display':'none'});
				jQuery('.update-listing-notification').css({'display':'table-row'});
				
				/* Show hide renew listing notification email tr*/				
				jQuery('.edit-renew-listing-notification').css({'display':'none'});
				jQuery('.renew-listing-notification').css({'display':'table-row'});
				
				/* Show hide pending listing notification email tr*/				
				jQuery('.edit-pending-listing-notification').css({'display':'none'});
				jQuery('.pending-listing-notification').css({'display':'table-row'});
				
				/* Display changes instantly */
				//alert(results.action);
				
				//var r = jQuery.parseJSON(results);
				jQuery.each(results, function(key, value) {//alert(key);
					jQuery('.'+key).html(value);
				});
				/* Display changes instantly*/
			}
		});
	});
	
});
//Hide show tr for quick edit
function open_quick_edit(tr_hide,tr_show){
	jQuery.noConflict();
	var tr_hide = '.'+tr_hide;
	var tr_show = '.'+tr_show;
	jQuery(tr_hide).css({'display':'none'});
	jQuery(tr_show).css({'display':'table-row'});
}
//Reset to default value in email settings
function reset_to_default(subject,message,spinner){
	jQuery.noConflict();
	jQuery('.'+spinner+' .spinner').css({'display':'block'});
	var subject = subject;
	var message = message;
	var subjectstring = '';
	var msgstring = '';
	var datastring = '';
	if(subject!=""){
		subjectstring = '&subject='+subject;
	}
	if(message!=""){
		msgstring = '&message='+message;
	}
	datastring = subjectstring+msgstring;
	jQuery.ajax({
		url:ajaxurl,
		type:'POST',
		data:'action=reset_email_data' + datastring,
		success:function(results) {
			jQuery('.'+spinner+' .spinner').css({'display':'none'});
			jQuery('.'+spinner+' .qucik_reset').css({'display':'block'});
			jQuery('.'+spinner+' .qucik_reset').delay(2000).fadeOut();
			var r = jQuery.parseJSON(results);
			jQuery.each(r[0], function(key, value) {
				jQuery('#'+key).val(value);
				if(jQuery("#"+key).css("display") == "none"){
					tinyMCE.get(key).setContent(value);
				}
			});
		}
	});
}
 
jQuery(document).ready(function() {
	jQuery('#tevolution_login').change(function(){	
                                        check_page_hase_shortcode('tevolution_login',this.value, 'tevolution_login', 'tevolution_login_page');
                         });
});
jQuery(document).ready(function() {
	jQuery('#tevolution_register').change(function(){	
                                        check_page_hase_shortcode('tevolution_register',this.value, 'tevolution_register', 'tevolution_register_page');
	});
});
jQuery(document).ready(function() {
	jQuery('#tevolution_profile').change(function(){
                                        check_page_hase_shortcode('tevolution_profile',this.value, 'tevolution_profile', 'tevolution_profile_page');
	});
});

/* common function for find the short code avaliable in page  */
function check_page_hase_shortcode(input_id,pageid, shortcode, validation_div,is_submit){
                         pageid = '&pageid='+ pageid;
                         shortcode = '&shortcode='+shortcode;
                        jQuery.ajax({
                                      url:ajaxurl,
                                      type:'POST',
                                      data:'action=check_page_has_shortcode' +pageid+  shortcode,
                                      success:function(results) {
                                           results = jQuery.parseJSON(results);
                                           if(results.result == '0'){
                                                jQuery('#'+validation_div).css('display','block');
                                                jQuery('#'+validation_div).fadeIn('slow');
                                                jQuery('#'+input_id).parent('td').parent('tr').addClass('form-invalid');
                                                jQuery('#input_'+input_id).val(0);
                                          }else{
                                                jQuery('#'+validation_div).css('display','none');
                                                jQuery('#'+input_id).parent('td').parent('tr').removeClass('form-invalid');
                                                jQuery('#input_'+input_id).val(1);
                                          }
                                      }
                         });
}

/*Custom Field Sorting */

jQuery('form#post_custom_fields table.tevolution_page_custom_setup tbody').sortable({ 
	items:'tr',
	//items:(jQuery('.paging-input .current-page').val() == 1) ? 'tr:gt(5)' :'tr',
	cursor:'move',
	axis:'y',
	handle: 'td',
	scrollSensitivity:40,
	helper:function(e,ui){
		ui.children().each(function(){
			jQuery(this).width(jQuery(this).width());
		});
		ui.css('left', '0');
		return ui;
	},
	start:function(event,ui){
		ui.item.css('background-color','#f6f6f6');
	},
	update: function(event, ui){
		//var custom_sort_order = jQuery("#post_custom_fields :input").serialize();
		var total_pages=jQuery('.paging-input span.total-pages').html();
		var paging_input=(total_pages!=1)? jQuery('.paging-input .current-page').val() : 0;		
		var post_type=(jQuery('#custom_filed_post_type').val())? jQuery('#custom_filed_post_type').val() : '';
		var custom_sort_order = jQuery('form#post_custom_fields table.tevolution_page_custom_setup :input').serialize();		
		jQuery.ajax({
				 url: ajaxurl,
				 type: 'POST',
				 data:'action=custom_field_sortorder&paging_input='+paging_input+'&' + custom_sort_order+'&post_type='+post_type,
				 success:function(result){
					 //alert(result)
				 }
			 });	
	},
	stop:function(event,ui){
		ui.item.removeAttr('style');
	}
});
/* payment Gateway sorting */
jQuery('.tevolution_paymentgatway table.tevolution_page_monetization tbody').sortable({
	items:'tr',
	//items:(jQuery('.paging-input .current-page').val() == 1) ? 'tr:gt(5)' :'tr',
	cursor:'move',
	axis:'y',
	handle: 'td',
	scrollSensitivity:40,
	helper:function(e,ui){
		ui.children().each(function(){
			jQuery(this).width(jQuery(this).width());
		});
		ui.css('left', '0');
		return ui;
	},
	start:function(event,ui){
		ui.item.css('background-color','#f6f6f6');
	},
	update: function(event, ui){
		//var custom_sort_order = jQuery("#post_custom_fields :input").serialize();
		var total_pages=jQuery('.paging-input span.total-pages').html();
		var paging_input=(total_pages!=1)? jQuery('.paging-input .current-page').val() : 0;
		var payment_sorder = jQuery('.tevolution_paymentgatway table.tevolution_page_monetization :input').serialize();		
		jQuery.ajax({
				 url: ajaxurl,
				 type: 'POST',
				 data:'action=paymentgateway_sortorder&paging_input='+paging_input+'&' + payment_sorder,		 
				 success:function(result){					
				 }
			 });	
	},
	stop:function(event,ui){
		ui.item.removeAttr('style');
	}
});
/* Price Package Sorting sorting */
jQuery('.tevolution_price_package table.tevolution_page_monetization tbody').sortable({
	items:'tr',
	//items:(jQuery('.paging-input .current-page').val() == 1) ? 'tr:gt(5)' :'tr',
	cursor:'move',
	axis:'y',
	handle: 'td:not(".package_link")',
	scrollSensitivity:40,
	helper:function(e,ui){
		ui.children().each(function(){
			jQuery(this).width(jQuery(this).width());
		});
		ui.css('left', '0');
		return ui;
	},
	start:function(event,ui){
		ui.item.css('background-color','#f6f6f6');
	},
	update: function(event, ui){
		//var custom_sort_order = jQuery("#post_custom_fields :input").serialize();
		var total_pages=jQuery('.paging-input span.total-pages').html();
		var paging_input=(total_pages!=1)? jQuery('.paging-input .current-page').val() : 0;
		var price_package_order = jQuery('.tevolution_price_package table.tevolution_page_monetization :input').serialize();		
		jQuery.ajax({
				 url: ajaxurl,
				 type: 'POST',
				 data:'action=price_package_order&paging_input='+paging_input+'&' + price_package_order,		 
				 success:function(result){					 
				 }
			 });	
	},
	stop:function(event,ui){
		ui.item.removeAttr('style');
	}
});
/* User Custom field */
jQuery('form#register_custom_fields table.tevolution_page_custom_setup tbody').sortable({
	items:'tr',	
	cursor:'move',
	axis:'y',
	handle: 'td',
	scrollSensitivity:40,
	helper:function(e,ui){
		ui.children().each(function(){
			jQuery(this).width(jQuery(this).width());
		});
		ui.css('left', '0');
		return ui;
	},
	start:function(event,ui){
		ui.item.css('background-color','#f6f6f6');
	},
	update: function(event, ui){
		//var custom_sort_order = jQuery("#post_custom_fields :input").serialize();
		var total_pages=jQuery('.paging-input span.total-pages').html();
		var paging_input=(total_pages!=1)? jQuery('.paging-input .current-page').val() : 0;
		var price_package_order = jQuery('form#register_custom_fields table.tevolution_page_custom_setup :input').serialize();		
		jQuery.ajax({
				 url: ajaxurl,
				 type: 'POST',
				 data:'action=user_customfield_sort&paging_input='+paging_input+'&' + price_package_order,		 
				 success:function(result){					 
				 }
			 });	
	},
	stop:function(event,ui){
		ui.item.removeAttr('style');
	}
});
/* License key popup window on load */
jQuery(document).ready(function() {
		var id = '#dialog';
	
		//Get the screen height and width
		var maskHeight = jQuery(document).height();
		var maskWidth = jQuery(window).width();
	
		//Set heigth and width to mask to fill up the whole screen
		jQuery('#mask').css({'width':maskWidth,'height':maskHeight});
		
		//transition effect		
		jQuery('#mask').fadeIn(500);	
		jQuery('#mask').fadeTo("slow",0.5);	
	
		//Get the window height and width
		var winH = jQuery(window).height();
		var winW = jQuery(window).width();
		    
		//Set the popup window to center
		jQuery(id).css('top',  winH/2-jQuery(id).height()/2);
		jQuery(id).css('left', winW/2-jQuery(id).width()/2);
	
		//transition effect
		jQuery(id).fadeIn(2000); 	
	
	//if close button is clicked
	jQuery('.window .close').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		
		jQuery('#mask').hide();
		jQuery('.window').hide();
	});		
	
	//if mask is clicked
	jQuery('#mask').click(function () {
		jQuery(this).hide();
		jQuery('.window').hide();
	});		
	
});

//
function chek_file()
{
	jQuery.noConflict();
	
	var csv_import = jQuery('#csv_import').val();
	var my_post_type = jQuery('input[name=my_post_type]:checked', '#bukl_upload_frm').val();
	var ext = csv_import.split('.').pop().toLowerCase();
	if(csv_import == ""){
		jQuery('#csv_import_id').addClass('form-invalid');
		jQuery('#csv_import').focus();
		jQuery('#status').html('Please select csv file to import');
		return false;
	}else if(csv_import != "" && ext != "csv" ){
		jQuery('#csv_import_id').addClass('form-invalid');
		jQuery('#csv_import').focus();
		jQuery('#status').html('Upload csv files only');
		return false;
	}else if(!confirm('Would you like to import data in "'+my_post_type+'" post type ?')){
		return false;
	}else{
		var file_size = jQuery("#csv_import")[0].files[0].size;
		var allowed_file_size = wp_max_upload_size;
		if(file_size > allowed_file_size){					
			jQuery('#csv_import_id').addClass('form-invalid');
			jQuery('#csv_import').focus();
			var file_sizes = new Array( 'KB', 'MB', 'GB' );		
			for ( var file_u = -1; file_size > 1024 && file_u < (file_sizes.length) - 1; file_u++ ) {
				file_size /= 1024;
			}
			if ( file_u < 0 ) {
				file_size = 0;
				file_u = 0;
			} else {
				file_size = Math.round(file_size);
			}
			jQuery('#status').css("display","none");
			jQuery('#csv_status').html("Csv file is too large. Maximum upload file size is "+upload_size_unit+ " "+ file_sizes[file_u] + ", uploaded file size is "+file_size+ " " +file_sizes[file_u]);
			return false;
		}else{
			jQuery('#csv_import_id').removeClass('form-invalid');
			jQuery('#status').html('');
			jQuery('#csv_status').html('');
			return true;
		}
	}
}
/*
*	TypeWatch 2.2
*
*	Examples/Docs: github.com/dennyferra/TypeWatch
*	
*  Copyright(c) 2013 
*	Denny Ferrassoli - dennyferra.com
*   Charles Christolini
*  
*  Dual licensed under the MIT and GPL licenses:
*  http://www.opensource.org/licenses/mit-license.php
*  http://www.gnu.org/licenses/gpl.html
*/
(function(jQuery) {
	
	jQuery.ajaxSetup({
			async: true
		});
	jQuery.fn.typeWatch = function(o) {		
		// The default input types that are supported
		var _supportedInputTypes =
			['TEXT', 'TEXTAREA', 'PASSWORD', 'TEL', 'SEARCH', 'URL', 'EMAIL', 'DATETIME', 'DATE', 'MONTH', 'WEEK', 'TIME', 'DATETIME-LOCAL', 'NUMBER', 'RANGE'];
		// Options
		var options = jQuery.extend({
			wait: 750,
			callback: function() { },
			highlight: true,
			captureLength: 2,
			inputTypes: _supportedInputTypes
		}, o);
		function checkElement(timer, override) {
			var value = jQuery(timer.el).val();
			// Fire if text >= options.captureLength AND text != saved text OR if override AND text >= options.captureLength
			if ((value.length >= options.captureLength && value.toUpperCase() != timer.text)
				|| (override && value.length >= options.captureLength))
			{
				timer.text = value.toUpperCase();
				timer.cb.call(timer.el, value);
			}
		};
		function watchElement(elem) {
			var elementType = elem.type.toUpperCase();
			if (jQuery.inArray(elementType, options.inputTypes) >= 0) {
				// Allocate timer element
				var timer = {
					timer: null,
					text: jQuery(elem).val().toUpperCase(),
					cb: options.callback,
					el: elem,
					wait: options.wait
				};
				// Set focus action (highlight)
				if (options.highlight) {
					jQuery(elem).focus(
						function() {
							this.select();
						});
				}
				// Key watcher / clear and reset the timer
				var startWatch = function(evt) {
					var timerWait = timer.wait;
					var overrideBool = false;
					var evtElementType = this.type.toUpperCase();
					// If enter key is pressed and not a TEXTAREA and matched inputTypes
					if (typeof evt.keyCode != 'undefined' && evt.keyCode == 13 && evtElementType != 'TEXTAREA' && jQuery.inArray(evtElementType, options.inputTypes) >= 0) {
						timerWait = 1;
						overrideBool = true;
					}
					var timerCallbackFx = function() {
						checkElement(timer, overrideBool)
					}
					// Clear timer					
					clearTimeout(timer.timer);
					timer.timer = setTimeout(timerCallbackFx, timerWait);
				};
				jQuery(elem).on('keydown paste cut input', startWatch);
			}
		};
		// Watch Each Element
		return this.each(function() {
			watchElement(this);
		});
	};
})(jQuery);
// JavaScript Document



/* Update tevolution custom fields heading post type */
jQuery('select.custom_field_heading_type').on('change',function(){
	var value=jQuery(this).val();
	var post_id=jQuery(this).attr('data-id');
	var post_type=(jQuery('#custom_filed_post_type').val())? jQuery('#custom_filed_post_type').val() : '';
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		data:'action=update_tevolution_custom_fields&heading_type='+encodeURIComponent(value)+'&post_id=' + post_id+"&post_type="+post_type,
		success:function(result){
			jQuery('span.heading_type_results').remove();
			jQuery('select.custom_field_heading_type_'+post_id).after('<span class="heading_type_results">'+result+'</span>');
			jQuery("span.heading_type_results").fadeIn('slow').delay(500).fadeOut('slow');
		}
	});	
});

var custom_field_sort_order=null
jQuery('input.custom_field_sort_order').on('keyup',function(e){
	var value=jQuery(this).val();
	var post_id=jQuery(this).attr('data-id');
	var post_type=(jQuery('#custom_filed_post_type').val())? jQuery('#custom_filed_post_type').val() : '';
	custom_field_sort_order=jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		data:'action=update_tevolution_custom_fields&sort_order='+value+'&post_id=' + post_id+"&post_type="+post_type,
		beforeSend : function(){
			if(custom_field_sort_order != null){
				custom_field_sort_order.abort();
			}
        },
		success:function(result){
			jQuery('span.sort_order_results').remove();
			jQuery('input.custom_field_sort_order_'+post_id).after('<span class="sort_order_results">'+result+'</span>');
			jQuery("span.sort_order_results").fadeIn('slow').delay(500).fadeOut('slow');
		}
	});	
});


/* custom field validation check form function  */
function chk_field_form()
{

	jQuery.noConflict();
	var field_title = jQuery('#admin_title').val();
	var html_var_title = jQuery('#htmlvar_name').val();
	var is_valid_html = jQuery('#is_valid_html').val();
	var sort_order = jQuery("#sort_order").val();
	var is_search = jQuery(".advance_is_search").is(":checked");
	var ctype = jQuery("#ctype").val();
	var search_ctype = jQuery("#search_ctype").val();
	var range_min_value = jQuery("#range_min_value").val();
	var range_max_value = jQuery("#range_max_value").val();
	var option_title = jQuery("#search_option_title").val();
	var option_values = jQuery("#search_option_values").val();
	
	var search_min_option_title = jQuery("#search_min_option_title").val();
	var search_min_option_values = jQuery("#search_min_option_values").val();
	var search_max_option_title = jQuery("#search_max_option_title").val();
	var search_max_option_values = jQuery("#search_max_option_values").val();
	
	var dml = document.forms['custom_fields_frm'];
	var chk = dml.elements['post_type_sel[]'];
	var len = dml.elements['post_type_sel[]'].length;
	var post_type_selected = '';
	for (i = 0; i < len; i++)
	{
		if(chk[i].checked == true)
		{
			post_type_selected = 'selected';
		}
	}
	
	if(field_title == "" || html_var_title == '' || is_valid_html == 0 || sort_order == '' || post_type_selected == '')
	{
		if(post_type_selected == '')
			jQuery('#post_type').addClass('form-invalid');
		jQuery('#post_type').change(on_change_post_type);
		if(field_title == '')
			jQuery('#admin_title_id').addClass('form-invalid');
		jQuery('#admin_title_id').change(on_change_admin_title);
		if(html_var_title == '' || is_valid_html == 0)
			jQuery('#html_var_name').addClass('form-invalid');
	
		jQuery('#html_var_name').change(on_change_html_var);
		if(sort_order == '')
			jQuery('#sort_order_id').addClass('form-invalid');			
		
		jQuery('#sort_order_id').change(on_change_sort_order);
		var htstr = jQuery('#htmlvar_name').val();
		var htstr1 = htstr.indexOf(" ");
		if(htstr1 > 0)
		{
			jQuery('#html_var_name').addClass('form-invalid');
		}
		if (jQuery("#is_require").is(":checked")) {
			var field_require_desc = jQuery('#field_require_desc').val();
			var validation_type = jQuery('#validation_type').val();
			if(validation_type == ' ')
			{
				jQuery('#validation_type_id').addClass('form-invalid');
				jQuery('#validation_type_id').change(on_change_field_require_type);
			}
			if(field_require_desc == '')
			{
				jQuery('#field_require_desc_id').addClass('form-invalid');
				jQuery('#field_require_desc_id').change(on_change_field_require_desc);
			}
		}
		return false;
	}
	var validate_flag = 1;
	/*allow html variable name only with small character and underscore.*/
	if(html_var_title != '')
	{
		var pat = /^[a-z\_]+$/;
		if(pat.test(html_var_title) == false)
		{
			jQuery('#html_var_name').addClass('form-invalid');
			validate_flag = 0;
		}
		else
		{
			jQuery('#html_var_name').removeClass('form-invalid');
		}
	}
	if(is_search=='1' && search_ctype=='slider_range' && (range_min_value=='' || range_max_value=='')){
		jQuery('#min_max_range_option').addClass('form-invalid');
		return false;
	}
	if(jQuery('#ctype option:selected').val()=='select' || jQuery('#ctype option:selected').val()=='radio' || jQuery('#ctype option:selected').val()=='multicheckbox' ){
		if(jQuery('#option_title').val()=='' && jQuery('#option_values').val()==''){
			jQuery('#ctype_option_title_tr_id').addClass('form-invalid');
			jQuery('#ctype_option_tr_id').addClass('form-invalid');
			validate_flag = 0;
		}else if(jQuery('#option_title').val()==''){
			jQuery('#ctype_option_title_tr_id').addClass('form-invalid');
			jQuery('#ctype_option_tr_id').removeClass('form-invalid');
			validate_flag = 0;
		}else if(jQuery('#option_values').val()==''){
			jQuery('#ctype_option_title_tr_id').removeClass('form-invalid');
			jQuery('#ctype_option_tr_id').addClass('form-invalid');
			validate_flag = 0;
		}else{
			var optiontitle= jQuery('#option_title').val().split(',');
			var optionvalues= jQuery('#option_values').val().split(',');
			if(optiontitle.length!=optionvalues.length || optiontitle[optiontitle.length-1] == '' || optionvalues[optionvalues.length-1] == ''){
				jQuery('#ctype_option_title_tr_id').addClass('form-invalid');
				jQuery('#ctype_option_tr_id').addClass('form-invalid');
				jQuery('#option_error').css('display','');
				validate_flag = 0;
			}else{
				jQuery('#option_error').css('display','none');	
			}
			jQuery('#ctype_option_title_tr_id').removeClass('form-invalid');
			jQuery('#ctype_option_tr_id').removeClass('form-invalid');
		}
	}
	
	if(is_search && search_ctype=='select' && ctype=='text' ){		
		if( option_title=='' &&  option_values==''){
			jQuery('#search_select_value').addClass('form-invalid');
			jQuery('#search_select_title').addClass('form-invalid');
			validate_flag = 0;	
		}else if(option_title==''){
			jQuery('#search_select_title').addClass('form-invalid');
			jQuery('#search_select_value').removeClass('form-invalid');
			validate_flag = 0;
		}else if(option_values==''){
			jQuery('#search_select_value').addClass('form-invalid');
			jQuery('#search_select_title').removeClass('form-invalid');
			validate_flag = 0;
		}
	}
	
	
	//
	if(is_search && search_ctype=='min_max_range_select' ){		
		if( search_min_option_title=='' &&  search_min_option_values=='' ){
			jQuery('#search_min_select_title').addClass('form-invalid');
			jQuery('#search_min_select_value').addClass('form-invalid');			
			validate_flag = 0;	
		}else if(search_min_option_values=='' ){
			jQuery('#search_min_select_title').removeClass('form-invalid');
			jQuery('#search_min_select_value').addClass('form-invalid');						
			validate_flag = 0;
		}else if(search_min_option_title==''){
			jQuery('#search_min_select_title').addClass('form-invalid');
			jQuery('#search_min_select_value').removeClass('form-invalid');
			validate_flag = 0;
		}
		
		
		if( search_max_option_title=='' &&  search_max_option_values=='' ){
			jQuery('#search_max_select_title').addClass('form-invalid');
			jQuery('#search_max_select_value').addClass('form-invalid');			
			validate_flag = 0;
		}else if(search_max_option_values=='' ){
			jQuery('#search_max_select_title').removeClass('form-invalid');
			jQuery('#search_max_select_value').addClass('form-invalid');						
			validate_flag = 0;
		}else if(search_max_option_title==''){
			jQuery('#search_max_select_title').addClass('form-invalid');
			jQuery('#search_max_select_value').removeClass('form-invalid');
			validate_flag = 0;
		}
		
	}
	//
	
	if(is_search && search_ctype=='min_max_range_select' && jQuery('#search_min_option_title').val() != '' && jQuery('#search_min_option_values').val() != '' ){
		var optiontitle= jQuery('#search_min_option_title').val().split(',');
		var optionvalues= jQuery('#search_min_option_values').val().split(',');
		if(optiontitle.length!=optionvalues.length){
			jQuery('#search_min_select_title').addClass('form-invalid');
			jQuery('#search_min_select_value').addClass('form-invalid');
			jQuery('#search_min_option_error').css('display','');
			validate_flag = 0;
		}else{
			jQuery('#search_min_select_value').css('display','none');	
		}
		jQuery('#search_min_select_title').removeClass('form-invalid');
		jQuery('#search_min_option_error').removeClass('form-invalid');
	}
	
	if(is_search && search_ctype=='min_max_range_select' && jQuery('#search_max_option_title').val() != '' && jQuery('#search_max_option_values').val() != '' ){
		var optiontitle= jQuery('#search_max_option_title').val().split(',');
		var optionvalues= jQuery('#search_max_option_values').val().split(',');
		if(optiontitle.length!=optionvalues.length){
			jQuery('#search_max_select_title').addClass('form-invalid');
			jQuery('#search_max_select_value').addClass('form-invalid');
			jQuery('#search_max_option_error').css('display','');
			validate_flag = 0;
		}else{
			jQuery('#search_max_select_value').css('display','none');	
		}
		jQuery('#search_max_select_title').removeClass('form-invalid');
		jQuery('#search_max_option_error').removeClass('form-invalid');
	}
	
	
	if(is_search=='1' && ctype=='range_type' && search_ctype==''){
		jQuery('#option_search_ctype').addClass('form-invalid');
		validate_flag = 0;
	}
	
	if (jQuery("#is_require").is(":checked")) {
		var field_require_desc = jQuery('#field_require_desc').val();
		var validation_type = jQuery('#validation_type').val();
		if(validation_type == '')
		{
			jQuery('#validation_type_id').addClass('form-invalid');
			jQuery('#validation_type_id').change(on_change_field_require_type);
		}
		if(field_require_desc == '')
		{
			jQuery('#field_require_desc_id').addClass('form-invalid');
			jQuery('#field_require_desc_id').change(on_change_field_require_desc);
			validate_flag = 0;
		}
    }
	if(validate_flag == 0)
	{
		return false;
	}
	else if(validate_flag == 1)
	{
		return true;
	}
	function on_change_post_type()
	{
		var dml = document.forms['custom_fields_frm'];
		var chk = dml.elements['post_type_sel[]'];
		var len = dml.elements['post_type_sel[]'].length;
		var post_type_selected = '';
		for (i = 0; i < len; i++)
		{
			if(chk[i].checked == true)
			{
				post_type_selected = 'selected';
			}
		}
		if(post_type_selected == "")
		{
			jQuery('#post_type').addClass('form-invalid');
			return false;
		}
		else
		{
			jQuery('#post_type').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_admin_title()
	{
		var field_title = jQuery('#admin_title').val();
		if(field_title == "")
		{
			jQuery('#admin_title_id').addClass('form-invalid');
			return false;
		}
		else
		{
			jQuery('#admin_title_id').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_html_var()
	{
		var html_var_title = jQuery('#htmlvar_name').val();
		if(html_var_title == "")
		{
			jQuery('#html_var_name').addClass('form-invalid');
			return false;
		}
		else
		{
			jQuery('#html_var_name').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_sort_order()
	{
		var sort_order_title = jQuery('#sort_order').val();
		if(sort_order_title == "")
		{
			jQuery('#sort_order_id').addClass('form-invalid');
			return false;
		}
		else
		{
			jQuery('#sort_order_id').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_field_require_desc()
	{
		var field_require_desc = jQuery('#field_require_desc').val();
		if(field_require_desc == "")
		{
			jQuery('#field_require_desc_id').addClass('form-invalid');
			return false;
		}
		else
		{
			jQuery('#field_require_desc_id').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_field_require_type()
	{
		var validation_type = jQuery('#validation_type').val();
		if(validation_type == "")
		{
			jQuery('#validation_type_id').addClass('form-invalid');
			return false;
		}
		else
		{
			jQuery('#validation_type_id').removeClass('form-invalid');
			return true;
		}
	}
}

jQuery(document).ready(function (){
	jQuery('form#general_setting_form #registration_page_setup .button-primary').click(function(e) {
                                               
		if(jQuery('#allow_facebook_login').length > 0 )
		{
			var show_facebook_key = true;
			var show_google_key = true;
			var show_twitter_key = true;
                                                                           var input_tevolution_login = true;
                                                                           var input_tevolution_register = true;
                                                                           var input_tevolution_profile = true;
			if(jQuery('#allow_facebook_login').prop('checked') == true && ( jQuery('#facebook_key').val() == '' || jQuery('#facebook_secret_key').val() == '') )
			{
				show_facebook_key = false
			}
			else
			{
				show_facebook_key = true
			}
		}
		if(jQuery('#allow_google_login').length > 0 )
		{
			if(jQuery('#allow_google_login').prop('checked') == true && ( jQuery('#google_key').val() == '' || jQuery('#google_secret_key').val() == '') )
			{
				show_google_key = false;
			}
			else
			{
				show_google_key = true;
			
			}
		}
		if(jQuery('#allow_twitter_login').length > 0 )
		{
			if(jQuery('#allow_twitter_login').prop('checked') == true && ( jQuery('#twitter_key').val() == '' || jQuery('#twitter_secret_key').val() == '') )
			{
				show_twitter_key = false
			}
			else
			{
				show_twitter_key = true;
			}
		}
                                                   /*  this validation is for check hidden filed value is 1 for true return page hase short code */
                                                   if(jQuery('#tevolution_login').length > 0){
                                                        if(jQuery('#input_tevolution_login').val() ==1){
                                                             input_tevolution_login = true;
                                                             jQuery('#tevolution_login_page').css('display','none');
                                                            jQuery('#tevolution_login').parent('td').parent('tr').removeClass('form-invalid');
                                                        }else{
                                                             input_tevolution_login = false;
                                                              jQuery('#tevolution_login_page').css('display','block');
                                                              jQuery('#tevolution_login_page').fadeIn('slow');
                                                              jQuery('#tevolution_login').parent('td').parent('tr').addClass('form-invalid');
                                                        }
                                                   }
                                                  if(jQuery('#tevolution_register').length > 0){
                                                       if(jQuery('#input_tevolution_register').val() ==1){
                                                              input_tevolution_register = true;
                                                              jQuery('#tevolution_register_page').css('display','none');
                                                              jQuery('#tevolution_register').parent('td').parent('tr').removeClass('form-invalid');
                                                        }else{
                                                             input_tevolution_register = false;
                                                              jQuery('#tevolution_register_page').css('display','block');
                                                              jQuery('#tevolution_register_page').fadeIn('slow');
                                                              jQuery('#tevolution_register').parent('td').parent('tr').addClass('form-invalid');
                                                        }
                                                  } 
                                                  if(jQuery('#tevolution_profile').length > 0 ){
                                                       if(jQuery('#input_tevolution_profile').val() == 1){
                                                              input_tevolution_profile = true;
                                                              jQuery('#tevolution_profile_page').css('display','none');
                                                              jQuery('#tevolution_profile').parent('td').parent('tr').removeClass('form-invalid');
                                                        }else{
                                                             input_tevolution_profile = false;
                                                              jQuery('#tevolution_profile_page').css('display','block');
                                                              jQuery('#tevolution_profile_page').fadeIn('slow');
                                                              jQuery('#tevolution_profile').parent('td').parent('tr').addClass('form-invalid');
                                                        }
                                                  }
                                           
		if(show_facebook_key == true && show_google_key == true && show_twitter_key == true && input_tevolution_login == true && input_tevolution_register == true && input_tevolution_profile == true)
		{
			jQuery('#show_twitter_key').removeClass('form-invalid');
			jQuery('#show_google_key').removeClass('form-invalid');
			jQuery('#show_facebook_key').removeClass('form-invalid');
                                                                           return true;
		}
		else
		{
			if(jQuery('#allow_twitter_login').prop('checked') == true)
			{
				jQuery('#show_twitter_key').addClass('form-invalid');
			}
			if(jQuery('#allow_google_login').prop('checked') == true)
			{									   
				jQuery('#show_google_key').addClass('form-invalid');
			}
			if(jQuery('#allow_facebook_login').prop('checked') == true)
			{
				jQuery('#show_facebook_key').addClass('form-invalid');
			}
                                     		return false;
		}
	});
});

/*validate form if captcha option is enabled and keys are not inserted.*/
jQuery(document).ready(function (){
	jQuery('form#general_setting_form #captcha_settings .button-primary').click(function() {
               
		var checkedNum = jQuery('input[name="user_verification_page[]"]:checked').length;
		if(checkedNum > 0 )
		{
			var site_key = true;
			var secret = true;
			if(jQuery('input[name="site_key"]').val() == ''  )
			{
				site_key = false;
			}
			else
			{
				site_key = true;
			}
		
			if(jQuery('input[name="secret"]').val() == ''  )
			{
				secret = false;
			}
			else
			{
				secret = true;
			}
			if(site_key == true && secret == true)
			{
				jQuery('input[name="site_key"]').parent().parent().removeClass('form-invalid');
				jQuery('input[name="secret"]').parent().parent().removeClass('form-invalid');
				return true;
			}
			else
			{
				if(jQuery('input[name=site_key]').val() == ''  )
				{
					jQuery('input[name=site_key]').parent().parent().addClass('form-invalid');
				}
				else
				{
					jQuery('input[name="site_key"]').parent().parent().removeClass('form-invalid');
				}
				if(jQuery('input[name="secret"]').val() == ''  )
				{
					jQuery('input[name="secret"]').parent().parent().addClass('form-invalid');
				}
				else
				{
					jQuery('input[name="secret"]').parent().parent().removeClass('form-invalid');
				}
				return false;
			}
		}
	});
});



/* Script to add tabs without refresh in tevolution general settings */
jQuery(document).ready(function (){
	jQuery("#general_setting_form .tmpl-general-settings").hide();
	jQuery("#general_setting_form .active-tab").show();
	
	jQuery('#tev_general_settings li a').click(function (e) {
		jQuery("#general_setting_form .tmpl-general-settings").hide();
		jQuery("#general_setting_form .tmpl-general-settings").removeClass('active-tab');
		jQuery("#tev_general_settings li a").removeClass('current');
		
		jQuery(this).parents('li').addClass('active');
		jQuery(this).addClass('current');
		jQuery("#general_setting_form table#"+this.id).show();				
		jQuery("#general_setting_form table#"+this.id).addClass('tmpl-general-settings form-table active-tab');	
	});
	/* For email settings*/
	jQuery("#email_setting_form .tmpl-email-settings").hide();
	jQuery("#email_setting_form .active-tab").show();
	
	jQuery('#tev_email_settings li a').click(function (e) {
		jQuery("#email_setting_form .tmpl-email-settings").hide();
		jQuery("#email_setting_form .tmpl-email-settings").removeClass('active-tab');
		jQuery("#tev_email_settings li a").removeClass('current');
		
		jQuery(this).parents('li').addClass('active');
		jQuery(this).addClass('current');
		jQuery("#email_setting_form div#"+this.id).show();				
		jQuery("#email_setting_form div#"+this.id).addClass('tmpl-email-settings form-table active-tab');	
		
		if(this.id == "notifications_settings"){
			jQuery("#legend_notifications").hide();
		}else{
			jQuery("#legend_notifications").show();
		}
	});
});
/* Script to hide autoinstall section from plugins area. */
jQuery(document).ready(function(e){
	jQuery('.dismiss-tevolution-autoinstall-notification').click(function(e){
		e.preventDefault();
		jQuery.post(ajaxurl,{action:"tevolution_hide_autoinstall_notification",nonce:jQuery.trim(jQuery("#install-notification-nonce").text())},
		function(e){jQuery(".tevolution-welcome-panel").fadeOut("normal")})
	});
});