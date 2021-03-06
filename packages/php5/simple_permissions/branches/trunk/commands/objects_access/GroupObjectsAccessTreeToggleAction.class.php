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
require_once(LIMB_COMMON_DIR . '/actions/site_structure/TreeToggleAction.class.php');
require_once(dirname(__FILE__) . '/../../AccessPolicy.class.php');

class GroupObjectsAccessTreeToggleAction extends TreeToggleAction
{
  protected $objects_ids = array();

  protected function _defineDataspaceName()
  {
    return 'set_group_access';
  }

  public function perform($request, $response)
  {
    if ($filter_groups = Limb :: toolkit()->getSession()->get('filter_groups'))
      $this->dataspace->set('filter_groups', $filter_groups);

    parent :: perform($request, $response);

    $this->_setTemplateTree();
    $this->_initDataspace($request);
  }

  protected function _initDataspace($request)
  {
    $access_policy = new AccessPolicy();

    $policy = $access_policy->getObjectsAccessByIds($this->object_ids, AccessPolicy :: ACCESSOR_TYPE_GROUP);

    $this->dataspace->set('policy', $policy);
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
