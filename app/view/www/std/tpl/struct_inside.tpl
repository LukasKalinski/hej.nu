<?xml version="1.0" encoding="{$lang.env.charset}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset={$lang.env.charset}" />
    <meta http-equiv="imagetoolbar" content="no" />
    <title>{$lang.env.cycom_short_url}</title>
    <link rel="stylesheet" type="text/css" href="{#CSS_ROOT}{eval@load_css}" />
    <script type="text/javascript" src="{eval@load_js scramble_level="high"}"></script>
    {* Import env for all menu button types: *}
    {eval@make_button import_env=true type="nav_primary_tc_0"}
    {eval@make_button import_env=true type="nav_primary_sc"}
    <script type="text/javascript">
      var GFX_ROOT = '{#GFX_ROOT}';
      var BTN_ROOT = '{#BTN_ROOT}';
      var IFR_MAIN = null;
      var ifr_main_name = '{$lang.env.cycom_namespace}main';
      var cthmid = '{#THEME}';
      var tcbtn_staticsx = '{$cte.splugin.make_button.nav_primary_tc_0_staticsizex}';
      var scbtn_staticsx = '{$cte.splugin.make_button.nav_primary_sc_staticsizex}';
      
      {* Fetch static button sizes for current theme: *}
      {literal}var re_getsx = new RegExp('.*'+cthmid+':([0-9]{1,2}).*', '');{/literal}
      
      tcbtn_staticsx = tcbtn_staticsx.replace(re_getsx, '$1');
      scbtn_staticsx = scbtn_staticsx.replace(re_getsx, '$1');
      
      {* Home *}
      {eval@getscram name="menu"}[1] = new {eval@getscram name="btn_grp"}('/home/home.news.php',
                            '{eval@make_button type="nav_primary_tc_0" ext=no label=$lang.btn.nav_primary_tc__home}',
                            '{eval@make_button type="nav_primary_tc_1" ext=no label=$lang.btn.nav_primary_tc__home}',
                            {$cte.splugin.make_button.lasttextsizex});
      
      {* Users place *}
      {eval@getscram name="menu"}[2] = new {eval@getscram name="btn_grp"}('/user/user.main.php?userid={$sessuser->get_uid()}',
                            '{eval@make_button type="nav_primary_tc_0" ext=no label=$lang.btn.nav_primary_tc__users_place}',
                            '{eval@make_button type="nav_primary_tc_1" ext=no label=$lang.btn.nav_primary_tc__users_place}',
                            {$cte.splugin.make_button.lasttextsizex});
      
      {* Search *}
      {eval@getscram name="menu"}[3] = new {eval@getscram name="btn_grp"}('',
                            '{eval@make_button type="nav_primary_tc_0" ext=no label=$lang.btn.nav_primary_tc__search}',
                            '{eval@make_button type="nav_primary_tc_1" ext=no label=$lang.btn.nav_primary_tc__search}',
                            {$cte.splugin.make_button.lasttextsizex});
      
      {* Communicate *}
      {eval@getscram name="menu"}[4] = new {eval@getscram name="btn_grp"}('',
                            '{eval@make_button type="nav_primary_tc_0" ext=no label=$lang.btn.nav_primary_tc__communicate}',
                            '{eval@make_button type="nav_primary_tc_1" ext=no label=$lang.btn.nav_primary_tc__communicate}',
                            {$cte.splugin.make_button.lasttextsizex});
      
      {* Settings *}
      {eval@getscram name="menu"}[5] = new {eval@getscram name="btn_grp"}('',
                            '{eval@make_button type="nav_primary_tc_0" ext=no label=$lang.btn.nav_primary_tc__settings}',
                            '{eval@make_button type="nav_primary_tc_1" ext=no label=$lang.btn.nav_primary_tc__settings}',
                            {$cte.splugin.make_button.lasttextsizex});
      
      {* Help *}
      {eval@getscram name="menu"}[6] = new {eval@getscram name="btn_grp"}('',
                            '{eval@make_button type="nav_primary_tc_0" ext=no label=$lang.btn.nav_primary_tc__help}',
                            '{eval@make_button type="nav_primary_tc_1" ext=no label=$lang.btn.nav_primary_tc__help}',
                            {$cte.splugin.make_button.lasttextsizex});
      
      {* Log out *}
      {eval@getscram name="menu"}[7] = new {eval@getscram name="btn_grp"}('exituser',
                            '{eval@make_button type="nav_primary_tc_0" ext=no label=$lang.btn.nav_primary_tc__quit}',
                            '{eval@make_button type="nav_primary_tc_1" ext=no label=$lang.btn.nav_primary_tc__quit}',
                            {$cte.splugin.make_button.lasttextsizex});
      
      {*/* SC (Sub Category menu) Loading, must appear in _THIS_ order. */*}
      with({eval@getscram name="menu"}[1])
      {
        {eval@getscram name="addscbtn"}('/home/home.news.php',
                                        '{eval@make_button type="nav_primary_sc" ext=no label=$lang.btn.nav_primary_sc__start_news}',
                                        {$cte.splugin.make_button.lasttextsizex});
        {eval@getscram name="addscbtn"}('',
                                        '{eval@make_button type="nav_primary_sc" ext=no label=$lang.btn.nav_primary_sc__start_history}',
                                        {$cte.splugin.make_button.lasttextsizex});
        {eval@getscram name="addscbtn"}('',
                                        '{eval@make_button type="nav_primary_sc" ext=no label=$lang.btn.nav_primary_sc__start_faq}',
                                        {$cte.splugin.make_button.lasttextsizex});
      }
      with({eval@getscram name="menu"}[2])
      {
        {eval@getscram name="addscbtn"}('/user/user.main.php?userid={$sessuser->get_uid()}',
                                        '{eval@make_button type="nav_primary_sc" ext=no label=$lang.btn.nav_primary_sc__user_main}',
                                        {$cte.splugin.make_button.lasttextsizex});
        {eval@getscram name="addscbtn"}('/user/user.guestbook.php?userid={$sessuser->get_uid()}',
                                        '{eval@make_button type="nav_primary_sc" ext=no label=$lang.btn.nav_primary_sc__user_guestbook}',
                                        {$cte.splugin.make_button.lasttextsizex});
        {eval@getscram name="addscbtn"}('',
                                        '{eval@make_button type="nav_primary_sc" ext=no label=$lang.btn.nav_primary_sc__user_diary}',
                                        {$cte.splugin.make_button.lasttextsizex});
      }
      with({eval@getscram name="menu"}[5])
      {
        {eval@getscram name="addscbtn"}('/config/config.public.php',
                                        '{eval@make_button type="nav_primary_sc" ext=no label=$lang.btn.nav_primary_sc__settings_public}',
                                        {$cte.splugin.make_button.lasttextsizex});
        {eval@getscram name="addscbtn"}('/config/config.photo.php',
                                        '{eval@make_button type="nav_primary_sc" ext=no label=$lang.btn.nav_primary_sc__settings_photo}',
                                        {$cte.splugin.make_button.lasttextsizex});
      }
      with({eval@getscram name="menu"}[7])
      {
        {eval@getscram name="addscbtn"}('exituser',
                                        '{eval@make_button type="nav_primary_sc" ext=no label=$lang.btn.nav_primary_sc__quit_logout_exit}',
                                        {$cte.splugin.make_button.lasttextsizex});
        {eval@getscram name="addscbtn"}('swapuser',
                                        '{eval@make_button type="nav_primary_sc" ext=no label=$lang.btn.nav_primary_sc__quit_logout_login}',
                                        {$cte.splugin.make_button.lasttextsizex});
      }
      
      {* Secondary navigation; the one directly above main-frame *}
      {eval@getscram name="secnav"}['home'] = new {eval@getscram name="secnav_grp"}('home');
      with({eval@getscram name="secnav"}['home'])
      {
        {eval@getscram name="addBtn"}('news',
                                      '/home/home.news.php',
                                      '{eval@make_button type="nav_secondary_0" ext=no label=$lang.btn.nav_secondary__news}',
                                      '{eval@make_button type="nav_secondary_1" ext=no label=$lang.btn.nav_secondary__news}');
        {eval@getscram name="addBtn"}('',
                                      '',
                                      '{eval@make_button type="nav_secondary_0" ext=no label=$lang.btn.nav_secondary__faq}',
                                      '{eval@make_button type="nav_secondary_1" ext=no label=$lang.btn.nav_secondary__faq}');
        {eval@getscram name="addBtn"}('',
                                      '',
                                      '{eval@make_button type="nav_secondary_0" ext=no label=$lang.btn.nav_secondary__history}',
                                      '{eval@make_button type="nav_secondary_1" ext=no label=$lang.btn.nav_secondary__history}');
      }
      {eval@getscram name="secnav"}['user'] = new {eval@getscram name="secnav_grp"}('user');
      with({eval@getscram name="secnav"}['user'])
      {
        {eval@getscram name="addBtn"}('main',
                                      '/user/user.main.php?userid={$sessuser->get_uid()}',
                                      '{eval@make_button type="nav_secondary_0" ext=no label=$lang.btn.nav_secondary__user_presentation}',
                                      '{eval@make_button type="nav_secondary_1" ext=no label=$lang.btn.nav_secondary__user_presentation}');
        {eval@getscram name="addBtn"}('guestbook',
                                      '/user/user.guestbook.php?userid={$sessuser->get_uid()}',
                                      '{eval@make_button type="nav_secondary_0" ext=no label=$lang.btn.nav_secondary__user_guestbook}',
                                      '{eval@make_button type="nav_secondary_1" ext=no label=$lang.btn.nav_secondary__user_guestbook}');
      }
      {eval@getscram name="secnav"}['config'] = new {eval@getscram name="secnav_grp"}('config');
      with({eval@getscram name="secnav"}['config'])
      {
        {eval@getscram name="addBtn"}('public',
                                      '/config/config.public.php',
                                      '{eval@make_button type="nav_secondary_0" ext=no label=$lang.btn.nav_secondary__settings_public}',
                                      '{eval@make_button type="nav_secondary_1" ext=no label=$lang.btn.nav_secondary__settings_public}');
        {eval@getscram name="addBtn"}('photo',
                                      '/config/config.photo.php',
                                      '{eval@make_button type="nav_secondary_0" ext=no label=$lang.btn.nav_secondary__settings_photo}',
                                      '{eval@make_button type="nav_secondary_1" ext=no label=$lang.btn.nav_secondary__settings_photo}');
      }
      
      {* Load admin menus when admin: *}
      {if $sessuser->is_admin()}
        {eval@getscram name="menu"}[0] = new {eval@getscram name="btn_grp"}('',
                            '{eval@make_button type="nav_primary_tc_0" ext=no label=$lang.btn.nav_primary_sc__admin}',
                            '{eval@make_button type="nav_primary_tc_1" ext=no label=$lang.btn.nav_primary_sc__admin}',
                            {$cte.splugin.make_button.lasttextsizex});
        
        {eval@getscram name="secnav"}['admin'] = new {eval@getscram name="secnav_grp"}('admin');
        {if $sessuser->has_permission("LANG_STRUCT") or $sessuser->has_permission("LANG_DATA")}
          {eval@getscram name="menu"}[0].{eval@getscram name="addscbtn"}('/admin/admin.lang.php',
                                                '{eval@make_button type="nav_primary_sc" ext=no label=$lang.btn.nav_primary_sc__admin_lang}',
                                                {$cte.splugin.make_button.lasttextsizex});
          {eval@getscram name="secnav"}['admin'].{eval@getscram name="addBtn"}('lang',
                                                '/admin/admin.lang.php',
                                                '{eval@make_button type="nav_secondary_0" ext=no label=$lang.btn.nav_secondary__admin_lang}',
                                                '{eval@make_button type="nav_secondary_1" ext=no label=$lang.btn.nav_secondary__admin_lang}');
        {/if}
      {/if}
    </script>
  </head>
  <body>
  {if $sessuser->is_admin()}
    <map id="adm_statbar_map" name="adm_statbar_map">
      <area shape="rect" coords="369,1,393,16" href="javascript:alert('hej');" alt="" />
    </map>
    <div id="astatbar">
      <img id="abar_bg" src="{#GFX_ROOT}struct_i/adm_statbar.gif" alt="" usemap="#adm_statbar_map" />
      <div id="abar_msg">Admin bar!</div>
    </div>
  {/if}
    {*** Header section ***}
    <img id="h_ban"
         src="{#GFX_ROOT}struct_i/h_bg.jpg"
         alt="" />
    <img id="h_pnl_L"
         src="{#GFX_ROOT}struct_i/h_pnl_L.gif"
         alt="" />
    <div id="h_mwrapper">
      <div id="h_menu">
        <img id="h_mpnl" src="{#GFX_ROOT}struct_i/h_mpnl.gif" alt="" />
        <div id="h_tc_list"></div>
        <div id="h_sc_list"></div>
      </div>
    </div>
    <div id="pnl_sicn">
      <img src="{#GFX_ROOT}struct_i/sicn_rel{if $new_rel > 0}1{else}0{/if}.gif" alt="" title="{$lang.txt.struct_i_alt__n_new_rel n=$new_rel}" />
      <img src="{#GFX_ROOT}struct_i/sicn_gst{if $new_gst > 0}1{else}0{/if}.gif" alt="" title="{$lang.txt.struct_i_alt__n_new_gst n=$new_gst}" />
      <img src="{#GFX_ROOT}struct_i/sicn_mil{if $new_mil > 0}1{else}0{/if}.gif" alt="" title="{$lang.txt.struct_i_alt__n_new_mil n=$new_mil}" />
      <img src="{#GFX_ROOT}struct_i/sicn_frm{if $new_frm > 0}1{else}0{/if}.gif" alt="" title="{$lang.txt.struct_i_alt__n_new_frm n=$new_frm}" />
    </div>
    <img id="h_pnl_R"
         src="{#GFX_ROOT}struct_i/h_pnl_R.gif"
         alt="" />
    <div id="h_online_num" class="pnlban">? online</div>
    
    {*** Content section ***}
    <img id="c_header"
         src="{#GFX_ROOT}struct_i/c_header.gif"
         alt="" />
    <div id="secnav"></div>
    <div id="c_wrapper">
      <iframe id="ifr_main"
              name="{$lang.env.cycom_namespace}main"
              frameborder="0"
              scrolling="auto"
              src="/home/home.news.php"
              onload="if(!{eval@getscram name="B"}.{eval@getscram name="ie5"})this.style.visibility='visible';
                      setTimeout('{eval@getscram name="toggleHeaderMenu"}(false)',600);"></iframe>
      <div id="div_right"></div>
    </div>
    <img id="c_footer"
         src="{#GFX_ROOT}struct_i/c_footer.gif"
         alt="" />
    
    {*** Footer section ***}
    <img id="f_pnl" src="{#GFX_ROOT}struct_i/f_pnl.gif" alt="" usemap="#panel_map" />
    <map id="panel_map" name="panel_map">
      <area shape="rect"
            coords="12,1,34,22"
            href="javascript:alert('[1] This should trigger a quick-help window...');"
            alt="" />
      <area shape="rect"
            coords="371,1,396,22"
            href="javascript:alert('[2] This should trigger a quick-help window...');"
            alt="" />
    </map>
    
    {* <form> is required since IE suffer from some illness and behaves weird without it. *}
    <form onsubmit="return false;"><input type="text" id="f_cmd" /></form>
    
    <div id="f_stat">{$lang.txt.logged_in_as_name name=$username}</div>
    <img src="{#GFX_ROOT}dec002.gif" id="dec002" alt="" />
  </body>
</html>