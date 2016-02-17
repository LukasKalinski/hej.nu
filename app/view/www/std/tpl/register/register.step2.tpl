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
      var routeAbort = '{eval@#ROUTE_ABORT}';
      var LANG__confirm__reg_abort = '{$LANG.js_txt.confirm__reg_abort}';
      var LANG__language_fail = '{$LANG.js_txt.form__language_not_valid}';
      var LANG__country_fail = '{$LANG.js_txt.form__country_not_valid}';
      var LANG__region_fail = '{$LANG.js_txt.form__region_not_valid}';
      var LANG__city_fail = '{$LANG.js_txt.form__city_not_valid}';
    </script>
    <script type="text/javascript" src="{eval@load_js}"></script>
    
    {subtemplate src="register/register.body_start.stpl" title=$LANG.txt.page_title__register_location step=2}
    <div class="info_box">
      {$LANG.txt.page_text__register_step2_desc req_mark="<span class=\"form_field_req\">*</span>"}
    </div>
    <br /><br />
    <form id="frm_location"
          method="POST"
          action="_route.php?r={#ROUTE_STEP_2_POST_HANDLER}"
          onsubmit="return reg_continue(this);"
          accept-charset="UTF-8">
      <table class="frm">
        <tr>
          <td class="frm">
            <div class="form_field_title">{$LANG.txt.form_title__language}: <span class="form_field_req">*</span></div>
            <select id="sel_language" name="language_id">
              <option value="0"># {$LANG.txt.select__choose_language}</option>
              {section id="i" src=$languages}
                <option value="{$languages[i].id}"
                        {if isset($language_id) and $language_id eq $languages[i].id} selected{/if}>{$languages[i].name|ucfirst}</option>
              {/section}
            </select>
          </td>
          <td class="frm">
            <div class="form_field_title">{$LANG.txt.form_title__country}: <span class="form_field_req">*</span></div>
            <select id="sel_country" name="country_id" onchange="refreshForm(this);">
              <option value="0"># {$LANG.txt.select__choose_country}</option>
              {section id="i" src=$countries}
                <option value="{$countries[i].id}"
                        {if isset($country_id) and $country_id eq $countries[i].id} selected{/if}>{$countries[i].name}</option>
              {/section}
            </select>
          </td>
          <td class="frm">
            <div class="form_field_title">{$LANG.txt.form_title__region}: <span class="form_field_req">*</span></div>
            <select id="sel_region" name="region_id" onchange="refreshForm(this);"{if !isset($region_id)} disabled{/if}>
              <option value="0"># {$LANG.txt.select__choose_region}</option>
              {if isset($regions) and isset($region_id)}
                {section id="i" src=$regions}
                  <option value="{$regions[i].id}"
                          {if $region_id eq $regions[i].id} selected{/if}>{$regions[i].name}</option>
                {/section}
              {/if}
            </select>
          </td>
        </tr>
      </table>
      <table class="frm">
        <tr>
          <td class="frm">
            <div class="form_field_title">{$LANG.txt.form_title__city}: <span class="form_field_req">*</span></div>
            <select id="sel_city" name="city_id"{if !isset($city_id)} disabled{/if}>
              <option value="0"># {$LANG.txt.select__choose_city}</option>
              {if isset($cities) and isset($city_id)}
                {section id="i" src=$cities}
                  <option value="{$cities[i].id}"
                          {if $city_id eq $cities[i].id} selected{/if}>{$cities[i].name}</option>
                {/section}
              {/if}
            </select>
          </td>
          <td class="frm" colspan="2">
            <div class="form_field_title">{$LANG.txt.form_title__district}:</div>
            <input type="text" name="district" class="inp_t" maxlength="30"{if isset($district)} value="{$district}"{/if} />
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