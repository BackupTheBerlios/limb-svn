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
class EmptyTemplate
{
  public function findParentByClass($class)
  {
    return null;
  }

  public function findChildByClass($class)
  {
    return null;
  }

  public function findChild($server_id)
  {
    return null;
  }

  public function getChild($server_id)
  {
    return null;
  }

  public function display()
  {
    throw new LimbException('template is empty');
  }
}

?>