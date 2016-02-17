<?php
/********************************************************************************\
* @File:          class.MIL_Account.php                                          *
* @Description:   Internal mail account class.                                   *
* @Author:        Lukas Kalinski                                                 *
* @Copyright:     2001-2003, CyLab Sweden                                        *
\********************************************************************************/

require_once('/home/h/hej/_include/system/system.mysql.php');
require_once('/home/h/hej/_include/system/system.session.php');
require_once('/home/h/hej/_include/classes/class.User.php');
require_once('/home/h/hej/_include/functions/function.generate-GUID.php');

# Errors
define('ERROR__REVCEIVER_NOT_FOUND', 'Mottagaren kunde inte hittas.');

/**
* Class: Mil_Account
*/
class MIL_Account extends User
{
  var $error;
  var $writer_GUID, $writer_name, $writer_gender, $writer_birthdate;
  
  /**
  * Constructor
  */
  function MIL_Account($user_GUID, $mysql_link=FALSE)
  {
    $this->user_GUID        = $user_GUID;
    $this->DB               = $mysql_link;
    $this->writer_GUID      = NULL;
    $this->writer_name      = NULL;
    $this->writer_gender    = NULL;
    $this->writer_birthdate = NULL;
    $this->error            = '';
  }
  
  /**
  * Function: MIL_setClassDB()
  * Purpose: Select the database for this class
  */
  function MIL_setClassDB()
  {
    if($this->DB !== FALSE)
      $this->DB->useDB('hej_M');
  }
  
  /**
  * Function: MIL_getMessages();
  * Returns: A DB_query-class instance containing the query-result.
  */
  function MIL_getMessages($from_limit, $to_limit, $folder, $folder_GUID=FALSE)
  {
    $this->MIL_setClassDB();
    
    switch($folder)
    {
      case 'inbox':
        $query = $this->DB->query("SELECT mail_GUID, writer_GUID, writer_name, writer_gender, ".
                                  "writer_birthdate, mail_subject, condition, post_date ".
                                  "FROM mail WHERE receiver_GUID='".$this->user_GUID."' AND ".
                                  "inbox_status='EXISTS' AND folder_GUID=".($folder_GUID ? "'".$folder_GUID."' " : "1 ").
                                  "ORDER BY mail_ID DESC LIMIT ".$from_limit.",".$to_limit);
      break;
      case 'outbox':
        $query = $this->DB->query("SELECT mail_GUID, receiver_GUID, receiver_name, receiver_gender, ".
                                  "receiver_birthdate, mail_subject, condition, post_date ".
                                  "FROM mail WHERE writer_GUID='".$this->user_GUID."' AND ".
                                  "outbox_status='EXISTS' ".
                                  "ORDER BY mail_ID DESC LIMIT ".$from_limit.",".$to_limit);
      break;
    }
    
    $query->run();
    
    return $query;
  }
  
  /**
  * Function: MIL_getFolders();
  * Returns: A DB_query-class instance containing the query-result.
  */
  function MIL_getFolders($data)
  {
    $this->MIL_setClassDB();
    
    $query = $this->DB->query("SELECT ".$data." FROM folders WHERE user_GUID='".$this->user_GUID."' ".
                              "ORDER BY folder_ID DESC");
    $query->run();
    
    return $query;
  }
  
  /**
  * Function: MIL_addFolder();
  */
  function MIL_addFolder($folder_name)
  {
    $this->MIL_setClassDB();
    
    $this->DB->lockTables('folders');
    
    //$next_auto_ID = $this->DB->getNextAutoID("folders");
    $query = $this->DB->query("INSERT INTO folders (folder_GUID,user_GUID,folder_name,date) VALUES ('?','?','?','?')");
    $query->feed(generate_GUID(), $this->user_GUID, $folder_name, date('YmdHis'));
    $query->run();
    $query->destroy();
    
    $this->DB->unlockTables();
  }
  
  /**
  * Function: MIL_deleteFolder();
  */
  function MIL_deleteFolder($folder_GUID)
  {
    $this->MIL_setClassDB();
    
    $query = $this->DB->query("SELECT mail_GUID FROM mail WHERE folder_GUID='".$folder_GUID."' AND ".
                              "receiver_GUID='".$this->user_GUID."'");
    $query->run();
    
    while($query->fetchArray($r))
    {
      $this->MIL_delete($r['mail_GUID']);
    }
    
    $query = $this->DB->query("DELETE FROM folders WHERE folder_GUID='".$folder_GUID."'");
    $query->run();
  }
  
