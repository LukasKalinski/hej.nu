<?php
/********************************************************************************
 * File:          regional/se/class.Region.php
 * Description:   Implementation of interface:regional for country se=sweden.
 * Begin:         2006-02-26
 * Edit:          
 * Author:        Lukas Kalinski
 * Copyright:     2006- CyLab Sweden
\********************************************************************************/

require('regional/interface.regional.php');
class Region implements regional
{
  public static function ssnValid(&$_ssn, $_dob, $_gender)
  {
    $SSN_OK = true;
  
    $_ssn = str_replace('-', '', $_ssn);
    
    // Compose date string using DOB-components (year, month and day).
    $dob = $_dob[0].str_pad($_dob[1], 2, '0', STR_PAD_LEFT).str_pad($_dob[2], 2, '0', STR_PAD_LEFT);
    $dob = substr($dob, 2);
    $ssn_dob = substr($_ssn, 0, 6);
    
    // Compare ssn dob indicatior (YYMMDD) with chosen date of birth.
    if($dob != $ssn_dob)
      return false;
    
    // Compare ssn gender with chosen gender.
    $ssn_gender = ((int)substr($_ssn, 8, 1) % 2 == 0 ? 'F' : 'M');
    if($_gender != $ssn_gender)
      return false;
    
    // Calculate last last number.
    $ssn_9 = substr($_ssn, 0, 9);
    $last_num = 0;
    $multiplier = 2;
    for($i=0; $i<strlen($ssn_9); $i++)
    {
      $current_result = (string)((int)$ssn_9{$i} * $multiplier);
      if(strlen($current_result) > 1)
        $current_result = (string)((int)$current_result{0} + (int)$current_result{1});
      $last_num += $current_result;
      $multiplier = ($multiplier == 2 ? 1 : 2);
    }
    $last_num = (string)$last_num;
    if(strlen($last_num) > 1)
      $last_num = (string)((((int)$last_num{0}+1)*10) - (int)$last_num);
    
    if($last_num != substr($_ssn, 9, 1))
      return false;
    
    $_ssn = substr($_ssn, 0, 6) . '-' . substr($_ssn, 6, 4);
    return true;
  }
}
?>