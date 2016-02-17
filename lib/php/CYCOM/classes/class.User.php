<?php
/********************************************************************************\
* @File:          class.User.php                                                 *
* @Description:   Contains all functions related to the individual user.         *
*                 The DB-functions are only meant for the hej_U-database.        *
* @Author:        Lukas Kalinski                                                 *
* @Copyright:     2001-2003, CyLab Sweden                                        *
\********************************************************************************/

require_once('/home/h/hej/_include/gui/gui.common.php');
require_once('/home/h/hej/_include/system.php');
require_once('/home/h/hej/_include/functions/function.generate-GUID.php');

/**
* Class: User
*/
class User
{
	var $DB;						// Mysql class reference.
	var $user_GUID;     // The main user.
	
	/**
  * Function: User(); (Constructor)
	* Notes: Remember to send possible mysql connection link if necessary.
	*/
	function User($user_GUID=FALSE, $mysql_link=FALSE)
	{
		$this->DB = $mysql_link;
    if($this->DB !== FALSE) { $this->DB->useDB('hej_U'); }
		$this->user_GUID = $user_GUID;
	}
  
  /**
  * Function: USR_setClassDB()
  * Purpose: Select the database for this class
  */
  function USR_setClassDB()
  {
    if($this->DB !== FALSE)
      $this->DB->useDB('hej_U');
  }
  
  //-------------------------------------------------------------------------------------------//
	//	Calculating funtions                                                                     //
	//-------------------------------------------------------------------------------------------//
	
  /**
  * Function: USR_generateStats();
  */ 
  function USR_generateStats($type='30days')
  {
    $this->USR_setClassDB();
    
    # Settings - Note; these become global. Change that...
    define('POINTS__GUESTBOOK',       0.2);
    define('POINTS__MAIL',            0.3);
    define('POINTS__LOGIN',           0.2);
    define('POINTS__ONLINE_DURATION', 0.2); // Max duration (12 hours)
    define('POINTS__PHOTO',           0.1);
    define('MAX_PER_DAY',             1);
    define('MIN_PER_DAY',             0);
    $points = 0;
    
    switch($type)
    {
      case '30days':
      
        $stats = $this->DB->query("SELECT guestbook,mail,photo,online_duration,date FROM user_stats ".
                                  "WHERE user_GUID='".$this->user_GUID."' AND date>=".(date("Y").(date("m")-1).date("d"))." ".
                                  "LIMIT 30");
        $stats->run();
        
        if($stats->num_rows > 0)
        {
          $init = TRUE;
          while($stats->fetchArray($s))
          {
            if($init) $countFrom = $s['date'];
            $init = FALSE;
            $points += POINTS__LOGIN;
            if($s['guestbook'])       $points += POINTS__GUESTBOOK;
            if($s['mail'])            $points += POINTS__MAIL;
            if($s['photo'])           $points += POINTS__PHOTO;
            if($s['online_duration'])
            {
              $duration = min(($s['online_duration'] / 60) / 60, 12);
              $points += ($duration / 12) * POINTS__ONLINE_DURATION;
            }
          }
          $daysPassed = max(daysBetween(strtotime($countFrom),time()), 1);
          $points = $points / $daysPassed;
        }
      
      break;
    }
    
    return max(min($points,1),0);
  }
  
	//-------------------------------------------------------------------------------------------//
	//	Database SELECT functions                                                                //
	//-------------------------------------------------------------------------------------------//
	
	/**
  * Function: USR_resolveGUID();
	* Returns: user_GUID/FALSE.
  */
	function USR_resolveGUID($user)
	{
    $this->USR_setClassDB();
    
		if(is_numeric($user)) // Search for user_ID.
    {
			$query = $this->DB->query("SELECT user_GUID FROM users WHERE user_ID='".$user."' LIMIT 1");
    }
    elseif(preg_match('/[a-z0-9_]/i', $user)) // Search for username.
    {
      $query = $this->DB->query("SELECT user_GUID FROM users WHERE username='".$user."' LIMIT 1");
    }
		else
    {
      return FALSE;
    }
    
    $query->run();
    $query->putResultFA($user_data);
    $query->destroy();
    
  	if(strlen($user_data['user_GUID']) == 38) { return $user_data['user_GUID']; }
  	else                                      { return FALSE; }
	}
  
