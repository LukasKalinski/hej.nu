<?
require_once("/home/h/hej/_include/system.php");
require_once("/home/h/hej/_include/gui/gui.globals.php");
?>
//<script>
/**
 * COPYRIGHT - Copyright 2002. All rights reserved. - This notice must remain untouched.
 * File: jsc_local.rgb-map.php
 * 
 * --- Description ---
 * The first section is the javascript compilator for user presentations.
 * The second section contains a class for the color-handler.
 * 
 * --- Copyright notice ---
 * Copying, modifying, using or distributing this script without the authors permission is an act of a true lamer.
 * Want to use this script? Fine, just ask. Although the purposes must be non-commercial.
 * 
 * --- External sources ---
 * http://gimp-savvy.com/BOOK/index.html?node52.html
 * http://www.pcigeomatics.com/cgi-bin/pcihlp/RGB
 * 
 * Last modified: 2003.09.08
 * Author: Lukas Kalinski (lukas@cylab.se)
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
    
  this.HEX = "<?=COLOR__PRESENTATION_DEFAULT?>";
  this.R = 0;
  this.G = 0;
  this.B = 0;
  this.whiteness = 0;
  this.S = 0;
  this.V = 0;
  this.H = 0;
  
  this.logEvent = function(ev)
  {
    ev = new Event_Object(ev);
    this.evX = ev.objPosX;
    this.evY = ev.objPosY;
  }
  
  this.setupGUI = function(c)
  {
    switch(c)
    {
      case "RGB":
        this.HEXfromRGB();
        this.HSVfromRGB();
        break;
      case "HSV":
        this.RGBfromHSV();
        this.HEXfromRGB();
        break;
      case "HEX":
        this.RGBfromHEX();
        this.HSVfromRGB();
        break;
    }
    this.refreshGUI();
  }
  
  this.refreshGUI = function()
  {
    getObjStyleRef('rgbPreview').backgroundColor = "#"+this.HEX;
    ref = getObjRef('frmColorMap');
    ref.H.value = Math.round(this.H);
    ref.S.value = Math.round(this.S * 100);
    ref.V.value = Math.round(this.V * 100);
    ref.R.value = this.R;
    ref.G.value = this.G;
    ref.B.value = this.B;
    ref.HEX.value = this.HEX;
    ref.cHEX.value = "#"+this.HEX;
    
    var hPos = Math.min(Math.max(this.mapSizeX * (this.H / 360) + this.mapOffsetX, 0), this.mapSizeX + this.mapOffsetX);
    var vPos = Math.min(Math.max(this.mapSizeY * (1 - this.V) + this.mapOffsetY, 0), this.mapSizeY + this.mapOffsetY);
    var sPos = Math.min(Math.max((this.S * this.sliderSizeX) - 2, 1), 144);
    var wPos = Math.min(Math.max((this.whiteness * this.sliderSizeX) - 2, 1), 144);
    
    setObjPos('curH', hPos - 3, false);
    setObjPos('curV', false, vPos - 3);
    setObjPos('curMap', hPos - 5, vPos - 5);
    setObjPos('curS', sPos, false);
    setObjPos('curW', wPos, false);
  }
  
  this.scanNumericChange = function(inpObj)
  {
    var varVal = inpObj.value;
    var varMax, varSys;
    var doDiv = false;
    
    switch(inpObj.name)
    {
      case "R":
        varMax = 255;
        varSys = "RGB";
        break;
      case "G":
        varMax = 255;
        varSys = "RGB";
        break;
      case "B":
        varMax = 255;
        varSys = "RGB";
        break;
      case "H":
        varMax = 360;
        varSys = "HSV";
        break;
      case "S":
        varMax = 100;
        varSys = "HSV";
        doDiv = true;
        break;
      case "V":
        varMax = 100;
        varSys = "HSV";
        doDiv = true;
        break;
      case "HEX":
        varSys = "HEX";
        break;
    }
    if(varVal.length > 0)
    {
      var hexValidated = true;
      if(varSys == "HEX")
      {
        if(inpObj.value.match(/[0-9a-f]{6}/gi)) { this.HEX = inpObj.value; }
        else { hexValidated = false; }
      }
      else
      {
        varVal = varVal.replace(/[^0-9]/gi, "");
        if(isNaN(varVal)) { varVal = 0; }
        varVal = Math.min(varVal, varMax);
        if(doDiv) { varVal = varVal / varMax }
        eval("this." + inpObj.name + '=' + varVal);
      }
      if(hexValidated) { this.setupGUI(varSys); }
    }
  }
  
  this.startGraphicMove = function(ev, obj)
  {
    this.scan = true;
    this.scanGraphicMove(ev, obj);
  }
  this.scanGraphicMove = function(ev, obj)
  {
    if(this.scan)
    {
      this.logEvent(ev);
      switch(obj)
      {
        case "map":
          this.V = Math.min(Math.max(1 - ((this.evY - this.mapOffsetY) / (this.mapSizeY)), 0), 1);
          this.H = Math.min(Math.max(360 * ((this.evX - this.mapOffsetX) / this.mapSizeX), 0), 360);
          break;
        case "saturation":
          this.S = Math.min(Math.max(this.evX / this.sliderSizeX, 0), 1);
          break;
        case "whiteness":
          this.whiteness = this.evX / this.sliderSizeX;
          break;
      }
      this.setupGUI('HSV');
    }
  }
  this.stopGraphicMove = function()
  {
    if(this.scan) { getObjRef('frmColorMap').HEX.focus(); } // just to prevent this ugly select-all thing
    this.scan = false;
  }
  
  this.HEXfromRGB = function()
  {
    function hex(n)
    {
      n = n.toString(16);
      if(n.length < 2) { n = "0" + n; }
      return n;
    }
    this.HEX = hex(this.R) + hex(this.G) + hex(this.B);
    this.HEX = this.HEX.toUpperCase();
  }
  
  this.RGBfromHEX = function()
  {
    this.R = parseInt(this.HEX.substr(0,2), 16);
    this.G = parseInt(this.HEX.substr(2,2), 16);
    this.B = parseInt(this.HEX.substr(4,2), 16);
  }
  
  this.HSVfromRGB = function()
  {
    var cMin = Math.min(Math.min(this.R, this.G), this.B);
    var cMax = Math.max(Math.max(this.R, this.G), this.B);
    
    this.S = (cMax != 0 ? ((cMax - cMin) / cMax) : 0);
    this.whiteness = 0;
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
  }
  
  this.RGBfromHSV = function()
  {
    var hue = this.H;
    if(hue == 360) { hue = 0; }
    hue = hue / 60;
    var hueSector = Math.floor(hue);
    var hueDelta = hue - hueSector;
    var cmi = this.whiteness + (this.V * (1 - this.S));
    var cma = this.whiteness + (this.V);
    var dec = this.whiteness + (this.V * (1 - (this.S * hueDelta)));
    var inc = this.whiteness + (this.V * (1 - (this.S * (1 - hueDelta))));
    
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
  }
  
  this.copyHEX = function()
  {
    if(b.ie)
    {
      oText = getObjRef('frmColorMap').cHEX.createTextRange();
      oText.execCommand("Copy");
    }
    else
      alert("Din webläsare har tyvärr inte stöd för den här funktionen.\n"+
            "Markera HEX-koden, högerklicka och välj kopiera. Lägg sedan till\n"+
            "ett '#' före koden när du använder den. Exempel: #OOFF00");
  }
}

var RGB = new RGB_Map();