<?php
/**
 * Paths Config
 * Config file containing all paths relevant to the CYCOM environment.
 * 
 * @package CYCOM.Config
 * @since 2006-06-20
 * @version 2006-06-20
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

// System scope:
define('PATH_SYS__DOCUMENT_ROOT',     getenv('DOCUMENT_ROOT') . '/');
define('PATH_SYS__ERROR_OUTPUT_ROOT', PROJECT_ROOT.'log/error/');
define('PATH_SYS__PHOTO_ROOT',        PROJECT_ROOT.'usr/photos/');

// WWW scope:
define('PATH_WWW__DOCUMENT_ROOT',     '/');
define('PATH_WWW__JSC_OUTPUT_ROOT',   PATH_WWW__DOCUMENT_ROOT.'_lib/~jsc/');
define('PATH_WWW__CSS_OUTPUT_ROOT',   PATH_WWW__DOCUMENT_ROOT.'_lib/~css/');
define('PATH_WWW__GFX_OUTPUT_ROOT',   PATH_WWW__DOCUMENT_ROOT.'_lib/gfx/');
define('PATH_WWW__PHOTO_ROOT',        PATH_WWW__DOCUMENT_ROOT.'usr/photos/');
define('PATH_WWW__GFX_ROOT',          PATH_WWW__DOCUMENT_ROOT.'_lib/gfx/');
define('PATH_WWW__GFX_COMMON_ROOT',   PATH_WWW__GFX_ROOT.'_cmn/');
?>