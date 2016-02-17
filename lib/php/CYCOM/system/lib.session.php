<?php
/********************************************************************************
 * File:          system/lib.session.php
 * Description:   Global session handling
 * Begin:         2005-04-20
 * Edit:          2006-01-27
 * Author:        Lukas Kalinski
 * Copyright:     2005-2006 CyLab Sweden
 ********************************************************************************/

define('LIB_SESSION', 1);

require_once('env.globals.php');

if(strlen(session_id()) == 0)
{
  session_name('CYCOM'); // STATIC; DO NOT CHANGE THIS!
  session_start();
}

// Keys for $_SESSION[]
define('SESS_GLOBAL',     'sess_global');
define('SESSLEV_OUTSIDE', 'sesslev_out');
define('SESSLEV_INSIDE',  'sesslev_ins');

// Keys for $_SESSION[...][]
define('SESS_KEY_TEMP', 0);
define('SESS_KEY_FLAGS', 1);
define('SESSINS_KEY_USER', 2);

define('NOSESSLEV__REDIRECT_TO_ROOT', 1);
define('NOSESSLEV__SYSMSG',           2);


/**
 * @desc Core session behaviour class.
 */
abstract class Session
{
  private static $s;
  private static $current_sesslev = null;
  
  /**
   * @desc Destroys everything except SESS_GLOBAL and initiates a new session (SESSLEV_INSIDE or SESSLEV_OUTSIDE).
   * @param int $sesslev  SESSLEV_OUTSIDE or SESSLEV_INSIDE
   * @return void
   */
  protected static function initlev($sesslev)
  {
    // Clean environment:
    foreach($_SESSION as $key => $value)
      if($key != SESS_GLOBAL)
        unset($_SESSION[$key]);
    
    self::$current_sesslev = $sesslev;
    $_SESSION[self::$current_sesslev] = array();
    
    self::$s = &$_SESSION[self::$current_sesslev];
    
    self::$s = array();
    self::$s[SESS_KEY_TEMP] = array();
    self::$s[SESS_KEY_FLAGS] = array();
  }
  
  /**
   * @desc Returns true if arg:$sesslev is set, true if any level is set and arg:$sesslev==null, or false otherwise.
   * @param string $sesslev
   * @return bool
   */
  protected static function haslev($sesslev=null)
  {
    return (!is_null($sesslev) ? key_exists($sesslev, $_SESSION) : key_exists(self::$current_sesslev, $_SESSION));
  }
  
  /**
   * @desc Requires a certain session level to be available.
   * @param int $sesslev
   * @param int $on_fail (NOSESSLEV__REDIRECT_TO_ROOT)
   * @return void
   */
  protected static function uselev($sesslev, $on_fail=NOSESSLEV__REDIRECT_TO_ROOT)
  {
    if(!self::haslev($sesslev))
    {
      switch($on_fail)
      {
        case NOSESSLEV__REDIRECT_TO_ROOT:
          header('Location: /');
          exit;
      }
    }
    
    self::$current_sesslev = $sesslev;
    self::$s = &$_SESSION[self::$current_sesslev];
  }
  
  /**
   * @param string $flag
   * @return void
   */
  public static function set_flag($flag)
  {
    self::$s[SESS_KEY_FLAGS][$flag] = 1;
  }
  
  /**
   * @param string $flag
   * @return bool
   */
  public static function flag_isset($flag)
  {
    return (key_exists($flag, self::$s[SESS_KEY_FLAGS]));
  }
  
  /**
   * @return void
   */
  public static function clear_flags()
  {
    unset(self::$s[SESS_KEY_FLAGS]);
    self::$s[SESS_KEY_FLAGS] = array();
  }
  
  /**
   * @param string $key
   * @param mixed $value
   * @return void
   */
  public static function temp_set($key, $value)
  {
    self::$s[SESS_KEY_TEMP][$key] = $value;
  }
  
  /**
   * @param string $keys
   * @return bool
   */
  public static function temp_isset($keys)
  {
    $keys = explode(',', $keys);
    for($i=0, $ii=count($keys); $i<$ii; $i++)
      if(!key_exists($keys[$i], self::$s[SESS_KEY_TEMP]))
        return false;
    
    return true;
  }
  
  /**
   * @return mixed
   */
  public static function temp_get($key)
  {
    if(!is_null(self::$current_sesslev) && key_exists($key, self::$s[SESS_KEY_TEMP]))
      return self::$s[SESS_KEY_TEMP][$key];
    else
      return null;
  }
  
  /**
   * @desc Imports requested (or all if $keys==null) keys from $assoc into temp.
   *       Always returns true except for the case when $keys!=null and one of the
   *       requested keys is missing, then it'll return false.
   * @param array[string=>mixed] $assoc
   * @param string $keys
   * @return bool
   */
  public static function temp_importassoc($assoc, $keys=null)
  {
    if(is_null($keys))
    {
      foreach($assoc as $key => $value)
        self::$s[SESS_KEY_TEMP][$key] = $value;
    }
    else
    {
      $keys = explode(',', $keys);
      for($i=0, $ii=count($keys); $i<$ii; $i++)
        self::$s[SESS_KEY_TEMP][$keys[$i]] = $assoc[$keys[$i]];
    }
  }
  
