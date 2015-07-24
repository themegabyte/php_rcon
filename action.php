<?php
$server_login_error = '<h2>Error: Not logged in.</h2>';

require ('init.inc.php');

$corrupt_response_separator = "\xff\xff\xff\xffprint\n";
$corrupt_response_sep_small = "\xff";
$corrupt_replacement = '<span style="background-color: #663C15">&nbsp;</span>';

$a = $_POST['a'];
$b = $_POST['b'];
$c = $_POST['c'];
$d = $_POST['d'];


if ($a == 'suggest')
	{
	echo '=';

	if (! is_file('commands/'.$game.'.txt')) {exit;}
	$fp = @ fopen('commands/'.$game.'.txt','r');
	if (! $fp) {exit;}

	$c = strtolower($b);
	$d = strlen($c);
	if (($suggest_partial) && ($d<2))
	    exit;
	while (!feof($fp))
		{
    	$buf = fgets($fp, 4096);
    	$ok = false;
		if ($suggest_partial)
		    {
		    $tmp = explode(' ',$buf, 2);
		    $tmp = strtolower($tmp[0]);
			if (strpos($tmp, $c) !== false)
				{
				$ok = true;
				}
			}
			else
			{
			if (strtolower(substr($buf,0,$d)) == $c)
				{
				$ok = true;
				}
			}
		if ($ok)
		    {
			$buf = trim(str_replace('"','',$buf));
			echo $buf."\r\n";
			}
		}

    fclose($fp);
	exit;
	}


$res = '';
$colors = $c;
$server_buffer_cur = $server_buffer_results;
if (!isset($corrupted_join_char_fix))
	{$corrupted_join_char_fix = false;}

function exitwithmsg($s)
	{
	global $connect;
	if ($connect) {@fclose($connect);}
	die('<h2>'.$s.'</h2>');
	}


// check PHP server configuration
$fnd = array();
foreach (array('fsockopen','socket_set_timeout','socket_get_status') as $fn)
	{
    if (! function_exists($fn))
        $fnd[] = $fn;
	}

if (count($fnd)>0)
	{exitwithmsg($lang['php_config_error'].': '. implode(', ',$s));}
unset($fnd);


// check php rcon server config
if ((!(strlen($server_ip)>0)) || (!($server_port>0)))
	{ exitwithmsg($lang['noserver_error']); }


$server_addr = "udp://" . $server_ip;
$connect = @ fsockopen($server_addr, $server_port, $re, $errstr, $server_timeout);
if (! $connect)
	{ exitwithmsg($lang['connection_error']); }

@socket_set_timeout ($connect, $server_timeout); //some servers block this command, silently ignore exception

function ColorizeName($s) {
	global $corrupt_response_separator, $corrupt_replacement, $corrupted_join_char_fix;
	$pattern[0]="^0";	$replacement[0]='</font><font color="black">';
	$pattern[1]="^1";	$replacement[1]='</font><font color="red">';
	$pattern[2]="^2";	$replacement[2]='</font><font color="lime">';
	$pattern[3]="^3";	$replacement[3]='</font><font color="yellow">';
	$pattern[4]="^4";	$replacement[4]='</font><font color="blue">';
	$pattern[5]="^5";	$replacement[5]='</font><font color="aqua">';
	$pattern[6]="^6";	$replacement[6]='</font><font color="#FF00FF">';
	$pattern[7]="^7";	$replacement[7]='</font><font color="white">';
	$pattern[8]="^8";	$replacement[8]='</font><font color="white">';
	$pattern[9]="^9";	$replacement[9]='</font><font color="gray">';
 	$pattern[10]=$corrupt_response_separator;	$replacement[10]=(($corrupted_join_char_fix)?$corrupt_replacement:'');

	$s = str_replace($pattern, $replacement, htmlspecialchars($s));
	$i = strpos($s, '</font>');
	if ($i !== false)
		{return substr($s, 0, $i) . substr($s, $i+7, strlen($s)) . '</font>';}
	else
		{return $s;}
	}

