<?php

// make sure this file is writeable for the webserver
//
// add users by creating a new line:
// $list_of_users[] = 'admin_name password[ rights[ language]]';
//
// rights can be either 0 [limited] or 1 [full, default]
// language is two letter lowercase char with according language file existing in languages/ dir [default is taken from config]
// - if both parameters are skipped, they will be filled with defaults - compatible with old format
//
// users can change their password and language from the web interface
// when they do, it is stored here (password in encrypted form)
// all passwords are case sensitive


$list_of_users[] = 'admin pass 1 en';
// $list_of_users[] = 'myserver pass 0';

?>