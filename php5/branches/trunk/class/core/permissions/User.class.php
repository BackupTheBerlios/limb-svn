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
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . '/class/core/object.class.php');

class user extends object
{
  const DEFAULT_USER_ID = -1;
  protected static $_instance = null;

  protected $_is_logged_in = false;
  protected $__session_class_path;

  function __construct()
  {
    //important!!!
    $this->__session_class_path = addslashes(__FILE__);

    parent :: __construct();
  }

  static public function instance()
  {
    if (!self :: $_instance)
      self :: $_instance = instantiate_session_object('user');

    return self :: $_instance;
  }

  public function login()
  {
    $this->_is_logged_in = true;
  }

  public function logout()
  {
    $this->reset();

    $this->_is_logged_in = false;
  }

  public function is_logged_in()
  {
    return $this->_is_logged_in;
  }

  public function get_login()
  {
    return $this->get('login');
  }

  public function get_id()
  {
    return $this->get('id', self :: DEFAULT_USER_ID);
  }
}
?>