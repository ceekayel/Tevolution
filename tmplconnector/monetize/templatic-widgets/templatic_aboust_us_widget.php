<?php
/*
 * Create the templatic about us widget
 */
	
class templatic_aboust_us extends WP_Widget {
	function templatic_aboust_us() {
	//Constructor
		$widget_ops = array('classname' => 'widget Templatic About Us', 'description' => __('Use this widget to show some basic information about your website or company. HTML tags are allowed. Works best in sidebar areas.',ADMINDOMAIN),'before_widget'=>'<div class="column_wrap">' );
		$this->WP_Widget('templatic_aboust_us', __('T &rarr; About Us',ADMINDOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
	// prints the widget
		extract($args, EXTR_SKIP);
	
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']); 		
		$about_us = empty($instance['about_us']) ? __("Templatic is five year old company which specilizes in creating beautiful app-like WordPress themes. Our themes are known for their stunning design and powerful features which provide a unique experience for visitors to the tens of thousands of websites we have helped create. Your dream site is literally just a few clicks away!",DOMAIN) : apply_filters('widget_about_us', $instance['about_us']);
		echo $args['before_widget'];
		if (function_exists('icl_register_string')) {	
			icl_register_string(DOMAIN,'templatic_about_title'.$title,$title);
			$title = icl_t(DOMAIN, 'templatic_about_title'.$title,$title);
			icl_register_string(DOMAIN,'templatic_about_description'.$about_us,$about_us);
			$about_us = icl_t(DOMAIN, 'templatic_about_description'.$about_us,$about_us);
		}
		if ( $title <> "" ) { 
			echo $args['before_title'].$title.$args['after_title'];
		}
		?>
        <div class="templatic_about_us">
        	<?php echo $about_us;?>
        </div>
        <?php		
		echo $args['after_widget'];
	}
	function update($new_instance, $old_instance) {
	//save the widget
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['about_us'] = $new_instance['about_us'];
		return $instance;
	}
	function form($instance) {
	//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'title' => '',  'about_us' => '',) );
		$title = ($instance['title']) ? $instance['title'] : __("About Templatic",ADMINDOMAIN);
		$about_us = ($instance['about_us']) ? $instance['about_us'] : __("Templatic is five year old company which specilizes in creating beautiful app-like WordPress themes. Our themes are known for their stunning design and powerful features which provide a unique experience for visitors to the tens of thousands of websites we have helped create. Your dream site is literally just a few clicks away!",ADMINDOMAIN) ;
	?>
	<p>
	  <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title:',ADMINDOMAIN);?>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	  </label>
	</p>	
	<p>	  
    	<label for="<?php echo $this->get_field_id('about_us'); ?>"><?php echo __('Description:',ADMINDOMAIN);?>
    	<textarea class="widefat" name="<?php echo $this->get_field_name('about_us'); ?>" cols="20" rows="16"><?php echo esc_attr($about_us); ?></textarea>	
        </label>
	</p>	
	<?php
	}
}
/*
 * templatic about us widget init
 */
add_action( 'widgets_init', create_function('', 'return register_widget("templatic_aboust_us");') );
?>
