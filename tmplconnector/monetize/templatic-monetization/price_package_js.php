<?php ob_start(); ?>
<script type="text/javascript">
/*
Name :show_featuredprice
Description : Return the total prices and add the calculation in span.
*/
<?php if(!isset($_REQUEST['action']) && $_REQUEST['action'] !='edit'){ ?>
	window.onload=function(){ if(document.getElementById('price_package_price_list')){document.getElementById('price_package_price_list').style.display =='none'; } };
<?php }  ?>
function show_featuredprice(pkid)
{

	if(document.getElementById("all_packages_error"))
	{
		jQuery("#all_packages_error").html("");
	}
	if (pkid=="")
	  {
	  document.getElementById("featured_h").innerHTML="";
	  return;
	  }else{
	  //document.getElementById("featured_h").innerHTML="";
		if(document.getElementById("process2"))
		{
	  		document.getElementById("process2").style.visibility ="";
		}
		document.getElementById('is_featured').style.display='none';
	  }
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  	xmlhttp=new XMLHttpRequest();
	  }
		else
	  {// code for IE6, IE5
	  	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
		xmlhttp.onreadystatechange=function()
	  {
	    if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			jQuery(this).parents('div.package');
			 theHtml = jQuery('div#process2').html();			
			if(document.getElementById("process2"))
			{
				document.getElementById("process2").style.visibility ="hidden";
			}
		
		var myString =xmlhttp.responseText;
		var myStringArray = myString.split("###RAWR###");  /* split with ###RAWR### because return result is concated with ###RAWR###*/
		document.getElementById('alive_days').value = myStringArray[6];
		
		if(myStringArray[5] == 1){
		if(document.getElementById('is_featured').style.display == "none")
		{
			document.getElementById('is_featured').style.display="";
		}
			if(document.getElementById('price_package_price_list'))
			{ 
				if(myStringArray[1] > 0 ||  myStringArray[0] > 0 || myStringArray[4] > 0)
				{
					if(document.getElementById('moderate_comment'))
					{
						if(parseFloat(myStringArray[8] ) > 0 || myStringArray[1] > 0 ||  myStringArray[0] > 0 || myStringArray[4] > 0){
							if(parseFloat(document.getElementById('all_cat_price').value) > 0)
							{
								document.getElementById('cat_price').style.display = "";
								document.getElementById('cat_price_id').style.display = "";
								document.getElementById('before_cat_price_id').style.display = "";
								document.getElementById('pakg_add').style.display = "";
							}
							else
							{
								document.getElementById('cat_price').style.display = "none";
								document.getElementById('cat_price_id').style.display = "none";
								document.getElementById('before_cat_price_id').style.display = "none";
								document.getElementById('pakg_add').style.display = "none";
							}
							if(myStringArray[4] > 0)
							{
								document.getElementById('before_pkg_price_id').style.display = "";
								document.getElementById('pkg_price').style.display = "";
								document.getElementById('pkg_price_id').style.display = "";
							}
							else
							{
								document.getElementById('before_pkg_price_id').style.display = "none";
								document.getElementById('pkg_price').style.display = "none";
								document.getElementById('pkg_price_id').style.display = "none";
								document.getElementById('pakg_add').style.display = "none";
							}
							if(document.getElementById("all_cat_price").value > 0)
							{
								document.getElementById('cat_price_total_price').style.display = "";
							}
							document.getElementById('price_package_price_list').style.display = "block";
							
						}
					}
					else
					{
						if(parseFloat(document.getElementById('all_cat_price').value) > 0)
						{
							document.getElementById('cat_price').style.display = "";
							document.getElementById('cat_price_id').style.display = "";
							document.getElementById('before_cat_price_id').style.display = "";
							document.getElementById('pakg_add').style.display = "";
						}
						else
						{
							document.getElementById('cat_price').style.display = "none";
							document.getElementById('cat_price_id').style.display = "none";
							document.getElementById('before_cat_price_id').style.display = "none";
							document.getElementById('pakg_add').style.display = "none";
						}
						if(myStringArray[4] > 0)
						{
							document.getElementById('before_pkg_price_id').style.display = "";
							document.getElementById('pkg_price').style.display = "";
							document.getElementById('pkg_price_id').style.display = "";
							document.getElementById('pakg_add').style.display = "";
						}
						else
						{
							document.getElementById('before_pkg_price_id').style.display = "none";
							document.getElementById('pkg_price').style.display = "none";
							document.getElementById('pkg_price_id').style.display = "none";
							document.getElementById('pakg_add').style.display = "none";
						}
						if(document.getElementById("all_cat_price").value > 0)
						{
							document.getElementById('cat_price_total_price').style.display = "";
						}
						document.getElementById('price_package_price_list').style.display = 'block';
					}
				}
				else
				{
					if(parseFloat(document.getElementById('all_cat_price').value) > 0)
					{
						document.getElementById('cat_price').style.display = "";
						document.getElementById('cat_price_id').style.display = "";
						document.getElementById('before_cat_price_id').style.display = "";
						document.getElementById('price_package_price_list').style.display = 'block';
						document.getElementById('cat_price_total_price').style.display = "";
						document.getElementById('pakg_add').style.display = "";
					}
					else
					{
						document.getElementById('cat_price').style.display = "none";
						document.getElementById('cat_price_id').style.display = "none";
						document.getElementById('before_cat_price_id').style.display = "none";
						document.getElementById('price_package_price_list').style.display = 'none';
						document.getElementById('cat_price_total_price').style.display = "none";
						document.getElementById('pakg_add').style.display = "none";
					}
				}
			}
			if(document.getElementById('moderate_comment'))
			{
				if(myStringArray[7] == 1 && (parseFloat(myStringArray[8]) > 0 )){
					document.getElementById('moderate_comment').style.display = "block";
				}
				else
				{
					document.getElementById('moderate_comment').style.display = "none";
				}
			}
			document.getElementById('featured_c').value = myStringArray[1];
			document.getElementById('featured_h').value = myStringArray[0];
			
			if(document.getElementById('author_can_moderate_comment'))
				document.getElementById('author_can_moderate_comment').value = myStringArray[8];
			
			var positionof = '<?php echo get_option('currency_pos'); ?>';
			var ftrhome_value = '';
			var ftrhome_currency_symbol = '';
			var ftrcat_value = '';
			var ftrcat_currency_symbol = '';
			if(myStringArray[0] <=0)
			{
				ftrhome_value = '<?php _e('Free',DOMAIN); ?>';
			}
			else
			{
				ftrhome_value = addCurrencyFormate(parseFloat(myStringArray[0]).toFixed(2));
				ftrhome_currency_symbol = '<?php echo tmpl_fetch_currency();?>';
			}
			if(myStringArray[1] <=0)
			{
				ftrcat_value = '<?php _e('Free',DOMAIN); ?>';
				ftrcat_currency_symbol = '';
			}
			else
			{
				ftrcat_value = addCurrencyFormate(parseFloat(myStringArray[1]).toFixed(2));
				ftrcat_currency_symbol = '<?php echo tmpl_fetch_currency();?>';
			}
			
			if(positionof == 1){ 
			
			document.getElementById('ftrhome').innerHTML = "("+ftrhome_currency_symbol+ftrhome_value+")";
			document.getElementById('ftrcat').innerHTML = "("+ftrcat_currency_symbol+ftrcat_value+")";
			if(document.getElementById('ftrcomnt'))
			{
				document.getElementById('ftrcomnt').innerHTML =  "(<?php echo tmpl_fetch_currency();?>"+addCurrencyFormate(parseFloat(myStringArray[8]).toFixed(2))+")";
			}
			}else if(positionof == 2){
			document.getElementById('ftrhome').innerHTML = "("+ftrhome_currency_symbol+' '+ftrhome_value+")";
			document.getElementById('ftrcat').innerHTML = "("+ftrcat_currency_symbol+' '+ftrcat_value+")";
			if(document.getElementById('ftrcomnt'))
			{
				document.getElementById('ftrcomnt').innerHTML = "(<?php echo tmpl_fetch_currency(get_option('currency_symbol'),'currency_symbol');?> "+myStringArray[8]+")";
			}
			}else if(positionof == 3){
			document.getElementById('ftrhome').innerHTML = "("+ftrhome_value+ftrhome_currency_symbol+")";
			document.getElementById('ftrcat').innerHTML = "("+ftrcat_value+ftrcat_currency_symbol+")";
			if(document.getElementById('ftrcomnt'))
			{
				document.getElementById('ftrcomnt').innerHTML = "("+myStringArray[8]+" <?php echo tmpl_fetch_currency();?>)";
			}
			}else{
			document.getElementById('ftrhome').innerHTML = "("+ftrhome_value+' '+ftrhome_currency_symbol +")";
			document.getElementById('ftrcat').innerHTML = "("+ftrcat_value+' '+ftrcat_currency_symbol+")";
			if(document.getElementById('ftrcomnt'))
			{
				document.getElementById('ftrcomnt').innerHTML = "("+myStringArray[8]+" <?php echo tmpl_fetch_currency();?>)";
			}
			}
			if(document.getElementById('pkg_price'))
				document.getElementById('pkg_price').innerHTML = addCurrencyFormate(parseFloat(myStringArray[4]).toFixed(2));
		}else{
			if(document.getElementById('moderate_comment'))
			{
				if(myStringArray[7] == 1 && ( parseFloat(myStringArray[8]) > 0 )){
					document.getElementById('moderate_comment').style.display = "";
					document.getElementById('ftrcomnt').innerHTML =  "("+myStringArray[8]+" <?php echo tmpl_fetch_currency();?>)";
					if(document.getElementById('author_can_moderate_comment'))
						document.getElementById('author_can_moderate_comment').value = myStringArray[8];
				}
				else
				{
					document.getElementById('moderate_comment').style.display = "none";
				}
			}
			if(document.getElementById('price_package_price_list'))
			{
				if(myStringArray[1] > 0 ||  myStringArray[0] > 0 || myStringArray[4] > 0 )
				{
					if(document.getElementById('moderate_comment'))
					{
						if(parseFloat(myStringArray[8]) > 0 || myStringArray[1] > 0 ||  myStringArray[0] > 0 || myStringArray[4] > 0 ){
						
							if(parseFloat(document.getElementById('all_cat_price').value) > 0)
							{
								document.getElementById('cat_price').style.display = "";
								document.getElementById('cat_price_id').style.display = "";
								document.getElementById('before_cat_price_id').style.display = "";
								document.getElementById('pakg_add').style.display = "";
							}
							else
							{
								document.getElementById('cat_price').style.display = "none";
								document.getElementById('cat_price_id').style.display = "none";
								document.getElementById('before_cat_price_id').style.display = "none";
								document.getElementById('pakg_add').style.display = "none";
							}
							
							if(myStringArray[4] > 0)
							{
								document.getElementById('before_pkg_price_id').style.display = "";
								document.getElementById('pkg_price').style.display = "";
								document.getElementById('pkg_price_id').style.display = "";
							}
							else
							{
								document.getElementById('before_pkg_price_id').style.display = "none";
								document.getElementById('pkg_price').style.display = "none";
								document.getElementById('pkg_price_id').style.display = "none";
							}
							if(document.getElementById("all_cat_price").value > 0)
							{
								document.getElementById('cat_price_total_price').style.display = "";
							}
							document.getElementById('price_package_price_list').style.display = "block";
						}
					}
					else
					{
						if(parseFloat(document.getElementById('all_cat_price').value) > 0)
						{
							document.getElementById('cat_price').style.display = "";
							document.getElementById('cat_price_id').style.display = "";
							document.getElementById('before_cat_price_id').style.display = "";
							document.getElementById('pakg_add').style.display = "";
							document.getElementById('cat_price_total_price').style.display = "";
						}
						else
						{
							document.getElementById('cat_price').style.display = "none";
							document.getElementById('cat_price_id').style.display = "none";
							document.getElementById('before_cat_price_id').style.display = "none";
							document.getElementById('pakg_add').style.display = "none";
							document.getElementById('cat_price_total_price').style.display = "none";
						}
						if(myStringArray[4] > 0)
						{
							document.getElementById('before_pkg_price_id').style.display = "";
							document.getElementById('pkg_price').style.display = "";
							document.getElementById('pkg_price_id').style.display = "";
						}
						else
						{
							document.getElementById('before_pkg_price_id').style.display = "none";
							document.getElementById('pkg_price').style.display = "none";
							document.getElementById('pkg_price_id').style.display = "none";
						}
						document.getElementById('price_package_price_list').style.display = 'block';
					}
				}
				else
				{
					if(parseFloat(document.getElementById('all_cat_price').value) > 0)
					{
						document.getElementById('cat_price').style.display = "";
						document.getElementById('cat_price_id').style.display = "";
						document.getElementById('before_cat_price_id').style.display = "";
						document.getElementById('price_package_price_list').style.display = 'block';
					}
					else
					{
						document.getElementById('cat_price').style.display = "none";
						document.getElementById('cat_price_id').style.display = "none";
						document.getElementById('before_cat_price_id').style.display = "none";
						document.getElementById('price_package_price_list').style.display = 'none';
					}
				}
			}
			if(document.getElementById('pkg_price')){
				document.getElementById('pkg_price').innerHTML = addCurrencyFormate(parseFloat(myStringArray[4]).toFixed(2));
			}
			if(document.getElementById('featured_c')){
				document.getElementById('featured_c').value=0;
			}
			if(document.getElementById('ftrcat')){
				document.getElementById('ftrcat').innerHTML	= '<?php _e('Free',DOMAIN); ?>';	
			}	
			
			document.getElementById('featured_h').value=0;
			if(document.getElementById('ftrhome')){
				document.getElementById('ftrhome').innerHTML = '<?php _e('Free',DOMAIN); ?>';
			}
			if(document.getElementById('is_featured')){
				document.getElementById('is_featured').style.display = "none"; 
			}
			
			document.getElementById('total_price').value = parseFloat(myStringArray[0]) + parseFloat(myStringArray[1]) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(myStringArray[4]);
			document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(myStringArray[0]) + parseFloat(myStringArray[1]) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(myStringArray[4])).toFixed(2));
		
		}		
		
		if((document.getElementById('featured_h').checked== true) && (document.getElementById('featured_c').checked== true))
		{			
			if(myStringArray[0]==""){myStringArray[0]=0}else{myStringArray[0]=myStringArray[0];}
			if(myStringArray[1]==""){myStringArray[1]=0}else{myStringArray[1]=myStringArray[1];}
			
			var myStringArray1 = parseFloat(myStringArray[0]);
			var myStringArray2 = parseFloat(myStringArray[1]);
			var myStringArray4 = parseFloat(myStringArray[4]);
			var myStringArrayNum = isNaN(myStringArray1) ? 0.0 : myStringArray1;
			var myStringArrayNum2 = isNaN(myStringArray2) ? 0.0 : myStringArray2;
			var myStringArrayNum4 = isNaN(myStringArray4) ? 0.0 : myStringArray4;
			document.getElementById('feture_price').innerHTML = addCurrencyFormate((parseFloat(myStringArrayNum) + parseFloat(myStringArrayNum2)).toFixed(2));
			
			document.getElementById('total_price').value = (parseFloat(myStringArrayNum) + parseFloat(myStringArrayNum2) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(myStringArrayNum4)).toFixed(2);
			
			document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(myStringArrayNum) + parseFloat(myStringArrayNum2) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(myStringArrayNum4)).toFixed(2));
			
			
		}else if((document.getElementById('featured_h').checked == true) && (document.getElementById('featured_c').checked == false)){
			if(myStringArray[0]==""){myStringArray[0]=0}else{myStringArray[0]=myStringArray[0];}			
			document.getElementById('feture_price').innerHTML = addCurrencyFormate(parseFloat(myStringArray[0]).toFixed(2));
			
			document.getElementById('total_price').value = (parseFloat(document.getElementById('feture_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(myStringArray[4])).toFixed(2);
			document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('feture_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(myStringArray[4])).toFixed(2));
			
		}else if((document.getElementById('featured_h').checked == false) && (document.getElementById('featured_c').checked == true)){
			if(myStringArray[1]==""){myStringArray[1]=0}else{myStringArray[1]=myStringArray[1];}
			document.getElementById('feture_price').innerHTML = addCurrencyFormate(parseFloat(myStringArray[1]).toFixed(2));
			document.getElementById('total_price').value = (parseFloat(document.getElementById('feture_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(myStringArray[4])).toFixed(2);
			document.getElementById('result_price').innerHTML =  addCurrencyFormate((parseFloat(document.getElementById('feture_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(myStringArray[4])).toFixed(2));
		}else if((document.getElementById('author_can_moderate_comment')) && (document.getElementById('author_can_moderate_comment').checked == false) && (document.getElementById('author_can_moderate_comment').checked == true)){
			if(myStringArray[8]==""){myStringArray[8]=0}else{myStringArray[8]=myStringArray[8];}
			var ftrcomnt = 0;
			if(document.getElementById('ftrcomnt'))
			{
				document.getElementById('ftrcomnt').innerHTML = parseFloat(myStringArray[8]).toFixed(2);
				ftrcomnt = parseFloat(myStringArray[8]);
			}
			document.getElementById('total_price').value = (parseFloat(document.getElementById('feture_price').innerHTML.replace(',','')) +  parseFloat(ftrcomnt) + parseFloat(myStringArray[8])).toFixed(2);
			
			document.getElementById('result_price').innerHTML =  addCurrencyFormate(parseFloat(document.getElementById('feture_price').innerHTML.replace(',','')).toFixed(2) +  parseFloat(ftrcomnt).toFixed(2) + parseFloat(myStringArray[8]).toFixed(2));
		}
		else{			
			document.getElementById('total_price').value = (parseFloat(document.getElementById('feture_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(myStringArray[4])).toFixed(2);
			
			document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('feture_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(myStringArray[4])).toFixed(2));
		}
		if(document.getElementById('submit_coupon_code') && document.getElementById('total_price').value > 0)
		{
			document.getElementById('submit_coupon_code').style.display='';
		}
		else
		{
			if(document.getElementById('submit_coupon_code'))
			{
				document.getElementById('submit_coupon_code').style.display='none';
			}
		}
		if(document.getElementById('category_can_select') && document.getElementById('moderate_comment')){
				document.getElementById('category_can_select').value = myStringArray[9];
		}
		if(document.getElementById('category_can_select') ){
				document.getElementById('category_can_select').value = myStringArray[7];
		}
	  } 
	  }
	  url="<?php echo TEMPL_PLUGIN_URL;?>/tmplconnector/monetize/templatic-monetization/ajax_price.php?pkid="+pkid
	  xmlhttp.open("GET",url,true);
	  xmlhttp.send();
	 
}
/*
Name :fetch_packages
Description : Rerun package details and category pricing( term_price for category ) USE THIE IF IN FUTURE WE are going to give categorywise pricing
*/
function fetch_packages(pkgid,form,pri)
{ 
	var total = 0;
	var t=0;
	if(pkgid != '')
	{
		document.getElementById("category_error").style.display = 'none';
	}
	<?php $tmpdata = get_option('templatic_settings'); ?>
     var cat_display = '<?php echo $tmpdata['templatic-category_type']; ?>';
	var cat_wise_display = '<?php echo $tmpdata['templatic-category_custom_fields']; ?>';
	var is_post_upgrade = '<?php echo @$_REQUEST['upgpkg']; ?>';
	var is_fronted_edit=(jQuery('.frontend_edit_submit_cat').val())? jQuery('.frontend_edit_submit_cat').val() : '';	
	var dml = document.forms['submit_form'];
	var c = document.getElementsByName('category[]');
	
	var cats = document.getElementById('all_cat').value;
	if(document.getElementById('all_cat')){
		document.getElementById('all_cat').value = "";
	}
	if(document.getElementById('all_cat_price')){
		document.getElementById('all_cat_price').value = 0;
	}		
	if(document.getElementById('cat_price')){
		document.getElementById('cat_price').innerHTML = 0;
	}
	
	if(document.getElementById('all_cat_price').value <= 0)
	{
		if(document.getElementById('before_cat_price_id')){
			document.getElementById('before_cat_price_id').style.display = "none";
		
		document.getElementById('cat_price').style.display = "none";
		
		document.getElementById('cat_price_id').style.display = "none";
		
		document.getElementById('pakg_add').style.display = "none";
		
		document.getElementById('cat_price_total_price').style.display = "none";
		}
	}
	
	if(cat_display =='checkbox' || cat_display==''){
		for(var i=0;i<c.length;i++){
			c[i].checked?t++:null;
			if(c[i].checked)
			{	
				var a = c[i].value.split(",");
				
				document.getElementById('all_cat').value += a[0]+"|";
				
				
				document.getElementById('all_cat_price').value = parseFloat(document.getElementById('all_cat_price').value) + parseFloat(a[1]);
				
				document.getElementById('cat_price').innerHTML = addCurrencyFormate(parseFloat(document.getElementById('all_cat_price').value).toFixed(2));
			}
			if(document.getElementById('all_cat_price').value > 0)
			{
				if(document.getElementById('before_cat_price_id')){
				document.getElementById('before_cat_price_id').style.display = "";
				
				
				
				document.getElementById('pakg_add').style.display = "";
				
				document.getElementById('cat_price_total_price').style.display = "";
				document.getElementById('cat_price_id').style.display = "";
				}
				document.getElementById('cat_price').style.display = "";				
				
			}
			else
			{
				if( document.getElementById('before_cat_price_id') ){
					document.getElementById('before_cat_price_id').style.display = "none";
				}
				if( document.getElementById('cat_price') ){
					document.getElementById('cat_price').style.display = "none";
				}
				if( document.getElementById('cat_price_id') ){				
					document.getElementById('cat_price_id').style.display = "none";
				}
			}
				if(document.getElementById('pkg_price'))
				{
					document.getElementById('total_price').value =  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('feture_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',',''));
					
					document.getElementById('result_price').innerHTML =  addCurrencyFormate((parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('feture_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',',''))).toFixed(2));
				}else
				{
					document.getElementById('total_price').value =  parseFloat(document.getElementById('all_cat_price').value) ;
					
					document.getElementById('result_price').innerHTML =  addCurrencyFormate(parseFloat(document.getElementById('all_cat_price').value).toFixed(2)) ;
				}
		}
		if(document.getElementById('price_package_price_list')){
			if(document.getElementById('all_cat_price').value > 0)
			{
				document.getElementById('price_package_price_list').style.display = 'block';
			}
			else
			{
				document.getElementById('price_package_price_list').style.display = 'none';
			}
		}
	}else{		
		if(cat_display == 'select'){
			var s = document.getElementById('select_category'); /* var is use for select box */
			if(s.options[s.selectedIndex].value){
					var a = s.options[s.selectedIndex].value.split(",");
					document.getElementById('all_cat').value += a[0]+"|";
					document.getElementById('all_cat_price').value = parseFloat(document.getElementById('all_cat_price').value) + parseFloat(a[1]);
					document.getElementById('cat_price').innerHTML = addCurrencyFormate(parseFloat(document.getElementById('all_cat_price').value).toFixed(2));
			}
			
			if(document.getElementById('all_cat_price').value > 0)
			{
				if(document.getElementById('before_cat_price_id')){
					document.getElementById('before_cat_price_id').style.display = "";
					document.getElementById('pakg_add').style.display = "";
					document.getElementById('cat_price_total_price').style.display = "";
					document.getElementById('cat_price_id').style.display = "";
				}
				document.getElementById('cat_price').style.display = "";
			}
			else
			{
				if( document.getElementById('before_cat_price_id') ){
					document.getElementById('before_cat_price_id').style.display = "none";
				}
				if( document.getElementById('cat_price') ){
					document.getElementById('cat_price').style.display = "none";
				}
				if( document.getElementById('cat_price_id') ){				
					document.getElementById('cat_price_id').style.display = "none";
				}
			}
			document.getElementById('total_price').value = (parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('feture_price').innerHTML.replace(',','')) + parseFloat(document.getElementById('pkg_price').innerHTML.replace(',',''))).toFixed(2);			
			document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('feture_price').innerHTML.replace(',','')) + parseFloat(document.getElementById('pkg_price').innerHTML.replace(',',''))).toFixed(2));
			
		}else if(cat_display == 'multiselectbox'){
			var s = document.getElementById('select_category'); /* var is use for select box */
			
			for(var i=0;i < s.options.length;i++){
				s.options[i].selected?t++:null;
				
				if(s.options[ i ].selected){
						var a = s.options[ i ].value.split(",");
						document.getElementById('all_cat').value += a[0]+"|";
						document.getElementById('all_cat_price').value = parseFloat(document.getElementById('all_cat_price').value) + parseFloat(a[1]);
						document.getElementById('cat_price').innerHTML = addCurrencyFormate(parseFloat(document.getElementById('all_cat_price').value).toFixed(2));
				}
				if(document.getElementById('all_cat_price').value > 0)
				{
					if(document.getElementById('before_cat_price_id')){
						document.getElementById('before_cat_price_id').style.display = "";
						document.getElementById('pakg_add').style.display = "";
						document.getElementById('cat_price_total_price').style.display = "";
						document.getElementById('cat_price_id').style.display = "";
					}
					document.getElementById('cat_price').style.display = "";
				}
				else
				{
					if( document.getElementById('before_cat_price_id') ){
						document.getElementById('before_cat_price_id').style.display = "none";
					}
					if( document.getElementById('cat_price') ){
						document.getElementById('cat_price').style.display = "none";
					}
					if( document.getElementById('cat_price_id') ){				
						document.getElementById('cat_price_id').style.display = "none";
					}
				}
				
				document.getElementById('total_price').value = (parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('feture_price').innerHTML.replace(',','')) + parseFloat(document.getElementById('pkg_price').innerHTML.replace(',',''))).toFixed(2);			
				document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('feture_price').innerHTML.replace(',','')) + parseFloat(document.getElementById('pkg_price').innerHTML.replace(',',''))).toFixed(2));
			}
		}
	}
	if(document.getElementById('total_price').value=='' || document.getElementById('total_price').value==0 ){
		document.getElementById('price_package_price_list').style.display = "none";
	}
	var cats = document.getElementById('all_cat').value;
	var post_type = document.getElementById('cur_post_type').value ;
	var taxonomy = document.getElementById('cur_post_taxonomy').value ;
	/* Below code is for category wise packages */
	if(cat_wise_display == 'No' || is_post_upgrade== 1 || is_fronted_edit==1)
	 {
		document.getElementById("packages_checkbox").innerHTML="";
		if(document.getElementById("process2"))
		{
	    	document.getElementById("process2").style.visibility ="";
		}
	 
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
			else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
			xmlhttp.onreadystatechange=function()
		  {
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("packages_checkbox").innerHTML =xmlhttp.responseText;
			if(document.getElementById("process2"))
			{
				document.getElementById("process2").style.visibility ="hidden";
			}
			}
		  }
		  <?php
		$language='';
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$language="&language=".$current_lang_code;
		}
		if(isset($_REQUEST['upgpkg']) && $_REQUEST['upgpkg']==1 && isset($_REQUEST['pid']) && $_REQUEST['pid']!=""){
			$language.='&upgpkg=1&pid='.$_REQUEST['pid'];	
		}
		
		?>		
		  url="<?php echo TEMPL_PLUGIN_URL;?>/tmplconnector/monetize/templatic-monetization/ajax_price.php?pckid="+cats+"&post_type="+post_type+"&taxonomy="+taxonomy+"<?php echo $language;?>";
		  xmlhttp.open("GET",url,true);
		  xmlhttp.send();
	 }
}
/*
Name :templ_all_categories
Description : function return the result when all categories selected
*/
function templ_all_categories(cp_price) {
	var total = 0;
	var t=0;
	//var c= form['category[]'];
	var dml = document.forms['submit_form'];
	var c = dml.elements['category[]'];
	var selectall = dml.elements['selectall'];
	if(selectall.checked == false){
		cp_price = 0;
	} else {
		cp_price = cp_price;
	}
	if(document.getElementById('price_package_price_list')){
		if(cp_price > 0)
		{
			document.getElementById('price_package_price_list').style.display = 'block';
		}
		else
		{
			document.getElementById('price_package_price_list').style.display = 'none';
		}
	}
	var post_type = document.getElementById('cur_post_type').value ;
	var taxonomy = document.getElementById('cur_post_taxonomy').value ;
	var cats = document.getElementById('all_cat').value;
	document.getElementById('all_cat').value = "";
	document.getElementById('all_cat_price').value = 0;
	document.getElementById('feture_price').innerHTML = 0;
	document.getElementById('cat_price').innerHTML = 0;
	
		for(var i=0 ;i < c.length;i++){
		c[i].checked?t++:null;
		if(c[i].checked){	
			var a = c[i].value.split(",");
			if(i ==  (c.length - 1) ){
				document.getElementById('all_cat').value += a[0];
			} else {
				document.getElementById('all_cat').value += a[0]+"|";
			}
		}
	}
	document.getElementById('all_cat_price').value = parseFloat(cp_price);
	document.getElementById('cat_price').innerHTML = parseFloat(document.getElementById('all_cat_price').value);
	document.getElementById('total_price').value =  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('feture_price').innerHTML) +  parseFloat(document.getElementById('pkg_price').innerHTML);
	document.getElementById('result_price').innerHTML =  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('feture_price').innerHTML) +  parseFloat(document.getElementById('pkg_price').innerHTML);
	
	var cats = document.getElementById('all_cat').value ;
	
	  document.getElementById("packages_checkbox").innerHTML="";
	  if(document.getElementById("process2")){
		document.getElementById("process2").style.visibility ="";
		}
	  if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
		else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
		xmlhttp.onreadystatechange=function()
	  {
	    if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		document.getElementById("packages_checkbox").innerHTML =xmlhttp.responseText;
			if(document.getElementById("process2"))
			{
			document.getElementById("process2").style.visibility ="hidden";
			}
		}
	  }	  
	   url="<?php echo TEMPL_PLUGIN_URL;?>/tmplconnector/monetize/templatic-monetization/ajax_price.php?pckid="+cats+"&post_type="+post_type+"&taxonomy="+taxonomy
	  xmlhttp.open("GET",url,true);
	  xmlhttp.send();	
	}
	
	function myfields(fid)
{
	document.getElementById(fid+'_hidden').value = document.getElementById(fid).value;
}
/*
Name :featured_list
Description : function return the result after user select feature listing type(check box)
*/
function featured_list(fid)
{

	<?php 
	if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
	?>
	if((document.getElementById('featured_h').checked== true) && (document.getElementById('featured_c').checked== true) && (document.getElementById('author_can_moderate_comment').checked == true))
	{
		document.getElementById('featured_type').value = 'both';
		document.getElementById('author_moderate').value = '1';
		var totprice = parseFloat(document.getElementById('featured_c').value) + parseFloat(document.getElementById('featured_h').value) + parseFloat(document.getElementById('author_can_moderate_comment').value);
		var resprice = parseFloat(document.getElementById('featured_c').value) + parseFloat(document.getElementById('featured_h').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('author_can_moderate_comment').value);
		document.getElementById('feture_price').innerHTML = addCurrencyFormate(totprice.toFixed(2));
		document.getElementById('result_price').innerHTML = addCurrencyFormate(resprice.toFixed(2));
		document.getElementById('total_price').value = (parseFloat(document.getElementById('featured_c').value) + parseFloat(document.getElementById('featured_h').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('author_can_moderate_comment').value)).toFixed(2);
	
	}else if((document.getElementById('featured_h').checked == true) && (document.getElementById('featured_c').checked == false) && (document.getElementById('author_can_moderate_comment').checked == true)){
		document.getElementById('featured_type').value = 'h';
		document.getElementById('author_moderate').value = '1';
		document.getElementById('feture_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('featured_h').value) + parseFloat(document.getElementById('author_can_moderate_comment').value)).toFixed(2));
		
		document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('featured_h').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('author_can_moderate_comment').value)).toFixed(2));
		
		document.getElementById('total_price').value = (parseFloat(document.getElementById('featured_h').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('author_can_moderate_comment').value)).toFixed(2);
	}else if((document.getElementById('featured_h').checked == false) && (document.getElementById('featured_c').checked == true) && (document.getElementById('author_can_moderate_comment').checked == true)){
		document.getElementById('author_moderate').value = '1';
		document.getElementById('featured_type').value = 'c';
		document.getElementById('feture_price').innerHTML = addCurrencyFormate((parseFloat(parseFloat(document.getElementById('featured_c').value) + parseFloat(document.getElementById('author_can_moderate_comment').value))).toFixed(2));
		
		document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('featured_c').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('author_can_moderate_comment').value)).toFixed(2));
		
		document.getElementById('total_price').value = (parseFloat(document.getElementById('featured_c').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('author_can_moderate_comment').value)).toFixed(2);
		
	}else if((document.getElementById('featured_h').checked == false) && (document.getElementById('featured_c').checked == false) && (document.getElementById('author_can_moderate_comment').checked == true)){
		document.getElementById('author_moderate').value = '1';
		document.getElementById('featured_type').value = '';
		document.getElementById('feture_price').innerHTML = addCurrencyFormate(( parseFloat(document.getElementById('author_can_moderate_comment').value)).toFixed(2));
		
		document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('featured_c').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('author_can_moderate_comment').value)).toFixed(2));
		
		document.getElementById('total_price').value = (parseFloat(document.getElementById('featured_c').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('author_can_moderate_comment').value)).toFixed(2);		
	}
	
	if((document.getElementById('featured_h').checked== true) && (document.getElementById('featured_c').checked== true) && (document.getElementById('author_can_moderate_comment').checked == false))
	{
		document.getElementById('author_moderate').value = '0';
		document.getElementById('featured_type').value = 'both';
		var totprice = parseFloat(document.getElementById('featured_c').value) + parseFloat(document.getElementById('featured_h').value) ;
		var resprice = parseFloat(document.getElementById('featured_c').value) + parseFloat(document.getElementById('featured_h').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value);
		document.getElementById('feture_price').innerHTML = addCurrencyFormate(totprice.toFixed(2));
		document.getElementById('result_price').innerHTML = addCurrencyFormate(resprice.toFixed(2));
		
		document.getElementById('total_price').value = (parseFloat(document.getElementById('featured_c').value) + parseFloat(document.getElementById('featured_h').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')).innerHTML) +  parseFloat(document.getElementById('all_cat_price').value).toFixed(2);
	
	}else if((document.getElementById('featured_h').checked == true) && (document.getElementById('featured_c').checked == false) && (document.getElementById('author_can_moderate_comment').checked == false)){
		document.getElementById('author_moderate').value = '0';
		document.getElementById('featured_type').value = 'h';
		document.getElementById('feture_price').innerHTML = addCurrencyFormate(parseFloat(document.getElementById('featured_h').value).toFixed(2));
		
		document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('featured_h').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value)).toFixed(2));
		
		document.getElementById('total_price').value = (parseFloat(document.getElementById('featured_h').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value)).toFixed(2);
	}else if((document.getElementById('featured_h').checked == false) && (document.getElementById('featured_c').checked == true) && (document.getElementById('author_can_moderate_comment').checked == false)){
		document.getElementById('featured_type').value = 'c';
		document.getElementById('author_moderate').value = '0';
		document.getElementById('feture_price').innerHTML = addCurrencyFormate(parseFloat(document.getElementById('featured_c').value).toFixed(2));
		
		document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('featured_c').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value)).toFixed(2));
		
		document.getElementById('total_price').value = (parseFloat(document.getElementById('featured_c').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value)).toFixed(2);
		
	}
	else if((document.getElementById('featured_h').checked == false) && (document.getElementById('featured_c').checked == false) && (document.getElementById('author_can_moderate_comment').checked == true)){
		document.getElementById('author_moderate').value = '1';
		document.getElementById('feture_price').innerHTML =  addCurrencyFormate(parseFloat(document.getElementById('author_can_moderate_comment').value).toFixed(2));
		
		document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('author_can_moderate_comment').value)).toFixed(2));
		
		document.getElementById('total_price').value = parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value) + parseFloat(document.getElementById('author_can_moderate_comment').value);
	}
	else if((document.getElementById('featured_h').checked == false) && (document.getElementById('featured_c').checked == false) && (document.getElementById('author_can_moderate_comment').checked == false)){
		document.getElementById('author_moderate').value = '0';
		document.getElementById('featured_type').value = 'n';
		document.getElementById('feture_price').innerHTML = '0';
		document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value)).toFixed(2));
		
		document.getElementById('total_price').value = (parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value)).toFixed(2);
	}
	<?php
	}
	else
	{
	?>
		if((document.getElementById('featured_h').checked== true) && (document.getElementById('featured_c').checked== true))
	{
		document.getElementById('featured_type').value = 'both';
		var totprice = parseFloat(document.getElementById('featured_c').value) + parseFloat(document.getElementById('featured_h').value);
		var resprice = parseFloat(document.getElementById('featured_c').value) + parseFloat(document.getElementById('featured_h').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value);
		
		document.getElementById('feture_price').innerHTML = addCurrencyFormate(totprice.toFixed(2));
		document.getElementById('result_price').innerHTML = addCurrencyFormate(resprice.toFixed(2));
		
		document.getElementById('total_price').value = (parseFloat(document.getElementById('featured_c').value) + parseFloat(document.getElementById('featured_h').value) +   parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value)).toFixed(2);
	
	}else if((document.getElementById('featured_h').checked == true) && (document.getElementById('featured_c').checked == false)){
		document.getElementById('featured_type').value = 'h';
		document.getElementById('feture_price').innerHTML = parseFloat(document.getElementById('featured_h').value.replace(',','')).toFixed(2);		
		
		document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('featured_h').value.replace(',','')) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value.replace(',',''))).toFixed(2));		
		
		document.getElementById('total_price').value = (parseFloat(document.getElementById('featured_h').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value)).toFixed(2);
	}else if((document.getElementById('featured_h').checked == false) && (document.getElementById('featured_c').checked == true)){
		document.getElementById('featured_type').value = 'c';
		document.getElementById('feture_price').innerHTML = parseFloat(document.getElementById('featured_c').value).toFixed(2);
		
		document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('featured_c').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value)).toFixed(2));	
		
		document.getElementById('total_price').value = (parseFloat(document.getElementById('featured_c').value) +  parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value)).toFixed(2);
		
	}else if((document.getElementById('featured_h').checked == false) && (document.getElementById('featured_c').checked == false)){
		document.getElementById('featured_type').value = 'n';
		document.getElementById('feture_price').innerHTML = '0';
		document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value)).toFixed(2));
		document.getElementById('total_price').value = (parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value)).toFixed(2);
	
	}else{
		document.getElementById('featured_type').value = 'n';
		document.getElementById('feture_price').innerHTML = '0';
		document.getElementById('result_price').innerHTML = addCurrencyFormate((parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value)).toFixed(2));
		document.getElementById('total_price').value = (parseFloat(document.getElementById('pkg_price').innerHTML.replace(',','')) +  parseFloat(document.getElementById('all_cat_price').value)).toFixed(2);
	}
	<?php
	}
	?>
	if(document.getElementById('featured_type').value == "n")
	{
		document.getElementById('feture_price_id').style.display = "none";
		document.getElementById('feture_price').style.display = "none";
		document.getElementById('before_feture_price_id').style.display = "none";
		if(document.getElementById("all_cat_price").value > 0 || document.getElementById('feture_price').innerHTML != 0 )
		{
			document.getElementById('cat_price_total_price').style.display = "";
		}else
		{
			document.getElementById('cat_price_total_price').style.display = "none";
		}
		document.getElementById('pakg_price_add').style.display = "none";
	}
	else if(document.getElementById('featured_type').value == "h" &&  parseFloat(document.getElementById('featured_h').value) > 0)
	{
		document.getElementById('feture_price_id').style.display = "";
		document.getElementById('feture_price').style.display = "";
		document.getElementById('before_feture_price_id').style.display = "";
		if(document.getElementById("all_cat_price").value > 0 || document.getElementById('pkg_price').style.display == '')
		{
			document.getElementById('cat_price_total_price').style.display = "";
			document.getElementById('pakg_price_add').style.display = "";
		}else
		{
			document.getElementById('cat_price_total_price').style.display = "none";
			document.getElementById('pakg_price_add').style.display = "none";
		}
	}
	else if(document.getElementById('featured_type').value == "h" &&  parseFloat(document.getElementById('featured_h').value) <= 0)
	{
		if(document.getElementById('author_can_moderate_comment'))
		{
			if( parseFloat(document.getElementById('author_can_moderate_comment').value) > 0 && parseFloat(document.getElementById('author_moderate').value) == 1 )
			{
				document.getElementById('feture_price_id').style.display = "";
				document.getElementById('feture_price').style.display = "";
				document.getElementById('before_feture_price_id').style.display = "";
				if(document.getElementById('pkg_price').innerHTML != 0)
				{
					document.getElementById('cat_price_total_price').style.display = "";
					document.getElementById('pakg_price_add').style.display = "";
				}
			}
			else
			{
				document.getElementById('feture_price_id').style.display = "none";
				document.getElementById('feture_price').style.display = "none";
				document.getElementById('before_feture_price_id').style.display = "none";
				if(document.getElementById("all_cat_price").value > 0 || document.getElementById('feture_price').innerHTML != 0)
				{
					document.getElementById('cat_price_total_price').style.display = "";
				}else
				{
					document.getElementById('cat_price_total_price').style.display = "none";
				}
				document.getElementById('pakg_price_add').style.display = "none";
			}
		}else
		{
			document.getElementById('feture_price_id').style.display = "none";
			document.getElementById('feture_price').style.display = "none";
			document.getElementById('before_feture_price_id').style.display = "none";
			if(document.getElementById("all_cat_price").value > 0 || document.getElementById('feture_price').innerHTML != 0)
			{
				document.getElementById('cat_price_total_price').style.display = "";
			}else
			{
				document.getElementById('cat_price_total_price').style.display = "none";
			}
			document.getElementById('pakg_price_add').style.display = "none";
		}
	}
	else if(document.getElementById('featured_type').value == "c" &&  parseFloat(document.getElementById('featured_c').value) > 0)
	{
		document.getElementById('feture_price_id').style.display = "";
		document.getElementById('feture_price').style.display = "";
		document.getElementById('before_feture_price_id').style.display = "";
		if((document.getElementById("all_cat_price").value > 0 || document.getElementById('feture_price').innerHTML != 0) && document.getElementById('pkg_price').innerHTML != 0)
		{
			document.getElementById('cat_price_total_price').style.display = "";
			document.getElementById('pakg_price_add').style.display = "";
		}else
		{
			document.getElementById('cat_price_total_price').style.display = "none";
			document.getElementById('pakg_price_add').style.display = "none";
		}
	}
	else if(document.getElementById('featured_type').value == "c" )
	{
		if(document.getElementById('author_can_moderate_comment'))
		{
			if( parseFloat(document.getElementById('author_can_moderate_comment').value) > 0 && parseFloat(document.getElementById('author_moderate').value) == 1 )
			{
				document.getElementById('feture_price_id').style.display = "";
				document.getElementById('feture_price').style.display = "";
				document.getElementById('before_feture_price_id').style.display = "";
				if(document.getElementById('feture_price').innerHTML != 0)
				{
					document.getElementById('cat_price_total_price').style.display = "none";
					document.getElementById('pakg_price_add').style.display = "none";
				}else
				{
						document.getElementById('cat_price_total_price').style.display = "";
					document.getElementById('pakg_price_add').style.display = "";
				}
			}
			else
			{
				document.getElementById('feture_price_id').style.display = "none";
				document.getElementById('feture_price').style.display = "none";
				document.getElementById('before_feture_price_id').style.display = "none";
				if(document.getElementById("all_cat_price").value > 0 || document.getElementById('pkg_price').innerHTML != 0)
				{
					document.getElementById('cat_price_total_price').style.display = "";
				}else
				{
					document.getElementById('cat_price_total_price').style.display = "none";
				}
				document.getElementById('pakg_price_add').style.display = "none";
			}
		}
		else{
			document.getElementById('feture_price_id').style.display = "none";
			document.getElementById('feture_price').style.display = "none";
			document.getElementById('before_feture_price_id').style.display = "none";
			if(document.getElementById("all_cat_price").value > 0 || document.getElementById('feture_price').innerHTML != 0)
			{
				document.getElementById('cat_price_total_price').style.display = "";
			}else
			{
				document.getElementById('cat_price_total_price').style.display = "none";
			}
			document.getElementById('pakg_price_add').style.display = "none";
		}
	}
	else if(document.getElementById('featured_type').value == "both" && ( parseFloat(document.getElementById('featured_h').value) > 0 ||  parseFloat(document.getElementById('featured_c').value) > 0))
	{
		document.getElementById('feture_price_id').style.display = "";
		document.getElementById('feture_price').style.display = "";
		document.getElementById('before_feture_price_id').style.display = "";
		if(document.getElementById("all_cat_price").value > 0 || document.getElementById('pkg_price').style.display == '')
		{
			document.getElementById('cat_price_total_price').style.display = "";
			document.getElementById('pakg_price_add').style.display = "";
		}else
		{
			document.getElementById('cat_price_total_price').style.display = "none";
			document.getElementById('pakg_price_add').style.display = "none";
		}
	}
	else if(document.getElementById('author_can_moderate_comment').value)
	{
		if( parseFloat(document.getElementById('author_can_moderate_comment').value) > 0 && parseFloat(document.getElementById('author_moderate').value) == 1 )
		{
			document.getElementById('feture_price_id').style.display = "";
			document.getElementById('feture_price').style.display = "";
			document.getElementById('before_feture_price_id').style.display = "";
			if(document.getElementById('feture_price').innerHTML != 0 || document.getElementById("all_cat_price").value > 0)
			{
				document.getElementById('cat_price_total_price').style.display = "";
				document.getElementById('pakg_price_add').style.display = "";
			}
		}
		if(document.getElementById('featured_type').value == "n" && parseFloat(document.getElementById('author_moderate').value) != 1 )
		{
			document.getElementById('feture_price_id').style.display = "none";
			document.getElementById('feture_price').style.display = "none";
			document.getElementById('before_feture_price_id').style.display = "none";
			if(document.getElementById("all_cat_price").value > 0 )
			{
				document.getElementById('cat_price_total_price').style.display = "";
			}else
			{
				document.getElementById('cat_price_total_price').style.display = "none";
			}
			document.getElementById('pakg_price_add').style.display = "none";
		}
	}
	else if(document.getElementById('featured_type').value == "n")
	{
		document.getElementById('feture_price_id').style.display = "none";
		document.getElementById('feture_price').style.display = "none";
		document.getElementById('before_feture_price_id').style.display = "none";
		if(document.getElementById("all_cat_price").value > 0 || document.getElementById('pkg_price').innerHTML != 0 )
		{
			document.getElementById('cat_price_total_price').style.display = "";
		}else
		{
			document.getElementById('cat_price_total_price').style.display = "none";
		}
		document.getElementById('pakg_price_add').style.display = "none";
	}
	else
	{
		document.getElementById('feture_price_id').style.display = "none";
		document.getElementById('feture_price').style.display = "none";
		document.getElementById('before_feture_price_id').style.display = "none";
		if(document.getElementById("all_cat_price").value > 0)
		{
			document.getElementById('cat_price_total_price').style.display = "";
		}else
		{
			document.getElementById('cat_price_total_price').style.display = "none";
		}
		document.getElementById('pakg_price_add').style.display = "none";
	}
	if(document.getElementById('submit_coupon_code') && document.getElementById('total_price').value > 0)
	{
		document.getElementById('submit_coupon_code').style.display='';
	}
	else
	{
		if(document.getElementById('submit_coupon_code'))
		{
			document.getElementById('submit_coupon_code').style.display='none';
		}
	}
}

function addCurrencyFormate(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}
</script>