  /**
  * Function: USR_getData();
  * Description: Get custom data about this-user, OR (if user_GUID is set) about an other user.
  * Returns: The result on success and FALSE on failure.
  */
  function USR_getData($data, $user_GUID=FALSE)
  {
    $this->USR_setClassDB();
    
    if($user_GUID == FALSE) { $user_GUID = $this->user_GUID; }
    
    $query = $this->DB->query("SELECT ".$data." FROM users WHERE user_GUID='".$user_GUID."' ".
                              "AND account_status='ACTIVE' LIMIT 1");
    $query->run();
    $query->putResultFA($result);
    
    if($query->num_rows == 1)
    {
      $query->destroy();
      return $result;
    }
    else
    {
      $query->destroy();
      return FALSE;
    }
  }
  
  /**
  * Function: USR_getPresData();
  * Description: Get custom data from the user-presentation table belonging to this-user.
  */ 
  function USR_getPresData($data)
  {
    $this->USR_setClassDB();
    
    $query = $this->DB->query("SELECT ".$data." FROM user_presentations WHERE user_GUID='".$this->user_GUID."' LIMIT 1");
    $query->run();
    $query->putResultFA($result);
    $query->destroy();
    
    return $result;
  }
  
	//-------------------------------------------------------------------------------------------//
	//	Database INSERT functions                                                                //
	//-------------------------------------------------------------------------------------------//
	
	/**
  * Function: USR_addVisitor();
	*/
	function USR_addVisitor($visitor_GUID)
	{
    $this->USR_setClassDB();
    
    $delFromLog = $this->DB->query("DELETE FROM visitors WHERE visitor_GUID='".$visitor_GUID."' AND ".
                                   "user_GUID='".$this->user_GUID."'");
    $delFromLog->run();
    
    $counterIncremented = FALSE;
		if($delFromLog->affected_rows == 0) // ändra ? (man får inte vara i besöksloggen för att besöksräknaren ska ökas på, kanske?)
		{
      $this->USR_setVisitorCounter(1);
      $counterIncremented = TRUE;
		}
    
    $delFromLog->destroy();
    
		# Add a new field.
    $query = $this->DB->query("INSERT INTO visitors (user_GUID,visitor_GUID) VALUES ('?','?')");
    $query->feed($this->user_GUID, $visitor_GUID);
		$query->run();
		
		# Delete the oldest field if there are over 15 visitors.
    $visitors = $this->DB->query("SELECT visitor_ID FROM visitors WHERE user_GUID='".$this->user_GUID."' ".
                                 "ORDER BY visit_date ASC LIMIT 16");
    $visitors->run();
    
		if($visitors->num_rows > 10)
		{
      $visitors->fetchArray($v);
      $visitors->destroy();
      $query = $this->DB->query("DELETE FROM visitors WHERE visitor_ID=".$v['visitor_ID']);
			$query->run();
		}
    
    return $counterIncremented;
	}
  
	//-------------------------------------------------------------------------------------------//
	//  Database UPDATE functions                                                                 //
	//-------------------------------------------------------------------------------------------//
	
  function USR_setOnlineStatus($status_ID)
  {
    $this->USR_setClassDB();
    
    $query = $this->DB->query("UPDATE users SET online_status=? WHERE user_GUID='".$this->user_GUID."'");
    $query->feed($status_ID);
		$query->run();
    $query->destroy();
  }
  
	function USR_setGstCounter($num)
	{
    $this->USR_setClassDB();
    
    $query = $this->DB->query("UPDATE users SET gst_counter=gst_counter+(?) WHERE user_GUID='".$this->user_GUID."'");
    $query->feed($num);
		$query->run();
    $query->destroy();
	}
  
	function USR_setVisitorCounter($num)
	{
    $this->USR_setClassDB();
    
    $query = $this->DB->query("UPDATE users SET visitor_counter=visitor_counter+(?) WHERE user_GUID='".$this->user_GUID."'");
    $query->feed($num);
		$query->run();
    $query->destroy();
	}
  
  //-------------------------------------------------------------------------------------------//
	//	Verification functions                                                                   //
	//-------------------------------------------------------------------------------------------//
	
	/**
  * Function: USR_authenticate();
	* Purpose: Authenticates a user. Returns TRUE if username/password or user_GUID/password exists.
	*/
	function USR_authenticate($username, $password)
	{
    exit('Fix class.User.php on line: '.__LINE__);
    $query = $this->DB->query("SELECT user_ID FROM users WHERE (".
															($username ? "username='".$username."'" : "user_GUID='".$this->user_GUID."'").
															"AND password='".$password."') LIMIT 1");
    
		if(mysql_num_rows($this->DB->DB_runQuery()) > 0) { return TRUE; }
		else { return FALSE; }
	}
		
