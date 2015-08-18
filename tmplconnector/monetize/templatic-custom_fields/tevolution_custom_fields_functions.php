<?php
/*
 * custom function related to front end.
 */
if(!is_admin() && !strstr($_SERVER['REQUEST_URI'],'/wp-admin/' ) || isset($_REQUEST['front']) && $_REQUEST['front']==1){
	add_filter('tiny_mce_plugins','tmpl_tiny_mce_plugins');
	add_filter('mce_buttons','tmpl_mce_buttons');
	add_filter('mce_buttons_2','tmpl_mce_buttons_2');
}
/*
 * This function user for get the tevolution custom fields on wpml language wise post join filter
 */
function custom_field_posts_where_filter($join)
{
	global $wpdb, $pagenow, $wp_taxonomies,$ljoin;
	$language_where='';
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		$language = ICL_LANGUAGE_CODE;
		$join .= " {$ljoin} JOIN {$wpdb->prefix}icl_translations t ON {$wpdb->posts}.ID = t.element_id			
			AND t.element_type IN ('post_custom_fields') JOIN {$wpdb->prefix}icl_languages l ON t.language_code=l.code AND l.active=1 AND t.language_code='".$language."'";
	}	
	return $join;
}


/* 
 * This function user for get the post type wise custom filed from  frontend submit post type
 * only get the show on submit form enable custom fields.
 */
function get_post_custom_fields_templ_plugin($post_types,$category_id='',$taxonomy='',$heading_type='',$remove_post_id='',$pakg_id ='') {	
	global $wpdb,$post,$_wp_additional_image_sizes,$sitepress;
	$templatic_settings = get_option('templatic_settings');
	if(@$_REQUEST['page'] != 'paynow'  && @$_REQUEST['page'] != 'transcation' && $category_id!=''  )
	{
		$category_id = explode(",",$category_id);
	}	
	remove_all_actions('posts_where');
	$remove_post_id_array = explode(",",$remove_post_id);
	
	/* Get the custom fields on heading type wise */
	if($heading_type){
		$args=array('post_type'      => 'custom_fields',
					'posts_per_page' => -1	,
					'post_status'    => array('publish'),
					'post__not_in'   => $remove_post_id_array,
					'meta_query'     => array('relation' => 'AND',
											array('key' => 'post_type_'.$post_types, 'value' => array('all',$post_types),'compare' => 'IN','type'=> 'text'),											
											array('key' => 'show_on_page','value' =>  array('user_side','both_side'),'compare' => 'IN','type'=> 'text'),											
											array('key' => $post_types.'_heading_type','value' =>  array('basic_inf',htmlspecialchars_decode($heading_type)),'compare' => 'IN'),
											array('key' => 'is_submit_field', 'value' =>  '1','compare' => '='),
										),
					'meta_key'       => $post_types.'_sort_order',
					'orderby'        => 'meta_value_num',
					'meta_value_num' => $post_types.'_sort_order',
					'order'          => 'ASC'
				);		
		if((isset($_REQUEST['pid']) && $_REQUEST['pid']!='' && isset($_REQUEST['action']) && $_REQUEST['action']=='edit') || (isset($_REQUEST['action_edit']) && $_REQUEST['action_edit']=='edit')){
			/* Unset is submit field  on edit listing page for display all custom fields post type wise*/			
			unset($args['meta_query'][3]);	
		}
		
	}else{ /*Get the custom fields without heading type wise */
		$args=array('post_type'      => 'custom_fields',
					'posts_per_page' => -1	,
					'post_status'    => array('publish'),
					'post__not_in'   => $remove_post_id_array,
					'meta_query'     => array('relation' => 'AND',
											array('key' => 'post_type_'.$post_types.'','value' => array('all',$post_types),'compare' => 'In','type'=> 'text'),
											array('key' => 'show_on_page','value' =>  array('user_side','both_side'),'compare' => 'IN','type'=> 'text'),
											array('key' => 'is_submit_field', 'value' =>  '1','compare' => '='),
									    ),
					'meta_key'       => $post_types.'_sort_order',
					'orderby'        => 'meta_value_num',
					'meta_value_num' => $post_types.'_sort_order',
					'order'          => 'ASC'
					);
		
		if((isset($_REQUEST['pid']) && $_REQUEST['pid']!='' && isset($_REQUEST['action']) && $_REQUEST['action']=='edit') || (isset($_REQUEST['action_edit']) && $_REQUEST['action_edit']=='edit')){
			/* Unset is submit field  on edit listing page for display all custom fields post type wise*/
			unset($args['meta_query'][2]);	
		}		
	}	
	
	/* Get the custom fields category wise if category id not equal to blank */

	if($category_id!=''){
		$args['tax_query']	= array('relation' => 'OR',
									array('taxonomy' => $taxonomy,'field' => 'id','terms' => $category_id,'operator'  => 'IN','include_children' => false),
									array('taxonomy' => 'category','field' => 'id','terms' => 1,'operator'  => 'IN','include_children' => false)
								 );
	}	
	$post_query = null;
	remove_all_actions('posts_orderby');	
	
	/* add posts)join filter for get the custom fields on wpml language wise */
	add_filter('posts_join', 'custom_field_posts_where_filter');	
	$post_meta_info = new WP_Query($args);
	$return_arr = array();	
	if($post_meta_info){
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
			$is_active=get_post_meta($post->ID,"is_active",true);
			$ctype=get_post_meta($post->ID,"ctype",true);

			/*Custom fields loop returns if is active not equal to one or ctype equal to heading type */
			if(is_plugin_active('Tevolution-FieldsMonetization/fields_monetization.php') && (isset($_REQUEST['pakg_id']) && $_REQUEST['pakg_id']!='' || $pakg_id!='')){
				if($pakg_id!= '')
				{
					$package_select=$pakg_id;
				}
				else
				{
					$package_select=$_REQUEST['pakg_id'];
				}
				$field_monetiz_custom_fields=get_post_meta($package_select,'custom_fields',true);
				if(!empty($field_monetiz_custom_fields) && !in_array($post->ID, $field_monetiz_custom_fields)){
					continue;
				}
			}
			
			if($is_active!=1 || $ctype=='heading_type'){
				continue;
			}
		
			if(get_post_meta($post->ID,"ctype",true)){
				$options = explode(',',get_post_meta($post->ID,"option_values",true));
			}
			$field_category=get_post_meta($post->ID,"field_category",true);
			$custom_fields = array(
					"id"		          => $post->ID,
					"name"		          => get_post_meta($post->ID,"htmlvar_name",true),
					"label" 	          => $post->post_title,
					"htmlvar_name" 	      => get_post_meta($post->ID,"htmlvar_name",true),
					"default" 	          => get_post_meta($post->ID,"default_value",true),
					"ctype" 	 	          => get_post_meta($post->ID,"ctype",true),
					"desc"                => $post->post_content,
					"option_title"        => get_post_meta($post->ID,"option_title",true),
					"option_values"       => get_post_meta($post->ID,"option_values",true),
					"is_require"          => get_post_meta($post->ID,"is_require",true),
					"is_active"           => get_post_meta($post->ID,"is_active",true),
					"show_on_listing"     => get_post_meta($post->ID,"show_on_listing",true),
					"show_on_detail"      => get_post_meta($post->ID,"show_on_detail",true),
					"validation_type"     => get_post_meta($post->ID,"validation_type",true),
					"field_require_desc"  => get_post_meta($post->ID,"field_require_desc",true),
					"style_class"         => get_post_meta($post->ID,"style_class",true),
					"extra_parameter"     => get_post_meta($post->ID,"extra_parameter",true),
					"show_in_email"       => get_post_meta($post->ID,"show_in_email",true),
					"range_min"           => get_post_meta($post->ID,"range_min",true),
					"range_max"           => get_post_meta($post->ID,"range_max",true),
					"search_ctype"        => get_post_meta($post->ID,"search_ctype",true),
					"field_category"      => ($field_category)?$field_category: 'all',
					);
			if($options)
			{
				$custom_fields["options"]=$options;
			}
			$return_arr[get_post_meta($post->ID,"htmlvar_name",true)] = $custom_fields;
		endwhile;wp_reset_query();
	}
	/*remove posts_join wpml language filter */
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	
	return $return_arr;
	
}

/*
	Difference between two date date must be in Y-m-d format
*/
function templ_number_of_days($date1, $date2,$adays =30) {
	$date1Array = explode('-', $date1);
	$date1Epoch = mktime(0, 0, 0, $date1Array[1],
	$date1Array[2], $date1Array[0]);
	$date2Array = explode('-', $date2);
	$date2Epoch = mktime(0, 0, 0, $date2Array[1],
	$date2Array[2], $date2Array[0]);
	
	if(date('Y-m-d',$date1Epoch) == date('Y-m-d',$date2Epoch)){
		$date_diff = $date2Epoch - $date1Epoch;
		return round($date_diff / 60 / 60 / 24);
	}else{
		$date_diff = $date2Epoch - $date1Epoch;
		return round($date_diff / 60 / 60 / 24);
	}
	
}
/* this function will remove the space and convert upper case latter to lower case */
function tmplCompFld($str){
	$str = strtolower(trim($str));
	return $str;
}

/* 
 * Returns all custom fields html
 */

