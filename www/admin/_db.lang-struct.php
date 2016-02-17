<?php
// ## Database file for: admin.lang-struct

require_once('system/lib.common-inside.php');
require_once('system/lib.session-inside.php');

Session_Inside::checkuselev();
$__sessuser = &Session_Inside::shortcut(SESSINS_KEY_USER);
CYCOM_Inside::register_user($__sessuser);

CYCOM_Inside::require_permissions('LANG_STRUCT', true);

require_once('function.postvars_set.php');
require_once('function.getvars_set.php');

if(!Cylib__getvars_set('a'))
  CYCOM_Inside::err('_db_admin_lang__missing_get_keys', 'keys: a', null, __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());

switch($_GET['a'])
{
  case 'getcontext':
    require_once('system/lib.ajax.php');
    require_once('system/db/class.DB_Lang_Retriever.php');
    $lang = new DB_Lang_Retriever();
    $context = $lang->get_context_list();
    $lang->destroy();
    AJAX::result($context);
    break;
  
  case 'gettopcat':
    require_once('system/lib.ajax.php');
    if(!Cylib__getvars_set('context'))
      CYCOM_Inside::err('missing_get_keys', 'keys: context', null, __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
    require_once('system/db/class.DB_Lang_Retriever.php');
    $lang = new DB_Lang_Retriever();
    $topcat = $lang->get_topcat_list($_GET['context'], 'id,name,comments');
    $lang->destroy();
    AJAX::result($topcat);
    break;
  
  case 'getsubcat':
    require_once('system/lib.ajax.php');
    if(!Cylib__getvars_set('context,topcat'))
      CYCOM_Inside::err('_db_admin_lang__missing_get_keys', 'keys: context, topcat', null,
                        __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
    
    require_once('system/db/class.DB_Lang_Retriever.php');
    $lang = new DB_Lang_Retriever();
    $subcat = $lang->get_subcat_list($_GET['context'], $_GET['topcat'], 'id,name,comments');
    $lang->destroy();
    AJAX::result($subcat);
    break;
  
  case 'getentry':
    require_once('system/lib.ajax.php');
    if(!Cylib__getvars_set('context'))
      CYCOM_Inside::err('_db_admin_lang__missing_get_keys', 'keys: context', null,
                        __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
    
    if(!key_exists('topcat', $_GET) || $_GET['topcat'] == 0)
      $_GET['topcat'] = null;
    if(!key_exists('subcat', $_GET) || $_GET['subcat'] == 0)
      $_GET['subcat'] = null;
    
    require_once('system/db/class.DB_Lang_Retriever.php');
    $lang = new DB_Lang_Retriever();
    $entry = $lang->get_entry_list($_GET['context'], $_GET['topcat'], $_GET['subcat'], 'id,name,comments');
    $lang->destroy();
    AJAX::result($entry);
    break;
  
  case 'add':
    require_once('system/lib.ajax.php');
    if(!Cylib__postvars_set('context,comments'))
      CYCOM_Inside::err('_db_admin_lang__missing_post_vars', 'keys: context, comments', null,
                        __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
    
    require_once('system/db/class.DB_Lang_Manipulator.php');
    $lang = new DB_Lang_Manipulator();
    
    $success = false;
    
    if(key_exists('entry', $_POST))
      foreach($_POST as $key => $value)
        if($key == 'topcat' || $key == 'subcat')
          if($value == 0)
            $_POST[$key] = null;
    
    // ## Case; entry:
    if(Cylib__postvars_set('context,topcat,subcat,entry'))
    {
      if(($success = $lang->add_entry($_POST['context'], $_POST['topcat'], $_POST['subcat'], $_POST['entry'], $_POST['comments'])) !== true)
        CYCOM_Inside::err('_db_admin_lang__entry_storage_failed', null, null, __FILE__, __LINE__, ERR_ACTION_CONTINUE, true);
    }
    // ## Case; sub category:
    elseif(Cylib__postvars_set('topcat,subcat'))
    {
      if(($success = $lang->add_subcat($_POST['context'], $_POST['topcat'], $_POST['subcat'], $_POST['comments'])) !== true)
        CYCOM_Inside::err('_db_admin_lang__subcat_storage_failed', null, null, __FILE__, __LINE__, ERR_ACTION_CONTINUE, true);
    }
    // ## Case; top category:
    elseif(Cylib__postvars_set('topcat'))
    {
      if(($success = $lang->add_topcat($_POST['context'], $_POST['topcat'], $_POST['comments'])) !== true)
        CYCOM_Inside::err('_db_admin_lang__topcat_storage_failed', null, null, __FILE__, __LINE__, ERR_ACTION_CONTINUE, true);
    }
    // ## Case; context:
    else
    {
      if(($success = $lang->add_context($_POST['context'], $_POST['comments'])) !== true)
        CYCOM_Inside::err('_db_admin_lang__context_storage_failed', null, null, __FILE__, __LINE__, ERR_ACTION_CONTINUE, true);
    }
    
    AJAX::result($success);
    break;
  
  case 'edit':
    require_once('system/lib.ajax.php');
    if(!Cylib__postvars_set('context,comments'))
      CYCOM_Inside::err('_db_admin_lang__missing_post_vars', 'keys: context, comments', null,
                        __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
    
    if(key_exists('entry', $_POST))
      foreach($_POST as $key => $value)
        if($key == 'topcat' || $key == 'subcat')
          if($value == 0)
            $_POST[$key] = null;
    
    require_once('system/db/class.DB_Lang_Manipulator.php');
    $lang = new DB_Lang_Manipulator();
    
    // ## Case; entry:
    if(Cylib__postvars_set('context,topcat,subcat,entry') && $_POST['entry'] > 0)
    {
      if(($success = $lang->update_ctse('entry', $_POST['entry'], null, $_POST['comments'])) !== true)
        CYCOM_Inside::err('_db_admin_lang__entry_update_failed', null, null, __FILE__, __LINE__, ERR_ACTION_CONTINUE, true);
    }
    // ## Case; sub category:
    elseif(Cylib__postvars_set('topcat,subcat') && $_POST['topcat'] > 0 && $_POST['subcat'] > 0)
    {
      if(($success = $lang->update_ctse('subcat', $_POST['subcat'], null, $_POST['comments'])) !== true)
        CYCOM_Inside::err('_db_admin_lang__subcat_update_failed', null, null, __FILE__, __LINE__, ERR_ACTION_CONTINUE, true);
    }
    // ## Case; top category:
    elseif(Cylib__postvars_set('topcat') && $_POST['topcat'] > 0)
    {
      if(($success = $lang->update_ctse('topcat', $_POST['topcat'], null, $_POST['comments'])) !== true)
        CYCOM_Inside::err('_db_admin_lang__topcat_update_failed', null, null, __FILE__, __LINE__, ERR_ACTION_CONTINUE, true);
    }
    // ## Case; context:
    else
    {
      if(($success = $lang->update_ctse('context', $_POST['context'], null, $_POST['comments'])) !== true)
        CYCOM_Inside::err('_db_admin_lang__context_update_failed', null, null, __FILE__, __LINE__, ERR_ACTION_CONTINUE, true);
    }
    
    AJAX::result($success);
    break;
  
  default:
    CYCOM_Inside::err('_db_admin_lang__unknown_action_key', 'key: a=$0', array($_GET['a']),
                      __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
}
?>