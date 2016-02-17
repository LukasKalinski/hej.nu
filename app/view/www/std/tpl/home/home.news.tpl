<?xml version="1.0" encoding="{$lang.env.charset}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset={$lang.env.charset}" />
    <meta http-equiv="imagetoolbar" content="no" />
    <title>{$lang.env.cycom_short_url}</title>
    <link rel="stylesheet" type="text/css" href="{#CSS_ROOT}{eval@load_css include="ins_inner_base"}" />
    <script type="text/javascript" src="{eval@load_js scramble_level="low"}"></script>
  </head>
  {subtemplate src="ii.body_start.stpl"}
    <div id="chead"><div id="chead_title">{$lang.txt.news__title_news}</div></div>
    <div id="test"></div>
    Waiting for news system ...
  {subtemplate src="ii.body_end.stpl"}
</html>