<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbBaseToolkit.class.php 1105 2005-02-15 13:46:50Z pachanga $
*
***********************************************************************************/

class SimpleACLBaseToolkit// implements SimpleACLToolkit
{
  var $authorizer;
  var $authenticator;

  function & getAuthorizer()
  {
    if($this->authorizer)
      return $this->authorizer;

    include_once(dirname(__FILE__) . '/SimpleACLAuthorizer.class.php');
    $this->authorizer = new SimpleACLAuthorizer();

    include_once(dirname(__FILE__) . '/$SimpleACLIniBasedPolicyLoader.class.php');
    $loader = new SimpleACLIniBasedPolicyLoader();

    $loader->load($this->authorizer);

    return $this->authorizer;
  }

  function & getAuthenticator()
  {
    if($this->authenticator)
      return $this->authenticator;

    include_once(dirname(__FILE__) . '/SimpleACLAuthenticator.class.php');
    $this->authenticator = new SimpleACLAuthenticator();

    include_once(dirname(__FILE__) . '/DAO/SimpleACLIniBasedUsersDAO.class.php');
    $this->dao = new SimpleACLIniBasedUsersDAO();

    $this->authenticator->setUsersDAO($this->dao)

    return $this->authenticator;
  }
}

?>
