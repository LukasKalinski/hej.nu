<?php
/*
  +---------------------------------------------------------------------------------------------------------------+
  | Remote data manager (javascript)                                                                              |
  | [jsrdm.env.php]                                                                                               |
  | Retriever for all database env_-tables.                                                                       |
  +--------------------------------+------------------------------------------------------------------------------+
  | Accepted local $_GET keys:     |                                                                              |
  | action                         | Action, could be: 'getregionlist' or 'getcitylist'                           |
  | data_co                        | Data container object, name of the javascript object definition.             |
  | data_c                         | Data container, name of the javascript array to store data objects in.       |
  +--------------------------------+------------------------------------------------------------------------------+
*/

require('system/lib.rdm.php');
require('system/db/class.DB_Geo_Retriever.php');

RDM::begin();

$params_ok = false;
if(key_exists('data_co', $_GET) && preg_match('/^__[a-z][a-z0-9_]*$/i', $_GET['data_co'])  &&
   key_exists('data_c', $_GET)  && preg_match('/^__[a-z][a-z0-9_]*$/i', $_GET['data_c']))
{
  require_once('modifier.rm_html_entities.php');
  echo 'var '.$_GET['data_c'].' = new Array();';
  
  switch($_GET['action'])
  {
    case 'getregionlist':
      if(key_exists('country_id', $_GET) && is_numeric($_GET['country_id']))
      {
        $params_ok = true;
        
        $ENV = new DB_Geo_Retriever();
        $regions = $ENV->get_regions($_GET['country_id']);
        $ENV->destroy();
        
        for($i=0, $ii=count($regions); $i<$ii; $i++)
          echo $_GET['data_c'].'['.$i.']=new '.$_GET['data_co'].'('.$regions[$i]['id'].',"'.CYCOM__rm_html_entities($regions[$i]['name']).'");';
      }
      break;
    
    case 'getcitylist':
      if(key_exists('region_id', $_GET) && is_numeric($_GET['region_id']))
      {
        $params_ok = true;
        
        $ENV = new DB_Geo_Retriever();
        $cities = $ENV->get_cities($_GET['region_id']);
        $ENV->destroy();
        
        for($i=0, $ii=count($cities); $i<$ii; $i++)
          echo $_GET['data_c'].'['.$i.']=new '.$_GET['data_co'].'('.$cities[$i]['id'].',"'.CYCOM__rm_html_entities($cities[$i]['name']).'");';
      }
      break;
  }
}

if(!$params_ok)
  RDM::handle_error('jsrdm__regionlist_get_parameters_failed',
                    'Parameter failure, see file specification for further instructions.',
                    __FILE__, __LINE__);

RDM::end();
?>