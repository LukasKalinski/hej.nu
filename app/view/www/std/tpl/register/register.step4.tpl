<?xml version="1.0" encoding="{$LANG.env.charset}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset={$LANG.env.charset}" />
    <meta http-equiv="imagetoolbar" content="no" />
    <title>{$LANG.env.cycom_short_url}</title>
    <link rel="stylesheet" type="text/css" href="{#CSS_ROOT}{eval@load_css import="out_base;forms"}" />
  </head>
  <body>
    {* Load scripts *}
    <script type="text/javascript">
      var routeAbort = "{eval@#ROUTE_ABORT}";
      var LANG__confirm__reg_abort = "{$LANG.js_txt.confirm__reg_abort}";
      var CONSTR__GENDER_REGEX = {#CONSTR__GENDER_REGEX};
      var CONSTR__USERNAME_REGEX = {#CONSTR__USERNAME_REGEX};
      var CONSTR__PASSWORD_REGEX = {#CONSTR__PASSWORD_REGEX};
      var LANG__dob_invalid = "{$LANG.js_txt.form__dob_not_valid}";
      var LANG__gender_not_chosen = "{$LANG.js_txt.form__gender_not_chosen}";
      var LANG__username_invalid = "{$LANG.js_txt.form__username_not_valid}";
      var LANG__password_invalid = "{$LANG.js_txt.form__password_not_valid}";
      var LANG__password_not_confirmed = "{$LANG.js_txt.form__password_not_confirmed}";
      var LANG__username_available = "{$LANG.js_txt.form__username_available}";
      var LANG__username_taken = "{$LANG.js_txt.form__username_taken}";
    </script>
    <script type="text/javascript" src="{eval@load_js include="lib.form.js|lib.date.js|modifier.strfill.js|lib.rdm.js"}"></script>
    {subtemplate src="register/register.body_start.stpl" title=$LANG.txt.page_title__account_setup step=4}
    <div class="info_box">
      {$LANG.txt.page_text__register_step4_desc}
    </div>
    <br /><br/>
    <form method="POST"
          action="_route.php?r={#ROUTE_STORE}"
          onsubmit="return reg_save(this);"
          accept-charset="UTF-8">
      <table class="frm">
        <tr>
          <td class="frm">
            <div class="frm_ftitle">{$LANG.txt.form_title__dob}: <span class="frm_freq">*</span></div>
            <select id="sel_dob_y" name="dob_year" />
              <option value="0"># {$LANG.txt.select__choose_dob_year}</option>
              {section id="i" src=$years}
                <option value="{$years[i]}"{if isset($dob_year) and $dob_year == $years[i]} selected{/if}>{$years[i]}</option>
              {/section}
            </select>
            <select id="sel_dob_m" name="dob_month" />
              <option value="0"># {$LANG.txt.select__choose_dob_month}</option>
              {foreach id="m" src=$LANG.assoc_list.months key="month_num" value="month_name"}
                <option value="{$month_num}"{if isset($dob_month) and $dob_month == $month_num} selected{/if}>{$month_name}</option>
              {/foreach}
            </select>
            <select id="sel_dob_d" name="dob_day" />
              <option value="0"># {$LANG.txt.select__choose_dob_day}</option>
              {section id="i" src=$days}
                <option value="{$days[i]}"{if isset($dob_day) and $dob_day == $days[i]} selected{/if}>{$days[i]|zerofill:2}</option>
              {/section}
            </select>
          </td>
          <td class="frm">
            <div class="frm_ftitle">{$LANG.txt.form_title__gender}: <span class="frm_freq">*</span></div>
            <select id="sel_gender" name="gender" />
              <option value="0"># {$LANG.txt.select__choose_gender}</option>
              <option value="F"{if isset($gender) and $gender == "F"} selected{/if}>{$LANG.txt.gender_f_long|ucfirst}</option>
              <option value="M"{if isset($gender) and $gender == "M"} selected{/if}>{$LANG.txt.gender_m_long|ucfirst}</option>
            </select>
          </td>
        </tr>
      </table>
      <table class="frm">
        <tr>
          <td class="frm">
            <div class="frm_ftitle">{$LANG.txt.form_title__username}: <span class="frm_freq">*</span></div>
            <input type="text"
                   id="inp_username"
                   class="inp_t"
                   name="username"
                   {if isset($username)}value="{$username}"{/if}
                   maxlength="{#CONSTR__USERNAME_MAXLEN}" />
          </td>
          <td class="frm">
            <div class="frm_ftitle">{$LANG.txt.form_title__password}: <span class="frm_freq">*</span></div>
            <input type="password"
                   class="inp_t inp_passw"
                   name="password"
                   maxlength="{#CONSTR__PASSWORD_MAXLEN}" />
          </td>
          <td class="frm">
            <div class="frm_ftitle">{$LANG.txt.form_title__password_repeat}: <span class="frm_freq">*</span></div>
            <input type="password" class="inp_t inp_passw" name="password_repeat" maxlength="{#CONSTR__PASSWORD_MAXLEN}" />
          </td>
        </tr>
      </table>
      
      <div id="footer">
        <div id="footer_carea_r">
          <a href="javascript:reg_abort();">
            <img src="{#BTN_ROOT}{eval@make_button type="global" label=$LANG.btn.global__abort_registration}" alt="" />
          </a>
          <a href="javascript:check_username();">
            <img src="{#BTN_ROOT}{eval@make_button type="global" label=$LANG.btn.global__check_username icon_r="qmark"}" alt="" />
          </a>
          <input type="image" class="inp_i" src="{#BTN_ROOT}{eval@make_button type="global" label=$LANG.btn.global__save}" />
        </div>
        <div id="footer_carea_l">
          <a href="javascript:history.go(-1);">
            <img src="{#BTN_ROOT}{eval@make_button type="global" label=$LANG.btn.global__back icon_l="arrows_left"}" alt="" />
          </a>
        </div>
      </div>
    </form>
    {subtemplate src="register/register.body_end.stpl"}
  </body>
</html>