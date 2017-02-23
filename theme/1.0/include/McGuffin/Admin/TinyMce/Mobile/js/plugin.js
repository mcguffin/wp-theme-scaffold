tinymce.PluginManager.add( 'mobile' , function( editor ){
	var mobileBtn, nodes = 'H1,H2,H3,H4,H5,H6,DIV,P,PRE,ADDRESS',
		mobile_values = [
			{ 'value' : '' , 'text' : 'Everywhere' },
			{ 'value' : 'hidden-xs' , 'text' : 'Desktop only' },
			{ 'value' : 'visible-xs'  , 'text' : 'Phones only' }
		];
	function resetMobileVisible( ) {
		var parent_node = editor.dom.getParent( editor.selection.getNode() ,nodes),
			classes;
		for ( i=1;i<mobile_values.length;i++) {
			if ( editor.dom.getAttrib( parent_node, 'class' ).indexOf( mobile_values[i].value ) !== -1 ) {
				classes = mobile_values[i].value.split(' ');
				for (var j=0;j<classes.length;j++) {
					jQuery(parent_node).removeClass(classes[j]);
				}
			}
		}
	}
	function setMobileVisibleState( ) {
		var parent_node = editor.dom.getParent( editor.selection.getNode() ,nodes), i;
		mobileBtn.disabled( ! parent_node );
		for ( i=1;i<mobile_values.length;i++) {
			if ( editor.dom.getAttrib( parent_node, 'class' ).indexOf( mobile_values[i].value ) !== -1 ) {
				mobileBtn.value( mobile_values[i].value );
				return;
			}
		}
		mobileBtn.value( '' );
// 		
	}
	function setMobileVisible( value ) {
		var node = editor.dom.getParent( editor.selection.getNode() ,nodes), $node = jQuery(node),
			classes = value.split(' ');
		resetMobileVisible( );
		if ( value != '' ) {
			for ( i=0;i<classes.length;i++ )
				$node.addClass(classes[i]);
		}
	}
	
	editor.addButton('mobile', {
		type: 'listbox',
		text: 'Mobile',
		tooltip: 'Visible on Devices',
		menu_button : true,
		classes : 'widget btn fixed-width', 
		onselect: function(e) {
			setMobileVisible( this.value() );
		},
		values: mobile_values,
		onPostRender: function() {
			mobileBtn = this;
			editor.on( 'nodechange', function( event ) {
				setMobileVisibleState( );
			});
		}
		
	});



} );

