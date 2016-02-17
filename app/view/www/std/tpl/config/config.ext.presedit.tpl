<?xml version="1.0" encoding="{$lang.env.charset}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset={$lang.env.charset}" />
    <meta http-equiv="imagetoolbar" content="no" />
    <title>{$lang.env.cycom_short_url}</title>
    <link rel="stylesheet" type="text/css" href="{#CSS_ROOT}{eval@load_css import="forms"}" />
    <script type="text/javascript">
    var GFX_ROOT = '{#GFX_ROOT}';
    var RE_USERNAME = {#CONSTR__USERNAME_REGEX};
    var CSS_RULE_PREFIX = '{#PE__CSS_RULE_PREFIX}';
    var LANG__err_elem_name_invalid = '{$lang.txt.config_pe__err_elem_name_invalid|securejsstr}';
    var LANG__err_missing_body_start = '{$lang.txt.config_pe__err_missing_body_start|securejsstr}';
    var LANG__err_too_many_body_start = '{$lang.txt.config_pe__err_too_many_body_start|securejsstr}';
    var LANG__err_id_already_used = '{$lang.txt.config_pe__err_id_already_used|securejsstr}';
    var LANG__err_expecting_end_tag = '{$lang.txt.config_pe__err_expecting_end_tag|securejsstr}';
    var LANG__err_unnecessary_end_tag = '{$lang.txt.config_pe__err_unnecessary_end_tag|securejsstr}';
    var LANG__err_empty_tag_no_end = '{$lang.txt.config_pe__err_empty_tag_no_end|securejsstr}';
    var LANG__err_tag_not_empty = '{$lang.txt.config_pe__err_tag_not_empty|securejsstr}';
    var LANG__err_req_attrs_missing = '{$lang.txt.config_pe__err_req_attrs_missing|securejsstr}';
    var LANG__err_attr_val_invalid = '{$lang.txt.config_pe__err_attr_val_invalid|securejsstr}';
    var LANG__err_unknown_attr = '{$lang.txt.config_pe__err_unknown_attr|securejsstr}';
    var LANG__err_err_attr_used = '{$lang.txt.config_pe__err_attr_used|securejsstr}';
    var LANG__err_tag_empty = '{$lang.txt.config_pe__err_tag_empty|securejsstr}';
    var LANG__err_errors_cant_save = '{$lang.txt.config_pe__err_errors_cant_save|securejsstr}';
    var LANG__link_to_exturl = '{$lang.txt.config_pe__desc_link_to_exturl|securejsstr}';
    var LANG__link_to_user = '{$lang.txt.config_pe__desc_link_to_user|securejsstr}';
    var LANG__expecting_body_end = '{$lang.txt.config_pe__err_expecting_body_end|securejsstr}';
    var LANG__pe_msg_default = '{$lang.txt.config_pe__desc_default|securejsstr}';
    var LANG__pe_msg_mouse_over_obj_id = '{$lang.txt.config_pe__desc_mouse_over_obj_id|securejsstr}';
    var LANG__pe_msg_mouse_over_obj_class = '{$lang.txt.config_pe__desc_mouse_over_obj_class|securejsstr}';
    var DBUSER_css_raw = '{$dbuser.pres_css_raw}';
    var CONSTR_RE_URL = /^{$PE->re("attr_href")}$/i;
    var CONSTR_RE_RESTR_URL = /{$PE->re("attr_href_restricted")}/i;
    var CSS_EMPTY = '{#USR__PRES_NO_CSS}';
    </script>
    <script type="text/javascript" src="{eval@load_js scramble_level="high"}"></script>
  </head>
  <body>
    {* Color Picker *}
    <div id="colpick">
      <img id="cp_body" src="{#GFX_ROOT}config/pe_colpick_body.gif" alt="" />
      <div id="cp_mapenv"
           onmousedown="{eval@getscram name="RGB"}.{eval@getscram name="startGraphicMove"}(event,'map');"
           onmousemove="{eval@getscram name="RGB"}.{eval@getscram name="scanGraphicMove"}(event,'map');"
           onmouseup="{eval@getscram name="RGB"}.{eval@getscram name="stopGraphicMove"}();"
           onmouseout="{eval@getscram name="RGB"}.{eval@getscram name="stopGraphicMove"}();">
        <img id="cur_h" src="{#GFX_ROOT}config/pe_colpick_xcur.gif" alt="" />
        <img id="cur_v" src="{#GFX_ROOT}config/pe_colpick_ycur.gif" alt="" />
        <img id="cp_gmap" src="{#GFX_COMMON_ROOT}config/pe_colpick_gmap.jpg" alt="" /> {* Gray scale map *}
        <img id="cp_map" src="{#GFX_COMMON_ROOT}config/pe_colpick_map.jpg" alt="" />
        <img id="cur_map" src="{#GFX_ROOT}config/pe_colpick_mcur.gif" alt="" />
        <div id="cp_mapinv"></div> {* This element has a transparent gif as background because IE wants it that way. *}
      </div>
      <div id="rgbPreview" onclick="{eval@getscram name="RGB"}.{eval@getscram name="copyHEX"}();"></div>
      <form id="frmColorMap">
        <input type="text" id="inp_H" name="H" class="inp_params"
               onkeyup="{eval@getscram name="RGB"}.{eval@getscram name="scanNumericChange"}(this);" maxlength="3" />
        <input type="text" id="inp_S" name="S" class="inp_params"
               onkeyup="{eval@getscram name="RGB"}.{eval@getscram name="scanNumericChange"}(this);" maxlength="3" />
        <input type="text" id="inp_V" name="V" class="inp_params"
               onkeyup="{eval@getscram name="RGB"}.{eval@getscram name="scanNumericChange"}(this);" maxlength="3" />
        <input type="text" id="inp_R" name="R" class="inp_params"
               onkeyup="{eval@getscram name="RGB"}.{eval@getscram name="scanNumericChange"}(this);" maxlength="3" />
        <input type="text" id="inp_G" name="G" class="inp_params"
               onkeyup="{eval@getscram name="RGB"}.{eval@getscram name="scanNumericChange"}(this);" maxlength="3" />
        <input type="text" id="inp_B" name="B" class="inp_params"
               onkeyup="{eval@getscram name="RGB"}.{eval@getscram name="scanNumericChange"}(this);" maxlength="3" />
        <input type="text" id="inp_HEX" name="HEX" class="inp_params"
               onkeyup="{eval@getscram name="RGB"}.{eval@getscram name="scanNumericChange"}(this);" maxlength="6" />
        <input type="hidden" id="inp_cHEX" name="cHEX" value="" />
      </form>
      <div id="slider_s"
           onmousemove="{eval@getscram name="RGB"}.{eval@getscram name="scanGraphicMove"}(event, 'saturation');" 
           onmousedown="{eval@getscram name="RGB"}.{eval@getscram name="startGraphicMove"}(event, 'saturation');" 
           onmouseup="{eval@getscram name="RGB"}.{eval@getscram name="stopGraphicMove"}();" 
           onmouseout="{eval@getscram name="RGB"}.{eval@getscram name="stopGraphicMove"}();">
        <img id="cur_s" src="{#GFX_ROOT}config/pe_colpick_scur.gif" alt="" />
        <div class="slider_inv"></div> {* Has a transparent gif as background because IE wants it that way. *}
      </div>
    </div>
    
    {* Tag Managment *}
    <div id="tagm">
      <div id="tagm_head"></div>
      <div id="tagm_body">
      
        {* Element Creation *}
        <div id="tagm_create">
          <div class="shead"><div class="shead_title">{$lang.txt.config_pe__tagm_title_create}</div></div>
          <div class="frm_ftitle">{$lang.txt.config_pe__tagm_create_elemname}:</div>
          <form id="frm_tagm_create">
            <input id="inp_tagm_name" type="text" name="tagm_name" class="inp_t" />
          </form>
          <img id="rad_class" src="" alt="" /><span id="rad_class_label">{$lang.txt.config_pe__tagm_create_elemtype_class}</span>
          <img id="rad_id" src="" alt="" /><span id="rad_id_label">{$lang.txt.config_pe__tagm_create_elemtype_id}</span>
          <div class="btnsp">
            <div class="btnsp_r">
              <img src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__cancel}" class="hand" alt=""
                   onclick="{eval@getscram name="tagm_go"}('tag_manager');" />
              <img src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__ok}" class="hand" alt=""
                   onclick="{eval@getscram name="cml_addElem"}();" />
            </div>
          </div>
        </div>
        
        {* Tag Manager *}
        <div id="tagm_manage">
          <div class="shead"><div class="shead_title">{$lang.txt.config_pe__tagm_title_manage}</div></div>
          <form>
            <div class="frm_ftitle">{$lang.txt.config_pe__tagm_title_choose_elem}:</div>
            <select id="sel_elem" onchange="{eval@getscram name="EH"}.{eval@getscram name="updateGUI"}(this.value);"></select>
          </form>
          <form id="frm_cssProps" onsubmit="return false;">
          
            {* TAGM Property group: "Background" - BGR *}
            <div id="div__pg_background" class="csspg">
              <div id="div__pg_background_head" class="csspg_t0" onclick="{eval@getscram name="tagm_togglepg"}(this);">
                {$lang.txt.config_pe__tp_grouptitle_background}
              </div>
              <div id="div__pg_background_body" class="csspg_b">
                <div id="div__p_background-color" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_backgroundcolor}:</div>
                  <div class="cssp_val">
                    #<input id="cssp_background-color" type="text" class="inp_t" maxlength="6"
                            onkeyup="{eval@getscram name="cml_update"}(this,'background-color');" />
                  </div>
                </div>
              </div>
            </div>
            
            {* TAGM Property group: "Foreground" - FGR *}
            <div id="div__pg_foreground" class="csspg">
              <div id="div__pg_foreground_head" class="csspg_t0" onclick="{eval@getscram name="tagm_togglepg"}(this);">
                {$lang.txt.config_pe__tp_grouptitle_foreground}
              </div>
              <div id="div__pg_foreground_body" class="csspg_b">
                <div id="div__p_color" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_color}:</div>
                  <div class="cssp_val">
                    #<input id="cssp_color" type="text" class="inp_t" maxlength="6" onkeyup="{eval@getscram name="cml_update"}(this,'color');" />
                  </div>
                </div>
              </div>
            </div>
            
            {* TAGM Property group: "Margins" - MRG *}
            <div id="div__pg_margins" class="csspg">
              <div id="div__pg_margins_head" class="csspg_t0" onclick="{eval@getscram name="tagm_togglepg"}(this);">
                {$lang.txt.config_pe__tp_grouptitle_margins}
              </div>
              <div id="div__pg_margins_body" class="csspg_b">
                <div id="div__p_margin-left" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_marginleft}:</div>
                  <div class="cssp_val">
                    <input id="cssp_margin-left" type="text" value="0" class="inp_t" maxlength="3"
                           onchange="{eval@getscram name="cml_update"}(this,'margin-left');" />
                  </div>
                </div>
                <div id="div__p_margin-right" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_marginright}:</div>
                  <div class="cssp_val">
                    <input id="cssp_margin-right" type="text" value="0" class="inp_t" maxlength="3"
                            onchange="{eval@getscram name="cml_update"}(this,'margin-right');" />
                  </div>
                </div>
                <div id="div__p_margin-top" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_margintop}:</div>
                  <div class="cssp_val">
                    <input id="cssp_margin-top" type="text" value="0" class="inp_t" maxlength="3"
                            onchange="{eval@getscram name="cml_update"}(this,'margin-top');" />
                  </div>
                </div>
                <div id="div__p_margin-bottom" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_marginbottom}:</div>
                  <div class="cssp_val">
                    <input id="cssp_margin-bottom" type="text" value="0" class="inp_t" maxlength="3"
                            onchange="{eval@getscram name="cml_update"}(this,'margin-bottom');" />
                  </div>
                </div>
              </div>
            </div>
            
            {* TAGM property group: "Layout/Appearence" - LTA *}
            <div id="div__pg_layout" class="csspg">
              <div id="div__pg_layout_head" class="csspg_t0" onclick="{eval@getscram name="tagm_togglepg"}(this);">
                {$lang.txt.config_pe__tp_grouptitle_layout}
              </div>
              <div id="div__pg_layout_body" class="csspg_b">
                <div id="div__p_display" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_display}:</div>
                  <div class="cssp_val">
                    <select id="cssp_display" onchange="{eval@getscram name="cml_update"}(this,'display');">
                      <option value="none">{$lang.txt.config_pe__css_display_none}</option>
                      <option value="inline">{$lang.txt.config_pe__css_display_inline}</option>
                      <option value="block">{$lang.txt.config_pe__css_display_block}</option>
                    </select>
                  </div>
                </div>
                <div id="div__p_position" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_position}:</div>
                  <div class="cssp_val">
                    <select id="cssp_position" onchange="{eval@getscram name="cml_update"}(this,'position');">
                      <option value="relative">{$lang.txt.config_pe__css_position_relative}</option>
                      <option value="absolute">{$lang.txt.config_pe__css_position_absolute}</option>
                    </select>
                  </div>
                </div>
                <div id="div__p_top" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_top}:</div>
                  <div class="cssp_val">
                    <input id="cssp_top" type="text" value="0" class="inp_t" maxlength="4" onchange="{eval@getscram name="cml_update"}(this,'top',1);" />
                  </div>
                </div>
                <div id="div__p_left" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_left}:</div>
                  <div class="cssp_val">
                    <input id="cssp_left" type="text" value="0" class="inp_t" maxlength="3" onchange="{eval@getscram name="cml_update"}(this,'left',1);" />
                  </div>
                </div>
                <div id="div__p_width" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_width}:</div>
                  <div class="cssp_val">
                    <input id="cssp_width" type="text" value="" class="inp_t" maxlength="3" onchange="{eval@getscram name="cml_update"}(this,'width');" />
                  </div>
                </div>
                <div id="div__p_height" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_height}:</div>
                  <div class="cssp_val">
                    <input id="cssp_height" type="text" value="" class="inp_t" maxlength="4" onchange="{eval@getscram name="cml_update"}(this,'height');" />
                  </div>
                </div>
                <div id="div__p_padding" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_padding}:</div>
                  <div class="cssp_val">
                    <input id="cssp_padding" type="text" value="0" class="inp_t" maxlength="2" onchange="{eval@getscram name="cml_update"}(this,'padding');" />
                  </div>
                </div>
                <div id="div__p_z-index" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_zindex}:</div>
                  <div class="cssp_val">
                    <input id="cssp_z-index" type="text" value="" class="inp_t" maxlength="3" onchange="{eval@getscram name="cml_update"}(this,'z-index');" />
                  </div>
                </div>
              </div>
            </div>
            
            {* TAGM property group: "Border" - BRD *}
            <div id="div__pg_border" class="csspg">
              <div id="div__pg_border_head" class="csspg_t0" onclick="{eval@getscram name="tagm_togglepg"}(this);">
                {$lang.txt.config_pe__tp_grouptitle_border}
              </div>
              <div id="div__pg_border_body" class="csspg_b">
                <div id="div__p_border-width" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_borderwidth}:</div>
                  <div class="cssp_val">
                    <input id="cssp_border-width" type="text" value="0" class="inp_t" maxlength="2"
                           onchange="{eval@getscram name="cml_update"}(this,'border-width');" />
                  </div>
                </div>
                <div id="div__p_border-style" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_borderstyle}:</div>
                  <div class="cssp_val">
                    <select id="cssp_border-style" onchange="{eval@getscram name="cml_update"}(this,'border-style');">
                      <option value="none">{$lang.txt.config_pe__css_borderstyle_none}</option>
                      <option value="dotted">{$lang.txt.config_pe__css_borderstyle_dotted}</option>
                      <option value="dashed">{$lang.txt.config_pe__css_borderstyle_dashed}</option>
                      <option value="solid">{$lang.txt.config_pe__css_borderstyle_solid}</option>
                      <option value="double">{$lang.txt.config_pe__css_borderstyle_double}</option>
                      <option value="groove">{$lang.txt.config_pe__css_borderstyle_groove}</option>
                      <option value="ridge">{$lang.txt.config_pe__css_borderstyle_ridge}</option>
                      <option value="inset">{$lang.txt.config_pe__css_borderstyle_inset}</option>
                      <option value="outset">{$lang.txt.config_pe__css_borderstyle_outset}</option>
                    </select>
                  </div>
                </div>
                <div id="div__p_border-color" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_bordercolor}:</div>
                  <div class="cssp_val">
                    #<input id="cssp_border-color" type="text" class="inp_t" maxlength="6" onkeyup="{eval@getscram name="cml_update"}(this,'border-color');" />
                  </div>
                </div>
              </div>
            </div>
            
            {* TAGM property group: "Text/Font" - TXT *}
            <div id="div__pg_font" class="csspg">
              <div id="div__pg_font_head" class="csspg_t0" onclick="{eval@getscram name="tagm_togglepg"}(this);">
                {$lang.txt.config_pe__tp_grouptitle_font}
              </div>
              <div id="div__pg_font_body" class="csspg_b">
                <div id="div__p_font-family" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_fontfamily}:</div>
                  <div class="cssp_val">
                    <select id="cssp_font-family" onchange="{eval@getscram name="cml_update"}(this,'font-family');">
                      <option value="Arial">Arial</option>
                      <option value="Verdana" selected>Verdana</option>
                    </select>
                  </div>
                </div>
                <div id="div__p_font-size" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_fontsize}:</div>
                  <div class="cssp_val">
                    <input id="cssp_font-size" type="text" value="10" class="inp_t" maxlength="2"
                           onchange="{eval@getscram name="cml_update"}(this,'font-size');" />
                  </div>
                </div>
                <div id="div__p_font-weight" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_fontweight}:</div>
                  <div class="cssp_val">
                    <select id="cssp_font-weight" onchange="{eval@getscram name="cml_update"}(this,'font-weight');">
                      <option value="normal">{$lang.txt.config_pe__css_fontweight_normal}</option>
                      <option value="bold">{$lang.txt.config_pe__css_fontweight_bold}</option>
                    </select>
                  </div>
                </div>
                <div id="div__p_font-style" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_fontstyle}:</div>
                  <div class="cssp_val">
                    <select id="cssp_font-style" onchange="{eval@getscram name="cml_update"}(this,'font-style');">
                      <option value="normal">{$lang.txt.config_pe__css_fontstyle_normal}</option>
                      <option value="italic">{$lang.txt.config_pe__css_fontstyle_italic}</option>
                    </select>
                  </div>
                </div>
                <div id="div__p_text-decoration" class="cssp">
                  <div class="cssp_nam">{$lang.txt.config_pe__css_textdecoration}:</div>
                  <div class="cssp_val">
                    <select id="cssp_text-decoration" onchange="{eval@getscram name="cml_update"}(this,'text-decoration');">
                      <option value="none">{$lang.txt.config_pe__css_textdecoration_none}</option>
                      <option value="underline">{$lang.txt.config_pe__css_textdecoration_underline}</option>
                      <option value="overline">{$lang.txt.config_pe__css_textdecoration_overline}</option>
                      <option value="line-through">{$lang.txt.config_pe__css_textdecoration_linethrough}</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </form>
          <div class="btnsp">
            <div class="btnsp_r">
              <img src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__create_element}"
               class="hand"
               alt=""
               onclick="{eval@getscram name="tagm_go"}('create_elem');" />
            </div>
          </div>
        </div>
      </div>
      <div id="tagm_foot"></div>
    </div>
    
    {* Editor - Presentation raw input *}
    <div id="editarea">
      <div id="ea_head"></div>
      <div id="ea_body">
        <form id="pedit">
          <textarea id="pe_data">{$dbuser.pres_raw}</textarea>
        </form>
        <div class="btnp">
          <div class="btnp_r">
            <img src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__save}" class="hand" alt=""
                 onclick="{eval@getscram name="requestSave"}();" />
          </div>
        </div>
      </div>
      <div id="ea_foot"></div>
    </div>
    
    {* Presentation Preview Area *}
    <div id="prevarea">
      <form id="pe_save" action="_db.php?a={#DBACTION__STORE_PRES}" method="POST">
        <input type="hidden" name="r_pres" value="" /> {* Raw presentation: Like it is in the textarea.*}
        <input type="hidden" name="r_css" value="" />  {* Raw CSS: CSS-property-string linked to a name. *}
        <input type="hidden" name="c_pres" value="" /> {* Compiled presentation: To display on the user's main page. *}
        <input type="hidden" name="c_css" value="" />  {* Compiled CSS: ready to put in a file and use together with compiled presentation. *}
      </form>
      <div id="pa_head"></div>
      <div id="pa_body">
        <div id="pe_msg">{$lang.txt.config_pe__desc_default}</div>
        <div id="pe_prev"><div style="padding:3px">{$lang.txt.config_pe__pres_loading}</div></div>
      </div>
      <div id="pa_foot"></div>
    </div>
  </body>
</html>