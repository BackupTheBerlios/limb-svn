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
require_once(LIMB_DIR . '/class/core/site_objects/SiteObject.class.php');

class ImageObject extends SiteObject
{
  protected $_variations = array();

  public function attachVariation($variation)
  {
    $this->_variations[$variation->getName()] = $variation;
  }

  public function getVariations()
  {
    return $this->_variations;
  }

  public function getVariation($variation)
  {
    if(isset($this->_variations[$variation]))
      return $this->_variations[$variation];
  }

  public function getDescription()
  {
    return $this->get('description');
  }

  public function setDescription($description)
  {
    $this->set('description', $description);
  }

}

?>
