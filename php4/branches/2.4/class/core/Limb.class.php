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

class Limb
{
  const STATUS_SUCCESS_MASK = 15;
  const STATUS_PROBLEM_MASK = 240;

  const STATUS_OK = 1;
  const STATUS_FORM_SUBMITTED = 2;
  const STATUS_FORM_DISPLAYED = 4;

  const STATUS_FORM_NOT_VALID = 16;
  const STATUS_ERROR = 32;

  var $toolkits = array();

  function registerToolkit($toolkit)
  {
    Limb :: $toolkits[] = $toolkit;
  }

  function popToolkit()
  {
    array_pop(Limb :: $toolkits);
  }

  function toolkit()
  {
    return end(Limb :: $toolkits);
  }
}

?>
