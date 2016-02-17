<?php
require_once('system/lib.common.php');
require_once('system/lib.session-outside.php');

Session_Outside::checkuselev(false);

CYCOM::err('korv', __FILE__, null, null, __LINE__, ERR_ACTION_ABORT, true);

exit;
// Check for cached username.
$username = '';
if(Session_Outside::temp_isset('username'))
  $username = Session_Outside::temp_get('username');
elseif(key_exists('username', $_COOKIE))
  $username = $_COOKIE['username'];

// Reset outside session.
Session_Outside::clear_flags();
Session_Outside::clear_temp();

require_once('function.generate_key.php');
Session_Outside::set_flag('login_page_passed');
Session_Outside::temp_set('password_check_key', md5(Cylib__generate_key(10)));

require('cte/engine/class.CTE.php');
require_once('system/db/constraints.usr.php');
CTE::create_var('password_check_key', Session_Outside::temp_get('password_check_key'));
CTE::register_var('username', $username);
CTE::display('struct_outside.tpl');
?>
