<?php
require ('init.inc.php');

if ((! isset($userconfig_enable)) || $userconfig_enable == false)
	{
	header ('Location: login.php');
	exit;
	}

$msg = false;


// change pwd? verify input
if (($_POST['old'] != '') || ($_POST['pass'] != '') || ($_POST['pass2'] != ''))
	{
	if (($_POST['old'] != '') && ($_POST['pass'] != '') && ($_POST['pass'] == $_POST['pass2']))
	    {
	    if (strlen($_POST['pass']) >= $userconfig_pass_minchars)
			{
			$pass = $_POST['old'];
			$passc = crypt($pass, $pw_salt);

			$do_change_pass = true;
			}
			else
			{
            $msg = strtr($lang['userconfig_error_newpw_short'],array('/'=>$userconfig_pass_minchars));
			}
		}
		else
		{$msg = $lang['userconfig_error_newpw'];}
	}

if ((isset($_GET['lang'])) && ($_GET['lang']!=''))
	{
	$tmp = trim(preg_replace("/[^a-z]/",'', strtolower($_GET['lang'])));
	if (is_file('languages/'.$tmp.'.inc.php'))
	    {
        $newlang = $tmp;
		$do_change_lang = true;
		}
	}



if (($do_change_pass) || ($do_change_lang))
	{

	$users = file('users.inc.php');
	$cnt = count($users);
	$userl = strtolower($_SESSION['sess_rcon_user']);
	for($i=0; $i<$cnt; $i++)
	    {
	    $cur = trim($users[$i]);
	    $cur = explode('=',$cur,2);
	    if (trim($cur[0]) == '$list_of_users[]')
	        {
			$cur = explode('\'',$cur[1],3);
			
			unset($u);
			list($u['name'],$u['pass'],$u['rights'],$u['lang']) = explode(' ',$cur[1],4);

			// import old pwd format
			if (!isset($u['rights']))
			    {
				$u['rights'] = '1';
				}
			if (!isset($u['lang']))
			    {
				$u['lang'] = $interface_language;
				}


			if ($userl == strtolower($u['name']))
			    {
			    $pok = false;
			    
				if ($do_change_pass)
				    {
					if ((($pass == $u['pass']) && (substr($u['pass'],0,1)!='$')) || //plain pw
					($passc == $u['pass'])) // encrypted pw
						{
						$u['pass'] = crypt($_POST['pass'], $pw_salt);
						$pok = true;
						}
						else
						{$msg = $lang['userconfig_error_oldpw'];}
					}

				if ($do_change_lang)
				    {
					$u['lang'] = $newlang;
					$_SESSION['sess_rcon_lang'] = $newlang;
					include 'languages/'.$newlang.'.inc.php';
					$pok = true;
					}

				if ($pok)
					{
					$users[$i] = '$list_of_users[] = \''.implode(' ',array($u['name'],$u['pass'],$u['rights'],$u['lang']))."';\r\n";
					if (file_put_contents('users.inc.php', implode('',$users)) !== false)
						{$msg = $lang['userconfig_write_success'];}
						else
						{$msg = $lang['userconfig_write_error'];}
					}
					
				break;
				}
			}
		}
	}

include 'header.inc.php';

echo '<div id="phprcon">';
echo '<h1>'.(($home_admin_link)?'<a href="'.$home_admin_link.'">Admin</a> / ':'').$lang['userconfig_title'].'</h1>';

if ($msg)
	{echo $msg.'<br />';}

echo '<h2>'.$lang['userconfig_pass_title'].'</h2>

<table width="200"><form action="'.$_SERVER['PHP_SELF'].'" method="POST">
<tr>
<td>'.$lang['userconfig_old_password'].':</td></tr><tr>
<td align="right"><input class=query type="password" name="old" size="25" AUTOCOMPLETE="off"></td></tr><tr>
<td>'.$lang['userconfig_new_password'].':</td></tr><tr>
<td align="right"><input class=query type="password" name="pass" size="25" AUTOCOMPLETE="off"></td></tr><tr>
<td>'.$lang['userconfig_confirm_new_password'].':</td></tr><tr>
<td align="right"><input class=query type="password" name="pass2" size="25" AUTOCOMPLETE="off"></td></tr><tr>
<td align="right"><input class="button" type="submit" value=" OK "></form></td>
</tr></table>

<br />

<h2>'.$lang['userconfig_lang_title'].'</h2>
<br />
';


// lets find some languages and respective flags
$flags = array();
$p = opendir('languages/');
while ($f = readdir($p))
	{
	if ($f[0] != '.')
	    {
		$tmp = explode('.',$f,2);
		if (($tmp[1] == 'inc.php') && ($tmp[0] != 'en'))
		    {
		    $flags[] = $tmp[0];
			}
		}
	}

sort($flags);
$flags = array_merge(array('en'),$flags);

foreach ($flags as $tmp)
	{
	$tmp2 = strtoupper($tmp);
	if ($tmp == 'en')
	    {$tmp2 = 'US';}
	$img = 'flags/'.$tmp2.'.png';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?lang='.$tmp.'" title="'.$tmp.'">';
	if (is_file($img))
	    {
		echo '<img src="'.$img.'" alt="'.$tmp2.'" />';
		} else {
		echo $tmp2;
		}
	echo '</a>&nbsp; ';
	}

echo '</div>';
include 'footer.inc.php';

?>
