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
  protected $object_id;
  protected $node_mapped_by_request;
  protected $request;

  public function reset()
  {
    parent :: reset();

    $this->object_id = null;
    $this->node_mapped_by_request = null;
    $this->request = null;
  }

  public function setRequest($request)
  {
    $this->request = $request;
  }

  public function getObjectIds()
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

  public function mapUriToNode($uri, $recursive = false)
  {
    $tree = Limb :: toolkit()->getTree();

    if(($node_id = $uri->getQueryItem('node_id')) === false)
      $node = $tree->getNodeByPath($uri->getPath(), '/', $recursive);
    else
      $node = $tree->getNode((int)$node_id);

    return $node;
  }

  public function mapRequestToNode($request)
  {
    if($this->node_mapped_by_request)
      return $this->node_mapped_by_request;

    if($node_id = $request->get('node_id'))
      $node = Limb :: toolkit()->getTree()->getNode((int)$node_id);
    else
      $node = $this->mapUriToNode($request->getUri());

    $this->node_mapped_by_request = $node;
    return $node;
  }
}

?>