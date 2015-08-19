<?php
/* Custom fields function - Template custom fields functions */
/*
 * include the generalization css in header
 */
add_action( 'wp_enqueue_scripts', 'tevolution_general_function' );
function tevolution_general_function(){	
	if(is_single()){
		wp_enqueue_script("generalization-basic",TEMPL_PLUGIN_URL.'tmplconnector/monetize/templatic-generalization/js/basic.js','','',true);
	}
}

/* add css in $tev_css global variable for detail page */

add_action( 'tevolution_css', 'tevolution_general_css' ,15);

/* return the geralize css name */

function tevolution_general_css(){
	global $tev_css;
	if(is_single()){
	if(!empty($tev_css)){
		$tev_css = array_merge($tev_css, array(TEMPL_PLUGIN_URL.'tmplconnector/monetize/templatic-generalization/css/style.css'));
	}else{
		$tev_css = array(TEMPL_PLUGIN_URL.'tmplconnector/monetize/templatic-generalization/css/style.css');
	}
	}
}
/*
 *To Include the sent to friend form in footer,It will open after click on sent to friend button
 */
function send_email_to_friend()
{	
	wp_reset_postdata();
	include_once(TEMPL_MONETIZE_FOLDER_PATH."templatic-generalization/popup_frms.php");
}
/*
 * include popup_inquiry_frm.php
 */
function send_inquiry()
{
	wp_reset_postdata();
	include_once(TEMPL_MONETIZE_FOLDER_PATH."templatic-generalization/popup_inquiry_frm.php");	
}
/* start code to add add to favourites on author dash board */
add_action('init','tevolution_author_favourites_tab');
function tevolution_author_favourites_tab(){
	if(current_theme_supports('tevolution_my_favourites')){		
		add_action('tevolution_author_tab','tmpl_dashboard_favourites_tab'); /* to display tab */
	}
}

/* 
	Return the category name in custom fields.
*/
function display_custom_category_name($custom_metaboxes,$session_variable,$taxonomy){
	foreach($custom_metaboxes as $key=>$val) {
		$type = $val['type'];	
		$site_title = $val['label'];	
	?>
	
	   <?php if($type=='post_categories')
		{ 
		 ?>
		 <div class="form_row clearfix categories_selected">
			<label><?php echo __('Category','templatic'); ?></label>
             <div class="category_label">
			 <?php 			
				 for($i=0;$i<count($session_variable);$i++)
				 {
					if($i == (count($session_variable) -1 ))
						$sep = '';
					else
						$sep = ',';
					$category_name = get_term_by('id', $session_variable[$i], $taxonomy);
					if($category_name)
					 {
						echo "<strong>".$category_name->name.$sep."</strong>";
						echo '<input type="hidden"  value="'.$session_variable[$i].'" name="category[]">';
						echo '<input type="hidden"  value="'.$session_variable[$i].'" name="category_new[]">';
					 }
				}
				if(isset($_SESSION['custom_fields']['cur_post_id']) && count($_SESSION['custom_fields']['cur_post_id']) > 0 && !isset($_REQUEST['cur_post_id']) && $_REQUEST['category'] == '')
					$id = $_SESSION['custom_fields']['cur_post_id'];
				elseif(isset($_REQUEST['cur_post_id']) && count($_REQUEST['cur_post_id']) > 0)
					$id = $_REQUEST['cur_post_id'];
				$permalink = get_permalink( $id );
		?></div>
		<?php
		/* Go back and edit link */
		if(strpos($permalink,'?'))
		{
			  if($_REQUEST['pid']){ $postid = '&amp;pid='.$_REQUEST['pid']; }
				 $gobacklink = $permalink."&backandedit=1&amp;".$postid;
		}else{
			if($_REQUEST['pid']){ $postid = '&amp;pid='.$_REQUEST['pid']; }
			$gobacklink = $permalink."?backandedit=1";
		}
			if(!isset($_REQUEST['pid']) || (isset($_REQUEST['renew']) && $_REQUEST['renew'] == 1)){
			?>
			  <a href="<?php echo $gobacklink; ?>" class="btn_input_normal fl" ><?php _e('Go back and edit','templatic');?></a>
			<?php } ?>
		
		</div>   	
		<?php }	
	}
}

/* With the help of User custom fields array, To fetch out the user custom fields */

function display_usermeta_fields($user_meta_array)
{
	$form_fields_usermeta	= $user_meta_array;
	global $user_validation_info;
	$user_validation_info = array();
  foreach($form_fields_usermeta as $key=>$val)
	{
		
		if($key!='user_email' && $key!='user_fname')
			continue;
	$str = ''; $fval = '';
	$field_val = $key.'_val';
	if(isset($_REQUEST['user_fname']) || (!isset($_REQUEST['backandedit'])  && $_REQUEST['backandedit'] == '')){ $field_val = $_REQUEST[$key]; } elseif(isset($_REQUEST['backandedit']) && $_REQUEST['backandedit'] == '1' ) {$field_val = $_SESSION['custom_fields'][$key]; }
	if(@$field_val){ $fval = $field_val; }else{ $fval = $val['default']; }
   
	if($val['is_require'])
	{
		$user_validation_info[] = array(
								   'name'	=> $key,
								   'espan'	=> $key.'_error',
								   'type'	=> $val['type'],
								   'text'	=> $val['label'],
								   );
	}
	if($val['type']=='text')
	{
		$str = '<input name="'.$key.'" type="text" '.$val['extra'].' value="'.$fval.'">';
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';
		}
	}elseif($val['type']=='hidden')
	{
		$str = '<input name="'.$key.'" type="hidden" '.$val['extra'].' value="'.$fval.'">';	
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='textarea')
	{
		$str = '<textarea name="'.$key.'" '.$val['extra'].'>'.$fval.'</textarea>';	
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='file')
	{
		$str = '<input name="'.$key.'" type="file" '.$val['extra'].' value="'.$fval.'">';
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='include')
	{
		$str = @include_once($val['default']);
	}else
	if($val['type']=='head')
	{
		$str = '';
	}else
	if($val['type']=='date')
	{
		?>
         <script type="text/javascript" async >	
				jQuery(function(){
				var pickerOpts = {
						showOn: "both",
						dateFormat: 'yy-mm-dd',
						monthNames: objectL11tmpl.monthNames,
						monthNamesShort: objectL11tmpl.monthNamesShort,
						dayNames: objectL11tmpl.dayNames,
						dayNamesShort: objectL11tmpl.dayNamesShort,
						dayNamesMin: objectL11tmpl.dayNamesMin,
						isRTL: objectL11tmpl.isRTL,
						buttonText: '<i class="fa fa-calendar"></i>',
					};	
					jQuery("#<?php echo $key;?>").datepicker(pickerOpts);					
				});
			</script>
        <?php
		$str = '<input name="'.$key.'" id="'.$key.'" type="text" '.$val['extra'].' value="'.$fval.'">';			
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='catselect')
	{
		$term = get_term( (int)$fval, CUSTOM_CATEGORY_TYPE1);
		$str = '<select name="'.$key.'" '.$val['extra'].'>';
		$args = array('taxonomy' => CUSTOM_CATEGORY_TYPE1);
		$all_categories = get_categories($args);
		foreach($all_categories as $key => $cat) 
		{
		
			$seled='';
			if($term->name==$cat->name){ $seled='selected="selected"';}
			$str .= '<option value="'.$cat->name.'" '.$seled.'>'.$cat->name.'</option>';	
		}
		$str .= '</select>';
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='catdropdown')
	{
		$cat_args = array('name' => 'post_category', 'id' => 'post_category_0', 'selected' => $fval, 'class' => 'textfield', 'orderby' => 'name', 'echo' => '0', 'hierarchical' => 1, 'taxonomy'=>CUSTOM_CATEGORY_TYPE1);
		$cat_args['show_option_none'] = __('Select Category','templatic');
		$str .=wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='select')
	{
		$str = '<select name="'.$key.'" '.$val['extra'].'>';
		 $str .= '<option value="" >'.PLEASE_SELECT.' '.$val['label'].'</option>';	
		$option_values_arr = explode(',', $val['options']);
		for($i=0;$i<count($option_values_arr);$i++)
		{
			$seled='';
			
			if($fval==$option_values_arr[$i]){ $seled='selected="selected"';}
			$str .= '<option value="'.$option_values_arr[$i].'" '.$seled.'>'.$option_values_arr[$i].'</option>';	
		}
		$str .= '</select>';
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='catcheckbox')
	{
		$fval_arr = explode(',',$fval);
		$str .= $val['tag_before'].get_categories_checkboxes_form(CUSTOM_CATEGORY_TYPE1,$fval_arr).$oval.$val['tag_after'];
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='catradio')
	{
		$args = array('taxonomy' => CUSTOM_CATEGORY_TYPE1);
		$all_categories = get_categories($args);
		foreach($all_categories as $key1 => $cat) 
		{
			
			
				$seled='';
				if($fval==$cat->term_id){ $seled='checked="checked"';}
				$str .= $val['tag_before'].'<input name="'.$key.'" type="radio" '.$val['extra'].' value="'.$cat->name.'" '.$seled.'> '.$cat->name.$val['tag_after'];	
			
		}
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='checkbox')
	{
		if($fval){ $seled='checked="checked"';}
		$str = '<input name="'.$key.'" type="checkbox" '.$val['extra'].' value="1" '.$seled.'>';
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='upload')
	{
		
		$str = '<input name="'.$key.'" type="file" '.$val['extra'].' '.$uclass.' value="'.$fval.'" > ';
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}
	else
	if($val['type']=='radio')
	{
		$options = $val['options'];
		if($options)
		{
			$option_values_arr = explode(',',$options);
			for($i=0;$i<count($option_values_arr);$i++)
			{
				$seled='';
				if($fval==$option_values_arr[$i]){$seled='checked="checked"';}
				$str .= $val['tag_before'].'<input name="'.$key.'" type="radio" '.$val['extra'].'  value="'.$option_values_arr[$i].'" '.$seled.'> '.$option_values_arr[$i].$val['tag_after'];
			}
			if($val['is_require'])
			{
				$str .= '<span id="'.$key.'_error"></span>';	
			}
		}
	}else
	if($val['type']=='multicheckbox')
	{
		$options = $val['options'];
		if($options)
		{  $chkcounter = 0;
			
			$option_values_arr = explode(',',$options);
			for($i=0;$i<count($option_values_arr);$i++)
			{
				$chkcounter++;
				$seled='';
				$fval_arr = explode(',',$fval);
				if(in_array($option_values_arr[$i],$fval_arr)){ $seled='checked="checked"';}
				$str .= $val['tag_before'].'<input name="'.$key.'[]"  id="'.$key.'_'.$chkcounter.'" type="checkbox" '.$val['extra'].' value="'.$option_values_arr[$i].'" '.$seled.'> '.$option_values_arr[$i].$val['tag_after'];
			}
			if($val['is_require'])
			{
				$str .= '<span id="'.$key.'_error"></span>';	
			}
		}
	}
	else
	if($val['type']=='packageradio')
	{
		$options = $val['options'];
		foreach($options as $okey=>$oval)
		{
			$seled='';
			if($fval==$okey){$seled='checked="checked"';}
			$str .= $val['tag_before'].'<input name="'.$key.'" type="radio" '.$val['extra'].' value="'.$okey.'" '.$seled.'> '.$oval.$val['tag_after'];	
		}
		if($val['is_require'])
		{
			$str .= '<span id="'.$key.'_error"></span>';	
		}
	}else
	if($val['type']=='geo_map')
	{
		do_action('templ_submit_form_googlemap');	
	}else
	if($val['type']=='image_uploader')
	{
		do_action('templ_submit_form_image_uploader');	
	}
	
	if (function_exists('icl_register_string')) {		
			icl_register_string('templatic', $val['type'].'_'.$key,$val['label']);	
			$val['label'] = icl_t('templatic', $val['type'].'_'.$key,$val['label']);
	   }
	if($val['is_require'])
	{
		$label = '<label>'.$val['label'].' <span class="indicates">*</span> </label>';
	}else
	{
		$label = '<label>'.$val['label'].'</label>';
	}
	if($val['type']=='texteditor')
			{
				echo $val['outer_st'].$label.$val['tag_st'];
				 echo $val['tag_before'].$val['tag_after'];
            /* default settings  */
					$settings =   array(
						'wpautop' => false,
						'media_buttons' => $media_pro,
						'textarea_name' => $key,
						'textarea_rows' => apply_filters('tmpl_wp_editor_rows',get_option('default_post_edit_rows',6)), /* rows="..."*/
						'tabindex' => '',
						'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>',
						'editor_class' => '',
						'toolbar1'=> 'bold,italic,underline,bullist,numlist,link,unlink,forecolor,undo,redo',
						'editor_height' => '150',
						'teeny' => false,
						'dfw' => false,
						'tinymce' => true,
						'quicktags' => false
					);					
					if(isset($fval) && $fval != '') 
					{  $content=$fval; }
					else{$content= $fval; } 				
					wp_editor( $content, $key, $settings);				
			
					if($val['is_require'])
					{
						$str .= '<span id="'.$key.'_error"></span>';	
					}
				echo $str.$val['tag_end'].$val['outer_end'];
			}else{	
				echo $val['outer_st'].$label.$val['tag_st'].$str.$val['tag_end'].$val['outer_end'];
			}
 }
}
/*
	put this function where you want to use captcha
*/
function templ_captcha_integrate($form)
{
	$tmpdata = get_option('templatic_settings');
	$display = @$tmpdata['user_verification_page'];
	$recaptcha=0;
		
	if(@in_array($form,$display))
	{
		$recaptcha=1;
		?>
		<div id="captcha_div" class="captcha_div"></div>            
<?php } ?>
 <script type="text/javascript" async>var recaptcha='<?php echo $recaptcha?>';</script>
 <?php
}

