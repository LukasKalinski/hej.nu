<?php
// ## Database file for: admin.lang-data

require_once('system/lib.common-inside.php');
require_once('system/lib.session-inside.php');

Session_Inside::checkuselev();
$__sessuser = &Session_Inside::shortcut(SESSINS_KEY_USER);
CYCOM_Inside::register_user($__sessuser);

CYCOM_Inside::require_permissions('LANG_DATA,LANG_STRUCT', false);

require_once('function.postvars_set.php');
require_once('function.getvars_set.php');

if(!Cylib__getvars_set('a'))
  CYCOM_Inside::err('missing_get_keys', 'keys: a', null, __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());

switch($_GET['a'])
{
  case 'getlanguage':
    require_once('system/lib.ajax.php');
    require_once('system/db/class.DB_Lang_Retriever.php');
    $lang = new DB_Lang_Retriever();
    $language_list = $lang->get_language_list('id,name');
    $lang->destroy();
    AJAX::result($language_list);
    break;
  
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
    if(!Cylib__getvars_set('context_id'))
      CYCOM_Inside::err('missing_get_keys', 'keys: context_id', null, __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
    
    require_once('system/db/class.DB_Lang_Retriever.php');
    $lang = new DB_Lang_Retriever();
    $topcat_list = $lang->get_topcat_list($_GET['context_id'], 'id,name');
    $lang->destroy();
    AJAX::result($topcat_list);
    break;
  
  case 'getsubcat':
    require_once('system/lib.ajax.php');
    if(!Cylib__getvars_set('context_id,topcat_id'))
      CYCOM_Inside::err('missing_get_keys', 'keys: context_id', null, __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
    
    require_once('system/db/class.DB_Lang_Retriever.php');
    $lang = new DB_Lang_Retriever();
    $subcat_list = $lang->get_subcat_list($_GET['context_id'], $_GET['topcat_id'], 'id,name');
    $lang->destroy();
    AJAX::result($subcat_list);
    break;
  
  case 'getentry':
    require_once('system/lib.ajax.php');
    if(!Cylib__getvars_set('context_id'))
      CYCOM_Inside::err('missing_get_keys', 'keys: context_id,topcat_id,subcat_id', null,
                        __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
    
    if(!key_exists('topcat_id', $_GET))
      $_GET['topcat_id'] = null;
    if(!key_exists('subcat_id', $_GET))
      $_GET['subcat_id'] = null;
    
    require_once('system/db/class.DB_Lang_Retriever.php');
    $lang = new DB_Lang_Retriever();
    $entry_list = $lang->get_entry_list((int)$_GET['context_id'], $_GET['topcat_id'], $_GET['subcat_id'], 'id,name,comments');
    $lang->destroy();
    AJAX::result($entry_list);
    break;
  
  case 'getentryvalue':
    require_once('system/lib.ajax.php');
    if(!Cylib__getvars_set('language_id,entry_id'))
      CYCOM_Inside::err('missing_get_keys', 'keys: language_id,entry_id', null,
                        __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
    
    require_once('system/db/class.DB_Lang_Retriever.php');
    $lang = new DB_Lang_Retriever();
    $entry_value = $lang->get_entry_value_by_id((int)$_GET['entry_id'], $_GET['language_id'], true, 'value');
    $entry_value_default = $lang->get_entry_value_by_id((int)$_GET['entry_id'], LANG_DEFAULT, true, 'value');
    $entry_desc = $lang->get_entry_by_id((int)$_GET['entry_id'], 'comments');
    $lang->destroy();
    
    require_once('modifier.encode_numeric_entity.php');
    require_once('modifier.decode_numeric_entity.php');
    $entry_value          = $entry_value['value'];
    $entry_value          = ($entry_value != null ? Cylib__decode_numeric_entity($entry_value) : $entry_value);
    $entry_desc           = Cylib__encode_numeric_entity($entry_desc);
    $entry_value_default  = Cylib__encode_numeric_entity($entry_value_default['value']);
    $entry_desc           = $entry_desc['comments'];
    
    AJAX::result(array('value' => $entry_value,
                       'default_value' => $entry_value_default,
                       'description' => $entry_desc,
                       'language_id' => $_GET['language_id'],
                       'entry_id' => $_GET['entry_id']));
    break;
  
  case 'save':
    require_once('system/lib.ajax.php');
    if(!Cylib__postvars_set('language_id,entry_id,entry_value'))
    {
      CYCOM_Inside::err('missing_get_keys', 'keys: language_id, entry_id, entry_value', null,
                        __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
    }
    
    require_once('system/db/class.DB_Lang_Manipulator.php');
    $lang = new DB_Lang_Manipulator();
    $result = $lang->set_entry_value($_POST['entry_id'], $_POST['language_id'], $_POST['entry_value']);
    $lang->destroy();
    
    AJAX::result($result);
    break;
  
  default:
    CYCOM_Inside::err('unknown_action_key', 'key: a=$0', array($_GET['a']),
                      __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
}
?>