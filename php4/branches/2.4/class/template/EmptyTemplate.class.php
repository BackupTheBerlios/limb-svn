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
  function findParentByClass($class)
  {
    return null;
  }

  function findChildByClass($class)
  {
    return null;
  }

  function findChild($server_id)
  {
    return null;
  }

  function getChild($server_id)
  {
    return null;
  }

  function display()
  {
    throw new LimbException('template is empty');
  }
}

?>