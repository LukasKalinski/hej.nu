<?php
/**
 * USR Island Validator
 *
 * @package CYCOM.DB
 * @since 2006-06-18
 * @version 2006-06-19
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

require_once('model/logic/DB/Validator/Abstract.php');

class CYCOM_DB_Island_Usr_Validator extends CYCOM_DB_Validator_Abstract
{
  public function __construct()
  {
    /**
     * Length >= 2 & <= 16
     * Don't mess with UTF-8 here.
     */
    self::add(self::CHECK_REGEXP, 'username', '/^[a-z_][a-z0-9_]{1,15}$/i');
    
    /**
     * Length >= 6 & <= 14
     * Will be md5()-hashed anyways, just a local restriction.
     * Don't mess with UTF-8 here.
     */
    self::add(self::CHECK_REGEXP, 'password', '/^[a-z0-9_-]{6,14}$/i');
    
    self::add(self::CHECK_REGEXP, 'gender',       '/^M|F$/');
    self::add(self::CHECK_REGEXP, 'email',        '/^[a-z_][a-z0-9_.-]*\@[a-z][a-z0-9_-]+(?:\.[a-z][a-z0-9_-]+)+$/i');
    self::add(self::CHECK_REGEXP, 'phone_number', '/^\+?[0-9]{3,}\-?[0-9]+$/');
    self::add(self::CHECK_REGEXP, 'post_code',    '/^[0-9]{5}$/');
    
    self::add(self::CHECK_MINLEN, 'username',     2);
    self::add(self::CHECK_MINLEN, 'password',     6);
    self::add(self::CHECK_MINLEN, 'first_name',   2);
    self::add(self::CHECK_MINLEN, 'last_name',    2);
    self::add(self::CHECK_MINLEN, 'address',      4);
    self::add(self::CHECK_MINLEN, 'phone_number', 8);
    self::add(self::CHECK_MINLEN, 'post_city',    4);
    
    self::add(self::CHECK_MAXLEN, 'district', 30);
    self::add(self::CHECK_MAXLEN, 'username', 16);
    self::add(self::CHECK_MAXLEN, 'password', 14);
    self::add(self::CHECK_MAXLEN, 'first_name', 20);
    self::add(self::CHECK_MAXLEN, 'last_name', 30);
    self::add(self::CHECK_MAXLEN, 'address', 40);
    self::add(self::CHECK_MAXLEN, 'phone_number', 15);
    self::add(self::CHECK_MAXLEN, 'post_city', 30);
  }
}
?>
