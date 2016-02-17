@{
@include "lib.browser.js"
@include "lib.keysniffer.js"
@include "lib.event.js"
@include "function.preloadImages.js"
@include "function.urlvars2array.js"
@include "function.array_search.js"
@scramble_skip_name "secNavSet"
@}

var menu = new Array();
var secnav = new Array();

var OLF = function()
{
  resizeContentWrapper();
  loadMainNav();
  if(B.ie5) { document.getElementById('ifr_main').style.visibility = 'visible'; }
};
add_onload_func(OLF);

function resizeContentWrapper()
{
  var clipTop = 140;
  var clipBottom = 41;
  var clientHeight = B.gecko ? window.innerHeight : document.body.clientHeight;
  var newHeight = clientHeight - clipTop - clipBottom;
  
  if(newHeight < 0) { newHeight = 0; }
  if(B.ie5 && newHeight > 0) { newHeight--; }
  
  document.getElementById('c_wrapper').style.height = newHeight + 'px';
}
window.onresize = resizeContentWrapper;

function ifr_loadUrl(u)
{
  document.getElementById('ifr_main').src = u;
}

function btn_grp(url,img0,img1,btntxtw)
{
  this.img0 = img0;
  this.img1 = img1;
  this.url  = url;
  this.textwidth = btntxtw;
  this.scwidth = 0;
  this.buttons = new Array();
  this.nextBtnIndex = 0; // Since Array.push() is not supported in <ie6 we have to use a more clumsy solution...
  
  /**
   * @desc Adds sub-category button (NOT secondary nav).
   * @param string url          # Url to go to on click.
   * @param string scbtntxtw    # Button text width
   */
  this.addscbtn = function(url, img, scbtntxtw)
  {
    this.buttons[this.nextBtnIndex++] = {"url":url, "img":img};
    this.scwidth += (parseInt(scbtntxtw) + parseInt(scbtn_staticsx));
  };
  return this;
}

// ##
// ## Primary navigation
// ## 

/**
 * void toggleHeaderMenu(bool, integer)
 * @param bool      expand
 * @param integer   msec      Milliseconds passed from movement start.
 */
var _THM__POS_MIN = 0;
var _THM__POS_MAX = 24;
var _THM__TIME_TOTAL = 8;
var _THM__TIME_INTERVAL = 1;
var THM_is_expanded = false;
function toggleHeaderMenu(expand, msec)
{
  if(THM_is_expanded == expand) return;
  
  var newPos = null;
  if(msec == null) msec = 0;
  
  if(expand)
  {
    newPos = Math.floor(_THM__POS_MAX - _THM__POS_MAX * msec/_THM__TIME_TOTAL);
    if(newPos < _THM__POS_MIN) newPos = _THM__POS_MIN;
  }
  else
  {
    newPos = Math.floor(_THM__POS_MAX * msec/_THM__TIME_TOTAL);
    if(newPos > _THM__POS_MAX) newPos = _THM__POS_MAX;
  }
  
  document.getElementById('h_menu').style.top = newPos + 'px';
  
  if(msec < _THM__TIME_TOTAL)
  {
    msec += _THM__TIME_INTERVAL;
    setTimeout("toggleHeaderMenu("+expand+','+msec+')', _THM__TIME_INTERVAL);
  }
  else
  {
    THM_is_expanded = expand;
  }
}

var _SC_MAXWIDTH = 597;
var menuHiderTimeout = null;
function loadMainNav()
{
  var imgtc=null, imgsc=null, div=null;
  var tcpanel = document.getElementById('h_tc_list');
  var scpanel = document.getElementById('h_sc_list');
  var tcbtn_pos=0, currentbtnw=null, scpos=null;
  
  var hasAdmMenu = (typeof(menu[0]) != 'undefined');
  
  // The menu list starts at index=1 if the admin menu (with index=0) is not available.
  for(var i=(!hasAdmMenu ? 1 : 0), ii=menu.length; i<ii; i++)
  {
    currentbtnw = parseInt(menu[i].textwidth) + parseInt(tcbtn_staticsx);
    
    preloadImages(BTN_ROOT, menu[i].img1+'.gif');
    
    imgtc = document.createElement('img');
    imgtc.id = 'tc' + i;
    imgtc.src = BTN_ROOT + menu[i].img0 + '.gif';
    imgtc.alt = '';
    imgtc.className = 'hand';
    imgtc.onclick = function()
    {
      var mId = parseInt(this.id.substr(2));
      
      if(Key.isDown('CTRL'))
      {
        var url = menu[mId].url;
        if(url == 'exituser') { document.location.href = '/_sys/sys.logout.php'; }
        else if(url == 'swapuser') { document.location.href = '/_sys/sys.logout.php'; }
        else { ifr_loadUrl(url); }
      }
      else
      {
        toggleHeaderMenu(true);
        clearTimeout(menuHiderTimeout);
        menuHiderTimeout = setTimeout("toggleHeaderMenu(false)", 5000);
      }
      menuSwap(mId);
      this.src = BTN_ROOT + menu[mId].img1+'.gif';
    };
    
    scpos = Math.max((tcbtn_pos + Math.floor(currentbtnw/2))-(Math.floor(menu[i].scwidth/2)),0);
    if((scpos + menu[i].scwidth) > _SC_MAXWIDTH) { scpos = _SC_MAXWIDTH - menu[i].scwidth; }
    divsc = document.createElement('div');
    divsc.id = 'sc' + i;
    divsc.className = 'sc_section';
    divsc.style.width = menu[i].scwidth + 'px';
    divsc.style.left = scpos + 'px';
    scpanel.appendChild(divsc);
    
    for(var j=0, jj=menu[i].buttons.length; j<jj; j++)
    {
      imgsc = document.createElement('img');
      imgsc.id = 'sc' + i + '_' + j;
      imgsc.src = BTN_ROOT + menu[i].buttons[j].img + '.gif';
      imgsc.alt = '';
      imgsc.className = 'hand';
      imgsc.__url = menu[i].buttons[j].url;
      imgsc.onclick = function()
      {
        if(this.__url == 'exituser') { document.location.href = '/_sys/sys.logout.php'; }
        else if(this.__url == 'swapuser') { document.location.href = '/_sys/sys.logout.php?swapuser=1'; }
        else { ifr_loadUrl(this.__url); }
        if(B.ie5)
        {
          clearTimeout(menuHiderTimeout);
          menuHiderTimeout = setTimeout("toggleHeaderMenu(false)", 1000);
        }
      };
      divsc.appendChild(imgsc);
    }
    
    tcpanel.appendChild(imgtc);
    tcbtn_pos += currentbtnw;
  }
}

