@{
@include "lib.browser.js"
@}

/**
 * not in use yet!
 * Returns size on success, -1 when height/width=auto and 0 if browser isn't gecko.
 * direction -> height,width
 */
function getElemContentOffset(elem, direction)
{
  function calc(e,p)
  {
    var result = parseInt(e.getPropertyValue(p));
    if(isNaN(result))
      result = 0;
    return result;
  }
  
  if(B.gecko)
  {
    var size = 0;
    var s = d.defaultView.getComputedStyle(elem, '');
    switch(direction)
    {
      case 'height':
        size += calc(s, 'padding-top');
        size += calc(s, 'padding-bottom');
        size += calc(s, 'border-top');
        size += calc(s, 'border-bottom');
        return size;
      case 'width':
        size += calc(s, 'padding-left');
        size += calc(s, 'padding-right');
        size += calc(s, 'border-left');
        size += calc(s, 'border-right');
        return size;
    }
  }
  else
  {
    return 0;
  }
}