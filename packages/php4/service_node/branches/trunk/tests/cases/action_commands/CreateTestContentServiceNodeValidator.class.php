<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: OneTableObjectMapper.class.php 1094 2005-02-08 13:09:14Z pachanga $
*
***********************************************************************************/
require_once(LIMB_SERVICE_NODE_DIR . '/validators/CommonCreateServiceNodeValidator.class.php');

class CreateTestContentServiceNodeValidator extends CommonCreateServiceNodeValidator
{
  function validate(&$DataSource)
  {
    $this->addRule(new Handle(WACT_ROOT . '/validation/rule.inc.php|RequiredRule', array('annotation')));
    $this->addRule(new Handle(WACT_ROOT . '/validation/rule.inc.php|RequiredRule', array('content')));

    parent :: validate($DataSource);
 }
}

?>