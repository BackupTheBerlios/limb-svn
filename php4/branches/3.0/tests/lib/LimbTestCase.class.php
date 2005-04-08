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

class LimbTestCase extends UnitTestCase
{
  function LimbTestCase($label = false, $auto_label = true)
  {
    if($label && $auto_label)
      $label = make__FILE__readable($label);

    parent :: UnitTestCase($label);
  }

  function &_createRunner(&$reporter)
  {
    if ($this->_isDebugging())
      return new SimpleRunner($this, $reporter);

    return parent::_createRunner($reporter);
  }

  function _isDebugging()
  {
    return (isset($_SERVER['SERVER_PORT']) &&  ($_SERVER['SERVER_PORT'] == 81));
  }
}

?>