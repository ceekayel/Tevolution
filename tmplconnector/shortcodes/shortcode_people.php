<?php
/* get the authors count */
function tevolution_custom_count_authors($args = '',$params = array(),$role,$display_author_type=0) {
	global $wpdb,$posts_per_page, $paged,$post;
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	if($paged<=0)
	{
		$paged = 1;
	}
	if($params['pagination'])
	{
		$paged = 1;
	}
	if(@$args['users_per_page'])
	{
		$posts_per_page = $args['users_per_page'];
	}
	$startlimit = ($paged-1)*$posts_per_page;
	$endlimit = $paged*$posts_per_page;
	$defaults = array(
		'optioncount' => false, 'exclude_admin' => true,
		'show_fullname' => false, 'hide_empty' => true,
		'feed' => '', 'feed_image' => '', 'feed_type' => '', 'echo' => true,
		'style' => 'list', 'html' => true
	);
	$r = wp_parse_args( $args, $defaults );
	extract($r, EXTR_SKIP);
	$return = '';
	
	global $table_prefix, $wpdb;
	$capabilities = "wp_capabilities";
	$capabilities2 = $table_prefix."capabilities";
	$sdub_sql = "select user_id from $wpdb->usermeta  where (meta_key like \"$capabilities2\" and meta_value like \"%".$role."%\")"; /* this query will show all agents with 0 property*/
	/* $sdub_sql = "SELECT  $wpdb->users.ID FROM $wpdb->users,$wpdb->usermeta,$wpdb->posts where $wpdb->users.ID=$wpdb->usermeta.user_id and
                         $wpdb->users.ID= $wpdb->posts.post_author and ($wpdb->usermeta.meta_key LIKE '%".$capabilities2."%' AND $wpdb->usermeta.meta_value LIKE '%".$role."%')"; /* this query will except all agents with 0 property */
                         
                         /* show all author or only has the post author */     
                         if($display_author_type ==1){	
                                        $sql = "select count(DISTINCT u.ID) from $wpdb->users u where u.ID in ($sdub_sql)";
                              }else{
                                        $sql = "select count(DISTINCT u.ID) from $wpdb->users u ,$wpdb->posts p where u.ID = p.post_author and u.ID in ($sdub_sql) and p.post_status in ('publish') ";
                              }
                              
	if($params['sort']=='alpha')
	{
		if($_REQUEST['kw'])
		{
			$kw = $_REQUEST['kw'];
			$sql .= " and u.display_name like \"$kw%\" ";	
		}
	}
	if($params['sort']=='most' && $display_author_type ==0)
	{
		$sql .= " ORDER BY (select count(p.ID) from $wpdb->posts p where u.ID=p.post_author and p.post_status='publish') desc ";	
	}
	else
	{
		$sql .= " ORDER BY display_name ";	
	}
	$authors = $wpdb->get_var($sql);
	if($authors)
	{
		return $authors;
	}
	return $return_arr;
}
/* get the list of authors */
function tevolution_custom_list_authors($args = '',$params = array()) {
	global $wpdb,$posts_per_page, $paged,$post;
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	if($paged<=0)
	{
		$paged = 1;
	}
	if($params['pagination'])
	{
		$paged = 1;
	}
	if($args['users_per_page'])
	{
		$posts_per_page = $args['users_per_page'];
	}
	$startlimit = ($paged-1)*$posts_per_page;
	$endlimit = $paged*$posts_per_page;
	$defaults = array(
		'optioncount' => false, 'exclude_admin' => true,
		'show_fullname' => false, 'hide_empty' => true,
		'feed' => '', 'feed_image' => '', 'feed_type' => '', 'echo' => true,
		'style' => 'list', 'html' => true
	);
	$r = wp_parse_args( $args, $defaults );
	extract($r, EXTR_SKIP);
	$return = '';
	
	global $table_prefix, $wpdb;
	$capabilities = "wp_capabilities";
	$capabilities2 = $table_prefix."capabilities";
	$role = $args['role'];
	$sdub_sql = "select user_id from $wpdb->usermeta  where (meta_key like \"$capabilities2\" and meta_value like \"%".$role."%\")"; /* this query will show all agents with 0 property*/
	/* $sdub_sql = "SELECT  $wpdb->users.ID FROM $wpdb->users,$wpdb->usermeta,$wpdb->posts where $wpdb->users.ID=$wpdb->usermeta.user_id and
    $wpdb->users.ID= $wpdb->posts.post_author and ($wpdb->usermeta.meta_key LIKE '%".$capabilities2."%' AND $wpdb->usermeta.meta_value LIKE '%".$role."%')"; /* this query will except all agents with 0 property */
          
          /* get all the author or get only author who has the posts*/
          if($args['display_author_has_no_post'] == 1){
                    $sql = "select u.* from $wpdb->users u where u.ID in ($sdub_sql)";
          }else{	
                    $sql = "select u.* from $wpdb->users u ,$wpdb->posts p where u.ID = p.post_author and u.ID in ($sdub_sql) and p.post_status in ('publish') ";
          }
          
          if($params['sort']=='alpha')
	{
		if($_REQUEST['kw'])
		{
			$kw = $_REQUEST['kw'];
			$sql .= " and u.display_name like \"$kw%\" ";	
		}
	}
	$sql .= " group by u.ID ";
	if($params['sort']=='most' && $display_author_type ==0)
	{
		$sql .= " ORDER BY (select count(p.ID) from $wpdb->posts p where u.ID=p.post_author and p.post_status='publish') desc ";	
	}
	else
	{
		$sql .= " ORDER BY display_name ";	
	}
	$sql .= " limit $startlimit,$posts_per_page";
	$authors = $wpdb->get_results($sql);
	$return_arr = array();
	foreach ( (array) $authors as $author ) 
	{
		$return_arr[] = get_userdata( $author->ID );
	}	
	return $return_arr;
}
/* get the post count of authors */
function tevolution_custom_list_authors_count($args = '',$params = array(),$role) {
	global $wpdb;
	$defaults = array(
		'optioncount' => false, 'exclude_admin' => true,
		'show_fullname' => false, 'hide_empty' => true,
		'feed' => '', 'feed_image' => '', 'feed_type' => '', 'echo' => true,
		'style' => 'list', 'html' => true
	);
	$r = wp_parse_args( $args, $defaults );
	extract($r, EXTR_SKIP);
	global $table_prefix, $wpdb;
	$capabilities = "wp_capabilities";
	$capabilities2 = $table_prefix."capabilities";
	$sub_sql = "select user_id from $wpdb->usermeta where (meta_key like \"$capabilities2\" and meta_value like \"%".$role."%\")";
	$sql = "select count(u.ID) from $wpdb->users u where u.ID in ($sub_sql) ";
	if($params['sort']=='alpha')
	{
		if($_REQUEST['kw'])
		{
			$kw = $_REQUEST['kw'];
			$sql .= " and u.display_name like \"$kw%\" ";	
		}
	}

	$authors = $wpdb->get_var($sql);
	if($authors)
	{
		return $authors;
	}else
	{
		return '1';
	}
}
/*
Name : tevolution_get_posts_count
desc : Count the total number of the posts submited by user
args : user id , post status
*/
function tevolution_get_posts_count($userid,$post_status='publish')
{
	global $wpdb;
	if($userid)
	{
		/* get all post types which are created with tevoluion plugin + concate post because we provide to enable post too.*/
		$post_types = get_option('templatic_custom_post');
		$post_types = str_replace(",","','",implode(',',array_keys($post_types)).",post");
		$srch_sql = "select count(p.ID) as post_count from $wpdb->posts p where p.post_type IN ('$post_types') and  p.post_author=$userid ";
		if($post_status=='all')
		{
			$srch_sql .= " and p.post_status in ('publish','draft')";
		}else
		if($post_status=='publish')
		{
			$srch_sql .= " and p.post_status in ('publish')";
		}
		else
		if($post_status=='draft')
		{
			$srch_sql .= " and p.post_status in ('draft')";
		}
		$totalpost_count = $wpdb->get_var($srch_sql);	
		return $totalpost_count;
	}
}
/*
Name : tevolution_author_list_fun
desc : Shortcode function to display the list of peoples
args : attributes
*/
function tevolution_author_list_fun($atts){
		global $post;
		ob_start();
		extract( shortcode_atts( array (
			'post_type'   =>'post',				
			), $atts ) 
		);	
		
		remove_filter( 'the_content', 'wpautop' , 12);
		if($_REQUEST['sort']=='')
		{
			$_REQUEST['sort'] = 'all';	
		}
		if($_REQUEST['sort']=='alpha'){
		$kw = $_REQUEST['kw'];
		if($kw==''){$kw = 'all';}
		}		

		$arrpeoples= tevolution_custom_list_authors(array('role'=>$atts['role'],'users_per_page'=>$atts['users_per_page'],'display_author_has_no_post' => $atts['display_author_has_no_post']),array('kw'=>$kw,'sort'=>$_REQUEST['sort']));
		$page_url = get_permalink($post->ID);
		
		?>
		<ul class="tabs">
			<li class="normal"><?php _e('Sort By','templatic'); ?></li>
			<li class="tab-title <?php if($_REQUEST['sort']=='all'){	echo 'active'; }?>"><a href="<?php echo $page_url;?>?sort=all" > <?php _e('All','templatic'); ?> </a></li>
			<li class="tab-title <?php if($_REQUEST['sort']=='alpha'){	echo 'active';	}?>"><a href="<?php echo $site_url;?>?sort=alpha"> <?php _e('Alphabetical','templatic'); ?></a></li>
			<li class="tab-title <?php if($_REQUEST['sort']=='most'){	echo 'active';	}?>"><a href="<?php echo $site_url;?>?sort=most"> <?php _e('Most Submitted','templatic'); ?></a></li>
		</ul>
			
		<?php if($_REQUEST['sort']=='alpha'){
			$alpha = array(__('All','templatic'),__('A','templatic'),__('B','templatic'),__('C','templatic'),__('D','templatic'),__('E','templatic'),__('F','templatic'),__('G','templatic'),__('H','templatic'),__('I','templatic'),__('J','templatic'),__('K','templatic'),__('L','templatic'),__('M','templatic'),__('N','templatic'),__('O','templatic'),__('P','templatic'),__('Q','templatic'),__('R','templatic'),__('S','templatic'),__('T','templatic'),__('U','templatic'),__('V','templatic'),__('W','templatic'),__('X','templatic'),__('Y','templatic'),__('Z','templatic'));
			?>
			<div class="sort_order_alphabetical">
				<ul class="alphabetical">
				<?php foreach($alpha as $akey => $avalue) { 
					if($akey =='All'){ ?>
					<li <?php if($kw == strtolower($avalue)){ echo 'class="nav-author-post-tab-active active"';}?>><a href="<?php echo $page_url;?>?sort=alpha&amp;"><?php echo $avalue; ?></a></li>
				<?php
					}else{ 
				?>
					<li <?php if($kw == strtolower($avalue)){ echo 'class="nav-author-post-tab-active"';}?>><a href="<?php echo $page_url;?>?sort=alpha&amp;kw=<?php echo strtolower($avalue); ?>"><?php echo $avalue; ?></a></li><?php } 
					}?>
				</ul>
			</div>
		<?php }?>
		    
		<ul class="peoplelisting">
			<?php 
			if($_REQUEST['sort']=='alpha'){
				$kw = $_REQUEST['kw'];
				if($kw==''){$kw = 'a';}
			}
			
			$totalpost_count = tevolution_custom_list_authors_count('',array('kw'=>$kw,'sort'=>$_REQUEST['sort']),$atts['role']);
			if(count($arrpeoples)>0)
			{
			foreach($arrpeoples as $key => $value)
				{

				 $userDetail=get_user_meta( $value->ID,'user_address_info'); ?>
					 <li> 
					 <?php /* Author photo with link */
					 if(get_user_meta($value->ID,'profile_photo',true) != ""){ ?>
					<a href="<?php echo get_author_posts_url($value->ID);?>">
					<?php	echo '<img src="'.get_user_meta($value->ID,'profile_photo',true).'" alt="'.$value->display_name.'" title="'.$value->display_name.'" width="150" height="150"/>'; ?>
					</a>
					<?php
					}else{ ?>	
						<a href="<?php echo get_author_posts_url($value->ID);?>"><?php
						echo get_avatar($value->user_email, apply_filters('tev_people_photo_size',150) ); ?>
						</a><?php
					}
					
							
					$value->user_url=($value->user_url)? $value->user_url : $value->url;
					?> 
					   <div class="people_info">    
						 <h3><span class="fl"> 
							<a href="<?php echo get_author_posts_url($value->ID);?>"><?php echo $value->display_name; ?></a> 
							</span>
							<span class="total_homes"> 
								<a href="<?php echo get_author_posts_url($value->ID);?>">
							<?php 
							$all_published_entry = tevolution_get_posts_count($value->ID,'publish');
				   
							
							
							if($all_published_entry  != 0 && $all_published_entry != 1){  
								 echo " ".$all_published_entry." "; 
								_e('Listings','templatic'); 
							}elseif($all_published_entry == 1){
								echo " ".$all_published_entry." "; 
								_e('Listing','templatic'); 
							}else{
								_e('No listings','templatic');
							}?>
							</a></span></h3>
						 <p class="peoplelink" >
						 <?php if($value->user_url){ ?>
						  <span class="website"><a href="<?php echo $value->user_url; ?>"><?php _e('Visit Website','templatic'); ?></a></span> 
						  <?php } ?>
						 <?php if($value ->facebook){ ?>
						  <span class="facebook"><a href="<?php echo $value->facebook; ?>"><?php _e('Facebook','templatic'); ?></a></span> 
						  <?php } ?>
						  
						  <?php if($value ->twitter){ ?>
						  <span class="twitter"><a href="<?php echo $value->twitter; ?>"><?php _e('Twitter','templatic'); ?></a></span> 
						  <?php } ?>
						  
						  <?php if($value ->linkedin){ ?>
						  <span class="linkedin"><a href="<?php echo $value->linkedin; ?>"><?php _e('LinkedIn','templatic'); ?></a></span> 
						  <?php } ?>
						  </p>
						 <p><?php echo substr(strip_tags($value->user_description),0,250); ?> </p>						
							<p class="links"><span class="email"><a href="<?php echo antispambot("mailto:".$value->user_email);?>" class="i_email_agent"><?php _e('Email Me','templatic'); ?></a></span>
							<?php if($value->user_phone){ ?>
								<span class="phone"><?php echo $value->user_phone; ?></span> 
							<?php } ?>
							<span class="fr profile" ><a href="<?php echo get_author_posts_url($value->ID);?>"  class="" ><?php _e('View Profile','templatic'); ?> &raquo;</a></span> </p>
						</div>
					</li>                   
					<?php } ?>
			<?php
			}else
			{
			?>
			<p class="ac"><?php _e('This page is most likely empty now. It will be populated automatically once people add posts on the site.','templatic');?><b><?php echo strtoupper($kw);?>.</b></p>
			<?php
			}
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			if(isset($_REQUEST['sort']) && $_REQUEST['sort']=='alpha'){
				$kw = $_REQUEST['kw'];
				if($kw==''){$kw = 'a';}
				
				$total_authors = tevolution_custom_count_authors('',array('kw'=>$kw,'sort'=>$_REQUEST['sort']),$atts['role'], $atts['display_author_has_no_post']);
			}
			else
			{
				$totalpost_count = tevolution_custom_count_authors('',array('kw'=>$kw,'sort'=>$_REQUEST['sort']),$atts['role'], $atts['display_author_has_no_post']);

				$total_authors = $totalpost_count;
			}
			if($atts['users_per_page'])
			{
				$posts_per_page = $atts['users_per_page'];
			}
			else
			{
				$posts_per_page = get_option('posts_per_page');
			}
			
			/* Calculate the total number of pages for the pagination*/
			$total_pages = ceil($total_authors / $posts_per_page);
			
			?>	              
        </ul>
            <!-- Pagination -->
			<?php if($total_pages > 1 )
			{?>
			<div id="listpagi">
              <div class="pagination pagination-position">
			 <?php
				$pagenavi_options = array();
			   /* $pagenavi_options['pages_text'] = ('Page %CURRENT_PAGE% of %TOTAL_PAGES%:');*/
				$pagenavi_options['current_text'] = '%PAGE_NUMBER%';
				$pagenavi_options['page_text'] = '%PAGE_NUMBER%';
				$pagenavi_options['first_text'] = __('First Page','templatic');
				$pagenavi_options['last_text'] = __('Last Page','templatic');
				$pagenavi_options['next_text'] = '<strong class="page-numbers">'.__('NEXT','templatic').'</strong>';
				$pagenavi_options['prev_text'] = '<strong class="page-numbers">'.__('PREV','templatic').'</strong>';
				$pagenavi_options['dotright_text'] = '...';
				$pagenavi_options['dotleft_text'] = '...';
				$pagenavi_options['num_pages'] = 5; /*continuous block of page numbers*/
				$pagenavi_options['always_show'] = 0;
				$pagenavi_options['num_larger_page_numbers'] = 0;
				$pagenavi_options['larger_page_numbers_multiple'] = 5;
			 
			 ?>
				<?php if ($paged != 1) { ?>
					<a class="page-numbers" rel="prev" href="<?php the_permalink() ?>page/<?php echo $paged - 1; ?>/"><strong><?php _e('Prev','templatic'); ?></strong></a>
				<?php } ?>
				<?php
					for($i = ($offset+1); $i  <= $total_pages; $i++) {
						if($i == $paged) {
							$current_page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['current_text']);
							echo '<a  class="current page-numbers">'.$current_page_text.'</a>';
						} else {
							$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
							echo '<a href="'.esc_url(get_pagenum_link($i)).'" class="page-numbers" title="'.$page_text.'"><strong>'.$page_text.'</strong></a>';
						}
					}
				?>
				<?php if ($paged < $total_pages ) { ?>
					<a rel="next" class="page-numbers" href="<?php the_permalink() ?>page/<?php echo $paged + 1; ?>/"><strong><?php _e('Next','templatic'); ?></strong> </a>
				<?php } ?>
	
			 </div>
			</div>
		<?php
		}
		return ob_get_clean();
}
?>