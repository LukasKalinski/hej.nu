<?php
/**
 * Database management library
 *
 * @package system.lib.db
 * @since 2005-03-22
 * @version 2006-06-11
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

require_once('modifier.squote.php');

/**
 * @desc Static utility class for SQL queries.
 */
class SQL_Util
{
  private function __construct() {} // Ensure static.
  
  /**
   * @desc Typecasts php variable into valid SQL value.
   * @param mixed $value
   * @return mixed
   */
  public static function typecast($value)
  {
    switch(gettype($value))
    {
      case 'integer':
      case 'double':
      case 'double':
        return $value;
      case 'string':
        return Cylib__squote($value); // 2006-06-03: Changed behaviour of Cylab__squote... might affect this function...
      case 'boolean':
        return ($value ? 'TRUE' : 'FALSE');
      case 'NULL':
        return 'NULL';
      default:
        return null;
    } 
  }
}

/**
 * SQL Query class.
 */
class SQL_Query
{
  /**
  * @var int
  */
  private $query_type = null;
  
  /**
  * @var string
  */
  private $query_raw = null;  // @var string
  
  /**
  * @var string
  */
  private $query_fed = null;
  
  /**
   * @param string $query
   * @param string $type
   */
  public function __construct($query, $type)
  {
    $this->query_raw = $query;
    $this->query_buffer_type = $type;
  }
  
  /**
   * @desc Replaces $1, $2, $3 .. with corresponding values in the argument array.
   * @param array $data
   * @return void
   */
  public function feed($data)
  {
    $this->query_fed = $this->query_raw;
    if(count($data) > 0)
    {
      $data = array_reverse($data, true);
      foreach($data as $key => $value)
      {
        $value = Cylab_DB__convert_type_php2psql($value);
        
        if(is_null($value)) // @todo Take care of chaotic error handling below...
        {
          /*CYCOM::err('db_query_invalid_food_datatype', 'datatype: $0', array(gettype($value)),
                     __FILE__, __FUNCTION__, __CLASS__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());*/
          exit(__FILE__ . ' @ line' . __LINE__);
        }
        
        $this->query_fed = preg_replace('/\$('.$key.')/', $value, $this->query_fed, 1);
      }
      // @todo Wouldn't this replace UPDATE ... SET col=NULL with UPDATE ... SET col IS NULL ?
      $this->query_fed = preg_replace('/([a-z0-9_]+)\s*=\s*NULL/i', '\1 IS NULL', $this->query_fed); // ADDED @ 2006-03-06
    }
  }
  
  /**
   * @desc Get raw query string.
   * @return string
   */
  public function get_raw()
  {
    return $this->query_raw;
  }
  
  /**
   * @desc Get fed query string.
   * @return string
   */
  public function get_fed()
  {
    return $this->query_fed;
  }
  
  /**
   * @return int
   */
  public function get_type()
  {
    return $this->query_buffer_type;
  }
}

define('SQL_Query::TYPE_INSERT',          1);
define('SQL_Query::TYPE_DELETE',          2);
define('SQL_Query::TYPE_SELECT_MULTIPLE', 3); // One or many rows.
define('SQL_Query::TYPE_SELECT_SINGLE',   4); // Always one row.
define('SQL_Query::TYPE_SELECT_COUNT',    5); // Always one row.
define('SQL_Query::TYPE_UPDATE',          6);

/**
 * @desc Parent for all DB operational classes.
 */
abstract class __Cylab_DB
{
  private static $server = array('host'     => '',
                                 'user'     => 'cycom',
                                 'password' => 'VoatyalAng5',
                                 'db_name'  => 'cycom');
  private static $conn = null;
  private static $conn_open = false;
  
  /**
   * @var SQL_Query[]
   */
  private $query_buffer = array();
  
  /**
   * Current fed query.
   * @var string
   */
  private $current_query = null;
  
  /**
   * @var int[]
   */
  private $num_rows = array();
  
  /**
   * @var mixed
   */
  private $last_query_id = null;
  
  /**
   * @var bool
   */
  private $handle_errors = true;
  
  /**
   * @var bool
   */
  private $error_occured = false;
  
  /**
   * Default constructor.
   *
   * @param bool $do_connect
   */
  public function __construct($do_connect=false)
  {
    if($do_connect)
      $this->connect();
  }
  
  /**
   * Closes database connection and unsets all buffers.
   *
   * @return void
   */
  public function destroy()
  {
    @pg_close(self::$conn);
    self::$conn_open = false;
    unset($this->query_buffer);
  }
  
