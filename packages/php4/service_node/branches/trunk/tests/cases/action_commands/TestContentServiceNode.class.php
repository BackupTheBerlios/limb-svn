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
require_once(LIMB_SERVICE_NODE_DIR . 'ContentServiceNode.class.php');

class TestContentServiceNode extends ContentServiceNode
{
  var $__class_name = 'TestContentServiceNode';

  function TestContentServiceNode()
  {
    parent :: ContentServiceNode('OneTableObjectMapperTest');
  }
}
?>
