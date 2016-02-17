<?php
/**
 * Auto-prepend file for project CYCOM
 *
 * @package CYCOM
 * @since 2006-06-20
 * @version 2006-06-20
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

// Load Cylab framework:
require_once('Cylab.php');

// Globals:
define('PROJECT_ROOT', preg_replace('/(.*)www\/?/i', '\1', getenv('DOCUMENT_ROOT')));
?>
