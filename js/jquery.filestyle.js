/* Style File - jQuery plugin for styling file input elements
 * Copyright (c) 2007-2008 Mika Tuupola
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 * Based on work by Shaun Inman
 * http://www.shauninman.com/archive/2007/09/10/styling_file_inputs_with_css_and_the_dom
 * Revision: $Id: jquery.filestyle.js 303 2008-01-30 13:53:24Z tuupola $
 */
(function($) {
    $.fn.filestyle = function(options) {
        /* TODO: This should not override CSS. */
        return this.each(function() {
            var self = this;
			if(upload_single_title!=""){
				upload_single_title = upload_single_title;
			}else{
				upload_single_title = "Upload Image";
			}
			
			var wrapper = $('<div id="upload_button_'+self.id+'" class="upload button secondary_btn"> <span class="upload_title">'+upload_single_title+'</span>').css({"display": "inline","position": "relative","overflow": "hidden"});
			var filename = $('<input type="hidden" class="file">').addClass($(self).attr("class")).css({"display": "inline"});
			
			$(self).before(filename);
			$(self).wrap(wrapper);
			$(self).css({"position": "absolute","display": "inline","cursor": "pointer","opacity": "0","left": "0","right": "0","top": "0","bottom": "0"});
			
			$('#upload_button_'+self.id).after('<span class="file_value'+self.id+'" style="display:inline-block;margin-left:5px"></span>');
			$(self).bind("change", function() {
				var file_extension = $(self).val().search('.php');
				var file_extension_js = $(self).val().search('.js');
				var file_extension_txt = $(self).val().search('.txt');
				var file_extension_docx = $(self).val().search('.docx');
				if(file_extension !=-1 || file_extension_js != -1 || file_extension_txt != -1 || file_extension_docx != -1)
				{
					$('.file_value'+self.id).addClass('error');
					$('.file_value'+self.id).text("Added file type can't be uploaded");
					$(self).val('');
					return false;
				}
				if($('.file_value'+self.id).hasClass('error'))
				{
					$('.file_value'+self.id).removeClass('error');
				}
				filename.val($(self).val());
				$('.file_value'+self.id).html($(self).val().split('\\').pop());
            	});      
        });
    };
})(jQuery);
jQuery(document).ready(function() {
	jQuery("input[type=file]").filestyle();
}); 