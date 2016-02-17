<?php
require_once('system/db/class.DB_Lang.php');

class DB_Lang_Retriever extends DB_Lang
{
  /**
   * @desc 
   * @param string $language_id
   * @param string $cols
   * @return array
   */
  public function get_language_by_id($id, $cols='name')
  {
    parent::register_query('lang_get_language', Cylab_DB_Query::TYPE_SELECT_SINGLE,
                           'SELECT '.$cols.' FROM lang_language WHERE id=$0');
    return parent::execute_query('lang_get_language', array($id));
  }
  
  /**
   * @desc Returns a single 'context' row.
   * @param string $id
   * @param string $cols
   * @return array
   */
  public function get_context_by_id($id, $cols='name,comments')
  {
    parent::register_query('lang_get_context', Cylab_DB_Query::TYPE_SELECT_SINGLE,
                           'SELECT '.$cols.' FROM lang_context WHERE id=$0');
    return parent::execute_query('lang_get_context', array((int)$id));
  }
  
  /**
   * @desc Returns a single 'topcat' row.
   * @param string $id
   * @param string $cols
   * @return array
   */
  public function get_topcat_by_id($id, $cols='name,comments')
  {
    parent::register_query('lang_get_topcat', Cylab_DB_Query::TYPE_SELECT_SINGLE,
                           'SELECT '.$cols.' FROM lang_topcat WHERE id=$0');
    return parent::execute_query('lang_get_topcat', array((int)$id));
  }
  
  /**
   * @desc Returns a single 'subcat' row.
   * @param string $id
   * @param string $cols
   * @return array
   */
  public function get_subcat_by_id($id, $cols='name,comments')
  {
    parent::register_query('lang_get_subcat', Cylab_DB_Query::TYPE_SELECT_SINGLE,
                           'SELECT '.$cols.' FROM lang_subcat WHERE id=$0');
    return parent::execute_query('lang_get_subcat', array((int)$id));
  }
  
  /**
   * @desc Returns a single 'entry' row.
   * @param string $id
   * @param string $cols
   * @return array
   */
  public function get_entry_by_id($id, $cols='name,comments')
  {
    parent::register_query('lang_get_entry', Cylab_DB_Query::TYPE_SELECT_SINGLE,
                           'SELECT '.$cols.' FROM lang_entry WHERE id=$0');
    return parent::execute_query('lang_get_entry', array((int)$id));
  }
  
  /**
   * @desc Returns value for selected language, if not found null will be returned.
   * @param int $entry_id
   * @param string $language_id
   * @param bool $force_requested_lang
   * @param string $cols
   * @return string
   */
  public function get_entry_value_by_id($entry_id, $language_id, $force_requested_lang=false, $cols='value')
  {
    parent::register_query(__FUNCTION__, Cylab_DB_Query::TYPE_SELECT_SINGLE,
                           'SELECT '.$cols.' FROM lang_entry_value WHERE entry_id=$0 AND language_id=$1');
    $result = parent::execute_query(__FUNCTION__, array((int)$entry_id, $language_id));
    
    if(!$force_requested_lang && $result == null && $language_id != LANG_DEFAULT)
      $result = parent::execute_query(__FUNCTION__, array((int)$entry_id, LANG_DEFAULT));
    
    return $result;
  }
  
  /**
   * Enter description here...
   *
   * @param unknown_type $korv
   */
  public function foo($korv)
  {
    
  }
  
  /**
   * @param string $cols
   * @return array
   */
  public function get_unichar_list($cols)
  {
    parent::register_query(__FUNCTION__, Cylab_DB_Query::TYPE_SELECT_MULTIPLE, 'SELECT '.$cols.' FROM lang_unichar');
    return parent::execute_query(__FUNCTION__, null);
  }
  
  /**
   * @desc 
   * @param string $cols
   * @return array
   */
  public function get_language_list($cols='id,name')
  {
    parent::register_query('lang_get_languages', Cylab_DB_Query::TYPE_SELECT_MULTIPLE,
                           'SELECT '.$cols.' FROM lang_language ORDER BY name');
    return parent::execute_query('lang_get_languages');
  }
  
  /**
   * @desc Returns a full list of available language contexts.
   * @param string $cols
   * @return array[]
   */
  public function get_context_list($cols='id,name,comments')
  {
    parent::register_query('lang_get_context_list', Cylab_DB_Query::TYPE_SELECT_MULTIPLE,
                           'SELECT '.$cols.' FROM lang_context ORDER BY name ASC');
    return parent::execute_query('lang_get_context_list', null);
  }
  
  /**
   * @desc Returns a context dependent list of top categories.
   * @param int $context_id
   * @param string $cols
   * @return array[]
   */
  public function get_topcat_list($context_id, $cols='id,name,comments')
  {
    parent::register_query('lang_get_topcat_list', Cylab_DB_Query::TYPE_SELECT_MULTIPLE,
                           'SELECT '.$cols.' FROM lang_topcat WHERE context_id=$0 ORDER BY name ASC');
    return parent::execute_query('lang_get_topcat_list', array((int)$context_id));
  }
  
  /**
   * @desc Returns a context+topcat dependent list of sub categories.
   * @param int $context_id
   * @param int $topcat_id
   * @param string $cols
   * @return array[]
   */
  public function get_subcat_list($context_id, $topcat_id, $cols='id,name,comments')
  {
    parent::register_query('lang_get_subcat_list', Cylab_DB_Query::TYPE_SELECT_MULTIPLE,
                           'SELECT '.$cols.' FROM lang_subcat WHERE context_id=$0 AND topcat_id=$1 ORDER BY name ASC');
    return parent::execute_query('lang_get_subcat_list', array((int)$context_id, $topcat_id));
  }
  
  /**
   * @desc Returns a context+topcat+subcat dependent list of sub categories.
   * @param int $context_id
   * @param int $topcat_id
   * @param int $subcat_id
   * @param string $cols
   * @return array[]
   */
  public function get_entry_list($context_id, $topcat_id=null, $subcat_id=null, $cols='id,name,comments')
  {
    parent::register_query('lang_get_entry_list', Cylab_DB_Query::TYPE_SELECT_MULTIPLE,
                           'SELECT '.$cols.' FROM lang_entry WHERE context_id=$0 AND topcat_id=$1 AND subcat_id=$2 ORDER BY name ASC');
    return parent::execute_query('lang_get_entry_list', array((int)$context_id, $topcat_id, $subcat_id));
  }
}
?>