<?php
/**
 * string possess(string)
 * English [en] possession grammar modifier.
 */
function CTEPL_LANG__possess($owner, $possession)
{
  if(strtolower(substr($owner,-1, 1)) == 's')
    return $owner.'\' '.$possession;
  else
    return $owner.'\'s '.$possession;
}
?>