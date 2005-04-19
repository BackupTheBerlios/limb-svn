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
require_once(LIMB_DIR . '/core/entity/Entity.class.php');
require_once(LIMB_DIR . '/core/NodeConnection.class.php');
require_once(LIMB_DIR . '/core/ServiceLocation.class.php');

class ServiceNode extends Entity
{
  var $__class_name = 'ServiceNode';

  function ServiceNode()
  {
    parent :: Entity();

    $this->registerPart('node', new NodeConnection());
    $this->registerPart('service', new ServiceLocation());
  }
}
?>
