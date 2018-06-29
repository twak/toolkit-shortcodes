/* global tinyMCE */
(function($){
    var media = wp.media, shortcode_string = 'tk_panel';
    wp.mce = wp.mce || {};
    wp.mce.tk_panel = {
        shortcode_data: {},
        template: media.template( 'tk_panel_shortcode' ),
        getContent: function() {
            var options = this.shortcode.attrs.named;
            options.content = this.shortcode.content;
            return this.template(options);
        },
        View: { // before WP 4.2:
            template: media.template( 'tk_panel_shortcode' ),
            postID: $('#post_ID').val(),
            initialize: function( options ) {
                this.shortcode = options.shortcode;
                wp.mce.tk_panel.shortcode_data = this.shortcode;
            },
            getHtml: function() {
                var options = this.shortcode.attrs.named;
                options.content = this.shortcode.content;
                return this.template(options);
            }
        },
        edit: function( data ) {
            var shortcode_data = wp.shortcode.next(shortcode_string, data);
            var values = shortcode_data.shortcode.attrs.named;
            values.content = shortcode_data.shortcode.content;
            wp.mce.tk_panel.popupwindow(tinyMCE.activeEditor, values);
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
                            content : e.data.content,
                            attrs : {
                                title    : e.data.title,
                                footer   : e.data.footer,
                                type     : e.data.type
                            }
                        };
                    editor.insertContent( wp.shortcode.string( args ) );
                };
            }
            editor.windowManager.open( {
                title: 'Panel',
                body: [
                    {
                        type: 'textbox',
                        name: 'title',
                        label: 'Panel title',
                        value: values.title,
                        tooltip: 'Leave blank for none'
                    },
                    {
                        type: 'listbox',
                        name: 'type',
                        label: 'Panel Type',
                        value: values.type,
                        'values': [
                            {text: 'Default', value: 'default'},
                            {text: 'Info', value: 'info'},
                            {text: 'Primary', value: 'primary'},
                            {text: 'Success', value: 'success'},
                            {text: 'Warning', value: 'warning'},
                            {text: 'Danger', value: 'danger'}
                        ],
                        tooltip: 'Select the type of panel you want'
                    },
                    {
                        type: 'textbox',
                        name: 'content',
                        label: 'Panel Content',
                        value: values.content,
                        multiline: true,
                        minWidth: 300,
                        minHeight: 100
                    },
                    {
                        type: 'textbox',
                        name: 'footer',
                        label: 'Panel Footer',
                        value: values.footer,
                        tooltip: 'Leave blank for none'
                    }
                ],
                onsubmit: onsubmit_callback
            } );
        }
    };
    wp.mce.views.register( shortcode_string, wp.mce.tk_panel );
    tinymce.PluginManager.add( shortcode_string, function( editor ) {
        editor.addButton( shortcode_string, {
            text: 'Panel',
            icon: 'tk_panel',
            onclick: function() {
                wp.mce.tk_panel.popupwindow(editor);
            }
        });
    });
}(jQuery));