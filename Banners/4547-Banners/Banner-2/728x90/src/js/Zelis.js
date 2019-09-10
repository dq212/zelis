// Jquery-like selector and helper methods
// Selector: nameSpace.$( 'el' )
// HasClass: nameSpace.$( '#id' ).hasClass( 'class' );
// AddClass: nameSpace.$( '.class' ).addClass( 'class' )
// RemoveClass: nameSpace.$( '#id .class' ).removeClass( 'class' )

var Zelis = Zelis || {};

(function() {
	Zelis.$ = function ( el ) {
		var supportsQuerySelectorAll = !! document.querySelectorAll,
			elObj;

		if ( supportsQuerySelectorAll ) {
			try {
				elObj = document.querySelectorAll( el );
				
				elObj.hasClass = hasClass;
				elObj.addClass = addClass;
				elObj.removeClass = removeClass;
			} catch ( e ) {}
		}

		return elObj;
	};

	function hasClass ( className ) {
		if ( typeof className === 'string' ) {
			for ( var i = 0; i < this.length; i++ ) {
				if ( this[i].nodeType === 1 && this[i].className.match( new RegExp( '(\\s|^)' + className + '(\\s|$)' ) ) ) {
					return true;
				}
			}
		}
		
		return false;
	}

	function addClass ( className ) {
		if ( typeof className === 'string' ) {
			for ( var i = 0; i < this.length; i++ ) {
				if ( ! this.hasClass ( className ) ) {
					for ( var j = 0; j < this.length; j++ ) {
						this[ j ].className += ( this[ j ].className === "" ? "" : " " ) + className;
					}
				}
			}
		}

		return this;
	}

	function removeClass ( className ) {
		if ( typeof className === 'string' ) {
			for ( var i = 0; i < this.length; i++ ) {
				if ( this.hasClass ( className ) ) {
					var reg = new RegExp( '(\\s|^)' + className + '(\\s|$)' );
					this[ i ].className = this[ i ].className.replace( reg, ' ' );
				}
			}
		}

		return this;
	}
})();