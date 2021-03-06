<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/


require_once(LIMB_DIR . '/core/template/component.class.php');

class order_component extends component
{
  function prepare()
  {
    $params = array();
    $params['id'] = $this->get('node_id');
    $params['action'] = 'order';
    $params['rn'] = time();
    $params['popup'] = 1;

    $this->set('order_up_alt', strings :: get('order_up'));
    $this->set('order_down_alt', strings :: get('order_down'));

    if (!$this->get('is_first_child'))
    {
      $params['direction'] = 'up';
      $this->set('order_up_href', add_url_query_items($_SERVER['PHP_SELF'], $params));
    }
    else
      $this->set('order_up_href', '');

    if (!$this->get('is_last_child'))
    {
      $params['direction'] = 'down';
      $this->set('order_down_href', add_url_query_items($_SERVER['PHP_SELF'], $params));
    }
    else
      $this->set('order_down_href', '');

    return parent :: prepare();
  }


}

?>