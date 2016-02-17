@{
@include "lib.browser.js"
@include "lib.ajax.js"
@include "lib.event.js"
@include "lib.form.js"
@include "modifier.trim.js"
@}

var _STATINDICATOR_X_MAX = 309;
var _RW_HEIGHT = 78;
var S_loop = null;        // @var loop    - Window scroll loop timeout.
var RW_CurrentEnv;        // @var object  - Reply window environment data.
var RW_isopen = false;    // @var bool    - Reply window open status flag.
var RW_cev;               // @var event   - Reply window button click event.
var lastDelBtn = null;    // @var element - Element reference.
var deletedMsgs = 0;
var CW_elem = null;
var CW_posy = null;
var CW_hideTimeout = null;
var DB_PROCESSING = false;
var DB_PROCESS_TIMEOUT = null;
var NMW_ISOPEN = false;
var CURRENT_PAGE;
var LAST_PAGE = null;
var REQUESTED_PAGE = null;
var PAGE_NAV_POS = 1;
var MSGG;

var OLF = function()
{
  parent.secNavSet('user', 'guestbook', 'userid');
  CURRENT_PAGE = PAGE; // PAGE is external; in script file.
  MSGG = new MessageGroup(USER_ID);
  CW_elem = document.getElementById('cw');
  AJAX.request('gst_msgget', '_db.guestbook.php', {'get':'a='+ACTION_GET+'&userid='+USER_ID}, __GST_callback);
};
add_onload_func(OLF);

with(document)
{
  var BTN_DEL = createElement('img');
  var BTN_REP = createElement('img');
  var BTN_HIS = createElement('img');
  var BTN_GOT = createElement('img');
}

BTN_DEL.alt = LANG__alt_msgdel;
BTN_DEL.src = GFX_ROOT + 'user/gb_delbtn0.gif';
BTN_DEL.className = 'hand msg_delbtn';
BTN_DEL.__img1 = new Image();
BTN_DEL.__img1.src = GFX_ROOT + 'user/gb_delbtn1';

BTN_REP.alt = LANG__alt_msgrep;
BTN_REP.src = GFX_ROOT + 'user/gb_repbtn0.gif';
BTN_REP.className = 'hand msg_repbtn';
BTN_REP.__img0 = new Image();
BTN_REP.__img1 = new Image();
BTN_REP.__img0.src = GFX_ROOT + 'user/gb_repbtn0.gif';
BTN_REP.__img1.src = GFX_ROOT + 'user/gb_repbtn1.gif';

BTN_HIS.alt = LANG__alt_msghis;
BTN_HIS.src = GFX_ROOT + 'user/gb_hisbtn.gif';
BTN_HIS.className = 'hand msg_hisbtn';

BTN_GOT.alt = LANG__alt_msggot;
BTN_GOT.src = GFX_ROOT + 'user/gb_gotbtn.gif';
BTN_GOT.className = 'hand msg_gotbtn';

