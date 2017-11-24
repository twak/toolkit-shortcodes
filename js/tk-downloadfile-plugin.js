(function() {
	tinymce.PluginManager.add('downloadfile', function( editor, url ) {
		var sh_tag = 'downloadfile';

		//helper functions 
		function getAttr(s, n) {
			n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
			return n ?  window.decodeURIComponent(n[1]) : '';
		};

		function html( cls, data ,con) {
			var placeholder = url + '/mce-img/downloadfile/' + getAttr(data,'type') + '.png';
			data = window.encodeURIComponent( data );
			content = window.encodeURIComponent( con );

			return '<img class="mceItem ' + sh_tag + '" src="' + placeholder + '" ' + 'data-dl-attr="' + data + '" data-dl-content="'+ con+'" data-mce-resize="false" data-mce-placeholder="1" />';
		}

		function replaceShortcodes( content ) {
			return content.replace( /\[downloadfile([^\]]*)\]([^\]]*)\[\/downloadfile\]/g, function( all,attr,con) {
				return html( 'wp-downloadfile', attr , con);
			});
		}

		function restoreShortcodes( content ) {
			return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(?:<\/p>)*/g, function( match, image ) {
				var data = getAttr( image, 'data-dl-attr' );
				var con = getAttr( image, 'data-dl-content' );

				if ( data ) {
					return '<p>[' + sh_tag + data + ']' + con + '[/'+sh_tag+']</p>';
				}
				return match;
			});
		}

		//add popup
		editor.addCommand('downloadfile_popup', function(ui, v) {
			//setup defaults
			var url = '';
			if (v.url)
				url = v.url;
			var type = 'pdf';
			if (v.type)
				type = v.type;
			var content = '';
			if (v.content)
				content = v.content;

			editor.windowManager.open( {
				title: 'File Download Shortcode',
				body: [
					{
						type: 'textbox',
						name: 'content',
						label: 'Link text',
						value: content,
						tooltip: 'Text to link to the file',
                        minWidth: 300,
					},
					{
						type: 'textbox',
						name: 'url',
						label: 'File URL',
						value: url,
						tooltip: 'Include full URL',
                        minWidth: 300,
					},
					{
						type: 'listbox',
						name: 'type',
						label: 'File Type',
						value: type,
						'values': [
                            {text: 'Word document', value: 'word'},
                            {text: 'Powerpoint file', value: 'powerpoint'},
                            {text: 'Excel spreadsheet', value: 'excel'},
                            {text: 'Zip archive', value: 'zip'},
                            {text: 'PDF file', value: 'pdf'}
						],
						tooltip: 'Select the type of file'
					}
				],
				onsubmit: function( e ) {
					var shortcode_str = '[' + sh_tag + ' type="' + e.data.type + '" url="' + e.data.url + '"]' + e.data.content + '[/' + sh_tag + ']';
					//insert shortcode to tinymce
					editor.insertContent( shortcode_str);
				}
			});
	      	});

		//add button
		editor.addButton('downloadfile', {
			icon: 'tk_downloadfile',
			tooltip: 'Add File Download',
			onclick: function() {
				editor.execCommand('downloadfile_popup','',{
					url : '',
					type : 'pdf',
					content: ''
				});
			}
		});

		//replace from shortcode to an image placeholder
		editor.on('BeforeSetcontent', function(event){ 
			event.content = replaceShortcodes( event.content );
		});

		//replace from image placeholder to shortcode
		editor.on('GetContent', function(event){
			event.content = restoreShortcodes(event.content);
		});

		//open popup on placeholder double click
		editor.on('click',function(e) {
			if ( e.target.nodeName == 'IMG' && e.target.className.indexOf('downloadfile') > -1 ) {
				var attr = e.target.attributes['data-dl-attr'].value;
				attr = window.decodeURIComponent(attr);
				var content = e.target.attributes['data-dl-content'].value;
				editor.execCommand('downloadfile_popup','',{
					url : getAttr(attr,'url'),
					type   : getAttr(attr,'type'),
					content: content
				});
			}
		});
	});
})();