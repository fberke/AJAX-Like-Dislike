<?php

// include class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {	
	include(WB_PATH.'/framework/class.secure.php'); 
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php')) {
	include($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php'); 
} else {
	$subs = explode('/', dirname($_SERVER['SCRIPT_NAME']));	$dir = $_SERVER['DOCUMENT_ROOT'];
	$inc = false;
	foreach ($subs as $sub) {
		if (empty($sub)) continue; $dir .= '/'.$sub;
		if (file_exists($dir.'/framework/class.secure.php')) { 
			include($dir.'/framework/class.secure.php'); $inc = true;	break; 
		} 
	}
	if (!$inc) trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
}
// end include class.secure.php

$ALD = array ();

$ALD ['LIKEBUTTON_TOOLTIP'] = 'Gefällt mir';
$ALD ['LIKEBUTTON_TEXT'] = 'Gefällt mir';
$ALD ['DISLIKEBUTTON_TOOLTIP'] = 'Missfällt mir';
$ALD ['DISLIKEBUTTON_TEXT'] = 'Missfällt mir';
$ALD ['THEAD_VOTES'] = 'Stimmen';
$ALD ['THEAD_PERCENT'] = 'Grafik/Prozent';
$ALD ['TBODY_LIKE_SG'] = 'gefällt das';
$ALD ['TBODY_LIKE_PL'] = 'gefällt das';
$ALD ['TBODY_DISLIKE_SG'] = 'gefällt das nicht';
$ALD ['TBODY_DISLIKE_PL'] = 'gefällt das nicht';
$ALD ['JUSTLIKE_SG'] = 'Person gefällt das';
$ALD ['JUSTLIKE_PL'] = 'Personen gefällt das';

?>