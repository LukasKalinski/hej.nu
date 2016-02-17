<?
require_once("/home/h/hej/_include/system.php");
require_once("/home/h/hej/_include/gui/gui.globals.php");
?>
//<script>
var RW = "replyWindow";         <?// reference to the reply-window (layer)?>
var RW_ref = null;
var RW_sRef = null;
var DB_processing = false;      <?// this is set to true during the whole sending process ?>
var current_mID = 0;            <?// Current message ID ?>
var ce = 0;                     <?// Current event reference ?>
var lastEvent = 0;              <?// Store 'ce' in this variable to remember the last position of the message - even if new event is triggered. ?>

<?/**
* Function: init();
* Purpose: Loads automatically and stores every message's data in an array.
*/?>
var arr_message = new Array(); <?// Stores all message-objects ?>
function guestbookInit()
{
  RW_ref = getObjRef(RW);
  RW_sRef = getObjStyleRef(RW);
  
	var mData;
	for(i=0; i<arr_mData.length; i++)
	{
		mData = arr_mData[i].split("|");
		arr_message[i] = new Message('btn_reply'+i,'btn_delete'+i,'btn_goto'+i,mData[0],mData[1],mData[2],i);
	}
}

<?/**
* Class: message();
* Purpose: Create an object-reference for the button-group and user data in each message.
*/?>
function Message(button1,button2,button3,message_GUID,thread_GUID,writer_GUID,message_ID)
{
	var err_undef = "[ERROR]\nUndefined variable (GUID) found.";
	this.lay_btn1 = (getObjRef(button1) ? getObjRef(button1) : 0);
  this.lay_btn2 = (getObjRef(button2) ? getObjRef(button2) : 0);
	this.lay_btn3 = (getObjRef(button3) ? getObjRef(button3) : 0);
	(message_GUID != null ? this.mGUID = message_GUID : alert(err_undef));
	(thread_GUID  != null ? this.tGUID = thread_GUID  : alert(err_undef));
	(writer_GUID  != null ? this.wGUID = writer_GUID  : alert(err_undef));
	
	this.lay_btn1.onclick = function(e)
  {
		if(!b.ns4 && (b.ns || b.ie || b.op7))
		{
			if(b.ns) ce = new Event_Object(e);
			else ce = new Event_Object(event);
			RW_toggle(message_ID);
		}
    else alert("Det här är inte klart för din browser. Kontakta crew om du får upp det här meddelandet.");
	}
  if(this.lay_btn2 !== 0) { this.lay_btn2.onclick = function() { delete_message(message_ID,"DELETE"); } }
	return this;
}

<?/**
* Function: make_scrollY();
* Purpose: Make a smooth scroll if the reply-window ends up in a negative Y-pos.
*/?>
var S_loop = "";
function make_scrollY(targetCoord,way,jump,mID)
{
	var currentCoord = (b.ie ? document.body.scrollTop : window.pageYOffset);
	scrolled = false;
	if(jump == 1) jump = way;
	else jump = Math.ceil(Math.abs((currentCoord-targetCoord)/4)) * way;
	window.scrollBy(0,jump);
	if(currentCoord > targetCoord && currentCoord > 0)
	{
		var ref = "make_scrollY("+targetCoord+","+way+",0,"+mID+")";
		S_loop = setTimeout(ref,5);
	}
	else RW_toggle(mID);
}

<?/**
* Function: RW_toggle();
* Purpose: Toggle the visibility of the reply-window.
*/?>
var RW_opened = false;
function RW_toggle(mID)
{
	if(!DB_processing)
	{
		if(!RW_opened) RW_setup(mID);
		else if(RW_opened)
		{
			var confirmed = true;
			if(document.getElementById('repmsg').message.value.length > 0)
			{
				confirmed = confirm("Vill du stänga fönstret och ta bort allt du skrivit?");
			}
			if(confirmed) RW_reset();
			if(confirmed && current_mID != mID) RW_setup(mID);
		}
	}
	<?//else { alert("Du måste vänta tills förra inlägget skickats..."); }?>
}

<?/**
* Function RW_setup();
* Purpose: Setup the Reply Window.
*/?>
preloadImages('<?=GFXL_ROOT?>csbtn','gst_reply0.gif|gst_reply1.gif|gst_replied.gif');
function RW_setup(mID)
{
  var replyWindowHeight = parseInt(RW_sRef.height)+1;
	var posX = 146;
	var posY = Math.max(ce.pageOffsetY - ce.objPosY - replyWindowHeight-8, 0);
	CW_toggle('HIDE');
	if((posY+4) - (b.ie ? document.body.scrollTop : window.pageYOffset) < 0) { make_scrollY(posY-4,-1,1,mID);clearTimeout("S_loop"); }
	else
	{
		setObjPos(RW,posX,posY);
		setObjVisibility('RW1',1);
		setObjVisibility('RW2',1);
		setObjVisibility(RW,1);
		arr_message[mID].lay_btn1.alt = "Stäng";
		arr_message[mID].lay_btn1.src = "<?=GFXL_ROOT?>csbtn/gst_reply1.gif";
		setTimeout("document.getElementById('repmsg').message.focus()", 1); <?// Netscape goes wierd otherways?>
		document.getElementById('repmsg').mGUID.value    = arr_message[mID].mGUID;
		document.getElementById('repmsg').tGUID.value    = arr_message[mID].tGUID;
		document.getElementById('repmsg').userGUID.value = arr_message[mID].wGUID;
		current_mID = mID;  <?// Set the active mID?>
		RW_opened = true;   <?// Window is opened?>
	}
}

