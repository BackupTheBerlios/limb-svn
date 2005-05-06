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
require_once(LIMB_SERVICE_NODE_DIR . 'ServiceNode.class.php');

// Note: abstract class !!
class ContentServiceNode extends ServiceNode
{
  function ContentServiceNode()
  {
    parent :: ServiceNode();

    $this->registerPart('content', new Object());
  }

  function & getContentPart()
  {
    return $this->getPart('content');
  }
}
?>
