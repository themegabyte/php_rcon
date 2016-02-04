<?php
// PHP RCon 2.35
// Created by Ashus, 2008-2010
// this is a new branch

require ('init.inc.php');

$header .= '
	<script type="text/javascript" src="jquery.hotkeys.js"></script>
	<script type="text/javascript" src="scripts.js.php?server='.$server_id.'"></script>
	';

include 'header.inc.php';

echo '<div id="phprcon">
<div id="working"></div>
<h1>'.(($home_admin_link)?'<a href="'.$home_admin_link.'">Admin</a> / ':'').'PHP RCon';

if (count($servers)>1)
	{
	echo ' / <select name="server" class="dropdown" onchange="window.location=\'index.php?server=\'+this.value;">';
	foreach ($servers as $i=>$n)
		{
		$n = explode(' ',$n,3);
		echo '<option value="'.htmlspecialchars($i).'"'.(($i == $server_id)?' selected':'').'>'.htmlspecialchars($n[2]).'</option>';
		}

	echo '</select>';
	}

echo '</h1>';
$s = explode('/',$lang['page_refresh_remain'],2);
echo '<div id="refreshcontainer"><a href="#" class="rconbtn rb_refresh" onclick="RefreshNow(); return false" title="Hotkey: R">'.(($disable_icons)?$s[0].'</a> '.$s[1]:$s[0].' '.$s[1].'</a>').' <span id="refreshinfo"></span> &nbsp; <a href="#" class="rconbtn rb_pause" onclick="StartOrStopTimer(); return false" title="Hotkey: S">' . $lang['page_refresh_start_stop'] . '</a></div><br />';

$tmp = false;
if (is_array($custom_links)) {
	echo '<div id="custom_links">';
	foreach ($custom_links as $clnk)
		{
		if ($clnk!='')
		    {
			$clnk = explode('/',$clnk,2);
			if ($tmp)
            	echo ' &nbsp;&nbsp; | &nbsp;&nbsp; '."\n";
			if ($clnk[1] != '')
				{echo '<a href="'.$clnk[1].'" target="_blank">'.htmlspecialchars($clnk[0]).'</a>';}
				else {echo htmlspecialchars($clnk[0]);}
            $tmp = true;
			} else
 			{
 			echo '<br />';
 			$tmp = false;
 			}
		}
	echo '<br /><br />';
	echo '</div>';
	}


if (is_array($custom_cmd)) {
    echo '<div id="custom_commands">';
	foreach ($custom_cmd as $ccmd)
		{
		if ($ccmd!='')
		    {
			$i = 0;
			$ccmd = explode('/',$ccmd);
		    $c = count($ccmd);
			if (($c % 2) != 0)
			    {
			    echo htmlspecialchars($ccmd[0]).(($c>1)?': &nbsp;':'');
			    $i++;
				}
			for(;$i<$c;$i+=2)
			    {

				// detecting custom icon
                $cmd_img_background = '';
				preg_match('`^[a-z0-9\_]+`i',$ccmd[$i+1],$cmd_img_classname);
                $funcname = strtolower($cmd_img_classname[0]);
                $cmd_img_classname = 'rconbtn rbc_'.$funcname;
                $as_icon = in_array($funcname, $icons_only_default, true);

				$x = explode('|',$ccmd[$i],3);
				if (strlen($x[1])>0)
					{
					switch ($x[1])
						{
						case '0':
                            $as_icon = false;
							break;
						case '1':
							$as_icon = true;
							break;
						case '2':
							$as_icon = true;
							$cmd_img_classname .= ' ro_text';
							break;
						}
					}

				if ($as_icon)
					{
                    $cmd_img_classname = ' class="'.$cmd_img_classname.'"';
					if (strlen($x[2])>0)
						{
	                    $tmp = 'graphics/icons-custom/'.$x[2];

						if (is_file($tmp))
	                        {$cmd_img_background = ' style="background-image: url(\''.$tmp.'\');"';}
						}
					} else {
					$cmd_img_classname = '';
					}

                $ccmd[$i] = $x[0];

	            echo '<a href="#"'.$cmd_img_classname.$cmd_img_background.' onclick="'.((strpos($ccmd[$i+1], '%m') !== false)?'CmdMsg(\''.addslashes($ccmd[$i]).'\',\''.addslashes($ccmd[$i+1]).'\',\'\',null)':'CustomCmd(\''.addslashes($ccmd[$i+1]).'\')').'; return false" title="'.htmlspecialchars(html_entity_decode($ccmd[$i+1])).'">'.htmlspecialchars($ccmd[$i]).'</a>';

				if ($i+2 < $c)
				    echo '&nbsp; | &nbsp;';
				}
			echo '<br />'."\n";
			} else
 			{
 			echo '<br />';
 			}
		}
	echo '<br />';
	echo '</div>';
	}

echo '<div id="plist"></div>';
echo '<div id="res"></div>

<form method="POST" action="#" onsubmit="return HandleEnter();">
';


echo '<div id="input_controls">';

if ($suggest_enable)
	{$suggest_enable = (is_file('commands/'.$game.'.txt'));}

