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
require_once(LIMB_DIR . '/core/actions/FormAction.class.php');

class MultiTogglePublishStatusAction extends FormAction
{
  function _defineDataspaceName()
  {
    return 'grid_form';
  }

  function _validPerform(&$request, &$response)
  {
    if($request->hasAttribute('popup'))
      $response->write(closePopupResponse($request));

    $data = $this->dataspace->export();

    if(!isset($data['ids']) ||  !is_array($data['ids']))
    {
      $request->setStatus(Request :: STATUS_FAILURE);
      return;
    }

    $objects = $this->_getObjects(array_keys($data['ids']));

    foreach($objects as $id => $item)
    {
      if (!isset($item['actions']['publish']) ||  !isset($item['actions']['unpublish']))
        continue;

      $object = wrapWithSiteObject($item);
      $status = $object->get('status');

      if ($status & SITE_OBJECT_PUBLISHED_STATUS)
      {

        $status &= ~SITE_OBJECT_PUBLISHED_STATUS;
        $action = 'unpublish';
      }
      else
      {
        $status |= SITE_OBJECT_PUBLISHED_STATUS;
        $action = 'publish';
      }

      $object->set('status', $status);
      $object->update(false);

      $this->_applyAccessPolicy($object, $action);
    }

    $request->setStatus(Request :: STATUS_SUCCESS);
  }

  function _getObjects($node_ids)
  {
    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('SiteObjectsByNodeIdsDatasource');
    $datasource->setNodeIds($node_ids);

    return $datasource->fetch();
  }

  function _applyAccessPolicy($object, $action)
  {
    $access_policy = new AccessPolicy();
    $access_policy->applyAccessTemplates($object, $action);

    if(catch_error('LimbException', $e))
      MessageBox :: writeNotice("Access template of " . get_class($object) . " for action '{$action}' not defined!!!");
    elseif(catch_error('LimbException', $e))
      return throw_error($e);
  }
}

?>