<?php
/**
 * USR Island Abstract
 *
 * @package CYCOM.DB
 * @since 2006-06-19
 * @version 2006-06-19
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

require_once('model/logic/DB/Abstract.php');

abstract class CYCOM_DB_Island_Usr_Abstract extends CYCOM_DB_Abstract
{
  // ## !! DO NOT CHANGE THESE ONCE ESTABLISHED !! ##
  const PRES_BROWSER_CASE_MSIE = 1; // Arrays in PostgreSQL use 1 as their start index.
  const PRES_BROWSER_CASE_GECKO = 2;
  const MAGIC_VALUE_PRES_NOPRES = '<empty>'; // This value means "show default presentation".
  // ## //
}
?>