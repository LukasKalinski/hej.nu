<?
require_once("/home/h/hej/_include/system.php");
require_once("/home/h/hej/_include/gui/gui.common.php");
?>
//<script>

var SM_WIDTH = 180;
var SM_HEIGHT = 20;
var SM_titles = new Array();
var SM_titleID = 0;
var SM_items = new Array();
SM_items[SM_titleID] = new Array();

function SMenu_setup()
{
  for(var i=0; (ref = document.getElementsByTagName("div")[i]) != null; i++)
  {
    if(ref.id.match(/SMenu/gi))
    {
      ref.onmouseover = function()
      {
        var ref = document.getElementById('SM'+this.id.replace(/[^0-9]/gi, ""));
        ref.style.backgroundColor = "#FEFFB1";
      }
      ref.onmouseout = function()
      {
        var ref = document.getElementById('SM'+this.id.replace(/[^0-9]/gi, ""));
        ref.style.backgroundColor = "#B5A671";
      }
      ref.onmousedown = function()
      {
        var ref = document.getElementById('SM'+this.id.replace(/[^0-9]/gi, ""));
        ref.style.backgroundColor = "#908255";
      }
      ref.onmouseup = function()
      {
        var ref = document.getElementById('SM'+this.id.replace(/[^0-9]/gi, ""));
        ref.style.backgroundColor = "#FEFFB1";
      }
    }
  }
}

function SMenu_addTitle(tName, tAction)
{
  var key = SM_titles.length;
  SM_titles[key] = new Array();
  SM_titles[key].tName = tName;
  SM_titles[key].tAction = tAction;
  SM_titleID++;
}

function SMenu_addItem(iName, iAction)
{
  var key = SM_items[SM_titleID].length;
  SM_items[SM_titleID] = new Array();
  SM_items[SM_titleID][key] = new Array();
  SM_items[SM_titleID][key].iName = iName;
  SM_items[SM_titleID][key].iAction = iAction;
}

function SMenu_toggle(objID)
{
  var ref = getObjRef('smExp'+objID);
  if(ref.display == "none")
    ref.display = "block";
  else
    ref.display = "none";
}

function SMenu_make(targetID)
{
  var smHeight = this.smHeight;
  
  function makeTitle(tID, tName, tAction)
  {
    return '<div style="position:relative;height:22px;" onclick="SMenu_toggle('+tID+');">'+
           '<div id="SM'+tID+'" style="position:absolute;width:10px;height:22px;background-color:#B5A671;"></div>'+
           '<div id="SMenu'+tID+'" class="SMenu" style="position:absolute;left:10px;">'+tName+'</div>'+
           '</div>';
  }
  function makeItem(iID, iName, iAction)
  {
    return '<div style="position:relative;height:22px;">'+
           '<div id="SM'+iID+'" style="position:absolute;width:10px;height:22px;background-color:#B5A671;"></div>'+
           '<div id="SMenu'+iID+'" class="SMenu" style="position:absolute;left:10px;">'+iName+'</div>'+
           '</div>';
  }
  function ac(c) { document.getElementById(targetID).innerHTML += c; }
  
  for(var i=0; i<SM_titles.length; i++)
  {
    ac(makeTitle(i, SM_titles[i].tName, SM_titles[i].tAction));
    ac('<div id="smExp'+i+'" style="display:none;">');
    for(var j=0; j<SM_items[i].length; j++)
    {
      ac(makeItem(i, SM_items[i].iName, SM_items[i].iAction));
    }
    ac('</div>');
  }
  SMenu_setup();
}