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
require_once(LIMB_DIR . '/class/core/actions/FormAction.class.php');

class SavePriorityAction extends FormAction
{
  function _defineDataspaceName()
  {
    return 'grid_form';
  }

  function _validPerform(&$request, &$response)
  {
    $data = $this->dataspace->export();
    $toolkit =& Limb :: toolkit();
    $object =& $toolkit->createSiteObject('SiteStructure');

    if(isset($data['priority']))
      $object->savePriority($data['priority']);

    $request->setStatus(Request :: STATUS_SUCCESS);

    if($request->hasAttribute('popup'))
      $response->write(closePopupResponse($request));
  }
}

?>