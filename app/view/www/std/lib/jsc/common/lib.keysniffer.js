@{
@include "lib.event.js"
@}

var KEY_SHIFT = 16, KEY_CTRL = 17, KEY_ALT = 18, KEY_SPACE = 32, KEY_F5 = 116;
function KeySniffer()
{
  this.keys = new Array();
  
  this.setKeyDown = function(k)
  {
    this.keys[k] = true;
  };
  
  this.setKeyUp = function(k)
  {
    this.keys[k] = false;
  };
  
  this.isDown = function(k)
  {
    return (typeof(this.keys[k]) != 'undefined' && this.keys[k] === true);
  };
}
var Key = new KeySniffer();

document.onkeydown = function(e)
{
  var Ev = new Event(e);
  Key.setKeyDown(Ev.ref.keyCode);
};
document.onkeyup = function(e)
{
  var Ev = new Event(e);
  Key.setKeyUp(Ev.ref.keyCode);
};
window.onblur = function(e)
{
  for(var k in Key.keys)
    Key.keys[k] = false;
};