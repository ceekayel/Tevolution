<?php
/*
 * Create the templatic browse by categories widget
 */
	
class templatic_browse_by_categories extends WP_Widget {
	function templatic_browse_by_categories() {
	//Constructor
		$widget_ops = array('classname' => 'widget Templatic browse_by_categories', 'description' => __('Display categories of a specific post type. Works best in sidebar areas.',ADMINDOMAIN) );
		$this->WP_Widget('templatic_browse_by_categories', __('T &rarr; Browse By Categories',ADMINDOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
	// prints the widget
		extract($args, EXTR_SKIP);
		echo $args['before_widget'];
		$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';
		global $current_cityinfo;
		$title = empty($instance['title']) ? __("Find By Category",DOMAIN) : apply_filters('widget_title', $instance['title']); 		
		$post_type = empty($instance['post_type']) ? '' : apply_filters('widget_post_type', $instance['post_type']); 		
		$categories_count = empty($instance['categories_count']) ? 0 : $instance['categories_count'] ;
		// Get all the taxonomies for this post type
		$output = 'names'; // or objects
		$operator = 'and'; // 'and' or 'or'
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));
		if($post_type!='post'){	
				$taxo=$taxonomies[0];
		}else
			$taxo='category';
						
		if ( $title <> "" ) { 
			echo $args['before_title'].$title.$args['after_title'];
		}	
		if(is_plugin_active('woocommerce/woocommerce.php') && $instance['post_type'] == 'product'){
			$taxo = $taxonomies[1];
		}
		$cat_args = array(
						'taxonomy'=>$taxo,
						'orderby' => 'name', 
						'show_count' => $categories_count, 
						'hide_empty'	=> 0,
						'echo'     => 0,
						'hierarchical' => 'true',
						'pad_counts' => 0,
						'title_li'=>'');	
		
		$transient_name=(!empty($current_cityinfo))? $current_cityinfo['city_slug']: '';		
		if ( false === ( $widget_category_list = get_transient( '_tevolution_query_browsecategories'.$post_type.$transient_name.$cur_lang_code )) && get_option('tevolution_cache_disable')==1 ) {
			do_action('tevolution_category_query');
			$widget_category_list =  wp_list_categories($cat_args);
			set_transient( '_tevolution_query_browsecategories'.$post_type.$transient_name.$cur_lang_code, $widget_category_list, 12 * HOUR_IN_SECONDS );				
		}elseif(get_option('tevolution_cache_disable')==''){
			do_action('tevolution_category_query');
			$widget_category_list =  wp_list_categories($cat_args);			
		}
		
		
		?>
		<script type="text/javascript">
			jQuery.noConflict();
			jQuery(document).ready(function() {
				jQuery('.browse_by_category ul.children').css({"display":"none"});
				jQuery('ul.browse_by_category li:has(> ul)').addClass('hasChildren');
				jQuery('ul.browse_by_category li.hasChildren').mouseenter(function () {
					jQuery(this).addClass('heyHover').children('ul').show();
					return false;
				});
				jQuery('ul.browse_by_category li.hasChildren').mouseleave(function () {
					jQuery(this).removeClass('heyHover').children('ul').hide();
					return false;
				});
			});
		</script>
		<?php 
			echo '<ul class="browse_by_category">';
			echo $widget_category_list;
			echo "</ul>";			
		
		
		echo $args['after_widget'];	
	}
	function update($new_instance, $old_instance) {
	//save the widget	
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['post_type'] = strip_tags($new_instance['post_type']);	
		$instance['categories_count'] = strip_tags($new_instance['categories_count']);
		return $instance;
	}
	function form($instance) {
	//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'title' => '','post_type'=>'', 'categories_count' => '') );
		$title = ($instance['title']) ? strip_tags($instance['title']) : __("Find By Category",ADMINDOMAIN);
		$current_post_type = (strip_tags($instance['post_type'])) ? strip_tags($instance['post_type']) : 'post';
		$categories_count = (strip_tags($instance['categories_count'])) ? strip_tags($instance['categories_count']) : '' ;
	?>
	<p>
	  <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title:',ADMINDOMAIN);?>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	  </label>
	</p>	   
    <p>
    	<label for="<?php echo $this->get_field_id('post_type');?>" ><?php echo __('Select Post Type:',ADMINDOMAIN);?></label>	
    	<select  id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="widefat">        	
    <?php
		$all_post_types = get_option("templatic_custom_post");
		foreach($all_post_types as $key=>$post_type){?>
			<option value="<?php echo $key;?>" <?php if($key== $current_post_type)echo "selected";?>><?php echo esc_attr($post_type['label']);?></option>
     <?php }?>	
    	</select>
   
    	<span><?php echo __('Display all categories list from the selected post type.',ADMINDOMAIN);?></span>
    </p>
	<p>
      <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('categories_count'); ?>" name="<?php echo $this->get_field_name('categories_count'); ?>"  <?php if(esc_attr($categories_count)) echo 'checked';?> />
	  <label for="<?php echo $this->get_field_id('categories_count'); ?>"><?php echo __('Show Categories Count:',ADMINDOMAIN);?></label>
	</p>	
	<?php
	}
}
/*
 * templatic recent post widget init
 */
add_action( 'widgets_init', create_function('', 'return register_widget("templatic_browse_by_categories");') );
?>