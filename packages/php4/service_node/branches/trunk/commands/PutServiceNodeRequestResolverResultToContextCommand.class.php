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
require_once(LIMB_DIR . '/core/commands/PutRequestResolverResultToContextCommand.class.php');

class PutServiceNodeRequestResolverResultToContextCommand extends PutRequestResolverResultToContextCommand
{
  function PutServiceNodeRequestResolverResultToContextCommand($entity_field_name)
  {
    parent :: PutRequestResolverResultToContextCommand('service_node', $entity_field_name);
  }
}

?>
