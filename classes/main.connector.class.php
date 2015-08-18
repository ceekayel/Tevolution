<?php
class Main_Connector_Class  {
	var $components;
	var $current_action_response;
	public function __construct() {		
		$this->sections = array(
								'tevolution_bundled' => array(
														'name' => __( 'Core Module', ADMINDOMAIN ), 
														'description' => __( 'Features bundled with Tevolution.', ADMINDOMAIN )
													), 								
								'standalone_plugin' => array(
														'name' => __( 'Extend', ADMINDOMAIN ), 
														'description' => __( 'Plugins developed by Templatic.', ADMINDOMAIN )
													)
								);
		$this->templatic_components = array(
								'tevolution_bundled' => array(
											    array('name'=>__('Bulk Import / Export', ADMINDOMAIN )),
											    array('name'=>__('Claim Post Manager', ADMINDOMAIN )),
											    array('name'=>__('Custom Fields Manager', ADMINDOMAIN )),
											    array('name'=>__('Custom Post Types Manager', ADMINDOMAIN )),
											    array('name'=>__('Security Manager', ADMINDOMAIN)),
											    array('name'=>__('Monetization', ADMINDOMAIN)),
											    array('name'=>__('User registration/Login Management', ADMINDOMAIN)),
											    ),
								//add templatic standalone plugin information
								'standalone_plugin' => @$stand_alone_plugin
								);
			
		$this->closed_components = array();
		
	} // End __construct()
	
	public function get_section_links () {
		$html = '';
		
		$total = 0;
		
		$sections = array(
						'all' => array( 'href' => '#all', 'name' => __( 'All', ADMINDOMAIN ), 'class' => 'current all tab' )
					);
					
		foreach ( $this->sections as $k => $v ) {			
			$total += count( $this->templatic_components[$k] );
			$sections[$k] = array( 'href' => '#' . esc_attr( @$this->config->token . $k ), 'name' => $v['name'], 'class' => 'tab', 'count' => count( $this->templatic_components[$k] ) );			
		}		
		$sections['all']['count'] = $total;		
		$sections = apply_filters( @$this->config->token . '_main_section_links_array', $sections );
		
			
		
		$count = 1;
		foreach ( $sections as $k => $v ) {
			
			$count++;
			if ( $v['count'] > 0 ) 
			{				
				$html .= '<li><a href="' . $v['href'] . '"';
				if ( isset( $v['class'] ) && ( $v['class'] != '' ) ) { $html .= ' class="' . esc_attr( $v['class'] ) . '"'; }
				$html .= '>' . $v['name'] . '</a>';
				$html .= ' <span>(' . $v['count'] . ')</span>';
				if ( $count <= count( $sections ) ) { $html .= ' | '; }
				$html .= '</li>' . "\n";
			}
		}
		
		echo $html;
		do_action( @$this->config->token . '_main_get_section_links' );
	} // End get_section_links()	
		
}
/*
 * Add action display listing of templatic plugin
 */
 
if((!isset($_REQUEST['tab'])&& @$_REQUEST['tab']=='') || (isset($_REQUEST['tab']) && @$_REQUEST['tab'] =='overview')) { 
	add_action('tevolution_plugin_list','list_of_templatic_plugin');
}
/*
 * Function Name: list_of_templatic_plugin
 * Return Display link for Templatic bundle featur list and also templatic standalone plugin
 */
