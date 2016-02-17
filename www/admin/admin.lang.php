<?php
require_once('system/lib.common-inside.php');
require_once('system/lib.session-inside.php');

Session_Inside::checkuselev();
$__sessuser = &Session_Inside::shortcut(SESSINS_KEY_USER);
CYCOM_Inside::register_user($__sessuser);
CYCOM_Inside::require_permissions('LANG_DATA,LANG_STRUCT', false);

require('cte/engine/class.CTE.php');
CTE::set_language(LANG);

if(!key_exists('manage', $_GET))
{
  CTE::display('admin/admin.lang.tpl');
}
else
{
  switch($_GET['manage'])
  {
    case 'struct':
      require('system/db/constraints.lang.php');
      CTE::display('admin/admin.lang-struct.tpl');
      break;
    case 'data':
      CTE::display('admin/admin.lang-data.tpl');
      break;
  }
}
?>