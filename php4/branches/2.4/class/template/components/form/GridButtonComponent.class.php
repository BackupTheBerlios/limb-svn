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
require_once(LIMB_DIR . '/class/template/components/form/FormElement.class.php');

class GridButtonComponent extends FormElement
{
  var $path = '';
  var $action = '';
  var $reload_parent = 0;
  var $onclick = '';

  function _processAttributes()
  {
    if (isset($this->attributes['path']))
      $this->path = $this->attributes['path'];

    if (isset($this->attributes['action']))
      $this->action = $this->attributes['action'];

    if (isset($this->attributes['reload_parent']))
      $this->reload_parent = $this->attributes['reload_parent'];

    if (isset($this->attributes['onclick']))
      $this->onclick = $this->attributes['onclick'];

    unset($this->attributes['path']);
    unset($this->attributes['action']);
    unset($this->attributes['reload_parent']);
    unset($this->attributes['onclick']);
  }

  function renderAttributes()
  {
    $this->_processAttributes();

    if (!$this->path)
    {
      $action_path = $_SERVER['PHP_SELF'];

      if($node_id = Limb :: toolkit()->getRequest()->get('node_id'))
        $action_path .= '?node_id=' . $node_id;
    }
    else
      $action_path = $this->path;

    if (strpos($action_path, '?') === false)
      $action_path .= '?';
    else
      $action_path .= '&';

    if($this->action)
      $action_path .= 'action=' . $this->action;

    if ((bool)$this->reload_parent)
    {
      $action_path .= '&reload_parent=1';
    }

    $this->attributes['onclick'] = $this->onclick;
    $this->attributes['onclick'] .= "submitForm(this.form, '{$action_path}')";

    parent :: renderAttributes();

    unset($this->attributes['onclick']);
  }
}

?>
