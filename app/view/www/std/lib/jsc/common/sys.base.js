function frameExist(f)
{
  if(typeof(f) != 'undefined') return true;
  else return false;
}
function frameLoaded(f)
{
  if(frameExist(f) && typeof(f.__FL) != 'undefined') return true;
  else return false;
}
if(b.ie)
{
  document.onmousedown=function()
  {
    for(var i=0; i<document.links.length; i++) document.links[i].onclick=document.links[i].blur;
  }
}
//document.oncontextmenu = function() { return false; } // Disable context-menu