	/**
  * Function: USR_checkUsername();
	* Requirements: $password length 2-15 chars. Valid chars: [a-z] [A-Z] [0-9] and _
  */
	function USR_checkUsername($username, &$err)
	{
    $this->USR_setClassDB();
    
    exit('Fix class.User.php on line: '.__LINE__);
    
    $query = $this->DB->query("SELECT user_ID FROM users WHERE username='".$username."'");
		if(mysql_num_rows($this->DB->DB_runQuery()) > 0)
		{
			$err = 'Användarnamnet '.$username.' är redan upptaget.';
			return FALSE;
		}
		elseif(eregi("^[_0-9a-z]*$",$username) && strlen($username) >= 2 && strlen($username) <= 16)
		{
			$err = FALSE;
			return TRUE;
		}
		else
		{
			system_error(__FILE__, $_SERVER['SCRIPT_FILENAME'], __LINE__,
                   'Username too long/short or containing forbidden chars.<br>'.
									 '$username = '.$username,
                   FALSE, FALSE, TRUE);
			return FALSE;
		}
	}
	
	/**
  * Function: USR_checkPassword();
	* Requirements: $password length 6-15 chars. Valid chars: [a-z] [A-Z] [0-9] and _
  */
	function USR_checkPassword($password)
	{
		if(eregi("^[_0-9a-z]{6,16}$",$password)) { return TRUE; }
		else { return FALSE; }
	}
	
	/**
  * Function: USR_checkName();
	* Requirements: $first_name -> length 2-15 chars AND $last_name -> length 2-20 chars
  */
	function USR_checkName($first_name,$last_name)
	{
		if(
				strlen($first_name) < 2 || strlen($first_name) > 15 ||
				strlen($last_name) < 2 || strlen($last_name) > 20
			)
		{
			return FALSE;
		}
		else { return TRUE; }
	}
	
	/**
  * Function: USR_checkPrivacy();
	* Requirements: $level must be found in $levels
  */
	function USR_checkPrivacy($level, $levels=array('NONE','FRIENDS','ALL'))
	{
		if(in_array($level,$levels)) { return TRUE; }
		else { return FALSE; }
	}
	
	/**
  * Function: USR_checkGender();
	* Requirements: $gender must be "M" or "F"
  */
	function USR_checkGender($gender)
	{
		if($gender == "M" || $gender == "F") { return TRUE; }
		else { return FALSE; }
	}
	
	/**
  * Function: USR_checkEmail();
	* Requirements: 
  */
	function USR_checkEmail($email, $key)
	{
		if($key[0] == $key[1])     { return TRUE; }
		elseif($key[0] != $key[1]) { return FALSE; }
		elseif($key[0] != $key[1] && $tried_keys > 3)
		{
			system_error(__FILE__, $_SERVER['SCRIPT_FILENAME'], __LINE__,
                   "User tried over 3 keys<br><br>".
									 "$key[0] -> ".$key[0]."<br>".
									 "$key[1] -> ".$key[1]."<br>",
                   FALSE, FALSE, TRUE);
		}
	}
	
	/**
  * Function: USR_checkGeo();
	* Requirements: Location must exist in geo_cities and strlen($district) <= 20
  */
	function USR_checkGeo($country_ID, $county_ID, $city_ID, $district)
	{
		if(
				mysql_num_rows($this->DB->DB_runQuery("SELECT city_ID FROM |geo_cities| WHERE ".
																								 "city_ID='".$city_ID."' AND country_ID='".$country_ID."' AND ".
																								 "county_ID='".$county_ID."' LIMIT 1",TRUE)) == 1 &&
				strlen($district) <= 20
			)
		{
			return TRUE;
		}
		else { return FALSE; }
	}
	
	/**
  * Function: USR_checkLanguage();
	*/
	function USR_checkLanguage($language_ID)
	{
		if($language_ID == 1) { return TRUE; }
		else { return FALSE; }
	}
	
	/**
  * Function: USR_checkBirthdate();
	* Requirements: A valid date.
  */
	function USR_checkBirthdate($date)
	{
		$month = substr($date,4,2);
		$day = substr($date,6,2);
		$year = substr($date,0,4);
		if(checkdate($month, $day, $year)) { return TRUE; }
		else { return FALSE; }
	}
}
?>