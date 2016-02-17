<?php
/********************************************************************************\
 * File:          lang/lib.UTF8_Chars.php
 * Description:   
 * Notes:         !!! Not in use yet.
 * Ext libs:      
 * Begin:         2006-03-19
 * Edit:          
 * Author:        Lukas Kalinski
 * Copyright:     2006- CyLab
\********************************************************************************/

require_once('function.unichr.php');
require_once('function.uniord.php');

define('LANG__CHARMAP_ALPHA_CHAR_HEX_START', 0x00BE);
define('LANG__CHARMAP_ALPHA_CHAR_HEX_END',   0x0233);

class UTF8_Chars
{
  private $convmap = array(LANG__CHARMAP_ALPHA_CHAR_HEX_START,
                           LANG__CHARMAP_ALPHA_CHAR_HEX_END,
                           0x0000, 0xFFFF);
  
  /**
   * @desc Case-sorted alphanum characters.
   * @var array
   * 
   * ### IMPORTANT ###
   *   - Do not change the entry order, once added and used the order stays.
   *     Therefore new characters must be added at the end.
   *   - Case-rule: index 0 = Upper-case, index 1 = Lower-case,
   *     index 2 = Upper-case, index 3 = Lower-case,
   *     ... and so on.
   *   - Characters are represented by their ascii hex number: XXXX
   *     (always four characters, zerofill if necessary).
   *   - Upper and lower case chars must be added simultaneously (=same index for both).
   */
  private $uc_chars = array();
  private $lc_chars = array();
  
  public function __construct()
  {
    
  }
  
  public function get_uc_chars() { return $this->uc_chars; }
  public function get_lc_chars() { return $this->lc_chars; }
  public function get_convmap() { return $this->convmap; }
  
  /**
   * @param string{hex}/int $upper
   * @param string{hex}/int $lower
   * @return void
   */
  private function add_alphapair($upper, $lower)
  {
    if(gettype($upper) == 'string')
      $upper = hexdec($upper);
    if(gettype($lower) == 'string')
      $lower = hexdec($lower);
    
    array_push($this->uc_hars, Cylib__unichr($upper));
    array_push($this->lc_hars, Cylib__unichr($lower));
  }
}
?>