<?/**
* Function RW_reset();
* Purpose: Reset the Reply Window.
*/?>
function RW_reset()
{
  setObjVisibility('RW1',0);
  setObjVisibility('RW2',0);
	setObjVisibility(RW,0);
	RW_opened = false;
	if(arr_btnReply[current_mID] == "READ")
	{
		arr_message[current_mID].lay_btn1.alt = "Svara";
		arr_message[current_mID].lay_btn1.src = "<?=GFXL_ROOT?>csbtn/gst_reply0.gif";
	}
	else
	{
		arr_message[current_mID].lay_btn1.alt = "Svara (Du har redan svarat på det här inlägget)";
		arr_message[current_mID].lay_btn1.src = "<?=GFXL_ROOT?>csbtn/gst_replied.gif";
	}
	document.getElementById('repmsg').message.value  = '';
	document.getElementById('repmsg').mGUID.value    = '';
	document.getElementById('repmsg').tGUID.value    = '';
	document.getElementById('repmsg').userGUID.value = '';
	RW_opened = false; <?// Window is closed ?>
}

<?/**
* Function: CW_toggle();
* Purpose: Send feedback when the reply-on message is sent and reset all related values.
*/?>
var HC_loop = ""; <?// Hide-Confirm loop reference ?>
function CW_toggle(theCase)
{
	var CW = "replyConfirmation";
	var posX = 332;
	var posY = lastEvent.pageOffsetY - lastEvent.objPosY + 6;
	
	switch(theCase)
	{
		case "SUCCESS":
			setObjPos(CW,posX,posY);
			setObjVisibility(CW,1);
			clearTimeout(HC_loop);
      arr_btnReply[current_mID] = "REPLIED";
			var tref = "CW_toggle('HIDE');";
			HC_loop = setTimeout(tref,2000);
			RW_reset();
			DB_processing = false;
		break;
    case "HIDE":   setObjVisibility(CW,0); break;
		case "FAILED": DB_processing = false; break;
		default:       DB_processing = false; break;
	}
}

<?/**
* Function: submit_message();
* Purpose: The final reply-function, this one checks the values and submits the message.
*/?>
function submit_message()
{
	if(!DB_processing)
	{
    var f = new Form_Object('repmsg');
  	f.fRef.message.value = trim(f.fRef.message.value);
    
    f.verify("message", 'f->value.length < 3', "Eller så skriver man mer...", true);
    f.verify("message", 'f->value.length > 1024', "Nu blev det lite för långt...\n("+f.fRef.message.value.length+" av 1024 tillåtna tecken)", true);
    f.verify("mGUID",   'f->value.length != 38', "Det här felet borde inte visas.", true);
    
  	if(f.verified)
  	{
      f.doSubmit();
      f.resetSubmit();
			DB_processing = true;
			lastEvent = ce;
  	}
	}
	else alert("Det räcker med att skicka en gång faktiskt...");
}

<?/**
* Function: delete_message();
* Purpose: Delete a message by sending a reference (mID).
*/?>
var total_deleted = 0; // Count deleted messages
function delete_message(mID,theCase)
{
	switch(theCase)
	{
		case "DELETE":
			if(arr_message[mID].lay_btn2.src != "<?=GFXL_ROOT?>csbtn/deleted.gif")
			{
				if(!DB_processing)
				{
					if(confirm("Är du säker på att du vill ta bort inlägget?"))
					{
						current_mID = mID;
						arr_message[mID].lay_btn2.alt = "Tar bort inlägget...";
						gst_process.location="usr_guestbook-db.php?mGUID="+arr_message[mID].mGUID+"&userGUID="+__user_GUID+"&action=delete";
						DB_processing = true;
					}
				}
				else alert("Gör inte allt på en gång nu.. resultatet kan bli lite fel då");
			}
			else alert("Det här inlägget är redan borttaget.");
		break;
		
		case "DELETED":
			DB_processing = false;
			arr_message[current_mID].lay_btn2.alt = "Inlägget är borttaget";
			arr_message[current_mID].lay_btn2.src = "<?=GFXL_ROOT?>csbtn/deleted.gif";
			total_deleted++;
			if(total_deleted == delete_permissions) { document.location = document.location; }
		break;
		
		default:
			DB_processing = false;
		break;
	}
}