function list_of_templatic_plugin()
{	
	global $Main_Connector_Class;
	$Main_Connector_Class= new Main_Connector_Class();
	?><p class="tevolution_desc"><?php echo __('<b>Tevolution</b> is the base Templatic plugin. It\'s used to enable features such as price packages, custom fields and bulk import. If you do not need some of its features just disable them below. Explore the other tabs to see what else is possible with Tevolution and how you can extend the functionality even further. For more on Tevolution <a href="http://templatic.com/plugins/tevolution" target="_blank">Click here</a>.',ADMINDOMAIN)?></p>	
     <?php
}
remove_all_actions('templconnector_bundle_box');
add_action('tevolution_setup_steps_box','tevolution_setup_steps_box_display'); // write setup steps here
/* Check User ragistration module is active or not */
function tevolution_user_registration(){
	if(is_active_addons('templatic-login')){
		return true;
	}else{
		return false;
	}
}
/* end */
/* Check that user had created the posttype or not */
function tevolution_templatic_custom_post(){
	if(get_option('templatic_custom_post') !=''){
		return true;
	}else{
		return false;
	}
}
/* end */
/* Check that user had created submition form or not */
function tevoltion_submission_form(){
	global $wp_query;
	$args = array('post_type'=>'page',
				'meta_query' => array(
							array(
							'key' => 'is_tevolution_submit_form',
							'value' => 1,
							'compare' => '==',
							)
					));
	$data = get_posts($args);
	if(!empty($data)){
		return true;
	}else{
		return false;
	}
} 
/* check nav menu craeted or not */
function tevolution_nav_menu(){
	if(has_nav_menu('primary') || has_nav_menu('secondary')){
		return true;
	}else{
		return false;
	}
}
function tevoltion_monetization_status(){
	if(get_option('monetization') =='Active'){
		return true;
	}else{
		return false;
	}
}
/* end */
function tevolution_setup_steps_box_display(){ 
	$setup_steps = array();
	
	$setup_post_type = tevolution_templatic_custom_post();
	$user_reg_mod = tevolution_user_registration();
	$setup_submit_form = tevoltion_submission_form();
	$monetization_status = tevoltion_monetization_status();
	$tevolution_nav_menu = tevolution_nav_menu();
	$permalink = get_option('permalink_structure');
	
	if($permalink == '/%postname%/'){
		$permalink_status = true;
	}else{
		$permalink_status = false;
	}	
	$other_flag = false;
	$other_opts = array();
	$other_opts = get_option('templatic_settings');
	if( !empty( $other_opts ) ){
		$other_flag = true;
	}
	
	$setup_steps = array(
				 array('title'=>__('Create a Post type',ADMINDOMAIN),
				'link'=> site_url().'/wp-admin/admin.php?page=custom_taxonomy',
				'desc'=>sprintf(__('Create your own <a href="%s/wp-admin/admin.php?page=custom_taxonomy">post type</a> (e.g. events, places etc) and extend default WordPress functionality to manage your content.',ADMINDOMAIN),site_url()),
				'status'=> $setup_post_type),
				 array('title'=>__('Set the Permalink',ADMINDOMAIN),
				'link'=> site_url().'/wp-admin/options-permalink.php',
				'desc'=>sprintf(__('Set the  <a href="%s/wp-admin/options-permalink.php">Permalink</a> after creating the custom post type so wordpress can easily flush the permalink rule for your new created post type.',ADMINDOMAIN),site_url()),
				'status'=> $permalink_status),
				array('title'=>__('Setup the Page settings',ADMINDOMAIN),
				'link'=> site_url()."/wp-admin/admin.php?page=templatic_settings",
				'desc'=>sprintf(__('Go to <a href="%s/wp-admin/admin.php?page=templatic_settings">General settings</a> to setup the settings for all your pages which includes how your listing page, detail page and submission form are going to work, also what specific things you want to include on each of the pages mentioned here.',ADMINDOMAIN),site_url()),
				'status'=> $other_flag), 
				 array('title'=>__('Create a Submission Form',ADMINDOMAIN),
				'link'=> site_url()."/wp-admin/post-new.php?post_type=page",
				'desc'=>sprintf(__('Add a new <a href="%s/wp-admin/post-new.php?post_type=page">WordPress page</a> and add this shortcode [submit_form post_type=\'your post type\'] in its editor to create a submission form for the post type you have created. Your site visitors will be able to post their entries(e.g. Events) from front end using this submission form.',ADMINDOMAIN),site_url()),
				'status'=> $setup_submit_form),  			
				array('title'=>__('Create Custom fields',ADMINDOMAIN),
				'link'=> site_url().'/wp-admin/admin.php?page=custom_fields',
				'desc'=>sprintf(__('Create new <a href="%s/wp-admin/admin.php?page=custom_fields">custom fields</a> with different field types which will appear in the submission form created in the above step to get relevant information from your users on submitting their entries. We have already created some default fields (and necessary) for you.',ADMINDOMAIN),site_url()),
				'status'=>'true'),
				array('title'=>__('Create Login/Registration and Profile Pages',ADMINDOMAIN),
				'link'=>  site_url().'/wp-admin/admin.php?page=templatic_settings#registration_page_setup',
				'desc'=>sprintf(__('Registration and related process are helpful to let users register themselves with you. So, we have already created login, registration and profile pages on your site, however you can change these WordPress page to use as these forms from <a href="%s/wp-admin/admin.php?page=templatic_settings#registration_page_setup">General settings</a>.<br/> Make sure you add appropriate shortcodes in those pages,<br/> Login page: Use [tevolution_login] <br/> Registration Page:  [tevolution_register] <br/> Profile Page: [tevolution_profile]',ADMINDOMAIN),site_url()),
				'status'=>$user_reg_mod),
				array('title'=>__('Create Profile Custom Fields',ADMINDOMAIN),
				'link'=>  site_url().'/wp-admin/admin.php?page=user_custom_fields',
				'desc'=>sprintf(__('Create <a href="%s/wp-admin/admin.php?page=user_custom_fields">profile custom fields</a> which will appear on the registration and profile pages that you have created in the above steps to get the required information from your users when they register on your site.',ADMINDOMAIN),site_url()),
				'status'=> $user_reg_mod),
				array('title'=>__('Create a Navigation Menu',ADMINDOMAIN),
				'link'=> site_url().'/wp-admin/nav-menus.php',
				'desc'=>sprintf(__('WordPress Menu makes it extremely easy to manage which links you want to show in your navigation menu. Create your menu <a href="%s/wp-admin/nav-menus.php">now</a>.',ADMINDOMAIN),site_url()),
				'status'=> $tevolution_nav_menu),
				array('title'=>__('Monetize Your Site',ADMINDOMAIN),
				'link'=> site_url().'/wp-admin/admin.php?page=monetization',
				'desc'=>sprintf(__('Charge your users for posting their entries by adding <a href="%s/wp-admin/admin.php?page=monetization">price packages</a> on submission forms. There is wide range of selection of payment gateways to charge them too.',ADMINDOMAIN),site_url()),
				'status'=> $monetization_status),
				array('title'=>__('Email Setup ',ADMINDOMAIN),
				'link'=> site_url().'/wp-admin/admin.php?page=templatic_settings&tab=email',
				'desc'=>sprintf(__('Configure all the emails which will be sent to users and also the ones you will receive as an admin. Give all your emails a personal touch <a href="%s/wp-admin/admin.php?page=templatic_settings&tab=email"><b>editing their content</b></a>.',ADMINDOMAIN),site_url()),
				'status'=> true),
				array('title'=>__(' Configure other options',ADMINDOMAIN),
				'link'=>site_url().'/wp-admin/admin.php?page=templatic_settings',
				'desc'=>sprintf(__('You can change the appearance of your site with bulk of other options we provide. <a href="%s/wp-admin/admin.php?page=templatic_settings">Explore</a> these options to learn more about it. ',ADMINDOMAIN),site_url()),
				'status'=> $other_flag));
				if($setup_steps){ ?>
				<div class="templatic_setup_steps">
				<?php
				for($sp =0; $sp <= count($setup_steps); $sp++){
						if(isset($setup_steps[$sp]['title']) && $setup_steps[$sp]['title'] !=''){
				?>
						
						<div id="templatic_<?php echo str_replace('-','',$setup_steps[$sp]['title']);?>" class="widget_div">
							<div class="inside">
								<div class="t_module_desc">
									<div class="step_number"><?php echo $sp+1; ?></div>
									<div class="tev_setup_entry">
										<a href="<?php echo $setup_steps[$sp]['link']; ?>" title="<?php echo $setup_steps[$sp]['title']; ?>"><h3 class="hndle"><span><?php echo $setup_steps[$sp]['title']; ?></span></h3></a>
										<p class="mod_desc"><?php echo $setup_steps[$sp]['desc']; ?></p>
									</div>
									<div class="tev_status">
									<?php if( isset($setup_steps[$sp]['status']) && $setup_steps[$sp]['status'] !=''){ ?>
										<img src="<?php echo  str_replace('/classes','',plugin_dir_url( __FILE__ )); ?>images/1.png"/>
										<?php }else{ ?>
										<img src="<?php echo  str_replace('/classes','',plugin_dir_url( __FILE__ )); ?>images/0.png"/>
									<?php } ?>
									</div>
								</div>
							</div>
						</div>
				
				<?php } } ?>
				</div>
				<?php
				}
}
/*
 * Function Name: tevolution_extend_box
 * Return: display the extend plugins list
 */
