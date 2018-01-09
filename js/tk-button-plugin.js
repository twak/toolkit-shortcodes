/* global tinyMCE */
(function($){
    var media = wp.media, shortcode_string = 'tk_button';
    wp.mce = wp.mce || {};
    wp.mce.tk_button = {
        shortcode_data: {},
        template: media.template( 'tk_button_shortcode' ),
        getContent: function() {
            var options = this.shortcode.attrs.named;
            return this.template(options);
        },
        View: { // before WP 4.2:
            template: media.template( 'tk_button_shortcode' ),
            postID: $('#post_ID').val(),
            initialize: function( options ) {
                this.shortcode = options.shortcode;
                wp.mce.tk_button.shortcode_data = this.shortcode;
            },
            getHtml: function() {
                var options = this.shortcode.attrs.named;
                return this.template(options);
            }
        },
        edit: function( data ) {
            var shortcode_data = wp.shortcode.next(shortcode_string, data);
            var values = shortcode_data.shortcode.attrs.named;
            wp.mce.tk_button.popupwindow(tinyMCE.activeEditor, values);
        },
        // this is called from our tinymce plugin, also can call from our "edit" function above
        // wp.mce.boutique_banner.popupwindow(tinyMCE.activeEditor, "bird");
        popupwindow: function(editor, values, onsubmit_callback){
            values = values || [];
            if(typeof onsubmit_callback !== 'function'){
                onsubmit_callback = function( e ) {
                    // Insert content when the window form is submitted (this also replaces during edit, handy!)
                    var args = {
                            tag     : shortcode_string,
                            attrs : {
                                title    : e.data.title,
                                link     : e.data.link,
                                type     : e.data.type,
                                block    : e.data.block
                            }
                        };
                    editor.insertContent( wp.shortcode.string( args ) );
                };
            }
            editor.windowManager.open( {
                title: 'Button Shortcode',
                body: [
                    {
                        type: 'textbox',
                        name: 'title',
                        label: 'Button text',
                        value: values.title,
                        tooltip: 'Cannot be left blank!'
                    },
                    {
                        type: 'textbox',
                        name: 'link',
                        label: 'Button link URL',
                        value: values.link,
                        tooltip: 'Cannot be left blank!'
                    },
                    {
                        type: 'listbox',
                        name: 'type',
                        label: 'Button Type',
                        value: values.type,
                        'values': [
                            {text: 'Default', value: 'default'},
                            {text: 'Info', value: 'info'},
                            {text: 'Primary', value: 'primary'},
                            {text: 'Success', value: 'success'},
                            {text: 'Warning', value: 'warning'},
                            {text: 'Danger', value: 'danger'},
                            {text: 'Purple', value: 'purple'}
                        ],
                        tooltip: 'Select the type of button you want'
                    },
                    {
                        type: 'checkbox',
                        name: 'block',
                        label: 'Make the button a block? (100% width)',
                        checked: (values.block.toLowerCase() == 'false'? false: true) 
                    },
                ],
                onsubmit: onsubmit_callback
            } );
        }
    };
    wp.mce.views.register( shortcode_string, wp.mce.tk_button );
    tinymce.PluginManager.add( shortcode_string, function( editor ) {
        editor.addButton( shortcode_string, {
            text: 'Button',
            icon: 'tk_button',
            onclick: function() {
                wp.mce.tk_button.popupwindow(editor);
            }
        });
    });
}(jQuery));