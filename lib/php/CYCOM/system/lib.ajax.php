<?php
/**
 * Remote Data Management Library (with AJAX).
 *
 * @package system.lib.ajax
 * @since 2006-02-18
 * @version 2006-06-03
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

require_once('system/lib.common.php');
require_once('function.array_is_assoc.php');

class AJAX
{
  /**
   * Stores error message in xml-format and calls CYCOM::err() with ERR_ACTION_CONTINUE.
   * 
   * @todo make sure no errors are viewed when show_errors is Off.
   */
  public static function handle_error($err_id, $err_msg, $file, $line)
  {
    self::result(array(array('errid' => $err_id, 'file' => $file, 'line' => $line, 'msg' => $err_msg,
                             'params' => preg_replace('/\s+/', ' ', print_r(array('_GET' => $_GET, '_POST' => $_POST), true)))), true);
    
    /**
     * @todo Fix error logging; enable "log only"-mode in error handler...
     */
  }
  
  /**
   * Returns a php-array stored in a json-string.
   *
   * @param array $array
   * @return string
   */
  private static function array2json($array)
  {
    $json = '{';
    foreach($array as $k => $v)
      $json .= '\'' . $k . '\':' . Cylib__js_typecast($v) . ',';
    $json = substr($json, 0, -1);
    $json .= '}';
    return $json;
  }
  
  /**
   * Generates content for the eval-function in javascript.
   *
   * @param mixed $result
   * @return EXIT
   */
  public static function result($result, $is_error=false)
  {
    require_once('modifier.js_typecast.php');
    
    if(is_array($result))
    {
      if(Cylib__array_is_assoc($result))
      {
        echo self::array2json($result);
      }
      else
      {
        $output = ($is_error ? 'err@' : '') . 'new Array(';
        for($i=0, $ii=count($result); $i<$ii; $i++)
        {
          if(is_array($result[$i]))
            $output .= self::array2json($result[$i]);
          else
            $output .= Cylib__js_typecast($result[$i]);
          
          $output .= ($i+1 < $ii ? ',' : '');
        }
        echo $output.')';
      }
    }
    else
    {
      echo Cylib__js_typecast($result);
    }
    exit;
  }
  
  /**
   * Generates content with a CTE template for the eval-function in javascript.
   *
   * @param string $file
   * @param string $lang
   * @param mixed[] $vars
   * @return #exit
   */
  public static function cteresult($file, $lang, $vars)
  {
    require_once('cte/engine/class.CTE.php');
    CTE::set_language($lang);
    foreach($vars as $key => $value)
      CTE::create_var($key, $value);
    CTE::display('_lib/rdm/'.$file);
    exit;
  }
}

/**
 * Redirect PHP errors to AJAX/Javascript output.
 */
function AJAX_ERR_HANDLER($errno, $err_text, $file, $line)
{
  AJAX::handle_error('phperrno_'.$errno, $err_text, $file, $line);
}

set_error_handler('AJAX_ERR_HANDLER');
header('Content-type:text/plain');
?>