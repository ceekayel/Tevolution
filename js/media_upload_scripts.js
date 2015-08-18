/*
*	TypeWatch 2.2
*
*	Examples/Docs: github.com/dennyferra/TypeWatch
*	
*  Copyright(c) 2013 
*	Denny Ferrassoli - dennyferra.com
*   Charles Christolini
*  
*  Dual licensed under the MIT and GPL licenses:
*  http://www.opensource.org/licenses/mit-license.php
*  http://www.gnu.org/licenses/gpl.html
*/
(function(jQuery) {
	
	jQuery.ajaxSetup({
			async: true
		});
	jQuery.fn.typeWatch = function(o) {		
		// The default input types that are supported
		var _supportedInputTypes =
			['TEXT', 'TEXTAREA', 'PASSWORD', 'TEL', 'SEARCH', 'URL', 'EMAIL', 'DATETIME', 'DATE', 'MONTH', 'WEEK', 'TIME', 'DATETIME-LOCAL', 'NUMBER', 'RANGE'];
		// Options
		var options = jQuery.extend({
			wait: 750,
			callback: function() { },
			highlight: true,
			captureLength: 2,
			inputTypes: _supportedInputTypes
		}, o);
		function checkElement(timer, override) {
			var value = jQuery(timer.el).val();
			// Fire if text >= options.captureLength AND text != saved text OR if override AND text >= options.captureLength
			if ((value.length >= options.captureLength && value.toUpperCase() != timer.text)
				|| (override && value.length >= options.captureLength))
			{
				timer.text = value.toUpperCase();
				timer.cb.call(timer.el, value);
			}
		};
		function watchElement(elem) {
			var elementType = elem.type.toUpperCase();
			if (jQuery.inArray(elementType, options.inputTypes) >= 0) {
				// Allocate timer element
				var timer = {
					timer: null,
					text: jQuery(elem).val().toUpperCase(),
					cb: options.callback,
					el: elem,
					wait: options.wait
				};
				// Set focus action (highlight)
				if (options.highlight) {
					jQuery(elem).focus(
						function() {
							this.select();
						});
				}
				// Key watcher / clear and reset the timer
				var startWatch = function(evt) {
					var timerWait = timer.wait;
					var overrideBool = false;
					var evtElementType = this.type.toUpperCase();
					// If enter key is pressed and not a TEXTAREA and matched inputTypes
					if (typeof evt.keyCode != 'undefined' && evt.keyCode == 13 && evtElementType != 'TEXTAREA' && jQuery.inArray(evtElementType, options.inputTypes) >= 0) {
						timerWait = 1;
						overrideBool = true;
					}
					var timerCallbackFx = function() {
						checkElement(timer, overrideBool)
					}
					// Clear timer					
					clearTimeout(timer.timer);
					timer.timer = setTimeout(timerCallbackFx, timerWait);
				};
				jQuery(elem).on('keydown paste cut input', startWatch);
			}
		};
		// Watch Each Element
		return this.each(function() {
			watchElement(this);
		});
	};
})(jQuery);

// Uploading files
var file_frame;
 
jQuery(document).on('click', '.upload_file_button', function (event) {
  var input_id =  jQuery(this).attr('id');
  var data_id = jQuery(this).attr('data-id');
	event.preventDefault();
 
	// If the media frame already exists, reopen it.
	
    wp.media.model.settings.post.id = '';
	// Create the media frame.
	file_frame = wp.media.frames.downloadable_file = wp.media({
		title: 'Choose file',
		library : { type : 'image'},
		button: {
			text: 'Set As ' + input_id
		},
		multiple: false
	});
 
	// When an image is selected, run a callback.
	file_frame.on('select', function () {
		var attachment = file_frame.state().get('selection').first().toJSON();
		jQuery('#'+data_id).val(attachment.url);
		jQuery('.'+data_id+'-sm-preview').append('<img src="' +  attachment.url+ '" />');
 
	});
 	// Finally, open the modal.
	file_frame.open();
});

/* multi image uploader script for submit form */

jQuery(document).ready(function($){
	// Uploading files
	var image_gallery_frame;
	var $image_gallery_ids = jQuery('#tevolution_image_gallery');
	var $images_gallery = jQuery('#images_gallery_container ul.images_gallery');
	var btn_name =  jQuery("#tmpl-upload-img").attr('data-name');
	var btn_text =  jQuery("#tmpl-upload-img").attr('data-text');
	var dlt_text =  jQuery("#tmpl-upload-img").attr('data-dtext');
	var dlt_lbl =  jQuery("#tmpl-upload-img").attr('data-dlbl');
	jQuery('.add_tevolution_images').on( 'click', 'button', function( event ) {
		var $el = $(this);
		var attachment_ids = $image_gallery_ids.val();
		event.preventDefault();
		// If the media frame already exists, reopen it.
		if ( image_gallery_frame ) {
			image_gallery_frame.open();
			return;
		}
		// Create the media frame.
		image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
			// Set the title of the modal.
			title: btn_name,
			button: {
				text: btn_text,
			},
			multiple: true
		});
		// When an image is selected, run a callback.
		image_gallery_frame.on( 'select', function() {
			var selection = image_gallery_frame.state().get('selection');
			selection.map( function( attachment ) {
				attachment = attachment.toJSON();
				if ( attachment.id ) {
					attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;
					$images_gallery.append('\
						<li class="image" data-attachment_id="' + attachment.id + '">\
							<img src="' + attachment.url + '" />\
							<ul class="actions">\
								<li><a href="#" class="delete" title="'+dlt_text+'">'+dlt_lbl+'</a></li>\
							</ul>\
						</li>');
				}
			} );
			$image_gallery_ids.val( attachment_ids );
		});
		// Finally, open the modal.
		image_gallery_frame.open();
	});
	// Image ordering
	$images_gallery.sortable({
		items: 'li.image',
		cursor: 'move',
		scrollSensitivity:40,
		forcePlaceholderSize: true,
		forceHelperSize: false,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start:function(event,ui){
			ui.item.css('background-color','#f6f6f6');
		},
		stop:function(event,ui){
			ui.item.removeAttr('style');
		},
		update: function(event, ui) {
			var attachment_ids = '';
			$('#images_gallery_container ul li.image').css('cursor','default').each(function() {
				var attachment_id = jQuery(this).attr( 'data-attachment_id' );
				attachment_ids = attachment_ids + attachment_id + ',';
			});
			$image_gallery_ids.val( attachment_ids );
		}
	});
	// Remove images
	jQuery('#images_gallery_container').on( 'click', 'a.delete', function() {
		
		jQuery(this).closest('li.image').remove();
		var attachment_ids = '';
		jQuery('#images_gallery_container ul li.image').css('cursor','default').each(function() {
			var attachment_id = jQuery(this).attr( 'data-attachment_id' );
			attachment_ids = attachment_ids + attachment_id + ',';
		});						
		$image_gallery_ids.val( attachment_ids );
		var delete_id=jQuery(this).closest('li.image ul.actions li a').attr('id');
		if(delete_id!=''){
			jQuery.ajax({
				url: ajaxUrl,
				type:'POST',
				data:'action=delete_gallery_image&image_id=' + delete_id,
				success:function(results) {
				}
			});
		}
		return false;
	} );
});