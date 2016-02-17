<?xml version="1.0" encoding="{$lang.env.charset}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset={$lang.env.charset}" />
    <meta http-equiv="imagetoolbar" content="no" />
    <title>{$lang.env.cycom_short_url}</title>
    <link rel="stylesheet" type="text/css" href="{#CSS_ROOT}{eval@load_css include="ins_inner_base;forms"}" />
    <script type="text/javascript" src="{eval@load_js}"></script>
  </head>
  {subtemplate src="ii.body_start.stpl"}
    <div id="chead"><div id="chead_title">Language entry editor</div></div>
    
    {* CASE: MANAGE LANGUAGE STRUCTURE *}
    <div class="box">
      <div class="boxh">
        Add/edit context and categories
      </div>
      <div class="boxc">
        <b>Here you can:</b><br />
        <ul>
          <li>Add new context (ini-sections)</li>
          <li>Add new top category ("user", "config" etc)</li>
          <li>Add new sub category ("guestbook", "photo" etc)</li>
          <li>Add new entry ("welcome_user" etc)</li>
        </ul>
      </div>
    </div>
    <div class="btnp">
      <div class="btnp_r">
        <a href="?manage=struct">
          <img src="{#BTN_ROOT}{eval@make_button type="global" icon_r="arrows_right" label=$lang.btn.global__go}" class="hand" alt="" />
        </a>
      </div>
    </div>
    
    {* CASE: MANAGE LANGUAGE DATA *}
    <div class="box">
      <div class="boxh">
        Edit language data
      </div>
      <div class="boxc">
        <b>Here you can:</b><br />
        <ul>
          <li>Edit language data</li>
        </ul>
      </div>
    </div>
    <div class="btnp">
      <div class="btnp_r">
        <a href="?manage=data">
          <img src="{#BTN_ROOT}{eval@make_button type="global" icon_r="arrows_right" label=$lang.btn.global__go}" class="hand" alt="" />
        </a>
      </div>
    </div>
    
  {subtemplate src="ii.body_end.stpl"}
</html>