function display_custom_post_field_plugin($custom_metaboxes,$session_variable,$post_type,$pkg_id='',$submit_page_id=''){
	global $wpdb;	
	
	foreach($custom_metaboxes as $heading=>$_custom_metaboxes)
	{
		$i = 0;
		$activ = fetch_active_heading($heading);
		/* Display custom fields heading  fields wise */
		if($activ):
			$PostTypeObject = get_post_type_object($post_type);
			$_PostTypeName = $PostTypeObject->labels->name;
			if(function_exists('icl_register_string')){
				icl_register_string(DOMAIN,$_PostTypeName.'submit',$_PostTypeName);
				$_PostTypeName =icl_t(DOMAIN,$_PostTypeName.'submit',$_PostTypeName);
			}			
			$_PostTypeName = $_PostTypeName . ' ' . __('Information',DOMAIN);
			if($heading == '[#taxonomy_name#]' && $_custom_metaboxes)
			{
				$heading='';			
				$heading_desc='';
            }
			else
			{
				if($_custom_metaboxes){
					if(function_exists('icl_register_string')){
						icl_register_string(DOMAIN,$heading,$heading);
					}
					if(function_exists('icl_t')){
						$heading = icl_t(DOMAIN,$heading,$heading);
					}else{
						$heading = sprintf(__("%s",DOMAIN),$heading);
					}					
				}
				$heading_desc=$_custom_metaboxes['basic_inf']['desc'];
			}
			if($_custom_metaboxes && $i == 0 ){
				echo '<div class="sec_title">';
				
					if(tmplCompFld($heading) != tmplCompFld('Label of Field'))
						echo '<h3>'.$heading.'</h3>';
					echo ($heading_desc!='')? '<p>'.$heading_desc.'</p>' : '';
				echo '</div>';
				$i++;
			}
		endif;	
		/* Finish custom field heading display section */
		
		foreach($_custom_metaboxes as $key=>$val) {
			$name = $val['name'];
			$site_title = $val['label'];
			$type = $val['ctype'];
			$htmlvar_name = $val['htmlvar_name'];			
			$field_category=$val['field_category'];
			$allowed_types = "jpeg,jpg,png,gif,doc,zip";
			$allowed_type = apply_filters('tmpl_allowed_types',$allowed_types,$htmlvar_name);
			/*set the post category , post title, post content, post image and post expert replace as per post type*/
			if($htmlvar_name=="category")
			{
				$site_title=str_replace('Post Category',__('Category',DOMAIN),$site_title);
			}
			if($htmlvar_name=="post_title")
			{
				$site_title=str_replace('Post Title',__('Title',DOMAIN),$site_title);
			}
			if($htmlvar_name=="post_content")
			{
				$site_title=str_replace('Post Content',ucfirst($post_type)." ".__('Description',DOMAIN),$site_title);
			}
			if($htmlvar_name=="post_excerpt")
			{
				$site_title=str_replace('Post Excerpt',ucfirst($post_type)." ".__('description in two lines (will be shown on listing pages)',DOMAIN),$site_title);
			}
			if($htmlvar_name=="post_images")
			{
				$site_title=str_replace('Post Images',__('Images',DOMAIN),$site_title);
			}
			/*finish post type wise replace post category, post title, post content, post expert, post images*/
			$admin_desc = $val['desc'];
			$option_values = $val['option_values'];
			$default_value = $val['default'];
			$style_class = $val['style_class'];
			$extra_parameter = $val['extra_parameter'];
			$field_require_desc = $val['field_require_desc'];
			if(!$extra_parameter){ $extra_parameter ='';}
			/* Is required CHECK BOF */
			$is_required = '';
			$input_type = '';
			if(trim($val['validation_type']) != ''){
				if($val['is_require'] == '1'){
				$is_required = '<span class="required">*</span>';
				}
				
				$is_required_msg = '<span id="'.$name.'_error" class="message_error2"></span>';
			} else {
				$is_required = '';
				$is_required_msg = '';
			}
			/* Is required CHECK EOF */
			$value = "";
			if(@$_REQUEST['pid'])
			{
				$post_info = get_post($_REQUEST['pid']);
				if($name == 'post_title') {
					$value = $post_info->post_title;
				}
				elseif($name == 'post_content') {
					$value = $post_info->post_content;
				}
				elseif($name == 'post_excerpt'){
					$value = $post_info->post_excerpt;
				}elseif($name == 'post_tags'){
						$terms = get_the_terms( $_REQUEST['pid'], 'listingtags' );
						if($terms){
									foreach( $terms as $term ){
										$term_names[] = $term->name;
									}

						$value =  implode( ', ', $term_names );
						}else{
									 $value = '';
						}
				}else {
					$value = get_post_meta($_REQUEST['pid'], $name,true);
				}
			}
			
			if(isset($_SESSION[$session_variable]) && !empty($_SESSION[$session_variable]))
			{
				$value = @$_SESSION[$session_variable][$name];
			}elseif(isset($_REQUEST[$name])){
				$value = $_REQUEST[$name];
			}
			$value = apply_filters('SelectBoxSelectedOptions',$value,$name);			

		/* custom fields loop continue when custom field type equal to heading type */
		if($type=='heading_type' || $type=='post_categories'){
			continue;
		}
		do_action('tmpl_custom_fields_'.$name.'_before_wrap');		
		
		$custom_fileds=($type!='post_categories')?'custom_fileds':'';
		?>
		<div class="form_row clearfix <?php echo $custom_fileds.' '.$style_class. ' '.$name;?>">
		   
		<?php
		/* label of custom fields */
		if($type=='text'){
			$labelclass= apply_filters('tmpl_cf_lbl_class_'.$name ,'r_lbl');
		}else{
			$labelclass= apply_filters('tmpl_cf_lbl_class_'.$name ,'');
		}
		
		/*Show label as heading type if the fields heading type is set as "Label of field" */
		if(tmplCompFld($heading) == tmplCompFld('Label of Field')){
			echo '<div class="sec_title">';
					echo '<h3>'.$site_title.$is_required.'</h3>';
			echo '</div>';
		}else{
			$label = "<label class=".$labelclass.">".$site_title.$is_required."</label>";
			if((tmplCompFld($site_title) != tmplCompFld('category')) && (tmplCompFld($site_title) != tmplCompFld('Multi City')))
				echo $label;
		}
			
		   
		/* label of custom fields */
		
		switch ($type) {
			case "text":
				/* input type text - when the fields name is geo latitude and longitude we needs to add extra functions in input field */
				if($name == 'geo_latitude' || $name == 'geo_longitude') {
					$extra_script = apply_filters('tmpl_cf_extra_fields_'.$name,'onblur="changeMap();"');
				} else {
					$extra_script =  apply_filters('tmpl_cf_extra_fields_'.$name,'"');;
				}

				do_action('tmpl_custom_fields_'.$name.'_before'); ?>
				
				<input name="<?php echo $name;?>" id="<?php echo $name;?>" value="<?php if(isset($value) && $value!=''){ echo stripslashes($value); } ?>" type="text" class="textfield <?php echo $style_class;?>" <?php echo $extra_parameter; ?> <?php echo $extra_script;?> placeholder="<?php echo @$val['default']; ?>"/>
				
				<?php echo $is_required_msg;
				if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;
				
				do_action('tmpl_custom_fields_'.$name.'_after'); 
				do_action('tmpl_text_cutom_fields_settings',$custom_metaboxes,$session_variable,$post_type,$pkg_id,$val['name']);
				break;
			case "date":
				/* Script for date picker */
				
				if(function_exists('tmpl_wp_is_mobile') && !tmpl_wp_is_mobile()){
					?>     
					<script type="text/javascript">
						jQuery(function(){
							var pickerOpts = {						
								showOn: "both",
								dateFormat: 'yy-mm-dd',
								/*buttonImage: "<?php echo TEMPL_PLUGIN_URL;?>css/datepicker/images/cal.png",*/
								buttonText: '<i class="fa fa-calendar"></i>',
								buttonImageOnly: false,
								monthNames: objectL11tmpl.monthNames,
								monthNamesShort: objectL11tmpl.monthNamesShort,
								dayNames: objectL11tmpl.dayNames,
								dayNamesShort: objectL11tmpl.dayNamesShort,
								dayNamesMin: objectL11tmpl.dayNamesMin,
								isRTL: objectL11tmpl.isRTL,
								onChangeMonthYear: function(year, month, inst) {
								jQuery("#<?php echo $name;?>").blur();
								},
								onSelect: function(dateText, inst) {
								/*jQuery("#<?php echo $name;?>").focusin();*/
								jQuery("#<?php echo $name;?>").blur();
								}
							};	
							jQuery("#<?php echo $name;?>").datepicker(pickerOpts);
						});
					</script>
					<?php 
					$type="text";
				}else{
					$type ="date";
				}	
				do_action('tmpl_custom_fields_'.$name.'_before'); ?>
				
				<input type="<?php echo $type; ?>" readonly="readonly" name="<?php echo $name;?>" id="<?php echo $name;?>" class="textfield <?php echo $style_class;?>" value="<?php echo esc_attr(stripslashes($value)); ?>" size="25" <?php echo 	$extra_parameter;?> placeholder="<?php echo @$val['default']; ?>"/>
				
				<?php echo $is_required_msg;
				
				if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;
				
				do_action('tmpl_custom_fields_'.$name.'_after'); ?>	          
				<?php
				break;
			case "multicheckbox":
				$options = $val['option_values'];				
				$option_titles = $val['option_title'];	
				if(!is_array($value))		{
					if(strstr($value,','))
					{							
						update_post_meta($_REQUEST['pid'],$htmlvar_name,explode(',',$value));
						$value=get_post_meta($_REQUEST['pid'],$htmlvar_name,true);
					}
				}
				if(!isset($_REQUEST['pid']) && !isset($_REQUEST['backandedit']))
				{
					$default_value = explode(",",$val['default']);
				}
	
				if($options)
				{  
					$chkcounter = 0;
					echo '<div class="form_cat_left hr_input_multicheckbox">';
					do_action('tmpl_custom_fields_'.$name.'_before');
					$option_values_arr = explode(',',$options);
					$option_titles_arr = explode(',',$option_titles);
					for($i=0;$i<count($option_values_arr);$i++)
					{
						$chkcounter++;
						$seled='';
						if(isset($_REQUEST['pid']) || isset($_REQUEST['backandedit']))
						  {
							$default_value = $value;
						  }
						if($default_value !=''){
						if(in_array($option_values_arr[$i],$default_value)){ 
						$seled='checked="checked"';} }	
						$option_titles_arr[$i] = (!empty($option_titles_arr[$i])) ? $option_titles_arr[$i] : $option_values_arr[$i];
						echo '

						<div class="form_cat">
							
								<input name="'.$key.'[]"  id="'.$key.'_'.$chkcounter.'" type="checkbox" value="'.$option_values_arr[$i].'" '.$seled.'  '.$extra_parameter.' /> <label for="'.$key.'_'.$chkcounter.'">'.$option_titles_arr[$i].'
							</label>
						</div>';
					}
					echo '</div>';
					
					echo $is_required_msg;
					
					if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;
					
					do_action('tmpl_custom_fields_'.$name.'_after');
				}
				break;  
			case "texteditor":
				do_action('tmpl_custom_fields_'.$name.'_before');
				$media_buttons = apply_filters('tmpl_media_button_pro',false);
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				
				do_action('tmpl_texteditor_cutom_fields_settings',$custom_metaboxes,$session_variable,$post_type,$pkg_id,$val['name'],$submit_page_id);
				/* Wp editor on submit form */
				$settings =   apply_filters('tmpl_cf_wpeditor_settings',array(
						'wpautop' => false,
						'media_buttons' => $media_buttons,
						'textarea_name' => $name,
						'textarea_rows' => apply_filters('tmpl_wp_editor_rows',get_option('default_post_edit_rows',6)), /* rows="..."*/
						'tabindex' => '',
						'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>',
						'editor_class' => '',
						'toolbar1'=> 'bold,italic,underline,bullist,numlist,link,unlink,forecolor,undo,redo',
						'teeny' => false,
						'dfw' => false,
						'tinymce' => true,
						'quicktags' => false
					));				
				if(isset($value) && $value != '') 
				{  $content=$value; 
				}else{
					$content= $val['default']; 
				} 				
				wp_editor( stripslashes($content), $name, apply_filters('tmpl_wp_editor_settings',$settings,$name));

				echo $is_required_msg;

				if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;
				
				do_action('tmpl_custom_fields_'.$name.'_after');
				do_action('tmpl_text_cutom_fields_settings',$custom_metaboxes,$session_variable,$post_type,$pkg_id,$val['name']);
				break;
		    case "textarea":
				
				do_action('tmpl_custom_fields_'.$name.'_before'); ?>
				
                <textarea name="<?php echo $name;?>" id="<?php echo $name;?>" class="<?php if($style_class != '') { echo $style_class;}?> textarea" <?php echo $extra_parameter;?> placeholder="<?php echo @$val['default']; ?>" rows="3"><?php if(isset($value))echo stripslashes($value);?></textarea>
               	<?php echo $is_required_msg;
				
				if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;
				
				do_action('tmpl_custom_fields_'.$name.'_after'); 
				do_action('tmpl_textarea_cutom_fields_settings',$custom_metaboxes,$session_variable,$post_type,$pkg_id,$val['name']);
				break;
				
		    case "radio":
				
				do_action('tmpl_custom_fields_'.$name.'_before'); 
			
				$options = $val['option_values'];
				$option_title = $val['option_title'];
				if($options)
				{ 
					$chkcounter = 0;
					echo '<div class="form_cat_left">';
					$option_values_arr = explode(',',$options);
					$option_titles_arr = explode(',',$option_title);
			
					if($option_title ==''){  $option_titles_arr = $option_values_arr;  }
					
					echo '<ul class="hr_input_radio">';
					for($i=0;$i<count($option_values_arr);$i++)
					{
						$chkcounter++;
						$seled='';
						
						/* show radio button default selected when it is as cumpalsary field, otherwise all radiobuttons will ne unchecked */
						if($val['is_require'] == 1 && empty($default_value) && empty($value))
						{
							if($i==0 && trim($value)==''){ $seled='checked="checked"';}	
						}
						elseif(trim($value) == $option_values_arr[$i])
						{ $seled='checked="checked"';}
						
						if (isset($val['default']) && trim($val['default']) == trim($option_values_arr[$i])){ $seled='checked="checked"';}
						$event_type = array("Regular event", "Recurring event");
						
						if($key == 'event_type'):
							if (trim(@$value) == trim($event_type[$i])){ $seled="checked=checked";}
							echo '<li>
									<input name="'.$key.'"  id="'.$key.'_'.$chkcounter.'" type="radio" value="'.$event_type[$i].'" '.$seled.'  '.$extra_parameter.' /> <label for="'.$key.'_'.$chkcounter.'">'.$option_titles_arr[$i].'</label>
								</li>';
						else:
							echo '<li><input name="'.$key.'"  id="'.$key.'_'.$chkcounter.'" type="radio" value="'.$option_values_arr[$i].'" '.$seled.'  '.$extra_parameter.' /> <label for="'.$key.'_'.$chkcounter.'">'.$option_titles_arr[$i].'</label>
								</li>';
						endif;
					}
					echo '</ul>';	
					
					echo '</div>';
				}
				
				echo $is_required_msg;
				
				if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;
				
				do_action('tmpl_custom_fields_'.$name.'_after');
				
				break;
		    case "select":
			
				if(@$_REQUEST['pid']) {
					$value = get_post_meta($_REQUEST['pid'], $name,true);
				}
				
				do_action('tmpl_custom_fields_'.$name.'_before'); ?>
                <select name="<?php echo $name;?>" id="<?php echo $name;?>" class="textfield textfield_x <?php echo $style_class;?>" <?php echo $extra_parameter;?>>
					<option value=""><?php _e("Please Select",DOMAIN);?></option>
					<?php if($option_values){
						/*$option_values_arr = explode(',',$option_values);*/
						$option_title = ($val['option_title']) ? $val['option_title'] : $val['option_values'];
						$option_values_arr = apply_filters('SelectBoxOptions',explode(',',$option_values),$name);
						$option_title_arr = apply_filters('SelectBoxTitles',explode(',',$option_title),$name);
						
						for($i=0;$i<count($option_values_arr);$i++)
						{
							$selcted = '';
							echo trim($value) .'=='. trim($option_values_arr[$i]);
							if($val['is_require'] == 1 && empty($default_value) && empty($value))
							{
								if($i==0 && trim($value)==''){ $selcted='selected="selected"';}	
							}
							elseif(trim($value) == trim($option_values_arr[$i]))
							{ $selcted ='selected="selected"';}
						?>
						<option value="<?php echo $option_values_arr[$i]; ?>" <?php echo $selcted; ?>><?php echo $option_title_arr[$i]; ?></option>
						<?php	
						}
					}?>
				</select>
                <?php echo $is_required_msg;
				
				if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;
				
				do_action('tmpl_custom_fields_'.$name.'_after');
				break;
		    case "upload": ?>
				<!-- html for image upload for submit form front end -->
				<div class="upload_box <?php echo apply_filters('tmpl_cf_img_uploder_class',''); ?>">
                 <div class="hide_drag_option_ie">
					<p><?php _e('You can drag &amp; drop images from your computer to this box.',DOMAIN); ?></p>
					<p><?php _e('OR',DOMAIN); ?></p>
                 </div>
					<?php
					
					
					
					echo '<div class="tmpl_single_uploader">';
						do_action('tmpl_custom_fields_'.$name.'_before');
						$wp_upload_dir = wp_upload_dir();?>
						
						<!-- Save the uploaded image path in hidden fields -->
						<input type="hidden" value="<?php echo stripslashes($value); ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="fileupload uploadfilebutton"  placeholder="<?php echo @$val['default']; ?>"/>
						<div id="<?php echo $name; ?>"></div>
						
						<div id="fancy-contact-form">
							<div class="dz-default dz-message" ><span  id="fancy-<?php echo $name; ?>"><span><i class="fa fa-folder"></i>  <?php _e('Upload File',DOMAIN); ?></span></span></div>
                            <?php
							if(@$_REQUEST['pid']==''){
							?>
								<span  id="image-<?php echo $name; ?>"></span>
                            <?php } ?>
							<input type="hidden" name="submitted" value="1">
						</div>
						<script>
							var image_thumb_src = '<?php echo  $wp_upload_dir['url'];?>/';
							jQuery(document).ready(function(){
								var settings = {
									url: '<?php echo plugin_dir_url( __FILE__ ); ?>single-upload.php',
									dragDrop:true,
									fileName: "<?php echo $name; ?>",
									allowedTypes:"<?php echo $allowed_type; ?>",	
									returnType:"json",
									multiple:false,
									showDone:false,
									showAbort:false,
									showProgress:true,
									onSubmit:function(files, xhr)
									{
										/*jQuery('.ajax-file-upload-statusbar').html('');*/
									},
									onSuccess:function(files,data,xhr)
									{
										jQuery('#image-<?php echo $name; ?>').html('');
										if(jQuery('#img_<?php echo $name; ?>').length > 0)
										{
											jQuery('#img_<?php echo $name; ?>').remove();
										}
									    var img = jQuery('<img height="60px" width="60px" id="img_<?php echo $name; ?>">'); /*Equivalent: $(document.createElement('img'))*/
									    data = data+'';
										if(data != 'error'){
											var id_name = data.split('.');
											console.log(id_name);
											if(id_name[1] == 'pdf')
												var img_name = '<?php echo TEVOLUTION_PAGE_TEMPLATES_URL."/images/pdfthumb.png"; ?>';
											else
												var img_name = '<?php echo bloginfo('template_url')."/images/tmp/"; ?>'+id_name[0]+"."+id_name[1];	
											
											img.attr('src', img_name);
											img.appendTo('#image-<?php echo $name; ?>');
										}
										else
										{
											jQuery('#image-<?php echo $name; ?>').html("<?php _e('Image can&rsquo;t be uploaded due to some error.',DOMAIN); ?>");
											jQuery('.ajax-file-upload-statusbar').css('display','none');
											return false;
										}
										jQuery('#image-<?php echo $name; ?>').css('display','');
										jQuery('#<?php echo $name; ?>').val(image_thumb_src+data);
										jQuery('.ajax-file-upload-filename').css('display','none');
										jQuery('.ajax-file-upload-red').css('display','none');
										jQuery('.ajax-file-upload-progress').css('display','none');
									},
									showDelete:true,
									deleteCallback: function(data,pd)
									{
										for(var i=0;i<data.length;i++)
										{
											jQuery.post("<?php echo plugin_dir_url( __FILE__ ); ?>delete_image.php",{op:"delete",name:data[i]},
											function(resp, textStatus, jqXHR)
											{
												/*Show Message  */
												jQuery('#image-<?php echo $name; ?>').html("<div><?php _e('File Deleted',DOMAIN);?></div>");
												jQuery('#<?php echo $name; ?>').val('');
											});
										 }      
										pd.statusbar.hide(); /*You choice to hide/not.*/

									}
								}
								var uploadObj = jQuery("#fancy-"+'<?php echo $name; ?>').uploadFile(settings);
							});
							function single_delete_image(name,field_name)
							{
								jQuery.ajax({
									 url: '<?php echo TEMPL_PLUGIN_URL; ?>tmplconnector/monetize/templatic-custom_fields/delete_image.php?op=delete&name='+name,
									 type: 'POST',
									 success:function(result){
										jQuery('#image-'+field_name).html("<div>File Deleted</div>");
										jQuery('#'+field_name).val('');			
									}				 
								 });
							}
						</script>
						<?php do_action('tmpl_custom_fields_'.$name.'_after');
						
						/* check the format of uploaded file ( is image ??)*/
						if($_REQUEST['pid'] || $_SESSION['custom_fields'][$name] != '' || $_REQUEST[$name] != ''):
							if($_SESSION['custom_fields'][$name] != '')
							{
								$image = $_SESSION['custom_fields'][$name];
							}
							else
							{
								$image = get_post_meta($_REQUEST['pid'],$name, $single = true);
							}
							if(isset($_REQUEST[$name]) && $_REQUEST[$name] != '')
							{
								$image = $_REQUEST[$name];
							}
							$upload_file=strtolower(substr(strrchr($image,'.'),1));
							if($upload_file =='jpg' || $upload_file =='jpeg' || $upload_file =='gif' || $upload_file =='png' || $upload_file =='jpg' ){
									?>
								<p id="image-<?php echo $name; ?>" class="resumback"><img height="60px" width="60px" src="<?php echo $image; ?>" /><span class="ajax-file-upload-red" onclick="single_delete_image('<?php echo basename($value);?>','<?php echo $name;?>')"><?php _e('Delete',ADMINDOMAIN); ?></span></p>
						<?php }elseif($upload_file != ''){ ?>
								<p id="image-<?php echo $name; ?>" class="resumback"><a href="<?php echo get_post_meta($_REQUEST['pid'],$name, $single = true); ?>"><?php echo basename(get_post_meta($_REQUEST['pid'],$name, $single = true)); ?></a><span class="ajax-file-upload-red" onclick="single_delete_image('<?php echo basename($value);?>','<?php echo $name;?>')"><?php _e('Delete',ADMINDOMAIN); ?></span></p>
							<?php } 
						endif; 
						echo '</div>';
						if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif; ?>
				<?php echo $is_required_msg;?>
				</div>
			<?php	
				break;
				
		    case "oembed_video": ?>
			
				<?php do_action('tmpl_custom_fields_'.$name.'_before'); ?>
				
				<input name="<?php echo $name;?>" id="<?php echo $name;?>" value='<?php if(isset($value) && $value!=''){ echo stripslashes($value); } ?>' type="text" class="textfield <?php echo $style_class;?>" <?php echo $extra_parameter; ?> <?php echo $extra_script;?> placeholder="<?php echo @$val['default']; ?>"/>
				
				<?php echo $is_required_msg;
				if($admin_desc!=""):?>
					<div class="description"><?php echo $admin_desc; ?></div>
				<?php endif;
				
				do_action('tmpl_custom_fields_'.$name.'_after');
				break;
				
		    case "range_type":
			
				$range_min=$val['range_min'];
				$range_max=$val['range_max'];
				global $validation_info;					
				if($val['is_require']==0 && $range_min!="" && $range_max!='' && $val['search_ctype']=='slider_range'){
					$validation_info[] = array(
						'title'	       => $val['label'],
						'name'	       => $key,
						'espan'	       => $key.'_error',
						'type'	       => $val['type'],
						'text'	       => $val['text'],
						'is_require'	  => 1,
						'validation_type'=> 'digit',
						'search_ctype'=> $val['search_ctype']
					);
				}
				
				do_action('tmpl_custom_fields_'.$name.'_before'); ?> 
				
			    <input name="<?php echo $name;?>" id="<?php echo $name;?>" value="<?php if(isset($value) && $value!=''){ echo stripslashes($value); } ?>" type="text" class="textfield <?php echo $style_class;?>" <?php echo $extra_parameter; ?> <?php echo $extra_script;?> placeholder="<?php echo @$val['default']; ?>" min="<?php echo $range_min?>" max="<?php echo $range_max?>"/>
				<?php echo $is_required_msg;
			  
				if($admin_desc!=""):?><div class="description"><?php echo $admin_desc; ?></div><?php endif;
			  
				do_action('tmpl_custom_fields_'.$name.'_after'); 
				
				break;
				
			case "image_uploader":
			
				echo '<div class="upload_box">';
					add_action('wp_footer','callback_on_footer_fn');?>
                    <div class="hide_drag_option_ie">
                        <p><?php _e('You can drag &amp; drop images from your computer to this box.',DOMAIN); ?></p>
                        <p><?php _e('OR',DOMAIN); ?></p>
                    </div>
					<?php
					include (apply_filters('include_image_upload_script',TEMPL_MONETIZE_FOLDER_PATH."templatic-custom_fields/image_uploader.php")); ?>
                    <span class="message_note"><?php echo $admin_desc;?></span>
                    <span class="message_error2" id="post_images_error"></span>
					<span class="safari_error" id="safari_error"></span>
				<?php
				echo "</div>";
				break;
				
		    case "geo_map":
				include_once(TEMPL_MONETIZE_FOLDER_PATH."templatic-custom_fields/location_add_map.php"); 
				
				if($admin_desc == ''): ?>
					<span class="message_note"><?php echo $GET_MAP_MSG;?></span>
				<?php endif; 
				break;
		    default:
				do_action('tevolution_custom_fieldtype',$key,$val,$post_type);
		}
		do_action('tmpl_cutom_fields_settings',$custom_metaboxes,$session_variable,$post_type,$pkg_id,$name);
		/* Switch case end */

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		do_action('show_new_custom_field',$type,$site_title,$is_required);
		
		?>     
		 
		</div>    
		<?php
		do_action('tmpl_custom_fields_'.$name.'_after_wrap');
		}
	}	
}




