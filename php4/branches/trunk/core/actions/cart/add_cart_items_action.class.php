<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/actions/form_action.class.php');
require_once(LIMB_DIR . '/core/model/shop/cart.class.php');

class add_cart_items_action extends form_action
{
  function _define_dataspace_name()
  {
    return 'order_form';
  }

  function _define_catalog_object()
  {
    return 'catalog_object';
  }

  function _valid_perform(&$request, &$response)
  {
    if(!$objects_amounts = $this->dataspace->get('amount'))
    {
      $request->set_status(REQUEST_STATUS_FAILURE);

      if($request->has_attribute('popup'))
        $response->write(close_popup_response($request));

      return;
    }

    $objects_data =& fetch_by_node_ids(
                          array_keys($objects_amounts),
                          $this->_define_catalog_object(),
                          $counter);

    if(!$objects_data)
    {
      $request->set_status(REQUEST_STATUS_FAILURE);

      if($request->has_attribute('popup'))
        $response->write(close_popup_response($request));

      return;
    }

    $object =& site_object_factory :: create($this->_define_catalog_object());

    if(!method_exists($object, 'get_cart_item'))
    {
      $request->set_status(REQUEST_STATUS_FAILURE);

      if($request->has_attribute('popup'))
        $response->write(close_popup_response($request));

      return;
    }

    $cart =& cart :: instance();

    foreach($objects_data as $key => $object_data)
    {
      $object->merge_attributes($object_data);
      $cart_item =& $object->get_cart_item();

      if(!$objects_amounts[$key])
        continue;

      $cart_item->set_amount($objects_amounts[$key]);
      $cart->add_item($cart_item);
    }

    $response->redirect('/root/cart?popup=1');
  }
}

?>