/*
 * claculate price for price package ,category and fetuared option
 */
var total_price = 0;
var final_cat_price = 0;
jQuery(document).ready(function(){
	/*check whether home or category page featured option exists or not*/
	if(jQuery('#featured_h').length > 0 || jQuery('#featured_c').length > 0 ) 
	{
		/*while on change of selection of home page featured option*/
		jQuery('#featured_h').live('change',function() {
			/*check whether category page featured option exists or not.If not set it to zero by default*/
			if(jQuery('#featured_c').length > 0 )
			{
				if(jQuery('#featured_c').prop('checked') == true)
				{
					var featured_c = (jQuery('#featured_c').val()).split(thousands_sep).join('');
				}
				else
				{
					var featured_c = 0;
				}
			}
			else
			{
				var featured_c = 0;
			}
			/*if home page featured option is checked or not*/			
			if(this.checked) {
				/*calculate the price of featured option for home page and category page*/
				total_price = parseFloat((jQuery('#featured_h').val().split(thousands_sep).join(''))) + parseFloat(featured_c);
				
				/*if package price greater than zero than show the addition symbol between price package amount and featured option price*/	
				if(total_price >0)
				{
					if(position == 3 || position == 4)
					{
						jQuery('#feture_price_id').css('display','block');
					}
					else
					{
						jQuery('#before_feture_price_id').css('display','block');
					}
					if(pkg_price > 0)
						jQuery('#pakg_price_add').css('display','block');
				}
				if(edit == 1 && total_price >0 )
				{
					if(position == 3 || position == 4)
					{
						jQuery('#feture_price_id').css('display','block');
					}
					else
					{
						jQuery('#before_feture_price_id').css('display','block');
					}					
					if(final_cat_price > 0){
						jQuery('#pakg_price_add').css('display','block');
					}
					if( final_cat_price > 0 && pkg_price<=0){
						jQuery('#cat_price_add').css('display','block');
					}
				}
				jQuery('#feture_price').css('display','block');
				jQuery('#feture_price').html((thousandseperator(total_price.toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
				/*jquery to show final result*/
				if(total_price > 0 && (final_cat_price > 0 || pkg_price > 0 ))
				{
					if((final_cat_price > 0 && pkg_price <= 0 )){					
						jQuery('#cat_price_add').css('display','block');	
					}
					jQuery('#cat_price_total_price').css('display','block');
					jQuery('#result_price_equ').css('display','block');
					jQuery('#currency_before_result_price').css('display','block');
					jQuery('#result_price').css('display','block');
					jQuery('#result_price').html((thousandseperator((parseFloat(total_price)+pkg_price+parseFloat(final_cat_price)).toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
				}
				else
				{					
					if(final_cat_price <=0)
					{
						jQuery('#cat_price_total_price').css('display','none');
						jQuery('#result_price_equ').css('display','none');
						jQuery('#currency_before_result_price').css('display','none');
						jQuery('#result_price').css('display','none');
						jQuery('#result_price').html();
					}
					if(final_cat_price > 0 &&  parseFloat(jQuery('#featured_h').val()) > 0)
					{
						jQuery('#cat_price_add').css('display','block');
					}
				}
			}
			else
			{
				/*subtract the home page featured price from total featured price when unselecte the option*/
				total_price = parseFloat(total_price) - parseFloat((jQuery('#featured_h').val()).split(thousands_sep).join(''))
				if(jQuery('#featured_c').prop('checked') == true && total_price > 0)
				{
					if(position == 3 || position == 4)
					{
						jQuery('#feture_price_id').css('display','block');
					}
					else
					{
						jQuery('#before_feture_price_id').css('display','block');
					}
					/*if(final_cat_price > 0 || pkg_price > 0 )*/
					if(pkg_price > 0 )
						jQuery('#pakg_price_add').css('display','block');
					jQuery('#feture_price').css('display','block');
					jQuery('#feture_price').html((thousandseperator(total_price.toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
				}
				else if(total_price <= 0)
				{
					if(position == 3 || position == 4)
					{
						jQuery('#feture_price_id').css('display','none');
					}
					else
					{
						jQuery('#before_feture_price_id').css('display','none');
					}
					jQuery('#pakg_price_add').css('display','none');
					jQuery('#feture_price').css('display','none');
					jQuery('#cat_price_add').css('display','none');	
					jQuery('#feture_price').html();
					if(parseFloat(final_cat_price) > 0 && pkg_price > 0)
						jQuery('#result_price').html((thousandseperator((pkg_price+parseFloat(final_cat_price)).toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
					else
					{
						jQuery('#cat_price_total_price').css('display','none');
						jQuery('#result_price_equ').css('display','none');
						jQuery('#currency_before_result_price').css('display','none');
						jQuery('#result_price').css('display','none');
						jQuery('#result_price').html();
					}
				}
				/*jquery to show final result*/
				if(total_price > 0 && (final_cat_price > 0 || pkg_price > 0 ))
				{
					if((final_cat_price > 0 && pkg_price <= 0 )){					
						jQuery('#cat_price_add').css('display','block');	
					}
					jQuery('#cat_price_total_price').css('display','block');
					jQuery('#result_price_equ').css('display','block');
					jQuery('#currency_before_result_price').css('display','block');
					jQuery('#result_price').css('display','block');
					if(!jQuery('#featured_c').prop('checked') == true)
					{
						jQuery('#pakg_price_add').css('display','none');
						jQuery('#feture_price').css('display','none');
						jQuery('#before_feture_price_id').css('display','none');
					}
					jQuery('#result_price').html((thousandseperator((parseFloat(total_price)+parseFloat(pkg_price)+parseFloat(final_cat_price)).toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
				}
				else if(final_cat_price <= 0 && total_price > 0 )
				{
					jQuery('#cat_price_add').css('display','none');
					jQuery('#cat_price_total_price').css('display','none');
					jQuery('#result_price_equ').css('display','none');
					jQuery('#currency_before_result_price').css('display','none');
					jQuery('#result_price').css('display','none');
					jQuery('#cat_price_add').css('display','none');					
					jQuery('#result_price').html();
				}
				else
				{
					jQuery('#feture_price').css('display','none');
					jQuery('#feture_price').html();
				}
			}
			jQuery('#total_price').val((thousandseperator((parseFloat(total_price)+parseFloat(pkg_price)+parseFloat(final_cat_price)).toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
			if(parseFloat(jQuery('#total_price').val().split(thousands_sep).join('')) >0)
			{
				jQuery('#submit_coupon_code').css('display','block');
				jQuery('#price_package_price_list').css('display','block');
			}
			else
			{
				jQuery('#submit_coupon_code').css('display','none');
				jQuery('#price_package_price_list').css('display','none');
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
		});
		/*while on change of selection of category page featured option*/
		jQuery('#featured_c').live('change',function() {
		  /*check whether home page featured option exists or not.If not set it to zero by default*/
			if(jQuery('#featured_h').length > 0 )
			{
				if(jQuery('#featured_h').prop('checked') == true)
				{
					var featured_h = jQuery('#featured_h').val().split(thousands_sep).join('');
				}
				else
				{
					var featured_h = 0;
				}
			}
			else
			{
				var featured_h = 0;
			}
			/*if category page featured option is checked or not*/
			if(this.checked) {
				/*calculate the price of featured option for home page and category page*/
				total_price = parseFloat(total_price)  + parseFloat(jQuery('#featured_c').val().split(thousands_sep).join(''));
				if(total_price >0)
				{
					if(position == 3 || position == 4)
					{
						jQuery('#feture_price_id').css('display','block');
					}
					else
					{
						jQuery('#before_feture_price_id').css('display','block');
					}
					if(pkg_price > 0 )
						jQuery('#pakg_price_add').css('display','block');
				}
				if(edit == 1 && total_price >0 )
				{
					if(position == 3 || position == 4)
					{
						jQuery('#feture_price_id').css('display','block');
					}
					else
					{
						jQuery('#before_feture_price_id').css('display','block');
					}
					if(final_cat_price > 0)
						jQuery('#pakg_price_add').css('display','block');
				}
				jQuery('#feture_price').css('display','block');
				jQuery('#feture_price').html((thousandseperator(total_price.toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
				/*jquery to show final result*/				
				if(total_price > 0 && (final_cat_price > 0 || pkg_price > 0 ))
				{					
					jQuery('#cat_price_total_price').css('display','block');
					jQuery('#result_price_equ').css('display','block');
					jQuery('#currency_before_result_price').css('display','block');
					jQuery('#result_price').css('display','block');
					
					if((final_cat_price > 0 && pkg_price <= 0 )){					
						jQuery('#cat_price_add').css('display','block');	
					}
					jQuery('#result_price').html((thousandseperator((parseFloat(total_price)+parseFloat(pkg_price)+parseFloat(final_cat_price)).toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
				}else
				{
					if(final_cat_price <=0)
					{
						jQuery('#cat_price_total_price').css('display','none');
						jQuery('#result_price_equ').css('display','none');
						jQuery('#currency_before_result_price').css('display','none');
						jQuery('#result_price').css('display','none');
						jQuery('#result_price').html();
					}
				}
			}
			else
			{
				/*subtract the category page featured price from total featured price when unselecte the option*/
				total_price = parseFloat(total_price) - parseFloat(jQuery('#featured_c').val().split(thousands_sep).join(''));				
				if(jQuery('#featured_h').prop('checked') == true && total_price > 0)
				{
					if(position == 3 || position == 4)
					{
						jQuery('#feture_price_id').css('display','block');
					}
					else
					{
						jQuery('#before_feture_price_id').css('display','block');
					}
					/*if(final_cat_price > 0 || pkg_price > 0 )*/
					if(pkg_price > 0 )
						jQuery('#pakg_price_add').css('display','block');
					jQuery('#feture_price').css('display','block');
					jQuery('#feture_price').html((thousandseperator(total_price.toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
				}
				else if(total_price <= 0)
				{
					if(position == 3 || position == 4)
					{
						jQuery('#feture_price_id').css('display','none');
					}
					else
					{
						jQuery('#before_feture_price_id').css('display','none');
					}
					jQuery('#pakg_price_add').css('display','none');
					jQuery('#feture_price').css('display','none');
					jQuery('#cat_price_add').css('display','none');	
					jQuery('#feture_price').html();
					if(parseFloat(final_cat_price) > 0 && pkg_price > 0)
						jQuery('#result_price').html((thousandseperator((pkg_price+parseFloat(final_cat_price)).toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
					else
					{
						jQuery('#cat_price_total_price').css('display','none');
						jQuery('#result_price_equ').css('display','none');
						jQuery('#currency_before_result_price').css('display','none');
						jQuery('#result_price').css('display','none');
						jQuery('#result_price').html();
					}
				}
				/*jquery to show final result*/
				if(total_price > 0 && (final_cat_price > 0 || pkg_price > 0 ))
				{
					jQuery('#cat_price_total_price').css('display','block');
					jQuery('#result_price_equ').css('display','block');
					jQuery('#currency_before_result_price').css('display','block');
					jQuery('#result_price').css('display','block');
					if(!jQuery('#featured_h').prop('checked') == true)
					{
						jQuery('#pakg_price_add').css('display','none');
						jQuery('#feture_price').css('display','none');
						jQuery('#before_feture_price_id').css('display','none');
					}
					jQuery('#result_price').html((thousandseperator((parseFloat(total_price)+parseFloat(pkg_price)+parseFloat(final_cat_price)).toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
				}
				
				else if(final_cat_price <= 0 && total_price > 0)
				{
					jQuery('#cat_price_total_price').css('display','none');
					jQuery('#result_price_equ').css('display','none');
					jQuery('#currency_before_result_price').css('display','none');
					jQuery('#result_price').css('display','none');
					jQuery('#result_price').html();
				}
				else
				{
					jQuery('#feture_price').css('display','none');
					jQuery('#feture_price').html();
				}
			}
			jQuery('#total_price').val((thousandseperator((parseFloat(total_price)+parseFloat(pkg_price)+parseFloat(final_cat_price)).toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
			if(parseFloat(jQuery('#total_price').val().split(thousands_sep).join('')) >0)
			{
				jQuery('#submit_coupon_code').css('display','block');
				jQuery('#price_package_price_list').css('display','block');
			}
			else
			{
				jQuery('#submit_coupon_code').css('display','none');
				jQuery('#price_package_price_list').css('display','none');
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
		});
	}
	
	/*calculation for category wise prices*/
	var category_featured_price = jQuery('#featured_c').val();
	var home_featured_price = jQuery('#featured_h').val();
	var category_price_set = 'false';
	/*loop for category price calculation both for select box and for checkbox*/
	jQuery(".category_label input[name^='category'], .category_label input[name='selectall'],.category_label select[name^='category']").live('click', function() {
		
		final_cat_price = 0;
		var total_price = jQuery('#total_price').val();
		/*loop for selected or checked category price calculation both for select box and for checkbox*/
		jQuery.each(jQuery("#submit_form input[name^='category']:checked,#submit_form select[name^='category'] option:selected").not(":disabled"), function() {
			var cat_value = (jQuery(this).val());
			/*split the value to get the category price*/
			cat_price = cat_value.split(",");
			cat_price = parseFloat(cat_price[1]);
			/*final price of selected category*/
			final_cat_price = final_cat_price + cat_price;
		});
		home_featured_price=jQuery("input[name='featured_h']:checked").val();
		category_featured_price=jQuery("input[name='featured_c']:checked").val();
		if(home_featured_price == '' || !home_featured_price)
		{
			home_featured_price=0;
			
		}
		if(category_featured_price == '' || !category_featured_price )
		{
			category_featured_price=0;
		}
		cat_price=final_cat_price;		
		/*if category price is greater than zero*/
		if(final_cat_price > 0 )
		{
			/*tmp variable to get that the selected category has value greater than zero price or not*/
			category_price_set = true;
			
			/*show category price if price package value is greater than zero*/
			if(pkg_price > 0)
			{
				jQuery('#pakg_add').css('display','block');
				if( position == 3 || position == 4 )
				{
					jQuery('#cat_price_id').css('display','block');
					
				}
				else
				{
					jQuery('#before_cat_price_id').css('display','block');
				}
				jQuery('#cat_price').css('display','block');
				jQuery('#cat_price').html((thousandseperator(final_cat_price.toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
			}
			else if(cat_price > 0 && pkg_price <=0)
			{				
				
				
				/*show category price if price package value is less than zero and featured option selected*/
				if(jQuery('#featured_h').prop('checked') == true || jQuery('#featured_c').prop('checked') == true )
				{
					if(category_featured_price > 0 )
					{
						if(pkg_price <=0){
							jQuery('#cat_price_add').css('display','block');	
							jQuery('#cat_price_total_price').css('display','block');
							jQuery('#result_price_equ').css('display','block');
							jQuery('#currency_before_result_price').css('display','block');
							jQuery('#result_price').css('display','block');
							jQuery('#result_price').html((thousandseperator((final_cat_price+total_price).toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
						}
						if(edit == 1)
						{
							jQuery('#pakg_price_add').css('display','block');
							jQuery('#cat_price_total_price').css('display','block');
							jQuery('#result_price_equ').css('display','block');
							jQuery('#currency_before_result_price').css('display','block');
							jQuery('#result_price').css('display','block');
							jQuery('#result_price').html((thousandseperator((final_cat_price+total_price).toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
						}
						else if(pkg_price > 0 )
						{
							jQuery('#pakg_add').css('display','block');
						}
						if( position == 3 || position == 4 )
						{
							jQuery('#cat_price_id').css('display','block');
						}
						else
						{
							jQuery('#before_cat_price_id').css('display','block');
						}
						jQuery('#cat_price').css('display','block');
						jQuery('#cat_price').html((thousandseperator(final_cat_price.toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
					}
					else if(home_featured_price > 0)
					{
						if(pkg_price <=0){
							jQuery('#cat_price_add').css('display','block');	
						}
						if(edit == 1)
						{
							jQuery('#pakg_price_add').css('display','block');
							jQuery('#cat_price_total_price').css('display','block');
							jQuery('#result_price_equ').css('display','block');
							jQuery('#currency_before_result_price').css('display','block');
							jQuery('#result_price').css('display','block');
							jQuery('#result_price').html((thousandseperator((final_cat_price+total_price).toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
						}
						else
						{
							jQuery('#pakg_add').css('display','block');
						}
						if( position == 3 || position == 4 )
						{
							jQuery('#cat_price_id').css('display','block');
						}
						else
						{
							jQuery('#before_cat_price_id').css('display','block');
						}
						jQuery('#cat_price').css('display','block');
						jQuery('#cat_price').html((thousandseperator(final_cat_price.toFixed(num_decimals))).replace('.',decimal_sep).replace(' ',''));
					}
					else
					{
						
						jQuery('#cat_price_add').css('display','none');
						jQuery('#cat_price_total_price').css('display','none');
						jQuery('#result_price_equ').css('display','none');
						jQuery('#currency_before_result_price').css('display','none');
						jQuery('#result_price').css('display','none');
						jQuery('#result_price').html();
						if(edit == 1)
						{
							jQuery('#pakg_price_add').css('display','none');
						}
						else
						{
							jQuery('#pakg_add').css('display','none');
						}
						if( position == 3 || position == 4 )
						{
							jQuery('#cat_price_id').css('display','none');
						}
						else
						{
							jQuery('#before_cat_price_id').css('display','none');
						}
						jQuery('#cat_price').css('display','none');
						jQuery('#cat_price').html();
					}
				}
				else
				{
					if(pkg_price > 0)
					{
						jQuery('#pakg_add').css('display','block');
					}
					if( position == 3 || position == 4 )
					{
						jQuery('#cat_price_id').css('display','block');
					}
					else
					{
						jQuery('#before_cat_price_id').css('display','block');
					}
					jQuery('#cat_price').css('display','block');
					jQuery('#cat_price').html(thousandseperator(final_cat_price.toFixed(num_decimals)).replace('.',decimal_sep).replace(' ',''));
				}
			}
			if(pkg_price > 0)
			{
				jQuery('#cat_price_total_price').css('display','block');
				jQuery('#result_price_equ').css('display','block');
				jQuery('#currency_before_result_price').css('display','block');
				jQuery('#result_price').css('display','block');
				jQuery('#result_price').html(thousandseperator((parseFloat(pkg_price)+final_cat_price+parseFloat(category_featured_price)+parseFloat(home_featured_price)).toFixed(num_decimals)).replace('.',decimal_sep).replace(' ',''));
			}
			else if(cat_price < 0)
			{
				jQuery('#cat_price_total_price').css('display','none');
				jQuery('#result_price_equ').css('display','none');
				jQuery('#currency_before_result_price').css('display','none');
				if( position == 3 || position == 4 )
				{
					jQuery('#cat_price_id').css('display','none');
				}
				else
				{
					jQuery('#before_cat_price_id').css('display','none');
				}
				jQuery('#result_price').html();
			}
		}
		else
		{
			if(pkg_price <= 0 && final_cat_price<=0)
			{
				jQuery('#cat_price_add').css('display','none');
				jQuery('#cat_price_total_price').css('display','none');
				jQuery('#result_price_equ').css('display','none');
				jQuery('#currency_before_result_price').css('display','none');
			}
			if(parseFloat(total_price) <=0 )
			{
				jQuery('#cat_price_total_price').css('display','none');
				jQuery('#result_price_equ').css('display','none');
				jQuery('#currency_before_result_price').css('display','none');
			}
			if( position == 3 || position == 4 )
			{
				jQuery('#cat_price_id').css('display','none');
			}
			else
			{
				jQuery('#before_cat_price_id').css('display','none');
			}
			jQuery('#result_price').html(thousandseperator((parseFloat(pkg_price)+parseFloat(category_featured_price)+parseFloat(home_featured_price)).toFixed(num_decimals)).replace('.',decimal_sep).replace(' ',''));
		}
	
		/*if all the category is unselected than it goes in the following condition*/
		if(jQuery("input[name='category[]']:checked,select option:selected").not(":disabled").length <= 0)
		{
			jQuery('#pakg_add').css('display','none');
			if( position == 3 || position == 4 )
			{
				jQuery('#cat_price_id').css('display','none');
			}
			else
			{
				jQuery('#before_cat_price_id').css('display','none');
			}
			jQuery('#cat_price').css('display','none');
			jQuery('#cat_price').html();
			if(jQuery('#featured_h').prop('checked') == true || jQuery('#featured_c').prop('checked') == true )
			{
				if(category_featured_price > 0 && pkg_price > 0)
				{
					jQuery('#cat_price_total_price').css('display','block');
					jQuery('#result_price_equ').css('display','block');
					jQuery('#currency_before_result_price').css('display','block');
					jQuery('#result_price').css('display','block');
					jQuery('#result_price').html(thousandseperator((parseFloat(total_price)).toFixed(num_decimals)).replace('.',decimal_sep).replace(' ',''));
				}
				else if(home_featured_price > 0 && pkg_price > 0)
				{
					jQuery('#cat_price_total_price').css('display','block');
					jQuery('#result_price_equ').css('display','block');
					jQuery('#currency_before_result_price').css('display','block');
					jQuery('#result_price').css('display','block');
					jQuery('#result_price').html(thousandseperator((parseFloat(total_price)).toFixed(num_decimals)).replace('.',decimal_sep).replace(' ',''));
				}
				else
				{
					jQuery('#cat_price_total_price').css('display','none');
					jQuery('#result_price_equ').css('display','none');
					jQuery('#currency_before_result_price').css('display','none');
					jQuery('#result_price').css('display','none');
					jQuery('#result_price').html();
					jQuery('#pakg_add').css('display','none');
					if( position == 3 || position == 4 )
					{
						jQuery('#cat_price_id').css('display','none');
					}
					else

					{
						jQuery('#before_cat_price_id').css('display','none');
					}
					if(edit == 1)
					{
						jQuery('#pakg_price_add').css('display','none');
					}
					jQuery('#cat_price').css('display','none');
					jQuery('#cat_price').html();
				}
			}
			else
			{
				if(pkg_price > 0)
				{
					jQuery('#pakg_add').css('display','block');
				}
				else
				{
					jQuery('#pakg_add').css('display','none');
				}
				jQuery('#cat_price_total_price').css('display','none');
				jQuery('#result_price_equ').css('display','none');
				jQuery('#currency_before_result_price').css('display','none');
				jQuery('#result_price').css('display','none');
				jQuery('#result_price').html();
			}
		}
		/*set default option*/
		if(final_cat_price <=0)
		{
			jQuery('#pakg_add').css('display','none');
			if( position == 3 || position == 4 )
			{
				jQuery('#cat_price_id').css('display','none');
			}
			else
			{
				jQuery('#before_cat_price_id').css('display','none');
			}
			jQuery('#cat_price').css('display','none');
			jQuery('#cat_price').html();
			if(jQuery('#featured_h').prop('checked') == true || jQuery('#featured_c').prop('checked') == true )
			{
				if(category_featured_price > 0 && pkg_price > 0)
				{
					jQuery('#cat_price_total_price').css('display','block');
					jQuery('#result_price_equ').css('display','block');
					jQuery('#currency_before_result_price').css('display','block');
					jQuery('#result_price').css('display','block');
					jQuery('#result_price').html(thousandseperator((parseFloat(pkg_price)+parseFloat(category_featured_price)+parseFloat(home_featured_price)).toFixed(num_decimals)).replace('.',decimal_sep).replace(' ',''));
				}
				else if(home_featured_price > 0 && pkg_price > 0)
				{
					jQuery('#cat_price_total_price').css('display','block');
					jQuery('#result_price_equ').css('display','block');
					jQuery('#currency_before_result_price').css('display','block');
					jQuery('#result_price').css('display','block');
					jQuery('#result_price').html(thousandseperator((parseFloat(pkg_price)+parseFloat(category_featured_price)+parseFloat(home_featured_price)).toFixed(num_decimals)).replace('.',decimal_sep).replace(' ',''));
				}
				else
				{
					jQuery('#cat_price_total_price').css('display','none');
					jQuery('#result_price_equ').css('display','none');
					jQuery('#currency_before_result_price').css('display','none');
					jQuery('#result_price').css('display','none');
					jQuery('#result_price').html();
					jQuery('#pakg_add').css('display','none');
					if( position == 3 || position == 4 )
					{
						jQuery('#cat_price_id').css('display','none');
					}
					else
					{
						jQuery('#before_cat_price_id').css('display','none');
					}
					if(edit == 1)
					{
						jQuery('#pakg_price_add').css('display','none');
					}
					jQuery('#cat_price').css('display','none');
					jQuery('#cat_price').html();
				}
			}
			else
			{
				jQuery('#pakg_add').css('display','none');
				jQuery('#cat_price_total_price').css('display','none');
				jQuery('#result_price_equ').css('display','none');
				jQuery('#currency_before_result_price').css('display','none');
				jQuery('#result_price').css('display','none');
				jQuery('#result_price').html();
				total_price = pkg_price;
			}
		}
		if(home_featured_price == '' || !home_featured_price)
		{
			home_featured_price=0;
			
		}
		if(category_featured_price == '' || !category_featured_price )
		{
			category_featured_price=0;
		}
		
		if(isNaN(pkg_price))
			pkg_price = 0;

		jQuery('#total_price').val(thousandseperator((parseFloat(pkg_price)+parseFloat(final_cat_price)+parseFloat(category_featured_price)+parseFloat(home_featured_price)).toFixed(num_decimals)).replace('.',decimal_sep).replace(' ',''));

		
		if(parseFloat(jQuery('#total_price').val().split(thousands_sep).join('')) >0)
		{
			jQuery('#submit_coupon_code').css('display','block');
			jQuery('#price_package_price_list').css('display','block');
		}
		else
		{
			jQuery('#submit_coupon_code').css('display','none');
			jQuery('#price_package_price_list').css('display','none');
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
	});
	
	/*add thousand seperator*/
	function thousandseperator(amt)
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
	
});