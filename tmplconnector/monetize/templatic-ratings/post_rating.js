/*
 * js to add selected rating count in hidden field and validation to select rating
 */
post_rating_max = POSTRATINGS_MAX;
function current_rating_star_on(post_id, rating, rating_text) {
	
	for(i=1;i<=post_rating_max;i++)
	{
		document.getElementById('rating_' + post_id + '_' + i).innerHTML = RATING_IMAGE_OFF;
	}
	for(i=1;i<=rating;i++)
	{
		document.getElementById('rating_' + post_id + '_' + i).innerHTML = RATING_IMAGE_ON;
	}
	document.getElementById('ratings_' + post_id + '_text').innerHTML = rating_text;
	document.getElementById('post_' + post_id + '_rating').value = rating;
}
function current_rating_star_off(post_id, rating) {
}
if(VALIDATION_RATING)
{
	jQuery(document).ready(function($) {
		jQuery("#commentform").submit(function(){
			var post_id = document.getElementById('rating_post_id').value;
			var comment_parent  = document.getElementById('comment_parent').value;
			if(jQuery('#post_' + post_id + '_rating').val() <= 0 && comment_parent <=0 )
			{
				jQuery( "#commentform .error" ).remove( "" );
				jQuery( ".comment-form-comment" ).after( "<span class='error'>"+VALIDATION_MESSAGE+"</p>" );
				return false;
			}
			return true;
		});
	});
}
