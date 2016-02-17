<?php
/**
 * Constraints for the Guestbook (gst_*) table island.
 *
 * @package system/db/constraints.gst
 * @since ?
 * @version 2006-06-06
 * @copyright Cylab 2006
 * @author Lukas Kalinski
 */

define('CONSTR_GST__MESSAGE_MINLEN', 3);
define('CONSTR_GST__MESSAGE_MAXLEN', 1000);
define('CONSTR_GST__MESSAGE_MAXLEN_HTMLE', CONSTR_GST__MESSAGE_MAXLEN*6);
?>