function Message(msgNumUid, msg_id, w_id, t_id, username, genderage, photo_src, msg_content, condition, date)
{
  this.div_msg = null;
  this.delAllow = false;
  
  var div_msghead, div_msgfrom, div_msgdelbtn, div_msgbtngrp, div_msgbody, span_msgphoto, span_msgcontent, img_photo;
  with(document)
  {
    img_photo = createElement('img');
    img_photo.src = photo_src;
    img_photo.alt = '';
    
    this.div_msg    = createElement('div');
    div_msghead     = createElement('div');
    div_msgfrom     = createElement('div');
    div_msgdate     = createElement('div');
    div_msgbtngrp   = createElement('div');
    div_msgbody     = createElement('div');
    span_msgphoto   = createElement('span');
    span_msgcontent = createElement('span');
    
    span_msgphoto.appendChild(img_photo);
  }
  
  this.div_msg.id = 'msg' + msgNumUid;
  this.div_msg.className = 'msg';
  this.__deleted = false;
  div_msghead.className = 'msg_head';
  div_msgfrom.className = 'msg_from';
  div_msgdate.className = 'msg_date';
  div_msgbtngrp.className = 'msg_btngrp';
  div_msgbody.className = 'msg_body';
  span_msgphoto.className = 'msg_photo';
  span_msgcontent.className = 'msg_content';
  
  div_msgfrom.innerHTML = '<a href="/user/user.main.php?userid='+w_id+'" class="cmn">'+username+'</a>'+' '+genderage;
  div_msgdate.innerHTML = date;
  span_msgcontent.innerHTML = msg_content;
  
  var btn = null;
  
  // ## Delete button
  if((w_id == SESSUSER_ID && condition == 0) || USER_ID == SESSUSER_ID) // @todo check message condition too
  {
    this.delAllow = true;
    
    btn = BTN_DEL.cloneNode(false);
    btn.__msgNum = msgNumUid;
    btn.__message_id = msg_id;
    btn.__set1 = function()
    {
      this.src = BTN_DEL.__img1.src;
      this.alt = LANG__alt_msgdeleted;
      this.onclick = function() { alert(LANG__message_already_deleted); };
    };
    
    /**
     * Message delete with AJAX call.
     */
    btn.onclick = function(e)
    {
      if(startDBProcess(LANG__del_dbprocess_active))
      {
        var ev = new Event(e);
        lastDelBtn = this;
        document.getElementById('msg'+this.__msgNum).__deleted = true;
        CW_posy = ev.pageOffsetY - ev.objPosY + 7;
        AJAX.request('gst_msgdel', '_db.guestbook.php', {'get':'a='+ACTION_DEL+'&mid='+this.__message_id}, __GST_callback);
      }
    };
    div_msghead.appendChild(btn);
  }
  
  // ## Reply button
  if(w_id != SESSUSER_ID && USER_ID == SESSUSER_ID)
  {
    btn = BTN_REP.cloneNode(false);
    btn.__writer_id = w_id;
    btn.__message_id = msg_id;
    btn.__thread_id = t_id;
    btn.__msgNum = msgNumUid;
    btn.__set0 = function() { this.src = BTN_REP.__img0.src; this.alt = LANG__alt_msgrep; };
    btn.__set1 = function() { this.src = BTN_REP.__img1.src; this.alt = LANG__alt_msgrepc; };
    btn.onclick = function(e)
    {
      if(document.getElementById('msg'+this.__msgNum).__deleted)
      {
        this.onclick = function() { alert(LANG__noreply_msg_deleted); };
        alert(LANG__noreply_msg_deleted);
        return;
      }
      RW_cev = new Event(e);
      RW_toggle(this);
    };
    div_msghead.appendChild(btn);
  }
  
  // ## History button
  if(w_id == SESSUSER_ID || USER_ID == SESSUSER_ID)
  {
    btn = BTN_HIS.cloneNode(false);
    div_msghead.appendChild(btn);
  }
  
  // ## Goto button
  if(w_id != USER_ID)
  {
    btn = BTN_GOT.cloneNode(false);
    btn.__writer_id = w_id;
    btn.onclick = function(e) { document.location.href = 'user.guestbook.php?userid=' + this.__writer_id; };
    div_msghead.appendChild(btn);
  }
  
  div_msghead.appendChild(div_msgfrom);
  div_msghead.appendChild(div_msgdate);
  div_msghead.appendChild(div_msgbtngrp);
  div_msgbody.appendChild(span_msgphoto);
  div_msgbody.appendChild(span_msgcontent);
  this.div_msg.appendChild(div_msghead);
  this.div_msg.appendChild(div_msgbody);
  
  return this;
}

