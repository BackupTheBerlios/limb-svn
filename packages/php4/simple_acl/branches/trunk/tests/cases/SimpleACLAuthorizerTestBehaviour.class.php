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
require_once(LIMB_DIR . '/core/behaviours/Behaviour.class.php');

class SimpleACLAuthorizerTestBehaviour extends Behaviour
{
  function getReadActionProperties()
  {
    return array('access' => 1);
  }

  function getEditActionProperties()
  {
    return array('access' => 2);
  }

  function getCreateActionProperties()
  {
    return array('access' => 4);
  }

  function getDeleteActionProperties()
  {
    return array('access' => 128);
  }

  function getActionsList()
  {
    return array('read', 'edit', 'create', 'delete');
  }
}


?>

