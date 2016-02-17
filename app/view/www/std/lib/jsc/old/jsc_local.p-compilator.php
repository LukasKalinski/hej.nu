<?php
require_once('/home/h/hej/_include/system/system.conditions.php');
require_once('/home/h/hej/_include/system/system.session.php');
require_once('/home/h/hej/_include/system/system.globals.php');

require_session('__USER_SESSION');
?>
//<script>
/**
 * COPYRIGHT - Copyright 2002. All rights reserved. - This notice must remain untouched.
 * File: jsc_local.p-compilator.php
 * 
 * --- Description ---
 * The first section is the javascript compilator for user presentations.
 * The second section contains a class for the color-handler.
 * 
 * --- Copyright notice ---
 * Copying, modifying, using or distributing this script without the authors permission is an act of a true lamer.
 * Want to use this script? Fine, just ask. Although the purposes must be non-commercial and it _must_not_ be used 
 * for competition with this page.
 * 
 * Last modified: 2003.10.10
 * Author: Lukas Kalinski (lukas@cylab.se)
*/

var TPL_color = "<?=REGEXP__COLOR?>";
var TPL_border = "<?=REGEXP__BORDER?>";
var TPL_url = "http://{1}[_a-z0-9./-:?&~,#}{)(=]{5,}";
var RE_url_forbidden = "<?=WWW_ROOT?>".replace(/(http:\/\/)/gi, "");
RE_url_forbidden = RE_url_forbidden.replace(/\/$/gi, "");
RE_url_forbidden = RE_url_forbidden.replace(/(\.)/gi, "\\.");
RE_url_forbidden = new RegExp(RE_url_forbidden, "gi");

function TagProp(pId, RE, prop, propDefault)
{
	this.propID = pId;
	this.RE = new RegExp(RE, "gi");
  this.prop = (prop != null ? prop : "");
  this.propDefault = (propDefault != null ? propDefault : "");
	return this;
}

function Tag(RE_startTag, tagProps, RE_endTag, startTag, endTag, standAlone, extras)
{
	this.RE_startTag = new RegExp(RE_startTag, "gi");
	this.tagProps = tagProps;
	this.RE_endTag = new RegExp(RE_endTag, "gi");
	this.startTag = startTag;
	this.endTag = endTag;
	this.tagOpen = false;
	this.standAlone = standAlone;
	this.extras = extras;
	return this;
}

