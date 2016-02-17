<?php
/********************************************************************************\
 * File:          system/class.User_Session.php
 * Description:   CYCOM User Session class.
 * Notes:         
 * Begin:         2006-01-15
 * Edit:          2006-03-04
 * Author:        Lukas Kalinski
 * Copyright:     2005-2006 CyLab Sweden
\********************************************************************************/

class User_Session
{
  // ## Session data
  private $logged_in        = false;  // @var bool
  private $login_timestamp  = 0;      // @var int
  
  // ## User data
  private $admin = false;  // @var bool
  private $uid = null;        // @var string
  private $data = array();    // @var string[]
  
  // ## Admin data
  private $permissions = null;   // @var integer
  
  /**
   * Post event tracker (any post event: internal mail, guestbook, forum etc).
   * Stores information about the last post and is used to check if the user tries to 
   * perform message flooding.
   */
  protected $post_event = array('type' => NULL, 'target_id' => NULL, 'timestamp' => 0);
  
  /**
   * Constructor (string)
   * @param string $user_id
   * @param bool $is_admin
   * @param aarray $permissions
   */
  public function __construct($user_id, $is_admin=false, $permissions=null)
  {
    $this->uid = $user_id;
    
    if($is_admin)
    {
      $this->admin = true;
      $this->permissions = $permissions;
    }
  }
  
  /**
   * @return bool
   */
  public function is_admin()
  {
    return $this->admin;
  }
  
  /**
  * @desc Returns current user's ID.
  * @return string
  */
  public function get_uid()
  {
    return $this->uid;
  }
  
  /**
   * @desc Returns value in private array: data[$key]
   * @param string $key
   * @return mixed
   */
  public function get($key)
  {
    return $this->data[$key];
  }
  
  /**
   * @return void
   */
  public function set($key, $value)
  {
    $this->data[$key] = $value;
  }
  
  /**
   * @desc Returns true if the user is logged in, false otherwise.
   * @return bool
   */
  public function is_logged_in()
  {
    return $this->logged_in;
  }
  
  /**
   * @desc Marks user as logged in if $b==true and not logged in if $b==false.
   * @param bool $b
   * @return void
   */
  public function set_logged_in($b)
  {
    $this->logged_in = $b;
  }
  
  // ##
  // ## Admin level ->
  
  /**
   * @param string $flag
   * @return bool
   */
  public function has_permission($flag)
  {
    return ($this->permissions != null && key_exists(md5($flag), $this->permissions));
  }
}
?>