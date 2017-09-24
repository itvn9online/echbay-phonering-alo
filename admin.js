

try {
	
	
	(function () {
		
		if ( typeof $ != 'function' ) {
			$ = jQuery;
		}
		
		
		
		$('.btn-restore-default').click(function () {
			var a = $(this).attr('data-set') || '';
		//	console.log(a);
			
			var b = 'input[id="' + a + '"]';
			
			$(b).val( $(b).attr('placeholder') || '' );
		});
		
	}());
	
	
} catch ( e ) {
	console.log( 'stack: ' + (e.stackTrace || e.stack) );
}



