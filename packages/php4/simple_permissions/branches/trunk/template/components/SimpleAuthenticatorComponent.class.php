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
require_once(dirname(__FILE__) . '/../../SimpleAuthenticator.class.php');

class SimpleAuthenticatorComponent extends Component
{
  function isUserInGroups($groups)
  {
    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();

    if (($user->isLoggedIn()) &&  SimpleAuthenticator :: isUserInGroups($groups))
      return true;
  }

}

?>