<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');

class image_object extends site_object
{
  protected $_variations = array();
  
  public function attach_variation($variation)
  {    
    $this->_variations[$variation->get_name()] = $variation;
  }
  
  public function get_variations()
  {
    return $this->_variations;
  }

  public function get_variation($variation)
  {
    if(isset($this->_variations[$variation]))
      return $this->_variations[$variation];
  }
  
  public function get_description()
  {
    return $this->get('description');
  }   
  
  public function set_description($description)
  {
    $this->set('description', $description);
  }
  
}

?>
