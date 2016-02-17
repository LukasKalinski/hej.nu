@{
@include "lib.form.js"
@include "lib.ajax.js"
@include "modifier.nl2br.js"
@include "lib.detachInner.js"
@}

var comments = {};
comments.context = {};
comments.topcat = {};
comments.subcat = {};
comments.entry = {};

var availNames = {};
availNames._current = new Array(); // Currently in display...
availNames.context = new Array();
availNames.topcat = new Array();
availNames.subcat = new Array();

var EG = {};
var frm_edit;
function __onload()
{
  parent.secNavSet('admin','lang');
  
  // ## Setup selects; "action" and "object":
  var i;
  
  var sel_a = d.getElementById('sel_action');
  i = 0;
  sel_a.options.length = 0;
  sel_a.options[i++] = new Option(LANG.title.edit, 'edit');
  sel_a.options[i] = new Option(LANG.title.add, 'add');
  sel_a.options[i++].selected = true;
  
  var sel_e = d.getElementById('sel_target');
  i = 0;
  sel_e.options.length = 0;
  sel_e.options[i++] = new Option(LANG.title.context, 'context');
  sel_e.options[i++] = new Option(LANG.title.topcat, 'topcat');
  sel_e.options[i++] = new Option(LANG.title.subcat, 'subcat');
  sel_e.options[i] = new Option(LANG.title.entry, 'entry');
  sel_e.options[i++].selected = true;
  i=0;
  
  // ## Setup Edit Group instances:
  EG.context = new EditGroupObj('context', LANG.title['context']);
  EG.topcat  = new EditGroupObj('topcat', LANG.title['topcat']);
  EG.subcat  = new EditGroupObj('subcat', LANG.title['subcat']);
  EG.entry   = new EditGroupObj('entry', LANG.title['entry']);
  EG.topcat.setParent(EG.context);
  EG.subcat.setParent(EG.topcat);
  EG.entry.setParent(EG.subcat);
  
  // Add comments textarea:
  var E_title = d.createElement('div');
      E_title.className = 'frm_ftitle';
      E_title.innerHTML = LANG.title.comments + ':';
  var E_textarea = d.createElement('textarea');
      E_textarea.setAttribute('id', 'comments');
      E_textarea.setAttribute('name', 'comments');
      E_textarea.setAttribute('cols', '37');
      E_textarea.setAttribute('rows', '8');
  d.getElementById('eg_comments').appendChild(E_title);
  d.getElementById('eg_comments').appendChild(E_textarea);
  
  frm_edit = d.getElementById('frm_edit'); // Set global ref to #frm_edit container.
  setupEdit();
}
add_onload_func(__onload);

function setComment(id, title, text)
{
  var com = document.getElementById('commentview');
  var comh = document.getElementById('cv_head');
  var comb = document.getElementById('cv_body');
  if(!id)
  {
    com.style.display = 'none';
  }
  else if(id)
  {
    comh.innerHTML = LANG.title.decription_for + ': ' + title;
    comb.innerHTML = nl2br(text);
    com.style.display = 'block';
  }
}

/**
 * @desc Edit Group Object.
 *       Valid id's are: 'context', 'topcat', 'subcat' and 'entry'.
 */
