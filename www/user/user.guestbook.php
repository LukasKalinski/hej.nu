<?php
/**
 * User guestbook
 *
 * @package user.guestbook
 * @since 2005-02-27
 * @version 
 * @copyright Cylab 2005-2006
 * @author Lukas Kalinski
 */

require_once('system/lib.common-inside.php');
require_once('system/lib.session-inside.php');

Session_Inside::checkuselev();
$__sessuser = &Session_Inside::shortcut(SESSINS_KEY_USER);
CYCOM_Inside::register_user($__sessuser);

require('system/db/class.DB_Gst_Retriever.php');
require('system/db/constraints.gst.php');
$GST = new DB_Gst_Retriever();
$messages = $GST->get_messages($_GET['userid'], (isset($_GET['page']) ? ($_GET['page']-1) : 0));

$dbuser = array();
if($_GET['userid'] == $__sessuser->get_uid())
{
  $dbuser['username'] = $__sessuser->get('username');
  $dbuser['gender']   = $__sessuser->get('gender');
  $dbuser['dob']      = $__sessuser->get('dob');
}
else
{
  require('system/db/class.CYCOM_DB_Usr_Accessor.php');
  $USR = new CYCOM_DB_Usr_Accessor();
  $dbuser = $USR->get_by_id($_GET['userid'], 'username,gender,dob');
}

require('_common.guestbook.php');
require('cte/engine/class.CTE.php');
CTE::set_language(LANG);
CTE::register_var('sessuser', $__sessuser);
CTE::register_var('GET', $_GET);
CTE::register_var('user', $dbuser);
CTE::create_var('page', (isset($_GET['page']) ? $_GET['page'] : 1));
CTE::register_var('messages', $messages);
CTE::display('user/user.guestbook.tpl');
?>