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
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . '/class/core/Object.class.php');

class User extends Object
{
  const DEFAULT_USER_ID = -1;

  var $_is_logged_in = false;
  var $__session_class_path;

  function __construct()
  {
    //important!!!
    $this->__session_class_path = addslashes(__FILE__);

    parent :: __construct();
  }

  function & instance()
  {
    if (!isset($GLOBALS['UserGlobalInstance']) || !is_a($GLOBALS['UserGlobalInstance'], 'User'))
      $GLOBALS['UserGlobalInstance'] =& instantiateSessionObject('User');

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
    return $this->get('id', User :: DEFAULT_USER_ID);
  }
}
?>