/*	this function will fetch the default status of the paid posts set by the admin in backend general settings */
function fetch_posts_default_paid_status()
{
	$tmpdata = get_option('templatic_settings');
	$post_default_status = $tmpdata['post_default_status_paid'];
	if($post_default_status ==''){
		$post_default_status ='publish';
	}
	return $post_default_status;
}

/*
* searching filter for custom fields return the where condition 
*/
add_filter('posts_where', 'templ_searching_filter_where');
function templ_searching_filter_where($where){
	if(is_search() && @$_REQUEST['adv_search'] ==1)
	{
		global $wpdb;
		$serch_post_types = $_REQUEST['post_type'];
		$s = get_search_query();
		$custom_metaboxes = templ_get_all_custom_fields($serch_post_types,'','user_side','1');
		foreach($custom_metaboxes as $key=>$val) {
		$name = $key;
			if($_REQUEST[$name]){ 
				$value = $_REQUEST[$name];
				if($name == 'proprty_desc' || $name == 'event_desc'){
					$where .= " AND ($wpdb->posts.post_content like \"%$value%\" )";
				} else if($name == 'property_name'){
					$where .= " AND ($wpdb->posts.post_title like \"%$value%\" )";
				}else {
					$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$name' and ($wpdb->postmeta.meta_value like \"%$value%\" ))) ";
					/* Placed "AND" instead of "OR" because of Vedran said results are ignoring address field */
				}
			}
		}
		
		 /* Added for tags searching */
		if(is_search() && !@$_REQUEST['catdrop']){
			$where .= " OR  ($wpdb->posts.ID in (select p.ID from $wpdb->terms c,$wpdb->term_taxonomy tt,$wpdb->term_relationships tr,$wpdb->posts p ,$wpdb->postmeta t where c.name like '".$s."' and c.term_id=tt.term_id and tt.term_taxonomy_id=tr.term_taxonomy_id and tr.object_id=p.ID and p.ID = t.post_id and p.post_status = 'publish' group by  p.ID))";
		}
	}
	return $where;
}

/*
 * Advance search function 
 */
if(!is_admin())
{
	add_action('init', 'advance_search_template_function_',11);
}
function advance_search_template_function_(){
	
	add_action('pre_get_posts', 'advance_search_template_function',11);
	
	
}
/*
 * call the filter for advance search widget.
 */
function advance_search_template_function($query){		

	if(is_search() && (isset($_REQUEST['search_template']) && $_REQUEST['search_template']==1) )
	{		
		remove_all_actions('posts_where');
		do_action('advance_search_action');
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			 global $sitepress;
			 add_filter('posts_join', array($sitepress,'posts_join_filter'), 10, 2);
			 add_filter('posts_where', array($sitepress,'posts_where_filter'), 10, 2);
		}
		add_filter('posts_where', 'advance_search_template_where');	
				
	}else
	{
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			remove_filter('posts_join', 'wpml_search_language');
		}
	}
}
/*
 * return where for advance search widget
 */
function advance_search_template_where($where)
{	
	if(isset($_REQUEST['search_template']) && $_REQUEST['search_template']==1 && is_search())
	{
		global $wpdb,$current_cityinfo;
		$post_type=$_REQUEST['post_type'];		
		$tag_s=$_REQUEST['tag_s'];
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));

		if(isset($_REQUEST['todate']) && $_REQUEST['todate']!=''):
			$todate = trim($_REQUEST['todate']);		
		else:
			$todate ='';
		endif;
		if(isset($_REQUEST['frmdate']) && $_REQUEST['frmdate']!=''):
			$frmdate = trim($_REQUEST['frmdate']);
		else:
			$frmdate ='';
		endif;
		if(isset($_REQUEST['articleauthor']) && $_REQUEST['articleauthor']!=''):
			$articleauthor = trim($_REQUEST['articleauthor']);
		else:
			$articleauthor = '';
		endif;
		
		if(isset($_REQUEST['exactyes']) && $_REQUEST['exactyes']!=''):
			$exactyes = trim($_REQUEST['exactyes']);
		else:
			$exactyes ='';
		endif;
		
		if(isset($_REQUEST['todate']) && $_REQUEST['todate'] != ""){
			$todate = $_REQUEST['todate'];
			$todate= explode('/',$todate);
			$todate = $todate[2]."-".$todate[0]."-".$todate[1];
			
		}
		if(isset($_REQUEST['frmdate']) && $_REQUEST['frmdate'] != ""){
			$frmdate = $_REQUEST['frmdate'];
			$frmdate= explode('/',$frmdate);
			$frmdate = $frmdate[2]."-".$frmdate[0]."-".$frmdate[1];
		}
		
		if(is_plugin_active( 'Tevolution-Events/events.php') && (isset($_REQUEST['post_type']) && $_REQUEST['post_type']=='event'))
		{
			add_filter('posts_orderby', 'event_manager_filter_orderby',11);
		}
		
		if($todate!="" && $frmdate=="")
		{
			$where .= " AND   DATE_FORMAT($wpdb->posts.post_date,'%Y-%m-%d %G:%i:%s') >='".$todate."'";
		}
		else if($frmdate!="" && $todate=="")
		{
			
			$where .= " AND  DATE_FORMAT($wpdb->posts.post_date,'%Y-%m-%d %G:%i:%s') <='".$frmdate."'";
		}
		else if($todate!="" && $frmdate!="")
		{
			$where .= " AND  DATE_FORMAT($wpdb->posts.post_date,'%Y-%m-%d %G:%i:%s') BETWEEN '".$todate."' and '".$frmdate."'";
			
		}
		if($articleauthor!="" && $exactyes!=1)
		{
			$where .= " AND  $wpdb->posts.post_author in (select $wpdb->users.ID from $wpdb->users where $wpdb->users.display_name  like '".$articleauthor."') ";
		}
		if($articleauthor!="" && $exactyes==1)
		{
			$where .= " AND  $wpdb->posts.post_author in (select $wpdb->users.ID from $wpdb->users where $wpdb->users.display_name  = '".$articleauthor."') ";
		}		
		/* search custom field */
		if(isset($_REQUEST['search_custom']) && is_array($_REQUEST['search_custom']))
		{
			foreach($_REQUEST['search_custom'] as $key=>$value)
			{
				if($_REQUEST[$key]!="" && $key != 'category' && $key != 'st_date' && $key != 'end_date'  && $value!='slider_range' && $value!='multicheckbox' && $value!='min_max_range_select' && $value!='geo_map')
				{
					/* exclude category, start date, end date, slider range, multicheckbox field and include other custom fields type query where concate */
					if(!strstr($key,'_radio')) /*all custom field type query where concatenate except radio field */
					{
						if(is_array($_REQUEST[$key]))
						{
							foreach($_REQUEST[$key] as $val)
							{
									$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and ($wpdb->postmeta.meta_value like \"%$val%\" ))) ";
							}
						}
						else
						{
							if(strtolower($_REQUEST[$key])!='any'){
								$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and ($wpdb->postmeta.meta_value like \"%$_REQUEST[$key]%\" ))) ";					
							}
						}	
					}
					else /*only radio custom field query where concatenate */
					{
						$key_value = explode('_radio',$key);
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key_value[0]' and ($wpdb->postmeta.meta_value like \"%$_REQUEST[$key]%\" ))) ";	
					}					
					
				}elseif($value=='slider_range' || $value=='min_max_range'){
					/*Rnage type custom field query where concatenate */
					if($value=='min_max_range'){						
						$min_value=trim($_REQUEST[$key.'_min']);
						$max_value=trim($_REQUEST[$key.'_max']);
					}else{
						$key_value = explode('-',$_REQUEST[$key]);
						$min_value=trim($key_value[0]);
						$max_value=trim($key_value[1]);
					}
					if($min_value!='' && $max_value!=''){
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and ($wpdb->postmeta.meta_value >= $min_value and  $wpdb->postmeta.meta_value <= $max_value))) ";
					}elseif($min_value!=''){
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and $wpdb->postmeta.meta_value >= $min_value)) ";
					}elseif($max_value!=''){
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and $wpdb->postmeta.meta_value <= $max_value)) ";
					}

				}elseif($value=='min_max_range_select'){
					$min_value=trim($_REQUEST[$key.'_min']);
					$max_value=trim($_REQUEST[$key.'_max']);
					if($min_value!='' && $max_value!='' && strtolower($min_value)!='any' && strtolower($max_value)!='any'){
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and ($wpdb->postmeta.meta_value >= $min_value and  $wpdb->postmeta.meta_value <= $max_value))) ";
					}elseif($min_value!='' && strtolower($min_value)!='any'){
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and $wpdb->postmeta.meta_value >= $min_value)) ";
					}elseif($max_value!='' && strtolower($max_value)!='any'){
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and $wpdb->postmeta.meta_value <= $max_value)) ";
					}
					
					
				}elseif($value=='geo_map'){
					
					if(!is_plugin_active('Directory-ProximitySearch/proximitysearch.php') && !is_plugin_active('Tevolution-ProximitySearch/proximitysearch.php'))
					{
						if($_REQUEST[$key] &&  !isset($_REQUEST['radius'])){
							$where .= " AND ($wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key ='$key' and pm.meta_value like \"%$_REQUEST[$key]%\") )";
						}elseif($_REQUEST[$key] &&  (isset($_REQUEST['radius']) && $_REQUEST['radius']=='')){
							$where .= " AND ($wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key ='$key' and pm.meta_value like \"%$_REQUEST[$key]%\") )";
						}
					}
					/* Distance wise search results */
					if($value=='geo_map' && isset($_REQUEST['radius']) && $_REQUEST['radius']!='' && isset($_REQUEST['radius_type']) && $_REQUEST['radius_type']!=''){
						
						$search = str_replace(' ','',$_REQUEST[$key]);
						if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; }
						$arg=array('method' => 'POST',
							 'timeout' => 45,
							 'redirection' => 5,
							 'httpversion' => '1.0',
							 'blocking' => true,			 			 
							 'user-agent' => 'WordPress/'. $wp_version .'; '. home_url(),
							 'cookies' => array()
						);	
						$response = wp_remote_get($http.'maps.google.com/maps/api/geocode/json?address='.$search.'&sensor=false',$arg );
						$output=json_decode($response['body']);	
						
						if(!is_wp_error( $response )) {
							/* if multiple results are geting, then get current countru related result */
							if(count($output->results) > 1){
								$d = 1;
								foreach($output->results as $cityinfo){
								
									foreach($cityinfo->address_components as $address_components){
											
											/* check if current country iso code with results */
											if($current_cityinfo['country_iso2'] == $address_components->short_name){
												$cordinates = $cityinfo->geometry->location;
											}
									}
									$d++;
								}
								if(!empty($cordinates)){
									if(isset($cordinates->lat))
										$lat = $cordinates->lat;
									if(isset($cordinates->lng))
										$long = $cordinates->lng;
								}else{
									
									if(isset($output->results[0]->geometry->location->lat))
										$lat = $output->results[0]->geometry->location->lat;
									if(isset($output->results[0]->geometry->location->lng))
										$long = $output->results[0]->geometry->location->lng;
								
								}
								
							}else{
								
								if(isset($output->results[0]->geometry->location->lat))
									$lat = $output->results[0]->geometry->location->lat;
								if(isset($output->results[0]->geometry->location->lng))
									$long = $output->results[0]->geometry->location->lng;
									
							}
						
						}
						$miles = @$_REQUEST['radius'];						
						
						if(isset($_REQUEST['radius_type']) && $_REQUEST['radius_type']== strtolower('Kilometer')){
							$miles = @$_REQUEST['radius'] / 0.621;
						}else{
							$miles = @$_REQUEST['radius'];	
						}
						$tbl_postcodes = $wpdb->prefix . "postcodes";
						
						
						if(!empty($_REQUEST['post_type']) )
						{
							$post_type1='';
							
							if(count($_REQUEST['post_type']) >1){
								$post_type = implode(",",$_REQUEST['post_type']);
							}else{
								$post_type = $_REQUEST['post_type'];
							}
							$post_type_array = explode(",",$post_type);
							$sep = ",";
							for($i=0;$i<count($post_type_array);$i++)
							{
								if($i == (count($post_type_array) - 1))
								{
									$sep = "";
								}
								if(isset($post_type_array[$i]))
								$post_type1 .= "'".$post_type_array[$i]."'".$sep;
							}
						}
						
						if($lat!='' && $long!='' && (isset($_REQUEST['radius']) && $_REQUEST['radius']!='')){
							if (function_exists('icl_register_string')) {
								if($lat !='' && $long !=''){
									$where .= " AND ($wpdb->posts.ID in (SELECT post_id FROM  $tbl_postcodes WHERE $tbl_postcodes.post_type in (".$post_type1.")  AND truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) <= ".$miles." ORDER BY truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) ASC))";
								}
							}
							else
							{
								if($lat !='' && $long !=''){
									$where .= " AND ($wpdb->posts.ID in (SELECT post_id FROM  $tbl_postcodes WHERE $tbl_postcodes.post_type in (".$post_type1.") AND truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) <= ".$miles." ORDER BY truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) ASC))";
								}
							}
						}
							
					}
					/*finish distance wise  search results */
				
				}else{
					/*Multicheckbox custom field query where concate */
					if(!empty($_REQUEST[$key]) && $key != 'st_date' && $key != 'end_date' && $value!='slider_range' &&  $value=='multicheckbox'){
						$where.=" AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='".$key."' AND (";
						$count=count($_REQUEST[$key]);
						$c=1;	
						foreach($_REQUEST[$key] as $val){
							if($c<$count){
								$seprator='OR';	
							}else{
								$seprator='';	
							}
							$where .= "  ($wpdb->postmeta.meta_value like '%".$val."%' ) $seprator ";
							$c++;
						}						
						$where.=')))';
					}
				}
				
				if($_REQUEST[$key]!="" && $key == 'st_date' ){
					$templatic_current_tab = isset($event_manager_setting['templatic-current_tab'])? $event_manager_setting['templatic-current_tab']:'';
						if(!isset($_REQUEST['etype']))			
						{	
							$_REQUEST['etype']=($templatic_current_tab == '')?'current':$templatic_current_tab;
							$to_day = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
						}
						
						if(isset($_REQUEST['sortby']) && $_REQUEST['sortby']!=''){
							$where .= "  AND $wpdb->posts.post_title like '".$_REQUEST['sortby']."%'";
						}
						
						if(isset($_REQUEST['etype']) && $_REQUEST['etype']=='upcoming')
						{				
							$today = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
							$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_st_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') >'".$today."')) ";
						}			
						elseif(isset($_REQUEST['etype']) && $_REQUEST['etype']=='past')
						{				
							$today = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
							$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_end_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') < '".$today."')) ";
						}elseif($_REQUEST['etype']=='current')
						{
							$today = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
							$where .= "  AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_st_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') <='".$today."')) AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_end_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') >= '".$today."')) ";
						}
						
						if(isset($_REQUEST['st_date']) && $_REQUEST['st_date'] != '' && $_REQUEST['end_date'] == ''){
							$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='st_date' and ($wpdb->postmeta.meta_value =  '".$_REQUEST['st_date']."'))) ";
						} else if(isset($_REQUEST['end_date']) && $_REQUEST['end_date'] != '' && $_REQUEST['st_date']== '' ){
							$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='st_date' and ($wpdb->postmeta.meta_value =  '".$_REQUEST['st_date']."'))) ";
						}elseif($_REQUEST['end_date'] != '' && $_REQUEST['st_date'] != ''){
							$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='st_date' and ($wpd
							b->postmeta.meta_value BETWEEN  '".$_REQUEST['st_date']."' AND '".$_REQUEST['end_date']."' ))) ";
						}
				}
				if($_REQUEST[$key]!="" && $key == 'end_date'){
					 $templatic_current_tab = isset($event_manager_setting['templatic-current_tab'])? $event_manager_setting['templatic-current_tab']:'';
						if(!isset($_REQUEST['etype']))			
						{	
							$_REQUEST['etype']=($templatic_current_tab == '')?'current':$templatic_current_tab;
							$to_day = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
						}
						
						if(isset($_REQUEST['sortby']) && $_REQUEST['sortby']!=''){
							$where .= "  AND $wpdb->posts.post_title like '".$_REQUEST['sortby']."%'";
						}
						
						if(isset($_REQUEST['etype']) && $_REQUEST['etype']=='upcoming')
						{				
							$today = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
							$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_st_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') >'".$today."')) ";
						}			
						elseif(isset($_REQUEST['etype']) && $_REQUEST['etype']=='past')
						{				
							$today = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
							$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_end_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') < '".$today."')) ";
						}elseif($_REQUEST['etype']=='current')
						{
							$today = date_i18n('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')));
							$where .= "  AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_st_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') <='".$today."')) AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='set_end_time' and date_format($wpdb->postmeta.meta_value,'%Y-%m-%d %H:%i:%s') >= '".$today."')) ";
						}
						$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='end_date' and ($wpdb->postmeta.meta_value BETWEEN  '".$_REQUEST['st_date']."' AND '".$_REQUEST['end_date']."' ))) ";
				}
			}
		}
		/* finish custom field	 */		
		
		if(isset($_REQUEST['category']) && $_REQUEST['category']!="" &&  $_REQUEST['category'] !=0)
		{
			
			$scat = $_REQUEST['category'];
			$where .= " AND  $wpdb->posts.ID in (select $wpdb->term_relationships.object_id from $wpdb->term_relationships join $wpdb->term_taxonomy on $wpdb->term_taxonomy.term_taxonomy_id=$wpdb->term_relationships.term_taxonomy_id where $wpdb->term_taxonomy.taxonomy=\"$taxonomies[0]\" AND $wpdb->term_taxonomy.term_id=\"$scat\" ) ";
		}
		
		 /* Added for tags searching */
		if(is_search() && $_REQUEST['tag_s']!=""){
			$where .= " AND  ($wpdb->posts.ID in (select p.ID from $wpdb->terms c,$wpdb->term_taxonomy tt,$wpdb->term_relationships tr,$wpdb->posts p ,$wpdb->postmeta t where c.name like '".$tag_s."' and c.term_id=tt.term_id and tt.term_taxonomy_id=tr.term_taxonomy_id and tr.object_id=p.ID and p.ID = t.post_id and p.post_status = 'publish' group by  p.ID))";
		}
		return $where;
	}
   
	return $where;
}
function wpml_search_language($where)
{
	$language = ICL_LANGUAGE_CODE;
	$where .= " and t.language_code='".$language."'";
	return $where;
}
if(( @$_REQUEST['post'] ) && isset($_REQUEST['post'])){
	$post_type = get_post_type( @$_REQUEST['post'] );
}else{
	$post_type = '';
}
/*
 * execute post session expire daily once
 */
