(function() {
	tinymce.PluginManager.add('et_mce_button', function( editor, url ) {
		editor.addButton( 'et_mce_button', {
            icon: ' et-shortcodes-icon ',
			tooltip: 'Etheme Shortcodes',
			type: 'menubutton',
			minWidth: 210,
			menu: [
				{
					text: 'Button',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Button',
							body: [
								{
									type: 'listbox',
									name: 'style',
									label: 'Style',
									'values': [
										{text: 'Small', value: 'small'},
										{text: 'Medium', value: 'medium'},
										{text: 'Big', value: 'big'},
										{text: 'Small black', value: 'small black'},
										{text: 'Medium black', value: 'medium black'},
										{text: 'Big black', value: 'big black'},
										{text: 'Small active', value: 'small active'},
										{text: 'Medium active', value: 'medium active'},
										{text: 'Big active', value: 'big active'},
										{text: 'Small white', value: 'small white'},
										{text: 'Medium white', value: 'medium white'},
										{text: 'Big white', value: 'big white'},
										{text: 'Small bordered', value: 'small bordered'},
										{text: 'Medium bordered', value: 'medium bordered'},
										{text: 'Big bordered', value: 'big bordered'},
									]
								},
								{
									type: 'textbox',
									name: 'url',
									label: 'URL',
									value: ''
								},
								{
									type: 'textbox',
									name: 'icon',
									label: 'Icon',
									value: ''
								},
								{
									type: 'textbox',
									name: 'title',
									label: 'Title',
									value: 'BUTTON TEXT'
								}

							],
							onsubmit: function( e ) {
								editor.insertContent( '[button style="' + e.data.style + '" url="' + e.data.url + '" icon="' + e.data.icon + '" title="' + e.data.title + '"]');
							}
						});
					}
				},
				{
					text: 'Blockquote',
					menu: [
						{
							text: 'Style 1',
							onclick: function() {
								editor.insertContent( '[blockquote][/blockquote]');
							}
						},
						{
							text: 'Style 2',
							onclick: function() {
								editor.insertContent( '[blockquote class="style2"][/blockquote]');
							}
						},
						{
							text: 'Style 3',
							onclick: function() {
								editor.insertContent( '[blockquote class="style3"][/blockquote]');
							}
						},
					]
				},
				{
					text: 'Divider',
					menu: [
						{
							text: 'Short',
							onclick: function() {
								editor.insertContent( '<hr>');
							}
						},
						{
							text: 'Wide',
							onclick: function() {
								editor.insertContent( '<hr class="wide">');
							}
						},
						{
							text: 'Full width',
							onclick: function() {
								editor.insertContent( '<hr class="full-width">');
							}
						},
					]
				},
				{
					text: 'Dropcaps',
					menu: [
						{
							text: 'Light',
							onclick: function() {
								editor.insertContent( '[dropcap style="light" color=""][/dropcap]');
							}
						},
						{
							text: 'Dark',
							onclick: function() {
								editor.insertContent( '[dropcap style="dark" color=""][/dropcap]');
							}
						},
						{
							text: 'Bordered',
							onclick: function() {
								editor.insertContent( '[dropcap style="bordered" color=""][/dropcap]');
							}
						},
					]
				},
				{
					text: 'Highlight',
					menu: [
						{
							text: 'Text',
							onclick: function() {
								editor.insertContent( '[mark style="text" color=""][/mark]');
							}
						},
						{
							text: 'Paragraph',
							onclick: function() {
								editor.insertContent( '[mark style="paragraph" color=""][/mark]');
							}
						},
						{
							text: 'Paragraph boxed',
							onclick: function() {
								editor.insertContent( '[mark style="paragraph-boxed" color=""][/mark]');
							}
						},
					]
				},
				{
					text: 'Ordered List',
					onclick: function() {
						editor.windowManager.open( {
							title: 'List params',
							body: [
								{
									type: 'listbox',
									name: 'style',
									label: 'Style',
									'values': [
										{text: 'Simple', value: 'simple'},
										{text: 'Active', value: 'active'},
										{text: 'Squared', value: 'squared'}
									]
								}

							],
							onsubmit: function( e ) {

								var html = [
									'<ol class="' + e.data.style + '">',
										'<li>List item 1</li>',
										'<li>List item 2</li>',
										'<li>List item 3</li>',
									'</ol>',
								].join('\n');

								editor.insertContent( html );
							}
						});
					}
				},
				{
					text: 'Unordered List',
					onclick: function() {
						editor.windowManager.open( {
							title: 'List params',
							body: [
								{
									type: 'listbox',
									name: 'style',
									label: 'Style',
									'values': [
										{text: 'Square', value: 'square'},
										{text: 'Circle', value: 'circle'},
										{text: 'Arrow', value: 'arrow'},
									]
								}

							],
							onsubmit: function( e ) {

								var html = [
									'<ul class="' + e.data.style + '">',
										'<li>List item 1</li>',
										'<li>List item 2</li>',
										'<li>List item 3</li>',
									'</ul>',
								].join('\n');

								editor.insertContent( html );
							}
						});
					}
				},
			]
		});
	});
})();