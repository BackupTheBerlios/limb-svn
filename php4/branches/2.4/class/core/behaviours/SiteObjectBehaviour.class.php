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
require_once(LIMB_DIR . '/class/i18n/Strings.class.php');
require_once(LIMB_DIR . '/class/core/Object.class.php');

class SiteObjectBehaviour extends Object
{
  protected $_actions_list = array();

  public function getId()
  {
    return (int)$this->get('id');
  }

  public function setId($id)
  {
    $this->set('id', (int)$id);
  }

  public function getDefaultAction()
  {
    return 'display';
  }

  public function getActionsList()
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

  public function actionExists($action)
  {
    return in_array($action, $this->getActionsList());
  }
}

?>