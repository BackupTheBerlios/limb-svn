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

/** --------------------------------------------------------------------------------
// This is a bit problematic, since not every component is a dataspace.
// every template is a dataspace, however.
// Bit of a refused bequest here.

* Base class for runtime components.<br />
* Note that components that output XML tags should not inherit directly from
* component but rather the child tag_component<br />
* Note that in the comments for this class, the terms parent and child
* refer to the given components relative position in a template's
* hierarchy, not to the PHP class hierarchy
* @access public
* @abstract
* @package LIMB_COMPONENT
*/
class component extends dataspace
{
  /**
  * Array of child components
  *
  * @var array of component objects
  * @access private
  */
  var $children = array();
  /**
  * parent component - "parent" refers to nesting in template
  * not to class hierarchy.
  *
  * @var object component object
  * @access private
  */
  var $parent;
  /**
  * root component in template
  *
  * @var object component object
  * @access private
  */
  var $root;
  /**
  * ID of component, corresponding to it's ID attribute in the template
  *
  * @var string
  * @access private
  */
  var $id;

  /**
  * Returns the ID of the component, as defined in the template tags
  * ID attribute
  *
  * @return string
  * @access public
  */
  function get_server_id()
  {
    return $this->id;
  }

  /**
  * Returns a child component given it's ID.<br />
  * Note this is a potentially expensive operation if dealing with
  * many components, as it calls the find_child method of children
  * based on alphanumeric order: strcasecmp(). Attempt to call it via
  * the nearest known component to the required child.
  *
  * @param string $ id
  * @return mixed refernce to child component object or false if not found
  * @access public
  */
  function &find_child($server_id)
  {
    foreach(array_keys($this->children) as $key)
    {
      if (strcasecmp($key, $server_id))
      {
        $result = &$this->children[$key]->find_child($server_id);
        if ($result)
          return $result;
      }
      else
        return $this->children[$key];
    }
    return false;
  }

  /**
  * Returns the first child component matching the supplied LIMB_TEMPLATE
  * component PHP class name<br />
  *
  * @param string $ component class name
  * @return mixed reference to child component object or false if not found
  * @access public
  */
  function &find_child_by_class($class)
  {
    foreach(array_keys($this->children) as $key)
    {
      if (is_a($this->children[$key], $class))
        return $this->children[$key];
      else
      {
        $result = &$this->children[$key]->find_child_by_class($class);
        if ($result)
          return $result;
      }
    }
    return false;
  }

  /**
  * Recursively searches through parents of this component searching
  * for a given LIMB_TEMPLATE component PHP class name
  *
  * @param string $ component class name
  * @return mixed reference to parent component object or false if not found
  * @access public
  */
  function &find_parent_by_class($class)
  {
    $parent = &$this->parent;
    while ($parent && !is_a($parent, $class))
    $parent = &$parent->parent;

    return $parent;
  }

  /**
  * Adds a reference to a child component to this component, using it's
  * ID attribute as the child array key
  *
  * @param object $ child component
  * @param string $ value for ID attribute
  * @return void
  * @access public
  */
  function add_child(&$child, $server_id = null)
  {
    if (is_null($server_id))
    {
      static $genid = 1;
      $server_id = 'widxxx_' . $genid;
      $genid++;
    }

    $child->parent = &$this;
    $child->id = $server_id;
    $this->children[$server_id] = &$child;
  }
}

?>