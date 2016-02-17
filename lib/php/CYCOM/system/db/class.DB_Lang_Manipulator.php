<?php
require_once('system/db/class.DB_Lang.php');

class DB_Lang_Manipulator extends DB_Lang
{
  /**
   * @desc Updates lang_$target where $target = (context || topcat || subcat || entry).
   * @param int $id
   * @param string $name
   * @param string $comments
   * @return bool
   */
  public function update_ctse($target, $id, $name=null, $comments)
  {
    if($target != 'context' && $target != 'topcat' && $target != 'subcat' && $target != 'entry')
      parent::trigger_error('lang_manipulator__invalid_target', null, __LINE__, __FILE__);
    
    if(!is_null($name) && !preg_match(constant('CONSTR__REGEX_'.strtoupper($target)), $name))
      parent::trigger_error('lang_manipulator__constr_failed_'.$target.'_name', null, __LINE__, __FILE__);
    
    parent::register_query('lang_update_'.$target, Cylab_DB_Query::TYPE_UPDATE,
                           'UPDATE lang_'.$target.' SET '.(!is_null($name) ? 'name=$0,' : '').' comments=$1 WHERE id=$2');
    return (parent::execute_query('lang_update_'.$target, array($name, $comments, (int)$id)) > 0);
  }
  
  /**
   * @param string $context_name
   * @param string $context_comments
   * @return bool
   */
  public function add_context($context_name, $context_comments)
  {
    if(!preg_match(CONSTR__REGEX_CONTEXT, $context_name))
      parent::trigger_error('lang_manipulator__constr_failed_context_name', null, __LINE__, __FILE__);
    
    if(parent::context_exists($context_name))
      return false;
    
    parent::register_query('lang_add_context', Cylab_DB_Query::TYPE_INSERT,
                           'INSERT INTO lang_context (name,comments) VALUES ($0,$1)');
    return (parent::execute_query('lang_add_context', array($context_name, $context_comments)) > 0);
  }
  
  /**
   * @param int $context_id
   * @param string $topcat_name
   * @param string $topcat_comments
   * @return bool
   */
  public function add_topcat($context_id, $topcat_name, $topcat_comments)
  {
    if(!preg_match(CONSTR__REGEX_TOPCAT, $topcat_name))
      parent::trigger_error('lang_manipulator__constr_failed_topcat_name', 'topcat='.$topcat_name, __LINE__, __FILE__);
    
    if(parent::topcat_exists($context_id, $topcat_name))
      return false;
    
    parent::register_query('lang_add_topcat', Cylab_DB_Query::TYPE_INSERT,
                           'INSERT INTO lang_topcat (context_id,name,comments) VALUES ($0,$1,$2)');
    return (parent::execute_query('lang_add_topcat', array((int)$context_id, $topcat_name, $topcat_comments)) > 0);
  }
  
  /**
   * @param int $context_id
   * @param int $topcat_id
   * @param string $subcat_name
   * @param string $subcat_comments
   * @return bool
   */
  public function add_subcat($context_id, $topcat_id, $subcat_name, $subcat_comments)
  {
    if(!preg_match(CONSTR__REGEX_SUBCAT, $subcat_name))
      parent::trigger_error('lang_manipulator__constr_failed_subcat_name', null, __LINE__, __FILE__);
    
    if(parent::subcat_exists($context_id, $topcat_id, $subcat_name))
      return false;
    
    parent::register_query('lang_add_subcat', Cylab_DB_Query::TYPE_INSERT,
                           'INSERT INTO lang_subcat (context_id,topcat_id,name,comments) VALUES ($0,$1,$2,$3)');
    return (parent::execute_query('lang_add_subcat', array((int)$context_id, $topcat_id, $subcat_name, $subcat_comments)) > 0);
  }
  
  /**
   * @param int $context_id
   * @param int $topcat_id
   * @param int $subcat_id
   * @param string $entry_name
   * @param string $entry_comments
   * @return bool
   */
  public function add_entry($context_id, $topcat_id=null, $subcat_id=null, $entry_name, $entry_comments)
  {
    if(!preg_match(CONSTR__REGEX_ENTRY, $entry_name))
      parent::trigger_error('lang_manipulator__constr_failed_entry_name', null, __LINE__, __FILE__);
    if($topcat_id == null && $subcat_id != null) // Check hierarchy.
      parent::trigger_error('lang_manipulator__entry_hierarchy_failed', 'topcat_id was null, subcat_id was not.', __LINE__, __FILE__);
    
    if(parent::entry_exists($context_id, $topcat_id, $subcat_id, $entry_name))
      return false;
    
    parent::register_query('lang_add_entry', Cylab_DB_Query::TYPE_INSERT,
                           'INSERT INTO lang_entry (context_id,topcat_id,subcat_id,name,comments) VALUES ($0,$1,$2,$3,$4)');
    return (parent::execute_query('lang_add_entry', array((int)$context_id, $topcat_id, $subcat_id, $entry_name, $entry_comments)) > 0);
  }
  
  /**
   * @desc Updates entry value or inserts a new one if not found.
   *       Note: encodes numeric entities in $entry_value.
   * @param int $entry_id
   * @param int $language_id
   * @param string $entry_value
   * @return bool
   */
  public function set_entry_value($entry_id, $language_id, $entry_value)
  {
    require_once('modifier.encode_numeric_entity.php');
    $entry_value = Cylib__encode_numeric_entity($entry_value);
    
    if(parent::entry_value_exists($entry_id, $language_id))
    {
      parent::register_query(__FUNCTION__, Cylab_DB_Query::TYPE_UPDATE,
                             'UPDATE lang_entry_value SET value=$2 WHERE entry_id=$0 AND language_id=$1');
    }
    else
    {
      parent::register_query(__FUNCTION__, Cylab_DB_Query::TYPE_INSERT,
                             'INSERT INTO lang_entry_value (entry_id,language_id,value) VALUES ($0,$1,$2)');
    }
    return (parent::execute_query(__FUNCTION__, array((int)$entry_id, $language_id, $entry_value)) > 0);
  }
}