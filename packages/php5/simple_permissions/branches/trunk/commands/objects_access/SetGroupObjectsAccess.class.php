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
require_once(LIMB_DIR . '/class/core/actions/FormAction.class.php');
require_once(dirname(__FILE__) . '/../../AccessPolicy.class.php');

class SetGroupObjectsAccess extends FormAction
{
  protected  $objects_ids = array();

  protected function _defineDataspaceName()
  {
    return 'set_group_access';
  }

  public function perform($request, $response)
  {
    $parents =& Limb :: toolkit()->getSession()->getReference('tree_expanded_parents');
    Limb :: toolkit()->getTree()->setExpandedParents($parents);

    if ($filter_groups = Limb :: toolkit()->getSession()->get('filter_groups'))
      $this->dataspace->set('filter_groups', $filter_groups);

    parent :: perform($request, $response);

    $this->_fillPolicy();
  }

  protected function _fillPolicy()
  {
    $access_policy = new AccessPolicy();
    $policy = $access_policy->getObjectsAccessByIds($this->object_ids, AccessPolicy :: ACCESSOR_TYPE_GROUP);

    $this->dataspace->set('policy', $policy);
  }

  protected function _initDataspace($request)
  {
    parent :: _initDataspace($request);

    $this->_setTemplateTree();

    $this->_fillPolicy();
  }

  protected function _validPerform($request, $response)
  {
    $data = $this->dataspace->export();

    if($groups = $this->dataspace->get('filter_groups'))
      Limb :: toolkit()->getSession()->set('filter_groups', $groups);

    if(isset($data['update']) &&  isset($data['policy']))
    {
      $access_policy = new AccessPolicy();
      $access_policy->saveObjectsAccess($data['policy'], AccessPolicy :: ACCESSOR_TYPE_GROUP, $groups);
    }

    $this->_setTemplateTree();

    $request->setStatus(Request :: STATUS_FORM_SUBMITTED);
  }

  protected function _setTemplateTree()
  {
    $datasource = Limb :: toolkit()->getDatasource('GroupObjectAccessDatasource');
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
    $dataset = $datasource->getDataset($count, $params);

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