function EditGroupObj(id, title, onchg)
{
  this.id = id;
  this.a = null; // Will be set in this.setAction().
  this.title = title;
  this.parent = null;
  this.child = null;
  this.available = false; // Tells whether the element is available/visible or not.
  this.action = null; // Values 'edit' or 'add'; Should be the same for all instances; consider it "java-static".
  this.elem = null; // Field element; will be available as soon as we print out all elements.
  this.fieldType = null;
  
  this.setParent = function(o)
  {
    this.parent = o;
    this.parent.setChild(this);
  };
  
  this.setChild = function(o)
  {
    this.child = o;
  };
  
  this.setAction = function(a)
  {
    this.action = a;
    if(this.child != null)
      this.child.setAction(a);
  };
  
  /**
   * @desc Deactivates object if it has fieldType == 'SELECT'.
   */
  this.deactivate = function(doDisable)
  {
    var eRef = document.getElementById(this.id);
    if(eRef && eRef.tagName.toUpperCase() == 'SELECT') // Since eRef doesn't have to be defined, for example: subcat is not available when creating topcat.
    {
      eRef.value = 0;
      eRef.options.length = 1;
      eRef.disabled = doDisable;
    }
  };
  
  this.getElemValue = function()
  {
    return this.elem.value;
  };
  
  this.getCommentTitle = function()
  {
    return (this.parent != null && this.parent.elem.value > 0 ? this.parent.getCommentTitle() + '.' : '') +
           this.elem.options[this.elem.selectedIndex].text;
  };
  
  this.getCommentText = function()
  {
    return '<b>[' + this.title + ']</b>\n' +
           comments[this.id][this.getElemValue()] +
           '\n\n' +
           (this.parent != null && this.parent.elem.value > 0 ? this.parent.getCommentText() : '');
  };
  
  this.riseComments = function(fetch)
  {
    if(this.fieldType != 'SELECT')
      return;
    
    if(this.action == 'edit')
      document.getElementById('comments').value = comments[this.id][this.getElemValue()];
    else
      setComment(this.id, this.getCommentTitle(), this.getCommentText());
  };
  
  /**
   * @desc Generates and inserts a edit group element into chosen container.
   */
  this.insert = function(fieldType)
  {
    var E_root = d.createElement('td');      // Root element.
        E_root.id = 'eg_'+this.id;
        if(this.id == 'entry') E_root.setAttribute('colspan', 3);
    var E_title = d.createElement('div');    // Title element.
        E_title.className = 'frm_ftitle';
        E_title.innerHTML = this.title + ':';
    
    this.fieldType = fieldType;
    if(fieldType == 'SELECT')
    {
      var E_field = d.createElement('SELECT');  // Select element.
      E_field.setAttribute('id', this.id);
      E_field.setAttribute('name', this.id);
      E_field.disabled = (this.id == 'topcat' || this.id == 'subcat' || (this.id == 'entry' && this.action == 'edit'));
      E_field.options[0] = new Option(emptyopt, 0);
    }
    else
    {
      var E_field = d.createElement('INPUT');   // Input element.
      E_field.id = this.id;
      E_field.name = this.id;
      E_field.className = 'inp_t';
      E_field.type = 'text';
    }
    
    E_root.appendChild(E_title);
    E_root.appendChild(E_field);
    d.getElementById((this.id != 'entry' ? 'fields_holder' : 'entry_holder')).appendChild(E_root);
    
    this.elem = document.getElementById(this.id);
    this.available = true;
    
    if(fieldType == 'SELECT')
    {
      this.elem.onchange = function()
      {
        var egobj = EG[this.id]; // shortcut
        
        // Deactivate affected fields:
        if(this.id == 'topcat') // When choosing top category sub category will be disabled.
          egobj.child.deactivate(true);
        if((this.id == 'topcat' || this.id == 'subcat') && egobj.child.available)
          egobj.child.deactivate(this.id == 'topcat');
        else if(this.id == 'context' && EG.entry.available)
        {
          EG.topcat.deactivate(true);
          EG.subcat.deactivate(true);
          EG.entry.deactivate(true);
        }
        
        dumpAvailNames(this.id, null, true); // Clear avail names container.
        
        // Search for field to set comments for:
        // Order: backward = ([[[entry] -> subcat] -> topcat] -> context)
        var tmp_egobj = EG[d.getElementById('sel_target').value], commentsSet = false;
        while(tmp_egobj != null)
        {
          if(tmp_egobj.fieldType == 'SELECT' && tmp_egobj.elem.value > 0 && tmp_egobj.available)
          {
            tmp_egobj.riseComments();
            dumpAvailNames(tmp_egobj.id);
            commentsSet = true;
            break;
          }
          tmp_egobj = tmp_egobj.parent;
        }
        if(!commentsSet)
        {
          if(egobj.action == 'add')
            setComment(null);
          else
            document.getElementById('comments').value = '';
        }
        
        // Load data into fields:
        if(egobj.elem.value > 0)
        {
          // Load topcat list:
          if(egobj.id == 'context' && EG.topcat.available)
          {
            var urlget = EG.context.valuesToUrlvars() + '&a=gettopcat';
            AJAX.request('topcat', '_db.lang-struct.php', {'get':urlget}, AJAX_callback);
          }
          // Load subcat list:
          else if(egobj.id == 'topcat' && EG.subcat.available)
          {
            var urlget = EG.topcat.valuesToUrlvars() + '&a=getsubcat';
            AJAX.request('subcat', '_db.lang-struct.php', {'get':urlget}, AJAX_callback);
          }
          
          // Load entries for context/topcat/subcat:
          if(egobj.id != 'entry' && EG.entry.available)
            AJAX.request('entry', '_db.lang-struct.php', {'get':'a=getentry&='+EG.entry.valuesToUrlvars()}, AJAX_callback,
                         {'caller_id':egobj.id});
        }
      };
    }
    else
    {
      this.elem.onkeyup = function()
      {
        dumpAvailNames(this.id, this.value);
      };
    }
  };
  
  this.valuesToUrlvars = function()
  {
    var result = this.id + '=' + this.elem.value;
    return result +  (this.parent != null ? '&' + this.parent.valuesToUrlvars() : '');
  };
  
  return this;
}

