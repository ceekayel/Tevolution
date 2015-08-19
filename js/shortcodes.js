/*
 * add script for tevolution shortcode
 */
jQuery(document).ready(function($) {


    tinymce.PluginManager.add('tevolution_shortcodes', function( editor, url ) {
        editor.addButton( 'tevolution_shortcodes', {
            title: 'Tevolution Shortcodes',
            type: 'menubutton',
            icon: 'tevolution_shortcodes_icon',
            menu: [
					{
						text: 'Submit Form',
						value: '[submit_form post_type="your post type slug"]',
						onclick: function() {
							editor.insertContent(this.value());
						}
					},
					{
						text: 'Registration Form',
						value: '[tevolution_register]',
						onclick: function() {
							editor.insertContent(this.value());
						}
					},
					{
						text: 'Login Form',
						value: '[tevolution_login]',
						onclick: function() {
							editor.insertContent(this.value());
						}
					},
					{
						text: 'Edit Profile Form/Page',
						value: '[tevolution_profile]',
						onclick: function() {
							editor.insertContent(this.value());
						}
					},					
					{
						text: 'User Listing',
						value: '[tevolution_author_list role="" users_per_page="" display_author_has_no_post="0"]',
						onclick: function() {
							editor.insertContent(this.value());
						}
					},
					{
						text: 'All Taxonomies Map',
						value: '[tevolution_listings_map post_type="your post types,your post type" image="thumbnail" latitude="" longitude="" map_type="ROADMAP" zoom_level="13"]',
						onclick: function() {
							editor.insertContent(this.value());
						}
					},
					{
						text: 'Map',
						value: '[map_page post_type="your post type slug" image="thumbnail" latitude="" longitude="" map_type="ROADMAP" map_display="ROADMAP" zoom_level="13" height="500"]',
						onclick: function() {
							editor.insertContent(this.value());
						}
					},
           ],
        
        });
    });



});
