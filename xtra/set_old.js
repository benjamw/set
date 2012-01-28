

// this is our Set object
function Set( ) {

	/*
		- fill:
			0 = solid
			1 = stripped
			2 = hollow
		- shape:
			0 = squiggle
			1 = diamond
			2 = oval
		- color:
			0 = red
			1 = purple
			2 = green
		- number:
			0 = 1
			1 = 2
			2 = 3

		fill - shape - color - number
	*/

	// properties
	this.cards = {
		'01' : '0000' , '02' : '0001' , '03' : '0002' ,
		'04' : '0010' , '05' : '0011' , '06' : '0012' ,
		'07' : '0020' , '08' : '0021' , '09' : '0022' ,
		'10' : '0100' , '11' : '0101' , '12' : '0102' ,
		'13' : '0110' , '14' : '0111' , '15' : '0112' ,
		'16' : '0120' , '17' : '0121' , '18' : '0122' ,
		'19' : '0200' , '20' : '0201' , '21' : '0202' ,
		'22' : '0210' , '23' : '0211' , '24' : '0212' ,
		'25' : '0220' , '26' : '0221' , '27' : '0222' ,
		'28' : '1000' , '29' : '1001' , '30' : '1002' ,
		'31' : '1010' , '32' : '1011' , '33' : '1012' ,
		'34' : '1020' , '35' : '1021' , '36' : '1022' ,
		'37' : '1100' , '38' : '1101' , '39' : '1102' ,
		'40' : '1110' , '41' : '1111' , '42' : '1112' ,
		'43' : '1120' , '44' : '1121' , '45' : '1122' ,
		'46' : '1200' , '47' : '1201' , '48' : '1202' ,
		'49' : '1210' , '50' : '1211' , '51' : '1212' ,
		'52' : '1220' , '53' : '1221' , '54' : '1222' ,
		'55' : '2000' , '56' : '2001' , '57' : '2002' ,
		'58' : '2010' , '59' : '2011' , '60' : '2012' ,
		'61' : '2020' , '62' : '2021' , '63' : '2022' ,
		'64' : '2100' , '65' : '2101' , '66' : '2102' ,
		'67' : '2110' , '68' : '2111' , '69' : '2112' ,
		'70' : '2120' , '71' : '2121' , '72' : '2122' ,
		'73' : '2200' , '74' : '2201' , '75' : '2202' ,
		'76' : '2210' , '77' : '2211' , '78' : '2212' ,
		'79' : '2220' , '80' : '2221' , '81' : '2222' };

	this.cardsRev = {
		'0000' : '01' , '0001' : '02' , '0002' : '03' ,
		'0010' : '04' , '0011' : '05' , '0012' : '06' ,
		'0020' : '07' , '0021' : '08' , '0022' : '09' ,
		'0100' : '10' , '0101' : '11' , '0102' : '12' ,
		'0110' : '13' , '0111' : '14' , '0112' : '15' ,
		'0120' : '16' , '0121' : '17' , '0122' : '18' ,
		'0200' : '19' , '0201' : '20' , '0202' : '21' ,
		'0210' : '22' , '0211' : '23' , '0212' : '24' ,
		'0220' : '25' , '0221' : '26' , '0222' : '27' ,
		'1000' : '28' , '1001' : '29' , '1002' : '30' ,
		'1010' : '31' , '1011' : '32' , '1012' : '33' ,
		'1020' : '34' , '1021' : '35' , '1022' : '36' ,
		'1100' : '37' , '1101' : '38' , '1102' : '39' ,
		'1110' : '40' , '1111' : '41' , '1112' : '42' ,
		'1120' : '43' , '1121' : '44' , '1122' : '45' ,
		'1200' : '46' , '1201' : '47' , '1202' : '48' ,
		'1210' : '49' , '1211' : '50' , '1212' : '51' ,
		'1220' : '52' , '1221' : '53' , '1222' : '54' ,
		'2000' : '55' , '2001' : '56' , '2002' : '57' ,
		'2010' : '58' , '2011' : '59' , '2012' : '60' ,
		'2020' : '61' , '2021' : '62' , '2022' : '63' ,
		'2100' : '64' , '2101' : '65' , '2102' : '66' ,
		'2110' : '67' , '2111' : '68' , '2112' : '69' ,
		'2120' : '70' , '2121' : '71' , '2122' : '72' ,
		'2200' : '73' , '2201' : '74' , '2202' : '75' ,
		'2210' : '76' , '2211' : '77' , '2212' : '78' ,
		'2220' : '79' , '2221' : '80' , '2222' : '81' };

	this.shownCards = [];
	this.usedCards = [];
	this.sets = [];
	this.selection;

	// methods
	this.solveAttribute = solveAttribute;
	this.solveSets = solveSets;
}


function replaceCards( ) {
	var i;

	// user clicked on 'No Set', see if that's true
	if ('none' == this.selection) {
		if (0 == this.sets.length) {
			// place three more cards
			for (i = 0; i < 3; ++i) {
				this.shownCards[] = this.selectCard( );
			}
		}
		else {
			alert('There is at least one set in the cards');
		}
	}
	else if (3 == this.selection.length) {
		// check if the user selected a valid set
		this.selection = this.selection.sortNum( );
		if (this.sets.indexOf(this.selection)) {
			


function solveSets( ) {
	var solution, match, index;
	var i, j, k;

	this.sets = [];

	// repeat once for each card
	for (i = 0; i < this.shownCards.length; ++i) {
		// repeat once for every _other_ card
		for (j = (i + 1); j < this.shownCards.length; ++j) {
			solution = '';
			match = []

			// repeat once for each attribute
			for (k = 0; k < 4; ++k) {
				solution += this.solveAttribute(this.shownCards[i].charAt(k), this.shownCards[j].charAt(k));
			}

			// we have our solution card, look for it
			index = this.shownCards.indexOf(solution);
			if (false !== index) {
				match = [i, j, index];
				
				if ( ! this.sets.indexOf(match)) {
					this.sets[] = match;
				}
			}
		}
	}
}


function solveAttribute(val1, val2) {
	val1 = parseInt(val1);
	val2 = parseInt(val2);

	// the truncated value is 3 if the supplied values are identical
	var val = (3 == trunc( ~ (val1 ^ val2))) ? val1 : trunc( ~ (val1 ^ val2));

	return val;
}



// other functions and prototypes
function rand(min, max) {
	if (min == max) {
		return min;
	}

	if (max < min) {
		var temp = max;
		max = min;
		min = temp;
	}

	// 'seed' our random number (cuz js random numbers suck)
	var date = new Date( );
	for (var i = 0; i < (date.getMilliseconds( ) % 10); ++i) {
		Math.random( );
	}

	return min + Math.floor((max - min + 1) * (Math.random( ) % 1));
}

Array.prototype.sortNum = function() {
   return this.sort( function(a, b) { return a - b; } );
}