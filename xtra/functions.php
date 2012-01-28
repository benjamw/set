<?php

// this should be a class, but I don't have PHP 5 on my server
// and i didn't really want to make a PHP 4 version, so i didn't.
// you don't like it?   refactor it.

function cleanSelection($selection) {
	if ( ! is_array($selection)) {
		$selection = explode(',', $selection);
	}

	array_walk($selection, create_function('&$v','$v = (int) trim($v);'));

	return $selection;
}

function replaceCards($selection) {
	dump('REPLACING CARDS');
	if ('none' == (string) $selection) {
		// test if there are any sets
		if (0 == count($_SESSION['sets'])) {
			addCards(3);
			solveSet( );
		}
		else {
			return 'ERROR: There is a set in the group';
		}
	}
	else if (is_array($selection)) {
		sort($selection);
		// test to see if the selection is a set or not
		if (in_array(implode(',', $selection), $_SESSION['sets'])) {
			$addMore = removeCards($selection);
			addCards(3, $addMore);
			solveSet( );
		}
		else {
			return 'ERROR: That is not a set';
		}
	}
	else { // create a new game
		$_SESSION['shownCards'] = array( );
		$_SESSION['usedCards'] = array( );
		addCards(12);
		solveSet( );
	}

	return true;
}


function getIndexes( ) {
	$indexes = array( );

	foreach ($_SESSION['shownCards'] as $card) {
		$indexes[] = array_search($card, $_SESSION['cards']);
	}

	return $indexes;
}


function addCards($num, $addMore = true) {
	if ( ! $addMore) {
		return false;
	}

	dump('ADDING '.$num.' CARDS');
	$cards = $_SESSION['cards'];
	$shownCards = $_SESSION['shownCards'];
	$usedCards = $_SESSION['usedCards'];

	if ( ! is_array($cards)) {
		exit;
	}

	for ($i = 0; $i < $num; ++$i) {
		dump($i.' ================================================');
		// grab a card from the deck
		// (one that we haven't used yet)
		$card = false;
		$k = 0;
		while (( ! $card || isset($usedCards[$index])) && (100 > $k)) {
			dump('CARD NOT FOUND');
			$index = array_rand($cards);
			$card = $cards[$index];
			dump($index);
			dump($card);
			dump($cards[$index]);
		}

		//put the card in the deck
		for ($j = 0; $j < count($shownCards) + $num; ++$j) {
			if ( ! isset($shownCards[$j])) {
				dump('ADDING CARD');
				$shownCards[$j] = $card;
				break;
			}
		}

		// put the card in the used pile
		$usedCards[$index] = $card;
	}

	// sort the deck by key
	ksort($usedCards);

	$_SESSION['shownCards'] = $shownCards;
	$_SESSION['usedCards'] = $usedCards;
}


function removeCards($cards) {
	dump('REMOVING CARDS');
	dump($cards);
	$shownCards = $_SESSION['shownCards'];

	// remove the cards
	foreach ($cards as $card) {
		dump($card);
		unset($shownCards[$card]);
	}

	// if shown cards is 15, move the rest into the pile
	$addMore = true;
	if (15 <= count($shownCards)) {
		$addMore = false;

		for ($i = 0; $i < count($shownCards) - count($cards); ++$i) {
			if ( ! isset($shownCards[$i])) {
				$shownCards[$i] = array_pop($shownCards);
			}
		}
	}

	$_SESSION['shownCards'] = $shownCards;

	return $addMore;
}


function solveSet( ) {
	$sets = array( );
	$cards = $_SESSION['shownCards'];

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

					if ( ! in_array($match, $sets)) {
						$sets[] = $match;
					}
				}
			}
		}
	}

	$_SESSION['sets'] = $sets;
	return $sets;
}


