<?xml version="1.0" encoding="{$LANG.env.charset}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset={$LANG.env.charset}" />
    <meta http-equiv="imagetoolbar" content="no" />
    <title>{$LANG.env.cycom_short_url}</title>
    <link rel="stylesheet" type="text/css" href="{#CSS_ROOT}{eval@load_css import="out_base"}" />
  </head>
  <body>
    {subtemplate src="register/register.body_start.stpl" title=$LANG.txt.page_title__user_agreement step=1}
      Hej hej!
      <div id="footer">
        <div id="footer_carea_r">
          <img src="{#BTN_ROOT}{eval@make_button type="global" label=$LANG.btn.global__ok}" alt="" />
        </div>
      </div>
    {subtemplate src="register/register.body_end.stpl"}
  </body>
</html>