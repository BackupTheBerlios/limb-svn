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
require_once(LIMB_DIR . '/class/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/class/system/objects_support.inc.php');
require_once(LIMB_DIR . '/class/Object.class.php');

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
    $this->reset();

    $this->_is_logged_in = false;
  }

  function isLoggedIn()
  {
    return $this->_is_logged_in;
  }

  function getLogin()
  {
    return $this->get('login');
  }

  function getId()
  {
    return $this->get('id', DEFAULT_USER_ID);
  }
}
?>