<?php
require('system/lib.common-inside.php');
require('system/lib.session-inside.php');

Session_Inside::checkuselev();
$__sessuser = &Session_Inside::shortcut(SESSINS_KEY_USER);
CYCOM_Inside::register_user($__sessuser);

require('system/db/class.CYCOM_DB_Usr_Accessor.php');
$USR_R = new CYCOM_DB_Usr_Accessor();
$event_monitor = $USR_R->read_event_monitor($__sessuser->get_uid());

require('cte/engine/class.CTE.php');
CTE::register_var('sessuser', $__sessuser);
CTE::register_var('new_gst', $event_monitor['new_gst_num']);
CTE::register_var('new_mil', $event_monitor['new_mil_num']);
CTE::register_var('new_rel', $event_monitor['new_rel_num']);
CTE::register_var('new_frm', $event_monitor['new_frm_num']);
CTE::create_var('username', $__sessuser->get('username'));
CTE::display('struct_inside.tpl');
?>
