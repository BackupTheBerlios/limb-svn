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
require_once(LIMB_DIR . '/class/core/commands/FormCommand.class.php');

class FormEditSiteObjectCommand extends FormCommand
{
  function _registerValidationRules(&$validator, &$dataspace)
  {
    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/tree_node_id_rule', 'parent_node_id'));
    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'identifier'));

    $object_data = $this->_loadObjectData();

    if(($parent_node_id = $dataspace->get('parent_node_id')) === null)
      $parent_node_id = $object_data['parent_node_id'];

    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/tree_identifier_rule',
                                     'identifier',
                                     (int)$parent_node_id,
                                     (int)$object_data['node_id']));
  }

  function _initFirstTimeDataspace(&$dataspace, &$request)
  {
    $object_data = $this->_loadObjectData();
    ComplexArray :: map($this->_defineDatamap(), $object_data, $data = array());

    $dataspace->merge($data);
  }

  function _loadObjectData()
  {
    $toolkit = Limb :: toolkit();
    $datasource = $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($toolkit->getRequest());

    return $datasource->fetch();
  }

  function _defineDatamap()
  {
    return array(
      'parent_node_id' => 'parent_node_id',
      'identifier' => 'identifier',
      'title' => 'title'
    );
  }
}
?>