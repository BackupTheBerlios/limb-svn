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
require_once(LIMB_DIR . '/class/template/Component.class.php');

class OrderComponent extends Component
{
  function prepare()
  {
    $params = array();
    $params['id'] = $this->get('node_id');
    $params['action'] = 'order';
    $params['rn'] = time();
    $params['popup'] = 1;

    $this->set('order_up_alt', Strings :: get('order_up'));
    $this->set('order_down_alt', Strings :: get('order_down'));

    if (!$this->get('is_first_child'))
    {
      $params['direction'] = 'up';
      $this->set('order_up_href', addUrlQueryItems($_SERVER['PHP_SELF'], $params));
    }
    else
      $this->set('order_up_href', '');

    if (!$this->get('is_last_child'))
    {
      $params['direction'] = 'down';
      $this->set('order_down_href', addUrlQueryItems($_SERVER['PHP_SELF'], $params));
    }
    else
      $this->set('order_down_href', '');

    return parent :: prepare();
  }
}
?>