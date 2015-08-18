<?php 
global $post,$rating_image_on,$rating_image_off,$rating_table_name;
$tmpdata = get_option('templatic_settings');
?>
<script type="text/javascript"> 
var RATING_IMAGE_ON = '<?php echo $rating_image_on;?>';
var RATING_IMAGE_OFF = '<?php echo $rating_image_off;?>';
var POSTRATINGS_MAX = '<?php echo POSTRATINGS_MAX;?>';
<?php if($tmpdata['validate_rating'] == 'yes')
{?>
	var VALIDATION_MESSAGE = '<?php _e("Please give rating",DOMAIN);?>';
	var VALIDATION_RATING = 1;
<?php } else {?>var VALIDATION_RATING = 0; <?php } ?>
</script>
<script src="<?php echo plugin_dir_url( __FILE__ ).'post_rating.js';?>" type="text/javascript"></script>
<?php
	for($i=1;$i<=POSTRATINGS_MAX;$i++)
	{
		if($i==1){$rating_text = $i.' '.__('rating',DOMAIN);}else{$rating_text = $i.' '.__('ratings',DOMAIN);}
		
		echo '<img src="'.$rating_image_off.'" class="rating_img" onmouseover="current_rating_star_on(\''.$post->ID.'\',\''.$i.'\',\''.$rating_text.'\');" onmousedown="current_rating_star_off(\''.$post->ID.'\',\''.$i.'\');" id="rating_'.$post->ID.'_'.$i.'"  alt="" />';							
	}
	echo '<span id="ratings_'.$post->ID.'_text" style="display:inline-table; position:relative; top:-2px; padding-left:10px; " ></span>';
	echo '<input type="hidden" name="post_id" id="rating_post_id" value="'.$post->ID.'" />';
	echo '<input type="hidden" name="post_'.$post->ID.'_rating" id="post_'.$post->ID.'_rating" value="" />';
 	echo '<script type="text/javascript">current_rating_star_on(\''.$post->ID.'\',0,\'0 '.__('rating',DOMAIN).'\');</script>';
 //POST RATING 
 
?> 
