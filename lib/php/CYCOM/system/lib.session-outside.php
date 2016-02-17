<?php
/********************************************************************************\
 * File:          system/lib.session-outside.php
 * Description:   Session outside (=user is NOT logged in) library.
 * Begin:         2006-01-15
 * Edit:          
 * Author:        Lukas Kalinski
 * Copyright:     2006 CyLab Sweden
\********************************************************************************/

require_once('lib.session.php'); // Must be "once" (see sys.login.php).

class Session_Outside extends Session
{
  private static $s;
  
  /**
   * @desc Destroys possible preceding session level and initiates a SESSLEV_OUTSIDE session.
   */
  public static function init()
  {
    parent::initlev(SESSLEV_OUTSIDE);
    self::$s = &$_SESSION[SESSLEV_OUTSIDE];
  }
  
  /**
   * @return bool
   */
  public static function exists()
  {
    return parent::haslev(SESSLEV_OUTSIDE);
  }
  
  /**
   * @desc Checks that we have SESSLEV_OUTSIDE initiated and calls Session::uselev(SESSLEV_OUTSIDE). Do NOT call this if init is called in the same context.
   * @param bool $require_check
   * @return void
   */
  public static function checkuselev($require_check=true)
  {
    if(!parent::haslev(SESSLEV_OUTSIDE)) // Level SESSLEV_OUTSIDE was not found.
    {
      self::init();
    }
    else // Level SESSLEV_OUTSIDE was found; use it.
    {
      parent::uselev(SESSLEV_OUTSIDE);
      self::$s = &$_SESSION[SESSLEV_OUTSIDE];
    }
  }
}
?>