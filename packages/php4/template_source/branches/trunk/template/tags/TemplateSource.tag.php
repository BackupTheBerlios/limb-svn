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
class TemplateSourceTagInfo
{
  public $tag = 'dev:TEMPLATE_SOURCE';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'template_source_tag';
}

registerTag(new TemplateSourceTagInfo());

class TemplateSourceTag extends ServerComponentTag
{
  function TemplateSourceTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../components/template_source_component';
  }

  function generateContents($code)
  {
    if(isset($this->attributes['target']))
      $target = 'target=' . $this->attributes['target'];
    else
      $target = '';

    $code->writePhp('echo "<a ' . $target . ' href=" . '  . $this->getComponentRefCode() . '->get_current_template_source_link() . ">"');

    parent :: generateContents($code);

    $code->writePhp('echo "</a>"');
  }
}

?>