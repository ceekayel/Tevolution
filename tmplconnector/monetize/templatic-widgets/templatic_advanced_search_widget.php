<?php
/*
 * Create the templatic advanced search widget
 */
class templatic_advanced_search extends WP_Widget {
	function templatic_advanced_search() {
	/*Constructor*/
		$widget_ops = array('classname' => 'templatic-advanced-search', 'description' => __('Display search fields for a specific post type. Custom fields selected to show inside the Advanced search form will also show inside this widget. Works best in sidebar areas.',ADMINDOMAIN),'before_widget'=>'<div class="column_wrap">' );
		$this->WP_Widget('templatic_advanced_search', __('T &rarr; Advanced Search',ADMINDOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
	/* prints the widget*/
		extract($args, EXTR_SKIP);
		global $wp_locale;
		
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']); 		
		$post_type = empty($instance['post_type']) ? 'post' : apply_filters('widget_post_type', $instance['post_type']);
		$search_custom_fields = empty($instance['search_custom_fields']) ? array() : apply_filters('widget_search_custom_fields', $instance['search_custom_fields']);
		$search_ctype = empty($instance['search_ctype']) ? array() : apply_filters('widget_search_ctype', $instance['search_ctype']);
		$orderby_customfields = empty($instance['orderby_customfields']) ? array() : apply_filters('widget_orderby_customfields', array_unique($instance['orderby_customfields']));
		
		echo $args['before_widget'];
		if (function_exists('icl_register_string')) {	
			icl_register_string(DOMAIN,'templatic_about_title'.$title,$title);
			$title = icl_t(DOMAIN, 'templatic_about_title'.$title,$title);
		}
		if ( $title <> "" ) { 
			echo $args['before_title'].$title.$args['after_title'];
		}		
		
		/* include datepicker js file */
		wp_enqueue_script('jquery-ui-datepicker');	
		 /*localize our js*/
		$aryArgs = array(
			'monthNames'        => strip_array_indices( $wp_locale->month ),
			'monthNamesShort'   => strip_array_indices( $wp_locale->month_abbrev ),
			'monthStatus'       => __( 'Show a different month', DOMAIN ),
			'dayNames'          => strip_array_indices( $wp_locale->weekday ),
			'dayNamesShort'     => strip_array_indices( $wp_locale->weekday_abbrev ),
			'dayNamesMin'       => strip_array_indices( $wp_locale->weekday_initial ),
			/* is Right to left language? default is false*/
			'isRTL'             => (isset($wp_locale->is_rtl))? $wp_locale->is_rtl :'',
		);
		
		/* Pass the array to the enqueued JS*/
		wp_localize_script( 'jquery-ui-datepicker', 'objectL11tmpl', $aryArgs );
		
		?>
            <form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="form_front_style">        
                <div class="form_row clearfix search_keyword">               
                    <input class="adv_input" name="s" id="adv_s" type="text" PLACEHOLDER="<?php _e('Search',DOMAIN); ?>" value="" />			  
                    <span class="message_error2"  style="color:red;font-size:12px;" id="search_error"></span>			  
                </div>
                <?php				
				if(!empty($orderby_customfields) && is_array($orderby_customfields)){
					
					foreach($orderby_customfields as $value){
						
						display_search_widget_custom_post_fields($value,$instance,1);

					}/*Finish Foreach loop*/

				}/*finish search_custom_fields if condition*/
				?>
                
                <input type="hidden" name="search_template" value="1"/>
                <!--<input class="adv_input" name="adv_search" id="adv_search" type="hidden" value="1"  />-->
                <input class="adv_input" name="post_type" id="post_type" type="hidden" value="<?php echo $post_type; ?>"  />
                <input type="submit" name="submit" value="<?php _e('Search',DOMAIN); ?>" class="adv_submit"  onclick="return set_adv_search();"/>              
            </form>
		<?php		
		echo $args['after_widget'];
	}
	function update($new_instance, $old_instance) {
		/*save the widget*/
		$new_instance['orderby_customfields']=array_unique($new_instance['orderby_customfields']);
		return $new_instance;
	}
	function form($instance) {
		/*widgetform in backend*/
		global $search_custom_fields_instance;
		$instance = wp_parse_args( (array) $instance, array( 'title' => '',  'post_type' => '','search_custom_fields'=>'','search_ctype'=>'','orderby_customfields'=>'') );
		$title = ($instance['title']) ? $instance['title'] : __("Advanced Search",ADMINDOMAIN);
		$current_post_type = ($instance['post_type']) ? $instance['post_type'] : 'post';
		
		/*global set advance search widget instance */
		$search_custom_fields_instance = $instance;
		$search_custom_fields = $this->get_field_name('search_custom_fields');
		$search_ctype = $this->get_field_name('search_ctype');
		$orderby_customfields = $this->get_field_name('orderby_customfields');
	?>
	<p>
	  <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title:',ADMINDOMAIN);?>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	  </label>
	</p>
	<p>
    	<label for="<?php echo $this->get_field_id('post_type');?>" ><?php echo __('Post Type:',ADMINDOMAIN);?>     </label>
    	<select  id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="widefat" onchange="tmpl_search_post_type(this,'<?php echo $this->get_field_id('custom_fields_lists'); ?>','<?php echo $search_custom_fields;?>','<?php echo $search_ctype;?>','<?php echo $this->get_field_id('search_custom_fields'); ?>','<?php echo $this->get_field_id('custom_fields_lists')?>');">
			<option value="post"><?php _e("Post",DOMAIN);?></option>
    <?php
		$all_post_types = get_option("templatic_custom_post");
		foreach($all_post_types as $key=>$post_type){ 
			if($key=='admanager')
				continue;
		?>	
			<option value="<?php echo $key;?>" <?php if($key == $current_post_type){ echo 'selected="selected"';}?>><?php echo esc_attr($post_type['label']);?></option>
     <?php }?>
    	</select>
        <span class="description"><?php echo __('Select the post type you wish to display on an advanced search form.',ADMINDOMAIN);?></span>
    </p>
    
     
    <span><label for="<?php echo $this->get_field_id('search_field');?>" ><?php echo __('Search Field:',ADMINDOMAIN);?>     </label></span>
    <div id="<?php echo $this->get_field_id('custom_fields_lists'); ?>" class="custom_fields_list dropdown <?php echo $this->get_field_id('custom_fields_lists'); ?>" data-class="<?php echo $this->get_field_id('search_custom_fields');?>"  data-order="<?php echo $orderby_customfields;?>">
    <?php
	if($current_post_type){
		echo tmpl_advance_search_custom_fields($current_post_type,$search_custom_fields,$search_ctype,$this->get_field_id('search_custom_fields'),$this->get_field_id('custom_fields_lists'));
	}
	?>
    </div>
    <p class="description"><?php echo __('Select custom field to display selected custom fields on frontend.',ADMINDOMAIN);?></p>
    <br class="clear">
    
    <div class="<?php echo $this->get_field_id('search_custom_fields'); ?>  advance_search_custom_fields" id="<?php echo $this->get_field_id('search_custom_fields'); ?>">
    <?php
	if(!empty( $instance['orderby_customfields']) && is_array($instance['orderby_customfields'])):
		foreach(array_unique($instance['orderby_customfields']) as $k=>$value){
			$key=$value;
				
			$ctype=get_post_meta($key,"ctype",true);
			$search_ctype_val=$instance['search_ctype'][$key]['search_ctype'];
			
			$option_title=$instance['search_ctype'][$key]['option_title'];
			$option_values=$instance['search_ctype'][$key]['option_values'];
			
			$min_option_title=$instance['search_ctype'][$key]['min_option_title'];
			$min_option_values=$instance['search_ctype'][$key]['min_option_values'];
			$max_option_title=$instance['search_ctype'][$key]['max_option_title'];
			$max_option_values=$instance['search_ctype'][$key]['max_option_values'];
			$range_min=$instance['search_ctype'][$key]['range_min'];
			$range_max=$instance['search_ctype'][$key]['range_max'];
			
			?>
            <div class="widget <?php echo 'custom_'. $this->get_field_id('search_custom_fields').'_'.$key; ?>" id="search_custom_field_<?php echo $key; ?>"  data-sort="<?php echo $key;?>">
                <input type="hidden" value="<?php echo $key;?>" name="<?php echo $orderby_customfields; ?>[]">
                <div class="widget-top">
                    <div class="widget-title-action">
                        <a class="widget-action hide-if-no-js" href="#available-widgets"></a>                        
                    </div>
                    <div class="widget-title">
                   		<h4><?php echo get_the_title($key);?></h4>                       
                    </div><!--Finish widget-title div-->
                </div><!--Finish widget-top div-->
                <?php
				echo '<div class="widget-inside">';
				if($ctype=='post_categories' || $ctype=='multicity' || $ctype=='geo_map' || $ctype=='date'){
						
					echo "<input type='hidden' value='".$ctype."' name='".$search_ctype."[".$key."][search_ctype]'>";
					
					if($ctype=='geo_map'){
						$radius_measure=$instance['search_ctype'][$key]['radius_measure'];
						$miles_search=$instance['search_ctype'][$key]['miles_search'];						
						echo '<p><label><input name="'.$search_ctype.'['.$key.'][miles_search]" type="checkbox" value="1" style="width:10px;"  '.(($miles_search=='1')? 'checked':'').' />'.__('Search By Distance?',ADMINDOMAIN).'</label>';
						echo '<p><label>'.__('Search By','templatic-admin').'
							<select name="'.$search_ctype.'['.$key.'][radius_measure]">
								<option value="kilometer" '.(($radius_measure=='kilometer')? 'selected':'').'>'.__('Kilometers',ADMINDOMAIN).'</option>
								<option value="miles" '.(($radius_measure=='miles')? 'selected':'').'>'. __('Miles',ADMINDOMAIN).'</option>             
							</select>
						</p>';						
					}

				}else{
					/*Disable search type option */
					$text=$date=$multicheckbox=$radio=$select=$min_max_range=$min_max_range_select=$slider_range='';
					if($ctype=='text' || $ctype=='textarea' || $ctype=='texteditor'){
						$date=$multicheckbox=$radio=$select=$min_max_range=$min_max_range_select=$slider_range='disabled';
					}elseif($ctype=='multicheckbox' || $ctype=='select' || $ctype=='radio'){
						$date=$text=$min_max_range=$min_max_range_select=$slider_range='disabled';
					}elseif($ctype=='range_type'){
						$text=$date=$multicheckbox=$radio=$select='disabled';
					}
					if($ctype=='text'){
						$select='';		 
					}
					
					echo '<p><label>'.__('Show on search as',ADMINDOMAIN).'</label>';
					echo "<select name='".$search_ctype."[".$key."][search_ctype]' class='search_ctype_".$key." search_custom_ctype' data-post-id='".$key."' onchange='select_search_type_option(this);' data-search-type='".$ctype."'>";
						echo '<option value="" >'. __('Select type on search',ADMINDOMAIN).'</option>';
						echo '<option '.$text.' value="text" '.(($search_ctype_val=='text') ? 'selected':'').'>'. __('Text',ADMINDOMAIN).'</option>';
						/*echo '<option '.$date.' value="date">'.__('Date Picker',ADMINDOMAIN).'</option>';*/
						echo '<option '.$multicheckbox.' value="multicheckbox" '.(($search_ctype_val=='multicheckbox')? 'selected':'').'>'.__('Multi Checkbox',ADMINDOMAIN).'</option>';
						echo '<option '.$radio.' value="radio"  '.(($search_ctype_val=='radio')? 'selected':'').'>'.__('Radio',ADMINDOMAIN).'</option>';
						echo '<option '.$select.' value="select" '.(($search_ctype_val=='select')? 'selected':'').'>'.__('Select',ADMINDOMAIN).'</option>';
						echo '<option '.$min_max_range.' value="min_max_range" '.(($search_ctype_val=='min_max_range')? 'selected':'').'>'.__('Min-Max Range (Text)',ADMINDOMAIN).'</option>';
						echo '<option '.$min_max_range_select.' value="min_max_range_select" '.(($search_ctype_val=='min_max_range_select')? 'selected':'').'>'.__('Min-Max Range (Select)',ADMINDOMAIN).'</option>';
						echo '<option '.$slider_range.' value="slider_range" '.(($search_ctype_val=='slider_range')? 'selected':'').'>'.__('Range Slider',ADMINDOMAIN).'</option>';
					echo "</select>";
					echo '</p>';
					
					/*select option */
					echo "<div class='search_select_".$key." search_select' ".(($ctype=='text' && $search_ctype_val=='select')? "style='display:block'":"style='display:none'").">";
					echo '<p><label>'.__('Option Title',ADMINDOMAIN).'</label><input type="text" size="41" value="'.$option_title.'" id="search_option_title" name="'.$search_ctype.'['.$key.'][option_title]"><span class="clearfix description">'.__('Separate multiple option titles with a comma. eg. Yes,No',ADMINDOMAIN).'</span></p>';
					echo '<p><label>'.__('Option values',ADMINDOMAIN).'</label><input type="text" size="41" value="'.$option_values.'" id="search_option_values" name="'.$search_ctype.'['.$key.'][option_values]"><span class="clearfix description">'.__('Separate multiple option titles with a comma. eg. Yes,No',ADMINDOMAIN).'</span></p>';							
					echo "</div>";
					/*End Search title */
			
					/* Range field related option */
					echo "<div class='range_type_select_".$key." range_type_select' ".(($search_ctype_val=='min_max_range_select')? "style='display:block'":"style='display:none'").">";
					echo '<p><label>'.__('Min Option Title',ADMINDOMAIN).'</label><input type="text" size="41" value="'.$min_option_title.'" id="search_min_option_title" name="'.$search_ctype.'['.$key.'][min_option_title]"><span class="clearfix description">'.__('Separate multiple option titles with a comma. eg. Yes,No',ADMINDOMAIN).'</span></p>';
					echo '<p><label>'.__('Min Option values',ADMINDOMAIN).'</label><input type="text" size="41" value="'.$min_option_values.'" id="search_min_option_values" name="'.$search_ctype.'['.$key.'][min_option_values]"><span class="clearfix description">'.__('Separate multiple option titles with a comma. eg. Yes,No',ADMINDOMAIN).'</span></p>';
					echo '<p><label>'.__('Max Option Title',ADMINDOMAIN).'</label><input type="text" size="41" value="'.$max_option_title.'" id="search_max_option_title" name="'.$search_ctype.'['.$key.'][max_option_title]"><span class="clearfix description">'.__('Separate multiple option titles with a comma. eg. Yes,No',ADMINDOMAIN).'</span></p>';
					echo '<p><label>'.__('Max Option values',ADMINDOMAIN).'</label><input type="text" size="41" value="'.$max_option_values.'" id="search_mzx_option_values" name="'.$search_ctype.'['.$key.'][max_option_values]"><span class="clearfix description">'.__('Separate multiple option titles with a comma. eg. Yes,No',ADMINDOMAIN).'</span></p>';
					echo "</div>";
					
					/* Range slider related option */
					echo "<div class='range_type_slider_".$key." range_type_slider' ".(($search_ctype_val=='slider_range')? "style='display:block'":"style='display:none'").">";
					echo '<p><label>'.__('Define your range',ADMINDOMAIN).'</label>';
					echo '<fieldset>
					<input type="text" placeholder="Min value" value="'.$range_min.'" name="'.$search_ctype.'['.$key.'][range_min]" id="range_min_value">
					<input type="text" placeholder="max value" value="'.$range_max.'" name="'.$search_ctype.'['.$key.'][range_max]" id="range_max_value">
					</fieldset>';
					echo '</p>';
					echo "</div>";
				}
				
					echo '<div class="widget-control-actions">';
					echo '<div class="alignleft">';
					echo '<a class="widget-control-remove" href="#remove">'.__('Delete',ADMINDOMAIN).'</a> | <a class="widget-control-close" href="#close">'.__('Close',ADMINDOMAIN).'</a>';
					echo '</div><br class="clear" />';
					
					echo '</div>';/*Finish widget control actionsdiv*/
				
				echo '</div>';/*Finish widget-inside div*/
				?>
            </div>
            <?php
		}
	
	endif;/*finish search ctype if condition*/
	?>
    </div>
    <br class="clear">
    <script  type="text/javascript" async >
	jQuery(document).ready(function($){
		jQuery("#<?php echo $this->get_field_id('search_custom_fields'); ?>").sortable({			
			 'start': function (event, ui) {
				   /*jQuery Ui-Sortable Overlay Offset Fix*/
				   if ($.browser.webkit) {
					  wscrolltop = $(window).scrollTop();
				   }
			 },
			 'sort': function (event, ui) {				   
				   if ($.browser.webkit) {
					  ui.helper.css({ 'top': ui.position.top + wscrolltop + 'px' });
				   }
			 },
			 update: function(e, ui){
				var widget = jQuery(e.target).closest('div.widget')
				var image_names = '',data_sort='',data_sort_key='';				
				var sortable=0;
				/*Sort order loop */
				jQuery("#<?php echo $this->get_field_id('search_custom_fields'); ?> .widget").css('cursor','default').each(function() {
					data_sort=jQuery(this).attr('data-sort');
					/* search custom field sort order assign */
					widget.find('form').find("input:hidden[name='<?php echo $orderby_customfields;?>["+sortable+"]']").val(data_sort);
					sortable++;
				});
				data = widget.find('form').serialize();
				widget = jQuery(widget);
				jQuery('.spinner', widget).show();

				jQuery.ajax({
					 url: ajaxUrl,
					 type: 'POST',
					 data:'action=save-widget&savewidgets='+jQuery('#_wpnonce_widgets').val()+'&'+data, 					 
					 success:function(result){	
							 jQuery('.spinner').hide();
							/*document.getElementById('imgarr').value = result;*/
					 }
				 });		
				
			}
		 });
	});	
	
	
	function show_custom_fields(custom_fields_lists){
		jQuery("#"+custom_fields_lists+" dd ul").show();		
		return false;
	}
	
	function add_search_custom_field(value,custom_fields_lists){
		var cvalue=value;
		jQuery("#"+custom_fields_lists+" dd ul").hide();		
		var custom_field_div = custom_fields_lists;
		var custom_field_class = jQuery('#'+custom_fields_lists).attr('data-class');
		var data_search_order = jQuery('#'+custom_fields_lists).attr('data-order');
		
		var html_custom_search='';
		html_custom_search+='<div class="widget open custom_'+custom_field_class+'_'+cvalue+'" id="search_custom_field_'+cvalue+'" data-sort="'+cvalue+'">';
		html_custom_search+='<input type="hidden" name="'+data_search_order+'[]" value="'+cvalue+'" >';
		html_custom_search+=jQuery('#'+custom_field_div+' ul li div#search_custom_field_'+cvalue).html();
		html_custom_search+='</div>';
		jQuery('.'+custom_field_class).prepend(html_custom_search);		
		return false;
	}	
	
	jQuery(document).bind('click', function(e) {
		var $clicked = jQuery(e.target);
		if (! $clicked.parents().hasClass("dropdown"))
			jQuery("#<?php echo $this->get_field_id('custom_fields_lists'); ?> dd ul").hide();
	});
	</script>
	<?php
	add_action('admin_footer','tmpl_advance_searchable_scripts');	
	}
}
/*
 * templatic about us widget init
 */
add_action( 'widgets_init', create_function('', 'return register_widget("templatic_advanced_search");') );


function tmpl_advance_search_custom_fields($post_types,$search_custom_fields,$search_ctype,$search_custom_fields_id,$custom_fields_list_id){
	
	global $wpdb,$post,$_wp_additional_image_sizes,$sitepress,$search_custom_fields_instance;

	/* Get the search custom fields value from global search custom fields instance variable */
	$search_custom_fields_value = ($search_custom_fields_instance['search_custom_fields']) ? $search_custom_fields_instance['search_custom_fields'] : array();	
	
	$args=array('post_type'      => 'custom_fields',
					'posts_per_page' => -1	,
					'post_status'    => array('publish'),
					'post__not_in'   => $remove_post_id_array,
					'meta_query'     => array('relation' => 'AND',
											array('key' => 'post_type_'.$post_types, 'value' => array('all',$post_types),'compare' => 'IN','type'=> 'text')
										),
					'meta_key'       => $post_types.'_sort_order',
					'orderby'        => 'meta_value_num',
					'meta_value_num' => $post_types.'_sort_order',
					'order'          => 'ASC',
					''
				);
	
	add_filter('posts_join', 'templ_advance_search_custom_fields_where_filter');
	
	$post_meta_info = new WP_Query($args);
	
	remove_filter('posts_join', 'templ_advance_search_custom_fields_where_filter');
	
	if($post_meta_info->have_posts()){
		echo '<dt><a href="#" onclick=\'show_custom_fields("'.$custom_fields_list_id.'");return false;\'><span>'.__('Add a Search Field',ADMINDOMAIN).'</span></a></dt>';
		
		echo "<dd><ul>";
		$i=0;
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
			$is_active=get_post_meta($post->ID,"is_active",true);
			$ctype=get_post_meta($post->ID,"ctype",true);
			
			$show_in = apply_filters('templ_advance_search_showin_option',get_post_meta($post->ID,"show_in_property_search",true),$post);
			
			
			
			/* Continue Loop on custom field disable or heading type custom fields */
			if($is_active!=1 || $ctype=='heading_type' || $ctype=='image_uploader' || $ctype=='oembed_video' || $ctype=='coupon_uploader' || $ctype=='upload' ){
				continue;
			}
			
			/*Get the  custom fields htmlvar_name */
			$htmlvar_name=get_post_meta($post->ID,"htmlvar_name",true);
			/*Translate custom fields id using icl object id function */
			if(function_exists('icl_object_id')){
				$icl_post_id=icl_object_id(get_the_ID(), 'post_custom_fields', false);
				$post->post_title=get_the_title($icl_post_id);
			}
			
			if($htmlvar_name!='post_title' && $htmlvar_name!='post_content' && $htmlvar_name!='post_excerpt' && $post->post_title!=''){
				$checked=(!empty($search_custom_fields_value) && in_array(get_the_ID(),$search_custom_fields_value))? 'checked="checked"' : '';
				
				echo "<li>";				
				echo "<label><a href='#' data-value='".get_the_ID()."' onclick=\"add_search_custom_field(".get_the_ID().",'".$custom_fields_list_id."');return false;\">".$post->post_title."</a></label>";
				/*echo "<label><input type='checkbox' name='".$search_custom_fields."[".$i++."]' value='".get_the_ID()."' ".$checked."> ".$post->post_title."</label>";*/
				echo '<div class="widget custom_'. $search_custom_fields_id.'_'.get_the_ID().' open" id="search_custom_field_'.get_the_ID().'" style="display:none" data-show="'.$show_in.'" data-search-id="'.get_the_ID().'">';
					echo '<div class="widget-top">';
						echo '<div class="widget-title-action">
                        <a class="widget-action hide-if-no-js" href="#available-widgets"></a>                        
                   		</div>';
						echo '<div class="widget-title">';
							echo '<h4>'.$post->post_title.'</h4>';
						echo '</div>';/*Finish widget-title div*/
					echo '</div>';/*Finish widget-top div*/
					
					echo '<div class="widget-inside" style="display: block;">';
					if($ctype=='post_categories' || $ctype=='multicity' || $ctype=='geo_map' || $ctype=='date'){
						
						echo "<input type='hidden' value='".$ctype."' name='".$search_ctype."[".get_the_ID()."][search_ctype]'>";
						if($ctype=='geo_map'){
							$radius_measure=(isset($search_custom_fields_instance['search_ctype'][get_the_ID()]['radius_measure']))?$search_custom_fields_instance['search_ctype'][get_the_ID()]['radius_measure']:'';
							$miles_search=(isset($search_custom_fields_instance['search_ctype'][get_the_ID()]['miles_search']))?$search_custom_fields_instance['search_ctype'][get_the_ID()]['miles_search']:'';							
							echo '<p><label><input name="'.$search_ctype.'['.get_the_ID().'][miles_search]" type="checkbox" value="1" style="width:10px;"  />'.__('Search By Distance?',ADMINDOMAIN).'</label>';
							echo '<p><label>'.__('Search By','templatic-admin').'
								<select name="'.$search_ctype.'['.get_the_ID().'][radius_measure]">
									<option value="kilometer" '.(($radius_measure=='kilometer')? 'selected':'').'>'.__('Kilometers',ADMINDOMAIN).'</option>
									<option value="miles" '.(($radius_measure=='miles')? 'selected':'').'>'. __('Miles',ADMINDOMAIN).'</option>             
								</select>
							</p>';							
						}
					}else{
						/*Disable search type option */
						$text=$date=$multicheckbox=$radio=$select=$min_max_range=$min_max_range_select=$slider_range='';
						if($ctype=='text' || $ctype=='textarea' || $ctype=='texteditor'){
							$date=$multicheckbox=$radio=$select=$min_max_range=$min_max_range_select=$slider_range='disabled';
						}elseif($ctype=='multicheckbox' || $ctype=='select' || $ctype=='radio'){
							$date=$text=$min_max_range=$min_max_range_select=$slider_range='disabled';
						}elseif($ctype=='range_type'){
							$text=$date=$multicheckbox=$radio=$select='disabled';
						}
						if($ctype=='text'){
							$select='';		 
						}
						/*Get the saved Data */
						$search_ctype_val=(isset($search_custom_fields_instance['search_ctype'][get_the_ID()]['search_ctype']))?  $search_custom_fields_instance['search_ctype'][get_the_ID()]['search_ctype'] :'';
						if($search_ctype_val==''){
							$search_ctype_val=get_post_meta(get_the_ID(),'search_ctype',true);	
						}
						
						$option_title=(isset($search_custom_fields_instance['search_ctype'][get_the_ID()]['option_title']))? $search_custom_fields_instance['search_ctype'][get_the_ID()]['option_title']: '';
						$option_values=(isset($search_custom_fields_instance['search_ctype'][get_the_ID()]['option_values']))? $search_custom_fields_instance['search_ctype'][get_the_ID()]['option_values'] : '';
						
						if($option_title=='' && $option_values==''){							  
							$option_title=get_post_meta(get_the_ID(),'option_title',true);
							$option_values=get_post_meta(get_the_ID(),'option_values',true);
						}
						
						
						$min_option_title=(isset($search_custom_fields_instance['search_ctype'][get_the_ID()]['min_option_title']))? $search_custom_fields_instance['search_ctype'][get_the_ID()]['min_option_title'] : '';
						$min_option_values=(isset($search_custom_fields_instance['search_ctype'][get_the_ID()]['min_option_values']))? $search_custom_fields_instance['search_ctype'][get_the_ID()]['min_option_values']:'';
						$max_option_title=(isset($search_custom_fields_instance['search_ctype'][get_the_ID()]['max_option_title']))?$search_custom_fields_instance['search_ctype'][get_the_ID()]['max_option_title']:'';
						$max_option_values=(isset($search_custom_fields_instance['search_ctype'][get_the_ID()]['max_option_values']))?$search_custom_fields_instance['search_ctype'][get_the_ID()]['max_option_values']:'';
						$range_min=(isset($search_custom_fields_instance['search_ctype'][get_the_ID()]['range_min']))?$search_custom_fields_instance['search_ctype'][get_the_ID()]['range_min']:'';
						$range_max=(isset($search_custom_fields_instance['search_ctype'][get_the_ID()]['range_max']))?$search_custom_fields_instance['search_ctype'][get_the_ID()]['range_max']:'';
						
						/* value not set */
						if($min_option_title=='' && $min_option_values=='' && $max_option_title=='' && $max_option_values==''){							  
							$min_option_title=get_post_meta(get_the_ID(),'search_min_option_title',true);
							$min_option_values=get_post_meta(get_the_ID(),'search_min_option_values',true);							
							$max_option_title=get_post_meta(get_the_ID(),'search_max_option_title',true);
							$max_option_values=get_post_meta(get_the_ID(),'search_max_option_values',true);
						}
						
						if($range_min=='' && $range_max==''){							  
							$range_min=get_post_meta(get_the_ID(),'range_min',true);
							$range_max=get_post_meta(get_the_ID(),'range_max',true);
						}
						
						
							echo '<p><label>'.__('Show on search as',ADMINDOMAIN).'</label>';
							echo "<select name='".$search_ctype."[".get_the_ID()."][search_ctype]' class='search_ctype_".get_the_ID()." search_custom_ctype' data-post-id='".get_the_ID()."' onchange='select_search_type_option(this);' data-search-type='".$ctype."'>";
								echo '<option value="" >'. __('Select type on search',ADMINDOMAIN).'</option>';					
								echo '<option '.$text.' value="text" '.(($search_ctype_val=='text') ? 'selected':'').'>'. __('Text',ADMINDOMAIN).'</option>';
								/*echo '<option '.$date.' value="date">'.__('Date Picker',ADMINDOMAIN).'</option>';*/
								echo '<option '.$multicheckbox.' value="multicheckbox" '.(($search_ctype_val=='multicheckbox')? 'selected':'').'>'.__('Multi Checkbox',ADMINDOMAIN).'</option>';
								echo '<option '.$radio.' value="radio"  '.(($search_ctype_val=='radio')? 'selected':'').'>'.__('Radio',ADMINDOMAIN).'</option>';
								echo '<option '.$select.' value="select" '.(($search_ctype_val=='select')? 'selected':'').'>'.__('Select',ADMINDOMAIN).'</option>';
								echo '<option '.$min_max_range.' value="min_max_range" '.(($search_ctype_val=='min_max_range')? 'selected':'').'>'.__('Min-Max Range (Text)',ADMINDOMAIN).'</option>';
								echo '<option '.$min_max_range_select.' value="min_max_range_select" '.(($search_ctype_val=='min_max_range_select')? 'selected':'').'>'.__('Min-Max Range (Select)',ADMINDOMAIN).'</option>';
								echo '<option '.$slider_range.' value="slider_range" '.(($search_ctype_val=='slider_range')? 'selected':'').'>'.__('Range Slider',ADMINDOMAIN).'</option>';
							echo "</select>";
							echo '</p>';
							
							/*select option */
							echo "<div class='search_select_".get_the_ID()." search_select' ".(($ctype=='text' && $search_ctype_val=='select')? "style='display:block'":"style='display:none'").">";
							echo '<p><label>'.__('Option Title',ADMINDOMAIN).'</label><input type="text" size="41" value="'.$option_title.'" id="search_option_title" name="'.$search_ctype.'['.get_the_ID().'][option_title]"><span class="clearfix description">'.__('Separate multiple option titles with a comma. eg. Yes,No',ADMINDOMAIN).'</span></p>';
							echo '<p><label>'.__('Option values',ADMINDOMAIN).'</label><input type="text" size="41" value="'.$option_values.'" id="search_option_values" name="'.$search_ctype.'['.get_the_ID().'][option_values]"><span class="clearfix description">'.__('Separate multiple option titles with a comma. eg. Yes,No',ADMINDOMAIN).'</span></p>';							
							echo "</div>";
							/*End Search title */
							
							/* Range field related option */
							echo "<div class='range_type_select_".get_the_ID()." range_type_select' ".(($search_ctype_val=='min_max_range_select')? "style='display:block'":"style='display:none'").">";
							echo '<p><label>'.__('Min Option Title',ADMINDOMAIN).'</label><input type="text" size="41" value="'.$min_option_title.'" id="search_min_option_title" name="'.$search_ctype.'['.get_the_ID().'][min_option_title]"><span class="clearfix description">'.__('Separate multiple option titles with a comma. eg. Yes,No',ADMINDOMAIN).'</span></p>';
							echo '<p><label>'.__('Min Option values',ADMINDOMAIN).'</label><input type="text" size="41" value="'.$min_option_values.'" id="search_min_option_values" name="'.$search_ctype.'['.get_the_ID().'][min_option_values]"><span class="clearfix description">'.__('Separate multiple option titles with a comma. eg. Yes,No',ADMINDOMAIN).'</span></p>';
							echo '<p><label>'.__('Max Option Title',ADMINDOMAIN).'</label><input type="text" size="41" value="'.$max_option_title.'" id="search_max_option_title" name="'.$search_ctype.'['.get_the_ID().'][max_option_title]"><span class="clearfix description">'.__('Separate multiple option titles with a comma. eg. Yes,No',ADMINDOMAIN).'</span></p>';
							echo '<p><label>'.__('Max Option values',ADMINDOMAIN).'</label><input type="text" size="41" value="'.$max_option_values.'" id="search_mzx_option_values" name="'.$search_ctype.'['.get_the_ID().'][max_option_values]"><span class="clearfix description">'.__('Separate multiple option titles with a comma. eg. Yes,No',ADMINDOMAIN).'</span></p>';
							echo "</div>";
							
							/* Range slider related option */
							echo "<div class='range_type_slider_".get_the_ID()." range_type_slider' ".(($search_ctype_val=='slider_range')? "style='display:block'":"style='display:none'").">";
							echo '<p><label>'.__('Define your range',ADMINDOMAIN).'</label>';
							echo '<fieldset>
							<input type="text" placeholder="Min value" value="'.$range_min.'" name="'.$search_ctype.'['.get_the_ID().'][range_min]" id="range_min_value">
							<input type="text" placeholder="max value" value="'.$range_max.'" name="'.$search_ctype.'['.get_the_ID().'][range_max]" id="range_max_value">
							</fieldset>';
							echo '</p>';						
							echo "</div>";
					}
					
					echo '<div class="widget-control-actions">';
					echo '<div class="alignleft">';
					echo '<a class="widget-control-remove" href="#remove">'.__('Delete',ADMINDOMAIN).'</a> | <a class="widget-control-close" href="#close">'.__('Close',ADMINDOMAIN).'</a>';
					echo '</div><br class="clear" />';
					echo '</div>';/*Finish widget control actionsdiv*/
					echo '</div>';/*Finish widget-inside div*/
					
				echo '</div>';/*Finish main widget div*/
				echo "</li>";
			}
		endwhile;
		echo "</ul></dd>";
	}
}


/* wp_ajax hook call for get the post type wise custom fields display */
add_action('wp_ajax_tmpl_advance_search_custom_fields','tmpl_advance_searchcustomfields');
function tmpl_advance_searchcustomfields(){
	
	echo tmpl_advance_search_custom_fields($_REQUEST['post_types'],$_REQUEST['search_custom_fields'],$_REQUEST['search_ctype'],$_REQUEST['search_custom_fields_id'],$_REQUEST['custom_fields_list_id']);
	
	exit;
}

/* add advance search widget script for display post type wise custom fields and hide show search type option */
function tmpl_advance_searchable_scripts(){
	?>
    <script  type="text/javascript" async >
	function tmpl_search_post_type(str,id,search_custom_fields,search_ctype,search_custom_fields_id,custom_fields_list_id){
		
		if(confirm("<?php echo __('Changing postype will reset search fields below !',ADMINDOMAIN);?>"))
		{
			var post_types=str.value;
			jQuery.ajax({
					url:ajaxUrl,
					type:'POST',
					data:'action=tmpl_advance_search_custom_fields&post_types=' + post_types+'&search_custom_fields='+search_custom_fields+'&search_ctype='+search_ctype+'&search_custom_fields_id='+search_custom_fields_id+'&custom_fields_list_id='+custom_fields_list_id,
					success:function(results) {
						jQuery('#'+id).html('');
						jQuery('#'+id).html(results);
						jQuery('#'+search_custom_fields_id).html('');
						jQuery( '#'+id+' dd ul li div.widget' ).each(function() {
							if(jQuery(this).attr('data-show')==1){
								var cvalue=jQuery(this).attr('data-search-id');
								var custom_field_div = custom_fields_list_id;
								var custom_field_class = jQuery('#'+custom_fields_list_id).attr('data-class');
								var data_search_order = jQuery('#'+custom_fields_list_id).attr('data-order');
								
								var html_custom_search='';
								html_custom_search+='<div class="widget custom_'+custom_field_class+'_'+cvalue+'" id="search_custom_field_'+cvalue+'" data-sort="'+cvalue+'">';
								html_custom_search+='<input type="hidden" name="'+data_search_order+'[]" value="'+cvalue+'" >';
								html_custom_search+=jQuery('#'+id+' dd ul li div#search_custom_field_'+cvalue).html();
								html_custom_search+='</div>';
								jQuery('.'+custom_field_class).append(html_custom_search);								
								jQuery('.'+custom_field_class+' div.widget div.widget-inside').hide();
							}
							
						});
						
					}
				});
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function select_search_type_option(str){
		var select_value=str.value;
		var data_post_id=jQuery( "select[name='"+str.name+"']" ).attr('data-post-id');
		
		var data_search_type=jQuery( "select[name='"+str.name+"']" ).attr('data-search-type');
		
		
		if(select_value=='min_max_range'){
			jQuery('.range_type_select_'+data_post_id).hide();
			jQuery('.range_type_slider_'+data_post_id).hide();
			jQuery('.search_select_'+data_post_id).hide();
		}else if(select_value=='min_max_range_select'){
			jQuery('.range_type_select_'+data_post_id).show();
			jQuery('.range_type_slider_'+data_post_id).hide();
			jQuery('.search_select_'+data_post_id).hide();
		}else if(select_value=='slider_range'){
			jQuery('.range_type_select_'+data_post_id).hide();
			jQuery('.range_type_slider_'+data_post_id).show();
			jQuery('.search_select_'+data_post_id).hide();
		}else if(select_value=='select' && data_search_type=='text'){			
			jQuery('.search_select_'+data_post_id).show();			
		}else{
			jQuery('.range_type_select_'+data_post_id).hide();
			jQuery('.range_type_slider_'+data_post_id).hide();
			jQuery('.search_select_'+data_post_id).hide();			
		}
	}
	</script>
    <?php
}


/*
 *  display search widget custom fields as per selected custom field on advance search widget
 */
$show_label = apply_filters('tmpl_show_searchfields_label',1);
function display_search_widget_custom_post_fields($value,$instance, $show_label){
	$htmlvar_name=get_post_meta($value,'htmlvar_name',true);
	
	if($instance['search_ctype'][$value]['option_values']!='' && $instance['search_ctype'][$value]['option_title']!='' ){		
		$option_values=explode(',',$instance['search_ctype'][$value]['option_values']);
		$option_title=explode(',',$instance['search_ctype'][$value]['option_title']);
	}else{	
		$option_values=explode(',',get_post_meta($value,'option_values',true));
		$option_title=explode(',',get_post_meta($value,'option_title',true));
	}
	$site_title=$label=get_the_title($value);
	
	if(function_exists('icl_object_id')){
		$icl_post_id=icl_object_id($value, 'post_custom_fields', false);
		$site_title=$label=get_the_title($icl_post_id);
	}
	
	$type = ($instance['search_ctype'][$value]['search_ctype']!='') ? $instance['search_ctype'][$value]['search_ctype'] : get_post_meta($value,'ctype',true);
	
	$post_type=$instance['post_type'];	
	
	$val['range_min']=(@$instance['search_ctype'][$value]['range_min'])?$instance['search_ctype'][$value]['range_min'] :'';
	$val['range_max']=(@$instance['search_ctype'][$value]['range_max'])? $instance['search_ctype'][$value]['range_max'] :'';	
	
	$style_class = get_post_meta($value,'style_class',true);
	$extra_parameter = get_post_meta($value,'extra_parameter',true);
	$default=get_post_meta($value,"default_value",true);
	$radio_type = '';
	if($type == 'radio')
		$radio_type = '_radio';
	?>
    <input type="hidden" name="search_custom[<?php echo $htmlvar_name.$radio_type;?>]" value="<?php echo $type;?>"  />
    <?php	
	if($type=='post_categories'){		
		
		/*fetch the categories of selected post type */
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));
		$args = array(
					'show_option_all'    => __('Select Categories',DOMAIN),
					'show_option_none'   => '',
					'orderby'            => 'name', 
					'order'              => 'ASC',
					'show_count'         => 0,
					'hide_empty'         => 1, 
					'child_of'           => 0,
					'echo'               => 1,
					'selected'           => 0,
					'hierarchical'       => 1, 
					'name'               => 'category',
					'tab_index'          => 0,
					'taxonomy'           => $taxonomies[0],
					'hide_if_empty'      => false,
				);
		
		echo '<div class="form_row clearfix">';
		wp_dropdown_categories($args);
		echo '</div>';
		
	}elseif($type=='text' || $type=='geo_map' || $type=='texteditor' || $type=='textarea'){
		
		echo '<div class="form_row clearfix '.((isset($instance['search_ctype'][$value]['miles_search']) && $instance['search_ctype'][$value]['miles_search']==1)? 'address_search' :'').'">';
		echo '<input type="text" name="'.$htmlvar_name.'" value="" placeholder="'.$label.'" class="textfield '.$style_class.'" '.$extra_parameter.' />';
		
		/* Display within radius field on geo_map custom field type */
		if($type=='geo_map' && $instance['search_ctype'][$value]['miles_search']==1){
			$radius_measure=$instance['search_ctype'][$value]['radius_measure'];
			$radius_type=($radius_measure=='miles')? __('Miles',DOMAIN) : __('Kilometers',DOMAIN);
			?>
            <select id="radius" name="radius">
                <option value=''><?php _e('Within?',DOMAIN); ?></option>
                <option value="1" <?php if(isset($distance_factor) && esc_attr($distance_factor)=='1'){ echo 'selected="selected"';} ?>>1 <?php echo ($radius_measure=='miles')? __('Mile',DOMAIN) : __('Kilometer',DOMAIN);; ?></option>
                <option value="5" <?php if(isset($distance_factor) && esc_attr($distance_factor)=='5'){ echo 'selected="selected"';} ?>>5 <?php echo $radius_type; ?></option>
                <option value="10" <?php if(isset($distance_factor) && esc_attr($distance_factor)=='10'){ echo 'selected="selected"';} ?>>10 <?php echo $radius_type; ?></option>
                <option value="100" <?php if(isset($distance_factor) && esc_attr($distance_factor)=='100'){ echo 'selected="selected"';} ?>>100 <?php echo $radius_type; ?></option>
                <option value="1000" <?php if(isset($distance_factor) && esc_attr($distance_factor)=='1000'){ echo 'selected="selected"';} ?>>1000 <?php echo $radius_type; ?></option>
                <option value="5000" <?php if(isset($distance_factor) && esc_attr($distance_factor)=='5000'){ echo 'selected="selected"';} ?>> 5000 <?php echo $radius_type; ?></option>      
            </select>
            <input type="hidden" name="radius_type" value="<?php echo $radius_measure?>" />
            <?php
		}/*Finish geo_map custom field miles search option*/
		
		echo '</div>';
	}elseif($type=='date'){
		?>
        <script  type="text/javascript" async >
			jQuery(function(){
				var pickerOpts = {						
					showOn: "both",
					dateFormat: 'yy-mm-dd',							
					buttonText: '<i class="fa fa-calendar"></i>',
					monthNames: objectL11tmpl.monthNames,
					monthNamesShort: objectL11tmpl.monthNamesShort,
					dayNames: objectL11tmpl.dayNames,
					dayNamesShort: objectL11tmpl.dayNamesShort,
					dayNamesMin: objectL11tmpl.dayNamesMin,
					isRTL: objectL11tmpl.isRTL,
				};	
				jQuery("#tmpl_search_<?php echo $htmlvar_name;?>").datepicker(pickerOpts);
			});
		</script>
        <div class="form_row clearfix">            
            <input type="text" name="<?php echo $htmlvar_name;?>" id="tmpl_search_<?php echo $htmlvar_name;?>"  placeholder="<?php echo $label;?>" class="textfield <?php echo $style_class;?>" value="" size="25" />
        </div>	
        <?php
	}elseif($type=='radio'){
		
		if(empty($option_title) || $option_title==''){
			 $option_titles = $option_values;
		 }
		?>
        <div class="form_row clearfix">
        	<label class="r_lbl"><?php echo $label; ?></label>
            <?php if(!empty($option_values) && is_array($option_values)){
					$chkcounter = 0;
					echo '<div class="form_cat_left">';
					echo '<ul class="hr_input_radio">';				
					for($i=0;$i<count($option_values);$i++)
					{
						if($option_values[$i]=='') continue;
						$chkcounter++;
						$seled='';
						if($default_value == $option_values[$i]){ $seled='checked="checked"';}
						if (isset($value) && trim($value) == trim($option_values[$i])){ $seled='checked="checked"';}
						echo '<li>
							
								<input name="'.$htmlvar_name.'_radio"  id="'.$htmlvar_name.'_'.$chkcounter.'" type="radio" value="'.trim($option_values[$i]).'" '.$seled.'  '.$extra_parameter.' /> <label class="r_lbl" for="'.$htmlvar_name.'_'.$chkcounter.'">'.$option_title[$i].'</label>
						</li>';
					}
					echo '</ul></div>';
					
				}?>
        </div>
        <?php
	}elseif($type=='multicheckbox'){				
		
		if(empty($option_title) || $option_title==''){
			 $option_titles = $option_values;
		 }
		?>
        <div class="form_row clearfix">
        	<label class="r_lbl"><?php echo $label; ?></label>
            <?php if(!empty($option_values) && is_array($option_values)){
					$chkcounter = 0;
					echo '<div class="form_cat_left hr_input_multicheckbox">';					
					for($i=0;$i<count($option_values);$i++)
					{
						if($option_values[$i]=='') continue;
						$chkcounter++;
						$seled='';
						if($default_value == $option_values[$i]){ $seled='checked="checked"';}
						if (isset($value) && trim($value) == trim($option_values[$i])){ $seled='checked="checked"';}
						echo '<div class="form_cat">
							
								<input name="'.$htmlvar_name.'[]"  id="'.$htmlvar_name.'_'.$chkcounter.'" type="checkbox" value="'.trim($option_values[$i]).'" '.$seled.'  '.$extra_parameter.' /> <label class="r_lbl" for="'.$htmlvar_name.'_'.$chkcounter.'">'.$option_title[$i].'</label>
						</div>';
					}
					echo '</div>';
					
			}?>
        </div>
        <?php
		
	}elseif($type=='select'){
		if(empty($option_title) || $option_title==''){
			 $option_titles = $option_values;
		 }
		?>
        <div class="form_row clearfix">
        	<?php if($show_label = 1){ ?>
				<label class="r_lbl"><?php echo $label; ?></label>
            <?php 
			}
			if(!empty($option_values) && is_array($option_values)){
				$chkcounter = 0;
				echo '<div class="select">';
				echo '<select name="'.$htmlvar_name.'" id="'.$htmlvar_name.'" class="textfield textfield_x '.$style_class.' select" '.$extra_parameter.'>';
				echo '<option value="">'.sprintf(__('Please Select %s',DOMAIN),$label).'</option>';
				for($i=0;$i<count($option_values);$i++)
				{
					if($option_values[$i]=='') continue;
					$chkcounter++;
					$seled='';
					if($default_value == $option_values[$i]){ $seled='checked="checked"';}
					if (isset($value) && trim($value) == trim($option_values[$i])){ $seled='checked="checked"';}
					
					echo '<option value="'.trim($option_values[$i]).'" >'.$option_title[$i].'</option>';						
				}
				echo '</select></div>';
					
			}?>
        </div>
        <?php
	}elseif($type=='min_max_range' ){
		?>
        <div class="form_row clearfix">
            <div class="half_row clearfix">      
            	<input type="text" name="<?php echo $htmlvar_name.'_min';?>" id="<?php echo $htmlvar_name;?>_min" value="" placeholder="<?php if($show_label ==0){  echo $site_title.' ';	_e('Min value',DOMAIN); } ?>" class="min_range" onfocus="if (this.placeholder == '<?php  echo $site_title.' ';	_e('Min value',DOMAIN); ?>') {this.placeholder = '';}" onblur="if (this.placeholder == '') {this.placeholder = '<?php  echo $site_title.' ';	_e('Min value',DOMAIN); ?>';}"/>
            </div>
            <div class="half_row clearfix">      
            	<input type="text" name="<?php echo $htmlvar_name.'_max';?>" id="<?php echo $htmlvar_name;?>_max" value="" placeholder="<?php if($show_label ==0){  echo $site_title.' ';	_e('Max value',DOMAIN); } ?>" class="max_range" onfocus="if (this.placeholder == '<?php echo $site_title.' ';	_e('Max value',DOMAIN); ?>') {this.placeholder = '';}" onblur="if (this.placeholder == '') {this.placeholder = '<?php echo $site_title.' ';	_e('Max value',DOMAIN); ?>';}"/>
            </div>
        </div>
        <?php	
	}elseif($type=='slider_range' && $instance['search_ctype'][$value]['range_min']!='' && $instance['search_ctype'][$value]['range_max']!=''){
		$min_range=$val['range_min'];
		$max_range=$val['range_max'];
		
		wp_enqueue_script("jquery-ui-slider");	
		?>
        <div class="form_row clearfix">		  
            <label class="r_lbl"><?php echo $site_title; ?></label>

            <input type="text" name="<?php echo $htmlvar_name;?>" id="<?php echo $htmlvar_name;?>_range" value="<?php echo $max_range; ?>" style="border:0; font-weight:bold;"  readonly="readonly"/>
            </label>
            <div id="<?php echo $htmlvar_name;?>_range_type" class="clearfix" style="width:95%;"></div>
            <script  type="text/javascript" async >
            jQuery(function(){jQuery("#<?php echo $htmlvar_name?>_range_type").slider({range:true,min:<?php echo $min_range;?>,max:<?php echo $max_range; ?>,values:[<?php echo $min_range;?>,<?php echo $max_range; ?>],slide:function(e,t){jQuery("#<?php echo $htmlvar_name;?>_range").val(t.values[0]+" - "+t.values[1])}});jQuery("#<?php echo $htmlvar_name;?>_range").val(jQuery("#<?php echo $htmlvar_name?>_range_type").slider("values",0)+" - "+jQuery("#<?php echo $htmlvar_name?>_range_type").slider("values",1))})
            </script>
		</div>
		<?php
	}elseif($type=='min_max_range_select' && $instance['search_ctype'][$value]['min_option_title']!='' && $instance['search_ctype'][$value]['min_option_values']!='' && $instance['search_ctype'][$value]['max_option_title']!='' && $instance['search_ctype'][$value]['max_option_values']!='' ){
		$title_min_range=explode(',',$instance['search_ctype'][$value]['min_option_title']);
		$value_min_range=explode(',',$instance['search_ctype'][$value]['min_option_values']);
		$title_max_range=explode(',',$instance['search_ctype'][$value]['max_option_title']);
		$value_max_range=explode(',',$instance['search_ctype'][$value]['max_option_values']);
		?>
		<div class="form_row clearfix">
            <div class="selectbox">
            <select name="<?php echo $htmlvar_name;?>_min" id="<?php echo $htmlvar_name;?>_min" class="textfield textfield_x <?php echo $style_class;?> select" <?php echo $extra_parameter;?>>
                <option value=""><?php echo sprintf(__('Please Select %s Min value',DOMAIN),$site_title);?></option>
                <?php if(!empty($value_min_range)){
                for($i=0;$i<count($value_min_range);$i++){?>
                    <option value="<?php echo $value_min_range[$i]; ?>" <?php if($value==$value_min_range[$i]){ echo 'selected="selected"';} else if($default_value==$value_min_range[$i]){ echo 'selected="selected"';}?>><?php echo ($title_min_range[$i])? $title_min_range[$i]:$value_min_range[$i]; ?></option>
                    <?php	
                    }
                }?>                
            </select>
            </div>
		</div>
            
        <div class="form_row clearfix">
			<div class="selectbox">
                <select name="<?php echo $htmlvar_name;?>_max" id="<?php echo $htmlvar_name;?>_max" class="textfield textfield_x <?php echo $style_class;?> select" <?php echo $extra_parameter;?>>
                <option value=""><?php echo sprintf(__('Please Select %s Max value',DOMAIN),$site_title);?></option>
                <?php if(!empty($value_max_range)){
                for($i=0;$i<count($value_max_range);$i++){?>
                    <option value="<?php echo $value_max_range[$i]; ?>" <?php if($value==$value_max_range[$i]){ echo 'selected="selected"';} else if($default_value==$value_max_range[$i]){ echo 'selected="selected"';}?>><?php echo ($title_max_range[$i])? $title_max_range[$i]:$value_max_range[$i]; ?></option>
                    <?php	
                    }
                }?>                
            </select>
            </div>
		</div>
		<?php
		
	}else{
		
		$val['name']=$htmlvar_name;
		$val['label']= $label;
		$val['type']=$type;
		$val['htmlvar_name']=$htmlvar_name;	
		$val['option_values']=$option_values;
		$val['option_title']=$option_title;
		$val['default']=$default;
		$val['style_class']=$style_class;
		$val['extra_parameter']=$extra_parameter;
		
		/* Add  advancesearch_custom_fieldtype hook for add additinal search html  on frontend*/
		do_action('advancesearch_custom_fieldtype',$htmlvar_name,$val,$post_type);	
	}
	
}

function templ_advance_search_custom_fields_where_filter($join)
{
	global $wpdb, $pagenow, $wp_taxonomies,$ljoin,$sitepress;
	$language_where='';
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		$language = $sitepress->get_default_language();
		$join .= " {$ljoin} JOIN {$wpdb->prefix}icl_translations t ON {$wpdb->posts}.ID = t.element_id			
			AND t.element_type IN ('post_custom_fields') JOIN {$wpdb->prefix}icl_languages l ON t.language_code=l.code AND l.active=1 AND t.language_code='".$language."'";
	}	
	return $join;
}
?>