<?php
/**
 * USR Island Accessor
 *
 * @package CYCOM.DB
 * @since 2006-06-18
 * @version 2006-06-19
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

require_once('Abstract.php');

class CYCOM_DB_Island_Usr_Accessor extends CYCOM_DB_Island_Usr_Abstract
{
  /**
   * Returns array with $cols on success and null on failure or num_rows = 0.
   * 
   * @param string $username
   * @param string $cols
   * @throws Cylab_DB_Exception
   * @return aArray
   */
  public function get_user_by_name($username, $cols='id')
  {
    return $this->query("SELECT $cols FROM usr_user WHERE username=$1", array($username))->fetch_assoc();
  }
  
  /**
   * Returns array with $cols on success and false on failure.
   * 
   * NOTE:
   * A table name DOES NOT include the island prefix in this context, i.e.
   * user = correct, usr_user = incorrect.
   * 
   * @param string $user_id
   * @param string $cols      - Columns to fetch.
   * @param string $table     - Table name.
   * @throws Cylab_DB_Exception
   * @return aArray
   */
  public function get_by_id($user_id, $cols, $table='user')
  {
    $id_field = ($table != 'user' ? 'user_id' : 'id');
    return $this->query("SELECT $cols FROM usr_$table WHERE $id_field=$1", array($user_id))->fetch_assoc();
  }
  
  /**
   * Reads event monitor and returns the result.
   * 
   * @param string $user_id
   * @throws Cylab_DB_Exception
   * @return mixed
   */
  public function read_event_monitor($user_id)
  {
    return $this->query("SELECT new_gst_num,new_mil_num,new_rel_num,new_frm_num ".
                        "FROM usr_event_monitor WHERE user_id=$1",
                        array($user_id))->fetch_assoc();
  }
}
?>
