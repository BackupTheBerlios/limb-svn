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

class FormCreateSiteObjectCommand extends FormCommand
{
  function _registerValidationRules(&$validator, &$dataspace)
  {
    if (($parent_node_id = $dataspace->get('parent_node_id')) === null)
    {
      if(!$parent_object_data = $this->_loadParentObjectData())
        return;

      $parent_node_id = $parent_object_data['parent_node_id'];
    }

    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/tree_node_id_rule', 'parent_node_id'));
    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/tree_identifier_rule', 'identifier', $parent_node_id));
  }

  function _loadParentObjectData()
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