add_action('tevolution_extend_box','tevolution_extend_box');
function tevolution_extend_box(){
	
	$buttontext =  __('Purchase and install',ADMINDOMAIN); 
	$activate =  __('Activate',ADMINDOMAIN);
	$deactivatetext =  __('Deactivate',ADMINDOMAIN);

	// Add stand alone plugin list in transient
	if ( false === ( $response = get_transient( '_tevolution_standalone_plugin') ) ) {
		$response = wp_remote_get( 'http://templatic.net/api/templatic-standalone-plugin.xml', array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true		
		    )
		);	
		set_transient( '_tevolution_standalone_plugin', $response, 12 * HOUR_IN_SECONDS );				
	}
	//finish stand alone plugin list in transient
	if( is_wp_error( $response ) ) {		 
			echo '<div id="standalone_plugin_error" class="metabox-holder wrapper widgets-holder-wrap">';
			printf(__('<strong>templatic.com connect Error</strong>: %s',ADMINDOMAIN), $response->get_error_message());		
		} else {
		  $data = $response['body'];
		}	
	
		if($data){
			$doc = new DOMDocument();
			@$doc->loadXML($data);
			$sourceNode = $doc->getElementsByTagName("templatic-standalone-plugin");
		} 
		
		if($sourceNode){
			
			foreach($sourceNode as $source)
			{
				$plugin_type = $source->getElementsByTagName("plugin-type");
							
				$plugin_name = $source->getElementsByTagName("plugin-name");
				$name = $plugin_name->item(0)->nodeValue; 
				
				$plugin_folder = $source->getElementsByTagName("plugin-folder");
				$pluginfolder = $plugin_folder->item(0)->nodeValue; 
				
				$plugin_image = $source->getElementsByTagName("plugin-image");
				$image = $plugin_image->item(0)->nodeValue; 
				
				$plugin_description = $source->getElementsByTagName("plugin-description");
				$short_description = $plugin_description->item(0)->nodeValue; 
				
				$plugin_path = $source->getElementsByTagName("plugin-path");
				$filepath = $plugin_path->item(0)->nodeValue; 
				
				$plugin_download_url = $source->getElementsByTagName("plugin-download-url");
				$donwload_url = $plugin_download_url->item(0)->nodeValue; 
				
				$plugin_argument = $source->getElementsByTagName("plugin-argument");
				$add_query_arg = $plugin_argument->item(0)->nodeValue; 
				
				$plugin_price = $source->getElementsByTagName("plugin-price");
				$price = $plugin_price->item(0)->nodeValue; 
				
				$plugin_type = $source->getElementsByTagName("plugin-type");
				$type = $plugin_type->item(0)->nodeValue; 
				
				if(strstr($type,',')){
					$type = explode(',',$type);
				}else{
					$type = array($type);
				}
				$filename= get_tmpl_plugin_directory().$filepath;
				
				$theme = wp_get_theme();
				$parent_theme = $theme['Template'];
				
				/* come only if directory theme start*/
				
				if($parent_theme =='Directory' && in_array('Directory',$type)){ 
					if(!file_exists($filename))
					{
					
					?>
					<div id="templatic_<?php echo str_replace('-','',$name);?>" class="widget_div">
						
						
						  <div class="t_dashboard_icon">
								<img class="dashboard_img" src="<?php echo $image;?>" />
						  </div>
						  <div class="inside">
							  <div class="t_module_desc"> 
								<h3 class="hndle"><span><?php echo $name; ?></span></h3>
								<p class="mod_desc"><?php echo $short_description;?></p>
							  </div>
							  <div id="publishing-action" class="settings_style">
								<a href="<?php echo $donwload_url;?>" class="button-primary" target="_blank"><?php echo $buttontext; ?></a>
                                        <p class="plugin_price"><?php echo $price;?></p>
							  </div>
						  </div>
					</div>
					<?php	
					
				}else if(is_plugin_active($filepath) || !is_plugin_active($filepath))
				{
					// delete payment gateway plugin
					if((isset($_REQUEST['deactivate']) && $_REQUEST['deactivate']!='') && (isset($_REQUEST['plugin']) && $_REQUEST['plugin']!="")){
						delete_option($_REQUEST['deactivate']);
						$current_plugin = get_option( 'active_plugins' );
						foreach($current_plugin as $key=>$current){
								if($current==$_REQUEST['plugin']){
									unset($current_plugin[$key]);
								}
						}						
						sort( $current_plugin );		  
						update_option( 'active_plugins', $current_plugin );		  						
						
					}
					
					?>
                         <div id="templatic_<?php echo str_replace('-','',$name);?>" class="widget_div">
						
						  
						  	 <div class="t_dashboard_icon">
								<img class="dashboard_img" src="<?php echo $image;?>" />
							  </div>
							<div class="inside">
							<div class="t_module_desc"> 
								<h3 class="hndle"><span><?php echo $name; ?></span></h3>
								<p class="mod_desc"><?php echo $short_description;?></p>
							</div>
                            <div id="publishing-action" class="settings_style">    
                            	<?php 						
						if(!get_option($add_query_arg)):?>                               	
                              	<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&tab=extend&activated=$add_query_arg&plugin=".$filepath."&true=1";?>" class="button-primary"><i class="fa fa-check"></i><?php echo $activate; ?> &rarr;</a>
                              <?php else:?>                                   
                                 <a class="button" href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&tab=extend&deactivate=$add_query_arg&plugin=".$filepath."&true=0";?>">
                                   <i class="fa fa-times"></i><?php echo $deactivatetext; ?> &rarr;</a>
                              <?php endif;?>
                             </div>
						  </div>
					</div>
				<?php
				}}else{
					if($parent_theme !='Directory' && in_array('Other',$type)){
						if(!file_exists($filename))
						{
						?>
						<div id="templatic_<?php echo str_replace('-','',$name);?>" class="widget_div">
							
							
								  <div class="t_dashboard_icon">
									<img class="dashboard_img" src="<?php echo $image;?>" />
								  </div>
							  <div class="inside">
								  <div class="t_module_desc"> 
									<h3 class="hndle"><span><?php echo $name; ?></span></h3>
									<p class="mod_desc"><?php echo $short_description;?></p>
								  </div>
								  <div id="publishing-action" class="settings_style">
									<a href="<?php echo $donwload_url;?>" class="button-primary" target="_blank"><?php echo $buttontext; ?></a>
											<p class="plugin_price"><?php echo $price;?></p>
								  </div>
							  </div>
						</div>
						<?php	
						
					}else if(is_plugin_active($filepath) || !is_plugin_active($filepath))
					{
						// delete payment gateway plugin
						if((isset($_REQUEST['deactivate']) && $_REQUEST['deactivate']!='') && (isset($_REQUEST['plugin']) && $_REQUEST['plugin']!="")){
							delete_option($_REQUEST['deactivate']);
							$current_plugin = get_option( 'active_plugins' );
							foreach($current_plugin as $key=>$current){
									if($current==$_REQUEST['plugin']){
										unset($current_plugin[$key]);
									}
							}						
							sort( $current_plugin );		  
							update_option( 'active_plugins', $current_plugin );		  						
							
						} ?>
							 <div id="templatic_<?php echo str_replace('-','',$name);?>" class="widget_div">
								<div class="t_dashboard_icon">
									<img class="dashboard_img" src="<?php echo $image;?>" />
								</div>
								<div class="inside">
									<div class="t_module_desc"> 
										<h3 class="hndle"><span><?php echo $name; ?></span></h3>
										<p class="mod_desc"><?php echo $short_description;?></p>
									</div>
									<div id="publishing-action" class="settings_style">    
										<?php if(!get_option($add_query_arg)):?>                               	
										<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&tab=extend&activated=$add_query_arg&plugin=".$filepath."&true=1";?>" class="button-primary"><i class="fa fa-check"></i><?php echo $activate; ?> &rarr;</a>
										<?php else:?>                                   
										<a class="button" href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&tab=extend&deactivate=$add_query_arg&plugin=".$filepath."&true=0";?>">
										   <i class="fa fa-times"></i><?php echo $deactivatetext; ?> &rarr;
										</a>
										<?php endif;?>
									 </div>
							    </div>
						</div>
					<?php
					}
				}
				}/* come only if directory theme end*/
			
					if((isset($_REQUEST['activated']) && $_REQUEST['activated']!="") &&(isset($_REQUEST['plugin']) && $_REQUEST['plugin']!=""))
					{
						$current = get_option( 'active_plugins' );
						$plugin = plugin_basename( trim($_REQUEST['plugin'] ) );	
						if ( !in_array( $plugin, $current ) ) {
						   $current[] = $plugin;
						   sort( $current );		  
						   update_option( 'active_plugins', $current );		  
						}
						update_option($_REQUEST['activated'],'Active');
						if($i==0):
						?>
                       	<script type="text/javascript">
							window.location='<?php echo "?page=templatic_system_menu&tab=extend&activated=".$_REQUEST['activated']."&true=1";?>';
						</script>                              
						<?php endif;
					}
			}
		}
}
/*
 * Function Name: tevolution_payment_gateway
 * Return: display the payment gatway plugin list
 */
