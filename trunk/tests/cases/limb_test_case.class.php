<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class LimbTestCase extends UnitTestCase 
{
  function LimbTestCase($label = false) 
  {
    parent :: UnitTestCase($label);
  }
  
  function &_createRunner(&$reporter) 
  {
    if ($this->_isDebugging()) 
      return SimpleRunner($this, $reporter);
    
    return parent::_createRunner($reporter);
  }
  
  function _isDebugging() 
  {
    return (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == 81));
  }
}

?>