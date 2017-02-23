// ------------------------------
// http://twitter.com/mattsince87
// ------------------------------

(function($){
	var $root = $('html, body');
	$("a[href^='#']:not('[role=\"button\"]')").on('click', function(e) {
		
		var $this = $(this), hash = this.hash, offs, 
			update_hash = !parseInt($this.data('discrete'));
			
		if ( hash !== '' ) {
			e.preventDefault();
			this.blur();
			$this.closest('[aria-expanded]').ariaSetState(false);
			offstop = $this.data('scroll-position');
			if ( 'undefined' === typeof(offstop) ) {
				offs = $(hash).offset();
				offstop = offs.top;
			} else {
				offstop = parseInt(offstop);
			}
			if ( 'undefined' !== typeof(offstop) ) {
				$root.stop().animate({
						scrollTop: offstop
					}, 
					500, 
					function(){
						update_hash && (window.location.hash = hash);
					}
				);
			}
			return false;
		}
	});
})(jQuery);