/* Fetch heading type custom fields. its display in custom field create or edit section */
function fetch_heading_posts()
{
	global $wpdb,$post;
	remove_all_actions('posts_where');
	$heading_title = array();
	/* Wp query passing argument for fetch is active heading type*/
	$args=array('post_type'      => 'custom_fields',
				'posts_per_page' => -1	,
				'post_status'    => array('publish'),
				'meta_query'     => array('relation' => 'AND',
									   array('key' => 'ctype','value' => 'heading_type','compare' => '=','type'=> 'text'),
									   array('key' => 'is_active','value' => '1','compare' => '=','type'=> 'text')
									),
				'meta_key'       => 'sort_order',
				'orderby'        => 'meta_value_num',
				'meta_value_num' =>'sort_order',
				'order'          => 'ASC'
				);
	$post_query = null;
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$post_query = new WP_Query($args);
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	$post_meta_info = $post_query;
	
	if($post_meta_info){
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
			$heading_title[$post->post_name] = $post->post_title;
		endwhile;
	}
	return $heading_title;
}

/*
	Return the categories array of taxonomy which we pass in argument
*/
function templ_get_parent_categories($taxonomy) {
	$cat_args = array(
	'taxonomy'=>$taxonomy,
	'orderby' => 'name', 				
	'hierarchical' => 'true',
	'parent'=>0,
	'hide_empty' => 0,	
	'title_li'=>'');				
	$categories = get_categories( $cat_args );	/* fetch parent categories */
	return $categories;
}
/*
	If we pass parent category ID and taxonomy in functions argument it will return all the child categories 
*/
function templ_get_child_categories($taxonomy,$parent_id) {
	$args = array('child_of'=> $parent_id,'hide_empty'=> 0,'taxonomy'=>$taxonomy);                        
	$child_cats = get_categories( $args );	/* get child cats */
	return $child_cats;
}

