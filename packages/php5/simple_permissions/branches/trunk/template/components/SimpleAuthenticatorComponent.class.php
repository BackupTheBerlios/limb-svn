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
  public function isUserInGroups($groups)
  {
    if ((Limb :: toolkit()->getUser()->isLoggedIn()) &&  SimpleAuthenticator :: isUserInGroups($groups))
      return true;
  }

}

?>