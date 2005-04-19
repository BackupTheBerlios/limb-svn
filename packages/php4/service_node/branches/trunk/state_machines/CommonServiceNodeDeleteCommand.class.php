<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CrudMainService.class.php 23 2005-02-26 18:11:24Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/StateMachineCommand.class.php');

class CommonServiceNodeDeleteCommand extends StateMachineCommand
{
  function CommonServiceNodeDeleteCommand()
  {
    parent :: StateMachineCommand();

    $entity_field_name = 'entity';

    $this->registerState('init_object',
                          new LimbHandle(LIMB_DIR . '/core/commands/PutCurrentEntityToContextCommand',
                                         array($entity_field_name)),
                          array(LIMB_STATUS_OK => 'delete',
                                LIMB_STATUS_ERROR => 'error'));

    $this->registerState('delete',
                          new LimbHandle(LIMB_SERVICE_NODE_DIR .
                                         '/commands/DeleteServiceNodeCommand',
                                         array($entity_field_name)),
                          array(LIMB_STATUS_OK => 'redirect',
                                LIMB_STATUS_ERROR => 'error'));

    $this->registerState('redirect',
                          new LimbHandle(LIMB_DIR . '/core/commands/RedirectToParentNodeCommand',
                                         array($entity_field_name)));

    $this->registerState('error',
                          new LimbHandle(LIMB_DIR . '/core/commands/UseViewCommand',
                                         array('/restricted.html')),
                          array(LIMB_STATUS_OK => 'render'));

    $this->registerState('render',
                          new LimbHandle(LIMB_DIR . '/core/commands/DisplayViewCommand'));
  }
}

?>