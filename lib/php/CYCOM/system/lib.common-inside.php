<?php
/********************************************************************************\
 * File:          system/lib.common-inside.php
 * Description:   Common inside (=user is logged in) library.
 * Restrictions:  Use only in global context; means NOT in functions or classes.
 * Begin:         2006-01-15
 * Edit:          2006-03-04
 * Author:        Lukas Kalinski
 * Copyright:     2006- CyLab Sweden
\********************************************************************************/

require_once('lib.common.php');

class CYCOM_Inside extends CYCOM
{
  private static $session_user;
  
  /**
   * @desc Registers user session for common setup (dev etc...)
   * @param User_Session &$suser
   * @return void
   */
  public static function register_user(User_Session &$suser)
  {
    self::$session_user = &$suser;
    self::dev_setup();
  }
  
  /**
   * @desc Generates a GUI-adapted message and exits.
   * @param string $msg_id
   * @param string $url
   * @param int $flags              # MSG_APPEAR_JS or MSG_APPEAR_HTML
   * @param string $additional
   * @return void->exit
   */
  public static function msg($msg_id, $url, $flags=null, $additional_vars=null)
  {
    $flags = (is_null($flags) ? MSG_LBASE_INS : $flags|MSG_LBASE_INS);
    parent::msg($msg_id, $url, $flags, $additional_vars);
  }
  
  /**
   * @desc Throws a system abuse error if no permission exists. (NOT TESTED ENOUGH YET)
   * @param string $flags   # Flags separated by comas (,).
   * @param bool $require_all       # true=requires all flags, false=requires at least one flag.
   * @return void
   */
  public static function require_permissions($flags, $require_all)
  {
    $flags = explode(',', $flags);
    $flag_found = false;
    foreach($flags as $flag)
    {
      if(!$require_all && self::$session_user->has_permission($flag))
      {
        // At least one permission set when not required all: $flag_found is set to true and we can continue safely.
        $flag_found = true;
        break;
      }
      elseif($require_all)
      {
        if(self::$session_user->has_permission($flag))
        {
          $flag_found = true;
          continue;
        }
        
        // One permission failed when required all: $flag_found is false and we get a system error.
        $flag_found = false;
        break;
      }
    }
    if(!$flag_found)
      self::err('system_abuse__permissions_not_found', null, null, __FILE__, __LINE__, ERR_ACTION_ABORT, true, array('flags' => $flags));
  }
  
  /**
   * @desc Handles "inside" cycom errors.
   *       Setting string arguments to null will disable them.
   *       A pipe (|) in err_msg is interpreted as a line break.
   * 
   * @param string $e_id                # Error id: To distinguish between errors.
   * @param string $e_msg               # Error message
   * @param array $e_vars               # Error vars: Variables for merging with err_msg.
   * @param int $line                   # Line
   * @param int $action                 # Action to take on error (see ERR_ACTION_* constants for details).
   * @param bool $abuse_possible        # Abuse possible: true means the error could be triggered by some kind of abuse, false means it could not.
   * @param aarr $def_vars              # This should, if necessary, be set to get_defined_vars().
   * @param string $r_url               # Redirect URL: The url to redirect to in case of an action involving a redirect (null means 404-error).
   *
   * @return void/void->EXIT
   */
  public static function err($e_id, $e_msg, $e_vars, $file, $line, $action, $abuse_possible, $def_vars=null, $r_url=null)
  {
    parent::err($e_id, $e_msg, $e_vars, $file, null, null, $line, $action, $abuse_possible, $def_vars, $r_url);
  }
  
  /**
   * @desc Setups dev environment ONLY IF user is admin and has appropriate permissions.
   * @return void
   */
  public static function dev_setup()
  {
    if(self::$session_user->is_admin())
    {
      define('CYCOM_INDEVMODE', true);
      require('system/lib.dev.php');
    }
    else
    {
      define('CYCOM_INDEVMODE', false);
    }
  }
}
?>