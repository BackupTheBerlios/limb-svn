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
define('LIMB_STATUS_SUCCESS_MASK', 15);
define('LIMB_STATUS_PROBLEM_MASK', 240);

define('LIMB_STATUS_OK', 1);
define('LIMB_STATUS_FORM_SUBMITTED', 2);
define('LIMB_STATUS_FORM_DISPLAYED', 4);

define('LIMB_STATUS_FORM_NOT_VALID', 16);
define('LIMB_STATUS_ERROR', 32);

class Limb
{
  var $toolkits = array();

  function & instance()
  {
    if (!isset($GLOBALS['LimbGlobalInstance']) || !is_a($GLOBALS['LimbGlobalInstance'], 'Limb'))
      $GLOBALS['LimbGlobalInstance'] =& new Limb();

    return $GLOBALS['LimbGlobalInstance'];
  }

  function registerToolkit(&$toolkit)
  {
    $limb =& Limb :: instance();
    $limb->toolkits[] =& $toolkit;
  }

  function & popToolkit()
  {
    $limb =& Limb :: instance();
    $toolkit =& array_pop($limb->toolkits);
    return $toolkit;
  }

  function & toolkit()
  {
    $limb =& Limb :: instance();
    if(sizeof($limb->toolkits) == 0)
      return false;

    return $limb->toolkits[sizeof($limb->toolkits) - 1];
  }
}

?>
