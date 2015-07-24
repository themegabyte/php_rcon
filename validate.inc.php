<?php
//session_save_path('/var/lib/php5');

if (!isset($no_more_headers))
	{
	session_name('php_rcon'); session_set_cookie_params(0,'','',false);
	session_start(); header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');

	error_reporting(E_ALL & ~E_NOTICE); // leave this as it is
	$no_more_headers = true;
	}

if (!isset($noredirect))
	{
	if (($_SESSION['sess_rcon_rights'] < 1) || ($_SESSION['sess_rcon_appdir'] != getcwd()))
		{
		session_destroy();
		header ('Location: login.php');
		exit;
		}
	}
?>
