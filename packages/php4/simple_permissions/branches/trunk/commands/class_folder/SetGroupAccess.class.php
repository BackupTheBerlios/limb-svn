<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commans/FormCommand.class.php');
require_once(dirname(__FILE__) . '/../../AccessPolicy.class.php');

class SetGroupAccessCommand extends FormCommand
{
  function SetGroupAccessCommand()
  {
    parent :: FormCommand('set_group_access');
  }

  function _initDataspace()
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();
    $request =& $toolkit->getRequest();

    if (!$class_id = $request->get('class_id'))
      return throw(new LimbException('class_id not defined'));

    $access_policy =& $this->_getAccessPolicy();
    $policy = $access_policy->getActionsAccess($class_id, ACCESS_POLICY_ACCESSOR_TYPE_GROUP);

    $dataspace->set('policy', $policy);
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();
    $request =& $toolkit->getRequest();

    if (!$class_id = $request->get('class_id'))
      return throw(new LimbException('class_id not defined'));

    $access_policy =& $this->_getAccessPolicy();
    $access_policy->saveActionsAccess($class_id,
                                        $dataspace->get('policy'),
                                        ACCESS_POLICY_ACCESSOR_TYPE_GROUP);

    return LIMB_STATUS_OK;
  }

  // for mocking
  function _getAccessPolicy()
  {
    return new AccessPolicy();
  }

}

?>