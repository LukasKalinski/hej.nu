@{
@include "lib.keysniffer.js"
@}

if(parent == self)
  parent.secNavSet = function() { }; // eliminate menu function when in detached mode.
else
  add_onload_func(function(){d.getElementById('chead').onclick = function() {if(Key.isDown(KEY_CTRL)) detach();};});

function detach()
{
  window.open(window.location.href, '', 'width=553,scrollbars=yes,resizable=yes');
}
