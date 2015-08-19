<?php 
/*
 * rating html for comment form
 */
global $post,$rating_image_on,$rating_image_off,$rating_table_name;
$tmpdata = get_option('templatic_settings');
?>
<script src="<?php echo plugin_dir_url( __FILE__ ).'post_rating.js';?>" type="text/javascript"></script>
<?php
	echo '<ul>';
	for($i=1;$i<=POSTRATINGS_MAX;$i++)
	{
		if($i==1){$rating_text = $i.' '.__('rating','templatic');}else{$rating_text = $i.' '.__('ratings','templatic');}
		echo '<li  id="rating_'.$post->ID.'_'.$i.'" onmouseover="current_rating_star_on(\''.$post->ID.'\',\''.$i.'\',\''.$rating_text.'\');" onmousedown="current_rating_star_off(\''.$post->ID.'\',\''.$i.'\');" >';
		echo '<i class="fa fa-star rating-off" ></i>';							
		echo '</li>';
	}
	echo '</ul>';
	echo '<span class="rate-comment" id="ratings_'.$post->ID.'_text" style="display:inline-table; position:relative; top:-2px; padding-left:10px; " ></span>';
	echo '<input type="hidden" name="post_id" id="rating_post_id" value="'.$post->ID.'" />';
	echo '<input type="hidden" name="post_'.$post->ID.'_rating" id="post_'.$post->ID.'_rating" value="" />';
 	echo '<script type="text/javascript">current_rating_star_on(\''.$post->ID.'\',0,\'0 '.__('ratings','templatic').'\');</script>';
?>