/*
 * tevolution script for auto complete search captcha , addToFavourite , etc script
 */
var captcha ='';
jQuery(document).ready(function() {
	// auto-complete
	jQuery('input.ui-autocomplete-input').click(function() {
		jQuery( "body" ).addClass('overlay-dark'); 
		jQuery('input.ui-autocomplete-input').addClass('temp-zindex');
	}); 
	jQuery('.exit-selection').click(function() {
		jQuery( "body" ).removeClass('overlay-dark'); 
		jQuery(".ui-widget-content.ui-autocomplete.ui-front").css('display','none');
		jQuery('input.ui-autocomplete-input').removeClass('temp-zindex');
	});
	  // remove class when 'esc' key pressed
	jQuery( "html" ).keydown(function( event ) {
	  if ( event.which == 27 ) {
		jQuery( "body" ).removeClass('overlay-dark'); 
		jQuery(".ui-widget-content.ui-autocomplete.ui-front").css('display','none');
		jQuery('input.ui-autocomplete-input').removeClass('temp-zindex');
	  }
	});
	
    var autocomplet_ajax = null;
	var search_text='';
    jQuery(".searchform .searchpost").autocomplete({
       minLength: 0,
	   create: function () {
            jQuery(this).data('ui-autocomplete')._renderItem = function (ul, item) {				
				return jQuery("<li>").addClass('instant_search').append("<a>").attr("href",item.url).html( item.label ).appendTo(ul);                
            };
        },
       source: function( request, response ) {
			var post_type='';			
			jQuery(".searchform input[name^='post_type']").each(function(){
				post_type+=jQuery(this).val()+',';
			});			
			if(search_text=='' || request.term!=''){
				search_text=request.term;
			}
			var submit_from = jQuery('form.searchform').serialize();
			autocomplet_ajax=jQuery.ajax({
				url:tevolutionajaxUrl,
				type:'POST',
				dataType: "json",
				data:'action=tevolution_autocomplete_callBack&search_text='+search_text+'&post_type='+post_type+'&'+submit_from,
				beforeSend : function(){
					if(autocomplet_ajax != null){
						autocomplet_ajax.abort();
					}
				},
				success:function(data) {						
					 response(jQuery.map(data.results, function(item) {
                            return {
                                label: item.title,
                                value: item.label,
                                url: item.url,								
                            };
                        }));
				}
			});			
		},
        autoFocus: false,
        scroll: true,		
		select: function( event, ui ) {
			if ( ui.item.url !== '#' ) {
				location = ui.item.url;
				//jQuery('input[name="s"]').val(ui.item.value);
				//jQuery('#searchform').submit();
				//jQuery(this).val(ui.item.value).closest('form').submit(); 
			} else {
				return true;
			}
		},
		open: function(event, ui) {		
			var acData = jQuery(this).data('uiAutocomplete');
			acData
					.menu
					.element
					.find('a')
					.each(function () {
						var $self = jQuery( this ),
							keywords = jQuery.trim( acData.term ).split( ' ' ).join('|');
						$self.html($self.text().replace(new RegExp("(" + keywords + ")", "gi"), '$1'));
					});
			jQuery(event.target).removeClass('sa_searching');
		},		
    }).focus(function() {
        jQuery(this).autocomplete("search", "");
    });
});

/* Script to make element like select box in category pages */

jQuery("ul.sorting_option").on("click", ".init", function() {
	jQuery(this).closest("ul.sorting_option").children('li:not(.init)').slideToggle(30);
	jQuery('.exit-sorting').toggle();
});

var allOptions = jQuery("ul.sorting_option").children('li:not(.init)');
jQuery(".exit-sorting").on("click", function() {
	allOptions.slideUp(30);
	jQuery('.exit-sorting').css('display','none');
});

jQuery("ul.sorting_option").on("click", "li:not(.init)", function() {
    allOptions.removeClass('selected');
    jQuery(this).addClass('selected');
    jQuery("ul.sorting_option").children('.init').html(jQuery(this).html());
    allOptions.slideUp();
    jQuery('.exit-sorting').css('display','none');
});

/**
* Author post delete code
*/

jQuery(document).ready(function(){
	jQuery(".autor_delete_link").click(function(){
		if( confirm( delete_confirm) ){
			jQuery(this).after("<span class='delete_append'><?php _e('Deleting.',DOMAIN);?></span>");
			jQuery(".delete_append").css({"margin":"5px","vertical-align":"sub","font-size":"14px"});
			setTimeout(function() {
			   jQuery(".delete_append").html(deleting);
			}, 700);
			setTimeout(function() {
			   jQuery(".delete_append").html(deleting);
			}, 1400);
			jQuery.ajax({
				url:ajaxUrl,
				type:'POST',
				data:'action=delete_auth_post&security='+delete_auth_post+'&postId='+ jQuery(this).attr('data-deleteid') + '&currUrl='+currUrl,
				success:function(redirect) {	
					window.location.href = redirect;
				},
			});
			
			return false;
		}else{
			return false;
		}
	});
});


/* addToFavourite function */
function addToFavourite(post_id,action)
{	
	if(current_user!=0){ 
		
		if(action == 'add'){
			action = 'add';
		}else{
			action = 'removed';
		}
		
		jQuery.ajax({	
			url:ajaxUrl,
			type:'POST',
			async: true,
			data:'action=tmpl_add_to_favourites&ptype=favorite&action1='+action+'&pid='+post_id,
			success: function(html){
				if(favourites_sort==1){
					document.getElementById('post-'+post_id).style.display='none';					
				}
				jQuery('.fav_'+post_id).html(html);
			}
		});
		return false;
	}
}


/*Register/login form function */
function tmpl_registretion_frm(){
	jQuery("#tmpl_reg_login_container #tmpl_sign_up").show();
	jQuery("#tmpl_reg_login_container #tmpl_login_frm").hide();
}
function tmpl_login_frm(){
	jQuery("#tmpl_reg_login_container #tmpl_sign_up").hide();
	jQuery("#tmpl_reg_login_container #tmpl_login_frm").show();
}

/*single page Print button function*/
function tmpl_printpage(){
	window.print();
}

/*Browser by category widget script for display sub category on parent category hover */
/* Add Js to slide down the categories */
jQuery(document).ready(function() {
	jQuery('.browse_by_category ul.children').css({"display":"none"});
	jQuery('ul.browse_by_category li:has(> ul)').addClass('hasChildren');
	jQuery('ul.browse_by_category li.hasChildren').mouseenter(function () {
		jQuery(this).addClass('heyHover').children('ul').show();
		return false;
	});
	jQuery('ul.browse_by_category li.hasChildren').mouseleave(function () {
		jQuery(this).removeClass('heyHover').children('ul').hide();
		return false;
	});
});


