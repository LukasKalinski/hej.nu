<?xml version="1.0" encoding="{$lang.env.charset}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset={$lang.env.charset}" />
    <meta http-equiv="imagetoolbar" content="no" />
    <title>{$lang.env.cycom_short_url}</title>
    <link rel="stylesheet" type="text/css" href="{#CSS_ROOT}{eval@load_css include="ins_inner_base"}" />
    <script type="text/javascript" src="{eval@load_js include="lib.browser.js"}"></script>
  </head>
  {subtemplate src="ii.body_start.stpl"}
    <div id="chead"><div id="chead_title">{$lang.txt.config__title_public}</div></div>
    <div class="box">
      <div class="boxhead">{$lang.txt.config__title_public_facts}</div>
      <div class="boxcontent">{$lang.txt.config__desc_public_facts}</div>
    </div>
    <div class="box">
      <div class="boxhead">{$lang.txt.config__title_public_pres}</div>
      <div class="boxcontent">{$lang.txt.config__desc_public_pres}</div>
    </div>
    <div class="btnp">
      <div class="btnp_r">
        <a href="javascript:openPresEdit();">
          <img src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__edit_pres}"
               class="hand"
               alt="" />
        </a>
      </div>
    </div>
    <div class="box">
      <div class="boxhead">{$lang.txt.config__title_public_misc}</div>
      <div class="boxcontent">{$lang.txt.config__desc_public_misc}</div>
    </div>
  {subtemplate src="ii.body_end.stpl"}
</html>