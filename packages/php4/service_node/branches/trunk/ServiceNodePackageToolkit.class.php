<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: DAO.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/

class ServiceNodePackageToolkit
{
  var $locator;

  function & getServiceNodeLocator()
  {
    if(is_object($this->locator))
      return $this->locator;

    include_once(LIMB_SERVICE_NODE_DIR . '/ServiceNodeLocator.class.php');
    $this->locator = new ServiceNodeLocator();

    return $this->locator;
  }

}

?>
