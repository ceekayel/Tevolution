<?php
/* File contain the form of add/edit the custom fields */
global $wpdb,$current_user;

/* Get the last custom field sort order number */ 
$query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type ='custom_fields' ";
$count_custom_fields=$wpdb->get_results($query);
$sort_order=$count_custom_fields[0]->num_posts; 
/* Finish the get the last custom filed sort order number*/


if(isset($_REQUEST['lang']) && $_REQUEST['lang']!=''){
	$post_field_id = $_REQUEST['trid']; /* to fetch th all original fields value for translation */
	$post_val = get_post($post_field_id);
	
}			
if(isset($_REQUEST['field_id'])){
	$post_field_id = $_REQUEST['field_id'];
	$post_id = $_REQUEST['field_id'];
	$post_val = get_post($post_id);
}else{
	if(!isset($_REQUEST['lang']) && $_REQUEST['lang']=='')
		$post_val='';
}
 
if(isset($_POST['submit-fields']) && $_POST['submit-fields'] !='')
{ 
	//echo '<pre>';print_r($_POST);echo '</pre>';die;
	global $wpdb;
	/* clear transient for all tev query - so user don't need to clear cache again n gain */
	$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name like '%s'",'%_tevolution_query_%' ));
	$ctype = $_POST['ctype'];
	$admin_title = $_POST['admin_title'];
	$htmlvar_name = $_POST['htmlvar_name'];
	$admin_desc = $_POST['admin_desc'];
	$default_value = $_POST['default_value'];
	$sort_order = $_POST['sort_order'];
	
	$ptype = $_POST['post_type_sel'];
	$option_values = $_POST['option_values'];
	$show_on_page = $_POST['show_on_page'];
	$extra_parameter = $_POST['extra_parameter'];
	$validation_type = $_POST['validation_type'];
	$field_require_desc = stripslashes($_POST['field_require_desc']);
	$style_class = $_POST['style_class'];
	
	
	$_POST['is_require'] = (isset($_POST['is_require']))? $_POST['is_require'] :0;
	$_POST['is_active'] = (isset($_POST['is_active']))? $_POST['is_active'] :0;
	$_POST['show_on_listing'] = (isset($_POST['show_on_listing']))? $_POST['show_on_listing'] :0;
	$_POST['show_on_detail'] = (isset($_POST['show_on_detail']))? $_POST['show_on_detail'] :0;
	$_POST['show_on_success'] = (isset($_POST['show_on_success']))? $_POST['show_on_success'] : 0;
	$_POST['show_in_column'] = (isset($_POST['show_in_column']))? $_POST['show_in_column'] :0;
	$_POST['show_in_email'] = (isset($_POST['show_in_email']))? $_POST['show_in_email'] :0;
	$_POST['is_search'] = (isset($_POST['is_search']))? $_POST['is_search'] :0;
	$_POST['is_submit_field'] = (isset($_POST['is_submit_field']))? $_POST['is_submit_field'] :0;
	
	$is_delete = $_POST['is_delete'];
	$is_edit = $_POST['is_edit'];
	
	
	if(isset($_REQUEST['field_id']))
	{   /* when edit the field */
		$post_type = $_POST['post_type_sel'];
	
		
		/* code for - when we update the heading type - all related fields should be assign to same heading type 
		
		Here we do this because we assign the heading type with title , if admin change the title all fields will be not assign to same heading, whcih should be
		*/
		$title = @$_POST['admin_title'];
						
		if($_POST['ctype'] =='heading_type'){
			
			if(count($post_type) > 0)
			{
				foreach($post_type as $_post_type)
				{ 
					$post_type_ex = explode(",",$_post_type);
					$old_heading_type = get_post($post_id);
					
					if($old_heading_type->post_title != @$_POST['admin_title']){ 
						$args=array('post_type'      => 'custom_fields','meta_key'=>$post_type_ex[0].'_heading_type','meta_value' => $old_heading_type->post_title,
								'posts_per_page' => -1	,
								'post_status'    => array('publish'));
						$custom_query = new WP_Query($args);
						
						if($custom_query->have_posts()){
							while ($custom_query->have_posts()) : $custom_query->the_post();global $post;
								
								update_post_meta($post->ID, $post_type_ex[0].'_heading_type',trim($title));
							endwhile;
						}
					}
				}
				
			} 
		}
		/* code end */
		

		$postdata = get_post($_REQUEST['field_id']);
		$my_post = array(
		 'post_title' => $admin_title,
		 'post_content' => $admin_desc,
		 'post_status' => 'publish',
		 'post_author' => 1,
		 'post_type' => "custom_fields",
		 'post_name' => $postdata->post_name,
		 'ID' => $_REQUEST['field_id'],
		);
		global $post_id;
		$post_id = wp_insert_post( $my_post );
		/* Finish the place geo_latitude and geo_longitude in postcodes table*/
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		$wpdb->delete( "$wpdb->term_relationships", array( 'object_id' => $post_id ,'term_taxonomy_id' => 1), array( '%d' ,'%d') );		
		if(isset($_POST['post_type_sel']) && $_POST['post_type_sel']!=""){
			$tax = $_POST['post_type_sel'];
			foreach($tax as $key=> $_tax)
			{
				$taxexp = explode(",",$_tax);
				wp_delete_object_term_relationships( $post_id, $taxexp[1] ); 
			}	
		}		
		if(!$_POST['category']){
			update_post_meta($post_id,'field_category','');	
		}
		
                if(isset($_POST['category']) && $_POST['category'] !=''){
                        $tax = $_POST['post_type_sel'];
                        foreach($tax as $key=> $_tax)
                        {
                                $taxexp = explode(",",$_tax);
                                wp_delete_object_term_relationships( $post_id, $taxexp[1] ); 
                                if($taxexp[1] != 'all')
                                  {
                                        foreach($_POST['category'] as $category)
                                         {
                                                $term = get_term_by('id',$category,$taxexp[1]);
                                                if(!empty($term)){

                                                        wp_set_post_terms($post_id,$category,$taxexp[1],true);
                                                }

                                         }
                                  }
                        }
                }
              
	
		foreach($_POST as $key=>$meta_value)
		 {
			if($key != 'save' && $key != 'category' && $key != 'admin_title' && $key != 'post_type' && $key != 'admin_desc' && $key != 'htmlvar_name' && $key!='sort_order' && $key!='heading_type')
			 {				 
				 if(!is_array($meta_value))
					update_post_meta($post_id, $key, rtrim($meta_value,","));
				 else
				 	update_post_meta($post_id, $key, $meta_value);
			 }
		 }

		 $option_title_array = array('radio','select','multicheckbox');
		 if(isset($_POST['search_option_values']) && $_POST['search_option_values']!='' && isset($_POST['search_option_title']) && $_POST['search_option_title']!='' && @$_POST['is_search'] !='' && !in_array($_POST['ctype'],$option_title_array)){
			 update_post_meta($post_id, 'option_title', rtrim($_POST['search_option_title'],','));
			 update_post_meta($post_id, 'option_values', rtrim($_POST['search_option_values'],','));
		 }else{
			 update_post_meta($post_id, 'option_title', rtrim($_POST['option_title'],','));
			 update_post_meta($post_id, 'option_values', rtrim($_POST['option_values'],','));
		 }
		 $post_type = $_POST['post_type_sel'];
		 $total_post_type = get_option('templatic_custom_post');
		 delete_post_meta($post_id, 'post_type_post');
		 delete_post_meta($post_id, 'taxonomy_type_category');
		 foreach($total_post_type as $key=> $_total_post_type)
		 {
			delete_post_meta($post_id, 'post_type_'.$key.'');
			delete_post_meta($post_id, 'taxonomy_type_'.$_total_post_type['slugs'][0].'');
		 }
	

		if(count($post_type) > 0)
		{
			 foreach($post_type as $_post_type)
			 {
				 $post_type_ex = explode(",",$_post_type);
				 update_post_meta($post_id, 'post_type_'.$post_type_ex[0].'', $post_type_ex[0]);
				 update_post_meta($post_id, 'taxonomy_type_'.$post_type_ex[1].'', $post_type_ex[1]);
				 $finpost_type .= $post_type_ex[0].",";
				 $_heading_type1 = array();
				 
				/* Fetch Heading type custom fields */
				$heading_type1 = fetch_heading_per_post_type($post_type_ex[0]);
				
				/* get heading type attached to post type */
				foreach($heading_type1 as $_heading_type){
					$_heading_type1[] = $_heading_type;
				}
				 
				 if(!isset($_POST['heading_type']) &&  get_post_meta($post_id, $post_type_ex[0].'_heading_type',true) ==''){
					$heading_type = '[#taxonomy_name#]';
				 }else if(get_post_meta($post_id, $post_type_ex[0].'_heading_type',true) !=''){
					$heading_type = (is_array($_heading_type1) && !empty($_heading_type1) && in_array(get_post_meta($post_id, $post_type_ex[0].'_heading_type',true),$_heading_type1)) ? get_post_meta($post_id, $post_type_ex[0].'_heading_type',true) : '[#taxonomy_name#]';
				 }else{
					
					/* check if given heading type is availabe in it or not. If not then apply taxonomy name to heading type */
					$heading_type = (is_array($_heading_type1) && !empty($_heading_type1) && in_array($_POST['heading_type'],$_heading_type1)) ? $_POST['heading_type'] : '[#taxonomy_name#]';
				 }
				 update_post_meta($post_id, $post_type_ex[0].'_heading_type',$heading_type);
				 update_post_meta($post_id, 'search_sort_order', $_POST['sort_order']);
				 if(!get_post_meta($post_id, $post_type_ex[0].'_sort_order')){					 
					update_post_meta($post_id, $post_type_ex[0].'_sort_order', $_POST['sort_order']);
				 }
				
			 }
		 }		  

		 update_post_meta($post_id, 'post_type',substr($finpost_type,0,-1));
                 
                 $category_string = '';
                 if(isset($_POST['selectall_value']) && $_POST['selectall_value'] == 'all'){
                    $category_string = 'all,';
                 }
		 if(isset($_POST['category']) && $_POST['category']!=''){
                     $category_string = $category_string . implode(",",$_POST['category']);
                     update_post_meta($post_id,"field_category",$category_string);
		 }
		$msgtype = 'edit';
	}else
	{
		global $wpdb;
		/* clear transient for all tev query - so user don't need to clear cache again n gain */
		$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name like '%s'",'%_tevolution_query_%' ));
		$my_post = array(
		 'post_title' => $admin_title,
		 'post_content' => $admin_desc,
		 'post_status' => 'publish',
		 'post_author' => 1,
		 'post_type' => "custom_fields",
		 'post_name' => $htmlvar_name,
		);
		$post_id = wp_insert_post( $my_post );
		/* Finish the place geo_latitude and geo_longitude in postcodes table*/
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			if(!isset($_REQUEST['icl_trid']) && $_REQUEST['icl_trid']==''){
				$_REQUEST['icl_trid']=$post_id;
			}			
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $_REQUEST['icl_trid'], $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		$tax = $_POST['post_type_sel'];
		if($tax!="" || !empty($tax)){
			foreach($tax as $key=> $_tax)
			{
				if(isset($_POST['category']) && $_POST['category']!="")
				{
					 $taxexp = explode(",",$_tax);
					 if($taxexp[1] != 'all')
					   {
						 foreach($_POST['category'] as $category)
						 {
							wp_set_post_terms($post_id,$category,$taxexp[1],true);
						 }
					   }
				}
			}
		}
		foreach($_POST as $key=>$meta_value)
		 {
			if($key != 'save' && $key != 'category' && $key != 'admin_title' && $key != 'post_type' && $key != 'admin_desc')
			 {
				 if(!is_array($meta_value))
					add_post_meta($post_id, $key, rtrim($meta_value,","));
				 else
				 	add_post_meta($post_id, $key, $meta_value);				
			 }
		 }
		 
		 if(isset($_POST['search_option_values']) && $_POST['search_option_values']!='' && isset($_POST['search_option_title']) && $_POST['search_option_title']!='' && @$_POST['is_search'] !=''){
			 update_post_meta($post_id, 'option_title', rtrim( $_POST['search_option_title'],','));
			 update_post_meta($post_id, 'option_values', rtrim($_POST['search_option_values'],','));
		 }else{
			 update_post_meta($post_id, 'option_title', rtrim($_POST['option_title'],','));
			 update_post_meta($post_id, 'option_values', rtrim($_POST['option_values'],','));
		 }
		 
		 if(isset($_POST['post_type_sel']) && $_POST['post_type_sel']!="")
		 {
			 $post_type = $_POST['post_type_sel'];
			 foreach($post_type as $_post_type)
			  {				 
					 $post_type_ex = explode(",",$_post_type);
				
					update_post_meta($post_id, 'taxonomy_type_'.$post_type_ex[1].'', $post_type_ex[1]);
			
					if(in_array('all',$post_type_ex))
					{
						update_post_meta($post_id, 'post_type_'.$post_type_ex[0].'', 'all');
					}else{
						update_post_meta($post_id, 'post_type_'.$post_type_ex[0].'', $post_type_ex[0]);
					}
					 
					$finpost_type .= $post_type_ex[0].",";
					
					update_post_meta($post_id, $post_type_ex[0].'_sort_order', $_POST['sort_order']);
					
					if(!get_post_meta($post_id, 'search_sort_order',true)){
					
						update_post_meta($post_id, 'search_sort_order', $_POST['sort_order']);
					}
					update_post_meta($post_id, $post_type_ex[0].'_heading_type', $_POST['heading_type']);
				  
			  }
			 update_post_meta($post_id, 'post_type',substr($finpost_type,0,-1));
		 }
		 
		 
		 $category_string = '';
                 if(isset($_POST['selectall_value']) && $_POST['selectall_value'] == 'all'){
                    $category_string = 'all,';
                 }
		 if(isset($_POST['category']) && $_POST['category']!=''){
                     $category_string = $category_string . implode(",",$_POST['category']);
                     update_post_meta($post_id,"field_category",$category_string);
		 }
			 
		 $msgtype = 'add';
	}
	
	if(isset($_POST['ctype']) && $_POST['ctype']=='heading_type'){
		delete_post_meta($post_id,'heading_type');
	}
	
	update_option('tevolution_query_cache',1);
	$location = site_url().'/wp-admin/admin.php';
	echo '<form action="'.$location.'" method="get" id="frm_edit_custom_fields" name="frm_edit_custom_fields">
				<input type="hidden" value="custom_setup" name="page">
				<input type="hidden" value="custom_fields" name="ctab">
				<input type="hidden" value="success" name="custom_field_msg"><input type="hidden" value="'.$msgtype.'" name="custom_msg_type">
		  </form>
		  <script>document.frm_edit_custom_fields.submit();</script>';
		  exit;
}

