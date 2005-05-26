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

class GroupActionsDatasourceProcessorComponent extends Component
{
  var $processor;
  var $group_name = 'jip';

  function setGroupName($group_name)
  {
    $this->group_name = $group_name;
  }

  function process()
  {
    $actions_processor =& $this->_getActionsProcessor();
    $actions_processor->process($this->parent->getDataSource());
  }

  function & _getActionsProcessor()
  {
    if(is_object($this->processor))
      return $this->processor;

    include_once(LIMB_DIR . '/core/services/GroupActionsProcessor.class.php');
    $this->processor = new GroupActionsProcessor($this->group_name);

    return $this->processor;
  }
}

?>