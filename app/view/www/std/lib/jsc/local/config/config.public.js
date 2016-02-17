@{
scramble_skip_name "openPresEdit"
@}

function openPresEdit()
{
  var h = window.screen.availHeight-100;
  var l = (window.screen.availWidth/2)-393;
  w = window.open("/config/config.ext.presedit.php","","width=794,height="+h+",scrollbars=yes,resizable=yes,left="+l+",top=20");
  w.focus();
}

add_onload_func(function(){parent.secNavSet('config','public');});