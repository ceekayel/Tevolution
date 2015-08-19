<?php 
/*
 * file is called after if cancel the payment
 */
global $current_user,$wpdb;
$transaction_tabel = $wpdb->prefix."transactions";
define('PAY_CANCELATION_TITLE',__('Payment Cancellation',DOMAIN));
define('PAY_FAST_CANCEL_MSG',__('Payment has been cancelled successfully. The post submitted by you has not been published.',DOMAIN));
$postid = @$_REQUEST['pid'];
$trans_id = @$_REQUEST['trans_id'];
$post_author = $wpdb->get_var( "select post_author from $wpdb->posts where ID = {$postid}" );
if( $post_author == $current_user->ID ){
	get_header();
?>
	<div id="content">
		<div class="hfeed">
		<h1 class="page_head"><?php echo PAY_CANCELATION_TITLE;?></h1>			
		<?php
			if( $postid ){
				if( @$postid !=''){
					/*	Update post status	*/
					$my_post = array();
					$my_post['ID'] = $postid;
					$my_post['post_status'] = 'draft';
					wp_update_post( $my_post );
					
					/*	Update transaction status	*/
					if( $trans_id != "" ){
						$sql_status_update = $wpdb->query("update $transaction_tabel set status = 2 where trans_id= {$trans_id} ");
					}					
				}
			?>
			<h4><?php echo PAY_FAST_CANCEL_MSG; ?></h4> 
			<?php 
			}else{
					_e('Invalid procedure.',T_DOMAIN);
			}	
		?>
		</div>
	</div> <!-- content #end -->
<?php 
	get_sidebar('primary');
	get_footer(); 
}else{
	wp_die(__('You have not permission to access this page',DOMAIN));
}
?>