function MessageGroup(uid)
{
  this.user_id = uid;
  this.messages = new Array();
  this.numOfMsgs = 0;
  this.deleteableMsgNum = 0;
  
  this.load = function(src)
  {
    this.reset();
    var s;
    for(var i=0; i<src.length; i++)
    {
      s = src[i];
      this.messages[i] = new Message(i, s.mId, s.wId, s.tId, s.username, s.genderage, s.uphoto, s.message, s.condition, s.tstamp);
      if(this.messages[i].delAllow)
        this.deleteableMsgNum++;
      this.numOfMsgs++;
    }
  };
  
  this.reset = function()
  {
    this.numOfMsgs = 0;
    this.deleteableMsgNum = 0;
    this.messages = new Array();
  };
  
  this.clearMsgArea = function()
  {
    var msgc = document.getElementById('messages');
    while(msgc.childNodes.length)
      msgc.removeChild(msgc.firstChild);
    deletedMsgs = 0;
  };
  
  this.refresh = function()
  {
    this.clearMsgArea();
    
    if(this.messages.length > 0)
    {
      // Trim margin-bottom on last message.
      this.messages[this.messages.length-1].div_msg.style.marginBottom = '0px';
      
      for(var i=0, ii=this.messages.length; i<ii; i++)
        document.getElementById('messages').appendChild(this.messages[i].div_msg);
    }
    else
    {
      var nomsgElem = document.createElement('div');
      nomsgElem.id = 'noMsgNotice';
      nomsgElem.innerHTML = LANG__gb_empty;
      document.getElementById('messages').appendChild(nomsgElem);
    }
    return;
  };
  
  this.numMsg = function() { return this.numOfMsgs; };
}

function startDBProcess(failMsg)
{
  if(!DB_PROCESSING)
  {
    DB_PROCESSING = true;
    DB_PROCESS_TIMEOUT = setTimeout('resetDBProcess(false)', 20000);
    return true;
  }
  else
  {
    if(failMsg !== false)
      alert(failMsg);
    return false;
  }
}

/**
 * Resets DB process lock. If no response was found (that is both failure and success) an error will be triggered.
 */
function resetDBProcess(response)
{
  if(!response) alert(LANG__server_timeout);
  DB_PROCESSING = false;
  clearTimeout(DB_PROCESS_TIMEOUT);
}

/**
 * Delete message from DOM; i.e. ONLY the DOM (Document Object Model tree).
 */
function delLast()
{
  if(lastDelBtn != null)
    lastDelBtn.__set1();
  
  lastDelBtn = null;
  deletedMsgs++;
}

function page_scrollY(targetCoord,way,jump)
{
	var currentCoord = (B.ie ? document.body.scrollTop : window.pageYOffset);
	scrolled = false;
	if(jump == 1) { jump = way; }
	else { jump = Math.ceil(Math.abs((currentCoord-targetCoord)/4)) * way; }
	window.scrollBy(0,jump);
	if(currentCoord > targetCoord && currentCoord > 0)
	{
		S_loop = setTimeout('page_scrollY('+targetCoord+','+way+',0)',5);
	}
	else { clearTimeout(S_loop); }
}

function RW_updateTypeStat(ta)
{
  var curLen = ta.value.length;
  if(curLen > CONSTR__msg_maxlen)
  {
    ta.value = ta.value.substring(0,CONSTR__msg_maxlen);
    ta.focus();
    RW_updateTypeStat(ta);
    return;
  }
  
  document.getElementById('rw_stati').style.left = Math.floor((curLen / CONSTR__msg_maxlen) * _STATINDICATOR_X_MAX) - _STATINDICATOR_X_MAX + 'px';
}

function RW_ENV(caller)
{
  this.mId = caller.__msgNum;
  this.callerBtn = caller;
  this.callerBtn.__set1();
  return this;
}

function RW_toggle(callerBtn)
{
  if(!RW_isopen)
  {
    RW_setup(callerBtn);
  }
  else
  {
    RW_reset(1);
    if(RW_CurrentEnv.callerBtn.__msgNum != callerBtn.__msgNum) { RW_setup(callerBtn); }
  }
}

function RW_setup(callerBtn)
{
  CW_reset();
  RW_CurrentEnv = new RW_ENV(callerBtn);
  var rw = document.getElementById('rw');
  var newY = Math.max(RW_cev.pageOffsetY - RW_cev.objPosY - _RW_HEIGHT - 9, 0);
  
  CW_posy = newY + _RW_HEIGHT + 16; // Set confirmation window pos.
  
  if((newY+4) - (B.ie ? document.body.scrollTop : window.pageYOffset) < 0) { page_scrollY(newY-4,-1,1); }
  
  with(rw.style)
  {
    top = newY + 'px';
    visibility = 'visible';
  }
  
  var txta = document.getElementById('rw_txta');
  txta.focus();
  RW_updateTypeStat(txta);
  RW_isopen = true;
}

