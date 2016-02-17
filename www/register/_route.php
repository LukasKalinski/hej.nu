<?php
require('system/lib.common-outside.php');
require_once('system/lib.session-outside.php');

Session_Outside::checkuselev();

require('function.getvars_set.php');
require('function.postvars_set.php');
require('_common.php');

// Check that we have a route.
if(!Cylib__getvars_set('r'))
  CYCOM_Outside::err('register__no_get_key_route', '$_GET[r] not set.', null, __FILE__, __LINE__, ERR_ACTION_REDIRECT, true, get_defined_vars());

switch($_GET['r'])
{
  case ROUTE_STEP_1:
    Session_Outside::clear_temp();
    Session_Outside::clear_flags();
    
    require('cte/engine/class.CTE.php');
    CTE::set_language(LANG);
    CTE::display('register/register.step1.tpl');
    
    Session_Outside::set_flag('REG_STEP_1_PASSED');
    break;
  
  case ROUTE_STEP_2:
    if(Session_Outside::flag_isset('REG_STEP_1_PASSED'))
    {
      require_once('system/db/class.DB_Geo_Retriever.php');
      require('cte/engine/class.CTE.php');
      
      CTE::set_language(LANG);
      
      $GEO = new DB_Geo_Retriever();
      CTE::register_var('languages', $GEO->get_languages());
      CTE::register_var('countries', $GEO->get_countries());
      
      Session_Outside::temp_exporttocte('language_id,country_id,region_id,city_id,district');
      if(Session_Outside::temp_isset('country_id,region_id'))
      {
        CTE::create_var('regions', $GEO->get_regions(Session_Outside::temp_get('country_id')));
        CTE::create_var('cities', $GEO->get_cities(Session_Outside::temp_get('region_id')));
      }
      
      $GEO->destroy();
      
      CTE::display('register/register.step2.tpl');
      
      Session_Outside::set_flag('REG_STEP_2_PASSED');
    }
    else
    {
      CYCOM_Outside::err('reg__invalid_step_order_1_2', 'Reg step 1 not passed when requesting step 2.', null,
                         __FILE__, __LINE__, ERR_ACTION_REDIRECT, true, get_defined_vars(), $_SERVER['PHP_SELF'].'?r='.ROUTE_STEP_1);
    }
    break;
  
  case ROUTE_STEP_2_POST_HANDLER:
    if(Session_Outside::flag_isset('REG_STEP_2_PASSED') && Cylib__postvars_set('language_id,country_id,region_id,city_id,district'))
    {
      require_once('modifier.encode_numeric_entity.php');
      $_POST = Cylib__encode_numeric_entity($_POST);
      
      $SESS_GLOBAL['lang'] = $_POST['language_id'];
      
      Session_Outside::temp_importassoc($_POST, 'language_id,country_id,region_id,city_id,district');
      Session_Outside::set_flag('REG_STEP_2_POST_HANDLER_PASSED');
      
      header('Location: '.$_SERVER['PHP_SELF'].'?r='.ROUTE_STEP_3);
      exit;
    }
    else
    {
      CYCOM_Outside::err('reg__invalid_step_order_2_2ph', 'Reg step 2 not passed when requesting step 2 post handler.', null,
                         __FILE__, __LINE__, ERR_ACTION_REDIRECT, true, get_defined_vars(), $_SERVER['PHP_SELF'].'?r='.ROUTE_STEP_1);
    }
    break;
  
  case ROUTE_STEP_3:
    if(Session_Outside::flag_isset('REG_STEP_2_POST_HANDLER_PASSED'))
    {
      $years = array();
      $months = array();
      $days = array();
      for($i=(int)date('Y')-4; $i>((int)date('Y')-90); $i--)
        array_push($years, $i);
      for($i=1; $i<=12; $i++)
        array_push($months, $i);
      for($i=1; $i<=31; $i++)
        array_push($days, $i);
      
      require_once('system/db/constraints.usr.php');
      require('cte/engine/class.CTE.php');
      CTE::set_language(LANG);
      
      // Export settings if set (user pressed back button or something).
      Session_Outside::temp_exporttocte('first_name,last_name,ssn,email,phone_number,citizenship_id,address,post_code,post_city');
      
      require_once('system/db/class.DB_Geo_Retriever.php');
      $GEO = new DB_Geo_Retriever();
      CTE::register_var('countries', $GEO->get_countries());
      $GEO->destroy();
      
      CTE::register_var('months', $months);
      CTE::register_var('days', $days);
      CTE::display('register/register.step3.tpl');
      
      Session_Outside::set_flag('REG_STEP_3_PASSED');
    }
    else
    {
      CYCOM_Outside::err('reg__invalid_step_order_2ph_4', 'Reg step 2 post handler not passed when requesting step 3.', null,
                         __FILE__, __LINE__, ERR_ACTION_REDIRECT, true, get_defined_vars(), $_SERVER['PHP_SELF'].'?r='.ROUTE_STEP_1);
    }
    break;
  
  case ROUTE_STEP_3_POST_HANDLER:
    if(Session_Outside::flag_isset('REG_STEP_3_PASSED') && Cylib__postvars_set('first_name,last_name,ssn,email,phone_number,address,post_code,post_city'))
    {
      require_once('modifier.encode_numeric_entity.php');
      $_POST = Cylib__encode_numeric_entity($_POST);
      
      Session_Outside::temp_importassoc($_POST, 'first_name,last_name,ssn,email,phone_number,citizenship_id,address,post_code,post_city');
      Session_Outside::set_flag('REG_STEP_3_POST_HANDLER_PASSED');
      header('Location: '.$_SERVER['PHP_SELF'].'?r='.ROUTE_STEP_4);
      exit;
    }
    else
    {
      CYCOM_Outside::err('reg__step3_not_passed_in_step3_posthandler', 'Reg step 3 not passed when requesting step 3 post handler.', null,
                         __FILE__, __LINE__, ERR_ACTION_REDIRECT, true, get_defined_vars(), $_SERVER['PHP_SELF'].'?r='.ROUTE_STEP_1);
    }
    break;
  
  case ROUTE_STEP_4:
    if(Session_Outside::flag_isset('REG_STEP_3_POST_HANDLER_PASSED'))
    {
      $years = array();
      $months = array();
      $days = array();
      for($i=(int)date('Y')-4; $i>((int)date('Y')-90); $i--)
        array_push($years, $i);
      for($i=1; $i<=31; $i++)
        array_push($days, $i);
      
      require_once('system/db/constraints.usr.php');
      require('cte/engine/class.CTE.php');
      CTE::set_language(LANG);
      CTE::register_var('years', $years);
      CTE::register_var('days', $days);
      Session_Outside::temp_exporttocte('dob_year,dob_month,dob_day,gender,username');
      CTE::display('register/register.step4.tpl');
      
      Session_Outside::set_flag('REG_STEP_4_PASSED');
    }
    else
    {
      CYCOM_Outside::err('reg__invalid_step_order_3ph_4', 'Reg step 3 post handler not passed when requesting step 4.', null,
                         __FILE__, __LINE__, ERR_ACTION_REDIRECT, true, get_defined_vars(), $_SERVER['PHP_SELF'].'?r='.ROUTE_STEP_1);
    }
    break;
  
  case ROUTE_STORE:
    if(Session_Outside::flag_isset('REG_STEP_4_PASSED'))
    {
      require_once('modifier.encode_numeric_entity.php');
      $_POST = Cylib__encode_numeric_entity($_POST);
      Session_Outside::temp_importassoc($_POST, 'dob_year,dob_month,dob_day,gender,username,password');
      header('Location: _db.php');
      exit;
    }
    else
    {
      CYCOM_Outside::err('reg__invalid_step_order_4_store', 'Reg step 4 not passed when requesting db storage.', null,
                         __FILE__, __LINE__, ERR_ACTION_REDIRECT, true, get_defined_vars(), $_SERVER['PHP_SELF'].'?r='.ROUTE_STEP_1);
    }
    break;
  
  case ROUTE_ABORT:
    Session_Outside::clear_temp();
    Session_Outside::clear_flags();
    CYCOM_Outside::msg('registration_aborted', '/struct_outside.php', MSG_APPEAR_JS);
    break;
  
  case ROUTE_REG_SUCCESS:
    CYCOM_Outside::msg('account_creation_success', '/struct_outside.php', MSG_APPEAR_JS, array('email' => Session_Outside::temp_get('email')));
    exit;
    break;
  
  default:
    CYCOM_Outside::err('reg__invalid_route_value', 'Invalid value supplied for $_GET[r].', null, __FILE__, __LINE__, ERR_ACTION_REDIRECT, true, get_defined_vars());
}
?>