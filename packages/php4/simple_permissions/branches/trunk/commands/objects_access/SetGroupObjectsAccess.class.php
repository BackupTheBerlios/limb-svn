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
require_once(dirname(__FILE__) . '/../../AccessPolicy.class.php');

class SetGroupObjectsAccess extends FormAction
{
  var $objects_ids = array();

  function _defineDataspaceName()
  {
    return 'set_group_access';
  }

  function perform(&$request, &$response)
  {
    $toolkit =& Limb :: toolkit();
    $session =& $toolkit->getSession();
    $tree =& $toolkit->getTree();

    $parents =& $session->getReference('tree_expanded_parents');
    $tree->setExpandedParents($parents);

    if ($filter_groups = $session->get('filter_groups'))
      $this->dataspace->set('filter_groups', $filter_groups);

    parent :: perform(&$request, &$response);

    $this->_fillPolicy();
  }

  function _fillPolicy()
  {
    $access_policy = new AccessPolicy();
    $policy = $access_policy->getObjectsAccessByIds($this->object_ids, ACCESS_POLICY_ACCESSOR_TYPE_GROUP);

    $this->dataspace->set('policy', $policy);
  }

  function _initDataspace(&$request)
  {
    parent :: _initDataspace($request);

    $this->_setTemplateTree();

    $this->_fillPolicy();
  }

  function _validPerform(&$request, &$response)
  {
    $data = $this->dataspace->export();

    $toolkit =& Limb :: toolkit();
    $session =& $toolkit->getSession();

    if($groups = $this->dataspace->get('filter_groups'))
      $session->set('filter_groups', $groups);

    if(isset($data['update']) &&  isset($data['policy']))
    {
      $access_policy = new AccessPolicy();
      $access_policy->saveObjectsAccess($data['policy'], ACCESS_POLICY_ACCESSOR_TYPE_GROUP, $groups);
    }

    $this->_setTemplateTree();

    $request->setStatus(Request :: STATUS_FORM_SUBMITTED);
  }

  function _setTemplateTree()
  {
    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('GroupObjectAccessDatasource');

    $params = array(
      'path' => '/root',
      'depth' => -1,
      'loader_class_name' => 'site_object',
      'restrict_by_class' => false,
      'include_parent' => 'true',
      'check_expanded_parents' => 'true',
      'order' => array('class_ordr' => 'ASC', 'identifier' => 'ASC'),
      'fetch_method' => 'fetch_by_ids'

    );
    $count = null;
    $dataset =& $datasource->getDataset($count, $params);

    $this->object_ids = array();
    $dataset->reset();
    while($dataset->next())
    {
      $object = $dataset->export();
      $this->object_ids[$object['id']] = $object['id'];
    }

    $dataset->reset();
    $access_tree = $this->view->findChild('access');
    $access_tree->registerDataset($dataset);
  }
}

?>