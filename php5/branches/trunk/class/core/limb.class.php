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
  static protected $toolkit;
  
  static public function registerToolkit($toolkit)
  {
    self :: $toolkit = $toolkit;
  }

  static public function toolkit()
  {
    return self :: $toolkit;
  }
}

?> 
