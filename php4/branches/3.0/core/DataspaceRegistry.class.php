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
  function & get($name)
  {
    $obj = null;

    $instance_name = "global_dataspace_instance_{$name}";

    if(isset($GLOBALS[$instance_name]))
      $obj =& $GLOBALS[$instance_name];

    if(!is_a($obj, 'Dataspace'))
    {
      $obj =& new Dataspace();
      $GLOBALS[$instance_name] =& $obj;
    }

    return $obj;
  }
}

?>