/*
	Return Validation type on manage/Add custom fields form
*/
function validation_type_cmb_plugin($validation_type = ''){
	$validation_type_display = '';
	$validation_type_array = array(" "=>__("Select validation type",'templatic'),"require"=>__("Require",'templatic'),"phone_no"=>__("Phone No.",'templatic'),"digit"=>__("Digit",'templatic'),"email"=>__("Email",'templatic'));
	foreach($validation_type_array as $validationkey => $validationvalue){
		if($validation_type == $validationkey){
			$vselected = 'selected';
		} else {
			$vselected = '';
		}
		$validation_type_display .= '<option value="'.$validationkey.'" '.$vselected.'>'.__($validationvalue,'templatic').'</option>';
	}
	return $validation_type_display;
}

$tmpdata = get_option('templatic_settings');
?>
<script type="text/javascript" async >
var is_showcat=null;
function showcat(str,scats)
{
	if (str=="")
	{
		document.getElementById("field_category").innerHTML="";
		return;
	}else{
		document.getElementById("field_category").innerHTML="";
		document.getElementById("process").style.display ="block";
	}
	  
	var valarr = '';
	if(str == 'all,all')
	{
		var valspl = str.split(",");
		valarr = valspl[1];
	}else{
		var val = [];
		var valfin = '';			
		jQuery("tr#post_type input[name='post_type_sel[]']").each(function() {
			if (jQuery(this).attr('checked'))
			{	
				val = jQuery(this).val();
				valfin = val.split(",");
				valarr+=valfin[1]+',';
			}
		});
	}		
		
	if(valarr==''){ valarr ='all'; }
	<?php
	$language='';
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		global $sitepress;
		$current_lang_code= ICL_LANGUAGE_CODE;
		$language="&language=".$current_lang_code;
	}?>
	
	 is_showcat=jQuery.ajax({
	 url: ajaxUrl,
	 type:'POST',
	 async: true,
	 data:'action=tmpl_ajax_custom_taxonomy&post_type='+valarr+'&scats='+scats+'&page=custom_setup&ctab=custom_fields<?php echo $language;?>',
	 beforeSend : function(){
			if(is_showcat != null){
				is_showcat.abort();
			}
        },
	 success:function(result){
		 document.getElementById("process").style.display ="none";
		 document.getElementById("field_category").innerHTML=result;
	 }
	});	
}
function displaychk_frm()
{
	dml = document.forms['custom_fields_frm'];
	chk = dml.elements['category[]'];
	len = dml.elements['category[]'].length;
	
	if(document.getElementById('selectall').checked == true) { 
		for (i = 0; i < len; i++)
		chk[i].checked = true ;
                document.getElementById("selectall_value").value = 'all';
	} else { 
		for (i = 0; i < len; i++)
		chk[i].checked = false ;
                document.getElementById("selectall_value").value = '';
	}
}
jQuery(document).ready(function(){
    jQuery('#category_checklist input:checkbox').click(function() {
        var total = jQuery('#category_checklist input:checkbox').length;
        var checked = jQuery('#category_checklist input:checkbox:checked').length;

        if(total === checked){
            jQuery('#selectall').prop('checked', true);
            document.getElementById("selectall_value").value = 'all';
        }else{
            jQuery('#selectall').prop('checked', false);
            document.getElementById("selectall_value").value = '';
        }
        //displaychk_frm();
    });
});
function selectall_posttype()
{
	dml = document.forms['custom_fields_frm'];
	chk = dml.elements['post_type_sel[]'];
	len = dml.elements['post_type_sel[]'].length;
	
	if(document.getElementById('selectall_post_type').checked == true) { 
		for (i = 0; i < len; i++)
		chk[i].checked = true ;
	} else { 
		for (i = 0; i < len; i++)
		chk[i].checked = false ;
	}
}
</script>
<div class="wrap">

	<div id="icon-edit" class="icon32 icon32-posts-post"></div>
    <h2>
	<?php 
	if(isset($_REQUEST['field_id']) && $_REQUEST['field_id'] != ''){  
		_e('Edit - '.$post_val->post_title,'templatic');
	}else{ 
		echo __('Add a new field','templatic-admin');
	}
	
	$custom_msg = sprintf(__('Use this section to define new fields for your submission forms. Fields can be created for all posts typed created using the  section.','templatic-admin'),'<a href="'.admin_url('admin.php?page=custom_setup').'" target="_blank" title="Custom Field Guide">Custom Post Types</a>');
	?>    
	<a id="edit_custom_user_custom_field" href="<?php echo site_url();?>/wp-admin/admin.php?page=custom_setup&ctab=custom_fields" name="btnviewlisting" class="add-new-h2" title="<?php _e('Back to manage custom fields','templatic');?>"/><?php echo __('Back to manage custom field list','templatic-admin'); ?></a>
    </h2>    
    <p class="tevolution_desc"><?php echo $custom_msg;?></p>
	<!-- Function to fetch categories -->

	<form class="form_style" action="<?php echo site_url();?>/wp-admin/admin.php?<?php echo $_SERVER['QUERY_STRING'];?>" method="post" name="custom_fields_frm" onsubmit="return chk_field_form();">
	<?php
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		echo '<input type="hidden" name="icl_post_language" value="'. @$_REQUEST['lang'].'" />';	
		echo '<input type="hidden" name="icl_trid" value="'. @$_REQUEST['trid'].'" />';	
		echo '<input type="hidden" name="icl_translation_of" value="'. @$_REQUEST['trid'].'" />';			
	}
	$html_var = get_post_meta($post_field_id,"htmlvar_name",true);
	?>
	
	<input type="hidden" name="save" value="1" /> 
    <input type="hidden" name="is_delete" value="<?php if($post_val){ echo get_post_meta($post_field_id,"is_delete",true); }?>" />
	<?php if(@$_REQUEST['field_id']){?>
		<input type="hidden" name="field_id" value="<?php echo $_REQUEST['field_id'];?>" />
	<?php
		$is_edit=get_post_meta($post_field_id,'is_edit',true);
		$is_ctype=get_post_meta($post_field_id,"ctype",true);		
		$htmlvar_name=get_post_meta($post_field_id,"htmlvar_name",true);
		$readonly_fields=apply_filters('tmpl_allow_readonly_fields',array('map_view'));/* array for showing readonly fields */
		$exclude_show_fields=array('post_title','category');
		if($is_ctype=='heading_type'){
			$exclude_show_fields[]=$htmlvar_name;
		}
		
		$exclude_show_fields=apply_filters('exclude_show_fields',$exclude_show_fields,$htmlvar_name);
		
		$post_types = array();
		if( @$_REQUEST['field_id'] || @$_REQUEST['lang'] )
		{
			$post_types = explode(",",get_post_meta($post_field_id,'post_type',true));
		}
		
	}?>
     <table class="form-table" id="form_table">       
		<tbody>
            <tr>
				<th colspan="2">
					<div class="tevo_sub_title" style="margin-top:0px"><?php echo __("Basic Options",'templatic-admin');?></div>
				</th>
			</tr>
            <?php do_action('customfields_before_post_type');/*customfields_before_post_type hook add additional custom field */?>
			<tr id="post_type"  style="display:block;" >
            	<th>
                	<label for="post_name" class="form-textfield-label"><?php echo __('Enable for','templatic-admin');?><span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
            	</th>
            	<td>
               	<?php				
				$custom_post_types = apply_filters('tmpl_allow_custofields_posttype',get_option("templatic_custom_post"));
				$i = 0;	
				$scats = get_post_meta($id,"field_category",true);	
				if($scats ==''){
					$scats ='0';
				}
				
				?>
               	<fieldset>				
				<label for="selectall_post_type"><input type="checkbox" name="post_type_sel[]" id="selectall_post_type" onClick="showcat(this.value,'<?php echo $scats; ?>');selectall_posttype();" value="all,all" />&nbsp;<?php echo __('Select All', 'templatic-admin');?></label><br />
				
				<label for="post_type_post">
					<input type="checkbox" name="post_type_sel[]" id="post_type_post" onClick="showcat(this.value,'<?php echo $scats; ?>');" value="post,category" <?php if(!empty($post_types) && in_array('post',$post_types) || @$post_field_id== '') {	$post_types[]= 'post'; ?> checked="checked" <?php } ?> />
					<?php echo 'Post';?>
				</label><br />
				<?php
				foreach ($custom_post_types as $content_type=>$content_type_label) {
					?>
						
					<label for="post_type_<?php echo $i; ?>"><input type="checkbox" name="post_type_sel[]" id="post_type_<?php echo $i; ?>" onClick="showcat(this.value,'<?php echo $scats; ?>');" value="<?php if(isset($content_type_label['slugs'][0]) || isset($content_type)) { echo $content_type.",".$content_type_label['slugs'][0]; } ?>" <?php if(in_array($content_type,$post_types) || @$post_field_id== '') { $post_types[]= $content_type;?> checked="checked" <?php } ?> />
						<?php echo $content_type_label['label'];?></label><br />
						
				<?php				
				$i++;	
				} ?>
                    </fieldset>
                    <p class="description"><?php echo __('The field you&rsquo;re creating will only work for the post types you select above', 'templatic-admin');?></p>
			</td>
			</td>
		 </tr>
         <?php do_action('customfields_after_post_type'); /*customfields_after_post_type hook add additional custom field */?>
         
         <?php do_action('customfields_before_category'); /*customfields_before_category hook add additional custom field */?>
		 <tr style="display:block;">
            <th>
                <label for="post_slug" class="form-textfield-label"><?php echo __('Select the categories','templatic-admin'); ?> <span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
            </th>
            <td>
                <div class="element cf_checkbox wp-tab-panel" id="field_category" style="width:300px;overflow-y: scroll; margin-bottom:5px;">
                    <label for="selectall">
                        <?php
                        $is_select_all = '';
                        $field_category = get_post_meta($post_field_id,"field_category",true);
                        if(strpos($field_category,'all') !== false){
                            $is_select_all = 'all';
                        }
                        ?>
                    	<input type="checkbox" name="selectall" id="selectall" class="checkbox" onclick="displaychk_frm();" <?php echo ($is_select_all=='all') ? 'checked' : '';?>/>&nbsp;<?php if(is_admin()){  echo __('Select All',	'templatic-admin'); }else{ _e('Select All',	'templatic'); } ?></label>
                        <input type="hidden" name="selectall_value" id="selectall_value" value="<?php echo ($is_select_all=='all') ? 'all' : '';?>"/>
                        <ul id="category_checklist" data-wp-lists="list:listingcategory" class="categorychecklist form_cat">
                        <?php
						
						if(!empty($post_types))
							$post_types=array_unique($post_types); /*Remove duplicate post type value */						
                        if(!empty($post_types)){
							$scats = explode(',',get_post_meta($post_field_id,"field_category",true));							
							if(empty($scats) || $scats[0]==''){
								$scats = array('all');
							}
							foreach($post_types as $_post_types)
							{
								foreach ($custom_post_types as $content_type=>$content_type_label)
								{
									$cat_slug = '';									
									if($content_type== $_post_types)
									{
										$cat_slug = $content_type_label['slugs'][0];
										$cat_label=$content_type_label['taxonomies'][0];
										break;
									}else{
										$cat_label=$cat_slug='category';
									}
								}
								echo "<li><label style='font-weight:bold;'>".$cat_label."</label></li>";
								if($cat_slug!='')
									tmpl_get_wp_category_checklist_plugin($pkg_id, array( 'taxonomy' =>$cat_slug,'popular_cats' => $popular_ids,'selected_cats'=>$scats  ) );
							}
						}else{
							echo "<p class='required'>".__('Please select the post types.','templatic-admin')."</p>";
                        } ?>  
                        </ul>
                </div>
                <span id='process' style='display:none;'><i class="fa fa-circle-o-notch fa-spin"></i></span>
            </td>
		</tr>
        <?php do_action('customfields_after_category'); /*customfields_after_category hook add additional custom field */
		
		
		/* fetch The heading custom fields */
  		$heading_type = fetch_heading_posts();
  		asort($heading_type);
		foreach ($heading_type as $key => $val) {
			$heading_type[$key] = $val;
		}
		do_action('customfields_before_heading_type'); /*customfields_before_heading_type hook add additional custom field */
		
		if(count($heading_type) > 0):

		?>  
        <tr <?php echo (isset($_REQUEST['field_id']) && $_REQUEST['field_id']!='' )? 'style="display:none;"' : ' style="display:block;"';?> id="heading_type_id">
            <th>
                <label for="heading_type" class="form-textfield-label"><?php echo __('Heading','templatic-admin');?></label>
            </th>
            <td>
                  <select name="heading_type" id="heading_type">
                  	<option value=""><?php echo __('Select heading type','templatic-admin');?></option>
                    <?php foreach($heading_type as $key=> $_heading_type):?>
                        <option value="<?php echo $_heading_type; ?>" <?php if( @get_post_meta($post_field_id,"heading_type",true) == $_heading_type){ echo 'selected="selected"';}elseif(@$post_field_id== '' && $_heading_type == '[#taxonomy_name#]'){echo 'selected="selected"';}?>><?php echo $_heading_type;?></option>
                    <?php endforeach; ?>  
                  </select>
                   <p class="description"><?php echo __('Choose the group under which the field should be placed. Select the taxonomy_name option to place it inside the main grouping area.','templatic-admin');?></p>
            </td>
        </tr>
	   <?php endif; 
	   do_action('customfields_after_heading_type');/*customfields_after_heading_type hook add additional custom field */
	   
	   do_action('customfields_before_field_type');/*customfields_before_field_type hook add additional custom field */
	   ?>  
		<tr id="tax_name" style="display:block;">
          <th>
          	<label for="field_type" class="form-textfield-label"><?php echo __('Type','templatic-admin');?></label>
          </th>
          <td>
               <select name="ctype" id="ctype" onchange="show_option_add(this.value)" <?php if(get_post_meta($post_field_id,"ctype",true)=='geo_map'){ ?>style="pointer:none;" readonly=readonly<?php } ?>>
                    <option value="date" <?php if( @get_post_meta($post_field_id,"ctype",true)=='date'){ echo 'selected="selected"';}?>><?php echo __('Date Picker','templatic-admin');?></option>
                    <option value="upload" <?php if( @get_post_meta($post_field_id,"ctype",true)=='upload'){ echo 'selected="selected"';}?>><?php echo __('File uploader','templatic-admin');?></option>
                    <option value="geo_map" <?php if( @get_post_meta($post_field_id,"ctype",true)=='geo_map'){ echo 'selected="selected"';}?>><?php echo __('Geo Map','templatic-admin');?></option>
                    <option value="heading_type" <?php if( @get_post_meta($post_field_id,"ctype",true)=='heading_type'){ echo 'selected="selected"';}?>><?php echo __('Heading','templatic-admin');?></option>
                    <option value="multicheckbox" <?php if( @get_post_meta($post_field_id,"ctype",true)=='multicheckbox'){ echo 'selected="selected"';}?>><?php echo __('Multi Checkbox','templatic-admin');?></option>
                    <?php 
                    	do_action('cunstom_field_type',$post_field_id); /* do action use for new field type option*/
                    ?>
                    <option value="image_uploader" <?php if( @get_post_meta($post_field_id,"ctype",true)=='image_uploader'){ echo 'selected="selected"';}?>><?php echo __('Multi image uploader','templatic-admin');?></option>

                    <option value="oembed_video" <?php if( @get_post_meta($post_field_id,"ctype",true)=='oembed_video'){ echo 'selected="selected"';}?>><?php echo __('oEmbed Video','templatic-admin');?></option>
                
                    <option value="post_categories" <?php if( @get_post_meta($post_field_id,"ctype",true)=='post_categories'){ echo 'selected="selected"';}?>><?php echo __('Post Categories','templatic-admin');?></option>
                    <option value="radio" <?php if( @get_post_meta($post_field_id,"ctype",true)=='radio'){ echo 'selected="selected"';}?>><?php echo __('Radio','templatic-admin');?></option>
                    <option value="range_type" <?php if( @get_post_meta($post_field_id,"ctype",true)=='range_type'){ echo 'selected="selected"';}?>><?php echo __('Range Type','templatic-admin');?></option>
                    <option value="select" <?php if( @get_post_meta($post_field_id,"ctype",true)=='select'){ echo 'selected="selected"';}?>><?php echo __('Select','templatic-admin');?></option>
					<option value="text" <?php if( @get_post_meta($post_field_id,"ctype",true)=='text' || @$post_field_id == ''){ echo 'selected="selected"';}?>><?php echo __('Text','templatic-admin');?></option>
                    <option value="textarea" <?php if( @get_post_meta($post_field_id,"ctype",true)=='textarea'){ echo 'selected="selected"';}?>><?php echo __('Textarea','templatic-admin');?></option>
					<option value="texteditor" <?php if( @get_post_meta($post_field_id,"ctype",true)=='texteditor'){ echo 'selected="selected"';}?>><?php echo __('Text Editor','templatic-admin');?></option>
                    <?php do_action('new_custom_field_type',$post_field_id); /* do action use for new field type option */ ?>
               </select>
          </td>
    </tr>
    <?php do_action('customfields_after_field_type');/*customfields_after_field_type hook add additional custom field */?>
    
	<tr id="ctype_option_title_tr_id"  <?php echo ( $is_edit == 'false')? 'style="display:none;"': ' style="display:block;"';?> >
          <th>          
          	<label for="option_title" class="form-textfield-label"><?php echo __('Option Title','templatic-admin');?></label>
          </th>
          <td>
               <input type="text" name="option_title" id="option_title" value="<?php echo get_post_meta($post_field_id,"option_title",true);?>" size="50"  />
               <p class="description"><?php echo __('Separate multiple option titles with a comma. eg. Yes,No','templatic-admin');?></p>
          </td>
	</tr>
	<tr id="ctype_option_tr_id"  <?php echo ( $is_edit == 'false')? 'style="display:none;"': ' style="display:block;"';?> >
          <th>
          	<label for="option_value" class="form-textfield-label"><?php echo __('Option values','templatic-admin');?></label>
          </th>
          <td>
				<?php if(!empty($readonly_fields) && in_array(@$htmlvar_name,$readonly_fields)): ?>
			    <script type="text/javascript" async>
					jQuery(document).ready(function(){
						jQuery('#option_values').focus(function(){
							 jQuery("#option_values").attr("readonly", "readonly");
						});
					});
				</script>
				<?php endif; ?>
               <input type="text" name="option_values" <?php if(!empty($readonly_fields) && in_array(@$htmlvar_name,$readonly_fields)){ echo 'readonly'; } ?> id="option_values" value="<?php echo get_post_meta($post_field_id,"option_values",true);?>" size="50"  />
               <p class="description"><?php echo __('Separate multiple option values with a comma. eg. Yes,No(Do not add space after comma)','templatic-admin');?></p>
               <p id="option_error"class="error" style="display:none;"><?php echo __('Number of option titles and option values can not be different. i.e. If you have added 4 option titles you must add 4 option values too.','templatic-admin');?></p>
          </td>
	</tr>
    <?php do_action('customfields_before_field_label');/*customfields_before_field_label hook add additional custom field */?>
    <tr style="display:block;" id="admin_title_id">
          <th>
          	<label for="field_title" class="form-textfield-label"><?php echo __('Label','templatic-admin');?><span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
          </th>
          <td>
          	<input type="text" class="regular-text" name="admin_title" id="admin_title" value="<?php if($post_val){ echo $post_val->post_title; } ?>" size="50" />
            <p class="description"><?php echo __('Set the title for this field. The same label is applied to both the front-end and the back-end.', 'templatic-admin');?></p>
          </td>
    </tr>
	<?php do_action('customfields_after_field_label');/*customfields_after_field_label hook add additional custom field */?>
	<tr <?php echo ( $is_edit == 'false')? 'style="display:none;"': ' style="display:block;"';?> id="html_var_name">
          <th>
          	<label for="field_name" class="form-textfield-label"><?php echo __('Unique variable name','templatic-admin');?><span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
          </th>
          <td>
               <input type="text" class="regular-text" name="htmlvar_name" id="htmlvar_name" value="<?php echo @get_post_meta($post_field_id,"htmlvar_name",true);?>" size="50"  <?php if( @$_REQUEST['field_id'] !="") { ?>readonly=readonly style="pointer-events: none;"<?php } ?>/>
               <p class="description"><?php echo __('This name is used by the theme internally. It <b>must be</b> unique with no special characters or spaces (use underscores instead). ','templatic-admin'); ?></p>
          </td>
    </tr>
    <?php do_action('customfields_before_field_description');/*customfields_before_field_description hook add additional custom field */?>
	<tr style="display:block;">
          <th>
          	<label for="description" class="form-textfield-label"><?php echo __('Description','templatic-admin');?></label>
          </th>
          <td>
               <input type="text" class="regular-text" name="admin_desc" id="admin_desc" value="<?php if($post_val) { echo $post_val->post_content; } ?>" size="50" />
               <p class="description"><?php echo __('Provide more information about this custom field. It will be displayed below the field on your site. NOTE: Description will not be displayed if <a href="http://templatic.com/plugins/directory-add-ons/wysiwyg-submission" target="_blank">WYSIWYG submission add-on</a> is active.','templatic-admin');?></p>
          </td>
    </tr>
	<?php do_action('customfields_after_field_description');/*customfields_after_field_description hook add additional custom field */?>
    
    <?php do_action('customfields_before_default_value');/*customfields_before_default_value hook add additional custom field */?>
	<tr style="display:block;" id="default_value_id">
          <th>
          	<label for="default_value" class="form-textfield-label"><?php echo __('Default value','templatic-admin');?> </label>
          </th>
          <td>
               <input type="text" class="regular-text" name="default_value" id="default_value" value="<?php echo @get_post_meta($post_field_id,"default_value",true);?>" size="50" />
               <p class="description"><?php echo __("This value will be applied automatically, even if visitors don't select anything. Note: For simple Text field it will work as a placeholder.",'templatic-admin');?></p>
          </td>
    </tr>
	<?php do_action('customfields_after_default_value');/*customfields_after_default_value hook add additional custom field */?>
	<tr style="display:block;">
          <th>
         		<label for="active" class="form-textfield-label"><?php echo __('Active','templatic-admin');?></label>
          </th>
          <td>
          	<input type="checkbox" name="is_active" id="is_active" value="1" <?php if( @get_post_meta($post_field_id,"is_active",true)=='1' || (isset($_REQUEST['action']) && $_REQUEST['action']=='addnew'  && !isset($_REQUEST['field_id'])) ){ echo 'checked="checked"';}?>  />&nbsp;<label for="is_active"><?php echo __('Yes','templatic-admin');?></label>              
               <p class="description"><?php echo __('Uncheck this box only if you want to create the field but not use it right away.','templatic-admin');?></p>
          </td>
    </tr>
    
    <?php do_action('customfields_before_validation_options');/*customfields_before_validation_options hook add additional custom field */?>
	<!-- is required and required message start-->
	<tr <?php echo ( $is_edit == 'false')? 'style="display:none;"': ' style="display:table-row;"';?> id="validation_options" >
		<th colspan="2">
			<div class="tevo_sub_title" style="margin-top:0px"><?php echo __("Validation Options",'templatic-admin');?></div>
		</th>
	</tr>
	<tr <?php echo ( $is_edit == 'false')? 'style="display:none;"': ' style="display:block;"';?> id="is_require_id">
		<th>
		<label for="active" class="form-textfield-label"><?php echo __('Validation','templatic-admin');?></label>
		</th>
		<td>
		<div class="input-switch">
		   <input type="checkbox" name="is_require" id="is_require" onchange="return show_validation_type();" value="1"  <?php if( @get_post_meta($post_field_id,"is_require",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="is_require"><?php echo __('Yes','templatic-admin');?></label>
		</div>
		  <p class="description"><?php echo __('Required fields cannot be left empty during submission. A value must be entered before moving on to the next step.','templatic-admin');?></p>
		</td>
    </tr>
    <?php do_action('customfields_before_validation_type');/*customfields_before_validation_type hook add additional custom field */?>
	<!-- validation start -->
	<tr <?php echo ( $is_edit == 'false')? 'style="display:none;"': ' style="display:block;"';?> id="validation_type_id">
          <th>
          	<label for="validation_type" class="form-textfield-label"><?php echo __('Validation type','templatic-admin');?><span class="required">*</span></label>
          </th>
          <td>
               <select name="validation_type" id="validation_type"><?php echo validation_type_cmb_plugin(get_post_meta($post_field_id,"validation_type",true));?></select></div>
               <p class="description"><?php echo '<small><b>'.__('Require','templatic-admin').'</b> - '.__('the field cannot be left blank (default setting).','templatic-admin').'<br/><b>'.__('Phone No.','templatic-admin').'</b> - '.__('values must be in phone number format.','templatic-admin').'<br/><b>'.__('Digit','templatic-admin').'</b> - '.__('values must be all numbers.','templatic-admin').'<br/><b>'.__('Email','templatic-admin').'</b> - '.__('the value must be in email format.','templatic-admin').'</small>';?></p>
          </td>
    </tr>
	<!-- validation end -->
	<?php do_action('customfields_after_validation_type');/*customfields_after_validation_type hook add additional custom field */?>
	<!-- required field msg start -->

	<tr <?php echo ( $is_edit == 'false')? 'style="display:none;"': ' style="display:block;"';?> id="field_require_desc_id">
          <th>
          	<label for="active" class="form-textfield-label"><?php echo __('Required field warning message','templatic-admin');?><span class="required">*</span></label>
          </th>
          <td>
               <textarea name="field_require_desc" class="tb_textarea" id="field_require_desc"><?php echo @get_post_meta($post_field_id,"field_require_desc",true);?></textarea>
               <p class="description"><?php __('The message that will appear when a mandatory field is left blank.','templatic-admin');?></p>
          </td>
    </tr>
<?php
	do_action('customfields_after_validation_options');/*customfields_after_validation_options hook add additional custom field */
	
	do_action('customfields_before_display_option'); /*customfields_before_display_option hook to add additional custom field */?>
	<!-- required field msg end -->
	<tr <?php echo ( $is_edit == 'false')? 'style="display:none;"': ' style="display:table-row;"';?>>
		<th colspan="2">
			<div class="tevo_sub_title" style="margin-top:0px"><?php echo __("Display Options",'templatic-admin');?></div>
		</th>
	</tr>
	<!-- is required and required message end-->
	<tr <?php echo ( isset($_REQUEST['field_id']) && $_REQUEST['field_id']!='')? 'style="display:none;"': ' style="display:block;"';?> id="sort_order_id">
          <th>
          	<label for="sort_order" class="form-textfield-label"><?php echo __('Position (display order)','templatic-admin');?><span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
          </th>
          <td>
               <input type="text" class="regular-text" name="sort_order" id="sort_order"  value="<?php echo ((isset($_REQUEST['field_id']) && $_REQUEST['field_id']!='') || (isset($_REQUEST['trid']) && $_REQUEST['trid']!=''))?@get_post_meta($post_field_id,"sort_order",true): $sort_order+1;?>" size="50" />
               <p class="description"><?php echo __('A numeric value that determines the position of the field inside the submission form. Enter 1 to make the field appear at the top.','templatic-admin');?></p>
          </td>
    </tr>
    <tr <?php echo ( $is_edit == 'false')? 'style="display:none;"': ' style="display:block;"';?>>
        <th>
        	<label for="display_location" class="form-textfield-label"><?php echo __('Display location','templatic-admin');?></label>
        </th>
        <td>
			<?php $not_for_admin = array('post_title','category','post_content','post_excerpt','post_images','post_tags'); ?>
            <select name="show_on_page" id="show_on_page" >
				<?php if(!in_array($htmlvar_name,$not_for_admin)){ ?>
                <option value="admin_side" <?php if( @get_post_meta($post_field_id,"show_on_page",true)=='admin_side'){ echo 'selected="selected"';}?>><?php echo __('Admin side (Backend side) ','templatic-admin');?></option>
				<option value="both_side" <?php if( @get_post_meta($post_field_id,"show_on_page",true)=='both_side' || @$post_field_id == ''){ echo 'selected="selected"';}?>><?php echo __('Both','templatic-admin');?></option>
				<?php } ?>
                <option value="user_side" <?php if( @get_post_meta($post_field_id,"show_on_page",true)=='user_side'){ echo 'selected="selected"';}?>><?php echo __('User side (Frontend side)','templatic-admin');?></option>
				
            </select>
           <p class="description"><?php echo __('Choose where the field will display; to you (back-end), your visitors (front-end) or both.','templatic-admin');?></p>
        </td>
    </tr>
    
    <!-- Show Display Option -->
    <tr <?php echo ( $is_edit == 'false' || ( is_array($exclude_show_fields) && in_array($htmlvar_name,$exclude_show_fields)))? 'style="display:none;"': ' style="display:block;"';?>>
    		<th><label for="display_option" class="form-textfield-label"><?php echo __('Show the field in','templatic-admin');?></label></th>
          <td>          
          	<fieldset>				
               	<input type="checkbox" name="show_in_column" id="show_in_column" value="1" <?php if( @get_post_meta($post_field_id,"show_in_column",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="show_in_column" ><?php echo __('Back-end (as a column in listing areas, e.g. Posts -> All Posts)','templatic-admin');?></label><?php do_action('tmpl_show_in_column_field',$post_field_id);?><br />
               	<input type="checkbox" id="show_on_listing" name="show_on_listing" value="1" <?php if( @get_post_meta($post_field_id,"show_on_listing",true)=='1' || (isset($_REQUEST['action']) && $_REQUEST['action']=='addnew'  && !isset($_REQUEST['field_id']))){ echo 'checked="checked"';}?>/>&nbsp;<label for="show_on_listing" ><?php echo __('Archive pages and home page widget','templatic-admin');?></label><?php do_action('tmpl_show_on_listing_field',$post_field_id);?><br />
               	<input type="checkbox" name="show_in_email" id="show_in_email" value="1" <?php if( @get_post_meta($post_field_id,"show_in_email",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="show_in_email" ><?php echo __('Confirmation email (sent after successful submission)','templatic-admin');?></label><?php do_action('tmpl_show_in_email_field',$post_field_id);?><br />
                <input type="checkbox" name="show_on_detail" id="show_on_detail" value="1" <?php if( @get_post_meta($post_field_id,"show_on_detail",true)=='1' || (isset($_REQUEST['action']) && $_REQUEST['action']=='addnew'  && !isset($_REQUEST['field_id']) && !is_plugin_active('Directory-TabsManager/fieldtabs.php') )){ echo 'checked="checked"';}?>/>&nbsp;<label for="show_on_detail" ><?php echo __('Detail page','templatic-admin');?></label><?php do_action('tmpl_show_on_detail_field',$post_field_id);?><br />
                
                <input type="checkbox" name="is_submit_field" id="is_submit_field" value="1" <?php if( @get_post_meta($post_field_id,"is_submit_field",true)=='1' || (isset($_REQUEST['action']) && $_REQUEST['action']=='addnew'  && !isset($_REQUEST['field_id']))){ echo 'checked="checked"';}?>/>&nbsp;<label for="is_submit_field" ><?php echo __('Submission form (field will show on editing screen regardless)','templatic-admin');?></label><?php do_action('tmpl_is_submit_field_field',$post_field_id);?><br />                
                <input type="checkbox" name="show_on_success" id="show_on_success" value="1" <?php if( @get_post_meta($post_field_id,"show_on_success",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="show_on_success" ><?php echo __('Success page (the page that shows after submission)','templatic-admin');?></label><br />
                <?php do_action('tmpl_extra_show_in_field',$post_field_id);?>
               </fieldset>
          </td>
    </tr>
    
    <tr id="option_search_ctype" <?php if( @get_post_meta($post_field_id,"is_search",true)=='1' || (is_array($exclude_show_fields) && !in_array($htmlvar_name,$exclude_show_fields))){?> style="display:block;" <?php }else{?> style="display:none;" <?php }?>>
    	<th><label><?php echo __('Show on search as','templatic-admin');?></label></th>
        <td>
        	<select name="search_ctype" id="search_ctype">
                <option value="" ><?php echo __('Select type on search','templatic-admin');?></option>
                <option value="text" <?php if( @get_post_meta($post_field_id,"search_ctype",true)=='text'){ echo 'selected="selected"';}?>><?php echo __('Text','templatic-admin');?></option>
                <option value="date" <?php if( @get_post_meta($post_field_id,"search_ctype",true)=='date'){ echo 'selected="selected"';}?>><?php echo __('Date Picker','templatic-admin');?></option>
                <option value="multicheckbox" <?php if( @get_post_meta($post_field_id,"search_ctype",true)=='multicheckbox'){ echo 'selected="selected"';}?>><?php echo __('Multi Checkbox','templatic-admin');?></option>
                <option value="radio" <?php if( @get_post_meta($post_field_id,"search_ctype",true)=='radio'){ echo 'selected="selected"';}?>><?php echo __('Radio','templatic-admin');?></option>
                <option value="select" <?php if( @get_post_meta($post_field_id,"search_ctype",true)=='select'){ echo 'selected="selected"';}?>><?php echo __('Select','templatic-admin');?></option>                                
                <option value="min_max_range" <?php if( @get_post_meta($post_field_id,"search_ctype",true)=='min_max_range'){ echo 'selected="selected"';}?>><?php echo __('Min-Max Range (Text)','templatic-admin');?></option>
                <option value="min_max_range_select" <?php if( @get_post_meta($post_field_id,"search_ctype",true)=='min_max_range_select'){ echo 'selected="selected"';}?>><?php echo __('Min-Max Range (Select)','templatic-admin');?></option>
                <option value="slider_range" <?php if( @get_post_meta($post_field_id,"search_ctype",true)=='slider_range'){ echo 'selected="selected"';}?>><?php echo __('Range Slider','templatic-admin');?></option>
           </select>
           <p class="description"><?php echo __('The type selected here will be displayed on the advance search form for this field.','templatic-admin');?></p>
            <p class="description" id="min_max_description" <?php if( @get_post_meta($post_field_id,"search_ctype",true)=='min_max_range'){?> style="display:block;" <?php }else{?> style="display:none;" <?php }?>><?php echo __('Two text boxes will appear on your advance search form from where you can enter minumun and maximum values to search in a specific range.','templatic-admin');?></p>
            <p class="description" id="slider_range_description" <?php if( @get_post_meta($post_field_id,"search_ctype",true)=='slider_range'){?> style="display:block;" <?php }else{?> style="display:none;" <?php }?>><?php echo __('A range slider will appear with minimum and maximum values, you can drag and select your range to search.','templatic-admin');?></p>
        </td>
    </tr> 
       
    <tr id="min_max_range_option" <?php if( @get_post_meta($post_field_id,"search_ctype",true)=='slider_range'){?> style="display:block;" <?php }else{?> style="display:none;" <?php }?> >
    	<th><label for="range_option"><?php echo __('Define your range','templatic-admin');?></label></th>
        <td>
            <fieldset>
				<input type="text" id="range_min_value" name="range_min" value="<?php echo get_post_meta($post_field_id,"range_min",true)?>"  placeholder='<?php echo __('Min value','templatic-admin');?>'/>
                <input type="text" id="range_max_value" name="range_max" value="<?php echo get_post_meta($post_field_id,"range_max",true)?>" placeholder='<?php echo __('max value','templatic-admin');?>'/>
            </fieldset>
            <p class="description"><?php echo __('Users will be able to enter values between this range on the submition form, and advance search form will also display this range.','templatic-admin');?></p>
        </td>
    </tr>
    <tr id="search_select_title" style="display: none;" >
        <th><label for="option_title" class="form-textfield-label"><?php echo __('Option Title','templatic-admin');?></label></th>
        <td>
            <input type="text" name="search_option_title" id="search_option_title" value="<?php echo get_post_meta($post_field_id,"option_title",true);?>" size="50"  />
            <p class="description"><?php echo __('Separate multiple option titles with a comma. eg. Yes,No','templatic-admin');?></p>
        </td>
    </tr>
    <tr id="search_select_value" style="display: none;">
        <th><label for="option_value" class="form-textfield-label"><?php echo __('Option values','templatic-admin');?></label></th>
        <td>
        	<input type="text" name="search_option_values" id="search_option_values" value="<?php echo get_post_meta($post_field_id,"option_values",true);?>" size="50"  />
        	<p class="description"><?php echo __('Separate multiple option values with a comma. eg. Yes,No(Do not add space after comma)','templatic-admin');?></p>
        </td>
    </tr>
    <!--Start Min Max Range select -->
     <tr id="search_min_select_title" style="display: none;" >
        <th><label for="option_title" class="form-textfield-label"><?php echo __('Min Option Title','templatic-admin');?></label></th>
        <td>
            <input type="text" name="search_min_option_title" id="search_min_option_title" value="<?php echo get_post_meta($post_field_id,"search_min_option_title",true);?>" size="50"  />
            <p class="description"><?php echo __('Separate multiple option titles with a comma. eg. Yes,No','templatic-admin');?></p>
        </td>
    </tr>
    <tr id="search_min_select_value" style="display: none;">
        <th><label for="option_value" class="form-textfield-label"><?php echo __('Min Option values','templatic-admin');?></label></th>
        <td>
        	<input type="text" name="search_min_option_values" id="search_min_option_values" value="<?php echo get_post_meta($post_field_id,"search_min_option_values",true);?>" size="50"  />
        	<p class="description"><?php echo __('Separate multiple option values with a comma. eg. Yes,No(Do not add space after comma)','templatic-admin');?></p>
            <p id="search_min_option_error"class="error" style="display:none;"><?php echo __('Number of option titles and option values can not be different. i.e. If you have added 4 option titles you must add 4 option values too.','templatic-admin');?></p>
        </td>
    </tr>
     <tr id="search_max_select_title" style="display: none;" >
        <th><label for="option_title" class="form-textfield-label"><?php echo __('Max Option Title','templatic-admin');?></label></th>
        <td>
            <input type="text" name="search_max_option_title" id="search_max_option_title" value="<?php echo get_post_meta($post_field_id,"search_max_option_title",true);?>" size="50"  />
            <p class="description"><?php echo __('Separate multiple option titles with a comma. eg. Yes,No','templatic-admin');?></p>
        </td>
    </tr>
    <tr id="search_max_select_value" style="display: none;">
        <th><label for="option_value" class="form-textfield-label"><?php echo __('Max Option values','templatic-admin');?></label></th>
        <td>
        	<input type="text" name="search_max_option_values" id="search_max_option_values" value="<?php echo get_post_meta($post_field_id,"search_max_option_values",true);?>" size="50"  />
        	<p class="description"><?php echo __('Separate multiple option values with a comma. eg. Yes,No(Do not add space after comma)','templatic-admin');?></p>
            <p id="search_max_option_error"class="error" style="display:none;"><?php echo __('Number of option titles and option values can not be different. i.e. If you have added 4 option titles you must add 4 option values too.','templatic-admin');?></p>
        </td>
    </tr>
    <!--END Select Min-Max Range -->
    
    <?php do_action('customfields_before_miscellaneous'); /* customfields_before_miscellaneous hook add additional custom fields*/?>
    <!--Finish Show Display Option -->
    <tr id="miscellaneous_options" >
        <th colspan="2">
        	<div class="tevo_sub_title" style="margin-top:0px"><?php echo __("Miscellaneous Options",'templatic-admin');?></div>
        </th>
    </tr>
     <!-- css class start -->
     <tr style="display: block;" id="style_class_id">
     	<th>
          	<label for="css_class" class="form-textfield-label"><?php echo __('CSS class','templatic-admin');?></label>
        </th>
        <td>
            <input type="text" class="regular-text" name="style_class" id="style_class" value="<?php echo @get_post_meta($post_field_id,"style_class",true); ?>"></div>
        	<p class="description"><?php echo __('Apply a custom CSS class to the fields label. For more details on this','templatic-admin').' <a href="http://templatic.com/docs/tevolution-guide/#miscellaneous" title="'.__('Add New Custom Field','templatic-admin').'" target="_blank">'.__('click here','templatic-admin').'</a>';?></p>
     	</td>
     </tr>
     <!-- css class end -->
     
     <!-- extra prameters -->
     <tr style="display: block;" id="extra_parameter_id">
          <th>
          	<label for="extra_parameter" class="form-textfield-label"><?php echo __('Extra parameter','templatic-admin');?></label>
          </th>
          <td>
               <input type="text" class="regular-text" name="extra_parameter" id="extra_parameter" value="<?php echo @get_post_meta($post_field_id,"extra_parameter",true); ?>"></div>
               <p class="description"><?php echo __('Apply an extra parameter to the fields input part. For more information <a href="http://templatic.com/docs/tevolution-guide/#miscellaneous" title="Add New Custom Field" target="_blank">click here</a>','templatic-admin');?></p>
          </td>
     </tr>
     <!-- extra perameters -->
     
     <?php do_action('customfields_after_miscellaneous'); /* customfields_after_miscellaneous hook add additional custom fields*/?>
     <tr style="display:block;">
          <td>
			<?php if(isset($_REQUEST['field_id'])): ?>
               	<input type="submit" name="submit-fields" value="<?php echo __('Update changes','templatic-admin');?>" class="button button-primary button-hero">
               <?php else: ?>
               	<input type="submit" name="submit-fields" value="<?php echo __('Save all changes','templatic-admin');?>" class="button button-primary button-hero"> 
               <?php endif; ?> 
          </td>		
     </tr>
   
	</tbody>
	</table>
</form>
</div>
<script type="text/javascript" async >
function show_option_add(htmltype){
	
	if(htmltype=='select' || htmltype=='multiselect' || htmltype=='radio' || htmltype=='multicheckbox')	{
		
		document.getElementById('ctype_option_tr_id').style.display='block';		
		document.getElementById('ctype_option_title_tr_id').style.display='block';
	
	}else{
		document.getElementById('ctype_option_tr_id').style.display='none';	
		document.getElementById('ctype_option_title_tr_id').style.display='none';	
	}
	if(htmltype=='heading_type'){
		jQuery('#heading_type_id').hide();
		jQuery("#heading_type_id option[value='']").attr('selected', true)
		jQuery('#default_value_id').hide();
		jQuery('#is_require_id').hide();
		jQuery('#show_on_listing_id').hide();
		jQuery('#is_search_id').hide();
		jQuery('#show_in_column_id').hide();
		jQuery('#show_in_email_id').hide();
		jQuery('#field_require_desc_id').hide();
		jQuery('#validation_type_id').hide();
		jQuery('#style_class_id').hide();
		jQuery('#extra_parameter_id').hide();
		jQuery('#show_on_detail_id').hide();
		jQuery('#show_on_success_id').hide();
		jQuery('#show_on_column_id').hide();
		jQuery('#validation_options').hide();
		jQuery('#miscellaneous_options').hide();
		
	}else{
		
		<?php if(get_post_meta($post_field_id,"is_edit",true) == 'true' || get_post_meta($post_field_id,"is_edit",true) == ''){ ?>
		jQuery('#default_value_id').show();
		jQuery('#is_require_id').show();
		jQuery('#show_on_listing_id').show();
		jQuery('#is_search_id').show();
		jQuery('#show_in_column_id').show();
		if (jQuery("#is_require").is(":checked")) {
			jQuery('#field_require_desc_id').show();
			jQuery('#validation_type_id').show();
		}else
		{
			jQuery('#field_require_desc_id').hide();
			jQuery('#validation_type_id').hide();
		}
		
		jQuery('#show_in_email_id').show();
		jQuery('#style_class_id').show();
		jQuery('#extra_parameter_id').show();
		jQuery('#show_on_detail_id').show();
		jQuery('#show_on_success_id').show();
		jQuery('#show_on_column_id').show();
		jQuery('#validation_options').show();
		jQuery('#miscellaneous_options').show();
		<?php } 
		if(!isset($_REQUEST['field_id']) && $_REQUEST['field_id'] == '')
		{?>
			jQuery('#heading_type_id').show();
	<?php } ?>
	}
	if(htmltype == 'image_uploader' || htmltype == 'upload')
	{
		if(htmltype == 'image_uploader'){	/* if user chooses multi image uploader then show the notice */
			if(jQuery('#ctype').parent().find('.message_error2').length == 0){
				jQuery('#ctype').after( '<p class="message_error2"><?php echo __('<b>NOTE:</b> You can not use multiple image uploader into a single submission for. So if there is any image uploader already there, then no need to create another filed.','templatic-admin'); ?></p>' );
			}	
		}
		jQuery('#show_in_email_id').hide();
	}
	if(htmltype == 'geo_map')
	 {
		 document.getElementById('htmlvar_name').value='address';
		 document.getElementById('html_var_name').style.display='none';
	 }
	else
	 {
		 document.getElementById('html_var_name').style.display='block';
	 }
	 
}
if(document.getElementById('ctype').value){
	show_option_add(document.getElementById('ctype').value)	;
}
function show_validation_type()
{

	if (jQuery("#is_require").is(":checked")) {
		jQuery('#field_require_desc_id').show();
		jQuery('#validation_type_id').show();
		if(jQuery('#validation_type').val() ==''){
			jQuery('#validation_type').val('require');
		}
    }else
    {
		jQuery('#field_require_desc_id').hide();
		jQuery('#validation_type_id').hide();
	}
    return true;
}


/* Disable search ctype optoon value according cusom field type when custom field not equal to blank */
jQuery(document).ready(function(){  
	var ctype_val = jQuery("select#ctype option:selected").attr('value');
	if(ctype_val!='' && jQuery(".advance_is_search").is(':checked') && jQuery(".advance_is_search").attr('checked')=='checked'){
		ShowHideSearch_ctypeOption(ctype_val);	
	}else{
		jQuery("#option_search_ctype").css('display','none');	
	}
	
	if(ctype_val=='upload'){			
		jQuery('#default_value').attr('placeholder','http://www.xyz.com/image/image.jpg');
	}
	jQuery('select#validation_type option').each(function(){			
		if(ctype_val=='texteditor' && (jQuery(this).val()=='phone_no' || jQuery(this).val()=='digit' || jQuery(this).val()=='email')){
			jQuery(this).prop('disabled', true);
		}else{
			jQuery(this).prop('disabled', false);
		}
	});
}); 
</script>