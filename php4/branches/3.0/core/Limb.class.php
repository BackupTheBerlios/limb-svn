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

require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');

class Limb
{
  var $toolkits = array(array());

  function & instance()
  {
    if (!isset($GLOBALS['LimbGlobalInstance']) || !is_a($GLOBALS['LimbGlobalInstance'], 'Limb'))
      $GLOBALS['LimbGlobalInstance'] =& new Limb();

    return $GLOBALS['LimbGlobalInstance'];
  }

  function registerToolkit(&$toolkit, $name = 'default')
  {
    $limb =& Limb :: instance();
    $limb->toolkits[$name][] =& $toolkit;
  }

  function & restoreToolkit($name = 'default')
  {
    $limb =& Limb :: instance();

    if(!isset($limb->toolkits[$name]) || sizeof($limb->toolkits[$name]) == 0)
      return false;

    $toolkit =& array_pop($limb->toolkits[$name]);
    return $toolkit;
  }

  function & toolkit($name = 'default')
  {
    $limb =& Limb :: instance();

    if(!isset($limb->toolkits[$name]) || sizeof($limb->toolkits[$name]) == 0)
      return false;

    return $limb->toolkits[$name][sizeof($limb->toolkits[$name]) - 1];
  }

  function saveToolkit($name = 'default')
  {
    //no &, we simply make a copy
    $toolkit = clone(Limb :: toolkit($name));
    $toolkit->reset();
    Limb :: registerToolkit($toolkit, $name);
  }
}

Limb :: registerToolkit(new LimbBaseToolkit());//???

?>
