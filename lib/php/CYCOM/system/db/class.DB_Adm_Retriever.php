<?php
require_once('system/lib.db.php');

class DB_Adm_Retriever extends Cylab_DB
{
  /**
   * @desc Returns admin id if found and null otherwise.
   * @param string $user_id
   * @return string
   */
  public function get_admin_id($user_id)
  {
    parent::register_query(__FUNCTION__, Cylab_DB_Query::TYPE_SELECT_SINGLE, 'SELECT id FROM adm_admin WHERE user_id=$0');
    $result = parent::execute_query(__FUNCTION__, array($user_id));
    return (!is_null($result) && key_exists('id', $result) ? $result['id'] : null);
  }
  
  /**
   * @param string $admin_id
   * @return aarray
   */
  public function get_permissions($admin_id)
  {
    parent::register_query('get_adm_permissions', Cylab_DB_Query::TYPE_SELECT_MULTIPLE,
                           'SELECT adm_permission.name FROM adm_permission,adm_permissions WHERE adm_permissions.admin_id=$0 AND '.
                           'adm_permissions.permission_id=adm_permission.id');
    $result = parent::execute_query('get_adm_permissions', array($admin_id));
    
    if(is_null($result))
      return null;
    
    $return = array();
    for($i=0,$ii=count($result); $i<$ii; $i++) // Simplify result (from result[x]['name']=><name> to result[x]=><name>).
      array_push($return, $result[$i]['name']);
    return $return;
  }
}
?>