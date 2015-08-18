<?php
/*
	Name : templatic_key_search_widget
    Desc : location wise search widget
*/
class templatic_key_search_widget extends WP_Widget {
	function templatic_key_search_widget() {
		//Constructor
		$widget_ops = array('classname' => 'search_key', 'description' => __('A single-input widget that allows you to search within specific custom fields. Works best inside the Header widget area. It can also be used inside sidebar areas.',ADMINDOMAIN) );
		$this->WP_Widget('templatic_key_search_widget', __('T &rarr; All in One Search',ADMINDOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
		// prints the widget
		extract($args, EXTR_SKIP);
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$post_type = empty($instance['post_type']) ? 'listing' : apply_filters('widget_post_type', $instance['post_type']);
		$miles_search = empty($instance['miles_search']) ? '' : apply_filters('widget_miles_search', $instance['miles_search']);
		$radius_measure= empty($instance['radius_measure']) ? 'miles' : apply_filters('widget_radius_measure', $instance['radius_measure']);		
		$exact_search= empty($instance['exact_search']) ? 'OR' : apply_filters('widget_exact_search', $instance['exact_search']);		
		$search_criteria= empty($instance['search_criteria']) ? 'all' : apply_filters('widget_search_criteria', $instance['search_criteria']);		
		$search_in_city= empty($instance['search_in_city']) ? '' : apply_filters('widget_search_criteria', $instance['search_in_city']);		
		$radius_type=($radius_measure=='miles')? __('Miles',DOMAIN) : __('Kilometers',DOMAIN);
		$search_in_city=($search_in_city=='')? '' : 1;
		if($exact_search ==1){ $exact_search ='AND'; }
		echo $args['before_widget'];
		
		if(isset($_REQUEST['s']) && $_REQUEST['s'] !=''){
			$search_txt= esc_html($_REQUEST['s']);
		}else{
			$search_txt= __('What?',DOMAIN);
		}
		if($miles_search==1){
			$class=' search_by_mile_active';
		}
		$search_id= rand();
		$distance_factor = @$_REQUEST['radius'];
		if(isset($_REQUEST['location'])) { $location= @$_REQUEST['location']; }else{$location='';  }
		if(isset($_REQUEST['search_key'])) { $what=  $_REQUEST['search_key']; }else{$what='';  }
		echo '<div class="search_nearby_widget'.$class.'">';
		if($instance['title']){ echo $args['before_title'].$title.$args['after_title']; }
		$nonce = wp_create_nonce  ('tmpl_search');
		
		?>
		<form method="get" id="searchform" action="<?php echo home_url(); ?>/">
				<?php 
				/* loop to fetch meta key Start */
	
				if(!empty($post_type) && is_array($post_type)){
					foreach($post_type as $val):?>
					<input type="hidden" name="post_type[]" value="<?php echo $val;?>" />
                <?php endforeach;	
				}else{ ?>
					<input type="hidden" name="post_type[]" value="<?php echo $post_type;?>" />
				<?php }
				/* loop to fetch meta key End */
				
				
				/* loop to fetch meta key Start */
		
				if(!empty($search_criteria) && is_array($search_criteria)){
				foreach($search_criteria as $value):?>
					<input type="hidden" name="mkey[]" value="<?php echo $value;?>" />
               <?php endforeach;
			    }else{ ?>
					<input type="hidden" name="mkey[]" value="all" />
				<?php }
			   /* loop to fetch meta key End*/
			   ?>
               <input type="text" value="<?php echo $what; ?>" name="s" id="search_near-<?php echo $search_id;?>" class="searchpost" onfocus="if (this.placeholder == '<?php echo $search_txt;?>') {this.placeholder = '';}" onblur="if (this.placeholder == '') {this.placeholder = '<?php echo $search_txt;?>';}" placeholder="<?php if(isset($_REQUEST['s']) && trim($_REQUEST['s']) == '') { echo $search_txt;} else { echo $search_txt; }?>"/>

               <input type="hidden" name="t" value="<?php echo $nonce; ?>" />
			   <input type="hidden" name="relation" class="sgo" value="<?php echo $exact_search; ?>" />
               <input type="submit" class="sgo" onclick="find_click(<?php echo $search_id;?>);" value="<?php _e('Search',DOMAIN);?>" />
			   <?php if(@$search_in_city ==1){ ?>
			     <input type="hidden" name="search_in_city" class="sgo" value="1" />
			   <?php } ?>
          </form>
          <script type="text/javascript">
			function find_click(search_id)
			{
				if(jQuery('#search_near-'+search_id).val() == '<?php  echo $search_txt; ?>')
				{
					jQuery('#search_near-'+search_id).val(' ');
				}
				if(jQuery('#location').val() == '<?php _e('Address',DIR_DOMAIN); ?>')
				{
					jQuery('#location').val('');
				}
			}
			
          </script>
		<?php
		echo '</div>';
		echo $args['after_widget'];
	}
	
	function update($new_instance, $old_instance) {
		//save the widget
		return $new_instance;
	}
	function form($instance) {
		//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('Search By Meta Key',ADMINDOMAIN) ,'post_type' => 'post' ) );		
		$title = strip_tags($instance['title']);
		$post_type = $instance['post_type'];
		$search_criteria = $instance['search_criteria'];
		$exact_search = $instance['exact_search'];	
		$search_in_city = $instance['search_in_city'];	
		if(empty($search_criteria)){ $search_criteria =array(); }
		if(empty($search_in_city)){ $search_in_city =array(); }
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title',ADMINDOMAIN);?>:
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('post_type');?>" ><?php echo __('Select Post Type',ADMINDOMAIN);?>:    </label>	
			<select  id="<?php echo $this->get_field_id('post_type');?>" name="<?php echo $this->get_field_name('post_type'); ?>[]" multiple="multiple" class="widefat" onclick="tevolution_search_fields(this.id,'<?php echo $this->get_field_name('search_criteria'); ?>','<?php echo $this->get_field_id('search_criteria'); ?>');">        	
				<?php
                    $all_post_types = get_option("templatic_custom_post");
					if(empty($post_type)){ $post_type = array('post','listing');  }
					
                    foreach($all_post_types as $key=>$post_types){
						if($key !=''){
					?>
						<option value="<?php echo $key;?>" <?php if(is_array($post_type) && in_array($key,@$post_type))echo "selected";?>><?php echo esc_attr($post_types['label']);?></option>
					<?php
                    } }
                    ?>	
			</select>
		</p>
		<p> <p id='search_process_<?php echo $this->get_field_name('search_criteria'); ?>' style='display:none;'><img src="<?php echo TEMPL_PLUGIN_URL."tmplconnector/monetize/templatic-monetization/images/process.gif"; ?>" alt='Processing..' /></p><label for="<?php echo $this->get_field_id('search_criteria'); ?>"><?php echo __('Enable to search from',ADMINDOMAIN);?>: </label> </p> 
             <p> 
				<label for="<?php echo $this->get_field_id(search_criteria_cats); ?>">
				<input id="<?php echo $this->get_field_id('search_criteria_cats'); ?>" name="<?php echo $this->get_field_name('search_criteria'); ?>[]" type="checkbox" value="cats" <?php if(in_array('cats',$search_criteria)){ ?>checked=checked<?php } ?>style="width:10px;"  /><b><?php echo __('Categories',ADMINDOMAIN);?></b>             
               </label></p>
			 <p>
				<label for="<?php echo $this->get_field_id('search_criteria_tags'); ?>">
					<input id="<?php echo $this->get_field_id('search_criteria_tags'); ?>" name="<?php echo $this->get_field_name('search_criteria'); ?>[]" type="checkbox" value="tags" <?php if(in_array('tags',$search_criteria)){ ?>checked=checked<?php } ?>style="width:10px;"  /><b><?php echo __('Tags',ADMINDOMAIN);?></b>             
               </label></p>
			<p> <label for="<?php echo $this->get_field_id('search_criteria_review'); ?>">
					<input id="<?php echo $this->get_field_id('search_criteria_review'); ?>" name="<?php echo $this->get_field_name('search_criteria'); ?>[]" type="checkbox" value="reviews" <?php if(in_array('reviews',$search_criteria)){ ?>checked=checked<?php } ?>style="width:10px;"  /><b><?php echo __('Reviews',ADMINDOMAIN);?></b>
				</label></p>
			<div class="searchable_fields" id="<?php echo $this->get_field_id('search_criteria'); ?>">
				<?php 
				$post_types = $post_type;
				$custom_fileds_ = array();
				for($i=0; $i <= count($post_types); $i++){
					if($post_types[$i] !=''){
						$default_custom_metaboxes = array_filter(get_search_post_fields_templ_plugin($post_types[$i],'custom_fields',$post_types)); 
						
						foreach($default_custom_metaboxes as $key=>$val) {
										$name = $val['name'];
										$site_title = $val['label'];
										$type = $val['type'];
										$htmlvar_name = $val['htmlvar_name']; 
										
										if(in_array($htmlvar_name,$search_criteria)){
											$checked = "1";
										}else{
											$checked = "";
										}
										$custom_fileds_[$htmlvar_name] = array(
													 'ID'=>$this->get_field_id("search_criteria_".$val['htmlvar_name']),
													 'name'=>$this->get_field_name('search_criteria'),
													 'htmlvar_name'=>$htmlvar_name,
													 'site_title' => $val['label'],
													  'checked' =>$checked);
									
						}
					}
				} 
						$custom_fileds_se  = array_filter($custom_fileds_);
						foreach($custom_fileds_se as $key=>$val) {
							$name = $val['name'];
							$site_title = $val['site_title'];
							$htmlvar_name = $val['htmlvar_name'];
							$id = $val['ID'];
							$checked = $val['checked'];
							if($checked ==1){ $checked = "checked=checked"; }else{ $checked = ''; }
						
							echo "<p><label for=".$id."><input id=".$id." name='".$name."[]' type='checkbox' value='".$htmlvar_name."' ".$checked."/><b>".$site_title."</b></label></p>";
						}
				?>
            </div>
			<p class="description"><?php echo __('Search results will come from all selected options.','ADMIN_DOMAIN'); ?></p>
			<p>
               <label for="<?php echo $this->get_field_id('exact_search'); ?>">
               <input id="<?php echo $this->get_field_id('exact_search'); ?>" name="<?php echo $this->get_field_name('exact_search'); ?>" type="checkbox" value="1" <?php if($exact_search =='1'){ ?>checked=checked<?php } ?>style="width:10px;"  /><b><?php echo __('Search with exact match conditions?',ADMINDOMAIN);?></b>             
               </label>
            </p>
			<p> <label for="<?php echo $this->get_field_id('search_in_city'); ?>">
					<input id="<?php echo $this->get_field_id('search_in_city'); ?>" name="<?php echo $this->get_field_name('search_in_city'); ?>[]" type="checkbox" value="search_in_city" <?php if(in_array('search_in_city',$search_in_city)){ ?>checked=checked<?php } ?>style="width:10px;"  /><b><?php echo __('Search from current city',ADMINDOMAIN);?></b>
				</label>
				 
			</p>
			<p><?php echo __('By default it will search from all cities, Enable the above option if you want to search from current city only',ADMINDOMAIN);?></p>   
     
		<?php	
		
		add_action('admin_footer','tevolution_searchable_scripts');		
					
	}
}
/*
 * templatic Key search widget init
 */
add_action( 'widgets_init', create_function('', 'return register_widget("templatic_key_search_widget");') );

add_action('wp_ajax_tevolution_searchable_fields','tevolution_searchable_fields');
add_action('wp_ajax_nopriv_tevolution_searchable_fields','tevolution_searchable_fields');		
/*
Name: Tevolution_searchable_fields
Desc: Tevolution back end search by address widget - fetch post types custom fields to enable on seach page.
*/
function tevolution_searchable_fields($post_types){
	$post_types = $_REQUEST['post_types'];
	$fname = $_REQUEST['fid'];
	
	$post_types = explode(',',$_REQUEST['post_types']);
	$fields = '';
	//$custom_fileds = array();
	for($i=0; $i <= count($post_types); $i++){ 
		if($post_types[$i] !=''){
			$default_custom_metaboxes = get_search_post_fields_templ_plugin($post_types[$i],'custom_fields',$post_types); 
			
			foreach($default_custom_metaboxes as $key=>$val) {
							
							$name = $val['name'];
							$site_title = $val['label'];
							$type = $val['type'];
							$htmlvar_name = $val['htmlvar_name'];
						//	if(in_array($htmlvar_name,$sc)){ $checked ="checked=checked"; }else{ $checked ="checked=checked"; }
							
							$custom_fileds[$htmlvar_name] = array('name'=>$val['htmlvar_name'],
													 'site_title' => $val['label'],
													 'fid'=> $fname ,
													);
													
			}
		}
	} 
	if(!empty($custom_fileds)){	
	$custom_fileds1  = array_filter($custom_fileds);
	
	foreach($custom_fileds1 as $key=>$val) {
		$htmlvar_name = $val['name'];
		$site_title = $val['site_title'];
		$ID = $val['fid'];
		$fields .='<p><label for="'.$ID.$htmlvar_name.'"><input id="'.$ID.$htmlvar_name.'" name="'.$fname.'[]" type="checkbox" value="'.$htmlvar_name.'" /><b>'.$site_title.'</b></label></p>';
	}
	}
	echo $fields;
	exit;

}

/*
Name: tevolution_searchable_scripts
Desc: To include search by address widget script in footer back end.
*/
function tevolution_searchable_scripts(){ ?>
	<script type="text/javascript">

	function tevolution_search_fields(fid,fname,fields_div){ 
				document.getElementById('search_process_'+fname).style.display = '';
				if(document.getElementById('process_search'))
					document.getElementById('process_search').style.display="block";	
				var post_types = jQuery('#'+fid).val();
				jQuery.ajax({
				url:ajaxUrl,
				type:'POST',
				data:'action=tevolution_searchable_fields&post_types=' + post_types+'&fid='+fname,
				success:function(results) {
					if(document.getElementById('process_search'))
						document.getElementById('process_search').style.display="none";		
					document.getElementById('search_process_'+fname).style.display = 'none';
					document.getElementById(fields_div).innerHTML=results;		
				}
			});
		}
	</script>
<?php }
?>
