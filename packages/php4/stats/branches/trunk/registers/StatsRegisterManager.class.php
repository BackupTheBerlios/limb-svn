<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class StatsRegisterManager
{
  var $registers = array();

  function addRegister(&$register)
  {
    $this->registers[] =& $register;
  }

  function register($stats_request)
  {
    foreach(array_keys($this->registers) as $key)
      $this->registers[$key]->register($stats_request);
  }
}

?>