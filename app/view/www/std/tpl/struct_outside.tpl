<?xml version="1.0" encoding="{$lang.env.charset}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset={$lang.env.charset}" />
    <meta http-equiv="imagetoolbar" content="no" />
    <title>{$lang.env.cycom_short_url}</title>
    <link rel="stylesheet" type="text/css" href="{#CSS_ROOT}{eval@load_css import="out_base;forms"}" />
    <script type="text/javascript">
      var pwdCheckKey = '{$password_check_key}';
      var constr_username_regex = {#CONSTR__USERNAME_REGEX};
      var constr_password_regex = {#CONSTR__PASSWORD_REGEX};
      var LANG__usr_fail = '{$lang.js_txt.form__username_not_valid}';
      var LANG__pwd_fail = '{$lang.js_txt.form__password_not_valid}';
    </script>
    <script type="text/javascript" src="{eval@load_js}"></script>
  </head>
  <body>
    <div id="bg_illustration">
      <div id="content_island">
        {* Panel header *}
        <div id="content_outer_header">
          <div id="content_outer_title">
            {$lang.env.cycom_name}
          </div>
        </div>
        {* Content outer holder *}
        <div id="content_outer_wrapper">
          <div id="c_box_wrapper">
            {* Content inner holder: About *}
            <div id="c_box_about" class="c_box">
              <div class="content_inner_title_bg">
                <div class="content_inner_title_holder pagehead_title">
                  {$lang.txt.title_about}
                </div>
              </div>
              {$lang.txt.content_about}
              <img src="{#GFX_ROOT}dec001.gif" alt="" id="dec001" />
              <div class="content_inner_footer_bg">
                <div class="content_inner_footer_holder">
                  <img class="btn_global" src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__cookies icon_r="qmark"}" alt="" />
                </div>
              </div>
            </div>
            
            {* Content inner holder: Register *}
            <div id="c_box_reg" class="c_box">
              <div class="content_inner_title_bg">
                <div class="content_inner_title_holder pagehead_title">
                  {$lang.txt.title_register}
                </div>
              </div>
              {$lang.txt.content_register}
              <div class="content_inner_footer_bg">
                <div class="content_inner_footer_holder">
                  <a href="{eval@#DOC_ROOT}register/_route.php?r=1">
                    <img class="btn_global" src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__register}" alt="" />
                  </a>
                </div>
              </div>
            </div>
            
            {* Content inner holder: Login *}
            <div id="c_box_login" class="c_box">
              <div class="content_inner_title_bg">
                <div class="content_inner_title_holder pagehead_title">
                  {$lang.txt.title_login}
                </div>
              </div>
              <br />
              <form id="frm_login" method="POST" action="{eval@#DOC_ROOT}_sys/sys.login.php" onsubmit="return sys_login(this);">
                <input type="hidden" name="lang" value="{$lang.env.lang_id}" />
                <input type="hidden" name="browser" value="{#BROWSER}" />
                <input type="hidden" name="pwd_check" value="" />
                <table id="login_group" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="form_field_title login_group_left">{$lang.txt.inp_title_username}:</td>
                    <td class="form_field_title login_group_right">{$lang.txt.inp_title_password}:</td>
                  </tr>
                  <tr>
                    <td class="login_group_left">
                      <input class="inp_t login_group_field" type="text" name="username" class="login"{if !empty($username)} value="{$username}{/if}" />
                    </td>
                    <td class="login_group_right">
                      <input class="inp_t login_group_field" type="password" name="password" class="login" />
                    </td>
                  </tr>
                </table>
                <div class="content_inner_footer_bg">
                <div class="content_inner_footer_holder">
                    <img src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__forgot_password icon_r="qmark"}" alt="" />
                    <input type="image"
                           class="inp_i"
                           src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__login icon_r="login"}" />
                  </div>
                </div>
              </form>
            </div>
          </div>
        {* Panel footer *}
        </div>
        <img src="{#GFX_ROOT}/c_o_footer.gif" id="content_outer_footer" alt="" />
      </div>
    </div>
  </body>
</html>