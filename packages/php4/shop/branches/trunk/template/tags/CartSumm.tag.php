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
  public $tag = 'cart:SUMM';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'cart_summ_tag';
}

registerTag(new CartSummTagInfo());

class CartSummTag extends ServerComponentTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../components/cart_summ_component';
  }

  public function generateContents($code)
  {
    $code->writePhp('echo '. $this->getComponentRefCode() . '->get_cart_summ();');
  }
}

?>