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
require_once(LIMB_DIR . '/class/core/actions/Action.class.php');

class RecoverVersionAction extends Action
{
  function perform($request, $response)
  {
    if($request->hasAttribute('popup'))
      $response->write(closePopupNoParentReloadResponse());

    $request->setStatus(Request :: STATUS_FAILURE);

    if(!$version = $request->get('version'))
      return;

    if(!$node_id = $request->get('version_node_id'))
      return;

    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('SingleObjectDatasource');
    $datasource->setNodeId($node_id);

    if(!$site_object = wrapWithSiteObject($datasource->fetch()))
      return;

    if(!is_subclass_of($site_object, 'ContentObject'))
      return;

    if(!$site_object->recoverVersion((int)$version))
      return;

    if($request->hasAttribute('popup'))
      $response->write(closePopupResponse($request));

    $request->setStatus(Request :: STATUS_SUCCESS);
  }
}

?>