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
require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . '/core/lib/i18n/strings.class.php');

class cart_object_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/cart/display.html',
            'popup' => true
        ),
        'add_item' => array(
            'action_path' => '/cart/add_cart_item_action',
            'popup' => true,
            'action_name' => strings :: get('add_item', 'cart'),
        ),
        'add_items' => array(
            'action_path' => '/cart/add_cart_items_action',
            'popup' => true,
            'action_name' => strings :: get('add_items', 'cart'),
        ),
        'remove_items' => array(
            'action_path' => '/cart/remove_cart_items_action',
            'popup' => true,
            'action_name' => strings :: get('remove_items', 'cart'),
        ),
        'recount' => array(
            'action_path' => '/cart/recount_cart_items_action',
            'popup' => true,
            'action_name' => strings :: get('recount', 'cart'),
        ),
        'send' => array(
            'action_path' => '/cart/send_cart_order_action',
            'popup' => true,
            'action_name' => strings :: get('send', 'cart'),
        ),
        'checkout' => array(
            'action_path' => '/cart/checkout_cart_order_action',
            'template_path' => '/cart/checkout.html',
            'popup' => true,
            'action_name' => strings :: get('checkout', 'cart'),
        )
    );
  }
}

?>