<?php
/*FUNCTION TO ADD META BOX ON EDIT POST PAGE */
function admin_init_func()
{
	$posttaxonomy = get_option("templatic_custom_post");	
	if($posttaxonomy){
	foreach($posttaxonomy as $key=>$_posttaxonomy):		
		add_meta_box("block_ip", "Block IP", "fetch_ip_post_author",$key, "side", "high");
	endforeach;
	}
}
/* EOF - ADD META BOX */
/* FUNCTION TO FETCH IP ADDRESS OF THE AUTHORS */
function fetch_ip_post_author()
{ ?>
	<script type="text/javascript">
		/* <![CDATA[ */
		function block_ip(postid)
		{
			window.location = "<?php echo site_url(); ?>/wp-admin/post.php?post="+postid+"&action=edit&blockip="+postid;	
		}
		function unblock_ip(postid)
		{
			window.location = "<?php echo site_url(); ?>/wp-admin/post.php?post="+postid+"&action=edit&unblockip="+postid;
		}
		/* ]]> */
	</script>
	<?php global $post;
	$remote_ip =  get_post_meta($post->ID,'remote_ip',true);
	if($remote_ip == '')
		{
			echo "<p>" . IP_IS . ": ".getenv("REMOTE_ADDR"). "</p>";
			echo "<input type='hidden' name='remote_ip' id='remote_ip' value='".getenv("REMOTE_ADDR")."'/>";
			echo "<input type='hidden' name='ip_status' id='ip_status' value='0'/>";
		}
		else
		{
			$rip = get_post_meta($post->ID,'remote_ip',true);
			$ipstatus = get_post_meta($post->ID,'ip_status',true);
		if($rip != "")
		{
			echo SUBMITTED_IP . ": <strong>".$rip."</strong><br/>";
			global $wpdb,$post;
			$ipstatus = get_post_meta($post->ID,'ip_status',true);
			if($ipstatus == 0 || $ipstatus == "")
			{
				$parray = $wpdb->get_results("select * from $wpdb->postmeta where post_id != '".$post->ID."' and meta_value='".$rip."'");
				foreach($parray as $pa)
				{
					$blocked = get_post_meta($pa->post_id,'ip_status',true);
					if($blocked == 1)
					{
						break;
						return $blocked;
					}
				}
				if(isset($blocked) && $blocked != 0)
				{
					echo "<a href='javascript:void(0)' onclick='unblock_ip(".$post->ID.")'>" . UNBLOCK_IP . "</a>";
					update_post_meta($post->ID,'ip_status',1);
				}
				else
				{
					echo "<a href='javascript:void(0)' onclick='block_ip(".$post->ID.")'>" . BLOCK_IP . "</a>";
				}
			}
			else
			{
				echo "<a href='javascript:void(0)' onclick='unblock_ip(".$post->ID.")'>" . UNBLOCK_IP . "</a>";
			}
		}
		else
		{
			echo IP_NOT_DETECTED;
		}
	}
}
if(isset($_REQUEST['blockip']) && $_REQUEST['blockip'] != "")
{
	global $post,$wpdb;
	$post_id = $_REQUEST['blockip'];
	$rip = get_post_meta($post_id,'remote_ip',true);
	if($rip =="")
	{
		$rip = getenv("REMOTE_ADDR");
		add_post_meta($post_id,'remote_ip',$rip);
	}
	$ipstatus = get_post_meta($post_id,'ip_status',true);
	$parray = $wpdb->get_results("select * from $wpdb->postmeta where post_id != '".$post_id."' and meta_value='".$rip."'");
	if(mysql_affected_rows() > 0)
	{
		foreach($parray as $parrayobj)
		{
			$ips = get_post_meta($parrayobj->post_id,'ip_status',true);
			if($ips == 1)
			{ ?>
			<script>
				alert("<?php _e('Apologies, your IP has been blocked for this domain. You are not able to use the submit form.',DOMAIN); ?>");
				window.location = "<?php echo site_url(); ?>/wp-admin/post.php?post=<?php echo $post_id; ?>&action=edit";
			</script>	
		<?php }
		}
	}
	if($rip =="")
	{
		$rip = getenv("REMOTE_ADDR");
		add_post_meta($post_id,'remote_ip',$rip); 
	}
	if($rip != "")
	{
		if(!isset($ipstatus))
		{
			add_post_meta($post_id,'ip_status','1'); 
		}
		else
		{
			update_post_meta($post_id,'ip_status','1');
		}
		global $wpdb,$ip_db_table_name;
		$ip_db_table_name = $wpdb->prefix."ip_settings";
		$wpdb->get_row("select * from $ip_db_table_name where ipaddress = '".$rip."'");
		if(mysql_affected_rows() > 0)
		{
			$wpdb->query("update $ip_db_table_name set ipstatus = '1' where ipaddress = '".$rip."'");
		}
		else
		{
			$wpdb->query("INSERT INTO $ip_db_table_name (`ipid`, `ipaddress`, `ipstatus`) VALUES (NULL, '".$rip."', '1')");
		}
	} ?>
	<script>location.href = "<?php echo site_url(); ?>/wp-admin/post.php?post="+postid+"&action=edit";</script>
<?php 
}
elseif(isset($_REQUEST['unblockip']) != "")
{
	$post_id = $_REQUEST['unblockip'];
	update_post_meta($post_id,'ip_status',0);
	$rip = get_post_meta($post_id ,'remote_ip',true);
	$ipstatus = get_post_meta($post_id ,'ip_status',true);
	$parray = $wpdb->get_results("select * from $wpdb->postmeta where post_id != '".$post_id."' and meta_value='".$rip."'");
	global $wpdb,$ip_db_table_name;
	$ip_db_table_name = $wpdb->prefix."ip_settings";
	$wpdb->get_row("select * from $ip_db_table_name where ipaddress = '".$rip."'");
	if(mysql_affected_rows() > 0)
	{
		$wpdb->query("update $ip_db_table_name set ipstatus = '0' where ipaddress = '".$rip."'");
	}
	else
	{
		$wpdb->query("INSERT INTO $ip_db_table_name (`ipid`, `ipaddress`, `ipstatus`) VALUES (NULL, '".$rip."', '0')");
	}
	$parray = $wpdb->get_results("select * from $wpdb->postmeta where meta_value='".$rip."'");
	if(mysql_affected_rows() > 0)
	{
		foreach($parray as $parrayobj)
		{
			$ips = get_post_meta($parrayobj->post_id,'ip_status',true);
			if($ips == 1)
			{
				update_post_meta($parrayobj->post_id,'ip_status',0);
			}
		}
	}
	if($rip != "")
	{
		if(!isset($ipstatus))
		{
			add_post_meta($post_id ,'ip_status','0');
		}
		else
		{
			update_post_meta($post_id ,'ip_status','0');
		}
	} ?>
<script type="text/javascript">window.location ="<?php echo site_url(); ?>/wp-admin/post.php?post="+postid+"&action=edit";</script>
<?php 
}