/**
 * @param string id
 * @param string search
 */
function dumpAvailNames(id, search, clear)
{
  var c = document.getElementById('availnames');
  if(!clear)
  {
    var str = '';
    var names = (search != null ? availNames._current : availNames[id]);
    
    for(var i=0; i<names.length; i++)
    {
      if(search != null)
      {
        if(search.length == 0 || names[i].substr(0,search.length) == search)
          str += names[i] + '<br />';
      }
      else
      {
        availNames._current[i] = names[i];
        str += names[i] + '<br />';
      }
    }
    c.innerHTML = str;
  }
  else
  {
    availNames._current = new Array();
    availNames[id] = new Array();
    c.innerHTML = '';
  }
}

function AJAX_callback(r)
{
  if(r == null)
  {
    syserror('AJAX callback: file load failed');
    return;
  }
  
  switch(r._GET.a)
  {
    case 'getcontext':
    case 'gettopcat':
    case 'getsubcat':
    case 'getentry':
      if(r.data == null)
        break;
      
      if(r.id == 'entry' && EG.entry.fieldType == 'INPUT' && EG.entry.available)
      {
        availNames[r.vars.caller_id] = new Array();
        for(var i=0; i<r.data.length; i++)
          availNames[r.vars.caller_id][i] = r.data[i].name;
        dumpAvailNames(r.vars.caller_id);
      }
      
      comments[r.id] = {};
      for(var i=0; i<r.data.length; i++)
        comments[r.id][r.data[i].id] = r.data[i].comments;
      
      if(r.data.length > 0 || (EG[r.id].parent != null && EG[r.id].parent.getElemValue() > 0))
        if(EG[r.id].fieldType == 'SELECT' && EG[r.id].available)
          reloadSelect(r.id, r.data);
      break;
    case 'edit':
      comments[r.id][r._POST[r.id]] = r._POST.comments; // Update comments array using AJAX post-var container.
    case 'add':
      function appendCategory(egref)
      {
        if(!egref.available)
          return '';
        
        if(egref.fieldType == 'SELECT')
        {
          if(egref.elem.value > 0)
            return '.' + egref.elem.options[egref.elem.selectedIndex].text;
          else
            return '';
        }
        else // fieldType == INPUT
        {
          if(RE[egref.id].test(egref.elem.value))
            return '.' + egref.elem.value;
          else
            return '';
        }
      }
      var lang_tpl_path = appendCategory(EG.context) + appendCategory(EG.topcat) + appendCategory(EG.subcat) + appendCategory(EG.entry);
      logRecent((r.data === false ? 'FAIL @ ' : '') + r._GET.a, lang_tpl_path);
      
      if(r._GET.a == 'add' && availNames[r.id])
      {
        availNames[r.id][availNames.length] = r._POST[r.id];
        dumpAvailNames(r.id, r._POST[r.id]);
      }
      break;
  }
}

/**
 * @param string action
 * @param string target
 */