var prevMenuId = null;
function menuSwap(mId)
{
  if(prevMenuId != mId)
  {
    if(prevMenuId != null)
    {
      document.getElementById('sc'+prevMenuId).style.display = 'none';
      document.getElementById('tc'+prevMenuId).src = BTN_ROOT + menu[prevMenuId].img0 + '.gif';
    }
    document.getElementById('sc'+mId).style.display = 'block';
    prevMenuId = mId;
  }
}


// ##
// ## Secondary navigation
// ## 

/* ID maker */
function snbtnId(g,b)
{
  return 'sn_' + g + '_' + b;
}

/* ID token retriever */
function snbtnGetId(id)
{
  return id.substring(3, id.indexOf('_',4));
}

/**
 * class secnav_grp()
 * Secondary navigation group class.
 */
var secnavDoActivateBtnId = null;
var secnavLastActive = null;
function secnav_grp(id)
{
  this.id = id;
  this.activated = false;
  
  this.activate = function()
  {
    var wrapper = document.createElement('div');
    wrapper.id = 'secnav_' + this.id;
    wrapper.className = 'secnav_list';
    document.getElementById('secnav').appendChild(wrapper);
    
    var wrapImgL = document.createElement('img');
    var wrapImgR = document.createElement('img');
    wrapImgL.src = GFX_ROOT + 'struct_i/secnav_wrapper_L.gif';
    wrapImgL.alt = '';
    wrapImgR.src = GFX_ROOT + 'struct_i/secnav_wrapper_R.gif';
    wrapImgR.alt = '';
    wrapper.appendChild(wrapImgL);
    
    var bimg,img1,url;
    for(var i=0, ii=this.buttons.length; i<ii; i++)
    {
      preloadImages(BTN_ROOT,this.buttons[i].img1);
      bimg = document.createElement('img');
      bimg.id = snbtnId(this.id, this.buttons[i].name);
      bimg.src = BTN_ROOT + this.buttons[i].img0 + '.gif';
      bimg.alt = '';
      bimg.__grp_num = this.id;
      bimg.__own_num = i;
      bimg.deactivate = function()
      {
        document.getElementById('secnav_' + snbtnGetId(this.id)).style.visibility = 'hidden';
        this.src = BTN_ROOT + secnav[this.__grp_num].buttons[this.__own_num].img0 + '.gif';
        secnavLastActive = null;
      };
      bimg.activate = function()
      {
        document.getElementById('secnav_'+this.__grp_num).style.visibility = 'visible';
        this.src = BTN_ROOT + secnav[this.__grp_num].buttons[this.__own_num].img1 + '.gif';
        secnavLastActive = this;
      };
      bimg.onclick = function()
      {
        var url, cUrl = secnav[this.__grp_num].buttons[this.__own_num].url.split('?');
        if(IFRMAIN_GET_STR != null) { url = cUrl[0] + IFRMAIN_GET_STR; }
        else if(typeof(cUrl[1]) != 'undefined') { url = cUrl.join('?'); }
        else { url = cUrl.join(''); }
        ifr_loadUrl(url);
      };
      bimg.className = 'hand';
      if(secnavDoActivateBtnId != null && secnavDoActivateBtnId == bimg.id) { bimg.activate(); }
      wrapper.appendChild(bimg);
    }
    
    wrapper.appendChild(wrapImgR);
    
    this.activated = true;
  };
  
  this.buttons = new Array();
  this.nextBtnIndex = 0;
  this.addBtn = function(name, url, img0, img1)
  {
    this.buttons[this.nextBtnIndex] = {"name":name, "url":url, "img0":img0, "img1":img1};
    this.nextBtnIndex++;
  };
  return this;
}

var IFRMAIN_GET_STR = null;
/**
 * @param string grp      # Group
 * @param string name     # Page name or something...
 * @param string urlvars  # Coma-separated string with vars to fetch from url.
 */
function secNavSet(grp, name, save_vars)
{
  var urlvars = document.getElementById('ifr_main').contentWindow.document.location.href.split('?')[1];
  
  if(urlvars != null && save_vars != null)
  {
    save_vars = save_vars.split(',');
    urlvars = urlvars2array(urlvars);
    IFRMAIN_GET_STR = '?';
    for(var i=0; i<urlvars.length; i++)
      if(array_search(urlvars[i].name, save_vars))
        IFRMAIN_GET_STR += urlvars[i].name + "=" + urlvars[i].value;
  }
  else
  {
    IFRMAIN_GET_STR = null;
  }
  
  if(secnavLastActive != null) { secnavLastActive.deactivate(); }
  
  var ref = document.getElementById(snbtnId(grp,name));
  if(ref != null) { ref.activate(); }
  else { secnavDoActivateBtnId = snbtnId(grp,name); }
  
  if(typeof(secnav[grp]) != 'undefined' && !secnav[grp].activated) { secnav[grp].activate(); }
}