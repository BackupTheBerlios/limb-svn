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
class CartSummTagInfo
{
  var $tag = 'cart:SUMM';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'cart_summ_tag';
}

registerTag(new CartSummTagInfo());

class CartSummTag extends ServerComponentTag
{
  function CartSummTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../components/cart_summ_component';
  }

  function generateContents($code)
  {
    $code->writePhp('echo '. $this->getComponentRefCode() . '->get_cart_summ();');
  }
}

?>