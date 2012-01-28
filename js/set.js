
// the set.js function collection
// requires the fabulous jQuery library

// global vars
var count = 0;
var selection = [];


// run the live query stuff
$('#cards img')
	.livequery('click', function( ) {
		if ( ! $(this).hasClass('selected')) { // if we have not already selected this card
			var id = parseInt( $(this).addClass('selected').attr('id').substr(1) );
			selection.push(id);
			++count;

			if (3 == count) {
				// send it off to the ajax script
				selection = selection.sortNum( ).join(',');
				$('#guess').val(selection);
				$('#gameForm').submit( );
			}
		}
		else { // we need to remove it from the selection
			var id = parseInt( $(this).removeClass('selected').attr('id').substr(1) );
			selection.splice(selection.indexOf(id), 1);
			--count;
		}
	});


// extra array functions
Array.prototype.sortNum = function() {
   return this.sort( function(a, b) { return a - b; } );
}


if ( ! Array.prototype.indexOf) {
	Array.prototype.indexOf = function(item, startIndex) {
		var len = this.length;
		if (startIndex == null)
			startIndex = 0;
		else if (startIndex < 0) {
			startIndex += len;
			if (startIndex < 0)
				startIndex = 0;
		}
		for (var i = startIndex; i < len; i++) {
			var val = this[i] || this.charAt && this.charAt(i);
			if (val == item)
				return i;
		}
		return -1;
	};
}