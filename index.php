<?php

define('DEBUG', isset($_GET['DEBUG']));

require_once 'include/Set.class.php';

// set the session cookie parameters so the cookie is only valid for this game
$parts = pathinfo($_SERVER['REQUEST_URI']);

$path = $parts['dirname'];
if (empty($parts['extension'])) {
	$path .= $parts['basename'];
}
$path = str_replace('\\', '/', $path).'/';

session_set_cookie_params(0, $path);
session_start( );

// grab our set object from the session (if any)
call($_POST);
if (empty($_SESSION['SET'])) {
	$Set = new Set( );
	$_SESSION['SET'] = & $Set;
}
else {
	$Set = & $_SESSION['SET'];
}

if (isset($_POST['noset'])) {
	$_POST['guess'] = 'none';
}

if (isset($_POST['guess']) && ('' != $_POST['guess'])) {
	try {
		$Set->try_guess($_POST['guess']);
	}
	catch (Exception $e) {
		$error = $e->getMessage( );
	}
}
elseif (isset($_POST['showsets'])) {
	var_dump($Set->get_sets( ));
}
elseif (isset($_POST['new']) && ('' != $_POST['new'])) {
	$Set->new_game((bool) $_POST['new']);
}

dump($Set);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>

	<!-- lest edited: 2007-11-25 -->
	<title>SET&reg; Game</title>
	<!-- original URL: http://www.iohelix.net/set/ -->

	<!--
		set card images and gameplay used without permission
		originals can be found at: http://www.setgame.com

		CHANGE LOG - v0.8
		=========================================
		2007-05-28 v0.8
			- beta release
		=========================================

		TODO
		=========================================
		- add timer and scoring ability
		- output to user when there are no more cards
		- add 'inifinity' mode, where there is no 'used' pile
		- clean up and beautify the page
		- josh is making an instruction page   ;)
		=========================================

	-->

	<meta http-equiv="Content-Language"   content="en-us" />
	<meta http-equiv="Content-Type"       content="text/html; charset=iso-8859-1" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

	<meta name="description" content="SET Game" />
	<meta name="author"      content="Benjam Welker" />
	<meta name="copyright"   content="Benjam Welker" />

	<link rel="stylesheet" type="text/css" media="screen" href="css/set.css" />

<?php //*/ ?>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/jquery.livequery.js"></script>
	<script type="text/javascript" src="js/set.js"></script>
<?php //*/ ?>

</head>

<body>

	<h1>SET&reg; Game</h1>

	<h2>Play</h2>

	<div id="error"><?php if (isset($error)) { echo $error; } ?></div>

	<form action="" method="post" id="gameForm">
	<div id="cards"><div class="row"><?php

		$indexes = $Set->get_visible_cards( );

		$i = 1;
		foreach ($indexes as $key => $id) {
			$id = str_pad($id, 2, '0', STR_PAD_LEFT);
			echo '<img id="c'.$key.'" src="cards/'.$id.'.gif" alt="'.$id.'" />';
			echo ((0 == ($i % (count($indexes) / 3))) ? '</div><div class="row">' : '');
			++$i;
		}

		echo '</div><div class="row"><input type="submit" name="noset" id="noset" value="No Set" /></div>';

		if (defined('DEBUG') && DEBUG) {
			echo '<div class="row"><input type="submit" name="showsets" id="showsets" value="Show Sets" /></div>';
		}

	?><input type="hidden" name="guess" id="guess" value="" />
	</div>
	</form>

	<hr />

	<form action="" method="post"><div>
		<input type="radio" name="new" id="new1" value="0" /><label for="new1">Easy</label>
		<input type="radio" name="new" id="new2" value="1" checked="checked" /><label for="new2">Normal</label>
		<input type="submit" value="New Game" id="newSubmit" />
	</div></form>

	<hr />

	<p>If you click on a set and it does not clear, this means
	the game is over.  Click on 'New Game' to begin again.</p>

	<p>If you find any errors, please <a href="/contact.php">let me know</a></p>

</body>
</html>