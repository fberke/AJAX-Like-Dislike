<?php

// include class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {	
	include(WB_PATH.'/framework/class.secure.php'); 
} else {
	$oneback = "../";
	$root = $oneback;
	$level = 1;
	while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
		$root .= $oneback;
		$level += 1;
	}
	if (file_exists($root.'/framework/class.secure.php')) { 
		include($root.'/framework/class.secure.php'); 
	} else {
		trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
	}
}
// end include class.secure.php

include_once ('functions.php');

global $wb;

if (LANGUAGE_LOADED) {
	if (file_exists (WB_PATH.'/modules/ald/languages/'.LANGUAGE.'.php')) {
		require_once (WB_PATH.'/modules/ald/languages/'.LANGUAGE.'.php');
	} else {
		require_once (WB_PATH.'/modules/ald/languages/EN.php');
	}
}


function drawALDRating (
	$voted,
	$static,
	$justlike,
	$ajax,
	$vars) {

	global $ALD;
	
	// distinguish btw. [dis]like/likes and person/people
	if (!$justlike) {
		$tense_like = ($vars ['total_likes'] == 1) ? $ALD ['TBODY_LIKE_SG'] : $ALD ['TBODY_LIKE_PL'];
		$tense_dislike = ($vars ['total_dislikes'] == 1) ? $ALD ['TBODY_DISLIKE_SG'] : $ALD ['TBODY_DISLIKE_PL'];
	} else {
		$tense_like = ($vars ['total_likes'] == 1) ? $ALD ['JUSTLIKE_SG'] : $ALD ['JUSTLIKE_PL'];
	}
	
	$rater = array();
	
	if (!$ajax) {
		$rater [] = "\n".'<div class="aldbox">';
		$rater [] = '<div id="likebox_'.$vars ['id'].'" class="likebox_id">';
	}
	
	if ((!$static) && (!$voted)) {
		$rater [] = '<ul id="like_ul_'.$vars ['id'].'" class="likebuttons">';
		$rater [] = '<li><a class="rater" href="?id='.$vars ['id'].'&amp;ip='.$vars ['ip'].'&amp;rating=1&amp;ald=1" title="'.$ALD ['LIKEBUTTON_TOOLTIP'].'"><span>'.$ALD ['LIKEBUTTON_TEXT'].'</span></a></li>';
		(!$justlike) ? $rater [] = '<li><a class="rater" href="?id='.$vars ['id'].'&amp;ip='.$vars ['ip'].'&amp;rating=0&amp;ald=1" title="'.$ALD ['DISLIKEBUTTON_TOOLTIP'].'"><span class="noview">'.$ALD ['DISLIKEBUTTON_TEXT'].'</span></a></li>' : '';
	} else {
		$rater [] = '<ul class="likebuttons voted">';
		$rater [] = '<li><span>'.$ALD ['LIKEBUTTON_TEXT'].'</span></li>';
		(!$justlike) ? $rater [] = '<li><span class="noview">'.$ALD ['DISLIKEBUTTON_TEXT'].'</span></li>' : '';
	}
	$rater [] = '</ul>';
	
	if (!$justlike) {
		// calculate graphs
		$fullvotes = $vars ['total_likes'] + $vars ['total_dislikes'];
		$likesratio = @number_format ($vars ['total_likes'] / $fullvotes, 2) * 100;
		$dislikesratio = ($likesratio == 0) ? 0 : 100 - $likesratio;
		
		$rater [] = '<table>';
		$rater [] = '<thead class="noview">';
		$rater [] = '<tr>';
		$rater [] = '<th>'.$ALD ['THEAD_VOTES'].'</th>';
		$rater [] = '<th>'.$ALD ['THEAD_PERCENT'].'</th>';
		$rater [] = '</tr>';
		$rater [] = '</thead>';
		$rater [] = '<tbody>';
		$rater [] = '<tr>';
		$rater [] = '<td>'.$vars ['total_likes'].' '.$tense_like.'</td>';
		$rater [] = '<td class="barwrap">';
		$rater [] = '<span class="noview">'.$likesratio.' %</span>';
		$rater [] = '<div class="likebar" style="width: '.$likesratio.'%;" title="'.$likesratio.' %"></div>';
		$rater [] = '</td>';
		$rater [] = '</tr>';
		$rater [] = '<tr>';
		$rater [] = '<td>'.$vars ['total_dislikes'].' '.$tense_dislike.'</td>';
		$rater [] = '<td class="barwrap">';
		$rater [] = '<span class="noview">'.$dislikesratio.' %</span>';
		$rater [] = '<div class="dislikebar" style="width: '.$dislikesratio.'%;" title="'.$dislikesratio.' %"></div>';
		$rater [] = '</td>';
		$rater [] = '</tr>';
		$rater [] = '</tbody>';
		$rater [] = '</table>';
	} else {
		$rater [] = '<span>'.$vars ['total_likes'].' '.$tense_like.'</span>';
	}
	
	if (!$ajax) {
		$rater [] = '</div>';
		$rater [] = '</div>';
		$rater [] = "\n\n";
	}

	return join ("\n", $rater);
}


