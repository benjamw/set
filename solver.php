<?php

if (isset($_POST)) {
	$cards = makeCards($_POST);
	$sets = solveSet($cards);
}

?>
<html>
<head>
<title>Set Solver</title>
<style type="text/css">
	.card {
		width: 21%;
		float: left;
		border: 1px solid grey;
		padding: 5px;
		margin: 5px;
	}
</style>
</head>
<body>
<form action="" method="post">
<?php

	for ($i = 0; $i < 12; ++$i) {
		echo getSelects($i, $_POST);
	}

	echo '<div style="clear:both;">&nbsp;</div>';

	if (isset($sets) && is_array($sets)) {
		echo 'SETS FOUND ARE:<br />';

		foreach ($sets as $set) {
			$set = explode(',', $set);
			echo ($set[0]+1).' - '.($set[1]+1).' - '.($set[2]+1).'<br />';
		}
	}

?>
<input type="submit" />
</form>
</body>
</html>
<?php

// functions


function getSelects($n, $data)
{
	$html = '
		<div class="card">
			<select name="number'.$n.'">
				<option value="0"'.isSelected($data['number'.$n], 0).'>1</option>
				<option value="1"'.isSelected($data['number'.$n], 1).'>2</option>
				<option value="2"'.isSelected($data['number'.$n], 2).'>3</option>
			</select><br />
			<select name="color'.$n.'">
				<option value="0"'.isSelected($data['color'.$n], 0).'>red</option>
				<option value="1"'.isSelected($data['color'.$n], 1).'>purple</option>
				<option value="2"'.isSelected($data['color'.$n], 2).'>green</option>
			</select><br />
			<select name="fill'.$n.'">
				<option value="0"'.isSelected($data['fill'.$n], 0).'>solid</option>
				<option value="1"'.isSelected($data['fill'.$n], 1).'>striped</option>
				<option value="2"'.isSelected($data['fill'.$n], 2).'>hollow</option>
			</select><br />
			<select name="shape'.$n.'">
				<option value="0"'.isSelected($data['shape'.$n], 0).'>squiggle</option>
				<option value="1"'.isSelected($data['shape'.$n], 1).'>diamond</option>
				<option value="2"'.isSelected($data['shape'.$n], 2).'>oval</option>
			</select>
		</div>
	';

	return $html;
}


function isSelected($input, $value)
{
	if ($input == $value) {
		return ' selected="selected"';
	}
}


function makeCards($POST)
{
	$cards = array( );

	$n = 0;
	while (isset($POST['number'.$n])) {
		$cards[] = $POST['number'.$n] . $POST['color'.$n] . $POST['fill'.$n] . $POST['shape'.$n];
		++$n;
	}


	return $cards;
}


function solveSet($cards) {
	$set = array( );

	if (is_array($cards)) {
		$numCards = count($cards);

		// repeat once for each card
		for ($i = 0; $i < $numCards; ++$i) {
			// repeat once for every _other_ card
			for ($j = ($i + 1); $j < $numCards; ++$j) {
				$solution = '';

				// repeat once for each attribute
				for ($k = 0; $k < 4; ++$k) {
					$solution[$k] = solveAttribute($cards[$i][$k], $cards[$j][$k]);
				}

				// we have our solution card, look for it
				$index = array_search(implode('', $solution), $cards);
				if (false !== $index) {
					$match = array($i, $j, $index);
					sort($match);
					$match = implode(',', $match);

					if ( ! in_array($match, $set)) {
						$set[] = $match;
					}
				}
			}
		}
	}

	return $set;
}


function solveAttribute($val1, $val2)
{
	$val1 = (int) $val1;
	$val2 = (int) $val2;

	// the truncated value is 3 if the supplied values are identical
	$val = (3 == trunc( ~ ($val1 ^ $val2))) ? $val1 : trunc( ~ ($val1 ^ $val2));

	return $val;
}


// this function truncates the binary representation of the number from the LEFT side
// i.e.- if the number is 1111111111111101 (-3) (16 bit), the truncated(2) value is 01 (1)
function trunc($val, $num = 2)
{
	$bin = str_pad(decbin($val), $num, '0', STR_PAD_LEFT);
	$trunc = substr($bin, -$num);
	$dec = bindec($trunc);

	return $dec;
}

?>