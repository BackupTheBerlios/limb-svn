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

class StateMachineForEditEntityDialog extends StateMachineCommand
{
  function StateMachineForEditEntityDialog(&$factory)
  {
    parent :: StateMachineCommand($factory);

    $this->registerState('InitDialog',
                          array(LIMB_STATUS_OK => 'InitEntity'));

    $this->registerState('InitEntity',
                          array(LIMB_STATUS_OK => 'FormProcessing',
                                LIMB_STATUS_ERROR => 'NotFound'));

    $this->registerState('FormProcessing',
                          array(LIMB_STATUS_OK => 'MapDataspaceToEntity',
                                LIMB_STATUS_FORM_DISPLAYED => 'InitDataspace',
                                LIMB_STATUS_FORM_NOT_VALID => 'Render',
                                ));

    $this->registerState('InitDataspace',
                          array(LIMB_STATUS_OK => 'Render',
                                LIMB_STATUS_ERROR => 'Error',
                                ));

    $this->registerState('MapDataspaceToEntity',
                          array(LIMB_STATUS_OK => 'Redirect'));

    $this->registerState('Redirect');

    $this->registerState('Error',
                          array(LIMB_STATUS_OK => 'Render'));

    $this->registerState('NotFound',
                          array(LIMB_STATUS_OK => 'Render'));

    $this->registerState('Render');
  }
}

?>