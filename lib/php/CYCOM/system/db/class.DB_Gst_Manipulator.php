<?php
require_once('system/lib.db.php');
require_once('modifier.encode_numeric_entity.php');
require_once('modifier.rmnl.php');

class DB_Gst_Manipulator extends Cylab_DB
{
  /**
   * bool store_message(string, string, string)
   *
   * @param string $user_id
   * @param string $writer_id
   * @param string $message
   * @return bool
   */
  public function store_message($user_id, $writer_id, $message, $log_as_event, $thread_id=null)
  {
    $message = Cylib__rmnl($message);
    $message = Cylib__encode_numeric_entity($message);
    return parent::execute_function('gst_message_insert', $user_id, $writer_id, $message, $thread_id, $log_as_event);
  }
  
  /**
   * @desc ...
   * @param string $message_id
   * @return bool
   */
  public function delete_message($message_id, $user_id=null, $log_as_event=false)
  {
    return parent::execute_function('gst_message_delete', $message_id, $user_id, $log_as_event);
  }
}
?>