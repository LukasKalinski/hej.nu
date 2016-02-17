<?php
/**
 * 
 *
 * @package CYCOM.DB
 * @since 2006-06-19
 * @version 2006-06-20
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

require_once('Exception.php');

abstract class CYCOM_DB_Validator_Abstract
{
  const CHECK_MAXLEN = 1;
  const CHECK_MAXLEN_HTMLE = 2;   // With HTML entities.
  const CHECK_MINLEN = 3;
  const CHECK_REGEXP = 4;
  
  /**
   * @var aArray
   */
  protected $constraints = array();
  
  /**
   * 
   */
  public function __construct()
  {
    array_push($this->constraints, self::CHECK_MAXLEN, self::CHECK_MINLEN, self::CHECK_REGEXP);
  }
  
  /**
   * @param int $type
   * @param string $field
   * @param mixed $constraint
   * @return void
   */
  protected final function add($type, $field, $constraint)
  {
    $this->constraints[$type][strtoupper($field)] = $constraint;
  }
  
  /**
   * @param int $type
   * @param string $field
   * @return mixed|null  - Mixed on success, null otherwise.
   */
  private final function get_constr($type, $field)
  {
    $field = strtoupper($field);
    if(array_key_exists($field, $this->constraints[$type]))
    {
      return $this->constraints[$type][$field];
    }
    else
    {
      return null;
    }
  }
  
  /**
   * Validates a field.
   * 
   * Returns TRUE if:
   * - Field value matches the REGEX constant (no constant means continue to length check).
   * - Field length fits inside the restrictions (MAXLEN and/or MINLEN constants).
   * - No constraints are found for this field.
   * Returns FALSE if:
   * - Field value failes to match any existing constraint.
   * 
   * @param string $field
   * @param mixed $value
   * @return bool
   */
  public final function validate_field($field, $value)
  {
    $regex = self::get_field_regex($field);
    if(!is_null($regex))
    {
      return preg_match($regex, $value);
    }
    else
    {
      $maxlen = self::get_field_maxlen($field);
      $minlen = self::get_field_minlen($field);
      $len = strlen($value);
      return (is_null($maxlen) || $len <= $maxlen) && (is_null($minlen) || $len >= $minlen);
    }
  }
  
  /**
   * @return int|null  - Maxlength if defined, null otherwise.
   */
  public final function get_field_maxlen($field)
  {
    return self::get_constr(self::CHECK_MAXLEN, $field);
  }
  
  /**
   * @return int|null  - Minlength if defined, null otherwise.
   */
  public final function get_field_minlen($field)
  {
    return self::get_constr(self::CHECK_MINLEN, $field);
  }
  
  /**
   * @return string|null  - Regex string if defined, null otherwise.
   */
  public final function get_field_regex($field)
  {
    return self::get_constr(self::CHECK_REGEXP, $field);
  }
}
?>