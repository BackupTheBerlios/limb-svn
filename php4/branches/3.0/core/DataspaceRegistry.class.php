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
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');

class DataspaceRegistry
{
  var $dataspaces = array();

  function & get($name)
  {
    if(isset($this->dataspaces[$name]))
      return $this->dataspaces[$name];

    $this->dataspaces[$name] = new Dataspace();

    return $this->dataspaces[$name];
  }
}

?>