/* EOF - FETCH IP OF AUTHOR */
/* FUNCTION TO INSERT IP ADDRESS DATA INTO DATABASE */
function insert_ip_address_data($block_ip_data)
{
	global $post,$wpdb;
	$countip = explode(",",$block_ip_data);
	$countoldips = explode(",",$_POST['ipaddress2']);
	$new_array= array_diff($countoldips,$countip);
	/* SHOW ARRAY DIFFERENCE AND UPDATE FIELDS */
	if($new_array != "")
	{
		for($i=0 ; $i <=count($new_array) ; $i++)
		{
			foreach($new_array as $nip)
			{
				$nip = trim($nip);
				if($nip != "")
				{
					$ipres1 = $wpdb->get_results("select * from $wpdb->postmeta where meta_key='remote_ip' and meta_value = '".$nip."'");
					if(mysql_affected_rows() > 0)
					{
						foreach($ipres1 as $ipobj1)
						{
							update_post_meta($ipobj1->post_id,'ip_status',0);
						}
					}
					global $ip_db_table_name;
					$wpdb->update($ip_db_table_name,array('ipstatus' => '0'),array('ipaddress' => $nip));
				}
			}
		}
	}
	$new_insert_array= array_diff($countip,$countoldips);
	/* SHOW ARRAY DIFFERENCE AND INSERT FIELDS */
	if($new_insert_array != "")
	{
		global $ip_db_table_name;
		for($i=0 ; $i <=count($new_insert_array) ; $i++)
		{
			foreach($new_insert_array as $nip)
			{
				if($nip!= "")
				{
					$ipres1 = $wpdb->get_results("select * from $wpdb->postmeta where meta_key='remote_ip' and meta_value = '".$nip."'");
					$ipstatus = $wpdb->get_row("select * from $ip_db_table_name where ipaddress='".$nip."'");
					if($ipstatus != "")
					{
						$wpdb->update($ip_db_table_name,array('ipstatus' => '1'),array('ipaddress' => $nip));
					}
					else
					{
						$ipinsert = $wpdb->query("INSERT INTO $ip_db_table_name(`ipid`, `ipaddress`, `ipstatus`) VALUES (NULL,'".$nip."', '1')");
					}
					if(mysql_affected_rows() > 0)
					{
						foreach($ipres1 as $ipobj1)
						{
							update_post_meta($ipobj1->post_id,'ip_status',0);
						}
					}
					global $ip_db_table_name;
					$wpdb->update($ip_db_table_name,array('ipstatus' => '1'),array('ipaddress' => $nip));
				}
			}
		}
	}
	global $ip_db_table_name;
	for($i =0; $i <= count($countip); $i++)
	{
		$countip[$i];
		if($countip[$i] != "")
		{
			$ipres = $wpdb->get_results("select * from $wpdb->postmeta where meta_value = '".$countip[$i]."'");
			if(mysql_affected_rows() > 0)
			{
				foreach($ipres as $ipobj)
				{
					update_post_meta($ipobj->post_id,'ip_status',1);
				}
			}
		}
	}
}
/* EOF - INSERT IP ADDRESS DATA */
/* FUNCTION TO SAVE THE POST DATA */
if(!function_exists('save_post_data'))
{
	function save_post_data($post_id)
	{
		global $globals;
		$pID = @$_POST['post_ID'];
		/* SAVE REMOTE IP */
		if( get_post_meta( $pID, 'remote_ip' ) == "" )
		{
			add_post_meta($pID, 'remote_ip', @$_POST['remote_ip'], true );
		}
		elseif(isset($_POST['remote_ip']) && $_POST['remote_ip'] != get_post_meta($pID, 'remote_ip', true))
		{
			update_post_meta($pID, 'remote_ip', $_POST['remote_ip']);
		}
		elseif(isset($_POST['remote_ip']) && $_POST['remote_ip'] == "")
		{
			delete_post_meta($pID, 'remote_ip', get_post_meta($pID, 'remote_ip', true));
		}
		/* SAVE IP STATUS */
		if( get_post_meta( $pID, 'ip_status' ) == "" )
		{
			add_post_meta($pID, 'ip_status', @$_POST['ip_status'], true );
		}
		elseif(isset($_POST['ip_status']) && $_POST['ip_status'] != get_post_meta($pID, 'ip_status', true))
		{
			update_post_meta($pID, 'ip_status', $_POST['ip_status']);
		}
		elseif(isset($_POST['ip_status']) && $_POST['ip_status'] == "")
		{
			delete_post_meta($pID, 'ip_status', get_post_meta($pID, 'ip_status', true));
		}
	}
}
/* EOF - SAVE POST DATA */
function templ_fetch_ip(){
	global $wpdb,$ip_db_table_name;
	
	$ip_db_table_name = $wpdb->prefix."ip_settings";
	$ip = $wpdb->get_row("select * from $ip_db_table_name where ipaddress = '".getenv("REMOTE_ADDR")."' and ipstatus=1");
	
	return $ip;
}
add_action('save_post', 'save_post_data'); /* CALL A FUNCTION TO SAVE THE POST */