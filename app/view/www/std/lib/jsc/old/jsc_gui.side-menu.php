<?
require_once("/home/h/hej/_include/system.php");
require_once("/home/h/hej/_include/functions/function.browser.php");
require_once("/home/h/hej/_include/gui/gui.common.php");
?>
//<script>


function MENU_setBehaviour(prefix, elementType)
{
  var prefix = new RegExp('^'+prefix,'gi');
  for(var i=0; (ref = document.getElementsByTagName('div')[i]) != null; i++)
  {
    if(ref.id.match(prefix))
    {
      ref.onmouseover = function()
      {
        this.style.borderColor     = "#908255";
        this.style.backgroundColor = "#AFA378";
      }
      ref.onmouseout = function()
      {
        this.style.borderColor     = "#C0B283";
        this.style.backgroundColor = "#C0B283";
      }
      ref.onmousedown = function()
      {
        this.style.borderColor     = "#908255";
        this.style.backgroundColor = "#908255";
      }
      ref.onmouseup = function()
      {
        this.style.borderColor     = "#908255";
        this.style.backgroundColor = "#AFA378";
      }
    }
  }
}

function Menu()
{
  this.tcID = -1;
  this.itmID;
  this.TC = new Array();
  this.ITM = new Array();
  
  this.addSection = function(sName, sAction)
  {
    this.tcID++;
    this.TC[this.tcID] = new Array();
    this.TC[this.tcID].sName = sName;
    this.TC[this.tcID].sAction = sAction;
    this.ITM[this.tcID] = new Array();
    this.itmID = -1;
  }
  this.addItem = function(iName, iAction)
  {
    this.itmID++;
    this.ITM[this.tcID][this.itmID] = new Array();
    this.ITM[this.tcID][this.itmID].iName = iName;
    this.ITM[this.tcID][this.itmID].iAction = iAction;
  }
  this.make = function(targetID)
  {
    var tmContent = "";
    function ac(c)
    {
      tmContent += c;
    }
    
    for(var i=0; i<this.TC.length; i++)
    {
      ac('<div class="sideMenuTitle"><strong>'+this.TC[i].sName+'</strong></div>');
      for(var j=0; j<this.ITM[i].length; j++)
      {
        ac('<div class="sideMenu" style="width:<?=(browser("ns") ? 154 : 166)?>px;" onclick="'+this.ITM[i][j].iAction+'">');
          ac(this.ITM[i][j].iName);
        ac('</div>');
      }
    }
    document.getElementById(targetID).innerHTML = tmContent;
    tmContent = "";
    
    var ref;
    
  }
}

