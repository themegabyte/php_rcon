<?php

require 'validate.inc.php';

$commands_disabled = array();
$commands_enabled = array();

require_once 'config.inc.php';

if ($_SESSION['sess_rcon_rights'] == 1)
	{include_once 'config.access.full.inc.php';} else
	{include_once 'config.access.limited.inc.php';}

include_once 'servers.inc.php';

// exclude servers belonging to other users
if (($match_user_and_server) && (count($servers)>0) && ($_SESSION['sess_rcon_user']!='admin'))
	{
	foreach ($servers as $n=>$v)
		{
		if (! (preg_match('`^[^ ]* '.preg_quote($_SESSION['sess_rcon_user'], '`').'`si', $v)==1))
			{unset($servers[$n]);}
		}
	}


$server_id = (int) $_GET['server'];

list($game, $server_name, $server_friendly_name) = explode(' ',$servers[$server_id],3);

if ($server_friendly_name == '')
	{$server_friendly_name = $server_name;}

if (is_file('games/'.$game.'.inc.php'))
	include('games/'.$game.'.inc.php');

if (is_file('servers/'.$server_name.'.inc.php'))
	include('servers/'.$server_name.'.inc.php');

if ((strlen($_SESSION['sess_rcon_lang'])>0) && (is_file('languages/'.$_SESSION['sess_rcon_lang'].'.inc.php')))
	{$interface_language = $_SESSION['sess_rcon_lang'];}

include_once 'language.inc.php';

// set_time_limit(30);         		// maximum script execution time in seconds

$admin_name = $_SESSION['sess_rcon_user'];	// used as a prefix of sent messages

$icons_only_default = array('clientkick','tell','say','pb_sv_kick','pb_sv_ban','banclient','tempbanclient','pb_sv_getss');  // list of commands which will be translated to icons if defaults should be used (other will remain as text by default)

$colwidth = 90;						// width of columns to the left of playerlist

?>
