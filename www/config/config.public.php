<?php
require('system/lib.common-inside.php');
require('system/lib.session-inside.php');

Session_Inside::checkuselev();
$__sessuser = &Session_Inside::shortcut(SESSINS_KEY_USER);
CYCOM_Inside::register_user($__sessuser);

require('cte/engine/class.CTE.php');
CTE::set_language(LANG);
CTE::display('config/config.public.tpl');
?>
