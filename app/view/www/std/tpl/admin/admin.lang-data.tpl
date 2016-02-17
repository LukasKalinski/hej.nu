<?xml version="1.0" encoding="{$lang.env.charset}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset={$lang.env.charset}" />
    <meta http-equiv="imagetoolbar" content="no" />
    <title>{$lang.env.cycom_short_url}</title>
    <link rel="stylesheet" type="text/css" href="{#CSS_ROOT}{eval@load_css include="ins_inner_base;forms"}" />
    <script type="text/javascript">
    <!--
    {literal}
    var LANG = {};
    {/literal}
    LANG.language_not_set = 'Language not set.';
    LANG.context_not_set = 'Context not set.';
    LANG.entry_not_set = 'Entry not set.';
    LANG.entry_value_empty = 'no value set';
    LANG.saved = 'saved'
    LANG.reached_end = 'End reached.';
    LANG.reached_start = 'Start reached.';
    //-->
    </script>
    <script type="text/javascript" src="{eval@load_js scramble_level="low"}"></script>
  </head>
  {subtemplate src="ii.body_start.stpl" page_title="Language data editor"}
  
    {* DATA REQUESTING *}
    <div class="box">
      <div class="boxh">Setup Fetch Rules</div>
      <div class="boxc">
        <form id="frm_fetch">
          <table>
            <tr>
              <td>
                <div class="flabel">Language:</div>
                <select id="language" name="language_id" onchange="setLanguage(this.value);"></select>
              </td>
              <td>
                <div class="flabel">Context:</div>
                <select id="context" name="context_id" onchange="requestData(this);"></select>
              </td>
              <td>
                <div class="flabel">Top category:</div>
                <select id="topcat" name="topcat_id" onchange="requestData(this);"></select>
              </td>
              <td>
                <div class="flabel">Sub category:</div>
                <select id="subcat" name="subcat_id" onchange="requestData(this);"></select>
              </td>
            </tr>
            <tr>
              <td colspan="4">
                <div class="flabel">Entry:</div>
                <select id="entry" name="entry_id" onchange=""></select>
              </td>
            </tr>
          </table>
        </form>
      </div>
    </div>
    <div class="btnp">
      <div class="btnp_l">
        <a href="?"><img src="{#BTN_ROOT}{eval@make_button label="Back" type="global" icon_l="arrows_left"}" alt="" /></a>
      </div>
      <div class="btnp_r">
        <a href="javascript:load();"><img src="{#BTN_ROOT}{eval@make_button label="Load" type="global"}" alt="" /></a>
      </div>
    </div>
    
    {* DATA MANAGEMENT *}
    <div class="box">
      <div class="boxh">Currently Editing</div>
      <div class="boxc">
        <h2>Entry path:</h2>
        <p id="entry_path">-</p>
        <br />
        <h2>Entry description:</h2>
        <p id="entry_desc">-</p>
        <br />
        <form id="frm_entry_value">
          <input type="hidden" name="language_id" value="" />
          <input type="hidden" name="entry_id" value="" />
          <div class="flabel">Value:</div>
          <textarea name="entry_value" id="entry_value" value="" onkeyup="resizeTA(this);" cols="50" rows="3" disabled="disabled"></textarea>
          <span id="entry_saved"></span>
        </form>
        <h2>Default language value:</h2>
        <p id="entry_def">-</p>
      </div>
    </div>
    <div class="btnp">
      <div class="btnp_l">
        <a href="?"><img src="{#BTN_ROOT}{eval@make_button label="Back" type="global" icon_l="arrows_left"}" alt="" /></a>
      </div>
      <div class="btnp_r">
        <a href="javascript:go(-1);"><img src="{#BTN_ROOT}{eval@make_button label="Previous" type="global" icon_l="arrows_left"}" alt="" /></a>
        <a href="javascript:save();"><img src="{#BTN_ROOT}{eval@make_button label="Save" type="global"}" alt="" /></a>
        <a href="javascript:go(1);"><img src="{#BTN_ROOT}{eval@make_button label="Next" type="global" icon_r="arrows_right"}" alt="" /></a>
      </div>
    </div>
  
  {subtemplate src="ii.body_end.stpl"}
</html>