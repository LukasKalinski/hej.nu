<?php
require_once('system/lib.session-outside.php');
Session_Outside::init();
header('Location: '.PATH_WWW__DOCUMENT_ROOT);
exit;
?>