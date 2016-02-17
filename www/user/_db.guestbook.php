<?php
/**
 * Database file for user guestbook.
 *
 * @package user.guestbook
 * @since 2005-02-27
 * @version 
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

require_once('system/lib.common-inside.php');
require_once('system/lib.session-inside.php');

Session_Inside::checkuselev();
$__sessuser = &Session_Inside::shortcut(SESSINS_KEY_USER);
CYCOM_Inside::register_user($__sessuser);

require('_common.guestbook.php');
require('function.postvars_set.php');
require('function.getvars_set.php');

require('system/lib.ajax.php');

if(!Cylib__getvars_set('a'))
  CYCOM_Inside::err('_db_gst__missing_get_var', 'Missing $_GET[a].', null, __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());

/**
 * @desc Action switch. The guestbook owner id is assumed to be stored in $_GET[user_id].
 */
switch($_GET['a'])
{
  case ACTION__STORE:
    if(!Cylib__postvars_set('user_id,message'))
      CYCOM_Inside::err('_db_gst_store__missing_post_vars', 'Missing $_POST[user_id] and/or $_POST[message].', null,
                        __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());
    
    // Check values:
    require_once('system/db/constraints.gst.php');
    if(strlen($_POST['message']) < CONSTR_GST__MESSAGE_MINLEN || strlen($_POST['message']) > CONSTR_GST__MESSAGE_MAXLEN)
      CYCOM_Inside::err('_db_gst_store__message_length_invalid', 'Message too long/short.', null, __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());
    if(strlen($_POST['user_id']) != 38)
      CYCOM_Inside::err('_db_gst_store__user_id_length_invalid', 'User_id length was invalid', null,
                        __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());
    
    $thread_id = (key_exists('thread_id', $_POST) && strlen($_POST['thread_id']) == 38 ? $_POST['thread_id'] : null);
    
    require('system/db/class.DB_Gst_Manipulator.php');
    $GST_m = new DB_Gst_Manipulator();
    $msg_stored = $GST_m->insert_message($_POST['user_id'], $__sessuser->get_uid(), $_POST['message'], true, $thread_id);
    $GST_m->destroy();
    
    // Log error if message store failed:
    if(!$msg_stored)
      CYCOM_Inside::err('_db_gst_store__msg_storage_failed', 'Failed to store message.', null, __FILE__, __LINE__, ERR_ACTION_CONTINUE, true, get_defined_vars());
    
    AJAX::result($msg_stored);
    break;
  
  case ACTION__DELETE:
    if(!Cylib__getvars_set('mid'))
      CYCOM_Inside::err('_db_gst_delete__missing_post_vars', 'Missing $_GET[mid].', null, __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());
    
    require('system/db/class.DB_Gst_Manipulator.php');
    $GST = new DB_Gst_Manipulator();
    $msg_deleted = $GST->delete_message($_GET['mid'], $__sessuser->get_uid());
    $GST->destroy();
    
    // Log error if message delete failed:
    if(!$msg_deleted)
      CYCOM_Inside::err('_db_gst_delete__msg_delete_failed', 'Failed to delete message.', null, __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
    
    AJAX::result($msg_deleted);
    break;
  
  case ACTION__GET:
    if(!Cylib__getvars_set('userid'))
      CYCOM_Inside::err('_db_gst_get__missing_get_vars', 'Missing $_GET[userid].', null, __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());
    
    require('system/db/class.DB_Gst_Retriever.php');
    $GST = new DB_Gst_Retriever();
    if(Cylib__getvars_set('page'))
      $messages = $GST->get_messages($_GET['userid'], ($_GET['page']-1));
    else
      $messages = $GST->get_messages($_GET['userid']);
    $GST->destroy();
    
    AJAX::cteresult('rdm.gst.tpl', LANG, array('messages' => $messages));
    break;
  
  default:
    CYCOM_Inside::err('_db_gst__unknown_action', 'Unknown $_GET[a].', null, __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
}
?>