function Presentation()
{
	this.tagAlias = new Array("B","I","U","S","BIG","CENTER","RIGHT","USER","PHOTO","A","FONT","BOX","SWITCH","HR","BR");
	this.openTags = new Array();  // Store non-closed tags
	this.pTag = new Array();      // Tag properties
  
	this.pBgColor = "#A1915D";
  this.nLink = "";
  this.hLink = "";
  
	this.pCompiled = "";
	this.pHTML = new Array();
	this.pTEXT = new Array();
	
	this.setupBody = function(pBgColor)
	{
		var REc = new RegExp(TPL_color, "gi");
		if(pBgColor.match(REc)) { this.pBgColor = pBgColor; }
	}
  
  this.setupLinks = function(nColor, nStyle, nDecoration, nWeight, nBgColor, hColor, hStyle, hDecoration, hWeight, hBgColor)
  {
    
    this.nLink = "";
    this.nLink += "color:"           + nColor      + ";";
		this.nLink += "font-style:"      + nStyle      + ";";
		this.nLink += "text-decoration:" + nDecoration + ";";
		this.nLink += "font-weight:"     + nWeight     + ";";
		this.nLink += "background-color:"+ (nBgColor.match(RE_color) ? nBgColor : "transparent")    + ";";
    
    this.hLink = "";
    this.hLink += "color:"           + hColor      + ";";
		this.hLink += "font-style:"      + hStyle      + ";";
		this.hLink += "text-decoration:" + hDecoration + ";";
		this.hLink += "font-weight:"     + hWeight     + ";";
		this.hLink += "background-color:"+ (hBgColor.match(RE_color) ? hBgColor : "transparent")    + ";";
  }
	
	this.getPresentation = function()
	{
		return this.pCompiled;
	}
	
	this.openTag = function(tag, loop_id, decompiled)
	{
		this.openTags[tag] = new Array(loop_id, decompiled);
	}
	this.closeTag = function(tag) { this.openTags[tag] = false; }
	
	this.secureChars = function(str)
	{
		var RE_doubleQuote = new RegExp('"', "g");
		str = str.replace(/</g, "&#60;");
		str = str.replace(/>/g, "&#62;");
		str = str.replace(RE_doubleQuote, "&#34;");
		return str;
	}
	
	this.setupCompilator = function()
	{
		this.pCompiled = "";
		this.openTags = new Array();
		this.pTag["BR"]					= new Tag('<BR>', false, false, '<BR>', false, false, false);
		this.pTag["B"]					= new Tag('<B>', false, '</B>', '<B>', '</B>', false, false);
		this.pTag["I"]					= new Tag('<I>', false, '</I>', '<I>', '</I>', false, false);
		this.pTag["U"]					= new Tag('<U>', false, '</U>', '<U>', '</U>', false, false);
		this.pTag["S"]    			= new Tag('<S>', false, '</S>', '<S>', '</S>', false, false);
		this.pTag["BIG"]				= new Tag('<BIG>', false, '</BIG>', '<BIG>', '</BIG>', false, false);
		this.pTag["CENTER"]			= new Tag('<CENTER>', false, '</CENTER>', '<DIV align="center">', '</DIV>', false, false);
		this.pTag["RIGHT"]			= new Tag('<RIGHT>', false, '</RIGHT>', '<DIV align="right">', '</DIV>', false, false);
		this.pTag["FONT"]				= new Tag
                                    (
																		'<FONT.*>',
																		new Array(new TagProp(0, ' color="('+TPL_color+')"', 'color="[p]"', 'color="#000000"')),
																		'</FONT>',
																		'<FONT [0]>',
																		'</FONT>',
																		false,
																		false
																		);
		this.pTag["A"]					= new Tag
                                    (
																		'<A href="'+TPL_url+'">',
																		new Array(new TagProp(0, ' href="('+TPL_url+')"', "[p]", "about:blank")),
																		'</A>',
																		'<A HREF="[0]" target="_new" style="'+this.nLink+'" onmouseover="this.style.cssText=\''+this.hLink+'\';" onmouseout="this.style.cssText=\''+this.nLink+'\';">',
																		'</A>',
																		true,
																		'tempStartTag = tempStartTag.replace(RE_url_forbidden, "");'
																		);
    this.pTag["USER"]			= new Tag
                                    (
																		'<USER>',
																		false,
																		'</USER>',
                                    '<A href="javascript:alert(\'Länk till användaren.\')" style="'+this.nLink+'" '+
                                    'onmouseover="this.style.cssText=\''+this.hLink+'\';" '+
                                    'onmouseout="this.style.cssText=\''+this.nLink+'\';">',
																		'</A>',
																		true,
                                    false
																		);
		this.pTag["PHOTO"] = new Tag
                                    (
																		'<PHOTO.*>',
																		new Array
																		(
    new TagProp(0, ' size="small"',                   'width:40px;height:52px;',      'width:100px;height:130px;'),
		new TagProp(1, ' bordercolor="('+TPL_color+')"',  'border-color:[p];color:[p];',  'border-color:#000000;color:#000000;'),
		new TagProp(2, ' borderwidth="([0-9]{1})"',       'border-width:[p]px;',          'border-width:1px;'),
		new TagProp(3, ' borderstyle="('+TPL_border+')"', 'border-style:[p];',            'border-style:solid;'),
		new TagProp(4, ' alt="([^<>"]{1,30})"',           '[p]',                 ''),
		new TagProp(5, ' top="([0-9]{1,4})"',             'top:[p]px;',          ''),
		new TagProp(6, ' left="([0-9]{1,3})"',            'left:[p]px;',         ''),
    new TagProp(7, ' position="(relative|absolute)"', 'position:[p];',       ''),
    new TagProp(8, ' display="(inline|block)"',       'display:[p];',        ''),
    new TagProp(9, ' user="([^<>"]*)"',               'Foto-länk till [p].', 'Tom länk.\\nDu måste ange användaren som ska länkas.')
																		),
																		false,
                                    '<div style="[0][5][6][7][8]" title="[4]" '+
                                    'onclick="alert(\'[9]\');" style="cursor:<?=CURSOR__POINTER?>;">'+
                                    '<div style="[1][2][3]width:100%;height:100%;" align="center">'+
                                    '<div style="background:#0F0F0F;width:100%;height:100%;<?
                                    if(browser('ie')) echo 'filter: progid:DXImageTransform.Microsoft.Alpha(opacity=40);';
                                    else              echo '-moz-opacity: 40%;';
                                    ?>">'+
                                    '<div style="position:relative;[1]top:40%;" class="text">Foto</div>'+
                                    '</div></div></div>',
																		false,
																		false,
                                    false
																		);
    this.pTag["HR"] 				= new Tag
																		(
																		'<HR.*>',
																		new Array
																		(
																		new TagProp(0, ' color="('+TPL_color+')"',        'background:[p];', 'background:#000000;'),
																		new TagProp(1, ' width="([0-9]{1,3})"',           'width:[p]px;',    'width:100%;'),
																		new TagProp(2, ' height="([0-9]{1})"',            'height:[p]px;',   'height:1px;'),
																		new TagProp(3, ' top="([0-9]{1,3})"',             'top:[p]px;',      'top:8px;'),
																		new TagProp(4, ' left="([0-9]{1,3})"',            'left:[p]px;',     'left:8px;'),
																		new TagProp(5, ' position="(relative|absolute)"', 'position:[p];',   'position:static;'),
																		new TagProp(6, ' width="([0-9]{1,3}%)"',          'width:[p];',      '')
																		),
																		false,
																		'<table cellspacing="0" cellpadding="0" border="0" style="[5][0][1][3][4][6]">'+
                                    '<tr><td style="[2]"></td></tr></table>',
                                    false,
																		false,
																		false,
																		false
																		);
		this.pTag["BOX"]				= new Tag
																		(
																		'<BOX.*>',
																		new Array
																		(
        								new TagProp(0,  ' bgcolor="('+TPL_color+')"',      'background:[p];',     ''),
        								new TagProp(1,  ' bordercolor="('+TPL_color+')"',  'border-color:[p];',   'border-color:#000000;'),
        								new TagProp(2,  ' borderwidth="([0-9]{1})"',       'border-width:[p]px;', 'border-width:1px;'),
        								new TagProp(3,  ' borderstyle="('+TPL_border+')"', 'border-style:[p];',   'border-style:solid;'),
        								new TagProp(4,  ' width="([0-9]{1,3})"',           'width:[p]px;',        ''),
        								new TagProp(5,  ' height="([0-9]{1,4})"',          'height:[p]px;',       ''),
        								new TagProp(6,  ' top="([0-9]{1,4})"',             'top:[p]px;',          ''),
        								new TagProp(7,  ' left="([0-9]{1,3})"',            'left:[p]px;',         ''),
        								new TagProp(8,  ' padding="([0-9]{1})"',           'padding:[p]px;',      'padding:5px;'),
        								new TagProp(9,  ' zindex="([0-9]{1,2}|auto)"',     'z-index:[p];',        ''),
        								new TagProp(10, ' align="(left|center|right)"',    ' align="[p]"',        ''),
        								new TagProp(11, ' position="(relative|absolute)"', 'position:[p];',       'position:static;'),
        								new TagProp(12, ' width="([0-9]{1,3}%)"',          'width:[p];',          ''),
        								new TagProp(13, ' height="([0-9]{1,3}%)"',         'height:[p];',         ''),
        								new TagProp(14, ' id="([a-z0-9]{1,20})"',          ' id="uDef[p]"',       ''),
        								new TagProp(15, ' visibility="(visible|hidden)"',  'visibility:[p];',     'visibility:visible;'),
        								new TagProp(16, ' display="(inline|block)"',       'display:[p];',        '')
																		),
																		'</BOX>',
																		'<div[14] style="[11][15][16][0][6][7][9][4][5][12][13]">'+
                                    '<div class="text"[10] style="[1][2][3][8]">',
																		'</div></div>',
																		false,
																		false
																		);
    this.pTag["SWITCH"]		= new Tag
																		(
																		'<SWITCH.*>',
																		new Array
																		(
        								new TagProp(0, ' show="([a-z0-9]*|_all_)"',   "'[p]'", "false"),
        								new TagProp(1, ' hide="([a-z0-9]*|_all_)"',   "'[p]'", "false"),
        								new TagProp(2, ' toggle="([a-z0-9]*|_all_)"', "'[p]'", "false")
																		),
																		'</SWITCH>',
																		'<A href="javascript:box([0],[1],[2]);" style="'+this.nLink+'" '+
                                    'onmouseover="this.style.cssText=\''+this.hLink+'\';" '+
                                    'onmouseout="this.style.cssText=\''+this.nLink+'\';">',
																		'</A>',
																		true,
																		false
																		);
	}
	
	this.pPreCompile = function(pALL)
	{
    pALL = pALL.replace(/\|/gi, "&#124;");    // Secure the pipe which is used for separation in the compilator and mysql
		pALL = pALL.replace(/\n|\r|\t/gi, " ");   // Replace all these annoying escape chars with simple spaces
		pALL = pALL.replace(/<{1}/gi, "|<");      // Put a pipe in front of every <
		pALL = pALL.replace(/>{1}/gi, ">|");      // Put a pipe after every >
		pALL = pALL.replace(/\\\|\\\|/gi, "|");   // Remove double-pipes
		
		var RE_HTML = new RegExp("^<[^<>]{1,}>$", "g");
		
		var pALL = pALL.split("|");
		var pHTML = new Array();
		var pTEXT = new Array();
		for(var i=0; i<pALL.length; i++)
		{
			if(pALL[i].match(RE_HTML))
			{
				pHTML[i] = pALL[i];
				pTEXT[i] = "";
			}
			else
			{
				pHTML[i] = "";
				pTEXT[i] = pALL[i];
			}
		}
		return new Array(pHTML.join("|"), this.secureChars(pTEXT.join("|")));
	}
	
	this.pCompile = function(pALL)
	{
		this.setupCompilator();
		var pPreCompiled = this.pPreCompile(pALL);
		var pHTML = pPreCompiled[0].split("|");
		var pTEXT = pPreCompiled[1].split("|");
		var tagValidated = false;
		var standAloneTagOpen = false;
		var tempStartTag = "";
    
		pHTML_LOOP:
		for(var i=0; i<pHTML.length; i++)
		{
			if(pHTML[i] != "")
			{
				tagValidated = false;
				TAG_LOOP:
				for(var j=0; j<this.tagAlias.length; j++)
				{
					if(!this.pTag[this.tagAlias[j]].tagOpen && pHTML[i].match(this.pTag[this.tagAlias[j]].RE_startTag) && standAloneTagOpen === false)
					{
						tempStartTag = this.pTag[this.tagAlias[j]].startTag;
            
						if(this.pTag[this.tagAlias[j]].tagProps !== false)
						{
              var PROP = new RegExp("\\[p\\]", "gi");
              // Scan for properties
							PROP_LOOP:
							for(var k=0; k<this.pTag[this.tagAlias[j]].tagProps.length; k++)
							{
								var re = new RegExp("\\["+k+"\\]", "gi");
								if(pHTML[i].match(this.pTag[this.tagAlias[j]].tagProps[k].RE))
								{
									tempStartTag = tempStartTag.replace(re, this.pTag[this.tagAlias[j]].tagProps[k].prop.replace(PROP, RegExp.$1));
									tagValidated = true;
									continue;
								}
                else
                {
									tempStartTag = tempStartTag.replace(re, this.pTag[this.tagAlias[j]].tagProps[k].propDefault);
									tagValidated = true;
									continue;
								}
							}
						}
						
						if(this.pTag[this.tagAlias[j]].extras !== false) { eval(this.pTag[this.tagAlias[j]].extras); }
						if(this.pTag[this.tagAlias[j]].standAlone) { standAloneTagOpen = this.tagAlias[j]; }
						if(this.pTag[this.tagAlias[j]].endTag !== false)
						{
							this.pTag[this.tagAlias[j]].tagOpen = true;
							this.openTag(this.tagAlias[j], i, pHTML[i]);
						}
						
						pHTML[i] = tempStartTag;
						tagValidated = true;
						break;
					}
					else if(this.pTag[this.tagAlias[j]].tagOpen && this.pTag[this.tagAlias[j]].RE_endTag !== false && pHTML[i].match(this.pTag[this.tagAlias[j]].RE_endTag))
					{
						if(standAloneTagOpen == this.tagAlias[j]) { standAloneTagOpen = false;}
						if(!standAloneTagOpen)
						{
							pHTML[i] = this.pTag[this.tagAlias[j]].endTag;
							this.pTag[this.tagAlias[j]].tagOpen = false;
							this.closeTag(this.tagAlias[j]);
							tagValidated = true;
						}
						else { tagValidated = false; }
						break;
					}
				}
				if(!tagValidated) { pHTML[i] = this.secureChars(pHTML[i]); }
			}
		}
		
		for(var i=0; i<this.tagAlias.length; i++)
		{
			if(typeof(this.openTags[this.tagAlias[i]]) != "undefined" && this.openTags[this.tagAlias[i]] !== false)
			{
				pHTML[this.openTags[this.tagAlias[i]][0]] = this.secureChars(this.openTags[this.tagAlias[i]][1]);
			}
		}
		
		this.pCompiled += '<div style="position:absolute;top:8px;left:8px;width:503px;height:100%;" class="text">';
		for(var i=0; i<pHTML.length; i++) this.pCompiled += pHTML[i] + pTEXT[i];
		this.pCompiled += '</div>';
	}
	
	return this;
}

