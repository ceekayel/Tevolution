<?php
/* NAME :ADD A DASHBOARD METABOX
DESCRIPTION : THIS FUNCTION WILL ADD A METABOX IN WORDPRESS DASHBOARD */
function add_claim_dashboard_metabox()
{
	global $wp_meta_boxes,$current_user;
	if(is_super_admin($current_user->ID)) {
		wp_add_dashboard_widget('claim_dashboard_metabox', 'Ownership Claims', 'fetch_claims');
		@$wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'];
	}
}
/* EOF - CLAIM DASHBOARD METABOX */

/* NAME : FETCH CLAIMS
DESCRIPTION : THIS FUNCTION FETCHES CLAIMS IN A METABOX DISPLAYING ON WORDPRESS DASHBOARD */
function fetch_claims()
{
	global $wpdb,$claim_db_table_name; ?>
	<script type="text/javascript">
	/* <![CDATA[ */
	function confirmSubmit(str) {
			var answer = confirm("<?php echo DELETE_CONFIRM_ALERT; ?>");
			if (answer){
				window.location = "<?php echo site_url(); ?>/wp-admin/index.php?poid="+str;
				alert('<?php echo ENTRY_DELETED; ?>');
			}
		}
	function claimer_showdetail(str)
	{	
		if(document.getElementById('comments_'+str).style.display == 'block')	{
			document.getElementById('comments_'+str).style.display = 'none';
		} else {
			document.getElementById('comments_'+str).style.display = '';
		}
	}
	/* ]]> */
	</script>
	<?php /* DISPLAY CLAIM DATA IN TABLE */
	echo "<table class='widefat'>
	<thead>
	<tr>
		<th style='width:30%;'>".__('Claim On',DOMAIN)."</th>
		<th style='width:30%;'>".CLAIMER_TEXT."</th>
		<th>".STATUS."</th>
		<th>".ACTION_TEXT."</th>
	</tr></thead>";
	$claim_post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = 'claim'");	
	if(count($claim_post_ids) != 0)
	{
		$counter =0;
		foreach ($claim_post_ids as $claim_post_id) :			
			$data = get_post_meta($claim_post_id,'post_claim_data',true);			
			/* FETCH CLAIM DATA */			
			$post_id = $data['post_id'];
			$post_title = $data['post_title'];
			$claimer_name = $data['claimer_name'];
			$name = str_word_count($claimer_name,1);
			$claimer_contact = $data['claimer_contact'];
			$claimer_email=$data['claimer_email'];
			$author_id = $data['author_id'];
			$status = $data['claim_status'];
			$msg = $data['claim_msg'];
			$udata = get_userdata($author_id);
			?>
               <tr>
                    <td>
                    	<?php echo $claim_post_id;?>&nbsp;<a href="<?php echo site_url().'/wp-admin/post.php?post='.$post_id.'&action=edit';?>" title="<?php echo VIEW_CLAIM; ?>"><?php __('By',ADMINDOMAIN); ?><?php echo $post_title?></a>
                    </td>
                    <td><?php echo $claimer_name;?>: <?php echo $claimer_email;?></td>                    
               	<?php if($status == 'approved' && get_post_meta($post_id,'is_verified',true) == 1) :?>
                    	<td id="verified"><?php echo YES_VERIFIED; ?></td>
                    <?php elseif($status == 'declined') : ?>   
                    	<td id="declined"><?php echo DECLINED; ?></td>
                    <?php else : ?>
                    	<td id="unapproved"><?php echo PENDING; ?></td>
                    <?php endif;?>
                    <td>
                    	<a href="javascript:void(0);claimer_showdetail('<?php echo $claim_post_id;?>');"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/details.png" alt="<?php echo DETAILS_CLAIM; ?>" title="<?php echo DETAILS_CLAIM; ?>" border="0" /></a> &nbsp;&nbsp;
						<?php if($status == 'approved' && get_post_meta($post_id,'is_verified',true) == 1){ ?>
							<a href="<?php echo site_url().'/wp-admin/post.php?post='.$post_id.'&action=edit&verified=no&clid='.$claim_post_id;?>" title="<?php echo VERIFY_CLAIM; ?>"><img style="width:16px; height:16px;" src="<?php echo plugin_dir_url( __FILE__ ); ?>images/close.gif" alt="<?php echo VERIFY_CLAIM; ?>" border="0" /></a>&nbsp;&nbsp;
						<?php }else{ ?>
							<a href="<?php echo site_url().'/wp-admin/post.php?post='.$post_id.'&action=edit&verified=yes&clid='.$claim_post_id .'&user='.$name[0]?>" title="<?php echo VERIFY_CLAIM; ?>"><img style="width:16px; height:16px;" src="<?php echo plugin_dir_url( __FILE__ ); ?>images/accept.png" alt="<?php echo VERIFY_CLAIM; ?>" border="0" /></a>&nbsp;&nbsp;
						<?php } ?>
                         <a href="javascript:void(0);" onclick="return confirmSubmit(<?php echo $claim_post_id; ?>);" title="<?php echo DELETE_CLAIM; ?>"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/delete.png" alt="<?php echo DELETE_CLAIM; ?>" border="0" /></a>
                    </td>
               </tr>
			<tr id='<?php echo "comments_".$claim_post_id; ?>' style='display:none; padding:5px;'><td colspan="7"><?php echo $msg; ?> </td></tr>
		<?php
		$c = $counter ++;
		endforeach;
	}
	else
	{
		echo "<tr><td colspan='6'>".NO_CLAIM_REQUEST."</td></tr>";
	}
	echo "</table>";
}

