<?php
require('system/lib.common-outside.php');
require('system/lib.session-outside.php');
require('_common.php');

Session_Outside::checkuselev();
Session_Outside::temp_exportassoc($reg_data);


// ## Start value validation.

require('system/db/constraints.usr.php');

// ## Regional and language setup data
require('system/db/class.DB_Geo_Retriever.php');
$GEO = new DB_Geo_Retriever();

// Check language:
if($GEO->get_language($reg_data['language_id']) == false)
  CYCOM_Outside::err('reg__invalid_language_id', 'Invalid language ID.', null, __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());

// Check location (country, region, city):
if(!$GEO->location_is_valid($reg_data['country_id'], $reg_data['region_id'], $reg_data['city_id']))
  CYCOM_Outside::err('reg__invalid_location', 'Invalid location.', null, __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());

// Check district_desc:
if(strlen($reg_data['district']) > CYCOM_DB_Usr_Constraints::DISTRICT_MAXLEN)
  CYCOM_Outside::err('reg__district_desc_maxlen_exceeded', 'District maxlen exceeded.', null, __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());

// ## Private Section data
// Check first_name:
if(strlen($reg_data['first_name']) < CYCOM_DB_Usr_Constraints::FIRST_NAME_MINLEN && strlen($reg_data['first_name']) > CYCOM_DB_Usr_Constraints::FIRST_NAME_MAXLEN)
  CYCOM_Outside::err('reg__first_name_invalid_length', 'Firstname too short/long.', null, __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());

// Check last_name:
if(strlen($reg_data['last_name']) < CYCOM_DB_Usr_Constraints::LAST_NAME_MINLEN && strlen($reg_data['last_name']) > CYCOM_DB_Usr_Constraints::LAST_NAME_MAXLEN)
  CYCOM_Outside::err('reg__last_name_invalid_length', 'Last name too short/long.', null, __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());
exit('fix @todo');
// Prepare ssn validator:
$file_ssn_validator = PATH_SYS__PHPLIB_ROOT.'regional/country'.$reg_data['citizenship_id'].'.ssnvalidator.php'; // @todo fix
if(!file_exists($file_ssn_validator))
  CYCOM_Outside::err('reg__ssn_validating_file_not_found', 'File ($0) for SSN validation not found.', array($file_ssn_validator),
                     __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());
// Check ssn:
require_once($file_ssn_validator);
if(!CYCOM_REGIONAL__ssn_is_valid($reg_data['ssn'], array($reg_data['dob_year'],$reg_data['dob_month'],28), $reg_data['gender']))
{
  CYCOM_Outside::err('reg__ssn_invalid', 'Invalid SSN.', null, __FILE__, __LINE__, ERR_ACTION_CONTINUE, true, get_defined_vars());
  
  if(!Session_Outside::flag_isset('REG_DB', 'SSN_FAILED_ONCE'))
  {
    Session_Outside::set_flag('REG_DB', 'SSN_FAILED_ONCE');
    CYCOM_Outside::msg('failed_match_dob_ssn', '_route.php?r=2', MSG_APPEAR_JS);
  }
  else
  {
    CYCOM_Outside::msg('failed_match_dob_ssn', '/', MSG_APPEAR_JS);
  }
}
// Check email:
if(!preg_match(CYCOM_DB_Usr_Constraints::EMAIL_REGEX, $reg_data['email']))
  CYCOM_Outside::err('reg__email_regex_match_failed', 'Email did not match constraint regex.', null,
                     __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());

// Check phone_number:
if(!preg_match(CYCOM_DB_Usr_Constraints::PHONENUM_REGEX, $reg_data['phone_number']))
  CYCOM_Outside::err('reg__phone_number_regex_match_failed', 'Phone number did not match constraint regex.', null,
                     __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());

// Check citizenship_id:
if(!$GEO->location_is_valid($reg_data['citizenship_id']))
  CYCOM_Outside::err('reg__citizenship_id_invalid', 'Invalid citizenship ID.', null, __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());

