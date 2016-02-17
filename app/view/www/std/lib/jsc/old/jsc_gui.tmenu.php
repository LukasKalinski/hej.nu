<?
require_once("/home/h/hej/_include/system.php");
require_once("/home/h/hej/_include/functions/function.browser.php");
require_once("/home/h/hej/_include/gui/gui.common.php");
?>
//<script>

var TCID = 0;
var TC = new Array();
var SC = new Array();
var ITM = new Array();

function TMenu_section(sectionName, sectionAction)
{
  var SCID = 0;
  var ITMID = 0;
  
  TC[TCID] = new Array();
  TC[TCID].sectionName = sectionName;
  TC[TCID].sectionAction = sectionAction;
  TC[TCID].SC = new Array();
  
  this.openSC = function(sectionName,sectionAction)
  {
    TC[TCID].SC[SCID] = new Array();
    TC[TCID].SC[SCID].sectionName = sectionName;
    TC[TCID].SC[SCID].sectionAction = sectionAction;
    TC[TCID].SC[SCID].ITM = new Array();
  }
  this.addITM = function(itemName,itemAction)
  {
    TC[TCID].SC[SCID].ITM[ITMID] = new Array();
    TC[TCID].SC[SCID].ITM[ITMID].itemName = itemName;
    TC[TCID].SC[SCID].ITM[ITMID].itemAction = itemAction;
    ITMID++;
  }
  this.closeTC = function() { TCID++; }
  this.closeSC = function() { SCID++; ITMID = 0; }
}

function TMenu_toggle(objID,nodeID)
{
  ref = document.getElementById(objID);
  if(ref.style.display == "none") ref.style.display = "block";
  else ref.style.display = "none";
  node = document.getElementById("node"+nodeID);
  if(node.src == "<?=GFX_ROOT?>tmenu_node0.gif") node.src = "<?=GFX_ROOT?>tmenu_node1.gif";
  else if(node.src == "<?=GFX_ROOT?>tmenu_node1.gif") node.src = "<?=GFX_ROOT?>tmenu_node0.gif";
}

