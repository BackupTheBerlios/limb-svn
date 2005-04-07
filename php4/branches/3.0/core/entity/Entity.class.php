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
require_once(LIMB_DIR . '/core/Object.class.php');

class Entity extends Object
{
  var $parts = array();

  function & getPart($name)
  {
    if(isset($this->parts[$name]))
    {
       $part =& Handle :: resolve($this->parts[$name]);
       $this->parts[$name] =& $part;
      return $this->parts[$name];
    }
  }

  function registerPart($name, $handle)
  {
    $this->parts[$name] = $handle;
  }

  function & getParts()
  {
    $parts = array();
    foreach(array_keys($this->parts) as $name)
    {
      $parts[$name] =& $this->getPart($name);
    }
    return $parts;
  }

  function export()
  {
     $res = parent :: export();

    foreach(array_keys($this->parts) as $name)
    {
      $part =& $this->getPart($name);
      $export = $part->export();

      foreach($export as $key => $data)
        $res['_' . $name . '_' . $key] = $data;
    }

    return $res;
  }
}

?>
