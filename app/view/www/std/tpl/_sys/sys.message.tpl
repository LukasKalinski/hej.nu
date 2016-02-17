{*
  System message viewer
    Display a message triggered by PHP.
  
  Variables
    optional:$msg_id          ID of the message (see language data.ini).
    optional:$appearence      Message appearence (html or js), default is "js", which is a simple javascript alert.
    optional:$url             The url to redirect to, default is #DOC_ROOT.
*}
<?xml version="1.0" encoding="{$lang.env.charset}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset={$lang.env.charset}" />
    <meta http-equiv="imagetoolbar" content="no" />
    <title>{$lang.env.cycom_short_url}</title>
    {if $layout eq #MSG_LBASE_INS} {* Message in logged in environment. *}
      <link rel="stylesheet" type="text/css" href="{$css_root}{eval@load_css import="ins_inner_base"}" />
    {else} {* Message in outside environment. *}
      <link rel="stylesheet" type="text/css" href="{$css_root}{eval@load_css import="out_base"}" />
    {/if}
  </head>
  <body>
  {if $appearence eq #MSG_APPEAR_JS}
    <script type="text/javascript">{*</script> just to avoid color coding.. *}
    var m;
    {if isset($msg_id)}
      {if     $msg_id == "system_error"}                           m='{$lang.js_txt.sysmsg__system_error err_id=nreq@$err_id}';
      {elseif $msg_id == "possible_abuse_action_logged"}           m='{$lang.js_txt.sysmsg__possible_abuse_action_logged}';
      {elseif $msg_id == "registration_aborted"}                   m='{$lang.js_txt.sysmsg__registration_aborted}';
      {elseif $msg_id == "no_registration_process_available"}      m='{$lang.js_txt.sysmsg__no_registration_process_available}';
      {elseif $msg_id == "failed_match_dob_ssn"}                   m='{$lang.js_txt.sysmsg__failed_match_dob_ssn}';
      {elseif $msg_id == "account_creation_success"}               m='{$lang.js_txt.sysmsg__account_creation_success email=nreq@$email}';
      {elseif $msg_id == "account_activate_success"}               m='{$lang.js_txt.sysmsg__account_activate_success}';
      {elseif $msg_id == "wrong_username_or_password"}             m='{$lang.js_txt.sysmsg__wrong_username_or_password}';
      {elseif $msg_id == "session_lost"}                           m='{$lang.js_txt.sysmsg__session_lost}';
      {elseif $msg_id == "account_not_activated"}                  m='{$lang.js_txt.sysmsg__account_not_activated}';
      {elseif $msg_id == "username_in_use"}                        m='{$lang.js_txt.sysmsg__username_in_use username=nreq@$username}';
       {else}                                                       m='{$lang.js_txt.sysmsg__unknown_error}';
      {/if}
    {/if}
    alert(m);
    var url = '{if isset($url)}{$url}{else}{$doc_root}{/if}';
    {if $url eq $doc_root or $url eq "/" or $url eq "/_sys/sys.logout.php"}
      top.location.replace(url);
    {else}
      document.location.replace(url);
    {/if}
    </script>
  {elseif $appearence eq #MSG_APPEAR_HTML}
    
  {/if}
  </body>
</html>