function do_daily_schedule_expire_session(){
	/* Post EXPIRY SETTINGS CODING START */
	global $table_prefix,$wpdb,$table_name;
	$table_name = $table_prefix . "post_expire_session";
	$transection_db_table_name = $wpdb->prefix.'transactions'; 
	$current_date = date_i18n('Y-m-d',strtotime(date('Y-m-d')));	
	if($wpdb->query("SHOW TABLES LIKE '".$table_name."'")==1):
		$today_executed = $wpdb->get_var("select session_id from $table_name where execute_date='".$current_date."'");	
		if($today_executed && $today_executed>0){
			/* if one time execution in a day is done then do nothing */
		}else{ 
				$tmpdata = get_option('templatic_settings');
				$listing_email_notification = @$tmpdata['listing_email_notification'];
				if($listing_email_notification != ""){
					$number_of_grace_days = $listing_email_notification;
					$postid_str = $wpdb->get_results("select p.ID,p.post_author,p.post_date, p.post_title,t.payment_date,t.post_id from $wpdb->posts p,$transection_db_table_name t where p.ID = t.post_id and p.post_status='publish' AND (t.package_type is NULL OR t.package_type=0) and datediff('".$current_date."',date_format(t.payment_date,'%Y-%m-%d')) = (select meta_value from $wpdb->postmeta pm where post_id=p.ID  and meta_key='alive_days')-$number_of_grace_days ");
					foreach($postid_str as $postid_str_obj)
					{
						$ID = $postid_str_obj->ID;
						/*fetch current date*/
						$current_day = strtotime(date('Y-m-d h:i:s'));
						/*fetch payment date*/
						$payment_date = strtotime($postid_str_obj->payment_date);
						/*fetch post alive days*/
						$alive_days = get_post_meta($ID,'alive_days',true);
						/*fetch post package id*/
						$package_select = get_post_meta($ID,'package_select',true);
						/*check package is recurring or not*/
						$recurring = get_post_meta($package_select,'recurring',true);
						/*fetch billing cycle for recurring price package*/
						$billing_cycle = get_post_meta($package_select,'billing_cycle',true);
						
						$seconds_diff = $current_day - $payment_date;
						/*day difference between current date and post date*/
						$post_day = floor($seconds_diff/3600/24);
						/*fetch package type for particular post*/
						$package_type = get_post_meta($package_select,'package_type',true);
						
						/*fetch post date*/
						$publish_date = strtotime(get_post_meta($ID,'publish_date',true));
						
						$recurring_seconds_diff = $current_day - $publish_date;
						/*day difference between current date and post date*/
						$recurring_post_day = floor($recurring_seconds_diff/3600/24);
						
						/*if current post is recurring than does not send mail to user until price package gets expired*/
						if(@$recurring == 1 && $post_day <= (($alive_days *  $billing_cycle )-$number_of_grace_days) && $package_type == 2 )
						{
							continue;
						}
						if(@$recurring == 1 && $recurring_post_day < (($alive_days *  $billing_cycle )-$number_of_grace_days) && $package_type == 1 )
						{
							continue;
						}
						$paid_date = $wpdb->get_var("select payment_date from $transection_db_table_name t where post_id = '".$ID."' AND (t.package_type is NULL OR t.package_type=0) order by t.trans_id DESC"); /* change it to calculate expired day as per transactions */
						$auth_id = $postid_str_obj->post_author;
						$post_author = $postid_str_obj->post_author;
						$post_date = date_i18n(get_option('date_format'),strtotime($postid_str_obj->post_date));
						$paid_on = date_i18n(get_option('date_format'),strtotime($paid_date));
						$post_title = $postid_str_obj->post_title;
						$userinfo = $wpdb->get_results("select user_email,display_name,user_login from $wpdb->users where ID=\"$auth_id\"");
						
						do_action('tmpl_post_expired_beforemail',$postid_str_obj);
						
						$user_email = $userinfo[0]->user_email;
						$display_name = $userinfo[0]->display_name;
						$user_login = $userinfo[0]->user_login;
						
						$fromEmail = get_site_emailId_plugin();
						$fromEmailName = get_site_emailName_plugin();
						$store_name = '<a href="'.home_url().'">'.get_option('blogname').'</a>';
						$alivedays = get_post_meta($ID,'alive_days',true);
						$productlink = get_permalink($ID);
						$loginurl = get_tevolution_login_permalink();
						$siteurl = home_url();
						$client_message = $tmpdata['listing_expiration_content'];
						if(!$client_message)
						{
							$client_message ="<p>Dear [#user_login#],<p><p>Your listing -<b>[#post_title#]</b> posted on [#post_date#] and paid on [#transection_date#] for [#alivedays#] days.</p><p>Is going to expire in [#days_left#] day(s). Once the listing expires, it will no longer appear on the site.</p><p> In case you wish to renew this listing, please login to your member area on our site and renew it as soon as it expires. You can login on the following link [#site_login_url_link#].</p><p>Your login ID is <b>[#user_login#]</b> and Email ID is <b>[#user_email#]</b>.</p><p>Thank you,<br />[#site_name#].</p>";
						}
						$search_array = array('[#user_login#]','[#post_link#]','[#post_title#]','[#post_date#]','[#transection_date#]','[#alivedays#]','[#days_left#]','[#site_login_url_link#]','[#user_login#]','[#user_email#]','[#site_name#]');
						$replace_array = array($user_login,$productlink,$post_title,$post_date,$paid_on,$alivedays,$number_of_grace_days,$loginurl,$user_login,$user_email,$store_name);
						$client_message=str_replace($search_array,$replace_array,$client_message);
						$subject = $tmpdata['listing_expiration_subject'];
						if(!$subject)
						{
							$subject = "Listing expiration Notification";
						}
						templ_send_email($fromEmail,$fromEmailName,$user_email,$display_name,$subject,stripslashes($client_message),$extra='');
						do_action('tmpl_post_expired_aftermail');
					}
				}			
				
				$postid_str = $wpdb->get_var("select group_concat(p.ID),t.payment_date,t.post_id from $wpdb->posts p,$transection_db_table_name t where  p.ID = t.post_id and p.post_status='publish'  and datediff('".$current_date."',date_format(t.payment_date,'%Y-%m-%d')) = (select DISTINCT meta_value from $wpdb->postmeta pm where post_id=p.ID  and meta_key='alive_days')");
		
				if($postid_str)
				{
					$tmpdata = get_option('templatic_settings');
					$listing_ex_status = $tmpdata['post_listing_ex_status'];
					if($listing_ex_status=='')
					{
						$listing_ex_status = 'draft';
					}
					$wpdb->query("update $wpdb->posts set post_status=\"$listing_ex_status\" where ID in ($postid_str)");
				}
		
				$wpdb->query("insert into $table_name (execute_date,is_run) values ('".$current_date."','1')");
			
		}
	endif;
}
add_action( 'wp_footer', 'do_daily_schedule_expire_session' );
add_action( 'init', 'tevolution_daily_schedule_expire_session' );
/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function tevolution_daily_schedule_expire_session(){
	if ( ! wp_next_scheduled( 'daily_schedule_expire_session' ) ) {		
		wp_schedule_event( time(), 'daily', 'daily_schedule_expire_session');
	}
}

