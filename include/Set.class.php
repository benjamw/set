<?php
/*
+---------------------------------------------------------------------------
|
|   set.class.php (php 5.x)
|
|   by Benjam Welker
|   http://www.iohelix.net
|
+---------------------------------------------------------------------------
|
|	This module is built to play the game of Set, it cares not about
|	database structure or the goings on of the website, only about Set
|
+---------------------------------------------------------------------------
|
|   > Set module
|   > Date started: 2007-05-28
|
|   > Module Version Number: 0.8.0
|
+---------------------------------------------------------------------------
*/

// TODO: organize better

class Set
{

	/**
	 *		PROPERTIES
	 * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/** static public property CARDS
	 *		Holds the card data in a string of four trits,
	 *		each trit (3-state bit) holds one attribute
	 *		in one of three states (0, 1, 2) in the following
	 *		format: 1234
	 *			1st trit - Fill Type: 0-solid, 1-striped, 2-hollow
	 *			2nd trit - Shape: 0-squiggle, 1-diamond, 2-oval
	 *			3rd trit - Color: 0-red, 1-purple, 2-green
	 *			4th trit - Number: 0-1 item, 1-2 items, 2-3 items
	 *
	 * @var array (index starts at 1)
	 */
	static public $CARDS = array(
			 1 => '0000' ,  2 => '0001' ,  3 => '0002' ,
			 4 => '0010' ,  5 => '0011' ,  6 => '0012' ,
			 7 => '0020' ,  8 => '0021' ,  9 => '0022' ,
			10 => '0100' , 11 => '0101' , 12 => '0102' ,
			13 => '0110' , 14 => '0111' , 15 => '0112' ,
			16 => '0120' , 17 => '0121' , 18 => '0122' ,
			19 => '0200' , 20 => '0201' , 21 => '0202' ,
			22 => '0210' , 23 => '0211' , 24 => '0212' ,
			25 => '0220' , 26 => '0221' , 27 => '0222' ,
			28 => '1000' , 29 => '1001' , 30 => '1002' ,
			31 => '1010' , 32 => '1011' , 33 => '1012' ,
			34 => '1020' , 35 => '1021' , 36 => '1022' ,
			37 => '1100' , 38 => '1101' , 39 => '1102' ,
			40 => '1110' , 41 => '1111' , 42 => '1112' ,
			43 => '1120' , 44 => '1121' , 45 => '1122' ,
			46 => '1200' , 47 => '1201' , 48 => '1202' ,
			49 => '1210' , 50 => '1211' , 51 => '1212' ,
			52 => '1220' , 53 => '1221' , 54 => '1222' ,
			55 => '2000' , 56 => '2001' , 57 => '2002' ,
			58 => '2010' , 59 => '2011' , 60 => '2012' ,
			61 => '2020' , 62 => '2021' , 63 => '2022' ,
			64 => '2100' , 65 => '2101' , 66 => '2102' ,
			67 => '2110' , 68 => '2111' , 69 => '2112' ,
			70 => '2120' , 71 => '2121' , 72 => '2122' ,
			73 => '2200' , 74 => '2201' , 75 => '2202' ,
			76 => '2210' , 77 => '2211' , 78 => '2212' ,
			79 => '2220' , 80 => '2221' , 81 => '2222'
		);



	/** protected property _guess
	 *		The current guess from the player
	 *
	 * @var array of ints
	 */
	protected $_guess;


	/** protected property _avail_cards
	 *		The card ids available to the game right now
	 *
	 * @var array of ints
	 */
	protected $_avail_cards;


	/** protected property _visible_cards
	 *		The card ids visible to the player right now
	 *
	 * @var array of ints
	 */
	protected $_visible_cards;


	/** protected property _used_cards
	 *		The card ids that have been used
	 *
	 * @var array of ints
	 */
	protected $_used_cards;


	/** protected property _sets
	 *		The card ids that constitute valid sets for
	 *		the currently visible cards
	 *
	 * @var array of csv strings of ints
	 */
	protected $_sets;


