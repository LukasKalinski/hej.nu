<?php
/********************************************************************************\
* @File:          class.GST-Account.php                                          *
* @Description:   Functions related to the individual guestbook-account.         *
* @Author:        Lukas Kalinski                                                 *
* @Copyright:     2001-2003, CyLab Sweden                                        *
\********************************************************************************/

require_once('/home/h/hej/_include/system/system.mysql.php');
require_once('/home/h/hej/_include/system/system.session.php');
require_once('/home/h/hej/_include/system/system.date.php');
require_once('/home/h/hej/_include/classes/class.User.php');
require_once('/home/h/hej/_include/functions.php');

/**
* Class: GST_Account
*/
class GST_Account extends User
{
	var $writer_GUID, $writer_name, $writer_gender, $writer_birthdate, $writer_city, $writer_photo;
	
  /**
  * Constructor
  */
	function GST_Account($user_GUID, $mysql_link)
	{
		$this->user_GUID        = $user_GUID;
		$this->DB               = $mysql_link;
		$this->writer_GUID      = NULL;
		$this->writer_name      = NULL;
		$this->writer_gender    = NULL;
		$this->writer_birthdate = NULL;
		$this->writer_city      = NULL;
		$this->writer_photo     = NULL;
	}
  
  /**
  * Function: GST_setDBLink()
  */
  function GST_setDBLink($mysql_link)
  {
    $this->DB = $mysql_link;
  }
  
  /**
  * Function: GST_setClassDB()
  * Purpose: Select the database for this class
  */
  function GST_setClassDB()
  {
    if($this->DB !== FALSE)
      $this->DB->useDB('hej_G');
  }
	
	/**
  * Function: GST_getMessages();
	*/
	function GST_getMessages($from_limit, $to_limit, $threadGUID=FALSE, $writerGUID=FALSE)
	{
    $this->GST_setClassDB();

    if($threadGUID)      { $condition = "thread_GUID='".$threadGUID."'"; }
    elseif($writerGUID)  { $condition = "writer_GUID='".$writerGUID."' AND user_GUID='".$this->user_GUID."'"; }
    else                 { $condition = "user_GUID='".$this->user_GUID."'"; }
    
		$query = $this->DB->query("SELECT message_GUID, thread_GUID, writer_GUID, writer_name, writer_gender, writer_birthdate, ".
													    "writer_county, writer_city, writer_photo, post_date, message, condition ".
													    "FROM guestbook WHERE ".$condition." ORDER BY message_ID DESC LIMIT ".$from_limit.",".$to_limit);
    $query->run();
        
    return $query;
	}
	
	/**
  * Function: GST_setWriterData();
	*/
	function GST_setWriterData($wGUID, $wName=FALSE, $wGender=FALSE, $wBirthdate=FALSE, $wCounty=FALSE, $wCity=FALSE, $wPhoto=FALSE)
	{
		if(!$wName)
		{
      $data = User::USR_getData('username,gender,birthdate,county,city,photo', $wGUID);
			
      $this->writer_guid      = $wGUID;
			$this->writer_name			= $data['username'];
			$this->writer_gender		= $data['gender'];
			$this->writer_birthdate	= $data['birthdate'];
			$this->writer_county		= $data['county'];
			$this->writer_city			= $data['city'];
			$this->writer_photo			= $data['photo'];
		}
		else
		{
			$this->writer_GUID			= $wGUID;
			$this->writer_name			= $wName;
			$this->writer_gender		= $wGender;
			$this->writer_birthdate = $wBirthdate;
			$this->writer_county		= $wCounty;
			$this->writer_city			= $wCity;
			$this->writer_photo			= $wPhoto;
		}
	}
	
	/**
  * Function: GST_postMessage();
  */
	function GST_postMessage($message, $threadGUID=FALSE)
	{
    $this->GST_setClassDB();
    
		$message = word_wrap($message,40);
		
		$this->DB->lockTables('guestbook', 'WRITE');
		//$next_auto_ID = $this->DB->getNextAutoID('guestbook');

    $message_GUID = generate_GUID();
    $thread_GUID  = ($threadGUID ? $threadGUID : $message_GUID);
    
		$qPost = $this->DB->query("INSERT INTO guestbook (".
                              "message_GUID, thread_GUID, user_GUID, writer_GUID, ".
                              "writer_name, writer_gender, writer_birthdate, ".
                              "writer_county, writer_city, writer_photo, ".
                              "message, post_date".
                              ") VALUES ('?', '?', '?', '?','?', '?', '?','?', '?', '?','?', '?')");
    
    $qPost->feed($message_GUID,         $thread_GUID,         $this->user_GUID,         $this->writer_GUID,
                 $this->writer_name,    $this->writer_gender, $this->writer_birthdate,
                 $this->writer_county,  $this->writer_city,   $this->writer_photo,
                 $message,              date("YmdHis"));
    
    $qPost->run();
		
		$this->DB->unlockTables();
    
		if($qPost->affected_rows > 0) { User::USR_setGstCounter(1); }
    
		return $qPost->affected_rows;
	}
	
	/**
  * Function: GST_setReplied();
	* Purpose: Set a messages condition to 'replied'.
	*/
	function GST_setReplied($message_GUID)
	{
    $this->GST_setClassDB();
    
		$query = $this->DB->query("UPDATE guestbook SET condition='REPLIED' WHERE message_GUID='".$message_GUID."'");
    $query->run();
    $query->destroy();
	}
	
	/**
  * Function: GST_setRead();
	* Purpose: Set a messages condition to 'read'.
	*/ 
	function GST_setRead()
	{
    $this->GST_setClassDB();
    
		$query = $this->DB->query("UPDATE guestbook SET condition='READ' WHERE condition!='REPLIED' AND user_GUID='".$this->user_GUID."'");
    $query->run();
    
		return $query->affected_rows;
	}
	
	/**
  * Function: GST_deleteOldMessages();
	* Purpose: Delete old messages. An user has 200 messages and the overflow will be deleted.
	*/
	function GST_deleteOldMessages($number=1)
	{
    $this->GST_setClassDB();
    
		$overflow = $this->DB->query("SELECT message_GUID FROM guestbook WHERE user_GUID='".$_SESSION['__user_GUID']."' ".
                                 "ORDER BY message_ID DESC LIMIT 200,".$number);
    $overflow->run();
    
    $deleted = 0;
    
		while($overflow->fetchArray($o))
		{
			$query = $this->DB->query("DELETE FROM guestbook WHERE message_GUID='".$o['message_GUID']."'");
      $query->run();
      $deleted += $overflow->affected_rows;
		}
    
    $query->destroy();
    
		return $deleted;
	}
	
	/**
  * Function: GST_deleteMessage();
	* Purpose: Delete one or more messages.
	*/
	function GST_deleteMessage($message_GUID, $check_if_read=TRUE)
	{
    $this->GST_setClassDB();
    
		$GUIDs = explode('|', $message_GUID);
		$affected = 0;
    
		for($i=0; $i<count($GUIDs); $i++)
		{
      $query = $this->DB->query("DELETE FROM guestbook WHERE message_GUID='".$GUIDs[$i]."' ".
                                ($check_if_read ? "AND condition!='UNREAD' " : "")."AND ".
                                "(user_GUID='".$_SESSION['__user_GUID']."' OR writer_GUID='".$_SESSION['__user_GUID']."')");
      $query->run();
			if($query->affected_rows == 1) { $affected--; }
		}
    
		User::USR_setGstCounter($affected);
    
		return $affected;
	}
}
?>