function RemoveJoiningChars($s) {
	global $corrupt_response_separator, $corrupt_replacement, $corrupted_join_char_fix;
	$s = htmlspecialchars($s);
	$s = str_replace($corrupt_response_separator, (($corrupted_join_char_fix)?$corrupt_replacement:''), $s);
	return $s;
	}

function RequestToGame($cmd)
	{
	global $server_rconpass, $connect, $server_extra_wait, $server_buffer_cur, $server_extra_footer;
	$send = "\xff\xff\xff\xff" . 'rcon "' . $server_rconpass . '" '.$cmd.(($server_extra_footer)?"\x0a\x00":'');
	fwrite($connect, $send);

	$output = (($server_extra_wait)?(fread ($connect, 1)):'');
		do {
		$status_pre = socket_get_status ($connect);
		if ((($server_extra_wait) && ($output != '')) || (! $server_extra_wait))
			$output .= fread ($connect, $server_buffer_cur);
		$status_post = socket_get_status ($connect);
		} while ($status_pre['unread_bytes'] != $status_post['unread_bytes']);

// 		// Debug server response
// 	    for ($i=0; $i < strlen($output); $i++) {
//         	$t = dechex(ord(substr($output,$i)));
//         	if ($t != 'a')
//             {$output2 .= '\\'.$t;}
//             else {$output2 .= "\n";}
//     		}
// 		$output = $output2;

	return $output;
	}

function LogCommand($s)
	{
	global $log_enable, $admin_name, $server_friendly_name, $lang;

	if ($log_enable === true)
		{
		$logfile = 'log.data.php';

		if (! is_file($logfile))
		    {
            if (! @file_put_contents($logfile, '<?php exit; ?'.">\n"))
				{
				echo '<h2>'.$lang['log_write_error'].'</h2>';
				return;
				}
			}
        @$fp = fopen($logfile,'a');
        if ($fp)
            {
	        fwrite($fp, date('Y-m-d H:i:s').' '.$admin_name.'@'.$server_friendly_name.': '.$s."\n");
	        fclose($fp);
            } else
            {
			echo '<h2>'.$lang['log_write_error'].'</h2>';
			return;
			}
		}
	}

