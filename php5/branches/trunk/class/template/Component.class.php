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
require_once(LIMB_DIR . '/class/core/Dataspace.class.php');

// This is a bit problematic, since not every component is a dataspace.
// every template is a dataspace, however.
// Bit of a refused bequest here.
class Component extends Dataspace
{
  /**
  * Array of child components
  */
  public $children = array();
  /**
  * parent component - "parent" refers to nesting in template
  * not to class hierarchy.
  */
  public $parent;
  /**
  * root component in template
  */
  public $root;
  /**
  * ID of component, corresponding to it's ID attribute in the template
  */
  public $id;

  /**
  * Returns the ID of the component, as defined in the template tags
  * ID attribute
  */
  public function getServerId()
  {
    return $this->id;
  }

  public function setServerId($id)
  {
    $this->id = $id;
  }

  /**
  * Returns a child component given it's ID.<br />
  * Note this is a potentially expensive operation if dealing with
  * many components, as it calls the find_child method of children
  * based on alphanumeric order: strcasecmp(). Attempt to call it via
  * the nearest known component to the required child.
  */
  public function findChild($server_id)
  {
    foreach(array_keys($this->children) as $key)
    {
      if (strcasecmp($key, $server_id))
      {
        $result = $this->children[$key]->findChild($server_id);
        if ($result)
          return $result;
      }
      else
        return $this->children[$key];
    }
    return false;
  }

  /**
  * Returns the first child component matching the supplied WACT_TEMPLATE
  * component PHP class name
  */
  public function findChildByClass($class)
  {
    foreach(array_keys($this->children) as $key)
    {
      if ($this->children[$key] instanceof $class)
        return $this->children[$key];
      else
      {
        $result = &$this->children[$key]->findChildByClass($class);
        if ($result)
          return $result;
      }
    }
    return false;
  }

  /**
  * Recursively searches through parents of this component searching
  * for a given WACT_TEMPLATE component PHP class name
  */
  public function findParentByClass($class)
  {
    $parent = $this->parent;

    while ($parent &&  !($parent instanceof $class))
      $parent = $parent->parent;

    return $parent;
  }

  /**
  * Adds a reference to a child component to this component, using it's
  * ID attribute as the child array key
  */
  public function addChild($child, $server_id = null)
  {
    if (is_null($server_id))
    {
      static $genid = 1;
      $server_id = 'widxxx_' . $genid;
      $genid++;
    }

    $child->parent = $this;
    $child->id = $server_id;
    $this->children[$server_id] = $child;
  }
}

?>