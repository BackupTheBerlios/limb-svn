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

class SimpleACLLoginCommand
{
  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $acl_toolkit =& Limb :: toolkit('SimpleACL');
    $authenticator =& $acl_toolkit->getAuthenticator();

    $authenticator->login($dataspace->get('login'), $dataspace->get('password'));

    $user =& $toolkit->getUser();

    if ($user->isLoggedIn())
      return LIMB_STATUS_OK;
    else
      return LIMB_STATUS_ERROR;
  }
}

?>