switch ($a)
	{
	case 'plist':
	    //////// playerlist begin
	  	$server_buffer_cur = $server_buffer;
		$output = RequestToGame($b);

		if ($output == '')
			{exitwithmsg ($lang['connection_error']);}

		$tmp = strpos($output,'You must log in');
		if (($tmp !== false) && ($tmp < 15))
			{exitwithmsg ($lang['rcon_error']);}

		$output = str_replace($corrupt_response_separator, $corrupt_response_sep_small, substr($output,strpos($output, $corrupt_response_separator)+strlen($corrupt_response_separator)));

		$corrupt_response_separator = $corrupt_response_sep_small;
		$output = explode ("\n", $output);
		$color2 = false;
		$cnt = count($output)-2;

		$output_offset = -1;
		for($i=0; $i<$cnt+2; $i++)
			{if (strpos($output[$i], 'map: ') !== false)
			    {
			    $output_offset = $i;
			    continue;
			    }
			}

		if ($output_offset == -1)
			{exitwithmsg ($lang['rcon_error']);}

		$curmap = substr(str_replace($corrupt_response_separator, '', $output[$output_offset]), 5);
		$curmap_orig = $curmap;
		if ($list_of_maps)
			{
			foreach ($list_of_maps as $map)
				{
			    $t = explode(' ',$map,2);
			    if ($t[0] == $curmap)
					{
					$curmap = $t[1];
					break;
					}
				}
			}

		$custom_cmds[] = $lang['kick'].'/clientkick/0';
		$custom_cmdcount = count($custom_cmds);

		// make a list of country names
        if ($geoip_resolve>0)
			{
            $cc_countries = array();
			$tmp = file('flags/-.csv');
			foreach ($tmp as $s)
				{
				$s = explode(';',$s,2);
                $cc_countries[strtoupper($s[0])] = trim(str_replace('"','', $s[1]));
				}
			}

	    echo '~'; //notifies the main script to display the result in playerlist field

		echo '<table class="playerlist">
		<tr class="'.(($color2) ? 'odd' : 'even').' header">
		<td'.(($disable_icons)?' style="text-align: center;"':'').(($custom_cmdcount>1)?' colspan='.$custom_cmdcount:'').'>
		<div id="mapimg" onmouseout="MapImgShow(\'\',\'\');" onclick="MapImgShow(\'\',\'\');"></div>
		<a href="#" class="rconbtn rb_map" onclick="MapImgShow(\''.$curmap.'\',\''. $game.'/'.$curmap_orig .'\'); return false" title="Hotkey: M" id="mapimgbtn">'. $curmap .'</a></td>
		<td style="text-align: center;" width="'.(($disable_icons)?$colwidth:1).'"><a href="#" class="rconbtn rb_say" onclick="CmdMsg(\''.$lang['enter_public_message'].'\',\'say &quot;^6'.$admin_name.' ('.$lang['msg_prefix_all'].'): ^7%m&quot;\',\'\'); return false" title="say">'.$lang['say'].'</a></td>
		'.(($geoip_resolve>0)?'<td style="text-align: center;"><span class="rconbtn rb_flag">CC</span></td>':'').'
		<td><pre>'.$output[$output_offset+1]."</pre></td></tr>\n";


        function GetPlainName($name) // copied from original - IPluginCOD.php
			{
			$repname = $name;
			for($y=0; $y < 2; $y++) // Loop around a few time in case we have embedded colors!
			{
				for($x=0; $x < 10; $x++)
				{
					$repname = str_replace("^$x","",$repname);
				}
			}
			return $repname;
			}


		if ($geoip_resolve>0)
			{
			function stringrpos($haystack,$needle,$offset=NULL)
				{
				   if (strpos($haystack,$needle,$offset) === FALSE)
				     return FALSE;

				   return strlen($haystack)
				           - strpos( strrev($haystack) , strrev($needle) , $offset)
				           - strlen($needle);
				}

			function checkValidIp($ip)
				{
				if(!preg_match("`^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$`", $ip)) $return = FALSE;
				else $return = TRUE;
				$tmp = explode(".", $ip);
				if($return == TRUE){
					foreach($tmp AS $sub){
						$sub = $sub * 1;
				            if($sub<0 || $sub>256) $return = FALSE;
				  }
				}
				return $return;
				}

			function match_network ($nets, $ip) {
			    $return = false;
			    if (!is_array ($nets)) $nets = array ($nets);

			    foreach ($nets as $net) {
			        $rev = (preg_match ("/^\!/", $net)) ? true : false;
			        $net = preg_replace ("/^\!/", "", $net);

			        $ip_arr   = explode('/', $net);
			        $net_long = ip2long($ip_arr[0]);
			        $x        = ip2long($ip_arr[1]);
			        $mask     = long2ip($x) == $ip_arr[1] ? $x : 0xffffffff << (32 - $ip_arr[1]);
			        $ip_long  = ip2long($ip);

			        if ($rev) {
			            if (($ip_long & $mask) == ($net_long & $mask)) return false;
			        } else {
			            if (($ip_long & $mask) == ($net_long & $mask)) $return = true;
			        }
		    	}
		    	return $return;
				}

			if ($geoip_resolve == 4)
			    {
                include('geoip.inc.php');
                @$geoip_fp = geoip_open('GeoIP.dat',GEOIP_STANDARD);
                if (! $geoip_fp->filehandle)
                    {
                    echo '<h2>'.$lang['geoipdat_error'].'</h2>';
					}
				}
			}

        $name_startPos = strpos($output[$output_offset+1], 'name ');
        $name_endPos = strpos($output[$output_offset+1], 'lastmsg ')-1;

		for($i=3; $i<$cnt-$output_offset; $i++)
			{
			$line = $output[$output_offset+$i];

            $tmp = preg_match_all('`\^[0-9]`', $line, $res);
            $tmp = ($name_endPos-$name_startPos)+($tmp*2);

			$i_username = GetPlainName(trim(substr($line,$name_startPos,$tmp)));

            $line = ColorizeName($line);
			$pat[0] = "/^\s+/";
			$pat[1] = "/\s{2,}/";
			$pat[2] = "/\s+\$/";
			$rep[0] = "";
			$rep[1] = " ";
			$rep[2] = "";
			$t = preg_replace($pat,$rep,$line);

			$t = explode(' ', $t, 2);
		    if (strpos($t[0], '!') !== false) $t[0] = '';
		    $color2 = ! $color2;
		    $is_num = is_numeric($t[0]);

            if (preg_match("`\s([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})`", $line, $tmp))
                {
                $i_ip = $tmp[1];
				}
				elseif (strpos($line, ' loopback   ') !== false)
				{
                $i_ip = '127.0.0.1';
				}
				else
				{$i_ip = '';}

				if ($geoip_resolve>0)
				    {
		            unset($tmp);

				    if (($i_ip != '') && (strpos($i_ip, $corrupt_replacement) === false) && (checkValidIp($i_ip)))
					    {
					    if ($geoip_local_network != '')
					        {
							if (match_network(array('10.0.0.0/8','192.168.0.0/16','172.16.0.0/12','127.0.0.0/24'),$i_ip))
							    {$tmp = $geoip_local_network;}
							}
						if ($tmp == '')
							{
							switch ($geoip_resolve)
								{
								case 1:
									if (function_exists('geoip_country_code_by_name'))
										$tmp = geoip_country_code_by_name($i_ip);
									break;
								case 2:
									$tmp = trim(exec('geoiplookup '.$i_ip));
									$tmp = explode(': ',$tmp,2);
									$tmp = explode(',',$tmp[1],2);
									$tmp = $tmp[0];
									break;
								case 3:
									$tmp = trim(exec('geoip-lookup '.$i_ip));
									break;
								case 4:
									if ($geoip_fp->filehandle)
										$tmp = geoip_country_code_by_addr($geoip_fp, $i_ip);
									break;
								default: ;
								}
							}
						}

					if ($tmp == '')
						$tmp = '--';

					if (!isset($cc_countries[$tmp]))
						{$cc_countries[$tmp] = $tmp;}

                $i_cc = $tmp;
				} else {
				// geoip resolve disabled
				$i_cc = '';
				}

			echo '<tr class="'.(($color2) ? 'odd' : 'even').'" userid="'.$t[0].'" username="'. htmlspecialchars($i_username) .'" userip="'.$i_ip.'" usercc="'.$i_cc.'">';
			if ($is_num)
				{
				echo '<td style="text-align: center;" width="'.(($disable_icons)?$colwidth:1).'">';
				foreach ($custom_cmds as $ccmd)
					{
					if ($ccmd[0] == '*')
						{
						echo substr($ccmd,1);
						} else {
						$ccmd = explode('/',$ccmd,3);

						// detecting custom icon
		                $cmd_img_background = '';
						preg_match('`^[a-z0-9\_]+`i',$ccmd[1],$cmd_img_classname);
	                    $funcname = strtolower($cmd_img_classname[0]);
		                $cmd_img_classname = 'rconbtn rbc_'.$funcname;
		                $as_icon = in_array($funcname, $icons_only_default, true);

						$x = explode('|',$ccmd[0],3);
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

	            	    $ccmd[0] = $x[0];


						if (strpos($ccmd[1], '%m') !== false)
						    {
							$s = ($t[0]+ (int) $ccmd[2]);
							echo '<a href="#"'.$cmd_img_classname.$cmd_img_background.' onclick="CmdMsg(\''.addslashes($ccmd[1]).'\',\''.addslashes($ccmd[1]).'\',\''.$s.'\',this); return false" title="'.htmlspecialchars(html_entity_decode($ccmd[1])).'">'.htmlspecialchars($ccmd[0]).'</a>';
							} else {
							if (strpos($ccmd[1],'%n') === false)
							    {$s = $ccmd[1].' '.($t[0]+ (int) $ccmd[2]);} else
							    {$s = strtr($ccmd[1],array('%n'=>($t[0]+ (int) $ccmd[2])));}

							echo '<a href="#"'.$cmd_img_classname.$cmd_img_background.' onclick="CustomCmd(\''.addslashes($s).'\',this); return false" title="'.htmlspecialchars(html_entity_decode($s)).'">'.htmlspecialchars($ccmd[0]).'</a>';
							}
						}

					echo '</td><td style="text-align: center;" width="'.(($disable_icons)?$colwidth:1).'">';
					}

				if ((! isset($disable_whisper)) || ($disable_whisper === false))
					{echo '<a href="#" class="rconbtn rb_tell" onclick="CmdMsg(\''.addslashes($lang['enter_message']).'\',\'tell '.$t[0].' &quot;^6'.$admin_name.' ('.$lang['msg_prefix_priv'].'): ^7%m&quot;\',\''.$t[0].'\',this); return false" title="tell '.$t[0].'">'.htmlspecialchars($lang['whisper']).'</a>';}

				} else
			    {
			    echo '<td style="text-align: center;" colspan="'.($custom_cmdcount+1).'">&nbsp;';
				}

				if ($geoip_resolve>0)
				    {
				    echo '</td><td>';
					if (($geoip_flags) && (is_file('flags/'.$i_cc.'.png')))
					    {
						echo '<img class="flag" src="flags/'.$i_cc.'.png" alt="'.$cc_countries[$i_cc].' | '.$i_ip.'" title="'.$cc_countries[$i_cc].' | '.$i_ip.'" />';} else
					        {echo '<span title="'.$cc_countries[$i_cc].' | '.$i_ip.'">'.$i_cc.'</span>';}

					}

				echo '</td><td><pre>'.$line."</pre></td></tr>\n";
			}
		echo '</table><br />';
		fclose($connect);
		exit;
		//////// playerlist end
		break;

	case 'cmd':
		if ($b != '')
		    {
			$ok = true;
			// check limited rights
            $tmp = trim(preg_replace("/[^a-z_ ]/",' ', strtolower($b)));
            if (substr($tmp,0,4) == 'set ')
                {$tmp = trim(substr($tmp,4));}
			$tmp = explode(' ',$tmp,2);
			$tmp = $tmp[0];

			if ($b != 'g_gametype') // always allow reading gametype
			    {
				if (in_array($tmp, $commands_disabled, true))
				    {
				    $ok = false;
					}

				if (($_SESSION['sess_rcon_rights'] == 2) && (! in_array($tmp, $commands_enabled, true)))
				    {
				    $ok = false;
					}
				}

			if ($ok)
			    {
				if ($b != 'g_gametype') // nelogujeme get g_gametype (podobne jako playerlist status)
					{
					LogCommand($b.(($d!='')?' //'.$d:''));
					}
				$res = RequestToGame($b);
				}
				else
				{
				LogCommand('Unauthorized: '.$b);
				exitwithmsg($lang['rights_error']);
				}
			}
		break;

	}

fclose($connect);

if ($res == '')
	{
	exitwithmsg($lang['connection_error']);
	}

if ($res != $corrupt_response_separator)
    {
	$res = substr($res,strpos($res, $corrupt_response_separator)+strlen($corrupt_response_separator));
    } else
    {
	$res = 'OK'; // if result value is empty, but valid, return OK as default
	}

$res = trim($res);

if ($res != '')
    {
	echo '<pre>'.(($colors == '1')?ColorizeName($res):RemoveJoiningChars($res)).'</pre>';
	}

?>
