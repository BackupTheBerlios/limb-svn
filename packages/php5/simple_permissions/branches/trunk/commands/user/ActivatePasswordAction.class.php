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
require_once(LIMB_DIR . '/class/core/actions/Action.class.php');

class ActivatePasswordAction extends Action
{
  public function perform($request, $response)
  {
    $object = Limb :: toolkit()->createSiteObject('UserObject');
    if(!$object->activatePassword())
    {
      MessageBox :: writeNotice('Password activation failed!');

      $request->setStatus(Request :: STATUS_FAILED);
      $response->redirect('/');
    }
  }
}

?>