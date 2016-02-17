<?php
require_once('system/lib.db.php');

class DB_Sys_Manipulator extends Cylab_DB
{
  /**
   * @todo Make this connect to a separate SQL server before release.
   */
  public function __construct()
  {
    parent::__construct(false);
  }
  
  /**
   * Stores CYCOM application errors (CGI).
   *
   * <p>
   * Returns false if database connection failed for some reason.
   * </p>
   * 
   * @param string $err_id
   * @param string $message
   * @param string $var_dump
   * @return bool
   */
  public function store_cgi_error($err_id, $message=null, $var_dump=null)
  {
    self::set_handle_errors(false);
    parent::execute_function('sys_log_cgi_error+', $err_id, $message, $var_dump);
    return !parent::had_error();
  }
}
?>