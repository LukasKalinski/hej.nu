<?php
require('system/lib.common-inside.php');
require('system/lib.session-inside.php');

Session_Inside::checkuselev();
$__sessuser = &Session_Inside::shortcut(SESSINS_KEY_USER);
CYCOM_Inside::register_user($__sessuser);

require('system/db/class.CYCOM_DB_Usr_Accessor.php');
require('system/db/constraints.usr.php');
require('classes/class.PresEdit.php');
require('_common.php');

$USR_R = new CYCOM_DB_Usr_Accessor();
$__dbusr = $USR_R->get_by_id($__sessuser->get_uid(), 'pres_raw,pres_css_raw', 'pres');
$USR_R->destroy();


require('cte/engine/class.CTE.php');
CTE::set_language(LANG);
CTE::register_var('dbuser', $__dbusr);
CTE::create_var('PE', new PresEdit());
CTE::display('config/config.ext.presedit.tpl');
?>