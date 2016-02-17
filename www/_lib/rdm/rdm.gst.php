<?php
/* DEPRECATED ...........................
  +---------------------------------------------------------------------------------------------------------------+
  | Remote data manager (javascript)                                                                              |
  | [jsrdm.env.php]                                                                                               |
  | Handling of all database gst_*-tables.                                                                        |
  +--------------------------------+------------------------------------------------------------------------------+
  | Accepted local $_GET keys:     |                                                                              |
  | action                         | Action to take.                                                              |
  +--------------------------------+------------------------------------------------------------------------------+
*/
require_once('system/lib.common-inside.php');
require_once('system/lib.session-inside.php');

Session_Inside::checkuselev();

require('system/lib.ajax.php');

require('function.getvars_set.php');

?>