add_action('tevolution_payment_gateway','tevolution_payment_gateway');
function tevolution_payment_gateway(){
	$buttontext =  __('Purchase and install',ADMINDOMAIN); 
	$activate =  __('Activate',ADMINDOMAIN);
	$deactivatetext =  __('Deactivate',ADMINDOMAIN);
	// Add payment gateway list in transient
	if ( false === ( $response = get_transient( '_tevolution_payment_gateways') ) ) {
		$response = wp_remote_get( 'http://templatic.net/api/templatic-paymentgateways-plugin.xml', array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'user-agent' => 'WordPress/'. @$wp_version .'; '. home_url(),
			'cookies' => array()	
		    )
		);
		
		set_transient( '_tevolution_payment_gateways', $response, 12 * HOUR_IN_SECONDS );				
	}
	//finish payment gateway listing in transient
	if( is_wp_error( $response ) ) {		 
		echo '<div id="standalone_plugin_error" class="metabox-holder wrapper widgets-holder-wrap">';
		printf(__('<strong>templatic.com connect Error</strong>: %s',ADMINDOMAIN), $response->get_error_message());		
	} else {
		$data = $response['body'];
	}
		
	if($data){
		$doc = new DOMDocument();
		@$doc->loadXML($data);
		$sourceNode = $doc->getElementsByTagName("templatic-standalone-plugin");
	}
	if($sourceNode){
		foreach($sourceNode as $source)
		{
			$plugin_name = $source->getElementsByTagName("plugin-name");
			$name = $plugin_name->item(0)->nodeValue; 
			
			$plugin_folder = $source->getElementsByTagName("plugin-folder");
			$pluginfolder = $plugin_folder->item(0)->nodeValue; 
			
			$plugin_image = $source->getElementsByTagName("plugin-image");
			$image = $plugin_image->item(0)->nodeValue; 
			
			$plugin_description = $source->getElementsByTagName("plugin-description");
			$short_description = $plugin_description->item(0)->nodeValue; 
			
			$plugin_path = $source->getElementsByTagName("plugin-path");
			$filepath = $plugin_path->item(0)->nodeValue; 
			
			$plugin_download_url = $source->getElementsByTagName("plugin-download-url");
			$donwload_url = $plugin_download_url->item(0)->nodeValue; 
			
			$plugin_argument = $source->getElementsByTagName("plugin-argument");
			$add_query_arg = $plugin_argument->item(0)->nodeValue; 
			
			$plugin_price = $source->getElementsByTagName("plugin-price");
			$price = $plugin_price->item(0)->nodeValue; 
			
			$filename= get_tmpl_plugin_directory().$filepath;
			if(!file_exists($filename))
			{
				?>
				<div id="templatic_<?php echo str_replace('-','',$name);?>" class="widget_div">
					  <div class="t_dashboard_icon">
						<img class="dashboard_img" src="<?php echo $image;?>" />
					  </div>
					  <div class="inside">
						  <div class="t_module_desc"> 
							<h3 class="hndle"><span><?php echo $name; ?></span></h3>
							<p class="mod_desc"><?php echo $short_description;?></p>
						  </div>
						  <div id="publishing-action" class="settings_style">
							<a href="<?php echo $donwload_url;?>" class="button-primary" target="_blank"><?php echo $buttontext; ?></a>
							<p class="plugin_price"><?php echo $price;?></p>
						  </div>
					  </div>
				</div>
				<?php	
				
			}else if(is_plugin_active($filepath) || !is_plugin_active($filepath))
			{
				// delete payment gateway plugin
				if((isset($_REQUEST['deactivate']) && $_REQUEST['deactivate']!='') && (isset($_REQUEST['plugin']) && $_REQUEST['plugin']!="")){
					delete_option($_REQUEST['deactivate']);
					$current_plugin = get_option( 'active_plugins' );
					foreach($current_plugin as $key=>$current){
							if($current==$_REQUEST['plugin']){
								unset($current_plugin[$key]);
							}
					}						
					sort( $current_plugin );		  
					update_option( 'active_plugins', $current_plugin );		  						
					
				}
				?>
				<div id="templatic_<?php echo str_replace('-','',$name);?>" class="widget_div">
					 <div class="t_dashboard_icon">
						<img class="dashboard_img" src="<?php echo $image;?>" />
					  </div>
					<div class="inside">
					<div class="t_module_desc"> 
						<h3 class="hndle"><span><?php echo $name; ?></span></h3>
						<p class="mod_desc"><?php echo $short_description;?></p>
					</div>
				   <div id="publishing-action" class="settings_style">    
					<?php 						
					if(!is_plugin_active($filepath)):?>                               	
						<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&tab=payment-gateways&activated=$add_query_arg&plugin=".$filepath."&true=1";?>" class="button-primary"><i class="fa fa-check"></i><?php echo $activate; ?> &rarr;</a>
					<?php else:?>                                   
					   <a class="button" href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&tab=payment-gateways&deactivate=$add_query_arg&plugin=".$filepath."&true=0";?>">
						<i class="fa fa-times"></i>
						 <?php echo $deactivatetext; ?> &rarr;
						</a>
					<?php endif;?>
				    </div>
					  </div>
				</div>
				<?php
				if((isset($_REQUEST['activated']) && $_REQUEST['activated']!="") && (isset($_REQUEST['plugin']) && $_REQUEST['plugin']!=""))
				{
					$current = get_option( 'active_plugins' );
					$plugin = plugin_basename( trim($_REQUEST['plugin'] ) );	
					if ( !in_array( $plugin, $current ) ) {
					   $current[] = $plugin;
					   sort( $current );		  
					   update_option( 'active_plugins', $current );		  
					}
					update_option($_REQUEST['activated'],'Active');
					if($i==0):
					?>
					<script type="text/javascript">
						window.location='<?php echo "?page=templatic_system_menu&tab=payment-gateways&activated=".$_REQUEST['activated']."&true=1";?>';
					</script>                              
					<?php endif;
				}
			}
		}
	}
}
?>