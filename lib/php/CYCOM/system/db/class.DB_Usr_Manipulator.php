<?php
require_once('system/lib.db.php');
require_once('system/db/globals.usr.php');

class DB_Usr_Manipulator extends Cylab_DB
{
  /**
   * __construct()
   */
  public function __construct() { }
  
  /**
   * string create(mixed[])
   */
  public function insert($data)
  {
    extract($data, EXTR_REFS);
    
    parent::register_query('insert_usr_user', Cylab_DB_Query::TYPE_INSERT, 'INSERT INTO usr_user (username, password_hash, gender, dob,'.
                                                                  'country_id, region_id, city_id, language_id,'.
                                                                  'district, reg_ip, first_name, last_name,'.
                                                                  'ssn, email, citizenship_id, address,'.
                                                                  'post_code, post_city, phone_number'.
                                                                  ') VALUES ($1,  $2,  $3,  $4::date, $5,  $6,  $7,  $8, $9, '.
                                                                            '$10, $11, $12, $13, $14, $15, $16, $17, $18, $19)');
    parent::register_query('get_uid', Cylab_DB_Query::TYPE_SELECT_SINGLE, 'SELECT id FROM usr_user WHERE username=$1');
    parent::register_query('get_pid', Cylab_DB_Query::TYPE_SELECT_SINGLE, 'SELECT id FROM usr_process WHERE user_id=$1 AND type=\'ACCACT\'');
    
    parent::execute_query('insert_usr_user', array(1 =>  $username,         2 =>  md5($password),
                                                   3 =>  $gender,           4 =>  $dob,
                                                   5 =>  $country_id,       6 =>  $region_id,
                                                   7 =>  $city_id,          8 =>  $language_id,
                                                   9 =>  $district,         10 => $_SERVER['REMOTE_ADDR'],
                                                   11 => $first_name,       12 => $last_name,
                                                   13 => $ssn,              14 => $email,
                                                   15 => $citizenship_id,   16 => $address,
                                                   17 => $post_code,        18 => $post_city,
                                                   19 => $phone_number));
    $uid = parent::execute_query('get_uid', array(1 => $username));
    $pid = parent::execute_query('get_pid', array(1 => $uid['id']));
    
    return $pid['id'];
  }
  
  /**
   * @return bool
   */
  public function account_set_active($process_id)
  {
    parent::register_query('activate_account', Cylab_DB_Query::TYPE_DELETE, 'DELETE FROM usr_process WHERE id=$0 AND type=\'ACCACT\'');
    return (parent::execute_query('activate_account', array($process_id)) == 1 ? true : false);
  }
  
  /**
   * bool update_pres(string, string, string, string[])
   *
   * @param string $__user_id
   * @param string $pres_raw
   * @param string $css_raw
   * @param string $pres_compiled
   * @param string[] $css_compiled_list
   */
  public function update_pres($__user_id, $pres_raw, $css_raw, $pres_compiled, $css_compiled_list)
  {
    if(strlen($css_raw) == 0)
      $css_raw = USR__PRES_NO_CSS;
    
    if(count($css_compiled_list) == 2)
    {
      $css_compiled = '{"'.addslashes($css_compiled_list[USR__PRES_BROWSER_CASE_MSIE]).'",'.
                      '"'.addslashes($css_compiled_list[USR__PRES_BROWSER_CASE_GECKO]).'"}';
    }
    else
    {
      $css_compiled = '{"'.USR__PRES_NO_CSS.'","'.USR__PRES_NO_CSS.'"}';
    }
    
    parent::register_query('update_pres', Cylab_DB_Query::TYPE_UPDATE, 'UPDATE usr_pres SET '.
                                                              'pres_raw=$0, pres_css_raw=$1, pres_compiled=$2, pres_css_compiled=$3 '.
                                                              'WHERE user_id=$4');
    parent::execute_query('update_pres', array($pres_raw, $css_raw, $pres_compiled, $css_compiled, $__user_id));
  }
}