function RW_submit()
{
  var F = new Form(document.getElementById('frm_rw'));
  
  F.ref.message.value = trim(F.ref.message.value);
  RW_updateTypeStat(document.getElementById('rw_txta'));
  
  F.ref.user_id.value = RW_CurrentEnv.callerBtn.__writer_id;
  F.ref.message_id.value = RW_CurrentEnv.callerBtn.__message_id;
  F.ref.thread_id.value = RW_CurrentEnv.callerBtn.__thread_id;
  
  F.verify('message', 'this->value.length > CONSTR__msg_minlen', LANG__msg_too_short, false);
  F.verify('message', 'this->value.length < CONSTR__msg_maxlen', LANG__msg_too_long, false);
  
  if(F.verified)
  {
    startDBProcess(LANG__send_dbprocess_active);
    AJAX.request('gst_msgstore', '_db.guestbook.php', {'post':F.data2urlvars(),'get':'a='+ACTION_STORE}, __GST_callback);
  }
}

function RW_reset(doConfirm)
{
  if(typeof(RW_CurrentEnv) == 'object')
  {
    if(doConfirm == null || document.getElementById('rw_txta').value.length == 0 || confirm(LANG__confirm_msgcancel))
    {
      RW_CurrentEnv.callerBtn.__set0();
      document.getElementById('rw').style.visibility = 'hidden';
      document.getElementById('rw_txta').value = '';
      RW_isopen = false;
    }
  }
}

function CW_reset()
{
  if(CW_hideTimeout != null)
    clearTimeout(CW_hideTimeout);
  
  CW_elem.style.visibility = 'hidden';
  if(deletedMsgs >= MSGG.deleteableMsgNum && MSGG.deleteableMsgNum != 0)
  {
    MSGG.clearMsgArea();
    setTimeout('pageNav(CURRENT_PAGE,1)', 500);
  }
}

function CW_show(msg)
{
  CW_reset();
  document.getElementById('cw_msg').innerHTML = msg;
  CW_elem.style.top = CW_posy + 'px';
  CW_elem.style.visibility = 'visible';
  CW_hideTimeout = setTimeout('CW_reset()', 3000);
}

/**
 * AJAX Callback handler.
 */
function __GST_callback(response)
{
  resetDBProcess(true);
  
  /**
   * Possible causes for this kind of failure:
   * - Server connection lost (DB and/or HTTP).
   * - Bad error handling in AJAX library (lib.ajax.js).
   */
  if(response == null)
  {
    syserror('Propably lost connection to server.');
    return;
  }
  
  switch(response.id)
  {
    case 'gst_msgget':
      CW_reset();               // Reset confirmation window.
      RW_reset();               // Reset reply window.
      MSGG.load(response.data); // Load data.
      MSGG.refresh();           // Display loaded data.
      if(REQUESTED_PAGE !== null)
      {
        LAST_PAGE = CURRENT_PAGE;
        CURRENT_PAGE = REQUESTED_PAGE;
      }
      updatePageNav();          // Update page navigation.
      if(PAGE_NAV_POS == 2)
        window.scrollBy(0,9999);
      break;
    
    case 'gst_msgstore':
      if(response.data)
      {
        if(response._POST.message_id) // Means we had a message reply.
        {
          CW_show(LANG__msg_reply_done);
          RW_reset();
        }
        else // New message.
        {
          NMW_toggle(true);
          REQUESTED_PAGE = 1;
          AJAX.request('gst_msgget', '_db.guestbook.php', {'get':'a='+ACTION_GET+'&userid='+USER_ID}, __GST_callback);
        }
      }
      else
      {
        alert(LANG__msg_store_fail);
      }
      break;
    
    case 'gst_msgdel':
      if(response.data)
      {
        CW_show(LANG__msg_delete_done);
        delLast();
      }
      else
      {
        alert(LANG__msg_del_fail);
      }
      break;
  }
}

/**
 * void NMW_toggle()
 * New Message Writer: toggle mode.
 */
