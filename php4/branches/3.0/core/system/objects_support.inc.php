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
require_once(WACT_ROOT . '/util/delegate.inc.php');

if (version_compare(phpversion(), '5.0') < 0)
{
  eval('
    function clone($object)
    {
      return $object;
    }
  ');
}

class LimbHandle extends Handle
{
  function LimbHandle($class, $args = array())
  {
    parent :: Handle(LimbHandle :: _processClass($class), $args);
  }

  function _processClass($class)
  {
    if(preg_match('~(\\\|/)([^\\\/.|]+)$~', $class, $matches))
      return $class . '.class.php|' . $matches[2];
    else
      return $class;
  }
}

function & instantiateSessionObject($class_name, &$arguments)
{
  if(	!isset($_SESSION['global_session_singleton_'. $class_name]) ||
      !is_a($_SESSION['global_session_singleton_'. $class_name], $class_name))
  {
    $object =& Handle :: resolve(new LimbHandle($class_name, $arguments));

    $_SESSION['global_session_singleton_' . $class_name] =& $object;
  }
  else
    $object =& $_SESSION['global_session_singleton_' . $class_name];

  return $object;
}

?>