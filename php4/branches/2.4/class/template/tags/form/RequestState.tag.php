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
class RequestStateTagInfo
{
  public $tag = 'request_state';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'request_state_tag';
}

registerTag(new RequestStateTagInfo());

class RequestStateTag extends ControlTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/request_state_component';
  }

  public function prepare()
  {
    $this->attributes['type'] = 'hidden';
  }

  public function getRenderedTag()
  {
    return 'input';
  }

  public function preGenerate($code)
  {
    if(isset($this->attributes['attach_form_prefix']))
      $code->writePhp($this->getComponentRefCode() . '->attach_form_prefix(true);');
    else
      $code->writePhp($this->getComponentRefCode() . '->attach_form_prefix(false);');

    parent :: preGenerate($code);
  }
}

?>