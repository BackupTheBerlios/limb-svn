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
require_once(dirname(__FILE__) . '/CreateGuestbookMessageAction.class.php');

class FrontCreateGuestbookMessageAction extends CreateGuestbookMessageAction
{
  protected function _defineDataspaceName()
  {
    return 'display';
  }

  protected function _validPerform($request, $response)
  {
    parent :: _validPerform($request, $response);

    if ($request->isSuccess())
      $response->reload();
  }
}

?>