  /**
  * Function: MIL_setFolder();
  * Returns: affected rows
  */
  function MIL_setFolder($mail_GUID, $folder_GUID)
  {
    $this->MIL_setClassDB();
    
    $query = $this->DB->query("UPDATE mail SET folder_GUID='".$folder_GUID."' WHERE mail_GUID='".$mail_GUID."' AND ".
                              "receiver_GUID='".$this->user_GUID."'");
    $query->run();
    
    return $query->affected_rows;
  }
  
  /**
  * Function: MIL_counter();
  */
  function MIL_counter($folder_GUID, $num)
  {
    $this->MIL_setClassDB();
    
    $query = $this->DB->query("UPDATE folders SET mail_counter=mail_counter+(?) WHERE ".
                              "folder_GUID='".$folder_GUID."' AND user_GUID='".$this->user_GUID."'");
    $query->feed($num);
    $query->run();
  }
  
  /**
  * Function: MIL_getFolderData();
  */
  function MIL_getFolderData($folder_GUID, $data)
  {
    $this->MIL_setClassDB();
    
    $query = $this->DB->query("SELECT ".$data." FROM folders WHERE folder_GUID='".$folder_GUID."' ".
                              "AND user_GUID='".$this->user_GUID."'");
    $query->run();
    $query->putResultFA($result);
    $query->destroy();
    
    return $result;
  }
  
  /**
  * Function: MIL_delete();
  */
  function MIL_delete($mail_GUID)
  {
    $this->MIL_setClassDB();
    
    $query = $this->DB->query("SELECT receiver_GUID,writer_GUID,inbox_status,outbox_status ".
                                     "FROM mail WHERE mail_GUID='".$mail_GUID."'",TRUE,TRUE);
    $query->run();
    $query->putResultFA($mil);
    $query->destroy();
    
    if($mil['inbox_status'] == 'DELETED' || $mil['outbox_status'] == 'DELETED')    // Delete BOTH - delete premanently.
    {
      $query = $this->DB->query("DELETE FROM mail WHERE mail_GUID='".$mail_GUID."' AND ".
                                "(writer_GUID='".$this->user_GUID."' OR receiver_GUID='".$this->user_GUID."')");
    }
    elseif($mil['receiver_GUID'] == $this->user_GUID)                              // Delete from INBOX.
    {
      $query = $this->DB->query("UPDATE mail SET inbox_status='DELETED' WHERE mail_GUID='".$mail_GUID."' AND ".
                                "(writer_GUID='".$this->user_GUID."' OR receiver_GUID='".$this->user_GUID."')");
    }
    elseif($mil['writer_GUID'] == $this->user_GUID)                                // Delete from OUTBOX.
    {
      $query = $this->DB->query("UPDATE mail SET outbox_status='DELETED' WHERE mail_GUID='".$mail_GUID."' AND ".
                                "(writer_GUID='".$this->user_GUID."' OR receiver_GUID='".$this->user_GUID."')");
    }
    
    $query->run();
    
    return $query->affected_rows;
  }
  
  /**
  * Function: MIL_permDelete();
  */
  function MIL_permDelete($mail_GUID)
  {
    $this->MIL_setClassDB();
    
    $query = $this->DB->query("DELETE FROM mail WHERE mail_GUID='".$mail_GUID."' AND (receiver_GUID='".$this->user_GUID."' ".
                              "OR writer_GUID='".$this->user_GUID."')");
    $query->run();
    
    return $query->affected_rows;
  }
  
  /**
  * Function: MIL_getMailData();
  */
  function MIL_getMailData($mail_GUID, $folder='inbox', $data=FALSE)
  {
    $this->MIL_setClassDB();
    
    if(!$data)
    {
      if($folder == 'folder') { $folder = 'inbox'; }
      
      switch($folder)
      {
        case 'inbox':
          $query = $this->DB->query("SELECT receiver_GUID, writer_GUID, writer_name, ".
                                    "writer_gender, writer_birthdate, mail_subject, ".
                                    "mail_content, condition, post_date, folder_GUID ".
                                    "FROM mail WHERE mail_GUID='".$mail_GUID."' AND inbox_status='EXISTS' ".
                                    "AND receiver_GUID='".$this->user_GUID."' LIMIT 1");
        break;
        case 'outbox':
          $query = $this->DB->query("SELECT writer_GUID, receiver_name, receiver_gender, ".
                                    "receiver_birthdate, mail_subject, ".
                                    "mail_content, condition, post_date, folder_GUID ".
                                    "FROM mail WHERE mail_GUID='".$mail_GUID."' AND outbox_status='EXISTS' ".
                                    "AND writer_GUID='".$this->user_GUID."' LIMIT 1");
        break;
      }
    }
    else
    {
      $query = $this->DB->query("SELECT ".$data." FROM mail WHERE mail_GUID='".$mail_GUID."' ".
                                "AND (writer_GUID='".$this->user_GUID."' OR receiver_GUID='".$this->user_GUID."') LIMIT 1");
    }
    
    $query->run();
    $query->putResultFA($result);
    $query->destroy();
    
    return $result;
  }
  
