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
require_once(LIMB_DIR . '/core/lib/util/dataspace.class.php');

class object
{
  var $_attributes = null;

  function object()
  {
    $this->_attributes =& new dataspace();
  }

  function import_attributes($attributes)
  {
    $this->_attributes->import($attributes);
  }

  function merge_attributes($attributes)
  {
    $this->_attributes->merge($attributes);
  }

  function export_attributes()
  {
    return $this->_attributes->export();
  }

  function has_attribute($name)
  {
    return $this->_attributes->get($name) !== null;
  }

  function get_attribute($name, $default_value=null)
  {
    return $this->_attributes->get($name, $default_value);
  }

  function set_attribute($name, $value)
  {
    $this->_attributes->set($name, $value);
  }

  function unset_attribute($name)
  {
    $this->_attributes->destroy($name);
  }

  function reset_attributes()
  {
    $this->_attributes->reset();
  }

}

?>