<?php
require('system/lib.common-inside.php');
require('system/lib.session-inside.php');

Session_Inside::checkuselev();
$__sessuser = &Session_Inside::shortcut(SESSINS_KEY_USER);
CYCOM_Inside::register_user($__sessuser);

if(!isset($_GET['userid']))
  CYCOM_Inside::err('user__no_get_userid_set', 'Missing $_GET[userid].', null, __FILE__, __LINE__, ERR_ACTION_SYSMSG, true, get_defined_vars());

require('system/db/class.CYCOM_DB_Usr_Accessor.php');
require('system/db/globals.usr.php');

// Check browser case for css retrieval:
$browser_case = CYCOM_DB_Usr::BROWSER_CASE_MSIE;
if(BROWSER == BROWSER__CASE_GECKO)
  $browser_case = CYCOM_DB_Usr::BROWSER_CASE_GECKO;

// +@ Open database connection.
$USR_R = new CYCOM_DB_Usr_Accessor();

$__dbusr = array(); // Result from database.

// Check if we have to get result from database (that is, it's not stored in the session):
if($_GET['userid'] != $__sessuser->get_uid())
{
  $__dbusr = $USR_R->get_by_id($_GET['userid'], 'username,gender,dob');
}
else
{
  $__dbusr['username'] = $__sessuser->get('username');
  $__dbusr['gender']   = $__sessuser->get('gender');
  $__dbusr['dob']      = $__sessuser->get('dob');
}
$__dbusr = array_merge($__dbusr, $USR_R->get_by_id($_GET['userid'], 'pres_compiled,pres_css_compiled['.$browser_case.']', 'pres'));

// -@ Close database connection.
$USR_R->destroy();

require_once('Cylab/functions/function.agecalc.php');
$__dbusr['age'] = Cylib__agecalc($__dbusr['dob']);

require('cte/engine/class.CTE.php');
CTE::set_language(LANG);
CTE::register_var('dbuser', $__dbusr);
CTE::display('user/user.main.tpl');

exit;
require_once('/home/h/hej/_include/gui/gui.frame-main.php');
require_once('/home/h/hej/_include/system.php');
require_once('/home/h/hej/_include/classes/class.User.php');
require_once('/home/h/hej/_include/classes/class.Presentation.php');
require_once('/home/h/hej/_include/functions/function.send-message.php');
require_once('/home/h/hej/_include/arrays/array.online-status.php');

require_session('__USER_SESSION');

$DB = new DB_Connection('veloci', 'hej_U');
$USR = new User($_GET['userGUID'], $DB);

$user_data = $USR->USR_getData('user_ID, username, gender, email, birthdate, show_email, show_birthdate, show_cellphone, '.
                               'county, city, district, hair_color, eye_color, height, clothes, personality, interests, '.
            								   'music_taste, politics, civil_status, foreign_background, icq, msn, cellphone, homepage, '.
            								   'photo, visitor_counter, login_counter, online_timestamp, online_status, login_log');

if(!isset($user_data['user_ID']))
{
	send_message('JS', FALSE, '', 'Anv�ndaren har antingen avslutat sitt medlemsskap h�r eller blivit avst�ngd.');
	exit;
}

$pres = $USR->USR_getPresData('compiled,height,bgColor');
$stats = $USR->USR_generateStats();

# Add new visitor.
if($_GET['userGUID'] != $_SESSION['__user_GUID'])
{
	if($USR->USR_addVisitor($_SESSION['__user_GUID']))
    $user_data['visitor_counter']++;
}

# Get visitor list
$visitors = $DB->query('SELECT UU.user_ID, UU.user_GUID, UU.username, UU.gender, UU.birthdate, UU.city, '.
                       'UV.visit_date, UU.photo, UU.online_timestamp '.
                       'FROM users as UU INNER JOIN visitors as UV ON UU.user_GUID=UV.visitor_GUID '.
								       'WHERE UV.user_GUID="'.$_GET['userGUID'].'" ORDER BY visitor_ID DESC');
$visitors->run();

$DB->disconnect();