echo '
<table width="100%"><tr>
<td width="100" style="white-space: nowrap;">'.$lang['command'].':
</td><td width="*">
<input class="query" id="cmdbox" type="text" name="cmd" size="80" value="'.htmlspecialchars($lastcmd).'" style="width: 100%" autocomplete="off" onfocus="selected_obj = \'submitcmd\'; return true;" onkeydown="selected_obj = \'submitcmd\'; return true;"'.(($suggest_enable)?' onkeyup="SuggestInit();"':'').' />
'.(($suggest_enable)?'<div id="suggest" class="suggest_inv"></div>':'').'
</td><td width="1" style="white-space: nowrap;">
	<input class="button" type="button" id="submitcmd" onclick="SubmitCustomCmd(); return false;" value="'.$lang['confirm'].'" style="margin-left: 10px;" />
	<input type="checkbox" id="colors" name="colors" value="1"'.(((! isset($_GET['colors']))||((int) $_GET['colors'] != 0))?' checked':'').'><label for="colors" class="rconbtn rb_colors"> '.$lang['colorized_output'].'</label>
</td></tr>';

echo '<tr><td nowrap>'.$lang['game_type'].':</td><td colspan="2">
<select name="gtype" class="dropdown" onkeydown="selected_obj = \'gtype_apply_after_map\'; return true;">
<option value="" selected>&nbsp;</option>';

foreach ($list_of_gtypes as $gtype)
	{
	$t = explode(' ',$gtype,2);
	echo '<option value="'.htmlspecialchars($t[0]).'">'.htmlspecialchars($t[1]).'</option>';
	}

echo '</select>&nbsp;
<a href="#" onclick="SubmitChangeMapOrGametype(0); return false" class="rconbtn rb_get" title="Hotkey: G">'.$lang['get'].'</a> &nbsp;|&nbsp;
<a href="#" onclick="SubmitChangeMapOrGametype(1); return false" id="gtype_apply_after_map">'.$lang['apply_after_map'].'</a> &nbsp;|&nbsp;
<a href="#" onclick="SubmitChangeMapOrGametype(2); return false" id="gtype_apply_now">'.$lang['apply_now'].'</a>'.$customhtml_gametype.'

</td></tr>

<tr><td nowrap>'.$lang['map'].':</td><td colspan="2">
<select name="map" class="dropdown" onkeydown="selected_obj = \'map_apply_now\'; return true;">
<option value="restart" selected>[restart]</option>';

foreach ($list_of_maps as $map)
	{
	$t = explode(' ',$map,2);
	echo '<option value="'.htmlspecialchars($t[0]).'">'.htmlspecialchars($t[1]).'</option>';
	}

echo '</select>&nbsp;
<a href="#" onclick="SubmitChangeMapOrGametype(3); return false" id="map_apply_now">'.$lang['apply_now'].'</a>'.$customhtml_map.'
</td></tr></table>
</div>

<div id="settings_frame">
<a href="#" class="rconbtn ro_text rb_settings" onclick="SwitchDiv(\'settings\'); return false">'.$lang['settings'].'</a><br />';


echo '
<div id="settings">
<table><tr><td nowrap>'.$lang['public_password'].':</td><td>
<input class="query" type="password" size="30" name="pass" id="public_password" value="" onfocus="selected_obj = \'pass_confirm\'; return true;" onkeydown="selected_obj = \'pass_confirm\'; return true;" autocomplete="off" />&nbsp;
<a href="#" class="rconbtn rb_ok" onclick="SubmitChangePass(); return false" id="pass_confirm">'.$lang['confirm'].'</a>
';


if (count($list_of_weapons)>0)
	{
	echo '</td></tr>
	<tr><td valign="top" nowrap>'.$lang['weapons'].':</td><td>';

	function AddSetting($name)
		{
		global $lang, $disable_icons;
		echo '<a href="#" class="rconbtn rb_get" onclick="SubmitChangeWeapon(\''.$name.'\',-1); return false">'.$lang['get'].'</a>'.(($disable_icons)?'&nbsp; |&nbsp;':' ').'
		<a href="#" class="rconbtn rb_disable" onclick="SubmitChangeWeapon(\''.$name.'\',0); return false">'.$lang['turn_off'].'</a>'.(($disable_icons)?'&nbsp; |&nbsp;':' ').'
		<a href="#" class="rconbtn rb_enable" onclick="SubmitChangeWeapon(\''.$name.'\',1); return false">'.$lang['turn_on'].'</a>&nbsp; &nbsp;'
		 .htmlspecialchars((($lang[$name]=='')?$name:$lang[$name])).'<br />';
		}

	foreach ($list_of_weapons as $weapon)
		{
		AddSetting($weapon);
		}
	}
	
echo '</td></tr></table></div></div>
</form>
';

if (($screenshots_enable) && (isset($screenshots_path)) && (($_SESSION['sess_rcon_rights'] == 1) || (($_SESSION['sess_rcon_rights'] == 2) && (in_array('pb_sv_getss', $commands_enabled, true)))))
	{
	echo '<div id="screenshots_frame">
	<a href="#" class="rconbtn ro_text rb_screenshots" onclick="ShowHideScreenshots(); return false">'.$lang['screenshots'].'</a><br />

	<div id="screenshot_img"></div>
	<div id="screenshots"></div>
	';
	}

if (($log_enable) && ($_SESSION['sess_rcon_rights'] == 1))
	{
	echo '<div id="log_frame">
	<a href="#" class="rconbtn ro_text rb_log" onclick="ShowHideLog(); return false">'.$lang['log'].'</a><br />

	<div id="log">
	</div>';
	}

echo '<br /><br /><small>PHP RCon 2.34 created by <a href="http://ashus.ashus.net/" target="_blank">Ashus</a>, 2008-2010</small>

</div>';

include 'footer.inc.php';

?>
