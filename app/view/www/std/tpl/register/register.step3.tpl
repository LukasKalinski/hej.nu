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
      var routePassed = {if isset($first_name)}true{else}false{/if};
      var LANG = "{eval@#LANG}";
      var CONSTR__EMAIL_REGEX = {#CONSTR__EMAIL_REGEX};
      var CONSTR__PHONE_NUMBER_REGEX = {#CONSTR__PHONE_NUMBER_REGEX};
      var CONSTR__POST_CODE_REGEX = {#CONSTR__POST_CODE_REGEX};
      var CONSTR__FIRST_NAME_LEN = new Array({#CONSTR__FIRST_NAME_MINLEN},{#CONSTR__FIRST_NAME_MAXLEN});
      var CONSTR__LAST_NAME_LEN = new Array({#CONSTR__LAST_NAME_MINLEN},{#CONSTR__LAST_NAME_MAXLEN});
      var CONSTR__ADDRESS_LEN = new Array({#CONSTR__ADDRESS_MINLEN},{#CONSTR__ADDRESS_MAXLEN});
      var CONSTR__PHONE_NUMBER_LEN = new Array({#CONSTR__PHONE_NUMBER_MINLEN},{#CONSTR__PHONE_NUMBER_MAXLEN});
      var CONSTR__POST_CITY_LEN = new Array({#CONSTR__POST_CITY_MINLEN},{#CONSTR__POST_CITY_MAXLEN});
      var LANG__first_name_invalid = "{$LANG.js_txt.form__first_name_not_valid}";
      var LANG__last_name_invalid = "{$LANG.js_txt.form__last_name_not_valid}";
      var LANG__ss_num_invalid = "{$LANG.js_txt.form__ss_num_not_valid}";
      var LANG__email_invalid = "{$LANG.js_txt.form__email_not_valid}";
      var LANG__address_invalid = "{$LANG.js_txt.form__address_not_valid}";
      var LANG__phone_number_invalid = "{$LANG.js_txt.form__phone_number_not_valid}";
      var LANG__citizenship_not_chosen = "{$LANG.js_txt.form__citizenship_not_chosen}";
      var LANG__post_code_invalid = "{$LANG.js_txt.form__post_code_not_valid}";
      var LANG__post_city_invalid = "{$LANG.js_txt.form__post_city_not_valid}";
      var LANG__private_data_authenticity = "{$LANG.js_txt.confirm__reg_private_data_authenticity}";
    	var nextRoute = '{eval@#ROUTE_STEP_4}';
    </script>
    <script type="text/javascript" src="{eval@load_js include="lib.form.js"}"></script>
    
    {subtemplate src="register/register.body_start.stpl" title=$LANG.txt.page_title__private_section_setup step=3}
    <div class="info_box">
      {$LANG.txt.page_text__register_step3_desc}
    </div>
    <br /><br/>
    <form method="POST"
          action="_route.php?r={#ROUTE_STEP_3_POST_HANDLER}"
          onsubmit="return reg_continue(this);"
          accept-charset="UTF-8">
      <table class="frm">
        <tr>
          <td class="frm">
            <div class="frm_ftitle">{$LANG.txt.form_title__first_name}: <span class="form_field_req">*</span></div>
            <input type="text"
                   id="inp_fname"
                   class="inp_t"
                   name="first_name"
                   {if isset($first_name)}value="{$first_name}"{/if}
                   maxlength="{#CONSTR__FIRST_NAME_MAXLEN}" />
          </td>
          <td class="frm">
            <div class="frm_ftitle">{$LANG.txt.form_title__last_name}: <span class="form_field_req">*</span></div>
            <input type="text"
                   id="inp_lname"
                   class="inp_t"
                   name="last_name"
                   {if isset($last_name)}value="{$last_name}"{/if}
                   maxlength="{#CONSTR__LAST_NAME_MAXLEN}" />
          </td>
          <td class="frm">
            <div class="frm_ftitle">{$LANG.txt.form_title__ss_num}: <span class="form_field_req">*</span></div>
            <input type="text"
                   id="inp_ssn"
                   class="inp_t"
                   {if isset($ssn)}value="{$ssn}"{/if}
                   name="ssn" />
          </td>
        </tr>
      </table>
      <table class="frm">
        <tr>
          <td class="frm">
            <div class="frm_ftitle">{$LANG.txt.form_title__email}: <span class="form_field_req">*</span></div>
            <input type="text"
                   id="inp_email"
                   class="inp_t"
                   {if isset($email)}value="{$email}"{/if}
                   name="email" />
          </td>
          <td class="frm">
            <div class="frm_ftitle">{$LANG.txt.form_title__phone_number}: <span class="form_field_req">*</span></div>
            <input type="text"
                   id="inp_phonenumber"
                   class="inp_t"
                   name="phone_number"
                   {if isset($phone_number)}value="{$phone_number}"{/if}
                   maxlength="{#CONSTR__PHONE_NUMBER_MAXLEN}" />
          </td>
          <td class="frm">
            <div class="frm_ftitle">{$LANG.txt.form_title__citizenship}: <span class="form_field_req">*</span></div>
            <select id="sel_citizenship" name="citizenship_id" onchange="refreshForm(this);">
              <option value="0"># {$LANG.txt.select__choose_citizenship}</option>
              {section id="i" src=$countries}
                <option value="{$countries[i].id}"
                        {if isset($citizenship_id) and $citizenship_id eq $countries[i].id} selected{/if}>{$countries[i].name}</option>
              {/section}
            </select>
          </td>
        </tr>
      </table>
      <table class="frm">
        <tr>
          <td class="frm">
            <div class="frm_ftitle">{$LANG.txt.form_title__street_address}: <span class="form_field_req">*</span></div>
            <input type="text"
                   id="inp_address"
                   class="inp_t"
                   name="address"
                   {if isset($address)}value="{$address}"{/if}
                   maxlength="{#CONSTR__ADDRESS_MAXLEN}" />
          </td>
          <td class="frm">
            <div class="frm_ftitle">{$LANG.txt.form_title__post_code}: <span class="form_field_req">*</span></div>
            <input type="text"
                   id="inp_postcode"
                   class="inp_t"
                   name="post_code"
                   {if isset($post_code)}value="{$post_code}"{/if}
                   maxlength="{#CONSTR__POST_CODE_MAXLEN}" />
          </td>
          <td class="frm">
            <div class="frm_ftitle">{$LANG.txt.form_title__post_city}: <span class="form_field_req">*</span></div>
            <input type="text"
                   id="inp_postcity"
                   class="inp_t"
                   name="post_city"
                   {if isset($post_city)}value="{$post_city}"{/if}
                   maxlength="{#CONSTR__POST_CITY_MAXLEN}" />
          </td>
        </tr>
      </table>
      
      <div id="footer">
        <div id="footer_carea_r">
          <a href="javascript:reg_abort();">
            <img src="{#BTN_ROOT}{eval@make_button type="global" label=$LANG.btn.global__abort_registration}" alt="" />
          </a>
          <input type="image" class="inp_i" src="{#BTN_ROOT}{eval@make_button type="global" label=$LANG.btn.global__continue}" />
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