/* 
 Returns category custom fields html on submit page
*/
function display_custom_category_field_plugin($custom_metaboxes,$session_variable,$post_type,$cpost_type='post'){ 
  
	foreach($custom_metaboxes as $key=>$val) { 
		$name = $val['name'];
		$site_title = $val['label'];
		$type = $val['ctype'];
		$htmlvar_name = $val['htmlvar_name'];
		$admin_desc = $val['desc'];
		$option_values = $val['option_values'];
		$default_value = $val['default'];
		$style_class = $val['style_class'];
		$extra_parameter = $val['extra_parameter'];
		if(!$extra_parameter){ $extra_parameter ='';}
		/* Is required CHECK BOF */
		$is_required = '';
		$input_type = '';
		if($val['is_require'] == '1'){
			$is_required = '<span class="required">*</span>';
			$is_required_msg = '<span id="'.$name.'_error" class="message_error2"></span>';
		} else {
			$is_required = '';
			$is_required_msg = '';
		}
		/* Is required CHECK EOF */
		if(@$_REQUEST['pid'])
		{
			$post_info = get_post($_REQUEST['pid']);
			if($name == 'post_title') {
				$value = $post_info->post_title;
			}else {
				$value = get_post_meta($_REQUEST['pid'], $name,true);
			}
			
		}else if(isset($_SESSION[$session_variable]) && !empty($_SESSION[$session_variable]))
		{
			$value = @$_SESSION[$session_variable][$name];
		}else{
			$value='';
		}
	   
	if($type=='post_categories')
	{ /* fetch catgories on action */	
		wp_reset_query();
		global $post;
		$submit_post_type=get_post_meta($post->ID,'submit_post_type',true);
		$PostTypeObject = get_post_type_object(($submit_post_type!='')?$submit_post_type:$cpost_type);
		$_PostTypeName = $PostTypeObject->labels->name;
		?>
        <div class="form_row clearfix">	  
            <label><?php _e('Select Categories',DOMAIN).$is_required; ?></label>
            <div class="category_label"><?php include(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/category.php');?></div>
            <?php echo $is_required_msg;?>
            <?php if($admin_desc!=""):?>
            	<div class="description"><?php echo $admin_desc; ?></div>
			<?php else: ?>
            	<span class="message_note msgcat"><?php _e("Select a category for your ",DOMAIN); echo strtolower($_PostTypeName); ?></span>
            <?php endif;
			
			/* check the category wise custom fields are enable or not - load Ajax if cat wise custom fields option is selected */
			
			$templatic_settings = get_option('templatic_settings');
		
			if((!isset($templatic_settings['templatic-category_custom_fields']) && $templatic_settings['templatic-category_custom_fields']=='') || (isset($templatic_settings['templatic-category_custom_fields']) && $templatic_settings['templatic-category_custom_fields']=='No')){
				$category_custom_fields = 0;
			}else{
				$category_custom_fields = 1;
			}
			?>
			<input type="hidden" name="cat_fields" id="cat_fields" value="<?php echo $category_custom_fields; ?>"/>
        </div>    
    <?php 
	}elseif(isset($_REQUEST['upgpkg']) && $_REQUEST['upgpkg'] ==1 && $type=='post_categories'){
		wp_reset_query();
		global $post;
		$PostTypeObject = get_post_type_object(get_post_meta($post->ID,'submit_post_type',true));
		$_PostTypeName = $PostTypeObject->labels->name;

		 ?>
        <div class="form_row clearfix">        
            <label><?php echo $_PostTypeName. __('Category',DOMAIN).$is_required; ?></label>
            <div class="category_label"><?php include(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/category.php');?></div>
            <?php echo $is_required_msg;
			
			if($admin_desc!=""):?>
            	<div class="description"><?php echo $admin_desc; ?></div>
			<?php else:
				$PostTypeObject = get_post_type_object($post_type);
				$_PostTypeName = $PostTypeObject->labels->name;?>
            	<span class="message_note msgcat"><?php _e("In which category you'd like to publish this ",DOMAIN); echo strtolower($_PostTypeName) ."?"; ?></span>
            <?php endif;?>
        </div>    
	<?php }
	do_action('show_additional_custom_field',$type,$site_title,$is_required,$cpost_type);
	}

}

/* This function use for display submit form custom fields category wise using jquery ajax.*/
add_action( 'wp_ajax_nopriv_submit_category_custom_fields','tmpl_get_submit_category_custom_fields');
add_action( 'wp_ajax_submit_category_custom_fields' ,'tmpl_get_submit_category_custom_fields');
function tmpl_get_submit_category_custom_fields(){
	
	$post_type=$_REQUEST['post_type'];
	$all_cat_id=$_REQUEST['category_id'];	
	$pakg_id = $_REQUEST['pakg_id'];
	$submit_page_id = $_REQUEST['submit_page_id'];
	/*Get the taxonomy mane from  post type */
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));
	$taxonomy = $taxonomies[0];
	
	/*fetch heading type from post type */
	$heading_type = fetch_heading_per_post_type($post_type);			
	if(count($heading_type) > 0)
	{
		foreach($heading_type as $_heading_type){
			/*custom fields for custom post type..*/
			$custom_metaboxes[$_heading_type] = get_post_custom_fields_templ_plugin($post_type,$all_cat_id,$taxonomy,$_heading_type);
		}
	}else{
		/*custom fields for custom post type..*/
		$custom_metaboxes[] = get_post_custom_fields_templ_plugin($post_type,$all_cat_id,$taxonomy,'');
	}	
	
	$validation_info=apply_filters('tevolution_submit_from_validation',$validation_info,$custom_metaboxes);
	/* Display custom fields post type wuse */
	/*display custom fields html on submit form.*/
	display_custom_post_field_plugin($custom_metaboxes,'custom_fields',$post_type,$pakg_id,$submit_page_id);
	/*action after custom fields*/
	do_action('action_after_custom_fields',$custom_metaboxes,'custom_fields',$post_type,$pakg_id);
	/* wp_editor load using jquery ajax script  */  
	if (class_exists('_WP_Editors')) {
		_WP_Editors::editor_js();
	}
	die();
}



/* 
 * Display submit preview on popup model window 
 * Load preview page template as per post type subit page
 */
add_action( 'wp_ajax_nopriv_tevolution_submit_from_preview','tmpl_get_tevolution_submit_from_preview');
add_action( 'wp_ajax_tevolution_submit_from_preview' ,'tmpl_get_tevolution_submit_from_preview');
function tmpl_get_tevolution_submit_from_preview(){
	
	$post_type=$_REQUEST['submit_post_type'];	
	/* Do action for add additional post type preview page display hook */	
	
	do_action('before_tevolution_submit_'.$post_type.'_preview');
	
	get_template_part( 'tevolution-single', $post_type.'-preview'); 
	
	do_action('after_tevolution_submit_'.$post_type.'_preview');
	
	die();
}

/* 
 * Display price package information while submission listing after selecting price package.
 */
add_action( 'wp_ajax_nopriv_tmpl_tevolution_submit_from_package_info','tmpl_tevolution_submit_from_package_info');
add_action( 'wp_ajax_tmpl_tevolution_submit_from_package_info' ,'tmpl_tevolution_submit_from_package_info');
function tmpl_tevolution_submit_from_package_info(){
	$package_array = get_post($_REQUEST['pkg_id']);
	$result = '';
	$result .='<span class="label label-default">'.ucfirst($package_array->post_title);
	if(get_post_meta($_REQUEST['pkg_id'],'package_amount',true) > 0 && isset($_REQUEST['pkg_subscribed']) && $_REQUEST['pkg_subscribed'] ==0) 
	{ 
		$result .= __(' Package with price ',DOMAIN);
		$result .= get_option('currency_symbol').get_post_meta($_REQUEST['pkg_id'],'package_amount',true); 
	}
	$result .='</span>';
	echo $result;exit;
}
/* 
 * Display category as per price package.
 */
add_action( 'wp_ajax_nopriv_tmpl_tevolution_submit_from_category','tmpl_tevolution_submit_from_category');
add_action( 'wp_ajax_tmpl_tevolution_submit_from_category' ,'tmpl_tevolution_submit_from_category');
function tmpl_tevolution_submit_from_category(){
	global $include_cat_array;
	
	$post_type=$_REQUEST['submit_post_type'];
	/*get the post type taxonomy */
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));
	$taxonomy = $taxonomies[0];
	if(isset($_REQUEST['package_select']) && $_REQUEST['package_selec'] != '')
	{
		$_REQUEST['pkg_id'] = $_REQUEST['package_select'];
	}
	$pkg_id=$_REQUEST['pkg_id'];
	/*Set the display category list on submit page  */
	$include_cat_array=get_post_meta($pkg_id,'category',true);
	if($include_cat_array!=''){
		$include_cat_array=explode(',',$include_cat_array);
	}
	/*echo $post_type."==".$taxonomy."==".$pkg_id."==".$cat_array;*/
	$default_custom_metaboxes = get_post_fields_templ_plugin($post_type,$all_cat_id,$taxonomy);/*custom fields for all category.*/
	$category_custom_metaboxes['category']=$default_custom_metaboxes['category'];
	/* Display post type category box */
	
	/*Unset action for get the category from package wise on submit form */
	unset($_REQUEST['action']);	
	/* Display Categort as per price package wise on submit form page */
	display_custom_category_field_plugin($category_custom_metaboxes,'custom_fields','post',$post_type);/*displaty  post category html.	*/
	do_action('action_after_custom_fields',$category_custom_metaboxes,'custom_fields',$post_type,$pkg_id);
	die();
}


