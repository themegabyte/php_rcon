<?php
// here is a place to enter specific settings for this server

$server_ip = 'localhost';
$server_port = 28960;
$server_rconpass = 'fsdafasd';

$server_timeout = 5;			// enter a number of seconds before connection to server times out; default=5 (try lower for increased performance, higher for troubleshooting)
$server_buffer = 1024;			// enter a number of bytes; decrease if you receive only a part of playerlist, increase to speed up
$server_buffer_results = 2048;	// enter a number of bytes; decrease if you receive only a part of returned results, increase to speed up
$server_extra_wait = false;		// true | false; if problems with receiving playerlist occur, enable
$server_extra_footer = true;	// true | false; if problems with receiving playerlist occur, enable

// $list_of_gtypes[] = 'utd UT Domination';
// $list_of_maps[] = 'mp_silotown Silotown';     // extra maps for this server

$screenshots_path = '/usr/games/cod2/cod2/pb/svss';		// path must contain the file pbsvss.htm, it may be local or remote (beginning with http:// or ftp://user:pass@hostname/ , it may be limited to webserver's IP)

?>