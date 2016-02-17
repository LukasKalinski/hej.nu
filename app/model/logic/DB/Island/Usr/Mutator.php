<?php
/**
 * USR Island Mutator
 *
 * @package CYCOM.DB
 * @since 2006-06-19
 * @version 2006-06-19
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

require_once('Abstract.php');
require_once('Validator.php');

class CYCOM_DB_Island_Usr_Mutator extends CYCOM_DB_Island_Usr_Abstract
{
  /**
   * @var CYCOM_DB_Island_Usr_Validator
   */
  private static $validator;
  
  /**
   * Initiates validator instance.
   */
  public function __construct()
  {
    parent::__construct();
    self::$validator = new CYCOM_DB_Island_Usr_Validator();
  }
  
  /**
   * Checks constraints and inserts a new row in the user table.
   * 
   * REQUIREMENTS:
   * - $data should contain following keys:
   *   username, password, gender, dob, country_id, region_id, city_id, language_id
   *   district, reg_ip, first_name, last_name, ssn, email, citizenship_id, address,
   *   post_code, post_city, phone_number
   * 
   * @param aArray $data
   * @throws CYCOM_DB_Validator_Exception
   * @throws CYCOM_DB_Exception
   * @throws Cylab_DB_Exception
   * @return string Process ID
   * 
   * @todo verify functionality
   */
  public function insert_user($data)
  {
    if(!array_key_exists('password', $data))
    {
      throw new CYCOM_DB_Exception('required key "password" not found in $data',
                                   CYCOM_Exception::CODE_ERROR);
    }
    
    require_once('Cylib/modifiers/array_preserve_keys.php');
    
    $data['password_hash'] = md5($data['password']);
    $data['ip'] = $_SERVER['REMOTE_ADDR'];
    
    $required_keys = 'username,password_hash,gender,dob,country_id,region_id,city_id,language_id'.
                     'district,reg_ip,first_name,last_name,ssn,email,citizenship_id,address,'.
                     'post_code,post_city,phone_number';
    
    // Remove all unwanted keys from $data:
    Cylib__array_preserve_keys($data, $required_fields);
    
    parent::query_insert('usr_user', $data, $required_keys, self::$validator);
    
    $uid = parent::query('SELECT id FROM usr_user WHERE username=$1', array($data['username']))->fetch_assoc();
    $pid = parent::query('SELECT id FROM usr_process WHERE user_id=$1 AND type=$2', array($uid['id'], 'ACCACT'))->fetch_assoc();
    
    return $pid['id'];
  }
  
  /**
   * Updates user row.
   * 
   * REQUIREMENTS:
   * - $data should not contain other than the following keys:
   *   username, password, gender, dob, country_id, region_id, city_id, language_id
   *   district, first_name, last_name, ssn, email, citizenship_id, address,
   *   post_code, post_city, phone_number, login_ip_log, login_tstamp_log, 
   *   photo_mode, account_status, sessid, online_tstamp, theme_id
   * 
   * @param string $user_id
   * @param aArray $data
   * @throws CYCOM_DB_Validator_Exception
   * @throws Cylab_DB_Exception
   * @return bool True if any rows were updated, false otherwise.
   * 
   * @todo verify functionality
   */
  public function update_user($user_id, $data)
  {
    // Keys to allow (update function phpdoc if changing):
    $allowed_keys = 'username,password,gender,dob,country_id,region_id,city_id,language_id'.
                    'district,first_name,last_name,ssn,email,citizenship_id,address,'.
                    'post_code,post_city,phone_number,login_ip_log,login_tstamp_log,'.
                    'photo_mode,account_status,sessid,online_tstamp,theme_id';
    
    $where = $this->quote_into('id=?', $user_id);
    $affected = $this->query_update('usr_user', $data, $where, $allowed_keys, self::$validator)->get_affected_rows();
    
    if($affected > 1)
    {
      throw new CYCOM_DB_Exception('usr_user update caused more than one affected row',
                                   CYCOM_Exception::CODE_CRITICAL_ERROR);
    }
    
    return ($affected == 1);
  }
  
  /**
   * @param string $process_id
   * @throws Cylab_DB_Exception
   * @return bool True if any rows were updated, false otherwise.
   * 
   * @todo verify functionality
   */
  public function delete_process($process_id)
  {
    $where = $this->quote_into('id=? AND type=\'ACCACT\'', $process_id);
    $affected = $this->query_delete('usr_process', $where)->get_affected_rows();
    
    return ($affected == 1);
  }
  
  /**
   * 
   * 
   * @param string $user_id
   * @param string $pres_raw
   * @param string $css_raw
   * @param string $pres_compiled
   * @param array $css_compiled
   * @throws Cylab_DB_Exception
   * @throws CYCOM_DB_Exception
   * @return bool True if any rows were updated, false otherwise.
   * 
   * @todo verify functionality
   */
  public function update_pres($user_id, $data)
  {
    require_once('Cylib/modifiers/array_preserve_keys.php');
    
    $allowed_keys = 'pres_raw,css_raw,pres_compiled,css_compiled';
    $where = $this->quote_into('user_id=?', $user_id);
    $affected = $this->query_update('usr_pres', $data, $where, $allowed_keys, self::$validator)->get_affected_rows();
    
    if($affected > 1)
    {
      throw new CYCOM_DB_Exception('usr_pres update caused more than one affected row',
                                   CYCOM_Exception::CODE_CRITICAL_ERROR);
    }
    
    return ($affected == 1);
  }
}
