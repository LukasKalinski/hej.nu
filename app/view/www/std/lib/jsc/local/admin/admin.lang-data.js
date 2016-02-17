@{
@include "lib.ajax.js"
@include "lib.form.js"
@include "lib.detachInner.js"
@}

var FIELD = {};
function OL()
{
  parent.secNavSet('admin','lang');
  FIELD.language = d.getElementById('language');
  FIELD.context = d.getElementById('context');
  FIELD.topcat = d.getElementById('topcat');
  FIELD.subcat = d.getElementById('subcat');
  FIELD.entry = d.getElementById('entry');
  
  for(var key in FIELD)
  {
    FIELD[key].options[0] = new Option('...', 0);
    FIELD[key].options[0].className = 'init';
  }
  
  FIELD.topcat.disabled = true;
  FIELD.subcat.disabled = true;
  
  AJAX.request('language', '_db.lang-data.php', {'get':'a=getlanguage'}, AJAX_callback);
  AJAX.request('context', '_db.lang-data.php', {'get':'a=getcontext'}, AJAX_callback);
}
add_onload_func(OL);

function setLanguage(v)
{
  d.getElementById('frm_entry_value').language_id.value = v;
}

function AJAX_callback(r)
{
  if(!r)
  {
    syserror('AJAX response was null.');
    return;
  }
  switch(r._GET.a)
  {
    case 'getlanguage':
      feedSelect('language', r.data);
      break;
    case 'getcontext':
      feedSelect('context', r.data);
      break;
    case 'gettopcat':
      FIELD.topcat.disabled = false;
      feedSelect('topcat', r.data);
      break;
    case 'getsubcat':
      FIELD.subcat.disabled = false;
      feedSelect('subcat', r.data);
      break;
    case 'getentry':
      feedSelect('entry', r.data);
      break;
    case 'getentryvalue':
      loadEntryValue(r.data);
      break;
    case 'save':
      d.getElementById('entry_saved').innerHTML = '[' + LANG.saved + ']';
      break;
  }
}

function loadEntryValue(entry)
{
  var c_val = d.getElementById('entry_value');
  var c_desc = d.getElementById('entry_desc');
  var c_defval = d.getElementById('entry_def');
  var c_curedit = d.getElementById('entry_path');
  var fev = d.getElementById('frm_entry_value');
  
  if(entry.value != null)
  {
    c_val.removeAttribute('class');
    c_val.value = entry.value;
    c_val.onclick = function() {};
  }
  else
  {
    c_val.setAttribute('class', 'hlight');
    c_val.value = '[' + LANG.entry_value_empty + ']';
    c_val.onclick = function() { this.value = ''; this.removeAttribute('class'); this.onclick = function() {}; };
  }
  c_val.onchange = c_val.onkeydown = function() { d.getElementById('entry_saved').innerHTML = ''; }
  fev.language_id.value = entry.language_id;
  fev.entry_id.value = entry.entry_id;
  c_val.disabled = false;
  c_desc.innerHTML = (entry.description ? entry.description : '-');
  c_defval.innerHTML = (entry.default_value ? entry.default_value : '-');
  c_curedit.innerHTML = composePath(FIELD.context) + composePath(FIELD.topcat) + composePath(FIELD.subcat) + composePath(FIELD.entry);
  function composePath(o)
  {
    return (o.value > 0 ? (o.id != 'context' ? '.' : '') + o.options[o.selectedIndex].text : '');
  }
}

function requestData(caller, entryOnly)
{
  if(typeof(caller) == 'string')
    caller = d.getElementById(caller);
  
  switch(caller.id)
  {
    case 'context':
      if(caller.value > 0)
      {
        var urlvars = 'context_id='+caller.value;
        if(!entryOnly) AJAX.request('topcat', '_db.lang-data.php', {'get':'a=gettopcat&'+urlvars}, AJAX_callback);
        AJAX.request('entry', '_db.lang-data.php', {'get':'a=getentry&'+urlvars}, AJAX_callback);
      }
      else
      {
        FIELD.topcat.disabled = true;
        feedSelect('topcat', null);
        feedSelect('subcat', null);
        feedSelect('entry', null);
      }
      break;
    case 'topcat':
      if(caller.value > 0)
      {
        var urlvars = 'context_id='+FIELD.context.value+'&topcat_id='+caller.value;
        if(!entryOnly) AJAX.request('subcat', '_db.lang-data.php', {'get':'a=getsubcat&'+urlvars}, AJAX_callback);
        AJAX.request('entry', '_db.lang-data.php', {'get':'a=getentry&'+urlvars}, AJAX_callback);
      }
      else
      {
        FIELD.subcat.disabled = true;
        requestData('context', true);
        feedSelect('subcat', null);
        feedSelect('entry', null);
      }
      break;
    case 'subcat':
      if(caller.value > 0)
      {
        var urlvars = 'context_id='+FIELD.context.value+'&topcat_id='+FIELD.topcat.value+'&subcat_id='+caller.value;
        AJAX.request('entry', '_db.lang-data.php', {'get':'a=getentry&'+urlvars}, AJAX_callback);
      }
      else
      {
        requestData('topcat', true);
        feedSelect('entry', null);
      }
      break;
    case 'entry':
      
      break;
  }
}

function resetEntryValueFrm()
{
  d.getElementById('entry_value').innerHTML = '';
  d.getElementById('entry_desc').innerHTML = '-';
  d.getElementById('entry_def').innerHTML = '-';
  d.getElementById('entry_path').innerHTML = '-';
  d.getElementById('frm_entry_value').entry_value.value = '';
  d.getElementById('frm_entry_value').disabled = true;
  d.getElementById('entry_saved').innerHTML = '';
}

function feedSelect(selId, data)
{
  var s = d.getElementById(selId);
  s.options.length = 1;
  if(data != null)
    for(var i=0; i<data.length; i++)
      s.options[i+1] = new Option(data[i].name, data[i].id);
    
}

function resizeTA(o)
{
  var lineHeight = 12;
  var newHeight = o.value.split('\n').length * lineHeight;
  o.style.height = (newHeight > lineHeight*3 ? newHeight : lineHeight*3) + 'px';
}

function load()
{
  var F = new Form('frm_fetch');
  F.verify('language_id', 'this->value != 0', LANG.language_not_set, true);
  F.verify('context_id', 'this->value > 0', LANG.context_not_set, true);
  F.verify('entry_id', 'this->value > 0', LANG.entry_not_set, true);
  
  if(F.verified)
  {
    resetEntryValueFrm();
    AJAX.request('data', '_db.lang-data.php', {'get':'a=getentryvalue&language_id='+F.ref.language_id.value+'&entry_id='+F.ref.entry_id.value}, AJAX_callback);
  }
}

function save()
{
  var F = new Form('frm_entry_value');
  AJAX.request('save_entry', '_db.lang-data.php', {'get':'a=save', 'post':F.data2urlvars()}, AJAX_callback);
}

function go(direction)
{
  var newVal;
  try { newVal = FIELD.entry.options[FIELD.entry.selectedIndex+direction].value; }
  catch(e1) { alert(LANG.reached_end); return; }
  if(newVal == 0) { alert(LANG.reached_start); return; }
  FIELD.entry.value = newVal;
  load();
}