add_action('init','tev_success_msg');
function tev_success_msg(){
	add_action('tevolution_submition_success_msg','tevolution_submition_success_msg_fn');
}
/*
 *  function while change in status from transaction detail page
 */
function tevolution_submition_success_msg_fn(){
	global $wpdb,$current_user,$monetization;
	if(isset($_REQUEST['upgrade']) && $_REQUEST['upgrade'] !=''){
		$upgrade_data = get_post_meta($_REQUEST['pid'],'upgrade_data',true);
		$paymentmethod = get_post_meta($_REQUEST['pid'],'upgrade_method',true);
		$paidamount = $upgrade_data['total_price'];
		$package_id = get_post_meta($_REQUEST['pid'],'package_select',true);		
		if($paidamount<=0)
		{
			$pid = $_REQUEST['pid']; /* it will be use when going for RENEW */
			$upgrade_post = get_post_meta($pid,'upgrade_data',true);
			$last_postid = $pid;
			$post_tax = fetch_page_taxonomy($upgrade_post['cur_post_id']);
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
		}
	}else{
		$paymentmethod = get_post_meta($_REQUEST['pid'],'paymentmethod',true);
		$paidamount = get_post_meta($_REQUEST['pid'],'paid_amount',true);
		$package_id = get_post_meta($_REQUEST['pid'],'package_select',true);
	}
	/* Get the payment method and paid amount */
	$transaction = $wpdb->prefix."transactions";
	$paymentmethod=($paymentmethod!='')?$paymentmethod:$_REQUEST['paydeltype'];
	
	if($paidamount==''){
		$paidamount_result = $wpdb->get_row("select payable_amt,package_id from $transaction t  order by t.trans_id DESC");
		$paidamount = $paidamount_result->payable_amt;
		$package_id = $paidamount_result->package_id;
	}
	
	if($paidamount !='')
	{
		$paidamount = str_replace(",", "", $paidamount);
		$paid_amount = display_amount_with_currency_plugin($paidamount);
	}
	
	
	$permalink = get_permalink($_REQUEST['pid']);
	$RequestedId = $_REQUEST['pid'];
	
	$tmpdata = get_option('templatic_settings');
	
	if($paymentmethod == 'prebanktransfer'){
		$post_default_status = 'draft';
	}else{
		$post_default_status = $tmpdata['post_default_status'];
	}
	if(isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''){
		$post_status = $wpdb->get_var("select $wpdb->posts.post_status from $wpdb->posts where $wpdb->posts.ID = ".$_REQUEST['pid']);
		$suc_post = get_post($_REQUEST['pid']);
	}
	if($post_default_status == 'publish' && $post_status == 'publish'){
		$post_link = "<a href='".get_permalink($_REQUEST['pid'])."'>".__("Click here",'templatic')."</a> ".__('for a preview of the submitted content.','templatic');
	}else{
		$post_link = '';
	}
	$store_name = '<a href="'.home_url().'">'.get_option('blogname').'</a>';
	if($paymentmethod == 'prebanktransfer')
	{
		$paymentupdsql = "select option_value from $wpdb->options where option_name='payment_method_".$paymentmethod."'";
		$paymentupdinfo = $wpdb->get_results($paymentupdsql);
		$paymentInfo = unserialize($paymentupdinfo[0]->option_value);
		$payment_method_name = $paymentInfo['name'];
		$payOpts = $paymentInfo['payOpts'];
		$bankInfo = $payOpts[0]['value'];
		$accountinfo = $payOpts[1]['value'];
	}
	$orderId = $_REQUEST['pid'];
	$siteName = "<a href='".home_url()."'>".$store_name."</a>";
	$search_array = array('[#post_type#]','[#payable_amt#]','[#bank_name#]','[#account_number#]','[#submition_Id#]','[#store_name#]','[#submited_information_link#]','[#site_name#]');
	$replace_array = array($suc_post->post_type,$paid_amount,@$bankInfo,@$accountinfo,$orderId,$store_name,$post_link,$siteName);	
	if(isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''){
		$fetch_status = $wpdb->get_var("select status from $transaction t where post_id=$orderId order by t.trans_id DESC");
	}
	$posttype_obj = get_post_type_object($suc_post->post_type);
	$post_lable = ( @$posttype_obj->labels->menu_name ) ? strtolower( @$posttype_obj->labels->menu_name ) :  strtolower( $posttype_obj->labels->singular_name );
	$theme_settings = get_option('templatic_settings');	
	if($fetch_status && $paymentmethod != 'prebanktransfer')
	{
		$filecontent = stripslashes($theme_settings['post_added_success_msg_content']);
		if (function_exists('icl_register_string')) {
			$filecontent = icl_t('templatic', 'post_added_success_msg_content',$filecontent);
		}
		if(!$filecontent){
			$filecontent = '<p class="sucess_msg_prop">'.__('Submission received successfully, thank you for listing with us.','templatic').'</p>[#submited_information_link#]';
		}
		
	}
	elseif($_REQUEST['action']=='edit' && !isset($_REQUEST['upgrade'])){
		$filecontent = '<p class="sucess_msg_prop">'.sprintf(__('Thank you for submitting your %s at our site, your %s request has been updated successfully.','templatic'),$suc_post->post_type,$suc_post->post_type).'</p><p>[#submited_information_link#]</p>';
	}elseif($paymentmethod == 'prebanktransfer' && $_REQUEST['action']!='edit'){
		if (function_exists('icl_register_string')) 
		{
			$filecontent = icl_t('templatic', 'post_pre_bank_trasfer_msg_content',$theme_settings['post_pre_bank_trasfer_msg_content']);
		}
		else
		{
			$filecontent .= stripslashes($theme_settings['post_pre_bank_trasfer_msg_content']);
		}		
		if(!stripslashes($theme_settings['post_pre_bank_trasfer_msg_content'])){
			$filecontent .= '<p>'.__("To complete the transaction, please transfer the amount of ",'templatic').' <b>[#payable_amt#] </b> ';
			$filecontent .=__("to our bank account on the details below.",'templatic').'</p>';
			$filecontent .='<p>'.__("Bank Name:",'templatic').' <b>[#bank_name#]</b></p><p>';
			$filecontent .=__("Account Number:",'templatic').' <b>[#account_number#]</b></p><p>';
			$filecontent .=__("Please include the number ",'templatic').'<b> [#submition_Id#]</b>'. __(" as the reference for the transaction.",'templatic') .'</p><p>[#submited_information_link#] </p><p>';
			$filecontent .=__("Thank you!",'templatic').'<br/>[#site_name#].</p>';
			
			$user_limit_post=get_user_meta($current_user->ID,$post_type.'_list_of_post',true); /*get the user wise limit post count on price package select*/
			if(!$user_limit_post)	
				$user_limit_post=get_user_meta($current_user->ID,$post_type.'_list_of_post',true); /*get the user wise limit post count on price package select*/
			$user_have_pkg = get_post_meta($package_id,'package_type',true); 
			$user_last_postid = $monetization->templ_get_packagetype_last_postid($current_user->ID,$post_type); /* User last post id*/
			$user_have_days = $monetization->templ_days_for_packagetype($current_user->ID,$post_type); /* return alive days(numbers) of last selected package  */
			$is_user_have_alivedays = $monetization->is_user_have_alivedays($current_user->ID,$post_type); /* return user have an alive days or not true/false */
			$is_user_package_have_alivedays = $monetization->is_user_package_have_alivedays($current_user->ID,$post_type,$package_id); /* return user have an alive days or not true/false */
			/*$filecontent .= '<p class="sucess_msg_prop">'.__('You have successfully subscribed to a membership package.Here are the details,','templatic').'</p>'; */
			
		}
	}else{		
		$filecontent = stripslashes($theme_settings['post_added_success_msg_content']);
		if (function_exists('icl_register_string')) {
			$filecontent = icl_t('templatic', 'post_added_success_msg_content',$filecontent);
		}
		if(!$filecontent){
			$filecontent = __(POST_SUCCESS_MSG,'templatic');
		}
	}
	tmpl_show_succes_page_info($current_user->ID,$post_type,$package_id,$payment_method_name);
	if(isset($_REQUEST['pid']) && $_REQUEST['pid']==''){
		$submit_form_package_url = '';
		$tevolution_post_type = tevolution_get_post_type();
		$submit_form_package_url='<ul>';
		$submit_form_package_url .= '<li class="sucess_msg_prop">'.'<a class="button" target="_blank" href="'.get_author_posts_url($current_user->ID).'">'.__('Your Profile','templatic').'</a></li>';
		foreach($tevolution_post_type as $post_type)
		{
			if($post_type != 'admanager')
			{
				global $post,$wp_query;
					$args=
					array( 
					'post_type' => 'page',
					'posts_per_page' => -1	,
					'post_status' => array('publish'),
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key' => 'submit_post_type',
							'value' =>  $post_type,
							'compare' => '='
							),
							array(
							'key' => 'is_tevolution_submit_form',
							'value' =>  1,
							'compare' => '='
							)
						)
					);
	
				$post_query = null;
				$post_query = new WP_Query($args);		
				$post_meta_info = $post_query;	
				if($post_meta_info->have_posts()){
					while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
						$PostTypeObject = get_post_type_object($post_type);
						$_PostTypeName = $PostTypeObject->labels->name;
						$submit_form_package_url .= "<li><a class='button' target='_blank' href='".get_the_permalink($post->ID)."'>".get_the_title($post->ID)."</a></li>";
				  endwhile;wp_reset_query();wp_reset_postData();
				}
			}
		}
		$submit_form_package_url.='</ul>';
	}
	$filecontent .= $submit_form_package_url;
	$filecontent = str_replace($search_array,$replace_array,$filecontent); 
	echo $filecontent;
}
/* add feature listing options */
add_action('init','tevolution_add_featured_fn1');
function tevolution_add_featured_fn1(){
	add_action('tevolution_featured_list','tevolution_featured_list_fn');
}
/*
	display terms and condition check box on submit page
*/
function tevolution_show_term_and_condition()
{
	$tmpdata = get_option('templatic_settings');
	if(isset($tmpdata['tev_accept_term_condition']) && $tmpdata['tev_accept_term_condition'] != "" && $tmpdata['tev_accept_term_condition'] == 1){	?>
        <div class="form_row clearfix">
             <input name="term_and_condition" id="term_and_condition" value="" type="checkbox" class="chexkbox" onclick="hide_error()"/>
            <label for="term_and_condition">&nbsp;
             <?php if(isset($tmpdata['term_condition_content']) && $tmpdata['term_condition_content']!=''){
				 	 if (function_exists('icl_register_string')){
						icl_register_string('templatic', 'term_condition',stripslashes($tmpdata['term_condition_content']));
						$term_condition = icl_t('templatic', 'term_condition',stripslashes($tmpdata['term_condition_content']));
						echo stripslashes($term_condition); 
				   }
				   else
				   {
	                   echo stripslashes($tmpdata['term_condition_content']); 
				   }
             }else{
                _e('Accept Terms and Conditions.','templatic');
             }?></label>
             <span class="error message_error2" id="terms_error"></span>
        </div>            
    <?php 
	}
}
/*
	Display the submitted fields informations of success page, using "tevolution_submition_success_post_content" hook you can change success page content from child theme
 */
