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

class ServiceNodeRegisterRootEntityCommand extends StateMachineCommand
{
  function ServiceNodeRegisterRootEntityCommand()
  {
    parent :: StateMachineCommand();

    $entity_field_name = 'entity';

    $this->registerState('initial',
                          new LimbHandle(LIMB_DIR . '/core/commands/UseViewCommand',
                                          array('/service_node/create.html')),
                          array(LIMB_STATUS_OK => 'form'));

    $this->registerState('form',
                          new LimbHandle(LIMB_DIR . '/core/commands/FormProcessingCommand',
                                         array('service_node_form', false)),
                          array(LIMB_STATUS_FORM_SUBMITTED => 'validate',
                                LIMB_STATUS_FORM_DISPLAYED => 'render'));

    $this->registerState('validate',
                          new LimbHandle(LIMB_DIR .
                                         '/core/commands/FormValidateCommand',
                                         array('service_node_form',
                                               new LimbHandle(LIMB_SERVICE_NODE_DIR .
                                                              '/validators/ServiceNodeRegisterValidator'))),
                          array(LIMB_STATUS_OK => 'new_object',
                                LIMB_STATUS_FORM_NOT_VALID => 'render'));

    $this->registerState('new_object',
                          new LimbHandle(LIMB_SERVICE_NODE_DIR .'/commands/CreateNewServiceNodeCommand',
                                         array($entity_field_name)),
                          array(LIMB_STATUS_OK => 'map_to_object'));

    $this->registerState('map_to_object',
                          new LimbHandle(LIMB_SERVICE_NODE_DIR .'/commands/MapDataspaceToServiceNodeCommand',
                                         array($entity_field_name)),
                          array(LIMB_STATUS_OK => 'register_object'));

    $this->registerState('register_object',
                          new LimbHandle(LIMB_DIR .'/core/commands/RegisterObjectCommand',
                                         array($entity_field_name)),
                          array(LIMB_STATUS_OK => 'redirect'));

    $this->registerState('redirect',
                          new LimbHandle(LIMB_DIR .
                                         '/core/commands/RedirectCommand',
                                         array('/service_nodes')));

    $this->registerState('error',
                          new LimbHandle(LIMB_DIR . '/core/commands/UseViewCommand',
                                          array('/not_found.html')),
                          array(LIMB_STATUS_OK => 'render'));

    $this->registerState('render',
                          new LimbHandle(LIMB_DIR . '/core/commands/DisplayViewCommand'));
  }
}

?>