if(empty($pres['compiled']))
{
	$pres['compiled']  = '<div style="background:#'.COLOR__PRESENTATION_DEFAULT.';padding:8px;" class="text">';
  if($_GET['userGUID'] == $_SESSION['__user_GUID'])
    $pres['compiled'] .= 'Du har inte skrivit n�gon presentation �nnu. Klicka '.
                         '<a href="../cfg/cfg_presentation.php" target="HEJ_main">[ h�r ]</a> '.
                         'f�r att g� till l�dbyggaren.';
  else
    $pres['compiled'] .= $user_data['username'].' har inte skrivit n�got om sig sj�lv �n.';
  $pres['compiled'] .= '</div>';
}
$pres['compiled'] = str_replace('<PHOTO_URL>', PHOTO_ROOT, $pres['compiled']);
$pres['compiled'] = str_replace('<USR_MAIN>', WWW_ROOT.'usr/usr_main.php?userGUID=', $pres['compiled']);
$pres['compiled'] = str_replace('<USER_SEARCH>', WWW_ROOT.'src/src_process.php?action=quicksearch&amp;user=', $pres['compiled']);

# Store contact-info in array
define('NOTICE__SHOW_TO_NONE',    'Visas f�r ingen');
define('NOTICE__SHOW_TO_FRIENDS', 'Visas bara f�r v�nner');
define('NOTICE__SHOW_TO_ALL',     'Visas f�r alla');
$security_highest = '';
$security_medium = '';
$security_low = '';
$inc = 0;

# E-mail
if($_SESSION['__user_GUID'] == $_GET['userGUID'] || ($is_friend == 1 && $user_data['show_email'] == 'FRIENDS') || $user_data['show_email'] == 'EVERYONE')
{
  $a_contactinfo[$inc]['title'] = 'E-mail';
  $a_contactinfo[$inc]['info'] = '<a href="mailto:'.$user_data['email'].'" class="in">'.truncate($user_data['email'],30).'</a>';
  if($_SESSION['__user_GUID'] == $_GET['userGUID'])
  {
  	switch($user_data['show_email'])
  	{
  	  case 'NONE':      $a_contactinfo[$inc]['show'] = $security_highest; break;
  	  case 'FRIENDS':   $a_contactinfo[$inc]['show'] = $security_medium;  break;
  	  case 'ALL':       $a_contactinfo[$inc]['show'] = $security_low;     break;
  	}
  }
  $inc++;
}

# ICQ
if($user_data['icq'] != 0)
{
  $a_contactinfo[$inc]['title'] = 'ICQ';
  $a_contactinfo[$inc]['info'] = $user_data['icq'];
  $a_contactinfo[$inc]['show'] = '-';
  $inc++;
}

# MSN
if($user_data['msn'] != '')
{
  $a_contactinfo[$inc]['title'] = 'MSN';
  $a_contactinfo[$inc]['info'] = truncate($user_data['msn'],30);
  $a_contactinfo[$inc]['show'] = '-';
  $inc++;
}

# Cellphone
if($_SESSION['__user_GUID'] == $_GET['userGUID'] || ($is_friend == 1 && $user_data['show_cellphone'] == 'FRIENDS') || $user_data['show_cellphone'] == 'EVERYONE')
{
  if($user_data['cellphone'] != '')
  {
  	$a_contactinfo[$inc]['title'] = 'Mobil';
  	$a_contactinfo[$inc]['info'] = $user_data['cellphone'];
  	if($_SESSION['__user_GUID'] == $_GET['userGUID'])
  	{
  		switch($user_data['show_cellphone'])
  		{
  		  case "NONE":    $a_contactinfo[$inc]['show'] = $security_highest;  break;
  		  case "FRIENDS": $a_contactinfo[$inc]['show'] = $security_medium;   break;
  		  case "ALL":     $a_contactinfo[$inc]['show'] = $security_low;      break;
  		}
  	}
  	$inc++;
  }
}

# Homepage
if($user_data['homepage'] != '')
{
  $a_contactinfo[$inc]['title'] = 'Hemsida';
  $a_contactinfo[$inc]['info'] = '<a href="'.$user_data['homepage'].'" target="_new" class="in">'.truncate($user_data['homepage'],30).'</a>';
  $a_contactinfo[$inc]['show'] = '-';
  $inc++;
}

