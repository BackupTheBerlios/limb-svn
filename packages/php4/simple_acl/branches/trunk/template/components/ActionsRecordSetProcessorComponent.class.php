<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: JIPComponent.class.php 1186 2005-03-23 09:47:34Z seregalimb $
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(LIMB_SIMPLE_ACL_DIR . '/dao/SimpleACLActionsRecordSet.class.php');

class ActionsRecordSetProcessorComponent extends Component
{
  var $source;

  function setSource($source)
  {
    $this->source = $source;
  }

  function process()
  {
    $component =& $this->parent->findChild($this->source);
    if(empty($component->dataSet))
      return;

    $new_rs = new SimpleACLActionsRecordSet($component->dataSet);
    $component->registerDataSet($new_rs);
  }
}

?>