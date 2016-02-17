<?php
/**
 * string get_userphoto_path(string, int, gender)
 *
 * @param string $user_id
 * @param int $mode
 * @param string $gender
 * @return string/int
 */
function CYCOM__get_userphoto_path($user_id, $mode, $gender)
{
  // Own photo mode.
  if($mode == -1)
  {
    require_once('function.get_user_files_path.php');
    return CYCOM__get_user_files_path($user_id) . '/photo/photo.jpg';
  }
  // No photo mode.
  elseif($mode == 0)
  {
    return GFX_ROOT . 'noup_' . $gender . '.gif';
  }
  // Illustration photo mode.
  elseif($mode > 0)
  {
    return UP_ILL_ROOT . '/' . $gender . $mode . '.gif';
  }
  // Invalid photo mode.
  else
  {
    CYCOM::err('function_get_userphoto_path.invalid_mode', 'Invalid photo mode in function get_userphoto_path.',
               array('mode'=>$mode), __FILE__, __FUNCTION__, null, __LINE__, ERR_ACTION_CONTINUE, false);
    return '';
  }
}
?>