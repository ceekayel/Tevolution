<?php
	/* Tevolution permalink settings file*/
	
	add_action('templ_add_admin_menu_', 'tevolution_taxonomies_permalink',100);
	function tevolution_taxonomies_permalink(){
		
		$menu_title = __('Customize Permalinks',ADMINDOMAIN);
		add_submenu_page('templatic_system_menu', "",   '<span class="tevolution-menu-separator" style="display:block; 1px -5px;  padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>',"administrator", "admin.php?page=templatic_system_menu"  );
		$taxonomy_screen_option = add_submenu_page('templatic_system_menu', $menu_title,$menu_title, 'administrator', 'custom_taxonomies_permalink', 'add_custom_taxonomies_permalink');	
	}

	function add_custom_taxonomies_permalink(){	
	
	$purl=site_url().'/wp-admin/admin.php?page=custom_taxonomies_permalink';	
	if(isset($_REQUEST['action']) && $_REQUEST['action']=='tevolution_reset_rules'){
		$tevolution_taxonomies_rules_data=array();
		update_option('tevolution_taxonomies_rules_data',$tevolution_taxonomies_rules_data);
		/* tevolution custom taxonomy */
		$tevolution_taxonomies=get_option('templatic_custom_taxonomy');			
		foreach($tevolution_taxonomies as $key=>$value){			
			if($key!=""){
				$tevolution_taxonomies[$key]['rewrite']=array('slug' => $key,'with_front' => false,'hierarchical' => true);
			}
		}		
		update_option('templatic_custom_taxonomy',$tevolution_taxonomies);
		/* Tevolution custom tags */
		$tevolution_taxonomies_tags=get_option('templatic_custom_tags');
		foreach($tevolution_taxonomies_tags as $key=>$value){			
			if($key!=""){
				$tevolution_taxonomies_tags[$key]['rewrite']=array('slug' => $key,'with_front' => false,'hierarchical' => true);
			}
		}
		update_option('templatic_custom_tags',$tevolution_taxonomies_tags);	
		/* Tevolution Custom post type */
		$tevolution_post=get_option('templatic_custom_post');
		foreach($tevolution_post as $key=>$value){
			if($key!=""){
				$tevolution_post[$key]['rewrite']=array('slug' => $key,'with_front' => false,'hierarchical' => true);
			}
		}
		update_option('templatic_custom_post',$tevolution_post);	
		tevolution_taxonimies_flush_event();
		
		$echo=__('All Tevolution custom permalink rules reset.', ADMINDOMAIN);
	}
	
	
	if(isset($_POST['taxonomies_permalink_submit']) && (isset($_POST['tevolution_taxonomies_rewrite_rules']) && $_POST['tevolution_taxonomies_rewrite_rules']=='true')){
		
		/* Tevolutuon Custom taxonomies */
		if(isset($_POST['tevolution_taxonimies_remove'])){
			foreach($_POST['tevolution_taxonimies_remove'] as $key=>$value){
				$tevolution_taxonomies_data['tevolution_taxonimies_remove'][$key]=$value;
			}
		}
		if(isset($_POST['tevolution_taxonimies_add'])){
			$tevolution_taxonomies=get_option('templatic_custom_taxonomy');
			if(!empty($tevolution_taxonomies)){
				foreach($tevolution_taxonomies as $key=>$value){
					$taxonomies_key[]=$key;
				}
			}
			$tevolution_taxonomies_tags=get_option('templatic_custom_tags');
			if(!empty($tevolution_taxonomies_tags)){
				foreach($tevolution_taxonomies_tags as $key=>$value){
					$tags_key[]=$key;
				}
			}			
			foreach($_POST['tevolution_taxonimies_add'] as $key=>$value){
				$tevolution_taxonomies_data['tevolution_taxonimies_add'][$key]=$value;
				if($value!="" && in_array($key,$taxonomies_key)){					
					$tevolution_taxonomies[$key]['rewrite']=array('slug' => $value,'with_front' => false,'hierarchical' => true);
				}elseif(in_array($key,$taxonomies_key)){
					$tevolution_taxonomies[$key]['rewrite']=array('slug' => $key,'with_front' => false,'hierarchical' => true);
				}
				if($value!="" && in_array($key,$tags_key)){
					$tevolution_taxonomies_tags[$key]['rewrite']=array('slug' => $value,'with_front' => false,'hierarchical' => true);
				}elseif(in_array($key,$tags_key)){
					$tevolution_taxonomies_tags[$key]['rewrite']=array('slug' => $key,'with_front' => false,'hierarchical' => true);
				}
			}
			update_option('templatic_custom_taxonomy',$tevolution_taxonomies);
			update_option('templatic_custom_tags',$tevolution_taxonomies_tags);
		}
		/*Finish Tevolution Custom taxonomies */
		
		/* Tevolution Custom Post Type*/
		if(isset($_POST['tevolution_single_post_remove'])){
			foreach($_POST['tevolution_single_post_remove'] as $key=>$value){
				$tevolution_taxonomies_data['tevolution_single_post_remove'][$key]=$value;
			}
		}
		if(isset($_POST['tevolution_single_post_add'])){
			$tevolution_post=get_option('templatic_custom_post');			
			if(!empty($tevolution_post)){
				foreach($tevolution_post as $key=>$value){
					$posttype[]=$key;
				}
			}					
			foreach($_POST['tevolution_single_post_add'] as $key=>$value){
				$tevolution_taxonomies_data['tevolution_single_post_add'][$key]=($value)? $value:$key;
				if($value!="" && in_array($key,$posttype)){					
					$tevolution_post[$key]['rewrite']=array('slug' => $value,'with_front' => false,'hierarchical' => true);
				}else{
					$tevolution_post[$key]['rewrite']=array('slug' => $key,'with_front' => false,'hierarchical' => true);
				}
			}
				
		}
		/* Finish Tevolution Custom Post Type*/
		
		/* Start Tevolution Author*/
		if(isset($_POST['tevolution_author'])){
			$tevolution_taxonomies_data['tevolution_author']=$_POST['tevolution_author'];
		}
		if(isset($_POST['tevolution_remove_author_base'])){
			$tevolution_taxonomies_data['tevolution_remove_author_base']=$_POST['tevolution_remove_author_base'];
		}else{
			unset($tevolution_taxonomies_data['tevolution_remove_author_base']);
		}
		/* Finish Tevolution Author*/
		$tevolution_taxonomies_data=apply_filters('tevolution_taxonomies_rules_data',$tevolution_taxonomies_data);		
		update_option('tevolution_taxonomies_rules_data',$tevolution_taxonomies_data);		
		tevolution_taxonimies_flush_event();		
		$echo=__('All Tevolution custom permalink rules options updated, Make sure you clear Tevolution cache after saving these settings.', ADMINDOMAIN);
	}
	$tevolution_taxonomies_data=get_option('tevolution_taxonomies_rules_data');
	$city_slug='';
	?>
     <div id="tevolution-page" class="wrap">
		<h2><?php echo __('Customize Tevolution permalinks',ADMINDOMAIN);?></h2>
          <!-- Remove Taxonomies and Author Base -->
          <form method="post" action="">
          <?php if(isset($echo) && $echo!=''){?>
				<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $echo;?></p></div>
		<?php }?>
          
          <?php do_action('tevolution_top_taxonimies_permalink',$tevolution_taxonomies_data)?>
          
          <div class="tevolution-section">
               <div class="tevo_sub_title"><?php echo __('Change or remove custom taxonomy base', ADMINDOMAIN);?></h3></div>
			    <p class="tevolution_desc"> <?php echo __('<strong>Be careful</strong>: With the bases removed you need to be extra careful while naming your categories. Slugs across all post types must be unique.',ADMINDOMAIN)?> </p>
				<?php do_action('tev_before_permaliknk_frm'); ?>
               <table class="form-table tevolution-inputs-taxonomies">
                    <?php 
				$tevolution_taxonomies=get_option('templatic_custom_taxonomy');
				if(!empty($tevolution_taxonomies)){
					foreach($tevolution_taxonomies as $key=>$value){
						$taxonomies[]=$key;
					}
				}
				$tevolution_taxonomies_tags=get_option('templatic_custom_tags');
				if(!empty($tevolution_taxonomies_tags)){
					foreach($tevolution_taxonomies_tags as $key=>$value){
						$taxonomies[]=$key;
					}
				}
				if(empty($taxonomies)){
					return;	
				}
				do_action('tev_before_permaliknk_frmrow');
				foreach (get_taxonomies('','objects') as $key => $taxonomy){					
                         if(!$taxonomy->rewrite){continue;}

					if(is_plugin_active('Tevolution-LocationManager/location-manager.php')){
						$location_post_type=implode(',',get_option('location_post_type'));
						if (strpos($location_post_type,','.$taxonomy->name) == false) {
							$city_slug='';
						}else{
							$city_slug= __('<em>(city-base)</em>/new-york/', ADMINDOMAIN);
						}
					}
					if(in_array($key,$taxonomies)){
                         ?>
                         <tr valign="top">
                              <th scope="row" style="width:18%;"><?php echo " ".$taxonomy->labels->name." " ; echo __('Base', ADMINDOMAIN); ?>
							 </th>
                              <td valign="top">
                              	<div class="clearfix">
                                        <fieldset>
                                        
                                        <p><input type="checkbox" id="<?php echo $taxonomy->name; ?>_remove_base" onclick="hidePermalinkbase(this,'<?php echo $taxonomy->name; ?>_base')" name="tevolution_taxonimies_remove[<?php echo $taxonomy->name; ?>]" <?php if($tevolution_taxonomies_data!='' && isset($tevolution_taxonomies_data['tevolution_taxonimies_remove'][$taxonomy->name])){echo "checked=checked";} ?> /><label for="<?php echo $taxonomy->name; ?>_remove_base"> <?php echo __('Remove base'); ?></label></p>
                                                                            
                                        </fieldset>
                                   </div>                                   
                                  <div id="<?php echo $taxonomy->name; ?>_base" class="clearfix" <?php if($tevolution_taxonomies_data!='' && isset($tevolution_taxonomies_data['tevolution_taxonimies_remove'][$taxonomy->name])){echo "style='display:none'";} ?> >
                                        <fieldset>
                                        <p> <?php echo __(' Or change the base to'); ?></p>
                                        <?php $tevolution_taxonimies_add_name = ($tevolution_taxonomies_data!='' && $tevolution_taxonomies_data['tevolution_taxonimies_add'][$taxonomy->name])? $tevolution_taxonomies_data['tevolution_taxonimies_add'][$taxonomy->name] : '';?>
                                        <p><code><?php echo get_bloginfo('url');?>/<?php echo $city_slug;?></code><input type="text" name="tevolution_taxonimies_add[<?php echo $taxonomy->name; ?>]" value="<?php echo $tevolution_taxonimies_add_name;?>" placeholder="<?php echo $taxonomy->name;?>" /></p>
                                        <p class="description"><?php echo __('Leave blank to keep the default setting',ADMINDOMAIN);?></p>    
                                        </fieldset>
                                   </div>
                              </td>
                         </tr>
                         <?php
						}
                         }
						do_action('tev_after_permaliknk_frmrow');						 
                         ?>
               </table>
          </div>
          <div class="tevolution-section">
               <div class="tevo_sub_title"><?php echo __('Change or remove custom post type base', ADMINDOMAIN);?></div>
               <table class="form-table tevolution-inputs-taxonomies">
                    <?php 
				$tevolution_post=get_option('templatic_custom_post');
				if(!empty($tevolution_post)){
					foreach($tevolution_post as $key=>$value){
						$posttype[]=$key;
					}
				}				
				if(empty($posttype)){
					return;	
				}
				foreach ( get_post_types( '', 'objects' ) as $key => $posts){				
                         if(!$posts->rewrite){continue;}
					if(is_plugin_active('Tevolution-LocationManager/location-manager.php')){
						$location_post_type=','.implode(',',get_option('location_post_type'));						
						if (strpos($location_post_type,','.$posts->name) !== false) {
							
							$city_slug= __('<em>(city-base)</em>/new-york/', ADMINDOMAIN);
						}else{
							$city_slug='';
						}
					}
					
					if(in_array($key,$posttype)){						
                         ?>
                         <tr valign="top">
                              <th scope="row" style="width:18%;"><?php echo " ".$posts->labels->name." " ; echo __('Base', ADMINDOMAIN); ?>
							  </th>
                              <td valign="top">                              	
                                   <div class="clearfix">                                   
                                        <fieldset>
                                        <p><input type="checkbox" id="<?php echo $posts->name; ?>_remove_base" onclick="hidePermalinkbase(this,'<?php echo $posts->name; ?>_base')" name="tevolution_single_post_remove[<?php echo $posts->name; ?>]" <?php if($tevolution_taxonomies_data!='' && $tevolution_taxonomies_data['tevolution_single_post_remove'][$posts->name]){echo "checked=checked";} ?> /> <label for="<?php echo $posts->name; ?>_remove_base"><?php echo __('Remove base'); ?></label></p>
								</fieldset>
							</div>
                                   <div id="<?php echo $posts->name; ?>_base" class="clearfix" <?php if($tevolution_taxonomies_data!='' && $tevolution_taxonomies_data['tevolution_single_post_remove'][$posts->name]){echo "style='display:none'";} ?>>
                                   	
                                        <p> <?php echo __('Or change the base to'); ?></p>
                                        <?php $tevolution_single_post_add = ($tevolution_taxonomies_data!='' && $tevolution_taxonomies_data['tevolution_single_post_add'][$posts->name])? $tevolution_taxonomies_data['tevolution_single_post_add'][$posts->name] : $posts->name;?>
                                        <p><code><?php echo get_bloginfo('url');?>/<?php echo $city_slug;?></code><input type="text" name="tevolution_single_post_add[<?php echo $posts->name; ?>]" value="<?php echo $tevolution_single_post_add;?>" placeholder="<?php echo $posts->name;?>" /></p>
                                        <p class="description"><?php echo __('Leave blank to keep the default setting',ADMINDOMAIN);?></p>    
                                        </fieldset>
                                   </div>
                              </td>
                         </tr>
                         <?php
						}
                         }	
                         ?>
               </table>
          </div>
          
          <div class="tevolution-section">
               <div class="tevo_sub_title"><?php echo __('Change or remove other base slugs', ADMINDOMAIN);?></div>
               <table class="form-table tevolution-inputs-taxonomies">                   
                    <tr valign="top">
                         <th scope="row" style="width:18%;"><?php echo __('Author Base', ADMINDOMAIN); ?></th>
                         <td >
                         	<div class="clearfix">
                                   <fieldset>
                                   <p>
                                   <input type="checkbox" id="tevolution_author_remove_base" onclick="hidePermalinkbase(this,'tevolution_author_base')" name="tevolution_remove_author_base" <?php if(@$tevolution_taxonomies_data['tevolution_remove_author_base']){echo "checked=checked";} ?> /><label for="tevolution_author_remove_base"><?php echo __('Remove base'); ?></label></p>
                                   
                                   </fieldset>
                              </div>                              
                              <div id="tevolution_author_base" class="clearfix" <?php if(@$tevolution_taxonomies_data['tevolution_remove_author_base']){echo "style='display:none'";} ?>>
                              	
							<p> <?php echo __('Or Change the base to'); ?></p>
                                   <input type="text" name="tevolution_author" value="<?php echo $tevolution_taxonomies_data['tevolution_author']; ?>" />
                                   <p><code><?php echo get_bloginfo('url');?>/<em><?php echo __('(your-base)', ADMINDOMAIN);?></em>/admin</code></p>
                              </div>
                         </td>                         
                    </tr>               
               </table>
          </div>
          
		<?php  do_action('tevolution_bottom_taxonimies_permalink',$tevolution_taxonomies_data);?>
          
          <div class="tevolution_taxonimies_menu">
               <a  href="<?php echo wp_nonce_url($purl."&action=tevolution_reset_rules", 'tevolution_taxonomies_reset_settings'); ?>" class="button-secondary">Reset all rules</a>
               <input type="hidden" name="tevolution_taxonomies_rewrite_rules" value="true"  />
               <input type="hidden" name="action" value="update" />
               <input type="submit" name="taxonomies_permalink_submit" value="Save all changes" class="button-primary">
               <p><?php echo __('Make sure you clear Tevolution cache after saving these settings.',ADMINDOMAIN);?></p>
          </div>
		</form>
     </div>
     <script type="text/javascript">
	function hidePermalinkbase(str,id){
		if(str.checked){		
			jQuery("#"+id).hide();
		}else{
			jQuery("#"+id).show();
		}
	}
	</script>
     <?php
	 
 }



