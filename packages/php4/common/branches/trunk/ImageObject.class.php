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
require_once(LIMB_DIR . '/core/DomainObject.class.php');

class ImageObject extends DomainObject
{
  var $_variations = array();

  function getTitle()
  {
    return $this->get('title');
  }

  function setTitle($title)
  {
    $this->set('title', $title);
  }

  function attachVariation(&$variation)
  {
    $this->_variations[$variation->getName()] =& $variation;
  }

  function getVariations()
  {
    return $this->_variations;
  }

  function & getVariation($variation)
  {
    if(isset($this->_variations[$variation]))
      return $this->_variations[$variation];
  }

  function getDescription()
  {
    return $this->get('description');
  }

  function setDescription($description)
  {
    $this->set('description', $description);
  }

}

?>
