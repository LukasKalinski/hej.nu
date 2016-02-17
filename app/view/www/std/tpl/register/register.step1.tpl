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
    {* Load scripts *}
    <script type="text/javascript">
    	var LANG__user_agreement_accept_confirm = '{$LANG.js_txt.confirm__user_agreement_accept}';
    	var nextRoute = '{eval@#ROUTE_STEP_2}';
    </script>
    <script type="text/javascript" src="{eval@load_js}"></script>
    
    {subtemplate src="register/register.body_start.stpl" title=$LANG.txt.page_title__user_agreement step=1}
      <div id="user_agreement" class="scrollBox">
        {$LANG.file.user_agreement}<br /><br />
      </div>
      <div id="footer">
        <div id="footer_carea_l">
          <a href="javascript:history.go(-1);">
            <img src="{#BTN_ROOT}{eval@make_button type="global" label=$LANG.btn.global__back icon_l="arrows_left"}" alt="" />
          </a>
        </div>
        <div id="footer_carea_r">
          <img src="{#BTN_ROOT}{eval@make_button type="global" label=$LANG.btn.global__decline icon_r="minus"}" alt="" />
          <a href="javascript:uag_accept();">
            <img src="{#BTN_ROOT}{eval@make_button type="global" label=$LANG.btn.global__accept icon_r="plus"}" alt="" />
          </a>
        </div>
      </div>
    {subtemplate src="register/register.body_end.stpl"}
  </body>
</html>