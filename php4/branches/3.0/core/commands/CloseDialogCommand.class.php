<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: RedirectCommand.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/RedirectCommand.class.php');
require_once(WACT_ROOT . '/iterator/arraydataset.inc.php');

class CloseDialogCommand
{
  var $template;

  function CloseDialogCommand($template = '/close_popup.html')
  {
    $this->template = $template;
  }

  function & _createTemplate()
  {
    include_once(WACT_ROOT . '/template/template.inc.php');
    return new Template($this->template);
  }

  function _getParamsArray()
  {
    return array(array('name' => 'from_dialog', 'value' => 1));
  }

  function perform()
  {
    $page =& $this->_createTemplate();

    $params = new ArrayDataSet($this->_getParamsArray());

    $list =& $page->findChild('params');
    $list->registerDataSet($params);

    $toolkit =& Limb :: toolkit();
    $response =& $toolkit->getResponse();
    $response->write($page->capture());
    return LIMB_STATUS_OK;
  }
}
?>
