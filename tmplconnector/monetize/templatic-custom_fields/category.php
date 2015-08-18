<?php
global $wpdb,$post;
wp_reset_query();
$total_cp_price = 0;
if($cpost_type ==''){ $cpost_type = $post_type; }
$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $cpost_type,'public'   => true, '_builtin' => true ));
$taxonomy = $taxonomies[0];

global $cat_array;
$total_cp_price = 0;
if(isset($_REQUEST['backandedit']) != '' || (isset($_REQUEST['pid']) && $_REQUEST['pid']!="") ){
	$place_cat_arr = $cat_array;
}
else
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
{ ?>
	<div class="cf_checkbox">
	<?php
		if(is_active_addons('monetization')){ 
				global $monetization;
				$total_price = $monetization->templ_total_price($taxonomy);
				$onclick = "onclick=displaychk();templ_all_categories($total_price);";
			}else{ $onclick = "onclick=displaychk()";}
			?>
	<label><input type="checkbox" name="selectall" id="selectall"  <?php echo $onclick; ?> /><?php _e('Select All',DOMAIN);?></label>
	<ul id="<?php echo 'listingcategory'; ?>checklist" data-wp-lists="list:<?php echo $taxonomy; ?>" class="categorychecklist form_cat">
		<?php tev_wp_terms_checklist($post->ID, array( 'taxonomy' =>$taxonomy,'selected_cats' => $place_cat_arr ) ) ?>
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
		echo '<span style="font-size:12px; color:red;">'.sprintf(__('You have not created any category for %s post type.So, this listing will be submited as uncategorized.',DOMAIN),$template_post_type).'</span>';
	}
	$args = array('hierarchical' => true ,'hide_empty' => 0, 'orderby' => 'term_group');
	$terms = templ_get_parent_categories($taxonomy);
	
	if($terms) :
		if(is_active_addons('monetization')):
			if($cat_display=='select'):
				$fetch_pkg = "onchange=fetch_packages(this.value,this.form);"; /* FUNCTION FOR FETCH PACKAGES */
			else:
				$fetch_pkg = "onclick=fetch_packages(this.value,this.form);"; /* FUNCTION FOR FETCH PACKAGES */
			endif;
		else:
			$fetch_pkg = '';
		endif;
		
		if($cat_display == 'multiselectbox'){ $multiple =  "multiple=multiple"; }else{ $multiple=''; } /* multi select box */
		$output .= '<select name="category[]" id="select_category" '.$fetch_pkg.' '.$multiple.'>';
		
		$output .= '<option value="">'.__('Select Category',DOMAIN).'</option>';
		foreach($terms as $term){	
			$term_id = $term->term_id;
			$scp = $term->term_price;
			if($scp == ""){
				$scp = 0 ;
			}
			/* price will display only when monetization is activated */
			if(is_active_addons('monetization') && $scp!='0') { $sdisplay_price = " (".fetch_currency_with_position($scp).")"; }else{ $sdisplay_price =''; }
			$term_name = $term->name;
			if(isset($place_cat_arr) && in_array($term_id,$place_cat_arr)){ $selected = 'selected=selected'; }else{ $selected='';} /* category must be selected when gobackand edit /Edit/Renew */
			$output .= '<option value='.$term_id.','.$scp.' '.$selected.'>'.$term_name.$sdisplay_price.'</option>';
			
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
					if(is_active_addons('monetization') && $child_cp!='0' ) { $cdisplay_price = " (".fetch_currency_with_position($child_cp).")"; }else{ $cdisplay_price =''; }
					$term_name = $child_term->name;
					if(isset($place_cat_arr) && in_array($child_term_id,$place_cat_arr)){ $cselected = 'selected=selected'; }else{ $cselected='';} /* category must be selected when gobackand edit /Edit/Renew */
					$output .= '<option value='.$child_term_id.','.$child_cp.' '.$cselected.'>'.$pad.$term_name.$cdisplay_price.'</option>';										
				endif;
            } //child category foreach loop
		}
		$output .= '</select>';
    echo $output;
	endif;
}
do_action('tmpl_display_categories_end');
?>
<script type="text/javascript">
function displaychk(){
	dml=document.forms['submit_form'];
	chk = document.getElementsByName('category[]');
	len = chk.length;
	if(document.submit_form.selectall.checked == true) {
		for (i = 0; i < len; i++)
		chk[i].checked = true ;
	} else {
		for (i = 0; i < len; i++)
		chk[i].checked = false ;
	}
}
</script>
