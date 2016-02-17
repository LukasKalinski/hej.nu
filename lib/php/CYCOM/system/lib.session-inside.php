<?php
/********************************************************************************\
 * File:          system/lib.session-inside.php
 * Description:   Session inside (=user is logged in) library.
 * Notes:         
 * Begin:         2005-04-22
 * Edit:          2006-01-15
 * Author:        Lukas Kalinski
 * Copyright:     2005-2006 CyLab Sweden
\********************************************************************************/

require('class.User_Session.php');
require_once('lib.session.php'); // Calls session_start() - Must be included after class.Session_User.php.
                                 // Must be once (see sys.login.php).

class Session_Inside extends Session
{
  private static $s;
  
  /**
   * @desc Destroys possible preceding session level and initiates a SESSLEV_INSIDE session.
   */
  public static function init()
  {
    parent::initlev(SESSLEV_INSIDE);
    self::$s = &$_SESSION[SESSLEV_INSIDE];
  }
  
  /**
   * @return bool
   */
  public static function exists()
  {
    return parent::haslev(SESSLEV_INSIDE);
  }
  
  /**
   * @desc Checks that we have SESSLEV_INSIDE initiated and calls Session::uselev(SESSLEV_INSIDE). Do NOT call this if init is called in the same context.
   * @return void
   */
  public static function checkuselev()
  {
    self::check_isset();
    parent::uselev(SESSLEV_INSIDE);
    self::$s = &$_SESSION[SESSLEV_INSIDE];
  }
  
  /**
   * @desc Returns a shortcut (reference) to a SESSLEV_INSIDE element, if key doesn't exist it will be created.
   * @param string $key
   * @return &ref
   */
  public static function &shortcut($key)
  {
    if(!key_exists($key, self::$s))
      self::$s[$key] = null;
    return self::$s[$key];
  }
  
  /**
   * @return void
   */
  private static function check_isset()
  {
    if(!parent::haslev(SESSLEV_INSIDE))
    {
      CYCOM_Inside::msg('session_lost', PATH_WWW__DOCUMENT_ROOT, MSG_APPEAR_JS);
      exit;
    }
  }
}
?>
