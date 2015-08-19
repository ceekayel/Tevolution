<?php
/*
 * for display rating add rating option in tevolution general settings
 */
global $wp_query,$wpdb;
/* Add action 'templatic_general_setting_data' for display rating*/
add_action('after_detail_page_setting','rating_setting_data',12);
/*
	Add the rating options in tevolution
 */
function rating_setting_data($column)
{
	$tmpdata = get_option('templatic_settings');
	if(!is_plugin_active('Templatic-MultiRating/multiple_rating.php')){
	?>           
		<tr>
			<th><?php echo __('Ratings','templatic-admin');?></th>
			<td>
			<div class="input-switch">
				<input id="rating_yes" type="checkbox" name="templatin_rating" value="yes" <?php if(@$tmpdata['templatin_rating']=='yes')echo 'checked';?> />
				<label for="rating_yes">&nbsp;<?php echo __('Enable','templatic-admin');?></label> 
			</div>                   
				<p class="description"><?php echo sprintf(__('Allows visitors to star rate a listing when leaving comments. For more comprehensive ratings check out the  "Multi Rating" add-on from %s','templatic-admin'),'<a href="http://templatic.com/directory-add-ons/star-rating-plugin-multirating/" title="Multi Rating" target="_blank">Here</a>'); ?></p>
			</td>
		</tr>
		<tr>
			<th><?php echo __('Force Ratings','templatic-admin');?></th>
			<td>
			<div class="input-switch">
				<input id="validate_rating" type="checkbox" name="validate_rating" value="yes" <?php if(@$tmpdata['validate_rating']=='yes')echo 'checked';?> />	
				<label for="validate_rating">&nbsp;<?php echo __('Enable','templatic-admin');?></label>
			</div>
				<p class="description"><?php echo __('If enabled, visitors won&#39;t be able to submit a comment without entering a rating first. ','templatic-admin'); ?></p>
			</td>
		</tr>
    <?php
	}
}
$tmpdata = get_option('templatic_settings');
if($tmpdata){
	if(isset($tmpdata['templatin_rating']) && $tmpdata['templatin_rating']=='yes')
	{
		if(file_exists(TEMPL_MONETIZE_FOLDER_PATH . 'templatic-ratings/templatic_post_rating.php'))
		{
			include_once (TEMPL_MONETIZE_FOLDER_PATH . 'templatic-ratings/templatic_post_rating.php');
		}
		if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-ratings/language.php'))
		{
			include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-ratings/language.php");
		}
	}
}
?>