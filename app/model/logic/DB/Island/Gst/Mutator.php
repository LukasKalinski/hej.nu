<?php
/**
 * GST Island Mutator
 *
 * @package CYCOM.DB
 * @since 2006-06-20
 * @version 2006-06-20
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

require_once('Abstract.php');

class CYCOM_DB_Island_Gst_Mutator extends CYCOM_DB_Island_Gst_Abstract
{
  /**
   * Inserts a guestbook message.
   * 
   * @param string $user_id
   * @param string $writer_id
   * @param string $message
   * @param bool $log_as_event
   * @param string $thread_id
   * @throws CYCOM_DB_Island_Gst_Validator
   * @throws Cylab_DB_Exception
   * @return bool
   * 
   * @todo Verify functionality.
   * @todo Make sure $message is properly formatted before coming here.
   */
  public function insert_message($user_id, $writer_id, $message, $log_as_event, $thread_id=null)
  {
    require_once('Validator.php');
    return $this->call('gst_message_insert', $user_id, $writer_id, $message, $thread_id, $log_as_event);
  }
  
  /**
   * Deletes a guestbook message.
   * 
   * @param string $message_id
   * @throws Cylab_DB_Exception
   * @return bool
   * 
   * @todo Verify functionality.
   */
  public function delete_message($message_id, $user_id=null, $log_as_event=false)
  {
    return $this->call('gst_message_delete', $message_id, $user_id, $log_as_event);
  }
}
?>