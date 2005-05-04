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
require_once(LIMB_DIR . '/core/commands/StateMachineCommand.class.php');
require_once(LIMB_DIR . '/core/commands/DisplayViewCommand.class.php');
require_once(LIMB_DIR . '/core/commands/UseViewCommand.class.php');
require_once(LIMB_DIR . '/core/commands/FormProcessingCommand.class.php');

class CreateServiceNodeAtStructurePageCommand extends StateMachineCommand
{
  var $affected_entity;

  function perform()
  {
    $render_command = new DisplayViewCommand();

    $result = $this->_performFormProcessing();

    if($result == LIMB_STATUS_FORM_NOT_VALID)
      return $render_command->perform();

    if($result == LIMB_STATUS_FORM_DISPLAYED)
    {
      if(!$this->_putParentNodeIdToDataspace())
      {
        $view_command = new UseViewCommand('/error.html');
        $view_command->perform();
      }
    }

    if($result == LIMB_STATUS_OK)
      $this->_performRegister();

    return $render_command->perform();
  }

  function _putParentNodeIdToDataspace()
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver('service_node');
    $parent_entity =& $resolver->resolve($toolkit->getRequest());

    if(!is_object($parent_entity))
      return false;

    $node =& $parent_entity->getPart('node');
    $dataspace =& $toolkit->getDataspace();
    $dataspace->set('parent_node_id', $node->get('id'));

    return true;
  }

  function _performFormProcessing()
  {
    $form_id = 'service_node_form';
    $validator =  new LimbHandle(LIMB_SERVICE_NODE_DIR . '/validators/ServiceNodeRegisterValidator');

    $view_command = new UseViewCommand('/service_node/create.html');
    $view_command->perform();

    $form_command = new FormProcessingCommand($form_id, false, $validator);
    return $form_command->perform();
  }

  function _performRegister()
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();
    $this->affected_entity = $toolkit->createObject($dataspace->get('class_name'));

    $register_command = new StateMachineCommand();

    $register_command->registerState('map_to_object',
                          new LimbHandle(LIMB_SERVICE_NODE_DIR .'/commands/MapDataspaceToServiceNodeCommand',
                                         array(&$this->affected_entity)),
                          array(LIMB_STATUS_OK => 'register_object'));

    $register_command->registerState('register_object',
                          new LimbHandle(LIMB_DIR .'/core/commands/RegisterObjectCommand',
                                         array(&$this->affected_entity)),
                          array(LIMB_STATUS_OK => 'redirect'));

    $register_command->registerState('redirect',
                          new LimbHandle(LIMB_SERVICE_NODE_DIR .
                                         '/commands/RedirectToServiceNodeAtSiteStructurePageCommand',
                                         array(&$this->affected_entity)));

    return $register_command->perform();
  }

  function & getAffectedEntity()
  {
    return $this->affected_entity;
  }
}

?>