  /**
   * @desc Exports requested (or all if $keys==null) temp keys to &$assoc.
   *       Always returns true except for the case when $keys!=null and one
   *       of the requested keys is missing, then it'll return false.
   * @param &string[] $assoc
   * @param string $keys
   * @return bool
   */
  public static function temp_exportassoc(&$assoc, $keys=null)
  {
    if(!is_array($assoc)) $assoc = array();
    
    $return = true;
    if(is_null($keys))
    {
      foreach(self::$s[SESS_KEY_TEMP] as $key => $value)
        $assoc[$key] = $value;
    }
    else
    {
      $keys = explode(',', $keys);
      for($i=0, $ii=count($keys); $i<$ii; $i++)
      {
        if(!key_exists($keys[$i], self::$s[SESS_KEY_TEMP]))
          $return = false;
        $assoc[$keys[$i]] = self::$s[SESS_KEY_TEMP][$keys[$i]];
      }
    }
    
    return $return;
  }
  
  /**
   * @return int
   */
  public static function temp_exporttocte($keys)
  {
    if(!class_exists('CTE'))
      return -1;
    
    $keys = explode(',', $keys);
    for($i=0, $ii=count($keys); $i<$ii; $i++)
      if(key_exists($keys[$i], self::$s[SESS_KEY_TEMP]))
        CTE::register_var($keys[$i], self::$s[SESS_KEY_TEMP][$keys[$i]]);
    return 1;
  }
  
  /**
   * @return void
   */
  public static function clear_temp($target_id=null)
  {
    if(!is_null($target_id) && key_exists($target_id, self::$s[SESS_KEY_TEMP]))
    {
      unset(self::$s[SESS_KEY_TEMP][$target_id]);
    }
    else
    {
      unset(self::$s[SESS_KEY_TEMP]);
      self::$s[SESS_KEY_TEMP] = array();
    }
  }
}

class Global_Session
{
  private static $s;
  
  /**
   * @desc Initiates $_SESSION[SESS_GLOBAL] if not initiated.
   * @param string[][] $defaults
   */
  public static function setup($defaults=null)
  {
    if(!key_exists(SESS_GLOBAL, $_SESSION))
    {
      $_SESSION[SESS_GLOBAL] = array();
      self::$s = &$_SESSION[SESS_GLOBAL];
      
      // Set lang:
      self::set_lang();
      
      // Set defaults:
      if(!is_null($defaults))
      {
        foreach($defaults as $key => $value)
        {
          switch($key)
          {
            case 'theme_id':
              self::set_theme($value);
              break;
            default:
              self::set($key, $value);
          }
        }
      }
      
      // Set browser:
      if(strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko'))
        self::set('browser', BROWSER__CASE_GECKO);
      elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') && !strpos($_SERVER['HTTP_USER_AGENT'], 'Opera'))
        self::set('browser', BROWSER__CASE_MSIE);
      else
        self::set('browser', BROWSER__CASE_OTHER);
    }
    else
    {
      self::$s = &$_SESSION[SESS_GLOBAL];
    }
  }
  
  /**
   * @desc Sets element with key=$key to $value.
   * @param string $key
   * @param mixed $value
   * @return void
   */
  public static function set($key, $value)
  {
    self::$s[$key] = $value;
  }
  
  /**
   * @desc Sets language to $lang if not null and to default otherwise.
   * @param string $lang
   * @return void
   */
  public static function set_lang($lang=null)
  {
    self::$s['lang'] = (is_null($lang) ? LANG_DEFAULT : $lang);
  }
  
  /**
   * @desc Fetches relevant theme data from theme ini and stores it in $__session_globals.
   * @param string $theme_id
   * @return void
   */
  public static function set_theme($theme_id)
  {
    require_once('function.parse_ini.php');
    self::$s['theme_id'] = $theme_id;
    self::$s['theme_hash'] = Cylib__parse_ini(PATH_SYS__THEME_LIB_ROOT.'theme.'.$theme_id.'.ini', false, 'hash');
  }
  
  /**
   * @desc Returns value of specified key if exists and null otherwise.
   * @param string $key
   * @return string
   */
  public static function get($key)
  {
    if(key_exists($key, self::$s))
      return self::$s[$key];
    else
      return null;
  }
}
Global_Session::setup(array('theme_id' => '001'));

// ## Shortcuts - Meant _ONLY_ for main php files and templates, not includes!!
$theme_rpath = Global_Session::get('theme_id').'/'.Global_Session::get('theme_hash').'/';
define('DOC_ROOT',        PATH_WWW__DOCUMENT_ROOT);
define('GFX_ROOT',        PATH_WWW__GFX_ROOT.$theme_rpath);
define('CSS_COMMON_ROOT', PATH_WWW__CSS_OUTPUT_ROOT);
define('CSS_ROOT',        PATH_WWW__CSS_OUTPUT_ROOT.$theme_rpath.Global_Session::get('browser').'/');
define('THEME',           Global_Session::get('theme_id'));
define('BROWSER',         Global_Session::get('browser'));
define('LANG',            Global_Session::get('lang'));
define('BTN_ROOT',        PATH_WWW__GFX_ROOT.$theme_rpath.'~btn/');
define('UP_ILL_ROOT',     PATH_WWW__GFX_ROOT.'_ill/up/'); // User photo illustrations root
define('GFX_COMMON_ROOT', PATH_WWW__GFX_ROOT.'_cmn/');
define('USER_FILES_ROOT', PATH_WWW__DOCUMENT_ROOT.'_files/');
?>
