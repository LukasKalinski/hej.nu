<?php
require_once('system/lib.rdm.php');
RDM::begin();

require('function.getvars_set.php');
if(!Cylib__getvars_set('username,data_c'))
  RDM::handle_error('rdm__missing_key', 'Required keys are missing, see file specification for further information.', __FILE__, __LINE__);

require('system/db/class.CYCOM_DB_Usr_Accessor.php');
$DBUSR = new CYCOM_DB_Usr_Accessor();
$user = $DBUSR->get_user_by_name($_GET['username'], 'id');
if($user !== false)
  echo $_GET['data_c'].'="'.$user['id'].'";';
else
  echo $_GET['data_c'].'=null;';

RDM::end();
?>