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
/**
* Base class for runtime components that output XML tags
*/
class TagComponent extends Component
{
  /**
  * Array of XML attributes
  */
  public $attributes = array();

  /**
  * Returns the value of the ID attribute
  */
  public function getClientId()
  {
    if (isset($this->attributes['id']))
      return $this->attributes['id'];
  }

  /**
  * Sets an attribute
  */
  public function setAttribute($attrib, $value)
  {
    $this->attributes[$attrib] = $value;
  }

  /**
  * Returns the value of an attribute, given it's name
  */
  public function getAttribute($attrib)
  {
    if (isset($this->attributes[$attrib]))
      return $this->attributes[$attrib];
  }

  public function unsetAttribute($attrib)
  {
    if (isset($this->attributes[$attrib]))
      unset($this->attributes[$attrib]);
  }

  /**
  * Check to see whether a named attribute exists
  */
  public function hasAttribute($attrib)
  {
    return array_key_exists($attrib, $this->attributes);
  }

  /**
  * Writes the contents of the attributes to the screen, using
  * htmlspecialchars to convert entities in values. Called by
  * a compiled template
  */
  public function renderAttributes()
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