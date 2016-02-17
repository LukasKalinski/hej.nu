<?php
require_once('system/lib.db.php');
require_once('system/db/constraints.lang.php');

abstract class DB_Lang extends Cylab_DB
{
  /**
   * @param string $context_name
   * @return bool
   */
  protected function context_exists($context_name)
  {
    parent::register_query(__FUNCTION__, Cylab_DB_Query::TYPE_SELECT_COUNT,
                           'SELECT COUNT(*) FROM lang_context WHERE name=$0 LIMIT 1');
    return (parent::execute_query(__FUNCTION__, array($context_name)) > 0);
  }
  
  /**
   * @param string $topcat_name
   * @return bool
   */
  protected function topcat_exists($context_id, $topcat_name)
  {
    parent::register_query(__FUNCTION__, Cylab_DB_Query::TYPE_SELECT_COUNT,
                           'SELECT COUNT(*) FROM lang_topcat WHERE context_id=$0 AND name=$1 LIMIT 1');
    return (parent::execute_query(__FUNCTION__, array((int)$context_id,$topcat_name)) > 0);
  }
  
  /**
   * @param int $topcat_id
   * @param string $subcat_name
   * @return bool
   */
  protected function subcat_exists($context_id, $topcat_id, $subcat_name)
  {
    parent::register_query(__FUNCTION__, Cylab_DB_Query::TYPE_SELECT_COUNT,
                           'SELECT COUNT(*) FROM lang_subcat WHERE context_id=$0 AND topcat_id=$1 AND name=$2 LIMIT 1');
    return (parent::execute_query(__FUNCTION__, array((int)$context_id,(int)$topcat_id,$subcat_name)) > 0);
  }
  
  /**
   * @param int $context_id
   * @param int $topcat_id
   * @param int $subcat_id
   * @param string $entry_name
   * @return bool
   */
  protected function entry_exists($context_id, $topcat_id, $subcat_id, $entry_name)
  {
    parent::register_query(__FUNCTION__, Cylab_DB_Query::TYPE_SELECT_COUNT,
                           'SELECT COUNT(*) FROM lang_entry WHERE context_id=$0 AND topcat_id=$1 AND subcat_id=$2 AND name=$3 LIMIT 1');
    return (parent::execute_query(__FUNCTION__, array((int)$context_id,(int)$topcat_id,(int)$subcat_id,$entry_name)) > 0);
  }
  
  /**
   * @param int $entry_id
   * @param string(2) $language_id
   */
  protected function entry_value_exists($entry_id, $language_id)
  {
    parent::register_query(__FUNCTION__, Cylab_DB_Query::TYPE_SELECT_COUNT,
                           'SELECT COUNT(*) FROM lang_entry_value WHERE entry_id=$0 AND language_id=$1 LIMIT 1');
    return (parent::execute_query(__FUNCTION__, array((int)$entry_id, $language_id)) > 0);
  }
}