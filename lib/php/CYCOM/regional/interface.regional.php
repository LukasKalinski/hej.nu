<?php
/********************************************************************************\
 * File:          regional/interface.regional.php
 * Description:   A regional interface which should be implemented for every region.
 * Begin:         2006-02-26
 * Edit:          
 * Author:        Lukas Kalinski
 * Copyright:     2006- CyLab
\********************************************************************************/

interface regional
{
  public static function ssnValid(&$_ssn, $_dob, $_gender);
}
?>