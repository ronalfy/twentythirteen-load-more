/* Contains the "Load More Posts" functionality */
jQuery( document ).ready( function( $ ) {
	var next_page = parseInt( ronalfy_load_more.current_page ) + 1;
	var max_pages = parseInt( ronalfy_load_more.max_pages );
	
	if ( next_page <= max_pages ) {
		$( '.paging-navigation' ).html( '<div class="nav-links"><a class="load_more" href="#">' + ronalfy_load_more.main_text + '</a><img class="load_more_img" style="display: none;" src="' + ronalfy_load_more.loading_img + '" alt="Loading..." /></div>' );
	}
	
	var mt_ajax_load_posts = function() {
		//Begin Ajax
		$.post( ronalfy_load_more.ajaxurl, { action: 'load_posts', next_page: next_page }, function( response ) {
			next_page = response.next_page;
			max_pages = response.max_pages;
			
			//Append the HTML
			var html = $.parseHTML( response.html );
			html = $( html ).filter( '.type-post' );
			$( '#content .type-post:last' ).after( html );
			
			//If the next page exceeds the number of pages available, get rid of the navigation
			if ( next_page > max_pages ) {
				$( '.paging-navigation' ).html( '' );
			}
			
			//Fade out loading img and fade in loading text
			$( '.load_more_img' ).fadeOut( 'slow', function() {
				$( '.load_more' ).fadeIn( 'slow' );
			} );
		}, 'json' );
	};
	
	//Clicking the load more button
	$( '.paging-navigation' ).on( 'click', 'a.load_more', function( e ) {
		e.preventDefault();
		
		$( '.load_more' ).fadeOut( 'slow', function() {
			$( '.load_more_img' ).fadeIn( 'slow', function() {
				mt_ajax_load_posts();
			} );
		} );
		
		
	} );
} );