<?php
/*
 * Create the templatic advanced search widget
 */
class templatic_advanced_search extends WP_Widget {
	function templatic_advanced_search() {
	//Constructor
		$widget_ops = array('classname' => 'widget templatic-advanced-search', 'description' => __('Display search fields for a specific post type. Custom fields selected to show inside the Advanced search form will also show inside this widget. Works best in sidebar areas.',ADMINDOMAIN),'before_widget'=>'<div class="column_wrap">' );
		$this->WP_Widget('templatic_advanced_search', __('T &rarr; Advanced Search',ADMINDOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
	// prints the widget
		extract($args, EXTR_SKIP);
	
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']); 		
		$post_type = empty($instance['post_type']) ? 'post' : apply_filters('widget_post_type', $instance['post_type']);
		echo $args['before_widget'];
		if (function_exists('icl_register_string')) {	
			icl_register_string(DOMAIN,'templatic_about_title'.$title,$title);
			$title = icl_t(DOMAIN, 'templatic_about_title'.$title,$title);
		}
		if ( $title <> "" ) { 
			echo $args['before_title'].$title.$args['after_title'];
		}
		?>
          <div class="templatic_advanced_search">
          <?php echo do_shortcode("[advance_search_page post_type='".$post_type."']"); ?>
          </div>
        <?php		
		echo $args['after_widget'];
	}
	function update($new_instance, $old_instance) {
	//save the widget
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['post_type'] = $new_instance['post_type'];
		return $instance;
	}
	function form($instance) {
	//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'title' => '',  'post_type' => '',) );
		$title = ($instance['title']) ? $instance['title'] : __("Advanced Search",ADMINDOMAIN);
		$current_post_type = ($instance['post_type']) ? $instance['post_type'] : 'post';
	?>
	<p>
	  <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title:',ADMINDOMAIN);?>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	  </label>
	</p>	
	<p>
    	<label for="<?php echo $this->get_field_id('post_type');?>" ><?php echo __('Post Type:',ADMINDOMAIN);?>     </label>	
    	<select  id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="widefat">        	
			<option value="post"><?php _e("Post");?></option>
    <?php
		$all_post_types = get_option("templatic_custom_post");
		foreach($all_post_types as $key=>$post_type){ ?>
			<option value="<?php echo $key;?>" <?php if($key == $current_post_type){ echo 'selected="selected"';}?>><?php echo esc_attr($post_type['label']);?></option>
     <?php }?>	
    	</select>
   
    	<p class="description"><?php echo __('Select the post type you wish to display on an advanced search form.',ADMINDOMAIN);?></p>
    </p>
	<?php
	}
}
/*
 * templatic about us widget init
 */
add_action( 'widgets_init', create_function('', 'return register_widget("templatic_advanced_search");') );
?>