function NMW_toggle(forceHide)
{
  forceHide = typeof(forceHide) == 'undefined' ? false : true;
  
  var div_nmw0 = document.getElementById('div_nmw0');
  var div_nmw1 = document.getElementById('div_nmw1');
  var div_nmw  = document.getElementById('div_nmw');
  
  // Hide
  if(NMW_ISOPEN)
  {
    var txta_nmw = document.getElementById('txta_nmw');
    if(forceHide || txta_nmw.value.length == 0 || confirm(LANG__confirm_msgcancel))
    {
      div_nmw.style.display = 'none';
      div_nmw0.style.display = 'block';
      div_nmw1.style.display = 'none';
      NMW_ISOPEN = false;
      txta_nmw.value = '';
    }
    else if(!forceHide)
    {
      txta_nmw.focus();
    }
  }
  // Show
  else if(!forceHide)
  {
    div_nmw.style.display = 'block';
    document.getElementById('txta_nmw').focus();
    div_nmw0.style.display = 'none';
    div_nmw1.style.display = 'block';
    NMW_ISOPEN = true;
  }
}

/**
 * Submits "new message" form. (NMW = New Message Window).
 */
function NMW_send()
{
  var F = new Form(document.getElementById('frm_nm'));
  
  F.verify('message', 'this->value.length > CONSTR__msg_minlen', LANG__msg_too_short, false);
  F.verify('message', 'this->value.length < CONSTR__msg_maxlen', LANG__msg_too_long, false);
  
  if(F.verified)
    AJAX.request('gst_msgstore', '_db.guestbook.php', {'post':F.data2urlvars(),'get':'a='+ACTION_STORE}, __GST_callback);
}

/**
 * Navigates to page pn.
 */
function pageNav(pn, pNavPos)
{
  PAGE_NAV_POS = pNavPos;
  
  if(startDBProcess(false))
  {
    if(!B.ie5) // Ie 5 crasches randomly for this solution... (which on the other hand was before AJAX solution)
    {
      REQUESTED_PAGE = pn;
      AJAX.request('gst_msgget', '_db.guestbook.php', {'get':'a='+ACTION_GET+'&page='+pn+'&userid='+USER_ID}, __GST_callback);
    }
    else
    {
      document.location.href = document.location.href.replace(/\?.*/, '') + '?userid=' + USER_ID + '&page=' + pn;
    }
  }
}

function selectPageToc()
{
  if(LAST_PAGE != null)
  {
    document.getElementById('ptoc_t_'+LAST_PAGE).className = 'toc';
    document.getElementById('ptoc_b_'+LAST_PAGE).className = 'toc';
  }
  document.getElementById('ptoc_t_'+CURRENT_PAGE).className = 'toc_cur';
  document.getElementById('ptoc_b_'+CURRENT_PAGE).className = 'toc_cur';
}

function updatePageNav()
{
  var pnav_prev = document.getElementById('pNav_prev');
  var pnav_next = document.getElementById('pNav_next');
  
  // ## Prev button
  if(CURRENT_PAGE == 1)
  {
    pnav_prev.style.display = 'none';
  }
  else if(CURRENT_PAGE > 1)
  {
    pnav_prev.style.display = 'block';
  }
  
  // ## Next button
  if(MSGG.numMsg() < 10)
  {
    pnav_next.style.display = 'none';
  }
  else
  {
    pnav_next.style.display = 'block';
  }
  
  if(CURRENT_PAGE > 1 || MSGG.numMsg() == 10)
  {
    if(!document.getElementById('toc_top').childNodes.length)
    {
      var htmltop = '', htmlbtm = '';
      for(var i=1; i<=20; i++)
      {
        htmltop += '<a id="ptoc_t_'+i+'" href="javascript:pageNav('+i+',1);" class="toc" onfocus="this.blur();">'+i+'</a>';
        htmlbtm += '<a id="ptoc_b_'+i+'" href="javascript:pageNav('+i+',1);" class="toc" onfocus="this.blur();">'+i+'</a>';
      }
      document.getElementById('toc_top').innerHTML = htmltop;
      document.getElementById('toc_btm').innerHTML = htmlbtm;
    }
    selectPageToc();
  }
}