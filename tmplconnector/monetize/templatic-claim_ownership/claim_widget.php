<?php
class claim_widget extends WP_Widget
{
	function claim_widget()
	{
		/* CONSTRUCTOR */
		$widget_ops = array('classname' => 'widget claim_widget', 'description' => __('Display a button for claiming ownership of a post. Edit Claim Ownership settings from Tevolution &raquo; General Settings. Use the widget in detail page sidebar areas.',DOMAIN) );		
		$this->WP_Widget('claim_widget', __('T &rarr; Claim Ownership',DOMAIN), $widget_ops);
	}
	
	function widget($args, $instance)
	{
		/* THIS WILL PRINT THE WIDGET IN FRONT END */
		extract($args, EXTR_SKIP);
		$title = empty($instance['title']) ? __("Claim Ownership",DOMAIN) : apply_filters('widget_title', $instance['title']); 
		$display_type = empty($instance['display_type']) ? 'Link' : apply_filters('widget_display_type', $instance['display_type']); 		
		echo $before_widget;
		global $current_user,$post;
		if(@$post->post_author!=@$current_user->ID)
		{
			if (!empty($instance['title']))
			{
				if(!isset($_REQUEST['pid']) && @$_REQUEST['pid']== '')
				{
					echo $before_title . apply_filters('widget_title', $instance['title']) . $after_title;
				}
			}
			?>
			
			  
			<?php insert_claim_ownership_data($_POST); /* CALL A FUNCTION TO INSERT CLAIM POST DATA */?>
			
			
			<?php /* DISPLAYING THE WIDGET IN FRONT END */
			global $post,$wpdb,$claimpost;
			$ptype = get_post_type($post->ID);
			global $current_user;	
	
			$tmpdata = get_option('templatic_settings'); /* FETCH CLAIM SETTINGS */
			$post_type = $tmpdata['claim_post_type_value']; /* FETCH CLAIM POST TYPES */
			
			
			if(is_array($post_type) && in_array($ptype,$post_type) && @$post->post_author!=@$current_user->ID):
				foreach ($post_type as $key => $val) :
					if($val == $ptype || $post_type == '') /* CONDITION FOR WHICH POST TYPES IT SHOULD DISPLAY */
					{
						echo '<div class="widget login_widget claim_ownership">';
						if(is_single() || is_page() && !is_page_template('page-template_form.php') ) : 						
							if(get_post_meta($post->ID,'is_verified',true) == 1)
							{ ?>
							<p class="i_verfied"><?php echo OWNER_VERIFIED; ?></p>
						<?php }
							else
							{ 
								$current_ip = $_SERVER["REMOTE_ADDR"]; /* FETCH CURRENT USER IP ADDRESS */												
								$post_claim_id = $wpdb->get_col("SELECT ID from $wpdb->posts WHERE (post_content = '".$post->ID."') AND post_status = 'publish' AND (post_excerpt = 'approved' OR post_excerpt = '') AND post_type='claim' limit 0,1");
								if(count($post_claim_id) > 0 )
								{
									foreach($post_claim_id as $key=>$val)
									{
										$data = get_post_meta($val,'post_claim_data',true); /* FETCH CLAIM ID */									
										if( $post->ID == $data['post_id'] )
										{
											$user_ip = $data['claimer_ip']; /* FETCH IP ADDRESS OF CLAIMED POST */
											if($current_ip == $user_ip && $user_ip != '')
											{ ?>
												<p class="claimed"><?php echo ALREADY_CLAIMED; ?>.</p>
									  <?php } else {
												if($display_type == 'Link') : ?>
												<a href="#claim_listing" id="trigger_id" title="claim_ownership" class="i_claim c_sendtofriend"><?php echo CLAIM_BUTTON;?></a>
												<?php else : ?>
												<a href="#claim_listing" id="trigger_id" title="claim_ownership" class="i_claim c_sendtofriend">
												<input type="button" value="<?php echo CLAIM_BUTTON;?>" /></a>
												<?php endif;
												include_once (TEMPL_MONETIZE_FOLDER_PATH . "templatic-claim_ownership/popup_claim_form.php"); ?>
												<?php }
										}
									}
								}
								else
								{
									if($display_type == 'Link') : ?>
										<a href="#claim_listing" id="trigger_id" title="claim_ownership" class="i_claim c_sendtofriend"><?php echo CLAIM_BUTTON;?></a>
									<?php else : ?>
										<a href="#claim_listing" id="trigger_id" title="claim_ownership" class="i_claim c_sendtofriend">
										<input type="button" value="<?php echo CLAIM_BUTTON;?>" /></a>
									<?php endif;
									include_once (TEMPL_MONETIZE_FOLDER_PATH . "templatic-claim_ownership/popup_claim_form.php"); 
								}
							}
						endif;
						echo '</div>';
					}
				endforeach;
			else:
				if(!isset($_REQUEST['pid']) && $_REQUEST['pid']== '')
					_e('Claim Ownership Is Not Enable',DOMAIN);		
			endif;
		}
				echo $after_widget;
	}
	
	function update($new_instance, $old_instance)
	{
		/* SAVE THE WIDGET */
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['display_type'] = strip_tags($new_instance['display_type']);
		return $instance;
	}
	
	function form($instance)
	{
		/* DISPLAY WIDGET FORM IN BACK END */
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'display_type' => 'link') );
		$title = (strip_tags($instance['title'])) ? strip_tags($instance['title']) : __("Claim Ownership",DOMAIN); 
		$display_type = (strip_tags($instance['display_type'])) ? strip_tags($instance['display_type']) : 'Link'; ?>
		
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title',DOMAIN); ?>: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('display_type'); ?>"><?php echo SELECT_DISPLAY_TYPE; ?>: </label>
          	<select name="<?php echo $this->get_field_name('display_type'); ?>" id="<?php echo $this->get_field_id('display_type'); ?>">
               	<option value="Link" <?php if($display_type == 'Link'){echo 'selected';}?>><?php echo LINK;?></option>
                    <option value="Button" <?php if($display_type == 'Button'){echo 'selected';}?>><?php echo BUTTON;?></option>
               </select>
		</p>
	<?php
	}
}?>