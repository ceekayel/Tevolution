<?php
/*
 * Function Name: tevolution_advance_search_page
 * Return: display the advance search form
 */
function tevolution_advance_search_page($atts){
	extract( shortcode_atts( array (
			'post_type'   =>'post',				
			), $atts ) 
		);	
	ob_start();
	global $wp_locale;
	/* include datepicker js file */
	wp_enqueue_script('jquery-ui-datepicker');	
		 //localize our js
		$aryArgs = array(
			'monthNames'        => strip_array_indices( $wp_locale->month ),
			'monthNamesShort'   => strip_array_indices( $wp_locale->month_abbrev ),
			'monthStatus'       => __( 'Show a different month', DOMAIN ),
			'dayNames'          => strip_array_indices( $wp_locale->weekday ),
			'dayNamesShort'     => strip_array_indices( $wp_locale->weekday_abbrev ),
			'dayNamesMin'       => strip_array_indices( $wp_locale->weekday_initial ),
			// is Right to left language? default is false
			'isRTL'             => (isset($wp_locale->is_rtl))? $wp_locale->is_rtl :'',
		);
	 
		// Pass the array to the enqueued JS
	wp_localize_script( 'jquery-ui-datepicker', 'objectL11tmpl', $aryArgs );
	remove_filter( 'the_content', 'wpautop' , 12);
	add_action('wp_footer','tev_advsearch_script');
	?>
	
     <form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="form_front_style">        
          <div class="form_row clearfix">               
               <input class="adv_input" name="s" id="adv_s" type="text" PLACEHOLDER="<?php _e('Search',DOMAIN); ?>" value="" />			  
               <span class="message_error2"  style="color:red;font-size:12px;" id="search_error"></span>			  
          </div>
          <!--Tags -->
          <div class="form_row clearfix">               
               <input class="adv_input" name="tag_s" id="tag_s" type="text"  PLACEHOLDER="<?php _e('Tags',DOMAIN); ?>" value=""  />			  
          </div>
          <!-- Post Type Castegory-->
          <div class="form_row clearfix">
          	<?php
			/*fetch the categories of selected post type */
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));
			$args = array(
						'show_option_all'    => __('Select Categories',DOMAIN),
						'show_option_none'   => '',
						'orderby'            => 'name', 
						'order'              => 'ASC',
						'show_count'         => 0,
						'hide_empty'         => 0, 
						'child_of'           => 0,
						'echo'               => 1,
						'selected'           => 0,
						'hierarchical'       => 1, 
						'name'               => 'category',
						'tab_index'          => 0,
						'taxonomy'           => $taxonomies[0],
						'hide_if_empty'      => false,
					);
					wp_dropdown_categories($args);
			?>                          
          </div>
          <div class="form_row clearfix">              
               <input name="articleauthor" type="text" PLACEHOLDER="<?php _e('Author',DOMAIN); ?>" />
               <label class="adv_author">
               <?php _e('Exact author',DOMAIN);?>
               <input name="exactyes" type="checkbox" value="1" class="checkbox" />	
               </label>
          </div>
          <?php 
		if(function_exists('get_search_post_fields_templ_plugin')){			
			$default_custom_metaboxes = get_search_post_fields_templ_plugin($post_type,'custom_fields','post');
			display_search_custom_post_field_plugin($default_custom_metaboxes,'custom_fields','post');//displaty custom fields html.
			}
		?>
          
          <input type="hidden" name="search_template" value="1"/>
          <!--<input class="adv_input" name="adv_search" id="adv_search" type="hidden" value="1"  />-->
          <input class="adv_input" name="post_type" id="post_type" type="hidden" value="<?php echo $post_type; ?>"  />
          <input type="submit" name="submit" value="<?php _e('Search',DOMAIN); ?>" class="adv_submit"  onclick="return set_adv_search();"/>              
     </form>
     <?php	
	return ob_get_clean();
}

function tev_advsearch_script(){ ?>
	<script>
	function set_adv_search()
	{
		if(document.getElementById('adv_s').value == '')
		{
			document.getElementById('adv_s').value = ' ';
		}
		return true;
	}
	</script>
<?php
}
?>
