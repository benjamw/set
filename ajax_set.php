<?php

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

$Set = & $_SESSION['SET'];

if (isset($_POST['selection']) && ('' != $_POST['selection'])) {
	$Set->try_guess($_POST['selection']);
}
else if (isset($_POST['new']) && ('' != $_POST['new'])) {
	$Set->new_game((bool) $_POST['new']);
}

if ('' == $Set->get_error( )) {
	$html = '<div class="row">';

	$indexes = $Set->get_card_indexes( );

	$i = 1;
	foreach ($indexes as $key => $id) {
		$id = str_pad($id, 2, '0', STR_PAD_LEFT);
		$html .= '<img id="c'.$key.'" src="cards/'.$id.'.gif" alt="'.$id.'" />';
		$html .= (0 == ($i % (count($indexes) / 3))) ? '</div><div class="row">' : '';
		++$i;
	}

	$html .= '</div><div class="row"><input type="button" name="noset" id="noset" value="No Set" /></div>';

	if ($Set->debug) {
		$html .= '<div class="row"><input type="button" name="showsets" id="showsets" value="Show Sets" /></div>';
	}

	echo $html;
}
else {
	echo 'ERROR: '.$Set->get_error( );
}

?>