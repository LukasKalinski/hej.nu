<?php
require_once('/home/h/hej/_include/system.php');
require_once('/home/h/hej/_include/gui/gui.globals.php');
?>
//<script>
var arr_mail = new Array();
function localInit()
{
  for(i=0; i<arr_mailGUIDs.length; i++) arr_mail[i] = new Mail_Object(i,arr_mailGUIDs[i]);
}

function findReceiver()
{
  var f = new Form_Object('setReceiver', 'top.HEJ_index.HEJ_right');
  f.verify("receiver", 'f->value.length < 1', "Det är bra om man anger en mottagare också..", true);
  f.verify("receiver", 'f->value == <?=$_SESSION["__user_ID"]?> || f->value == "<?=$_SESSION["__username"]?>"', "Alltid roligare att skicka mail till andra än sig själv.", true);
  if(f.verified)
  {
    top.HEJ_index.HEJ_main.location = "<?=WWW_ROOT?>mil/mil_db.php?action=setreceiver&receiver=" + f.fRef.receiver.value;
    f.fRef.receiver.value = "";
  }
	return false;
}

function change_folder()
{
	f = new Form_Object('setFolder');
  
	f.verify("folderGUID","f->value == 0", "Du måste välja en mapp.", true);
	f.verify("folderGUID","get_selected_GUIDs().length == 0", "Det vore ju bra om du markerade något mail också..", true);
	if(f.verified)
	{
		f.fRef.mailGUID.value = get_selected_GUIDs();
		f.fRef.submit();
	}
}

var sbox1 = new Image();
var sbox2 = new Image();
sbox1.src = "<?=GFXL_ROOT?>csbtn/mil_select.gif";
sbox2.src = "<?=GFXL_ROOT?>csbtn/mil_selected.gif";

var mailguids = "";
var all_selected = false;

function Mail_Object(mail_ID,mail_GUID)
{
	this.mailGUID = mail_GUID;
	this.is_selected = false;
	this.do_select = function() {
		swap_image("sbox"+mail_ID, sbox2.src);
		this.mailGUID = arr_mailGUIDs[mail_ID];
		this.is_selected = true;
	}
	this.do_unselect = function() {
		swap_image("sbox"+mail_ID, sbox1.src);
		this.mailGUID = '';
		this.is_selected = false;
	}
	return this;
}

function toggle_select(objnum)
{
	if(!arr_mail[objnum].is_selected) arr_mail[objnum].do_select();
	else                              arr_mail[objnum].do_unselect();
}

function toggle_select_all()
{
	for(i=0; i<arr_mailGUIDs.length; i++)
	{
		if(typeof(arr_mailGUIDs[i]) != 'undefined')
		{
			if(!all_selected) { arr_mail[i].do_select(); }
			else { arr_mail[i].do_unselect(); }
		}
	}
	if(all_selected) all_selected = false;
	else             all_selected = true;
}

function get_selected_GUIDs()
{
	var theString = "";
	for(i=0; i<arr_mail.length; i++)
	{
		if(arr_mail[i].is_selected) { theString += arr_mail[i].mailGUID+";"; }
	}
	return theString;
}