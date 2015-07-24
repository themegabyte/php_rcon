<?php
// global settings for users with Limited access rights


// Commands allowed for users with limited access
$commands_enabled[] = 'say';
$commands_enabled[] = 'tell';
$commands_enabled[] = 'clientkick';
$commands_enabled[] = 'pb_sv_kick';
$commands_enabled[] = 'pb_sv_getss';
// $commands_enabled[] = 'pb_sv_ban';
// $commands_enabled[] = 'g_gametype';  // this is an exception, if enabled, it can be set; get is always enabled
// $commands_enabled[] = 'map_restart';
// $commands_enabled[] = 'map';
// $commands_enabled[] = 'fast_restart';



// $custom_cmd[] = 'Update PunkBuster/pb_sv_update';

// $custom_cmds[] = 'Temp ban/tempbanclient/0';
// $custom_cmds[] = 'Ban/banclient/0';
// $custom_cmds[] = 'PB Kick/pb_sv_kick/1';
// $custom_cmds[] = 'PB Ban/pb_sv_ban/1';
// $custom_cmds[] = 'PB Kick+msg/pb_sv_kick %n 15 &quot;%m&quot;/1'; //kick for 15 minutes and pop up a box for reason
// $custom_cmds[] = 'PB Kick+msg/pb_sv_kick %n 15 &quot;Do not spam please.&quot;/1'; //kick for 15 minutes and tell him a preset reason
// $custom_cmds[] = 'PB Ban+msg/pb_sv_ban %n &quot;%m&quot;/1'; //ban player's guid, pop up a box for reason


// $userconfig_enable = false;  // disable changing password/language
// $disable_whisper = true;     // disable whisper buttons


// hide direct input and settings to limited access users
$header .= '
<script type="text/javascript">
<!--
$(function(){  	$("#input_controls, #settings_frame").hide(); })
//-->
</script>
';

?>