add_action('tevolution_submition_success_post_content','tevolution_submition_success_post_submited_content');
function tevolution_submition_success_post_submited_content()
{
	?>
     <!-- Short Detail of post -->
	<div class="submit_info_section sis_on_submitinfo">
		<h3><?php _e(POST_DETAIL,'templatic');?></h3>
	</div>
    <div class="submited_info">
	<?php
	global $wpdb,$post,$current_user;
	remove_all_actions('posts_where');
	$cus_post_type = get_post_type($_REQUEST['pid']);
	$args = 
	array( 'post_type' => 'custom_fields',
	'posts_per_page' => -1	,
	'post_status' => array('publish'),
	'meta_query' => array(
	   'relation' => 'AND',
		array(
			'key' => 'post_type_'.$cus_post_type.'',
			'value' => $cus_post_type,
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
			'key' => 'show_on_success',
			'value' =>  '1',
			'compare' => '='
		)
	),
		'meta_key' => 'sort_order',
		'orderby' => 'meta_value_num',
		'order' => 'ASC'
	);
	$post_query = null;
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$post_meta_info = new WP_Query($args);	
	
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	$suc_post = get_post($_REQUEST['pid']);
	
	$paidamount = get_post_meta($_REQUEST['pid'],'paid_amount',true);
	$success_post_type_object = get_post_type_object($suc_post->post_type);
	$success_post_title = $success_post_type_object->labels->menu_name;
		if($post_meta_info)
		  {
			echo "<div class='grid02 rc_rightcol clearfix'>";
			echo "<ul class='list'>";
			printf( __( '<li><p class="submit_info_label">'.__('Title','templatic').':</p> <p class="submit_info_detail"> %s </p></li>', 'templatic' ),  stripslashes($suc_post->post_title)  ); 
			
			while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
				$post->post_name=get_post_meta(get_the_ID(),'htmlvar_name',true);
				
				$htmlvar_name = get_post_meta($post->ID,"htmlvar_name",true);							
				if(get_post_meta($post->ID,"ctype",true) == 'post_categories')
				{
					$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $suc_post->post_type,'public'   => true, '_builtin' => true ));	
					
					$category_name = wp_get_post_terms($_REQUEST['pid'], $taxonomies[0]);
					if($category_name)
					{
						$_value = '';
						
						foreach($category_name as $value)
						 {
							$_value .= $value->name.",";
						 }
						 echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p class='submit_info_detail'> ".substr($_value,0,-1)."</p></li>";
						
					}
					 do_action('tmpl_on_success_after_categories');
				}
				if(get_post_meta($post->ID,"ctype",true) == 'heading_type' )
				  {
					
					 echo "<li><h3>".stripslashes($post->post_title)." </h3></li>";
					  do_action('tmpl_on_success_after_heading');
				  }
				if(get_post_meta($_REQUEST['pid'],$post->post_name,true))
				  {
					if(get_post_meta($post->ID,"ctype",true) == 'multicheckbox' )
					  {
						$_value = '';
							
						$option_values = explode(",",get_post_meta($post->ID,'option_values',true));				
						$option_titles = explode(",",get_post_meta($post->ID,'option_title',true));
						$field=get_post_meta($_REQUEST['pid'],$post->post_name,true);						
						$checkbox_value='';
						for($i=0;$i<count($option_values);$i++){
							if(in_array($option_values[$i],$field)){
								if($option_titles[$i]!=""){
									$checkbox_value .= $option_titles[$i].',';
								}else{
									$checkbox_value .= $option_values[$i].',';
								}
							}
						}						
						 echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p class='submit_info_detail'> ".substr($checkbox_value,0,-1)."</p></li>";
						 do_action('tmpl_on_success_after_'.$htmlvar_name,$post->ID);
					  }
					
                    elseif(get_post_meta($post->ID,"ctype",true) == 'multicity')
					{
						global $wpdb,$country_table,$zones_table,$multicity_table;
						
						$country_table = $wpdb->prefix."countries";
						$zones_table =$wpdb->prefix . "zones";	
						$multicity_table = $wpdb->prefix . "multicity";
							
						$city= get_post_meta($_REQUEST['pid'],'post_city_id',true);		
						$zones_id= get_post_meta($_REQUEST['pid'],'zones_id',true);		
						$country_id= get_post_meta($_REQUEST['pid'],'country_id',true);		
						
							$cityinfo = $wpdb->get_results($wpdb->prepare("select cityname from $multicity_table where city_id =%d",$city ));
							if($country_id !='')
								$countryinfo = $wpdb->get_results($wpdb->prepare("select country_name from $country_table where country_id =%d",$country_id ));
							if($zones_id !='')
								$zoneinfo = $wpdb->get_results($wpdb->prepare("select zone_name from $zones_table where zones_id =%d",$zones_id ));
						
						$multicity_value = '';
						
						if(!empty($countryinfo[0]->country_name))
							$multicity_value .= $countryinfo[0]->country_name.', ';
						if(!empty($zoneinfo[0]->zone_name))
							$multicity_value .= $zoneinfo[0]->zone_name.', ';
						if(!empty($cityinfo[0]->cityname))
							$multicity_value .= $cityinfo[0]->cityname;
								
								
						echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p class='submit_info_detail'> ".rtrim($multicity_value,',')."</p></li>";
						
					}
					elseif(get_post_meta($post->ID,"ctype",true) == 'radio')
					{
						$option_values = explode(",",get_post_meta($post->ID,'option_values',true));				
						$option_titles = explode(",",get_post_meta($post->ID,'option_title',true));
						for($i=0;$i<count($option_values);$i++){
							if(get_post_meta($_REQUEST['pid'],$post->post_name,true) == $option_values[$i]){
								if($option_titles[$i]!=""){
									$rado_value = $option_titles[$i];
								}else{
									$rado_value = $option_values[$i];
								}
								echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p class='submit_info_detail'> ".$rado_value."</p></li>";
							}
						}
					}else
					 {
						 $custom_field=stripslashes(get_post_meta($_REQUEST['pid'],$post->post_name,true));
						 if(substr($custom_field, -4 ) == '.jpg' || substr($custom_field, -4 ) == '.png' || substr($custom_field, -4 ) == '.gif' || substr($custom_field, -4 ) == '.JPG' 
										|| substr($custom_field, -4 ) == '.PNG' || substr($custom_field, -4 ) == '.GIF'){
							  echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p class='submit_info_detail'> <img src='".$custom_field."'  width='200'/></p></li>";
						 }							 
						 else
						 {
						   if(get_post_meta($post->ID,'ctype',true) == 'upload')
							{
							  echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p class='submit_info_detail'>".__('Click here to download File','templatic-admin')."<a href=".get_post_meta($_REQUEST['pid'],$post->post_name,true).">Download</a></p></li>";
							}
						   else
							{
							 if(get_post_meta($post->ID,"ctype",true) == 'texteditor'){
								 echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p> ".get_post_meta($_REQUEST['pid'],$post->post_name,true)."</p></li>"; 
							 }else{
							 	 echo "<li><p class='submit_info_label'>".stripslashes($post->post_title).": </p> <p class='submit_info_detail'> ".get_post_meta($_REQUEST['pid'],$post->post_name,true)."</p></li>";
							 }
							}
						 }
					 }
				  }
				if($post->post_name == 'post_content' && $suc_post->post_content!='')
				{
					$suc_post_con = $suc_post->post_content;
				}
				if($post->post_name == 'post_excerpt' && $suc_post->post_excerpt!='')
				{
					$suc_post_excerpt = $suc_post->post_excerpt;
				}
				if($post->post_name == 'post_images'){
					
					$post_img = bdw_get_images_plugin($suc_post->ID,'thumbnail');					
					if(!empty($post_img)){
						$images='<ul class="sucess_post_images submit_info_detail">';
						foreach($post_img as $key=>$value){
							$images.="<li><img src='".$value['file']."'></li>";
						}
						$images.='</ul>';
						
						echo "<li><p class='submit_info_label submit_post_images_label'>".stripslashes($post->post_title).": </p>".$images."</li>";
					}
				}
				if(get_post_meta($post->ID,"ctype",true) == 'geo_map')
				{
					$add_str = get_post_meta($_REQUEST['pid'],'address',true);
					$geo_latitude = get_post_meta($_REQUEST['pid'],'geo_latitude',true);
					$geo_longitude = get_post_meta($_REQUEST['pid'],'geo_longitude',true);
					$map_view = get_post_meta($_REQUEST['pid'],'map_view',true);
				}
				
				do_action('tmpl_on_success_after_'.$htmlvar_name);
			endwhile;
			
			if(get_post_meta($_REQUEST['pid'],'package_select',true))
			{
					$package_name = get_post(get_post_meta($_REQUEST['pid'],'package_select',true));
					if (function_exists('icl_register_string')) {									
						$package_name->post_title = icl_t('tevolution-price', 'package-name'.$package_name->ID,$package_name->post_title);
					}
					$package_type = get_post_meta($package_name->ID,'package_type',true);
					if($package_type  ==2){
						$pkg_type = __('Subscription','templatic'); 
					}else{ 
						$pkg_type = __('Single Submission','templatic'); 
					} ?>
					<li><p class="submit_info_label"><?php _e('Package Type','templatic');?>: </p> <p class="submit_info_detail"> <?php echo $pkg_type;?></p></li>
				 
		<?php
			}
			if(get_post_meta($_REQUEST['pid'],'alive_days',true))
			{
				 echo "<li><p class='submit_info_label'>"; _e('Validity','templatic'); echo ": </p> <p class='submit_info_detail'> ".get_post_meta($_REQUEST['pid'],'alive_days',true).' '; _e('Days','templatic'); echo "</p></li>";
			}
			if(get_user_meta($suc_post->post_author,'list_of_post',true))
			{
				 echo "<li><p class='submit_info_label'>"; _e('Number of Posts','templatic').": </p> <p class='submit_info_detail'> ".get_user_meta($suc_post->post_author,'list_of_post',true)."</p></li>";
			}
			if(get_post_meta(get_post_meta($_REQUEST['pid'],'package_select',true),'recurring',true))
			{
				$package_name = get_post(get_post_meta($_REQUEST['pid'],'package_select',true));
				//print_r($package_name);
				
				$package_amount = get_post_meta($package_name->ID,'package_amount',true);
				 echo "<li><p class='submit_info_label'>"; _e('Recurring Charges','templatic').": </p>";
			
				 echo "<p class='submit_info_detail'> ".fetch_currency_with_symbol($package_amount)."</p></li>";
			}
			if($paidamount > 0){
				fetch_payment_description($_REQUEST['pid']);
			}
			echo "</ul>";
			echo "</div>";
		  }		 
		do_action('after_tevolution_success_msg');
	?>
	</div>
	<?php if(isset($suc_post_con)): ?>
			  <div class="title_space">
				 <div class="submit_info_section">
					<h3><?php _e('Post Description', 'templatic');?></h3>
				 </div>
				 <p><?php echo nl2br($suc_post_con); ?></p>
			  </div>
	<?php endif;
	
	if(isset($suc_post_excerpt)): ?>
				<div class="title_space">
					<div class="submit_info_section">
						<h3><?php _e('Post Excerpt','templatic');?></h3>
					</div>
					<p><?php echo nl2br($suc_post_excerpt); ?></p>
				</div>
	<?php endif; 
	
	if(@$add_str)
	{
	?>
			<div class="title_space">
				<div class="submit_info_section">
					<h3><?php _e('Map','templatic'); ?></h3>
				</div>
				<p><strong><?php _e('Location','templatic'); echo ": "; echo $add_str;?></strong></p>
			</div>
			<div id="gmap" class="graybox img-pad">
				<?php if($geo_longitude &&  $geo_latitude): 
						$pimgarr = bdw_get_images_plugin($_REQUEST['pid'],'thumb',1);
						$contact = get_post_meta($_REQUEST['pid'],'phone',true);
						$website = get_post_meta($_REQUEST['pid'],'website',true);
						
						$pimg = $pimgarr[0]['file'];
						if(!$pimg):
							$pimg = plugin_dir_url( __FILE__ )."images/img_not_available.png";
						endif;	
						$title = stripslashes($suc_post->post_title);
						$address = $add_str;
						require_once (TEMPL_MONETIZE_FOLDER_PATH . 'templatic-custom_fields/preview_map.php');
						$retstr ="";
						$link = get_permalink($_REQUEST['pid']);
						$retstr .= "<div class=\"map_infobubble map_popup\"><div class=\"google-map-info map-image\"><div class=map-inner-wrapper><div class=map-item-info><div class=map-item-img><a href=\"$link\"><img src=\"$pimg\" width=\"150\" height=\"150\" alt=\"\" /></a></div>";
                              $retstr .= "<h6><a href=\'".get_permalink($_REQUEST['pid'])."\' class=\"ptitle\" ><span>$title</span></a></h6>";
                              if($address){$retstr .= "<p class=address>$address</p>";}
						if($contact){$retstr .= '<p class=contact>'.$contact.'</p>';}
						if($website){$retstr .= '<p class=website><a href= '.$website.'>'.$website.'</a></p>';}
						$retstr .= "</div></div></div></div>";

						preview_address_google_map_plugin($geo_latitude,$geo_longitude,$retstr,$map_view);
					  else:
				?>
						<iframe src="//maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $add_str;?>&amp;ie=UTF8&amp;z=14&amp;iwloc=A&amp;output=embed" height="358" width="100%" scrolling="no" frameborder="0" ></iframe>
				<?php endif; ?>
			</div>
	<?php } ?>
	
	
	<!-- End Short Detail of post -->
     <?php
	 unset($_SESSION['pament_done']);
}
				
