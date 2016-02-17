var FUNC_OK = 1;
var FUNC_FAIL = -1;

var OL_FUNCS = new Array();
var OL_FUNCS_i = 0;
function add_onload_func(f)
{
  OL_FUNCS[OL_FUNCS_i++] = f;
}

window.onload = function()
{
  var a = document.getElementsByTagName('a');
  for(var i=0; i<a.length; i++)
  {
    if(typeof(a[i].onclick) == 'function') // Take care of existing onclick procedures.
      a[i].__onclick = a[i].onclick;
    a[i].onclick = function() { if(this.__onclick) this.__onclick(); this.blur(); };
  }
  
  // Run all onload functions:
  for(var i=0; i<OL_FUNCS.length; i++)
    OL_FUNCS[i]();
};

d=document;

function syserror(msg)
{
  alert('An unexpected javascript error occured, please contact the administrator.\nSystem said:\n' + (msg ? msg : null));
}