<?php
/********************************************************************************
 * File:          system/lib.error.php
 * Description:   Error handling.
 * Begin:         2005-01-12
 * Author:        Lukas Kalinski
 * Copyright:     2004 CyLab Sweden
 ********************************************************************************/

define('ERR_NONE',           0);
define('ERR_USER_NOTICE',    1);
define('ERR_USER_WARNING',   2);
define('ERR_USER_ERROR',     4);
define('ERR_SYSTEM_NOTICE',  8);
define('ERR_SYSTEM_WARNING', 16);
define('ERR_SYSTEM_ERROR',   32);
define('ERR_ALL',            ERR_USER_NOTICE   | ERR_USER_WARNING   | ERR_USER_ERROR |
                             ERR_SYSTEM_NOTICE | ERR_SYSTEM_WARNING | ERR_SYSTEM_ERROR);

define('ERR_REPORTING',      ERR_ALL);


/**
 * @desc The base and therefore the most primitive function for error handling in the CYCOM environment.
 * @return void/void->EXIT
 */
function CYCOM__trigger_error($message, $file=NULL, $line=NULL, $error_type, $log=true, $override_err_reporting=-1)
{
  $err_reporting = ($override_err_reporting != -1 ? $override_err_reporting : ERR_REPORTING);
  
  switch($error_type)
  {
    case ERR_USER_NOTICE:
        if(($err_reporting & ERR_USER_NOTICE) > 0)
          echo '<br /><b>*** USER NOTICE ***</b><br />'.$message;
      break;
    case ERR_USER_WARNING:
        if(($err_reporting & ERR_USER_WARNING) > 0)
          echo '<br /><b>*** USER WARNING ***</b><br />'.$message;
      break;
    case ERR_USER_ERROR:
        if(($err_reporting & ERR_USER_ERROR) > 0)
          exit('<br /><b>*** USER ERROR ***</b><br />'.$message);
      break;
    case ERR_SYSTEM_NOTICE:
        if(($err_reporting & ERR_SYSTEM_NOTICE) > 0)
          echo '<br /><b>*** SYSTEM NOTICE ***</b><br />'.$message;
      break;
    case ERR_SYSTEM_WARNING:
        if(($err_reporting & ERR_SYSTEM_WARNING) > 0)
          echo '<br /><b>*** SYSTEM WARNING ***</b><br />'.$message;
      break;
    case ERR_SYSTEM_ERROR:
        if(($err_reporting & ERR_SYSTEM_ERROR) > 0)
          exit('<br /><b>*** SYSTEM ERROR ***</b><br />'.$message);
      break;
    
    default: exit('Unknown error type specified for __trigger_error()...');
  }
}
?>