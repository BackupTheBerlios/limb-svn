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

class SetPublishStatusAction extends Action
{
  public function perform($request, $response)
  {
    $request->setStatus(Request :: STATUS_SUCCESS);

    if($request->hasAttribute('popup'))
      $response->write(closePopupResponse($request));

    $datasource = Limb :: toolkit()->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);
    if(!$object = wrapWithSiteObject($datasource->fetch()))
      return;

    $site_object_controller = $object->getController();
    $action = $site_object_controller->getAction($request);

    switch ($action)
    {
      case 'publish':
        $status = $this->getPublishStatus($object);
      break;
      case 'unpublish':
        $status = $this->getUnpublishStatus($object);
      break;
      default:
        return ;
      break;
    }

    $object->set('status', $status);
    $object->update(false);

    $this->_applyAccessPolicy($object, $action);

    $datasource->flushCache();
  }

  public function getPublishStatus($object)
  {
    $current_status = $object->get('status');
    $current_status |= SiteObject :: STATUS_PUBLISHED;
    return $current_status;
  }

  public function getUnpublishStatus($object)
  {
    $current_status = $object->get('status');
    $current_status = $current_status & (~SiteObject :: STATUS_PUBLISHED);
    return $current_status;
  }

  protected function _applyAccessPolicy($object, $action)
  {
    try
    {
      $access_policy = new AccessPolicy();
      $access_policy->applyAccessTemplates($object, $action);
    }
    catch(LimbException $e)
    {
      MessageBox :: writeNotice("Access template of " . get_class($object) . " for action '{$action}' not defined!!!");
    }
  }
}

?>
