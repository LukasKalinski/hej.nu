<?php
header('Content-type: text/plain');
require_once('CTE.php');

$cte = new CTE();
$cte->display('test.tpl');

exit("\n\n\n #exit");
//Cylab::loadFile('CYCOM/system/lib.common-outside.php');
//Cylab::loadFile('CYCOM/system/lib.session-outside.php');

require_once('Cylib/functions/get_microtime.php');
$start = Cylib__get_microtime();

header('Content-type: text/plain');

require_once('model/logic/DB/Island/Usr/Accessor.php');
$usr_accessor = new CYCOM_DB_Island_Usr_Accessor();
$user_id = $usr_accessor->get_user_by_name('sfish');
$user_id = $user_id['id'];

require_once('model/logic/DB/Island/Gst/Accessor.php');
$gst_accessor = new CYCOM_DB_Island_Gst_Accessor();

print_r($gst_accessor->get_messages($user_id, 0, 1));

echo "\n\n\n".round(Cylib__get_microtime() - $start, 4);

/*
class foo extends Cylab_DB
{
  public function __construct($server)
  {
    parent::__construct($server);
    $query = self::create_query('SELECT * FROM usr_user', Cylab_DB_Query::TYPE_SELECT_MULTIPLE);
    $query->execute();
    print_r($query->get_parsed_result());
  }
}
*/
//$foo = new foo(array('user' => 'cycom', 'password' => 'VoatyalAng5'));

//require_once('Cylab/Lang/Accessor_DB.php');

// Access: w_plain.admin.lang_struct.form_field_label__action


//echo Cylab_Lang_Accessor_DB::get('w_plain.admin.lang_struct.form_field_label__action');

exit("\n\n# END");

Session_Outside::init();

// Check if account enabling is requested.
if(key_exists('pid', $_GET) && strlen($_GET['pid']) == 38)
{
  require_once('system/db/class.DB_Usr_Manipulator.php');
  $USR = new DB_Usr_Manipulator();
  $pid_found = $USR->account_set_active($_GET['pid']);
  $USR->destroy();

  if($pid_found)
  {
      CYCOM_Outside::msg('account_activate_success', PATH_WWW__DOCUMENT_ROOT, SYSMSG__AP_JS); // FIXME
  }
  else
  {
      CYCOM_Outside::err('reg__account_activation_failed', 'Account activation failed.', null, __FILE__, __LINE__,
                         ERR_ACTION__SYSMSG, true, get_defined_vars());
  }

  exit;
}

Cylab::loadFile('Cylab/CTE/engine/class.CTE.php');
CTE::display('index.tpl');
?>
