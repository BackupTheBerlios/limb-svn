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
require_once(LIMB_DIR . '/class/actions/FormAction.class.php');

class MultiDeleteAction extends FormAction
{
  function _defineDataspaceName()
  {
    return 'grid_form';
  }

  function _initDataspace(&$request)
  {
    parent :: _initDataspace($request);

    $this->_transferDataspace($request);
  }

  function _firstTimePerform(&$request, &$response)
  {
    $data = $this->dataspace->export();

    if(!isset($data['ids']) ||  !is_array($data['ids']))
    {
      $request->setStatus(Request :: STATUS_FAILURE);

      if($request->hasAttribute('popup'))
        $response->write(closePopupResponse($request));

      return;
    }

    $objects = $this->_getObjectsToDelete(array_keys($data['ids']));

    $grid = $this->view->findChild('multi_delete');

    $grid->registerDataset(new ArrayDataset($objects));

    parent :: _firstTimePerform(&$request, &$response);
  }

  function _validPerform(&$request, &$response)
  {
    $data = $this->dataspace->export();

    $request->setStatus(Request :: STATUS_FAILURE);

    if($request->hasAttribute('popup'))
      $response->write(closePopupResponse($request));

    if(!isset($data['ids']) ||  !is_array($data['ids']))
      return;

    $objects = $this->_getObjectsToDelete(array_keys($data['ids']));

    foreach($objects as $id => $item)
    {
      if($item['delete_status'] !== 0 )
        continue;

      $site_object = wrapWithSiteObject($item);

      $site_object->delete();

      if(catch('LimbException', $e)
      {
        MessageBox :: writeNotice("object {$id} - {$item['title']} couldn't be deleted!");
        $request->setStatus(Request :: STATUS_FAILURE);
        return throw($e);
      }
    }

    $request->setStatus(Request :: STATUS_SUCCESS);

    $response->write(closePopupResponse($request));
  }

  function _getObjectsToDelete($node_ids)
  {
    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('SiteObjectsByNodeIdsDatasource');
    $datasource->setNodeIds($node_ids);

    $objects = $datasource->fetch();

    $tree =& $toolkit->getTree();

    foreach($objects as $id => $item)
    {
      if (!isset($item['actions']['delete']))
      {
        $objects[$id]['delete_status'] = 1;
        $objects[$id]['delete_reason'] = Strings :: get('delete_action_not_accessible', 'error');
        continue;
      }

      $site_object = wrapWithSiteObject($item);
      if (!$site_object->canDelete())
      {
        $objects[$id]['delete_status'] = 1;
        $objects[$id]['delete_reason'] = Strings :: get('cant_be_deleted', 'error');
        continue;
      }

      $objects[$id]['delete_reason'] = Strings :: get('ok');
      $objects[$id]['delete_status'] = 0;
      $objects[$id]['ids'][$item['node_id']] = 1;
    }

    return $objects;
  }

}

?>