/** add favourites class to body*/
add_filter('body_class','tmpl_add_class_inbody',11,2);
function tmpl_add_class_inbody($classes,$class){
	global $post;
	
	/* Add class if listing is claimed */
	if(is_single() && get_post_meta($post->ID,'is_verified',true) == 1){
			$classes[] .= " claimed-listing";
	}
	if(isset($_GET['sort']) && $_GET['sort'] =='favourites'){
			$classes[] .= " tevolution-favoutites";
	}
	return $classes;
}

/* script to show message user can upload a single image whilemultiple upload from submit form*/
function callback_on_footer_fn(){ ?>
	<script type="text/javascript" async >
		jQuery.noConflict();
		var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;
		var is_safari = navigator.userAgent.indexOf("Safari") > -1;
		if ((is_chrome)&&(is_safari)) {is_safari=false;}
		if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
			jQuery("#safari_error").html("<?php _e("Safari will allow you to upload only one image, so we suggest you use some other browser.",'templatic');?>");
		}
	</script>
<?php }

/* 
 * Display the post related custom fields display
 */
add_action("single_post_custom_fields",'tevolution_post_detail_after_singular');
function tevolution_post_detail_after_singular()
{
	if((is_single() || is_archive()) && get_post_type()=='post'){		
		global $post;
			$post_type= get_post_type();
			$cus_post_type = get_post_type($post->ID);
			$PostTypeObject = get_post_type_object($cus_post_type);
			$PostTypeLabelName = $PostTypeObject->labels->name;
			
			$heading_type = fetch_heading_per_post_type(get_post_type());
			wp_reset_query();
			if(count($heading_type) > 0)
			{
				foreach($heading_type as $_heading_type)
				{	
					if(is_single()){
						$custom_metaboxes[$_heading_type] = get_post_custom_fields_templ_plugin($post_type,'','',$_heading_type);/*custom fields for custom post type..*/
					}
					if(is_archive()){
						$post_meta_info = listing_fields_collection();/*custom fields for custom post type..						*/
						while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
							if(get_post_meta($post->ID,"ctype",true)){
								$options = explode(',',get_post_meta($post->ID,"option_values",true));
							}
							$custom_fields = array(
									"id"		=> $post->ID,
									"name"		=> get_post_meta($post->ID,"htmlvar_name",true),
									"label" 	=> $post->post_title,
									"htmlvar_name" 	=> get_post_meta($post->ID,"htmlvar_name",true),
									"default" 	=> get_post_meta($post->ID,"default_value",true),
									"type" 		=> get_post_meta($post->ID,"ctype",true),
									"desc"      => $post->post_content,
									"option_title" => get_post_meta($post->ID,"option_title",true),
									"option_values" => get_post_meta($post->ID,"option_values",true),
									"is_require"  => get_post_meta($post->ID,"is_require",true),
									"is_active"  => get_post_meta($post->ID,"is_active",true),
									"show_on_listing"  => get_post_meta($post->ID,"show_on_listing",true),
									"show_on_detail"  => get_post_meta($post->ID,"show_on_detail",true),
									"validation_type"  => get_post_meta($post->ID,"validation_type",true),
									"style_class"  => get_post_meta($post->ID,"style_class",true),
									"extra_parameter"  => get_post_meta($post->ID,"extra_parameter",true),
									"show_in_email" =>get_post_meta($post->ID,"show_in_email",true),
									);
							if($options)
							{
								$custom_fields["options"]=$options;
							}
							$return_arr[get_post_meta($post->ID,"htmlvar_name",true)] = $custom_fields;
						endwhile;wp_reset_query();
						$custom_metaboxes[$_heading_type]=$return_arr;
					}
				}
			}			
		echo '<div class="single_custom_field">';		
		$j=0;
		foreach($custom_metaboxes as $mainkey=> $_htmlvar_name):
		$r=0;		
		if(!empty($_htmlvar_name) || $_htmlvar_name!='')
		{
		  foreach($_htmlvar_name as $key=> $_htmlvar_name):	
			if( $key!="post_content" && $key!="post_excerpt" &&  $key!='category' && $key!='post_title' && $key!='post_images' && $key!='basic_inf' && $_htmlvar_name['show_on_detail'] == 1)
			{
				if($_htmlvar_name['type'] == 'multicheckbox' && get_post_meta($post->ID,$key,true) !=''):
					if($r==0){
						 if( $mainkey == '[#taxonomy_name#]' ){
						 	echo '<h3>'.ucfirst($post_type).' ';_e("Information",'templatic');echo '</h3>';
							$r++;
						 }else{
						 	echo '<h3>';_e($mainkey,'templatic');echo '</h3>';
							$r++;
						 }
					}
			?>
						<li><label><?php echo $_htmlvar_name['label']; ?></label> : <span><?php echo implode(",",get_post_meta($post->ID,$key,true)); ?></span></li>
	               <?php elseif($_htmlvar_name['type']=='upload' && get_post_meta($post->ID,$key,true) !=''):
						if($r==0){
							 if( $mainkey == '[#taxonomy_name#]' ){
							 	echo '<h3>'.ucfirst($PostTypeLabelName).' ';_e("Information",'templatic');echo '</h3>';
								$r++;
							 }else{
							 	echo '<h3>';_e($mainkey,'templatic');echo '</h3>';
								$r++;
							 }
						}
			?>
               	 		<li><label><?php echo $_htmlvar_name['label']; ?> </label>: <span> <?php echo __('Click here to download File','templatic-admin'); ?> <a href="<?php echo stripslashes(get_post_meta($post->ID,$key,true)); ?>">Download</a></span></li>
			<?php else: 
					/* else start */					
					if(get_post_meta($post->ID,$key,true) !=''):
						if($r==0){
							 if( $mainkey == '[#taxonomy_name#]' ){
							 	echo '<h3>'.ucfirst($PostTypeLabelName).' ';_e("Information",'templatic');echo '</h3>';
								$r++;
							 }else{
							 	echo '<h3>';_e($mainkey,'templatic');echo '</h3>';
								$r++;
							 }
						}
						
					?>
					
						<?php if($_htmlvar_name['type']=='radio'){
								$options = explode(',',$_htmlvar_name['option_values']);
								$options_title = explode(',',$_htmlvar_name['option_title']);
						
								for($i=0; $i<= count($options); $i++){
									$val = $options[$i];
									if(trim($val) == trim(get_post_meta($post->ID,$key,true))){ 
										$val_label = $options_title[$i];
														
									}
								}
								if($val_label ==''){ $val_label = get_post_meta($post->ID,$post->post_name,true); } /* if title not set then display the value */

								?>
								<li><label><?php echo $_htmlvar_name['label']; ?></label> : <span><?php echo $val_label ; ?></span></li>
						<?php
							}else{ ?>
								<li><label><?php echo $_htmlvar_name['label']; ?></label> : <span><?php echo stripslashes(get_post_meta($post->ID,$key,true)); ?></span></li>
						<?php	}

				  endif;
				/*else end */				  ?>
			<?php endif; ?>
	<?php  	$i++; } /* first if condition finish */
			$j++;
				
			endforeach;	
		}			
		endforeach;
		echo '</div>';		
	}
	
}
add_action('admin_init','is_cdlocalization');
/*
* check is it codestyling localization or not
*/
if(!function_exists('is_cdlocalization')){
	function is_cdlocalization(){
		if(is_plugin_active('codestyling-localization/codestyling-localization.php')){
			return true;
		}else{
			return false;
		}
	}
}

/*
	To check image is available/exists or not
*/
if(!function_exists('tmpl_checkRemoteFile')){
	function tmpl_checkRemoteFile($url)
	{
		$response = wp_remote_get($url );
		if(!is_wp_error( $response ))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
/*
	Added filter while submitting a form as a logout user redirect to submit form page.
*/
add_filter('tevolution_login_redirect_to','login_redirect_to');
add_filter('tevolution_register_redirect_to','login_redirect_to');
function login_redirect_to($redirect_to){

	if(isset($_SESSION['redirect_to']) && $_SESSION['redirect_to']!=""){
		$redirect_to=$_SESSION['redirect_to'];
	}
	return $redirect_to;
}

/*
get the full page URL specially for pagination n all
*/
function tmpl_directory_full_url($post_type)
{
    global $wp_query;
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
    $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    $host = (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST']))? $_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];	

	if(!is_tax() && is_archive() && !is_search())
	{			
		$current_term = $wp_query->get_queried_object();
		$post_type=(get_post_type()!='')? get_post_type() : get_query_var('post_type');
		if($post_type == 'event'){
			$permalink = get_post_type_archive_link($post_type);
			if(isset($_REQUEST['etype']) && $_REQUEST['etype'] != '')
			{
				$permalink = $permalink.'/?etype='.$_REQUEST['etype'];
			}
			if(isset($_REQUEST['sortby']) && $_REQUEST['sortby']!='')
				$permalink=str_replace('&'.$post_type.'_sortby=alphabetical&sortby='.$_REQUEST['sortby'],'',$permalink);
		}	
		else{	
			$permalink = get_post_type_archive_link($post_type);				
			$permalink=str_replace('&'.$post_type.'_sortby=alphabetical&sortby='.$_REQUEST['sortby'],'',$permalink);
			$permalink=str_replace('&event_sortby=alphabetical&sortby='.$_REQUEST['sortby'],'',$permalink);
		}
	}elseif(is_search()){
		$search_query_str=str_replace('&'.$post_type.'_sortby=alphabetical&sortby='.@$_REQUEST['sortby'],'',$_SERVER['QUERY_STRING']);
		$permalink= site_url()."?".$search_query_str;
	}else{
		if( $wp_query->get( 'page_id' ) == get_option( 'page_on_front' ) ){
			$permalink= site_url();
			if(isset($_REQUEST['etype']) && $_REQUEST['etype'] != '')
			{
				$permalink = $permalink.'/?etype='.$_REQUEST['etype'];
			}
			if(isset($_REQUEST['sortby']) && $_REQUEST['sortby']!='')
				$permalink=str_replace('&'.$post_type.'_sortby=alphabetical&sortby='.$_REQUEST['sortby'],'',$permalink);
		}
		else
		{
			$current_term = $wp_query->get_queried_object();
			$permalink=($current_term->slug) ?  get_term_link($current_term->slug, $current_term->taxonomy):'';
			if(isset($_REQUEST['etype']) && $_REQUEST['etype'] != '')
			{
				$permalink = $permalink.'/?etype='.$_REQUEST['etype'];
			}
			if(isset($_REQUEST['sortby']) && $_REQUEST['sortby']!='')
				$permalink=str_replace('&'.$post_type.'_sortby=alphabetical&sortby='.$_REQUEST['sortby'],'',$permalink);
		}
		
	}	
	
	if(false===strpos($permalink,'?')){
	    $url_glue = '?';
	}else{
		$url_glue = '&amp;';	
	}
    return $permalink.$url_glue;
}

/*
 Get the custom fields details for detail page.
 */
if(!function_exists('tmpl_get_single_page_customfields_details')){
function tmpl_get_single_page_customfields_details($post_type,$heading='',$heading_key=''){	
	
	global $wpdb,$post,$posttitle;
	$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';
	
	remove_all_actions('posts_where');		
	$post_query = null;
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	add_filter('posts_join', 'custom_field_posts_where_filter');


		$args = apply_filters('tmpl_nondir_htmlvar_name_query',array( 'post_type' => 'custom_fields',
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
									),
									array(
										'key'     => $post->post_type.'_heading_type',
										'value'   =>  array('basic_inf',$heading),
										'compare' => 'IN'
									)
								),
					'meta_key' => 'sort_order',
					'orderby' => 'meta_value',
					'order' => 'ASC'
		),$post_type,$heading,$heading_key);
	
		/* save the data on transient to get the fast results */
		
			$post_query = new WP_Query($args);
	
		
		/* Join to make the custom fields WPML compatible */
		remove_filter('posts_join', 'custom_field_posts_where_filter');
		
		$htmlvar_name='';
		if($post_query->have_posts())
		{
			while ($post_query->have_posts()) : $post_query->the_post();
				$ctype = get_post_meta($post->ID,'ctype',true);
				$post_name=get_post_meta($post->ID,'htmlvar_name',true);
				$style_class=get_post_meta($post->ID,'style_class',true);
				$option_title=get_post_meta($post->ID,'option_title',true);
				$option_values=get_post_meta($post->ID,'option_values',true);
				$default_value=get_post_meta($post->ID,'default_value',true);
				$htmlvar_name[$post_name] = array( 'type'=>$ctype,
											'label'=> $post->post_title,
											'style_class'=>$style_class,
											'option_title'=>$option_title,
											'option_values'=>$option_values,
											'default'=>$default_value,
											);			
			endwhile;
			wp_reset_query();
		}
		return $htmlvar_name;
		
	}

}

/*
 * detail page show categories and tags 
 */
define('TMPL_CATEGORY_LABEL', __('Posted In ','templatic'));
function tmpl_get_the_posttype_taxonomies($label,$tax,$title = TMPL_CATEGORY_LABEL)
{
	global $post;
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post->post_type,'public'   => true, '_builtin' => true ));	
	$terms = get_the_terms($post->ID, $taxonomies[0]);
	$sep = ", ";
	$i = 0;
	$category_html = '';
	foreach($terms as $term)
	{
		
		if($i == ( count($terms) - 1))
		{
			$sep = '';
		}
		elseif($i == ( count($terms) - 2))
		{
			$sep = __(' and ','templatic');
		}
		$term_link = get_term_link( $term, $taxonomies[0] );
		if( is_wp_error( $term_link ) )
			continue;
		$taxonomy_category .= '<a href="' . $term_link . '">' . $term->name . '</a>'.$sep; 
		$i++;
	}
	if(!empty($terms))
	{
		$category_html = '<p class="bottom_line"><span class="i_category"><span>';
		$category_html .=  __('Posted In','templatic').' '.$taxonomy_category;
		$category_html.= '</span></span></p>';
	}
	return $category_html;
}

