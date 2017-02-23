//(function($){

tinymce.PluginManager.add( 'bootstrap' , function( editor ){
	var classesBtn, $ = jQuery,
		l10n = mce_bootstrap.l10n, 
		classes = mce_bootstrap.classes;

	function setSelection() {
		var selection = editor.selection.getNode(),
			editable, $el;

		$.each( classes, function( i, classSet ) {
			$el = $(selection);
			if ( ! $el.is( classSet.selector ) ) {
				$el = $( editor.dom.getParent( selection , classSet.selector ) );
			}
			if ( $el.is( classSet.selector ) ) {
				if ( classesBtn.menu ) {
					classesBtn.menu.remove();
					classesBtn.menu = null;
				}
				classesBtn.state.data.menu = classesBtn.settings.menu = classSet.classes;
				editable = true;

				classesBtn.value( $el.attr( 'class' ) || '' );
				return false;
			}
		} );
		classesBtn.disabled( ! editable );
	}
	
	
	function setElementClasses( value ) {
		var selection = editor.selection.getNode(), $el;

		$.each( classes, function( i, classSet ) {
			$el = $(selection);
			if ( ! $el.is( classSet.selector ) ) {
				$el = $( editor.dom.getParent( selection , classSet.selector ) );
			}
			if ( $el.is( classSet.selector ) ) {
				if ( value !== '' ) {
					$el.attr( 'class', value );
				} else {
					$el.removeAttr( 'class' );
				}
				return false;
			}
		});

	}

	editor.addButton('bootstrap', {
		type: 'listbox',
		text: 'Style',
		tooltip: '',
		menu_button : true,
		classes : 'widget btn fixed-width', 
		onselect: function(e) {
			setElementClasses( this.value() );
		},
		values: [],
		onPostRender: function() {
			classesBtn = this;
			editor.on( 'nodechange', function( event ) {
				setSelection( );
			});
		}
		
	});

} );


//})(jQuery);