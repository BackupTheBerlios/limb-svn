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
require_once(LIMB_DIR . '/class/actions/Action.class.php');

class ActivatePasswordAction extends Action
{
  function perform(&$request, &$response)
  {
    $toolkit =& Limb :: toolkit();
    $object =& $toolkit->createSiteObject('UserObject');
    if(!$object->activatePassword())
    {
      MessageBox :: writeNotice('Password activation failed!');

      $request->setStatus(Request :: STATUS_FAILED);
      $response->redirect('/');
    }
  }
}

?>