	/** protected property _DEBUG
	 *		Holds the DEBUG state for the class
	 *
	 * @var bool
	 */
	protected $_DEBUG = false;



	/**
	 *		METHODS
	 * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/** public function __construct
	 *		Class constructor
	 *		Sets all outside data
	 *
	 * @param void
	 * @action instantiates object
	 * @return void
	 */
	public function __construct( )
	{
		call(__METHOD__);

		if (defined('DEBUG')) {
			$this->_DEBUG = DEBUG;
		}
	}


	/** public function __destruct
	 *		Class destructor
	 *		Gets object ready for destruction
	 *
	 * @param void
	 * @action destroys object
	 * @return void
	 */
	public function __destruct( )
	{
		// do nothing, yet...
	}


	/** public function __get
	 *		Class getter
	 *		Returns the requested property if the
	 *		requested property is not _private
	 *
	 * @param string property name
	 * @return mixed property value
	 */
	public function __get($property)
	{
		if ( ! property_exists($this, $property)) {
			throw new Exception(__METHOD__.': Trying to access non-existent property ('.$property.')', 2);
		}

		if ('_' === $property[0]) {
			throw new Exception(__METHOD__.': Trying to access _private property ('.$property.')', 2);
		}

		return $this->$property;
	}


	/** public function __set
	 *		Class setter
	 *		Sets the requested property if the
	 *		requested property is not _private
	 *
	 * @param string property name
	 * @param mixed property value
	 * @action optional validation
	 * @return bool success
	 */
	public function __set($property, $value)
	{
		if ( ! property_exists($this, $property)) {
			throw new Exception(__METHOD__.': Trying to access non-existent property ('.$property.')', 3);
		}

		if ('_' === $property[0]) {
			throw new Exception(__METHOD__.': Trying to access _private property ('.$property.')', 3);
		}

		switch ($property) {
			default :
				// do nothing
				break;
		}

		$this->$property = $value;
	}


	/** public function new_game
	 *		Create a new game with the given difficulty
	 *		If $difficulty is false, one of the trits is
	 *		held constant, making the game easier
	 *
	 * @param bool difficult
	 * @action initialize variables
	 * @return void
	 */
	public function new_game($difficult = true)
	{
		call(__METHOD__);

		// set defaults
		$this->_avail_cards = Set::$CARDS;
		$this->_visible_cards = array( );
		$this->_used_cards = array( );
		$this->_sets = array( );
		$this->_guess = '';

		// based on the difficulty, delete some of the cards
		if ( ! $difficult) {
			$new_cards = array( );

			// pick a trit and a value at random to hold constant
			$attr = mt_rand(0, 3);
			$val = mt_rand(0, 2);

			foreach ($this->_avail_cards as $key => $card) {
				if (substr($card, $attr, 1) == $val) {
					$new_cards[$key] = $card;
				}
			}

			$this->_avail_cards = $new_cards;
		}

		try {
			$this->_add_cards(12);
			$this->_solve_sets( );
		}
		catch (Exception $e) {
			throw new Exception($e->getMessage( ), $e->getCode( ));
		}
	}


	/** public function get_visible_cards
	 *		Gets the indexes for the visible cards
	 *
	 * @param void
	 * @return array of ints
	 */
	public function get_visible_cards( )
	{
		$indexes = array( );

		if (is_array($this->_visible_cards)) {
			foreach ($this->_visible_cards as $key => $card) {
				$indexes[$key] = array_search($card, $this->_avail_cards);
			}
		}

		return $indexes;
	}


	/** public function get_sets
	 *		Gets the current valid sets
	 *
	 * @param void
	 * @return array of arrays of ints
	 */
	public function get_sets( ) {
		return $this->_sets;
	}


