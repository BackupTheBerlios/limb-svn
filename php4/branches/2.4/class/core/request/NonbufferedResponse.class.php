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
require_once(LIMB_DIR . '/class/core/request/Response.interface.php');

class NonbufferedResponse implements Response
{
  function write($string)
  {
    echo $string;
  }

  function commit()
  {
  }

  function isEmpty()
  {
    return true;
  }
}
?>