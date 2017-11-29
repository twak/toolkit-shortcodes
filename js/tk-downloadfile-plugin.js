/* global tinyMCE */
(function($){
    var media = wp.media, shortcode_string = 'downloadfile';
    wp.mce = wp.mce || {};
    wp.mce.downloadfile = {
        shortcode_data: {},
        template: media.template( 'downloadfile_shortcode' ),
        getContent: function() {
            var options = this.shortcode.attrs.named;
            options.content = this.shortcode.content;
            return this.template(options);
        },
        View: { // before WP 4.2:
            template: media.template( 'downloadfile_shortcode' ),
            postID: $('#post_ID').val(),
            initialize: function( options ) {
                this.shortcode = options.shortcode;
                wp.mce.downloadfile.shortcode_data = this.shortcode;
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
            wp.mce.downloadfile.popupwindow(tinyMCE.activeEditor, values);
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
                                url      : e.data.url,
                                type     : e.data.type
                            }
                        };
                    editor.insertContent( wp.shortcode.string( args ) );
                };
            }
            editor.windowManager.open( {
                title: 'Panel Shortcode',
                body: [
                    {
                        type: 'textbox',
                        name: 'url',
                        label: 'File URL',
                        value: values.url,
                        tooltip: 'Full URL of file'
                    },
                    {
                        type: 'listbox',
                        name: 'type',
                        label: 'File Type',
                        value: values.type,
                        'values': [
                            {text: 'PDF file', value: 'pdf'},
                            {text: 'Word document', value: 'word'},
                            {text: 'Powerpoint presentation', value: 'powerpoint'},
                            {text: 'Excel Spreadsheet', value: 'excel'},
                            {text: 'Zip archive', value: 'zip'}
                        ],
                        tooltip: 'Select the type of file'
                    },
                    {
                        type: 'textbox',
                        name: 'content',
                        label: 'Link text',
                        value: values.content,
                        minWidth: 300
                    }
                ],
                onsubmit: onsubmit_callback
            } );
        }
    };
    wp.mce.views.register( shortcode_string, wp.mce.downloadfile );
    tinymce.PluginManager.add( shortcode_string, function( editor ) {
        editor.addButton( shortcode_string, {
            tooltip: 'File Download',
            icon: 'downloadfile',
            onclick: function() {
                wp.mce.downloadfile.popupwindow(editor);
            }
        });
    });
}(jQuery));