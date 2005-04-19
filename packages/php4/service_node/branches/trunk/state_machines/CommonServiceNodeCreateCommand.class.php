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

class CommonServiceNodeCreateCommand extends StateMachineCommand
{
  function CommonServiceNodeCreateCommand($template_path,
                                           $form_name,
                                           $extra_dataspace_values)
  {
    parent :: StateMachineCommand();

    $entity_field_name = 'entity';

    $this->registerState('initial',
                          new LimbHandle(LIMB_DIR . '/core/commands/UseViewCommand',
                                          array($template_path)),
                          array(LIMB_STATUS_OK => 'form'));

    $this->registerState('form',
                          new LimbHandle(LIMB_DIR . '/core/commands/FormProcessingCommand',
                                         array($form_name, false)),
                          array(LIMB_STATUS_FORM_SUBMITTED => 'extra_data',
                                LIMB_STATUS_FORM_DISPLAYED => 'init'));

    $this->registerState('extra_data',
                          new LimbHandle(LIMB_DIR . '/core/commands/PutValuesToDataspaceCommand',
                                         array($extra_dataspace_values)),
                          array(LIMB_STATUS_OK => 'validate'));

    $this->registerState('init',
                          new LimbHandle(LIMB_SERVICE_NODE_DIR .
                                         '/commands/InitCreateContentServiceNodeDataspaceCommand'),
                          array(LIMB_STATUS_OK => 'render',
                                LIMB_STATUS_ERROR => 'error'));

    $this->registerState('validate',
                          new LimbHandle(LIMB_DIR .
                                         '/core/commands/FormValidateCommand',
                                         array($form_name,
                                               new LimbHandle(LIMB_SERVICE_NODE_DIR .
                                                              '/validators/CommonCreateServiceNodeValidator'))),
                          array(LIMB_STATUS_OK => 'new_object',
                                LIMB_STATUS_FORM_NOT_VALID => 'render'));

    $this->registerState('new_object',
                          new LimbHandle(LIMB_DIR .'/core/commands/InitializeNewObjectCommand',
                                         array(new LimbHandle(LIMB_SERVICE_NODE_DIR . '/ServiceNode'),
                                               $entity_field_name)),
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
                                         '/core/commands/RedirectToMappedNodeCommand'));

    $this->registerState('error',
                          new LimbHandle(LIMB_DIR . '/core/commands/UseViewCommand',
                                          array('/not_found.html')),
                          array(LIMB_STATUS_OK => 'render'));

    $this->registerState('render',
                          new LimbHandle(LIMB_DIR . '/core/commands/DisplayViewCommand'));
  }
}

?>