<?php
require_once('system/lib.common-inside.php');
require_once('system/lib.session-inside.php');

Session_Inside::checkuselev();
$__sessuser = &Session_Inside::shortcut(SESSINS_KEY_USER);
CYCOM_Inside::register_user($__sessuser);

require('cte/engine/class.CTE.php');
CTE::create_var('username', $__sessuser->get('username'));
CTE::display('home/home.news.tpl');
?>