  /**
  * Function: MIL_getReceiverData();
  */
  function MIL_getReceiverData($receiver_GUID)
  {
    return User::USR_getData('username,gender,birthdate', $receiver_GUID);
  }
  
  /**
  * Function: MIL_setWriterData()
  */
  function MIL_setWriterData($w_GUID, $w_name=FALSE, $w_gender=FALSE, $w_birthdate=FALSE)
  {
    if($w_name === FALSE)
    {
      $data = User::USR_getData('username,gender,birthdate', $w_GUID);
      
      if(empty($data['username']))
      {
        system_error(__FILE__, $_SERVER['SCRIPT_FILENAME'], __LINE__,
                     'Receiver not found. GUID: '.$w_GUID,
                     FALSE, FALSE, TRUE, 2);
      }
      
      $this->writer_GUID      = $w_GUID;
      $this->writer_name      = $data['username'];
      $this->writer_gender    = $data['gender'];
      $this->writer_birthdate = $data['birthdate'];
    }
    else
    {
      $this->writer_GUID      = $w_GUID;
      $this->writer_name      = $w_name;
      $this->writer_gender    = $w_gender;
      $this->writer_birthdate = $w_birthdate;
    }
  }
  
  /**
  * Function: MIL_post();
  */
  function MIL_post($receiver_GUID, $subject, $content, $storeInOutbox)
  {
    $r = User::USR_getData('username,gender,birthdate', $receiver_GUID);
    
    if(empty($r['username']))
    {
      $this->MIL_raiseError(ERROR__REVCEIVER_NOT_FOUND);
      return FALSE;
    }
    
    if($storeInOutbox) { $outbox_status = 'EXISTS';  }
    else               { $outbox_status = 'DELETED'; }
    
    $this->MIL_setClassDB();
    
    $this->DB->lockTables('mail');
    //$next_auto_ID = $this->DB->getNextAutoID("mail");
    $query = $this->DB->query("INSERT INTO mail (".
                              "mail_GUID, receiver_GUID, receiver_name, ".
                              "receiver_gender, receiver_birthdate, writer_GUID, ".
                              "writer_name, writer_gender, writer_birthdate, ".
                              "mail_subject, mail_content, post_date, ".
                              "inbox_status, outbox_status".
                              ") VALUES ('?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?')");
    $query->feed(generate_GUID(),     $receiver_GUID,       $r['username'], 
                 $r['gender'],        $r['birthdate'],      $this->writer_GUID,
                 $this->writer_name,  $this->writer_gender, $this->writer_birthdate,
                 $subject,            $content,             date('YmdHis'),
                 'EXISTS',            $outbox_status);
    $query->run();
    $query->destroy();
    
    $this->DB->unlockTables();
    
    return TRUE;
  }
  
  /**
  * Function: MIL_setReplied();
  */
  function MIL_setReplied($mail_GUID)
  {
    $this->MIL_setClassDB();
    
    $query = $this->DB->query("UPDATE mail SET condition='REPLIED' WHERE mail_GUID='".$mail_GUID."' ".
                              "AND (writer_GUID='".$this->user_GUID."' OR receiver_GUID='".$this->user_GUID."')");
    $query->run();
    $query->destroy();
  }
  
  /**
  * Function: MIL_setRead();
  */
  function MIL_setRead($mail_GUID)
  {
    $this->MIL_setClassDB();
    
    $query = $this->DB->query("UPDATE mail SET condition='READ' WHERE mail_GUID='".$mail_GUID."' ".
                              "AND receiver_GUID='".$this->user_GUID."'");
    $query->run();
    $query->destroy();
  }
  
  /**
  * Function: MIL_raiseError()
  */
  function MIL_raiseError($err)
  {
    $this->error = $err;
  }
  
  /**
  * Function: MIL_getError()
  */
  function MIL_getError()
  {
    return $this->error;
  }
}
?>