/*
 * detail page show tags
 */
define('TMPL_TAGS_LABEL', __('Tagged In ','templatic'));
function tmpl_get_the_posttype_tags($label,$taxtag,$title = TMPL_TAGS_LABEL)
{	
	global $post;
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post->post_type,'public'   => true, '_builtin' => true ));	
	$terms = get_the_terms($post->ID, $taxonomies[1]);
	$sep = ",";
	$i = 0;
	$tag_html = '';
	if(!empty($terms)){
		foreach($terms as $term)
		{
			
			if($i == ( count($terms) - 1))
			{
				$sep = '';
			}
			elseif($i == ( count($terms) - 2))
			{
				$sep = __(' and ','templatic');
			}
			$term_link = get_term_link( $term, $taxonomies[0] );
			if( is_wp_error( $term_link ) )
				continue;
			$taxonomy_category .= '<a href="' . $term_link . '">' . $term->name . '</a>'.$sep; 
			$i++;
		}
	}
	if(!empty($terms))
	{
		$tag_html = '<p class="bottom_line"><span class="i_category">';
		$tag_html .= __('Tagged In','templatic').' '.$taxonomy_category;
		$tag_html.= '</span></p>';
	}
	return $tag_html;
}

/*================================================ To get the category page custom fields ======================================================*/


/* get a drop down of categories */
function tmpl_get_category_dl_options($selected,$tcatslug)
{ 
		$cat_args = array('name' => 'scat', 'id' => 'scat', 'selected' => $selected, 'class' => 'select', 'orderby' => 'name', 'echo' => '0', 'hierarchical' => 1, 'taxonomy'=>$tcatslug,'hide_empty'  => 0);
		$cat_args['show_option_none'] = __('Select Category',EDOMAIN);
		return wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
}


/* show the search criteria on search result page that is searched for. */
remove_action('after_search_result_label','tmpl_get_property_search_criteria',1); 
add_action('after_search_result_label','tmpl_get_search_criteria',99);
function tmpl_get_search_criteria()
{

	global $wpdb;
	
	$htmlvar_name = tmpl_get_advance_search_list_customfields(@$_REQUEST['post_type']);

		echo '<div class="other_search_criteria">';
		    if(isset($_REQUEST['category']) && !empty($_REQUEST['category']))
		    {
				$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $_REQUEST['post_type'],'public'   => true, '_builtin' => true ));
				echo '<label>';
				_e('Category: ','templatic');
				echo '</label>';
			    echo tmpl_get_the_category_by_ID($_REQUEST['category'],$taxonomies[0]).', '; 
			}
			if(isset($_REQUEST['tag_s']) && !empty($_REQUEST['tag_s']))
			{
				echo '<label>';
				_e('Tags: ','templatic');
				echo '</label>';
				echo $_REQUEST['tag_s'].', ';
			} 
			if(isset($_REQUEST['articleauthor']) && !empty($_REQUEST['articleauthor']))
			{
				echo '<label>';
				_e('Author: ','templatic');
				echo '</label>';
				echo $_REQUEST['articleauthor'].', ';
			}
			
			if(isset($_REQUEST['min_price']) && !empty($_REQUEST['min_price']))
			{
				echo '<label>';
				_e('Min Price: ','templatic');
				echo '</label>';
				echo $_REQUEST['min_price'].', ';
			}
			
			if(isset($_REQUEST['max_price']) && !empty($_REQUEST['max_price']))
			{
				echo '<label>';
				_e('Max Price: ','templatic');
				echo '</label>';
				echo $_REQUEST['max_price'].', ';
			}
			
			if(is_array($_REQUEST['search_custom']) && !empty($_REQUEST['search_custom'])){
				foreach($_REQUEST['search_custom'] as $searchkey=>$searchval )
				{
					foreach($htmlvar_name as $key=>$val)
					{
							if($searchval == 'radio')
							{
								$searchkey1 = explode('_radio',$searchkey);
								$searchkey = $searchkey1[0];
							}
							
							if( $key == $searchkey)
							{
								if($searchval == 'radio')
									$searchkey = $searchkey.'_radio';
									
								if(!empty($_REQUEST[$searchkey]))
								{
									if(is_array($_REQUEST[$searchkey]))
										$_REQUEST[$searchkey] = implode(',',$_REQUEST[$searchkey]);
											
									$criteria .= '<label>'.$val['label'].':</label> '.$_REQUEST[$searchkey].', ';
								}
							}
					}
				}
			}
			echo rtrim($criteria,", ");
		echo '</div>';	

}


/*
 return the custom fields - which selected as show on Advance search form
 */
function tmpl_get_advance_search_list_customfields($post_type){
	global $wpdb,$post,$posttitle;
	if(is_array($post_type)){
		$post_type = $post_type[0];
	}else{
		$post_type = $post_type;
	}
	$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';
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
									'key'     => 'is_search',
									'value'   =>  '1',
									'compare' => '='
								)
							),
				'meta_key' => 'sort_order',
				'orderby' => 'meta_value',
				'suppress_filters' => true,
				'order' => 'ASC'
		);
	
	remove_all_actions('posts_where');		
	remove_action('pre_get_posts','location_pre_get_posts',12);
	$post_query = null;
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	remove_action('pre_get_posts', 'advance_search_template_function',11);
	add_filter('posts_join', 'custom_field_posts_where_filter');
	/* Set the results in transient to get fast results */

	$post_query = new WP_Query($args);

	remove_filter('posts_join', 'custom_field_posts_where_filter');
	
	$htmllist_advance_search_var_name='';
	if($post_query->have_posts())
	{
		while ($post_query->have_posts()) : $post_query->the_post();
			$ctype = get_post_meta($post->ID,'ctype',true);
			$post_name=get_post_meta($post->ID,'htmlvar_name',true);
			$style_class=get_post_meta($post->ID,'style_class',true);
			$label=get_post_meta($post->ID,'admin_title',true);
			$option_title=get_post_meta($post->ID,'option_title',true);
			$option_values=get_post_meta($post->ID,'option_values',true);
			
			$htmllist_advance_search_var_name[$post_name] = array( 'type'=>$ctype,
												'htmlvar_name'=> $post_name,
												'style_class'=> $style_class,
												'option_title'=> $option_title,
												'option_values'=> $option_values,
												'label'=> $post->post_title
											  );
			$posttitle[] = $post->post_title;
		endwhile;
		wp_reset_query();
	}	
	return $htmllist_advance_search_var_name;
	
}

/* 
	To get the category name from category id for custom post type
*/
function tmpl_get_the_category_by_ID( $cat_ID,$texonomy ) {
      $cat_ID = (int) $cat_ID;
      $category = get_term( $cat_ID, $texonomy );
	
	        if ( is_wp_error( $category ) )
               return $category;

	        return ( $category ) ? $category->name : '';
}

/* This filter will remove the extra buttons from front end wp editor */

if(!is_admin() && !strstr($_SERVER['REQUEST_URI'],'/wp-admin/' )){
	add_filter('tiny_mce_plugins','tmpl_tiny_mce_plugins');
	add_filter('mce_buttons','tmpl_mce_buttons');
	add_filter('mce_buttons_2','tmpl_mce_buttons_2');
}

/* remove extra plugin from editor */
function tmpl_tiny_mce_plugins(){
	return array();
}

/* remove extra buttons from wp editor tool bar 1  */
/* This use category wise filter field to show menu in post_content */
if(!function_exists('tmpl_mce_buttons')){
    function tmpl_mce_buttons(){
            return array('bold', 'italic', 'strikethrough', 'bullist', 'numlist', 'blockquote', 'hr', 'link', 'unlink');
    }
}

/* remove extra buttons from wp editor tool bar 2  */
function tmpl_mce_buttons_2(){
	return array();
}
/* show package information on success page */
function tmpl_show_succes_page_info($user_id='',$post_type,$package_id,$paymentmethod)
{
	global $current_user,$monetization;
	$user_have_pkg = get_post_meta($package_id,'package_type',true); 
	
	$package_limit_post=get_post_meta($package_id,'limit_no_post',true);/* get the price package limit number of post*/
	if(@$package_id)
		echo sprintf(__('You have subscribed to the %s package.','templatic'),'<b>'.get_the_title($package_id).'</b>');
	
	if(isset($_REQUEST['pid']) && $_REQUEST['pid'] != '')
	{
		$payable_amount = get_post_meta($_REQUEST['pid'],'payable_amount',true);
	}
	else
	{
		$payable_amount = get_post_meta($package_id,'package_amount',true);
	}
	$payable_amount = str_replace(",", "", $payable_amount);
	echo  '<div class="days">';
	if(!isset($_REQUEST['action_edit']))
	{
		echo  '<p><label>'; _e('Charges: ','templatic');echo  '</label><span>'; echo display_amount_with_currency_plugin($payable_amount);echo ' ';
	}
	/*show particular price package period or days*/
	if(@$package_id)
		tmpl_show_package_period($package_id);
	if(@get_post_meta($package_id,'package_amount',true))
		echo  '</span>'; 
	if($paymentmethod == '')
	{
		$paymentmethod = __('Free','templatic');
	}
	echo '<p class="panel-type price payment_method"><label>'; _e('Payment Method: ','templatic'); echo '</label>'; echo '<span>'; echo ucfirst($paymentmethod); echo '</span> </p>';
	echo '</div>';

	
}

/* code to add add to favourites on author dash board */
function tmpl_dashboard_favourites_tab(){
	global $current_user,$curauth,$wp_query;	
	$qvar = $wp_query->query_vars;
	$author = $qvar['author'];
	if(isset($author) && $author !='') :
		$curauth = get_userdata($qvar['author']);
	else :
		$curauth = get_userdata(intval($_REQUEST['author']));
	endif;	
	if(isset($_REQUEST['sort']) && $_REQUEST['sort'] =='favourites'){
		$class = 'active';
	}else{
		$class ='';
	}
	
	if($current_user->ID == $curauth->ID){
		echo "<li role='presentational' class='tab-title ".$class."'><a class='author_post_tab ' href='".esc_url(get_author_posts_url($current_user->ID).'?sort=favourites&custom_post=all')."'>";
		echo _e('My Favorites','templatic');
		echo "</a></li>";
	}
	
}
/* add filter to fetch favourites post listing on admin dashboard page */
if(isset($_REQUEST['sort']) && $_REQUEST['sort'] =='favourites'){
	global $current_user,$curauth,$wp_query,$sitepress;
	add_filter('posts_join','tevolution_favourites_post_join',12);
	add_filter('posts_where','tevolution_favourites_post',12);
}

