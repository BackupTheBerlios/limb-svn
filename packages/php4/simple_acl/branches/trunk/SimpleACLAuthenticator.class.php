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

class SimpleACLAuthenticator// implements Authenticator
{
  var $users_dao;

  function setUsersDAO(&$users_dao)
  {
    $this->users_dao =& $users_dao;
  }

  function & getUsersDAO()
  {
    if($this->users_dao)
      return $this->users_dao;

   include_once(dirname(__FILE__) . '/DAO/SimpleACLIniBasedUsersDAO.class.php');

   $this->users_dao = new SimpleACLIniBasedUsersDAO();

   return $this->users_dao;
  }

  function login($login, $password)
  {
    $users_dao =& $this->getUsersDAO();
    if(!$user_data =& $users_dao->findByLogin($login))
      return;

    if($user_data->get('password') != md5($password))
      return;

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $user->login();

    $user->setGroups($user_data->get('groups'));
    $user->setLogin($login);
  }
}
?>