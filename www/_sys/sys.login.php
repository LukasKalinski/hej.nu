<?php  
/**
 * User login/authentication.
 *
 * @package htdocs/sys/sys_login
 * @since 2005-03-21
 * @version 2006-06-06
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

require('system/lib.common-outside.php');
require('system/lib.session-outside.php');

Session_Outside::checkuselev();

// Require specified post keys.
require('function.postvars_set.php');
if(!Cylib__postvars_set('pwd_check,username,password'))
  CYCOM_Outside::err('sys_login__missing_required_post_keys', 'Missing required post keys.', null,
                     __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());

// Check that user visited login page before requesting this file.
if(!Session_Outside::flag_isset('login_page_passed'))
  CYCOM_Outside::err('sys_login__missing_flag', 'Missing flag indicating that login page has been passed.', null,
                     __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());

// Check if admin check is requested.
$perform_admin_check = false;
if(substr($_POST['username'], 0, 1) == '@')
{
  $_POST['username'] = substr($_POST['username'], 1);
  $perform_admin_check = true;
}

// Check that username is valid.
require('system/db/constraints.usr.php');
if(!preg_match(CYCOM_DB_Usr_Constraints::USERNAME_REGEX, $_POST['username']))
  CYCOM_Outside::err('sys_login__username_not_valid', 'Invalid username supplied.', null, __FILE__, __LINE__, ERR_ACTION_REDIRECT, true, get_defined_vars());

// Get user data from database.
require('system/db/class.CYCOM_DB_Usr_Accessor.php');
$usr_r = new CYCOM_DB_Usr_Accessor();
$user = $usr_r->get_user_by_name($_POST['username'], 'id,dob,gender,password_hash,language_id,account_status');

// No user was found.
if($user == null)
{
  Session_Outside::temp_set('username', $_POST['username']);
  CYCOM_Outside::msg('wrong_username_or_password', '/struct_outside.php', MSG_APPEAR_JS);
}

// We have a user.
switch($user['account_status'])
{
  // ## ACCOUNT IS ACTIVE:
  case 'AC':
    // Check password.
    if($_POST['pwd_check'] === md5(Session::temp_get('password_check_key').$user['password_hash']))
    {
      require('system/lib.session-inside.php');
      
      $user_is_admin = false;
      
      // Possible admin user.
      if($perform_admin_check)
      {
        require('system/db/class.DB_Adm_Retriever.php');
        $adm_r = new DB_Adm_Retriever();
        $admin_id = $adm_r->get_admin_id($user['id']);
        
        if(is_null($admin_id))
        {
          $adm_r->destroy();
          CYCOM_Outside::err('sys_login__no_admin_flags', 'User requested admin login and was not found in the admin-table.', null,
                             __FILE__, __LINE__, ERR_ACTION_REDIRECT, true, get_defined_vars());
        }
        else // We have an admin.
        {
          Session_Inside::init();
          $__sessuser = &Session_Inside::shortcut(SESSINS_KEY_USER);
          
          require('system/lib.common-inside.php'); // From now on we're INSIDE.
          
          $permissions = $adm_r->get_permissions($admin_id);
          $adm_r->destroy();
          
          if(!is_null($permissions))
          {
            $set_permissions = array();
            foreach($permissions as $permission)
              $set_permissions[md5($permission)] = true;
          }
          
          $__sessuser = new User_Session($user['id'], true, $set_permissions);
          unset($set_permissions);
          $user_is_admin = true;
        }
      }
      else
      {
        Session_Inside::init();
        $__sessuser = &Session_Inside::shortcut(SESSINS_KEY_USER);
        $__sessuser = new User_Session($user['id']);
        
        require('system/lib.common-inside.php'); // From now on we're INSIDE.
      }
      
      $usr_r->destroy();
      
      $__session_globals['lang'] = $user['language_id'];
      $__sessuser->set('username', $_POST['username']);
      $__sessuser->set('dob',      $user['dob']);
      $__sessuser->set('gender',   $user['gender']);
      
      // Create or replace cookie.
  		session_set_cookie_params(0);
      setcookie('username', '', -3600);
      setcookie('username', $__sessuser->get('username'), time()+60*60*24*60, '/');
      
      header('Location: /struct_inside.php');
      exit;
    }
    else
    {
      Session_Outside::temp_set('username', ($perform_admin_check ? '@' : '').$_POST['username']);
      CYCOM_Outside::msg('wrong_username_or_password', '/struct_outside.php', MSG_APPEAR_JS);
    }
    break;
  
  // ## ACCOUNT IS PENDING:
  case 'PE':
    Session_Outside::temp_set('username', $_POST['username']);
    CYCOM_Outside::msg('account_not_activated', '/struct_outside.php', MSG_APPEAR_JS);
    break;
}
?>
