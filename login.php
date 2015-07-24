<?php
$noredirect = true;

require 'init.inc.php';
$user_on_init = $_SESSION['sess_rcon_user'];

include 'header.inc.php';

echo '<div id="phprcon">';
echo '<h1>Admin</h1>';

if ($_GET['logoff'] == '1')
	{
	$_SESSION['sess_rcon_rights'] = 0;
	session_destroy();
	}

$user = $_POST['user'];

if ($user != '')
	{
	$_SESSION['sess_rcon_rights'] = 0;
	$userl = strtolower($user);
	$pass = $_POST['pass'];
	$passc = crypt($pass, $pw_salt);
	require 'users.inc.php';
	foreach ($list_of_users as $cur)
	    {
		unset($u);
		list($u['name'],$u['pass'],$u['rights'],$u['lang']) = explode(' ',$cur,4);

		// import old pwd format
		if (!isset($u['rights']))
		    {
			$u['rights'] = '1';
			}
		if (!isset($u['lang']))
		    {
			$u['lang'] = $interface_language;
			}

		if (($userl == strtolower($u['name']))&&
			((($pass == $u['pass']) && (substr($u['pass'],0,1)!='$')) || //plain pw
			($passc == $u['pass']))) // encrypted pw

			{
		    $_SESSION['sess_rcon_appdir'] = getcwd();
			$_SESSION['sess_rcon_user'] = $u['name'];
			$_SESSION['sess_rcon_lang'] = $u['lang'];
			
			if (($interface_language != $_SESSION['sess_rcon_lang']) && (is_file('languages/'.$interface_language.'.inc.php'))) // okamzite nacteni noveho jazyka
				{
				$interface_language = $_SESSION['sess_rcon_lang'];
				include 'languages/'.$interface_language.'.inc.php';
				}
				
			if ($u['rights'] == '1')
				{$_SESSION['sess_rcon_rights'] = 1;}
				else
				{$_SESSION['sess_rcon_rights'] = 2;}
			break;
			}
		}
	}


if ($user_on_init != $_SESSION['sess_rcon_user'])
	{
	$no_more_headers = true;
	require 'init.inc.php';
	}


if ($_SESSION['sess_rcon_rights'] > 0)
	{
	function InsertLink($name, $link)
		{
		echo '<a href="'.$link.'">'.$name.'</a><br>';
		}
	echo '<h2>'.$lang['login_logged_as'].': &nbsp; &nbsp; '.$_SESSION['sess_rcon_user']
		.(($userconfig_enable)?' &nbsp; | &nbsp; <a href="userconfig.php">['.$lang['login_userconfig'].']</a>':'')
		.' &nbsp; | &nbsp; <a href="'.$_SERVER['PHP_SELF'].'?logoff=1">['.$lang['login_logout'].']</a>'
		.'</h2><br>';

		if (count($servers)>1)
			{
			foreach ($servers as $i=>$n)
				{
				$n = explode(' ',$n,3);
				InsertLink('PHP RCon: '.$n[2],'index.php?server='.$i);
				}
			}
			else {
			InsertLink('PHP RCon','index.php');
		    }
		} else {
	
	echo '
<h2>'.$lang['login_please_enter'].'.</h2>
<form action="'.$_SERVER['PHP_SELF'].'" method="POST">
<table><tr>
<td width="60">'.$lang['login_name'].':</td>
<td><input class=query type="text" name="user" size="25"></td>
<td width="40">&nbsp;</td>
</tr><tr>
<td>'.$lang['login_password'].':</td>
<td><input class=query type="password" name="pass" size="25"></td>
<td width="40">&nbsp;</td>
</tr><tr>
<td colspan="3" align="right"><input class="button" type="submit" value="'.$lang['confirm'].'"></td>
</tr></table></form>
';
	}


echo '</div>';
include 'footer.inc.php';
?>