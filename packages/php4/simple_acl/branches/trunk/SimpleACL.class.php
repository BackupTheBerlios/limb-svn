<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: Limb.class.php 1105 2005-02-15 13:46:50Z pachanga $
*
***********************************************************************************/
class SimpleACL
{
  var $toolkits = array();

  function & instance()
  {
    if (!isset($GLOBALS['SimpleACLGlobalInstance']) || !is_a($GLOBALS['SimpleACLGlobalInstance'], 'SimpleACL'))
      $GLOBALS['SimpleACLGlobalInstance'] =& new SimpleACL();

    return $GLOBALS['LimbGlobalInstance'];
  }

  function registerToolkit(&$toolkit)
  {
    $limb =& SimpleACL :: instance();
    $limb->toolkits[] =& $toolkit;
  }

  function & popToolkit()
  {
    $limb =& SimpleACL :: instance();
    $toolkit =& array_pop($limb->toolkits);
    return $toolkit;
  }

  function & toolkit()
  {
    $limb =& SimpleACL :: instance();
    if(sizeof($limb->toolkits) == 0)
      return false;

    return $limb->toolkits[sizeof($limb->toolkits) - 1];
  }
}

?>
