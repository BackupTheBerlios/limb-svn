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
  var $tag = 'request_state';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'request_state_tag';
}

registerTag(new RequestStateTagInfo());

class RequestStateTag extends ControlTag
{
  function RequestStateTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/request_state_component';
  }

  function prepare()
  {
    $this->attributes['type'] = 'hidden';
  }

  function getRenderedTag()
  {
    return 'input';
  }

  function preGenerate($code)
  {
    if(isset($this->attributes['attach_form_prefix']))
      $code->writePhp($this->getComponentRefCode() . '->attach_form_prefix(true);');
    else
      $code->writePhp($this->getComponentRefCode() . '->attach_form_prefix(false);');

    parent :: preGenerate($code);
  }
}

?>