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

/**
* Base class for runtime components that output XML tags
*
* @access public
* @abstract
* @package LIMB_COMPONENT
*/
class tag_component extends component
{
  /**
  * Array of XML attributes
  *
  * @var array
  * @access private
  */
  var $attributes = array();

  /**
  * Returns the value of the ID attribute
  *
  * @param string $ component class name
  * @return string
  * @access public
  */
  function get_client_id()
  {
    if (isset($this->attributes['id']))
      return $this->attributes['id'];
  }

  /**
  * Sets an attribute
  *
  * @param string $ name of attribute
  * @param string $ value of attribute
  * @return void
  * @access public
  */
  function set_attribute($attrib, $value)
  {
    $this->attributes[$attrib] = $value;
  }

  /**
  * Returns the value of an attribute, given it's name
  *
  * @param string $ name of attribute
  * @return string value of attribute
  * @access public
  */
  function get_attribute($attrib)
  {
    if (isset($this->attributes[$attrib]))
      return $this->attributes[$attrib];
  }


  function unset_attribute($attrib)
  {
    if (isset($this->attributes[$attrib]))
      unset($this->attributes[$attrib]);
  }

  /**
  * Check to see whether a named attribute exists
  *
  * @param string $ name of attribute
  * @return boolean
  * @access public
  */
  function has_attribute($attrib)
  {
    return array_key_exists($attrib, $this->attributes);
  }

  /**
  * Writes the contents of the attributes to the screen, using
  * htmlspecialchars to convert entities in values. Called by
  * a compiled template
  *
  * @return void
  * @access public
  */
  function render_attributes()
  {
    foreach ($this->attributes as $name => $value)
    {
      echo ' ';
      echo $name;
      if (!is_null($value))
      {
        echo '="';
        echo htmlspecialchars($value, ENT_QUOTES);
        echo '"';
      }
    }
  }
}

?>