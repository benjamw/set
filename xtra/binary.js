
// constants
var STR_PAD_LEFT = 1;
var STR_PAD_RIGHT = 2;
var STR_PAD_BOTH = 3;


// functions


// this function converts decimal integers to binary strings
function decbin(decNum)
{
	decNum = parseInt(decNum, 10);

	// THIS FUNCTION IS ONLY 4 BIT SIGNED, THAT'S ALL I NEEDED AT THE TIME
	if ((7 < decNum) || (-7 > decNum)) { // i left out -8, because it's the wierd number
		return false;
	}

	// grab the absolute value of decNum
	var val = Math.abs(decNum);
	var sign = decNum / val;
	var posBin = pad(parseInt(val, 10).toString(2), 4, '0', STR_PAD_LEFT);

	// if it's a positive number
	if (0 < sign) {
		return posBin;
	}
	else { // get the two's compliment
		return comp2(posBin);
	}
}


// this function converts binary strings to decimal integers
function bindec(binStr)
{
	// THIS FUNCTION IS ONLY 4 BIT SIGNED, THAT'S ALL I NEEDED AT THE TIME
	if ('1000' == binStr) { // the wierd number (-8)
		return false;
	}

	if (4 > binStr.length) {
		binStr = pad(binStr, 4, '0', STR_PAD_LEFT);
	}

	if ('1' == binStr.substring(0, 1)) {
		binStr = comp2(binStr);
	}

	return parseInt(binStr, 2);
}


// this function finds the two's compliment
// (negative binary value) of a given binary string
function comp2(binStr)
{
	if ('1000' == binStr) { // the 'wierd' number
		return false;
	}

	// reverse the binStr
	binStr = strrev(binStr);

	// now parse through it until we find the first 1
	var found = 0;
	var negStr = '';
	for (var i = 0; i < binStr.length; ++i) {
		if ('0' == binStr.substring(i, i+1)) {
			if (found) {
				negStr += '1';
			}
			else {
				negStr += '0';
			}
		}
		else { // we found a 1
			if (found) {
				negStr += '0';
			}
			else {
				found = 1;
				negStr += '1';
			}
		}
	}

	return strrev(negStr);
}


// this function reverses a given string
function strrev(string)
{
	var newString = '';

	for (var i = string.length; i > 0; --i) {
		newString += string.substring(i-1, i);
	}

	return newString;
}


// this function truncates a binary string to the right
// so only num digits remain
// e.g.- trunc('11001010', 4) returns '1010'
function trunc(val, num)
{
    if (typeof(num) == "undefined") { var num = 2; }

	var bin = pad(decbin(val), num, '0', STR_PAD_LEFT);
	var trunc = bin.slice(num);
	var dec = bindec(trunc);

	return dec;
}


// this function pads str with pad until it is len characters long
function pad(str, len, pad, dir)
{
    if (typeof(len) == "undefined") { var len = 0; }
    if (typeof(pad) == "undefined") { var pad = ' '; }
    if (typeof(dir) == "undefined") { var dir = STR_PAD_RIGHT; }

    if (len + 1 >= str.length) {
        switch (dir) {
            case STR_PAD_LEFT:
                str = Array(len + 1 - str.length).join(pad) + str;
            	break;

            case STR_PAD_BOTH:
                var right = Math.ceil((padlen = len - str.length) / 2);
                var left = padlen - right;
                str = Array(left + 1).join(pad) + str + Array(right + 1).join(pad);
            	break;

            default: // STR_PAD_RIGHT
                str = str + Array(len + 1 - str.length).join(pad);
            	break;
        }
    }

    return str;
}