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

class StateMachineForCreateEntityDialog extends StateMachineCommand
{
  function StateMachineForCreateEntityDialog(&$factory)
  {
    parent :: StateMachineCommand($factory);

    $this->registerState('Initial',
                          array(LIMB_STATUS_OK => 'FormProcessing'));

    $this->registerState('FormProcessing',
                          array(LIMB_STATUS_OK => 'InitEntity',
                                LIMB_STATUS_FORM_DISPLAYED => 'InitDataspace',
                                LIMB_STATUS_FORM_NOT_VALID => 'Render',
                                ));

    $this->registerState('InitDataspace',
                          array(LIMB_STATUS_OK => 'Render'));

    $this->registerState('InitEntity',
                          array(LIMB_STATUS_OK => 'MapDataspaceToEntity',
                                LIMB_STATUS_ERROR => 'Error'));

    $this->registerState('MapDataspaceToEntity',
                          array(LIMB_STATUS_OK => 'RegisterEntity'));

    $this->registerState('RegisterEntity',
                          array(LIMB_STATUS_OK => 'Redirect'));

    $this->registerState('Redirect');

    $this->registerState('Error',
                          array(LIMB_STATUS_OK => 'Render'));

    $this->registerState('Render');
  }
}

?>