function pEdit()
{
  f = getObjRef('setPresentation');
  
  var RE_color = new RegExp("^"+TPL_color+"$", "gi");
  
  this.p = new Presentation();
  this.RTC = true;
  this.pHeight = 150;
  this.pBgColor = (f.pBgColor.value.match(RE_color) ? f.pBgColor.value : this.p.pBgColor);
  
  this.nLink            = "";
  this.nLinkColor       = "";
  this.nLinkStyle       = "";
  this.nLinkDec         = "";
  this.nLinkBgColor     = "";
  
  this.hLink            = "";
  this.hLinkColor       = "";
  this.hLinkStyle       = "";
  this.hLinkDec         = "";
  this.hLinkBgColor     = "";
  
  this.pALL = "";
  
  this.compileAll = function()
  {
    var startTS = new Date();
		startTS = startTS.getTime();
    
    this.setBgColor();
    this.setLinks();
    
		this.p.pCompile(this.pALL);
    
    getObjRef('pPreview3').innerHTML = this.p.getPresentation();
    
		var endTS = new Date();
		endTS = endTS.getTime();
    
    getObjRef('pLoadTime').innerHTML = "";
    if(((endTS-startTS)/1000) > 0.5)
    {
      getObjRef('pLoadTime').innerHTML = 'Du borde stänga av realtidsöversättningen, det börjar gå segt.<br>';
    }
		getObjRef('pLoadTime').innerHTML += "Presentationen tog "+((endTS-startTS)/1000)+" sekunder att generera.";
  }
  
  this.setPresentation = function()
  {
    this.pALL = f.pALL.value;
  }
  
  this.refreshPresentation = function(ev)
  {
    if(ev == null) ev = event;
    // Skip unnecessary compiling (when user press the arrow-keys or other non-input keys)
    if(ev.keyCode > 40 || ev.keyCode == 32 || ev.keyCode == 8 || ev.keyCode == 13)
    {
      this.setPresentation();
      if(this.RTC) { this.compileAll(); }
    }
  }
  
  this.setBgColor = function()
  {
    if(f.pBgColor.value.match(RE_color)) this.pBgColor = f.pBgColor.value;
    this.p.setupBody(this.pBgColor);
  }
  
  this.refreshBgColor = function()
  {
    this.setBgColor();
    getObjStyleRef('pPreview2').background = this.p.pBgColor;
  }
  
  this.setHeight = function()
  {
    h = parseInt(f.pHeight.value);
    if(h >= 150 && h <= 9000)
    {
      this.pHeight = h;
      getObjStyleRef('pPreview1').height = this.pHeight + "px";
    }
  }
  
  this.setLinks = function()
  {
    var nLinkColor    = (f.nLinkColor.value.match(RE_color)   ? f.nLinkColor.value   : "inherit");
    var nLinkBgColor  = (f.nLinkBgColor.value.match(RE_color) ? f.nLinkBgColor.value : "transparent");
    var hLinkColor    = (f.hLinkColor.value.match(RE_color)   ? f.hLinkColor.value   : "inherit");
    var hLinkBgColor  = (f.hLinkBgColor.value.match(RE_color) ? f.hLinkBgColor.value : "transparent");
    
    this.p.setupLinks(nLinkColor, f.nLinkStyle.value, f.nLinkDec.value, f.nLinkWeight.value, f.nLinkBgColor.value, 
                      hLinkColor, f.hLinkStyle.value, f.hLinkDec.value, f.hLinkWeight.value, f.hLinkBgColor.value);
  }
  
  this.refreshLinks = function()
  {
    this.setLinks();
    if(this.RTC) { this.compileAll(); }
  }
  
  this.toggleRTC = function()
  {
    if(this.RTC)
    {
      swapImage("btnRTcompile", "<?=GFXL_ROOT?>clbtn/realtime-translation0");
      this.RTC = false;
      setObjVisibility('btnPreview', 1);
      document.body.focus();
    }
    else
    {
      swapImage("btnRTcompile", "<?=GFXL_ROOT?>clbtn/realtime-translation1");
      this.RTC = true;
      setObjVisibility('btnPreview', 0);
      document.body.focus();
      this.compileAll();
    }
  }
  return this;
}