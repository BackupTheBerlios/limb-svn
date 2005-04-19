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

class ContentServiceNodeEditCommand extends StateMachineCommand
{
  function ContentServiceNodeEditCommand($template_path,
                                         $form_name,
                                         &$entity_handle,
                                         &$validator_handle,
                                         $content_map)
  {
    $entity_field_name = 'entity';

    $this->registerState('initial',
                         new LimbHandle(LIMB_DIR . '/core/commands/UseViewCommand',
                                        array($template_path)),
                         array(LIMB_STATUS_OK => 'init_service_node'));

    $this->registerState('init_service_node',
                         new LimbHandle(LIMB_DIR .
                                        '/core/commands/PutCurrentEntityToContextCommand',
                                        array($entity_field_name)),
                         array(LIMB_STATUS_OK => 'form',
                               LIMB_STATUS_ERROR => 'error',
                               ));

    $this->registerState('form',
                          new LimbHandle(LIMB_DIR . '/core/commands/FormProcessingCommand',
                                         array($form_name, false)),
                          array(LIMB_STATUS_FORM_SUBMITTED => 'validate',
                                LIMB_STATUS_FORM_DISPLAYED => 'map_to_dataspace'));

    $this->registerState('map_to_dataspace',
                          new LimbHandle(LIMB_SERVICE_NODE_DIR . '/commands/MapContentServiceNodeToDataspaceCommand',
                                         array($entity_field_name, array_flip($content_map))),
                          array(LIMB_STATUS_OK => 'render'));

    $this->registerState('validate',
                          new LimbHandle(LIMB_DIR .
                                         '/core/commands/FormValidateCommand',
                                         array($form_name, $validator_handle)),
                          array(LIMB_STATUS_OK => 'map_to_service_node',
                                LIMB_STATUS_FORM_NOT_VALID => 'render'));

    $this->registerState('map_to_service_node',
                          new LimbHandle(LIMB_SERVICE_NODE_DIR . '/commands/MapDataspaceToContentServiceNodeCommand',
                                         array($entity_field_name, $content_map)),
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