print_doctype();
?>
<html>
<head>
<?php print_html_headers(); ?>
<script language="JavaScript" type="text/javascript">
<!--
<?php JS_GUI_setup('usr', 0, 'userGUID='.$_GET['userGUID']); ?>
//-->
</script>
<?php
/*
// FACTS
$user_data['personality'] = explode(",",$user_data['personality']);
if($user_data['personality'][1]) { $user_data['personality'][0] .= "<br>"; }
if($user_data['personality'][2]) { $user_data['personality'][1] .= "<br>"; }
$user_data['personality'] = implode("",$user_data['personality']);

$user_data['interests'] = explode(",",$user_data['interests']);
if($user_data['interests'][0]) { $user_data['interests'][0] .= "<br>"; }
if($user_data['interests'][1]) { $user_data['interests'][1] .= "<br>"; }
$user_data['interests'] = implode("",$user_data['interests']);

$user_data['music_taste'] = explode(",",$user_data['music_taste']);
if($user_data['music_taste'][0]) { $user_data['music_taste'][0] .= "<br>"; }
if($user_data['music_taste'][1]) { $user_data['music_taste'][1] .= "<br>"; }
$user_data['music_taste'] = implode("",$user_data['music_taste']);


if(
		$_SESSION['__user_GUID'] == $_GET['userGUID'] || 
		($is_friend == 1 && $user_data['show_birthdate'] == 'FRIENDS') || 
		$user_data['show_birthdate'] == 'EVERYONE'
	)
{
	$birthdate = clarify_birthdate($user_data['birthdate']);
}
else { $birthdate = ''; }

if($user_data['height'] != '') { $user_data['height'] .= ($user_data['height'] != 0 ? " cm" : ""); }

$a_facts  = "H�rf�rg|".				$user_data['hair_color'];
$a_facts .= "|�gonf�rg|".			$user_data['eye_color'];
$a_facts .= "|L�ngd|".				$user_data['height'];
$a_facts .= "|Kl�dstil|".			$user_data['clothes'];
$a_facts .= "|Personlighet|".	$user_data['personality'];
$a_facts .= "|Intressen|".		$user_data['interests'];
$a_facts .= "|Musikstil|".		$user_data['music_taste'];
$a_facts .= "|Politik|".			$user_data['politics'];
$a_facts .= "|Civilstatus|".	$user_data['civil_status'];
$a_facts .= "|Fyller �r|".		$birthdate;
$a_facts .= "|Ursprung|".			$user_data['foreign_background'];
$a_facts = explode("|",$a_facts);
$facts = "";
for($i=0; $i<count($a_facts); $i+=2)
{
	if($a_facts[$i+1] == '') { $a_facts[$i+1] = $empty_field; }
	$bg = get_list_bg($bg);
	$facts .= '<tr>';
	$facts .= '<td class="listTextH2" bgcolor="#'.$bg.'" valign="top" width="40%">'.$a_facts[$i].':</td>';
	$facts .= '<td class="listText" bgcolor="#'.$bg.'" width="60%">'.($a_facts[$i+1] ? $a_facts[$i+1] : "- - -").'</td>';
	$facts .= '</tr>';
}
unset($a_facts);

// LOGINS
$arr_logins = explode(";",$user_data['login_log']);
$logins = "";
for($i=0; $i<count($arr_logins); $i++)
{
	if($arr_logins[$i] != "")
	{
		$logins .= '<tr><td class="text" align="right">'.clarify_date(date("YmdHis",$arr_logins[$i])).'&nbsp;</td></tr>';
	}
}
unset($facts);
*/
?>
</head>
<?php
print_html_body_start(array(
array('lbanTextH1',$user_data['username'].' - '.gender($user_data['gender'],'P','F').get_age($user_data['birthdate'])),
array(($user_data['online_timestamp'] > time() ? 'lbanOnline' : 'lbanOffline'),($user_data['online_timestamp'] > time() ? '+ '.$ARRAY__online_status[$user_data['online_status']].' +' : '- Offline -')),
array('lbanTextH1','ID: '.$user_data['user_ID'])));
?>
<script language="JavaScript" type="text/javascript">
<!--
<?php loadJS('lib.browser;lib.dom;lib.system;lib.event;lib.pres'); ?>
//-->
</script>
<table cellpadding="2" cellspacing="0" border="0" style="width:521px;">
  <tr>
    <td class="text" colspan="2">
      Odlar kaktusar i <?php
      echo $user_data['city'];
      echo (!empty($user_data['district']) ? ' ('.$user_data['district'].')' : '').' i '.$user_data['county'];
      ?>
    </td>
  </tr>
  <tr>
    <td class="text" style="width:108px;">
      <?php echo makeUserPhoto($user_data['photo'],$_GET['userGUID'],'M','ZOOM'); ?>
    </td>
    <td class="text" style="width:413px;" valign="top">
      <div style="background:url('<?=GFX_ROOT?>stt_bg-v.gif');width:16px;height:132px;" title="Aktivitet under de senaste 30 dagarna">
        <?php
        $height = round($stats * 132);
        $top = 131 - $height;
        ?>
        <table cellpadding="0" cellspacing="0" border="0" style="position:relative;top:<?=$top?>px;left:2px;width:12px;height:<?=$height?>px;background:url('<?=GFX_ROOT?>stt_v-neutral.gif');">
        <tr><td></td></tr>
        </table>
        <?php unset($height,$top); ?>
      </div>
    </td>
  </tr>
