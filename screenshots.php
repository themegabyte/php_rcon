<?php

require 'init.inc.php';

if (! (($_SESSION['sess_rcon_rights'] == 1) || (($_SESSION['sess_rcon_rights'] == 2) && (in_array('pb_sv_getss', $commands_enabled, true)))))
	{
	echo '<h2>'.$lang['rights_error'].'</h2>';
	exit;
	}

if (!isset($screenshots_path))
	{
	echo '<h2>'.$lang['screenshots_error'].'</h2>';
	exit;
	}

$screenshots_path = rtrim(str_replace('\\','/',$screenshots_path),'/').'/';

if (isset ($_GET['img']))
	{
	$fn = $screenshots_path.preg_replace('`[^a-z0-9]+`i','',$_GET['img']).'.png';
// 	if (is_file($fn))
		{
		header('Content-Type: image/png');
		$res = @readfile($fn);
		if ($res == false)
			{
			readfile('graphics/screenshot_empty.png');
			}
		}
	exit;
	} elseif ($_GET['ajax']!='1')
	{exit;}

$data = @file($screenshots_path.'pbsvss.htm');

if ($data == false)
	{
	echo '<h2>'.$lang['screenshots_error'].'</h2>';
	exit;
	}

$ss = array();

foreach ((array) $data as $c)
	{
	preg_match('`(pb[0-9]+)\.htm(.*?)\"([^\"]*?)\" (.*?) \[([0-9\.\: ]+)\]`i', $c, $a);
	$ss[$a[1]] = array($a[5],$a[3],$a[4]);
	}


if (count($ss)>1)
	{
	$tmp = array();
	foreach($ss as &$ma)
	    $tmp[] = &$ma[0];

	array_multisort($tmp, SORT_ASC, SORT_STRING, $ss);
	}

if (count($ss)>0)
	{
	function ColorizeName($s) {
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

		$s = str_replace($pattern, $replacement, htmlspecialchars($s));
		$i = strpos($s, '</font>');
		if ($i !== false)
			{return substr($s, 0, $i) . substr($s, $i+7, strlen($s)) . '</font>';}
		else
			{return $s;}
		}

	echo '<table style="width: 100%;">';
	$even = true;
	foreach ($ss as $k => &$c)
		{
		echo '<tr class="'.(($even)?'even':'odd').'"><td><a href="#" onclick="return ShowScreenshot(\''.$k.'\');">'.ColorizeName($c[1]).'</a></td><td>'.$c[0].'</td><td>'.htmlspecialchars($c[2]).'</td></tr>';
		$even = (!$even);
		} unset($c);

	echo '</table>';
	}

?>