function setupEdit()
{
  var action = d.getElementById('sel_action').value;
  var target = d.getElementById('sel_target').value;
  
  EG.context.setAction(action); // Children will be updated recursively.
  
  // Clear element container:
  var fhold = d.getElementById('fields_holder');
  while(fhold.childNodes.length > 0)
  {
    EG[fhold.childNodes[0].id.substr(3)].available = false;
    fhold.removeChild(fhold.childNodes[0]);
    if(fhold.childNodes.length == 0)
      fhold = d.getElementById('entry_holder');
  }
  
  var fieldTypeByAction = (action == 'add' ? 'INPUT' : 'SELECT'); // Set field type depending on action for last field.
  
  switch(target)
  {
    case 'context':
      EG.context.insert(fieldTypeByAction);
      break;
    case 'topcat':
      EG.context.insert('SELECT');
      EG.topcat.insert(fieldTypeByAction);
      break;
    case 'subcat':
      EG.context.insert('SELECT');
      EG.topcat.insert('SELECT');
      EG.subcat.insert(fieldTypeByAction);
      break;
    case 'entry':
      EG.context.insert('SELECT');
      EG.topcat.insert('SELECT');
      EG.subcat.insert('SELECT');
      EG.entry.insert(fieldTypeByAction);
      break;
  }
  AJAX.request('context', '_db.lang-struct.php', {'get':'a=getcontext'}, AJAX_callback);
  
  d.getElementById('comments').value = '';
  setComment();
}

function reloadSelect(id, data)
{
  var o = d.getElementById(id);
  o.options.length = 0;
  o.options[0] = new Option(emptyopt, 0);
  for(i=0; i<data.length; i++)
    o.options[i+1] = new Option(data[i].name, data[i].id);
  o.disabled = false;
}

function logRecent(action, target)
{
  var log = document.getElementById('log');
  if(action != null)
  {
    var E_child = document.createElement('li');
    E_child.innerHTML = '<b>' + action.toUpperCase() + ':</b> {$lang' + target + '}';
    if(log.childNodes.length > 0)
      log.insertBefore(E_child, log.childNodes[0]);
    else
      log.appendChild(E_child);
  }
  else
  {
    while(log.childNodes.length > 0)
      log.removeChild(log.childNodes[0]);
  }
}

function save()
{
  var F = new Form(d.getElementById('frm_edit'));
  
  if(F.ref.context)
  {
    if(F.isFieldType('context', 'INPUT'))
      F.verify('context', 'RE.context.test(this->value)', LANG.form.context_name_invalid, false);
    else
      F.verify('context', 'this->value > 0', LANG.form.context_id_invalid, false); // Context must always be set.
  }
  if(F.ref.topcat && F.isFieldType('topcat', 'INPUT'))
  {
    F.verify('topcat', 'RE.topcat.test(this->value)', LANG.form.topcat_name_invalid, false);
  }
  if(F.ref.subcat && F.isFieldType('subcat', 'INPUT'))
  {
    F.verify('subcat', 'RE.subcat.test(this->value)', LANG.form.subcat_name_invalid, false);
  }
  if(F.ref.entry && F.isFieldType('entry', 'INPUT'))
  {
    F.verify('entry', 'RE.entry.test(this->value)', LANG.form.entry_name_invalid, false);
  }
  
  var F_sav = new Form(d.getElementById('frm_save'));
  
  // Clear fields in frm_save:
  while(F_sav.ref.childNodes.length > 0)
  {
    F_sav.ref.value = (F_sav.ref.childNodes[0].tagName == 'SELECT' ? 0 : '');
    F_sav.ref.removeChild(F_sav.ref.childNodes[0]);
  }
  
  // Export frm_edit values to frm_save:
  var E_hidden;
  for(var i=0; i<F.ref.elements.length; i++)
  {
    E_hidden = d.createElement('input');
    E_hidden.setAttribute('type', 'hidden');
    E_hidden.setAttribute('name', F.ref.elements[i].name);
    E_hidden.setAttribute('value', F.ref.elements[i].value);
    F_sav.ref.appendChild(E_hidden);
  }
  
  if(F.verified)
  {
    var sel_a = d.getElementById('sel_action');
    var ajax_id = d.getElementById('sel_target').value;
    AJAX.request(ajax_id, '_db.lang-struct.php', {'post':F_sav.data2urlvars(),'get':'a='+sel_a.value}, AJAX_callback);
  }
}