/* 
 * Display featured option as per price package selected.
 */
add_action( 'wp_ajax_nopriv_tmpl_tevolution_submit_from_package_featured_option','tmpl_tevolution_submit_from_package_featured_option');
add_action( 'wp_ajax_tmpl_tevolution_submit_from_package_featured_option' ,'tmpl_tevolution_submit_from_package_featured_option');
function tmpl_tevolution_submit_from_package_featured_option(){
	global $current_user,$post,$monetization;
	if(isset($_REQUEST['package_select']) && $_REQUEST['package_select'] != '')
	{
		$_REQUEST['pkg_id'] = $_REQUEST['package_select'];
	}
	$post_type = $_REQUEST['submit_post_type'];
	$result = '';
	$result .= $monetization->tmpl_fetch_price_package_featured_option($current_user->ID,$post_type,$post->ID,$_REQUEST['pkg_id'],$_REQUEST['pkg_subscribed']);/*fetch the price package*/
	echo $result;exit;
}

/* get the link of submit form . which post type pass in argument */

if(!function_exists('tmpl_get_submitfrm_link')){
	function tmpl_get_submitfrm_link($post_type){
		global $current_user,$wp_query,$curauth;
		/*$curauth =  $wp_query->get_queried_object();*/

		if($current_user->ID == $curauth->ID)
		{
		/* query to get the submit form link, it will check the "submit_post_type" meta key is available with the value of pot type pass in arg*/
		$args=array('post_type'=>'page','posts_per_page'=>-1,
				'meta_query'     => array('relation' => 'AND',
						   array('key' => 'submit_post_type','value' => $post_type,'compare' => '=='),
						   array('key' => 'is_tevolution_submit_form','value' => '1','compare' => '==')
						),
				);
		$post_query = new WP_Query($args);
		$PostTypeObject = get_post_type_object($post_type);
		$_PostTypelabel = $PostTypeObject->labels->name;
		$submit_link='';
		if($post_query->have_posts()){
			while ($post_query->have_posts()) { $post_query->the_post();
				$submit_link = __(' Head over to the ',THEME_DOMAIN);
				$submit_link .='<a href="'.get_permalink().'" target="_blank">'.__('Submit ',DOMAIN)." ".ucfirst($_PostTypelabel)." ".__('Form',DOMAIN).'</a>';	
				$submit_link .= __( ' to add one.', THEME_DOMAIN );
			}
		}
		return $submit_link;
		}
	}
}
/* captcha validation while submit form */
add_action( 'wp_ajax_nopriv_submit_form_recaptcha_validation','tmpl_submit_form_recaptcha_validation');
add_action( 'wp_ajax_submit_form_recaptcha_validation' ,'tmpl_submit_form_recaptcha_validation');
function tmpl_submit_form_recaptcha_validation(){	

	$tmpdata = get_option('templatic_settings');
	$display =(isset($tmpdata['user_verification_page']) && $tmpdata['user_verification_page'] != "")? $tmpdata['user_verification_page']:"";
	
	if( in_array('submit',$display) ){
	
		/*fetch captcha private key*/
		$privatekey = $tmpdata['secret'];
		if($_REQUEST["g-recaptcha-response"]!="")
		{
			/*get the response from captcha that the entered captcha is valid or not*/
			$response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=".$privatekey."&response=".$_REQUEST["g-recaptcha-response"]."&remoteip=".getenv("REMOTE_ADDR"));											
			/*decode the captcha response*/
			$responde_encode = json_decode($response['body']);
			if (!$responde_encode->success) {				
				$send_data['recaptcha_error']= __('Please fill the captcha form.',DOMAIN);
			}else{
				$send_data['recaptcha_error']=true;
			}
		}else{
			$send_data['recaptcha_error']= __('Please fill the captcha form.',DOMAIN);
		}
		echo $send_data['recaptcha_error'];
	}else{
		echo true;	
	}
	exit;
}
/* display payment options only when monetization is activated */
add_action('action_before_html','show_payemnt_gateway_error');
function show_payemnt_gateway_error()
{
	 ?>
	<span style="color:red;font-weight:bold;display:block;" id="payment_errors"><?php 
		if(isset($_REQUEST['paypalerror']) && $_REQUEST['paypalerror']=='yes'){
			echo $_SESSION['paypal_errors'];
		}
		if(isset($_REQUEST['eway_error']) && $_REQUEST['eway_error']=='yes'){
			echo $_SESSION['display_message'];
		}
		if(isset($_REQUEST['stripeerror']) && $_REQUEST['stripeerror']=='yes'){
			echo $_SESSION['stripe_errors'];
		}
		if(isset($_REQUEST['psigateerror']) && $_REQUEST['psigateerror']=='yes'){
			echo $_SESSION['psigate_errors'];
		}
		if(isset($_REQUEST['braintreeerror']) && $_REQUEST['braintreeerror']=='yes'){
			echo $_SESSION['braintree_errors'];
		}
		if(isset($_REQUEST['inspire_commerceerror']) && $_REQUEST['inspire_commerceerror']=='yes'){
			echo $_SESSION['inspire_commerce_errors'];
		}
	?></span>
    <?php
}
/* 
	Its return the array of default custom fields with fields informations like "post title","post excerpt","post categories" etc.  
*/
function get_post_fields_templ_plugin($post_types,$category_id='',$taxonomy='') {
	global $wpdb,$post;
	remove_all_actions('posts_where');
	$tmpdata = get_option('templatic_settings');	
	$args=array('post_type' => 'custom_fields',
				'posts_per_page' => -1	,
				'post_status' => array('publish'),
				'meta_query' => array('relation' => 'AND',
								array('key' => 'post_type_'.$post_types.'','value' => array($post_types,'all'),'compare' => 'IN','type'=> 'text'),
								array('key' => 'show_on_page','value' =>  array('user_side','both_side'),'compare' => 'IN','type'=> 'text'),
								array('key' => 'is_submit_field', 'value' =>  '1','compare' => '='),
							),
				'meta_key' => 'sort_order',
				'orderby' => 'meta_value_num',
				'meta_value_num'=>'sort_order',
				'order' => 'ASC'
		);
	
	if((isset($_REQUEST['pid']) && $_REQUEST['pid']!='' && isset($_REQUEST['action']) && $_REQUEST['action']=='edit') || (isset($_REQUEST['action_edit']) && $_REQUEST['action_edit']=='edit')){
			/* Unset is submit field  on edit listing page for display all custom fields post type wise*/			
			unset($args['meta_query'][2]);	
		}
	$post_query = null;
	$post_query = new WP_Query($args);	
	$post_meta_info = $post_query;
	$return_arr = array();
	if($post_meta_info){
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
			$is_active=get_post_meta($post->ID,"is_active",true);
			$ctype=get_post_meta($post->ID,"ctype",true);
			/*Custom fields loop returns if is active not equal to one or ctype equal to heading type */
			if($is_active!=1 || $ctype=='heading_type'){
				continue;
			}
			if(get_post_meta($post->ID,"ctype",true)){
				$options = explode(',',get_post_meta($post->ID,"option_values",true));
			}
			$custom_fields = array(
					"name"		=> get_post_meta($post->ID,"htmlvar_name",true),
					"label" 	=> $post->post_title,
					"htmlvar_name" 	=> get_post_meta($post->ID,"htmlvar_name",true),
					"default" 	=> get_post_meta($post->ID,"default_value",true),
					"ctype" 		=> get_post_meta($post->ID,"ctype",true),
					"desc"      =>  $post->post_content,
					"option_values" => get_post_meta($post->ID,"option_values",true),
					"is_require"  => get_post_meta($post->ID,"is_require",true),
					"is_active"  => get_post_meta($post->ID,"is_active",true),
					"show_on_listing"  => get_post_meta($post->ID,"show_on_listing",true),
					"show_on_detail"  => get_post_meta($post->ID,"show_on_detail",true),
					"validation_type"  => get_post_meta($post->ID,"validation_type",true),
					"style_class"  => get_post_meta($post->ID,"style_class",true),
					"extra_parameter"  => get_post_meta($post->ID,"extra_parameter",true),
					"show_in_email" =>get_post_meta($post->ID,"show_in_email",true),
					"heading_type" => get_post_meta($post->ID,"heading_type",true),
					);
			if($options)
			{
				$custom_fields["options"]=$options;
			}
			$return_arr[get_post_meta($post->ID,"htmlvar_name",true)] = $custom_fields;
		endwhile;
	}
	return $return_arr;
}

/*
Display the categories check box like wordpress - wp-admin/includes/meta-boxes.php
 */
function tev_wp_terms_checklist($post_id = 0, $args = array()) {
	global  $cat_array;
 	$defaults = array(
		'descendants_and_self' => 0,
		'selected_cats' => false,
		'popular_cats' => false,
		'walker' => null,
		'taxonomy' => 'category',
		'checked_ontop' => true
	);

	if(isset($_REQUEST['backandedit']) != '' || (isset($_REQUEST['pid']) && $_REQUEST['pid']!="") ){
		$place_cat_arr = $cat_array;
		$post_id = $_REQUEST['pid'];
	}
	else
	{
		if(!empty($cat_array)){
			for($i=0; $i < count($cat_array); $i++){
				$place_cat_arr[] = @$cat_array[$i]->term_taxonomy_id;
			}
		}
	}
	$args = apply_filters( 'wp_terms_checklist_args', $args, $post_id );
	$template_post_type = get_post_meta($post->ID,'submit_post_type',true);
	extract( wp_parse_args($args, $defaults), EXTR_SKIP );

	if ( empty($walker) || !is_a($walker, 'Walker') )
		$walker = new Tev_Walker_Category_Checklist;

	$descendants_and_self = (int) $descendants_and_self;

	$args = array('taxonomy' => $taxonomy);

	$tax = get_taxonomy($taxonomy);
	$args['disabled'] = !current_user_can($tax->cap->assign_terms);

	if ( is_array( $selected_cats ) )
		$args['selected_cats'] = $selected_cats;
	elseif ( $post_id && (!isset($_REQUEST['upgpkg']) && !isset($_REQUEST['renew'])) )
		$args['selected_cats'] = wp_get_object_terms($post_id, $taxonomy, array_merge($args, array('fields' => 'ids')));
	else
		$args['selected_cats'] = array();

	if ( is_array( $popular_cats ) )
		$args['popular_cats'] = $popular_cats;
	else
		$args['popular_cats'] = get_terms( $taxonomy, array( 'get' => 'all', 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'hierarchical' => false ) );

	if ( $descendants_and_self ) {
		$categories = (array) get_terms($taxonomy, array( 'child_of' => $descendants_and_self, 'hierarchical' => 0, 'hide_empty' => 0 ) );
		$self = get_term( $descendants_and_self, $taxonomy );
		array_unshift( $categories, $self );
	} else {
		$categories = (array) get_terms($taxonomy, array('get' => 'all'));
	}

	if ( $checked_ontop ) {
		/* Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)*/
		$checked_categories = array();
		$keys = array_keys( $categories );
		$c=0;
		foreach( $keys as $k ) {
			if ( in_array( $categories[$k]->term_id, $args['selected_cats'] ) ) {
				$checked_categories[] = $categories[$k];
				unset( $categories[$k] );
			}
		}

		/* Put checked cats on top*/
		echo call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args));
	}
	/* Then the rest of them*/

	echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
	if(empty($categories) && empty($checked_categories)){
		echo '<span style="font-size:12px; color:red;">'.sprintf(__('You have not created any category for %s post type. So, this listing will be submited as uncategorized.',DOMAIN),$template_post_type).'</span>';
	}
}

/**
 * Walker to output an unordered list of category checkbox <input> elements.
 *
 */
