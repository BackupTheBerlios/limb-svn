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

class CommonEditServiceNodeValidator extends Validator
{
  function validate(&$DataSource)
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver('tree_based_entity');
    if(!is_object($resolver))
      return die('tree_based_entity not set');

    if($entity =& $resolver->resolve($toolkit->getRequest()))
    {
      $node =& $entity->getPart('node');
      $parent_node_id = $node->get('id');
      $node_id = $node->get('id');
    }
    else
    {
      $parent_node_id = -1000;
      $node_id = -1000;
    }

    $this->addRule(new LimbHandle(LIMB_DIR . '/core/validators/rules/TreeIdentifierRule',
                                  array('identifier', $parent_node_id, $node_id)));

    $this->addRule(new Handle(WACT_ROOT . '/validation/rule.inc.php|RequiredRule', array('identifier')));
    $this->addRule(new Handle(WACT_ROOT . '/validation/rule.inc.php|RequiredRule', array('title')));
    $this->addRule(new Handle(WACT_ROOT . '/validation/rule.inc.php|SizeRangeRule', array('title', 255)));

    return parent :: validate($DataSource);
  }
}
?>
