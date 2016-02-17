@{
@include "lib.browser.js"
@include "function.json2array.js"
@include "function.urlvars2json.js"
@include "modifier.squote.js"
@}

/**********************************************************\
 * Remote Data Management library
\**********************************************************/

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

function AJAX_Response(id, data, _get, _post)
{
  this.id = id;
  this.data = data;
  this._GET = _get;
  this._POST = _post;
  return this;
}

var AJAX_RETURN_JSON = 1;
var AJAX_RETURN_PLAIN = 2;

function AJAX_Handler()
{
  this.requests = {};
  
  this.abortAll = function()
  {
    for(var k in this.requests)
      this.requests[k].obj.abort();
  };
  
  /**
   * @desc Performs an XMLHttpRequest for ID=id if ID=id has no process assigned to it, otherwise old process will be aborted and replaced.
   * @param string id                                   # The id of the request; to avoid multiple requests for the same result.
   * @param string url                                  # The url to fetch/send data from/to.
   * @param string data                                 # Data to send (post or get)
   * @param string method                               # Method; post or get
   * @param function(string, AJAX_response) callback    # Function to call when ready (call_id, response_object).
   * @return void
   */
  this.request = function(id, url, url_vars, callback, returnAs)
  {
    // Abort if unfinished requests with ID=id found:
    if(this.requests[id] != null && this.requests[id].readyState != 0 && this.requests[id].readyState != 4)
      this.requests[id].abort();
    
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
    
//    window.open(url + '?' + dataVars); // DEBUG
    
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
            var rawData = r.responseText;
            var param = null;
            if((index = rawData.indexOf('@')) != -1)
            {
              param = rawData.substr(0, index);
              rawData = rawData.substr(index+1);
            }
            
            // Check if we have any errors and quit if true:
            if(param == 'err')
            {
              AJAX_throwError(json2array(rawData));
              callback(null);
            }
            
            switch(returnAs)
            {
              case AJAX_RETURN_JSON:
                data = json2array(rawData);
                break;
              case AJAX_RETURN_PLAIN:
                try { eval('data=' + rawData + ';'); }
                catch(e0) { syserror('AJAX data eval failed; '+rawData); }
                break;
              default:
                syserror('AJAX unknown return');
                return;
            }
            
            if(param == 'dev') // Dev display of data.
              AJAX_dumpDev(data);
            else
              callback(new AJAX_Response(id, data, getvars, postvars));
          }
          else
          {
            __DEB.err('AJAX lib doesn\'t support content-type\'s other than text/plain; $0 is invalid.', [r.getResponseHeader('content-type')]);
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

/*
function __callback(resp)
{
  var startDate = new Date();
  var d = resp.data;
  for(var i=0, ii=d.length; i<ii; i++)
  {
    for(var k in d[i])
      __DEB.println(k+': '+d[i][k]);
  }
//  alert(((new Date()).getTime()-startDate.getTime())/1000);
}

var tester = function()
{
  var request = AJAX.request('test', '/_lib/rdm/rdm.gst.php', 'action=msgget&korv=hej', __callback, 'GET');
  
//  request.open('GET', '/test.xml.php?action=msgget');
//  request.send(null);
};
add_onload_func(tester);
*/














// ## old .........

var RDM__SCRIPT_ROOT = '/_lib/rdm/';
var RDM__script_element = null;
var RDM__request_open = false;

function __RDM_callback()
{
  RDM_reset();
  if(LAST_FORM_OBJ != null)
    LAST_FORM_OBJ.resetSubmitFlag();
}

function RDM_reset()
{
  RDM__request_open = false;
  
  if(RDM__script_element != null)
    document.getElementsByTagName('head').item(0).removeChild(RDM__script_element);
  RDM__script_element = null;
}

/**
 * RDM_load(string, string, string)
 *
 * @param string file
 * @param string cbf            The callback function name.
 * @param string url_var_str    Additional variables to pass by url-get, ie: foo=bar&a=hej
 */
function RDM_load(_file, cbf, url_var_str, use_rdm_root)
{
  use_rdm_root = (typeof(use_rdm_root) == 'undefined' || use_rdm_root === true);
  if(RDM__request_open) return;
  RDM__request_open = true;
  
  var remoteElem = document.createElement('script');
  with(remoteElem)
  {
    type = 'text/javascript';
    src = (use_rdm_root ? RDM__SCRIPT_ROOT : '') +
          _file + (_file.indexOf('?') == -1 ? '?' : '&') + (url_var_str != null ? url_var_str + '&' : '') + 'cbf='+cbf + 
          '&r=' + Math.random();
  }
  
  RDM__script_element = document.getElementsByTagName('head').item(0).appendChild(remoteElem);
}

/**
 * void RDM_submitForm(Form)
 *
 * External libraries: lib.form.js
 */
function RDM_submitForm(FORM, cbf)
{
  if(RDM__request_open) return;
  RDM__request_open = true;
  
  var body = document.getElementsByTagName('body').item(0);
  if(document.getElementById('ifr_RDM') == null)
  {
    if(B.ie)
    {
      var ifr_c = document.createElement('div');
      ifr_c.innerHTML = '<iframe id="ifr_RDM" src="about:blank" name="RDM" style="display:none"></iframe>';
      body.appendChild(ifr_c);
    }
    else
    {
      var remoteElem = document.createElement('iframe');
      with(remoteElem)
      {
        id = 'ifr_RDM';
        name = 'RDM';
        src = 'about:blank';
        style.display = 'none';
//        style.height = style.width = '100px'; // DEBUG OPTION (shows iframe)
      }
      body.appendChild(remoteElem);
    }
  }
  
  RDM_setupForm(FORM, (FORM.ref.action.match(/.*\?[a-z0-9_]+/i) ? '&' : '') + 'cbf=' + cbf);
  
  if(!B.ie5) { FORM.doSubmit(); }
  else { setTimeout("LAST_FORM_OBJ.doSubmit()", 10); } // Time delay needed for this to work in <ie6...
}

var LAST_FORM_OBJ, LAST_FORM_ORIGIN_ACTION;
function RDM_setupForm(f,actionExt)
{
  if(typeof(LAST_FORM_OBJ) != 'object')
  {
    LAST_FORM_ORIGIN_ACTION = f.ref.action;
    f.ref.action = f.ref.action + actionExt;
    f.ref.target = 'RDM';
  }
  else
  {
    LAST_FORM_OBJ.ref.action = LAST_FORM_ORIGIN_ACTION + actionExt;
  }
  
  LAST_FORM_OBJ = f;
}