function solveMagicSquare($super = false)
{
	$cards = $_SESSION['cards'];

	if ( ! $super) {
		// grab two random cards
		for ($i = 0; $i < 2; ++$i) {
			$index = array_rand($cards);
			$grid[$i * 2][0] = $cards[$index];
		}

		// make sure the third card does NOT make a set
		$solution = $grid[1][1] = false;
		while ($grid[1][1] == $solution) {
			// find a new card
			$index = array_rand($cards);
			$grid[1][1] = $cards[$index];

			// generate the solution card
			$solution = solveCard($grid[0][0], $grid[2][0]);
		}

		// put the solution card into the 3x3 array
		$grid[1][0] = $solution;
	}
	else { // is super
		// grab a random card
		$grid[0][0] = $cards[array_rand($cards)];

		// generate another card with only one similar attribute
		$grid[2][0] = generateCard($grid[0][0]);

		// generate a solution card to the previous two
		$grid[1][0] = solveCard($grid[0][0], $grid[2][0]);

		// generate a fourth card with a single
		// different attribute in common with each previous
		// and the other attribute different to the one
		// attribute in common with each

		// find the attribute in common
		for ($n = 0; $n < 4; ++$n) {
			if ($grid[0][0][$n] == $grid[2][0][$n]) {
				$grid[1][1][$n] = selectOtherAttribute($grid[0][0][$n]);
				break;
			}
		}

		// generate random values to tell where we are placing which attribute in common
		$j[] = $n; // will be skipped in the reset( ) below
		for ($i = 0; $i < 3; ++$i) {
			$num = mt_rand(0, 3);

			while (in_array($num, $j)) {
				$num = mt_rand(0, 3);
			}

			$j[] = $num;
		}

		// generate our card
		reset($j); // clears out the 'common' from above
		$m = 0;
		for ($i = 0; $i < 3; ++$i) {
			$k = next($j);
			$grid[1][1][$k] = $grid[$m][0][$k];
			++$m;
		}

		ksort($grid[1][1]); // this is necessary
		$grid[1][1] = implode('', $grid[1][1]);
	}

	// solve the other cards
	$grid[1][2] = solveCard($grid[1][0], $grid[1][1]);
	$grid[0][2] = solveCard($grid[2][0], $grid[1][1]);
	$grid[2][2] = solveCard($grid[0][0], $grid[1][1]);
	$grid[0][1] = solveCard($grid[0][0], $grid[0][2]);
	$grid[2][1] = solveCard($grid[2][0], $grid[2][2]);

	// grab the index for each card
	$indexes = array( );
	for ($i = 0; $i < 3; ++$i) {
		for ($j = 0; $j < 3; ++$j) {
			$indexes[$i][$j] = array_search($grid[$i][$j], $cards);
		}
	}

	return $indexes;
}


function solveAttribute($val1, $val2)
{
	$val1 = (int) $val1;
	$val2 = (int) $val2;

	// the truncated value is 3 if the supplied values are identical
	$val = (3 == trunc( ~ ($val1 ^ $val2))) ? $val1 : trunc( ~ ($val1 ^ $val2));

	return $val;
}


function selectOtherAttribute($attribute) {
	if (0 == $attribute) {
		$attr = mt_rand(1, 2);
	}
	else if (1 == $attribute) {
		$attr = (0 == mt_rand(0, 1)) ? '0' : '2';
	}
	else {
		$attr = mt_rand(0, 1);
	}

	return (string) $attr;
}

function solveCard($card1, $card2) {
	if ((4 != strlen($card1)) || (4 != strlen($card2))) {
		return false;
	}

	for ($k = 0; $k < 4; ++$k) {
		$solution[$k] = solveAttribute($card1[$k], $card2[$k]);
	}

	ksort($solution);
	return implode('', $solution);
}


function generateCard($card, $numAttr = 1) {
	$j = array( );
	for ($i = 0; $i < $numAttr; ++$i) {
		$num = mt_rand(0, 3);

		while (in_array($num, $j)) {
			$num = mt_rand(0, 3);
		}

		$j[] = $num;
	}

	$outCard = '';
	for ($k = 0; $k < 4; ++$k) {
		if (in_array($k, $j)) {
			$outCard .= $card[$k];
		}
		else {
			$outCard .= selectOtherAttribute($card[$k]);
		}
	}

	return $outCard;
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


function dump($var, $bypass = false) {
	if ($bypass || (defined('DEBUG') && DEBUG)) {
		var_dump($var);
	}
}

?>