function TMenu_load(targetID)
{
  var tmContent = "";
  function ac(c)
  {
    tmContent += c;
  }
  
  function makeIndent(level,uID)
  {
    function makeImage(imgName, iX, iY)
    {
      iX+=2; // padding-left
      return '<img '+((imgName == "node0") ? 'id="node'+uID+'" ' : '')+'src="<?=GFX_ROOT?>tmenu_' + imgName + '.gif" '+
             'onclick="TMenu_toggle(\'TMenu'+uID+'\',\''+uID+'\');" '+
             'style="position:absolute;top:'+iY+'px;left:'+iX+'px;cursor:'+(b.ie5 ? "hand" : "pointer")+';z-index:2;" border="0" alt="">';
    }
    var str = "";
    
    switch(level)
    {
      case "s":
        str += makeImage("svline",4,8);
        str += makeImage("node0",0,6);
        str += makeImage("lhline",10,10);
        str += makeImage("dot",16,8);
      break;
      case "c":
        str += makeImage("lvline",4,0);
        str += makeImage("node0",0,6);
        str += makeImage("lhline",10,10);
        str += makeImage("dot",16,8);
      break;
      case "e":
        str += makeImage("svline",4,0);
        str += makeImage("node0",0,6);
        str += makeImage("lhline",10,10);
        str += makeImage("dot",16,8);
      break;
      case "n":
        str += makeImage("node0",0,6);
        str += makeImage("lhline",10,10);
        str += makeImage("dot",16,8);
      break;
      
      case "cs":
        str += makeImage("lvline",4,0);
        str += makeImage("lhline",24,10);
        str += makeImage("lvline",18,-8);
        str += makeImage("node0",14,6);
        str += makeImage("dot",30,8);
      break;
      case "es":
        str += makeImage("lhline",24,10);
        str += makeImage("node0",14,6);
        str += makeImage("lvline",18,-10);
        str += makeImage("dot",30,8);
      break;
      
      case "ccc":
        str += makeImage("lvline",4,0);
        str += makeImage("lvline",17,-8);
        str += makeImage("lhline",34,10);
        str += makeImage("lvline",32,-8);
        str += makeImage("arrow",38,8);
      break;
      case "cec":
        str += makeImage("lvline",4,0);
        str += makeImage("lhline",34,10);
        str += makeImage("lvline",32,-8);
        str += makeImage("arrow",38,8);
      break;
      case "ecc":
        str += makeImage("lvline",19,-8);
        str += makeImage("lhline",34,10);
        str += makeImage("lvline",32,-8);
        str += makeImage("arrow",38,8);
      break;
      case "eec":
        str += makeImage("lhline",34,10);
        str += makeImage("lvline",32,-8);
        str += makeImage("arrow",38,8);
      break;
    }
    
    return str;
  }
  
  var img1, img2, img3;
  for(var i=0; i<TC.length; i++)
  {
    if(i == 0 && i != (TC.length-1)) img1 = makeIndent("s",i);
    else if(i < (TC.length-1)) img1 = makeIndent("c",i);
    else if(i == 0 && i < TC.length) img1 = makeIndent("n",i);
    else img1 = makeIndent("e",i);
    
    ac('<div style="position:relative;">' + img1);
      ac('<div class="TMenu" style="padding-left:25px;padding-top:4px;" onclick="TMenu_toggle(\'TMenu'+i+'\',\''+i+'\');"><strong>');
      ac(TC[i].sectionName+'</strong></div>');
    ac('</div>');
    ac('<div id="TMenu'+i+'" style="display:none;">');
    for(var j=0; j<TC[i].SC.length; j++)
    {
      if(i < (TC.length-1)) img2 = makeIndent("cs", i+'-'+j);
      else img2 = makeIndent("es", i+'-'+j);
      ac('<div style="position:relative;">' + img2);
        ac('<div class="TMenu" style="padding-left:40px;" ');
        ac('onclick="'+TC[i].SC[j].sectionAction+'"><strong>'); // TMenu_toggle(\'TMenu'+i+'-'+j+'\',\''+i+'-'+j+'\');
        ac(TC[i].SC[j].sectionName+'</strong></div>');
      ac('</div>');
      ac('<div id="TMenu'+i+'-'+j+'" style="display:none;">');
      for(var k=0; k<TC[i].SC[j].ITM.length; k++)
      {
        if(i < (TC.length-1))
        {
          if(j < (TC[i].SC.length-1)) img3 = makeIndent("ccc");
          else img3 = makeIndent("cec");
        }
        else
        {
          if(j < (TC[i].SC.length-1)) img3 = makeIndent("ecc");
          else img3 = makeIndent("eec");
        }
        ac('<div style="position:relative;">' + img3);
          ac('<div class="TMenu" style="padding-left:48px;" onclick="'+TC[i].SC[j].ITM[k].itemAction+'">');
          ac(TC[i].SC[j].ITM[k].itemName+'</div>');
        ac('</div>');
      }
      ac('</div>');
    }
    ac('</div>');
  }
  document.getElementById(targetID).innerHTML = tmContent;
  tmContent = "";
  
  var ref;
  for(var i=0; (ref = document.getElementsByTagName("div")[i]) != null; i++)
  {
    if(ref.className == "TMenu")
    {
      ref.onmouseover = function()
      {
        this.style.borderColor = "#908255";
        this.style.backgroundColor = "#AFA378";
      }
      ref.onmouseout = function()
      {
        this.style.borderColor = "#C0B283";
        this.style.backgroundColor = "#C0B283";
      }
      ref.onmousedown = function()
      {
        this.style.borderColor = "#908255";
        this.style.backgroundColor = "#908255";
      }
      ref.onmouseup = function()
      {
        this.style.borderColor = "#908255";
        this.style.backgroundColor = "#AFA378";
      }
    }
  }
}
