<?php

require 'init.inc.php';

if ($_GET['ajax'] != '1')
	{
	include 'header.inc.php';
	echo '<div id="phprcon">';
	}

if ($_SESSION['sess_rcon_rights'] != 1)
	{

	die($lang['rights_error']);

	} else {
	
	if ($_GET['ajax'] != '1')
		{echo '<h1>'.(($home_admin_link)?'<a href="'.$home_admin_link.'">Admin</a> / ':'').'PHP RCon log</h1>';}

	$begin = true;
	$data = @fopen('log.data.php','r');
	if ($data)
	    {
		echo '<pre>';

		while ($s = fread($data, 1024))
			{
			if ($begin)
			    {
				$s = str_replace('<?php exit; ?'.">\n",'',$s);
				$begin = false;
				}

			echo htmlspecialchars($s);
			}

		echo '</pre>';
		fclose($data);
		}
	}
	

if ($_GET['ajax'] != '1')
	{
	echo '</div>';
	include 'footer.inc.php';
	}

?>