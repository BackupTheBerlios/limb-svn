<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class Limb
{
  const STATUS_SUCCESS_MASK = 15;
  const STATUS_PROBLEM_MASK = 240;

  const STATUS_OK = 1;
  const STATUS_FORM_SUBMITTED = 2;
  const STATUS_FORM_DISPLAYED = 4;

  const STATUS_FORM_NOT_VALID = 16;
  const STATUS_ERROR = 32;
  
  static protected $toolkits = array();
  
  static public function registerToolkit($toolkit)
  {    
    self :: $toolkits[] = $toolkit;
  }
    
  static public function popToolkit()
  {
    array_pop(self :: $toolkits);
  }

  static public function toolkit()
  {
    return end(self :: $toolkits);
  }
}

?> 
