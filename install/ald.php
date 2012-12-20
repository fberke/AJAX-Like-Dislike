<?php
//:Displays a like and/or dislike rating
//:id = Unique Identifier (up to 25 characters)
//:l = like only (no dislike displayed); true/false or 1/0
//:s = static (no rating possible); true/false or 1/0
//:p = private (logged-in users only); true/false or 1/0
//:Minimum call: [[ald?id=thisid]]
//:Full call:    [[ald?id=myid&l=1&s=1&p=1]]

global $wb;

if (!isset ($l)) $l = 0;
if (!isset ($s)) $s = 0;
if (!isset ($p)) $p = 0;

// timeout in hrs to to prevent voting from same IP
//  0 disables timeout
// -1 means timeout will never end and IPs are stored forever
$timeout = 6;

$return_value = 'There seems to be a problem with AJAX Like Dislike';

if (function_exists ('ALDRatingBar') && isset ($id)) {
	$return_value = ALDRatingBar ($id, $l, $s, $p, $timeout);
}

return $return_value;

?>
