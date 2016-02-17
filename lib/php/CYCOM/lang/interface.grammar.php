<?php
/********************************************************************************\
 * File:          lang/interface.lang.php
 * Description:   A language interface which should be implemented for every language.
 * Begin:         2006-02-27
 * Edit:          
 * Author:        Lukas Kalinski
 * Copyright:     2006- CyLab
\********************************************************************************/

interface grammar
{
  /**
   * @param string $possessor
   * @param string $possession
   * @return string
   */
  public static function possession($possessor, $possession);
}
?>