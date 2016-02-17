<?xml version="1.0" encoding="{$lang.env.charset}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset={$lang.env.charset}" />
    <meta http-equiv="imagetoolbar" content="no" />
    <title>{$lang.env.cycom_short_url}</title>
    <link rel="stylesheet" type="text/css" href="{#CSS_ROOT}{eval@load_css import="ins_inner_base;forms"}" />
    <script type="text/javascript">
    	var GFX_ROOT = "{#GFX_ROOT}";
      var CONSTR__msg_maxlen = {#CONSTR_GST__MESSAGE_MAXLEN};
      var CONSTR__msg_minlen = {#CONSTR_GST__MESSAGE_MINLEN};
      var LANG__gb_empty = "{$lang.txt.user__no_gb_messages_found}";
      var LANG__alt_msgdel = "{$lang.txt.user_alt__delete_message}";
      var LANG__alt_msgrep = "{$lang.txt.user_alt__reply_on_message}";
      var LANG__alt_msgrepc = "{$lang.txt.user_alt__cancel_message_reply}";
      var LANG__alt_msghis = "{$lang.txt.user_alt__view_message_history}";
      var LANG__alt_msggot = "{$lang.txt.user_alt__message_goto_writer}";
      var LANG__msg_too_short = "{$lang.js_txt.form__gst_message_too_short}";
      var LANG__msg_too_long = "{$lang.js_txt.form__gst_message_too_long}";
      var LANG__confirm_msgcancel = "{$lang.js_txt.confirm__cancel_message_post}";
      var LANG__msg_reply_done = "{$lang.txt.user__msg_reply_done}";
      var LANG__msg_delete_done = "{$lang.txt.user__msg_delete_done}";
      var LANG__alt_msgdeleted = "{$lang.txt.user_alt__message_deleted}";
      var LANG__send_dbprocess_active = "{$lang.js_txt.alert__msg_send_impossible_dbprocess_running}";
      var LANG__del_dbprocess_active = "{$lang.js_txt.alert__msg_del_impossible_dbprocess_running}";
      var LANG__server_timeout = "{$lang.js_txt.alert__server_timeout}";
      var LANG__message_already_deleted = "{$lang.js_txt.alert__message_already_deleted}";
      var LANG__msg_store_fail = "{$lang.js_txt.sysmsg__msg_store_failed}";
      var LANG__msg_del_fail = "Failed to delete message.";
      var LANG__noreply_msg_deleted = "{$lang.js_txt.alert__reply_impossible_msg_deleted}";
      var LANG__gender_m = "{$lang.txt.gender_m_short}";
      var LANG__gender_f = "{$lang.txt.gender_f_short}";
      var ACTION_DEL = {#ACTION__DELETE};
      var ACTION_GET = {#ACTION__GET};
      var ACTION_STORE = {#ACTION__STORE};
      var SESSUSER_ID = '{$sessuser->get_uid()}';
      var USER_ID = '{$GET.userid}';
      var PAGE = {$page};
    </script>
    <script type="text/javascript" src="{eval@load_js scramble_level="medium"}"></script>
  </head>
  {subtemplate src="ii.body_start.stpl"}
    
    {* Confirmation window/container *}
    <table id="cw" class="hand" title="{$lang.txt.hide}" onclick="this.style.visibility='hidden';">
      <tr>
        <td id="cw_bg_l"></td>
        <td id="cw_bg_c"><div id="cw_msg"></div></td>
        <td id="cw_bg_r"></td>
      </tr>
    </table>
    
    {* Reply window *}
    {if $sessuser->get_uid() == $GET.userid}
      <table id="rw">
        <tr>
          <td id="rw_writearea">
            <div id="rw_statiwrap">
              <img id="rw_stati" src="{#GFX_ROOT}user/gb_rwstatindicator.gif" alt="" />
            </div>
            <form id="frm_rw" action="_db.php?a={#ACTION__STORE}" method="POST">
              <input type="hidden" name="user_id" />
              <input type="hidden" name="message_id" />
              <input type="hidden" name="thread_id" />
              <textarea id="rw_txta" name="message" onkeyup="RW_updateTypeStat(this);"></textarea>
            </form>
          </td>
          <td class="rw_btn">
            <div id="rw_btnarea">
              <a href="javascript:void(0);" onclick="RW_submit();">
                <img id="rw_btnsend"
                         src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__send icon_r="send"}"
                         class="hand"
                         alt="" />
              </a>
            </div>
          </td>
          <td class="rw_btn">
            <img id="rw_btnareaend" src="{#GFX_ROOT}user/gb_rwbtnbgend.gif" class="hand" alt="" />
          </td>
        </tr>
      </table>
    {/if}
    
    {* New message window *}
    <div id="chead"><div id="chead_title">{$lang.txt.user__users_guestbook username=$user.username gender=$user.gender}</div></div>
    <form id="frm_nm" action="_db.php?a={#ACTION__STORE}" method="POST">
      <input type="hidden" name="user_id" value="{$GET.userid}" />
      <div id="div_nmw" class="box">
        <div class="boxh">{$lang.txt.user_gst__new_gst_message}</div>
        <div class="boxc">
          <textarea id="txta_nmw" name="message"></textarea>
        </div>
      </div>
    </form>
    
    {* Top button panel *}
    <div class="btnp">
      <div class="btnp_l">
      </div>
      <div id="div_nmw1" class="btnp_r">
        <img src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__cancel icon_r="minus"}"
             class="hand"
             alt=""
             onclick="NMW_toggle();" />
        <a href="javascript:NMW_send();">
          <img src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__send icon_r="send"}"
               class="hand"
               alt="" />
        </a>
      </div>
      <div id="div_nmw0" class="btnp_r">
        <img src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__new_gst_message icon_r="msg_compose"}"
             class="hand"
             alt=""
             onclick="NMW_toggle();" />
      </div>
    </div>
    
    {* Main body *}
    <div id="toc_top"></div>
    <div id="messages"></div>
    <div id="toc_btm"></div>
    
    {* Bottom button panel *}
    <div class="btnp">
      <div class="btnp_l">
        <img id="pNav_prev"
             src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__previous_page icon_l="arrows_left"}"
             class="hand"
             alt=""
             onclick="pageNav(CURRENT_PAGE-1,2);" />
      </div>
      <div class="btnp_r">
        <img id="pNav_next"
             src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__next_page icon_r="arrows_right"}"
             class="hand"
             alt=""
             onclick="pageNav(CURRENT_PAGE+1,2);" />
      </div>
    </div>
  {subtemplate src="ii.body_end.stpl"}
</html>