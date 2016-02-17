<?php
/**
 * GST Island Validator
 *
 * @package CYCOM.DB
 * @since 2006-06-20
 * @version 2006-06-20
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

require_once('Config/IO.php');
require_once('model/logic/DB/Validator/Abstract.php');

class CYCOM_DB_Island_Gst_Validator extends CYCOM_DB_Validator_Abstract
{
  public function __construct()
  {
    parent::add(parent::CHECK_MINLEN, 'message', 3);
    parent::add(parent::CHECK_MAXLEN, 'message', 1000);
    parent::add(parent::CHECK_MAXLEN_HTMLE, 'message', 
                parent::get_field_maxlen('message') * CYCOM_Config_IO::HTML_ENTITY_FIXED_LENGTH);
  }
}
?>