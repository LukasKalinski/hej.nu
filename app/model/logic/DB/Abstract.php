<?php
/**
 * CYCOM DB Abstract class
 *
 * @package CYCOM.DB
 * @since 2006-06-15
 * @version 2006-06-19
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

require_once('Cylab/DB/Adapter/Pgsql.php');
require_once('Exception.php');
require_once('Statement.php');

abstract class CYCOM_DB_Abstract extends Cylab_DB_Adapter_Pgsql
{
  /**
   * Loads a Cylab_DB_Adapter_Abstract object.
   */
  public function __construct()
  {
    parent::__construct(array('user' => 'cycom', 'password' => 'VoatyalAng5'));
  }
  
  /**
   *
   * @param string $table
   * @param aArray $data
   * @param string|array $required_fields
   * @param Cylab_DB_Validator_Abstract $validator
   * @throws CYCOM_DB_Exception
   * @throws Cylab_DB_Exception
   * @return CYCOM_DB_Statement
   */
  public function query_insert($table, $data, $required_fields=null, $validator=null)
  {
    if(!is_null($required_fields)) // -> check fields presence
    {
      require_once('Cylib/functions/array_keys_exist.php');
      
      // Check that all keys are present:
      if(!Cylib__array_keys_exist($required_fields, $data))
      {
        throw new CYCOM_DB_Exception('required keys not found in $data');
      }
    }
    
    if(!is_null($validator)) // -> validate fields
    {
      foreach($data as $field => $value)
      {
        if(!$validator->validate_field($field, $value))
        {
          throw new CYCOM_DB_Validator_Exception("failed to validate value for field: $field");
        }
      }
    }
    
    return $this->query(parent::generate_insert($table, $data));
  }
  
  /**
   * 
   * @param string $table
   * @param aArray $data
   * @param string $where     - Condition without 'WHERE'.
   * @param string|array $allowed_fields
   * @param Cylab_DB_Validator_Abstract $validator
   * @throws CYCOM_DB_Exception
   * @throws Cylab_DB_Exception
   * @return CYCOM_DB_Statement
   */
  public function query_update($table, $data, $where, $allowed_fields = null, $validator = null)
  {
    if(!is_null($allowed_fields))
    {
      require_once('Cylib/modifiers/array_preserve_keys.php');
      
      // Remove unwanted fields (keys):
      Cylib__array_preserve_keys($data, $allowed_fields);
    }
    
    if(count($data) == 0)
    {
      throw new CYCOM_DB_Exception('datasource $data is empty');
    }
    
    if(!is_null($validator))
    {
      foreach($data as $field => $value)
      {
        if(!$validator->validate_field($field, $value))
        {
          throw new CYCOM_DB_Validator_Exception("failed to validate value for field: $field");
        }
      }
    }
    
    return $this->query(parent::generate_update($table, $data, $where));
  }
  
  /**
   * Overload of parent method.
   * 
   * @param string $query
   * @param aArray $values
   * @return CYCOM_DB_Statement
   */
  public function query($query, $values = null)
  {
    $statement = new CYCOM_DB_Statement($this, $query);
    $statement->execute($query, $values);
    return $statement;
  }
}
?>