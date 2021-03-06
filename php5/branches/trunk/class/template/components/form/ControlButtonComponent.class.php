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

class ControlButtonComponent extends FormElement
{
  public function renderAttributes()
  {
    if (!isset($this->attributes['path']) ||  !$this->attributes['path'])
    {
      $action_path = $_SERVER['PHP_SELF'];

      $request = Limb :: toolkit()->getRequest();

      if($node_id = $request->get('node_id'))
        $action_path .= '?node_id=' . $node_id;
    }
    else
      $action_path = $this->attributes['path'];

    if (strpos($action_path, '?') === false)
      $action_path .= '?';
    else
      $action_path .= '&';

    if(isset($this->attributes['action']))
      $action_path .= 'action=' . $this->attributes['action'];

    if (isset($this->attributes['reload_parent']) &&  $this->attributes['reload_parent'])
    {
      $action_path .= '&reload_parent=1';
      unset($this->attributes['reload_parent']);
    }

    if(!isset($this->attributes['onclick']))
      $this->attributes['onclick'] = '';

    $this->attributes['onclick'] .= "submitForm(this.form, '{$action_path}')";

    unset($this->attributes['path']);
    unset($this->attributes['action']);

    parent :: renderAttributes();
  }
}

?>