  /**
   * Set $handle_errors on/off. Use this carefully and only when absolutely necessary.
   *
   * @param bool $value
   * @return void
   */
  public function set_handle_errors($value)
  {
    $this->handle_errors = $value;
  }
  
  /**
   * @return bool
   */
  public function had_error()
  {
    return $this->error_occured;
  }
  
  /**
   * Internal error handling function.
   *
   * @param string $desc
   * @param int $line
   * @param string $file
   * @return #EXIT
   */
  protected function handle_error($desc, $line, $function, $file=__FILE__, $class=__CLASS__)
  {
    $this->error_occured = true;
    
    if(!$this->handle_errors)
      return;
    
    @include_once('modifier.encode_numeric_entity.php');
    
    $desc = $desc . "\nDBMS said: " . (is_resource(self::$conn) ? @pg_last_error(self::$conn) : 'N/A');
    
    // Build message:
    $message = '<?xml version="1.0" encoding="' . ini_get('default_charset') . '"?>';
    $message .= '<message>';
    {
      $message .= '<script_file>' . $_SERVER['SCRIPT_FILENAME'] . '</script_file>';
      $message .= '<trigger_file>' . $file . '</trigger_file>';
      $message .= '<class>' . $class . '</class>';
      $message .= '<function>' . $function . '</function>';
      $message .= '<line>' . $line . '</line>';
      $message .= '<abuse_possible>yes</abuse_possible>';
      $message .= '<description>' . @Cylib__encode_numeric_entity($desc) . '</description>';
    }
    $message .= '</message>';
    
    $this->execute_function('sys_log_dbms_error', md5($file . '>' . $line), $message, $this->current_query);
    exit('DBMS error logged');
  }
  
  /**
   * @return void
   * @todo Make it distinguish between different connections.
   */
  private function connect()
  {
    if(!self::$conn_open)
    {
      $conn_str = 'user='.self::$server['user'].' password='.self::$server['password'];
      self::$conn = @pg_connect($conn_str) or die('DBMS: connection lost'); // Connection failed for some reason.
      self::$conn_open = true;
    }
  }
  
  /**
   * @param string $id
   * @param string $type
   * @param string $query
   * @return void
   */
  protected function register_query($id, $type, $query)
  {
    $this->query_buffer[$id] = new SQL_Query($query, $type);
  }
  
  /**
   * Returns fed query if found and raw if not.
   *
   * @param string $id
   * @return string
   */
  protected function get_query($id)
  {
    if($this->query_buffer[$id]->get_fed() != null)
      return $this->query_buffer[$id]->get_fed();
    else
      return $this->query_buffer[$id]->get_raw();
  }
  
  /**
   * @return void
   */
  protected function transaction_begin()
  {
    if(@pg_exec(self::$conn, 'BEGIN;') === false)
      $this->handle_error('Transaction begin failed.', __LINE__, __FUNCTION__);
  }
  
  /**
   * Executes a PLPGSQL-function and returns whatever the PGSQL-function returns, translated to a (if exists) corresponding php-datatype.
   *
   * In case of error this function either exits (the PHP exit();) or returns nothing.
   *
   * @param string $func_name
   * @param mixed $func_arg_1 First argument to the SQL function.
   * @param mixed $func_arg_2 and so on...
   * @return mixed
   */
  protected function execute_function($func_name)
  {
    $func_args = func_get_args();
    array_shift($func_args); // Remove $func_name from argument list.
    
    // Typecast arguments and build a call-string:
    for($i=0, $ii=count($func_args); $i<$ii; $i++)
      $func_args[$i] = Cylab_DB__convert_type_php2psql($func_args[$i]);
    $func_args = implode(',',$func_args);
    
    $this->connect();
    $this->current_query = 'SELECT '.$func_name.'('.$func_args.')';
    $result = @pg_query(self::$conn, $this->current_query);
    
    if(strlen(@pg_last_error(self::$conn)) > 0)
    {
      $this->handle_error('Failed to execute function: '.$func_name.' with argument list: '.$func_args, __LINE__, __FUNCTION__);
      return; // We don't want any further execution in case error handling is off.
    }
    
    $result = pg_fetch_result($result, $func_name);
    
    // Translate datatype:
    switch($result)
    {
      case 'f': $result = false; break;
      case 't': $result = true; break;
    }
    
    return $result;
  }
  
