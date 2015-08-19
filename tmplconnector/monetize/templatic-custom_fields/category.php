<?php
/* set global $include_cat_array for display only include category list on submit form */
global $wpdb,$post,$include_cat_array,$cat_array;
wp_reset_query();
wp_reset_postdata();
$total_cp_price = 0;
if($cpost_type ==''){ $cpost_type = $post_type; }
$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $cpost_type,'public'   => true, '_builtin' => true ));
$taxonomy = $taxonomies[0];
$total_cp_price = 0;
if(isset($_REQUEST['pid']) && $_REQUEST['pid']!="" ){
	$place_cat_arr = $cat_array;
}elseif(isset($_SESSION['custom_fields']) && $_SESSION['custom_fields']['category']!=''){
	$all_cat_id='';
	foreach($_SESSION['custom_fields']['category'] as $_category_arr)
	{
		$category = explode(",",$_category_arr);
		$place_cat_arr[]= $category[0];
	}
}else
{
	for($i=0; $i < count($cat_array); $i++){
		$place_cat_arr[] = @$cat_array[$i]->term_taxonomy_id;
	}
}
$cat_display = "";
$tmpdata = get_option('templatic_settings');
if(isset($tmpdata['templatic-category_type']) && $tmpdata['templatic-category_type'] != "")
{
	$cat_display = $tmpdata['templatic-category_type'];
}
if(!$cat_display)
{
	$cat_display = 'checkbox';
}

do_action('tmpl_display_categories_start');

/* Start of checkbox */
if($cat_display == 'checkbox')
{ 	
	?>
	<div class="cf_checkbox">
	<?php 
		global $monetization;
		$total_price = $monetization->templ_total_price($taxonomy);
		$onclick = "onclick=displaychk();";
			
	?>
	<label><input type="checkbox" name="selectall" id="selectall"  <?php echo $onclick; ?> /><?php _e('Select All','templatic');?></label>
	<ul id="<?php echo 'listingcategory'; ?>checklist" data-wp-lists="list:<?php echo $taxonomy; ?>" class="categorychecklist form_cat">
		<?php 
		/* check post type for display post id wise category in submit page */
		if(get_post_type($post->ID)!='custom_fields'){
			tev_wp_terms_checklist($post->ID, array( 'taxonomy' =>$taxonomy,'selected_cats' => $place_cat_arr ) );
		}else{
			tev_wp_terms_checklist(0, array( 'taxonomy' =>$taxonomy,'selected_cats' => $place_cat_arr ) );
		}?>
	</ul>
	</div>
<?php
 }
/* End of checkbox */
/* Start of selectbox */
if($cat_display=='select' || $cat_display=='multiselectbox')
{ 

	
	$catinfo = templ_get_parent_categories($taxonomy);
	if(count($catinfo) == 0)
	{
		echo '<span style="font-size:12px; color:red;">'.sprintf(__('You have not created any category for %s post type. So, this listing will be submited as uncategorized.','templatic'),$template_post_type).'</span>';
	}
	$args = array('hierarchical' => true ,'hide_empty' => 0, 'orderby' => 'term_group');
	$terms = templ_get_parent_categories($taxonomy);
	
	if($terms) :		
		
		if($cat_display == 'multiselectbox'){ $multiple =  "multiple=multiple"; }else{ $multiple=''; } /* multi select box */
		$output .= '<select name="category[]" id="select_category" '.$multiple.'>';
		
		$output .= '<option value="">'.__('Select Category','templatic').'</option>';
		foreach($terms as $term){	
			$term_id = $term->term_id;
			/* Check term id in include cart array if not in include cart array then continue loop  for display category price package wise set */
			if(is_array($include_cat_array) && !in_array($term_id,$include_cat_array) && !in_array('all',$include_cat_array))
				continue;
				
			if(isset($_REQUEST['pid']) && $_REQUEST['pid']!=""){
				$edit_id = $_REQUEST['pid'];
				/* get the submited price package  */
				$pkg_id=get_post_meta($edit_id,'package_select',true);
				$pkg_category=explode(',',get_post_meta($pkg_id,'category',true));
				/* check category on price package selected catgeory if category not in price package category then return output */
				if(!empty($pkg_category) && $pkg_category[0]!='' && !in_array($term_id,$pkg_category) && !in_array('all',$pkg_category)){				
					continue;
				}
			}
	
			/* finish display price package wise category */
			
			$scp = $term->term_price;
			if($scp == ""){
				$scp = 0 ;
			}
			/* price will display only when monetization is activated */
			if($scp!='0') { $sdisplay_price = " (".fetch_currency_with_position($scp).")"; }else{ $sdisplay_price =''; }
			$term_name = $term->name;
			if(isset($place_cat_arr) && in_array($term_id,$place_cat_arr)){ $selected = 'selected=selected'; }else{ $selected='';} /* category must be selected when gobackand edit /Edit/Renew */
			$output .= '<option  data-value="'.$term_id.'" value='.$term_id.','.$scp.' '.$selected.'>'.$term_name.$sdisplay_price.'</option>';
			
			$child_terms = templ_get_child_categories($taxonomy,$term_id);		/* get child categories term_id = parent id*/					
			$i=1;
			$parent_id = $term_id;
			$tmp_term_id=$term_id;
			foreach($child_terms as $child_term){ 
				$child_term_id = $child_term->term_id;
				$child_cp = $child_term->term_price;				
				if($child_term_id)
				{
					$pad ='';
					$catprice = $wpdb->get_row("select * from $wpdb->term_taxonomy tt ,$wpdb->terms t where t.term_id='".$child_term_id."' and t.term_id = tt.term_id AND tt.taxonomy ='".$taxonomy."'");
					for($i=0;$i<count($catprice);$i++)
					{
						if($catprice->parent)
						{	
							$pad .= '&ndash; ';
							$catprice1 = $wpdb->get_row("select * from $wpdb->term_taxonomy tt ,$wpdb->terms t where t.term_id='".$catprice->parent."' and t.term_id = tt.term_id AND tt.taxonomy ='".$taxonomy."'");
							if($catprice1->parent)
							{
								$i--;
								$catprice = $catprice1;
								continue;
							}
						}
					}
				}
				if($child_term->category_parent!=0):					
					/* price will display only when monetization is activated */
					if($child_cp!='0' ) { $cdisplay_price = " (".fetch_currency_with_position($child_cp).")"; }else{ $cdisplay_price =''; }
					$term_name = $child_term->name;
					if(isset($place_cat_arr) && in_array($child_term_id,$place_cat_arr)){ $cselected = 'selected=selected'; }else{ $cselected='';} /* category must be selected when gobackand edit /Edit/Renew */
					$output .= '<option data-value='.$child_term_id.' value='.$child_term_id.','.$child_cp.' '.$cselected.'>'.$pad.$term_name.$cdisplay_price.'</option>';										
				endif;
            } /* child category foreach loop  */
		}
		$output .= '</select>';
    echo $output;
	endif;
}
do_action('tmpl_display_categories_end');
?>