class Tev_Walker_Category_Checklist extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); /*TODO: decouple this*/
    var $selected_cats = array();

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		extract($args);
		global $include_cat_array;
		/* Check term id in include cart array if not in include cart array then continue loop  for display category price package wise set */
		if(is_array($include_cat_array) && !in_array($category->term_id,$include_cat_array) && !in_array('all',$include_cat_array)){			
			return ;
		}
		/* finish display price package wise category */
		if ( empty($taxonomy) )
			$taxonomy = 'category';

		if ( $taxonomy == 'category' )
			$name = 'post_category';
		else
			$name = 'tax_input['.$taxonomy.']';

		$selected = array();
		if($category->term_price !='' &&  $category->term_price!='0' ){$cprice = "&nbsp;(".display_amount_with_currency_plugin($category->term_price).")"; }else{ $cprice =''; }
		$disabled = '';
		if(isset($_REQUEST['pid']) && $_REQUEST['pid']!=""){
			$edit_id = $_REQUEST['pid'];
			/*get the submited price package */
			$pkg_id=get_post_meta($edit_id,'package_select',true);
			$pkg_category=explode(',',get_post_meta($pkg_id,'category',true));
			/* check category on price package selected catgeory if category not in price package category then return output */
			if(!empty($pkg_category) && $pkg_category[0]!='' && !in_array($category->term_id,$pkg_category) && !in_array('all',$pkg_category)){				
				return $output;	
			}
		}
		if((isset($edit_id) && $edit_id !='' && (!isset($_REQUEST['renew']))) && !isset($_REQUEST['backandedit']) )
		{
			if(checked( in_array( $category->term_id, $selected_cats ), true, false ) == " checked='checked'" && @$category->term_price > 0)
			{
				$disabled = "disabled='disabled'";
			}
		}
	/*	$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';*/
		$output .= "\n<li id='{$taxonomy}-{$category->term_id}'>" . '<label class="selectit"><input data-value="'.$category->term_id.'" value="' . $category->term_id . ','.$category->term_price.'" type="checkbox" name="category[]" id="in-'.$taxonomy.'-' . $category->term_id . '" '.$disabled.'  ' . checked( in_array( $category->term_id, $selected_cats ), true, false ) .    ' /> ' . esc_html( apply_filters('the_category', $category->name )) . $cprice.'</label>';
	}

	function end_el( &$output, $category, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}
/* 
	Return the upload image directory , where uploaded file will move
*/
function get_image_phy_destination_path_plugin()
{	
	$wp_upload_dir = wp_upload_dir();
	$path = $wp_upload_dir['path'];
	$url = $wp_upload_dir['url'];
	  $destination_path = $path."/";
      if (!file_exists($destination_path)){
      $imagepatharr = explode('/',str_replace(ABSPATH,'', $destination_path));
	   $year_path = ABSPATH;
		for($i=0;$i<count($imagepatharr);$i++)
		{
		  if($imagepatharr[$i])
		  {
			$year_path .= $imagepatharr[$i]."/";
			  if (!file_exists($year_path)){
				  mkdir($year_path, 0777);
			  }     
			}
		}
	}
	  return $destination_path;
}
/*
 * get the heading type for selected post type
 */
