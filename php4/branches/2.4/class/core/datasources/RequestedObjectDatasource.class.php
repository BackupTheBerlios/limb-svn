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

class RequestedObjectDatasource extends SiteObjectsDatasource
{
  var $object_id;
  var $node_mapped_by_request;
  var $request;

  function reset()
  {
    parent :: reset();

    $this->object_id = null;
    $this->node_mapped_by_request = null;
    $this->request = null;
  }

  function setRequest($request)
  {
    $this->request = $request;
  }

  function getObjectIds()
  {
    if($this->object_id)
      return array($this->object_id);

    if($this->request &&  $node = $this->mapRequestToNode($this->request))
    {
      $this->object_id = $node['object_id'];
      return array($this->object_id);
    }
    else
      throw new LimbException('request is null');
  }

  function mapUriToNode($uri, $recursive = false)
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $tree->getTree();

    if(($node_id = $uri->getQueryItem('node_id')) === false)
      $node = $tree->getNodeByPath($uri->getPath(), '/', $recursive);
    else
      $node = $tree->getNode((int)$node_id);

    return $node;
  }

  function mapRequestToNode($request)
  {
    if($this->node_mapped_by_request)
      return $this->node_mapped_by_request;

    if($node_id = $request->get('node_id'))
    {
      $toolkit =& Limb :: toolkit();
      $tree =& $tree->getTree();

      $node = $tree->getNode((int)$node_id);
    }
    else
      $node = $this->mapUriToNode($request->getUri());

    $this->node_mapped_by_request = $node;
    return $node;
  }
}

?>