<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

function is_a($object, $classname)
{
  return ((strtolower($classname) == get_class($object))
    or (is_subclass_of($object, $classname)));
}

function var_export($a)
{
  $result = "";
  switch (gettype($a))
  {
    case "array":
      reset($a);
      $result = "array(";
      while (list($k, $v) = each($a))
      {
        if (is_numeric($k))
          $key = $k;
        else
          $key = "'" . addslashes($k) . "'";

        $result .= "{$key} => " . var_export($v) . ", ";
      }
      $result .= ")";
      break;
    case "string":
      $result = "'" . addslashes($a) . "'";
      break;
    case "boolean":
      $result = ($a) ? "true" : "false";
      break;
    default:
      $result = $a;
      break;
  }
  return $result;
}

?>