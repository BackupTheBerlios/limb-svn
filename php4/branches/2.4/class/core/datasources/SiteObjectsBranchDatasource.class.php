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
require_once(dirname(__FILE__) . '/SiteObjectsDatasource.class.php');

class SiteObjectsBranchDatasource extends SiteObjectsDatasource
{
  var $path;
  var $check_expanded_parents;
  var $include_parent;
  var $depth;

  function setPath($path)
  {
    $this->path = $path;
  }

  function setCheckExpandedParents($status = true)
  {
    $this->check_expanded_parents = $status;
  }

  function setIncludeParent($status = true)
  {
    $this->include_parent = $status;
  }

  function setDepth($depth)
  {
    $this->depth = $depth;
  }

  function reset()
  {
    parent :: reset();

    $this->path = '';
    $this->check_expanded_parents = false;
    $this->include_parent = false;
    $this->depth = 1;
  }

  function getObjectIds()
  {
    if ($this->object_ids)
      return $this->object_ids;

    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    if(!$nodes = $tree->getSubBranchByPath($this->path,
                                           $this->depth,
                                           $this->include_parent,
                                           $this->check_expanded_parents))
    {
      return array();
    }

    $this->object_ids = ComplexArray :: getColumnValues('object_id', $nodes);

    return $this->object_ids;
  }
}

?>