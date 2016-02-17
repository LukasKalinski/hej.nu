function css_setPos(oId,x,y)
{
  var r = document.getElementById(oId).style;
  if(x != null) r.left = x + 'px';
  if(y != null) r.top = y + 'px';
}