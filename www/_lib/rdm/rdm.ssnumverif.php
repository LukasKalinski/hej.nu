<?php

// File is not in use ......

//require_once('system/lib.rdm.php');
//RDM::begin();
//
//require('function.getvars_set.php');
//if(!Cylib__getvars_set('data_c,country_id,ssnum'))
//  RDM::handle_error('rdm__missing_key', 'Required keys are missing, see file specification for further information.', __FILE__, __LINE__);
//
//switch($_GET['country_id'])
//{
//  case 204: // Sweden
//    extract($_GET, EXTR_REFS);
//    
//    $ssnum = explode('-', $ssnum);
//    $birth = $ssnum[0];
//    $vernum = $ssnum[1];
//    
//    if(strlen($birth) > 6) $birth = substr($birth, 2, 6);
//    // Birth date number fail.
//    if(strlen($birth) != 6)
//    {
//      echo 'var '.$data_c.'=false;';
//      break;
//    }
//    
//    
//    
//    echo 'alert("'.$ssnum.'");';
//    echo 'var '.$_GET['data_c'].'=false;';
//    break;
//  default:
//    echo 'var '.$_GET['data_c'].'=true;';
//}
//
//RDM::end();
?>