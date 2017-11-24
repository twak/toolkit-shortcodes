(function() {
	tinymce.PluginManager.add('tk_panel', function( editor, url ) {
		var sh_tag = 'tk_panel';

		//helper functions 
		function getAttr(s, n) {
			n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
			return n ?  window.decodeURIComponent(n[1]) : '';
		};

		function html( cls, data ,con) {
			var placeholder = url + '/mce-img/panel/' + getAttr(data,'type') + '.png';
			data = window.encodeURIComponent( data );
			content = window.encodeURIComponent( con );

			return '<img title="Click to edit" src="' + placeholder + '" class="mceItem ' + sh_tag + '" ' + 'data-sh-attr="' + data + '" data-sh-content="'+ con+'" data-mce-resize="false" data-mce-placeholder="1" />';
		}

		function replaceShortcodes( content ) {
			return content.replace( /\[tk_panel([^\]]*)\]([^\]]*)\[\/tk_panel\]/g, function( all,attr,con) {
				return html( 'wp-tk_panel', attr , con);
			});
		}

		function restoreShortcodes( content ) {
			return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(?:<\/p>)*/g, function( match, image ) {
				var data = getAttr( image, 'data-sh-attr' );
				var con = getAttr( image, 'data-sh-content' );

				if ( data ) {
					return '<p>[' + sh_tag + data + ']' + con + '[/'+sh_tag+']</p>';
				}
				return match;
			});
		}

		//add popup
		editor.addCommand('tk_panel_popup', function(ui, v) {
			//setup defaults
			var title = '';
			if (v.title)
				title = v.title;
			var footer = '';
			if (v.footer)
				footer = v.footer;
			var type = 'default';
			if (v.type)
				type = v.type;
			var content = '';
			if (v.content)
				content = v.content;

			editor.windowManager.open( {
				title: 'Toolkit Panel Shortcode',
				body: [
					{
						type: 'textbox',
						name: 'title',
						label: 'Panel title',
						value: title,
						tooltip: 'Leave blank for none'
					},
					{
						type: 'textbox',
						name: 'footer',
						label: 'Panel Footer',
						value: footer,
						tooltip: 'Leave blank for none'
					},
					{
						type: 'listbox',
						name: 'type',
						label: 'Panel Type',
						value: type,
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
						value: content,
						multiline: true,
						minWidth: 300,
						minHeight: 100
					}
				],
				onsubmit: function( e ) {
					var shortcode_str = '[' + sh_tag + ' type="'+e.data.type+'"';
					//check for title
					if (typeof e.data.title != 'undefined' && e.data.title.length)
						shortcode_str += ' title="' + e.data.title + '"';
					//check for footer
					if (typeof e.data.footer != 'undefined' && e.data.footer.length)
						shortcode_str += ' footer="' + e.data.footer + '"';

					//add panel content
					shortcode_str += ']' + e.data.content + '[/' + sh_tag + ']';
					//insert shortcode to tinymce
					editor.insertContent( shortcode_str);
				}
			});
	      	});

		//add button
		editor.addButton('tk_panel', {
			icon: 'tk_panel',
			tooltip: 'Add Panel',
			onclick: function() {
				editor.execCommand('tk_panel_popup','',{
					title : '',
					footer : '',
					type   : 'default',
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
			if ( e.target.nodeName == 'IMG' && e.target.className.indexOf('tk_panel') > -1 ) {
				var title = e.target.attributes['data-sh-attr'].value;
				title = window.decodeURIComponent(title);
				console.log(title);
				var content = e.target.attributes['data-sh-content'].value;
				editor.execCommand('tk_panel_popup','',{
					title : getAttr(title,'title'),
					footer : getAttr(title,'footer'),
					type   : getAttr(title,'type'),
					content: content
				});
			}
		});
	});
})();