if (isset ($_REQUEST['id']) && isset ($_REQUEST['ip']) && isset ($_REQUEST['rating']) && isset ($_REQUEST['ald'])) {
	//getting the values
	$id_sent = preg_replace ("/[^0-9a-zA-Z\-_]/", "", $_REQUEST['id']);
	$ip_sent = preg_replace ("/[^0-9\-]/", "", $_REQUEST['ip']);
	$vote_sent = preg_replace ("/[^0-1]/", "", $_REQUEST['rating']);
	$ajax = (isset ($_REQUEST ['a'])) ? preg_replace ("/[^0-9]/", "", $_REQUEST['a']) : 0;
	
	$ip = ip2long (getIP ());
	
	// get votes from DB
	$sql = "SELECT total_likes, total_dislikes, justlike FROM ".TABLE_PREFIX."mod_ald_ratings WHERE id='$id_sent' ";
	$db = $database->query ($sql);
	
	$ratings = $db->fetchRow();
	
	$voted = userHasVoted ($id_sent, $ip_sent, 'mod_ald_blocked_ip');

	$total_likes = $ratings ['total_likes']; // how many votes total
	$total_dislikes = $ratings ['total_dislikes']; // total number of rating added together and stored
	$justlike = $ratings ['justlike'];
	
	//IP check when voting
	if (!$voted) { //if the user hasn't yet voted, then vote normally...
		if (($vote_sent == 1 || $vote_sent == 0) && ($ip == $ip_sent)) { // only 2 possible values for vote, make sure IP matches - no monkey business!
			
			($vote_sent == 1) ? $total_likes ++ : $total_dislikes ++;
			
			$update = "UPDATE ".TABLE_PREFIX."mod_ald_ratings SET total_likes='".$total_likes."', total_dislikes='".$total_dislikes."' WHERE id='".$id_sent."' ";
			$database->query ($update);
			
			$insert = "INSERT INTO ".TABLE_PREFIX."mod_ald_blocked_ip (rating_id, ip_addr, timestamp) VALUES ('$id_sent', '$ip', '".time ()."')";
			$database->query ($insert);
			
			$voted = true;
		}
		
	}
	
	// name of the div id to be updated | the html that needs to be changed
	if ($ajax) {
		
		$vars = array (
			'id' => $id_sent,
			'ip' => $ip_sent,
			'total_likes' => $total_likes,
			'total_dislikes' => $total_dislikes
			);
		$output = 'likebox_'.$id_sent.'|'.drawALDRating ($voted, false, $justlike, $ajax, $vars);
		echo $output;
		// this is important to prevent the whole page from being redrawn
		exit ();
	}	
}


function ALDRatingBar (
	$id,
	$justlike = false,
	$static = false,
	$private = false,
	$timeout = 6
	) {
	
	global $admin;
	global $database;

	$ip = ip2long (getIP ());

	// get votes, values for the current rating bar
	$sql = "SELECT total_likes, total_dislikes FROM ".TABLE_PREFIX."mod_ald_ratings WHERE id='$id' ";
	$db = $database->query ($sql);

	// create DB entry if id doesn't exist yet
	if (!isset ($db) || ($db->numRows() == 0)) {
		$sql = "INSERT INTO ".TABLE_PREFIX."mod_ald_ratings (`id`, `total_likes`, `total_dislikes`, `justlike`) VALUES ('$id', '0', '0', '$justlike')";
		$db = $database->query ($sql);
		// get values again, otherwise $ratings is empty at first call
		$sql = "SELECT total_likes, total_dislikes FROM ".TABLE_PREFIX."mod_ald_ratings WHERE id='$id' ";
		$db = $database->query ($sql);
	}
	
	$ratings = $db->fetchRow();

	
	// determine whether the user has voted, so we know how to draw the rating list
	unblockIPs ($timeout, 'mod_ald_blocked_ip');
	$voted = userHasVoted ($id, $ip, 'mod_ald_blocked_ip');
	
	
	$vars = array (
		'id' => $id,
		'ip' => $ip,
		'total_likes' => $ratings ['total_likes'],
		'total_dislikes' => $ratings ['total_dislikes']
		);
		
	if (($private && ($admin->is_authenticated())) || (!$private)) {
		return drawALDRating ($voted, $static, $justlike, false, $vars);
	} // private mode
}
?>
