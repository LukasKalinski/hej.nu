<?php
require_once('system/env.globals.php');

/**
 * Note that this is a "real email" function. // 060613: ??
 *
 * @return bool
 * @todo Rebuild to work with new language management.
 */
function CYCOM__mail($mail_id, $receiver_address, $values)
{
  $email = 'foobar';//file_get_contents(PATH_SYS__LANG_ROOT.LANG.'/data/email/'.$mail_id.'.txt'); // @removeme
  
  $headers_length = strpos($email, '>');
  $headers = substr($email, 0, $headers_length);
  $message = trim(substr($email, $headers_length+1));
  
  $subject = '';
  $from = '';
  $headers = explode('|', $headers);
  for($i=0, $ii=count($headers); $i<$ii; $i++)
  {
    preg_match('/(.+):(.+)/i', $headers[$i], $match);
    switch($match[1])
    {
      case 'Subject': $subject = $match[2]; break;
      case 'From': $from = $match[2]; break;
    }
  }
  
  if(is_array($values))
  {
    foreach($values as $key => $value)
      $message = preg_replace('/\{&('.$key.')\}/i', $value, $message);
  }
  
  return (bool)file_get_contents('http://www.hej.nu/mail.php?hash=5832de9be207c8870b48eb43e01643ff'.
                                 '&to='.urlencode($receiver_address).
                                 '&from='.urlencode($from).
                                 '&subject='.urlencode($subject).
                                 '&msg='.urlencode($message), 'r');
}
?>