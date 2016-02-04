<?php
// here is a place to enter specific settings for this server

$server_ip = 'localhost';
$server_port = 28960;
$server_rconpass = 'fsdafasd';

$server_timeout = 1;			// enter a number of seconds before connection to server times out; default=5 (try lower for increased performance, higher for troubleshooting)
$server_buffer = 1024;			// enter a number of bytes; decrease if you receive only a part of playerlist, increase to speed up
$server_buffer_results = 2048;	// enter a number of bytes; decrease if you receive only a part of returned results, increase to speed up
$server_extra_wait = false;		// true | false; if problems with receiving playerlist occur, enable
$server_extra_footer = true;	// true | false; if problems with receiving playerlist occur, enable

// $list_of_gtypes[] = 'utd UT Domination';
// $list_of_maps[] = 'mp_silotown Silotown';     // extra maps for this server

$screenshots_path = '/usr/games/cod2/cod2/pb/svss';		// path must contain the file pbsvss.htm, it may be local or remote (beginning with http:// or ftp://user:pass@hostname/ , it may be limited to webserver's IP)

$custom_cmd[]='Force all to Spectator|2|bulb.png/set command forceallspec';
$custom_cmd[]=' ';
$custom_cmd[]='Announce RTs|2|bulb.png/say ^7^1[^7Announcement^1]^7 ALL REAL TAGS, OR KICK^3!!!';
$custom_cmd[]='Custom Announcement|2|bulb.png/say ^7^1[^7Announcement^1]^7 %m';
$custom_cmd[]=' ';


$custom_cmd[]='Privacy Settings';
$custom_cmd[]='Get current password|2|lock.png/g_password';
$custom_cmd[]='Set password to "fw1"|2|lock.png/g_password fw1';
$custom_cmd[]='Set password to "m4tch"|2|lock.png/g_password m4tch';
$custom_cmd[]='Set password to "w4r"|2|lock.png/g_password w4r';
$custom_cmd[]='Set password to "pglmatch1" (DEFAULT! must be set after a match has ended)|2|lock.png/g_password pglmatch1';
//$custom_cmd[]='Set custom password (opens pop-up)|2|lock.png/g_password %m';

$custom_cmd[]=' ';
$custom_cmd[]=' ';
$custom_cmd[]='USE WITH CAUTION';
$custom_cmd[]='Re-Exec Funwar Settings|2|excl.png/exec fw';
$custom_cmd[]='Reset server config|2|excl.png/exec myserver_match_1.cfg';

?>
