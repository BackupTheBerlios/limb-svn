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
require_once(LIMB_DIR . '/class/SysParam.class.php');

class SysParamComponent extends Component
{
  function getParam($name, $type)
  {
    $inst =& SysParam :: instance();
    echo $inst->getParam($name, $type);
  }
}

?>