function tmpl_fetch_heading_post_type($post_type){
	
	global $wpdb,$post,$heading_title;
	$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';
	
	remove_all_actions('posts_where');
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	remove_action('pre_get_posts','location_pre_get_posts',12);
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$heading_title = array();
	$args=
	array( 
	'post_type' => 'custom_fields',
	'posts_per_page' => -1	,
	'post_status' => array('publish'),
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key' => 'ctype',
			'value' => 'heading_type',
			'compare' => '=',
			'type'=> 'text'
		),
		array(
			'key' => 'post_type',
			'value' => $post_type,
			'compare' => 'LIKE',
			'type'=> 'text'
		)
		
	),
	'meta_key' => 'sort_order',	
	'orderby' => 'meta_value_num',
	'meta_value_num'=>'sort_order',
	'order' => 'ASC'
	);
	$post_query = null;
	remove_all_actions('posts_orderby');
	
	$post_query = get_transient( '_tevolution_query_heading'.trim($post_type).$cur_lang_code);
	if ( false === $post_query && get_option('tevolution_cache_disable')==1){
		$post_query = new WP_Query($args);
		set_transient( '_tevolution_query_heading'.trim($post_type).$cur_lang_code, $post_query, 12 * HOUR_IN_SECONDS );
	}elseif(get_option('tevolution_cache_disable')==''){
		$post_query = new WP_Query($args);
	}
	
	$post_meta_info = $post_query;	
	if($post_meta_info){
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
			$otherargs=
			array( 
			'post_type' => 'custom_fields',
			'posts_per_page' => -1	,
			'post_status' => array('publish'),
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'is_active',
					'value' => '1',
					'compare' => '=',
					'type'=> 'text'
				),
				array(
					'key' => $post_type.'_heading_type',
					'value' => $post->post_title,
					'compare' => '=',
					'type'=> 'text'
				)
			));		
			
			$other_post_query = null;
			$htmlvar_name=get_post_meta(get_the_ID(),'htmlvar_name',true);
			$other_post_query = get_transient( '_tevolution_query_heading'.trim($post_type).trim($htmlvar_name).$cur_lang_code );			
			if ( false === $other_post_query  && get_option('tevolution_cache_disable')==1) {
				$other_post_query = new WP_Query($otherargs);				
				set_transient( '_tevolution_query_heading'.trim($post_type).trim($htmlvar_name).$cur_lang_code, $other_post_query, 12 * HOUR_IN_SECONDS );
			}elseif(get_option('tevolution_cache_disable')==''){				
				$other_post_query = new WP_Query($otherargs);
			}
			
			if(count($other_post_query->post) > 0)
			{
				$heading_title[$htmlvar_name] = $post->post_title;
			}
		endwhile;
		wp_reset_query();
	}
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	return $heading_title;
}
/* function will return the fields which we shows by default on detail page.
we create the separate function because we needs want the variables name without heading type*/
if(!function_exists('tmpl_single_page_default_custom_field')){
function tmpl_single_page_default_custom_field($post_type){
	$custom_post_type = tevolution_get_post_type();
	
	/* check its detail page or preview page */
	if((is_single() || $_GET['page']=='preview') && $post_type !=''){
		global $wpdb,$post,$tmpl_flds_varname,$pos_title;
		
		$cus_post_type = $post_type;
		$heading_type = tmpl_fetch_heading_post_type($post_type);
		$tmpl_flds_varname = array();
		global $wpdb,$post,$posttitle;
		$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';
		
		remove_all_actions('posts_where');		
		$post_query = null;
		remove_action('pre_get_posts','event_manager_pre_get_posts');
		remove_action('pre_get_posts','directory_pre_get_posts',12);
		add_filter('posts_join', 'custom_field_posts_where_filter');


		$args = array( 'post_type' => 'custom_fields',
					'posts_per_page' => -1	,
					'post_status' => array('publish'),
					'meta_query' => array('relation' => 'AND',
									array(
										'key'     => 'post_type_'.$post_type.'',
										'value'   => $post_type,
										'compare' => '=',
										'type'    => 'text'
									),		
									array(
										'key'     => 'is_active',
										'value'   =>  '1',
										'compare' => '='
									),
									array(
										'key'     => 'show_on_detail',
										'value'   =>  '1',
										'compare' => '='
									)
								),
					'meta_key' => 'sort_order',
					'orderby' => 'meta_value',
					'order' => 'ASC'
		);
	
		/* save the data on transient to get the fast results */
		$post_query = get_transient( '_tevolution_query_single'.trim($post_type).trim($heading_key).$cur_lang_code );
		if ( false === $post_query && get_option('tevolution_cache_disable')==1 ) {
			$post_query = new WP_Query($args);
			set_transient( '_tevolution_query_single'.trim($post_type).trim($heading_key).$cur_lang_code, $post_query, 12 * HOUR_IN_SECONDS );
		}elseif(get_option('tevolution_cache_disable')==''){
			$post_query = new WP_Query($args);
		}

		
		
		/* Join to make the custom fields WPML compatible */
		remove_filter('posts_join', 'custom_field_posts_where_filter');
		
		$tmpl_flds_varname='';
		if($post_query->have_posts())
		{
			while ($post_query->have_posts()) : $post_query->the_post();
				$ctype = get_post_meta($post->ID,'ctype',true);
				$post_name=get_post_meta($post->ID,'htmlvar_name',true);
				$style_class=get_post_meta($post->ID,'style_class',true);
				$option_title=get_post_meta($post->ID,'option_title',true);
				$option_values=get_post_meta($post->ID,'option_values',true);
				$default_value=get_post_meta($post->ID,'default_value',true);
				$tmpl_flds_varname[$post_name] = array( 'type'=>$ctype,
											'label'=> $post->post_title,
											'style_class'=>$style_class,
											'option_title'=>$option_title,
											'option_values'=>$option_values,
											'default'=>$default_value,
											);			
			endwhile;
			wp_reset_query();
		}
		return $tmpl_flds_varname;
	}
}
}
/* Return User name while submit form as a guest user */
function get_user_name_plugin($fname,$lname='')
{
	global $wpdb;
	if($lname)
	{
		$uname = $fname.'-'.$lname;
	}else
	{
		$uname = $fname;
	}
	$nicename = strtolower(str_replace(array("'",'"',"?",".","!","@","#","$","%","^","&","*","(",")","-","+","+"," "),array('','','','-','','-','-','','','','','','','','','','-','-',''),$uname));
	$nicenamecount = $wpdb->get_var("select count(user_nicename) from $wpdb->users where user_nicename like \"$nicename\"");
	if($nicenamecount=='0')
	{
		return trim($nicename);
	}else
	{
		$lastuid = $wpdb->get_var("select max(ID) from $wpdb->users");
		return $nicename.'-'.$lastuid;
	}
}
/* 

	Return the site/admin email

*/
if(!function_exists('get_site_emailId_plugin')){
function get_site_emailId_plugin()
{

	$generalinfo = get_option('mysite_general_settings');

	if($generalinfo['site_email'])

	{

		return $generalinfo['site_email'];

	}else

	{

		return get_option('admin_email');

	}

}
}
/*  Here I made an array of user custom fields */
if(!function_exists('user_fields_array')){
function user_fields_array()
{
	global $post;
	remove_all_actions('posts_where');
	$user_args=
	array( 'post_type' => 'custom_user_field',
	'posts_per_page' => -1	,
	'post_status' => array('publish'),
	'meta_query' => array(
	   'relation' => 'AND',
		array(
			'key' => 'on_registration',
			'value' =>  '1',
			'compare' => '='
		)
	),
	'meta_key' => 'sort_order',
	'orderby' => 'meta_value',
	'order' => 'ASC'
	);
	$user_meta_sql = null;
	$user_meta_sql = new WP_Query($user_args);
	if($user_meta_sql)
 	{
	while ($user_meta_sql->have_posts()) : $user_meta_sql->the_post();
	$name = $post->post_name;
	$site_title = $post->post_title;
	$type = get_post_meta($post->ID,'ctype',true);
	$is_require = get_post_meta($post->ID,'is_require',true);
	$admin_desc = $post->post_content;
	$option_values = get_post_meta($post->ID,'option_values',true);
	$on_registration = get_post_meta($post->ID,'on_registration',true);
	$on_profile = get_post_meta($post->ID,'on_profile',true);
	$on_author_page =  get_post_meta($post->ID,'on_author_page',true);
	if($type=='text'){
		$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'text',
		"default"	=>	$default_value,
		"extra"		=>	'id="'.$name.'" size="25" class="textfield"',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'<div class="form_row clearfix">',
		"outer_end"	=>	'</div>',
		"tag_st"	=>	'',
		"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span>',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
	}elseif($type=='checkbox'){
		$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'checkbox',
		"default"	=>	$default_value,
		"extra"		=>	'id="'.$name.'" size="25" class="checkbox"',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'<div class="form_row clearfix checkbox_field">',
		"outer_end"	=>	'',
		"tag_st"	=>	'',
		"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span></div>',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
	}elseif($type=='textarea'){
		$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'textarea',
		"default"	=>	$default_value,
		"extra"		=>	'id="'.$name.'" size="25" class="textarea"',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'<div class="form_row clearfix">',
		"outer_end"	=>	'</div>',
		"tag_st"	=>	'',
		"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span>',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
		
	}elseif($type=='texteditor'){
		$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'texteditor',
		"default"	=>	$default_value,
		"extra"		=>	'id="'.$name.'" size="25" class="mce"',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'<div class="form_row clear">',
		"outer_end"	=>	'</div>',
		"tag_before"=>	'<div class="clear">',
		"tag_after"=>	'</div>',
		"tag_st"	=>	'',
		"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span>',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
	}elseif($type=='select'){
		/*$option_values=explode(",",$option_values );*/
		$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'select',
		"default"	=>	$default_value,
		"extra"		=>	'id="'.$name.'"',
		"options"	=> 	$option_values,
		"is_require"	=>	$is_require,
		"outer_st"	=>	'<div class="form_row clear">',
		"outer_end"	=>	'</div>',
		"tag_st"	=>	'',
		"tag_end"	=>	'',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
	}elseif($type=='radio'){
		/*$option_values=explode(",",$option_values );*/
		$form_fields_usermeta[$name] = array(
			"label"		=> $site_title,
			"type"		=>	'radio',
			"default"	=>	$default_value,
			"extra"		=>	'',
			"options"	=> 	$option_values,
			"is_require"	=>	$is_require,
			"outer_st"	=>	'<div class="form_row clear">',
			"outer_end"	=>	'</div>',
			"tag_before"=>	'<div class="form_cat">',
			"tag_after"=>	'</div>',
			"tag_st"	=>	'',
			"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span>',
			"on_registration"	=>	$on_registration,
			"on_profile"	=>	$on_profile,
			"on_author_page" => $on_author_page,
			);
	}elseif($type=='multicheckbox'){
		/*$option_values=explode(",",$option_values );*/
		$form_fields_usermeta[$name] = array(
			"label"		=> $site_title,
			"type"		=>	'multicheckbox',
			"default"	=>	$default_value,
			"extra"		=>	'',
			"options"	=> 	$option_values,
			"is_require"	=>	$is_require,
			"outer_st"	=>	'<div class="form_row clear">',
			"outer_end"	=>	'</div>',
			"tag_before"=>	'<div class="form_cat">',
			"tag_after"=>	'</div>',
			"tag_st"	=>	'',
			"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span>',
			"on_registration"	=>	$on_registration,
			"on_profile"	=>	$on_profile,
			"on_author_page" => $on_author_page,
			);
	
	}elseif($type=='date'){
		$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'date',
		"default"	=>	$default_value,
		"extra"		=>	'id="'.$name.'" size="25" class="textfield_date"',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'<div class="form_row clearfix">',
		"outer_end"	=>	'</div>',		
		"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span>',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
		
	}elseif($type=='upload'){
	$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'upload',
		"default"	=>	$default_value,
		"extra"		=>	'id="'.$name.'" class="textfield"',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'<div class="form_row clearfix upload_img">',
		"outer_end"	=>	'</div>',
		"tag_st"	=>	'',
		"tag_end"	=>	'<span class="message_note">'.$admin_desc.'</span>',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
	}elseif($type=='head'){
	$form_fields_usermeta[$name] = array(
		"label"		=> $site_title,
		"type"		=>	'head',
		"outer_st"	=>	'<h5 class="form_title">',
		"outer_end"	=>	'</h5>',
		);
	}elseif($type=='geo_map'){
	$form_fields_usermeta[$name] = array(
		"label"		=> '',
		"type"		=>	'geo_map',
		"default"	=>	$default_value,
		"extra"		=>	'',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'',

		"outer_end"	=>	'',
		"tag_st"	=>	'',
		"tag_end"	=>	'',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);		
	}elseif($type=='image_uploader'){
	$form_fields_usermeta[$name] = array(
		"label"		=> '',
		"type"		=>	'image_uploader',
		"default"	=>	$default_value,
		"extra"		=>	'',
		"is_require"	=>	$is_require,
		"outer_st"	=>	'',
		"outer_end"	=>	'',
		"tag_st"	=>	'',
		"tag_end"	=>	'',
		"on_registration"	=>	$on_registration,
		"on_profile"	=>	$on_profile,
		"on_author_page" => $on_author_page,
		);
	}
  endwhile;
  return $form_fields_usermeta;
}
}
}
/* 
	Return the site title
*/
if(!function_exists('get_site_emailName_plugin')){
function get_site_emailName_plugin()
{
	$generalinfo = get_option('mysite_general_settings');
	if($generalinfo['site_email_name'])
	{
		return stripslashes($generalinfo['site_email_name']);
	}else
	{
		return stripslashes(get_option('blogname'));
	}
}
}
define('TMPL_HEADING_TITLE',__('Other Information',DOMAIN));
/* To display the custom fields on detail page */
if(!function_exists('tmpl_fields_detail_informations')){
function tmpl_fields_detail_informations($not_show = array('title'),$title_text = TMPL_HEADING_TITLE){
	global $post,$htmlvar_name,$heading_type;
	
	$is_edit='';
	if(isset($_REQUEST['action']) && $_REQUEST['action']=='edit'){
		$is_edit=1;
	}	
	$j=0;
	/*echo "<pre>"; print_r($htmlvar_name); echo "</pre>";*/
	if(!empty($htmlvar_name)){
		echo '<div class="tevolution_custom_field  listing_custom_field">';
		
		foreach($htmlvar_name as $key=>$value){
			$i=0;
			if(!empty($value)){
			foreach($value as $k=>$val){
			
				if(isset($_REQUEST['page']) && $_REQUEST['page'] =='preview' && isset($_SESSION['custom_fields'][$k])){
					$field= $_SESSION['custom_fields'][$k];		
				}else{
					$field= get_post_meta($post->ID,$k,true);		
				} 
				$tmpl_key = ($key=='basic_inf')? $title_text: $heading_type[$key];
				
				/* Show other custom fields */
				if($k!='post_title' && $k!='category' && $k!='post_content' && $k!='post_excerpt' && $k!='post_images' && $k!='listing_timing' && $k!='address' && $k!='listing_logo' && $k!='video' && $k!='post_tags' && $k!='map_view' && $k!='proprty_feature' && $k!='phone' && $k!='email' && $k!='website' && $k!='twitter' && $k!='facebook' && $k!='google_plus' && $k!='contact_info' && !in_array($k,$not_show))
				{
					/* To display the title and Locations information on top */
					$key_value = get_post_meta($post->ID,$k,true);
					
					if($is_edit ==1 && $i==0 && $key_value !=''){
						echo '<h2 class="custom_field_headding">'.$tmpl_key.'</h2>';
					}
					if($i==0 && $key_value !=''){ 
					if($is_edit =='')
					{
						/*	echo apply_filters('tmpl_custom_fields_listtitle','<h2 class="custom_field_headding">'.$tmpl_key.'</h2>');*/
						$field= get_post_meta(get_the_ID(),$k,true);	
						if($i==0 && $field!='' && $key != 'field_label'){echo apply_filters('tmpl_custom_fields_listtitle','<h2 class="custom_field_headding">'.$heading_key.'</h2>');;$i++;}
						if($field!='' && $key == 'field_label'){echo apply_filters('tmpl_custom_fields_listtitle','<h2 class="custom_field_headding">'.$val['label'].'</h2>');$i++;}
					}
						/* Show locations informations - country/state/city*/
						if($htmlvar_name['basic_inf']['post_city_id'] && $htmlvar_name['basic_inf']['post_city_id']['type'] =='multicity' && $k=='post_city_id'){
								global $wpdb,$country_table,$zones_table,$multicity_table;
								if(isset($_REQUEST['page']) && $_REQUEST['page'] =='preview'){
									$city= $_SESSION['custom_fields']['post_city_id'];		
									$country_id= @$_SESSION['custom_fields']['country_id'];		
									$zones_id= @$_SESSION['custom_fields']['zones_id'];		
								}else{
									$city= get_post_meta($post->ID,'post_city_id',true);		
									$zones_id= get_post_meta($post->ID,'zones_id',true);		
									$country_id= get_post_meta($post->ID,'country_id',true);		
								} 
								$cityinfo = $wpdb->get_results($wpdb->prepare("select cityname from $multicity_table where city_id =%d",$city ));
								if($country_id !='')
									$countryinfo = $wpdb->get_results($wpdb->prepare("select country_name from $country_table where country_id =%d",$country_id ));
								if($zones_id !='')
									$zoneinfo = $wpdb->get_results($wpdb->prepare("select zone_name from $zones_table where zones_id =%d",$zones_id ));
								
								if($countryinfo[0]->country_name){
									?><p class='<?php echo $val['style_class'];?>'><label><?php _e('Country',DOMAIN); ?>:</label> <strong><span><?php echo $countryinfo[0]->country_name; ?></span></strong></p>
								<?php }
									if($zoneinfo[0]->zone_name){ ?>
									<p class='<?php echo $val['style_class'];?>'><label><?php _e('State',DOMAIN); ?>:</label> <strong><span><?php echo $zoneinfo[0]->zone_name; ?></span></strong></p>
								<?php } 
									if($cityinfo[0]->cityname){ ?>
									<p class='<?php echo $val['style_class'];?>'><label><?php _e('City',DOMAIN); ?>:</label> <strong><span><?php echo $cityinfo[0]->cityname; ?></span></strong></p>
							<?php }
						}
					
					}
					if($val['type'] == 'multicheckbox' &&  ($field!="" || $is_edit==1)):
						$checkbox_value = '';				
						$option_values = explode(",",$val['option_values']);				
						$option_titles = explode(",",$val['option_title']);
						for($i=0;$i<count($option_values);$i++){ 
							if(isset($option_values[$i]) && $option_values[$i] !='' && count($field)>0){
								if($option_values[$i] !='' && is_array($field) && in_array($option_values[$i],$field)){
									if($option_titles[$i]!=""){
										$checkbox_value .= $option_titles[$i].', ';
									}else{
										$checkbox_value .= $option_values[$i].', ';
									}
								}
							}
						}
					?>
					<p class='<?php echo $val['style_class']; ?>'><?php if($key != 'field_label') { ?><label><?php echo $val['label']; ?>:&nbsp; </label><?php }?> <strong><span <?php if($is_edit==1):?>id="frontend_multicheckbox_<?php echo $k;?>" <?php endif;?> class="multicheckbox"><?php echo substr($checkbox_value,0,-2);?></span></strong></p>

				 <?php 
				elseif($val['type']=='radio' && ($field || $is_edit==1)):
					$option_values = explode(",",$val['option_values']);				
					$option_titles = explode(",",$val['option_title']);
					for($i=0;$i<count($option_values);$i++){
						if($field == $option_values[$i]){
							if($option_titles[$i]!=""){
								$rado_value = $option_titles[$i];
							}else{
								$rado_value = $option_values[$i];
							}
						?>
					   <p class='<?php echo $val['style_class'];?>'><?php if($key != 'field_label') { ?><label><?php echo $val['label']; ?>:&nbsp; </label><?php } ?><strong><span <?php if($is_edit==1):?>id="frontend_radio_<?php echo $k;?>" <?php endif;?>><?php echo $rado_value;?></span></strong></p>
					   <?php
						}
					}
				elseif($val['type']=='oembed_video' && ($field || $is_edit==1)):?>
					<p class='<?php echo $val['style_class'];?>'><?php if($key != 'field_label') { ?><label><?php echo $val['label']; ?>:&nbsp;</label><?php } ?>
						<?php if($is_edit==1):?>					
						<span id="frontend_edit_<?php echo $k;?>" class="frontend_oembed_video button" ><?php _e('Edit Video',DOMAIN);?></span>
						<input type="hidden" class="frontend_<?php echo $k;?>" name="frontend_edit_<?php echo $k;?>" value='<?php echo $field;?>' />
						<?php endif;?>
					<span class="frontend_edit_<?php echo $k;?>"><?php             
					$embed_video= wp_oembed_get( $field);            
					if($embed_video!=""){
						echo $embed_video;
					}else{
						echo $field;
					}
					?></span></p>
				<?php	
				endif;
				if($val['type']  == 'upload' || ($is_edit==1 && $val['type']  == 'upload'))
				{
					if($_SESSION['upload_file'][$name])
					{
						$upload_file=strtolower(substr(strrchr($_SESSION['upload_file'][$name],'.'),1));
					}
					else{
						$upload_file=strtolower(substr(strrchr($field,'.'),1));
					}
					 if($is_edit==1):?>
						<p class="<?php echo $val['style_class'];?>"><?php if($key != 'field_label') { ?><label><?php echo $val['label']; ?>: </label><?php } ?>
							<span class="entry-header-<?php echo $k;?> span_uploader" >
							<span style="display:none;" class="frontend_<?php echo $k;?>"><?php echo $field?></span>                            
							<span id="fronted_upload_<?php echo $k;?>" class="frontend_uploader button"  data-src="<?php echo $field?>">	                 	
								<span><?php echo __( 'Upload ', ADMINDOMAIN ).$val['label']; ?></span>
							</span>
							</span>
						</p>
					<?php elseif($upload_file=='jpg' || $upload_file=='jpeg' || $upload_file=='gif' || $upload_file=='png' || $upload_file=='jpg' ):?>
						<p class="<?php echo $val['style_class'];?>"><img src="<?php echo $field; ?>" /></p>
					<?php else:
						if(!empty($field))
						{
							?>
								<p class="<?php echo $val['style_class'];?>"><?php if($key != 'field_label') { ?><label><?php echo $val['label']; ?>: </label><?php } ?><a href="<?php echo $field; ?>" target="_blank"><?php echo basename($field); ?></a></p>
							<?php
						}
						endif;
				}
				if(($val['type'] != 'multicheckbox' && $val['type'] != 'radio' && $val['type'] != 'multicity' && $val['type']  != 'upload' && $val['type'] !='oembed_video') && ($field!='' || $is_edit==1)):				
				?>
					<p class='<?php echo $val['style_class'];?>'>
						<?php if($key != 'field_label') { ?><label><?php echo $val['label']; ?>:&nbsp;</label><?php } ?>
						<?php if($val['type']=='texteditor'):?>
						<div <?php if($is_edit==1):?>id="frontend_<?php echo $val['type'].'_'.$k;?>" class="frontend_<?php echo $k; if($val['type']=='texteditor'){ echo ' editblock';} ?>" <?php endif;?>>
							<?php echo $field;?>
						</div>
					<?php else: ?>
						<strong><span <?php if($is_edit==1):?>id="frontend_<?php echo $val['type'].'_'.$k;?>" contenteditable="true" class="frontend_<?php echo $k;?>" <?php endif;?>>
							<?php echo $field;?>
						</span></strong>
					<?php endif;?>
					</p>
				<?php
				endif; 

				}/* End If condition*/
				
				$j++;
			}/* End second foreach*/
			}
		}/* END First foreach*/
		echo '</div>';
	}
}
}
/*
	Show on detail page enable fields
*/
if(!function_exists('tmpl_show_on_detail')){
function tmpl_show_on_detail($cur_post_type,$heading_type){
	global $wpdb,$post;
	remove_all_actions('posts_where');
	add_filter('posts_join', 'custom_field_posts_where_filter');
	if($heading_type)
	 {
		$args = array( 'post_type' => 'custom_fields',
				'posts_per_page' => -1	,
				'post_status' => array('publish'),
				'meta_query' => array(
				 'relation' => 'AND',
				array(
					'key' => 'post_type_'.$cur_post_type.'',
					'value' => $cur_post_type,
					'compare' => '=',
					'type'=> 'text'
				),
				array(
					'key' => 'show_on_page',
					'value' =>  array('user_side','both_side'),
					'compare' => 'IN'
				),
				array(
					'key' => 'is_active',
					'value' =>  '1',
					'compare' => '='
				),
				array(
					'key' => 'heading_type',
					'value' =>  $heading_type,
					'compare' => '='
				),
				array(
					'key' => 'show_on_detail',
					'value' =>  '1',
					'compare' => '='
					)
				),
				'meta_key' => 'sort_order',
				'orderby' => 'meta_value',
				'order' => 'ASC'
		);
	 }
	else
	 {
		$args = array( 'post_type' => 'custom_fields',
			'posts_per_page' => -1	,
			'post_status' => array('publish'),
			'meta_query' => array(
			 'relation' => 'AND',
			array(
				'key' => 'post_type_'.$cur_post_type.'',
				'value' => $cur_post_type,
				'compare' => '=',
				'type'=> 'text'
			),
			array(
				'key' => 'show_on_page',
				'value' =>  array('user_side','both_side'),
				'compare' => 'IN'
			),
			array(
				'key' => 'is_active',
				'value' =>  '1',
				'compare' => '='
			),
			array(
				'key' => 'show_on_detail',
				'value' =>  '1',
				'compare' => '='
				)
			),
			'meta_key' => 'sort_order',
			'orderby' => 'meta_value',
			'order' => 'ASC'
		);
 
	 }
	$post_query = null;
	$upload = array();
	$post_query = new WP_Query($args);
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	return $post_query;
}
}

