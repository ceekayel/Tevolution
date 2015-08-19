/*
 * manage submit form script
 */
jQuery(document).ready(function(event){
	var submit_form_button = 0;
	/* Submit form using jquery submit */	
	jQuery("#submit_form_button").click(function(e){
		/* can't submit form whitout logged in*/
		if(!user_login)
		{
			jQuery('.payment_head').append('<p id="error_message" class=\"message_error2\"></p>')
			jQuery('#error_message').html(submit_form_error);
			jQuery('#error_message').css('width','100%');
			jQuery('#error_message').css('display','inline-block');
			jQuery('#error_message').delay(5000).fadeOut('slow');
			return false
		}
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
		/* submit form after payment gateway condition is satisfied*/
		if(jQuery('#total_price').val() <=0 || validate_gateway)
		{
			jQuery('#submit_form').submit();
		}
		return false;
	});
				
	// Perform AJAX login on form submit
	jQuery('form#submit_form #submit_form_login').live('click', function(e){
		jQuery('.wp-editor-container textarea').each(function(){
			var name=jQuery(this).attr('id');
			jQuery('<input>').attr({
				type: 'hidden',
				id: name,
				name: name,
				value: tinyMCE.get(name).getContent()
			}).appendTo('#submit_form');
		});
		var submit_from = jQuery('form#submit_form').serialize();
		var username=jQuery('form#submit_form #user_login').val(); 
		var password= jQuery('form#submit_form #user_pass').val();
		var security= jQuery('form#submit_form #security').val() ;
		var pkg_id = jQuery('form#submit_form #pkg_id').val();
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxUrl,
			data:'action=ajaxlogin&username='+username+'&password='+password+'&security='+ security+'&pkg_id='+pkg_id+'&'+submit_from,			
			success: function(data){
				if(data.loggedin)
				{					
					if(data.package_type == 2 && data.selected_package_type==2)
					{
						window.location.reload();
						return;
					}
					user_login = true;
					currentStep = 'auth';
					jQuery('div#step-auth').addClass('complete');
					if((parseFloat(jQuery('#total_price').val()) <=0 || jQuery('#total_price').val() == '' || jQuery('#package_free_submission').val() >0 ) )
					{
						submit_form_button = 1;
					}else if(front_submission == 0){
						addFinishStep('step-auth');
						showNextStep();
					}
					jQuery('p.status').css('display','block');
					jQuery('p.status').text(data.message);
					jQuery('#common_error,.common_error_not_login').html('');
					
					jQuery('#loginform').css('display','none');
					jQuery( "#login_user_meta" ).remove();
				}
				else
				{
					jQuery('p.status').css('display','block');
					jQuery('p.status').css('color','red');
					jQuery('p.status').text(data.message);
				}
				if(submit_form_button == 1)
				{
					jQuery('#submit_form_button').trigger('click');
				}
			}
			
		});
		e.preventDefault();
		
	});
	
	/*function to get the querystring from url*/
	function getUrlVars()
	{
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++)
		{
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	}
	var price_package_id = getUrlVars()["pkg_id"];
	
	finishStep = [];
	currentStep = 'plan';
	var pkg_post = '';
	/*set package id after selection certain package*/
	jQuery('.select-plan').live('click',function(event){
		event.preventDefault();
		finishStep = [];
		var $target = jQuery(event.currentTarget),
		$ul = $target.closest('ul'),
		$ula = $target.closest('ul a'),
		amount = $ul.attr('data-price'),
		pkg_type =  $ul.attr('data-type'),
		package_free_submission =  $ul.attr('data-free'),
		pkg_subscribed =  $ul.attr('data-subscribed'),
		$step = $ul.closest('div.step'),
		view = this;
		package_id = $ul.attr("data-id");
		upgrade = $ula.attr("data-upgrade");	
		jQuery('#pkg_id').val(package_id);
		jQuery('#pkg_type').val(pkg_type);
		jQuery('#package_free_submission').val(package_free_submission);
		jQuery('#upgrade').val(upgrade);
		jQuery('#total_price').val(amount);
		if($ul.attr("data-post"))
		{
			pkg_post = $ul.attr("data-post");
		}
		currentStep = 'plan';
		jQuery('#step-post').css('display','block');
		if (parseInt(jQuery('#step-auth').length) === 0)
		{
			jQuery('#select_payment').html('3');
		}
		jQuery("#plan").find("ul.selected").removeClass('selected');
		/*added class to highlight the selected package*/
		$target.parents('ul').addClass('selected');
		/*fetch category as per selected price package*/
		jQuery('#submit_category_box').css('opacity','0.5');
		jQuery('#submit_category_box').addClass('overlay_opacity');
		var submit_from = jQuery('form#submit_form').serialize();
		jQuery.ajax({
			url:ajaxUrl,
			type:'POST',
			async: true,
			data:'action=tmpl_tevolution_submit_from_category&pkg_subscribed='+pkg_subscribed+'&' + submit_from,
			success:function(results){
				jQuery('#submit_category_box').html(results);
				jQuery('#submit_category_box').css('opacity','');
				jQuery('#submit_category_box').removeClass('overlay_opacity');
			}
		});
		/*fetch selected price package featured option*/
		jQuery.ajax({
			url:ajaxUrl,
			type:'POST',
			async: true,
			data:'action=tmpl_tevolution_submit_from_package_featured_option&front_end=1&pkg_subscribed='+pkg_subscribed+'&' + submit_from,
			success:function(results){
				
				if(jQuery('#package_free_submission').val() <= 0 || jQuery('#package_free_submission').val() == '' || jQuery('#completed').val() == 'completed' || pkg_type==1 || upgrade == 'upgrade' )
				{
					jQuery('#show_featured_option').css('display','block');
					jQuery('div#show_featured_option').html(results);
					if(parseFloat(jQuery('#total_price').val()) >0)
					{
						jQuery('#submit_coupon_code').css('display','block');
						jQuery('#price_package_price_list').css('display','block');
					}
					else
					{
						jQuery('#submit_coupon_code').css('display','none');
						jQuery('#price_package_price_list').css('display','none');
					}
				}
			}
		});
		// hide all content step
		$ul.closest('div.step-wrapper').addClass('complete');
		// add step plan to finish array
		addFinishStep('step-plan');
		// show next step
		showNextStep();
		if(jQuery.isFunction(initialize)){				
			initialize();
			changeMap();
		}
	});
	
	jQuery('#step-plan.complete a').live('click',function(event){
		// add step plan to finish array
		addFinishStep('step-plan');
		// show next step
		showNextStep();
		if(jQuery.isFunction(initialize)){				
			initialize();
			changeMap();
		}								 
	});
	
	function addFinishStep(step) {
		if (typeof finishStep === 'undefined') {
			finishStep = [];
		}
		finishStep.push(step);
	}
	function showNextStep()
	{
		var next = 'post',
		view = this;
		jQuery('.step-wrapper').removeClass('current');
		jQuery('.content').slideUp(500, function() {
			// current step is plan
			if (currentStep === 'plan') {
				if((jQuery('#pkg_type').val() == 1 || pkg_post == 1 || jQuery('#post_upgrade').val() == 'post_upgrade') && jQuery('#upgrade').val() != 'upgrade' )
				{
					next = 'post';
					if (parseInt(jQuery('#step-auth').length) === 0)
					{
						user_login = true;
					}
					else
					{
						user_login = false;
					}
				}
				else if((jQuery('#pkg_type').val() == 2 && jQuery('#package_free_submission').val() == '' && front_submission == 0 ) || ( jQuery('#pkg_type').val() == 2 && jQuery('#upgrade').val() == 'upgrade' && front_submission == 0)  )
				{
					jQuery('#step-post').css('display','none');
					if(jQuery('#upgrade_price').length > 0)
						jQuery('#total_price').val(jQuery('#upgrade_price').val());
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
				else if((jQuery('#pkg_type').val() == 2 && jQuery('#package_free_submission').val() != '')  || ( jQuery('#pkg_type').val() == 2 && jQuery('#upgrade').val() != 'upgrade'))
				{
					next = 'post';
					jQuery('#show_featured_option').css('display','none');
					jQuery('#submit_coupon_code').css('display','none');
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
					 goToByScroll('step-auth');
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
			/*show payment tab if total price is greater tha zero*/
			if(parseFloat(jQuery('#total_price').val().split(thousands_sep).join('')) > 0)
			{
				jQuery('#step-payment').css('display','block');
			}
			else
			{
				jQuery('#step-payment').css('display','none');
			}
			// show next step
			jQuery('.step-' + next + '  .content').slideDown(10).end();
			jQuery('.step-' + next).addClass('current');
		});
	}

	jQuery('#continue_submit_from').live('click',function(event){
		event.preventDefault();
		var $target = jQuery(event.currentTarget),
		$ul = $target.closest('ul'),
		view = this;
		currentStep = 'post';
		jQuery('div#step-post').addClass('complete');		
		if((parseFloat(jQuery('#total_price').val()) <=0 || jQuery('#total_price').val() == '' ||  (jQuery('#completed').val() != 'completed'  && jQuery('#package_free_submission').val() > 0) ) && parseInt(jQuery('#step-auth').length) === 0 )
		{
			user_login = true;
			jQuery('#submit_form_button').trigger('click');			
		}else{
			addFinishStep('step-post');
			showNextStep();
          // Call the scroll function
       		 goToByScroll('step-payment'); 
		}
	});


	jQuery('.step-heading').bind('click',function(event){
		event.preventDefault();
		var $target = jQuery(event.currentTarget),
		$wrapper = $target.parents('.step-wrapper'),
		view = this,
		select = $wrapper.attr('id');
		
		// step post
		if (select == 'step-post') {
			if (jQuery('#step-auth').length > 0 && finishStep.length < 1) return false;
			if (parseInt(jQuery('#step-auth').length) === 0 && parseInt(finishStep.length) < 1){ 
				return false;
			};
		}
		// step authentication
		if (select == 'step-auth') {
			if (finishStep.length < 2)
				return;
			goToByScroll('step-auth');
		}
		// step payment

		if (select == 'step-payment') {
			if (jQuery('#step-auth').length > 0 && finishStep.length < 3 && jQuery('#pkg_type').val() == 1 || !user_login) return;
			if (jQuery('#step-auth').length == 0 && finishStep.length < 2 && (jQuery('#pkg_type').val() == 1 || jQuery('#pkg_type').val() == '')) return;
			if (jQuery('#step-auth').length > 0 && finishStep.length < 2 && jQuery('#pkg_type').val() == 2 || !user_login ) return;
			if (jQuery('#step-auth').length == 0 && finishStep.length < 2 &&  (jQuery('#pkg_type').val() == 2 || pkg_post == 1)) return;
			if (jQuery('#step-auth').length == 0 && finishStep.length < 1 &&  (jQuery('#pkg_type').val() == 2 || jQuery('#pkg_type').val() == '')) return;
			goToByScroll('step-payment');
		}
	
		if (!$target.closest('div').hasClass('current')) {
			// toggle content of selected step
			jQuery('.step-wrapper').removeClass('current');
			jQuery('.content').slideUp(500);
			$target.closest('div').addClass('current').find('.content').slideDown(500);
			return false;
		}
	});
	
	jQuery(window).load( function( response, status ){
		var cat_wise_fields = jQuery('#cat_fields').val();
		if(jQuery('input[name=action]').val() == 'edit' && jQuery('#submit_pid').val() != '' && cat_wise_fields  == 1)
		{
			jQuery(".category_label input[name^='category'], .category_label input[name='selectall'],.category_label select[name^='category']").trigger('change');
		}
		/*if url contains package id*/
		if(price_package_id)
		{
			/*function to select the pacakge with the package id passed in url*/
			select_price_package(price_package_id);
		}
		var submit_from = jQuery('form#submit_form').serialize();
		jQuery.ajax({
			url:ajaxUrl,
			type:'POST',
			async: true,
			data:'action=tmpl_tevolution_select_pay_per_subscription_price_package&' + submit_from,
			success:function(results){
				if(Math.floor(results) == results && jQuery.isNumeric(results)) 
				{
					jQuery('.select-plan').trigger('click');
					if (parseInt(jQuery('#step-auth').length) === 0)
					{	
						jQuery('#select_payment').html('2');
					}
					else
					{
						jQuery('#span_user_login').html('2');
						jQuery('#select_payment').html('3');
					}
				}
				var pkg_price = parseInt(jQuery('#price_package_price_list #pkg_price').text());
				var cat_price = parseInt(jQuery('#price_package_price_list #cat_price').text());
				if(cat_price > 0 && pkg_price > 0){
					var totalp = pkg_price + cat_price;
					jQuery('#cat_price_total_price #result_price').html(thousandseperator1(totalp.toFixed(num_decimals)));
					jQuery('#total_price').html(totalp);
					jQuery('#cat_price_total_price').show();
				}
			}
		});
		return true;
	});
	
	/*add thousand seperator*/
	function thousandseperator1(amt)
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
	
	function select_price_package(pkg_id)
	{
		$ul = jQuery("ul").find("[data-id='" + pkg_id + "']"),
		amount = $ul.attr('data-price'),
		pkg_type =  $ul.attr('data-type'),
		$step = $ul.closest('div.step'),
		view = this;
		package_id = $ul.attr("data-id");
		
		currentStep = 'plan';
		/*added class to highlight the selected package*/
		$ul.closest('ul').addClass('selected');

		pkg_type =  $ul.closest('ul').attr('data-type');
		upgrade =  $ul.closest('ul a').attr('data-upgrade');
		if($ul.closest('ul').attr('data-free'))
		{
			package_free_submission =  $ul.closest('ul').attr('data-free');
		}
		else
		{
			package_free_submission = '';
		}
		jQuery('#pkg_id').val(package_id);
		jQuery('#pkg_type').val(pkg_type);
		jQuery('#package_free_submission').val(package_free_submission);
		jQuery('#upgrade').val(upgrade);
		if($ul.closest('ul').attr('data-post'))
		{
			pkg_post = $ul.closest('ul').attr('data-post');
		}
		jQuery('#total_price').val($ul.closest('ul').attr('data-price'));
		if(parseFloat(jQuery('#total_price').val()) >0)
		{
			jQuery('#submit_coupon_code').css('display','block');
			jQuery('#price_package_price_list').css('display','block');
			
		}
		else
		{
			jQuery('#submit_coupon_code').css('display','none');
			jQuery('#price_package_price_list').css('display','block');
		}
		/*fetch category as per selected price package*/
		jQuery('#submit_category_box').css('opacity','0.5');
		jQuery('#submit_category_box').addClass('overlay_opacity');
		var submit_from = jQuery('form#submit_form').serialize();
		jQuery.ajax({
			url:ajaxUrl,
			type:'POST',
			async: true,
			data:'action=tmpl_tevolution_submit_from_category&' + submit_from,
			success:function(results){
				
				jQuery('#submit_category_box').html(results);
				jQuery('#submit_category_box').css('opacity','');
				jQuery('#submit_category_box').removeClass('overlay_opacity');
			}
		});
		/*fetch selected price package featured option*/
		jQuery.ajax({
			url:ajaxUrl,
			type:'POST',
			async: true,
			data:'action=tmpl_tevolution_submit_from_package_featured_option&front_end=1&' + submit_from,
			success:function(results){
				
				if(jQuery('#package_free_submission').val() <= 0 || jQuery('#package_free_submission').val() == '' || pkg_type==1 || upgrade == 'upgrade')
				{
					jQuery('#show_featured_option').css('display','block');
					jQuery('div#show_featured_option').html(results);
					if(parseFloat(jQuery('#total_price').val()) >0)
					{
						jQuery('#submit_coupon_code').css('display','block');
						jQuery('#price_package_price_list').css('display','block');
						
					}
				}
			}
		});
		// hide all content step
		$ul.closest('div.step-wrapper').addClass('complete');
		// add step plan to finish array
		addFinishStep('step-plan');
		// show next step
		showNextStep();		
	
	}
	
});



/* Display select all category function on submit page */
function displaychk(){ 
	dml=document.forms['submit_form'];
	chk = document.getElementsByName('category[]');
	len = chk.length;
	if(document.getElementById("selectall").checked  == true) {
		for (i = 0; i < len; i++)
			chk[i].checked = true ;
		
		jQuery('#category_error').html("");
		jQuery('#category_error').removeClass('message_error2');	
	} else {
		for (i = 0; i < len; i++)
		chk[i].checked = false ;
	}
}


/* Display custom fields using selected category wise in submit form */
jQuery(document).ready(function(event){
var category_ajax=null;
var cat_fields = jQuery('#cat_fields').val();
if(cat_fields ==1){
	jQuery(".category_label input[name^='category'], .category_label input[name='selectall'],.category_label select[name^='category']").live('change', function (e) {
		var category_id='';
		/* get the submit post type*/
		var post_type=jQuery('#submit_post_type').val();
		if(jQuery('input[name=action]').val() == 'edit' && jQuery('#submit_pid').val() != '' && cat_fields  == 1)
		{
			var pakg_id = jQuery('#monetize_pkg_id').val();
		}
		else
		{
			var pakg_id = jQuery('#pkg_id').val();
		}
		var submit_page_id = jQuery('#submit_page_id').val();
		var action_edit=jQuery('#action_edit').val();
		/* Get the selected category id from input checkbox type*/
		jQuery("input[name^='category']").each(function(){
			jQuery(".category_label input[name^='selectall']").live('change', function (e) {
				displaychk();
			});
			if (jQuery(this).attr('checked')){
				category_id+=jQuery(this).attr('data-value')+',';			
			}
		});
		
		/* get the category id from select box  type*/
		jQuery("select[name^='category'] option:selected").each(function(){		
			category_id+=jQuery(this).attr('data-value')+',';			
		});
		
		/*Edit post listing id */
		var pid=jQuery('#submit_pid').val();
		var edit_id = '';
		if(pid)
		{
			edit_id = '&pid='+pid;
			user_login = true;
		}
		jQuery('.wp-editor-container textarea').each(function(){
		var name=jQuery(this).attr('id');
			jQuery('<input>').attr({
				type: 'hidden',
				id: name,
				name: name,
				class: 'remove_hidden_'+name,
				value: tinyMCE.get(name).getContent()
			}).appendTo('#submit_form');
		});	
		
		var submit_from = jQuery('form#submit_form').serialize();
		/*delete hidden field*/
		jQuery('.wp-editor-container textarea').each(function(){
			var name=jQuery(this).attr('id');
			jQuery('.remove_hidden_'+name).remove();
		});	
		
		/* Add class custom fields load to display image loader */
		jQuery('#submit_form_custom_fields').addClass('custom_fields_load');
		category_ajax =jQuery.ajax({
			url:ajaxUrl,
			type:'POST',
			async: true,
			data:submit_from+'&action=submit_category_custom_fields&category_id='+category_id+'&post_type='+post_type+'&front=1&action_edit='+action_edit+'&pakg_id='+pakg_id+'&submit_page_id='+submit_page_id+edit_id,	
			beforeSend : function(){
				if(category_ajax != null){
					category_ajax.abort();
				}
			},
			success:function(results){
				
				
				
				if(typeof tinyMCE !== 'undefined') tinyMCE.triggerSave();
				jQuery('#submit_form_custom_fields').removeClass('custom_fields_load');
				jQuery('#submit_form_custom_fields').html(results);
				if (jQuery( "#submit_form select" ).parent('.select-wrap').length == 0){
				jQuery( "#submit_form select" ).wrap( "<div class='select-wrap'></div>" );
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
				}	  
				if(jQuery.isFunction(initialize)){					
					initialize();					
					changeMap();
					
				}		
			}
		});
	});
}
});

/* Display preview submit form result */
jQuery('#preview_submit_from').live('click', function (e) {
	if(tinyMCE) tinyMCE.triggerSave();
	
	jQuery('.wp-editor-container textarea').each(function(){
		var name=jQuery(this).attr('id');
		jQuery('<input>').attr({
			type: 'hidden',
			id: name,
			name: name,
			value: tinyMCE.get(name).getContent()
		}).appendTo('#submit_form');
	});
	
	var submit_from = jQuery('form#submit_form').serialize();
	jQuery('div.preview_submit_from_data').html('<span><i class="fa fa-2x fa-circle-o-notch fa-spin"></i></span>');
	jQuery.ajax({
		url:ajaxUrl,
		type:'POST',
		async: true,
		data:submit_from+'&ptype=preview&action=tevolution_submit_from_preview',
		success:function(results){
			jQuery('div.preview_submit_from_data').html(results+'<a class="close-reveal-modal">&#215;</a>');
		}
	});
});
function goToByScroll(id){
	  // Reove "link" from the ID
	id = id.replace("link", "");
	  // Scroll
   jQuery('html,body').animate({
		scrollTop: jQuery("body").offset().top},
		'slow');
}
var social_login=0;
/* Social Media login script for submit form on social meadi login */
jQuery('#submit_form ul.social_media_login a').live('click',function (e){
	/*get the social media login link */
	var social_login_href=jQuery(this).attr('href');
	social_login=1;
	/*set social media login on submit form action */
	jQuery("#submit_form").attr("action",social_login_href);
	
	jQuery('.wp-editor-container textarea').each(function(){
		var name=jQuery(this).attr('id');
		jQuery('<input>').attr({
			type: 'hidden',
			id: name,
			name: name,
			value: tinyMCE.get(name).getContent()
		}).appendTo('#submit_form');
	});
	/*Submit form */
	jQuery('#submit_form').submit();
	return false;
});