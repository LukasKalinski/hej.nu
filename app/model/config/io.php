<?php
/**
 * IO Config
 * Config file for IO/string management.
 *
 * @package CYCOM.Config
 * @since 2006-06-20
 * @version 2006-06-20
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */


/**
 * Fixed length of HTML entities.
 * A HTML Entity should appear on the fixed form: &#xxx; where xxx is any decimal number.
 * Decimal numbers smaller than 100 should be prefixed with one or two zeros (0).
 */
define('IO__HTML_ENTITY_FIXED_LENGTH', 6);
?>