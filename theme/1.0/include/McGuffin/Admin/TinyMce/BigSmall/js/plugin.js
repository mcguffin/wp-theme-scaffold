(function($){
tinymce.PluginManager.add( 'bigsmall' , function( editor ) {
	var smallBtn, bigBtn;

	editor.addCommand( 'cmd_small', function() {
		if ( editor.formatter.matchNode( editor.selection.getNode(), 'small' ) ) {
			editor.formatter.remove( 'small' );
		} else {
			editor.formatter.apply( 'small' );
		}
	});

	editor.addCommand( 'cmd_big', function() {
		if ( editor.formatter.matchNode( editor.selection.getNode(), 'big' ) ) {
			editor.formatter.remove( 'big' );
		} else {
			editor.formatter.apply( 'big' );
		}
	});

	editor.addButton('small', {
		icon: 'small',
		tooltip: mce_bigsmall.l10n.small,
		cmd : 'cmd_small',
		onPostRender: function() {
			smallBtn = this;
			editor.on( 'nodechange', function( event ) {
				smallBtn.active( !!editor.formatter.matchNode( editor.selection.getNode(), 'small' ) );
			});
		}
	});

	editor.addButton('big', {
		icon: 'big',
		tooltip: mce_bigsmall.l10n.big,
		cmd : 'cmd_big',
		onPostRender: function() {
			bigBtn = this;
			editor.on( 'nodechange', function( event ) {
				bigBtn.active( !!editor.formatter.matchNode( editor.selection.getNode(), 'big' ) );
			});
		}
	});

	editor.on( 'init', function(){
		editor.formatter.register('big', {inline: 'big'});
		editor.formatter.register('small', {inline: 'small'});
	});

} );

} )(jQuery);

