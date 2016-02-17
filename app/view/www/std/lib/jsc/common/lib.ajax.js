@{
@include "lib.browser.js"
@include "function.urlvars2json.js"
@include "modifier.squote.js"
@}

// Create alias for IE:
if(typeof(XMLHttpRequest) == 'undefined')
{
  var XMLHttpRequest = function()
  {
  	var request = null;
  	try { request = new ActiveXObject('Msxml2.XMLHTTP'); }
  	catch(e1) { try { request = new ActiveXObject('Microsoft.XMLHTTP'); } catch(e2) {} }
  	return request;
  }
}

function AJAX_dumpDev(data)
{
  for(var i=0, ii=data.length; i<ii; i++)
  {
    tmpstr = 'AJAX dump '+i+':\n';
    for(var k in data[i])
      tmpstr += k + ': ' + data[i][k] + '\n';
    __DEB.println(tmpstr);
  }
}

function AJAX_throwError(err)
{
  var errHead = '### AJAX Error ###\n';
  for(var i=0, ii=err.length; i<ii; i++)
  {
    tmpstr = errHead;
    for(var k in err[i])
      tmpstr += k + ': ' + err[i][k] + '\n';
    __DEB.println(tmpstr);
  }
};

function AJAX_Response(id, data, _get, _post, vars)
{
  this.id = id;
  this.data = data;
  this._GET = _get;
  this._POST = _post;
  this.vars = vars;
  return this;
}

function AJAX_Handler()
{
  this.requests = {};
  
  this.abortAll = function()
  {
    for(var k in this.requests)
      this.requests[k].obj.abort();
  };
  
  /**
   * Performs an XMLHttpRequest for ID=id if ID=id has no process assigned to it, otherwise old process will be aborted and replaced.
   *
   * @param string id                                   # The id of the request; to avoid multiple requests for the same result.
   * @param string url                                  # The url to fetch/send data from/to.
   * @param string data                                 # Data to send (post or get)
   * @param string method                               # Method; post or get
   * @param function(string, AJAX_response) callback    # Function to call when ready (call_id, response_object).
   * @return void
   */
  this.request = function(id, url, url_vars, callback, additional_vars)
  {
    // Abort if unfinished requests with ID=id found:
    if(this.requests[id] != null && this.requests[id].readyState != 0 && this.requests[id].readyState != 4)
    {
      syserror('AJAX process already running [id='+id+']');
      return;
    }
    
    this.requests[id] = new XMLHttpRequest();
    var r = this.requests[id]; // Shortcut
    
    var method, getvars, postvars;
    
    // Check if we have any post-vars; import them and set method to POST if that is the case.
    if(url_vars && url_vars.post)
    {
      method = 'POST';
      postvars = urlvars2json(url_vars.post);
    }
    
    // Check if we have any get-vars; import them and set method to get IF it isn't set to POST yet.
    if(url_vars && url_vars.get)
    {
      if(method == null)
        method = 'GET';
      getvars = urlvars2json(url_vars.get);
      url = url + '?' + url_vars.get;
    }
    
    //window.open(url); // DEBUG
    
    // Run depending on method:
    switch(method)
    {
      case 'POST':
        r.open(method, url, true);
        r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        r.send(url_vars.post);
        break;
      case 'GET':
        r.open(method, url);
        r.send(null);
        break;
    }
    
    /**
     * Note:
     * readyState cases:
     * 0=uninitialised, 1=loading, 2=loaded, 3=interactive, 4=completed
     */
    r.onreadystatechange = function()
    {
      if(r.readyState == 4)
      {
        if(r.status == 200)
        {
          if(r.getResponseHeader('content-type').substr(0,10) == 'text/plain')
          {
            var data = r.responseText;
            var param = null;
            if((/^([a-z_][a-z0-9_]*)@(.*)/).test(data))
            {
              param = RegExp.$1;
              data = RegExp.$2;
            }
            
            // Try to evaluate data; exit on failure:
            try { eval('data=' + data + ';'); }
            catch(e0) { callback(null); syserror('AJAX data eval failed:\n'+data); return; }
            
            // Exit if we have any external errors ('err@' was found in the beggining of the result string):
            if(param == 'err')
            {
              callback(null);
              AJAX_throwError(data);
              return;
            }
            
            // Dump data for dev purposes ('dev@' was found in the beggining of the result string):
            if(param == 'dev')
            {
              AJAX_dumpDev(data);
              callback(null);
            }
            // Normal case; callback response:
            else
            {
              callback(new AJAX_Response(id, data, getvars, postvars, additional_vars));
            }
          }
          else
          {
            __DEB.err('AJAX lib doesn\'t support content-type\'s other than text/plain; $0 is invalid.\nData:\n$1',
                      [r.getResponseHeader('content-type'),r.responseText]);
          }
        }
        else if(r.status != 0)
        {
          __DEB.err('AJAX http access failed for id=$0', [r.id]);
        }
      }
    };
  };
  
  return this;
}
var AJAX = new AJAX_Handler();