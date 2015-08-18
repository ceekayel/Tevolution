<?php
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
	$sdub_sql = "select user_id from $wpdb->usermeta  where (meta_key like \"$capabilities2\" and meta_value like \"%".$role."%\")"; // this query will show all agents with 0 property
	/* $sdub_sql = "SELECT  $wpdb->users.ID FROM $wpdb->users,$wpdb->usermeta,$wpdb->posts where $wpdb->users.ID=$wpdb->usermeta.user_id and
    $wpdb->users.ID= $wpdb->posts.post_author and ($wpdb->usermeta.meta_key LIKE '%".$capabilities2."%' AND $wpdb->usermeta.meta_value LIKE '%".$role."%')"; // this query will except all agents with 0 property */
	$sql = "select u.* from $wpdb->users u where u.ID in ($sdub_sql) ";
	if($params['sort']=='alpha')
	{
		if($_REQUEST['kw'])
		{
			$kw = $_REQUEST['kw'];
			$sql .= " and u.display_name like \"$kw%\" ";	
		}
	}
	if($params['sort']=='most')
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
	
		$post_types = get_option('templatic_custom_post');
		$post_types = implode(',',array_keys($post_types));
		$srch_sql = "select count(p.ID) from $wpdb->posts p where p.post_type IN ('$post_types') and  p.post_author=$userid";
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
		if($kw==''){$kw = 'a';}
		}
		
		$arrpeoples= tevolution_custom_list_authors(array('role'=>$atts['role'],'users_per_page'=>$atts['users_per_page']),array('kw'=>$kw,'sort'=>$_REQUEST['sort']));
		$page_url = get_permalink($post->ID);
		
		?>
			<div class="author_post_tabs">
				<div class="author_custom_post_wrapper">
				 <ul class="tab">
					<li class="normal"><?php _e('Sort By',DOMAIN); ?></li>
					<li ><a href="<?php echo $page_url;?>?sort=all" <?php if($_REQUEST['sort']=='all'){echo 'class="nav-author-post-tab-active"';}?>> <?php _e('All',DOMAIN); ?> </a></li>
					<li ><a href="<?php echo $site_url;?>?sort=alpha" <?php if($_REQUEST['sort']=='alpha'){echo 'class="nav-author-post-tab-active"';}?>> <?php _e('Alphabetical',DOMAIN); ?></a></li>
					<li ><a href="<?php echo $site_url;?>?sort=most" <?php if($_REQUEST['sort']=='most'){echo 'class="nav-author-post-tab-active"';}?>> <?php _e('Most Submited',DOMAIN); ?></a></li>
				 </ul>
				</div>
			</div>
            <?php if($_REQUEST['sort']=='alpha'){
			$alpha = array(__('All',DOMAIN),__('A',DOMAIN),__('B',DOMAIN),__('C',DOMAIN),__('D',DOMAIN),__('E',DOMAIN),__('F',DOMAIN),__('G',DOMAIN),__('H',DOMAIN),__('I',DOMAIN),__('J',DOMAIN),__('K',DOMAIN),__('L',DOMAIN),__('M',DOMAIN),__('N',DOMAIN),__('O',DOMAIN),__('P',DOMAIN),__('Q',DOMAIN),__('R',DOMAIN),__('S',DOMAIN),__('T',DOMAIN),__('U',DOMAIN),__('V',DOMAIN),__('W',DOMAIN),__('X',DOMAIN),__('Y',DOMAIN),__('Z',DOMAIN));
			?>
			<div class="sort_order_alphabetical">
				<ul class="alphabetical">
				<?php foreach($alpha as $akey => $avalue) { 
					if($akey =='All'){ ?>
					<li <?php if($kw == strtolower($avalue)){ echo 'class="nav-author-post-tab-active active"';}?>><a href="<?php echo $page_url;?>?sort=alpha&amp;"><?php echo $avalue; ?></a></li>
				<?php	}else{ 
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
				}else{
					echo get_avatar($value->user_email, apply_filters('tev_people_photo_size',150) ); 
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
							_e('Listings',DOMAIN); 
						}elseif($all_published_entry == 1){
							echo " ".$all_published_entry." "; 
							_e('Listing',DOMAIN); 
						}else{
							_e('No listings',DOMAIN);
						}?>
						</a></span></h3>
                     <p class="peoplelink" >
                     <?php if($value->user_url){ ?>
                      <span class="website"><a href="<?php echo $value->user_url; ?>"><?php _e('Visit Website',DOMAIN); ?></a></span> 
                      <?php } ?>
                     <?php if($value ->facebook){ ?>
                      <span class="facebook"><a href="<?php echo $value->facebook; ?>"><?php _e('Facebook',DOMAIN); ?></a></span> 
                      <?php } ?>
					  
					  <?php if($value ->twitter){ ?>
                      <span class="twitter"><a href="<?php echo $value->twitter; ?>"><?php _e('Twitter',DOMAIN); ?></a></span> 
                      <?php } ?>
					  
					  <?php if($value ->linkedin){ ?>
                      <span class="linkedin"><a href="<?php echo $value->linkedin; ?>"><?php _e('Linkedin',DOMAIN); ?></a></span> 
                      <?php } ?>
                      </p>
                     <p><?php echo substr($value->user_description,0,250); ?> </p>						
						<p class="links"><span class="email"><a href="<?php echo antispambot("mailto:".$value->user_email);?>" class="i_email_agent"><?php _e('Email Me',DOMAIN); ?></a></span>
						<?php if($value->user_phone){ ?>
							<span class="phone"><?php echo $value->user_phone; ?></span> 
						<?php } ?>
						<span class="fr profile" ><a href="<?php echo get_author_posts_url($value->ID);?>"  class="" ><?php _e('View Profile',DOMAIN); ?> &raquo;</a></span> </p>
					</div>
                </li>                   
                <?php } ?>
		<?php
		}else
		{
		?>
		<p class="ac"><?php _e('This page is most likely empty now. It will be populated automatically as people register to your site.',DOMAIN);?><b><?php echo strtoupper($kw);?>.</b></p>
		<?php
		}
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		if(isset($_REQUEST['sort']) && $_REQUEST['sort']=='alpha'){
			$kw = $_REQUEST['kw'];
			if($kw==''){$kw = 'a';}
			
			$total_authors = tevolution_custom_list_authors_count('',array('kw'=>$kw,'sort'=>$_REQUEST['sort']),$atts['role']);
		}
		else
		{
			$my_users = new WP_User_Query( 
				array( 
					'role'	=> $atts['role'],
					'offset' => $offset ,
					'number' => $posts_per_page
				));
			$total_authors = $my_users->total_users;
		}
		if($atts['users_per_page'])
		{
			$posts_per_page = $atts['users_per_page'];
		}
		else
		{
			$posts_per_page = get_option('posts_per_page');
		}
		
		// Calculate the total number of pages for the pagination
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
			   // $pagenavi_options['pages_text'] = ('Page %CURRENT_PAGE% of %TOTAL_PAGES%:');
				$pagenavi_options['current_text'] = '%PAGE_NUMBER%';
				$pagenavi_options['page_text'] = '%PAGE_NUMBER%';
				$pagenavi_options['first_text'] = __('First Page',DOMAIN);
				$pagenavi_options['last_text'] = __('Last Page',DOMAIN);
				$pagenavi_options['next_text'] = '<strong class="page-numbers">'.__('NEXT',DOMAIN).'</strong>';
				$pagenavi_options['prev_text'] = '<strong class="page-numbers">'.__('PREV',DOMAIN).'</strong>';
				$pagenavi_options['dotright_text'] = '...';
				$pagenavi_options['dotleft_text'] = '...';
				$pagenavi_options['num_pages'] = 5; //continuous block of page numbers
				$pagenavi_options['always_show'] = 0;
				$pagenavi_options['num_larger_page_numbers'] = 0;
				$pagenavi_options['larger_page_numbers_multiple'] = 5;
			 
			 ?>
				<?php if ($paged != 1) { ?>
					<a rel="prev" href="<?php the_permalink() ?>page/<?php echo $paged - 1; ?>/"><strong class="page-numbers"><?php _e('Prev',DOMAIN); ?></strong></a>
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
					<a rel="next" href="<?php the_permalink() ?>page/<?php echo $paged + 1; ?>/"><strong class="page-numbers"?><?php _e('Next',DOMAIN); ?></strong> </a>
				<?php } ?>
	
			 </div>
			</div>
		<?php
		}
		return ob_get_clean();
}
?>