@{
@include "lib.browser.js"
@include "lib.event.js"
@include "function.css_setPos.js"
@include "lib.inp_radio.js"
@include "obj.list.js"
@include "obj.aarray.js"
@include "lib.keysniffer.js"
@include "function.objclone.js"
@}

/**
 Notes:
 
 From w3.org:
    Relationships between 'display', 'position', and 'float'
    1. If 'display' has the value 'none', user agents must ignore 'position' and 'float'. In this case, the element generates no box.
    2. Otherwise, 'position' has the value 'absolute' or 'fixed', 'display' is set to 'block' and 'float' is set to 'none'.
       The position of the box will be determined by the 'top', 'right', 'bottom' and 'left' properties and the box's containing block.
    3. Otherwise, if 'float' has a value other than 'none', 'display' is set to 'block' and the box is floated.
    4. Otherwise, the remaining 'display' properties apply as specified.
 */

var CUSTOM_ELEM_PREPROPS = 'position:relative;display:block;border-width:1px;border-style:solid;padding:3px;'+
    	                     'font-size:10px;border-color:#000000;overflow:hidden';
var CUSTOM_ELEM_AVAILPROPS = 'BGR*,FGR*,LTA*,BRD*,TXT*,MRG*';

var APSC = new AArray(); // Available Property Group Shortcuts.
  APSC.set('BGR', 'background-color');
  APSC.set('FGR', 'color');
  APSC.set('MRG', 'margin-left,margin-right,margin-top,margin-bottom');
  APSC.set('LTA', 'display,position,top,left,width,height,padding,z-index');
  APSC.set('BRD', 'border-width,border-style,border-color');
  APSC.set('TXT', 'font-family,font-size,font-weight,font-style,text-decoration');

