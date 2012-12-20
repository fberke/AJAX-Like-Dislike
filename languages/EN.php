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

$ALD ['LIKEBUTTON_TOOLTIP'] = 'I like this';
$ALD ['LIKEBUTTON_TEXT'] = 'Like';
$ALD ['DISLIKEBUTTON_TOOLTIP'] = 'I don\'t like this';
$ALD ['DISLIKEBUTTON_TEXT'] = 'Dislike';
$ALD ['THEAD_VOTES'] = 'Votes';
$ALD ['THEAD_PERCENT'] = 'Graph/Percent';
$ALD ['TBODY_LIKE_SG'] = 'like';
$ALD ['TBODY_LIKE_PL'] = 'likes';
$ALD ['TBODY_DISLIKE_SG'] = 'dislike';
$ALD ['TBODY_DISLIKE_PL'] = 'dislikes';
$ALD ['JUSTLIKE_SG'] = 'person likes this';
$ALD ['JUSTLIKE_PL'] = 'people like this';

?>