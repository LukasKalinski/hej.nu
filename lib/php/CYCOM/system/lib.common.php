<?php
/**
 * CYCOM library containing implementations used by the entire system.
 * 
 * <p>
 * CURRENT IMPLEMENTATIONS:
 *  - Error handling
 *  - System message viewer
 * </p>
 *
 * @package inc/system/lib.common
 * @since 2005-05-01
 * @version 2006-06-06
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

require_once('env.globals.php');

define('ERR_ACTION_CONTINUE', 1);
define('ERR_ACTION_REDIRECT', 2);
define('ERR_ACTION_ABORT',    3);
define('ERR_ACTION_SYSMSG',   4);

define('MSG_LBASE_OUT',   1); // @todo Could be determied using session data?
define('MSG_LBASE_INS',   2); // @todo - " -
define('MSG_APPEAR_JS',   4);
define('MSG_APPEAR_HTML', 8);

class CYCOM // @todo Add function for setting CYCOM error handler (for example in: lib.ajax.php)
{
  /**
   * Generates a GUI-adapted message and exits further code execution.
   *
   * @param string $msg_id
   * @param string $url
   * @param int $flags
   * @param string $additional_vars
   * @return #EXIT
   */
  public static function msg($msg_id, $url, $flags=MSG_APPEAR_JS, $additional_vars=null)
  {
    require_once('system/lib.session.php'); // Right now we need paths stored in Global_Session... *
    require_once('cte/engine/class.CTE.php');
    if(defined('LANG'))
      CTE::set_language(LANG);
    
    $appear_as   = (($flags & MSG_APPEAR_HTML) > 0 ? MSG_APPEAR_HTML : MSG_APPEAR_JS);
    $layout_base = (($flags & MSG_LBASE_INS) > 0 ? MSG_LBASE_INS : MSG_LBASE_OUT);
    
    CTE::register_var('CURRENT_SESSLEV', $CURRENT_SESSLEV);
    CTE::register_var('url', $url);
    CTE::register_var('msg_id', $msg_id);
    CTE::register_var('appearence', $appear_as);
    CTE::register_var('layout', $layout_base);
    CTE::create_var('css_root', CSS_ROOT); // *
    CTE::create_var('doc_root', DOC_ROOT); // *
    
    if(!is_null($additional_vars))
      foreach($additional_vars as $name => &$value)
        CTE::register_var($name, $value);
    
    CTE::display('_sys/sys.message.tpl');
    exit;
  }
  
  /**
   * Throws system abort->sysmsg error if $condition evaluates to FALSE.
   *
   * <p>
   * NOTE:
   *  - DO NOT use this function to catch possible system abusing. This is just used
   *    when functionality depends on for example a user browser, external server etc.
   * </p>
   *
   * @param bool $condition
   * @param string $message
   * @return void|#EXIT
   */
  public static function assert_true($condition, $message, $file, $line)
  {
    if(!$condition)
      self::err('assert_true_failed', $message, null, $file, __FUNCTION__, __CLASS__, $line, ERR_ACTION_SYSMSG, false);
  }
  
  /**
   * Handles any cycom errors. READ FUNCTION COMMENT FOR INSTRUCTIONS!
   *
   * <p>
   * This function is meant as a global error reporter. For local errors (like the inside or outside php pages)
   * please use the overloads in CYCOM_Inside and CYCOM_Outside respectively.
   *
   * GOOD TO KNOW:
   * - We call it "error", but it can also mean "warning" or "notice".
   * - All errors will be logged at least once.
   *
   * USAGE:
   * 1.  Param $e_desc (string)
   *     The error description should (if necessary) contain a preferably short but descriptive text about the error and its causes.
   * 2.  Param $file [REQUIRED] (string)
   *     The file in which the error was triggered (use __FILE__). 
   * 3.  Param $func (string)
   *     The function (if any) in which the error was triggered (use __FUNCTION__).
   * 4.  Param $class (string)
   *     The class (if any) in which the error was triggered (use __CLASS__).
   * 5.  Param $line [REQUIRED] (int)
   *     The line on which the error call was made (use __LINE__).
   * 6.  Param $action [REQUIRED] (int)
   *     Constants available for this parameter are listed below.
   *     - ERR_ACTION_CONTINUE   => Errors where neither exit nor error report is necessary and/or wanted.
   *     - ERR_ACTION_REDIRECT   => Errors where we need an URL/location redirect (if no $r_url is set URL root (/) will be used instead).
   *     - ERR_ACTION_ABORT      => Errors wher an abort (exit();) is required. This exit will mostly be hidden by a 404 HTTP error.
   *     - ERR_ACTION_SYSMSG     => Errors that for some reason need to be shown for everyone. These will be shown as a message 
   *                                if corresponding error id is found in the language database (if errors are stored with prefix
   *                                "syserror__" then it will automatically be appended when searching for entries,
   *                                DO NOT APPEND THEM MANUALLY!).
   *                                In the case of no match the error id will be shown instead.
   * 7.  Param $abuse_possible [REQUIRED] (bool)
   *     If there is just a liiiittle chance that the error was triggered as a cause of a system tamper, this should be set to true.
   * 8.  Param $relevant_vars (array or aarray)
   *     Array with relevant defined variables. Try to make it associative, so we know what variable contained what
   *     when debugging later on.
   * 9.  Param $r_url (string)
   *     The URL to redirect to in case of a controlled redirect. Setting this to null results in a HTTP error.
   *
   * NOTE:
   * - Disabling non-required arguments is done by setting them to null (in string case) or to (-1) in int case.
   * </p>
   * 
   * @param string $e_desc
   *
   * @param string $file
   * @param string $func
   * @param string $class
   * @param int $line
   *
   * @param int $action
   * @param bool $abuse_possible
   * @param aarr $relevant_vars
   * @param string $r_url
   *
   * @return void/#EXIT
   *
   * @todo Go through all calls to this function and remove $e_vars and $e_id from declaration.
   * @todo Implement ERR_ACTION_SYSMSG.
   */
  public static function err($e_desc, $file, $func, $class, $line, $action, $abuse_possible, $relevant_vars=null, $r_url=null)
  {
    @include_once('modifier.encode_numeric_entity.php');
    
    // Create error id:
    $e_id = md5($e_desc . '>' . $file . '>' . $line);
    
    // Filter $GLOBALS for debug dump:
    $debug_vars = array();
    foreach($GLOBALS as $key => $value)
    {
      if(preg_match('/^GLOBALS|HTTP_SESSION_VARS|debug_vars|key|value$/', $key))
        continue;
      $debug_vars[$key] = $value;
    }
    if(!is_null($relevant_vars) && is_array($relevant_vars))
    {
      foreach($relevant_vars as $key => $value)
        $debug_vars['RELEVANT_FOR_DEBUG'][$key] = $value;
    }
    $var_dump = serialize($debug_vars);
    
    // Build message:
    $message = '<?xml version="1.0" encoding="'.CHARSET_DEFAULT.'"?>';
    $message .= '<message>';
    {
      $message .= '<script_file>' . $_SERVER['SCRIPT_FILENAME'] . '</script_file>';
      $message .= '<trigger_file>' . $file . '</trigger_file>';
      $message .= '<class>' . (!is_null($class) ? $class : 'N/A') . '</class>';
      $message .= '<function>' . (!is_null($func) ? $func : 'N/A') . '</function>';
      $message .= '<line>' . $line . '</line>';
      $message .= '<taken_action>' . $action . '</taken_action>';
      $message .= '<abuse_possible>' . ($abuse_possible ? 'yes' : 'no') . '</abuse_possible>';
      $message .= '<description>' . @Cylib__encode_numeric_entity($e_desc) . '</description>';
    }
    $message .= '</message>';
    
    require_once('system/db/class.DB_Sys_Manipulator.php');
    
    // Try to store in DB:
    $sys_m = new DB_Sys_Manipulator();
    $stored = $sys_m->store_cgi_error($e_id, $message, $var_dump);
    $sys_m->destroy();
    
    // Store in file if DB failed:
    if(!$stored)
    {
      $handle = @fopen(PATH_SYS__ERROR_OUTPUT_ROOT . $e_id . '.txt', 'w') or die('Write to file failed in error handler.');
      @fwrite($handle, $message . "\n\t\n" . $var_dump);
      @fclose($handle);
    }
    
    switch($action)
    {
      case ERR_ACTION_CONTINUE:
        return;
      case ERR_ACTION_REDIRECT:
        if($url == null)
        {
          header('HTTP/1.0 404 Not Found');
        }
        else
        {
          header('Location: '.$r_url);
        }
        exit;
      case ERR_ACTION_ABORT:
        exit('error ' . $e_id);
      case ERR_ACTION_SYSMSG:
        exit('implement ERR_ACTION_SYSMSG in error handler...');
    }
  }
}
?>