/* DELETING THE CLAIM ON CLICK OF DELETE BUTTON OF DASHBOARD METABOX */
if(isset($_REQUEST['poid']) && @$_REQUEST['poid'] != "")
{
	global $wpdb,$post;
	$vclid = $_REQUEST['poid'];
	wp_delete_post($vclid,true);
}
/* EOF - FETCH CLAIMS IN DASHBOARD METABOX */
/* NAME : ADD METABOX IN POSTS
DESCRIPTION : THIS FUNCTION WILL ADD A METABOX ON ADD/EDIT PAGE OF EVERY POST */
function add_claim_metabox_posts ()
{
	global $post,$wpdb,$post_id;	
	if(isset($_REQUEST['post']) && $_REQUEST['post'] !=''){
			$post_id = $_REQUEST['post'];
	}else{
			$post_id = @$post->ID;
	}
	if(@$post->ID!=''){
		$tmpdata = get_option('templatic_settings');
		$post_type = $tmpdata['claim_post_type_value'];
		if($post_type){
			foreach($post_type as $type) :	
				if($post->ID !=''): 
					
				$post_content = $wpdb->get_row("SELECT post_content FROM $wpdb->posts WHERE $wpdb->posts.post_content = '".$post_id."' and $wpdb->posts.post_type = 'claim'");					
				add_meta_box("claim_post", "Claim post", "fetch_meta_options", $type, "side", "high");
				endif;
			endforeach;
		}
	}
}
/* EOF - ADD METABOX IN POSTS */
function add_verified_user_recurring($post_id)
{
	$args =array( 'post_type'      => 'event',
			    'posts_per_page' => -1	,
			    'post_status'    => 'recurring',
			    'post_parent'    =>$post_id,
			    'meta_query'     => array('relation'      => 'AND',
									array('key'     => '_event_id',
										 'value'   => $post_id,
										 'compare' => '=',
										 'type'    => 'text'
										),
							   )
				);
	$post_query = null;
	$post_query = new WP_Query($args);
	$post_title = get_the_title($_REQUEST['clid']);
	$post_content = $post_id;
	$post_author = 1;
	
	if($post_query){
		while ($post_query->have_posts()) : $post_query->the_post();
			 global $post;
			 	$claim_post_type = array( 'post_title'   => $post_title,
									 'post_content' => $post->ID,
									 'post_status'  => 'publish',
									 'post_author'  => 1,
									 'post_type'    => "claim",
									 'post_excerpt' => "approved"
									);
				$las_rec_post_id = wp_insert_post( $claim_post_type ); /* INSERT QUERY */
				add_post_meta($las_rec_post_id,'is_verified',1);
				/* INSERTING CLAIM INFORMATION IN POST META TABLE */
				$data = get_post_meta($_REQUEST['clid'],'post_claim_data',true); /* FETCH CLAIM ID */
				$data['post_id'] = $las_rec_post_id;
				add_post_meta($las_rec_post_id, 'post_claim_data', $data);
			 
		endwhile;
		wp_reset_query();
	}
}
/* NAME : FETCH META OPTIONS
DESCRIPTION : THIS FUNCTION WILL FETCH THE CLAIM DATA IN POST'S METABOX */
function fetch_meta_options()
{	
	global $wpdb,$post;
	$claim_status = "";	
	/* VERIFY THE USER */
	if((isset($_REQUEST['verified']) && $_REQUEST['verified'] == 'yes') && (isset($_REQUEST['user']) && $_REQUEST['user']!=''))
	{
		$clid = $_REQUEST['clid'];
		global $current_user,$post_id;  
		$sclid = $_REQUEST['sclid'];
		/* if self verify the claim */
		if(isset($sclid) && $sclid!=''){
			$clid1 = $post_id;
			global $post;
			$title = get_the_title(@$post_id);
			$claim_post_id =$post_id; /* claimed post id */
			$claim_post_type = array(
				 'post_title' => 'Claim for - '.$title.'',
				 'post_content' => ''.$claim_post_id.'',
				 'post_excerpt' => 'approved',
				 'post_status' => 'publish',
				 'post_author' => 1,
				 'post_type' => "claim",
				);
			 $claim_id = wp_insert_post( $claim_post_type ); /* INSERT QUERY */
			
			$claim_post = array('post_id' => $post_id,
				    'request_uri' => '',
				    'link_url' => $link_url,
				    'claimer_id' => $current_user->ID,
				    'author_id' => $current_user->ID,
				    'post_title' => $title,
				    'claimer_name' => $current_user->display_name,
				    'claimer_email' => $current_user->user_email,
				    'claimer_contact' => '' ,
				    'claim_msg' => '',
				    'claim_status' => 'approved'
				);		
			update_post_meta($claim_id,'post_claim_data',$claim_post); /* UPDATING THE WHOLE CLAIM DATA ARRAY */
			$clid = $claim_id; // claim id
			
	
			add_post_meta($post_id,'is_verified',1);
			update_post_meta($post_id,'is_verified',1);
			
		}
		/* End verify the claim */
		$_REQUEST['user'];
		/* UPDATE CLAIM STATUS WHEN THE ADMIN VERIFIES THE AUTHOR */
		$data = get_post_meta($clid, 'post_claim_data',true);
		$post_id = $data['post_id'];
		$request_uri = $data['request_uri'];
		$link_url = $data['link_url'];
		$claimer_id = $data['claimer_id'];
		$post_title = $data['post_title'];
		$claimer_name = $data['claimer_name'];
		$claimer_email = $data['claimer_email'];
		$claimer_contact = $data['claimer_contact'];
		$author_id = $data['author_id'];
		$claim_status = $data['claim_status'];
		$claim_msg = $data['claim_msg'];
		$claim_post = array('post_id' => $post_id,
				    'request_uri' => $request_uri,
				    'link_url' => $link_url,
				    'claimer_id' => $claimer_id,
				    'author_id' => $author_id,
				    'post_title' => $post_title,
				    'claimer_name' => $claimer_name,
				    'claimer_email' => $claimer_email,
				    'claimer_contact' => $claimer_contact ,
				    'claim_msg' => $claim_msg,
				    'claim_status' => 'approved'
				);		
		update_post_meta($clid,'post_claim_data',$claim_post); /* UPDATING THE WHOLE CLAIM DATA ARRAY */
		
		$event_type = get_post_meta($post_id,'event_type',true);
		if(trim(strtolower($event_type)) == trim(strtolower('Recurring Event')))
		{
			add_verified_user_recurring($post_id);
		}
		
		add_post_meta($post_id,'is_verified',1);
		if(isset($sclid) && $sclid!=''){
			update_post_meta($claim_post_id,'is_verified',1);
		}else{
			update_post_meta($claim_post_id,'is_verified',0);
		}
		$wpdb->update( $wpdb->posts, array('post_excerpt' => 'approved'), array('ID' => $clid));
		if ($claimer_id != '' && $claimer_id != '0' && $_REQUEST['user']){
			add_verified_user($clid); /* CALL A FUNCTION TO ADD VERIFIED USER */
		}		
	}
	elseif((isset($_REQUEST['verified']) && $_REQUEST['verified'] == 'no') && (isset($_REQUEST['clid']) && $_REQUEST['clid']))
	{
		$clid = $_REQUEST['clid'];
		$data = get_post_meta($clid, 'post_claim_data',true);
		$post_id = $data['post_id'];
		delete_post_meta($clid, 'post_claim_data');
		wp_delete_post($clid);
		delete_post_meta($post_id,'is_verified',0);
	}
	elseif((isset($_REQUEST['decline']) && $_REQUEST['decline'] == 'yes') && (isset($_REQUEST['clid']) && $_REQUEST['clid']!=''))
	{
		global $current_user;
		$clid = $_REQUEST['clid'];
		$_REQUEST['user'];
		/* UPDATE CLAIM STATUS WHEN THE ADMIN DECLINES THE AUTHOR */
		$data = get_post_meta($clid, 'post_claim_data',true);
		$post_id = $data['post_id'];
		$request_uri = $data['request_uri'];
		$link_url = $data['link_url'];
		$claimer_id = $data['claimer_id'];
		$post_title = $data['post_title'];
		$claimer_name = $data['claimer_name'];
		$claimer_email = $data['claimer_email'];
		$claimer_contact = $data['claimer_contact'];
		$author_id = $data['author_id'];
		$claim_status = $data['claim_status'];
		$claim_msg = $data['claim_msg'];
		$post = array('post_id' => $post_id,
					  'request_uri' => $request_uri,
					  'link_url' => $link_url,
					  'claimer_id' => $claimer_id,
					  'author_id' => $author_id,
					  'post_title' => $post_title,
					  'claimer_name' => $claimer_name,
					  'claimer_email' => $claimer_email,
					  'claimer_contact' => $claimer_contact ,
					  'claim_msg' => $claim_msg,
					  'claim_status' => 'declined');
		update_post_meta($clid,'post_claim_data',$post); /* UPDATING THE WHOLE CLAIM DATA ARRAY */
		if(isset($sclid) && $sclid!=''){
			update_post_meta($post_id,'is_verified',1);
		}else{
			update_post_meta($post_id,'is_verified',0);
		}
		
		$wpdb->update( $wpdb->posts, array('post_excerpt' => 'declined'), array('ID' => $clid)); 
	}
	/* PRINT THE DATA IN METABOX */	
	$data = get_post_meta(@$clid,'post_claim_data',true);	
	if($data['claim_status'] == 'approved' && get_post_meta($data['post_id'],'is_verified',true) == '1')
	{	
		global $post;
	
		$post_id = $data['post_id'];
		echo "<p>1 user has claimed for this post.</p>";
		?>
		<!-- <h4><img src="<?php //echo plugin_dir_url( __FILE__ ); ?>images/verified.png" alt="<?php //echo YES_VERIFIED;?>" border="0" align="middle" style="position:relative; top:-4px; margin-right:5px;" /> <?php //echo POST_VERIFIED_TEXT; ?></h4> -->
		
		<a href="<?php echo site_url().'/wp-admin/post.php?post='.$_REQUEST['post'].'&action=edit&verified=no&clid='.$clid;?>" title="<?php echo REMOVE_CLAIM_REQUEST; ?>"><?php echo REMOVE_CLAIM_REQUEST; ?></a>
	<?php 
	}else
	{
		global $current_user;
		$id = @$_REQUEST['clid'];
		$post_claim_id = $wpdb->get_col("SELECT ID from $wpdb->posts WHERE (post_content = '".$_REQUEST['post']."' OR post_content = '".$post->ID."') AND post_status = 'publish' AND (post_excerpt = 'approved' OR post_excerpt = '') and post_type='claim'"); /* FETCH TOTAL NUMBER OF CLAIMS FOR A POST */
		$data = get_post_meta($id,'post_claim_data',true);
		$post_id = $data['post_id'];
		if(count($post_claim_id) == '')
		{ 
			echo "<p>" . NO_CLAIM . "</p>"; ?>
			<a href="<?php echo site_url().'/wp-admin/post.php?post='.$post->ID.'&action=edit&verified=yes&sclid=1&user='.$current_user->ID;?>" title="<?php echo __('Set as verify',ADMINDOMAIN); ?>" class="verify_this">
					<strong><?php echo __('Self Verify',ADMINDOMAIN); ?></strong>
			</a>
			<?php
			
		}
		else
		{
			/* CONDITION TO DISPLAY THE COUNT OF CLAIMS IN METABOX */
			if(count($post_claim_id) == 1) :
				echo "<p>" . count($post_claim_id). " user has claimed for this post.</p>";
			else :
				echo "<p>" . count($post_claim_id). " users have claimed for this post.</p>";
			endif;
			?>
          
            <?php
			foreach($post_claim_id as $key => $val) :
				$data = get_post_meta($val,'post_claim_data',true);
				if($data['claim_status'] == 'pending') :
					$user_data = get_userdata($data['claimer_id']);
					$claim_user = get_post_meta($val,'post_claim_data',true);
					$name = str_word_count($claim_user['claimer_name'],1);?>
					<ul>
						<li>
							<a href="<?php echo site_url().'/wp-admin/post.php?post='.$post->ID.'&action=edit&verified=yes&clid='.$val.'&user='.$name[0];?>" title="<?php echo VERIFY_CLAIM; ?>" class="verify_this">
								<strong><?php echo VERIFY_CLAIM; ?></strong>
							</a> / 
							<a href="<?php echo site_url().'/wp-admin/post.php?post='.$post->ID.'&action=edit&decline=yes&clid='.$val.'&user='.$name[0];?>" title="<?php echo DECLINE_CLAIM; ?>" class="verify_this">
								<strong><?php echo DECLINE_CLAIM; ?></strong>
							</a>
					<?php $current_link = get_author_posts_url(@$user_data->ID);
						if($user_data != '' && $data['claimer_id'] != '0') {?>
							<a href="<?php echo $current_link; ?> "><?php echo $user_data->display_name; ?></a>
					<?php } else { echo $name[0]; }?>
						</li>
					</ul>
                    <?php
					else:
					?>
                      <a href="<?php echo site_url().'/wp-admin/post.php?post='.$_REQUEST['post'].'&action=edit&verified=no&clid='.$val;?>" title="<?php echo REMOVE_CLAIM_REQUEST; ?>"><?php echo REMOVE_CLAIM_REQUEST; ?></a>
                    <?php
					
					endif;// Finish claim status pending if condition 
		endforeach; // finish post claim id foreach
		} 
	}
}
/* EOF - FETCH META OPTIONS */
/* NAME : ADD THE VERIFIED USER
DESCRIPTION : THIS FUNTION WILL ADD A USER WHO HAS BEEN VERIFIED FOR THE CLAIMED POST */
function add_verified_user($clid)
{
	global $wpdb,$post;
	$data = get_post_meta($clid,'post_claim_data',true);
	get_post_meta($clid,'is_verified',true);
	$user_name = $data['claimer_name'];
	$name = str_word_count($user_name,1);
	$user_email = $data['claimer_email'];
	$get_user_data_by_id = '';
	$user_has_flag = 0;
	$post_id = 0;
	$user_pass = '';
	if( @$_REQUEST['post'] ){
		$post_id = @$_REQUEST['post'];
		$get_post_data = get_post($post_id);
	}
	if(!empty($get_post_data)){
		$post_title = $get_post_data->post_title;
	}
	
	if( $name[0] !="" ){
		$get_current_user = get_user_by( 'login', $name[0] );
		
		if( @$get_current_user->ID > 0 ){
			$get_user_data_by_id = @$get_current_user->ID;
			$user_has_flag = 1;
		}else{
			$user_pass = wp_generate_password(12,false);
			$get_user_data_by_id = wp_create_user( $name[0], $user_pass, $user_email );
			$user_has_flag = 0;
		}
	}
	if ( $get_user_data_by_id )
	{
		$user_info = get_userdata($get_user_data_by_id);
		$user_login = $user_info->user_login;		
		//$user_pass = $user_info->user_pass;
		$post_url_link = '<a href="'.$_REQUEST['link_url1'].'">'.$post_title.'</a>';
		$email_subject = "Claim verified for - ".$post_title;
		$fromEmail = get_option('admin_email');
		$fromEmailName = stripslashes(get_option('blogname'));			
		
		$msg = '<p>Dear '.$user_login.',</p>';
		$msg .= '<p>The Claim for the <a href="'.get_permalink($_REQUEST['post']).'">'.$post_title.'</a> has been verified.</p>';
		if( $user_has_flag == 0 ){
			$msg .= '<p>You can login with the following credentials : </p>
					 <p>Username: [#user_login#]</p>
					 <p>Password: [#user_password#]</p>
					 <p>You can login from [#site_login_url#] or copy this link and paste it to your browser\'s address bar: [#site_login_url_link#]</p>';
		}
		$msg .= '<p>Thanks,<br/> [#site_name#] </p>';
		$client_message =  $msg;
		$subject = $email_subject;
		$yourname_link = $yourname;
		if(function_exists('get_tevolution_login_permalink')){
			$store_login = '<a href="'.get_tevolution_login_permalink().'">Click Login</a>';
			$store_login_link = get_tevolution_login_permalink();
		}else{
			$store_login='';
			$store_login_link='';
		}
		
		$search_array = array('[#user_name#]','[#user_login#]','[#user_password#]','[#site_name#]','[#site_login_url#]','[#site_login_url_link#]');
		$replace_array = array($user_login,$user_login,$user_pass,$fromEmailName,$store_login,$store_login_link);
		$client_message = str_replace($search_array,$replace_array,$client_message);
		/* CALL A MAIL FUNCTION */
		templ_send_email($fromEmail,$fromEmailName,$user_email,$user_login,$subject,$client_message,$extra='');
	}
	
	/* UPDATING THE CLAIM DATA */
	$user_info = get_userdata($get_user_data_by_id);
	$user_id = $user_info->ID;
	$data = get_post_meta($clid, 'post_claim_data',true);
	$post_id = $data['post_id'];
	$request_uri = $data['request_uri'];
	$link_url = $data['link_url'];
	$claimer_id = $data['claimer_id'];
	$post_title = $data['post_title'];
	$claimer_name = $data['claimer_name'];
	$claimer_email = $data['claimer_email'];
	$claimer_contact = $data['claimer_contact'];
	$author_id = $data['post_author_id'];
	$claim_status = $data['claim_status'];
	$claim_msg = $data['claim_msg'];
	$post = array('post_id' => $post_id,
				  'request_uri' => $request_uri,
				  'link_url' => $link_url,
				  'claimer_id' => $user_id,
				  'author_id' => $user_id,
				  'post_title' => $post_title,
				  'claimer_name' => $claimer_name,
				  'claimer_email' => $claimer_email,
				  'claimer_contact' => $claimer_contact ,
				  'claim_msg' => $claim_msg,
				  'claim_status' => $claim_status);
	update_post_meta($post->ID,'post_claim_data',$post); /* UPDATING THE WHOLE CLAIM DATA ARRAY */
	
	/* UPDATING THE POST TABLE */
	$wpdb->get_results("Update $wpdb->posts set post_author ='".$user_id."' where ID = '".$post_id."' and post_status  = 'publish'");
}
/* EOF - ADD VERIFIED USER */
/* NAME :ADD A WIDGET
DESCRIPTION : THIS FUNCTION WILL REGISTER THE WIDGET OF CLAIM OWNERSHIP */
function add_claim_widget()
{
	register_widget('claim_widget');
}
/* EOF - ADD A WIDGET */
/* NAME : POST CLAIM FORM VALUES
DESCRIPTION : THIS FUNCTION POSTS THE DATA OF THE CLAIM FORM, CREATES A POST AND SAVES DATA IN POSTMETA */
function insert_claim_ownership_data($post_details)
{
	global $wpdb,$General,$upload_folder_path,$post;
	if(@$_POST['claimer_name'])
	{
		/* CODE TO CHECK WP-RECAPTCHA */
		$tmpdata = get_option('templatic_settings');
		$display = $tmpdata['user_verification_page'];
		if( $tmpdata['recaptcha'] == 'recaptcha')
		{
			if(file_exists(get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php') && is_plugin_active('wp-recaptcha/wp-recaptcha.php') && in_array('claim',$display))
			{
				require_once( get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php');
				$a = get_option("recaptcha_options");
				$privatekey = $a['private_key'];
				$resp = recaptcha_check_answer ($privatekey,
							getenv("REMOTE_ADDR"),
							$post_details["recaptcha_challenge_field"],
							$post_details["recaptcha_response_field"]);
									
				if ($resp->is_valid )
				{
					echo "<script>alert("._e('Your claim for this post has been sent successfully.',DOMAIN).");</script>";
				}
				else
				{
					echo "<script>alert('Invalid captcha. Please try again.');</script>";
					return false;	
				}	 
			}
		}
		/* END OF CODE - CHECK WP-RECAPTCHA */
		
		/* POST CLAIM FORM VALUES */
		$yourname = $post_details['claimer_name'];
		$youremail = $post_details['claimer_email'];
		$your_number = $post_details['claimer_contact'];
		$c_number = $post_details['claimer_contact'];
		$message = $post_details['claim_msg'];
		$claim_post_id = $post_details['post_id'];
		$post_title = $post_details['post_title'];
		$user_id = $current_user->ID;
		$author_id = $post_details['author_id'];
		if($claim_post_id != "")
		{
			$sql = "select ID,post_title from $wpdb->posts where ID ='".$claim_post_id."'";
			$postinfo = $wpdb->get_results($sql);
			foreach($postinfo as $postinfoObj)
			{
				$post_title = $postinfoObj->post_title;
			}
		}
		
		$user_ip = $_SERVER["REMOTE_ADDR"];
		
		/* INSERTING CLAIM POST TYPE IN POST TABLE */
		$id = get_the_title($post->ID);
		$claim_post_type = array(
			 'post_title' => 'Claim for - '.$id.'',
			 'post_content' => ''.$claim_post_id.'',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_type' => "claim",
			);
		$post_id = wp_insert_post( $claim_post_type ); /* INSERT QUERY */
		/* INSERTING CLAIM INFORMATION IN POST META TABLE */
		add_post_meta($post_id, 'post_claim_data', $post_details);
		/* END OF CODE - INSERT VALUES */
		$q = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID = 1");
		$to_email = get_option('admin_email');
		$to_name = $q->user_login;
		$site_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
		
		$tmpdata = get_option('templatic_settings');
		$email_subject = $tmpdata['claim_ownership_subject'];
		$claim = $tmpdata['claim_ownership_content'];
		
		if(@$email_subject == '' )
		{
			$email_subject = "Claim to - [#post_title#]";
		}
		
		$subject_search_array = array('[#post_id#]');
		$subject_replace_array = array($post_id);
		if(@$claim=='')
		{
			$claim =  '<p>Dear admin,</p><br/><p> [#claim_name#] has claimed for this post</p><p>[#message#]</p><p>Link :[#post_title#]</p><p>From : [#your_name#]</p><p>Email: [#claim_email#]<p>Phone Number : [#your_number#]</p>';
		}
		$filecontent_arr1 = $claim;
		$filecontent_arr2 = $filecontent_arr1;
		$client_message = $filecontent_arr2;
		$subject = $email_subject;
		$post_url_link = '<a href="'.$_REQUEST['link_url'].'">'.$post_title.'</a>';
		$yourname_link = __($yourname,DOMAIN);
		$search_array = array('[#to_name#]','[#post_title#]','[#message#]','[#your_name#]','[#your_number#]','[#post_url_link#]');
		$replace_array = array($to_name,$post_url_link,$message,$yourname_link,$your_number,$post_url_link);
		$client_message = str_replace($search_array,$replace_array,$client_message);
		
		/* CHECK THE PLAYTHRU */
		if(file_exists(get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php') && is_plugin_active('are-you-a-human/areyouahuman.php')  && in_array('claim',$display) && $tmpdata['recaptcha'] == 'playthru')
		{
			require_once( get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php');
			require_once(get_tmpl_plugin_directory().'are-you-a-human/includes/ayah.php');
			$ayah = new AYAH();
			/* The form submits to itself, so see if the user has submitted the form.
			Use the AYAH object to get the score. */
			$score = $ayah->scoreResult();
		
			if($score)
			{
				/* send mail */
				templ_send_email($youremail,$yourname,$to_email,$to_name,$subject,$client_message,$extra='');
			}
			else
			{
				echo "<script>alert('You need to play the game to send the mail successfully.');</script>";
				return false;
			}
		}
		else
		{
			/* CALL A MAIL FUNCTION */
			templ_send_email($youremail,$yourname,$to_email,$to_name,$subject,$client_message,$extra='');
		}
	}
}
/*
 * Function Name: Claim Ownership
 *
 */
function claim_ownership(){
	global $post,$wpdb;
	if(is_single() || is_page() && !is_page_template('page-template_form.php') ) :
		insert_claim_ownership_data($_POST);
		
		if(get_post_meta($post->ID,'is_verified',true) == 1)
		{ ?>
			<p class="i_verfied"><?php echo OWNER_VERIFIED; ?></p>
		<?php
		}else
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
							<p class="claimed"><?php echo ALREADY_CLAIMED; ?></p>
						<?php 
						}else{?>					
						<a href="#claim_listing" id="trigger_id" title="claim_ownership" class="i_claim c_sendtofriend" ><?php _e('Claim Ownership',DOMAIN);;?></a>
						<?php
							add_action('wp_footer','tevolution_claim_form'); // action for footer to include claim listing form   ?>
						<?php }
					}
				}
			}else{ 
				add_action('wp_footer','tevolution_claim_form'); // action for footer to include claim listing form  
				?>			
				<a href="#claim_listing" id="trigger_id" title="claim_ownership" class="i_claim c_sendtofriend"><?php _e('Claim Ownership',DOMAIN);;?></a>
				<?php
			}
		}
	endif;
}
/*
Name:tevolution_claim_form
Desc: include the claim ownership form in footer
*/
function tevolution_claim_form(){
	?>
		<script>
		jQuery(document).ready(function(){
			jQuery('#trigger_id').on('click', function(){ 
				jQuery('html,body').scrollTop(0);
				jQuery('#claim_listing').scrollTop(0);
			}); 
		});
		</script>
	<?php
	include_once (TEMPL_MONETIZE_FOLDER_PATH . "templatic-claim_ownership/popup_claim_form.php");
}
add_action('wp_ajax_tevolution_claimowner_ship','tevolution_claimowner_ship');
add_action('wp_ajax_nopriv_tevolution_claimowner_ship','tevolution_claimowner_ship');
function tevolution_claimowner_ship(){
	
	global $wpdb,$General,$upload_folder_path,$post;
	if(@$_REQUEST['claimer_name'])
	{
		/* CODE TO CHECK WP-RECAPTCHA */
		$tmpdata = get_option('templatic_settings');
		$display = $tmpdata['user_verification_page'];
		if( $tmpdata['recaptcha'] == 'recaptcha')
		{
			if(file_exists(get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php') && is_plugin_active('wp-recaptcha/wp-recaptcha.php') && in_array('claim',$display))
			{
				require_once( get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php');
				$a = get_option("recaptcha_options");
				$privatekey = $a['private_key'];
				$resp = recaptcha_check_answer ($privatekey,
							getenv("REMOTE_ADDR"),
							$_REQUEST["recaptcha_challenge_field"],
							$_REQUEST["recaptcha_response_field"]);
									
				if (!$resp->is_valid )
				{
					echo '1';
					exit;
					echo "<script>alert('Invalid captcha. Please try again.');</script>";
					return false;	
				}	 
			}
		}
		/* END OF CODE - CHECK WP-RECAPTCHA */
		
		/* POST CLAIM FORM VALUES */
		$yourname = $_REQUEST['claimer_name'];
		$youremail = $_REQUEST['claimer_email'];
		$your_number = $_REQUEST['claimer_contact'];
		$c_number = $_REQUEST['claimer_contact'];
		$message = $_REQUEST['claim_msg'];
		$claim_post_id = $_REQUEST['post_id'];
		$post_title = $_REQUEST['post_title'];
		$user_id = $current_user->ID;
		$author_id = $_REQUEST['author_id'];
		if($claim_post_id != "")
		{
			$sql = "select ID,post_title from $wpdb->posts where ID ='".$claim_post_id."'";
			$postinfo = $wpdb->get_results($sql);
			foreach($postinfo as $postinfoObj)
			{
				$post_title = $postinfoObj->post_title;
			}
		}
		
		$user_ip = $_SERVER["REMOTE_ADDR"];
		
		/* INSERTING CLAIM POST TYPE IN POST TABLE */
		$id = get_the_title($post->ID);
		$claim_post_type = array(
			 'post_title' => 'Claim for - '.$id.'',
			 'post_content' => ''.$claim_post_id.'',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_type' => "claim",
			);
		$post_id = wp_insert_post( $claim_post_type ); /* INSERT QUERY */
		/* INSERTING CLAIM INFORMATION IN POST META TABLE */
		add_post_meta($post_id, 'post_claim_data', $_REQUEST);
		/* END OF CODE - INSERT VALUES */
		$q = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID = 1");
		$to_email = get_option('admin_email');
		$to_name = $q->user_login;
		$tmpdata = get_option('templatic_settings');
		$email_content =  @stripslashes($tmpdata['claim_ownership_subject']);
		$email_subject =  @stripslashes($tmpdata['claim_ownership_content']);
		$site_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
		if(@$email_content == '')
		{
			$email_subject = "Claim to - [#post_title#]";
		}
		if(@$email_content == '')
		{
				$email_content = '<p>Dear admin,</p><br/><p> [#claim_name#] has claimed for this post</p><p>[#message#]</p><p>Link :[#post_title#]</p><p>From : [#your_name#]</p><p>Email: [#claim_email#]<p>Phone Number : [#your_number#]</p>';		
		}
		$post_url_link = '<a href="'.$_REQUEST['link_url'].'">'.$post_title.'</a>';
		$subject_search_array = array('[#post_title#]');
		$subject_replace_array = array($post_url_link);
		$email_subject = str_replace($subject_search_array,$subject_replace_array,$email_subject);
		$subject = $email_subject;
		
		$yourname_link = __($yourname,DOMAIN);
		$search_array = array('[#claim_name#]','[#to_name#]','[#post_title#]','[#message#]','[#your_name#]','[#your_number#]','[#post_url_link#]','[#claim_email#]');
		$replace_array = array($yourname,$to_name,$post_url_link,$message,$yourname_link,$your_number,$post_url_link,$youremail);
		$client_message = str_replace($search_array,$replace_array,$email_content);
		
		/* CHECK THE PLAYTHRU */
		if(file_exists(get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php') && is_plugin_active('are-you-a-human/areyouahuman.php')  && in_array('claim',$display) && $tmpdata['recaptcha'] == 'playthru')
		{
			require_once( get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php');
			require_once(get_tmpl_plugin_directory().'are-you-a-human/includes/ayah.php');
			$ayah = new AYAH();
			/* The form submits to itself, so see if the user has submitted the form.
			Use the AYAH object to get the score. */
			$score = $ayah->scoreResult();
		
			if($score)
			{
				/* send mail */
				_e('Your claim for this post has been sent successfully.',DOMAIN);
				templ_send_email($youremail,$yourname,$to_email,$to_name,$subject,$client_message,$extra='');
				exit;
			}
			else
			{
				echo '2';
				exit;
				echo "<script>alert('You need to play the game to send the mail successfully.');</script>";
				return false;
			}
		}
		else
		{
			/* CALL A MAIL FUNCTION */
			_e('Your claim for this post has been sent successfully.',DOMAIN);
			templ_send_email($youremail,$yourname,$to_email,$to_name,$subject,$client_message,$extra='');
			exit;
			
		}
	}
	
}
?>
