<?php
// here is a place to enter general settings for all servers using this game

$list_of_gtypes[] = 'dm Deathmatch';
$list_of_gtypes[] = 'tdm Team Deathmatch';
$list_of_gtypes[] = 'sd Search and Destroy';
$list_of_gtypes[] = 're Retrieval';
$list_of_gtypes[] = 'bel Behind Enemy Lines';
//$list_of_gtypes[] = 'hq Headquarters'; //1.5

//$list_of_maps[] = 'mp_bocage Bocage'; //1.5
$list_of_maps[] = 'mp_brecourt Brecourt';
$list_of_maps[] = 'mp_carentan Carentan';
$list_of_maps[] = 'mp_chateau Chateau';
$list_of_maps[] = 'mp_dawnville Dawnville';
$list_of_maps[] = 'mp_depot Depot';
$list_of_maps[] = 'mp_harbor Harbor';
$list_of_maps[] = 'mp_hurtgen Hurtgen';
//$list_of_maps[] = 'mp_neuville Neuville'; //1.5
$list_of_maps[] = 'mp_pavlov Pavlov';
$list_of_maps[] = 'mp_powcamp Powcamp';
$list_of_maps[] = 'mp_railyard Railyard';
$list_of_maps[] = 'mp_rocket Rocket';
$list_of_maps[] = 'mp_ship Ship';
//$list_of_maps[] = 'mp_stalingrad Stalingrad'; //1.5
//$list_of_maps[] = 'mp_tigertown Tigertown'; //1.5

$list_of_weapons[] = 'scr_allow_bar';
$list_of_weapons[] = 'scr_allow_bren';
$list_of_weapons[] = 'scr_allow_enfield';
$list_of_weapons[] = 'scr_allow_fg42';
$list_of_weapons[] = 'scr_allow_kar98k';
$list_of_weapons[] = 'scr_allow_kar98ksniper';
$list_of_weapons[] = 'scr_allow_m1carbine';
$list_of_weapons[] = 'scr_allow_m1garand';
$list_of_weapons[] = 'scr_allow_mp40';
$list_of_weapons[] = 'scr_allow_mp44';
$list_of_weapons[] = 'scr_allow_nagant';
$list_of_weapons[] = 'scr_allow_nagantsniper';
$list_of_weapons[] = 'scr_allow_panzerfaust';
$list_of_weapons[] = 'scr_allow_ppsh';
$list_of_weapons[] = 'scr_allow_springfield';
$list_of_weapons[] = 'scr_allow_sten';
$list_of_weapons[] = 'scr_allow_thompson';

$lang['scr_allow_bar'] = 'BAR';
$lang['scr_allow_bren'] = 'Bren LMG';
$lang['scr_allow_enfield'] = 'Lee-Enfield';
$lang['scr_allow_fg42'] = 'FG42';
$lang['scr_allow_kar98k'] = 'Kar98k';
$lang['scr_allow_kar98ksniper'] = 'Scoped Kar98k';
$lang['scr_allow_m1carbine'] = 'M1A1 Carbine';
$lang['scr_allow_m1garand'] = 'M1 Garand';
$lang['scr_allow_mp40'] = 'MP40';
$lang['scr_allow_mp44'] = 'MP44';
$lang['scr_allow_nagant'] = 'Mosin-Nagant';
$lang['scr_allow_nagantsniper'] = 'Scoped Mosin-Nagant';
$lang['scr_allow_panzerfaust'] = 'Panzerfaust';
$lang['scr_allow_ppsh'] = 'PPSh';
$lang['scr_allow_springfield'] = 'Springfield';
$lang['scr_allow_sten'] = 'Sten';
$lang['scr_allow_thompson'] = 'Thompson';


$commands_disabled[] = 'rconpassword';
$commands_disabled[] = 'sv_fps';
$commands_disabled[] = 'sv_maxrate';
$commands_disabled[] = 'sv_maxclients';
$commands_disabled[] = 'cvardump';

$custom_cmd[]='Swap players, syntax: enter two player IDs to swap (opens pop-up)|2|bulb.png/set command swap %m';

$custom_cmds[] = 'Force Allies|1|allies.png/set command force allies %n/0';
$custom_cmds[] = 'Force Axis|1|nazi.jpg/set command force axis %n/0';
$custom_cmds[] = 'Force Spectator|1|bulb.jpg/set command force spectator %n/0';



$corrupted_join_char_fix = false;

?>
