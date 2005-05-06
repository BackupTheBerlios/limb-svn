<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CrudMainBehaviour.class.php 23 2005-02-26 18:11:24Z server $
*
***********************************************************************************/
require_once(LIMB_SERVICE_NODE_DIR . '/commands/state_machines/StateMachineForEditEntityDialog.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/EditServiceNodeCommand.class.php');

class EditServiceNodeAtStructurePageCommand extends EditServiceNodeCommand
{
  function EditServiceNodeAtStructurePageCommand()
  {
    $template_name = '/service_node/edit.html';
    $form_id = 'service_node_form';
    $validator =  new LimbHandle(LIMB_SERVICE_NODE_DIR . '/validators/ServiceNodeEditValidator');

    parent :: EditServiceNodeCommand($template_name,
                                     $form_id,
                                     $validator,
                                     array(),
                                     array(),
                                     new ServiceNode());
  }

  function performInitEntity()
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver('service_node');

    if(!is_object($resolver))
      return LIMB_STATUS_ERROR;

    $this->entity =& $resolver->resolve($toolkit->getRequest());
    return LIMB_STATUS_OK;
  }

  function performRedirect()
  {
    include_once(LIMB_SERVICE_NODE_DIR .'/commands/RedirectToServiceNodeAtSiteStructurePageCommand.class.php');
    $command = new RedirectToServiceNodeAtSiteStructurePageCommand($this->entity);
    return $command->perform();
  }
}

?>