/*Single page inquiry form validation and submit email script */
jQuery(document).ready(function(){
//global vars
	var enquiry1frm = jQuery("#inquiry_frm");
	var full_name = jQuery("#full_name");
	var full_nameInfo = jQuery("#full_nameInfo");
	var your_iemail = jQuery("#your_iemail");
	var your_iemailInfo = jQuery("#your_iemailInfo");
	var sub = jQuery("#inq_subject");
	var subinfo = jQuery("#inq_subInfo");
	var frnd_comments = jQuery("#inq_msg");
	var frnd_commentsInfo = jQuery("#inq_msgInfo");
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
				data:'action=tevolution_send_inquiry_form&' + inquiry_data + '&postid=' + current_post_id,
				success:function(results) {	
					document.getElementById('process_state').style.display="none";					
					if(results==1){
						jQuery('#send_inquiry_msg').html(captcha_invalid_msg);
					}else{
						jQuery('#send_inquiry_msg').html(results);
						setTimeout(function(){
									jQuery("#lean_overlay").fadeOut(200);
									jQuery('#inquiry_div').css({"display":"none"});
									jQuery('#tmpl_send_inquiry').removeClass('open');
									jQuery('#tmpl_send_inquiry').attr('style','');
									jQuery('.reveal-modal-bg').css('display','none');
									jQuery('#inq_subject').val('');
									jQuery('#inq_msg').html('');
									jQuery('#send_inquiry_msg').html('');
									full_name.val('');
									your_iemail.val('');
									jQuery("#contact_number").val('');									
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
			full_nameInfo.text(fullname_error_msg);
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
			your_iemailInfo.text(email_error_msg);
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
		if(jQuery("#inq_subject").val() == '')
		{
			sub.addClass("error");
			subinfo.text(subject_error_msg);
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
		if(jQuery("#inq_msg").val() == '')
		{
			frnd_comments.addClass("error");
			frnd_commentsInfo.text(comment_error_msg);
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
/* END Single page inquiry form validation and submit email script */

/*Single page send to friend form validation and submit email script */
jQuery(document).ready(function(){
//global vars
	var send_to_frnd = jQuery("#send_to_frnd");
	var to_name_friend = jQuery("#to_name_friend");
	var to_name_friendInfo = jQuery("#to_name_friendInfo");
	var to_friend_email = jQuery("#to_friend_email");
	var to_friend_emailInfo = jQuery("#to_friend_emailInfo");
	var yourname = jQuery("#yourname");
	var yournameInfo = jQuery("#yournameInfo");
	var youremail = jQuery("#youremail");
	var youremailInfo = jQuery("#youremailInfo");
	var frnd_comments = jQuery("#frnd_comments");
	var frnd_commentsInfo = jQuery("#frnd_commentsInfo");
	
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
						jQuery('#send_friend_msg').html(captcha_invalid_msg);
					}else{
						jQuery('#send_friend_msg').html(results);
						setTimeout(function(){
									jQuery("#lean_overlay").fadeOut(200);
									jQuery('#tmpl_send_to_frd').removeClass('open');
									jQuery('#tmpl_send_to_frd').attr('style','');
									jQuery('.reveal-modal-bg').css('display','none');
									jQuery('#send_friend_msg').html('');
									jQuery('#frnd_subject').val('');
									jQuery('#frnd_comments').html('');
									yourname.val('');
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
		if(jQuery("#to_name_friend").val() == '')
		{
			to_name_friend.addClass("error");
			to_name_friendInfo.text(friendname_error_msg);
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
		if(jQuery("#to_friend_email").val() != '')
		{
			var a = jQuery("#to_friend_email").val();			
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
			to_friend_emailInfo.text(friendemail_error_msg);
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
		if(jQuery("#yourname").val() == '')
		{
			yourname.addClass("error");
			yournameInfo.text(fullname_error_msg);
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
		if(jQuery("#youremail").val() == '')
		{
			isvalidemailflag = 1;
		}else
		if(jQuery("#youremail").val() != '')
		{
			var a = jQuery("#youremail").val();
			var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
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
			youremailInfo.text(email_error_msg);
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
		if(jQuery("#frnd_comments").val() == '')
		{
			frnd_comments.addClass("error");
			frnd_commentsInfo.text(friend_comment_error_msg);
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
/* END Single page send to friend form validation and submit email script */


/*User Login/register related script */
jQuery.noConflict();
var checkclick = false;
var reg_email= 0;
var reg_name = 0;
var chkemailRequest=null;
var chknameRequest=null;
function chkemail(e)
{        
	if (jQuery("#" + e + " #user_email").val()) {
        var t = jQuery("#" + e + " #user_email").val()
    }
    jQuery("#" + e + " .user_email_spin").remove();
    jQuery("#" + e + " input#user_email").css("display", "inline");
    jQuery("#" + e + " input#user_email").after("<i class='fa fa-circle-o-notch fa-spin user_email_spin ajax-fa-spin'></i>");
    chkemailRequest = jQuery.ajax({
        url: ajaxUrl,
        type: "POST",
        async: true,
        data: "action=tmpl_ajax_check_user_email&user_email=" + t,
        beforeSend: function() {
            if (chkemailRequest != null) {
                chkemailRequest.abort()
            }
        },
        success: function(t) {
            var n = t.split(",");
            if (n[1] == "email") {
                if (n[0] > 0) {
                    jQuery("#" + e + " #user_email_error").html(user_email_error);
                    jQuery("#" + e + " #user_email_already_exist").val(0);
                    jQuery("#" + e + " #user_email_error").removeClass("available_tick");
                    jQuery("#" + e + " #user_email_error").addClass("message_error2");
                    reg_email = 0
                } else {
                    jQuery("#" + e + " #user_email_error").html(user_email_verified);
                    jQuery("#" + e + " #user_email_already_exist").val(1);
                    jQuery("#" + e + " #user_email_error").addClass("available_tick");
                    jQuery("#" + e + " #user_email_error").removeClass("message_error2");
                    reg_email = 1
                }
            }
            jQuery("#" + e + " .user_email_spin").remove()
        }
    });
    return true
}
function chkname(e){
    if (jQuery("#" + e + " #user_fname").val()) {
        var t = jQuery("#" + e + " #user_fname").val()
    }
    jQuery("#" + e + " .user_fname_spin").remove();
    jQuery("#" + e + " input#user_fname").css("display", "inline");
    jQuery("#" + e + " input#user_fname").after("<i class='fa fa-circle-o-notch fa-spin user_fname_spin ajax-fa-spin'></i>");
    chknameRequest = jQuery.ajax({
        url: ajaxUrl,
        type: "POST",
        async: true,
        data: "action=tmpl_ajax_check_user_email&user_fname=" + t,
        beforeSend: function() {
            if (chknameRequest != null) {
                chknameRequest.abort()
            }
        },
        success: function(t) {
            var n = t.split(",");
            if (n[1] == "fname") {
                if (n[0] > 0) {
                    jQuery("#" + e + " #user_fname_error").html(user_fname_error);
                    jQuery("#" + e + " #user_fname_already_exist").val(0);
                    jQuery("#" + e + " #user_fname_error").addClass("message_error2");
                    jQuery("#" + e + " #user_fname_error").removeClass("available_tick");
                    reg_name = 0
                } else {
                    jQuery("#" + e + " #user_fname_error").html(user_fname_verified);
                    jQuery("#" + e + " #user_fname_already_exist").val(1);
                    jQuery("#" + e + " #user_fname_error").removeClass("message_error2");
                    jQuery("#" + e + " #user_fname_error").addClass("available_tick");
                    if (jQuery("" + e + " #userform div").size() == 2 && checkclick) {
                        document.userform.submit()
                    }
                    reg_name = 1
                }
            }
            jQuery("#" + e + " .user_fname_spin").remove()
        }
    });
    return true
}


function set_login_registration_frm(val)
{
	if(val=='existing_user')
	{
		document.getElementById('login_user_meta').style.display = 'none';
		document.getElementById('login_user_frm_id').style.display = '';
		document.getElementById('login_type').value = val;
		if(document.getElementById('monetize_preview'))
			document.getElementById('monetize_preview').style.display = 'none';
	}else  //new_user
	{
		document.getElementById('login_user_meta').style.display = 'block';
		document.getElementById('login_user_frm_id').style.display = 'none';
		document.getElementById('login_type').value = val;
		if(document.getElementById('monetize_preview'))
			document.getElementById('monetize_preview').style.display = 'block';
	}
}

function showNextsubmitStep()
{

	var next = 'post',
	view = this;
	jQuery('.step-wrapper').removeClass('current');
	jQuery('.content').slideUp(500, function() {
		// current step is plan
		if (currentStep === 'plan') {
			if(jQuery('#pkg_type').val() == 1 || pkg_post == 1 )
			{
				next = 'post';
			}
			else if(jQuery('#pkg_type').val() == 2)
			{
				jQuery('#step-post').css('display','none');
				if (parseInt(jQuery('#step-auth').length) === 0)
				{
					jQuery('#select_payment').html('2');
					user_login = true;
				}
				else
				{
					jQuery('#span_user_login').html('2');
					jQuery('#select_payment').html('3');
					user_login = false;
				}
				if (user_login) {
					next = 'payment';
				}
				else
				{
					next = 'auth';
				}
			}
		}
		// current step is post
		if (currentStep == 'post') {
			if (parseInt(jQuery('#step-auth').length) === 0)
			{
				user_login = true;
			}
			else
			{
				user_login = false;
			}
			if (user_login) {
				next = 'payment';
			}
			else
			{
				next = 'auth';
			}
		}
		// current step is auth
		if (currentStep == 'auth') {
			// update user_login
			if (user_login) { // user login skip step auth
				next = 'payment';
			}
		}
		// show next step
		jQuery('.step-' + next + '  .content').slideDown(10).end();
		jQuery('.step-' + next).addClass('current');
	});
}
/*jquery to go to next step while registration on submit form*/
jQuery('form#submit_form #register_form').live('click',function(){
	if(reg_name==1 && reg_email==1){
		user_login = true;
		currentStep = 'auth';
		jQuery('div#step-auth').addClass('complete');
		if((parseFloat(jQuery('#total_price').val()) <=0 || jQuery('#total_price').val() == '' || jQuery('#package_free_submission').val() >0) )
		{
			/*Get the wp_editor content and append in submit form to get the wp_editor data on submit page */
			jQuery('.wp-editor-container textarea').each(function(){
				var name=jQuery(this).attr('id');
				jQuery('<input>').attr({
					type: 'hidden',
					id: name,
					name: name,
					value: tinyMCE.get(name).getContent()
				}).appendTo('#submit_form');
			});
			jQuery('#submit_form').submit();
		}
		else
		{
			finishStep.push('step-auth');
			showNextsubmitStep();
		}
	}
});
/*jquery to check whether user name is valid or not while login*/
var chkusernameRequest=null;
var user_login_name = false;
jQuery('form#loginform #user_login,#login_widget form#loginform #user_login,.login_pop_class form#loginform #user_login').live('keyup', function(e){
	var submit_from = jQuery(this).serialize();
	var username=jQuery(this).val(); 
	chkusernameRequest = jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: ajaxUrl,
		data:'action=ajaxcheckusername&username='+username,
		beforeSend : function(){
			if(chkusernameRequest != null){
				chkusernameRequest.abort();
			}
        },
		success: function(data){
			var $target = jQuery(e.currentTarget)
			$ul = jQuery($target).next();
			if(data == 1)
			{
				
				$ul.removeClass('message_error2');
				$ul.addClass('available_tick');
				$ul.html(user_name_verified);
				user_login_name = true;
			}
			else
			{
				$ul.removeClass('available_tick');
				$ul.addClass('message_error2');
				$ul.html(user_name_error);
				user_login_name = false;
			}
		}
	});
	e.preventDefault();
	
});
jQuery('form#loginform,form#loginform,form#loginform').submit(function(){

	if(user_login_name)
	{
		return true;
	}
	else
	{
		var submit_from = jQuery(this).serialize();
		var username=jQuery('form#loginform #user_login,#login_widget form#loginform #user_login,.login_pop_class form#loginform #user_login').val(); 
		chkusernameRequest = jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxUrl,
			data:'action=ajaxcheckusername&username='+username,
			beforeSend : function(){
				if(chkusernameRequest != null){
					chkusernameRequest.abort();
				}
			},
			success: function(data){
				var $target = jQuery(e.currentTarget)
				$ul = jQuery($target).next();
				if(data == 1)
				{
					
					$ul.removeClass('message_error2');
					$ul.addClass('available_tick');
					$ul.html(user_name_verified);
					return true;
				}
				else
				{
					$ul.removeClass('available_tick');
					$ul.addClass('message_error2');
					$ul.html(user_name_error);
					return false;
				}
			}
		});
	}
});
jQuery('form#loginform .lw_fpw_lnk,#login_widget form#loginform .lw_fpw_lnk,.login_pop_class form#loginform .lw_fpw_lnk').live('click', function(e){
	jQuery('.forgotpassword').show();
	e.preventDefault();
});

/*Social meadia share script like tweeter, facebook, pintrest, and google plus */
/*
 *  Sharrre.com - Make your sharing widget!
 *  Version: beta 1.3.4
 *  Author: Julien Hany
 *  License: MIT http://en.wikipedia.org/wiki/MIT_License or GPLv2 http://en.wikipedia.org/wiki/GNU_General_Public_License
 */
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}(';(6($,g,h,i){l j=\'1Y\',23={3i:\'1Y\',L:{O:C,E:C,z:C,I:C,p:C,K:C,N:C,B:C},2a:0,18:\'\',12:\'\',3:h.3h.1a,x:h.12,1p:\'1Y.3d\',y:{},1q:0,1w:w,3c:w,3b:w,2o:C,1X:6(){},38:6(){},1P:6(){},26:6(){},8:{O:{3:\'\',15:C,1j:\'37\',13:\'35-4Y\',2p:\'\'},E:{3:\'\',15:C,R:\'1L\',11:\'4V\',H:\'\',1A:\'C\',2c:\'C\',2d:\'\',1B:\'\',13:\'4R\'},z:{3:\'\',15:C,y:\'33\',2m:\'\',16:\'\',1I:\'\',13:\'35\'},I:{3:\'\',15:C,Q:\'4K\'},p:{3:\'\',15:C,1j:\'37\'},K:{3:\'\',15:C,11:\'1\'},N:{3:\'\',15:C,22:\'\'},B:{3:\'\',1s:\'\',1C:\'\',11:\'33\'}}},1n={O:"",E:"1D://4J.E.o/4x?q=4u%2X,%4j,%4i,%4h,%4f,%4e,46,%45,%44%42%41%40%2X=%27{3}%27&1y=?",z:"S://3W.3P.z.o/1/3D/y.2G?3={3}&1y=?",I:"S://3l.I.o/2.0/5a.59?54={3}&Q=1c&1y=?",p:\'S://52.p.o/4Q/2G/4B/m?3={3}&1y=?\',K:"",N:"S://1o.N.o/4z/y/L?4r=4o&3={3}&1y=?",B:""},2A={O:6(b){l c=b.4.8.O;$(b.r).X(\'.8\').Z(\'<n G="U 4d"><n G="g-25" m-1j="\'+c.1j+\'" m-1a="\'+(c.3!==\'\'?c.3:b.4.3)+\'" m-2p="\'+c.2p+\'"></n></n>\');g.3Z={13:b.4.8.O.13};l d=0;9(A 2x===\'F\'&&d==0){d=1;(6(){l a=h.1g(\'P\');a.Q=\'x/1c\';a.1r=w;a.17=\'//3w.2w.o/Y/25.Y\';l s=h.1d(\'P\')[0];s.1e.1f(a,s)})()}J{2x.25.3X()}},E:6(c){l e=c.4.8.E;$(c.r).X(\'.8\').Z(\'<n G="U E"><n 2T="1V-47"></n><n G="1V-1L" m-1a="\'+(e.3!==\'\'?e.3:c.4.3)+\'" m-1A="\'+e.1A+\'" m-11="\'+e.11+\'" m-H="\'+e.H+\'" m-3u-2c="\'+e.2c+\'" m-R="\'+e.R+\'" m-2d="\'+e.2d+\'" m-1B="\'+e.1B+\'" m-16="\'+e.16+\'"></n></n>\');l f=0;9(A 1i===\'F\'&&f==0){f=1;(6(d,s,a){l b,2s=d.1d(s)[0];9(d.3x(a)){1v}b=d.1g(s);b.2T=a;b.17=\'//4c.E.4n/\'+e.13+\'/4t.Y#4C=1\';2s.1e.1f(b,2s)}(h,\'P\',\'E-5g\'))}J{1i.3n.3p()}},z:6(b){l c=b.4.8.z;$(b.r).X(\'.8\').Z(\'<n G="U z"><a 1a="1D://z.o/L" G="z-L-U" m-3="\'+(c.3!==\'\'?c.3:b.4.3)+\'" m-y="\'+c.y+\'" m-x="\'+b.4.x+\'" m-16="\'+c.16+\'" m-2m="\'+c.2m+\'" m-1I="\'+c.1I+\'" m-13="\'+c.13+\'">3q</a></n>\');l d=0;9(A 2j===\'F\'&&d==0){d=1;(6(){l a=h.1g(\'P\');a.Q=\'x/1c\';a.1r=w;a.17=\'//1M.z.o/1N.Y\';l s=h.1d(\'P\')[0];s.1e.1f(a,s)})()}J{$.3C({3:\'//1M.z.o/1N.Y\',3E:\'P\',3F:w})}},I:6(a){l b=a.4.8.I;$(a.r).X(\'.8\').Z(\'<n G="U I"><a G="3H \'+b.Q+\'" 3L="3U 3V" 1a="S://I.o/2y?3=\'+V((b.3!==\'\'?b.3:a.4.3))+\'"></a></n>\');l c=0;9(A 43===\'F\'&&c==0){c=1;(6(){l s=h.1g(\'2z\'),24=h.1d(\'2z\')[0];s.Q=\'x/1c\';s.1r=w;s.17=\'//1N.I.o/8.Y\';24.1e.1f(s,24)})()}},p:6(a){9(a.4.8.p.1j==\'4g\'){l b=\'H:2r;\',2e=\'D:2B;H:2r;1B-1j:4y;1t-D:2B;\',2l=\'D:2C;1t-D:2C;2k-50:1H;\'}J{l b=\'H:53;\',2e=\'2g:58;2f:0 1H;D:1u;H:5c;1t-D:1u;\',2l=\'2g:5d;D:1u;1t-D:1u;\'}l c=a.1w(a.4.y.p);9(A c==="F"){c=0}$(a.r).X(\'.8\').Z(\'<n G="U p"><n 1T="\'+b+\'1B:5i 5j,5k,5l-5n;5t:3k;1S:#3m;2D:3o-2E;2g:2F;D:1u;1t-D:3r;2k:0;2f:0;x-3s:0;3t-2b:3v;">\'+\'<n 1T="\'+2e+\'2H-1S:#2I;2k-3y:3z;3A:3B;x-2b:2J;1O:2K 2L #3G;1O-2M:1H;">\'+c+\'</n>\'+\'<n 1T="\'+2l+\'2D:2E;2f:0;x-2b:2J;x-3I:2F;H:2r;2H-1S:#3J;1O:2K 2L #3K;1O-2M:1H;1S:#2I;">\'+\'<2N 17="S://1o.p.o/3M/2N/p.3N.3O" D="10" H="10" 3Q="3R" /> 3S</n></n></n>\');$(a.r).X(\'.p\').3T(\'1P\',6(){a.2O(\'p\')})},K:6(b){l c=b.4.8.K;$(b.r).X(\'.8\').Z(\'<n G="U K"><2P:28 11="\'+c.11+\'" 3h="\'+(c.3!==\'\'?c.3:b.4.3)+\'"></2P:28></n>\');l d=0;9(A 1E===\'F\'&&d==0){d=1;(6(){l a=h.1g(\'P\');a.Q=\'x/1c\';a.1r=w;a.17=\'//1M.K.o/1/1N.Y\';l s=h.1d(\'P\')[0];s.1e.1f(a,s)})();s=g.3Y(6(){9(A 1E!==\'F\'){1E.2Q();21(s)}},20)}J{1E.2Q()}},N:6(b){l c=b.4.8.N;$(b.r).X(\'.8\').Z(\'<n G="U N"><P Q="1Z/L" m-3="\'+(c.3!==\'\'?c.3:b.4.3)+\'" m-22="\'+c.22+\'"></P></n>\');l d=0;9(A g.2R===\'F\'&&d==0){d=1;(6(){l a=h.1g(\'P\');a.Q=\'x/1c\';a.1r=w;a.17=\'//1M.N.o/1Z.Y\';l s=h.1d(\'P\')[0];s.1e.1f(a,s)})()}J{g.2R.1W()}},B:6(b){l c=b.4.8.B;$(b.r).X(\'.8\').Z(\'<n G="U B"><a 1a="S://B.o/1K/2u/U/?3=\'+(c.3!==\'\'?c.3:b.4.3)+\'&1s=\'+c.1s+\'&1C=\'+c.1C+\'" G="1K-3j-U" y-11="\'+c.11+\'">48 49</a></n>\');(6(){l a=h.1g(\'P\');a.Q=\'x/1c\';a.1r=w;a.17=\'//4a.B.o/Y/4b.Y\';l s=h.1d(\'P\')[0];s.1e.1f(a,s)})()}},2S={O:6(){},E:6(){1V=g.2v(6(){9(A 1i!==\'F\'){1i.2t.2q(\'2U.2u\',6(a){1m.1l([\'1k\',\'E\',\'1L\',a])});1i.2t.2q(\'2U.4k\',6(a){1m.1l([\'1k\',\'E\',\'4l\',a])});1i.2t.2q(\'4m.1A\',6(a){1m.1l([\'1k\',\'E\',\'1A\',a])});21(1V)}},2V)},z:6(){2W=g.2v(6(){9(A 2j!==\'F\'){2j.4p.4q(\'1J\',6(a){9(a){1m.1l([\'1k\',\'z\',\'1J\'])}});21(2W)}},2V)},I:6(){},p:6(){},K:6(){},N:6(){6 4s(){1m.1l([\'1k\',\'N\',\'L\'])}},B:6(){}},2Y={O:6(a){g.19("1D://4v.2w.o/L?4w="+a.8.O.13+"&3="+V((a.8.O.3!==\'\'?a.8.O.3:a.3)),"","1b=0, 1G=0, H=2Z, D=20")},E:6(a){g.19("S://1o.E.o/30/30.3d?u="+V((a.8.E.3!==\'\'?a.8.E.3:a.3))+"&t="+a.x+"","","1b=0, 1G=0, H=2Z, D=20")},z:6(a){g.19("1D://z.o/4A/1J?x="+V(a.x)+"&3="+V((a.8.z.3!==\'\'?a.8.z.3:a.3))+(a.8.z.16!==\'\'?\'&16=\'+a.8.z.16:\'\'),"","1b=0, 1G=0, H=31, D=32")},I:6(a){g.19("S://I.o/4D/4E/2y?3="+V((a.8.I.3!==\'\'?a.8.I.3:a.3))+"&12="+a.x+"&1I=w&1T=w","","1b=0, 1G=0, H=31, D=32")},p:6(a){g.19(\'S://1o.p.o/4F?v=5&4G&4H=4I&3=\'+V((a.8.p.3!==\'\'?a.8.p.3:a.3))+\'&12=\'+a.x,\'p\',\'1b=1F,H=1h,D=1h\')},K:6(a){g.19(\'S://1o.K.o/28/?3=\'+V((a.8.p.3!==\'\'?a.8.p.3:a.3)),\'K\',\'1b=1F,H=1h,D=1h\')},N:6(a){g.19(\'1D://1o.N.o/4L/L?3=\'+V((a.8.p.3!==\'\'?a.8.p.3:a.3))+\'&4M=&4N=w\',\'N\',\'1b=1F,H=1h,D=1h\')},B:6(a){g.19(\'S://B.o/1K/2u/U/?3=\'+V((a.8.B.3!==\'\'?a.8.B.3:a.3))+\'&1s=\'+V(a.8.B.1s)+\'&1C=\'+a.8.B.1C,\'B\',\'1b=1F,H=4O,D=4P\')}};6 T(a,b){7.r=a;7.4=$.4S(w,{},23,b);7.4.L=b.L;7.4T=23;7.4U=j;7.1W()};T.W.1W=6(){l c=7;9(7.4.1p!==\'\'){1n.O=7.4.1p+\'?3={3}&Q=O\';1n.K=7.4.1p+\'?3={3}&Q=K\';1n.B=7.4.1p+\'?3={3}&Q=B\'}$(7.r).4W(7.4.3i);9(A $(7.r).m(\'12\')!==\'F\'){7.4.12=$(7.r).4X(\'m-12\')}9(A $(7.r).m(\'3\')!==\'F\'){7.4.3=$(7.r).m(\'3\')}9(A $(7.r).m(\'x\')!==\'F\'){7.4.x=$(7.r).m(\'x\')}$.1z(7.4.L,6(a,b){9(b===w){c.4.2a++}});9(c.4.3b===w){$.1z(7.4.L,6(a,b){9(b===w){4Z{c.34(a)}51(e){}}})}J 9(c.4.18!==\'\'){7.4.26(7,7.4)}J{7.2n()}$(7.r).1X(6(){9($(7).X(\'.8\').36===0&&c.4.3c===w){c.2n()}c.4.1X(c,c.4)},6(){c.4.38(c,c.4)});$(7.r).1P(6(){c.4.1P(c,c.4);1v C})};T.W.2n=6(){l c=7;$(7.r).Z(\'<n G="8"></n>\');$.1z(c.4.L,6(a,b){9(b==w){2A[a](c);9(c.4.2o===w){2S[a]()}}})};T.W.34=6(c){l d=7,y=0,3=1n[c].1x(\'{3}\',V(7.4.3));9(7.4.8[c].15===w&&7.4.8[c].3!==\'\'){3=1n[c].1x(\'{3}\',7.4.8[c].3)}9(3!=\'\'&&d.4.1p!==\'\'){$.55(3,6(a){9(A a.y!=="F"){l b=a.y+\'\';b=b.1x(\'\\56\\57\',\'\');y+=1Q(b,10)}J 9(a.m&&a.m.36>0&&A a.m[0].39!=="F"){y+=1Q(a.m[0].39,10)}J 9(A a.3a!=="F"){y+=1Q(a.3a,10)}J 9(A a[0]!=="F"){y+=1Q(a[0].5b,10)}J 9(A a[0]!=="F"){}d.4.y[c]=y;d.4.1q+=y;d.2i();d.1R()}).5e(6(){d.4.y[c]=0;d.1R()})}J{d.2i();d.4.y[c]=0;d.1R()}};T.W.1R=6(){l a=0;5f(e 1Z 7.4.y){a++}9(a===7.4.2a){7.4.26(7,7.4)}};T.W.2i=6(){l a=7.4.1q,18=7.4.18;9(7.4.1w===w){a=7.1w(a)}9(18!==\'\'){18=18.1x(\'{1q}\',a);$(7.r).1U(18)}J{$(7.r).1U(\'<n G="5h"><a G="y" 1a="#">\'+a+\'</a>\'+(7.4.12!==\'\'?\'<a G="L" 1a="#">\'+7.4.12+\'</a>\':\'\')+\'</n>\')}};T.W.1w=6(a){9(a>=3e){a=(a/3e).3f(2)+"M"}J 9(a>=3g){a=(a/3g).3f(1)+"k"}1v a};T.W.2O=6(a){2Y[a](7.4);9(7.4.2o===w){l b={O:{14:\'5m\',R:\'+1\'},E:{14:\'E\',R:\'1L\'},z:{14:\'z\',R:\'1J\'},I:{14:\'I\',R:\'29\'},p:{14:\'p\',R:\'29\'},K:{14:\'K\',R:\'29\'},N:{14:\'N\',R:\'L\'},B:{14:\'B\',R:\'1K\'}};1m.1l([\'1k\',b[a].14,b[a].R])}};T.W.5o=6(){l a=$(7.r).1U();$(7.r).1U(a.1x(7.4.1q,7.4.1q+1))};T.W.5p=6(a,b){9(a!==\'\'){7.4.3=a}9(b!==\'\'){7.4.x=b}};$.5q[j]=6(b){l c=5r;9(b===i||A b===\'5s\'){1v 7.1z(6(){9(!$.m(7,\'2h\'+j)){$.m(7,\'2h\'+j,5u T(7,b))}})}J 9(A b===\'5v\'&&b[0]!==\'5w\'&&b!==\'1W\'){1v 7.1z(6(){l a=$.m(7,\'2h\'+j);9(a 5x T&&A a[b]===\'6\'){a[b].5y(a,5z.W.5A.5B(c,1))}})}}})(5C,5D,5E);',62,351,'|||url|options||function|this|buttons|if||||||||||||var|data|div|com|delicious||element|||||true|text|count|twitter|typeof|pinterest|false|height|facebook|undefined|class|width|digg|else|stumbleupon|share||linkedin|googlePlus|script|type|action|http|Plugin|button|encodeURIComponent|prototype|find|js|append||layout|title|lang|site|urlCount|via|src|template|open|href|toolbar|javascript|getElementsByTagName|parentNode|insertBefore|createElement|550|FB|size|_trackSocial|push|_gaq|urlJson|www|urlCurl|total|async|media|line|20px|return|shorterTotal|replace|callback|each|send|font|description|https|STMBLPN|no|status|3px|related|tweet|pin|like|platform|widgets|border|click|parseInt|rendererPerso|color|style|html|fb|init|hover|sharrre|in|500|clearInterval|counter|defaults|s1|plusone|render||badge|add|shareTotal|align|faces|colorscheme|cssCount|padding|float|plugin_|renderer|twttr|margin|cssShare|hashtags|loadButtons|enableTracking|annotation|subscribe|50px|fjs|Event|create|setInterval|google|gapi|submit|SCRIPT|loadButton|35px|18px|display|block|none|json|background|fff|center|1px|solid|radius|img|openPopup|su|processWidgets|IN|tracking|id|edge|1000|tw|20url|popup|900|sharer|650|360|horizontal|getSocialJson|en|length|medium|hide|total_count|shares|enableCounter|enableHover|php|1e6|toFixed|1e3|location|className|it|pointer|services|666666|XFBML|inline|parse|Tweet|normal|indent|vertical|show|baseline|apis|getElementById|bottom|5px|overflow|hidden|ajax|urls|dataType|cache|ccc|DiggThisButton|decoration|7EACEE|40679C|rel|static|small|gif|api|alt|Delicious|Add|on|nofollow|external|cdn|go|setTimeout|___gcfg|20WHERE|20link_stat|20FROM|__DBW|20click_count|20comments_fbid|commentsbox_count|root|Pin|It|assets|pinit|connect|googleplus|20total_count|20comment_count|tall|20like_count|20share_count|20normalized_url|remove|unlike|message|net|jsonp|events|bind|format|LinkedInShare|all|SELECT|plus|hl|fql|15px|countserv|intent|urlinfo|xfbml|tools|diggthis|save|noui|jump|close|graph|DiggCompact|cws|token|isFramed|700|300|v2|en_US|extend|_defaults|_name|button_count|addClass|attr|US|try|top|catch|feeds|93px|links|getJSON|u00c2|u00a0|right|getInfo|story|total_posts|26px|left|error|for|jssdk|box|12px|Arial|Helvetica|sans|Google|serif|simulateClick|update|fn|arguments|object|cursor|new|string|_|instanceof|apply|Array|slice|call|jQuery|window|document'.split('|'),0,{}))

/*Claim OwerShip javascript validation  */
jQuery(document).ready(function()
{
	//global vars
	jQuery("#claimer_name").focus();
	var claimerfrm = jQuery("#claim_listing_frm");
	var claimer_name = jQuery("#claimer_name");
	var claimer_nameInfo = jQuery("#claimer_nameInfo");
	var claimer_email = jQuery("#claimer_email");
	var claimer_emailInfo = jQuery("#claimer_emailInfo");
	var claim_msg = jQuery("#claim_msg");
	var claim_msgInfo = jQuery("#claim_msgInfo");
	//On blur
	claimer_name.blur(validate_claimer_name);
	claimer_email.blur(validate_claimer_email);
	claim_msg.blur(validate_claim_msg);
	//On Submitting
	var validate_claim = '';
	claimerfrm.submit(function()
	{
		/*validate user field in claim form if user is logged in.*/
		if(jQuery('#claimer_id').val() == '' || jQuery('#claimer_id').val() <= 0 )
		{
			var validate_name = validate_claimer_name(is_submit=1);
			var validate_email = validate_claimer_email(is_submit=1);
			if(validate_name &&  validate_email)
			{
				validate_claim = true
			}
			else
			{
				validate_claim = false;
			}
		}
		else
		{
			validate_claim =  true;
		}
		if(validate_claim & validate_claim_msg() )
		{
			document.getElementById('process_claimownership').style.display="block";
			var claimerfrm_data = claimerfrm.serialize();				
			jQuery.ajax({
				url:ajaxUrl,
				type:'POST',
				data:'action=tevolution_claimowner_ship&' + claimerfrm_data,
				success:function(results) {	
					document.getElementById('process_claimownership').style.display="none";					
					if(results==1){
						jQuery('#claimownership_msg').html(captcha_invalid_msg);	
					}else{
						jQuery('#claimownership_msg').html(results);
						setTimeout(function(){
									jQuery("#lean_overlay").fadeOut(200);
									jQuery('#tmpl_claim_listing').removeClass('open');
									jQuery('#tmpl_claim_listing').attr('style','');
									jQuery('.reveal-modal-bg').css('display','none');
									jQuery('#claimownership_msg').html('');
									claimer_name.val('');
									claimer_email.val('');
									jQuery('.claim_ownership').html('<p class="claimed">'+already_claimed_msg+'</p>');
									jQuery('#claimer_contact').val('');
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
	} );
	//validation functions
	function validate_claimer_name(is_submit)
	{
		if(claimer_name.val() == '')
		{
			claimer_name.addClass("error");
			claimer_nameInfo.text(fullname_error_msg);
			claimer_nameInfo.addClass("message_error2");
			return false;
		}
		else
		{
			if(claimer_name != '' && ( jQuery('#claimer_id').val() == '' || jQuery('#claimer_id').val() <= 0 )){
				
				jQuery('.user_fname_spin').remove();
				jQuery( "input#claimer_name" ).css('display','inline' );
				if(is_submit == '')
					jQuery( "input#claimer_name" ).after( "<i class='fa fa-circle-o-notch fa-spin user_fname_spin ajax-fa-spin'></i>" );
				chknameRequest = jQuery.ajax({
				url:ajaxUrl,
				type:'POST',
				async: true,
				data:'action=tmpl_ajax_check_user_email&user_fname='+claimer_name.val(),
				beforeSend : function(){
					if(chknameRequest != null){
						chknameRequest.abort();
					}
				},
				success: function(data) { 
					var fname = data.split(",");
						if(fname[1] == 'fname')
						{
							if(fname[0] > 0)
							{ 
								document.getElementById("claimer_nameInfo").innerHTML = user_fname_error+user_login_link;
								document.getElementById("claimer_name_already_exist").value = 0;
								jQuery("#claimer_nameInfo").addClass('message_error2');
								jQuery("#claimer_nameInfo").removeClass('available_tick');

								reg_name=0;
							}
							else
							{	
								document.getElementById("claimer_nameInfo").innerHTML = user_fname_verified;
								document.getElementById("claimer_name_already_exist").value = 1;
								jQuery("#claimer_nameInfo").removeClass('message_error2');
								jQuery("#claimer_nameInfo").addClass('available_tick');
								if(jQuery("#claim_listing_frm div").size() == 2 && checkclick)
								 {
									 document.claim_listing_frm.submit();
								 }
								 reg_name=1;
							}
						}
					jQuery('.user_fname_spin').remove();
					//return true;
				}
				});
				if(reg_name ==1){
					return true;
				}else{
					return false;
				}
			}else{
		
				claimer_name.removeClass("error");
				claimer_nameInfo.text("");
				claimer_nameInfo.removeClass("message_error2");
				return true;
			}
		
		}
	}
	function validate_claimer_email(is_submit)
	{ 
		var isvalidemailflag = 0;
		if(claimer_email.val() == '')
		{
			isvalidemailflag = 1;
		}
		else
		{
			if(claimer_email.val() != '')
			{
				var a = claimer_email.val();
				var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				//if it's valid claimer_email
				if(filter.test(a))
				{
					isvalidemailflag = 0;
				}
				else
				{
					isvalidemailflag = 1;
				}
			}
		}
		if(isvalidemailflag == 1)
		{
			claimer_email.addClass("error");
			if(claimer_email.val() ==''){
				claimer_emailInfo.text(email_balnk_msg);
			}else{
				claimer_emailInfo.text(email_error_msg);
			}
			claimer_emailInfo.addClass("message_error2");
			return false;

		}
		else
		{
			if(claimer_email.val() != '')
			{
				var a = claimer_email.val();
				var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				//if it's valid claimer_email
				if(filter.test(a))
				{
					isvalidemailflag = 0;
						
					/* check the email entered is already exists or not */
					if(claimer_email != ''  && ( jQuery('#claimer_id').val() == '' || jQuery('#claimer_id').val() <= 0 )){
				
						jQuery('.user_email_spin').remove();
						jQuery( "input#claimer_email" ).css('display','inline' );
						if(is_submit == '')
							jQuery( "input#claimer_email" ).after( "<i class='fa fa-circle-o-notch fa-spin user_email_spin ajax-fa-spin'></i>" );
						chknameRequest = jQuery.ajax({
						url:ajaxUrl,
						type:'POST',
						async: true,
						data:'action=tmpl_ajax_check_user_email&user_email='+claimer_email.val(),
						beforeSend : function(){
							if(chknameRequest != null){
								chknameRequest.abort();
							}
						},
						success: function(data) { 
							var email = data.split(",");
								if(email[1] == 'email')
								{
									if(email[0] > 0)
									{ 
										document.getElementById("claimer_emailInfo").innerHTML = user_email_error;
										document.getElementById("claimer_name_already_exist").value = 0;
										jQuery("#claimer_emailInfo").addClass('message_error2');
										jQuery("#claimer_emailInfo").removeClass('available_tick');
										reg_email=0;
									}
									else
									{	
										document.getElementById("claimer_emailInfo").innerHTML = user_email_verified;
										document.getElementById("claimer_name_already_exist").value = 1;
										jQuery("#claimer_emailInfo").removeClass('message_error2');
										jQuery("#claimer_emailInfo").addClass('available_tick');
										if(jQuery("#claim_listing_frm div").size() == 2 && checkclick)
										 {
											 document.claim_listing_frm.submit();
										 }
										 reg_email=1;
									}
								}
							jQuery('.user_email_spin').remove();	
							//return true;
						}
						});
						if(reg_email ==1){
							return true;
						}else{
							return false;
						}
					}
				}
				else
				{
					isvalidemailflag = 1;
				}
			}else{
				claimer_email.addClass("error");
				claimer_emailInfo.text(email_error_msg);
				claimer_emailInfo.addClass("message_error2");
				return false;
			}
		}
	}
	function validate_claim_msg()
	{
		if(jQuery("#claim_msg").val() == '')
		{
			claim_msg.addClass("error");
			claim_msgInfo.text(claim_error_msg);
			claim_msgInfo.addClass("message_error2");
			return false;
		}
		else
		{
			claim_msg.removeClass("error");
			claim_msgInfo.text("");
			claim_msgInfo.removeClass("message_error2");
			return true;
		}
	}
} );
/*END Claim OwerShip java script validation  */

/* Pop up close button js*/
jQuery(function() { 
	
	if(jQuery("#tmpl_reg_login_container")){
		jQuery("#tmpl_reg_login_container .modal_close").click(function(){	jQuery('#tmpl_reg_login_container').attr('style',''); 	jQuery('.reveal-modal-bg').css('display','none'); jQuery('#tmpl_reg_login_container').removeClass('open'); });
	}
	
	if(jQuery("#lean_overlay")){
		jQuery("#lean_overlay").click(function(){if(captcha) {jQuery('#recaptcha_widget_div').html(captcha); }});
	}
	
	if(jQuery(".modal_close")){
		jQuery(".modal_close").click(function(){if(captcha) {jQuery('#recaptcha_widget_div').html(captcha);}});
	}
	
	jQuery(".reveal-modal-bg").live('click', function(e){
		if(captcha) {jQuery('#recaptcha_widget_div').html(captcha);}
	});
	
	if(jQuery("#tmpl_send_inquiry")){
		jQuery("#tmpl_send_inquiry .modal_close").click(function(){	jQuery('#tmpl_send_inquiry').attr('style',''); 	jQuery('.reveal-modal-bg').css('display','none'); jQuery('#tmpl_send_inquiry').removeClass('open'); });
		
		tmpl_close_popup();
	}
	
	if(jQuery("#claim-header")){
		jQuery("#claim-header .modal_close").click(function(){ jQuery('#tmpl_claim_listing').attr('style',''); 	jQuery('.reveal-modal-bg').css('display','none');  jQuery('#tmpl_claim_listing').removeClass('open'); });
		
		tmpl_close_popup();
	}
	
	if(jQuery("#tmpl_send_to_frd")){
		jQuery("#tmpl_send_to_frd .modal_close").click(function(){	jQuery('#tmpl_send_to_frd').attr('style',''); 	jQuery('.reveal-modal-bg').css('display','none'); jQuery('#tmpl_send_to_frd').removeClass('open'); });
		tmpl_close_popup();
	}

});
/* to close the pop up */
function tmpl_close_popup(){
	jQuery(".reveal-modal-bg").click(function(){ 	jQuery('.reveal-modal').attr('style',''); 	jQuery('.reveal-modal-bg').css('display','none'); jQuery('.eveal-modal').removeClass('open'); });
}

/* function write the script to design select box on search page */
jQuery( window ).load(function() {
	jQuery( ".sort_options select,#searchform select,#submit_form select,.search_filter select,.tmpl_search_property select,.widget_location_nav select,#srchevent select,#header_location .location_nav select,.horizontal_location_nav select,.widget select" ).wrap( "<div class='select-wrap'></div>" );
	jQuery( ".peoplelisting li" ).wrapInner( "<div class='peopleinfo-wrap'></div>");
	if (!jQuery.browser.opera) {
	       // select element styling
	      jQuery('.sort_options select,#searchform select,#submit_form select,.search_filter select,.tmpl_search_property select,.widget_location_nav select,#srchevent select,#header_location .location_nav select,.horizontal_location_nav select,.widget select').each(function(){
		var title = jQuery(this).attr('title');
		/*check multiple select attribute if its found then return script */
		if(jQuery(this).attr('multiple')=='multiple'){
			return ;	
		}
		var title = jQuery('option:selected',this).text();
		jQuery(this)
		    .css({'z-index':10,'opacity':0,'-khtml-appearance':'none'})
		    .after('<span class="select">' + title + '</span>')
		    .change(function(){
		        val = jQuery('option:selected',this).text();
							jQuery(this).next().text(val);
		        })
	      });

	  };
});


/* JavaScript Document*/
jQuery(document).ready(function() {
	/* auto-complete*/
    var autocomplet = null;
	var search_text='';
    jQuery("#searchform .range_address").autocomplete({
       minLength: 0,
	 create: function () {
		jQuery(this).data('ui-autocomplete')._renderItem = function (ul, item) {				
			return jQuery("<li>").addClass('instant_search').append("<a>").attr("href",item.url).html( item.label ).appendTo(ul);                
		};
	},
       source: function( request, response ) {		  
			if(search_text=='' || request.term!=''){
				search_text=request.term;
			}
			
			var post_type=jQuery('.miles_range_post_type').val();
			autocomplet=jQuery.ajax({
				url:tevolutionajaxUrl,
				type:'POST',
				dataType: "json",
				data:'action=tevolution_autocomplete_address_callBack&search_text='+search_text+'&post_type='+post_type,
				beforeSend : function(){
					if(autocomplet != null){
						autocomplet.abort();
					}
				},
				success:function(data) {
					 response(jQuery.map(data.results, function(item) {
                            return {
                                label: item.title,
                                value: item.label,
                                url: item.url,								

                            };
                        }));
				}
			});			
		},
        autoFocus: true,
        scroll: true,
		select: function( event, ui ) {			
			if ( ui.item.url !== '#' ) {
				//jQuery('#radius-range').trigger('slidestop');
			} else {
				return true;
			}
		},
		open: function(event, ui) {		
			var acData = jQuery(this).data('uiAutocomplete');
			acData
					.menu
					.element
					.find('a')
					.each(function () {
						var $self = jQuery( this ),
							keywords = jQuery.trim( acData.term ).split( ' ' ).join('|');
						$self.html($self.text().replace(new RegExp("(" + keywords + ")", "gi"), '$1'));
					});
			jQuery(event.target).removeClass('sa_searching');
		},		
    }).focus(function() {
        jQuery(this).autocomplete("search", "");
    });

    jQuery('iframe').each(function(){
		var url = jQuery(this).attr("src");
		if(url){
			if(url.indexOf('?') > -1)
			{
				jQuery(this).attr("src",url+"&wmode=transparent");
			}
			else
			{
				jQuery(this).attr("src",url+"?wmode=transparent");
			}
		}
	});
});

/*add thousand seperator*/
function tmpl_thousandseperator(amt)
{
	if(num_decimals == 0)
	{
		amt = parseFloat(amt).toFixed(2);
	}
	var parts = amt.split('.');
	var part1 = parts[0].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1"+thousands_sep);
	var part2 = parts[1];
	if(num_decimals == 0)
	{
		return part1;
	}
	else
	{
		return part1 + '.' + part2;
	}
}

function toggle_post_type(){
	var div1 = document.getElementById('toggle_postID');
	if (div1.style.display == 'none') {
		div1.style.display = 'block';
	} else {
		div1.style.display = 'none';
	}
	
	if(document.getElementById('toggle_post_type').getAttribute('class') == 'paf_row toggleoff'){		
		jQuery("#toggle_post_type").removeClass("paf_row toggleoff").addClass("paf_row toggleon");
	} else {		
		jQuery("#toggle_post_type").removeClass("paf_row toggleon").addClass("paf_row toggleoff");
	}
	
	if(document.getElementById('toggle_post_type').getAttribute('class').search('toggleoff')!=-1 && document.getElementById('toggle_post_type').getAttribute('class').search('map_category_fullscreen') !=-1){		
		jQuery("#toggle_post_type").removeClass("paf_row toggleoff map_category_fullscreen").addClass("paf_row toggleon map_category_fullscreen");
	} 
	if(document.getElementById('toggle_post_type').getAttribute('class').search('toggleon') !=-1 && document.getElementById('toggle_post_type').getAttribute('class').search('map_category_fullscreen') !=-1){
		jQuery("#toggle_post_type").removeClass("paf_row toggleon map_category_fullscreen").addClass("paf_row toggleoff map_category_fullscreen");
	}
}


/* Change listing paggination */
jQuery('#listpagi .search_pagination .page-numbers').live('click',function(e){
	e.preventDefault();
	post_link=jQuery(this).attr('href');	
	post_link= post_link.replace("#038;", "&"); 
	jQuery('.search_result_listing').addClass('loading_results');
	var from_data=jQuery(".tmpl_filter_results").serialize();
	
	jQuery.ajax({
		url:post_link+'&'+from_data,
		type:'POST',
		async: true,
		//data:url_data,
		success:function(results){
			jQuery('.search_result_listing').removeClass('loading_results');
			jQuery('.search_result_listing').html(results);
			jQuery("html, body").animate({ scrollTop: 0 }, 200);
		}
	});	
	jQuery.ajax({
		url:post_link+'&data_map=1&'+from_data,
		type:'POST',
		async: true,
		dataType: 'json',
		success:function(results){			
			googlemaplisting_deleteMarkers();
			markers=results.markers;			
			templ_add_googlemap_markers(markers);
			//jQuery('.search_result_listing').html(results);		
		},		
	});
	return false;
});	