function tevolution_taxonimies_refresh_rewrite_rules_later(){
	wp_schedule_single_event(time(), 'flush_event');
}
function tevolution_taxonimies_flush_event(){
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}


/*
 * Function Name: tevolution_taxonimies_filter_rewrite_rules
 * Return: rewrite urles after change the tevolution base permalink
 */
function tevolution_taxonimies_filter_rewrite_rules($rewrite_rules){	
	global $wpdb;
	
	$tevolution_taxonomies_data=get_option('tevolution_taxonomies_rules_data');
	/*
	 * Remove single custom post type slug rewrite rule
	 */	 
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php')){
		$multi_city=(get_option('location_multicity_slug'))? get_option('location_multicity_slug') : 'city';
	}	
	$tevolution_post=get_option('templatic_custom_post');	
	if(!empty($tevolution_post)){
		foreach($tevolution_post as $key=>$value){	
			if($key =='event'){
				$querystr = "SELECT {$wpdb->posts}.post_name FROM {$wpdb->posts} WHERE ({$wpdb->posts}.post_status = 'publish' or {$wpdb->posts}.post_status = 'recurring') AND {$wpdb->posts}.post_type = '{$key}'";
			}else{
				$querystr = "SELECT {$wpdb->posts}.post_name FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_status = 'publish' AND {$wpdb->posts}.post_type = '{$key}'";
			}
			$posts = $wpdb->get_results($querystr, OBJECT);
			foreach ($posts as $post) {
				if(is_plugin_active('Tevolution-LocationManager/location-manager.php')){
					unset($rewrite_rules[$multi_city.'/([^/]+)/'.$post->post_name.'$']);
					unset($rewrite_rules[$multi_city.'/([^/]+)/'.$key.'$']);					
					unset($rewrite_rules[$key.'/?$']);
				}else{
					unset($rewrite_rules[$post->post_name.'$']);
					unset($rewrite_rules[$key.'/?$']);
				}
			}	
		}
	}	
	if(@$tevolution_taxonomies_data['tevolution_single_post_remove']){
		$flg=0;
		foreach($tevolution_taxonomies_data['tevolution_single_post_remove'] as $key=>$value){
			if($value!=""){				
				if(is_plugin_active('Tevolution-LocationManager/location-manager.php')){
					if($key =='event'){
						$querystr = "SELECT {$wpdb->posts}.post_name FROM {$wpdb->posts} WHERE ({$wpdb->posts}.post_status = 'publish' or {$wpdb->posts}.post_status = 'recurring') AND {$wpdb->posts}.post_type = '{$key}'";
					}else{
						$querystr = "SELECT {$wpdb->posts}.post_name FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_status = 'publish' AND {$wpdb->posts}.post_type = '{$key}'";
					}
					$posts = $wpdb->get_results($querystr, OBJECT);
					$location_post_type=implode(',',get_option('location_post_type'));
					
					if (strpos($location_post_type,','.$key) !== false) {
						$flg=1;
					}
					unset($rewrite_rules[$multi_city.'/([^/]+)/'.$key.'/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$']);
					unset($rewrite_rules[$multi_city.'/([^/]+)/'.$key.'/([^/]+)/(feed|rdf|rss|rss2|atom)/?$']);
					unset($rewrite_rules[$multi_city.'/([^/]+)/'.$key.'/([^/]+)/page/?([0-9]{1,})/?$']);
					unset($rewrite_rules[$multi_city.'/([^/]+)/'.$key.'/([^/]+)(/[0-9]+)?/?$']);					
					foreach ($posts as $post) {
						if($flg==1){
							$new_rules[$multi_city.'/([^/]+)/'.$post->post_name.'$'] = 'index.php?'.$multi_city.'=$matches[1]&'.$key.'='.$post->post_name;
						}else{							
							$new_rules[$post->post_name.'$'] = 'index.php?'.$key.'='.$post->post_name;
						}
					}	
				}else{					
					$querystr = "SELECT {$wpdb->posts}.post_name FROM {$wpdb->posts} WHERE ({$wpdb->posts}.post_status = 'publish' or {$wpdb->posts}.post_status = 'recurring') AND {$wpdb->posts}.post_type = '{$key}'";
					$posts = $wpdb->get_results($querystr, OBJECT);
					foreach ($posts as $post){						
						$new_rules[$post->post_name.'$'] = 'index.php?'.$key.'='.$post->post_name;
					}	
				}
			}
			if(!empty($new_rules)){ 
				$rewrite_rules =  array_merge($new_rules, $rewrite_rules);
			}else{
				$rewrite_rules =  $rewrite_rules;
			}
		}
	}
	/* Set the custom post type archive page rewrite rules */
	
	/* Finish */
	if(@$tevolution_taxonomies_data['tevolution_single_post_add']){
		foreach($tevolution_taxonomies_data['tevolution_single_post_add'] as $post_key=>$v){
			if($v && $tevolution_taxonomies_data['tevolution_single_post_remove'][$post_key]=='' && is_plugin_active('Tevolution-LocationManager/location-manager.php')){
				
				unset($rewrite_rules[$multi_city.'/([^/]+)/'.$post_key.'/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$']);
				unset($rewrite_rules[$multi_city.'/([^/]+)/'.$post_key.'/([^/]+)/(feed|rdf|rss|rss2|atom)/?$']);
				unset($rewrite_rules[$multi_city.'/([^/]+)/'.$post_key.'/([^/]+)/page/?([0-9]{1,})/?$']);
				unset($rewrite_rules[$multi_city.'/([^/]+)/'.$post_key.'/([^/]+)(/[0-9]+)?/?$']);
				$new_rules = array( $multi_city.'/([^/]+)/'.$v.'/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?'.$multi_city.'=$matches[1]&'.$post_key.'=$matches[2]&feed=$matches[3]',
								$multi_city.'/([^/]+)/'.$v.'/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?'.$multi_city.'=$matches[1]&'.$post_key.'=$matches[2]&feed=$matches[3]',
								$multi_city.'/([^/]+)/'.$v.'/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?'.$multi_city.'=$matches[1]&'.$post_key.'=$matches[2]&paged=$matches[3]',
								$multi_city.'/([^/]+)/'.$v.'/([^/]+)(/[0-9]+)?/?$' => 'index.php?'.$multi_city.'=$matches[1]&'.$post_key.'=$matches[2]&page=$matches[3]',								
							);
				$rewrite_rules = $new_rules + $rewrite_rules;
				
				$new_archive_rules[$multi_city.'/([^/]+)/'.$post_key.'/?$'] = 'index.php?city=$matches[1]&post_type='.$post_key;
				$new_archive_rules[$multi_city.'/([^/]+)/'.$post_key.'/feed/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?city=$matches[1]&post_type='.$post_key.'&feed=$matches[2]';
				$new_archive_rules[$multi_city.'/([^/]+)/'.$post_key.'/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?city=$matches[1]&post_type='.$post_key.'&feed=$matches[2]';
				$new_archive_rules[$multi_city.'/([^/]+)/'.$post_key.'/page/([0-9]{1,})/?$'] = 'index.php?city=$matches[1]&post_type='.$post_key.'&paged=$matches[2]';
				$rewrite_rules =  $new_archive_rules + $rewrite_rules;	
				
				
			}		
			if($post_key != $v){
				$new_rules = array($v.'/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?'.$post_key.'=$matches[1]&feed=$matches[2]',
							    $v.'/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?'.$post_key.'=$matches[1]&feed=$matches[2]',
							    $v.'/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?'.$post_key.'=$matches[1]&paged=$matches[2]',
							    $v.'/([^/]+)(/[0-9]+)?/?$' => 'index.php?'.$post_key.'=$matches[1]&page=$matches[2]',
							    $v.'/([^/]+)/comment-page-([0-9]{1,})/?$' => 'index.php?'.$post_key.'=$matches[1]&cpage=$matches[2]',
								);	
			}
			
			$new_rules[$post_key.'/?$'] = 'index.php?post_type='.$post_key;
			$new_rules[$post_key.'/feed/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?post_type='.$post_key.'&feed=$matches[1]';
			$new_rules[$post_key.'/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?post_type='.$post_key.'&feed=$matches[1]';
			$new_rules[$post_key.'/page/([0-9]{1,})/?$'] = 'index.php?post_type='.$post_key.'&paged=$matches[1]';
			$rewrite_rules = $new_rules + $rewrite_rules;
			
		}
	}else{		
		$templatic_custom_post=get_option('templatic_custom_post');		
		if(!empty($templatic_custom_post)){
		foreach($templatic_custom_post as $post_type=>$post_type_values){
				$new_archive_rules[$post_type.'/?$'] = 'index.php?post_type='.$post_type;
				$new_archive_rules[$post_type.'/feed/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?post_type='.$post_type.'&feed=$matches[1]';
				$new_archive_rules[$post_type.'/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?post_type='.$post_type.'&feed=$matches[1]';			
				$new_archive_rules[$post_type.'/page/([0-9]{1,})/?$'] = 'index.php?post_type='.$post_type.'&paged=$matches[1]';
				if(!empty($rewrite_rules)){
					$rewrite_rules =  $new_archive_rules + $rewrite_rules;	
				}else{
					$rewrite_rules =  $new_archive_rules;	
				}
		}
		}
	}
	
	
	$page_base='page';	
	if(isset($tevolution_taxonomies_data['tevolution_author'])){
		$tevolution_author = ($tevolution_taxonomies_data['tevolution_author']!='')? $tevolution_taxonomies_data['tevolution_author']:'author';
		
		$new_rules = array(
			$tevolution_author.'/([^/]+)/([0-9]{4})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]',
			$tevolution_author.'/([^/]+)/([0-9]{4})/'.$page_base.'/?([0-9]{1,})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&paged=$matches[3]',			
			$tevolution_author.'/([^/]+)/([0-9]{4})/([0-9]{2})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&monthnum=$matches[3]',
			$tevolution_author.'/([^/]+)/([0-9]{4})/([0-9]{2})/'.$page_base.'/?([0-9]{1,})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&monthnum=$matches[3]&paged=$matches[4]',			
			$tevolution_author.'/([^/]+)/([0-9]{4})/([0-9]{2})/([0-9]{2})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]',
			$tevolution_author.'/([^/]+)/([0-9]{4})/([0-9]{2})/([0-9]{2})/'.$page_base.'/?([0-9]{1,})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&paged=$matches[5]');				
		$new_rules[$tevolution_author.'/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?author_name=$matches[1]&feed=$matches[2]';
		$new_rules[$tevolution_author.'/([^/]+)/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?author_name=$matches[1]&feed=$matches[2]';
		$new_rules[$tevolution_author.'/([^/]+)/page/?([0-9]{1,})/?$'] = 'index.php?author_name=$matches[1]&paged=$matches[2]';
		$new_rules[$tevolution_author.'/([^/]+)/?$'] = 'index.php?author_name=$matches[1]';
		$rewrite_rules = $new_rules + $rewrite_rules;
	}
	
	
	
	/*
	 * Remove custom tevolution generate taxonomies slug in listing page
	 */
	 
	$tevolution_taxonomies=get_option('templatic_custom_taxonomy');
	if(!empty($tevolution_taxonomies)){
		foreach($tevolution_taxonomies as $key=>$value){
			$taxonomies[]=$key;
		}
	}
	$tevolution_taxonomies_tags=get_option('templatic_custom_tags');
	if(!empty($tevolution_taxonomies_tags)){
		foreach($tevolution_taxonomies_tags as $key=>$value){
			$taxonomies[]=$key;
			$taxonomies_tags[]=$key;
		}
	}
	if(@$tevolution_taxonomies_data['tevolution_taxonimies_remove']){		
		foreach (get_taxonomies('','objects') as $key=>$taxonomy){
			
			if(!in_array($key,$taxonomies)){
				continue;
			}			
			$terms=get_terms($taxonomy->name,array( 'hide_empty'    => false, ));			
			foreach($terms as $term){				
				$base=$tevolution_taxonomies_data['tevolution_taxonimies_remove'][$taxonomy->name]? "":$taxonomy->rewrite['slug']."/";				
				if($term->parent!=0){
					$ancestors=tevolution_term_ancestors($taxonomy->name,$term->parent)."/";
				}else{
					$ancestors="";
				}				

				/* some query vars differ from tax name */
				switch($taxonomy->name){
					case "post_tag":
						$tax_name="tag";
						break;
					case "category":
						$tax_name="category_name";
						break;
					default:
						$tax_name=$taxonomy->name;
				}				
				if(!$base){
					if(is_plugin_active('Tevolution-LocationManager/location-manager.php') && get_option('tev_lm_new_city_permalink') == 1){						
						$new_terms_rules[$multi_city.'/([^/]+)/'.$ancestors.'('.$term->slug.')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?'.$multi_city.'=$matches[1]&'.$tax_name.'=$matches[2]&feed=$matches[3]';
						$new_terms_rules[$multi_city.'/([^/]+)/'.$ancestors.'('.$term->slug.')/page/?([0-9]{1,})/?$'] = 'index.php?'.$multi_city.'=$matches[1]&'.$tax_name.'=$matches[2]&paged=$matches[3]';
						$new_terms_rules[$multi_city.'/([^/]+)/'.$ancestors.'('.$term->slug.')'.$suffix.'/?$'] = 'index.php?'.$multi_city.'=$matches[1]&'.$tax_name.'=$matches[2]';						
					}else{					
						$new_terms_rules[$ancestors.'('.$term->slug.')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?'.$tax_name.'=$matches[1]&feed=$matches[2]';
						$new_terms_rules[$ancestors.'('.$term->slug.')/page/?([0-9]{1,})/?$'] = 'index.php?'.$tax_name.'=$matches[1]&paged=$matches[2]';
						$new_terms_rules[$ancestors.'('.$term->slug.')'.$suffix.'/?$'] = 'index.php?'.$tax_name.'=$matches[1]';
					}				
				}else{
					if(in_array($tax_name,$taxonomies_tags)){
						$new_terms_rules[$tax_name.'/([^/]+)/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?'.$tax_name.'=$matches[1]&feed=$matches[2]';
						$new_terms_rules[$tax_name.'/([^/]+)/page/?([0-9]{1,})/?$'] = 'index.php?'.$tax_name.'=$matches[1]&paged=$matches[2]';
						$new_terms_rules[$tax_name.'/([^/]+)/?$'] = 'index.php?'.$tax_name.'=$matches[1]';
					}else{
						$new_terms_rules[$tax_name.'/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?'.$tax_name.'=$matches[1]&feed=$matches[2]';
						$new_terms_rules[$tax_name.'/(.+?)/page/?([0-9]{1,})/?$'] = 'index.php?'.$tax_name.'=$matches[1]&paged=$matches[2]';
						$new_terms_rules[$tax_name.'/(.+?)/?$'] = 'index.php?'.$tax_name.'=$matches[1]';
					}					
				}
				$rewrite_rules =  $new_terms_rules + $rewrite_rules;				
			}
		}
	}	
	
	if(isset($tevolution_taxonomies_data['tevolution_author']) && $tevolution_taxonomies_data['tevolution_author']!=''){
		$rewrite_rules=str_replace('author/',$tevolution_taxonomies_data['tevolution_author'].'/',$rewrite_rules);		
	}
	
	return $rewrite_rules;
}

function remove_tevolution_taxonomies_from_rewrite_rules($rules){
	return array();
}


/*
 * Function Name:filter_tevolution_taxonomies_table_actions
 * change backend view taxonomy view link
 */
function filter_tevolution_taxonomies_table_actions( $actions, $tag){
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php')){
		global $current_cityinfo,$wpdb;
		$multicity_table = $wpdb->prefix . "multicity";
		$sql=$wpdb->prepare("SELECT * FROM $multicity_table where is_default=%d",1);
		$default_city = $wpdb->get_results($sql);
		$city_slug=get_option('location_multicity_slug');	
		$multi_city=($city_slug)? $city_slug : 'city';
		$city=(isset($current_cityinfo['city_slug']) && $current_cityinfo['city_slug']!='')? $current_cityinfo['city_slug'] :$default_city[0]->city_slug;		
		
		if($tag->parent!=0){
			$ancestors=tevolution_term_ancestors($tag->taxonomy,$tag->parent)."/";
		}else{
			$ancestors="";
		}

		if(get_option('tev_lm_new_city_permalink') == 1)
			$actions['view']='<a href="'.get_bloginfo('url')."/".$multi_city."/".$city."/".$ancestors.$tag->slug.'">View</a>';
		else
			$actions['view']='<a href="'.get_bloginfo('url')."/".$ancestors.$tag->slug.'">View</a>';
	}else{	
		$actions['view']='<a href="'.get_bloginfo('url').'/'.$ancestors.$tag->slug.'">View</a>';
	}
	return $actions;
}

function tevolution_term_ancestors($tax,$id){
	$term=get_term($id,$tax);
	$ancestor=$term->slug;
	
	if($term->parent!=0){
		$ancestor=tevolution_term_ancestors($tax,$term->parent)."/".$ancestor;
	}
	return $ancestor;
}


function tevolution_author_filter_link($link){
	$tevolution_taxonomies_data=get_option('tevolution_taxonomies_rules_data');	
	if($tevolution_taxonomies_data['tevolution_remove_author_base']){
		$link=str_replace("author/","",$link);
	}
	elseif(isset($tevolution_taxonomies_data['tevolution_author']) && $tevolution_taxonomies_data['tevolution_author']!=''){
		$link=str_replace("author",urlencode($tevolution_taxonomies_data['tevolution_author']),$link);
	}
	return $link;
}


/*
 * Function Name: tevolution_custom_term_link
 * Change term link action as per set tevolution permalink
 */
function tevolution_custom_term_link($termlink, $term, $taxonomy){	
	$tevolution_taxonomies_data=get_option('tevolution_taxonomies_rules_data');
	if(isset($tevolution_taxonomies_data['tevolution_taxonimies_remove'][$taxonomy])){
		$txs=get_taxonomies(array('name'=>$taxonomy),"object");
		foreach($txs as $t){
			if($term->parent!=0){
				$ancestors=tevolution_term_ancestors($term->taxonomy,$term->parent)."/";
			}else{
				$ancestors="";
			}

			if(is_plugin_active('Tevolution-LocationManager/location-manager.php')){
				global $current_cityinfo,$wpdb;
				$multicity_table = $wpdb->prefix . "multicity";
				$sql=$wpdb->prepare("SELECT * FROM $multicity_table where is_default=%d",1);
				$default_city = $wpdb->get_results($sql);
				$city_slug=get_option('location_multicity_slug');	
				$multi_city=($city_slug)? $city_slug : 'city';
				$city=(isset($current_cityinfo['city_slug']) && $current_cityinfo['city_slug']!='')? $current_cityinfo['city_slug'] : $default_city[0]->city_slug;				
				if(get_option('tev_lm_new_city_permalink') == 1)
					return get_bloginfo('url')."/".$multi_city."/".$city."/".$ancestors.$term->slug;	
				else
					return get_bloginfo('url')."/".$ancestors.$term->slug;	
			}else{			
				return get_bloginfo('url')."/".$ancestors.$term->slug;			
			}
		}
	}else{		
		global $current_cityinfo,$wpdb;
		if(is_admin()){
				$multicity_table = $wpdb->prefix . "multicity";
				$sql=$wpdb->prepare("SELECT * FROM $multicity_table where is_default=%d",1);
				$default_city = $wpdb->get_results($sql);
				$city_slug=get_option('location_multicity_slug');	
				$multi_city=($city_slug)? $city_slug : 'city';
				$city=(isset($current_cityinfo['city_slug']) && $current_cityinfo['city_slug']!='')? $current_cityinfo['city_slug'] : $default_city[0]->city_slug;
		}else{
			if(isset($current_cityinfo['city_slug']) && $current_cityinfo['city_slug']!=''){
				$city=$current_cityinfo['city_slug'];
			}else{
				$city='na';
			}
		}
		$termlink = str_replace(array('%city%'), array($city), $termlink);
		return $termlink;
	}	
	return $termlink;
}

/*
 * Function Name: filter_tevolution_taxonomies_table_actions_original
 * Return: repalce backed category action when location manager plugin activate
 */
function filter_tevolution_taxonomies_table_actions_original($actions, $tag){	
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php')){
		$tevolution_taxonomies_data=get_option('tevolution_taxonomies_rules_data');		
		global $current_cityinfo,$wpdb;
		$multicity_table = $wpdb->prefix . "multicity";
		$sql=$wpdb->prepare("SELECT * FROM $multicity_table where is_default=%d",1);
		$default_city = $wpdb->get_results($sql);
		$city_slug=get_option('location_multicity_slug');	
		$multi_city=($city_slug)? $city_slug : 'city';
		$taxonomy_slug=(@$tevolution_taxonomies_data['tevolution_taxonimies_add'][$tag->taxonomy]!="")?$tevolution_taxonomies_data['tevolution_taxonimies_add'][$tag->taxonomy] : $tag->taxonomy;
		$city=(isset($current_cityinfo['city_slug']) && $current_cityinfo['city_slug']!='')? $current_cityinfo['city_slug'] :$default_city[0]->city_slug;
		$actions['view']='<a href="'.get_bloginfo('url')."/".$multi_city."/".$city."/".$taxonomy_slug."/".$tag->slug.'">View</a>';
	}
	return $actions;
}

/*
 * Function Name: tevolution_remove_author_base_from_rewrite_rules
 * Remove author slug rewite rules
 */
function tevolution_remove_author_base_from_rewrite_rules($author_rewrite){
	$tevolution_taxonomies_data=get_option('tevolution_taxonomies_rules_data');
	if(@$tevolution_taxonomies_data['tevolution_remove_author_base']){		
		global $wpdb;
	    $author_rewrite = array();
	    $authors = $wpdb->get_results("SELECT user_nicename AS nicename from $wpdb->users");   
	    foreach($authors as $author) {
		   $author_rewrite["({$author->nicename})/page/?([0-9]+)/?$"] = 'index.php?author_name=$matches[1]&paged=$matches[2]';
		   $author_rewrite["({$author->nicename})/?$"] = 'index.php?author_name=$matches[1]';
	    }  
	}	
	return $author_rewrite;
}


/*
 * Function Name:tevolution_rewrite_rules_function
 * all action and filter call for permalink related
 */
add_action('init','tevolution_rewrite_rules_function');
function tevolution_rewrite_rules_function(){
	$tevolution_taxonomies_data=get_option('tevolution_taxonomies_rules_data');
	add_action('flush_event','tevolution_taxonimies_flush_event');	 
	add_filter('rewrite_rules_array','tevolution_taxonimies_filter_rewrite_rules');	
	flush_rewrite_rules(true);
	add_filter('term_link','tevolution_custom_term_link',10,3);
	remove_filter('term_link','templatic_create_term_permalinks',10,3);
	if(@$tevolution_taxonomies_data['tevolution_taxonimies_remove']){		
		$tevolution_taxonomies=get_option('templatic_custom_taxonomy');
		if(!empty($tevolution_taxonomies)){
				foreach($tevolution_taxonomies as $key=>$value){
					$taxonomies_key[]=$key;
				}
			}
		$tevolution_taxonomies_tags=get_option('templatic_custom_tags');
		if(!empty($tevolution_taxonomies_tags)){
			foreach($tevolution_taxonomies_tags as $key=>$value){
				$tags_key[]=$key;
			}
		}
		
		$tevolution_taxonomies=array_merge($taxonomies_key,$tags_key);
		foreach($tevolution_taxonomies as $key){
			if(!$tevolution_taxonomies_data['tevolution_taxonimies_remove'][$key]){
				add_filter($key."_row_actions",'filter_tevolution_taxonomies_table_actions_original', 10,2 );	
			}
		}
		foreach($tevolution_taxonomies_data['tevolution_taxonimies_remove'] as $tax=>$v){			
			if($v) {
				add_filter($tax.'_rewrite_rules','remove_tevolution_taxonomies_from_rewrite_rules');
				add_filter($tax."_row_actions",'filter_tevolution_taxonomies_table_actions', 10,2 );
			}
		}
	}
	if(@$tevolution_taxonomies_data['tevolution_taxonimies_remove']){
		add_action('created_term','tevolution_taxonimies_refresh_rewrite_rules_later');
		add_action('edited_term','tevolution_taxonimies_refresh_rewrite_rules_later');
		add_action('delete_term','tevolution_taxonimies_refresh_rewrite_rules_later');
	}
	
	if($tevolution_taxonomies_data['tevolution_author'] || $tevolution_taxonomies_data['tevolution_remove_author_base']){
		add_filter('author_link','tevolution_author_filter_link');
		add_filter('author_rewrite_rules', 'tevolution_remove_author_base_from_rewrite_rules',20);
	}
}

function change_taxonomies_rewrite_rules($taxo_slug,$change_slug){

	$tevolution_taxonomies=get_option('templatic_custom_taxonomy');
}


add_filter('post_type_link', 'tevolution_remove_custom_post_permalinks', 10, 3);
function tevolution_remove_custom_post_permalinks($permalink, $post, $leavename){	
	$tevolution_taxonomies_data=get_option('tevolution_taxonomies_rules_data');
	if($tevolution_taxonomies_data['tevolution_single_post_remove']){
		foreach($tevolution_taxonomies_data['tevolution_single_post_remove'] as $post_key=>$v){			
			if($v && $post_key==$post->post_type) {
				$permalink = str_replace( '/' . $post->post_type . '/', '/', $permalink );
			}
		}
	}
	if($tevolution_taxonomies_data['tevolution_single_post_add'] !=''){
		foreach($tevolution_taxonomies_data['tevolution_single_post_add'] as $post_key=>$v){			
			if($v!='' && $post_key==$post->post_type) {				
				$permalink = str_replace( '/' . $post->post_type . '/', '/'.$v.'/', $permalink );
			}
		}
	}
	return $permalink;
} 


/*
 * Function Name: tevolution_create_archive_permalinks
 * Return : archive post type link
 */
add_filter('post_type_archive_link','tevolution_create_archive_permalinks',11,2);
function tevolution_create_archive_permalinks( $link, $post_type){
	global $current_cityinfo,$wpdb;
	
	$multicity_table = $wpdb->prefix . "multicity";
	$tevolution_taxonomies_data=get_option('tevolution_taxonomies_rules_data');	
	$location_post_type=implode(',',get_option('location_post_type'));
	if (strpos($location_post_type,','.$post_type) !== false && $tevolution_taxonomies_data['tevolution_single_post_add'][$post_type]!=$post_type) 
	{
		$post_type=($tevolution_taxonomies_data['tevolution_single_post_add'][$post_type])? $tevolution_taxonomies_data['tevolution_single_post_add'][$post_type] : $post_type;
		$city_slug=get_option('location_multicity_slug');
		$multi_city=($city_slug)? $city_slug : 'city';
		$sql=$wpdb->prepare("SELECT * FROM $multicity_table where is_default=%d",1);
		$default_city = $wpdb->get_results($sql);
		$city=(isset($current_cityinfo['city_slug']) && $current_cityinfo['city_slug']!='')? $current_cityinfo['city_slug'] :$default_city[0]->city_slug;
		$link=get_bloginfo('url')."/".$multi_city."/".$city."/".$post_type;	
	}elseif($tevolution_taxonomies_data['tevolution_single_post_add'][$post_type]!=$post_type){
		$post_type=$tevolution_taxonomies_data['tevolution_single_post_add'][$post_type];		
		$link=get_bloginfo('url')."/".$post_type;	
	}
	
	return $link;
}
?>