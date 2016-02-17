/**
 * 
 * Usage:
 *  - When sending vars... correct: [var1, var2, var3], not correct: new Array(var1, var2, var3) (since new Array(<int>var) will mess things up).
 */
function __DEBUGGER()
{
  this.WIN = null;
  
  this.printFinal = function(eventType, msg, vars, doAppend)
  {
    if(this.WIN == null)
    {
      this.WIN = document.createElement('div');
      this.WIN.id = 'DEB';
      this.WIN.__reset = function() { this.innerHTML = '<b>Debugger Window</b><br /><br />'; };
      this.WIN.ondblclick = function() { this.__reset(); this.style.visibility = 'hidden'; };
      this.WIN.__reset();
      document.body.appendChild(this.WIN);
    }
    
    this.WIN.style.visibility = 'visible';
    
    if(vars != null)
    {
      var re;
      for(var i=0, ii=vars.length; i<ii; i++)
      {
        re = new RegExp('\\$'+i,'g');
        msg = msg.replace(re, '<span class="DEB_highlight">'+this.secureString(vars[i])+'</span>');
        msg = msg.replace(/\n/g, '<br />');
      }
    }
    
    if(doAppend !== true)
      this.WIN.__reset();
    
    // Note: frequent calling to innerHTML+= ... is slow, so don't worry.
    this.WIN.innerHTML += '<span class="DEB_prefix">#' + (eventType != null ? ' '+eventType+':' : '') + ' </span>' + msg + '<br />';
  };
  
  this.assert_true = function(b, msg, vars)
  {
    if(!b) this.printFinal('assert_true failed', msg, vars, true);
    return b;
  };
  
  this.secureString = function(s)
  {
    if(typeof(s) == 'string')
      return s.replace(/</g, '&#60;').replace(/>/g, '&#62;');
    else
      return s;
  };
  
  this.println = function(str, vars, doClear)
  {
    if(typeof(doClear) == 'undefined') doClear = false;
    var str = (typeof(str) == 'string' ? this.secureString(str).replace(/\n/g, '<br />') : str);
    this.printFinal(null, str, vars, !doClear);
  };
  
  this.err = function(str, vars) { this.println('@Error:\n'+str, vars, false); };
  
  return this;
}

var __DEB = new __DEBUGGER();
