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
require_once(WACT_ROOT . '/validation/validator.inc.php');

class ServiceNodeRegisterValidator extends Validator
{
  function validate(&$DataSource)
  {
    $toolkit =& Limb :: toolkit('service_node_toolkit');
    $locator =& $toolkit->getServiceNodeLocator();

    if($entity =& $locator->getCurrentServiceNode())
    {
      $node =& $entity->getPart('node');
      $parent_node_id = $node->get('id');
    }
    else
      $parent_node_id = -1000;

    $this->addRule(new Handle(WACT_ROOT . '/validation/rule.inc.php|RequiredRule', array('identifier')));
    $this->addRule(new LimbHandle(LIMB_DIR . '/core/validators/rules/TreeIdentifierRule',
                                  array('identifier', $parent_node_id)));
    $this->addRule(new Handle(WACT_ROOT . '/validation/rule.inc.php|RequiredRule', array('title')));
    $this->addRule(new Handle(WACT_ROOT . '/validation/rule.inc.php|SizeRangeRule', array('title', 255)));
    $this->addRule(new Handle(WACT_ROOT . '/validation/rule.inc.php|RequiredRule', array('class_name')));
    $this->addRule(new Handle(WACT_ROOT . '/validation/rule.inc.php|SizeRangeRule', array('class_name', 50)));
    $this->addRule(new Handle(WACT_ROOT . '/validation/rule.inc.php|RequiredRule', array('service_name')));
    $this->addRule(new Handle(WACT_ROOT . '/validation/rule.inc.php|SizeRangeRule', array('class_name', 100)));

    return parent :: validate($DataSource);
  }

}
?>
