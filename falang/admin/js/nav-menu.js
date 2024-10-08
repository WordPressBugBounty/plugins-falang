jQuery( document ).ready(
	function( $ ) {
		$( '#update-nav-menu' ).bind(
			'click',
			function( e ) {
				if ( e.target && e.target.className && -1 != e.target.className.indexOf( 'item-edit' ) ) {
					$( "input[value='#falang_switcher'][type=text]" ).parent().parent().parent().each(
						function(){
							var item = $( this ).attr( 'id' ).substring( 19 );
							$( this ).children( 'p:not( .field-move )' ).remove(); // remove default fields we don't need

							// item is a number part of id of parent menu item built by WordPress
							// falang_data is built server side with i18n strings without HTML and data retrieved from post meta
							// the usage of attr method is safe before append call. 
							h = $( '<input>' ).attr(
								{
									type: 'hidden',
									id:   'edit-menu-item-title-' + item,
									name: 'menu-item-title[' + item + ']',
									value: falang_data.title
								}
							);
							$( this ).append( h ); // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.append

							h = $( '<input>' ).attr(
								{
									type:  'hidden',
									id:    'edit-menu-item-url-' + item,
									name:  'menu-item-url[' + item + ']',
									value: '#falang_switcher'
								}
							);
							$( this ).append( h ); // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.append

							// a hidden field which exits only if our jQuery code has been executed
							h = $( '<input>' ).attr(
								{
									type:  'hidden',
									id:    'edit-menu-item-falang-detect-' + item,
									name:  'menu-item-falang-detect[' + item + ']',
									value: 1
								}
							);
							$( this ).append( h ); // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.append

							ids = Array( 'hide_current', 'display_flags', 'display_name', 'dropdown' ); // reverse order

							// add the fields
							for ( var i = 0, idsLength = ids.length; i < idsLength; i++ ) {
								p = $( '<p>' ).attr( 'class', 'description' );
								$( this ).prepend( p );
								// item is a number part of id of parent menu item built by WordPress
								// falang_data is built server side with i18n strings without HTML
								label = $( '<label>' ).attr( 'for', 'edit-menu-item-' + ids[ i ] + '-' + item ).text( ' ' + falang_data.strings[ ids[ i ] ] );
								p.append( label ); // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.append
								cb = $( '<input>' ).attr(
									{
										type:  'checkbox',
										id:    'edit-menu-item-' + ids[ i ] + '-' + item,
										name:  'menu-item-' + ids[ i ] + '[' + item + ']',
										value: 1
									}
								);
								if ( ( typeof( falang_data.val[ item ] ) != 'undefined' && falang_data.val[ item ][ ids[ i ] ] == 1 ) || ( typeof( falang_data.val[ item ] ) == 'undefined' && ids[ i ] == 'display_name' ) ) { // show_names as default value
									cb.prop( 'checked', true );
								}
								label.prepend( cb );
							}
						}
					);

					// disallow unchecking both show names and show flags
					$( '.menu-item-data-object-id' ).each(
						function() {
							var id = $( this ).val();
							var options = ['names-', 'flags-'];
							$.each(
								options,
								function( i, v ) {
									$( '#edit-menu-item-show_' + v + id ).change(
										function() {
											if ( 'checked' != $( this ).attr( 'checked' ) ) {
												$( '#edit-menu-item-show_' + options[ 1 - i ] + id ).prop( 'checked', true );
											}
										}
									);
								}
							);
						}
					);
				}
			}
		);
	}
);
