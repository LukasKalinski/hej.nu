<?php
/**
 * Creates a directory if not already created. Returns true if directory was created and false if it already existed.
 * 
 * @param string $path
 * @param int $chmod # Octal representation of permissions.
 * @param string[] $owner # 0:user and 1:group. null means default values (see system/env.globals.php).
 * @return bool
 */
function CYCOM__create_dir($path, $chmod, $owner=null)
{
  if(is_null($owner))
    $owner = array(FS_DEFAULT_USER, FS_DEFAULT_GROUP);
  
  if(!is_dir($path) && !file_exists($path))
  {
    mkdir($path);
    chown($path, $owner[0]);
    chgrp($path, $owner[1]);
    chmod($path, $chmod);
    return true;
  }
  else
  {
    return false;
  }
}
?>