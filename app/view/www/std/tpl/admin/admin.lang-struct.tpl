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
    LANG.title = {};
    LANG.form = {};
    var RE = {};
    {/literal}
    RE.context  = {#CONSTR__REGEX_CONTEXT};
    RE.topcat   = {#CONSTR__REGEX_TOPCAT};
    RE.subcat   = {#CONSTR__REGEX_SUBCAT};
    RE.entry    = {#CONSTR__REGEX_ENTRY};
    LANG.title.context = 'Context';
    LANG.title.topcat = 'Top category';
    LANG.title.subcat = 'Sub category';
    LANG.title.entry = 'Entry';
    LANG.title.comments = 'Comments';
    LANG.title.edit = 'Edit';
    LANG.title.add = 'Add';
    LANG.title.decription_for = 'Description for';
    LANG.form.context_id_invalid = 'Context not chosen.';
    LANG.form.context_name_invalid = 'Context name invalid.';
    LANG.form.topcat_id_invalid = 'Top category not chosen.';
    LANG.form.topcat_name_invalid = 'Top category name invalid.';
    LANG.form.subcat_id_invalid = 'Sub category not chosen.';
    LANG.form.subcat_name_invalid = 'Sub category name invalid.';
    LANG.form.entry_name_invalid = 'Entry name invalid.';
    var emptyopt = '-';
    //-->
    </script>
    <script type="text/javascript" src="{eval@load_js scramble_level="low"}"></script>
  </head>
  {subtemplate src="ii.body_start.stpl"}
    <div id="chead"><div id="chead_title">Language entry editor</div></div>
    <div class="box">
      <div class="boxh">Add new language path</div>
      <div class="boxc">
        <table id="at_holder">
          <tr>
            <td id="sel_action_wrap">
              <div class="frm_ftitle">Action:</div>
              <select id="sel_action" onchange="setupEdit();"></select>
            </td>
            <td id="sel_target_wrap">
              <div class="frm_ftitle">Object:</div>
              <select id="sel_target" onchange="setupEdit();"></select>
            </td>
          </tr>
        </table>
        <form id="frm_edit">
          <table><tr id="fields_holder"></tr><tr id="entry_holder"></tr></table>
          <div id="eg_comments"></div>
          <div id="availnamesview" class="note">
            <div class="noteh">Available names:</div>
            <div id="availnames" class="notec"></div>
          </div>
          <div id="commentview" class="note">
            <div id="cv_head" class="noteh"></div>
            <div id="cv_body" class="notec"></div>
          </div>
        </form>
        
        {* Actual form *}
        <form id="frm_save" method="post" action="_db.lang.php?a=add" onsubmit="return false;"></form>
      </div>
    </div>
    <div class="btnp">
      <div class="btnp_l">
        <a href="?">
          <img src="{#BTN_ROOT}{eval@make_button type="global" icon_l="arrows_left" label=$lang.btn.global__back}" class="hand" alt="" />
        </a>
      </div>
      <div class="btnp_r">
        <a href="javascript:save();">
          <img src="{#BTN_ROOT}{eval@make_button type="global" icon_r="plus" label=$lang.btn.global__save}" class="hand" alt="" />
        </a>
      </div>
    </div>
    <div class="box">
      <div class="boxh">Recently added/edited entries</div>
      <div class="boxc" id="log"><ul id=log"></ul></div>
    </div>
    <div class="btnp">
      <div class="btnp_r">
        <img src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__clear}" onclick="logRecent(null);" class="hand" alt="" />
      </div>
    </div>
  {subtemplate src="ii.body_end.stpl"}
</html>