/*
 * Save data for upgrade post from transaction approved.
 */
add_action('tranaction_upgrade_post','tranaction_upgrade_post');
function tranaction_upgrade_post($orderId)
{
	$catids_arr = array();
	$my_post = array();
	$pid = $orderId; /* it will be use when going for RENEW */
	$upgrade_post = get_post_meta($pid,'upgrade_data',true);
	$last_postid = $pid;
	$alive_days = $upgrade_post['alive_days'];
	$payment_method = get_post_meta($last_postid,'upgrade_method',true);
	$coupon = @$upgrade_post['add_coupon'];
	$featured_type = @$upgrade_post['featured_type'];
	$payable_amount = @$upgrade_post['total_price'];
	$post_tax = fetch_page_taxonomy($upgrade_post['cur_post_id']);		
	/*delete custom fields */
	$heading_type = fetch_heading_per_post_type(get_post_type($last_postid));
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' =>get_post_type($last_postid),'public'   => true, '_builtin' => true ));
	$taxonomy = $taxonomies[0];
	if(count($heading_type) > 0)
	{
		foreach($heading_type as $_heading_type){
			$upgrade_custom_metaboxes[] = get_post_custom_fields_templ_plugin(get_post_type($last_postid),$upgrade_post['category'],$taxonomy,$_heading_type,'',$upgrade_post['pkg_id']);
			/* custom fields for custom post type..  */
		}
	}else{
		$upgrade_custom_metaboxes[] = get_post_custom_fields_templ_plugin($post_type,$upgrade_post['category'],$taxonomy,'','',$upgrade_post['pkg_id']);/*custom fields for custom post type..*/
	}

	$terms = wp_get_post_terms( $last_postid, $taxonomy,  array("fields" => "ids") ); 
	$pkg_id = get_post_meta($pid,'pkg_id',true);
	if(count($heading_type) > 0)
	{
		foreach($heading_type as $_heading_type){
			$custom_metaboxes[] = get_post_custom_fields_templ_plugin(get_post_type($last_postid),$terms,$taxonomy,$_heading_type,'',$pkg_id);/*custom fields for custom post type..*/
		}
	}else{
		$custom_metaboxes[] = get_post_custom_fields_templ_plugin($post_type,$terms,$taxonomy,'','',$pkg_id);/*custom fields for custom post type..*/
	}
	
	for($h=0;$h<count($heading_type);$h++)
	{
		$result[] = array_diff_key($custom_metaboxes[$h],$upgrade_custom_metaboxes[$h]);
	}
	for($r=0;$r<count($result);$r++)
	{
		$custom_fields_name = array_keys($result[$r]);
		for($i=0;$i<count($custom_fields_name);$i++)
		{
			$custom_fields_value = get_post_meta($last_postid,$custom_fields_name[$i],true);
			delete_post_meta($last_postid,$custom_fields_name[$i],$custom_fields_value);
		}
	}
	/**/
	/* Here array separated by category id and price amount */
	if($upgrade_post['category'])
	{
		$category_arr = $upgrade_post['category'];
		foreach($category_arr as $_category_arr)
		 {
			$category[] = explode(",",$_category_arr);
		 }
		foreach($category as $_category)
		 {
			 $post_category[] = $_category[0];
			 $category_price[] = $_category[1];
		 }
	}

	if($payable_amount <= 0)
	{	
		if($upgrade_post['package_select'] !='')
		{
			global $monetization;
			$post_default_status = $monetization->templ_get_packaget_post_status($current_user->ID, get_post_meta($custom_fields['cur_post_id'],'submit_post_type',true));
			if($post_default_status =='recurring'){
				$post = get_post($custom_fields['cur_post_id']);
				
				$post_default_status = $monetization->templ_get_packaget_post_status($current_user->ID, $post->post_parent,'submit_post_type',true);
				if($post_default_status =='trash'){
					$post_default_status ='draft';
				}
			}
		}else{
			$post_default_status = fetch_posts_default_status();
		}
	}else
	{
		$post_default_status = 'draft';
	}
	
	
			$submit_post_type = get_post_meta($custom_fields['cur_post_id'],'submit_post_type',true);
			$package_post=get_post_meta($upgrade_post['package_select'],'limit_no_post',true);
			/* $user_limit_post=get_user_meta($current_user_id,$submit_post_type.'_list_of_post',true); */
			$user_limit_post=get_user_meta($current_user_id,'total_list_of_post',true);
		
				/* $limit_post=get_user_meta($current_user_id,$submit_post_type.'_list_of_post',true); */
				global $monetization;
				$listing_price_info = $monetization->templ_get_price_info($upgrade_post['pkg_id']);			
				update_post_meta($last_postid,'package_select',$upgrade_post['pkg_id']);
				update_post_meta($last_postid,'pkg_id',$upgrade_post['pkg_id']);
				update_post_meta($last_postid,'paid_amount',$upgrade_post['total_price']);
				update_post_meta($last_postid,'alive_days',$listing_price_info['alive_days']);

				$limit_post=get_user_meta($current_user_id,'total_list_of_post',true);				
				update_user_meta($current_user_id,$submit_post_type.'_list_of_post',$limit_post+1);
				update_user_meta($current_user_id,'total_list_of_post',$limit_post+1);
				update_user_meta($current_user_id,$submit_post_type.'_package_select',$upgrade_post['package_select']);
				update_user_meta($current_user_id,'package_selected',$upgrade_post['package_select']);
				
			foreach($upgrade_post as $key=>$val)
			{ 
				if($key != 'category' && $key != 'paid_amount' && $key != 'alive_days' && $key != 'post_title' && $key != 'post_content' && $key != 'imgarr' && $key != 'Update' && $key != 'post_excerpt' && $key != 'alive_days')
				  {
					if($key=='recurrence_bydays')
					{ 
						$val=implode(',',$val);
						update_post_meta($last_postid, $key, $val);
					}
					else
					{
						update_post_meta($last_postid, $key, $val);
					}
					
				  }
			}

			/* set post categories start */
			wp_set_post_terms( $last_postid,'',$post_tax,false);
			if($post_category){
			foreach($post_category as $_post_category)
			 { 
				if(taxonomy_exists($post_tax)):
					wp_set_post_terms( $last_postid,$_post_category,$post_tax,true);
				endif;
			 }
			} 
			/* set post categories end */
		
		 
		 /* Condition for Edit post */
			if( @$pid){
				$post_default_status = get_post_status($pid);
			}else{
				$post_default_status = 'publish';
			}
		
			if(class_exists('monetization')){
			
					global $monetization;
					$monetize_settings = $monetization->templ_set_price_info($last_postid,$pid,$payable_amount,$alive_days,$payment_method,$coupon,$featured_type);
	
			}
}
?>