</table>
<br>
<?php
if(empty($pres['height'])) $pres['height'] = 30;
?>
<div style="height:<?=($pres['height'])?>px;">
  <div class="borderStrong" style="height:100%;background:<?=(!empty($pres['bgColor']) ? $pres['bgColor'] : '#'.COLOR__PRESENTATION_DEFAULT)?>;">
    <div style="position:absolute;width:521px;height:100%;clip:rect(0px,519px,519px,0px);overflow:hidden;" class="text">
    <?php echo $pres['compiled']; unset($pres['compiled']); ?>
    </div>
  </div>
</div>
<table cellpadding="0" cellspacing="0" width="100%" border="0"><tr><td class="text" height="16"></td></tr></table>
<?php print_hdelim(); ?>
	<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<?php
	if(count($a_contactinfo) > 0)
	{
		$bg = get_list_bg(false,false);
		for($i=0; $i<count($a_contactinfo); $i++)
		{
			echo '<tr>';
			echo '<td class="listTextH2" bgcolor="#'.$bg.'" width="121" height="14">';
			echo '&nbsp;'.$a_contactinfo[$i]['title'].':';
			echo '</td>';
			echo '<td class="listText" bgcolor="#'.$bg.'" width="330" height="14">';
			echo $a_contactinfo[$i]['info'];
			echo '</td>';
			echo '<td class="listTextNotice" bgcolor="#'.$bg.'" width="170" align="right" height="14">';
			if($_SESSION['__user_GUID'] == $_GET['userGUID']) { echo $a_contactinfo[$i]['show'].'&nbsp;'; }
			echo '</td>';
			echo '</tr>';
			$bg = get_list_bg($bg,false);
		}
	}
	else { echo '<tr><td colspan="4" class="listText" height="15">'.$user_data['username'].' har inte fyllt i sina kontaktuppgifter �n...</td></tr>'; }
	?>
	</table>
	<?print_hdelim();?>
	<br><br>
	<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
	<td class="listTextH2" height="14" width="30%" colspan="2">&nbsp;Bes�kare</td>
	<td class="listTextH2" height="14" width="25%">Stad</td>
	<td class="listTextH2" height="14" width="30%">Datum</td>
	<td class="listTextH2" height="14" width="15%" align="right">ID&nbsp;</td>
	</tr>
	</table>
	<?print_hdelim();?>
	<table cellpadding="1" cellspacing="0" width="100%" border="0">
	<?
	if($visitors->num_rows > 0)
	{
		while($visitors->fetchArray($v))
		{
			$bg = get_list_bg($bg,false);
			echo '<tr style="height:15px;">';
      echo '<td bgcolor="#'.$bg.'" width="3%">'.makeListIcons($v['user_GUID'],$v['gender'],$v['photo'],$v['online_timestamp']).'</td>';
			echo '<td class="text" bgcolor="#'.$bg.'" width="27%">'.makeUserLink($v['user_GUID'],$v['username'],gender($v['gender'],"Pojke, ","Flicka, ").get_age($v['birthdate']).' �r','list').'</td>';
			echo '<td class="text" bgcolor="#'.$bg.'" width="25%">'.$v['city'].'</td>';
			echo '<td class="text" bgcolor="#'.$bg.'" width="30%">'.clarify_date($v['visit_date']).'</td>';
			echo '<td class="text" bgcolor="#'.$bg.'" width="15%" align="right"><a href="usr_main.php?userGUID='.$v['user_GUID'].'" target="HEJ_main" class="in">'.$v['user_ID'].'</a>&nbsp;</td>';
			echo '</tr>';
		}
	}
	elseif($user_data['visitor_counter'] > 0) { echo '<tr><td class="text" '.get_list_bg($bg,false).'>&nbsp;Bes�ksloggen har t�mts.</td></tr>'; }
	else { echo '<tr><td class="text" '.get_list_bg($bg,false).'>Ingen har bes�kt din l�da �n...</td></tr>'; }
	unset($visitors);
	?>
	</table>
	<?print_hdelim();?>
	<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr><td class="listTextH2" align="right" width="100%" colspan="4">Sammanlagt <?=$user_data['visitor_counter']?> bes�k&nbsp;</td></tr>
	</table>
<?print_html_body_end();?>
</html>
<?
unset($user_data);
?>
