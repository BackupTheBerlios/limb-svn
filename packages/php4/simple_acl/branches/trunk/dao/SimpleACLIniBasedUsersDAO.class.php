<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: SimpleAuthorizer.class.php 1032 2005-01-18 15:43:46Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');

class SimpleACLIniBasedUsersDAO
{
  function & findByLogin($login)
  {
    $toolkit =& Limb :: toolkit();
    $ini =& $toolkit->getIni('acl.ini');
    if(!$ini->hasOption('users'))
       return;

    $user_lines = $ini->getOption('users');

    foreach($user_lines as $user_line)
    {
      $user_data = explode(':', $user_line);
      if ($user_data[0] == $login)
        return $this->_createUserDataSpaceFromArray($user_data);
    }
  }

  function & _createUserDataSpaceFromArray($user_data)
  {
    $result = new DataSpace();
    $result->import(array('login' => $user_data[0],
                 'password' => $user_data[1],
                 'name' => $user_data[2],
                 'email' => $user_data[3],
                 'groups' => explode(',', $user_data[4])));
    return $result;
  }
}
?>