/*
* start function to list - favourites post on dashboard
*/
function tevolution_favourites_post(){
	global $wpdb,$current_user,$curauth,$wp_query;
	
	$where = '';
	$query_var = $wp_query->query_vars;
	$user_id = $query_var['author'];
	$post_ids = get_user_meta($current_user->ID,'user_favourite_post',true);
	$final_ids = '';
	if(!empty($post_ids))
	{
		$post_ids = implode(",",$post_ids);
	}
	else
	{
	 	$post_ids = "''";
	}
	$qvar = $wp_query->query_vars;
	$authname = $qvar['author_name'];
	$curauth = get_userdata($qvar['author']);
	$nicename = $current_user->user_nicename;
	
	if($_REQUEST['sort']=='favourites')	{
		$where .= " AND ($wpdb->posts.ID in ($post_ids))";			
	}else
	{	
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			$language = ICL_LANGUAGE_CODE;
			$where = " AND ($wpdb->posts.post_author = $user_id)  AND t.language_code='".$language."'";
		}else{
			$where = " AND ($wpdb->posts.post_author = $user_id) ";
		}
	}	
	return $where;
}
/*
* show message while licence key is not verified in front end.
*/
add_action('wp_head','tevolution_licence_message');
function tevolution_licence_message(){
	if(!is_admin() && !strstr($_SERVER['REQUEST_URI'],'wp-admin/')){
		$templatic_licence_key = get_option('templatic_licence_key');
		if(strstr($templatic_licence_key,'error_message') || !get_option('templatic_licence_key_'))
		{
			if(!get_option('templatic_licence_key_'))
			{
				echo "<h2 style='align-items: center;bottom: 0;display: flex;font-size: 24px;justify-content: center;position: absolute;text-align: center;top: 0;width: 100%;'>".__('Your copy of Templatic product hasn\'t been verified yet. To verify the product and unlock the site please <a href="'.admin_url( 'admin.php?page=templatic_system_menu').'" style="color:red;"> click here </a> to verify your licence key','templatic')."</h2>";
			}else{
				echo "<h2>".__('You are not allowed to run this site, because of invalid licence key. <a href="'.admin_url( 'admin.php?page=templatic_system_menu').'">click here</a> to verify your valid licence key','templatic')."</h2>";
			}
			die;
		}
	}
}

/* 
* add action for send to friend and send inquiry email - specially in tevolution templates 
*/
add_action('templ_after_post_content','tevolution_dir_popupfrms');
if(!function_exists('tevolution_dir_popupfrms')){
	function tevolution_dir_popupfrms($post){
		global $current_user,$post;
		$tmpdata = get_option('templatic_settings');	
		$link='';	
		
		/* Claim ownership link */
		if(is_single())
		{
			if(!empty($tmpdata['claim_post_type_value'])&& @in_array($post->post_type,$tmpdata['claim_post_type_value']) && function_exists('tmpl_claim_ownership') && @$post->post_author!=@$current_user->ID)
			{
				/*
					We add filter here so if you are creating a child theme and don't want to show here, then just remove from child theme.
					e.g. add_filter('tmpl_allow_claimlink_inlist',0);
				*/
				$allow_claim = apply_filters('tmpl_allow_claimlink_inlist',1);
				do_action('tmpl_before_claim');
				if($allow_claim && get_post_meta($post->ID,'is_verified',true) !=1){
					echo '<li class="claim_ownership">';
					echo	do_shortcode('[claim_ownership]');
					echo '</li>';
				}
			}
			
			if(isset($tmpdata['send_to_frnd'])&& $tmpdata['send_to_frnd']=='send_to_frnd' && function_exists('send_email_to_friend'))
			{
				/*
					We add filter here so if you are creating a child theme and don't want to show here, then just remove from child theme.
					e.g. add_filter('tmpl_sent_to_frd_link','');
				*/
				do_action('tmpl_before_send_tofrd');
				$send_to_frnd=	apply_filters('tmpl_sent_to_frd_link','<a class="small_btn tmpl_mail_friend" data-reveal-id="tmpl_send_to_frd" href="javascript:void(0);" id="send_friend_id"  title="'.__('Mail to a friend','templatic').'" >'. __('Send to friend','templatic').'</a>');				
				
				add_action('wp_footer','send_email_to_friend',10);
				echo "<li>".$send_to_frnd.'</li>';
			}
				
			/* sent inquiry link*/
			
			if(isset($tmpdata['send_inquiry'])&& $tmpdata['send_inquiry']=='send_inquiry' && function_exists('send_inquiry'))
			{		
				/*
					We add filter here so if you are creating a child theme and don't want to show here, then just remove from child theme.
					e.g. add_filter('tmpl_send_inquiry_link','');
				*/
				do_action('tmpl_before_send_inquiry');
				$send_inquiry=	apply_filters('tmpl_send_inquiry_link','<a class="small_btn tmpl_mail_friend" data-reveal-id="tmpl_send_inquiry"  href="javascript:void(0)" title="'.__('Send Inquiry','templatic').'" id="send_inquiry_id" >'.__('Send inquiry','templatic').'</a>');
				add_action('wp_footer','send_inquiry');		
				echo '<li>'.$send_inquiry.'</li>';
			} 
		
			/* Add to favourites */
			if(current_theme_supports('tevolution_my_favourites') && ($post->post_status == 'publish' )){
				global $current_user;
				$user_id = $current_user->ID;
				do_action('tmpl_before_addtofav');
				$link.= apply_filters('tmpl_add_to_favlink',tmpl_detailpage_favourite_html($user_id,@$post));
				echo $link;
				
			}
		}
	}
}

/*
* return the social media links of current post
*/

add_action('tevolution_socialpost_link','tevolution_socialpost_link_returns');
if(!function_exists('tevolution_socialpost_link')){
	function tevolution_socialpost_link($post){
		global $htmlvar_name,$tmpl_flds_varname;
		
		$is_edit='';
		if(isset($_REQUEST['action']) && $_REQUEST['action']=='edit'){
			$is_edit=1;
		}
		$facebook=get_post_meta($post->ID,'facebook',true);
		$facebook_show = apply_filters('tmpl_fb_share_link',1);
		$google_plus=get_post_meta($post->ID,'google_plus',true);
		$google_plus_show = apply_filters('tmpl_google_plus_share_link',1);
		$twitter=get_post_meta($post->ID,'twitter',true);
		$twitter_show=apply_filters('tmpl_twitter_share_link',1);
		echo '<div class="share_link">';
		do_action('tmpl_before_social_share_link');
		if($facebook!="" && $facebook_show && ((@$htmlvar_name['contact_info']['facebook'] || $tmpl_flds_varname['facebook']) || ($is_edit==1 && (@$htmlvar_name['contact_info']['facebook']) || $tmpl_flds_varname['facebook']))):
		if(!empty($facebook) && !strstr($facebook,'http'))
			$facebook = 'http://'.$facebook;
		?>
	 	<span><a id="facebook" class="frontend_facebook <?php if($is_edit==1):?>frontend_link <?php endif;?>" href="<?php echo $facebook;?>"><i class="fa fa-facebook"></i> Facebook</a></span>
		<?php endif;
	 
		if($twitter!="" && (@$htmlvar_name['contact_info']['twitter'] || $tmpl_flds_varname['twitter']) && $twitter_show ==1 || ($is_edit==1 && (@$htmlvar_name['contact_info']['twitter'] || $tmpl_flds_varname['twitter']))):
	 	if(!empty($twitter) && !strstr($twitter,'http'))
			$twitter = 'http://'.$twitter;
	 	?>
	 	<span><a id="twitter" class="frontend_twitter <?php if($is_edit==1):?>frontend_link <?php endif;?>" href="<?php echo $twitter;?>"><i class="fa fa-twitter"></i> Twitter</a></span>
		<?php endif;?>

		<?php if($google_plus!="" && (@$htmlvar_name['contact_info']['google_plus'] || $tmpl_flds_varname['google_plus']) && $google_plus_show ==1 || ($is_edit==1 && (@$htmlvar_name['contact_info']['google_plus']  || $tmpl_flds_varname['google_plus']))):
	 	if(!empty($google_plus) && !strstr($google_plus,'http'))
			$google_plus = 'http://'.$google_plus;
		?>
		<span><a id="google_plus" class="frontend_google_plus <?php if($is_edit==1):?>frontend_link <?php endif;?>" href="<?php echo $google_plus;?>"><i class="fa fa-google-plus"></i> Google Plus</a></span>
		<?php endif;
		do_action('tmpl_after_social_share_link');
	echo '</div>';
	}
}

/*
* Social media share link
*/
if(!function_exists('tevolution_socialmedia_sharelink')){
function tevolution_socialmedia_sharelink($post){
	$tmpdata = get_option('templatic_settings');	
	$title=($post->post_title);
	$post_img = bdw_get_images_plugin($post->ID,'thumb');
	$post_images = @$post_img[0]['file'];
	$url=(get_permalink($post->ID));
	$image=$post_images;
	if(@$tmpdata['google_share_detail_page'] == 'yes' || @$tmpdata['twitter_share_detail_page'] == 'yes' || @$tmpdata['pintrest_detail_page']=='yes')
	{?>
	<ul class='social-media-share'>
	<?php if($tmpdata['facebook_share_detail_page'] == 'yes') { ?>
		<li><div class="facebook_share" data-url="<?php echo $url; ?>" data-text="<?php echo $title; ?>"></div></li>
	<?php }
	
	if($tmpdata['twitter_share_detail_page'] == 'yes'): ?>
		<li><div class="twitter_share"  data-url="<?php echo $url; ?>" data-text="<?php echo $title; ?>"></div></li> 
	<?php endif; 
	if($tmpdata['google_share_detail_page'] == 'yes'): ?>
	    <li><div class="googleplus_share" href="javascript:void(0);"  data-url="<?php echo $url; ?>" data-text="<?php echo $title; ?>"></div></li>
	<?php endif;

	if(@$tmpdata['pintrest_detail_page']=='yes'):?>
		<li><div class="pinit_share" data-href="http://pinterest.com/pin/create/button/?url=<?php urlencode(the_permalink()); ?>" data-media="<?php echo $post_images; ?>" data-description="<?php the_title(); ?> - <?php the_permalink(); ?>"></div></li>
	<?php endif; ?>   
	</ul>
	<script type="text/javascript">
		var jQuery = jQuery.noConflict();
		jQuery( document ).ready(function() {
			jQuery('.twitter_share').sharrre({
			  share: {
				twitter: true
			  },
			  template: '<a class="box" href="#"><span class="share"><i class="step fa fa-twitter"></i></span> <span class="count" href="#">{total} <span class="showlabel"> '+TWEET+'</span></span></a>',
			  enableHover: false,
			  enableTracking: true,
			  buttons: { twitter: {}},
			  click: function(api, options){
				api.simulateClick();
				api.openPopup('twitter');
			  }
			});
			jQuery('.facebook_share').sharrre({
			  share: {
				facebook: true
			  },
			  template: '<a class="box" href="#"><span class="share"><i class="step fa fa-facebook"></i></span> <span class="count" href="#">{total}<span class="showlabel">&nbsp;'+FB_LIKE+'</span></span></a>',
			  enableHover: false,
			  enableTracking: true,
			  click: function(api, options){
				api.simulateClick();
				api.openPopup('facebook');
			  }
			});
			jQuery('.googleplus_share').sharrre({
			  share: {
				googlePlus: true
			  },
			  template: '<a class="box" href="#"><span class="share"><i class="fa fa-google-plus"></i> </span> <span class="count" href="#">{total} <span class="showlabel">+1</span></span></a>',
			  enableHover: false,
			  enableTracking: true,
			  urlCurl: '<?php echo TEMPL_PLUGIN_URL?>/tmplconnector/sharrre.php',
			  click: function(api, options){
				api.simulateClick();
				api.openPopup('googlePlus');
			  }
			});
			jQuery('.pinit_share').sharrre({
			  share: {
				pinterest: true
			  },
			  template: '<a class="box" href="#"><span class="share"><i class="fa fa-pinterest"></i></span> <span class="count" href="#">{total} <span class="showlabel"> '+PINT_REST+'</span></span></a>',
			  enableHover: false,
			  enableTracking: true,
			  urlCurl: '<?php echo TEMPL_PLUGIN_URL?>/tmplconnector/sharrre.php',
			  click: function(api, options){
				api.simulateClick();
				
			  }
			});
			jQuery('.pinit_share').on('click', function(e) {
				var $this = jQuery(this),


				media = encodeURI($this.data('media')),
				description = encodeURI($this.data('description'));
				 
					e.preventDefault();
					 
					window.open(
						jQuery(this).attr('data-href') + '&media=' + media + '&description=' + description,
						'pinterestDialog',
						'height=400, width=700, toolbar=0, status=0, scrollbars=1'
					);
			});
		});

	</script>
<?php 	
		
		} 
	}
}
?>