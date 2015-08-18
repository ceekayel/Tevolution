jQuery(document).ready(function(){  
    jQuery('#the-list').on('click', '.editinline', function(){  		
		var tag_id = jQuery(this).closest('tr').attr('id');   		
		var cat_price = jQuery('.price', '#'+tag_id).text().substr(1);
		jQuery('input[name="cat_price"]', '.inline-edit-row').val(cat_price);
	}); 
	
	jQuery('.advance_is_search,#search_ctype').on('click',function(){
			
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
	
	jQuery('#ctype,.advance_is_search').on('change',function(){
		var value=jQuery('#ctype').val();
		if(jQuery(".advance_is_search").is(':checked')){
			ShowHideSearch_ctypeOption(value);
		}else{
			jQuery("#option_search_ctype").css('display','none');
			jQuery("#search_select_title").css('display','none');
			jQuery("#search_select_value").css('display','none');
		}
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
	jQuery('#templatic_bulkupload .handlediv').click(function(){
		jQuery("#templatic_bulkupload .inside").toggle();
	})
	jQuery('#templatic_customfields .handlediv').click(function(){
		jQuery("#templatic_customfields .inside").toggle();
	})
	jQuery('#templatic_posttype .handlediv').click(function(){
		jQuery("#templatic_posttype .inside").toggle();
	})
	jQuery('#templatic_manage_ip_module .handlediv').click(function(){
		jQuery("#templatic_manage_ip_module .inside").toggle();
	})
	jQuery('#templatic_monetization .handlediv').click(function(){
		jQuery("#templatic_monetization .inside").toggle();
	})
	jQuery('#templatic_userreg .handlediv').click(function(){
		jQuery("#templatic_userreg .inside").toggle();
	})
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
 
/* */
jQuery(document).ready(function() {
	jQuery('#tevolution_login').change(function(){	
		jQuery('#tevolution_login_page').css('display','block');
		jQuery('#tevolution_login_page').fadeIn('slow');
	});
});
jQuery(document).ready(function() {
	jQuery('#tevolution_register').change(function(){	
		jQuery('#tevolution_register_page').css('display','block');	
		jQuery('#tevolution_register_page').fadeIn('slow');
	});
});
jQuery(document).ready(function() {
	jQuery('#tevolution_profile').change(function(){	
		jQuery('#tevolution_profile_page').css('display','block');	
		jQuery('#tevolution_profile_page').fadeIn('slow');
	});
});
/*Custom Field Sorting */
jQuery('form#post_custom_fields table.tevolution_page_custom_fields tbody').sortable({
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
		var custom_sort_order = jQuery('table.tevolution_page_custom_fields :input').serialize();		
		jQuery.ajax({
				 url: ajaxurl,
				 type: 'POST',
				 data:'action=custom_field_sortorder&paging_input='+paging_input+'&' + custom_sort_order,		 
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
jQuery('table.tevolution_page_user_custom_fields tbody').sortable({
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
		var price_package_order = jQuery('table.tevolution_page_user_custom_fields :input').serialize();		
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
/*Search Custom Field Sorting */
jQuery('form#post_search_custom_fields table.tevolution_page_custom_fields tbody').sortable({
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
		var custom_sort_order = jQuery('table.tevolution_page_custom_fields :input').serialize();		
		jQuery.ajax({
				 url: ajaxurl,
				 type: 'POST',
				 data:'action=search_custom_field_sortorder&paging_input='+paging_input+'&' + custom_sort_order,		 
				 success:function(result){
					 //alert(result)
				 }
			 });	
	},
	stop:function(event,ui){
		ui.item.removeAttr('style');
	}
});