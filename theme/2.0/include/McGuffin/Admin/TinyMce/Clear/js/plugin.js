var clearPluginCallback;

clearPluginCallback = function( editor ){

	var the_clear = '<div contenteditable="false" class="clear"></div>',
		$ = jQuery;
	
	editor.addCommand( 'cmd_clear', function() {
		var node = editor.selection.getNode(),
			parent_node = editor.dom.getParent( node ,'DIV'),
			sel, $sel;
		if ( ! parent_node ) {
	 		editor.insertContent(the_clear);
	 	} else {
	 		sel = editor.selection.getEnd();
	 		$sel = $(sel);
			while ( $sel.parent().is( ':not(body)' ) )
				$sel = $sel.parent();
			console.log($sel);
	 		$(the_clear).insertAfter($sel);
	 	}
	});
	
	editor.addButton('clear', {
		icon: 'clear',
		tooltip: mce_clear.l10n.insert_clear,
		cmd : 'cmd_clear',
		onPostRender: function() {
			var clearBtn = this;
			editor.on( 'nodechange', function( event ) {
				clearBtn.disabled( ! editor.selection.isCollapsed() );
			});
		}
	});
};

tinymce.PluginManager.add( 'clear', clearPluginCallback );

