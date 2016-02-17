<?php
/********************************************************************************\
 * File:          system/lib.common-outside.php
 * Description:   Common outside (=user is NOT logged in) library.
 * Begin:         2006-01-15
 * Edit:          
 * Author:        Lukas Kalinski
 * Copyright:     2006 CyLab Sweden
\********************************************************************************/

require_once('lib.common.php');

class CYCOM_Outside extends CYCOM
{
  /**
   * @desc Handles "inside" cycom errors.
   *       Setting string arguments to null will disable them.
   *       A pipe (|) in err_msg is interpreted as a line break.
   * 
   * @param string $e_id                # Error id: To distinguish between errors.
   * @param string $e_msg               # Error message
   * @param array $e_vars               # Error vars: Variables for merging with err_msg.
   *
   * @param int $line                   # Line
   *
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
}
?>