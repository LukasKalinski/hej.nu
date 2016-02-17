<?php
require_once('system/lib.db.php');

class DB_Gst_Retriever extends CYCOM_DB
{
  /**
   * @desc 
   * @param string $user_id
   * @param int $offset       (page number - 1)
   * @param int $limit
   * @return mixed
   */
  public function get_messages($user_id, $offset=0, $limit=10)
  {
    parent::register_query('gst_get_messages', Cylab_DB_Query::TYPE_SELECT_MULTIPLE, 
                           'SELECT '.
                                     'gst_message.id, gst_message.writer_id, gst_message.thread_id,'.
                                     'gst_message.condition, gst_message.message, gst_message.tstamp,'.
                                     'usr_user.username, usr_user.gender, extract(year from age(usr_user.dob)) as age,'.
                                     'usr_user.photo_mode,'.
                                     'usr_user.country, usr_user.country_id, '.
                                     'usr_user.region, usr_user.region_id, '.
                                     'usr_user.city, usr_user.city_id'.
                            ' FROM gst_message INNER JOIN usr_user ON '.
                            'gst_message.writer_id=usr_user.id WHERE user_id=$0 ORDER BY gst_message.tstamp DESC '.
                            'LIMIT '.$limit.' OFFSET '.($offset*$limit));
    return parent::execute_query('gst_get_messages', array($user_id));
  }
}
?>