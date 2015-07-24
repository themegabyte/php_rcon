<?php
// global settings (all games / all servers)

$refresh_rate = 40;				// enter a number of seconds to automatically refresh the window in

$interface_language = 'en';     // default language
/* $interface_language
	en - english
	cz - czech
	de - german
	es - spanish
	fr - french
	fi - finnish
	hu - hungarian
	it - italian
	nl - dutch
	no - norwegian
	pl - polish
	rs - serbian
	sk - slovak
*/

$disable_icons = false;			// use text-only interface variant

$suggest_enable = true;			// enable command and variable hints
$suggest_partial = false;		// enable only partial matches of commands; if disabled, compared to the beginning only

$match_user_and_server = false;
/* $match_user_and_server
	true | false;  if true, users will see only their own servers
	server file names must start with the username - case insensitive, eg. <username>.inc.php or <username>-main.inc.php
	user 'admin' is special - he has access rights to all servers
	if users get 'No servers available' error, it means they have no server configured for them.
*/

$userconfig_enable = true;		// enable changing admin passwords and customize language
$userconfig_pass_minchars = 6;	// minimum password length
$pw_salt = '$1$Rs7Et5Tf';		// unique password encryption parameter; if changed, all passwords have to be reset

$log_enable = true;			// enable logging of commands


$screenshots_enable = true;		// enable showing screenshots  , define $screenshots_path in server-specific config



$geoip_resolve = 4;
/* $geoip_resolve
	0 to disable
	1 to enable internal PHP function (apache module libapache_mod_geoip needed)
	2 to enable external system command 'geoiplookup IP' (result is expected in format 'uninteresting: CC, uninteresting', where CC is 2-letter Country code; if none received, '--' for unknown is returned)
	3 to enable external system command 'geoip-lookup IP' (returns only CC)
	4 to enable internal PHP library (first download and extract GeoIP.dat to php rcon root dir)
*/

$geoip_flags = true;			// true | false; if you want to enable flag images, enter true
$geoip_local_network = '--';	// if someone comes from internal IP range (10.0.0.0/8, 192.168.0.0/16, 172.16.0.0/12), assign a country code (use CAPITAL letters !on unix!)


// Custom links - favourite links
// string syntax: (button text)/(hyperlink opened in new window)
//            or: (title)           // insert a text to an extra line
//            or: (empty line)      // to leave an extra linebreak, duh :)

// $custom_links[] = 'RCon log/log.php';
// $custom_links[] = 'Country codes/http://www.maxmind.com/app/iso3166';

// Custom commands - favourite commands
// string syntax: (button text)[|icon mode[|custom icon]]/(command)
//            or: (title)           // insert a text to an extra line
//            or: (empty line)      // to leave an extra linebreak, duh :)
//            or: (title)/(button text)[|icon mode[|custom icon]]/(command)/(button text 2)[|icon mode[|custom icon]]/(command 2)/.....n   // displayed as  Title:  Button1  |  Button2  |  ...n

// $custom_cmd[] = 'Update PunkBuster/pb_sv_update';


// Custom commands - next to all players
// string syntax: (button text)[|icon mode[|custom icon]]/(command)/(player ID offset)
// `player ID offset` should be 0, use 1 for PunkBuster commands
// `icon mode` - 0 = text only, 1 = icon only, 2 = icon and text;  defaults are used for common commands like clientkick or pb_sv_getss if attribute is not set
// `custom icon` is a filename of an 16x16 image placed in "graphics/icons-custom/";  unknown commands without custom icon will display blue questionmarks
//
//
// if command contains %n, it is replaced by player ID, otherwise %n is added to the end behind the command
//
// if command contains %m, a query pops up for you to enter a message
//    then you have to specify position for player ID by %n
//    and remember to enclose the message into quotes: &quot;

$custom_cmds[] = 'Screenshot/pb_sv_getss/1';
// $custom_cmds[] = 'Temp ban/tempbanclient/0';
// $custom_cmds[] = 'Ban/banclient/0';
// $custom_cmds[] = 'PB Kick/pb_sv_kick/1';
// $custom_cmds[] = 'PB Ban/pb_sv_ban/1';
// $custom_cmds[] = 'PB Kick+msg/pb_sv_kick %n 15 &quot;%m&quot;/1'; //kick for 15 minutes and pop up a box for reason
// $custom_cmds[] = 'PB Kick+msg/pb_sv_kick %n 15 &quot;Do not spam please.&quot;/1'; //kick for 15 minutes and tell him a preset reason
// $custom_cmds[] = 'Spam kick|2|spam.png/pb_sv_kick %n 15 &quot;Do not spam please.&quot;/1'; //kick for 15 minutes and tell him a preset reason, use icon and text, the icon should be in graphics/icons-custom/spam.png
// $custom_cmds[] = 'PB Ban+msg/pb_sv_ban %n &quot;%m&quot;/1'; //ban player's guid, pop up a box for reason
// $custom_cmds[] = '*<a href="javascript:;" class="rconbtn rbc_banclient" onclick="var o=$(this).parents(\'tr\'); var u=\'../cod2_banlist/edit.php?name=\'+encodeURIComponent(o.attr(\'username\'))+\'&ip=\'+encodeURIComponent(o.attr(\'userip\')); window.open(u);">Ban</a>';  //just insert this code to each player (begins with *)


// Custom HTML next to map and gametype select boxes   (for mod used @ www.kafemlynek.cz)
//  - some use it to force map and gametype in the next vote - who needs to send a command with map name or gametype name / set the specific variable
//
// $customhtml_map = '&nbsp; | &nbsp;<a href="javascript:;" onclick="var v=$(\'select[name=map]\').val(); if (v==\'restart\') {v=\'\'}; CustomCmd(\'set am_next_map &quot;\'+v+\'&quot;\');">Force in next vote</a>';
// $customhtml_gametype = '&nbsp; | &nbsp;<a href="javascript:;" onclick="var v=$(\'select[name=gtype]\').val(); CustomCmd(\'set am_next_gametype &quot;\'+v+\'&quot;\');">Force in next vote</a>';

?>
