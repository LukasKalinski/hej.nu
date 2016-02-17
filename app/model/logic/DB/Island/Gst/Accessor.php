<?php
/**
 * GST Island Accessor
 *
 * @package CYCOM.DB
 * @since 2006-06-20
 * @version 2006-06-20
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

require_once('Abstract.php');

class CYCOM_DB_Island_Gst_Accessor extends CYCOM_DB_Island_Gst_Abstract
{
  /**
   * Returns all guestbook messages found at offset $offset, limited by
   * $limit and belonging to user with user_id $user_id.
   * 
   * @param string $user_id
   * @param int $offset       (Page number - 1)
   * @param int $limit
   * @throws Cylab_DB_Exception
   * @return array{aArray}
   * 
   * @todo Evaluate whether all these fields must be fetched or not.
   */
  public function get_messages($user_id, $offset=0, $limit=10)
  {
    $result = $this->query('SELECT '
                          .'gst_message.id, gst_message.writer_id, gst_message.thread_id,'
                          .'gst_message.condition, gst_message.message, gst_message.tstamp,'
                          .'usr_user.username, usr_user.gender,'
                          .'extract(year from age(usr_user.dob)) as age, usr_user.photo_mode,'
                          .'usr_user.country, usr_user.country_id, '
                          .'usr_user.region, usr_user.region_id, '
                          .'usr_user.city, usr_user.city_id '
                          .'FROM gst_message INNER JOIN usr_user ON '
                          .'gst_message.writer_id=usr_user.id WHERE user_id=$1 ORDER BY gst_message.tstamp DESC '
                          ."LIMIT $limit OFFSET " . ($offset * $limit), array($user_id));
    return $result->fetch_assoc_all();
  }
}
?>