<?php
/* Database file for group: CONFIG */

require_once('system/lib.common-inside.php');
require_once('system/lib.session-inside.php');

Session_Inside::checkuselev();
$__sessuser = &Session_Inside::shortcut(SESSINS_KEY_USER);

require('_common.php');
require('function.postvars_set.php');
require('function.getvars_set.php');

if(!Cylib__getvars_set('a'))
  CYCOM_Inside::err('_db_config__missing_get_var', 'Missing $_GET[a].', null, __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());

switch($_GET['a'])
{
  case DBACTION__STORE_PRES:
    if(!Cylib__postvars_set('r_pres,c_pres,r_css,c_css'))
      CYCOM_Inside::err('_db_config_presedit__missing_post_vars', 'Missing at least one of: POST{r_pres,c_pres,r_css,c_css}.', null,
                        __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
    
    require('classes/class.PresEdit.php');
    $PE = new PresEdit();
    $PE ->import_data($_POST['r_pres'], $_POST['r_css'], $_POST['c_pres'], $_POST['c_css']);
    
    $css_compiled = $PE->get_prepared_compiled_css();
    
    // Check that the css_compiled validation was successful: if not we'll throw an ABORT ERROR.
    if($css_compiled == PE__ERR_C_CSS_INVALID)
    {
      CYCOM_Inside::err('_db_config__compiled_css_invalid', 'PresEdit returned PE__ERR_C_CSS_INVALID when requested get_prepared_compiled_css().', null,
                        __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
    }
    
    require('system/db/class.DB_Usr_Manipulator.php');
    $USR_M = new DB_Usr_Manipulator();
    $USR_M->update_pres($__sessuser->get_uid(),
                        $PE->get_prepared_raw_pres(),
                        $PE->get_prepared_raw_css(),
                        $PE->get_prepared_compiled_pres(),
                        $css_compiled);
    $USR_M->destroy();
    
    exit('wie');
    break;
  
  default:
    CYCOM_Inside::err('_db_user__unknown_action', 'Missing $_GET[a].', null, __FILE__, __LINE__, ERR_ACTION_ABORT, true, get_defined_vars());
}
?>