var RE = new Array();
RE.fullTag = /^<(\/?)([a-z_][a-z0-9_]*)(\/?)\s?(.*)>$/; /* $1: '/' if end-tag, $2: tagName, $3: '/' if empty tag, $4: attrs */
RE.numSignHexColor = /^\#([a-f0-9]{6})$/i;
RE.hexColor = /^[a-f0-9]{6}$/i;
RE.units = /^(-?[0-9]+)px$/i;
RE.zindex = /^[1-9][0-9]{0,2}$/;
RE.widthHeight = /^[0-9]+$/;
RE.positioning = /^-?[0-9]+$/;
RE.padBordFont = /^[0-9]+$/;
RE.elemName = /^[a-z_][a-z0-9_:]*$/i;
RE.elemIdRestr = /^[a-z_][a-z0-9_]*$/i;
RE.elemNameRestr = /^[a-z_][a-z0-9_]*$/i;
RE.attrs = /\s?([a-z_][a-z0-9_]*=\".*?\")\s?/gi;
RE.attr = /^([a-z_][a-z0-9_]*)=\"(.*?)\"$/i;
RE.endTag =   /^\/([a-z_][a-z0-9_]*)$/i;
RE.emptyTag = /^([a-z_][a-z0-9_]*).*\/$/i;
RE.url = CONSTR_RE_URL;
RE.restrictedUrl = CONSTR_RE_RESTR_URL;

var CMLC, EH, Box;

/**
 * WINDOW ONLOAD
 */
var OLF = function()
{
  RGB.setupGUI('HEX');
  
  // Set up radio buttons for tag manager (tagmc = Tag Manager Create):
  var tagmc_type = new Inp_Radio('frm_tagm_create', 'tagm_type');
  tagmc_type.add('class', 1, 'rad_class');
  tagmc_type.add('id', 0, 'rad_id');
  
  EH = new CML_ElemHandler(d.getElementById('frm_cssProps'));
  if(DBUSER_css_raw == CSS_EMPTY)
  {
    /* Import default CSS */
    EH.addElem('a',     'class', 'a',          false, true,  'display:inline;font-weight:bold;text-decoration:none;font-size:10px;padding:0px;color:#3B4A44;'+
                                                             'background-color:transparent', 'BGR*,FGR*,TXT*,BRD*,background-color,padding');
    EH.addElem('a',     'class', 'a:hover',    false, true,  'display:inline;font-weight:bold;text-decoration:none;font-size:10px;padding:0px;color:#8C3F0F;'+
                                                             'background-color:transparent', 'BGR*,FGR*,TXT*,BRD*,background-color,padding');
    EH.addElem('a',     'class', 'user',       false, true,  'display:inline;font-weight:bold;text-decoration:none;font-size:10px;padding:0px;color:#3B4A44;'+
                                                             'background-color:transparent', 'BGR*,FGR*,TXT*,BRD*,background-color,padding');
    EH.addElem('a',     'class', 'user:hover', false, true,  'display:inline;font-weight:bold;text-decoration:none;font-size:10px;padding:0px;color:#8C3F0F;'+
                                                             'background-color:transparent', 'BGR*,FGR*,TXT*,BRD*,background-color,padding');
    EH.addElem('big',   'class', 'big',        false, false, '', null);
    EH.addElem('div',   'class', 'body',       false, false, 'position:relative;display:block;overflow:hidden;width:519px;height:400px;'+
                                                             'padding:5px;background-color:#858454',
                                                             'height,BGR*,FGR*,TXT*,padding');
    EH.addElem('span', 'class', 'b',           false, false, 'font-weight:bold', null);
    EH.addElem('span', 'class', 'i',           false, false, 'font-style:italic', null);
    EH.addElem('span', 'class', 's',           false, false, 'text-decoration:line-through', null);
    EH.addElem('span', 'class', 'u',           false, false, 'text-decoration:underline', null);
    EH.addElem('span', 'class', 'font',        false, true,  '', null);
    EH.addElem('div',  'class', 'center',      false, false, 'text-align:center', null);
    EH.addElem('div',  'class', 'right',       false, false, 'text-align:right', null);
    EH.addElem('hr',   'class', 'hr',          true,  false, 'background-color:#000000;border-style:none;height:1px;width:300px;',
                                                             'background-color,width,height');
    EH.addElem('br', 'class', 'br', true, false, '', null);
  }
  else
  {
    /* Import custom CSS */
    var ruleToks, ruleHeadToks, cssToks = DBUSER_css_raw.split('|');
    for(var i=0; i<cssToks.length; i++)
    {
     ruleToks = cssToks[i].split('>');
     EH.addElem(ruleToks[0], ruleToks[1], ruleToks[2], (ruleToks[3]=='true'), (ruleToks[4]=='true'), ruleToks[5], CUSTOM_ELEM_AVAILPROPS);
    }
  }
  EH.updateGUI('body');
  
  // Setup compiler sub-classes:
  Box = new BoxMover();
  
  // Box Object: Setup body event listeners.
  with(B.ie ? d.body : window)
  {
    onmousemove = function(e)
    {
      var Ev = new Event(e);
      Box.moveTo(Ev.pageOffsetX, Ev.pageOffsetY);
    };
    if(B.gecko)
      onmouseup = function() { Box.detectOut(true); };
  }
  
  // Setup and run compiler preview.
  CMLC = new CML_Compiler(d.getElementById('pe_data'), d.getElementById('pe_prev'));
  CMLC.updatePreview();
};
add_onload_func(OLF);
function pcpl(Ev) { if(Ev == null || Ev.ref.keyCode != KEY_F5) CMLC.updatePreview(); }

/**
 * object RGB_Map()
 */
function RGB_Map()
{
  this.scan = false;
  
  this.sliderSizeX = 147;
  this.mapSizeX = 150;
  this.mapSizeY = 152;
  this.mapOffsetX = 5;
  this.mapOffsetY = 5;
  this.evX = 0;
  this.evY = 0;
  
  this.HEX = '007878'; // Predefined color, maybe random in the future?
  this.R = 0;
  this.G = 0;
  this.B = 0;
  this.S = 0;
  this.V = 0;
  this.H = 0;
  
  this.logEvent = function(ev)
  {
    ev = new Event(ev);
    this.evX = ev.objPosX;
    this.evY = ev.objPosY;
  };
  
  this.setupGUI = function(c)
  {
    switch(c)
    {
      case 'RGB':
        this.HEXfromRGB();
        this.HSVfromRGB();
        break;
      case 'HSV':
        this.RGBfromHSV();
        this.HEXfromRGB();
        break;
      case 'HEX':
        this.RGBfromHEX();
        this.HSVfromRGB();
        break;
    }
    this.refreshGUI();
  };
  
  this.refreshGUI = function()
  {
    d.getElementById('rgbPreview').style.background = '#'+this.HEX;
    
    d.getElementById('inp_H').value = Math.round(this.H);
    d.getElementById('inp_S').value = Math.round(this.S * 100);
    d.getElementById('inp_V').value = Math.round(this.V * 100);
    d.getElementById('inp_R').value = this.R;
    d.getElementById('inp_G').value = this.G;
    d.getElementById('inp_B').value = this.B;
    d.getElementById('inp_HEX').value = this.HEX;
    d.getElementById('inp_cHEX').value = '#' + this.HEX;
    
    var hPos = Math.min(Math.max(this.mapSizeX * (this.H / 360) + this.mapOffsetX, 0), this.mapSizeX + this.mapOffsetX);
    var vPos = Math.min(Math.max(this.mapSizeY * (1 - this.V) + this.mapOffsetY, 0), this.mapSizeY + this.mapOffsetY);
    var sPos = Math.min(Math.max((this.S * this.sliderSizeX) - 2, 1), 144);
    
    css_setPos('cur_h', hPos-3, null);
    css_setPos('cur_v', null, vPos-3);
    css_setPos('cur_map', hPos-5, null);
    css_setPos('cur_map', null, vPos-5);
    css_setPos('cur_s', sPos, null);
    
    // Set opacity for saturation:
    with(d.getElementById('cp_map'))
    {
      if(B.ie)
        style.filter = 'alpha(opacity='+(this.S*100)+')';
      else
        style.opacity = this.S;
    }
  };
  
  this.scanNumericChange = function(inpObj)
  {
    var varVal = inpObj.value;
    var varMax, varSys;
    var doDiv = false;
    
    switch(inpObj.name)
    {
      case 'R':
      case 'G':
      case 'B':
        varMax = 255;
        varSys = 'RGB';
        break;
      case 'H':
        varMax = 360;
        varSys = 'HSV';
        break;
      case 'S':
      case 'V':
        varMax = 100;
        varSys = 'HSV';
        doDiv = true;
        break;
      case 'HEX':
        varSys = 'HEX';
        break;
    }
    
    if(varVal.length > 0)
    {
      var hexValidated = true;
      if(varSys == 'HEX')
      {
        if(RE.hexColor.test(inpObj.value)) { this.HEX = inpObj.value; }
        else if(inpObj.value.length == 6) { inpObj.value = this.HEX; }
        else { hexValidated = false; }
      }
      else
      {
        varVal = varVal.replace(/[^0-9]/gi, '');
        if(isNaN(varVal)) { varVal = 0; }
        varVal = Math.min(varVal, varMax);
        if(doDiv) { varVal = varVal / varMax }
        this[inpObj.name] = varVal;
      }
      if(hexValidated) { this.setupGUI(varSys); }
    }
  };
  
  this.startGraphicMove = function(ev, obj)
  {
    this.scan = true;
    this.scanGraphicMove(ev, obj);
  };
  
  this.scanGraphicMove = function(ev, obj)
  {
    if(this.scan)
    {
      this.logEvent(ev);
      switch(obj)
      {
        case 'map':
          this.V = Math.min(Math.max(1 - ((this.evY - this.mapOffsetY) / (this.mapSizeY)), 0), 1);
          this.H = Math.min(Math.max(360 * ((this.evX - this.mapOffsetX) / this.mapSizeX), 0), 360);
          break;
        case 'saturation':
          this.S = Math.min(Math.max(this.evX / this.sliderSizeX, 0), 1);
          break;
      }
      this.setupGUI('HSV');
    }
  };
  
  this.stopGraphicMove = function()
  {
    if(this.scan) { d.getElementById('inp_HEX').focus(); } // Just to prevent this ugly select-all thing.
    this.scan = false;
  };
  
  this.HEXfromRGB = function()
  {
    function hex(n)
    {
      n = n.toString(16);
      if(n.length < 2) { n = '0' + n; }
      return n;
    }
    this.HEX = hex(this.R) + hex(this.G) + hex(this.B);
    this.HEX = this.HEX.toUpperCase();
  };
  
  this.RGBfromHEX = function()
  {
    this.R = parseInt(this.HEX.substr(0,2), 16);
    this.G = parseInt(this.HEX.substr(2,2), 16);
    this.B = parseInt(this.HEX.substr(4,2), 16);
  };
  
  this.HSVfromRGB = function()
  {
    var cMin = Math.min(Math.min(this.R, this.G), this.B);
    var cMax = Math.max(Math.max(this.R, this.G), this.B);
    
    this.S = (cMax != 0 ? ((cMax - cMin) / cMax) : 0);
    this.V = cMax / 255;
    
    if(this.R == this.G && this.G == this.B) { this.H = 0; }
    else
    {
      var delta = cMax - cMin;
      switch(cMax)
      {
        case this.R: this.H = (this.G - this.B) / delta; break;
        case this.G: this.H = 2 + ((this.B - this.R) / delta); break;
        case this.B: this.H = 4 + ((this.R - this.G) / delta); break;
      }
      this.H = this.H * 60;
      if(this.H < 0) { this.H = this.H + 360; }
    }
  };
  
  this.RGBfromHSV = function()
  {
    var hue = this.H;
    if(hue == 360) { hue = 0; }
    hue = hue / 60;
    var hueSector = Math.floor(hue);
    var hueDelta = hue - hueSector;
    var cmi = (this.V * (1 - this.S));
    var cma = (this.V);
    var dec = (this.V * (1 - (this.S * hueDelta)));
    var inc = (this.V * (1 - (this.S * (1 - hueDelta))));
    
    switch(hueSector)
    {
      case 0:  this.R = cma; this.G = inc; this.B = cmi; break;
      case 1:  this.R = dec; this.G = cma; this.B = cmi; break;
      case 2:  this.R = cmi; this.G = cma; this.B = inc; break;
      case 3:  this.R = cmi; this.G = dec; this.B = cma; break;
      case 4:  this.R = inc; this.G = cmi; this.B = cma; break;
      case 5:  this.R = cma; this.G = cmi; this.B = dec; break;
    }
    
    this.R = Math.max(Math.min(Math.round(this.R * 255), 255), 0);
    this.G = Math.max(Math.min(Math.round(this.G * 255), 255), 0);
    this.B = Math.max(Math.min(Math.round(this.B * 255), 255), 0);
  };
  
  this.copyHEX = function()
  {
    if(B.ie)
    {
      oText = d.getElementById('inp_cHEX').createTextRange();
      oText.execCommand('Copy');
    }
    else
    {
      alert('browser blaha..');
    }
  };
}

var RGB = new RGB_Map();


/**
 * Presentation compilator
 * 
 * @begin 2005-08-07
 * @update 2006-01-04
 */

function tagm_go(m)
{
  var tagm_f = d.getElementById('frm_tagm_create');
  switch(m)
  {
    case 'tag_manager':
      d.getElementById('tagm_create').style.display = 'none';
      d.getElementById('tagm_manage').style.display = 'block';
      tagm_f.reset();
      break;
    case 'create_elem':
      d.getElementById('tagm_manage').style.display = 'none';
      d.getElementById('tagm_create').style.display = 'block';
      tagm_f.tagm_name.focus();
      break;
  }
}

function tagm_togglepg(Caller)
{
  var label_t='_title', label_b='_body';
  var c_body = d.getElementById(Caller.id.substring(0,Caller.id.length-label_t.length+1) + label_b);
  with(c_body.style)
  {
    if(display == 'none' || display.length == 0) { display = 'block'; Caller.className = 'csspg_t1'; }
    else { display = 'none'; Caller.className = 'csspg_t0'; }
  }
}

/**
 * object CML_Elem(string, string, string, string, bool, AArray)
 * Sub-class for CML_ElemHandler-class.
 * (CML = Custom Markup Language)
 */
function CML_Elem(eType, eName, eCss, html_eName, isEmpty, requiresAttrs, editableProps)
{
  this.__objId       = 'CML_Elem';
  this.touched       = false;          // @var bool      - Tells whether the object's css has been externaly touched (modified) or not.
  this.type          = eType;          // @var string    - Element name.
  this.name          = eName;          // @var string    - Element type (id or class).
  this.requiresAttrs = requiresAttrs;
  this.cssStr        = eCss;           // @var string    - Element CSS property string.
  this.cssProps      = new AArray();   // @var AArray    - Element CSS property associative array (AArray).
  this.cssUpdated    = false;          // @var bool      - Indicator for determining whether css has been updated since last property change or not.
  this.isEmpty       = isEmpty;        // @var bool      - Empty element or not: <elem /> or <elem> ... </elem>
  this.htmlElemName  = html_eName;     // @var string    - Element name in HTML.
  
  // Add specific properties for ID-elements:
  if(this.type == 'id')
  {
    this.extendedObj; // @var &CML_Elem
    this.setExtendedObj = function(o) { this.extendedObj = o; };
  }
  
  this.editableProps = editableProps;             // @var AArray  - Editable properties; these can be changed by the user.
  this.availProps = new ObjClone(editableProps);  // @var AArray  - Available properties; contains editableProps here and will be
                                                  //                extended with the properties in cssStr in the construct routines.
  this.availPropsChanged = false;                 // @var bool    - Keeps track of whether the available properties has been updated or not.
  
  /**
   * string getCssProp(string, bool)
   */
  this.getCssProp = function(nam, rmSymbols)
  {
    if(this.cssProps.isset(nam) && this.cssProps.get(nam) != null)
      return (rmSymbols ? this.cssProps.get(nam).replace(/px|\#|%|transparent/, '') : this.cssProps.get(nam));
    else
      return null;
  };
  
  /**
   * bool propIsEditable(string)
   */
  this.propIsEditable = function(pNam)
  {
    return (this.editableProps.get(pNam) != null);
  };
  
  /**
   * bool propIsAvail(string, AArray)
   */
  this.propIsAvail = function(pNam, src)
  {
    if(!src) src = this.availProps;
    var val = src.get(pNam);
    if(typeof(val) == 'undefined') return false;
    return (val == 0);
  };
  
  /**
   * void setPropsAvail(string, bool)
   * Takes a coma-separated (,) string of css properties that will be set.
   *
   * @param string propsStr
   * @param bool avail
   */
  this.setPropsAvail = function(propsStr, avail)
  {
    var props = propsStr.split(',');
    var newVal, currentVal;
    for(var i=0; i<props.length; i++)
    {
      currentVal = this.availProps.get(props[i]);
      if(typeof(currentVal) != 'number')
        currentVal = 0;
      
      newVal = Math.min(0, currentVal+(avail ? 1 : -1));
      this.availProps.set(props[i], newVal);
    }
  };
  
  /**
   * void setCssProp(string, string [, bool])
   * Sets or changes a css property.
   */
  this.setCssProp = function(pNam, pVal, update)
  {
    this.touched = true;
    this.cssUpdated = false;
    
    // Update available properties.
    if(this.getCssProp(pNam) != pVal)
    {
      // Switch availability of dependent properties.
      switch(pNam)
      {
        case 'position':
          if(pVal == 'relative' || pVal == 'static')
          {
            this.setPropsAvail('top,left', false);
            this.setPropsAvail(APSC.get('MRG'), true);
          }
          else // position:absolute
          {
            if(this.getCssProp('top') == null)
              this.setCssProp('top', '0px');
            if(this.getCssProp('left') == null)
              this.setCssProp('left', '0px');
            
            this.setPropsAvail('top,left', true);
            this.setPropsAvail(APSC.get('MRG'), false);
          }
          break;
        
        case 'display':
          if(pVal == 'inline')
          {
            this.setPropsAvail('padding,width,height,position,top,left', false);
            this.setPropsAvail(APSC.get('MRG'), false); // MGR = all margins.
            this.setPropsAvail(APSC.get('BRD'), false); // BRD = border related.
          }
          else if(pVal == 'block')
          {
            this.setPropsAvail('padding,width,height,position,top,left', true);
            this.setPropsAvail(APSC.get('MRG'), true);
            this.setPropsAvail(APSC.get('BRD'), true);
          }
          break;
      }
      this.availPropsChanged = true;
    }
    else
    {
      this.availPropsChanged = false;
    }
    
    this.cssProps.set(pNam, pVal);
    
    if(update)
      this.updateCss();
  };
  
  /**
   * string mcss(string, string)
   * Shortcut function: Merges css-property name with its value.
   *
   * @param string pNam
   * @param string pVal
   */
  this.mcss = function(pNam, pVal) { return pNam + ':' + pVal + ';'; };
  
  /**
   * mixed buildCssStr([string]])
   *   Case 1: Updates css string if it's not up to date. Returns true if any CSS-rules has been applied and false otherways.
   *   Case 2: Builds a css string based on the src input and returns it (see param specs). Note that the build will still 
   *           be based on the current element (this).
   * String is built on the following format (note the lack of a ; at the end):
   *   foo:val;bar:val
   *
   * @param string browser     - Set this either to 'msie' or 'gecko' for browser-specific compilation.
   */
  this.buildCssStr = function(browser)
  {
    // Create available properties AArray:
    var cssProps, availProps;
    if(this.type == 'id' && this.extendedObj)
    {
      availProps = new ObjClone(this.extendedObj.availProps);
      availProps.importOverwrite(this.availProps);
      cssProps = new ObjClone(this.extendedObj.cssProps);
      cssProps.importOverwrite(this.cssProps);
    }
    else
    {
      availProps = this.availProps;
      cssProps = this.cssProps;
    }
    
    var pVal, str = '';
    var width = null, height = null, padding = null, borderWidth = null;
    var wOffset = 0, hOffset = 0;
    var AA;
    while(AA = cssProps.foreach('__n','__v'))
    {
      pNam = AA.__n;
      pVal = AA.__v;
      
      // Continue loop if:
      //   1. We have no property-extending element (extElem) AND this.availProps is false for pNam AND we're 
      if(!this.propIsAvail(pNam, availProps) || pVal == null)
        continue;
      
      if(pNam.length && pVal.length)
      {
        // ## Collect relevant property values for the box dimensions (if not msie box model).
        if((B.ie && typeof(browser) == 'undefined') || browser == 'msie') // BUGFIX @ 2006-03-03
        {
          str += this.mcss(pNam, pVal);
        }
        else // Browser case: Gecko/Firefox right now...
        {
          switch(pNam)
          {
            case 'width':
              if(pVal != 'auto') width = parseInt(pVal);
              break;
            case 'height':
              if(pVal != 'auto') height = parseInt(pVal);
              break;
            
            case 'padding':
              padding = parseInt(pVal);
              wOffset += (padding*2);
              hOffset += (padding*2);
              str += this.mcss(pNam, pVal);
              break;
            
            case 'border-width':
              // If border-style is 'none' or is undefined then border-width won't apply... the inverse case gives:
              if(cssProps.isset('border-style') && cssProps.get('border-style') != 'none')
              {
                borderWidth = parseInt(pVal);
                wOffset += (borderWidth*2);
                hOffset += (borderWidth*2);
                str += this.mcss(pNam, pVal);
              }
              break;
            
            default:
              str += this.mcss(pNam, pVal);
          }
        }
      }
    }
    
    // ## Resize width and height according to the IE-way (which means: absolute width and height, independent of padding, margins etc).
    if(!B.ie || browser == 'gecko')
    {
      // Now we'll do something about the case when (padding + border-width) > (height and width respectively)...
      // Priority is: height > border-width > padding
      var addCssProps = '';
      var diff;
      
      if(width != null)
      {
        // Buffers: 'h' means horizontal (left and right) and 'v' means vertical (top and bottom).
        var hPadding, hBorderWidth;
        
        var width = diff = width - wOffset;
        if(diff <= 0)
        {
          // Trim padding.
          hPadding = diff = 2*padding + diff; // diff is negative here.
          
          // Check if diff became zero or negative.
          if(diff <= 0)
          {
            // Trim border.
            hBorderWidth = Math.max(0, 2*borderWidth + diff); // diff is negative here.
            addCssProps += this.mcss('border-left-width', Math.ceil(hBorderWidth/2) + 'px');
            addCssProps += this.mcss('border-right-width', Math.floor(hBorderWidth/2) + 'px');
            hPadding = 0;
          }
          
          // Trim the box systematically using the following priority:
          //   padding-bottom > padding-top > border-bottom-width > border-top-width
          addCssProps += this.mcss('padding-left', Math.ceil(hPadding/2) + 'px');
          addCssProps += this.mcss('padding-right', Math.floor(hPadding/2) + 'px');
          width = 0;
        }
        
        str += 'width:' + width + 'px;';
      }
      
      // Repeat the same procedure for height.
      if(height != null)
      {
        // Buffers: 'h' means horizontal (left and right) and 'v' means vertical (top and bottom).
        var vPadding, vBorderWidth;
        
        var height = diff = height - hOffset;
        if(diff <= 0)
        {
          // Trim padding.
          vPadding = diff = 2*padding + diff; // diff is negative here.
          
          // Check if diff became zero or negative.
          if(diff <= 0)
          {
            // Trim border.
            vBorderWidth = Math.max(0, 2*borderWidth + diff); // diff is negative here.
            addCssProps += this.mcss('border-top-width', Math.ceil(vBorderWidth/2) + 'px');
            addCssProps += this.mcss('border-bottom-width', Math.floor(vBorderWidth/2) + 'px');
            vPadding = 0;
          }
          
          // Trim the box systematically using the following priority:
          //   padding-bottom > padding-top > border-bottom-width > border-top-width
          addCssProps += this.mcss('padding-top', Math.ceil(vPadding/2) + 'px');
          addCssProps += this.mcss('padding-bottom', Math.floor(vPadding/2) + 'px');
          height = 0;
        }
        
        str += 'height:' + height + 'px;';
      }
      
      str += addCssProps;
    }
    
    if(str.length > 0)
    {
      this.cssUpdated = true; // ... Why under this condition?
      str = str.substr(0, str.length-1); // Remove last ;.
    }
    
    return str;
  };
  
  this.updateCss = function()
  {
    if(!this.cssUpdated)
      this.cssStr = this.buildCssStr();
    return this.cssUpdated;
  };
  
  /**
   * string getCssStr()
   * Returns CSS string.
   */
  this.getCssStr = function()
  {
    this.updateCss();
    return this.cssStr;
  };
  
  /**
   * mixed compileStartElem(string, AArray)
   * Returns (if type != id) a ready to use HTML start-element containing a:
   *    1. AArray not null: CSS string inside a style="".
   *    2. AArray is null:  Element name as class name inside a class="".
   * ...otherways false.
   *
   * @param string attrs       # Attribute string with a leading space character.
   * @param AArray doHardcode  # Set this to true if we want to hardcode the css string into the tag (usually used for preview).
   * @param CML_Elem ID_Elem   # Extending element; set to possible ID-Element that we'll use to extend the cssProps.
   */
  this.compileStartElem = function(attrs, doHardcode, ID_Elem)
  {
    if(this.type == 'class')
    {
      var extElem; // @var CML_Elem  - The extending element.
      var s = '<'; // Initiate return string.
      s += this.htmlElemName;
      s += (attrs != null && attrs.length > 0 ? attrs : '');
      if(doHardcode)
      {
        var tmp = (ID_Elem ? ID_Elem.buildCssStr() : this.buildCssStr());
        s += (tmp.length > 0 ? ' style="' + tmp + '"' : '');
      }
      s += (this.isEmpty ? ' /' : '');
      s += '>';
      
      return s;
    }
    else
    {
      return false;
    }
  };
  
  /**
   * mixed compileEndElem()
   * Returns ready to use HTML end-element if the element isn't an "empty element", otherways false.
   */
  this.compileEndElem = function()
  {
    if(!this.isEmpty && this.type == 'class') return '</' + this.htmlElemName + '>';
    else return false;
  };
  
  
  // ## On create (constructor functionality):
  
  // Import pre-defined css properties into property array.
  var pToks = this.cssStr.split(';');
  var pNam, pVal;
  for(var i=0, ii=pToks.length; i<ii; i++)
  {
    pNam = pToks[i].substr(0,pToks[i].indexOf(':'));
    pVal = pToks[i].substr(pNam.length+1);
    
    this.setPropsAvail(pNam, true);
    this.setCssProp(pNam, pVal, false);
  }
  
  return this;
}

function CML_FProp(nam,BlockElem,InpElem)
{
  this.name = nam;
  this.BElem = BlockElem; // Block element
  this.IElem = InpElem; // Input element
  
  this.set = function(s) { this.IElem.value = s; };
  this.show = function() { this.BElem.style.display = 'block'; };
  this.hide = function() { this.BElem.style.display = 'none'; };
}

/**
 * object CML_ElemHandler()
 */
function CML_ElemHandler(Form)
{
  this.F = Form;
  this.F_Props = new AArray();
  
  this.cssInpPropIdPrefix = 'cssp_';
  this.cssFrmPropIdPrefix = 'div__p_';  // String used to prefix property groups and individuals.
  this.CElem = null;                    // Current CML_Elem object.
  this.Elems = new AArray();            // Elements associative array.
  this.elemsUpdated = false;
  this.elemNum = 1;
  
  this.addElem = function(actualElemName, type, name, isEmpty, requiresAttrs, css, availPropsStr)
  {
    // Element is already defined.
    if(this.Elems.isset(name))
      return false;
    
    // Check if element name is valid.
    else if(!RE.elemName.test(name))
      return false;
    
    // * Element is valid here.
    
    var AA_AP = new AArray(); // Assoc array of available AND editable properties.
    
    // Test available properties:
    if(availPropsStr != null)
    {
      var ap = availPropsStr.split(',');
      for(var i=0, ii=ap.length; i<ii; i++)
      {
        if(ap[i].substr(3,1) == '*') // We have a property group (Available Property group ShortCuts)
        {
          ap[i] = APSC.get(ap[i].substr(0,3)).split(',');
          for(var j=0, jj=ap[i].length; j<jj; j++)
            AA_AP.set(ap[i][j], 0, true);
        }
        else // We have a single property.
        {
          AA_AP.set(ap[i], 0, true);
        }
      }
    }
    
    this.Elems.set(name, new CML_Elem(type, name, css, actualElemName, isEmpty, requiresAttrs, AA_AP));
    this.elemsUpdated = false;
    
    return true;
  };
  
  this.exportRawCss = function()
  {
    var str = '', cssStr;
    var AA;
    while((AA = this.Elems.foreach('__name', '__E')) !== false)
    {
      cssStr = AA.__E.buildCssStr('msie'); // MSIE's box model is considered standard in this environment.
      str += AA.__E.htmlElemName + '>' +
             AA.__E.type + '>' +
             AA.__E.name + '>' +
             AA.__E.isEmpty + '>' +
             AA.__E.requiresAttrs + '>' +
             cssStr + '|';
    }
    
    return str.substr(0, str.length-1);
  };
  
  this.compileStyleSheet = function(browser)
  {
    var str = '';
    var AA, cNam, cssStr; // cNam = className for css-property.
    while((AA = this.Elems.foreach('__name', '__E')) !== false)
    {
      cssStr = AA.__E.buildCssStr(browser);
      if(cssStr.length == 0)
        continue;
      
      cNam = (AA.__E.type == 'id' ? '#' : '.') + CSS_RULE_PREFIX + AA.__name;
      if(AA.__E.htmlElemName == 'a') // Do special treating to a-tags.
      {
        if(AA.__name.indexOf(':') == -1) // a:link, a:visited, a:active
        {
          str += 'a' + cNam + ':link{' + cssStr + '}';
          str += 'a' + cNam + ':visited{' + cssStr + '}';
          str += 'a' + cNam + ':active{'  + cssStr + '}';
        }
        else // a:hover
        {
          str += 'a' + cNam + '{'  + cssStr + '}';
        }
      }
      else
      {
        str += cNam + '{' + cssStr + '}';
      }
    }
    return str;
  };
  
  /**
   * bool setCss(object, string)
   *
   * @param object caller
   */
  this.setCss = function(Caller, pNam)
  {
    var pVal = Caller.value;                               // Suggested CSS property value (=not checked).
    var current_pVal = this.CElem.getCssProp(pNam, true);  // Shortcut to current CSS property value.
    
    // ## Check value and add symbols(#), units(px), aliases(transparent) to relevant css properties.
    switch(pNam)
    {
      // Colors:
      case 'background-color':
      case 'border-color':
      case 'color':
        if(RE.hexColor.test(pVal))
          pVal = '#' + pVal;
        else if(pVal.length == 0)
          pVal = null; // Removes property (not actually removes though, it just won't appear in the css string).
        else
        {
          if(pVal.length >= 6)
            Caller.value = (current_pVal != null ? current_pVal : '');
          return false;
        }
        break;
      
      // Appearence: z-index (positive numbers and not zero)
      case 'z-index':
        if(RE.zindex.test(pVal))  {  }
        else if(pVal.length == 0) { pVal = 'auto'; }
        else                      { Caller.value = (current_pVal != null ? current_pVal : Caller.getAttribute('value')); return false; }
        break;
      
      // Dimensions (positive numbers):
      case 'height':
      case 'width':
        if(RE.widthHeight.test(pVal))
        {
          // Do special treating for body:
          if(this.CElem.name == 'body' && pNam == 'height' && pVal < 20)
            Caller.value = pVal = 20;
          pVal += 'px';
        }
        else if(pVal.length == 0)     { pVal = 'auto'; }
        else                          { Caller.value = (current_pVal != null ? current_pVal : Caller.getAttribute('value')); return false; }
        break;
      
      // Positioning (positive and negative numbers):
      case 'top':
      case 'left':
      case 'margin-left':
      case 'margin-right':
      case 'margin-top':
      case 'margin-bottom':
        if(RE.positioning.test(pVal)) { pVal += 'px'; }
        else                          { Caller.value = (current_pVal != null ? current_pVal : Caller.getAttribute('value')); return false; }
        break;
      
      // Other numeric values (only positive numbers):
      case 'padding':
      case 'border-width':
      case 'font-size':
        if(RE.padBordFont.test(pVal)) { pVal += 'px'; }
        else                          { Caller.value = (current_pVal != null ? current_pVal : Caller.getAttribute('value')); return false; }
        break;
    }
    
    this.CElem.setCssProp(pNam, pVal, true);
    if(this.CElem.availPropsChanged)
      this.updateGUI(this.CElem.name, true);
    return true;
  };
  
  /**
   * void updateGUI(string, bool)
   */
  this.updateGUI = function(elemName, force)
  {
    // Check if we need to update.
    if(force !== true && (!this.Elems.isset(elemName) || (this.CElem != null && this.CElem.name == elemName)))
      return false;
    
    // Set current elem.
    this.CElem = this.Elems.get(elemName);
    
    // Reset form.
    this.F.reset();
    
    // ## Load select options (tags (class) and id's).
    var elems = new Array();
    elems[0] = new Array();
    elems[1] = new Array();
    var SEL_elem = d.getElementById('sel_elem');
    if(!this.elemsUpdated)
    {
      var i=0, j=0;
      while(AA = this.Elems.foreach('__elemName','__Elem'))
      {
        if(AA.__Elem.type == 'id')
          elems[1][i++] = AA.__elemName;
        else
          elems[0][j++] = AA.__elemName;
      }
      
      elems[0].sort();
      elems[1].sort();
      
      SEL_elem.options.length = 0;
      var optId = 0;
      for(i=0; i<elems.length; i++)
        for(j=0; j<elems[i].length; j++)
          SEL_elem.options[optId++] = new Option((i == 1 ? '#'+elems[i][j] : '<'+elems[i][j]+'>'), elems[i][j]);
      
      this.elemsUpdated = true;
    }
    SEL_elem.value = elemName;
    
    // ## Import css property values into corresponding form elements.
    var pNam, pVal, pCont, AA;
    while(AA = this.CElem.cssProps.foreach('__n','__v'))
    {
      pNam = AA.__n;
      pVal = AA.__v;
      
      // CSS property with value null means that it's not available.
      if(pVal == null)
        continue;
      
      if(pNam.length > 0)
      {
        // Remove # from hex-colors.
        if(RE.numSignHexColor.test(pVal))
          pVal = RegExp.$1;
        
        // Remove units from numbers.
        else if(RE.units.test(pVal))
          pVal = RegExp.$1;
        
        // Remove string:'transparent' and string:'auto' from color and size properties.
        else if(pVal == 'transparent' || pVal == 'auto')
          pVal = '';
        
        // Check if we have a container for current property and change it if so is the case.
        if(this.F_Props.isset(pNam))
          this.F_Props.get(pNam).set(pVal);
      }
    }
    
    // Hide all form properties by default.
    var AA;
    while(AA = this.F_Props.foreach('__n','__FP'))
      AA.__FP.hide();
    
    // Show wanted form properties.
    while(AA = this.CElem.availProps.foreach('__pNam','__val'))
    {
      if(this.CElem.propIsAvail(AA.__pNam) && this.CElem.propIsEditable(AA.__pNam))
        this.F_Props.get(AA.__pNam).show();
    }
    
    return true;
  };
  
  // ## Constructor routines:
  
  // Import Form CSS Properties.
  var fpNam, FPInp;
  var FProps = this.F.getElementsByTagName('div'), FP;
  for(var i=0, ii=FProps.length; i<ii; i++)
  {
    FP = FProps[i];
    if(FP.id.substr(0,7) != this.cssFrmPropIdPrefix)
      continue;
    
    fpNam = FP.id.substr(7);
    FPInp = d.getElementById(this.cssInpPropIdPrefix + fpNam);
    this.F_Props.set(fpNam, new CML_FProp(fpNam, FP, FPInp), false);
  }
}

/**
 * Creates and stores a new custom element.
 * !! This function is meant for the user, not the predefined tags etc...
 */
function cml_addElem()
{
  var f = d.getElementById('frm_tagm_create');
  if(RE.elemNameRestr.test(f.tagm_name.value) &&
     EH.addElem('div', f.tagm_type.value, f.tagm_name.value, false, false, CUSTOM_ELEM_PREPROPS, CUSTOM_ELEM_AVAILPROPS))
  {
    EH.updateGUI(f.tagm_name.value);
    f.reset();
    tagm_go('tag_manager');
    CMLC.updatePreview();
  }
  else { alert(LANG__err_elem_name_invalid); }
  
  pcpl(null);
}

/**
 * Function for updating concrete css properties (every form input calls this function).
 */
function cml_update(Caller, cssPropName, forceCompile)
{
  EH.setCss(Caller, cssPropName);
  
  // We need to recompile in case of a position-property change since event-listeners must be added/removed (for wysiwyg functionality).
  if(cssPropName == 'position' || forceCompile == true)
    CMLC.updatePreview();
  else
    CMLC.updateStyle();
}

/************************************************
 * Tools
 ***********************************************/
function str_importValues(str, vals, wrapper)
{
  var re;
  for(var i=0, ii=vals.length; i<ii; i++)
  {
    re = new RegExp('\\$'+i,'g');
    str = str.replace(re, wrapper[0]+vals[i]+wrapper[1]);
  }
  return str;
}


/************************************************
 * Compiler Environment
 ***********************************************/

function pe_htmlentities(s)
{
  return s.replace(/</g, '&#60;').replace(/>/g, '&#62;');
}

var pe_msg_current;
function pe_msg(msg, vals)
{
  if(vals)
    msg = str_importValues(msg, vals, ['<span class="pe_msg_highlight">','</span>']);
  
  pe_msg_current = d.getElementById('pe_msg').innerHTML = (msg ? '<span class="pe_msg_prefix">#</span> ' + msg : LANG__pe_msg_default);
}

function pe_msgUpdateWith(str,sep)
{
  if(!sep) sep = ' ';
  o = d.getElementById('pe_msg');
  o.innerHTML = pe_msg_current + (str ? sep + str : '');
}

/**
 * CML_Compiler()
 */
function CML_Compiler(C,P)
{
  this.DataCont  = C;    // Presentation data container (the textarea).
  this.PrevArea  = P;    // Presentation preview area (div-block).
  
  this.DebugElem = d.createElement('div');
  this.DebugElem.setAttribute('id', 'pe_debug');
  
  // ## Compiler trackers:
  this.tagStack     = new List();
  this.openTags     = new Array();
  this.regIds       = new AArray();
  this.errors       = new List();
  this.hasBodyStart = false;
  this.hasBodyEnd   = false;
  this.displaysErr  = false;
  
  // ## CSS compiler data.
  this.prepContentToks = new List();
    // Example: List.push(new Array(Mixed:<Element/Compiled string/Plain string>,
    //                              Bool:<Is compiled>),
    //                              String:<Attributes>,
    //                              AArray:<Additional css properties reference>);
  
  /**
   * void resetEnv();
   */
  this.resetEnv = function()
  {
    this.displaysErr = false;
    this.tagStack.clear();
    this.openTags = new Array();
    this.regIds   = new AArray();
    this.errors.clear();
    this.hasBodyStart = false;
    this.hasBodyEnd   = false;
    this.prepContentToks.clear();
  };
  
  /**
   * bool openTag(CML_Elem)
   */
  this.openTag = function(cml)
  {
    if(typeof(cml) == 'undefined') return false;
    if(cml.isEmpty) return true;
    
    this.tagStack.push(cml.name);
    
    if(typeof(this.openTags[cml.name]) == 'undefined')
      this.openTags[cml.name] = 1;
    else
      this.openTags[cml.name]++;
    
    return true;
  };
  
  /**
   * bool registerId(string)
   *
   * Warning: typeof(this.regIds['some']) (and maybe other keys) will return 'function' in firefox. Therefore we're going to prefix these.
   * _OR_ use AArray object instead (as we do just now, and yes, it is a change)...
   */
  this.registerId = function(id)
  {
    if(!this.regIds.isset(id))
    {
      this.regIds.set(id, 1);
      return true;
    }
    else
    {
      this.err(LANG__err_id_already_used, new Array(id));
      return false;
    }
  };
  
  /**
   * bool tagIsOpen(string)
   */
  this.tagIsOpen = function(n)
  {
    return (typeof(this.openTags[n]) != 'undefined' && this.openTags[n] > 0);
  };
  
  /**
   * bool closeTag(CML_Elem)
   */
  this.closeTag = function(cml)
  {
    if(typeof(cml) == 'undefined') return false;
    
    // User attempted to end an empty tag.
    if(cml.isEmpty)
    {
      this.err(LANG__err_empty_tag_no_end, new Array(cml.name));
      return false;
    }
    else if(this.tagIsOpen(cml.name) && this.tagStack.getLast() == cml.name)
    {
      this.tagStack.pop();
      this.openTags[cml.name]--;
      return true;
    }
    else if(!this.tagIsOpen(cml.name))
    {
      this.err(LANG__err_unnecessary_end_tag, new Array(cml.name));
      return false;
    }
    else
    {
      this.err(LANG__err_expecting_end_tag, new Array(this.tagStack.getLast(), cml.name));
      return false;
    }
  };
  
  /**
   * void err(String, String[])
   */
  this.err = function(msg, vals)
  {
    if(vals)
      msg = str_importValues(msg, vals, ['<span class="ped_msg_highlight">','</span>']);
    msg = msg.replace(/\n/g, '<br />');
    this.errors.push(msg);
  };
  
  /**
   * bool hasError()
   */
  this.hasError = function()
  {
    return (!this.errors.isEmpty());
  };
  
  /**
   * void dumpErrors()
   * Prints out max n stored errors.
   */
  this.dumpErrors = function(n)
  {
    var msg, msgs='';
    while(n-- > 0 && (msg = this.errors.shift()) !== false)
      msgs += '<div class="ped_msg"><span class="ped_msg_prefix">#</span> ' + msg + '</div>';
    this.DebugElem.innerHTML = msgs;
    this.PrevArea.innerHTML = ''; // Clear preview area.
    this.PrevArea.appendChild(this.DebugElem);
  };
  
  /**
   * void updatePreview(c)
   * 
   * @param string c  # String to update content with; if none is specified a new string will be compiled using default data input.
   */
  this.updatePreview = function(c)
  {
    if(c == null)
      c = this.makeCompile(true);
    
    if(!this.hasError())
    {
      // Abort possible wysiwyg-activity:
      Box.endMove();
      Box.detectOut();
      
      this.PrevArea.innerHTML = c;
    }
    else
    {
      this.PrevArea.innerHTML = '';
      this.dumpErrors(1);
      this.displaysErr = true;
    }
  };
  
  /**
   * void updateStyle()
   * Uses an array on the following format to make a fast recompile:
   *  List(new Array(0 => Mixed:<Element/Compiled string/Plain string>,
                     1 => Bool:<Is compiled>),
                     2 => String:<Attributes>,
                     3 => CML_Elem:<Extension>));
   */
  this.updateStyle = function()
  {
    var subject, isCompiled, attrs, extElem;
    if(!this.displaysErr)
    {
      var c = '';
      var ctok;
      this.prepContentToks.reset();
      while(this.prepContentToks.hasNext())
      {
        ctok = this.prepContentToks.current();
        subject = ctok[0];
        isCompiled = ctok[1];
        attrs = ctok[2];
        extElem = ctok[3];
        
        // String is not compiled.
        if(!isCompiled && subject != null)
          c += subject.compileStartElem(attrs, true, extElem);
        // String is compiled.
        else
          c += subject;
        
        this.prepContentToks.next();
      }
      
      this.updatePreview(c);
    }
  };
  
  /**
   * string makeCompile(bool)
   */
  this.makeCompile = function(isPreview)
  {
    this.resetEnv();
    var c = this.DataCont.value;
    
    c = c.replace(/\|/gi, '&#124;');   // Replace all pipe characters with corresponding HTML entities (since these are used for separation below).
  	c = c.replace(/<{1}/gi, '|<');     // Mark all < characters.
  	c = c.replace(/>{1}/gi, '>|');     // Mark all > characters.
  	
    var isEndTag, isEmpty, tagFound, attrsFound;
    var tagName, attrs, attr, attrStr='', attrNam, attrVal, usedAttrs;
    var id_attr, ahref_attr, username_attr;
    var hasCssId;
    var __ref_idElem;
    var compiled_str = '';
    var compiledElem;
    var attrList = new List(); // Attribute list for storage of parsed attribute names and values.
    var E, t, E_id; // Shortcuts: E=EH.elems[tagName], t=tokens[i], E_id=ref to id element
    var tokens = c.split(/\|{1,2}/);
    
    COMPILE_LOOP:
    for(var i=0, ii=tokens.length; i<ii; i++)
    {
      t = tokens[i];
      
      // Reset temporary variables:
      isEndTag = false;
      isEmpty = false;
      tagFound = false;
      attrsFound = false;
      attrStr = '';
      usedAttrs = new AArray();
      id_attr = null;
      ahref_attr = null;
      username_attr = null;
      compiledElem = null;
      __ref_idElem = null;
      hasCssId = false;
      E_id = null;
      
      if(RE.fullTag.test(tokens[i])) // $1: '/' if end-tag, $2: tagName, $3: '/' if empty tag, $4: attrs
      {
        isEndTag = (RegExp.$1 == '/');
        tagName = RegExp.$2;
        isEmpty = (RegExp.$3 == '/');
        attrs = RegExp.$4;
        
        E = EH.Elems.get(tagName); // Update shortcut to current element.
        
        if(E)
        {
          // Left- and right-trim attributes:
          attrs = attrs.replace(/^\s*(.*?)\s*$/, '$1');
          
          // Check if we have any attributes and separate them from tagName.
          if(attrs.length > 0)
          {
            attrs = attrs.replace(RE.attrs, '$1|'); // Separate attrs by |.
            attrs = attrs.substr(0, attrs.length-1).split('|');
            attrsFound = (attrs.length > 0);
            for(var attr_i=0; attr_i<attrs.length; attr_i++) // Validate attribute tokens.
            {
              if(RE.attr.test(attrs[attr_i]))
              {
                attrs[attr_i] = new Array(RegExp.$1, RegExp.$2);
              }
              else
              {
                attrsFound = false;
                break;
              }
            }
          }
          
          // Check that we have attributes if the tag requires them (this is the first check step, the second is in the attrs-loop):
          if(!attrsFound && !isEndTag && E.requiresAttrs)
          {
            this.err(LANG__err_req_attrs_missing, [tagName]);
            break COMPILE_LOOP;
          }
          
          // If we have a body start-tag we can continue.
          if(this.hasBodyStart)
          {
            // Check that we're not opening another body-tag:
            if(tagName == 'body' && !isEndTag)
            {
              this.err(LANG__err_too_many_body_start, ['body']);
              break;
            }
            
            // Start-tag found:
            if(!isEndTag && this.openTag(E))
            {
              // Check that we have a consequence with isEmpty and E.isEmpty (either both fale or both true).
              if((!E.isEmpty && isEmpty) || (E.isEmpty && !isEmpty)) // This should work as the xor-operator...
              {
                if(!E.isEmpty)
                  this.err(LANG__err_tag_not_empty, new Array(tagName));
                else
                  this.err(LANG__err_tag_empty, new Array(tagName));
                break;
              }
              
              tagFound = true;
              
              if(attrsFound)
              {
                // We have attributes to parse.
                
                for(var attr_i=0, attr_len=attrs.length; attr_i<attr_len; attr_i++)
                {
                  attrNam = attrs[attr_i][0];
                  attrVal = attrs[attr_i][1];
                  
                  // Check that we're not using the same attribute again:
                  if(usedAttrs.get(attrNam))
                  {
                    this.err(LANG__err_err_attr_used, [tagName,attrNam]);
                    break COMPILE_LOOP;
                  }
                  else
                  {
                    usedAttrs.set(attrNam, true);
                  }
                  
                  switch(tagName)
                  {
                    // Skip elements where we don't allow attributes:
                    case 'b':
                    case 'i':
                    case 's':
                    case 'u':
                    case 'center':
                    case 'right':
                    case 'big':
                    case 'br':
                      break;
                    
                    case 'font':
                      switch(attrNam)
                      {
                        case 'color':
                          if(RE.numSignHexColor.test(attrVal))
                          {
                            attrList.push(new Array('style', 'color:'+attrVal));
                          }
                          else
                          {
                            this.err(LANG__err_attr_val_invalid, [tagName,attrVal,attrNam]);
                            break COMPILE_LOOP;
                          }
                          break;
                        
                        default:
                          this.err(LANG__err_unknown_attr, [tagName,attrNam]);
                          break COMPILE_LOOP;
                      }
                      
                      // Check that we have all required attributes:
                      switch(attrNam)
                      {
                        case 'color': // Add other required attributes in separate cases here.
                        // ...
                          break;
                        default:
                          this.err(LANG__err_req_attrs_missing, [tagName]);
                          break COMPILE_LOOP;
                      }
                      break;
                    
                    case 'a':
                      switch(attrNam)
                      {
                        case 'href':
                          // Check that href-value is a valid url and that the url isn't restricted.
                          if(RE.url.test(attrVal) && !RE.restrictedUrl.test(attrVal))
                          {
                            if(isPreview)
                            {
                              attrList.push(new Array('href', 'javascript:void(0);'));
                            }
                            else
                            {
                              attrList.push(new Array('href', attrVal));
                              attrList.push(new Array('target','_new'));
                            }
                            ahref_attr = attrVal;
                          }
                          // Error: break main loop and report error.
                          else
                          {
                            this.err(LANG__err_attr_val_invalid, [tagName,attrVal,attrNam]);
                            break COMPILE_LOOP;
                          }
                          break;
                        
                        default:
                          this.err(LANG__err_unknown_attr, [tagName,attrNam]);
                          break COMPILE_LOOP;
                      }
                      
                      // Check that we have all required attributes:
                      switch(attrNam)
                      {
                        case 'href': // Add other required attributes in separate cases here.
                        // ...
                          break;
                        default:
                          this.err(LANG__err_req_attrs_missing, [tagName]);
                          break COMPILE_LOOP;
                      }
                      break;
                    
                    case 'user':
                      switch(attrNam)
                      {
                        case 'name':
                          // Chech that we have a valid username.
                          if(RE_USERNAME.test(attrVal))
                          {
                            if(isPreview)
                              attrList.push(new Array('href', 'javascript:void(0);'));
                            else
                              attrList.push(new Array('href','/usersrch.php?unam='+attrVal)); // Non-working url right now...
                            username_attr = attrVal;
                          }
                          // Error: break main loop and report error.
                          else
                          {
                            this.err(LANG__err_attr_val_invalid, [tagName,attrVal,attrNam]);
                            break COMPILE_LOOP;
                          }
                          break;
                        
                        default:
                          this.err(LANG__err_unknown_attr, [tagName,attrNam]);
                          break COMPILE_LOOP;
                      }
                      
                      // Check that we have all required attributes:
                      switch(attrNam)
                      {
                        case 'name': // Add other required attributes in separate cases here.
                        // ...
                          break;
                        default:
                          this.err(LANG__err_req_attrs_missing, [tagName]);
                          break COMPILE_LOOP;
                      }
                      break;
                    
                    default:
                      switch(attrNam)
                      {
                        case 'id':
                          if(RE.elemIdRestr.test(attrVal))
                          {
                            if(this.registerId(attrVal))
                            {
                              // The ID has an object related to it.
                              if(EH.Elems.isset(attrVal))
                              {
                                E_id = EH.Elems.get(attrVal);
                                
                                // Set E as extended element for ID-obj:
                                E_id.setExtendedObj(E);
                                
                                // Make the id-element inherit its properties fron its parent (class-element, or actually tag element; <class_name>),
                                // although we don't want to inherit anything if the properties of the ID-element has been changed before.
                                if(!E_id.touched)
                                  E_id.cssProps = new ObjClone(E.cssProps); // Copy css properties from parent element.
                                
                                if(isPreview)
                                  __ref_idElem = E_id; // Save ID-Element reference for further use below.
                                
                                hasCssId = true; // Now we know that no class attribute should be added.
                              }
                              
                              // Register id attribute.
                              attrList.push(new Array('id', CSS_RULE_PREFIX + attrVal));
                              
                              // Save ID temporarily for possible use below.
                              id_attr = attrVal;
                            }
                          }
                          else
                          {
                            this.err(LANG__err_attr_val_invalid, [tagName,attrVal,attrNam]);
                            break COMPILE_LOOP;
                          }
                          break;
                      }
                  }
                }
              }
              
              // Outside attribute loop, means we do not have repeats of this procedure for current tag.
              if(isPreview) // Add attributes used in preview...
              {
                // Add an ID that will identify this element as a CML element:
                attrList.push(new Array('id', 'CML_'+i));
                
                var ecbb = 'event.cancelBubble=true;'; // Shortcut...
                
                switch(tagName)
                {
                  // Skip non-relevant elements.
                  case 'b':
                  case 'i':
                  case 's':
                  case 'u':
                  case 'center':
                  case 'right':
                  case 'big':
                  case 'br':
                    if(!isEmpty)
                      attrList.push(new Array('onclick', 'event.cancelBubble=true;'));
                  case 'font':
                    break;
                  
                  case 'user':
                    if(username_attr)
                    {
                      attrList.push(new Array('onmouseover', "this.style.cssText=EH.Elems.get('user:hover').getCssStr();"+
                                                             "pe_msg(LANG__link_to_user,['"+username_attr+"']);"+ecbb));
                      attrList.push(new Array('onmouseout', "this.style.cssText=EH.Elems.get('user').getCssStr();pe_msg();"+ecbb));
                    }
                    attrList.push(new Array('onclick',     'this.blur();'));
                    break;
                  
                  case 'a':
                    if(ahref_attr)
                    {
                      attrList.push(new Array('onmouseover', "this.style.cssText=EH.Elems.get('a:hover').getCssStr();"+
                                                             "pe_msg(LANG__link_to_exturl,['"+ahref_attr+"']);"+ecbb));
                      attrList.push(new Array('onmouseout',  "this.style.cssText=EH.Elems.get('a').getCssStr();pe_msg();"+ecbb));
                    }
                    attrList.push(new Array('onclick',     'this.blur();'));
                    break;
                  
                  default:
                    if(E_id != null)
                    {
                      if(E_id.getCssProp('position') == 'absolute')
                      {
                        attrList.push(new Array('onmouseover', ecbb+"Box.detectOver(this,'"+id_attr+"');"));
                      }
                      else
                      {
                        attrList.push(new Array('onmouseover', ecbb+"pe_msg(LANG__pe_msg_mouse_over_obj_id, ['"+id_attr+"']);"));
                      }
                      attrList.push(new Array('onmouseout', "pe_msg();"+ecbb));
                      attrList.push(new Array('onclick', "EH.updateGUI('"+id_attr+"');"+ecbb));
                    }
                    else
                    {
                      if(E.getCssProp('position') == 'absolute')
                      {
                        attrList.push(new Array('onmouseover', ecbb+"Box.detectOver(this,'"+tagName+"');"));
                      }
                      else
                      {
                        attrList.push(new Array('onmouseover', ecbb+"pe_msg(LANG__pe_msg_mouse_over_obj_class, ['"+tagName+"']);"));
                      }
                      attrList.push(new Array('onclick', ecbb+"EH.updateGUI('"+tagName+"');"));
                      attrList.push(new Array('onmouseout', ecbb+"pe_msg();"));
                    }
                }
              }
              else
              {
                switch(tagName)
                {
                  case 'big':
                  case 'br':
                    break;
                  
                  default:
                    if(!hasCssId)
                      attrList.push(new Array('class', CSS_RULE_PREFIX + tagName));
                }
              }
              
              
              // Build attribute string.
              while((attr = attrList.pop()) !== false)
                attrStr += ' ' + attr[0] + '="' + attr[1] + '"';
              
              // Store as decompiled content token.
              this.prepContentToks.push(new Array(E, false, attrStr, __ref_idElem));
              compiled_str += isPreview ? E.compileStartElem(attrStr, true, __ref_idElem) : E.compileStartElem(attrStr, false);
            }
            // End-tag found:
            else if(this.closeTag(E) && !E.isEmpty)
            {
              tagFound = true;
              compiledElem = E.compileEndElem();
              this.prepContentToks.push(new Array(compiledElem, true, null, null));
              compiled_str += compiledElem;
              
              if(tagName == 'body')
              {
                this.hasBodyEnd = true;
                break;
              }
            }
          }
          
          // Check if we have a body start-tag:
          else if(!isEndTag && tagName == 'body')
          {
            this.openTag(E);
            
            attrStr = ' class="'+CSS_RULE_PREFIX+'body"';
            if(isPreview)
            {
              attrStr += ' id="CML_0"'+
                         ' onclick="'+"EH.updateGUI"+'(\''+tagName+'\');"'+
                         ' onmouseover="'+"pe_msg"+'(LANG__pe_msg_mouse_over_obj_class, [\''+tagName+'\']);'+ecbb+'"'+
                         ' onmouseout="'+"pe_msg"+'();'+ecbb+'"';
            }
            
            this.prepContentToks.push(new Array(E, false, attrStr, null));
            compiled_str += E.compileStartElem(attrStr, isPreview);
            this.hasBodyStart = true;
            
            continue;
          }
          
          else
          {
            this.err(LANG__err_missing_body_start, ['body']);
            continue;
          }
        }
      }
      
      // Plain text.
      if(!tagFound && this.hasBodyStart)
      {
        var tmp = pe_htmlentities(t);
        this.prepContentToks.push(new Array(tmp, true, null, null));
        compiled_str += tmp;
      }
    }
    
    if(!this.hasBodyStart)
      this.err(LANG__err_missing_body_start, ['body']);
    else if(!this.hasBodyEnd)
      this.err(LANG__expecting_body_end, ['body']);
    
    return compiled_str;
  };
  
  this.DataCont.onkeyup = function(e) { updateDataCont(this); pcpl(new Event(e)); };
  updateDataCont(this.DataCont);
  
  return this;
}

function updateDataCont(o)
{
  var lineHeight = 12;
  var newHeight = o.value.split('\n').length * lineHeight;
  o.style.height = (newHeight > lineHeight*3 ? newHeight : lineHeight*3) + 'px';
}

/* ###
 * ### WYSIWYG-functions:
 * ### */


/**
 * Object BoxMover()
 * !! Assumes that the one and only instance of this class is called 'Box'.
 */
function BoxMover()
{
  this.initX;
  this.initY;
  
  this.offsetX;
  this.offsetY;
  this.objX;
  this.objY;
  this.pX;
  this.pY;
  
  this.Elem;
  this.HtmlObj;
  this.doMove = false;
  this.hasMoved = false;
  this.isOver = false;
  this.touchesFrame = false;
  
  this.minX = -1;
  this.minY = -1;
  this.maxX;
  this.maxY;
  this.skipX = false; // Too large width and height controllers.
  this.skipY = false; // - " -
  
  this.ccElem = d.createElement('div'); // @var ELEM  - Content Cover Element.
  
  this.detectOver = function(o, cml_nam)
  {
    if(this.isOver)
      return;
    
    this.isOver = true;
    this.HtmlObj = o;
    
    // Calculate max X and Y positions for the current element.
    this.maxX = parseInt(this.HtmlObj.parentNode.offsetWidth)+1-this.HtmlObj.offsetWidth;
    this.maxY = parseInt(this.HtmlObj.parentNode.offsetHeight)+1-this.HtmlObj.offsetHeight;
    
    this.Elem = EH.Elems.get(cml_nam);
    // Check if we need to temporarily set width and/or height:
    var width = this.Elem.getCssProp('width'), height = this.Elem.getCssProp('height');
    if(!width || !height || width == 'auto' || height == 'auto')
    {
      // Calculate "padding+border"-offset for firefox:
      var offset = 0;
      if(!B.ie)
      {
        var ofstPadding, ofstBorder;
        if(ofstPadding = this.Elem.getCssProp('padding')) offset += 2*parseInt(ofstPadding);
        if(ofstBorder = this.Elem.getCssProp('border-width')) offset += 2*parseInt(ofstBorder);
      }
      
      width = this.HtmlObj.offsetWidth;
      height = this.HtmlObj.offsetHeight;
      
      this.HtmlObj.style.width = (width - offset) + 'px';
      this.HtmlObj.style.height = (height - offset) + 'px';
      
      // Update Element's cssProps:
      this.Elem.setCssProp('width', width + 'px');
      this.Elem.setCssProp('height', height + 'px');
    }
    
    // Clone HtmlObj and setup its clone:
    with(this.ccElem)
    {
      removeAttribute('id');
      if(B.ie)
      {
        clearAttributes();
        if(this.Elem.type == 'id') onmouseover = function() { pe_msg(LANG__pe_msg_mouse_over_obj_id, [cml_nam]); event.cancelBubble=true; };
        else onmouseover = function() { pe_msg(LANG__pe_msg_mouse_over_obj_class, [cml_nam]); event.cancelBubble=true; };
        /* @BUG: stressing the onmouseout eventhandler below sometimes makes IE crash. */
        onmouseout = function() { Box.detectOut(event.toElement.id.substr(0,3) != 'CML'); event.cancelBubble=true; };
        onmousedown = function() { Box.startMove(event); event.cancelBubble=true; };
        onmouseup = function() { Box.endMove(); event.cancelBubble=true; };
        onclick = function() { event.cancelBubble=true; };
      }
      else
      {
        removeAttribute('onmouseover');
        removeAttribute('onmouseout');
        removeAttribute('onmousedown');
        removeAttribute('onmouseup');
        removeAttribute('onclick');
        removeAttribute('style');
        setAttribute('onmouseover', "pe_msg(LANG__pe_msg_mouse_over_obj_"+this.Elem.type+", ['"+cml_nam+"']);event.cancelBubble=true;");
        setAttribute('onmouseout', "Box.detectOut();event.cancelBubble=true;");
        setAttribute('onmousedown', "Box.startMove(event);event.cancelBubble=true;");
        setAttribute('onmouseup', "Box.endMove();event.cancelBubble=true;");
        setAttribute('onclick', 'event.cancelBubble=true;');
      }
      
      style.cssText = this.Elem.getCssStr();
      style.cssText += ';padding:0px;border:0px;z-index:1000;background:#FFF582;' + (B.ie ? 'filter:alpha(opacity=30)' : 'opacity:0.3');
      style.cssText += ';height:'+this.HtmlObj.offsetHeight+'px;width:'+this.HtmlObj.offsetWidth+'px';
    }
    
    this.HtmlObj.parentNode.appendChild(this.ccElem);
  };
  
  this.detectOut = function(force)
  {
    if(!this.isOver || (this.doMove && !force))
      return;
    
    this.isOver = false;
    this.HtmlObj.parentNode.removeChild(this.ccElem);
    
    if(this.touchesFrame)
      this.endMove();
  };
  
  this.startMove = function(e)
  {
    if(!this.doMove)
    {
      var Ev = new Event(e);
      Ev.ref.cancelBubble = true;
      
      this.objX = Ev.objPosX;
      this.objY = Ev.objPosY;
      
      this.initX = parseInt(this.Elem.getCssProp('left'));
      this.initY = parseInt(this.Elem.getCssProp('top'));
      
      // A different (and the best) way to calculate the box position in this case (which is relative to the starting point):
      this.offsetX = this.initX - (Ev.pageOffsetX);
      this.offsetY = this.initY - (Ev.pageOffsetY);
      
      this.doMove = true;
      this.hasMoved = false;
      
      EH.updateGUI(this.Elem.name, true);
    }
    else
    {
      this.endMove();
    }
  };
  
  this.moveTo = function(x,y)
  {
    if(this.doMove)
    {
      // Check if we're touching the frame:
      var suggestedX = this.offsetX + x;
      var suggestedY = this.offsetY + y;
      this.touchesFrame = (suggestedX <= this.minX || suggestedX >= this.maxX || suggestedY >= this.maxY || suggestedY <= this.minY);
      
      // Calculate position without exceeding the max/min-frames.
      this.pX = Math.min(Math.max(this.minX, this.maxX), Math.max(this.minX, suggestedX));
      this.pY = Math.min(Math.max(this.minY, this.maxY), Math.max(this.minY, suggestedY));
      
      // Update form inputs:
      if(!this.skipX)
      {
        EH.F_Props.get('left').set(this.pX);
        this.ccElem.style.left = this.HtmlObj.style.left = (this.pX) + 'px'; // Updating TWO objects.
      }
      if(!this.skipY)
      {
        EH.F_Props.get('top').set(this.pY);
        this.ccElem.style.top = this.HtmlObj.style.top = (this.pY) + 'px'; // Updating TWO objects.
      }
      pe_msgUpdateWith('('+this.pX+', '+this.pY+')');
      
      this.hasMoved = true;
    }
  };
  
  this.endMove = function()
  {
    if(!this.doMove) // Make sure this function is performed once.
      return;
    
    this.doMove = false;
    
    // Change position if box was moved.
    if(this.hasMoved)
    {
      this.Elem.setCssProp('top', this.pY + 'px');
      this.Elem.setCssProp('left', this.pX + 'px', true); // We must update css string!
      EH.F_Props.get('top').set(this.pY);
      EH.F_Props.get('left').set(this.pX);
    }
  };
}

/**
 * void requestSave()
 * Submits the presentation-save form if everything is ok.
 */
function requestSave()
{
  f = d.getElementById('pe_save');
  if(!CMLC.hasError())
  {
    f.r_pres.value = d.getElementById('pe_data').value;
    f.r_css.value = EH.exportRawCss();
    f.c_pres.value = CMLC.makeCompile(false);
    f.c_css.value = 'msie=' + EH.compileStyleSheet('msie') + '|gecko=' + EH.compileStyleSheet('gecko');
    f.submit();
  }
  else
  {
    alert(LANG__err_errors_cant_save);
    return false;
  }
}