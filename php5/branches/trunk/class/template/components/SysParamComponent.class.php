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
require_once(LIMB_DIR . '/class/core/SysParam.class.php');

class SysParamComponent extends Component
{
  public function getParam($name, $type)
  {
    echo SysParam :: instance()->getParam($name, $type);
  }
}

?>