// Check address:
if(strlen($reg_data['address']) < CYCOM_DB_Usr_Constraints::ADDRESS_MINLEN && strlen($reg_data['address']) > CYCOM_DB_Usr_Constraints::ADDRESS_MAXLEN)
  CYCOM_Outside::err('reg__address_invalid_length', 'Address too short/long.', null, __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());

// Check post_code:
if(!preg_match(CYCOM_DB_Usr_Constraints::POST_CODE_REGEX, $reg_data['post_code']))
  CYCOM_Outside::err('reg__post_code_regex_match_failed', 'Post code did not match constraint regex.', null,
                     __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());

// Check post_city:
if(strlen($reg_data['post_city']) < CYCOM_DB_Usr_Constraints::POST_CITY_MINLEN && strlen($reg_data['post_city']) > CYCOM_DB_Usr_Constraints::POST_CITY_MAXLEN)
  CYCOM_Outside::err('reg__post_city_invalid_length', 'Post city too short/long.', null, __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());

// ## Account Section
// Check date of birth:
if(!checkdate((int)$reg_data['dob_month'], (int)$reg_data['dob_day'], (int)$reg_data['dob_year']))
  CYCOM_Outside::err('reg__dob_invalid', 'Date of birth is invalid.', null, __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());

// Check gender:
if(!preg_match(CYCOM_DB_Usr_Constraints::GENDER_REGEX, $reg_data['gender']))
  CYCOM_Outside::err('reg__gender_regex_match_failed', 'Gender did not match constraint regex.', null,
                     __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());

// Check username:
if(!preg_match(CYCOM_DB_Usr_Constraints::USERNAME_REGEX, $reg_data['username']))
  CYCOM_Outside::err('reg__username_regex_match_failed', 'Username did not match constraint regex.', null,
                     __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());

// Check password:
if(!preg_match(CYCOM_DB_Usr_Constraints::PASSWORD_REGEX, $reg_data['password']))
  CYCOM_Outside::err('reg__password_regex_match_failed', 'Password did not match constraint regex.', null,
                     __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());


// ##################
// ## Create account.
// ##################
$reg_data['dob'] = $reg_data['dob_year'] . str_pad($reg_data['dob_month'], 2, '0', STR_PAD_LEFT) . str_pad($reg_data['dob_day'], 2, '0', STR_PAD_LEFT);
unset($reg_data['dob_year'], $reg_data['dob_month'], $reg_data['dob_day']);

// Check if username is taken.
require('system/db/class.CYCOM_DB_Usr_Accessor.php');
$DBUSR = new CYCOM_DB_Usr_Accessor();
$user = $DBUSR->get_user_by_name($reg_data['username']);
if($user !== false)
{
  CYCOM_Outside::msg('username_in_use', '_route.php?r='.ROUTE_STEP_4, MSG_APPEAR_JS, array('username' => $reg_data['username']));
}
else
{
  require('system/db/class.DB_Usr_Manipulator.php');
  $DBUSR = new DB_Usr_Manipulator();
  
  $pid = $DBUSR->insert($reg_data);
  $DBUSR->destroy();
  
  if(strlen($pid) == 38)
  {
    require_once('function.mail.php');
    if(!CYCOM__mail('regconfirm', $reg_data['email'], array('username' => $reg_data['username'],
                                                            'password' => $reg_data['password'],
                                                            'url' => 'http://'.$_SERVER['HTTP_HOST'].'/?pid='.urlencode($pid))))
      CYCOM_Outside::err('reg__mail_function_failed', 'Mail function failed for some reason.', null,
                         __FILE__, __LINE__, ERR_ACTION_SYSMSG, false, get_defined_vars());
    else
    {
      header('Location: _route.php?r='.ROUTE_REG_SUCCESS);
      exit;
    }
  }
  else
  {
    CYCOM_Outside::err('reg__sys_invalid_process_id', 'DBMS storage process must have gone wrong somewhere and the process_id retrieval failed.', null,
                       __FILE__, __LINE__, ERR_ACTION_SYSMSG, false, get_defined_vars());
  }
}
?>