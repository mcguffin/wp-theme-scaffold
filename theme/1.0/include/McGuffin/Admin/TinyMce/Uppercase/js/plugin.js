var uppercasePluginCallback;

(function($){

	uppercasePluginCallback = function( editor ) {
		var smallBtn, bigBtn;

		editor.addCommand( 'cmd_uppercase', function() {

			var selNode = editor.selection.getNode(),
				matchNodeInline = editor.formatter.matchNode( selNode, 'uppercase_inline' ),
				matchNodeBlock = editor.formatter.matchNode( selNode, 'uppercase_block' );

			if ( !! matchNodeInline ) {
				editor.formatter.remove( 'uppercase_inline' );
			} else {
				editor.formatter.apply( 'uppercase_inline' );
			}

		});


		editor.addButton('uppercase', {
			icon: 'uppercase',
			tooltip: mce_uppercase.l10n.uppercase,
			cmd : 'cmd_uppercase',
			onPostRender: function() {
				smallBtn = this;
				editor.on( 'nodechange', function( event ) {
					var node = editor.selection.getNode();
					smallBtn.active( !!editor.formatter.matchNode( node, 'uppercase_inline' ) );
				});
			}
		});


		editor.on( 'init', function() {
			editor.formatter.register('uppercase_inline', { inline: 'span', classes:'text-uppercase'});
		});

	};

	tinymce.PluginManager.add( 'uppercase', uppercasePluginCallback);

} )(jQuery);