  /**
   * Executes a query.
   *
   * <p>
   * What are placeholders?
   * Placeholders are aliases for PHP variables. They can be used in the query as $<array_key> and will be replaced with
   * the corresponding value found in $values array.
   *   Example:
   *     SELECT foo FROM fooTable WHERE id=$0;
   *     will become:
   *     SELECT foo FROM fooTable WHERE id='bar';
   *     if $values = array('bar');
   *
   * About placeholders:
   *   - Placeholders are typecasted from PHP-types to corresponding Postgre-types
   *   - Postgre-specific types must be typecasted manually using :: operator.
   * Return cases:
   *   - On INSERT: [int] affected rows
   *   - On UPDATE: [int] affected rows
   *   - On DELETE: [int] affected rows
   *   - On SELECT_MULTIPLE: [array] an array with rows when num_rows>1, null otherwise.
   *   - On SELECT_SINGLE: [array] the associative result array if num_rows=1, null otherwise.
   *   - On SELECT_COUNT: [int] count
   * </p>
   * @param string $id        Unique name/id of the query.
   * @param mixed[] $values   Values to feed the query with.
   * @return mixed
   */
  protected function execute_query($id, $values=null)
  {
    $this->num_rows = null;
    
    if(!key_exists($id, $this->query_buffer))
      $this->handle_error('Unknown query ID supplied for execute_query().', __LINE__, __FUNCTION__);
    
    $this->query_buffer[$id]->feed($values);
    $this->last_query_id = $id;
    
    $this->connect();
    $this->current_query = $this->query_buffer[$id]->get_fed();
    $result = @pg_query(self::$conn, $this->current_query);
    
    if(strlen(@pg_last_error(self::$conn)) > 0)
      $this->handle_error('Failed to execute query: '.$this->query_buffer[$id]->get_fed(), __LINE__, __FUNCTION__);
    
    switch($this->query_buffer[$id]->get_type())
    {
      case SQL_Query::TYPE_INSERT:
        return @pg_affected_rows($result);
      
      case SQL_Query::TYPE_DELETE:
        return @pg_affected_rows($result);
      
      case SQL_Query::TYPE_UPDATE:
        return @pg_affected_rows($result);
      
      case SQL_Query::TYPE_SELECT_MULTIPLE:
        $this->num_rows[$id] = @pg_num_rows($result);
        if($this->num_rows[$id] > 0)
        {
          $return = array();
          while($row = @pg_fetch_assoc($result))
            array_push($return, $row);
        }
        else
        {
          $return = null;
        }
        return $return;
      
      case SQL_Query::TYPE_SELECT_SINGLE:
        $this->num_rows[$id] = @pg_num_rows($result);
        
        if($this->num_rows[$id] == 1)
          return @pg_fetch_assoc($result);
        elseif($this->num_rows[$id] > 1)
          $this->handle_error('A request for SELECT_SINGLE returned more than one row.', __LINE__, __FUNCTION__);
        else
          return null;
      
      case SQL_Query::TYPE_SELECT_COUNT:
        $return = @pg_fetch_assoc($result);
        if(!key_exists('count', $return))
          $this->handle_error('Result for SQL_Query::TYPE_SELECT_COUNT was invalid.', __LINE__, __FUNCTION__);
        return $return['count'];
      
      default:
        $this->handle_error('Unknown query type supplied for DB::execute_query: '.$this->query_buffer[$id]->get_type(),
                             __LINE__, __FUNCTION__);
    }
  }
  
  /**
   * @desc Returns num_rows for requested query id; or num_rows for last query when $query_id=null; -1 if no queries found.
   * @param string $query_id
   * @return int
   */
  protected function get_num_rows($query_id=null)
  {
    if(!is_null($query_id) && key_exists($query_id, $this->num_rows))
      return $this->num_rows[$query_id];
    elseif($this->last_query_id != null)
      return $this->num_rows[$this->last_query_id];
    else
      return -1;
  }
  
  /**
   * @return void
   */
  protected function transaction_rollback()
  {
    if(@pg_exec(self::$conn, 'ROLLBACK;') === false)
      $this->handle_error('Transaction rollback failed.', __LINE__, __FUNCTION__);
  }
  
  /**
   * @return void
   */
  protected function transaction_commit()
  {
    if(@pg_exec(self::$conn, 'COMMIT;') === false)
      $this->handle_error('Transaction commit failed.', __LINE__, __FUNCTION__);
  }
}
?>