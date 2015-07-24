<?php
// default language - if some entries are missing in outdated lang files, values from English one are used

if (is_file('languages/en.inc.php'))
	{include('languages/en.inc.php');}
	
if (($interface_language != '') && ($interface_language != 'en'))
	{
	if (is_file('languages/'.$interface_language.'.inc.php'))
		{include('languages/'.$interface_language.'.inc.php');}
	}

?>
