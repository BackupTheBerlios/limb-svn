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
require_once(LIMB_DIR . '/class/i18n/strings.class.php');
require_once(LIMB_DIR . '/class/core/object.class.php');

class site_object_behaviour extends object
{
  protected $_actions_list = array();

  public function get_id()
  {
    return (int)$this->get('id');
  }

  public function set_id($id)
  {
    $this->set('id', (int)$id);
  }

  public function get_default_action()
  {
    return 'display';
  }

  public function get_actions_list()
  {
    if($this->_actions_list)
      return $this->_actions_list;

    $methods = get_class_methods($this);
    foreach($methods as $method)
    {
      if(preg_match('~^define_(.*)$~', $method, $matches))
        $this->_actions_list[] = $matches[1];
    }
    return $this->_actions_list;
  }

  public function action_exists($action)
  {
    return in_array($action, $this->get_actions_list());
  }
}

?>