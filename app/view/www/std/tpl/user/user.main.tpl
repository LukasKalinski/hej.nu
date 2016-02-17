<?xml version="1.0" encoding="{$lang.env.charset}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset={$lang.env.charset}" />
    <meta http-equiv="imagetoolbar" content="no" />
    <title>{$lang.env.cycom_short_url}</title>
    <link rel="stylesheet" type="text/css" href="{#CSS_ROOT}{eval@load_css import="ins_inner_base"}" />
    <style type="text/css">
    {$dbuser.pres_css_compiled}
    </style>
    <script type="text/javascript" src="{eval@load_js}"></script>
  </head>
  {subtemplate src="ii.body_start.stpl" sn_group="user" sn_id=0}
    <div id="chead"><div id="chead_title">{$dbuser.username} {$dbuser.gender|gender:$lang.txt.gender_m_short:$lang.txt.gender_f_short}{$dbuser.age}</div></div>
    <div id="userpres">
    {$dbuser.pres_compiled}
    </div>
  {subtemplate src="ii.body_end.stpl"}
</html>