	/** public function try_guess
	 *		Submit a guess for a valid set
	 *
	 * @param bool difficult
	 * @action initialize variables
	 * @return void
	 */
	public function try_guess($guess)
	{
		call(__METHOD__);

		$this->_guess = $guess;

		try {
			$this->_clean_guess( );
			$this->_replace_cards( );
		}
		catch (Exception $e) {
			throw new Exception($e->getMessage( ), $e->getCode( ));
		}

		return true;
	}


	/** protected function _solve_sets
	 *		Finds all the valid sets in the visible cards
	 *
	 * @param void
	 * @action finds the sets
	 * @return void
	 */
	protected function _solve_sets( )
	{
		call(__METHOD__);
		$sets = array( );

		if (is_array($this->_visible_cards)) {
			$num_cards = count($this->_visible_cards);

			// repeat once for each card, except the last two
			for ($i = 0; $i < ($num_cards - 2); ++$i) {
				// repeat once for every _other_ card, except the last one
				for ($j = ($i + 1); $j < ($num_cards - 1); ++$j) {
					$solution = $this->_solve_set($i, $j);

					// we have our solution card, look for it
					$index = array_search($solution, $this->_visible_cards);
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

		$this->_sets = $sets;
	}


	/** protected function _solve_set
	 *		Finds the third card in the set, given the first two
	 *
	 * @param int card index
	 * @param int card index
	 * @action finds the third card in the set
	 * @return string the third card in the set's string code
	 */
	protected function _solve_set($index1, $index2)
	{
		call(__METHOD__);
		$card1 = $this->_visible_cards[$index1];
		$card2 = $this->_visible_cards[$index2];

		if ($card1 == $card2) {
			throw new Exception(__METHOD__.': Same cards given for set');
		}

		$card = '';

		for ($i = 0; $i < 4; ++$i) {
			$attr1 = (int) $card1[$i];
			$attr2 = (int) $card2[$i];

			// the truncated value is 3 if the supplied values are identical
			$card .= (3 == trunc( ~ ($attr1 ^ $attr2))) ? $attr1 : trunc( ~ ($attr1 ^ $attr2));
		}

		return $card;
	}


	/** protected function _clean_guess
	 *		Cleans the submitted guess and looks for invalid data
	 *
	 * @param void
	 * @action cleans the guess
	 * @return void
	 */
	protected function _clean_guess( )
	{
		call(__METHOD__);

		if ('none' == $this->_guess) {
			return true;
		}

		if ('' == $this->_guess) {
			throw new Exception('The guess was empty');
		}

		if ( ! is_array($this->_guess)) {
			$this->_guess = explode(',', $this->_guess);
		}

		array_walk($this->_guess, create_function('&$v','$v = (int) trim($v);'));
		sort($this->_guess);

		if (3 != count($this->_guess)) {
			throw new Exception('Incorrect number of cards in guess');
		}
	}


	/** protected function _clean_guess
	 *		Cleans the submitted guess and looks for invalid data
	 *
	 * @param void
	 * @action cleans the guess
	 * @return void
	 */
	protected function _replace_cards( )
	{
		call(__METHOD__);
		if ('none' == $this->_guess) {
			// test if there are any sets
			if (0 == count($this->_sets)) {
				try {
					$this->_add_cards( );
					$this->_solve_sets( );
				}
				catch (Exception $e) {
					throw new Exception($e->getMessage( ), $e->getCode( ));
				}
			}
			else {
				throw new Exception('There is a set in the group');
			}
		}
		else if (is_array($this->_guess)) {
			// test to see if the selection is a set or not
			if (in_array(implode(',', $this->_guess), $this->_sets)) {
				$this->_remove_cards( );

				try {
					$this->_add_cards( );
				}
				catch (Exception $e) {
					if (1 != $e->getCode( )) {
						throw new Exception($e->getMessage( ), $e->getCode( ));
					}
				}

				$this->_solve_sets( );
			}
			else {
				throw new Exception('The given set is invalid');
				// TODO: tell why it's invalid
			}
		}
	}


	/** protected function _remove_cards
	 *		Remove valid set cards from the pile
	 *
	 * @param void
	 * @action moves cards from the visible pile to the used pile
	 * @return void
	 */
	protected function _remove_cards( )
	{
		call(__METHOD__);

		// remove the cards
		foreach ($this->_guess as $card_index) {
			unset($this->_visible_cards[$card_index]);
		}
	}


	/** protected function _add_cards
	 *		Add cards to the visible cards
	 *
	 * @param int optional number of cards (default: 3)
	 * @action adds cards to the visible pile
	 * @return void
	 */
	protected function _add_cards($num = 3)
	{
		call(__METHOD__);

		// don't add cards if we have more than 12 showing
		if (12 > count($this->_visible_cards)) {
			dump('ADDING '.$num.' CARDS');
			for ($i = 0; $i < $num; ++$i) {
				// grab the cards remaining
				$remain = array_diff($this->_avail_cards, $this->_used_cards);
				dump($remain);

				if ( ! count($remain)) {
					break;
				}

				dump($i.' ================================================');
				// grab a card from the deck
				// (one that we haven't used yet)
				$k = 0;

				do {
					dump('CARD NOT FOUND');
					$index = array_rand($remain);
					$card = $remain[$index];
					dump($index);
					dump($card);
					dump($this->_avail_cards[$index]);
					++$k;
				} while (isset($this->_used_cards[$index]) && ((10 * count($remain)) > $k));

				//put the card in the visible cards
				for ($j = 0; $j < ($num + count($this->_visible_cards)); ++$j) {
					if ( ! isset($this->_visible_cards[$j])) {
						dump('ADDING CARD');
						$this->_visible_cards[$j] = $card;
						break;
					}
				}

				// put the card in the used pile as well
				$this->_used_cards[$index] = $card;
			}

			// sort the used pile by key
			ksort($this->_used_cards);
			ksort($this->_visible_cards);
		}

		dump('FILLING HOLES');
		for ($i = 0, $count = count($this->_visible_cards); $i < $count; ++$i) {
			dump($i);
			dump($this->_visible_cards[$i]);
			if ( ! isset($this->_visible_cards[$i])) {
				dump('filled');
				$this->_visible_cards[$i] = array_pop($this->_visible_cards);
			}
		}

		if ( ! count($remain)) {
			throw new Exception(__METHOD__.': No more cards available', 1);
		}
	}

} // end Set class


// other functions used in this class


/** protected function dump
 *		Used for debugging output
 *
 * @param mixed var to output
 * @param bool bypass debug setting
 * @action outputs var to browser
 * @return void
 */
if ( ! function_exists('dump')) {
	function dump($var = false, $bypass = false) {
		if ($bypass || (defined('DEBUG') && DEBUG)) {
			var_dump($var);
		}
	}
	function call($var = false, $bypass = false) { return dump($var, $bypass); }
}


/** protected function trunc
 *		Truncates the binary representation of
 *		the given number from the LEFT side (highest bit)
 *		i.e.- for trunc(-3, 2) the value is 1111111111111101 (-3) (16 bit),
 *			and the output truncated value is 1 (01)
 *
 * @param int value to truncate
 * @param int number of bits to return
 * @return int decimal value of truncated input
 */
if ( ! function_exists('trunc')) {
	function trunc($val, $num = 2)
	{
		$bin = str_pad(decbin($val), $num, '0', STR_PAD_LEFT);
		$trunc = substr($bin, -$num);
		$dec = bindec($trunc);

		return $dec;
	}
}



// the rest of these functions aren't used in the class
// but they are good to have around

function get_magic_square($super = false)
{
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


function select_other_attribute($attribute)
{
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

function solveCard($card1, $card2)
{
	if ((4 != strlen($card1)) || (4 != strlen($card2))) {
		return false;
	}

	for ($k = 0; $k < 4; ++$k) {
		$solution[$k] = solveAttribute($card1[$k], $card2[$k]);
	}

	ksort($solution);
	return implode('', $solution);
}


function generateCard($card, $numAttr = 1)
{
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

?>