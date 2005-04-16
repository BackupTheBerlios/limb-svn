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
require_once(LIMB_DIR . '/core/system/objects_support.inc.php');
require_once(LIMB_DIR . '/core/Object.class.php');

define('DEFAULT_USER_ID', -1);

class User extends Object
{
  var $_is_logged_in = false;
  var $__session_class_path;

  function User()
  {
    //important!!!
    $this->__session_class_path = addslashes(__FILE__);

    parent :: Object();
  }

  function & instance()
  {
    if (!isset($GLOBALS['UserGlobalInstance']) || !is_a($GLOBALS['UserGlobalInstance'], 'User'))
      $GLOBALS['UserGlobalInstance'] =& instantiateSessionObject('User', $a = array());

    return $GLOBALS['UserGlobalInstance'];
  }

  function login()
  {
    $this->_is_logged_in = true;
  }

  function logout()
  {
    $this->_is_logged_in = false;

    $this->removeAll();
  }

  function isLoggedIn()
  {
    return $this->_is_logged_in;
  }

  function getId()
  {
    return $this->get('id');
  }

  function setId($id)
  {
    $this->set('id', $id);
  }

  function getLogin()
  {
    return $this->get('login');
  }

  function setLogin($login)
  {
    return $this->set('login', $login);
  }

  function getGroups()
  {
    return $this->get('groups');
  }

  function setGroups($groups)
  {
    $this->set('groups', $groups);
  }
}
?>