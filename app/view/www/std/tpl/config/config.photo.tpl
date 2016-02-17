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
    <div id="chead"><div id="chead_title">{$lang.txt.config__title_photo}</div></div>
    <table class="cols2">
      <tr class="cols2">
        <td class="col2-1">
          <div class="box">
            <div class="boxhead">Last photo upload</div>
            <div id="curphoto_stat" class="boxcontent">
              <div id="curphoto" class="uphotoM"></div>
              <h6>Status: </h6>Validated<br />
              <h6>Date: </h6>2006-01-28<br />
              <h6>Validated in: </h6>5h<br />
            </div>
          </div>
        </td>
        <td class="col2-2">
          <div class="box">
            <div class="boxhead">Current picture</div>
            <div id="curphoto_stat" class="boxcontent">
              <div id="curphoto" class="uphotoM"></div>
            </div>
          </div>
        </td>
      </tr>
    </table>
    <div class="btnp">
      <div class="btnp_r">
        <a href="javascript:void(0);">
          <img src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__save}" class="hand" alt="" />
        </a>
      </div>
    </div>
    <div class="box">
      <div class="boxh">Upload new photo</div>
      <div class="boxc">
        <form action="" type="post">
          <input type="hidden" id="" value="" />
          <div class="frm_ftitle">{$lang.txt.form_title__photo}:</div>
          <input id="inp_photo" type="file" class="inp_f" size="50" onchange="alert('hej');" />
        </form>
      </div>
    </div>
    <div class="btnp">
      <div class="btnp_r">
        <a href="javascript:void(0);">
          <img src="{#BTN_ROOT}{eval@make_button type="global" label=$lang.btn.global__upload}" class="hand" alt="" />
        </a>
      </div>
    </div>
  {subtemplate src="ii.body_end.stpl"}
</html>