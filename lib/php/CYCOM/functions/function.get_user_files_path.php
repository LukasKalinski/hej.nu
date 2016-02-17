<?php
/**
 * string get_user_files_path(string)
 */
function CYCOM__get_user_files_path($uid)
{
  return USER_FILES_ROOT . substr($uid, 1, 3) . '/' . $uid . '/';
}
?>